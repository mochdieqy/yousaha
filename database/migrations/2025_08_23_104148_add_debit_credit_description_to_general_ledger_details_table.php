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
        Schema::table('general_ledger_details', function (Blueprint $table) {
            $table->decimal('debit', 18, 2)->default(0)->after('value');
            $table->decimal('credit', 18, 2)->default(0)->after('debit');
            $table->text('description')->nullable()->after('credit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('general_ledger_details', function (Blueprint $table) {
            $table->dropColumn(['debit', 'credit', 'description']);
        });
    }
};
