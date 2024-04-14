<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\TableController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Product Routes
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/search', [ProductController::class, 'search']);
Route::get('/products/{id}', [ProductController::class, 'show']);

// Category Routes
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{id}', [CategoryController::class, 'show']);
Route::get('/categories/{category}/products', [CategoryController::class, 'findProductsByCategory']);

// Order Routes
Route::delete('/orders/{id}', [OrderController::class, 'destroy']);
Route::get('/orders', [OrderController::class, 'index']);
Route::get('/orders/{id}', [OrderController::class, 'show']);
Route::put('/orders/{id}/change-status', [OrderController::class, 'updateStatus']);
Route::post('/orders', [OrderController::class, 'store']);

// Table Routes
Route::get('/tables', [TableController::class, 'index']);
Route::get('/tables/{id}', [TableController::class, 'show']);
Route::get('/tables/{id}/orders', [TableController::class, 'getOrdersForTable']);

// Review Routes
Route::get('/reviews', [ReviewController::class, 'index']);
Route::post('/reviews', [ReviewController::class, 'store']);
