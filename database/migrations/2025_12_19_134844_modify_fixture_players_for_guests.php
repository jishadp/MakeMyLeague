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
        Schema::table('fixture_players', function (Blueprint $table) {
            $table->unsignedBigInteger('player_id')->nullable()->change();
            $table->string('custom_name')->nullable()->after('player_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fixture_players', function (Blueprint $table) {
            // We cannot easily revert nullable to not null if nulls exist, 
            // but for down() we assume we can.
            // We might need to delete records with null player_id first.
            DB::table('fixture_players')->whereNull('player_id')->delete();
            $table->unsignedBigInteger('player_id')->nullable(false)->change();
            $table->dropColumn('custom_name');
        });
    }
};
