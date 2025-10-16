<?php

namespace App\Services;

use App\Contracts\InvoiceServiceInterface;
use App\Events\InvoiceCreated;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Seller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Class InvoiceService
 *
 * Responsible for all business logic related to invoice lifecycle:
 * creation, update, deletion, and event dispatching.
 *
 * Keeps controllers clean by encapsulating all domain logic here.
 */
class InvoiceService implements InvoiceServiceInterface
{
    /**
     * Create a new invoice and its related items in a transaction.
     *
     * Automatically calculates the total and fires an InvoiceCreated event.
     */
    public function createInvoice(Seller $seller, array $data): Invoice
    {
        return DB::transaction(function () use ($seller, $data) {
            // 1️⃣ Create main invoice record
            $invoice = new Invoice();
            $invoice->seller_id = $seller->id;
            $invoice->client_id = $data['client_id'];
            $invoice->number = $this->generateInvoiceNumber($seller);
            $invoice->notes = $data['notes'] ?? null;
            $invoice->total = 0;
            $invoice->save();

            // 2️⃣ Insert related items
            $total = 0;
            foreach ($data['items'] as $itemData) {
                $item = new InvoiceItem();
                $item->invoice_id = $invoice->id;
                $item->product_name = $itemData['product_name'];
                $item->quantity = $itemData['quantity'];
                $item->price = $itemData['price'];
                $item->subtotal = $itemData['quantity'] * $itemData['price'];
                $item->save();

                $total += $item->subtotal;
            }

            // 3️⃣ Update total
            $invoice->update(['total' => $total]);

            // 4️⃣ Fire event
            event(new InvoiceCreated($invoice));

            return $invoice->load('items', 'client');
        });
    }

    /**
     * Update an invoice and its items safely in a transaction.
     */
    public function updateInvoice(Seller $seller, Invoice $invoice, array $data): Invoice
    {
        return DB::transaction(function () use ($seller, $invoice, $data) {
            // Authorization safeguard
            if ($invoice->seller_id !== $seller->id) {
                abort(403, 'Unauthorized invoice access.');
            }

            // Update invoice header
            $invoice->update([
                'client_id' => $data['client_id'],
                'notes' => $data['notes'] ?? $invoice->notes,
            ]);

            // Remove old items (simple approach)
            $invoice->items()->delete();

            // Insert new items
            $total = 0;
            foreach ($data['items'] as $itemData) {
                $item = new InvoiceItem([
                    'product_name' => $itemData['product_name'],
                    'quantity' => $itemData['quantity'],
                    'price' => $itemData['price'],
                    'subtotal' => $itemData['quantity'] * $itemData['price'],
                ]);
                $invoice->items()->save($item);
                $total += $item->subtotal;
            }

            $invoice->update(['total' => $total]);

            return $invoice->fresh()->load('items', 'client');
        });
    }

    /**
     * Delete an invoice and all related items.
     *
     * Could be replaced by soft deletes if needed.
     */
    public function deleteInvoice(Seller $seller, Invoice $invoice): void
    {
        if ($invoice->seller_id !== $seller->id) {
            abort(403, 'Unauthorized invoice deletion.');
        }

        DB::transaction(function () use ($invoice) {
            $invoice->items()->delete();
            $invoice->delete();
        });
    }

    /**
     * Generate a unique, prefixed invoice number per seller.
     *
     * Format example: INV-23-00045
     */
    protected function generateInvoiceNumber(Seller $seller): string
    {
        $count = Invoice::where('seller_id', $seller->id)->count() + 1;
        return 'INV-' . date('y') . '-' . str_pad($count, 5, '0', STR_PAD_LEFT);
    }
}
