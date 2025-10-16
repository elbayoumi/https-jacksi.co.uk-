<?php

namespace Database\Factories;

use App\Models\InvoiceItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvoiceItemFactory extends Factory
{
    protected $model = InvoiceItem::class;

    public function definition(): array
    {
        $qty = fake()->numberBetween(1, 5);
        $price = fake()->randomFloat(2, 10, 200);

        return [
            // invoice_id is set when attaching to an invoice
            'product_name' => fake()->words(2, true),
            'quantity' => $qty,
            'price' => $price,
            'total' => $qty * $price, // denormalized subtotal per line
        ];
    }
}
