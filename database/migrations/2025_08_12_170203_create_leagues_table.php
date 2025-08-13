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
        Schema::create('leagues', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->date('start_date');
            $table->date('end_date');
            $table->foreignId('manager_id')->constrained('users')->onDelete('cascade');
            $table->enum('status', ['Pending', 'Active', 'Completed'])->default('Pending');
            $table->decimal('purse_balance', 10, 2)->default(10000000);
            $table->integer('min_players_needed')->default(11);
            $table->decimal('min_bid_amount', 10, 2)->default(100000);
            $table->boolean('auction_started')->default(false);
            $table->timestamps();
            
            $table->index('manager_id');
            $table->index('uuid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leagues');
    }
};
