<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dashboard\BranchWebController;
use App\Http\Controllers\Dashboard\CustomerWebController;
use App\Http\Controllers\Dashboard\AgentWebController;
use App\Http\Controllers\Dashboard\PackageWebController;
use App\Http\Controllers\Dashboard\BookingWebController;
use App\Http\Controllers\Dashboard\PaymentWebController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Dashboard\NotificationWebController;
use App\Http\Controllers\Dashboard\UserWebController;

Route::get('/', function () {
    return view('welcome');
});




Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');



    Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['verified'])->name('dashboard');

    Route::resource('branches', BranchWebController::class);
    Route::resource('customers', CustomerWebController::class);
    Route::resource('agents', AgentWebController::class);
    Route::resource('packages', PackageWebController::class);
    Route::resource('bookings', BookingWebController::class);
    Route::resource('payments', PaymentWebController::class);
    Route::get('/notifications/create', [NotificationWebController::class, 'create'])->name('notifications.create');
    Route::post('/notifications/send', [NotificationWebController::class, 'send'])->name('notifications.send');
    Route::resource('users', UserWebController::class);
});

require __DIR__ . '/auth.php';
