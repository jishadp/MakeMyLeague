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
        Schema::table('league_players', function (Blueprint $table) {
            $table->foreignId('league_player_category_id')->nullable()->constrained('league_player_categories')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('league_players', function (Blueprint $table) {
            $table->dropForeign(['league_player_category_id']);
            $table->dropColumn('league_player_category_id');
        });
    }
};
