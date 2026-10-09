<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Monev\BopController;
use App\Http\Controllers\Monev\JaspelController;
use App\Http\Controllers\Monev\InklusiController;
use App\Http\Controllers\Monev\GemilangController;
use App\Http\Controllers\Monev\MplsController;
use App\Http\Controllers\Monev\IjopController;
use App\Http\Controllers\Monev\KelembagaanPaudController;
use App\Http\Controllers\Monev\SothController;
use App\Http\Controllers\Monev\RplController;
use App\Http\Controllers\Monev\BospController;
use App\Http\Controllers\Monev\SKPengangkatanController;

Route::prefix('monev_baru')->name('monev_baru-')->middleware(['hakakses:dinas,bunda'])->group(function () {
  Route::match(['get', 'post'], 'bop/{halaman}', [BopController::class, 'halaman'])->name('bop');
  Route::match(['get', 'post'], 'jaspel/{halaman}', [JaspelController::class, 'halaman'])->name('jaspel');
  Route::match(['get', 'post'], 'kembang_inklusi/{halaman}', [InklusiController::class, 'halaman'])->name('kembang_inklusi');
  Route::match(['get', 'post'], 'gemilang/{halaman}', [GemilangController::class, 'halaman'])->name('gemilang');
  Route::match(['get', 'post'], 'mpls/{jenjang}/{halaman}', [MplsController::class, 'halaman'])->name('mpls');
  Route::match(['get', 'post'], 'ijop/{halaman}', [IjopController::class, 'halaman'])->name('ijop');
  Route::match(['get', 'post'], 'kelembagaan_paud/{halaman}', [KelembagaanPaudController::class, 'halaman'])->name('kelembagaan_paud');
  Route::match(['get', 'post'], 'soth/{halaman}', [SothController::class, 'halaman'])->name('soth');
  Route::match(['get', 'post'], 'rpl/{halaman}', [RplController::class, 'halaman'])->name('rpl');
  Route::match(['get', 'post'], 'bosp/{halaman}', [BospController::class, 'halaman'])->name('bosp');
  Route::match(['get', 'post'], 'sk_pengangkatan/{halaman}', [SKPengangkatanController::class, 'halaman'])->name('sk_pengangkatan');
});
