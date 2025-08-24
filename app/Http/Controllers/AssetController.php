<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\GeneralLedger;
use App\Models\GeneralLedgerDetail;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AssetController extends Controller
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

        $assets = Asset::where('company_id', $company->id)
            ->with(['accountAsset'])
            ->orderBy('purchased_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('pages.assets.index', compact('assets'));
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

        $assetAccounts = Account::where('company_id', $company->id)
            ->where('type', 'Asset')
            ->orderBy('code')
            ->get();

        return view('pages.assets.create', compact('assetAccounts'));
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
            'name' => 'required|string|max:100',
            'number' => 'required|string|max:50',
            'purchased_date' => 'required|date',
            'account_asset' => 'required|exists:accounts,id',
            'quantity' => 'required|integer|min:1',
            'location' => 'nullable|string|max:100',
            'reference' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            $asset = Asset::create([
                'company_id' => $company->id,
                'name' => $request->name,
                'number' => $request->number,
                'purchased_date' => $request->purchased_date,
                'account_asset' => $request->account_asset,
                'quantity' => $request->quantity,
                'location' => $request->location,
                'reference' => $request->reference,
            ]);

            // Create general ledger entry (commented out since assets don't store amounts)
            // $generalLedger = GeneralLedger::create([
            //     'company_id' => $company->id,
            //     'number' => $asset->number,
            //     'type' => 'Asset Purchase',
            //     'date' => $asset->purchased_date,
            //     'note' => 'Asset: ' . $asset->name,
            //     'total' => 0, // Assets don't store amount in the table
            //     'reference' => 'AST-' . $asset->id,
            //     'description' => 'Asset Purchase: ' . $asset->name,
            //     'status' => 'Posted',
            // ]);

            // Debit asset account (commented out since no amount available)
            // GeneralLedgerDetail::create([
            //     'general_ledger_id' => $generalLedger->id,
            //     'account_id' => $request->account_asset,
            //     'type' => 'debit',
            //     'value' => $request->amount,
            //     'debit' => $request->amount,
            //     'credit' => 0,
            //     'description' => 'Asset purchase: ' . $asset->name,
            // ]);

            // Credit payment account (commented out since no amount available)
            // GeneralLedgerDetail::create([
            //     'general_ledger_id' => $generalLedger->id,
            //     'account_id' => $request->payment_account_id,
            //     'type' => 'credit',
            //     'value' => $request->amount,
            //     'debit' => 0,
            //     'credit' => $request->amount,
            //     'description' => 'Payment for asset: ' . $asset->name,
            // ]);

            DB::commit();

            return redirect()->route('assets.index')
                ->with('success', 'Asset created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to create asset. Please try again.')
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Asset $asset)
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company || $asset->company_id !== $company->id) {
            abort(403);
        }

        $asset->load(['accountAsset']);

        return view('pages.assets.show', compact('asset'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Asset $asset)
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company || $asset->company_id !== $company->id) {
            abort(403);
        }

        $assetAccounts = Account::where('company_id', $company->id)
            ->where('type', 'Asset')
            ->orderBy('code')
            ->get();

        return view('pages.assets.edit', compact('asset', 'assetAccounts'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Asset $asset)
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company || $asset->company_id !== $company->id) {
            abort(403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'number' => 'required|string|max:50',
            'purchased_date' => 'required|date',
            'account_asset' => 'required|exists:accounts,id',
            'quantity' => 'required|integer|min:1',
            'location' => 'nullable|string|max:100',
            'reference' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            $asset->update([
                'name' => $request->name,
                'number' => $request->number,
                'purchased_date' => $request->purchased_date,
                'account_asset' => $request->account_asset,
                'quantity' => $request->quantity,
                'location' => $request->location,
                'reference' => $request->reference,
            ]);

            // Update general ledger entry (commented out since assets don't store amounts)
            // $generalLedger = GeneralLedger::where('reference', 'AST-' . $asset->id)->first();
            // if ($generalLedger) {
            //     $generalLedger->update([
            //         'number' => $asset->number,
            //         'date' => $asset->purchased_date,
            //         'note' => 'Asset: ' . $asset->name,
            //         'total' => $asset->amount,
            //         'description' => 'Asset Purchase: ' . $asset->name,
            //     ]);

            //     // Delete existing general ledger details
            //     $generalLedger->details()->delete();

            //     // Debit asset account
            //     GeneralLedgerDetail::create([
            //         'general_ledger_id' => $generalLedger->id,
            //         'account_id' => $request->account_asset,
            //         'type' => 'debit',
            //         'value' => $request->amount,
            //         'debit' => $request->amount,
            //         'credit' => 0,
            //         'description' => 'Asset purchase: ' . $asset->name,
            //     ]);

            //     // Credit payment account
            //     GeneralLedgerDetail::create([
            //         'general_ledger_id' => $generalLedger->id,
            //         'account_id' => $request->payment_account_id,
            //         'type' => 'credit',
            //         'value' => $request->amount,
            //         'debit' => 0,
            //         'credit' => $request->amount,
            //         'description' => 'Payment for asset: ' . $asset->name,
            //     ]);
            // }

            DB::commit();

            return redirect()->route('assets.index')
                ->with('success', 'Asset updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to update asset. Please try again.')
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Asset $asset)
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company || $asset->company_id !== $company->id) {
            abort(403);
        }

        // Check if asset has general ledger entries (commented out since assets don't create GL entries)
        // $generalLedger = GeneralLedger::where('reference', 'AST-' . $asset->id)->first();
        // if ($generalLedger) {
        //     return redirect()->back()
        //         ->with('error', 'Cannot delete asset. It has associated general ledger entries.');
        // }

        try {
            $asset->delete();

            return redirect()->route('assets.index')
                ->with('success', 'Asset deleted successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delete asset. Please try again.');
        }
    }
}
