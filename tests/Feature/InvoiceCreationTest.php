<?php

namespace Tests\Feature;

use App\Contracts\InvoiceServiceInterface;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Seller;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class InvoiceCreationTest
 *
 * Validates invoice creation flow end-to-end against the API.
 * Uses the seller guard context and asserts DB state is consistent.
 */
class InvoiceCreationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function seller_can_create_invoice_with_items_and_totals_via_api(): void
    {
        // Arrange
        $seller = Seller::factory()->create();
        $client = Client::factory()->create(['seller_id' => $seller->id]);

        $payload = [
            'client_id' => $client->id,
            'notes'     => 'Thanks for your business.',
            'items'     => [
                ['product_name' => 'Plan A', 'quantity' => 2, 'price' => 50],
                ['product_name' => 'Plan B', 'quantity' => 1, 'price' => 30],
            ],
        ];

        // Act (acting as seller guard for web session OR use Sanctum for API)
        $this->actingAs($seller, 'seller');

        $response = $this->postJson('/api/invoices', $payload);

        // Assert
        $response->assertCreated();
        $response->assertJsonPath('data.total', 130.0); // 2*50 + 1*30 = 130

        $this->assertDatabaseCount('invoices', 1);
        $this->assertDatabaseCount('invoice_items', 2);

        $invoice = Invoice::first();
        $this->assertEquals($seller->id, $invoice->seller_id);
        $this->assertEquals(130.0, (float) $invoice->total);
    }
}
