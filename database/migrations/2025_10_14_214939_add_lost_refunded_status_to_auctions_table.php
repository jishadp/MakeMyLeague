<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add 'lost' and 'refunded' to the status enum
        DB::statement("ALTER TABLE auctions MODIFY COLUMN status ENUM('won', 'ask', 'lost', 'refunded') DEFAULT 'ask'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to original enum values
        DB::statement("ALTER TABLE auctions MODIFY COLUMN status ENUM('won', 'ask') DEFAULT 'ask'");
    }
};
