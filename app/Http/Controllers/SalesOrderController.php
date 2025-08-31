<?php

namespace App\Http\Controllers;

use App\Models\SalesOrder;
use App\Models\SalesOrderProductLine;
use App\Models\SalesOrderStatusLog;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Stock;
use App\Models\StockDetail;
use App\Models\StockHistory;
use App\Models\Delivery;
use App\Models\DeliveryProductLine;
use App\Models\DeliveryStatusLog;
use App\Models\Income;
use App\Models\GeneralLedger;
use App\Models\GeneralLedgerDetail;
use App\Models\Warehouse;
use App\Models\Receipt;
use App\Models\ReceiptStatusLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\Account;
use Barryvdh\DomPDF\Facade\Pdf;

class SalesOrderController extends Controller
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

        $query = SalesOrder::with(['customer', 'warehouse', 'productLines.product'])
            ->where('company_id', $company->id);

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('number', 'like', "%{$search}%")
                  ->orWhere('salesperson', 'like', "%{$search}%")
                  ->orWhereHas('customer', function($customerQuery) use ($search) {
                      $customerQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Apply status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Apply warehouse filter
        if ($request->filled('warehouse')) {
            $query->where('warehouse_id', $request->warehouse);
        }

        $salesOrders = $query->orderBy('created_at', 'desc')->paginate(15);
        $warehouses = Warehouse::where('company_id', $company->id)->get();

        return view('pages.sales-order.index', compact('salesOrders', 'company', 'warehouses'));
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

        $customers = Customer::where('company_id', $company->id)->get();
        $products = Product::where('company_id', $company->id)->get();
        $warehouses = Warehouse::where('company_id', $company->id)->get();

        return view('pages.sales-order.create', compact('customers', 'products', 'warehouses', 'company'));
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
            'warehouse_id' => 'required|exists:warehouses,id',
            'customer_id' => 'required|exists:customers,id',
            'salesperson' => 'required|string|max:255',
            'activities' => 'nullable|string|max:500',
            'deadline' => 'required|date|after:today',
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

            // Generate sales order number
            $orderNumber = $this->generateOrderNumber($company->id);

            // Calculate total
            $total = $this->calculateTotal($request->products);

            // Create sales order
            $salesOrder = SalesOrder::create([
                'company_id' => $company->id,
                'warehouse_id' => $request->warehouse_id,
                'number' => $orderNumber,
                'customer_id' => $request->customer_id,
                'salesperson' => $request->salesperson,
                'activities' => $request->activities,
                'total' => $total,
                'status' => 'draft',
                'deadline' => $request->deadline,
            ]);

            // Create product lines
            $this->createProductLines($salesOrder, $request->products);

            // Create status log
            $this->createStatusLog($salesOrder, 'draft');

            DB::commit();

            return redirect()->route('sales-orders.index')
                ->with('success', 'Sales order created successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Failed to create sales order: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(SalesOrder $salesOrder)
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company || $salesOrder->company_id !== $company->id) {
            return redirect()->route('sales-orders.index')
                ->with('error', 'Sales order not found.');
        }

        $salesOrder->load(['customer', 'warehouse', 'productLines.product', 'statusLogs']);

        return view('pages.sales-order.show', compact('salesOrder', 'company'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SalesOrder $salesOrder)
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company || $salesOrder->company_id !== $company->id) {
            return redirect()->route('sales-orders.index')
                ->with('error', 'Sales order not found.');
        }

        // Check if sales order can be edited
        if (in_array($salesOrder->status, ['done', 'cancel'])) {
            return redirect()->route('sales-orders.index')
                ->with('error', 'Cannot edit completed or cancelled sales orders.');
        }

        $customers = Customer::where('company_id', $company->id)->get();
        $products = Product::where('company_id', $company->id)->get();
        $warehouses = Warehouse::where('company_id', $company->id)->get();

        $salesOrder->load(['productLines.product']);

        return view('pages.sales-order.edit', compact('salesOrder', 'customers', 'products', 'warehouses', 'company'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SalesOrder $salesOrder)
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company || $salesOrder->company_id !== $company->id) {
            return redirect()->route('sales-orders.index')
                ->with('error', 'Sales order not found.');
        }

        // Check if sales order can be edited
        if (in_array($salesOrder->status, ['done', 'cancel'])) {
            return redirect()->route('sales-orders.index')
                ->with('error', 'Cannot edit completed or cancelled sales orders.');
        }

        $validator = Validator::make($request->all(), [
            'warehouse_id' => 'required|exists:warehouses,id',
            'customer_id' => 'required|exists:customers,id',
            'salesperson' => 'required|string|max:500',
            'activities' => 'nullable|string|max:500',
            'deadline' => 'required|date|after:today',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'status' => 'required|in:draft,waiting,accepted,done,cancel',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // Calculate total
            $total = $this->calculateTotal($request->products);

            // Handle status changes that require additional actions first
            if (in_array($request->status, ['accepted', 'done', 'cancel'])) {
                $this->handleStatusChange($salesOrder, $request->status, $company, $total);
            }

            // Update sales order (status will be updated by handleStatusChange if needed)
            $updateData = [
                'warehouse_id' => $request->warehouse_id,
                'customer_id' => $request->customer_id,
                'salesperson' => $request->salesperson,
                'activities' => $request->activities,
                'total' => $total,
                'deadline' => $request->deadline,
            ];
            
            // Only update status if it wasn't already updated by handleStatusChange
            if ($salesOrder->status !== 'waiting' || $request->status !== 'accepted') {
                $updateData['status'] = $request->status;
            }
            
            $salesOrder->update($updateData);

            // Update product lines for draft and waiting status
            if (in_array($request->status, ['draft', 'waiting'])) {
                $this->updateProductLines($salesOrder, $request->products);
            }

            // Create status log (only if not already created by handleStatusChange)
            if ($salesOrder->status !== 'waiting' || $request->status !== 'accepted') {
                $this->createStatusLog($salesOrder, $request->status);
            }

            DB::commit();

            $message = 'Sales order updated successfully.';
            if ($request->status === 'accepted') {
                $message .= ' Delivery has been created automatically.';
            }

            return redirect()->route('sales-orders.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Failed to update sales order: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SalesOrder $salesOrder)
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company || $salesOrder->company_id !== $company->id) {
            return redirect()->route('sales-orders.index')
                ->with('error', 'Sales order not found.');
        }

        // Check if sales order can be deleted
        if (!in_array($salesOrder->status, ['draft'])) {
            return redirect()->route('sales-orders.index')
                ->with('error', 'Only draft sales orders can be deleted.');
        }

        try {
            DB::beginTransaction();

            // Delete related records
            $salesOrder->productLines()->delete();
            $salesOrder->statusLogs()->delete();
            $salesOrder->delete();

            DB::commit();

            return redirect()->route('sales-orders.index')
                ->with('success', 'Sales order deleted successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Failed to delete sales order: ' . $e->getMessage());
        }
    }

    /**
     * Generate quotation for the sales order.
     */
    public function generateQuotation(SalesOrder $salesOrder)
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company || $salesOrder->company_id !== $company->id) {
            return redirect()->route('sales-orders.index')
                ->with('error', 'Sales order not found.');
        }

        // Check if quotation can be generated
        if ($salesOrder->status !== 'draft') {
            return redirect()->route('sales-orders.index')
                ->with('error', 'Quotation can only be generated for draft sales orders.');
        }

        try {
            // Load the sales order with all necessary relationships
            $salesOrder->load(['customer', 'warehouse', 'productLines.product', 'company']);
            
            // Generate PDF quotation
            $pdf = Pdf::loadView('pdf.quotation', compact('salesOrder'));
            
            // Set paper size and orientation
            $pdf->setPaper('A4', 'portrait');
            
            // Download the PDF with a descriptive filename
            return $pdf->download('quotation-' . $salesOrder->number . '.pdf');
            
        } catch (\Exception $e) {
            return redirect()->route('sales-orders.index')
                ->with('error', 'Failed to generate quotation: ' . $e->getMessage());
        }
    }

    /**
     * Generate invoice for the sales order.
     */
    public function generateInvoice(SalesOrder $salesOrder)
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company || $salesOrder->company_id !== $company->id) {
            return redirect()->route('sales-orders.index')
                ->with('error', 'Sales order not found.');
        }

        // Check if invoice can be generated
        if ($salesOrder->status === 'draft') {
            return redirect()->route('sales-orders.index')
                ->with('error', 'Invoice cannot be generated for draft sales orders.');
        }

        try {
            // Load the sales order with all necessary relationships
            $salesOrder->load(['customer', 'warehouse', 'productLines.product', 'company']);
            
            // Generate PDF invoice
            $pdf = Pdf::loadView('pdf.invoice', compact('salesOrder'));
            
            // Set paper size and orientation
            $pdf->setPaper('A4', 'portrait');
            
            // Download the PDF with a descriptive filename
            return $pdf->download('invoice-' . $salesOrder->number . '.pdf');
            
        } catch (\Exception $e) {
            return redirect()->route('sales-orders.index')
                ->with('error', 'Failed to generate invoice: ' . $e->getMessage());
        }
    }

    /**
     * Update sales order status with business logic validation.
     */
    public function updateStatus(Request $request, SalesOrder $salesOrder)
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company || $salesOrder->company_id !== $company->id) {
            return redirect()->route('sales-orders.index')
                ->with('error', 'Sales order not found.');
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:draft,waiting,accepted,done,cancel',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $newStatus = $request->status;
        $oldStatus = $salesOrder->status;

        try {
            DB::beginTransaction();

            // Handle status change to 'accepted'
            if ($newStatus === 'accepted' && $oldStatus !== 'accepted') {
                $result = $this->handleAcceptedStatus($salesOrder, $company);
                
                if (!$result['success']) {
                    // If stock is insufficient, the status has already been changed to 'waiting'
                    // and the status log has been created, so we can commit and return
                    DB::commit();
                    return redirect()->route('sales-orders.index')
                        ->with('error', $result['message']);
                }
                
                // If successful, update status to accepted and create status log
                $salesOrder->update(['status' => 'accepted']);
                $this->createStatusLog($salesOrder, 'accepted');
            } else {
                // Handle status change to 'done'
                if ($newStatus === 'done' && $oldStatus !== 'done') {
                    $this->handleDoneStatus($salesOrder, $company);
                }

                // Handle status change to 'cancel'
                if ($newStatus === 'cancel' && $oldStatus !== 'cancel') {
                    $this->handleCancelledStatus($salesOrder, $company);
                }

                // Update sales order status for other status changes
                if ($newStatus !== 'accepted') {
                    $salesOrder->update(['status' => $newStatus]);
                    $this->createStatusLog($salesOrder, $newStatus);
                }
            }

            DB::commit();

            $message = 'Sales order status updated successfully.';
            if ($newStatus === 'accepted') {
                $message .= ' Delivery has been created automatically.';
            }

            return redirect()->route('sales-orders.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Failed to update sales order status: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Generate order number for sales order.
     */
    private function generateOrderNumber($companyId)
    {
        $lastOrder = SalesOrder::where('company_id', $companyId)
            ->orderBy('id', 'desc')
            ->first();
        
        $nextId = ($lastOrder ? $lastOrder->id + 1 : 1);
        return 'SO-' . $companyId . '-' . str_pad($nextId, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Calculate total amount for products.
     */
    private function calculateTotal($products)
    {
        $total = 0;
        foreach ($products as $product) {
            $productModel = Product::find($product['product_id']);
            if ($productModel) {
                $total += $productModel->price * $product['quantity'];
            }
        }
        return $total;
    }

    /**
     * Create product lines for sales order.
     */
    private function createProductLines($salesOrder, $products)
    {
        foreach ($products as $product) {
            SalesOrderProductLine::create([
                'sales_order_id' => $salesOrder->id,
                'product_id' => $product['product_id'],
                'quantity' => $product['quantity'],
            ]);
        }
    }

    /**
     * Update product lines for sales order.
     */
    private function updateProductLines($salesOrder, $products)
    {
        // Delete existing product lines
        $salesOrder->productLines()->delete();
        
        // Create new product lines
        $this->createProductLines($salesOrder, $products);
    }

    /**
     * Create status log for sales order.
     */
    private function createStatusLog($salesOrder, $status)
    {
        SalesOrderStatusLog::create([
            'sales_order_id' => $salesOrder->id,
            'status' => $status,
            'changed_at' => now(),
        ]);
    }

    /**
     * Handle status change with business logic.
     */
    private function handleStatusChange($salesOrder, $newStatus, $company, $total)
    {
        // Check stock availability if status is accepted
        if ($newStatus === 'accepted') {
            $stockCheck = $this->checkStockAvailability($salesOrder, $company);
            
            if (!$stockCheck['available']) {
                // Change status to 'waiting' due to insufficient stock
                $salesOrder->update(['status' => 'waiting']);
                
                // Create status log for waiting status
                $this->createStatusLog($salesOrder, 'waiting');

                throw new \Exception('Cannot change sales order status to accepted. Insufficient stock available for products: ' . implode(', ', $stockCheck['insufficient_products']) . '. Status has been automatically changed to waiting.');
            }
            
            // Stock is available, proceed with acceptance
            $this->processAcceptedSalesOrder($salesOrder, $company, $stockCheck['stock_data']);
            
            // Update status to accepted since stock check passed
            $salesOrder->update(['status' => 'accepted']);
            $this->createStatusLog($salesOrder, 'accepted');
        }
        
        // Update stock if status is done
        if ($newStatus === 'done') {
            $this->handleDoneStatus($salesOrder, $company);
        }
        
        // Handle cancelled status: release reserved stock and cancel deliveries
        if ($newStatus === 'cancel') {
            $this->handleCancelledStatus($salesOrder, $company);
        }
    }

    /**
     * Handle sales order status change to 'accepted'.
     */
    private function handleAcceptedStatus(SalesOrder $salesOrder, $company)
    {
        // Check stock availability for all products
        $stockCheck = $this->checkStockAvailability($salesOrder, $company);
        
        if (!$stockCheck['available']) {
            // Change status to 'waiting' due to insufficient stock
            $salesOrder->update(['status' => 'waiting']);
            
            // Create status log for waiting status
            $this->createStatusLog($salesOrder, 'waiting');

            return [
                'success' => false,
                'message' => 'Cannot change sales order status to accepted. Insufficient stock available for products: ' . implode(', ', $stockCheck['insufficient_products']) . '. Status has been automatically changed to waiting.'
            ];
        }

        // Stock is available, proceed with acceptance
        $this->processAcceptedSalesOrder($salesOrder, $company, $stockCheck['stock_data']);

        return ['success' => true];
    }

    /**
     * Check stock availability for sales order products.
     * Only checks stock for products that track inventory.
     */
    private function checkStockAvailability(SalesOrder $salesOrder, $company)
    {
        $available = true;
        $insufficient_products = [];
        $stock_data = [];

        foreach ($salesOrder->productLines as $productLine) {
            // Only check stock for products that track inventory
            if ($productLine->product && $productLine->product->shouldTrackInventory()) {
                $stock = Stock::where('company_id', $company->id)
                    ->where('warehouse_id', $salesOrder->warehouse_id)
                    ->where('product_id', $productLine->product_id)
                    ->first();

                if (!$stock || $stock->quantity_saleable < $productLine->quantity) {
                    $available = false;
                    $insufficient_products[] = $productLine->product->name;
                } else {
                    $stock_data[$productLine->product_id] = [
                        'stock' => $stock,
                        'required_quantity' => $productLine->quantity
                    ];
                }
            }
            // For non-inventory tracking products, skip stock check
        }

        return [
            'available' => $available,
            'insufficient_products' => $insufficient_products,
            'stock_data' => $stock_data
        ];
    }

    /**
     * Process accepted sales order: create delivery and update stock.
     */
    private function processAcceptedSalesOrder(SalesOrder $salesOrder, $company, $stockData)
    {
        // Filter products that track inventory
        $inventoryProductLines = [];
        
        foreach ($salesOrder->productLines as $productLine) {
            if ($productLine->product && $productLine->product->shouldTrackInventory()) {
                $inventoryProductLines[] = $productLine;
            }
        }

        // Create delivery only if there are inventory-tracking products
        if (!empty($inventoryProductLines)) {
            $delivery = Delivery::create([
                'company_id' => $company->id,
                'warehouse_id' => $salesOrder->warehouse_id,
                'delivery_address' => $salesOrder->customer->address ?? 'Customer address',
                'scheduled_at' => now()->addDays(1), // Schedule for tomorrow
                'reference' => $salesOrder->number,
                'status' => 'ready',
                'sales_order_id' => $salesOrder->id,
                'customer_id' => $salesOrder->customer_id,
                'number' => 'DO-' . $company->id . '-' . str_pad($salesOrder->id, 6, '0', STR_PAD_LEFT),
                'notes' => 'Auto-generated from accepted sales order ' . $salesOrder->number,
            ]);

            // Create delivery product lines only for inventory-tracking products
            foreach ($inventoryProductLines as $productLine) {
                DeliveryProductLine::create([
                    'delivery_id' => $delivery->id,
                    'product_id' => $productLine->product_id,
                    'quantity' => $productLine->quantity,
                ]);
            }

            // Create delivery status log
            DeliveryStatusLog::create([
                'delivery_id' => $delivery->id,
                'status' => 'ready',
                'changed_at' => now(),
            ]);
        }

        // Update stock: increase reserved quantity and decrease saleable quantity
        // Only for inventory-tracking products
        foreach ($stockData as $productId => $data) {
            $stock = $data['stock'];
            $requiredQuantity = $data['required_quantity'];

            // Check if this product tracks inventory
            $product = Product::find($productId);
            if ($product && $product->shouldTrackInventory()) {
                $stock->update([
                    'quantity_reserve' => $stock->quantity_reserve + $requiredQuantity,
                    'quantity_saleable' => $stock->quantity_saleable - $requiredQuantity,
                ]);

                // Create stock history for reservation
                StockHistory::create([
                    'stock_id' => $stock->id,
                    'company_id' => $company->id,
                    'warehouse_id' => $salesOrder->warehouse_id,
                    'product_id' => $productId,
                    'quantity_total_before' => $stock->quantity_total,
                    'quantity_total_after' => $stock->quantity_total,
                    'quantity_reserve_before' => $stock->quantity_reserve,
                    'quantity_reserve_after' => $stock->quantity_reserve + $requiredQuantity,
                    'quantity_saleable_before' => $stock->quantity_saleable,
                    'quantity_saleable_after' => $stock->quantity_saleable - $requiredQuantity,
                    'quantity_incoming_before' => $stock->quantity_incoming,
                    'quantity_incoming_after' => $stock->quantity_incoming,
                    'type' => 'reserve',
                    'reference' => 'sales_order',
                    'date' => now()->toDateString(),
                    'notes' => 'Stock reserved for sales order ' . $salesOrder->number,
                ]);
            }
        }
    }

    /**
     * Handle sales order status change to 'done'.
     */
    private function handleDoneStatus(SalesOrder $salesOrder, $company)
    {
        // Filter products that track inventory
        $inventoryProductLines = [];
        
        foreach ($salesOrder->productLines as $productLine) {
            if ($productLine->product && $productLine->product->shouldTrackInventory()) {
                $inventoryProductLines[] = $productLine;
            }
        }

        // Check if delivery already exists
        $existingDelivery = Delivery::where('sales_order_id', $salesOrder->id)->first();
        
        if (!$existingDelivery && !empty($inventoryProductLines)) {
            // Create delivery if it doesn't exist and there are inventory-tracking products
            $delivery = Delivery::create([
                'company_id' => $company->id,
                'warehouse_id' => $salesOrder->warehouse_id,
                'delivery_address' => $salesOrder->customer->address ?? 'Customer address',
                'scheduled_at' => now(),
                'reference' => $salesOrder->number,
                'status' => 'done',
                'sales_order_id' => $salesOrder->id,
                'customer_id' => $salesOrder->customer_id,
                'number' => 'DO-' . $company->id . '-' . str_pad($salesOrder->id, 6, '0', STR_PAD_LEFT),
                'notes' => 'Auto-generated from completed sales order ' . $salesOrder->number,
            ]);

            // Create delivery product lines only for inventory-tracking products
            foreach ($inventoryProductLines as $productLine) {
                DeliveryProductLine::create([
                    'delivery_id' => $delivery->id,
                    'product_id' => $productLine->product_id,
                    'quantity' => $productLine->quantity,
                ]);
            }

            // Create delivery status log
            DeliveryStatusLog::create([
                'delivery_id' => $delivery->id,
                'status' => 'done',
                'changed_at' => now(),
            ]);
        } else if ($existingDelivery) {
            // Update existing delivery status to done
            $existingDelivery->update(['status' => 'done']);
            
            // Create delivery status log if not exists
            $latestStatusLog = $existingDelivery->statusLogs()->latest('changed_at')->first();
            if (!$latestStatusLog || $latestStatusLog->status !== 'done') {
                DeliveryStatusLog::create([
                    'delivery_id' => $existingDelivery->id,
                    'status' => 'done',
                    'changed_at' => now(),
                ]);
            }
        }

        // Update stock: decrease reserved quantity and total quantity
        // Only for inventory-tracking products
        foreach ($inventoryProductLines as $productLine) {
            $stock = Stock::where('company_id', $company->id)
                ->where('warehouse_id', $salesOrder->warehouse_id)
                ->where('product_id', $productLine->product_id)
                ->first();

            if ($stock) {
                $stock->update([
                    'quantity_reserve' => max(0, $stock->quantity_reserve - $productLine->quantity),
                    'quantity_total' => max(0, $stock->quantity_total - $productLine->quantity),
                ]);

                // Create stock history for goods issue
                StockHistory::create([
                    'stock_id' => $stock->id,
                    'company_id' => $company->id,
                    'warehouse_id' => $salesOrder->warehouse_id,
                    'product_id' => $productLine->product_id,
                    'quantity_total_before' => $stock->quantity_total,
                    'quantity_total_after' => $stock->quantity_total - $productLine->quantity,
                    'quantity_reserve_before' => $stock->quantity_reserve,
                    'quantity_reserve_after' => $stock->quantity_reserve - $productLine->quantity,
                    'quantity_saleable_before' => $stock->quantity_saleable,
                    'quantity_saleable_after' => $stock->quantity_saleable,
                    'quantity_incoming_before' => $stock->quantity_incoming,
                    'quantity_incoming_after' => $stock->quantity_incoming,
                    'type' => 'out',
                    'reference' => 'sales_order',
                    'date' => now()->toDateString(),
                    'notes' => 'Goods issue for completed sales order ' . $salesOrder->number,
                ]);
            }
        }
        
        // Find critical accounts
        $salesRevenueAccount = Account::where('company_id', $company->id)
            ->where('code', '4000')
            ->first();
        
        $accountsReceivableAccount = Account::where('company_id', $company->id)
            ->where('code', '1100')
            ->first();

        // Create income record
        $income = Income::create([
            'company_id' => $company->id,
            'number' => 'IN-' . $company->id . '-' . str_pad($salesOrder->id, 6, '0', STR_PAD_LEFT),
            'date' => now(),
            'due_date' => now()->addDays(30), // Due in 30 days
            'description' => 'Income from completed sales order ' . $salesOrder->number,
            'total' => $salesOrder->total,
            'status' => 'confirmed',
            'receipt_account_id' => $accountsReceivableAccount ? $accountsReceivableAccount->id : null,
        ]);

        // Create income details - allocate to Sales Revenue account
        if ($salesRevenueAccount) {
            \App\Models\IncomeDetail::create([
                'income_id' => $income->id,
                'account_id' => $salesRevenueAccount->id,
                'value' => $salesOrder->total,
                'description' => 'Sales revenue from ' . $salesOrder->number,
            ]);
        }

        // Create general ledger entry
        $generalLedger = GeneralLedger::create([
            'company_id' => $company->id,
            'number' => 'GL-' . $company->id . '-' . str_pad($salesOrder->id, 6, '0', STR_PAD_LEFT),
            'type' => 'sales',
            'date' => now(),
            'note' => 'Sales order completion ' . $salesOrder->number,
            'total' => $salesOrder->total,
            'reference' => $salesOrder->number,
            'status' => 'posted',
        ]);

        // Create general ledger details
        if ($salesRevenueAccount && $accountsReceivableAccount) {
            GeneralLedgerDetail::create([
                'general_ledger_id' => $generalLedger->id,
                'account_id' => $salesRevenueAccount->id,
                'type' => 'credit',
                'value' => $salesOrder->total,
                'description' => 'Sales revenue from ' . $salesOrder->number,
            ]);

            GeneralLedgerDetail::create([
                'general_ledger_id' => $generalLedger->id,
                'account_id' => $accountsReceivableAccount->id,
                'type' => 'debit',
                'value' => $salesOrder->total,
                'description' => 'Accounts receivable from ' . $salesOrder->number,
            ]);
        }
    }

    /**
     * Handle sales order cancellation: release reserved stock and cancel related deliveries.
     * Note: In the current system, sales orders create deliveries, not receipts.
     * This method handles cancellation of deliveries and stock reservation release.
     */
    private function handleCancelledStatus(SalesOrder $salesOrder, $company)
    {
        // Cancel related deliveries
        $deliveries = Delivery::where('sales_order_id', $salesOrder->id)
            ->where('status', '!=', 'cancelled')
            ->get();

        foreach ($deliveries as $delivery) {
            $delivery->update(['status' => 'cancelled']);
            
            // Create delivery status log
            DeliveryStatusLog::create([
                'delivery_id' => $delivery->id,
                'status' => 'cancelled',
                'changed_at' => now(),
            ]);
        }

        // Cancel any receipts that might be related to this sales order
        // (This handles cases where receipts might be created from sales orders in the future)
        $relatedReceipts = Receipt::where('company_id', $company->id)
            ->where('reference', $salesOrder->number)
            ->where('status', '!=', 'cancelled')
            ->get();

        foreach ($relatedReceipts as $receipt) {
            $receipt->update(['status' => 'cancelled']);
            
            // Create receipt status log
            ReceiptStatusLog::create([
                'receipt_id' => $receipt->id,
                'status' => 'cancelled',
                'changed_at' => now(),
            ]);
        }

        // Release reserved stock back to saleable quantities
        // Only for inventory-tracking products
        foreach ($salesOrder->productLines as $productLine) {
            if ($productLine->product && $productLine->product->shouldTrackInventory()) {
                $stock = Stock::where('company_id', $company->id)
                    ->where('warehouse_id', $salesOrder->warehouse_id)
                    ->where('product_id', $productLine->product_id)
                    ->first();

                if ($stock && $stock->quantity_reserve > 0) {
                    $quantityToRelease = min($stock->quantity_reserve, $productLine->quantity);
                    
                    if ($quantityToRelease > 0) {
                        $stock->update([
                            'quantity_reserve' => max(0, $stock->quantity_reserve - $quantityToRelease),
                            'quantity_saleable' => $stock->quantity_saleable + $quantityToRelease,
                        ]);

                        // Create stock history for stock release
                        StockHistory::create([
                            'stock_id' => $stock->id,
                            'company_id' => $company->id,
                            'warehouse_id' => $salesOrder->warehouse_id,
                            'product_id' => $productLine->product_id,
                            'quantity_total_before' => $stock->quantity_total,
                            'quantity_total_after' => $stock->quantity_total,
                            'quantity_reserve_before' => $stock->quantity_reserve + $quantityToRelease,
                            'quantity_reserve_after' => $stock->quantity_reserve,
                            'quantity_saleable_before' => $stock->quantity_saleable - $quantityToRelease,
                            'quantity_saleable_after' => $stock->quantity_saleable,
                            'quantity_incoming_before' => $stock->quantity_incoming,
                            'quantity_incoming_after' => $stock->quantity_incoming,
                            'type' => 'release',
                            'reference' => 'sales_order_cancellation',
                            'date' => now()->toDateString(),
                            'notes' => 'Stock reservation released due to cancelled sales order ' . $salesOrder->number,
                        ]);
                    }
                }
            }
        }
    }
}
