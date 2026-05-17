<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\AdminDashboardController; // <-- Tambahkan import Controller Admin
use App\Http\Controllers\Admin\NasabahController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\WasteController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

// Route bawaan Breeze (Nantinya cocok untuk Dashboard Nasabah)
Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// ==========================================
// ROUTE KHUSUS ADMIN
// ==========================================
Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::resource('wastes', WasteController::class);
    // Rute Tambahan untuk Mengubah Role Pengguna
    Route::patch('nasabah/{nasabah}/change-role', [NasabahController::class, 'changeRole'])->name('nasabah.change-role');
    Route::resource('nasabah', NasabahController::class);

    // Tambahkan baris ini untuk Kelola Admin/Super Admin
    Route::resource('users', UserController::class);
});

// Route Profile bawaan Breeze
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';