<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\DeliveryPersonController;
use App\Http\Controllers\DeliveryController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('products', ProductsController::class);
Route::apiResource('vendors', VendorController::class);
Route::apiResource('categories', CategoriesController::class);
Route::apiResource('orders', OrdersController::class);
Route::apiResource('delivery-persons', DeliveryPersonController::class);
Route::apiResource('deliveries', DeliveryController::class);