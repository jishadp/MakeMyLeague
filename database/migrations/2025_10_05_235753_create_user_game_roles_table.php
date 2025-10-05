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
        Schema::create('user_game_roles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('game_id')->constrained()->onDelete('cascade');
            $table->foreignId('game_position_id')->constrained('game_positions')->onDelete('cascade');
            $table->boolean('is_primary')->default(false); // Primary game/position for the user
            $table->timestamps();
            
            // Ensure a user can only have one position per game
            $table->unique(['user_id', 'game_id']);
            
            // Ensure only one primary game per user
            $table->unique(['user_id', 'is_primary'], 'user_primary_game_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_game_roles');
    }
};