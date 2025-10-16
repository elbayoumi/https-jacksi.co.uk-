<?php

namespace Database\Factories;

use App\Models\Invoice;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvoiceFactory extends Factory
{
    protected $model = Invoice::class;

    public function definition(): array
    {
        return [
            // seller_id & client_id are set in seeder
            'number' => 'INV-'.fake()->unique()->numerify('########'),
            'subtotal' => 0,
            'tax' => 0,
            'total' => 0,
        ];
    }
}
