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
        Schema::create('leagues', function (Blueprint $table) {
            $table->id(); // Primary key, auto-incrementing INT
            $table->string('name'); // VARCHAR(255)
            $table->string('slug')->unique(); // Unique slug for URLs
            $table->foreignId('game_id')->constrained('games')->onDelete('cascade'); // FK to games.id
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Organizer who created the league
            $table->tinyInteger('season')->unsigned(); // TINYINT for season (1-100), add validation in model for range
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('max_teams');
            $table->integer('max_team_players');
            $table->double('team_reg_fee');
            $table->double('player_reg_fee');
            $table->boolean('retention')->default(false);
            $table->integer('retention_players')->default(0);
            $table->double('team_wallet_limit');
            $table->boolean('is_default')->default(false); // Default active league flag
            $table->enum('status', ['pending', 'active', 'completed', 'cancelled'])->default('active'); // Set default to active as requested
            $table->timestamps(); // created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leagues');
    }
};
