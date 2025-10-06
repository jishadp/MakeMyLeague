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
            $table->string('logo')->nullable()->after('name');
            $table->string('banner')->nullable()->after('logo');
            $table->decimal('winner_prize', 10, 2)->nullable()->after('team_wallet_limit');
            $table->decimal('runner_prize', 10, 2)->nullable()->after('winner_prize');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leagues', function (Blueprint $table) {
            $table->dropColumn(['logo', 'banner', 'winner_prize', 'runner_prize']);
        });
    }
};
