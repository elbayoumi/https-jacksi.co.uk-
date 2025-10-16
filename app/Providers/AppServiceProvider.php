<?php

namespace App\Providers;

use App\Contracts\InvoiceServiceInterface;
use App\Services\InvoiceService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
            $this->app->bind(InvoiceServiceInterface::class, InvoiceService::class);

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
