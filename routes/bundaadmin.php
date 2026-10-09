<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Bunda\UserController;
use App\Http\Controllers\Bunda\PaudController;
use App\Http\Controllers\Bunda\BkbController;
use App\Http\Controllers\Bunda\DashboardASWController;
use App\Http\Controllers\Bunda\DashboardIframeController;
use App\Http\Controllers\Bunda\EduController;
use App\Http\Controllers\Bunda\TalentaController;
use App\Http\Controllers\Bunda\TalentaTempatController;
use App\Http\Controllers\Bunda\JadwalTalentaController;
use App\Http\Controllers\Bunda\KonsultasiController;
use App\Http\Controllers\Bunda\PelatihanController;
use App\Http\Controllers\Bunda\FasilitasiController;
use App\Http\Controllers\Bunda\MonitoringController;
use App\Http\Controllers\Bunda\MonitoringMplsController;
use App\Http\Controllers\Bunda\RekapBusController;
use App\Http\Controllers\Bunda\VaksinasiController;
use App\Http\Controllers\Bunda\RekapSatuanController;
use App\Http\Controllers\Bunda\RekapBesarController;
use App\Http\Controllers\Bunda\SurveyTransisiController;
use App\Http\Controllers\Bunda\DiagramAnakController;
use App\Http\Controllers\Bunda\DiagramPendudukController;
use App\Http\Controllers\Bunda\DiagramGuruController;
use App\Http\Controllers\Bunda\DiagramLembagaController;
use App\Http\Controllers\Bunda\BelumSekolahController;
use App\Http\Controllers\Bunda\DashboardController as DashboardBunda;
use App\Http\Controllers\Opd\KKAController;
use App\Http\Controllers\Bunda\BeasiswaPaudController;
use App\Http\Controllers\Bunda\IframeController;
use App\Http\Controllers\Bunda\KerjasamaMitraController;
use App\Http\Controllers\Dinas\RekapKerjasamaMitraController;

use App\Http\Controllers\Paudmutu\DashboardController;
use App\Http\Controllers\Paudmutu\PendaftaranController;
use App\Http\Controllers\Paudmutu\PaudmutuController;
use App\Http\Controllers\Paudmutu\Tahap2Controller;

use App\Http\Controllers\Dinas\RekapSekolahController;
use App\Http\Controllers\Dinas\RekapSPController;
use App\Http\Controllers\Dinas\RekapPegawaiController;
use App\Http\Controllers\Dinas\RekapSiswaController;
use App\Http\Controllers\Dinas\RekapSidiaController;
use App\Http\Controllers\Dinas\RekapPuskesmasController;
use App\Http\Controllers\Dinas\MBGController;
use App\Http\Controllers\Dinas\TTDController;
use App\Http\Controllers\Dinas\DataAPSMKController;

Route::name('bunda-')->prefix('bunda')->group(function () {
  Route::prefix('umum')->group(function () {
    Route::post('get_kelurahan', [PaudController::class, 'get_kelurahan']);
  });

  Route::name('beranda-')->prefix('beranda')->group(function () {
    Route::get('/', [DashboardBunda::class, 'index'])->name('index');
    Route::post('/get_widget', [DashboardBunda::class, 'get_widget'])->name('get_widget');
    Route::post('/get_survey', [DashboardBunda::class, 'get_survey'])->name('get_survey');
  });

  Route::prefix('edu')->name('edu-')->group(function () {
    Route::get('/', [EduController::class, 'index'])->name('index');
    Route::get('/get_data', [EduController::class, 'get_data'])->name('get_data');
    Route::get('/kehadiran/{id}', [EduController::class, 'presensi'])->name('kehadiran');
    Route::post('/modal_konfirmasi', [EduController::class, 'modal_konfirmasi'])->name('modal_konfirmasi');
    Route::post('/get_jadwal', [EduController::class, 'get_jadwal'])->name('get_jadwal');
    Route::post('/konfirmasi', [EduController::class, 'konfirmasi'])->name('konfirmasi');
    Route::get('/data_peserta/{id_edu}', [EduController::class, 'data_peserta'])->name('data_peserta');
    Route::get('/export_peserta', [EduController::class, 'export_peserta'])->name('export_peserta');
    Route::post('/get_grafik', [EduController::class, 'get_grafik'])->name('get_grafik');
  });

  Route::prefix('konsultasi')->name('konsultasi-')->group(function () {
    Route::get('/', [KonsultasiController::class, 'index'])->name('index');
    Route::get('/get_data', [KonsultasiController::class, 'get_data'])->name('get_data');
    Route::get('/kehadiran/{id}', [KonsultasiController::class, 'presensi'])->name('kehadiran');
    Route::post('/modal_konfirmasi', [KonsultasiController::class, 'modal_konfirmasi'])->name('modal_konfirmasi');
    Route::post('/get_jadwal', [KonsultasiController::class, 'get_jadwal'])->name('get_jadwal');
    Route::post('/konfirmasi', [KonsultasiController::class, 'konfirmasi'])->name('konfirmasi');
  });

  Route::prefix('pelatihan')->name('pelatihan-')->group(function () {
    Route::get('/', [PelatihanController::class, 'index'])->name('index');
    Route::get('/get_data', [PelatihanController::class, 'get_data'])->name('get_data');
    Route::get('/kehadiran/{id}', [PelatihanController::class, 'presensi'])->name('kehadiran');
    Route::post('/modal_konfirmasi', [PelatihanController::class, 'modal_konfirmasi'])->name('modal_konfirmasi');
    Route::post('/get_jadwal', [PelatihanController::class, 'get_jadwal'])->name('get_jadwal');
    Route::post('/konfirmasi', [PelatihanController::class, 'konfirmasi'])->name('konfirmasi');
  });

  Route::prefix('fasilitasi')->name('fasilitasi-')->group(function () {
    Route::get('/', [FasilitasiController::class, 'index'])->name('index');
    Route::get('/get_data', [FasilitasiController::class, 'get_data'])->name('get_data');
    Route::get('/kehadiran/{id}', [FasilitasiController::class, 'presensi'])->name('kehadiran');
    Route::post('/modal_konfirmasi', [FasilitasiController::class, 'modal_konfirmasi'])->name('modal_konfirmasi');
    Route::post('/get_jadwal', [FasilitasiController::class, 'get_jadwal'])->name('get_jadwal');
    Route::post('/konfirmasi', [FasilitasiController::class, 'konfirmasi'])->name('konfirmasi');
  });

  Route::prefix('rekap_bus')->name('rekap_bus-')->group(function () {
    Route::get('/', [RekapBusController::class, 'index'])->name('index');
    Route::get('/get_data', [RekapBusController::class, 'get_data'])->name('get_data');
    Route::get('/kehadiran/{id}', [RekapBusController::class, 'presensi'])->name('kehadiran');
    Route::post('/modal_konfirmasi', [RekapBusController::class, 'modal_konfirmasi'])->name('modal_konfirmasi');
    Route::post('/get_jadwal', [RekapBusController::class, 'get_jadwal'])->name('get_jadwal');
    Route::post('/konfirmasi', [RekapBusController::class, 'konfirmasi'])->name('konfirmasi');
  });

  Route::name('vaksinasi-')->prefix('vaksinasi')->group(function () {
    Route::get('/', [VaksinasiController::class, 'index'])->name('index');
    Route::get('/get_data', [VaksinasiController::class, 'get_data'])->name('get_data');
    Route::post('/modal', [VaksinasiController::class, 'modal'])->name('modal');
    Route::get('/get_data_modal', [VaksinasiController::class, 'get_data_modal'])->name('get_data_modal');
  });

  Route::prefix('rekap_profil')->name('rekap_profil-')->group(function () {
    Route::get('/', [RekapSatuanController::class, 'index'])->name('index');
    Route::post('/get_kelurahan', [RekapSatuanController::class, 'get_kelurahan'])->name('get_kelurahan');
    Route::post('/get_data', [RekapSatuanController::class, 'get_data'])->name('get_data');
    Route::post('/detail_data', [RekapSatuanController::class, 'detail_data'])->name('detail_data');
    Route::post('/foto-store', [RekapSatuanController::class, 'fotoStore'])->name('foto_store');
  });

  Route::prefix('rekap_besar')->name('rekap_besar-')->group(function () {
    Route::get('/', [RekapBesarController::class, 'index'])->name('index');
    Route::post('/get_data', [RekapBesarController::class, 'get_data'])->name('get_data');
  });

  Route::prefix('monitoring')->name('monitoring-')->group(function () {
    Route::get('/', [MonitoringController::class, 'index'])->name('index');
    Route::post('/get_data', [MonitoringController::class, 'get_data'])->name('get_data');
  });

  Route::prefix('talenta')->name('talenta-')->group(function () {
    Route::get('/', [TalentaController::class, 'index'])->name('index');
    Route::post('/get_data', [TalentaController::class, 'get_data'])->name('get_data');
    Route::post('/load_page', [TalentaController::class, 'load_page'])->name('load_page');
    Route::get('/get_data_dokumen', [TalentaController::class, 'get_data_dokumen'])->name('get_data_dokumen');
    Route::post('/modal_konfirmasi', [TalentaController::class, 'modal_konfirmasi'])->name('modal_konfirmasi');
    Route::post('/get_jadwal', [TalentaController::class, 'get_jadwal'])->name('get_jadwal');
    Route::post('/konfirmasi', [TalentaController::class, 'konfirmasi'])->name('konfirmasi');
    Route::get('/cetak', [TalentaController::class, 'cetak'])->name('cetak');
    Route::post('/upload_berkas', [TalentaController::class, 'upload_berkas'])->name('upload_berkas');
    Route::get('/data_peserta/{id_jadwal}', [TalentaController::class, 'data_peserta'])->name('data_peserta');
  });

  Route::prefix('jadwal_talenta')->name('jadwal_talenta-')->group(function () {
    Route::get('/', [JadwalTalentaController::class, 'index'])->name('index');
    Route::get('/get_data', [JadwalTalentaController::class, 'get_data'])->name('get_data');
    Route::post('/modal_form', [JadwalTalentaController::class, 'modal_form'])->name('modal_form');
    Route::post('/konfirmasi', [JadwalTalentaController::class, 'konfirmasi'])->name('konfirmasi');
    Route::post('/hapus', [JadwalTalentaController::class, 'hapus'])->name('hapus');
  });
  Route::name('survey_transisi-')->prefix('survey_transisi')->group(function () {
    Route::get('/', [SurveyTransisiController::class, 'index'])->name('index');
    Route::post('store', [SurveyTransisiController::class, 'store'])->name('store');
    Route::get('monitor', [SurveyTransisiController::class, 'monitor'])->name('monitor');
    Route::post('monitor_detail', [SurveyTransisiController::class, 'monitorDetail'])->name('monitor_detail');
  });

  Route::name('monitoring_mpls-')->prefix('monitoring_mpls')->group(function () {
    Route::get('monitor', [MonitoringMplsController::class, 'monitor'])->name('monitor');
    Route::post('monitor_detail', [MonitoringMplsController::class, 'monitorDetail'])->name('monitor_detail');
  });

  Route::name('diagram_anak-')->prefix('diagram_anak')->group(function () {
    Route::get('main', [DiagramAnakController::class, 'main'])->name('main');
    Route::post('get_kelurahan', [DiagramAnakController::class, 'get_kelurahan'])->name('get_kelurahan');
    Route::post('get_data', [DiagramAnakController::class, 'get_data'])->name('get_data');
    Route::post('get_jumlah', [DiagramAnakController::class, 'get_jumlah'])->name('get_jumlah');
    Route::post('form_detail', [DiagramAnakController::class, 'form_detail'])->name('form_detail');
    Route::post('get_detail', [DiagramAnakController::class, 'get_detail'])->name('get_detail');
  });

  Route::name('diagram_guru-')->prefix('diagram_guru')->group(function () {
    Route::get('main', [DiagramGuruController::class, 'main'])->name('main');
    Route::post('get_kelurahan', [DiagramGuruController::class, 'get_kelurahan'])->name('get_kelurahan');
    Route::post('get_data', [DiagramGuruController::class, 'get_data'])->name('get_data');
    Route::post('get_jumlah', [DiagramGuruController::class, 'get_jumlah'])->name('get_jumlah');
    Route::post('form_detail', [DiagramGuruController::class, 'form_detail'])->name('form_detail');
    Route::post('get_detail', [DiagramGuruController::class, 'get_detail'])->name('get_detail');
  });

  Route::name('diagram_lembaga-')->prefix('diagram_lembaga')->group(function () {
    Route::get('main', [DiagramLembagaController::class, 'main'])->name('main');
    Route::post('get_kelurahan', [DiagramLembagaController::class, 'get_kelurahan'])->name('get_kelurahan');
    Route::post('get_data', [DiagramLembagaController::class, 'get_data'])->name('get_data');
    Route::post('get_jumlah', [DiagramLembagaController::class, 'get_jumlah'])->name('get_jumlah');
    Route::post('form_detail', [DiagramLembagaController::class, 'form_detail'])->name('form_detail');
    Route::post('get_detail', [DiagramLembagaController::class, 'get_detail'])->name('get_detail');
  });

  Route::name('diagram_penduduk-')->prefix('diagram_penduduk')->group(function () {
    Route::get('main', [DiagramPendudukController::class, 'main'])->name('main');
    Route::post('get_kelurahan', [DiagramPendudukController::class, 'get_kelurahan'])->name('get_kelurahan');
    Route::post('get_data', [DiagramPendudukController::class, 'get_data'])->name('get_data');
    Route::post('get_jumlah', [DiagramPendudukController::class, 'get_jumlah'])->name('get_jumlah');
    Route::post('form_detail', [DiagramPendudukController::class, 'form_detail'])->name('form_detail');
    Route::post('get_detail', [DiagramPendudukController::class, 'get_detail'])->name('get_detail');
  });

  Route::name('dashboard_asw-')->prefix('dashboard_asw')->group(function () {
    Route::get('main', [DashboardASWController::class, 'main'])->name('main');
    Route::post('get_kelurahan', [DashboardASWController::class, 'get_kelurahan'])->name('get_kelurahan');
    Route::post('get_data', [DashboardASWController::class, 'get_data'])->name('get_data');
    Route::post('modal', [DashboardASWController::class, 'modal'])->name('modal');
    Route::post('detail_data', [DashboardASWController::class, 'detail_data'])->name('detail_data');
    Route::post('get_grafik', [DashboardASWController::class, 'get_grafik'])->name('get_grafik');
  });

  Route::name('monitoring-blm_sekolah-')->prefix('monitoring_blm_sekolah')->group(function () {
    Route::get('/main', [BelumSekolahController::class, 'main'])->name('main');
    Route::post('/get_data', [BelumSekolahController::class, 'get_data'])->name('data');
    Route::post('/ganti', [BelumSekolahController::class, 'ganti'])->name('ganti');
    Route::post('/simpan', [BelumSekolahController::class, 'simpan'])->name('simpan');
    Route::post('/tambah', [BelumSekolahController::class, 'tambah'])->name('tambah');
    Route::post('/simpan_tambah', [BelumSekolahController::class, 'simpan_tambah'])->name('simpan_tambah');
    Route::post('/cari_nik', [BelumSekolahController::class, 'cari_nik'])->name('cari_nik');
    Route::match(['get', 'post'], 'survey/{halaman}', 'Bunda\BelumSekolah\SurveyController@halaman')->name('survey');
  });

  Route::name('dashboard_iframe-main')->get('dashboard_iframe/main', [DashboardIframeController::class, 'main']);
});

Route::name('opd-')->prefix('opd')->group(function () {
  Route::match(['get', 'post'], 'kka/{halaman}', [KKAController::class, 'halaman'])->name('kka');
});

Route::name('beasiswa_paud-')->group(function () {
  Route::match(['get', 'post'], 'beasiswa_paud/{halaman}', [BeasiswaPaudController::class, 'halaman'])->name('halaman');
});

Route::name('telenta_tempat-')->group(function () {
  Route::match(['get', 'post'], 'tempat_talenta/{halaman}', [TalentaTempatController::class, 'halaman'])->name('halaman');
});

Route::name('paud_mutu-')->prefix('paud_mutu')->group(function () {
  Route::match(['GET', 'POST'], 'dashboard/{halaman}', [DashboardController::class, 'halaman'])->name('dashboard');
  Route::match(['GET', 'POST'], 'pendaftaran/{halaman}', [PendaftaranController::class, 'halaman'])->name('pendaftaran');
  Route::match(['GET', 'POST'], 'paudmutu/{halaman}', [PaudmutuController::class, 'halaman'])->name('paudmutu');
  Route::match(['GET', 'POST'], 'tahap2/{halaman}', [Tahap2Controller::class, 'halaman'])->name('tahap2');
});

Route::match(['post', 'get'], '/dinas/rekap_sekolah/{halaman}', [RekapSekolahController::class, 'halaman'])->name('dinas-rekap_sekolah');
Route::match(['post', 'get'], '/dinas/rekap_sidia/{halaman}', [RekapSidiaController::class, 'halaman'])->name('dinas-rekap_sidia');
Route::match(['post', 'get'], '/dinas/rekap_puskesmas/{halaman}', [RekapPuskesmasController::class, 'halaman'])->name('dinas-rekap_puskesmas');

Route::match(['post', 'get'], '/bunda/iframe/{halaman}', [IframeController::class, 'halaman'])->name('bunda-iframe');

Route::match(['get', 'post'], '/bunda/kerjasama/mitra/{halaman}', [KerjasamaMitraController::class, 'halaman'])->name('bunda-kerjasama-mitra');
Route::match(['get', 'post'], '/bunda/rekap/kerjasama/mitra/{halaman}', [RekapKerjasamaMitraController::class, 'halaman'])->name('bunda-rekap-kerjasama-mitra');
Route::match(['get', 'post'], '/dinas/rekap/satuan_pendidikan/{halaman}', [RekapSPController::class, 'halaman'])->name('dinas-rekap-satuan_pendidikan');
Route::match(['get', 'post'], '/dinas/rekap/siswa/{halaman}', [RekapSiswaController::class, 'halaman'])->name('dinas-rekap-siswa');
Route::match(['get', 'post'], '/dinas/rekap/pegawai/{halaman}', [RekapPegawaiController::class, 'halaman'])->name('dinas-rekap-pegawai');

Route::match(['get', 'post'], '/dinas/rekap_mbg/{halaman}', [MBGController::class, 'halaman'])->name('dinas-rekap_mbg');
Route::match(['get', 'post'], '/dinas/rekap_ttd/{halaman}', [TTDController::class, 'halaman'])->name('dinas-rekap_ttd');

Route::match(['get', 'post'], '/dinas/data_aps_apm_apk/{halaman}', [DataAPSMKController::class, 'halaman'])->name('dinas-data_aps_apm_apk');
