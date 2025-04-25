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


Route::middleware('guest')->group(function () {
    Route::controller(LoginController::class)->group(function () {
        Route::get('/login', [LoginController::class, 'tampilkanLogin'])->name('login');
        Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
        Route::get('/register', [LoginController::class, 'tampilkanRegister'])->name('register');
        Route::post('/register', [LoginController::class, 'register'])->name('register.submit');
    });
});


// Logout route
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');


Route::middleware('auth')->group(function () {
    Route::controller(DashboardController::class)->group(function () {
        Route::get('/dashboard', 'tampilkanDashboard')->name('dashboard.index');
    });

    Route::controller(PlantController::class)->group(function () {
        Route::get('/dashboard/plant', 'tampilkanDataPlant')->name('dashboard.kelola.plant');
        Route::post('/dashboard/plant', 'tambahPlant')->name('dashboard.kelola.plant.tambah');
        Route::get('/dashboard/plant/{id}', 'detailPlant')->name('dashboard.kelola.plant.detail');
        Route::post('/dashboard/plant/{id}', 'editPlant')->name('dashboard.kelola.plant.edit');
        Route::post('/dashboard/plant/hapus/{id}', 'hapusPlant')->name('dashboard.kelola.plant.hapus');
    });

    Route::controller(OrderController::class)->group(function () {
        Route::get('/dashboard/orders', 'tampilkanDataOrder')->name('dashboard.kelola.order');
        Route::post('/dashboard/orders', 'tambahOrder')->name('dashboard.kelola.order.tambah');
        Route::post('/dashboard/orders/{id}', 'editOrder')->name('dashboard.kelola.order.edit');
        Route::post('/dashboard/orders/hapus/{id}', 'hapusOrder')->name('dashboard.kelola.order.hapus');
    });

    Route::controller(CustomerController::class)->group(function () {
        Route::get('/dashboard/customers', 'tampilkanDataCustomer')->name('dashboard.kelola.customer');
        Route::post('/dashboard/customers', 'tambahCustomer')->name('dashboard.kelola.customer.tambah');
        Route::post('/dashboard/customers/{id}', 'editCustomer')->name('dashboard.kelola.customer.edit');
        Route::post('/dashboard/customers/hapus/{id}', 'hapusCustomer')->name('dashboard.kelola.customer.hapus');
    });

    Route::controller(DeliveryController::class)->group(function () {
        Route::get('/dashboard/deliveries', 'tampilkanDataDelivery')->name('dashboard.kelola.delivery');
        Route::post('/dashboard/deliveries', 'tambahDelivery')->name('dashboard.kelola.delivery.tambah');
        Route::post('/dashboard/deliveries/{id}', 'editDelivery')->name('dashboard.kelola.delivery.edit');
        Route::post('/dashboard/deliveries/hapus/{id}', 'hapusDelivery')->name('dashboard.kelola.delivery.hapus');
    });
});




// require __DIR__.'/auth.php';
