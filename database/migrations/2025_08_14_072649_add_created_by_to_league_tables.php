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
            $table->foreignId('created_by')->nullable()->after('wallet_balance')->constrained('users')->nullOnDelete();
        });

        Schema::table('league_players', function (Blueprint $table) {
            $table->foreignId('created_by')->nullable()->after('base_price')->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('league_teams', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropColumn('created_by');
        });

        Schema::table('league_players', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropColumn('created_by');
        });
    }
};
