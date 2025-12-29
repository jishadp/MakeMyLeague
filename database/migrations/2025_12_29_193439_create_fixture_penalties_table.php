<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fixture_penalties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fixture_id')->constrained('fixtures')->onDelete('cascade');
            $table->foreignId('team_id')->constrained('league_teams')->onDelete('cascade');
            $table->foreignId('player_id')->nullable()->constrained('league_players')->onDelete('cascade');
            $table->string('player_name')->nullable(); // For guest players
            $table->boolean('scored')->default(false); // true = goal, false = miss
            $table->integer('attempt_number'); // 1, 2, 3, 4, 5...
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fixture_penalties');
    }
};
