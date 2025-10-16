<?php

use Illuminate\Support\Facades\Route;
Route::middleware('auth:sanctum')->group(function () {
    Route::middleware('abilities:seller,admin')->group(function () {
        Route::get('invoices', [InvoiceController::class,'index']);
        Route::post('clients', [ClientController::class,'store']);
        Route::post('invoices', [InvoiceController::class,'store']);
    });
    Route::middleware('abilities:admin')->group(function () {
        Route::get('admin/stats', [StatsApiController::class,'index']);
        Route::patch('admin/sellers/{seller}/toggle', [Admin\SellerController::class,'update']);
    });
});
