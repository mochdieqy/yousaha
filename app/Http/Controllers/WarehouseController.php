<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class WarehouseController extends Controller
{
    /**
     * Display a listing of warehouses.
     */
    public function index(Request $request)
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company) {
            return redirect()->route('company.choice')->with('error', 'Please select a company first.');
        }

        $query = Warehouse::forCompany($company->id);
        
        // Handle search
        if ($request->filled('search')) {
            $query->search($request->search);
        }
        
        $warehouses = $query->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('pages.warehouse.index', compact('warehouses', 'company', 'request'));
    }

    /**
     * Show the form for creating a new warehouse.
     */
    public function create()
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company) {
            return redirect()->route('company.choice')->with('error', 'Please select a company first.');
        }

        return view('pages.warehouse.create', compact('company'));
    }

    /**
     * Store a newly created warehouse in storage.
     */
    public function store(Request $request)
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company) {
            return redirect()->route('company.choice')->with('error', 'Please select a company first.');
        }

        $validator = Validator::make($request->all(), [
            'code' => 'required|string|max:50|regex:/^[a-zA-Z0-9-_]+$/|unique:warehouses,code,NULL,id,company_id,' . $company->id,
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:1000',
        ]);

        $validator->setAttributeNames([
            'code' => 'warehouse code',
            'name' => 'warehouse name',
            'address' => 'warehouse address',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $warehouse = Warehouse::create([
                'company_id' => $company->id,
                'code' => $request->code,
                'name' => $request->name,
                'address' => $request->address,
            ]);

            return redirect()->route('warehouses.index')
                ->with('success', 'Warehouse created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to create warehouse. Please try again.')
                ->withInput();
        }
    }

    /**
     * Show the form for editing the specified warehouse.
     */
    public function edit(Warehouse $warehouse)
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company) {
            return redirect()->route('company.choice')->with('error', 'Please select a company first.');
        }

        // Check if warehouse belongs to current company
        if ($warehouse->company_id !== $company->id) {
            return redirect()->route('warehouses.index')
                ->with('error', 'Warehouse not found.');
        }

        return view('pages.warehouse.edit', compact('warehouse', 'company'));
    }

    /**
     * Update the specified warehouse in storage.
     */
    public function update(Request $request, Warehouse $warehouse)
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company) {
            return redirect()->route('company.choice')->with('error', 'Please select a company first.');
        }

        // Check if warehouse belongs to current company
        if ($warehouse->company_id !== $company->id) {
            return redirect()->route('warehouses.index')
                ->with('error', 'Warehouse not found.');
        }

        $validator = Validator::make($request->all(), [
            'code' => 'required|string|max:50|regex:/^[a-zA-Z0-9-_]+$/|unique:warehouses,code,' . $warehouse->id . ',id,company_id,' . $company->id,
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:1000',
        ]);

        $validator->setAttributeNames([
            'code' => 'warehouse code',
            'name' => 'warehouse name',
            'address' => 'warehouse address',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $warehouse->update([
                'code' => $request->code,
                'name' => $request->name,
                'address' => $request->address,
            ]);

            return redirect()->route('warehouses.index')
                ->with('success', 'Warehouse updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to update warehouse. Please try again.')
                ->withInput();
        }
    }

    /**
     * Remove the specified warehouse from storage.
     */
    public function destroy(Warehouse $warehouse)
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company) {
            return redirect()->route('company.choice')->with('error', 'Please select a company first.');
        }

        // Check if warehouse belongs to current company
        if ($warehouse->company_id !== $company->id) {
            return redirect()->route('warehouses.index')
                ->with('error', 'Warehouse not found.');
        }

        // Check if warehouse has associated stocks
        if ($warehouse->hasStock()) {
            return redirect()->route('warehouses.index')
                ->with('error', 'Cannot delete warehouse. It has associated stock records.');
        }

        try {
            $warehouse->delete();
            return redirect()->route('warehouses.index')
                ->with('success', 'Warehouse deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('warehouses.index')
                ->with('error', 'Failed to delete warehouse. Please try again.');
        }
    }
}
