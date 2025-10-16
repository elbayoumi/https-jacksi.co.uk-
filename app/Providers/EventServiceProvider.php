<?php

namespace App\Providers;

use App\Events\InvoiceCreated;
use App\Listeners\LogInvoiceCreated;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

/**
 * Class EventServiceProvider
 *
 * Registers domain events and their listeners.
 */
class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        InvoiceCreated::class => [
            LogInvoiceCreated::class,
        ],
    ];

    public function boot(): void
    {
        //
    }
}
