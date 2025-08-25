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
        // Create a new table with the correct structure
        Schema::create('email_verifications_new', function (Blueprint $table) {
            $table->id();
            $table->string('email')->index();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        // Copy data from old table to new table
        if (Schema::hasTable('email_verifications')) {
            $oldData = \DB::table('email_verifications')->get();
            foreach ($oldData as $row) {
                \DB::table('email_verifications_new')->insert([
                    'email' => $row->email,
                    'token' => $row->token,
                    'created_at' => $row->created_at,
                ]);
            }
        }

        // Drop old table and rename new table
        Schema::dropIfExists('email_verifications');
        Schema::rename('email_verifications_new', 'email_verifications');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate the old table structure
        Schema::create('email_verifications_old', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        // Copy data back
        $newData = \DB::table('email_verifications')->get();
        foreach ($newData as $row) {
            \DB::table('email_verifications_old')->insert([
                'email' => $row->email,
                'token' => $row->token,
                'created_at' => $row->created_at,
            ]);
        }

        // Drop new table and rename old table
        Schema::dropIfExists('email_verifications');
        Schema::rename('email_verifications_old', 'email_verifications');
    }
};
