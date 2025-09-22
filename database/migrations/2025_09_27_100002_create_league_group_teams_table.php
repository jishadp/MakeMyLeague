<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('league_group_teams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('league_group_id')->constrained('league_groups')->onDelete('cascade');
            $table->foreignId('league_team_id')->constrained('league_teams')->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['league_group_id', 'league_team_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('league_group_teams');
    }
};