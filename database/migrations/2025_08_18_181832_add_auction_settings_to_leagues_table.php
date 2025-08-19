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
            // Auction settings
            $table->enum('bid_increment_type', ['predefined', 'custom'])->default('predefined')->after('status');
            $table->decimal('custom_bid_increment', 10, 2)->nullable()->after('bid_increment_type');
            $table->json('predefined_increments')->nullable()->after('custom_bid_increment');
            $table->boolean('auction_active')->default(false)->after('predefined_increments');
            $table->timestamp('auction_started_at')->nullable()->after('auction_active');
            $table->timestamp('auction_ended_at')->nullable()->after('auction_started_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leagues', function (Blueprint $table) {
            $table->dropColumn([
                'bid_increment_type',
                'custom_bid_increment',
                'predefined_increments',
                'auction_active',
                'auction_started_at',
                'auction_ended_at'
            ]);
        });
    }
};
