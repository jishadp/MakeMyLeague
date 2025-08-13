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
        Schema::create('league_grounds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('league_id')->constrained()->onDelete('cascade');
            $table->foreignId('ground_id')->constrained()->onDelete('cascade');
            $table->foreignId('localbody_id')->constrained('local_bodies')->onDelete('cascade');

            $table->string('name'); // Ground name
            $table->string('contact')->nullable(); // Contact person or phone number (optional)

            $table->timestamps();

            // Optional: prevent duplicate ground entries for same league
            // $table->unique(['league_id', 'ground_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('league_grounds');
    }
};
