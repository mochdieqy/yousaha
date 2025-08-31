<?php

namespace App\Http\Controllers;

use App\Models\Income;
use App\Models\IncomeDetail;
use App\Models\GeneralLedger;
use App\Models\GeneralLedgerDetail;
use App\Models\Account;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class IncomeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company) {
            return redirect()->route('company.choice')->with('error', 'Please select a company first.');
        }

        $query = Income::where('company_id', $company->id)
            ->with(['details.account', 'receiptAccount', 'customer']);

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('number', 'like', "%{$search}%")
                  ->orWhere('note', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Apply date filter
        if ($request->filled('date_from')) {
            $query->where('date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('date', '<=', $request->date_to);
        }

        // Apply amount filter
        if ($request->filled('amount_min')) {
            $query->where('total', '>=', $request->amount_min);
        }

        if ($request->filled('amount_max')) {
            $query->where('total', '<=', $request->amount_max);
        }

        $incomes = $query->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('pages.incomes.index', compact('incomes', 'company'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company) {
            return redirect()->route('company.choice')->with('error', 'Please select a company first.');
        }

        $accounts = Account::where('company_id', $company->id)
            ->where('type', 'Revenue')
            ->orderBy('code')
            ->get();

        $receiptAccounts = Account::where('company_id', $company->id)
            ->whereIn('type', ['Asset', 'Liability'])
            ->orderBy('code')
            ->get();

        $customers = Customer::where('company_id', $company->id)
            ->orderBy('name')
            ->get();

        return view('pages.incomes.create', compact('accounts', 'receiptAccounts', 'customers', 'company'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company) {
            return redirect()->route('company.choice')->with('error', 'Please select a company first.');
        }

        $validator = Validator::make($request->all(), [
            'number' => 'required|string|max:50',
            'date' => 'required|date',
            'customer_id' => 'nullable|exists:customers,id',
            'note' => 'nullable|string|max:500',
            'total' => 'required|numeric|min:0',
            'receipt_account_id' => 'required|exists:accounts,id',
            'description' => 'nullable|string|max:500',
            'details' => 'required|array|min:1',
            'details.*.account_id' => 'required|exists:accounts,id',
            'details.*.amount' => 'required|numeric|min:0',
            'details.*.description' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Validate that total matches sum of details
        $totalAmount = collect($request->details)->sum('amount');
        if (abs($totalAmount - $request->total) > 0.01) {
            return redirect()->back()
                ->withErrors(['total' => 'Total amount must equal the sum of detail amounts.'])
                ->withInput();
        }

        try {
            DB::beginTransaction();

            $income = Income::create([
                'company_id' => $company->id,
                'number' => $request->number,
                'date' => $request->date,
                'customer_id' => $request->customer_id,
                'note' => $request->note,
                'total' => $request->total,
                'receipt_account_id' => $request->receipt_account_id,
                'description' => $request->description,
            ]);

            // Create income details
            foreach ($request->details as $detail) {
                IncomeDetail::create([
                    'income_id' => $income->id,
                    'account_id' => $detail['account_id'],
                    'value' => $detail['amount'],
                    'description' => $detail['description'] ?? null,
                ]);
            }

            // Create general ledger entry
            $generalLedger = GeneralLedger::create([
                'company_id' => $company->id,
                'number' => $income->number,
                'type' => 'Income',
                'date' => $income->date,
                'note' => $income->note,
                'total' => $income->total,
                'reference' => 'INC-' . $income->id,
                'description' => 'Income: ' . $income->number,
                'status' => 'Posted',
            ]);

            // Credit income accounts
            foreach ($request->details as $detail) {
                GeneralLedgerDetail::create([
                    'general_ledger_id' => $generalLedger->id,
                    'account_id' => $detail['account_id'],
                    'type' => 'credit',
                    'value' => $detail['amount'],
                    'debit' => 0,
                    'credit' => $detail['amount'],
                    'description' => $detail['description'] ?? 'Income entry',
                ]);
            }

            // Debit receipt account
            GeneralLedgerDetail::create([
                'general_ledger_id' => $generalLedger->id,
                'account_id' => $request->receipt_account_id,
                'type' => 'debit',
                'value' => $income->total,
                'debit' => $income->total,
                'credit' => 0,
                'description' => 'Receipt for income: ' . $income->number,
            ]);

            DB::commit();

            return redirect()->route('incomes.index')
                ->with('success', 'Income created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to create income. Please try again.')
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Income $income)
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company || $income->company_id != $company->id) {
            abort(403);
        }

        $income->load(['details.account', 'customer', 'receiptAccount']);

        return view('pages.incomes.show', compact('income', 'company'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Income $income)
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company || $income->company_id != $company->id) {
            abort(403);
        }

        $accounts = Account::where('company_id', $company->id)
            ->where('type', 'Revenue')
            ->orderBy('code')
            ->get();

        $receiptAccounts = Account::where('company_id', $company->id)
            ->whereIn('type', ['Asset', 'Liability'])
            ->orderBy('code')
            ->get();

        $customers = Customer::where('company_id', $company->id)
            ->orderBy('name')
            ->get();

        $income->load('details');

        return view('pages.incomes.edit', compact('income', 'accounts', 'receiptAccounts', 'customers', 'company'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Income $income)
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company || $income->company_id != $company->id) {
            abort(403);
        }

        $validator = Validator::make($request->all(), [
            'number' => 'required|string|max:50',
            'date' => 'required|date',
            'customer_id' => 'nullable|exists:customers,id',
            'note' => 'nullable|string|max:500',
            'total' => 'required|numeric|min:0',
            'receipt_account_id' => 'required|exists:accounts,id',
            'description' => 'nullable|string|max:500',
            'details' => 'required|array|min:1',
            'details.*.account_id' => 'required|exists:accounts,id',
            'details.*.amount' => 'required|numeric|min:0',
            'details.*.description' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Validate that total matches sum of details
        $totalAmount = collect($request->details)->sum('amount');
        if (abs($totalAmount - $request->total) > 0.01) {
            return redirect()->back()
                ->withErrors(['total' => 'Total amount must equal the sum of detail amounts.'])
                ->withInput();
        }

        try {
            DB::beginTransaction();

            $income->update([
                'number' => $request->number,
                'date' => $request->date,
                'customer_id' => $request->customer_id,
                'note' => $request->note,
                'total' => $request->total,
                'receipt_account_id' => $request->receipt_account_id,
                'description' => $request->description,
            ]);

            // Delete existing details
            $income->details()->delete();

            // Create new income details
            foreach ($request->details as $detail) {
                IncomeDetail::create([
                    'income_id' => $income->id,
                    'account_id' => $detail['account_id'],
                    'value' => $detail['amount'],
                    'description' => $detail['description'] ?? null,
                ]);
            }

            // Update general ledger entry
            $generalLedger = GeneralLedger::where('reference', 'INC-' . $income->id)->first();
            if ($generalLedger) {
                $generalLedger->update([
                    'number' => $income->number,
                    'date' => $income->date,
                    'note' => $income->note,
                    'total' => $income->total,
                    'description' => 'Income: ' . $income->number,
                ]);

                // Delete existing general ledger details
                $generalLedger->details()->delete();

                // Credit income accounts
                foreach ($request->details as $detail) {
                    GeneralLedgerDetail::create([
                        'general_ledger_id' => $generalLedger->id,
                        'account_id' => $detail['account_id'],
                        'type' => 'credit',
                        'value' => $detail['amount'],
                        'debit' => 0,
                        'credit' => $detail['amount'],
                        'description' => $detail['description'] ?? 'Income entry',
                    ]);
                }

                // Debit receipt account
                GeneralLedgerDetail::create([
                    'general_ledger_id' => $generalLedger->id,
                    'account_id' => $request->receipt_account_id,
                    'type' => 'debit',
                    'value' => $income->total,
                    'debit' => $income->total,
                    'credit' => 0,
                    'description' => 'Receipt for income: ' . $income->number,
                ]);
            }

            DB::commit();

            return redirect()->route('incomes.index')
                ->with('success', 'Income updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to update income. Please try again.')
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Income $income)
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company || $income->company_id != $company->id) {
            abort(403);
        }

        // Check if income has general ledger entries
        $generalLedger = GeneralLedger::where('reference', 'INC-' . $income->id)->first();
        if ($generalLedger) {
            return redirect()->back()
                ->with('error', 'Cannot delete income. It has associated general ledger entries.');
        }

        try {
            DB::beginTransaction();

            // Delete income details first
            $income->details()->delete();
            
            // Delete the income
            $income->delete();

            DB::commit();

            return redirect()->route('incomes.index')
                ->with('success', 'Income deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to delete income. Please try again.');
        }
    }
}
