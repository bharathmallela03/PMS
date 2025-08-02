<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Authentication routes
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    // Customer Dashboard
    Route::get('customer/dashboard', [CustomerController::class, 'dashboard']);

    // Medicine
    Route::get('medicines', [CustomerController::class, 'medicines']);
    Route::get('medicines/{id}', [CustomerController::class, 'showMedicine']);

    // Cart
    Route::get('cart', [CustomerController::class, 'cart']);
    Route::post('cart/add', [CustomerController::class, 'addToCart']);
    Route::delete('cart/remove/{id}', [CustomerController::class, 'removeFromCart']);

    // Orders
    Route::get('orders', [CustomerController::class, 'orders']);
    Route::get('orders/{id}', [CustomerController::class, 'showOrder']);
    Route::post('orders/place', [CustomerController::class, 'placeOrder']);
    Route::post('orders/cancel/{id}', [CustomerController::class, 'cancelOrder']);

    // Profile
    Route::get('profile', [CustomerController::class, 'profile']);
    Route::post('profile/update', [CustomerController::class, 'updateProfile']);
    Route::post('password/update', [CustomerController::class, 'updatePassword']);

    // Addresses
    Route::get('addresses', [CustomerController::class, 'addresses']);
    Route::post('addresses/store', [CustomerController::class, 'storeAddress']);
    Route::put('addresses/update/{id}', [CustomerController::class, 'updateAddress']);
    Route::delete('addresses/delete/{id}', [CustomerController::class, 'deleteAddress']);
});
