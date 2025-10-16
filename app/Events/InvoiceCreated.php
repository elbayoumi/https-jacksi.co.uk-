<?php

namespace App\Events;

use App\Models\Invoice;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event fired whenever a new invoice is created.
 *
 * Listeners may log this, send notifications, or trigger analytics.
 */
class InvoiceCreated
{
    use Dispatchable, SerializesModels;

    public Invoice $invoice;

    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
    }
}
