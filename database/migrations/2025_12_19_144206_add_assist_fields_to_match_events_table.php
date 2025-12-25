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
        Schema::table('match_events', function (Blueprint $table) {
            $table->foreignId('assist_player_id')->nullable()->constrained('league_players')->onDelete('set null');
            $table->string('assist_player_name')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('match_events', function (Blueprint $table) {
            $table->dropForeign(['assist_player_id']);
            $table->dropColumn(['assist_player_id', 'assist_player_name']);
        });
    }
};
