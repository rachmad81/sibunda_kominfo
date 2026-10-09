<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Bunda\UserController;
use App\Http\Controllers\Bunda\PaudController;
use App\Http\Controllers\Bunda\BkbController;
use App\Http\Controllers\Bunda\EduController;
use App\Http\Controllers\Bunda\KonsultasiController;
use App\Http\Controllers\Bunda\PelatihanController;
use App\Http\Controllers\Bunda\FasilitasiController;
use App\Http\Controllers\Bunda\SekolahController;
use App\Http\Controllers\Bunda\KonfirmasiPegawaiController;
use App\Http\Controllers\Bunda\VaksinasiController;
use App\Http\Controllers\Bunda\LombaController;
use App\Http\Controllers\Bunda\HitungMplsController;
use App\Http\Controllers\Bunda\G7KaihController;
use App\Http\Controllers\Bunda\ImunisasiController;
use App\Http\Controllers\Penilai\DashboardController;
use App\Http\Controllers\Bunda\DashboardController as DashboardBunda;

Route::name('bunda-')->prefix('bunda')->group(function () {

  Route::name('user-')->prefix('user')->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('index');
    Route::post('/store', [UserController::class, 'store'])->name('store');
    Route::post('/update', [UserController::class, 'update'])->name('update');
    Route::post('/delete', [UserController::class, 'delete'])->name('delete');
  });

  Route::prefix('paud_hi')->name('paud_hi-')->group(function () {
    Route::get('/', [PaudController::class, 'index'])->name('index');
    Route::get('/get_data', [PaudController::class, 'get_data'])->name('get_data');
    Route::post('/modal', [PaudController::class, 'modal'])->name('modal');
    Route::post('/simpan', [PaudController::class, 'simpan'])->name('simpan');
    Route::post('/riwayat', [PaudController::class, 'riwayat'])->name('riwayat');
    Route::post('/tolak', [PaudController::class, 'tolak'])->name('tolak');
    Route::post('/setujui', [PaudController::class, 'setujui'])->name('setujui');
    Route::post('/riwayat_catatan_pengajuan', [PaudController::class, 'riwayat_catatan_pengajuan'])->name('riwayat_catatan_pengajuan');
    Route::get('/cetak/{id}', [PaudController::class, 'cetak'])->name('cetak');
  });

  Route::prefix('bkb')->name('bkb-')->group(function () {
    Route::get('/', [BkbController::class, 'index'])->name('index');
    Route::get('/get_data', [BkbController::class, 'get_data'])->name('get_data');
    Route::post('/presensi', [BkbController::class, 'presensi'])->name('presensi');
  });

  Route::name('sekolah-')->prefix('sekolah')->group(function () {
    Route::get('/', [SekolahController::class, 'index'])->name('index');
    Route::get('/get_data', [SekolahController::class, 'get_data'])->name('get_data');
    Route::post('/modal', [SekolahController::class, 'modal'])->name('modal');
    Route::get('/get_data_modal', [SekolahController::class, 'get_data_modal'])->name('get_data_modal');
  });

  Route::name('vaksinasi-')->prefix('vaksinasi')->group(function () {
    Route::get('/', [VaksinasiController::class, 'index'])->name('index');
    Route::get('/get_data', [VaksinasiController::class, 'get_data'])->name('get_data');
    Route::post('/modal', [VaksinasiController::class, 'modal'])->name('modal');
    Route::get('/get_data_modal', [VaksinasiController::class, 'get_data_modal'])->name('get_data_modal');
  });

  Route::name('konfirmasi-pegawai-')->prefix('konfirmasi-pegawai')->group(function () {
    Route::get('/', [KonfirmasiPegawaiController::class, 'index'])->name('index');
    Route::get('/get_data', [KonfirmasiPegawaiController::class, 'get_data'])->name('get_data');
    Route::post('/modal', [KonfirmasiPegawaiController::class, 'modal'])->name('modal');
    Route::post('/konfirmasi', [KonfirmasiPegawaiController::class, 'konfirmasi'])->name('konfirmasi');
  });

  Route::name('lomba-')->prefix('lomba/{tahun}')->group(function () {
    Route::get('/', [LombaController::class, 'main'])->name('index');
    Route::get('/get_data', [LombaController::class, 'get_data'])->name('get_data');
    Route::post('/get_form', [LombaController::class, 'get_form'])->name('get_form');

    Route::post('/simpan_identitas', [LombaController::class, 'simpan_identitas'])->name('simpan_identitas');
    Route::post('/simpan_berkas', [LombaController::class, 'simpan_berkas'])->name('simpan_berkas');

    Route::post('/simpan_kegiatan', [LombaController::class, 'simpan_kegiatan'])->name('simpan_kegiatan');
    Route::post('/hapus_kegiatan', [LombaController::class, 'hapus_kegiatan'])->name('hapus_kegiatan');
    Route::post('/modal_kegiatan', [LombaController::class, 'modal_kegiatan'])->name('modal_kegiatan');

    Route::post('/simpan_mitra', [LombaController::class, 'simpan_mitra'])->name('simpan_mitra');
    Route::post('/hapus_mitra', [LombaController::class, 'hapus_mitra'])->name('hapus_mitra');
  });

  Route::name('monitoring-mpls-')->prefix('monitoring_mpls')->group(function () {
    Route::get('/main/{jenjang}', [HitungMplsController::class, 'main'])->name('main');
    Route::post('/cari_sekolah', [HitungMplsController::class, 'cari_sekolah'])->name('cari_sekolah');
    Route::post('/form_identitas', [HitungMplsController::class, 'form_identitas'])->name('form_identitas');
    Route::post('/simpan', [HitungMplsController::class, 'simpan'])->name('simpan');
  });

  Route::name('g7kaih-')->prefix('g7kaih')->group(function () {
    Route::get('/main', [G7KaihController::class, 'main'])->name('main');
    Route::post('/cari_sekolah', [G7KaihController::class, 'cari_sekolah'])->name('cari_sekolah');
    Route::post('/get_siswa', [G7KaihController::class, 'get_siswa'])->name('get_siswa');
    Route::get('/tampilkan', [G7KaihController::class, 'tampilkan'])->name('tampilkan');
  });

  Route::name('imunisasi-')->prefix('imunisasi')->group(function () {
    Route::get('/main', [ImunisasiController::class, 'main'])->name('main');
    Route::post('/cari_sekolah', [ImunisasiController::class, 'cari_sekolah'])->name('cari_sekolah');
    Route::post('/get_siswa', [ImunisasiController::class, 'get_siswa'])->name('get_siswa');
    Route::post('/tampilkan', [ImunisasiController::class, 'tampilkan'])->name('tampilkan');
  });
});
