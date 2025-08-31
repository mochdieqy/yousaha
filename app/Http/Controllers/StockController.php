<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\StockDetail;
use App\Models\StockHistory;
use App\Models\Product;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class StockController extends Controller
{
    /**
     * Display a listing of stocks.
     */
    public function index(Request $request)
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company) {
            return redirect()->route('company.choice')->with('error', 'Please select a company first.');
        }

        $query = Stock::with(['product', 'warehouse'])
            ->where('company_id', $company->id);
        
        // Handle search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('product', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            })->orWhereHas('warehouse', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        // Handle warehouse filter
        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        // Handle product filter
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        // Handle stock status filter
        if ($request->filled('stock_status')) {
            switch ($request->stock_status) {
                case 'low':
                    $query->where('quantity_saleable', '<=', 10);
                    break;
                case 'out':
                    $query->where('quantity_saleable', '<=', 0);
                    break;
                case 'normal':
                    $query->where('quantity_saleable', '>', 10);
                    break;
            }
        }
        
        $stocks = $query->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        // Get filter options
        $warehouses = Warehouse::where('company_id', $company->id)->orderBy('name')->get();
        $products = Product::where('company_id', $company->id)->orderBy('name')->get();

        return view('pages.stock.index', compact('stocks', 'company', 'warehouses', 'products', 'request'));
    }

    /**
     * Show the form for creating a new stock.
     */
    public function create()
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company) {
            return redirect()->route('company.choice')->with('error', 'Please select a company first.');
        }

        $products = Product::where('company_id', $company->id)->orderBy('name')->get();
        $warehouses = Warehouse::where('company_id', $company->id)->orderBy('name')->get();

        return view('pages.stock.create', compact('company', 'products', 'warehouses'));
    }

    /**
     * Store a newly created stock in storage.
     */
    public function store(Request $request)
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company) {
            return redirect()->route('company.choice')->with('error', 'Please select a company first.');
        }

        $validator = Validator::make($request->all(), [
            'warehouse_id' => 'required|exists:warehouses,id',
            'product_id' => 'required|exists:products,id',
            'quantity_total' => 'required|numeric|min:0',
            'quantity_reserve' => 'nullable|numeric|min:0',
            'quantity_incoming' => 'nullable|numeric|min:0',
            'details' => 'nullable|array',
            'details.*.quantity' => 'required_with:details|numeric|min:0',
            'details.*.code' => 'nullable|string|max:100',
            'details.*.cost' => 'nullable|numeric|min:0',
            'details.*.reference' => 'nullable|string|max:255',
            'details.*.expiration_date' => 'nullable|date|after:today',
        ]);

        $validator->setAttributeNames([
            'warehouse_id' => 'warehouse',
            'product_id' => 'product',
            'quantity_total' => 'total quantity',
            'quantity_reserve' => 'reserved quantity',
            'quantity_incoming' => 'incoming quantity',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Check if stock already exists for this product in this warehouse
        $existingStock = Stock::where('company_id', $company->id)
            ->where('warehouse_id', $request->warehouse_id)
            ->where('product_id', $request->product_id)
            ->first();

        if ($existingStock) {
            return redirect()->back()
                ->withErrors(['product_id' => 'Stock already exists for this product in the selected warehouse. Please update the existing stock instead.'])
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // Calculate quantities
            $quantityTotal = $request->quantity_total;
            $quantityReserve = $request->quantity_reserve ?? 0;
            $quantityIncoming = $request->quantity_incoming ?? 0;
            $quantitySaleable = $quantityTotal - $quantityReserve;

            // Create stock record
            $stock = Stock::create([
                'company_id' => $company->id,
                'warehouse_id' => $request->warehouse_id,
                'product_id' => $request->product_id,
                'quantity_total' => $quantityTotal,
                'quantity_reserve' => $quantityReserve,
                'quantity_saleable' => $quantitySaleable,
                'quantity_incoming' => $quantityIncoming,
            ]);

            // Create stock details if provided
            if ($request->filled('details')) {
                foreach ($request->details as $detail) {
                    if ($detail['quantity'] > 0) {
                        StockDetail::create([
                            'stock_id' => $stock->id,
                            'quantity' => $detail['quantity'],
                            'code' => $detail['code'] ?? null,
                            'cost' => $detail['cost'] ?? null,
                            'reference' => $detail['reference'] ?? null,
                            'expiration_date' => $detail['expiration_date'] ?? null,
                        ]);
                    }
                }
            }

            // Create stock history
            StockHistory::create([
                'stock_id' => $stock->id,
                'quantity_total_before' => 0,
                'quantity_total_after' => $quantityTotal,
                'quantity_reserve_before' => 0,
                'quantity_reserve_after' => $quantityReserve,
                'quantity_saleable_before' => 0,
                'quantity_saleable_after' => $quantitySaleable,
                'quantity_incoming_before' => 0,
                'quantity_incoming_after' => $quantityIncoming,
                'type' => 'initial',
                'reference' => 'Initial stock creation',
                'date' => now(),
            ]);

            DB::commit();

            return redirect()->route('stocks.index')
                ->with('success', 'Stock created successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Failed to create stock: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show the form for editing the specified stock.
     */
    public function edit(Stock $stock)
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company) {
            return redirect()->route('company.choice')->with('error', 'Please select a company first.');
        }

        // Check if stock belongs to current company
        if ($stock->company_id != $company->id) {
            return redirect()->route('stocks.index')
                ->with('error', 'Stock not found.');
        }

        $products = Product::where('company_id', $company->id)->orderBy('name')->get();
        $warehouses = Warehouse::where('company_id', $company->id)->orderBy('name')->get();

        return view('pages.stock.edit', compact('stock', 'company', 'products', 'warehouses'));
    }

    /**
     * Update the specified stock in storage.
     */
    public function update(Request $request, Stock $stock)
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company) {
            return redirect()->route('company.choice')->with('error', 'Please select a company first.');
        }

        // Check if stock belongs to current company
        if ($stock->company_id != $company->id) {
            return redirect()->route('stocks.index')
                ->with('error', 'Stock not found.');
        }

        $validator = Validator::make($request->all(), [
            'warehouse_id' => 'required|exists:warehouses,id',
            'product_id' => 'required|exists:products,id',
            'quantity_total' => 'required|numeric|min:0',
            'quantity_reserve' => 'nullable|numeric|min:0',
            'quantity_incoming' => 'nullable|numeric|min:0',
            'details' => 'nullable|array',
            'details.*.quantity' => 'required_with:details|numeric|min:0',
            'details.*.code' => 'nullable|string|max:100',
            'details.*.cost' => 'nullable|numeric|min:0',
            'details.*.reference' => 'nullable|string|max:255',
            'details.*.expiration_date' => 'nullable|date|after:today',
        ]);

        $validator->setAttributeNames([
            'warehouse_id' => 'warehouse',
            'product_id' => 'product',
            'quantity_total' => 'total quantity',
            'quantity_reserve' => 'reserved quantity',
            'quantity_incoming' => 'incoming quantity',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Check if stock already exists for this product in this warehouse (excluding current stock)
        $existingStock = Stock::where('company_id', $company->id)
            ->where('warehouse_id', $request->warehouse_id)
            ->where('product_id', $request->product_id)
            ->where('id', '!=', $stock->id)
            ->first();

        if ($existingStock) {
            return redirect()->route('stocks.index')
                ->with('error', 'Stock already exists for this product in the selected warehouse.')
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // Store old values for history
            $oldQuantityTotal = $stock->quantity_total;
            $oldQuantityReserve = $stock->quantity_reserve;
            $oldQuantitySaleable = $stock->quantity_saleable;
            $oldQuantityIncoming = $stock->quantity_incoming;

            // Calculate new quantities
            $quantityTotal = $request->quantity_total;
            $quantityReserve = $request->quantity_reserve ?? 0;
            $quantityIncoming = $request->quantity_incoming ?? 0;
            $quantitySaleable = $quantityTotal - $quantityReserve;

            // Update stock record
            $stock->update([
                'warehouse_id' => $request->warehouse_id,
                'product_id' => $request->product_id,
                'quantity_total' => $quantityTotal,
                'quantity_reserve' => $quantityReserve,
                'quantity_saleable' => $quantitySaleable,
                'quantity_incoming' => $quantityIncoming,
            ]);

            // Update stock details
            if ($request->filled('details')) {
                // Delete existing details
                $stock->details()->delete();
                
                // Create new details
                foreach ($request->details as $detail) {
                    if ($detail['quantity'] > 0) {
                        StockDetail::create([
                            'stock_id' => $stock->id,
                            'quantity' => $detail['quantity'],
                            'code' => $detail['code'] ?? null,
                            'cost' => $detail['cost'] ?? null,
                            'reference' => $detail['reference'] ?? null,
                            'expiration_date' => $detail['expiration_date'] ?? null,
                        ]);
                    }
                }
            }

            // Create stock history
            StockHistory::create([
                'stock_id' => $stock->id,
                'quantity_total_before' => $oldQuantityTotal,
                'quantity_total_after' => $quantityTotal,
                'quantity_reserve_before' => $oldQuantityReserve,
                'quantity_reserve_after' => $quantityReserve,
                'quantity_saleable_before' => $oldQuantitySaleable,
                'quantity_saleable_after' => $quantitySaleable,
                'quantity_incoming_before' => $oldQuantityIncoming,
                'quantity_incoming_after' => $quantityIncoming,
                'type' => 'adjustment',
                'reference' => 'Stock adjustment',
                'date' => now(),
            ]);

            DB::commit();

            return redirect()->route('stocks.index')
                ->with('success', 'Stock updated successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Failed to update stock: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified stock from storage.
     */
    public function destroy(Stock $stock)
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company) {
            return redirect()->route('company.choice')->with('error', 'Please select a company first.');
        }

        // Check if stock belongs to current company
        if ($stock->company_id != $company->id) {
            return redirect()->route('stocks.index')
                ->with('error', 'Stock not found.');
        }

        try {
            DB::beginTransaction();

            // Delete stock details
            $stock->details()->delete();
            
            // Delete stock history
            $stock->histories()->delete();
            
            // Delete stock
            $stock->delete();

            DB::commit();

            return redirect()->route('stocks.index')
                ->with('success', 'Stock deleted successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('stocks.index')
                ->with('error', 'Failed to delete stock: ' . $e->getMessage());
        }
    }

    /**
     * Show stock details and history.
     */
    public function show(Stock $stock)
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company) {
            return redirect()->route('company.choice')->with('error', 'Please select a company first.');
        }

        // Check if stock belongs to current company
        if ($stock->company_id != $company->id) {
            return redirect()->route('stocks.index')
                ->with('error', 'Stock not found.');
        }

        $stock->load(['product', 'warehouse', 'details', 'histories']);

        return view('pages.stock.show', compact('stock', 'company'));
    }
}
