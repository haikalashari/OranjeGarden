<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PlantController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\LoginController;

Route::redirect('/', '/login');

// Login routes
Route::get('/login', [LoginController::class, 'tampilkanLogin'])->name('login');
Route::post('/login', [LoginController::class, 'login']);

// Logout route
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'tampilkanDashboard'])->name('dashboard');
});



Route::controller(PlantController::class)->group(function () {
    Route::get('/dashboard/plant', 'tampilkanDataPlant')->name('dashboard.kelola.plant');
});

Route::controller(OrderController::class)->group(function () {
    Route::get('/dashboard/orders', 'tampilkanDataOrder')->name('dashboard.kelola.order');
});

Route::controller(CustomerController::class)->group(function () {
    Route::get('/dashboard/customers', 'tampilkanDataCustomer')->name('dashboard.kelola.customer');
});

Route::controller(DeliveryController::class)->group(function () {
    Route::get('/dashboard/deliveries', 'tampilkanDataDelivery')->name('dashboard.kelola.delivery');
});


// require __DIR__.'/auth.php';
