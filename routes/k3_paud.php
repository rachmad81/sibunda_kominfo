<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\K3_PAUD\DashboardController;
use App\Http\Controllers\K3_PAUD\AnggotaController;

Route::name('k3_paud-')->prefix('k3_paud')->group(function () {
  Route::name('dashboard-')->prefix('dashboard')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('index');
    Route::post('/get_widget', [DashboardController::class, 'get_widget'])->name('get_widget');
  });
});

Route::name('paud_sd-')->prefix('paud_sd')->group(function () {
  Route::name('dashboard-')->prefix('dashboard')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('index');
    Route::post('/get_widget', [DashboardController::class, 'get_widget'])->name('get_widget');
  });
});

Route::name('mitra-')->prefix('mitra')->group(function () {
  Route::name('dashboard-')->prefix('dashboard')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('index');
    Route::post('/get_widget', [DashboardController::class, 'get_widget'])->name('get_widget');
  });
});

Route::name('keanggotaan-')->group(function () {
  Route::match(['get', 'post'], 'keanggotaan/{halaman}', [AnggotaController::class, 'halaman'])->name('halaman');
});
