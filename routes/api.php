<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductApiController;
use App\Http\Controllers\Api\CategoryApiController;
use App\Http\Controllers\Api\SupplierApiController;

/*
|--------------------------------------------------------------------------
| API Routes – Fase 2
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('products',   ProductApiController::class);
    Route::apiResource('categories', CategoryApiController::class)->except(['show']);
    Route::apiResource('suppliers',  SupplierApiController::class)->except(['show']);
});
