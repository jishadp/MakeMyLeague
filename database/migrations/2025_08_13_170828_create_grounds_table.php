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
        Schema::create('grounds', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('address')->nullable();
            $table->foreignId('localbody_id')->constrained('local_bodies')->onDelete('cascade');
            $table->foreignId('district_id')->constrained()->onDelete('cascade');
            $table->foreignId('state_id')->constrained()->onDelete('cascade');
            $table->integer('capacity')->nullable();
            $table->text('description')->nullable();
            $table->string('contact_person')->nullable();
            $table->string('contact_phone')->nullable();
            $table->boolean('is_available')->default(true);
            $table->string('image')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grounds');
    }
};
