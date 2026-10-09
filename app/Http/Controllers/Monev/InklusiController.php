<?php

namespace App\Http\Controllers\Monev;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth, Redirect, Session, DB, DataTables;

class InklusiController extends Controller
{
  function halaman(Request $request)
  {
    $halaman = $request->halaman;

    switch ($halaman) {
      case 'main':
        $tahun = DB::connection('pgsql_paud')->table('history.log_perkembangan_inklusi')
          ->selectRaw("EXTRACT(YEAR FROM tanggal) as tahun")
          ->groupByRaw("tahun")->get();
        $tahun = collect($tahun)->pluck('tahun')->toArray();
        array_push($tahun, date('Y'));
        $tahun = collect($tahun)->unique()->values()->toArray();
        arsort($tahun);
        $kecamatan = MasterController::data_kecamatan($request);

        $data = [
          'tahun' => $tahun,
          'kecamatan' => $kecamatan['kecamatan'],
          'data_kecamatan' => $kecamatan['semua'],
        ];
        return view('monev_baru.inklusi.main', $data);
        break;

      case 'get_data':
        $tahun_monev = $request->tahun_monev;
        $bulan_monev = $request->bulan_monev;
        $status_monev = $request->status_monev;

        $monev = DB::connection('pgsql_paud')
          ->table('history.log_perkembangan_inklusi as log')
          ->join('public.siswa as s', 's.siswa_id', 'log.siswa_id')
          ->selectRaw("
            s.npsn,
            COUNT(DISTINCT log.siswa_id) as jumlah_log
          ")
          ->whereRaw("EXTRACT(YEAR FROM log.tanggal)='$tahun_monev' AND EXTRACT(MONTH FROM log.tanggal)='$bulan_monev'")
          ->groupBy('s.npsn')->get();

        $npsn = collect($monev)->pluck('npsn')->unique()->toArray();

        $sekolah = MasterController::data_sekolah($request);
        $sekolah = $sekolah->when($status_monev != 'all', function ($w) use ($status_monev, $npsn) {
          if ($status_monev == 'Belum') {
            return $w->whereNotIn('s.npsn', $npsn);
          } else {
            return $w->whereIn('s.npsn', $npsn);
          }
        });

        return DataTables::queryBuilder($sekolah)
          ->addIndexColumn()
          ->addColumn('status_monev', function ($i) use ($monev, $tahun_monev, $bulan_monev) {
            $cari = collect($monev)->where('npsn', $i->npsn)->first();
            if (!empty($cari)) {
              return '<a href="javascript:void(0)" class="badge badge-success" onclick="modal_siswa(\'' . $i->npsn . '\',\'' . $tahun_monev . '\',\'' . $bulan_monev . '\')">Ada (' . $cari->jumlah_log . ')</a>';
            }
          })
          ->orderColumn('status_monev', function ($w, $order) use ($npsn) {
            $list = implode("','", $npsn);

            $w->orderByRaw("
              CASE
                  WHEN s.npsn IN ('$list') THEN 1
                  ELSE 0
              END $order
            ");
          })
          ->rawColumns(['status_monev'])
          ->make(true);
        break;

      case 'form_monev':
        $npsn = $request->npsn;
        $tahun = $request->tahun;
        $bulan = $request->bulan;

        $sekolah = DB::connection('pgsql_paud')->table('public.sekolah as s')
          ->selectRaw("npsn,nama")
          ->where('npsn', $npsn)->first();

        $log = DB::connection('pgsql_paud')
          ->table('history.log_perkembangan_inklusi as a')
          ->leftJoin('public.siswa as s', function ($j) {
            $j->on('s.siswa_id', '=', 'a.siswa_id');
          })
          ->join('public.sekolah as se', 'se.npsn', 's.npsn')
          ->join('public.indikator_perkembangan_inklusi as i', 'i.id', 'a.id_indikator')
          ->join('public.kategori_inklusi as k', 'k.id', 'i.kategori_inklusi_id')
          ->selectRaw("i.*,a.*,k.text_tampil,s.nama,se.alamat,se.npsn,se.nama as nama_lembaga,EXTRACT(YEAR FROM a.tanggal) as tahun,EXTRACT(MONTH FROM a.tanggal) as bulan,s.status_siswa,CASE WHEN s.alumni is true THEN 'Lulus' ELSE '' END as status_lulus")
          ->where('s.npsn', $npsn)
          ->whereRaw("EXTRACT(YEAR FROM a.tanggal)='$tahun' AND EXTRACT(MONTH FROM a.tanggal)='$bulan'")
          ->orderByRaw("s.nama,a.tanggal DESC")
          ->get();

        $data = [
          'log' => $log,
          'sekolah' => $sekolah,
        ];

        $content =  view('monev_baru.inklusi.modal_form', $data)->render();
        return ['content' => $content];
        break;

      default:
        return '404 - Halaman tidak ditemukan';
        break;
    }
  }
}
