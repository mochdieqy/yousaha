<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SupplierController extends Controller
{
    /**
     * Display a listing of suppliers.
     */
    public function index(Request $request)
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company) {
            return redirect()->route('company.choice')->with('error', 'Please select a company first.');
        }

        $query = Supplier::where('company_id', $company->id);
        
        // Handle search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }
        
        // Handle type filter
        if ($request->filled('type') && in_array($request->type, ['individual', 'company'])) {
            $query->where('type', $request->type);
        }
        
        $suppliers = $query->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('pages.supplier.index', compact('suppliers', 'company', 'request'));
    }

    /**
     * Show the form for creating a new supplier.
     */
    public function create()
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company) {
            return redirect()->route('company.choice')->with('error', 'Please select a company first.');
        }

        return view('pages.supplier.create', compact('company'));
    }

    /**
     * Store a newly created supplier in storage.
     */
    public function store(Request $request)
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company) {
            return redirect()->route('company.choice')->with('error', 'Please select a company first.');
        }

        $validator = Validator::make($request->all(), [
            'type' => 'required|in:individual,company',
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $supplier = Supplier::create([
                'company_id' => $company->id,
                'type' => $request->type,
                'name' => $request->name,
                'address' => $request->address,
                'phone' => $request->phone,
                'email' => $request->email,
            ]);

            return redirect()->route('suppliers.index')
                ->with('success', 'Supplier created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to create supplier: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show the form for editing the specified supplier.
     */
    public function edit(Supplier $supplier)
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company) {
            return redirect()->route('company.choice')->with('error', 'Please select a company first.');
        }

        // Check if supplier belongs to current company
        if ($supplier->company_id !== $company->id) {
            abort(403, 'Unauthorized access to supplier.');
        }

        return view('pages.supplier.edit', compact('supplier', 'company'));
    }

    /**
     * Update the specified supplier in storage.
     */
    public function update(Request $request, Supplier $supplier)
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company) {
            return redirect()->route('company.choice')->with('error', 'Please select a company first.');
        }

        // Check if supplier belongs to current company
        if ($supplier->company_id !== $company->id) {
            abort(403, 'Unauthorized access to supplier.');
        }

        $validator = Validator::make($request->all(), [
            'type' => 'required|in:individual,company',
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $supplier->update([
                'type' => $request->type,
                'name' => $request->name,
                'address' => $request->address,
                'phone' => $request->phone,
                'email' => $request->email,
            ]);

            return redirect()->route('suppliers.index')
                ->with('success', 'Supplier updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to update supplier: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified supplier from storage.
     */
    public function destroy(Supplier $supplier)
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company) {
            return redirect()->route('company.choice')->with('error', 'Please select a company first.');
        }

        // Check if supplier belongs to current company
        if ($supplier->company_id !== $company->id) {
            abort(403, 'Unauthorized access to supplier.');
        }

        try {
            // Check if supplier is used in any transactions
            if ($supplier->purchaseOrders()->exists() || 
                $supplier->receipts()->exists()) {
                return redirect()->back()
                    ->with('error', 'Cannot delete supplier. It is being used in purchase orders or receipts.');
            }

            $supplier->delete();

            return redirect()->route('suppliers.index')
                ->with('success', 'Supplier deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delete supplier: ' . $e->getMessage());
        }
    }
}
