<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiRekapController;
use App\Http\Controllers\Api\OpdController;
use App\Http\Controllers\Api\IframeController;
use App\Http\Controllers\Api\SinkronPendudukController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

// Route::get('pass',function(){
//     return Hash::make('opd123456');
// });

Route::prefix('rekap')->group(function () {
  Route::match(['post', 'get'], '/lomba/{jenis}', [ApiRekapController::class, 'lomba']);
  Route::match(['post', 'get'], '/nilai/{jenis}', [ApiRekapController::class, 'nilai']);
  Route::match(['post', 'get'], '/nilai2/{jenis}', [ApiRekapController::class, 'nilai2']);
  Route::match(['post', 'get'], '/nilai_akhir/{jenis}', [ApiRekapController::class, 'nilai_akhir']);
});

Route::middleware('m_api')->group(function () {
  Route::match(['post', 'get'], 'opd/{aksi}', [OpdController::class, 'master_api']);
});

Route::prefix('iframe')->group(function () {
  Route::get('penduduk', [IframeController::class, 'penduduk']);
  Route::get('detail_penduduk', [IframeController::class, 'detail_penduduk']);
  Route::post('get_detail_penduduk', [IframeController::class, 'get_detail_penduduk'])->name('api-iframe-get_detail_penduduk');
});

Route::match(['get', 'post'], '/cek_penduduk/{halaman}', [SinkronPendudukController::class, 'halaman']);
