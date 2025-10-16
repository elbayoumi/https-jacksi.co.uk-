<?php

namespace App\Listeners;

use App\Events\InvoiceCreated;
use App\Models\Admin;
use App\Notifications\NewInvoiceNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

/**
 * Class LogInvoiceCreated
 *
 * Handles the InvoiceCreated event:
 *  - Logs a structured line for observability.
 *  - (Optional) Notifies active admins about the new invoice.
 *
 * Implements ShouldQueue for non-blocking UX when notifications are enabled.
 */
class LogInvoiceCreated implements ShouldQueue
{
    /**
     * Handle the event.
     */
    public function handle(InvoiceCreated $event): void
    {
        $invoice = $event->invoice;

        // Structured logging for easy ingestion by log aggregators (ELK/Splunk).
        Log::info('Invoice created', [
            'invoice_id' => $invoice->id,
            'number'     => $invoice->number,
            'seller_id'  => $invoice->seller_id,
            'client_id'  => $invoice->client_id,
            'total'      => $invoice->total,
            'created_at' => $invoice->created_at?->toISOString(),
        ]);

        // OPTIONAL: Notify all active admins (toggle as needed).
        // Make sure you have mail set up if you plan to email.
        $notifyAdmins = config('app.notify_admins_on_invoice', false);

        if ($notifyAdmins) {
            $admins = Admin::query()->where('is_active', true)->get();
            if ($admins->isNotEmpty()) {
                Notification::send($admins, new NewInvoiceNotification($invoice));
            }
        }
    }
}
