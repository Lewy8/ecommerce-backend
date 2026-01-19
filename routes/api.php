<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/categories', [App\Http\Controllers\API\CategoryController::class, 'index']);
Route::get('/products', [App\Http\Controllers\API\ProductController::class, 'index']);
Route::get('/products/{slug}', [App\Http\Controllers\API\ProductController::class, 'show']);
Route::post('/orders', [App\Http\Controllers\API\OrderController::class, 'store']);
