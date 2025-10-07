<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'mobile' => fake()->unique()->numerify('##########'),
            'country_code' => '+91',
            'pin' => static::$password ??= Hash::make('1234'),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Create a user with Organiser role.
     */
    public function organiser(): static
    {
        return $this->afterCreating(function (User $user) {
            $organiserRole = \App\Models\Role::where('name', 'Organiser')->first();
            if ($organiserRole) {
                $user->roles()->attach($organiserRole->id);
            }
        });
    }

    /**
     * Create a user with Owner role.
     */
    public function owner(): static
    {
        return $this->afterCreating(function (User $user) {
            $ownerRole = \App\Models\Role::where('name', 'Owner')->first();
            if ($ownerRole) {
                $user->roles()->attach($ownerRole->id);
            }
        });
    }

    /**
     * Create a user with Player role.
     */
    public function player(): static
    {
        return $this->afterCreating(function (User $user) {
            $playerRole = \App\Models\Role::where('name', 'Player')->first();
            if ($playerRole) {
                $user->roles()->attach($playerRole->id);
            }
        });
    }
}
