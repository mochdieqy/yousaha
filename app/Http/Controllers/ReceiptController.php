<?php

namespace App\Http\Controllers;

use App\Models\Receipt;
use App\Models\ReceiptProductLine;
use App\Models\ReceiptStatusLog;
use App\Models\Stock;
use App\Models\StockDetail;
use App\Models\StockHistory;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ReceiptController extends Controller
{
    /**
     * Display a listing of receipts.
     */
    public function index()
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company) {
            return redirect()->route('company.choice')->with('error', 'Please select a company first.');
        }

        $receipts = Receipt::with(['supplier', 'warehouse', 'productLines.product'])
            ->where('company_id', $company->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('pages.receipt.index', compact('receipts'));
    }

    /**
     * Show the form for creating a new receipt.
     */
    public function create()
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company) {
            return redirect()->route('company.choice')->with('error', 'Please select a company first.');
        }

        $suppliers = Supplier::where('company_id', $company->id)->get();
        $products = Product::where('company_id', $company->id)->get();
        $warehouses = Warehouse::where('company_id', $company->id)->get();
        
        return view('pages.receipt.create', compact('suppliers', 'products', 'warehouses'));
    }

    /**
     * Store a newly created receipt in storage.
     */
    public function store(Request $request)
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company) {
            return redirect()->route('company.choice')->with('error', 'Please select a company first.');
        }

        $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'receive_from' => 'required|exists:suppliers,id',
            'scheduled_at' => 'required|date',
            'reference' => 'nullable|string|max:255',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|numeric|min:0.01',
        ]);

        try {
            DB::beginTransaction();

            // Create receipt
            $receipt = Receipt::create([
                'company_id' => $company->id,
                'warehouse_id' => $request->warehouse_id,
                'receive_from' => $request->receive_from,
                'scheduled_at' => $request->scheduled_at,
                'reference' => $request->reference,
                'status' => 'draft',
            ]);

            // Create product lines
            foreach ($request->products as $productData) {
                ReceiptProductLine::create([
                    'receipt_id' => $receipt->id,
                    'product_id' => $productData['product_id'],
                    'quantity' => $productData['quantity'],
                ]);
            }

            // Create initial status log
            ReceiptStatusLog::create([
                'receipt_id' => $receipt->id,
                'status' => 'draft',
                'changed_at' => now(),
            ]);

            // Automatically upsert stock and increase incoming quantities
            foreach ($request->products as $productData) {
                $stock = Stock::where('company_id', $company->id)
                    ->where('product_id', $productData['product_id'])
                    ->where('warehouse_id', $request->warehouse_id)
                    ->first();

                if ($stock) {
                    // Update existing stock - increment incoming quantity
                    $stock->increment('quantity_incoming', $productData['quantity']);
                    $stock->increment('quantity_total', $productData['quantity']);
                } else {
                    // Create new stock record
                    $stock = Stock::create([
                        'company_id' => $company->id,
                        'product_id' => $productData['product_id'],
                        'warehouse_id' => $request->warehouse_id,
                        'quantity_total' => $productData['quantity'],
                        'quantity_incoming' => $productData['quantity'],
                        'quantity_saleable' => 0,
                        'quantity_reserve' => 0,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('receipts.index')
                ->with('success', 'Receipt created successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->with('error', 'Failed to create receipt: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified receipt.
     */
    public function show(Receipt $receipt)
    {
        $receipt->load(['supplier', 'warehouse', 'productLines.product', 'statusLogs']);
        
        return view('pages.receipt.show', compact('receipt'));
    }

    /**
     * Show the form for editing the specified receipt.
     */
    public function edit(Receipt $receipt)
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company) {
            return redirect()->route('company.choice')->with('error', 'Please select a company first.');
        }

        // Check if receipt can be edited
        if (!in_array($receipt->status, ['draft', 'waiting'])) {
            return back()->with('error', 'Receipt cannot be edited in its current status.');
        }

        $suppliers = Supplier::where('company_id', $company->id)->get();
        $products = Product::where('company_id', $company->id)->get();
        $warehouses = Warehouse::where('company_id', $company->id)->get();
        $receipt->load(['warehouse', 'productLines.product']);
        
        return view('pages.receipt.edit', compact('receipt', 'suppliers', 'products', 'warehouses'));
    }

    /**
     * Update the specified receipt in storage.
     */
    public function update(Request $request, Receipt $receipt)
    {
        // Check if receipt can be updated
        if (!in_array($receipt->status, ['draft', 'waiting'])) {
            return back()->with('error', 'Receipt cannot be updated in its current status.');
        }

        $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'receive_from' => 'required|exists:suppliers,id',
            'scheduled_at' => 'required|date',
            'reference' => 'nullable|string|max:255',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|numeric|min:0.01',
        ]);

        try {
            DB::beginTransaction();

            // Store old quantities for stock adjustment
            $oldProductLines = $receipt->productLines()->with('product')->get();
            $oldQuantities = [];
            foreach ($oldProductLines as $line) {
                $key = $line->product_id . '_' . $receipt->warehouse_id;
                $oldQuantities[$key] = $line->quantity;
            }

            // Update receipt
            $receipt->update([
                'warehouse_id' => $request->warehouse_id,
                'receive_from' => $request->receive_from,
                'scheduled_at' => $request->scheduled_at,
                'reference' => $request->reference,
            ]);

            // Delete existing product lines
            $receipt->productLines()->delete();

            // Create new product lines
            foreach ($request->products as $productData) {
                ReceiptProductLine::create([
                    'receipt_id' => $receipt->id,
                    'product_id' => $productData['product_id'],
                    'quantity' => $productData['quantity'],
                ]);
            }

            // Adjust stock quantities based on changes
            $company = Auth::user()->currentCompany;
            
            // First, reverse the old quantities
            foreach ($oldProductLines as $line) {
                $stock = Stock::where('company_id', $company->id)
                    ->where('product_id', $line->product_id)
                    ->where('warehouse_id', $receipt->warehouse_id)
                    ->first();

                if ($stock) {
                    $stock->decrement('quantity_total', $line->quantity);
                    $stock->decrement('quantity_incoming', $line->quantity);
                }
            }

            // Then, add the new quantities
            foreach ($request->products as $productData) {
                $stock = Stock::where('company_id', $company->id)
                    ->where('product_id', $productData['product_id'])
                    ->where('warehouse_id', $request->warehouse_id)
                    ->first();

                if ($stock) {
                    $stock->increment('quantity_total', $productData['quantity']);
                    $stock->increment('quantity_incoming', $productData['quantity']);
                } else {
                    // Create new stock record if it doesn't exist
                    $stock = Stock::create([
                        'company_id' => $company->id,
                        'product_id' => $productData['product_id'],
                        'warehouse_id' => $request->warehouse_id,
                        'quantity_total' => $productData['quantity'],
                        'quantity_incoming' => $productData['quantity'],
                        'quantity_saleable' => 0,
                        'quantity_reserve' => 0,
                    ]);
                }
            }

            // Create status log
            ReceiptStatusLog::create([
                'receipt_id' => $receipt->id,
                'status' => $receipt->status,
                'changed_at' => now(),
            ]);

            DB::commit();

            return redirect()->route('receipts.index')
                ->with('success', 'Receipt updated successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->with('error', 'Failed to update receipt: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified receipt from storage.
     */
    public function destroy(Receipt $receipt)
    {
        // Check if receipt can be deleted
        if ($receipt->status !== 'draft') {
            return back()->with('error', 'Only draft receipts can be deleted.');
        }

        try {
            DB::beginTransaction();

            // Delete related records
            $receipt->productLines()->delete();
            $receipt->statusLogs()->delete();
            $receipt->delete();

            DB::commit();

            return redirect()->route('receipts.index')
                ->with('success', 'Receipt deleted successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to delete receipt: ' . $e->getMessage());
        }
    }

    /**
     * Process goods receiving for a receipt.
     */
    public function goodsReceive(Request $request, Receipt $receipt)
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company) {
            return redirect()->route('company.choice')->with('error', 'Receipt must be in ready status for goods receiving.');
        }

        // Check if receipt is ready for goods receiving
        if ($receipt->status !== 'ready') {
            return back()->with('error', 'Receipt must be in ready status for goods receiving.');
        }

        try {
            DB::beginTransaction();

            // Update receipt status
            $receipt->update(['status' => 'done']);

            // Create status log
            ReceiptStatusLog::create([
                'receipt_id' => $receipt->id,
                'status' => 'done',
                'changed_at' => now(),
            ]);

            // Now move quantities from incoming to saleable and create stock details
            foreach ($receipt->productLines as $productLine) {
                $stock = Stock::where('company_id', $company->id)
                    ->where('product_id', $productLine->product_id)
                    ->where('warehouse_id', $receipt->warehouse_id)
                    ->first();

                if ($stock) {
                    // Move quantity from incoming to saleable
                    $stock->decrement('quantity_incoming', $productLine->quantity);
                    $stock->increment('quantity_saleable', $productLine->quantity);

                    // Create stock detail for saleable quantity
                    StockDetail::create([
                        'stock_id' => $stock->id,
                        'quantity' => $productLine->quantity,
                        'reference' => 'Receipt #' . $receipt->id,
                    ]);

                    // Create stock history for the goods receiving
                    StockHistory::create([
                        'stock_id' => $stock->id,
                        'type' => 'goods_received',
                        'reference' => 'Receipt #' . $receipt->id,
                        'quantity_total_before' => $stock->quantity_total,
                        'quantity_total_after' => $stock->quantity_total,
                        'quantity_incoming_before' => $stock->quantity_incoming + $productLine->quantity,
                        'quantity_incoming_after' => $stock->quantity_incoming,
                        'quantity_saleable_before' => $stock->quantity_saleable - $productLine->quantity,
                        'quantity_saleable_after' => $stock->quantity_saleable,
                        'quantity_reserve_before' => $stock->quantity_reserve,
                        'quantity_reserve_after' => $stock->quantity_reserve,
                        'date' => now(),
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('receipts.index')
                ->with('success', 'Goods received successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to process goods receiving: ' . $e->getMessage());
        }
    }

    /**
     * Update receipt status.
     */
    public function updateStatus(Request $request, Receipt $receipt)
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company) {
            return redirect()->route('company.choice')->with('error', 'Please select a company first.');
        }

        $request->validate([
            'status' => 'required|in:draft,waiting,ready,done,cancel',
        ]);

        // Validate status transition logic
        $currentStatus = $receipt->status;
        $newStatus = $request->status;
        
        // Define allowed status transitions
        $allowedTransitions = [
            'draft' => ['waiting', 'cancel'],
            'waiting' => ['ready', 'cancel'],
            'ready' => ['done', 'cancel'],
            'done' => [], // No further transitions allowed
            'cancel' => ['draft'], // Can reactivate cancelled receipts
        ];
        
        // Check if the transition is allowed
        if (!in_array($newStatus, $allowedTransitions[$currentStatus])) {
            $allowedStatuses = implode(', ', $allowedTransitions[$currentStatus]);
            return back()->with('error', "Invalid status transition. Current status '{$currentStatus}' can only change to: {$allowedStatuses}");
        }

        try {
            DB::beginTransaction();

            $oldStatus = $receipt->status;
            
            // Handle stock adjustments before status change
            if ($newStatus === 'cancel' && $oldStatus !== 'cancel') {
                // Decrease stock quantities when cancelling
                foreach ($receipt->productLines as $productLine) {
                    $stock = Stock::where('company_id', $company->id)
                        ->where('product_id', $productLine->product_id)
                        ->where('warehouse_id', $receipt->warehouse_id)
                        ->first();

                    if ($stock) {
                        $stock->decrement('quantity_incoming', $productLine->quantity);
                        $stock->decrement('quantity_total', $productLine->quantity);
                    }
                }
            } elseif ($oldStatus === 'cancel' && $newStatus === 'draft') {
                // Reactivate cancelled receipt - increase stock quantities back
                foreach ($receipt->productLines as $productLine) {
                    $stock = Stock::where('company_id', $company->id)
                        ->where('product_id', $productLine->product_id)
                        ->where('warehouse_id', $receipt->warehouse_id)
                        ->first();

                    if ($stock) {
                        $stock->increment('quantity_incoming', $productLine->quantity);
                        $stock->increment('quantity_total', $productLine->quantity);
                    }
                }
            } elseif ($newStatus === 'done' && $oldStatus !== 'done') {
                // Move quantities from incoming to saleable when goods are received
                foreach ($receipt->productLines as $productLine) {
                    $stock = Stock::where('company_id', $company->id)
                        ->where('product_id', $productLine->product_id)
                        ->where('warehouse_id', $receipt->warehouse_id)
                        ->first();

                    if ($stock) {
                        // Move quantity from incoming to saleable
                        $stock->decrement('quantity_incoming', $productLine->quantity);
                        $stock->increment('quantity_saleable', $productLine->quantity);

                        // Create stock detail for saleable quantity
                        StockDetail::create([
                            'stock_id' => $stock->id,
                            'quantity' => $productLine->quantity,
                            'reference' => 'Receipt #' . $receipt->id,
                        ]);

                        // Create stock history for the goods receiving
                        StockHistory::create([
                            'stock_id' => $stock->id,
                            'type' => 'goods_received',
                            'reference' => 'Receipt #' . $receipt->id,
                            'quantity_total_before' => $stock->quantity_total,
                            'quantity_total_after' => $stock->quantity_total,
                            'quantity_incoming_before' => $stock->quantity_incoming + $productLine->quantity,
                            'quantity_incoming_after' => $stock->quantity_incoming,
                            'quantity_saleable_before' => $stock->quantity_saleable - $productLine->quantity,
                            'quantity_saleable_after' => $stock->quantity_saleable,
                            'quantity_reserve_before' => $stock->quantity_reserve,
                            'quantity_reserve_after' => $stock->quantity_reserve,
                            'date' => now(),
                        ]);
                    }
                }
            }

            // Update receipt status
            $receipt->update(['status' => $request->status]);

            // Create status log
            ReceiptStatusLog::create([
                'receipt_id' => $receipt->id,
                'status' => $request->status,
                'changed_at' => now(),
            ]);

            DB::commit();

            return back()->with('success', 'Receipt status updated successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to update receipt status: ' . $e->getMessage());
        }
    }
}
