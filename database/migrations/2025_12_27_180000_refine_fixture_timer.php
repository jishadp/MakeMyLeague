<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fixtures', function (Blueprint $table) {
            // Drop old columns if they exist
            if (Schema::hasColumn('fixtures', 'current_period')) $table->dropColumn('current_period');
            if (Schema::hasColumn('fixtures', 'accumulated_seconds')) $table->dropColumn('accumulated_seconds');
            if (Schema::hasColumn('fixtures', 'is_paused')) $table->dropColumn('is_paused');
            if (Schema::hasColumn('fixtures', 'timer_last_updated_at')) $table->dropColumn('timer_last_updated_at');

            // Add new columns
            if (!Schema::hasColumn('fixtures', 'match_state')) {
                $table->string('match_state')->default('NOT_STARTED')->after('status');
            }
            if (!Schema::hasColumn('fixtures', 'current_minute')) {
                $table->integer('current_minute')->default(0)->after('match_state');
            }
            if (!Schema::hasColumn('fixtures', 'is_running')) {
                $table->boolean('is_running')->default(false)->after('current_minute');
            }
            if (!Schema::hasColumn('fixtures', 'last_tick_at')) {
                $table->timestamp('last_tick_at')->nullable()->after('is_running');
            }
            
            // Ensure added_time columns exist (they were in previous migration, but just in case)
            if (!Schema::hasColumn('fixtures', 'added_time_first_half')) {
                $table->integer('added_time_first_half')->default(0);
            }
            if (!Schema::hasColumn('fixtures', 'added_time_second_half')) {
                $table->integer('added_time_second_half')->default(0);
            }
        });
    }

    public function down(): void
    {
        Schema::table('fixtures', function (Blueprint $table) {
            $table->dropColumn(['match_state', 'current_minute', 'is_running', 'last_tick_at']);
            // We don't restore old columns as they are replaced
        });
    }
};
