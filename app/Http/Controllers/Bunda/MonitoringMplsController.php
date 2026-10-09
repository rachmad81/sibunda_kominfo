<?php

namespace App\Http\Controllers\Bunda;

use App\Http\Controllers\Controller;
use App\Models\MonitoringMPLS\MonitoringMPLS;
use App\Models\ProfilSekolah\Kecamatan;
use App\Models\ProfilSekolah\Kelurahan;
use App\Models\ProfilSekolah\Sekolah;
use App\Models\SurveyTransisi\SurveyTransisi;
use App\Models\SurveyTransisi\SurveyTransisiJawaban;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class MonitoringMplsController extends Controller
{
  public function monitor(Request $request)
  {
    $tahun = 'satdik' . date('Y');
    $lembagas = DB::table('monitoring_mpls as mpls')
      ->leftJoin('monitoring_mpls_data as mpls_data', function ($q) {
        $q->on('mpls_data.tipe', 'mpls.tipe');
      })
      ->join('monitoring_mpls_jawaban as mpls_jawaban', function ($q) {
        $q->on('mpls_jawaban.monitoring_mpls_id', 'mpls_data.id');
      })
      ->where('mpls.tipe', $tahun)
      ->when($request->user()->role == 'bunda', function ($q) use ($request) {
        if ($request->user()->tingkat == 'kecamatan') {
          $q->where('mpls_data.kec_id', $request->user()->Kecamatan->kecamatan_kode);
        } elseif ($request->user()->tingkat == 'kelurahan') {
          $q->where('mpls_data.kel_id', $request->user()->Kelurahan->kelurahan_kode);
        }
      })
      ->select(
        'mpls_data.jenjang',
        'mpls_data.npsn',
        'mpls_data.nama',
        'mpls_data.kecamatan',
        'mpls_data.kelurahan',
        // DB::raw('MAX(mpls_jawaban.updated_at) as updated_at'),
        // DB::raw("COUNT(DISTINCT(CASE WHEN mpls.jawaban is true THEN mpls.id ELSE NULL END)) as jawaban_count"),
        // DB::raw("COUNT(DISTINCT(CASE WHEN mpls.jawaban is true AND mpls_jawaban.jawaban IS NOT NULL THEN mpls.id ELSE NULL END)) as jawaban_done_count"),
        // DB::raw("COUNT(DISTINCT(CASE WHEN mpls.keterangan is true THEN mpls.id ELSE NULL END)) as keterangan_count"),
        // DB::raw("COUNT(DISTINCT(CASE WHEN mpls.keterangan is true AND mpls_jawaban.keterangan IS NOT NULL THEN mpls.id ELSE NULL END)) as keterangan_done_count"),
        // DB::raw("COUNT(DISTINCT(CASE WHEN mpls.bukti_dokumentasi is true THEN mpls.id ELSE NULL END)) as bukti_dokumentasi_count"),
        // DB::raw("COUNT(DISTINCT(CASE WHEN mpls.bukti_dokumentasi is true AND mpls_jawaban.bukti_dokumentasi IS NOT NULL THEN mpls.id ELSE NULL END)) as bukti_dokumentasi_done_count"),
      )
      ->groupBy('mpls_data.jenjang', 'mpls_data.npsn', 'mpls_data.nama', 'mpls_data.kecamatan', 'mpls_data.kelurahan')
      ->get();

    return view('bunda.monitor.monitoring-mpls.monitor', compact('lembagas'));
  }

  public function monitorDetail(Request $request)
  {
    $tahun = 'satdik' . date('Y');
    $pertanyaans = MonitoringMPLS::orderBy('urutan')
      ->where('jenjang', 'ilike', "%{$request->jenjang}%")
      ->where('tipe', $tahun)
      ->with(['jawabanMonitor' => function ($q) use ($request) {
        $q->where('npsn', $request->npsn);
      }])
      ->with(['data' => function ($q) use ($request) {
        $q->where('npsn', $request->npsn);
      }])
      ->when($request->user()->role == 'bunda', function ($q) use ($request) {
        if ($request->user()->tingkat == 'kecamatan') {
          $q->where('mpls_data.kec_id', $request->user()->Kecamatan->kecamatan_kode);
        } elseif ($request->user()->tingkat == 'kelurahan') {
          $q->where('mpls_data.kel_id', $request->user()->Kelurahan->kelurahan_kode);
        }
      })
      ->get();
    $content = view('bunda.monitor.monitoring-mpls.monitor_detail', compact('pertanyaans'))->render();
    return ['content' => $content];
  }
}
