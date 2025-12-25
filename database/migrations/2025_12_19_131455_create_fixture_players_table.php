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
        Schema::create('fixture_players', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fixture_id')->constrained('fixtures')->onDelete('cascade');
            $table->foreignId('team_id')->constrained('league_teams')->onDelete('cascade');
            $table->foreignId('player_id')->constrained('league_players')->onDelete('cascade');
            $table->enum('status', ['starting', 'bench', 'subbed_in', 'subbed_out'])->default('bench');
            $table->boolean('is_active')->default(false); // True if currently on field
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fixture_players');
    }
};
