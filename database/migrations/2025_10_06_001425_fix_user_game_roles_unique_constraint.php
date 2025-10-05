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
        Schema::table('user_game_roles', function (Blueprint $table) {
            $table->dropUnique('user_primary_game_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_game_roles', function (Blueprint $table) {
            $table->unique(['user_id', 'is_primary'], 'user_primary_game_unique');
        });
    }
};