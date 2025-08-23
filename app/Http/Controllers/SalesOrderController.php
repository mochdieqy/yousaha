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
    public function index()
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company) {
            return redirect()->route('company.choice')->with('error', 'Please select a company first.');
        }

        $salesOrders = SalesOrder::with(['customer', 'warehouse', 'productLines.product'])
            ->where('company_id', $company->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('pages.sales-order.index', compact('salesOrders'));
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

        return view('pages.sales-order.create', compact('customers', 'products', 'warehouses'));
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
            $lastOrder = SalesOrder::where('company_id', $company->id)
                ->orderBy('id', 'desc')
                ->first();
            
            $orderNumber = 'SO-' . $company->id . '-' . str_pad(($lastOrder ? $lastOrder->id + 1 : 1), 6, '0', STR_PAD_LEFT);

            // Calculate total
            $total = 0;
            foreach ($request->products as $product) {
                $productModel = Product::find($product['product_id']);
                $total += $productModel->price * $product['quantity'];
            }

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
            foreach ($request->products as $product) {
                SalesOrderProductLine::create([
                    'sales_order_id' => $salesOrder->id,
                    'product_id' => $product['product_id'],
                    'quantity' => $product['quantity'],
                ]);
            }

            // Create status log
            SalesOrderStatusLog::create([
                'sales_order_id' => $salesOrder->id,
                'status' => 'draft',
                'changed_at' => now(),
            ]);

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

        return view('pages.sales-order.show', compact('salesOrder'));
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

        return view('pages.sales-order.edit', compact('salesOrder', 'customers', 'products', 'warehouses'));
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
            'salesperson' => 'required|string|max:255',
            'activities' => 'nullable|string|max:500',
            'deadline' => 'required|date|after:today',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'status' => 'required|in:draft,waiting,accepted,sent,done,cancel',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // Calculate total
            $total = 0;
            foreach ($request->products as $product) {
                $productModel = Product::find($product['product_id']);
                $total += $productModel->price * $product['quantity'];
            }

            // Update sales order
            $salesOrder->update([
                'warehouse_id' => $request->warehouse_id,
                'customer_id' => $request->customer_id,
                'salesperson' => $request->salesperson,
                'activities' => $request->activities,
                'total' => $total,
                'status' => $request->status,
                'deadline' => $request->deadline,
            ]);

            // Update product lines for draft and waiting status
            if (in_array($request->status, ['draft', 'waiting'])) {
                // Delete existing product lines
                $salesOrder->productLines()->delete();
                
                // Create new product lines
                foreach ($request->products as $product) {
                    SalesOrderProductLine::create([
                        'sales_order_id' => $salesOrder->id,
                        'product_id' => $product['product_id'],
                        'quantity' => $product['quantity'],
                    ]);
                }
            }

            // Handle status changes that require additional actions
            if (in_array($request->status, ['accepted', 'sent', 'done', 'cancel'])) {
                // Update stock if status is done
                if ($request->status === 'done') {
                    foreach ($request->products as $product) {
                        $stock = Stock::where('company_id', $company->id)
                            ->where('warehouse_id', $request->warehouse_id)
                            ->where('product_id', $product['product_id'])
                            ->first();

                        if ($stock) {
                            $stock->update([
                                'quantity' => $stock->quantity - $product['quantity']
                            ]);

                            // Create stock history
                            StockHistory::create([
                                'company_id' => $company->id,
                                'warehouse_id' => $request->warehouse_id,
                                'product_id' => $product['product_id'],
                                'type' => 'out',
                                'quantity' => $product['quantity'],
                                'reference_type' => 'sales_order',
                                'reference_id' => $salesOrder->id,
                                'notes' => 'Sales order completion',
                            ]);
                        }
                    }

                    // Create delivery record
                    $delivery = Delivery::create([
                        'company_id' => $company->id,
                        'warehouse_id' => $request->warehouse_id,
                        'number' => 'DO-' . $company->id . '-' . str_pad($salesOrder->id, 6, '0', STR_PAD_LEFT),
                        'customer_id' => $request->customer_id,
                        'sales_order_id' => $salesOrder->id,
                        'status' => 'done',
                        'notes' => 'Auto-generated from sales order',
                    ]);

                    // Create delivery product lines
                    foreach ($request->products as $product) {
                        DeliveryProductLine::create([
                            'delivery_id' => $delivery->id,
                            'product_id' => $product['product_id'],
                            'quantity' => $product['quantity'],
                        ]);
                    }

                    // Create delivery status log
                    DeliveryStatusLog::create([
                        'delivery_id' => $delivery->id,
                        'status' => 'done',
                        'changed_at' => now(),
                    ]);

                    // Create income record
                    $income = Income::create([
                        'company_id' => $company->id,
                        'number' => 'IN-' . $company->id . '-' . str_pad($salesOrder->id, 6, '0', STR_PAD_LEFT),
                        'date' => now(),
                        'description' => 'Income from sales order ' . $salesOrder->number,
                        'total' => $total,
                        'status' => 'confirmed',
                    ]);

                    // Create general ledger entry
                    $generalLedger = GeneralLedger::create([
                        'company_id' => $company->id,
                        'number' => 'GL-' . $company->id . '-' . str_pad($salesOrder->id, 6, '0', STR_PAD_LEFT),
                        'date' => now(),
                        'description' => 'Sales order completion ' . $salesOrder->number,
                        'total' => $total,
                        'status' => 'posted',
                    ]);

                            // Create general ledger details
        GeneralLedgerDetail::create([
            'general_ledger_id' => $generalLedger->id,
            'account_id' => Account::where('company_id', $company->id)->where('type', 'income')->first()->id,
            'type' => 'credit',
            'value' => $salesOrder->total,
        ]);

        GeneralLedgerDetail::create([
            'general_ledger_id' => $generalLedger->id,
            'account_id' => Account::where('company_id', $company->id)->where('type', 'asset')->first()->id,
            'type' => 'debit',
            'value' => $salesOrder->total,
        ]);
                }
            }

            // Create status log
            SalesOrderStatusLog::create([
                'sales_order_id' => $salesOrder->id,
                'status' => $request->status,
                'changed_at' => now(),
            ]);

            DB::commit();

            return redirect()->route('sales-orders.index')
                ->with('success', 'Sales order updated successfully.');

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
            'status' => 'required|in:draft,waiting,accepted,sent,done,cancel',
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
                    return redirect()->back()
                        ->with('error', $result['message'])
                        ->withInput();
                }
            }
            
            // Handle status change to 'done'
            if ($newStatus === 'done' && $oldStatus !== 'done') {
                $this->handleDoneStatus($salesOrder, $company);
            }

            // Update sales order status
            $salesOrder->update(['status' => $newStatus]);

            // Create status log
            SalesOrderStatusLog::create([
                'sales_order_id' => $salesOrder->id,
                'status' => $newStatus,
                'changed_at' => now(),
            ]);

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
            SalesOrderStatusLog::create([
                'sales_order_id' => $salesOrder->id,
                'status' => 'waiting',
                'changed_at' => now(),
            ]);

            return [
                'success' => false,
                'message' => 'Sales order status changed to waiting due to insufficient stock. Products: ' . implode(', ', $stockCheck['insufficient_products'])
            ];
        }

        // Stock is available, proceed with acceptance
        $this->processAcceptedSalesOrder($salesOrder, $company, $stockCheck['stock_data']);

        return ['success' => true];
    }

    /**
     * Check stock availability for sales order products.
     */
    private function checkStockAvailability(SalesOrder $salesOrder, $company)
    {
        $available = true;
        $insufficient_products = [];
        $stock_data = [];

        foreach ($salesOrder->productLines as $productLine) {
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
        // Create delivery
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

        // Create delivery product lines
        foreach ($salesOrder->productLines as $productLine) {
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

        // Update stock: increase reserved quantity and decrease saleable quantity
        foreach ($stockData as $productId => $data) {
            $stock = $data['stock'];
            $requiredQuantity = $data['required_quantity'];

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

    /**
     * Handle sales order status change to 'done'.
     */
    private function handleDoneStatus(SalesOrder $salesOrder, $company)
    {
        // Check if delivery already exists
        $existingDelivery = Delivery::where('sales_order_id', $salesOrder->id)->first();
        
        if (!$existingDelivery) {
            // Create delivery if it doesn't exist
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

            // Create delivery product lines
            foreach ($salesOrder->productLines as $productLine) {
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
        } else {
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
        foreach ($salesOrder->productLines as $productLine) {
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

        // Create income record
        $income = Income::create([
            'company_id' => $company->id,
            'number' => 'IN-' . $company->id . '-' . str_pad($salesOrder->id, 6, '0', STR_PAD_LEFT),
            'date' => now(),
            'due_date' => now()->addDays(30), // Due in 30 days
            'description' => 'Income from completed sales order ' . $salesOrder->number,
            'total' => $salesOrder->total,
            'status' => 'confirmed',
        ]);

        // Create general ledger entry
        $generalLedger = GeneralLedger::create([
            'company_id' => $company->id,
            'number' => 'GL-' . $company->id . '-' . str_pad($salesOrder->id, 6, '0', STR_PAD_LEFT),
            'type' => 'sales',
            'date' => now(),
            'note' => 'Sales order completion ' . $salesOrder->number,
            'total' => $salesOrder->total,
            'reference' => $salesOrder->number,
        ]);

        // Create general ledger details
        GeneralLedgerDetail::create([
            'general_ledger_id' => $generalLedger->id,
            'account_id' => Account::where('company_id', $company->id)->where('type', 'income')->first()->id,
            'type' => 'credit',
            'value' => $salesOrder->total,
        ]);

        GeneralLedgerDetail::create([
            'general_ledger_id' => $generalLedger->id,
            'account_id' => Account::where('company_id', $company->id)->where('type', 'asset')->first()->id,
            'type' => 'debit',
            'value' => $salesOrder->total,
        ]);
    }
}
