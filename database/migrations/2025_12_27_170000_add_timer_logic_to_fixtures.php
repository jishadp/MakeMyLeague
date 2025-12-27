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
        Schema::table('fixtures', function (Blueprint $table) {
            $table->string('match_state')->default('NOT_STARTED')->after('status'); // NOT_STARTED, FIRST_HALF, HALF_TIME, SECOND_HALF, EXTRA_TIME_FIRST, EXTRA_TIME_BREAK, EXTRA_TIME_SECOND, FULL_TIME
            $table->integer('current_minute')->default(0)->after('match_state');
            $table->boolean('is_running')->default(false)->after('current_minute');
            $table->integer('injury_time')->default(0)->after('is_running');
            $table->integer('half_duration')->default(45)->after('injury_time'); // Duration of a half in minutes
            $table->timestamp('last_tick_at')->nullable()->after('half_duration');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fixtures', function (Blueprint $table) {
            $table->dropColumn([
                'match_state',
                'current_minute',
                'is_running',
                'injury_time',
                'half_duration',
                'last_tick_at'
            ]);
        });
    }
};
