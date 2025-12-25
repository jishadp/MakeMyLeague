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
        Schema::table('league_teams', function (Blueprint $table) {
            $table->integer('played')->default(0);
            $table->integer('won')->default(0);
            $table->integer('lost')->default(0);
            $table->integer('drawn')->default(0);
            $table->integer('points')->default(0);
            $table->integer('goals_for')->default(0);
            $table->integer('goals_against')->default(0);
            $table->integer('goal_difference')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('league_teams', function (Blueprint $table) {
            $table->dropColumn([
                'played', 'won', 'lost', 'drawn', 'points', 
                'goals_for', 'goals_against', 'goal_difference'
            ]);
        });
    }
};
