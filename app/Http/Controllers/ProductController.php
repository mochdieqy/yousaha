<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Display a listing of products.
     */
    public function index(Request $request)
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company) {
            return redirect()->route('company.choice')->with('error', 'Please select a company first.');
        }

        $query = Product::where('company_id', $company->id)
            ->with(['stocks' => function($query) {
                $query->select('id', 'product_id', 'warehouse_id', 'quantity_saleable');
            }]);
        
        // Handle search with optimized query
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('reference', 'like', "%{$search}%");
            });
        }
        
        // Handle type filter
        if ($request->filled('type') && in_array($request->type, ['goods', 'service', 'combo'])) {
            $query->where('type', $request->type);
        }
        
        $products = $query->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('pages.product.index', compact('products', 'company', 'request'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create()
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company) {
            return redirect()->route('company.choice')->with('error', 'Please select a company first.');
        }

        return view('pages.product.create', compact('company'));
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(Request $request)
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company) {
            return redirect()->route('company.choice')->with('error', 'Please select a company first.');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:100|unique:products,sku,NULL,id,company_id,' . $company->id,
            'type' => 'required|in:goods,service,combo',
            'is_track_inventory' => 'boolean',
            'price' => 'required|numeric|min:0',
            'taxes' => 'nullable|numeric|min:0',
            'cost' => 'nullable|numeric|min:0',
            'barcode' => 'nullable|string|max:100',
            'reference' => 'nullable|string|max:255',
            'is_shrink' => 'boolean',
        ]);

        // Custom validation: Service products cannot track inventory
        $validator->after(function ($validator) use ($request) {
            if ($request->type === 'service' && $request->has('is_track_inventory')) {
                $validator->errors()->add('is_track_inventory', 'Service products cannot track inventory.');
            }
        });

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();
            
            $product = Product::create([
                'company_id' => $company->id,
                'name' => trim($request->name),
                'sku' => trim($request->sku),
                'type' => $request->type,
                'is_track_inventory' => $request->has('is_track_inventory'),
                'price' => $request->price,
                'taxes' => $request->taxes ?? 0,
                'cost' => $request->cost,
                'barcode' => $request->barcode ? trim($request->barcode) : null,
                'reference' => $request->reference ? trim($request->reference) : null,
                'is_shrink' => $request->has('is_shrink'),
            ]);

            DB::commit();

            return redirect()->route('products.index')
                ->with('success', 'Product "' . $product->name . '" created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Failed to create product. Please try again.')
                ->withInput();
        }
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(Product $product)
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company) {
            return redirect()->route('company.choice')->with('error', 'Please select a company first.');
        }

        // Check if product belongs to current company
        if ($product->company_id != $company->id) {
            abort(403, 'Unauthorized access to product.');
        }

        return view('pages.product.edit', compact('product', 'company'));
    }

    /**
     * Update the specified product in storage.
     */
    public function update(Request $request, Product $product)
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company) {
            return redirect()->route('company.choice')->with('error', 'Please select a company first.');
        }

        // Check if product belongs to current company
        if ($product->company_id != $company->id) {
            abort(403, 'Unauthorized access to product.');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:100|unique:products,sku,' . $product->id . ',id,company_id,' . $company->id,
            'type' => 'required|in:goods,service,combo',
            'is_track_inventory' => 'boolean',
            'price' => 'required|numeric|min:0',
            'taxes' => 'nullable|numeric|min:0',
            'cost' => 'nullable|numeric|min:0',
            'barcode' => 'nullable|string|max:100',
            'reference' => 'nullable|string|max:255',
            'is_shrink' => 'boolean',
        ]);

        // Custom validation: Service products cannot track inventory
        $validator->after(function ($validator) use ($request) {
            if ($request->type === 'service' && $request->has('is_track_inventory')) {
                $validator->errors()->add('is_track_inventory', 'Service products cannot track inventory.');
            }
        });

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();
            
            $product->update([
                'name' => trim($request->name),
                'sku' => trim($request->sku),
                'type' => $request->type,
                'is_track_inventory' => $request->has('is_track_inventory'),
                'price' => $request->price,
                'taxes' => $request->taxes ?? 0,
                'cost' => $request->cost,
                'barcode' => $request->barcode ? trim($request->barcode) : null,
                'reference' => $request->reference ? trim($request->reference) : null,
                'is_shrink' => $request->has('is_shrink'),
            ]);

            DB::commit();

            return redirect()->route('products.index')
                ->with('success', 'Product "' . $product->name . '" updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Failed to update product. Please try again.')
                ->withInput();
        }
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy(Product $product)
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company) {
            return redirect()->route('company.choice')->with('error', 'Please select a company first.');
        }

        // Check if product belongs to current company
        if ($product->company_id != $company->id) {
            abort(403, 'Unauthorized access to product.');
        }

        try {
            // Check if product is used in any transactions
            if ($product->salesOrderLines()->exists() || 
                $product->purchaseOrderLines()->exists() || 
                $product->receiptLines()->exists() || 
                $product->deliveryLines()->exists() || 
                $product->stocks()->exists()) {
                return redirect()->back()
                    ->with('error', 'Cannot delete product "' . $product->name . '". It is being used in transactions or has stock records.');
            }

            $productName = $product->name;
            $product->delete();

            return redirect()->route('products.index')
                ->with('success', 'Product "' . $productName . '" deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delete product. Please try again.');
        }
    }
}
