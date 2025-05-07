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
        Schema::create('internal_transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->string('number');
            $table->date('date');
            $table->foreignId('account_in')->constrained('accounts')->onDelete('cascade');
            $table->foreignId('account_out')->constrained('accounts')->onDelete('cascade');
            $table->string('note')->nullable();
            $table->decimal('value', 18, 2);
            $table->decimal('fee', 18, 2);
            $table->enum('fee_charged_to', ['in', 'out']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('internal_transfers');
    }
};
