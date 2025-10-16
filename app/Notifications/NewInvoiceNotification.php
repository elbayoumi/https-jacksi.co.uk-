<?php

namespace App\Notifications;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Class NewInvoiceNotification
 *
 * Simple notification to inform admins about a newly created invoice.
 * Channels: mail + database (adjust as you need).
 */
class NewInvoiceNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Invoice $invoice) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Invoice Created: ' . $this->invoice->number)
            ->greeting('Hello Admin,')
            ->line('A new invoice has been created.')
            ->line('Invoice #: ' . $this->invoice->number)
            ->line('Total: ' . number_format($this->invoice->total, 2))
            ->action('View Dashboard', url('/admin/dashboard'))
            ->line('Thank you.');
    }

    public function toArray($notifiable): array
    {
        return [
            'invoice_id' => $this->invoice->id,
            'number'     => $this->invoice->number,
            'total'      => $this->invoice->total,
        ];
    }
}
