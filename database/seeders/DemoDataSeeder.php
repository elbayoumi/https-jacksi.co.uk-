<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Seller;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Seeds clients + invoices distribution across existing sellers.
 * Target: 15 clients, 40 invoices with 1-4 items each.
 */
class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $sellers = Seller::all();

        if ($sellers->isEmpty()) {
            $this->command->warn('No sellers found. Run SellerSeeder first.');
            return;
        }

        // Create 15 clients distributed across sellers
        $clients = collect();
        foreach ($sellers as $seller) {
            // 15 total across all sellers (approx evenly)
            $perSeller = max(1, intdiv(15, max(1, $sellers->count())));
            $created = Client::factory($perSeller)->make()->each(function ($client) use ($seller) {
                $client->seller_id = $seller->id;
                $client->save();
            });

            $clients = $clients->merge($created);
        }

        // Adjust in case integer division left us short
        while ($clients->count() < 15) {
            $seller = $sellers->random();
            $extra = Client::factory()->create(['seller_id' => $seller->id]);
            $clients->push($extra);
        }

        // Create 40 invoices across random clients/sellers
        $targetInvoices = 40;
        for ($i = 0; $i < $targetInvoices; $i++) {
            $client = $clients->random();
            $sellerId = $client->seller_id;

            $invoice = Invoice::factory()->create([
                'seller_id' => $sellerId,
                'client_id' => $client->id,
                'subtotal' => 0,
                'tax' => 0,
                'total' => 0,
            ]);

            // Add 1-4 items
            $itemsCount = random_int(1, 4);
            $subtotal = 0;

            for ($j = 0; $j < $itemsCount; $j++) {
                $item = InvoiceItem::factory()->make();
                $subtotal += $item->total;
                $invoice->items()->create($item->toArray());
            }

            // Apply a simple tax rule (14%) for demo
            $tax = round($subtotal * 0.14, 2);
            $total = $subtotal + $tax;

            $invoice->update(compact('subtotal', 'tax', 'total'));
        }

        $this->command->info('Demo data seeded: 15 clients & 40 invoices.');
    }
}
