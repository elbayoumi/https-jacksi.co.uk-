
<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SellerAuthController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest:seller')->group(function () {
Route::get('login', [SellerAuthController::class,'showLogin'])->name('login');
Route::post('login', [SellerAuthController::class,'login'])->name('login.post');
});
Route::middleware('auth:seller')->group(function () {
Route::get('dashboard', fn() => view('seller.dashboard'))->name('dashboard');
Route::resource('clients', Seller\ClientController::class);
Route::resource('invoices', Seller\InvoiceController::class);
Route::get('invoices/{invoice}/pdf', [Seller\InvoicePdfController::class,'show'])->name('invoices.pdf');
});
Route::prefix('seller')->name('seller.')->middleware('auth:seller')->group(function () {
    Route::resource('invoices', \App\Http\Controllers\Seller\InvoiceController::class);
});
