<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dinas\UserController;
use App\Http\Controllers\Dinas\UserControllerBaru;
use App\Http\Controllers\Dinas\BundaController;
use App\Http\Controllers\Dinas\AllBundaController;
use App\Http\Controllers\Dinas\SurveyController;
use App\Http\Controllers\Dinas\Datapaud\WisataController;
use App\Http\Controllers\Dinas\Datapaud\PelatihanController;
use App\Http\Controllers\Dinas\LombaController;
use App\Http\Controllers\Dinas\MitraController;
use App\Http\Controllers\Dinas\SewaController;
use App\Http\Controllers\Penilai\PenilaianController;

Route::name('dinas-')->prefix('dinas')->group(function () {

  Route::match(['get', 'post'], 'user_baru/{halaman}', [UserControllerBaru::class, 'halaman'])->name('user_baru');

  Route::name('user-')->prefix('user')->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('index');
    Route::get('/get-wilayah', [UserController::class, 'getWilayah'])->name('get-wilayah');
    Route::post('/store', [UserController::class, 'store'])->name('store');
    Route::post('/update', [UserController::class, 'update'])->name('update');
    Route::post('/delete', [UserController::class, 'delete'])->name('delete');
  });

  Route::name('bunda-')->prefix('bunda')->group(function () {
    Route::get('/', [BundaController::class, 'index'])->name('index');
    Route::post('/get-list/{jenis}', [BundaController::class, 'getList'])->name('get-list');
  });

  Route::name('all-bunda-')->prefix('all_bunda')->group(function () {
    Route::get('/', [AllBundaController::class, 'index'])->name('index');
    Route::get('/get-list', [AllBundaController::class, 'getList'])->name('get-list');
    Route::post('/jabatan', [AllBundaController::class, 'jabatan'])->name('jabatan');
    Route::post('/modal', [AllBundaController::class, 'modal'])->name('modal');
    Route::post('/riwayat', [AllBundaController::class, 'riwayat'])->name('riwayat');
    Route::post('/simpan', [AllBundaController::class, 'simpan'])->name('simpan');
  });

  Route::name('survey-')->prefix('survey')->group(function () {
    Route::get('/', [SurveyController::class, 'index'])->name('index');
    Route::get('/data', [SurveyController::class, 'data'])->name('data');
  });

  Route::name('data_paud-')->prefix('data_paud')->group(function () {
    Route::name('wisata-')->prefix('wisata')->group(function () {
      Route::get('/', [WisataController::class, 'index'])->name('index');
      Route::get('/data', [WisataController::class, 'data'])->name('data');
      Route::post('/form', [WisataController::class, 'form'])->name('form');
      Route::post('/simpan', [WisataController::class, 'simpan'])->name('simpan');
    });
    Route::name('pelatihan-')->prefix('pelatihan')->group(function () {
      Route::get('/', [PelatihanController::class, 'index'])->name('index');
      Route::get('/data', [PelatihanController::class, 'data'])->name('data');
      Route::post('/form', [PelatihanController::class, 'form'])->name('form');
      Route::post('/simpan', [PelatihanController::class, 'simpan'])->name('simpan');
    });
  });

  Route::name('lomba-')->prefix('lomba')->group(function () {
    Route::get('/', [LombaController::class, 'index'])->name('index');
    Route::get('/get_data', [LombaController::class, 'get_data'])->name('get_data');
    Route::post('modal_identitas', [PenilaianController::class, 'modal_identitas'])->name('modal_identitas');
    Route::post('modal_nilai', [LombaController::class, 'modal_nilai'])->name('modal_nilai');
    Route::post('modal_nilai2', [LombaController::class, 'modal_nilai2'])->name('modal_nilai2');
    Route::post('reset', [LombaController::class, 'reset'])->name('reset');

    Route::post('detail', [LombaController::class, 'detail'])->name('detail');
  });

  Route::name('mitra-')->group(function () {
    Route::match(['get', 'post'], 'mitra/{halaman}', [MitraController::class, 'halaman'])->name('halaman');
  });

  Route::name('sewa-')->group(function () {
    Route::match(['get', 'post'], 'sewa/{halaman}', [SewaController::class, 'halaman'])->name('halaman');
  });
});
