<?php
// routes/web.php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HargaEmasController;
use App\Http\Controllers\PrediksiController;
use App\Http\Controllers\LaporanController;
use Illuminate\Support\Facades\Route;

// Halaman landing / welcome
Route::get('/', function () {
    return redirect()->route('login');
});

// Routes yang memerlukan autentikasi
Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/dashboard/update-harga', [DashboardController::class, 'updateHarga'])
        ->name('dashboard.update-harga');

    // Data Harga Emas
    Route::prefix('harga-emas')->name('harga-emas.')->group(function () {
        Route::get('/', [HargaEmasController::class, 'index'])->name('index');
        Route::post('/refresh', [HargaEmasController::class, 'refresh'])->name('refresh');
        Route::delete('/{id}', [HargaEmasController::class, 'destroy'])->name('destroy');
        Route::get('/grafik-bulanan', [HargaEmasController::class, 'grafikBulanan'])
            ->name('grafik-bulanan');
    });

    // Prediksi Harga
    Route::prefix('prediksi')->name('prediksi.')->group(function () {
        Route::get('/', [PrediksiController::class, 'index'])->name('index');
        Route::get('/create', [PrediksiController::class, 'create'])->name('create');
        Route::post('/generate', [PrediksiController::class, 'generate'])->name('generate');
        Route::get('/grafik', [PrediksiController::class, 'grafik'])->name('grafik');
    });

    // Laporan
    Route::prefix('laporan')->name('laporan.')->group(function () {
        Route::get('/', [LaporanController::class, 'index'])->name('index');
        Route::get('/download-pdf', [LaporanController::class, 'downloadPdf'])->name('download-pdf');
    });

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
