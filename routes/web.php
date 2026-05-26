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
use App\Http\Controllers\Nasabah\DashboardController as NasabahDashboardController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// ==========================================
// RUTE UTAMA: LANGSUNG REDIRECT KE LOGIN
// ==========================================
Route::get('/', function () {
    return redirect()->route('login');
});

// ==========================================
// RUTE JEMBATAN PENGARAH (REDIRECT ROLE)
// ==========================================
Route::get('/dashboard', function () {
    if (auth()->user()->role === 'admin' || auth()->user()->role === 'super_admin') {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('nasabah.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


// ==========================================
// ROUTE KHUSUS ADMIN & SUPER ADMIN
// ==========================================
Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    
    // Fitur Ekspor Excel Dashboard
    Route::get('/dashboard/export', [AdminDashboardController::class, 'exportExcel'])->name('dashboard.export');
    
    Route::resource('wastes', WasteController::class);
    
    // Rute Tambahan untuk Mengubah Role Pengguna
    Route::patch('nasabah/{nasabah}/change-role', [NasabahController::class, 'changeRole'])->name('nasabah.change-role');
    Route::resource('nasabah', NasabahController::class);

    // Kelola Admin/Super Admin (Proteksi dipindah ke dalam Controller)
    Route::resource('users', UserController::class);

    // Loket Setor Sampah Nasabah (Ditambahkan 'create')
    Route::resource('deposits', DepositController::class)->only(['index', 'create', 'store', 'destroy']);

    // Loket Tarik Saldo Tunai Nasabah (Ditambahkan 'create')
    Route::resource('withdrawals', WithdrawalController::class)->only(['index', 'create', 'store', 'destroy']);

    // Modul Jual Ke Pengepul Besar (Ditambahkan 'create')
    Route::resource('collector-sales', CollectorSaleController::class)->only(['index', 'create', 'store', 'destroy']);

    Route::get('gudang', [GudangController::class, 'index'])->name('gudang.index');
});

// ==========================================
// ROUTE KHUSUS NASABAH
// ==========================================
Route::middleware(['auth', 'verified'])->prefix('nasabah')->name('nasabah.')->group(function () {
    Route::get('/dashboard', [NasabahDashboardController::class, 'index'])->name('dashboard');
});

// ==========================================
// ROUTE PROFILE
// ==========================================
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/generate-symlink', function () {
    // Menghapus link lama jika masih tersisa untuk memastikan kebersihan path
    $shortcut = public_path('storage');
    if (file_exists($shortcut) || is_link($shortcut)) {
        @unlink($shortcut);
    }

    // Memicu perintah storage:link bawaan Laravel
    Artisan::call('storage:link');

    return 'Symlink gudang berhasil diperbarui!';
});

require __DIR__.'/auth.php';

// ==========================================
// OVERRIDE: NONAKTIFKAN TRANSAKSI REGISTRASI
// ==========================================
Route::get('/register', function () { abort(404); });
Route::post('/register', function () { abort(404); });