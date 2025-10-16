<?php

namespace Database\Factories;

use App\Models\Admin;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * AdminFactory
 *
 * Generates realistic Admin records for seeding and tests.
 * - Uses secure password hashing.
 * - Includes helpful states (inactive, unverified).
 * - Provides a convenience helper to override the password.
 *
 * Usage:
 *   // Basic
 *   Admin::factory()->create();
 *
 *   // With a known password
 *   Admin::factory()->withPassword('secret123')->create(['email' => 'admin@example.com']);
 *
 *   // Inactive admin
 *   Admin::factory()->inactive()->create();
 *
 *   // Unverified email
 *   Admin::factory()->unverified()->create();
 */
class AdminFactory extends Factory
{
    /** @var class-string<\App\Models\Admin> */
    protected $model = Admin::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name'              => $this->faker->name(),
            'email'             => $this->faker->unique()->safeEmail(),
            'password'          => Hash::make('password'), // default test password
            'is_active'         => true,
            'email_verified_at' => now(),
            'remember_token'    => Str::random(10),
        ];
    }

    /**
     * State: mark admin as inactive (disabled from logging in).
     */
    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }

    /**
     * State: mark admin as unverified (email not verified yet).
     */
    public function unverified(): static
    {
        return $this->state(fn () => ['email_verified_at' => null]);
    }

    /**
     * Helper: override the default password with a custom one (hashed).
     *
     * @param  string  $plain  The plain text password to hash.
     */
    public function withPassword(string $plain): static
    {
        return $this->state(fn () => ['password' => Hash::make($plain)]);
    }
}
