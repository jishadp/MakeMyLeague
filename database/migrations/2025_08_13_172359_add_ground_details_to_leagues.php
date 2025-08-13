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
            $table->json('ground_ids')->nullable()->after('user_id')->comment('JSON array of ground IDs used in this league');
            $table->foreignId('localbody_id')->nullable()->after('ground_ids')->constrained('local_bodies')->onDelete('set null');
            $table->string('venue_details')->nullable()->after('localbody_id')->comment('Additional venue information');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leagues', function (Blueprint $table) {
            $table->dropForeign(['localbody_id']);
            $table->dropColumn(['ground_ids', 'localbody_id', 'venue_details']);
        });
    }
};
