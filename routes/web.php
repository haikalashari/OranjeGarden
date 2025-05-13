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
    });
});

Route::middleware('auth', 'role:admin,super admin')->group(function () {
    Route::controller(LoginController::class)->group(function () {
        Route::get('/dashboard/users', [LoginController::class, 'tampilkanKelolaUser'])->name('dashboard.kelola.user');
        Route::post('/register', [LoginController::class, 'register'])->name('register.submit');
        Route::delete('/dashboard/users/hapus/{id}', [LoginController::class, 'hapusUser'])->name('dashboard.kelola.user.hapus');
    });
});


// Logout route
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');


Route::middleware('auth', 'role:admin,super admin')->group(function () {
    Route::controller(DashboardController::class)->group(function () {
        Route::get('/dashboard', 'tampilkanDashboard')->name('dashboard.index');
    });

    Route::controller(PlantController::class)->group(function () {
        Route::get('/dashboard/plant', 'tampilkanDataPlant')->name('dashboard.kelola.plant');
        Route::post('/dashboard/plant', 'tambahPlant')->name('dashboard.kelola.plant.tambah');
        Route::put('/dashboard/plant/{id}', 'editPlant')->name('dashboard.kelola.plant.edit');
        Route::delete('/dashboard/plant/hapus/{id}', 'hapusPlant')->name('dashboard.kelola.plant.hapus');
    });

    Route::controller(OrderController::class)->group(function () {
        Route::get('/dashboard/orders', 'tampilkanDataOrder')->name('dashboard.kelola.order');
        Route::post('/dashboard/orders', 'tambahOrder')->name('dashboard.kelola.order.tambah');
        Route::get('/dashboard/orders/{id}', 'tampilkanDetailOrder')->name('dashboard.kelola.order.detail');
        Route::post('/dashboard/orders/{id}/newTanamanBatch', 'tambahTanamanBatchBaru')->name('dashboard.kelola.order.tambah.tanamanbatch');
        Route::post('/dashboard/orders/{id}/delivererAmbilKembali', 'assignDelivererPengambilanKembali')->name('dashboard.kelola.order.tambah.deliverer.ambilkembali');
        Route::get('/dashboard/orders/{id}/edit', 'tampilkanEditOrder')->name('dashboard.kelola.order.edit.tampilkan');
        Route::put('/dashboard/orders/{id}/edit', 'editOrder')->name('dashboard.kelola.order.edit');
        Route::delete('/dashboard/orders/hapus/{id}', 'hapusOrder')->name('dashboard.kelola.order.hapus');
        Route::put('/dashboard/orders/{id}/orderselesai', 'orderSelesai')->name('dashboard.kelola.order.selesai');
        Route::put('/dashboard/orders/{id}/orderbatalkan', 'orderDibatalkan')->name('dashboard.kelola.order.batalkan');
        Route::get('/dashboard/orders/{id}/generateInvoice', 'generateInvoice')->name('dashboard.kelola.order.generate.invoice');
        Route::post('/dashboard/orders/{id}/edit-payment-proof', 'editPaymentProof')->name('dashboard.kelola.order.editPaymentProof');
        Route::delete('/dashboard/orders/{id}/delete-payment-proof', 'deletePaymentProof')->name('dashboard.kelola.order.deletePaymentProof');
    });

    Route::controller(CustomerController::class)->group(function () {
        Route::get('/dashboard/customers', 'tampilkanDataCustomer')->name('dashboard.kelola.customer');
        Route::post('/dashboard/customers', 'tambahCustomer')->name('dashboard.kelola.customer.tambah');
        Route::put('/dashboard/customers/{id}', 'editCustomer')->name('dashboard.kelola.customer.edit');
        Route::delete('/dashboard/customers/hapus/{id}', 'hapusCustomer')->name('dashboard.kelola.customer.hapus');
    });
});

Route::middleware(['auth', 'role:admin,delivery,super admin'])->group(function () {
    Route::controller(DeliveryController::class)->group(function () {
        Route::get('/dashboard/deliveries', 'tampilkanDataDelivery')->name('dashboard.kelola.delivery');
        Route::get('/dashboard/deliveries/{id}', 'tampilkanDetailDelivery')->name('dashboard.kelola.delivery.detail');
        Route::post('/dashboard/deliveries/{id}/konfirmasi', 'konfirmasiDelivery')->name('dashboard.kelola.delivery.konfirmasi');
    });
});




// require __DIR__.'/auth.php';
