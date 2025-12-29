<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::getConnection()->getDriverName() !== 'mysql') {
            return;
        }

        DB::statement("
            ALTER TABLE fixtures
            MODIFY match_type ENUM('group_stage','qualifier','eliminator','quarter_final','semi_final','final','third_place')
            DEFAULT 'group_stage'
        ");
    }

    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() !== 'mysql') {
            return;
        }

        DB::statement("
            ALTER TABLE fixtures
            MODIFY match_type ENUM('group_stage','quarter_final','semi_final','final')
            DEFAULT 'group_stage'
        ");
    }
};
