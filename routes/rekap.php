<?php

use App\Http\Controllers\Rekap\SatuanController;
use App\Http\Controllers\Rekap\UserController;
use App\Http\Controllers\Rekap\UmumController;
use App\Http\Controllers\Rekap\ProgramController;
use App\Http\Controllers\Rekap\ProfilController;
use App\Http\Controllers\Rekap\KegiatanController;

Route::prefix('rekap')->name('rekap-')->middleware('dinasAuth')->group(function () {
  // USER
  Route::prefix('user')->name('user-')->group(function () {
    Route::get('main', [UserController::class, 'main'])->name('main');
    Route::get('data', [UserController::class, 'data'])->name('data');
  });
  // PROFIL
  Route::prefix('profil')->name('profil-')->group(function () {
    Route::get('main', [ProfilController::class, 'main'])->name('main');
    Route::get('data', [ProfilController::class, 'data'])->name('data');
  });
  // SATUAN PAUD
  Route::prefix('satuan_paud')->name('satuan_paud-')->group(function () {
    Route::get('main', [SatuanController::class, 'main'])->name('main');
    Route::get('data', [SatuanController::class, 'data'])->name('data');
  });
  // TIMELINE
  Route::prefix('program')->name('program-')->group(function () {
    Route::get('main', [ProgramController::class, 'main'])->name('main');
    Route::get('data', [ProgramController::class, 'data'])->name('data');
  });
  // KEGIATAN
  Route::prefix('kegiatan')->name('kegiatan-')->group(function () {
    Route::get('main', [KegiatanController::class, 'main'])->name('main');
    Route::post('data', [KegiatanController::class, 'data'])->name('data');
    Route::post('export', [KegiatanController::class, 'export'])->name('export');
  });

  // UMUM
  Route::prefix('umum')->name('umum-')->group(function () {
    Route::post('get_kelurhaan', [UmumController::class, 'get_kelurahan'])->name('get_kelurahan');
  });
});
