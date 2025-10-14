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
        Schema::table('leagues', function (Blueprint $table) {
            $table->foreignId('winner_team_id')->nullable()->after('runner_prize')->constrained('league_teams')->nullOnDelete();
            $table->foreignId('runner_team_id')->nullable()->after('winner_team_id')->constrained('league_teams')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leagues', function (Blueprint $table) {
            $table->dropForeign(['winner_team_id']);
            $table->dropForeign(['runner_team_id']);
            $table->dropColumn(['winner_team_id', 'runner_team_id']);
        });
    }
};
