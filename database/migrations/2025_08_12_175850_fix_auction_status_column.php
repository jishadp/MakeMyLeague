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
        Schema::table('league_player', function (Blueprint $table) {
            $table->dropColumn('auction_status');
        });
        
        Schema::table('league_player', function (Blueprint $table) {
            $table->string('auction_status', 10)->nullable()->after('bid_amount');
            $table->index('auction_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('league_player', function (Blueprint $table) {
            $table->dropColumn('auction_status');
        });
        
        Schema::table('league_player', function (Blueprint $table) {
            $table->enum('auction_status', ['sold', 'unsold', 'skip'])->nullable();
        });
    }
};
