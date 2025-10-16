<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest:admin')->group(function () {
    Route::get('login', [AdminAuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AdminAuthController::class, 'login'])->name('login.post');
});
Route::middleware('auth:admin')->group(function () {
    Route::get('dashboard', fn() => view('admin.dashboard'))->name('dashboard');
    Route::resource('sellers', Admin\SellerController::class)->only(['index', 'update']);
    Route::post('logout', [AdminAuthController::class, 'logout'])->name('logout');
});
