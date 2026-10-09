<?php 
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Penilai\DashboardController;
use App\Http\Controllers\Penilai\Penilaian\Tahap1Controller;
use App\Http\Controllers\Penilai\Penilaian\Tahap2Controller;
use App\Http\Controllers\Penilai\PenilaianController;
use App\Http\Controllers\Bunda\LombaController;
use App\Http\Controllers\Dinas\LombaController as DinasLomba;

Route::name('penilai-')->prefix('penilai')->group(function(){
	Route::name('dashboard-')->prefix('dasboard')->group(function(){
		Route::get('/', [DashboardController::class, 'index'])->name('index');
	});

	Route::name('penilaian-')->prefix('penilaian')->group(function(){
		Route::name('tahap1-')->prefix('tahap1/{tahun}')->group(function(){
			Route::get('/',[Tahap1Controller::class, 'index'])->name('index');
			Route::get('/get_data',[Tahap1Controller::class, 'get_data'])->name('get_data');
			Route::post('penilaian_utama',[Tahap1Controller::class, 'penilaian_utama'])->name('penilaian_utama');
			Route::post('penilaian_pendukung',[Tahap1Controller::class, 'penilaian_pendukung'])->name('penilaian_pendukung');
        	Route::post('/modal_kegiatan', [LombaController::class, 'modal_kegiatan'])->name('modal_kegiatan');
		});

		Route::name('tahap2-')->prefix('tahap2/{tahun}')->group(function(){
			Route::get('/',[Tahap2Controller::class, 'index'])->name('index');
			Route::get('/get_data',[Tahap2Controller::class, 'get_data'])->name('get_data');
			Route::post('/modal_penilaian',[Tahap2Controller::class, 'modal_penilaian'])->name('modal_penilaian');
			Route::post('/simpan_nilai',[Tahap2Controller::class, 'simpan_nilai'])->name('simpan_nilai');

			Route::post('modal_nilai',[DinasLomba::class, 'modal_nilai'])->name('modal_nilai_tahap1');
			Route::post('detail',[DinasLomba::class, 'detail'])->name('detail');
		});

		Route::post('modal_identitas',[PenilaianController::class, 'modal_identitas'])->name('modal_identitas');
		Route::post('simpan_nilai',[PenilaianController::class, 'simpan_nilai'])->name('simpan_nilai');
	});
});
?>