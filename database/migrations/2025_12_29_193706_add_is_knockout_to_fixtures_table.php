<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fixtures', function (Blueprint $table) {
            $table->boolean('is_knockout')->default(true)->after('match_type'); // All scorer matches are knockout
            $table->boolean('has_penalties')->default(false)->after('is_knockout');
            $table->foreignId('penalty_winner_team_id')->nullable()->constrained('league_teams')->after('has_penalties');
        });
    }

    public function down(): void
    {
        Schema::table('fixtures', function (Blueprint $table) {
            $table->dropForeign(['penalty_winner_team_id']);
            $table->dropColumn(['is_knockout', 'has_penalties', 'penalty_winner_team_id']);
        });
    }
};
