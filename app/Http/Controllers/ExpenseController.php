<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseDetail;
use App\Models\GeneralLedger;
use App\Models\GeneralLedgerDetail;
use App\Models\Account;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company) {
            return redirect()->route('company.choice')->with('error', 'Please select a company first.');
        }

        $query = Expense::where('company_id', $company->id)
            ->with(['details.account', 'supplier', 'paymentAccount']);

        // Apply search filter
        if (request('search')) {
            $search = request('search');
            $query->where(function($q) use ($search) {
                $q->where('number', 'like', "%{$search}%")
                  ->orWhere('note', 'like', "%{$search}%")
                  ->orWhereHas('supplier', function($sq) use ($search) {
                      $sq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Apply date filters
        if (request('date_from')) {
            $query->where('date', '>=', request('date_from'));
        }
        if (request('date_to')) {
            $query->where('date', '<=', request('date_to'));
        }

        $expenses = $query->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('pages.expenses.index', compact('expenses'));
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
            ->where('type', 'Expense')
            ->orderBy('code')
            ->get();

        $paymentAccounts = Account::where('company_id', $company->id)
            ->whereIn('type', ['Asset', 'Liability'])
            ->orderBy('code')
            ->get();

        $suppliers = Supplier::where('company_id', $company->id)
            ->orderBy('name')
            ->get();

        return view('pages.expenses.create', compact('accounts', 'paymentAccounts', 'suppliers'));
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
            'supplier_id' => 'nullable|exists:suppliers,id',
            'note' => 'nullable|string|max:500',
            'payment_account_id' => 'required|exists:accounts,id',
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
        if ($totalAmount <= 0) {
            return redirect()->back()
                ->withErrors(['details' => 'Total amount must be greater than zero.'])
                ->withInput();
        }

        try {
            DB::beginTransaction();

            $expense = Expense::create([
                'company_id' => $company->id,
                'number' => $request->number,
                'date' => $request->date,
                'supplier_id' => $request->supplier_id ?: null,
                'note' => $request->note,
                'total' => $totalAmount,
                'payment_account_id' => $request->payment_account_id,
            ]);

            // Create expense details
            foreach ($request->details as $detail) {
                ExpenseDetail::create([
                    'expense_id' => $expense->id,
                    'account_id' => $detail['account_id'],
                    'amount' => $detail['amount'],
                    'description' => $detail['description'] ?? null,
                ]);
            }

            // Create general ledger entry
            $generalLedger = GeneralLedger::create([
                'company_id' => $company->id,
                'number' => $expense->number,
                'type' => 'Expense',
                'date' => $expense->date,
                'note' => $expense->note,
                'total' => $expense->total,
                'reference' => 'EXP-' . $expense->id,
                'description' => 'Expense: ' . $expense->number,
                'status' => 'Posted',
            ]);

            // Debit expense accounts
            foreach ($request->details as $detail) {
                GeneralLedgerDetail::create([
                    'general_ledger_id' => $generalLedger->id,
                    'account_id' => $detail['account_id'],
                    'type' => 'debit',
                    'value' => $detail['amount'],
                    'debit' => $detail['amount'],
                    'credit' => 0,
                    'description' => $detail['description'] ?? 'Expense entry',
                ]);
            }

            // Credit payment account
            GeneralLedgerDetail::create([
                'general_ledger_id' => $generalLedger->id,
                'account_id' => $request->payment_account_id,
                'type' => 'credit',
                'value' => $expense->total,
                'debit' => 0,
                'credit' => $expense->total,
                'description' => 'Payment for expense: ' . $expense->number,
            ]);

            DB::commit();

            return redirect()->route('expenses.index')
                ->with('success', 'Expense created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to create expense. Please try again.')
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Expense $expense)
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company || $expense->company_id != $company->id) {
            abort(403);
        }

        $expense->load(['details.account', 'supplier', 'paymentAccount', 'company']);

        return view('pages.expenses.show', compact('expense'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Expense $expense)
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company || $expense->company_id != $company->id) {
            abort(403);
        }

        $accounts = Account::where('company_id', $company->id)
            ->where('type', 'Expense')
            ->orderBy('code')
            ->get();

        $paymentAccounts = Account::where('company_id', $company->id)
            ->whereIn('type', ['Asset', 'Liability'])
            ->orderBy('code')
            ->get();

        $suppliers = Supplier::where('company_id', $company->id)
            ->orderBy('name')
            ->get();

        $expense->load('details');

        return view('pages.expenses.edit', compact('expense', 'accounts', 'paymentAccounts', 'suppliers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Expense $expense)
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company || $expense->company_id != $company->id) {
            abort(403);
        }

        $validator = Validator::make($request->all(), [
            'number' => 'required|string|max:50',
            'date' => 'required|date',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'note' => 'nullable|string|max:500',
            'payment_account_id' => 'required|exists:accounts,id',
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
        if ($totalAmount <= 0) {
            return redirect()->back()
                ->withErrors(['details' => 'Total amount must be greater than zero.'])
                ->withInput();
        }

        try {
            DB::beginTransaction();

            $expense->update([
                'number' => $request->number,
                'date' => $request->date,
                'supplier_id' => $request->supplier_id ?: null,
                'note' => $request->note,
                'total' => $totalAmount,
                'payment_account_id' => $request->payment_account_id,
            ]);

            // Delete existing details
            $expense->details()->delete();

            // Create new expense details
            foreach ($request->details as $detail) {
                ExpenseDetail::create([
                    'expense_id' => $expense->id,
                    'account_id' => $detail['account_id'],
                    'amount' => $detail['amount'],
                    'description' => $detail['description'] ?? null,
                ]);
            }

            // Update general ledger entry
            $generalLedger = GeneralLedger::where('reference', 'EXP-' . $expense->id)->first();
            if ($generalLedger) {
                $generalLedger->update([
                    'number' => $expense->number,
                    'date' => $expense->date,
                    'note' => $expense->note,
                    'total' => $expense->total,
                    'description' => 'Expense: ' . $expense->number,
                ]);

                // Delete existing general ledger details
                $generalLedger->details()->delete();

                // Debit expense accounts
                foreach ($request->details as $detail) {
                    GeneralLedgerDetail::create([
                        'general_ledger_id' => $generalLedger->id,
                        'account_id' => $detail['account_id'],
                        'type' => 'debit',
                        'value' => $detail['amount'],
                        'debit' => $detail['amount'],
                        'credit' => 0,
                        'description' => $detail['description'] ?? 'Expense entry',
                    ]);
                }

                // Credit payment account
                GeneralLedgerDetail::create([
                    'general_ledger_id' => $generalLedger->id,
                    'account_id' => $request->payment_account_id,
                    'type' => 'credit',
                    'value' => $expense->total,
                    'debit' => 0,
                    'credit' => $expense->total,
                    'description' => 'Payment for expense: ' . $expense->number,
                ]);
            }

            DB::commit();

            return redirect()->route('expenses.index')
                ->with('success', 'Expense updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to update expense. Please try again.')
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Expense $expense)
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company || $expense->company_id != $company->id) {
            abort(403);
        }

        // Check if expense has general ledger entries
        $generalLedger = GeneralLedger::where('reference', 'EXP-' . $expense->id)->first();
        if ($generalLedger) {
            return redirect()->back()
                ->with('error', 'Cannot delete expense. It has associated general ledger entries.');
        }

        try {
            DB::beginTransaction();

            // Delete expense details first
            $expense->details()->delete();
            
            // Delete the expense
            $expense->delete();

            DB::commit();

            return redirect()->route('expenses.index')
                ->with('success', 'Expense deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to delete expense. Please try again.');
        }
    }
}
