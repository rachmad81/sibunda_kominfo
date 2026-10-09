<?php

namespace App\Http\Controllers\Monev;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth, Redirect, Session, DB, DataTables;

class RplController extends Controller
{
  function halaman(Request $request)
  {
    $halaman = $request->halaman;

    switch ($halaman) {
      case 'main':
        $periode = DB::table('cutoff_rekap.rpl_ptk')->orderBy('periode', 'DESC')->pluck('periode')->unique()->toArray();

        $kecamatan = MasterController::data_kecamatan($request);

        $data = [
          'periode' => $periode,
          'kecamatan' => $kecamatan['kecamatan'],
          'data_kecamatan' => $kecamatan['semua'],
        ];
        return view('monev_baru.rpl.main', $data);
        break;

      case 'get_data':
        $tingkat = Auth::getUser()->tingkat;
        $kecamatan = $request->kecamatan ?? '';
        $kelurahan = $request->kelurahan ?? '';
        $periode = $request->periode ?? '';

        $anomali = $this->anomali_nama($request);

        $data = DB::table('cutoff_rekap.rpl_ptk')
          ->where('periode', $periode);

        return DataTables::queryBuilder($data)
          ->addIndexColumn()
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
