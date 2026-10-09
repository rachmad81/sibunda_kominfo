<?php

namespace App\Http\Controllers\Monev;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth, Redirect, Session, DB, DataTables;

class SothController extends Controller
{
  function halaman(Request $request)
  {
    $halaman = $request->halaman;

    switch ($halaman) {
      case 'main':
        $tahun = DB::table('cutoff_rekap.monev_bop')->pluck('tahun')->unique()->toArray();
        array_push($tahun, date('Y'));
        $tahun = collect($tahun)->unique()->values()->toArray();
        arsort($tahun);

        $kecamatan = MasterController::data_kecamatan($request);

        $data = [
          'tahun' => $tahun,
          'kecamatan' => $kecamatan['kecamatan'],
          'data_kecamatan' => $kecamatan['semua'],
        ];
        return view('monev_baru.soth.main', $data);
        break;

      case 'get_data':
        $tingkat = Auth::getUser()->tingkat;
        $kecamatan = $request->kecamatan ?? '';
        $kelurahan = $request->kelurahan ?? '';

        $anomali = $this->anomali_nama($request);

        $data = DB::connection('db_cutt_off')->table('public.penduduk_inject as a')
          ->join('public.api_data_soth as b', 'b.nik_balita', 'a.nik')
          ->selectRaw("
            a.nama_lengkap,a.alamat_dom,a.alamat,a.kelurahan_dom,a.kelurahan,a.kecamatan,a.kecamatan_dom,EXTRACT(YEAR FROM AGE(NOW(),a.tgl_lahir)) as usia,
            b.nama_ibu,b.alamat_soth,b.nama_soth
          ")
          ->whereRaw("(EXTRACT(YEAR FROM AGE(NOW(),a.tgl_lahir)) BETWEEN 0 AND 6)")
          ->when($kecamatan != 'all', function ($q) use ($anomali) {
            $q->whereIn('a.kecamatan_dom', $anomali['arr_kecamatan']);
          })
          ->when($kelurahan != 'all', function ($q) use ($anomali) {
            $q->whereIn('a.kelurahan_dom', $anomali['arr_kelurahan']);
          });

        return DataTables::queryBuilder($data)
          ->addIndexColumn()
          ->filterColumn('usia', function ($i, $keyword) {
            $i->where(DB::raw("EXTRACT(YEAR FROM AGE(NOW(),a.tgl_lahir))"), $keyword);
          })
          ->orderColumn('usia', function ($i, $order) {
            $i->orderBy(DB::raw("EXTRACT(YEAR FROM AGE(NOW(),a.tgl_lahir))"), $order);
          })
          ->make(true);
        break;

      default:
        return '404 - Halaman tidka ditemukan';
        break;
    }
  }

  function anomali_nama(Request $request)
  {
    // return $request->all();
    $kecamatan = $request->kecamatan ?? '';
    $kelurahan = $request->kelurahan ?? '';

    $arr_kecamatan = [];
    $arr_kelurahan = [];

    if ($kecamatan != '' && $kecamatan != 'all') {
      $data_kecamatan = DB::table('public.kecamatan')->where('kecamatan_dispenduk', $kecamatan)->first();
      $arr_kecamatan = [$data_kecamatan->kecamatan_dispenduk, $data_kecamatan->dispenduk_baru];
    }

    if ($kelurahan != '' && $kelurahan != 'all') {
      $data_kelurahan = DB::table('public.kelurahan')->where('kelurahan_dispenduk', $kelurahan)->first();
      $arr_kelurahan = [$data_kelurahan->kelurahan_dispenduk, $data_kelurahan->dispenduk_baru];
    }

    return ['arr_kecamatan' => $arr_kecamatan, 'arr_kelurahan' => $arr_kelurahan];
  }
}
