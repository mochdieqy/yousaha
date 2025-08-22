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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->string('name');
            $table->string('sku');
            $table->enum('type', ['goods', 'service', 'combo']);
            $table->boolean('is_track_inventory');
            $table->decimal('price', 18, 2);
            $table->decimal('taxes', 18, 2)->nullable();
            $table->decimal('cost', 18, 2)->nullable();
            $table->string('barcode')->nullable();
            $table->string('reference')->nullable();
            $table->boolean('is_shrink');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
