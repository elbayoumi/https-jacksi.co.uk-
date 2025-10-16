<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;

/**
 * Seeds the Super Admin using the AdminFactory.
 *
 * Uses the factory for flexibility and consistency.
 * Safe to re-run (idempotent).
 */
class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Admin::factory()->count(3)->inactive()->create();

        // Ensure a single verified, active Super Admin
        Admin::factory()
            ->withPassword('mohamedashrafelbayoumi@gmail.com')
            ->state([
                'name'  => 'Super Admin',
                'email' => 'mohamedashrafelbayoumi@gmail.com',
                'is_active' => true,
            ])
            ->create();

        $this->command->info('✅ Super Admin created via factory.');
    }
}
