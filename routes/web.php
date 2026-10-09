<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CommonController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\KegiatanController;
use App\Http\Controllers\SurveyController;
use App\Http\Controllers\Bunda\TalentaController;
use App\Http\Controllers\Bunda\PelatihanController;
use App\Http\Controllers\Bunda\EduController;
use App\Http\Controllers\Common\PengajuanApresiasiController;
use App\Http\Controllers\Common\MonitoringIjopController;
use App\Http\Controllers\Bunda\HitungMplsController;
use App\Http\Controllers\DP3A\LandingController as LandingDP3A;
use App\Http\Controllers\Bunda\DashboardASWController;
use App\Http\Controllers\HomeController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
// Route::get('pass',function(){
//     return Hash::make('123456');
// });

// Route::get('/test', function () {
//     Session::put('tes','tes');
//     return Redirect::to('/landing/home');
// });

Route::get('/', [LandingController::class, 'home']);
Route::middleware(['XSS'])->group(function () {
  Route::match(['get', 'post'], '/landing/{halaman}', [LandingController::class, 'halaman'])->name('landing');
  // Route::get('/landings/{halaman?}', function(){
  //     return 1;
  // })->name('landings');
});

// Route::group(['prefix'=>'new_land'],function(){
//     Route::get('/',function(){
//         return view('landing.new_layout');
//     });
// });

Route::get('/login', [LandingController::class, 'login'])->name('login')->middleware('notSignIn');

Route::get('reload-captcha', [LoginController::class, 'reloadCaptcha']);

Route::post('/login', [LoginController::class, 'login'])->name('try-login');
Route::get('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('signIn');
Route::match(['get', 'post'], '/login-as', [LoginController::class, 'userLoginAs'])->name('login-as')->middleware('signIn');
Route::get('storage/{path}/{type}', [CommonController::class, 'storage'])->name('storage');

Route::match(['get', 'post'], 'kegiatan/aksi/{halaman}', [KegiatanController::class, 'absensi'])->name('kegiatan-absensi');
Route::match(['get', 'post'], 'pelatihan/absensi/{halaman}', [PelatihanController::class, 'halaman_absensi'])->name('bunda-pelatihan-halaman_absensi');
Route::match(['get', 'post'], 'edu/absensi/{halaman}', [EduController::class, 'halaman_absensi'])->name('bunda-edu-halaman_absensi');

Route::name('common-')->prefix('common')->middleware('signIn')->group(function () {
  Route::get('/manajemen-data', [CommonController::class, 'indexManajemenData'])->name('manajemen-data');
  Route::get('/get-list', [CommonController::class, 'getList'])->name('get-list');

  Route::name('profil-')->prefix('profil')->group(function () {
    Route::get('/', [CommonController::class, 'indexProfil'])->name('index');
    Route::post('/update-profil', [CommonController::class, 'updateProfil'])->name('update-profil');
    Route::post('/update-akun', [CommonController::class, 'updateAkun'])->name('update-akun');
    Route::post('/update-foto', [CommonController::class, 'updateFoto'])->name('update-foto');
  });

  Route::name('berkas-')->prefix('berkas')->group(function () {
    Route::get('/', [CommonController::class, 'indexBerkas'])->name('index');
    Route::post('/store', [CommonController::class, 'storeBerkas'])->name('store');
  });

  Route::name('pokja-')->prefix('pokja')->group(function () {
    Route::get('/', [CommonController::class, 'indexPokja'])->name('index');
    Route::get('/kelola/{aksi}', [CommonController::class, 'kelolaPokja'])->name('kelola');
    Route::post('/store', [CommonController::class, 'storePokja'])->name('store');
    Route::post('/update', [CommonController::class, 'updatePokja'])->name('update');
    Route::post('/delete', [CommonController::class, 'deletePokja'])->name('delete');
  });

  Route::name('program-')->prefix('program')->group(function () {
    Route::get('/', [ProgramController::class, 'index'])->name('index');
    Route::post('/data', [ProgramController::class, 'data'])->name('data');
    Route::post('/form', [ProgramController::class, 'form'])->name('form');
    Route::post('/simpan', [ProgramController::class, 'simpan'])->name('simpan');
    Route::post('/hapus', [ProgramController::class, 'hapus'])->name('hapus');
  });

  Route::name('kegiatan-')->prefix('kegiatan')->group(function () {
    Route::get('/', [KegiatanController::class, 'index'])->name('index');
    Route::post('/data', [KegiatanController::class, 'data'])->name('data');
    Route::post('/form', [KegiatanController::class, 'form'])->name('form');
    Route::post('/form_laporan', [KegiatanController::class, 'form_laporan'])->name('form_laporan');
    Route::post('/simpan', [KegiatanController::class, 'simpan'])->name('simpan');
    Route::post('/simpan_laporan', [KegiatanController::class, 'simpan_laporan'])->name('simpan_laporan');
    Route::post('/hapus', [KegiatanController::class, 'hapus'])->name('hapus');
  });

  Route::name('apresiasi-')->prefix('apresiasi')->group(function () {
    Route::get('/pengajuan', [PengajuanApresiasiController::class, 'index'])->name('index');
    Route::get('/kelola/{aksi}', [PengajuanApresiasiController::class, 'kelola'])->name('kelola');
    Route::get('/kelola-kegiatan/{id}', [PengajuanApresiasiController::class, 'kelolaKegiatan'])->name('kelola-kegiatan');
    Route::post('/store', [PengajuanApresiasiController::class, 'store'])->name('store');
    Route::post('/update', [PengajuanApresiasiController::class, 'update'])->name('update');
    Route::post('/delete', [PengajuanApresiasiController::class, 'delete'])->name('delete');
  });

  Route::name('monitoring-')->prefix('monitoring')->group(function () {
    Route::get('/', [MonitoringIjopController::class, 'index'])->name('index');
    Route::post('/store', [MonitoringIjopController::class, 'store'])->name('store');
    Route::post('/update', [MonitoringIjopController::class, 'update'])->name('update');
    Route::post('/delete', [MonitoringIjopController::class, 'delete'])->name('delete');
  });

  Route::name('rating-')->prefix('rating_aplikasi')->group(function () {
    Route::post('/cek_survey', [SurveyController::class, 'cek_survey'])->name('cek_survey');
    Route::post('/simpan', [SurveyController::class, 'simpan'])->name('simpan');
  });

  Route::name('materi-')->prefix('materi')->group(function () {
    Route::get('/', [CommonController::class, 'materi'])->name('index');
  });
});

Route::get('clear', [LandingController::class, 'clear']);

Route::get('format', [TalentaController::class, 'format']);
Route::get('/cetak_monitoring_mpls', [HitungMplsController::class, 'cetak']);
Route::post('/form_cetak_monitoring_mpls', [HitungMplsController::class, 'form_cetak_monitoring_mpls']);
Route::match(['get', 'post'], 'dp3a/{aksi}', [LandingDP3A::class, 'iframe'])->name('dp3a');


Route::get('dashboard_asw/detail_informasi/{nik}', [DashboardASWController::class, 'detail_informasi'])->name('bunda-dashboard_asw-detail_informasi');
Route::get('dashboard_asw/halaman_sinkron/{nik}', [DashboardASWController::class, 'halaman_sinkron'])->name('bunda-dashboard_asw-halaman_sinkron');
Route::post('dashboard_asw/proses_sinkron', [DashboardASWController::class, 'proses_sinkron'])->name('bunda-dashboard_asw-proses_sinkron');
