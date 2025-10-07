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
        Schema::table('league_teams', function (Blueprint $table) {
            // Add auctioneer_id field to track who is assigned as auctioneer for this team in this league
            $table->foreignId('auctioneer_id')->nullable()->constrained('users')->onDelete('set null');
            
            // Add index for faster queries when checking auctioneer assignments
            $table->index(['league_id', 'auctioneer_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('league_teams', function (Blueprint $table) {
            $table->dropForeign(['auctioneer_id']);
            $table->dropIndex(['league_id', 'auctioneer_id']);
            $table->dropColumn('auctioneer_id');
        });
    }
};
