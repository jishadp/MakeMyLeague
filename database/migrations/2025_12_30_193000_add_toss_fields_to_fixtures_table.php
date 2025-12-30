<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fixtures', function (Blueprint $table) {
            $table->foreignId('toss_winner_team_id')->nullable()->after('penalty_winner_team_id')->constrained('league_teams')->onDelete('set null');
            $table->boolean('toss_conducted')->default(false)->after('toss_winner_team_id');
            $table->timestamp('toss_conducted_at')->nullable()->after('toss_conducted');
        });
    }

    public function down(): void
    {
        Schema::table('fixtures', function (Blueprint $table) {
            $table->dropForeign(['toss_winner_team_id']);
            $table->dropColumn(['toss_winner_team_id', 'toss_conducted', 'toss_conducted_at']);
        });
    }
};
