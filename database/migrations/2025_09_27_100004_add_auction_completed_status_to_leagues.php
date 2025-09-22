<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE leagues MODIFY COLUMN status ENUM('pending', 'active', 'completed', 'cancelled', 'auction_completed') DEFAULT 'active'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE leagues MODIFY COLUMN status ENUM('pending', 'active', 'completed', 'cancelled') DEFAULT 'active'");
    }
};