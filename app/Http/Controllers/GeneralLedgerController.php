<?php

namespace App\Http\Controllers;

use App\Models\GeneralLedger;
use App\Models\GeneralLedgerDetail;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class GeneralLedgerController extends Controller
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

        $generalLedgers = GeneralLedger::where('company_id', $company->id)
            ->with(['details.account'])
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('pages.general-ledger.index', compact('generalLedgers'));
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
            ->orderBy('code')
            ->get();

        return view('pages.general-ledger.create', compact('accounts'));
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
            'type' => 'required|string|max:50',
            'date' => 'required|date',
            'note' => 'nullable|string|max:500',
            'total' => 'required|numeric|min:0',
            'reference' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:500',
            'status' => 'required|string|max:50',
            'entries' => 'required|array|min:2',
            'entries.*.account_id' => 'required|exists:accounts,id',
            'entries.*.type' => 'required|in:debit,credit',
            'entries.*.value' => 'required|numeric|min:0',
            'entries.*.description' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Validate that debits equal credits
        $debitTotal = 0;
        $creditTotal = 0;
        
        foreach ($request->entries as $entry) {
            if ($entry['type'] === 'debit') {
                $debitTotal += $entry['value'];
            } else {
                $creditTotal += $entry['value'];
            }
        }

        if (abs($debitTotal - $creditTotal) > 0.01) {
            return redirect()->back()
                ->withErrors(['entries' => 'Total debits must equal total credits.'])
                ->withInput();
        }

        try {
            DB::beginTransaction();

            $generalLedger = GeneralLedger::create([
                'company_id' => $company->id,
                'number' => $request->number,
                'type' => $request->type,
                'date' => $request->date,
                'note' => $request->note,
                'total' => $request->total,
                'reference' => $request->reference,
                'description' => $request->description,
                'status' => $request->status,
            ]);

            // Create general ledger details
            foreach ($request->entries as $entry) {
                GeneralLedgerDetail::create([
                    'general_ledger_id' => $generalLedger->id,
                    'account_id' => $entry['account_id'],
                    'type' => $entry['type'],
                    'value' => $entry['value'],
                    'debit' => $entry['type'] === 'debit' ? $entry['value'] : 0,
                    'credit' => $entry['type'] === 'credit' ? $entry['value'] : 0,
                    'description' => $entry['description'] ?? null,
                ]);
            }

            DB::commit();

            return redirect()->route('general-ledger.index')
                ->with('success', 'General ledger created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to create general ledger. Please try again.')
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(GeneralLedger $generalLedger)
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company || $generalLedger->company_id !== $company->id) {
            abort(403);
        }

        $generalLedger->load(['details.account', 'company']);

        return view('pages.general-ledger.show', compact('generalLedger'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(GeneralLedger $generalLedger)
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company || $generalLedger->company_id !== $company->id) {
            abort(403);
        }

        $accounts = Account::where('company_id', $company->id)
            ->orderBy('code')
            ->get();

        $generalLedger->load('details');

        return view('pages.general-ledger.edit', compact('generalLedger', 'accounts'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, GeneralLedger $generalLedger)
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company || $generalLedger->company_id !== $company->id) {
            abort(403);
        }

        $validator = Validator::make($request->all(), [
            'number' => 'required|string|max:50',
            'type' => 'required|string|max:50',
            'date' => 'required|date',
            'note' => 'nullable|string|max:500',
            'total' => 'required|numeric|min:0',
            'reference' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:500',
            'status' => 'required|string|max:50',
            'entries' => 'required|array|min:2',
            'entries.*.account_id' => 'required|exists:accounts,id',
            'entries.*.type' => 'required|in:debit,credit',
            'entries.*.value' => 'required|numeric|min:0',
            'entries.*.description' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Validate that debits equal credits
        $debitTotal = 0;
        $creditTotal = 0;
        
        foreach ($request->entries as $entry) {
            if ($entry['type'] === 'debit') {
                $debitTotal += $entry['value'];
            } else {
                $creditTotal += $entry['value'];
            }
        }

        if (abs($debitTotal - $creditTotal) > 0.01) {
            return redirect()->back()
                ->withErrors(['entries' => 'Total debits must equal total credits.'])
                ->withInput();
        }

        try {
            DB::beginTransaction();

            $generalLedger->update([
                'number' => $request->number,
                'type' => $request->type,
                'date' => $request->date,
                'note' => $request->note,
                'total' => $request->total,
                'reference' => $request->reference,
                'description' => $request->description,
                'status' => $request->status,
            ]);

            // Delete existing details
            $generalLedger->details()->delete();

            // Create new general ledger details
            foreach ($request->entries as $entry) {
                GeneralLedgerDetail::create([
                    'general_ledger_id' => $generalLedger->id,
                    'account_id' => $entry['account_id'],
                    'type' => $entry['type'],
                    'value' => $entry['value'],
                    'debit' => $entry['type'] === 'debit' ? $entry['value'] : 0,
                    'credit' => $entry['type'] === 'credit' ? $entry['value'] : 0,
                    'description' => $entry['description'] ?? null,
                ]);
            }

            DB::commit();

            return redirect()->route('general-ledger.index')
                ->with('success', 'General ledger updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to update general ledger. Please try again.')
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(GeneralLedger $generalLedger)
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company || $generalLedger->company_id !== $company->id) {
            abort(403);
        }

        try {
            DB::beginTransaction();

            // Delete general ledger details first
            $generalLedger->details()->delete();
            
            // Delete the general ledger
            $generalLedger->delete();

            DB::commit();

            return redirect()->route('general-ledger.index')
                ->with('success', 'General ledger deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to delete general ledger. Please try again.');
        }
    }

    /**
     * Export general ledger for a specific period
     */
    public function export(Request $request)
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company) {
            return redirect()->route('company.choice')->with('error', 'Please select a company first.');
        }

        $validator = Validator::make($request->all(), [
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        $generalLedgers = GeneralLedger::where('company_id', $company->id)
            ->whereBetween('date', [$request->start_date, $request->end_date])
            ->with(['details.account'])
            ->orderBy('date')
            ->orderBy('number')
            ->get();

        // Generate CSV content
        $filename = 'general_ledger_' . $request->start_date . '_to_' . $request->end_date . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($generalLedgers) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, ['Date', 'Number', 'Type', 'Account', 'Debit', 'Credit', 'Description', 'Reference']);
            
            foreach ($generalLedgers as $ledger) {
                foreach ($ledger->details as $detail) {
                    fputcsv($file, [
                        $ledger->date->format('Y-m-d'),
                        $ledger->number,
                        $ledger->type,
                        $detail->account->code . ' - ' . $detail->account->name,
                        $detail->type === 'debit' ? $detail->value : '',
                        $detail->type === 'credit' ? $detail->value : '',
                        $detail->description ?? '',
                        $ledger->reference ?? '',
                    ]);
                }
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
