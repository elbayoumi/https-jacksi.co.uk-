<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SellerAuthController;
use App\Http\Controllers\Seller\DashboardController;
use App\Http\Controllers\Seller\ClientController;
use App\Http\Controllers\Seller\InvoiceController;
use App\Http\Controllers\Seller\InvoicePdfController;

/*
|--------------------------------------------------------------------------
| Seller Web Routes
|--------------------------------------------------------------------------
|
| All seller routes live under the "/seller" prefix and share the "seller."
| route-name prefix. We split guest-only auth screens from protected
| dashboard routes. Keep this file focused and predictable.
|
| URL Prefix: /seller
| Name Prefix: seller.
|
*/

Route::prefix('seller')->as('seller.')->group(function () {

    /**
     * Guest-only routes (no authenticated seller session)
     */
    Route::middleware(['web', 'guest:seller'])->group(function () {
        // Login form + submission
        Route::get('login', [SellerAuthController::class, 'showLogin'])->name('login');
        Route::post('login', [SellerAuthController::class, 'login'])->name('login.post');
    });

    /**
     * Authenticated seller routes
     * Prefer the alias 'auth.seller' if you registered it; otherwise use 'auth:seller'.
     */
    Route::middleware(['web', 'auth.seller'])->group(function () {
        // Dashboard (controller or simple view)
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        // Alternatively:
        // Route::get('/', fn () => view('seller.dashboard'))->name('dashboard');

        // Core resources
        Route::resource('clients', ClientController::class);

        // Invoices
        Route::resource('invoices', InvoiceController::class);
        Route::get('invoices/{invoice}/pdf', [InvoicePdfController::class, 'show'])
            ->name('invoices.pdf');

        // Logout
        Route::post('logout', [SellerAuthController::class, 'logout'])->name('logout');
    });

    /**
     * Optional: scoped 404 for seller area (keeps leakage out of global fallback)
     */
    // Route::fallback(fn () => abort(404));
});
