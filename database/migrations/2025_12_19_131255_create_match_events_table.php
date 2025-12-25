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
        Schema::create('match_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fixture_id')->constrained('fixtures')->onDelete('cascade');
            $table->string('event_type'); // GOAL, YELLOW_CARD, RED_CARD, SUB, COMMENTARY, START_HALF, END_HALF
            $table->integer('minute')->nullable();
            $table->foreignId('team_id')->nullable()->constrained('league_teams')->onDelete('cascade');
            
            // Player linking
            $table->foreignId('player_id')->nullable()->constrained('league_players')->onDelete('set null');
            $table->string('player_name')->nullable(); // For Guest Players
            
            // Substitution linking (Player ON)
            $table->foreignId('related_player_id')->nullable()->constrained('league_players')->onDelete('set null');
            $table->string('related_player_name')->nullable(); // For Guest Players coming ON
            
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('match_events');
    }
};
