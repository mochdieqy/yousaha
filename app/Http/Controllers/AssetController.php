<?php

namespace App\Http\Controllers;

use App\Models\Asset;
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

        $query = Asset::where('company_id', $company->id)
            ->with(['accountAsset']);

        // Apply search filter
        if (request('search')) {
            $search = request('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('number', 'like', "%{$search}%")
                  ->orWhere('reference', 'like', "%{$search}%");
            });
        }

        // Apply location filter
        if (request('location')) {
            $query->where('location', request('location'));
        }

        $assets = $query->orderBy('purchased_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('pages.assets.index', compact('assets', 'company'));
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

        return view('pages.assets.create', compact('assetAccounts', 'company'));
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

            Asset::create([
                'company_id' => $company->id,
                'name' => $request->name,
                'number' => $request->number,
                'purchased_date' => $request->purchased_date,
                'account_asset' => $request->account_asset,
                'quantity' => $request->quantity,
                'location' => $request->location,
                'reference' => $request->reference,
            ]);

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
        
        if (!$company || $asset->company_id != $company->id) {
            abort(403);
        }

        $asset->load(['accountAsset']);

        return view('pages.assets.show', compact('asset', 'company'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Asset $asset)
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company || $asset->company_id != $company->id) {
            abort(403);
        }

        $assetAccounts = Account::where('company_id', $company->id)
            ->where('type', 'Asset')
            ->orderBy('code')
            ->get();

        return view('pages.assets.edit', compact('asset', 'assetAccounts', 'company'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Asset $asset)
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company || $asset->company_id != $company->id) {
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
        
        if (!$company || $asset->company_id != $company->id) {
            abort(403);
        }

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
