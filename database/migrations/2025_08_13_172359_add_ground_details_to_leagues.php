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
            $table->foreignId('ground_id')->nullable()->after('user_id')->constrained('grounds')->onDelete('set null')->comment('Ground ID for this league');
            $table->foreignId('localbody_id')->nullable()->after('ground_id')->constrained('local_bodies')->onDelete('set null');
            $table->string('venue_details')->nullable()->after('localbody_id')->comment('Additional venue information');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leagues', function (Blueprint $table) {
            $table->dropForeign(['ground_id']);
            $table->dropForeign(['localbody_id']);
            $table->dropColumn(['ground_id', 'localbody_id', 'venue_details']);
        });
    }
};
