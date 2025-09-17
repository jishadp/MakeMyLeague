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
            $table->foreignId('league_id')->constrained('leagues')->onDelete('cascade');
            $table->foreignId('ground_id')->constrained('grounds')->onDelete('cascade');
            $table->timestamps();
        });

        // Optional: remove old ground_id column from leagues
        Schema::table('leagues', function (Blueprint $table) {
            $table->dropForeign(['ground_id']);
            $table->dropColumn('ground_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leagues', function (Blueprint $table) {
            $table->foreignId('ground_id')->nullable()->constrained('grounds')->onDelete('set null');
        });

        Schema::dropIfExists('league_grounds');
    }
};
