<?php

namespace App\Http\Controllers;

use App\Models\InternalTransfer;
use App\Models\GeneralLedger;
use App\Models\GeneralLedgerDetail;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class InternalTransferController extends Controller
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

        $internalTransfers = InternalTransfer::where('company_id', $company->id)
            ->with(['accountOut', 'accountIn'])
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('pages.internal-transfers.index', compact('internalTransfers'));
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
            ->whereIn('type', ['Asset', 'Liability', 'Equity'])
            ->orderBy('code')
            ->get();

        return view('pages.internal-transfers.create', compact('accounts'));
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
            'account_out' => 'required|exists:accounts,id',
            'account_in' => 'required|exists:accounts,id|different:account_out',
            'value' => 'required|numeric|min:0',
            'fee' => 'nullable|numeric|min:0',
            'fee_charged_to' => 'nullable|in:in,out',
            'note' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            $internalTransfer = InternalTransfer::create([
                'company_id' => $company->id,
                'number' => $request->number,
                'date' => $request->date,
                'account_out' => $request->account_out,
                'account_in' => $request->account_in,
                'value' => $request->value,
                'fee' => $request->fee ?? 0,
                'fee_charged_to' => $request->fee_charged_to ?? 'out',
                'note' => $request->note,
            ]);

            // Create general ledger entry
            $generalLedger = GeneralLedger::create([
                'company_id' => $company->id,
                'number' => $internalTransfer->number,
                'type' => 'Internal Transfer',
                'date' => $internalTransfer->date,
                'note' => $internalTransfer->note,
                'total' => $internalTransfer->value,
                'reference' => 'TRF-' . $internalTransfer->id,
                'description' => 'Internal Transfer: ' . $internalTransfer->number,
                'status' => 'Posted',
            ]);

            // Debit receiving account
            GeneralLedgerDetail::create([
                'general_ledger_id' => $generalLedger->id,
                'account_id' => $request->account_in,
                'type' => 'debit',
                'value' => $request->value,
                'debit' => $request->value,
                'credit' => 0,
                'description' => 'Transfer in: ' . $internalTransfer->number,
            ]);

            // Credit sending account
            GeneralLedgerDetail::create([
                'general_ledger_id' => $generalLedger->id,
                'account_id' => $request->account_out,
                'type' => 'credit',
                'value' => $request->value,
                'debit' => 0,
                'credit' => $request->value,
                'description' => 'Transfer out: ' . $internalTransfer->number,
            ]);

            // Update account balances
            $this->updateAccountBalances($request->account_in, $request->account_out, $request->value);

            DB::commit();

            return redirect()->route('internal-transfers.index')
                ->with('success', 'Internal transfer created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to create internal transfer. Please try again.')
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(InternalTransfer $internalTransfer)
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company || $internalTransfer->company_id !== $company->id) {
            abort(403);
        }

        $internalTransfer->load(['accountOut', 'accountIn']);

        return view('pages.internal-transfers.show', compact('internalTransfer'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(InternalTransfer $internalTransfer)
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company || $internalTransfer->company_id !== $company->id) {
            abort(403);
        }

        $accounts = Account::where('company_id', $company->id)
            ->whereIn('type', ['Asset', 'Liability', 'Equity'])
            ->orderBy('code')
            ->get();

        return view('pages.internal-transfers.edit', compact('internalTransfer', 'accounts'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, InternalTransfer $internalTransfer)
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company || $internalTransfer->company_id !== $company->id) {
            abort(403);
        }

        $validator = Validator::make($request->all(), [
            'number' => 'required|string|max:50',
            'date' => 'required|date',
            'account_out' => 'required|exists:accounts,id',
            'account_in' => 'required|exists:accounts,id|different:account_out',
            'value' => 'required|numeric|min:0',
            'fee' => 'nullable|numeric|min:0',
            'fee_charged_to' => 'nullable|in:in,out',
            'note' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            $internalTransfer->update([
                'number' => $request->number,
                'date' => $request->date,
                'account_out' => $request->account_out,
                'account_in' => $request->account_in,
                'value' => $request->value,
                'fee' => $request->fee ?? 0,
                'fee_charged_to' => $request->fee_charged_to ?? 'out',
                'note' => $request->note,
            ]);

            // Update general ledger entry
            $generalLedger = GeneralLedger::where('reference', 'TRF-' . $internalTransfer->id)->first();
            if ($generalLedger) {
                $generalLedger->update([
                    'number' => $internalTransfer->number,
                    'date' => $internalTransfer->date,
                    'note' => $internalTransfer->note,
                    'total' => $internalTransfer->value,
                    'description' => 'Internal Transfer: ' . $internalTransfer->number,
                ]);

                // Delete existing general ledger details
                $generalLedger->details()->delete();

                // Debit receiving account
                GeneralLedgerDetail::create([
                    'general_ledger_id' => $generalLedger->id,
                    'account_id' => $request->account_in,
                    'type' => 'debit',
                    'value' => $request->value,
                    'debit' => $request->value,
                    'credit' => 0,
                    'description' => 'Transfer in: ' . $internalTransfer->number,
                ]);

                // Credit sending account
                GeneralLedgerDetail::create([
                    'general_ledger_id' => $generalLedger->id,
                    'account_id' => $request->account_out,
                    'type' => 'credit',
                    'value' => $request->value,
                    'debit' => 0,
                    'credit' => $request->value,
                    'description' => 'Transfer out: ' . $internalTransfer->number,
                ]);
            }

            // Update account balances
            $this->updateAccountBalances($request->account_in, $request->account_out, $request->value);

            DB::commit();

            return redirect()->route('internal-transfers.index')
                ->with('success', 'Internal transfer updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to update internal transfer. Please try again.')
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(InternalTransfer $internalTransfer)
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company || $internalTransfer->company_id !== $company->id) {
            abort(403);
        }

        // Check if internal transfer has general ledger entries
        $generalLedger = GeneralLedger::where('reference', 'TRF-' . $internalTransfer->id)->first();
        if ($generalLedger) {
            return redirect()->back()
                ->with('error', 'Cannot delete internal transfer. It has associated general ledger entries.');
        }

        try {
            // Reverse the account balances before deleting
            $this->reverseAccountBalances($internalTransfer->account_in, $internalTransfer->account_out, $internalTransfer->value);

            $internalTransfer->delete();

            return redirect()->route('internal-transfers.index')
                ->with('success', 'Internal transfer deleted successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delete internal transfer. Please try again.');
        }
    }

    /**
     * Update account balances after internal transfer
     */
    private function updateAccountBalances($accountInId, $accountOutId, $amount)
    {
        try {
            // Get the accounts
            $accountIn = Account::find($accountInId);
            $accountOut = Account::find($accountOutId);

            if ($accountIn && $accountOut) {
                // Update receiving account (debit - increases asset/expense, decreases liability/equity/revenue)
                $this->updateAccountBalance($accountIn, $amount, 'debit');

                // Update sending account (credit - decreases asset/expense, increases liability/equity/revenue)
                $this->updateAccountBalance($accountOut, $amount, 'credit');
            }
        } catch (\Exception $e) {
            \Log::error('Failed to update account balances for internal transfer: ' . $e->getMessage());
        }
    }

    /**
     * Update individual account balance based on account type and entry type
     */
    private function updateAccountBalance($account, $amount, $entryType)
    {
        $currentBalance = $account->balance;
        $newBalance = $currentBalance;

        // Calculate new balance based on account type and entry type
        switch ($account->type) {
            case 'Asset':
            case 'Expense':
                // Assets and Expenses increase with debit, decrease with credit
                if ($entryType === 'debit') {
                    $newBalance = $currentBalance + $amount;
                } else {
                    $newBalance = $currentBalance - $amount;
                }
                break;

            case 'Liability':
            case 'Equity':
            case 'Revenue':
                // Liabilities, Equity, and Revenue decrease with debit, increase with credit
                if ($entryType === 'debit') {
                    $newBalance = $currentBalance - $amount;
                } else {
                    $newBalance = $currentBalance + $amount;
                }
                break;
        }

        // Update the account balance
        $account->update(['balance' => $newBalance]);
    }

    /**
     * Reverse account balances when internal transfer is deleted
     */
    private function reverseAccountBalances($accountInId, $accountOutId, $amount)
    {
        try {
            // Get the accounts
            $accountIn = Account::find($accountInId);
            $accountOut = Account::find($accountOutId);

            if ($accountIn && $accountOut) {
                // Reverse receiving account (was debited, now credit it back)
                $this->updateAccountBalance($accountIn, $amount, 'credit');

                // Reverse sending account (was credited, now debit it back)
                $this->updateAccountBalance($accountOut, $amount, 'debit');
            }
        } catch (\Exception $e) {
            \Log::error('Failed to reverse account balances for internal transfer deletion: ' . $e->getMessage());
        }
    }
}
