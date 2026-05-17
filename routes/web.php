<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\DepositController;
use App\Http\Controllers\Admin\NasabahController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\WasteController;
use App\Http\Controllers\Admin\WithdrawalController;
use App\Http\Controllers\Admin\CollectorSaleController; 
use App\Http\Controllers\Admin\GudangController;
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
    
    // Fitur Ekspor Excel Dashboard (Sudah diperbaiki penamaannya)
    Route::get('/dashboard/export', [AdminDashboardController::class, 'exportExcel'])->name('dashboard.export');
    
    Route::resource('wastes', WasteController::class);
    
    // Rute Tambahan untuk Mengubah Role Pengguna
    Route::patch('nasabah/{nasabah}/change-role', [NasabahController::class, 'changeRole'])->name('nasabah.change-role');
    Route::resource('nasabah', NasabahController::class);

    // Kelola Admin/Super Admin
    Route::resource('users', UserController::class);

    // Loket Setor Sampah Nasabah
    Route::resource('deposits', DepositController::class)->only(['index', 'store', 'destroy']);

    // Loket Tarik Saldo Tunai Nasabah
    Route::resource('withdrawals', WithdrawalController::class)->only(['index', 'store', 'destroy']);

    // Modul Jual Ke Pengepul Besar
    Route::resource('collector-sales', CollectorSaleController::class)->only(['index', 'store', 'destroy']);

    Route::get('gudang', [GudangController::class, 'index'])->name('gudang.index');
});

// Route Profile bawaan Breeze
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';