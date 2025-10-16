<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\Admin\SellerController;

/*
|--------------------------------------------------------------------------
| Admin Web Routes
|--------------------------------------------------------------------------
|
| This file contains all routes related to the admin dashboard.
| It separates authentication (login/logout) from protected routes
| that require the "auth.admin" middleware.
|
| Prefix: /admin
| Route Name Prefix: admin.
|
*/

Route::prefix('admin')->as('admin.')->group(function () {

    /**
     * Guest-only routes (accessible only when admin is not logged in)
     */
    Route::middleware('guest:admin')->group(function () {
        Route::get('login', [AdminAuthController::class, 'showLogin'])
            ->name('login');

        Route::post('login', [AdminAuthController::class, 'login'])
            ->name('login.post');
    });

    /**
     * Authenticated admin routes (requires auth.admin middleware)
     */
    Route::middleware(['web', 'auth.admin'])->group(function () {

        // Admin Dashboard
        Route::get('dashboard', fn() => view('admin.dashboard'))
            ->name('dashboard');

        // Seller Management (index + update)
        Route::resource('sellers', SellerController::class)
            ->only(['index', 'update']);

        // Logout
        Route::post('logout', [AdminAuthController::class, 'logout'])
            ->name('logout');
    });
});
