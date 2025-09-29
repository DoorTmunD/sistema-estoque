<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductApiController;
use App\Http\Controllers\Api\CategoryApiController;
use App\Http\Controllers\Api\SupplierApiController;

// Proteção só por Sanctum, checagem de permissão/nivel é nas policies/controllers
Route::middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('products',   ProductApiController::class);
    Route::apiResource('categories', CategoryApiController::class)->except(['show']);
    Route::apiResource('suppliers',  SupplierApiController::class)->except(['show']);
});