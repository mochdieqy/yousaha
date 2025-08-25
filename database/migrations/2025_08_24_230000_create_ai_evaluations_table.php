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
        Schema::create('ai_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('category');
            $table->string('title');
            $table->longText('content')->nullable();
            $table->json('data_summary')->nullable();
            $table->json('insights')->nullable();
            $table->json('recommendations')->nullable();
            $table->date('evaluation_date')->nullable();
            $table->date('period_start')->nullable();
            $table->date('period_end')->nullable();
            $table->enum('status', ['draft', 'completed', 'failed'])->default('draft');
            $table->foreignId('generated_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            
            $table->index(['company_id', 'category']);
            $table->index(['company_id', 'status']);
            $table->index(['company_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_evaluations');
    }
};
