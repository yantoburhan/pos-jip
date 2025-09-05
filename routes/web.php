<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductPendingController;
use App\Http\Controllers\LevelController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // Bawaan dari Breeze
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Route Resource Lengkap
    Route::resource('users', UserController::class);
    Route::resource('roles', RoleController::class);
    Route::resource('levels', LevelController::class);
    Route::resource('products', ProductController::class);
    
    Route::get('/customers/search', [CustomerController::class, 'search'])->name('customers.search');
    Route::resource('customers', CustomerController::class);

    // --- Grup Route untuk Produk Pending ---
    Route::prefix('products/pending')->name('products.pending.')->controller(ProductPendingController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/{pending}/approve', 'approve')->name('approve');
        Route::post('/{pending}/reject', 'reject')->name('reject');
        Route::post('/{pending}/cancel', 'cancel')->name('cancel');
        Route::get('/{pending}/edit', 'edit')->name('edit');
        Route::put('/{pending}', 'update')->name('update');
    });
    
    // --- Route untuk Transaksi (Ini akan membuat transactions.show, .edit, .destroy, dll.) ---
    Route::resource('transactions', TransactionController::class);
     Route::prefix('transactions')->name('transactions.')->controller(TransactionController::class)->group(function () {
        Route::get('/pending/list', 'pending')->name('pending');
        Route::patch('/{transaction}/approve', 'approve')->name('approve');
        Route::delete('/{transaction}/reject', 'reject')->name('reject');
    });
    // --- Route untuk Pencarian (AJAX) ---
    Route::get('/search/customers', [TransactionController::class, 'searchCustomers'])->name('search.customers');
    Route::get('/search/products', [TransactionController::class, 'searchProducts'])->name('search.products');
});

require __DIR__.'/auth.php';