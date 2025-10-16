<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Seeds a default super admin account.
 */
class AdminSeeder extends Seeder
{
    public function run(): void
    {
        Admin::query()->firstOrCreate(
            ['email' => 'mohamedashrafelbayoumi@gmail.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('mohamedashrafelbayoumi@gmail.com'),
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
    }
}
