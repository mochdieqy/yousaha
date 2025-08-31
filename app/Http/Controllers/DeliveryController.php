<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Models\DeliveryProductLine;
use App\Models\DeliveryStatusLog;
use App\Models\Product;
use App\Models\Stock;
use App\Models\StockDetail;
use App\Models\StockHistory;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class DeliveryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company) {
            return redirect()->route('company.choice')
                ->with('error', 'Please select a company before viewing deliveries.');
        }

        $query = Delivery::with(['warehouse', 'productLines.product'])
            ->where('company_id', $company->id);

        // Apply search filter
        if (request('search')) {
            $search = request('search');
            $query->where(function($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                  ->orWhere('delivery_address', 'like', "%{$search}%")
                  ->orWhere('id', 'like', "%{$search}%");
            });
        }

        // Apply status filter
        if (request('status')) {
            $query->where('status', request('status'));
        }

        // Apply warehouse filter
        if (request('warehouse_id')) {
            $query->where('warehouse_id', request('warehouse_id'));
        }

        $deliveries = $query->orderBy('created_at', 'desc')->paginate(10);
        $warehouses = Warehouse::where('company_id', $company->id)->get();

        return view('pages.delivery.index', compact('deliveries', 'warehouses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company) {
            return redirect()->route('company.choice')
                ->with('error', 'Please select a company before creating deliveries.');
        }

        $warehouses = Warehouse::where('company_id', $company->id)->get();
        $products = Product::where('company_id', $company->id)->get();

        // Check if required data exists
        if ($warehouses->isEmpty()) {
            return redirect()->route('warehouses.index')
                ->with('error', 'No warehouses found. Please create at least one warehouse before creating deliveries.');
        }

        if ($products->isEmpty()) {
            return redirect()->route('products.index')
                ->with('error', 'No products found. Please create at least one product before creating deliveries.');
        }

        return view('pages.delivery.create', compact('warehouses', 'products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company) {
            return redirect()->route('company.choice')
                ->with('error', 'Please select a company before creating deliveries.');
        }

        $validator = Validator::make($request->all(), [
            'warehouse_id' => 'required|exists:warehouses,id',
            'delivery_address' => 'required|string|max:255',
            'scheduled_at' => 'required|date|after:now',
            'reference' => 'nullable|string|max:255',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // Create delivery
            $delivery = Delivery::create([
                'company_id' => $company->id,
                'warehouse_id' => $request->warehouse_id,
                'delivery_address' => $request->delivery_address,
                'scheduled_at' => $request->scheduled_at,
                'reference' => $request->reference,
                'status' => 'draft',
            ]);

            // Create delivery product lines
            foreach ($request->products as $productData) {
                DeliveryProductLine::create([
                    'delivery_id' => $delivery->id,
                    'product_id' => $productData['product_id'],
                    'quantity' => $productData['quantity'],
                ]);
            }

            // Check stock availability and update status automatically
            $this->checkStockAvailabilityAndUpdateStatus($delivery);

            DB::commit();

            // Create final status log after transaction is committed
            try {
                if ($delivery->status !== 'draft') {
                    DeliveryStatusLog::create([
                        'delivery_id' => $delivery->id,
                        'status' => $delivery->status,
                        'changed_at' => now(),
                    ]);
                }
            } catch (\Exception $e) {
                // Don't fail the whole process for status log creation
            }

            $statusMessage = $delivery->status === 'ready' 
                ? 'Delivery created successfully and marked as ready. Stock has been reserved.'
                : 'Delivery created successfully and marked as waiting due to insufficient stock.';

            return redirect()->route('deliveries.index')
                ->with('success', $statusMessage);

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->with('error', 'Failed to create delivery: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Delivery $delivery)
    {
        $delivery->load(['warehouse', 'productLines.product', 'statusLogs']);
        
        return view('pages.delivery.show', compact('delivery'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Delivery $delivery)
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company) {
            return redirect()->route('company.choice')
                ->with('error', 'Please select a company before editing deliveries.');
        }

        // Check if delivery can be edited
        if (!in_array($delivery->status, ['draft', 'waiting'])) {
            return back()->with('error', 'Delivery cannot be edited in its current status.');
        }

        $warehouses = Warehouse::where('company_id', $company->id)->get();
        $products = Product::where('company_id', $company->id)->get();
        $delivery->load('productLines.product');

        // Check if required data exists
        if ($warehouses->isEmpty()) {
            return redirect()->route('warehouses.index')
                ->with('error', 'No warehouses found. Please create at least one warehouse before editing deliveries.');
        }

        if ($products->isEmpty()) {
            return redirect()->route('products.index')
                ->with('error', 'No products found. Please create at least one product before editing deliveries.');
        }

        return view('pages.delivery.edit', compact('delivery', 'warehouses', 'products'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Delivery $delivery)
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company) {
            return redirect()->route('company.choice')
                ->with('error', 'Please select a company before updating deliveries.');
        }

        // Check if delivery can be updated
        if (!in_array($delivery->status, ['draft', 'waiting'])) {
            return back()->with('error', 'Delivery cannot be updated in its current status.');
        }

        $validator = Validator::make($request->all(), [
            'warehouse_id' => 'required|exists:warehouses,id',
            'delivery_address' => 'required|string|max:255',
            'scheduled_at' => 'required|date|after:now',
            'reference' => 'nullable|string|max:255',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // Update delivery
            $delivery->update([
                'warehouse_id' => $request->warehouse_id,
                'delivery_address' => $request->delivery_address,
                'scheduled_at' => $request->scheduled_at,
                'reference' => $request->reference,
            ]);

            // Update product lines
            $delivery->productLines()->delete();
            foreach ($request->products as $productData) {
                DeliveryProductLine::create([
                    'delivery_id' => $delivery->id,
                    'product_id' => $productData['product_id'],
                    'quantity' => $productData['quantity'],
                ]);
            }

            // Create status log
            DeliveryStatusLog::create([
                'delivery_id' => $delivery->id,
                'status' => $delivery->status,
                'changed_at' => now(),
            ]);

            DB::commit();

            return redirect()->route('deliveries.index')
                ->with('success', 'Delivery updated successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->with('error', 'Failed to update delivery: ' . $e->getMessage());
        }
    }

    /**
     * Update delivery status
     */
    public function updateStatus(Request $request, Delivery $delivery)
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company) {
            return redirect()->route('company.choice')
                ->with('error', 'Please select a company before updating delivery status.');
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:draft,waiting,ready,done,cancel',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            $oldStatus = $delivery->status;
            $delivery->update(['status' => $request->status]);

            // Create status log
            DeliveryStatusLog::create([
                'delivery_id' => $delivery->id,
                'status' => $request->status,
                'changed_at' => now(),
            ]);

            // If status is being updated to 'ready', update stock
            if ($request->status === 'ready') {
                $this->updateStockForReadyStatus($delivery);
            }
            
            // If status is being updated to 'done', process goods issue
            if ($request->status === 'done') {
                $this->processGoodsIssue($delivery);
            }

            DB::commit();

            return back()->with('success', 'Delivery status updated successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to update delivery status: ' . $e->getMessage());
        }
    }

    /**
     * Process goods issue
     */
    public function goodsIssue(Request $request, Delivery $delivery)
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company) {
            return redirect()->route('company.choice')
                ->with('error', 'Please select a company before processing goods issue.');
        }

        // Check if delivery can be processed
        if ($delivery->status !== 'ready') {
            return back()->with('error', 'Only deliveries with ready status can be processed.');
        }

        $validator = Validator::make($request->all(), [
            'validate' => 'required|boolean',
            'cancel' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            if ($request->cancel) {
                $delivery->update(['status' => 'cancel']);
                
                DeliveryStatusLog::create([
                    'delivery_id' => $delivery->id,
                    'status' => 'cancel',
                    'changed_at' => now(),
                ]);
            } else {
                $delivery->update(['status' => 'done']);
                
                DeliveryStatusLog::create([
                    'delivery_id' => $delivery->id,
                    'status' => 'done',
                    'changed_at' => now(),
                ]);

                // Update stock
                $this->processGoodsIssue($delivery);
            }

            DB::commit();

            $message = $request->cancel ? 'Delivery cancelled successfully.' : 'Goods issued successfully.';
            return back()->with('success', $message);

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to process goods issue: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Delivery $delivery)
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company) {
            return redirect()->route('company.choice')
                ->with('error', 'Please select a company before deleting deliveries.');
        }

        // Check if delivery can be deleted
        if ($delivery->status !== 'draft') {
            return back()->with('error', 'Only draft deliveries can be deleted.');
        }

        try {
            $delivery->delete();
            return redirect()->route('deliveries.index')
                ->with('success', 'Delivery deleted successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete delivery: ' . $e->getMessage());
        }
    }

    /**
     * Update stock when delivery status changes to ready
     */
    private function updateStockForReadyStatus(Delivery $delivery)
    {
        foreach ($delivery->productLines as $productLine) {
            $stock = Stock::where('company_id', $delivery->company_id)
                ->where('warehouse_id', $delivery->warehouse_id)
                ->where('product_id', $productLine->product_id)
                ->first();

            if ($stock) {
                // Check if there's enough saleable stock
                if ($stock->quantity_saleable < $productLine->quantity) {
                    throw new \Exception("Insufficient saleable stock for product: {$productLine->product->name}");
                }
            }
        }
    }

    /**
     * Check stock availability and automatically update delivery status
     */
    private function checkStockAvailabilityAndUpdateStatus(Delivery $delivery)
    {
        $allStockAvailable = true;
        $stockUpdates = [];

        // Check stock availability for all product lines
        foreach ($delivery->productLines as $productLine) {
            $stock = Stock::where('company_id', $delivery->company_id)
                ->where('warehouse_id', $delivery->warehouse_id)
                ->where('product_id', $productLine->product_id)
                ->first();

            if (!$stock || $stock->quantity_saleable < $productLine->quantity) {
                $allStockAvailable = false;
                break;
            }

            // Store stock updates for later processing
            $stockUpdates[] = [
                'stock' => $stock,
                'quantity' => $productLine->quantity
            ];
        }

        // Update status based on stock availability
        if ($allStockAvailable) {
            // Stock is available - change status to ready and reserve stock
            $delivery->update(['status' => 'ready']);
            
            // Update stock quantities
            foreach ($stockUpdates as $update) {
                $stock = $update['stock'];
                $quantity = $update['quantity'];
                
                $stock->update([
                    'quantity_saleable' => $stock->quantity_saleable - $quantity,
                    'quantity_reserve' => $stock->quantity_reserve + $quantity
                ]);

                // Create stock history for reservation
                StockHistory::create([
                    'stock_id' => $stock->id,
                    'type' => 'reserve',
                    'quantity_total_before' => $stock->quantity_total,
                    'quantity_total_after' => $stock->quantity_total,
                    'quantity_reserve_before' => $stock->quantity_reserve,
                    'quantity_reserve_after' => $stock->quantity_reserve + $quantity,
                    'quantity_saleable_before' => $stock->quantity_saleable,
                    'quantity_saleable_after' => $stock->quantity_saleable - $quantity,
                    'quantity_incoming_before' => $stock->quantity_incoming,
                    'quantity_incoming_after' => $stock->quantity_incoming,
                    'reference' => 'Delivery #' . $delivery->id,
                    'date' => now(),
                ]);
            }
        } else {
            // Stock not available - change status to waiting
            $delivery->update(['status' => 'waiting']);
        }
    }

    /**
     * Check and update waiting deliveries when stock becomes available
     */
    public function checkWaitingDeliveriesForStockAvailability()
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company) {
            return;
        }

        // Get all waiting deliveries for the current company
        $waitingDeliveries = Delivery::with('productLines.product')
            ->where('company_id', $company->id)
            ->where('status', 'waiting')
            ->get();

        foreach ($waitingDeliveries as $delivery) {
            $this->checkStockAvailabilityAndUpdateStatus($delivery);
        }
    }

    /**
     * Manually check stock availability for a specific delivery
     */
    public function checkStockAvailability(Delivery $delivery)
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company) {
            return back()->with('error', 'Please select a company before checking stock availability.');
        }

        // Check if delivery belongs to current company
        if ($delivery->company_id != $company->id) {
            return back()->with('error', 'Delivery not found.');
        }

        // Check stock availability and update status
        $this->checkStockAvailabilityAndUpdateStatus($delivery);

        $statusMessage = $delivery->status === 'ready' 
            ? 'Stock is now available. Delivery status updated to ready and stock has been reserved.'
            : 'Stock is still insufficient. Delivery remains in waiting status.';

        return back()->with('success', $statusMessage);
    }

    /**
     * Process goods issue - update stock quantities
     */
    private function processGoodsIssue(Delivery $delivery)
    {
        foreach ($delivery->productLines as $productLine) {
            $stock = Stock::where('company_id', $delivery->company_id)
                ->where('warehouse_id', $delivery->warehouse_id)
                ->where('product_id', $productLine->product_id)
                ->first();

            if ($stock) {
                // Update stock quantities - decrease reserved and total
                $stock->update([
                    'quantity_reserve' => $stock->quantity_reserve - $productLine->quantity,
                    'quantity_total' => $stock->quantity_total - $productLine->quantity
                ]);

                // Delete stock details (FIFO)
                $remainingQuantity = $productLine->quantity;
                $stockDetails = StockDetail::where('stock_id', $stock->id)
                    ->orderBy('created_at', 'asc')
                    ->get();

                foreach ($stockDetails as $stockDetail) {
                    if ($remainingQuantity <= 0) break;
                    
                    if ($stockDetail->quantity <= $remainingQuantity) {
                        $remainingQuantity -= $stockDetail->quantity;
                        $stockDetail->delete();
                    } else {
                        $stockDetail->update([
                            'quantity' => $stockDetail->quantity - $remainingQuantity
                        ]);
                        $remainingQuantity = 0;
                    }
                }

                // Create stock history
                StockHistory::create([
                    'stock_id' => $stock->id,
                    'type' => 'out',
                    'quantity_total_before' => $stock->quantity_total + $productLine->quantity,
                    'quantity_total_after' => $stock->quantity_total,
                    'quantity_reserve_before' => $stock->quantity_reserve + $productLine->quantity,
                    'quantity_reserve_after' => $stock->quantity_reserve,
                    'quantity_saleable_before' => $stock->quantity_saleable,
                    'quantity_saleable_after' => $stock->quantity_saleable,
                    'quantity_incoming_before' => $stock->quantity_incoming,
                    'quantity_incoming_after' => $stock->quantity_incoming,
                    'reference' => 'Delivery #' . $delivery->id,
                    'date' => now(),
                ]);
            }
        }
    }

    /**
     * Cancel delivery and release reserved stock
     */
    public function cancelDelivery(Request $request, Delivery $delivery)
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company) {
            return redirect()->route('company.choice')
                ->with('error', 'Please select a company before cancelling deliveries.');
        }

        // Check if delivery can be cancelled
        if ($delivery->status !== 'ready') {
            return back()->with('error', 'Only deliveries with ready status can be cancelled.');
        }

        try {
            DB::beginTransaction();

            // Update delivery status to cancelled
            $delivery->update(['status' => 'cancel']);

            // Create status log
            DeliveryStatusLog::create([
                'delivery_id' => $delivery->id,
                'status' => 'cancel',
                'changed_at' => now(),
            ]);

            // Release reserved stock back to saleable inventory
            $this->releaseReservedStock($delivery);

            DB::commit();

            return back()->with('success', 'Delivery cancelled successfully. Reserved stock has been released back to saleable inventory.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to cancel delivery: ' . $e->getMessage());
        }
    }

    /**
     * Release reserved stock back to saleable inventory
     */
    private function releaseReservedStock(Delivery $delivery)
    {
        foreach ($delivery->productLines as $productLine) {
            $stock = Stock::where('company_id', $delivery->company_id)
                ->where('warehouse_id', $delivery->warehouse_id)
                ->where('product_id', $productLine->product_id)
                ->first();

            if ($stock) {
                // Release reserved stock back to saleable
                $stock->update([
                    'quantity_saleable' => $stock->quantity_saleable + $productLine->quantity,
                    'quantity_reserve' => $stock->quantity_reserve - $productLine->quantity
                ]);

                // Create stock history for stock release
                StockHistory::create([
                    'stock_id' => $stock->id,
                    'type' => 'release',
                    'quantity_total_before' => $stock->quantity_total,
                    'quantity_total_after' => $stock->quantity_total,
                    'quantity_reserve_before' => $stock->quantity_reserve + $productLine->quantity,
                    'quantity_reserve_after' => $stock->quantity_reserve,
                    'quantity_saleable_before' => $stock->quantity_saleable - $productLine->quantity,
                    'quantity_saleable_after' => $stock->quantity_saleable,
                    'quantity_incoming_before' => $stock->quantity_incoming,
                    'quantity_incoming_after' => $stock->quantity_incoming,
                    'reference' => 'Delivery #' . $delivery->id . ' (Cancelled)',
                    'date' => now(),
                ]);
            }
        }
    }
}
