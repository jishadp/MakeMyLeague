<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('slug')->nullable()->after('name');
        });

        // Populate existing records with slugs
        $users = \App\Models\User::all();
        foreach ($users as $user) {
            $user->slug = $this->generateUniqueSlug($user->name, $user->id);
            $user->save();
        }

        // Make slug unique and not nullable
        Schema::table('users', function (Blueprint $table) {
            $table->string('slug')->unique()->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }

    /**
     * Generate unique slug for user
     */
    private function generateUniqueSlug($name, $userId)
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug;
        $counter = 1;

        while (\App\Models\User::where('slug', $slug)->where('id', '!=', $userId)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
};
