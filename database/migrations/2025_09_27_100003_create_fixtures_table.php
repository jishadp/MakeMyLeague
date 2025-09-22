<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fixtures', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->foreignId('league_id')->constrained('leagues')->onDelete('cascade');
            $table->foreignId('home_team_id')->nullable()->constrained('league_teams')->onDelete('cascade');
            $table->foreignId('away_team_id')->nullable()->constrained('league_teams')->onDelete('cascade');
            $table->foreignId('league_group_id')->nullable()->constrained('league_groups')->onDelete('cascade');
            $table->enum('match_type', ['group_stage', 'quarter_final', 'semi_final', 'final'])->default('group_stage');
            $table->enum('status', ['unscheduled', 'scheduled', 'in_progress', 'completed', 'cancelled'])->default('unscheduled');
            $table->date('match_date')->nullable();
            $table->time('match_time')->nullable();
            $table->string('venue')->nullable();
            $table->integer('home_score')->nullable();
            $table->integer('away_score')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['league_id', 'status']);
            $table->index(['league_id', 'match_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fixtures');
    }
};