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
        Schema::create('stock_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_id')->constrained('stocks')->onDelete('cascade');
            $table->integer('quantity_total_before');
            $table->integer('quantity_total_after');
            $table->integer('quantity_reserve_before');
            $table->integer('quantity_reserve_after');
            $table->integer('quantity_saleable_before');
            $table->integer('quantity_saleable_after');
            $table->integer('quantity_incoming_before');
            $table->integer('quantity_incoming_after');
            $table->string('type');
            $table->string('reference');
            $table->date('date')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_histories');
    }
};
