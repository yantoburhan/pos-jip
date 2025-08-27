<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductPendingController;
use App\Http\Controllers\LevelController;
use App\Http\Controllers\CustomerController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // bawaan dari npm breeze
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // route lengkap dari laravel yang menggunakan resource
    // untuk melihat method-nya dengan menggunakan perintah di terminal php artisan route:list
    Route::resource('users', UserController::class);
    Route::resource('roles', RoleController::class);
    Route::resource('levels', LevelController::class);
    Route::resource('customers', CustomerController::class);

    // --- Grup Route untuk Produk Pending ---
    Route::prefix('products/pending')->name('products.pending.')->controller(ProductPendingController::class)->group(function () {
        Route::get('/', 'index')->name('index'); // handles products.pending.index
        Route::post('/{pending}/approve', 'approve')->name('approve'); // handles products.pending.approve
        Route::post('/{pending}/reject', 'reject')->name('reject'); // handles products.pending.reject
        Route::post('/{pending}/cancel', 'cancel')->name('cancel'); // handles products.pending.cancel
        Route::get('/{pending}/edit', 'edit')->name('edit'); // handles products.pending.edit
        Route::put('/{pending}', 'update')->name('update'); // handles products.pending.update
    });
    Route::resource('products', ProductController::class);
});

require __DIR__.'/auth.php';
