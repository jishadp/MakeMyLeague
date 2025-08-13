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
        // Add auction fields to league_team pivot
        Schema::table('league_team', function (Blueprint $table) {
            $table->string('name')->after('team_id');
            $table->foreignId('owner_id')->nullable()->constrained('users')->onDelete('set null')->after('name');
            $table->decimal('purse_balance', 10, 2)->default(0)->after('owner_id');
            $table->decimal('initial_purse_balance', 10, 2)->default(0)->after('purse_balance');
        });
        
        // Add auction fields to league_player pivot
        Schema::table('league_player', function (Blueprint $table) {
            $table->decimal('bid_amount', 10, 2)->nullable()->after('player_id');
            $table->enum('auction_status', ['sold', 'unsold', 'skip'])->nullable()->after('bid_amount');
            $table->foreignId('league_team_id')->nullable()->constrained('league_team')->onDelete('set null')->after('auction_status');
            $table->index('auction_status');
        });
    }

    public function down(): void
    {
        Schema::table('league_team', function (Blueprint $table) {
            $table->dropColumn(['name', 'owner_id', 'purse_balance', 'initial_purse_balance']);
        });
        
        Schema::table('league_player', function (Blueprint $table) {
            $table->dropColumn(['bid_amount', 'auction_status', 'league_team_id']);
        });
    }
};
