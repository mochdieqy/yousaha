<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('stock_histories', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable()->constrained('companies')->onDelete('cascade')->after('id');
            $table->foreignId('warehouse_id')->nullable()->constrained('warehouses')->onDelete('cascade')->after('company_id');
            $table->foreignId('product_id')->nullable()->constrained('products')->onDelete('cascade')->after('warehouse_id');
            $table->integer('quantity')->default(0)->after('product_id');
            $table->string('reference_type')->nullable()->after('quantity');
            $table->unsignedBigInteger('reference_id')->nullable()->after('reference_type');
            $table->text('notes')->nullable()->after('reference_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_histories', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropForeign(['warehouse_id']);
            $table->dropForeign(['product_id']);
            $table->dropColumn(['company_id', 'warehouse_id', 'product_id', 'quantity', 'reference_type', 'reference_id', 'notes']);
        });
    }
};
