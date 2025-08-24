<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderProductLine;
use App\Models\PurchaseOrderStatusLog;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\Stock;
use App\Models\StockDetail;
use App\Models\StockHistory;
use App\Models\Receipt;
use App\Models\ReceiptProductLine;
use App\Models\ReceiptStatusLog;
use App\Models\Expense;
use App\Models\GeneralLedger;
use App\Models\GeneralLedgerDetail;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\Account; // Added this import for Account model

class PurchaseOrderController extends Controller
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

        $purchaseOrders = PurchaseOrder::with(['supplier', 'warehouse', 'productLines.product'])
            ->where('company_id', $company->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('pages.purchase-order.index', compact('purchaseOrders'));
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

        $suppliers = Supplier::where('company_id', $company->id)->get();
        $products = Product::where('company_id', $company->id)->get();
        $warehouses = Warehouse::where('company_id', $company->id)->get();

        return view('pages.purchase-order.create', compact('suppliers', 'products', 'warehouses'));
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
            'supplier_id' => 'required|exists:suppliers,id',
            'requestor' => 'required|string|max:255',
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

            // Generate purchase order number
            $lastOrder = PurchaseOrder::where('company_id', $company->id)
                ->orderBy('id', 'desc')
                ->first();
            
            $orderNumber = 'PO-' . $company->id . '-' . str_pad(($lastOrder ? $lastOrder->id + 1 : 1), 6, '0', STR_PAD_LEFT);

            // Calculate total
            $total = 0;
            foreach ($request->products as $product) {
                $productModel = Product::find($product['product_id']);
                $total += $product['quantity'] * ($productModel->cost ?? $productModel->price);
            }

            // Create purchase order
            $purchaseOrder = PurchaseOrder::create([
                'company_id' => $company->id,
                'warehouse_id' => $request->warehouse_id,
                'number' => $orderNumber,
                'supplier_id' => $request->supplier_id,
                'requestor' => $request->requestor,
                'activities' => $request->activities,
                'total' => $total,
                'status' => 'draft',
                'deadline' => $request->deadline,
            ]);

            // Create product lines
            foreach ($request->products as $product) {
                PurchaseOrderProductLine::create([
                    'purchase_order_id' => $purchaseOrder->id,
                    'product_id' => $product['product_id'],
                    'quantity' => $product['quantity'],
                ]);
            }

            // Create status log
            PurchaseOrderStatusLog::create([
                'purchase_order_id' => $purchaseOrder->id,
                'status' => 'draft',
                'changed_at' => now(),
            ]);

            DB::commit();

            return redirect()->route('purchase-orders.index')
                ->with('success', 'Purchase order created successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->with('error', 'Failed to create purchase order: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(PurchaseOrder $purchaseOrder)
    {
        $this->authorizeCompany($purchaseOrder);
        
        $purchaseOrder->load(['supplier', 'warehouse', 'productLines.product', 'statusLogs']);
        
        return view('pages.purchase-order.show', compact('purchaseOrder'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PurchaseOrder $purchaseOrder)
    {
        $this->authorizeCompany($purchaseOrder);
        
        if (in_array($purchaseOrder->status, ['done', 'cancel'])) {
            return back()->with('error', 'Cannot edit purchase order with status: ' . $purchaseOrder->status);
        }

        $company = Auth::user()->currentCompany;
        $suppliers = Supplier::where('company_id', $company->id)->get();
        $products = Product::where('company_id', $company->id)->get();
        $warehouses = Warehouse::where('company_id', $company->id)->get();
        
        $purchaseOrder->load(['warehouse', 'productLines.product']);

        return view('pages.purchase-order.edit', compact('purchaseOrder', 'suppliers', 'products', 'warehouses'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        $this->authorizeCompany($purchaseOrder);
        
        if (in_array($purchaseOrder->status, ['done', 'cancel'])) {
            return back()->with('error', 'Cannot edit purchase order with status: ' . $purchaseOrder->status);
        }

        $validator = Validator::make($request->all(), [
            'warehouse_id' => 'required|exists:warehouses,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'requestor' => 'required|string|max:255',
            'activities' => 'nullable|string|max:500',
            'deadline' => 'required|date',
            'status' => ['required', Rule::in(['draft', 'accepted', 'sent', 'done', 'cancel'])],
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Validate status transition for draft orders
        if ($purchaseOrder->status === 'draft' && !in_array($request->status, ['draft', 'accepted', 'cancel'])) {
            return back()->withInput()->with('error', 'Draft purchase orders can only be accepted or cancelled.');
        }

        try {
            DB::beginTransaction();

            // Calculate total
            $total = 0;
            foreach ($request->products as $product) {
                $productModel = Product::find($product['product_id']);
                $total += $product['quantity'] * ($productModel->cost ?? $productModel->price);
            }

            // Update purchase order
            $purchaseOrder->update([
                'warehouse_id' => $request->warehouse_id,
                'supplier_id' => $request->supplier_id,
                'requestor' => $request->requestor,
                'activities' => $request->activities,
                'total' => $total,
                'status' => $request->status,
                'deadline' => $request->deadline,
            ]);

            // Update product lines if status is draft
            if ($purchaseOrder->status === 'draft') {
                // Delete existing product lines
                $purchaseOrder->productLines()->delete();
                
                // Create new product lines
                foreach ($request->products as $product) {
                    PurchaseOrderProductLine::create([
                        'purchase_order_id' => $purchaseOrder->id,
                        'product_id' => $product['product_id'],
                        'quantity' => $product['quantity'],
                    ]);
                }
            }

            // Create status log
            PurchaseOrderStatusLog::create([
                'purchase_order_id' => $purchaseOrder->id,
                'status' => $request->status,
                'changed_at' => now(),
            ]);

            // Handle status-specific actions
            if (in_array($request->status, ['accepted', 'sent', 'done', 'cancel'])) {
                $this->handleStatusChange($purchaseOrder, $request->status);
            }

            DB::commit();

            return redirect()->route('purchase-orders.index')
                ->with('success', 'Purchase order updated successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->with('error', 'Failed to update purchase order: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PurchaseOrder $purchaseOrder)
    {
        $this->authorizeCompany($purchaseOrder);
        
        if (!in_array($purchaseOrder->status, ['draft'])) {
            return back()->with('error', 'Only draft purchase orders can be deleted.');
        }

        try {
            DB::beginTransaction();
            
            // Delete related records
            $purchaseOrder->productLines()->delete();
            $purchaseOrder->statusLogs()->delete();
            $purchaseOrder->delete();
            
            DB::commit();
            
            return redirect()->route('purchase-orders.index')
                ->with('success', 'Purchase order deleted successfully.');
                
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to delete purchase order: ' . $e->getMessage());
        }
    }

    /**
     * Update purchase order status
     */
    public function updateStatus(Request $request, PurchaseOrder $purchaseOrder)
    {
        $this->authorizeCompany($purchaseOrder);
        
        $validator = Validator::make($request->all(), [
            'status' => ['required', Rule::in(['draft', 'accepted', 'sent', 'done', 'cancel'])],
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Validate status transition for draft orders
        if ($purchaseOrder->status === 'draft' && !in_array($request->status, ['draft', 'accepted', 'cancel'])) {
            return back()->with('error', 'Draft purchase orders can only be accepted or cancelled.');
        }

        try {
            DB::beginTransaction();

            $purchaseOrder->update(['status' => $request->status]);

            // Create status log
            PurchaseOrderStatusLog::create([
                'purchase_order_id' => $purchaseOrder->id,
                'status' => $request->status,
                'changed_at' => now(),
            ]);

            // Handle status-specific actions
            if (in_array($request->status, ['accepted', 'sent', 'done', 'cancel'])) {
                $this->handleStatusChange($purchaseOrder, $request->status);
            }

            DB::commit();

            return back()->with('success', 'Purchase order status updated successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to update status: ' . $e->getMessage());
        }
    }

    /**
     * Handle status change actions
     */
    private function handleStatusChange(PurchaseOrder $purchaseOrder, string $status)
    {
        $company = Auth::user()->currentCompany;

        switch ($status) {
            case 'accepted':
                // Create receipt when status changes to accepted
                $receipt = Receipt::create([
                    'company_id' => $company->id,
                    'warehouse_id' => $purchaseOrder->warehouse_id,
                    'receive_from' => $purchaseOrder->supplier_id,
                    'scheduled_at' => now(),
                    'reference' => $purchaseOrder->number,
                    'status' => 'ready',
                ]);

                // Create receipt product lines
                foreach ($purchaseOrder->productLines as $line) {
                    ReceiptProductLine::create([
                        'receipt_id' => $receipt->id,
                        'product_id' => $line->product_id,
                        'quantity' => $line->quantity,
                    ]);
                }

                // Create receipt status log
                ReceiptStatusLog::create([
                    'receipt_id' => $receipt->id,
                    'status' => 'ready',
                    'changed_at' => now(),
                ]);

                // Automatically upsert stock and increase incoming quantities
                foreach ($purchaseOrder->productLines as $line) {
                    $stock = Stock::where('company_id', $company->id)
                        ->where('product_id', $line->product_id)
                        ->where('warehouse_id', $purchaseOrder->warehouse_id)
                        ->first();

                    if ($stock) {
                        // Update existing stock - increment incoming quantity
                        $stock->increment('quantity_incoming', $line->quantity);
                        $stock->increment('quantity_total', $line->quantity);
                    } else {
                        // Create new stock record
                        $stock = Stock::create([
                            'company_id' => $company->id,
                            'product_id' => $line->product_id,
                            'warehouse_id' => $purchaseOrder->warehouse_id,
                            'quantity_total' => $line->quantity,
                            'quantity_incoming' => $line->quantity,
                            'quantity_saleable' => 0,
                            'quantity_reserve' => 0,
                        ]);
                    }
                }
                break;

            case 'done':
                // Automatically update related receipt statuses to "done" (except those already done)
                // This ensures that when a purchase order is completed, all related receipts are also completed
                // and stock quantities are properly moved from incoming to saleable
                $this->updateRelatedReceiptsToDone($purchaseOrder);

                // Find Cost of Goods Sold account (5000) for debit entry
                $costOfGoodsSoldAccount = Account::where('company_id', $company->id)
                    ->where('code', '5000')
                    ->first();
                
                // Find Accounts Payable account (2000) for credit entry
                $accountsPayableAccount = Account::where('company_id', $company->id)
                    ->where('code', '2000')
                    ->first();
                    
                // Create expense
                $expense = Expense::create([
                    'company_id' => $company->id,
                    'number' => 'EXP-' . $company->id . '-' . str_pad(Expense::where('company_id', $company->id)->count() + 1, 6, '0', STR_PAD_LEFT),
                    'date' => now(),
                    'due_date' => now()->addDays(30), // Set due date to 30 days from now
                    'total' => $purchaseOrder->total,
                    'paid' => false, // Default to unpaid
                    'status' => 'pending', // Default status
                    'note' => 'Purchase from ' . $purchaseOrder->supplier->name,
                    'supplier_id' => $purchaseOrder->supplier_id,
                    'payment_account_id' => $accountsPayableAccount ? $accountsPayableAccount->id : null,
                ]);

                // Create expense details - allocate to Cost of Goods Sold account
                if ($costOfGoodsSoldAccount) {
                    \App\Models\ExpenseDetail::create([
                        'expense_id' => $expense->id,
                        'account_id' => $costOfGoodsSoldAccount->id,
                        'value' => $purchaseOrder->total,
                        'description' => 'Cost of goods sold from ' . $purchaseOrder->number,
                        'status' => 'active',
                    ]);
                }

                // Create general ledger entry
                $generalLedger = GeneralLedger::create([
                    'company_id' => $company->id,
                    'number' => 'GL-' . $company->id . '-' . str_pad(GeneralLedger::where('company_id', $company->id)->count() + 1, 6, '0', STR_PAD_LEFT),
                    'type' => 'expense', // Set the required type field
                    'date' => now(),
                    'note' => 'Purchase expense from ' . $purchaseOrder->supplier->name,
                    'total' => $purchaseOrder->total,
                    'reference' => 'PO-' . $purchaseOrder->number, // Add reference to purchase order
                    'status' => 'posted',
                ]);

                // Create general ledger details
                if ($costOfGoodsSoldAccount && $accountsPayableAccount) {
                    GeneralLedgerDetail::create([
                        'general_ledger_id' => $generalLedger->id,
                        'account_id' => $costOfGoodsSoldAccount->id,
                        'type' => 'debit',
                        'value' => $purchaseOrder->total,
                        'description' => 'Cost of goods sold from ' . $purchaseOrder->number,
                    ]);

                    GeneralLedgerDetail::create([
                        'general_ledger_id' => $generalLedger->id,
                        'account_id' => $accountsPayableAccount->id,
                        'type' => 'credit',
                        'value' => $purchaseOrder->total,
                        'description' => 'Accounts payable from ' . $purchaseOrder->number,
                    ]);
                }
                break;
        }
    }

    /**
     * Authorize that the user can access this company's data
     */
    private function authorizeCompany(PurchaseOrder $purchaseOrder)
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company || $purchaseOrder->company_id !== $company->id) {
            abort(403, 'Unauthorized access to purchase order.');
        }
    }

    /**
     * Automatically update related receipt statuses to "done" (except those already done)
     */
    private function updateRelatedReceiptsToDone(PurchaseOrder $purchaseOrder)
    {
        $company = Auth::user()->currentCompany;
        
        // Find all receipts related to this purchase order by reference
        $receipts = Receipt::where('company_id', $company->id)
            ->where('reference', $purchaseOrder->number)
            ->where('status', '!=', 'done') // Exclude receipts that are already done
            ->get();

        foreach ($receipts as $receipt) {
            // Update receipt status to 'done'
            $receipt->update(['status' => 'done']);

            // Create receipt status log
            ReceiptStatusLog::create([
                'receipt_id' => $receipt->id,
                'status' => 'done',
                'changed_at' => now(),
            ]);

            // Move quantities from incoming to saleable when receipt is marked as done
            foreach ($receipt->productLines as $line) {
                
                $stock = Stock::where('company_id', $company->id)
                    ->where('product_id', $line->product_id)
                    ->where('warehouse_id', $receipt->warehouse_id)
                    ->first();

                if ($stock) {
                    
                    // Only move quantities if there are sufficient incoming quantities
                    $quantityToMove = min($line->quantity, $stock->quantity_incoming);
                    
                    if ($quantityToMove > 0) {
                        // Move quantity from incoming to saleable
                        $stock->decrement('quantity_incoming', $quantityToMove);
                        $stock->increment('quantity_saleable', $quantityToMove);

                        // Create stock detail for saleable quantity
                        StockDetail::create([
                            'stock_id' => $stock->id,
                            'quantity' => $quantityToMove,
                            'reference' => 'Receipt #' . $receipt->id,
                        ]);

                        // Create stock history for the goods receiving
                        StockHistory::create([
                            'stock_id' => $stock->id,
                            'type' => 'goods_received',
                            'reference' => 'Receipt #' . $receipt->id,
                            'quantity_total_before' => $stock->quantity_total,
                            'quantity_total_after' => $stock->quantity_total,
                            'quantity_incoming_before' => $stock->quantity_incoming + $quantityToMove,
                            'quantity_incoming_after' => $stock->quantity_incoming,
                            'quantity_saleable_before' => $stock->quantity_saleable - $quantityToMove,
                            'quantity_saleable_after' => $stock->quantity_saleable,
                            'quantity_reserve_before' => $stock->quantity_reserve,
                            'quantity_reserve_after' => $stock->quantity_reserve,
                            'date' => now(),
                        ]);
                    }
                }
            }
        }
    }
}
