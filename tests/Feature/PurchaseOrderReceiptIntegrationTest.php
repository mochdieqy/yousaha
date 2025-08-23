<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Warehouse;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderProductLine;
use App\Models\Receipt;
use App\Models\ReceiptProductLine;
use App\Models\Stock;
use App\Models\StockDetail;
use App\Models\StockHistory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class PurchaseOrderReceiptIntegrationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $company;
    protected $supplier;
    protected $warehouse;
    protected $product;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create permissions
        \Spatie\Permission\Models\Permission::create(['name' => 'purchase-orders.view']);
        \Spatie\Permission\Models\Permission::create(['name' => 'purchase-orders.edit']);
        \Spatie\Permission\Models\Permission::create(['name' => 'receipts.view']);
        \Spatie\Permission\Models\Permission::create(['name' => 'receipts.edit']);
        
        // Create Company Owner role
        $companyOwnerRole = \Spatie\Permission\Models\Role::create(['name' => 'Company Owner']);
        $companyOwnerRole->givePermissionTo(\Spatie\Permission\Models\Permission::all());
        
        // Create test data
        $this->user = User::factory()->create();
        $this->company = Company::factory()->create(['owner' => $this->user->id]);
        $this->user->assignRole('Company Owner');
        $this->supplier = Supplier::factory()->create(['company_id' => $this->company->id]);
        $this->warehouse = Warehouse::factory()->create(['company_id' => $this->company->id]);
        $this->product = Product::factory()->create(['company_id' => $this->company->id]);
        
        // Create necessary accounts for general ledger entries
        $this->expenseAccount = \App\Models\Account::create([
            'company_id' => $this->company->id,
            'code' => '5000',
            'name' => 'Cost of Goods Sold',
            'type' => 'expense',
            'balance' => 0.00,
        ]);
        
        $this->payableAccount = \App\Models\Account::create([
            'company_id' => $this->company->id,
            'code' => '2000',
            'name' => 'Accounts Payable',
            'type' => 'liability',
            'balance' => 0.00,
        ]);
        
        // Set the current company in session for the user
        session(['current_company_id' => $this->company->id]);
    }

    /** @test */
    public function when_purchase_order_status_changes_to_done_receipt_status_automatically_changes_to_done()
    {
        // Create a purchase order
        $purchaseOrder = PurchaseOrder::create([
            'company_id' => $this->company->id,
            'warehouse_id' => $this->warehouse->id,
            'number' => 'PO-TEST-001',
            'supplier_id' => $this->supplier->id,
            'requestor' => 'Test User',
            'activities' => 'Test activities',
            'total' => 1000.00,
            'status' => 'accepted',
            'deadline' => now()->addDays(7),
        ]);

        // Create product lines
        PurchaseOrderProductLine::create([
            'purchase_order_id' => $purchaseOrder->id,
            'product_id' => $this->product->id,
            'quantity' => 10,
        ]);

        // Create a receipt related to this purchase order
        $receipt = Receipt::create([
            'company_id' => $this->company->id,
            'warehouse_id' => $this->warehouse->id,
            'receive_from' => $this->supplier->id,
            'scheduled_at' => now(),
            'reference' => $purchaseOrder->number,
            'status' => 'ready',
        ]);

        // Create receipt product lines
        ReceiptProductLine::create([
            'receipt_id' => $receipt->id,
            'product_id' => $this->product->id,
            'quantity' => 10,
        ]);

        // Create initial stock with incoming quantities
        $stock = Stock::create([
            'company_id' => $this->company->id,
            'product_id' => $this->product->id,
            'warehouse_id' => $this->warehouse->id,
            'quantity_total' => 10,
            'quantity_incoming' => 10,
            'quantity_saleable' => 0,
            'quantity_reserve' => 0,
        ]);

        // Act: Change purchase order status to 'done'
        $response = $this->actingAs($this->user)
            ->post(route('purchase-orders.update-status', $purchaseOrder), [
                'status' => 'done'
            ]);

        // Assert: Purchase order status is updated to 'done'
        $this->assertEquals('done', $purchaseOrder->fresh()->status);

        // Assert: Receipt status is automatically updated to 'done'
        $this->assertEquals('done', $receipt->fresh()->status);

        // Assert: Stock quantities are properly updated
        $stock->refresh();
        $this->assertEquals(0, $stock->quantity_incoming); // Decreased from 10 to 0
        $this->assertEquals(10, $stock->quantity_saleable); // Increased from 0 to 10
    }

    /** @test */
    public function when_purchase_order_status_changes_to_done_only_non_done_receipts_are_updated()
    {
        // Create a purchase order
        $purchaseOrder = PurchaseOrder::create([
            'company_id' => $this->company->id,
            'warehouse_id' => $this->warehouse->id,
            'number' => 'PO-TEST-002',
            'supplier_id' => $this->supplier->id,
            'requestor' => 'Test User',
            'activities' => 'Test activities',
            'total' => 1000.00,
            'status' => 'draft', // Start with draft to avoid automatic receipt creation
            'deadline' => now()->addDays(7),
        ]);

        // Create product lines
        PurchaseOrderProductLine::create([
            'purchase_order_id' => $purchaseOrder->id,
            'product_id' => $this->product->id,
            'quantity' => 10,
        ]);

        // First change status to accepted to create receipts
        $this->actingAs($this->user)
            ->post(route('purchase-orders.update-status', $purchaseOrder), [
                'status' => 'accepted'
            ]);

        // Create a receipt that is already done
        $doneReceipt = Receipt::create([
            'company_id' => $this->company->id,
            'warehouse_id' => $this->warehouse->id,
            'receive_from' => $this->supplier->id,
            'scheduled_at' => now(),
            'reference' => $purchaseOrder->number,
            'status' => 'done',
        ]);

        // Create a receipt that is not done
        $pendingReceipt = Receipt::create([
            'company_id' => $this->company->id,
            'warehouse_id' => $this->warehouse->id,
            'receive_from' => $this->supplier->id,
            'scheduled_at' => now(),
            'reference' => $purchaseOrder->number,
            'status' => 'ready',
        ]);

        // Create receipt product lines
        ReceiptProductLine::create([
            'receipt_id' => $doneReceipt->id,
            'product_id' => $this->product->id,
            'quantity' => 5,
        ]);

        ReceiptProductLine::create([
            'receipt_id' => $pendingReceipt->id,
            'product_id' => $this->product->id,
            'quantity' => 5,
        ]);

        // Create initial stock
        $stock = Stock::create([
            'company_id' => $this->company->id,
            'product_id' => $this->product->id,
            'warehouse_id' => $this->warehouse->id,
            'quantity_total' => 10,
            'quantity_incoming' => 10,
            'quantity_saleable' => 0,
            'quantity_reserve' => 0,
        ]);

        // Now change status to done
        $this->actingAs($this->user)
            ->post(route('purchase-orders.update-status', $purchaseOrder), [
                'status' => 'done'
            ]);

        // Assert: Purchase order status is updated
        $this->assertEquals('done', $purchaseOrder->fresh()->status);

        // Assert: Done receipt remains done
        $this->assertEquals('done', $doneReceipt->fresh()->status);

        // Assert: Pending receipt is updated to done
        $this->assertEquals('done', $pendingReceipt->fresh()->status);

        // Assert: Stock quantities are properly updated (only for the pending receipt)
        // Find the stock record using the same criteria as the controller
        $stock = Stock::where('company_id', $this->company->id)
            ->where('product_id', $this->product->id)
            ->where('warehouse_id', $this->warehouse->id)
            ->first();
        
        $this->assertNotNull($stock, 'Stock record not found');
        
        // When status changes to 'accepted', it creates a receipt and increases incoming quantities
        // When status changes to 'done', it moves quantities from incoming to saleable
        // The system processes all receipts, so the final quantities depend on the total quantities in all receipts
        // We expect: incoming = 0 (all moved to saleable), saleable = total quantity from all receipts
        $this->assertEquals(0, $stock->quantity_incoming); // All incoming quantities moved to saleable
        $this->assertEquals(10, $stock->quantity_saleable); // Total quantity from all receipts
    }

    /** @test */
    public function when_purchase_order_status_changes_to_done_stock_details_and_history_are_created()
    {
        // Create a purchase order
        $purchaseOrder = PurchaseOrder::create([
            'company_id' => $this->company->id,
            'warehouse_id' => $this->warehouse->id,
            'number' => 'PO-TEST-003',
            'supplier_id' => $this->supplier->id,
            'requestor' => 'Test User',
            'activities' => 'Test activities',
            'total' => 1000.00,
            'status' => 'accepted',
            'deadline' => now()->addDays(7),
        ]);

        // Create product lines
        PurchaseOrderProductLine::create([
            'purchase_order_id' => $purchaseOrder->id,
            'product_id' => $this->product->id,
            'quantity' => 10,
        ]);

        // Create a receipt
        $receipt = Receipt::create([
            'company_id' => $this->company->id,
            'warehouse_id' => $this->warehouse->id,
            'receive_from' => $this->supplier->id,
            'scheduled_at' => now(),
            'reference' => $purchaseOrder->number,
            'status' => 'ready',
        ]);

        // Create receipt product lines
        ReceiptProductLine::create([
            'receipt_id' => $receipt->id,
            'product_id' => $this->product->id,
            'quantity' => 10,
        ]);

        // Create initial stock
        $stock = Stock::create([
            'company_id' => $this->company->id,
            'product_id' => $this->product->id,
            'warehouse_id' => $this->warehouse->id,
            'quantity_total' => 10,
            'quantity_incoming' => 10,
            'quantity_saleable' => 0,
            'quantity_reserve' => 0,
        ]);

        // Act: Change purchase order status to 'done'
        $this->actingAs($this->user)
            ->post(route('purchase-orders.update-status', $purchaseOrder), [
                'status' => 'done'
            ]);

        // Assert: Stock detail is created
        $this->assertDatabaseHas('stock_details', [
            'stock_id' => $stock->id,
            'quantity' => 10,
            'reference' => 'Receipt #' . $receipt->id,
        ]);

        // Assert: Stock history is created
        $this->assertDatabaseHas('stock_histories', [
            'stock_id' => $stock->id,
            'type' => 'goods_received',
            'reference' => 'Receipt #' . $receipt->id,
        ]);
    }

    /** @test */
    public function when_purchase_order_status_changes_to_done_only_receipts_with_matching_reference_are_updated()
    {
        // Create a purchase order
        $purchaseOrder = PurchaseOrder::create([
            'company_id' => $this->company->id,
            'warehouse_id' => $this->warehouse->id,
            'number' => 'PO-TEST-004',
            'supplier_id' => $this->supplier->id,
            'requestor' => 'Test User',
            'activities' => 'Test activities',
            'total' => 1000.00,
            'status' => 'accepted',
            'deadline' => now()->addDays(7),
        ]);

        // Create product lines
        PurchaseOrderProductLine::create([
            'purchase_order_id' => $purchaseOrder->id,
            'product_id' => $this->product->id,
            'quantity' => 10,
        ]);

        // Create a receipt with matching reference
        $matchingReceipt = Receipt::create([
            'company_id' => $this->company->id,
            'warehouse_id' => $this->warehouse->id,
            'receive_from' => $this->supplier->id,
            'scheduled_at' => now(),
            'reference' => $purchaseOrder->number,
            'status' => 'ready',
        ]);

        // Create a receipt with different reference
        $nonMatchingReceipt = Receipt::create([
            'company_id' => $this->company->id,
            'warehouse_id' => $this->warehouse->id,
            'receive_from' => $this->supplier->id,
            'scheduled_at' => now(),
            'reference' => 'DIFFERENT-REF',
            'status' => 'ready',
        ]);

        // Create receipt product lines
        ReceiptProductLine::create([
            'receipt_id' => $matchingReceipt->id,
            'product_id' => $this->product->id,
            'quantity' => 10,
        ]);

        ReceiptProductLine::create([
            'receipt_id' => $nonMatchingReceipt->id,
            'product_id' => $this->product->id,
            'quantity' => 5,
        ]);

        // Act: Change purchase order status to 'done'
        $this->actingAs($this->user)
            ->post(route('purchase-orders.update-status', $purchaseOrder), [
                'status' => 'done'
            ]);

        // Assert: Matching receipt is updated to done
        $this->assertEquals('done', $matchingReceipt->fresh()->status);

        // Assert: Non-matching receipt remains unchanged
        $this->assertEquals('ready', $nonMatchingReceipt->fresh()->status);
    }
}
