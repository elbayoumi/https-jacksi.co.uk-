<?php

namespace Database\Seeders;

use App\Models\Seller;
use Illuminate\Database\Seeder;

/**
 * Seeds a small pool of active sellers.
 */
class SellerSeeder extends Seeder
{
    public function run(): void
    {
        // Create 3 active sellers
        Seller::factory()->count(3)->create();

        // Optionally print the created sellers to console
        $this->command->info('Sellers created with password: "password".');
    }
}
