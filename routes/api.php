<?php

use App\Http\Controllers\Api\AgentController;
use App\Http\Controllers\Api\BookingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController; 
use App\Http\Controllers\Api\BranchController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\PackageController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\PaymentGatewayController; 




Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);


Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::get('/user/customer-profile', [AuthController::class, 'getCustomerProfile']);
    Route::put('/user/customer-profile', [AuthController::class, 'updateCustomerProfile']); 
    Route::post('/user/save-device-token', [AuthController::class, 'saveDeviceToken']); 

    
    
    Route::apiResource('branches', BranchController::class);
    Route::apiResource('customers', CustomerController::class);
    Route::apiResource('agents', AgentController::class);
    Route::apiResource('packages', PackageController::class);
    Route::apiResource('bookings', BookingController::class);
    Route::apiResource('payments', PaymentController::class);
    Route::get('/my-bookings', [BookingController::class, 'myBookings']);
    Route::post('/payments/create-intent', [PaymentGatewayController::class, 'createPaymentIntent']);
    Route::post('/payments/confirm', [PaymentGatewayController::class, 'confirmPayment']);
    Route::get('/my-bookings', [BookingController::class, 'myBookings']);
    Route::post('/customer/bookings', [BookingController::class, 'storeCustomerBooking']); 


    
});
