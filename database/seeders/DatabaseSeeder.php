<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Order matters: create roles/owners first, then business data
        $this->call([
            AdminSeeder::class,
            SellerSeeder::class,
            DemoDataSeeder::class,
        ]);

        $this->command->info('Seeding finished. Credentials:');
        $this->command->warn('Admin: admin@example.com / password');
        $this->command->warn('Sellers: check "sellers" table — all use password "password".');
    }
}
