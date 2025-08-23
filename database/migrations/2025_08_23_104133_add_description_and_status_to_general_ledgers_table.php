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
        Schema::table('general_ledgers', function (Blueprint $table) {
            $table->text('description')->nullable()->after('note');
            $table->string('status')->default('draft')->after('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('general_ledgers', function (Blueprint $table) {
            $table->dropColumn(['description', 'status']);
        });
    }
};
