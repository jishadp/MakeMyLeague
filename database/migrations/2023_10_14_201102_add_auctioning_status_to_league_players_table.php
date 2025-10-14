<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('league_players', function (Blueprint $table) {
            // Update the status enum to include 'auctioning'
            DB::statement("ALTER TABLE league_players MODIFY COLUMN status ENUM('pending', 'available', 'auctioning', 'sold', 'unsold', 'skip') DEFAULT 'pending'");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('league_players', function (Blueprint $table) {
            // Revert the status enum to its original definition without 'auctioning'
            DB::statement("ALTER TABLE league_players MODIFY COLUMN status ENUM('pending', 'available', 'sold', 'unsold', 'skip') DEFAULT 'pending'");
        });
    }
};
