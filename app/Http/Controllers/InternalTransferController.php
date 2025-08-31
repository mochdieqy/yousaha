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
use App\Services\AccountBalanceService;

class InternalTransferController extends Controller
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

        $query = InternalTransfer::where('company_id', $company->id)
            ->with(['accountOut', 'accountIn']);

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('number', 'like', "%{$search}%")
                  ->orWhere('note', 'like', "%{$search}%")
                  ->orWhereHas('accountOut', function($q) use ($search) {
                      $q->where('code', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('accountIn', function($q) use ($search) {
                      $q->where('code', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%");
                  });
            });
        }

        // Apply account type filter
        if ($request->filled('account_type')) {
            $accountType = $request->account_type;
            $query->where(function($q) use ($accountType) {
                $q->whereHas('accountOut', function($q) use ($accountType) {
                    $q->where('type', $accountType);
                })->orWhereHas('accountIn', function($q) use ($accountType) {
                    $q->where('type', $accountType);
                });
            });
        }

        // Apply date filter
        if ($request->filled('date_from')) {
            $query->where('date', '>=', $request->date_from);
        }

        $internalTransfers = $query->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

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

            $this->createGeneralLedgerEntry($internalTransfer);

            // Update account balances using AccountBalanceService
            AccountBalanceService::updateBalancesForTransaction($company->id, [
                ['account_id' => $request->account_in, 'type' => 'debit', 'value' => $request->value],
                ['account_id' => $request->account_out, 'type' => 'credit', 'value' => $request->value],
            ]);

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

            $this->updateGeneralLedgerEntry($internalTransfer);

            // Update account balances using AccountBalanceService
            AccountBalanceService::updateBalancesForTransaction($company->id, [
                ['account_id' => $request->account_in, 'type' => 'debit', 'value' => $request->value],
                ['account_id' => $request->account_out, 'type' => 'credit', 'value' => $request->value],
            ]);

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
            // Reverse the account balances before deleting using AccountBalanceService
            AccountBalanceService::reverseBalancesForTransaction($company->id, [
                ['account_id' => $internalTransfer->account_in, 'type' => 'credit', 'value' => $internalTransfer->value],
                ['account_id' => $internalTransfer->account_out, 'type' => 'debit', 'value' => $internalTransfer->value],
            ]);

            $internalTransfer->delete();

            return redirect()->route('internal-transfers.index')
                ->with('success', 'Internal transfer deleted successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delete internal transfer. Please try again.');
        }
    }

    /**
     * Create general ledger entry for internal transfer
     */
    private function createGeneralLedgerEntry(InternalTransfer $internalTransfer): void
    {
        $generalLedger = GeneralLedger::create([
            'company_id' => $internalTransfer->company_id,
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
            'account_id' => $internalTransfer->account_in,
            'type' => 'debit',
            'value' => $internalTransfer->value,
            'debit' => $internalTransfer->value,
            'credit' => 0,
            'description' => 'Transfer in: ' . $internalTransfer->number,
        ]);

        // Credit sending account
        GeneralLedgerDetail::create([
            'general_ledger_id' => $generalLedger->id,
            'account_id' => $internalTransfer->account_out,
            'type' => 'credit',
            'value' => $internalTransfer->value,
            'debit' => 0,
            'credit' => $internalTransfer->value,
            'description' => 'Transfer out: ' . $internalTransfer->number,
        ]);
    }

    /**
     * Update general ledger entry for internal transfer
     */
    private function updateGeneralLedgerEntry(InternalTransfer $internalTransfer): void
    {
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
                'account_id' => $internalTransfer->account_in,
                'type' => 'debit',
                'value' => $internalTransfer->value,
                'debit' => $internalTransfer->value,
                'credit' => 0,
                'description' => 'Transfer in: ' . $internalTransfer->number,
            ]);

            // Credit sending account
            GeneralLedgerDetail::create([
                'general_ledger_id' => $generalLedger->id,
                'account_id' => $internalTransfer->account_out,
                'type' => 'credit',
                'value' => $internalTransfer->value,
                'debit' => 0,
                'credit' => $internalTransfer->value,
                'description' => 'Transfer out: ' . $internalTransfer->number,
            ]);
        }
    }
}
