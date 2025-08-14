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
        Schema::create('league_players', function (Blueprint $table) {
            $table->id();
            $table->foreignId('league_team_id')->nullable()->constrained('league_teams')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('slug')->unique();
            $table->boolean('retention')->default(false);
            $table->enum('status', ['pending', 'available', 'sold', 'unsold', 'skip'])->default('pending');
            $table->double('base_price')->default(0.0);
            $table->timestamps();
            
            // Unique constraint only for assigned players
            $table->unique(['user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('league_players');
    }
};
