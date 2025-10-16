<?php

namespace App\Providers;

use App\Contracts\InvoiceServiceInterface;
use App\Services\InvoiceService;
use Illuminate\Support\ServiceProvider;
use App\Contracts\ClientRepositoryInterface;
use App\Repositories\ClientRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(InvoiceServiceInterface::class, InvoiceService::class);
        $this->app->bind(ClientRepositoryInterface::class, ClientRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
