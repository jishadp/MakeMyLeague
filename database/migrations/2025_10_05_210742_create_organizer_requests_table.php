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
        Schema::create('organizer_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('league_id')->constrained('leagues')->onDelete('cascade');
            $table->text('message')->nullable(); // User's request message
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('admin_notes')->nullable(); // Admin's response
            $table->foreignId('reviewed_by')->nullable()->constrained('users'); // Admin who reviewed
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
            
            // Prevents duplicate requests
            $table->unique(['user_id', 'league_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organizer_requests');
    }
};