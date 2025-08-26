<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
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
    Route::resource('products', ProductController::class);
    Route::resource('levels', LevelController::class);
    Route::resource('customers', CustomerController::class);
});

require __DIR__.'/auth.php';
