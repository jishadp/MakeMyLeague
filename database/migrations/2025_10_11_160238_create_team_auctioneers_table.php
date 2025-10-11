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
        Schema::create('team_auctioneers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('league_id')->constrained('leagues')->onDelete('cascade');
            $table->foreignId('team_id')->constrained('teams')->onDelete('cascade');
            $table->foreignId('league_team_id')->constrained('league_teams')->onDelete('cascade');
            $table->foreignId('auctioneer_id')->constrained('users')->onDelete('cascade');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Unique constraint: One user can only be auctioneer for one team per league
            $table->unique(['league_id', 'auctioneer_id'], 'unique_auctioneer_per_league');
            
            // Index for faster queries
            $table->index(['league_id', 'team_id']);
            $table->index(['league_team_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('team_auctioneers');
    }
};
