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
            // Auction access control
            $table->boolean('auction_access_granted')->default(false)->after('auction_ended_at');
            $table->timestamp('auction_access_requested_at')->nullable()->after('auction_access_granted');
            $table->text('auction_access_notes')->nullable()->after('auction_access_requested_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leagues', function (Blueprint $table) {
            $table->dropColumn([
                'auction_access_granted',
                'auction_access_requested_at',
                'auction_access_notes'
            ]);
        });
    }
};