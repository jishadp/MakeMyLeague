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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('type'); // 'auctioneer_assigned', 'auctioneer_removed', etc.
            $table->string('title');
            $table->text('message');
            $table->json('data')->nullable(); // Additional data like league_id, team_id, etc.
            $table->boolean('read')->default(false);
            $table->timestamps();
            
            $table->index(['user_id', 'read']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
