<?php

namespace App\Http\Controllers\Dinas;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use DB, Auth, DataTables;

class RekapKerjasamaMitraController extends Controller
{
  function halaman(Request $request)
  {
    $halaman = $request->halaman;

    switch ($halaman) {
      case 'main':
        $kecamatan = DB::table('public.kecamatan')->orderBy('kecamatan_dispenduk')->get();
        $data = [
          'kecamatan' => $kecamatan,
        ];
        return view('dinas.rekap_kerjasama.main', $data);
        break;

      case 'get_data':
        // return $request->all();
        $kecamatan = $request->kecamatan ?? '';
        $kecamatan_kode = $request->kecamatan_kode ?? '';
        $kelurahan = $request->kelurahan ?? '';
        $kelurahan_kode = $request->kelurahan_kode ?? '';

        $data = DB::table('public.kerjasama_mitra as ks')
          ->join('public.users as u', 'u.id', 'ks.user_id')
          ->join('public.mitra as m', 'm.id_mitra', 'ks.mitra_id')
          ->leftJoin('public.kecamatan as kec', 'kec.kecamatan_id', 'u.id_kecamatan')
          ->leftJoin('public.kelurahan as kel', 'kel.kelurahan_id', 'u.id_kelurahan')
          ->when($kecamatan !== 'All', function ($w) use ($kecamatan_kode, $kelurahan_kode, $kelurahan) {
            if ($kelurahan !== 'All') {
              return $w->where('kel.kelurahan_kode', $kelurahan_kode);
            } else {
              return $w->where('kec.kecamatan_kode', $kecamatan_kode)->orWhere('kel.kecamatan_kode', $kecamatan_kode);
            }
          })
          ->whereNull('ks.deleted_at')
          ->select([
            'm.nama_mitra',
            'u.nama as nama_bunda',
            'ks.sebagai',
            'ks.mou',
            'u.tingkat',
            'kec.kecamatan_dispenduk',
            'kec.kecamatan_kode',
            'kel.kelurahan_dispenduk',
            'kel.kelurahan_kode',
          ]);

        return DataTables::queryBuilder($data)
          ->addIndexColumn()
          ->addColumn('file_mou', function ($a) {
            $button = '';
            if ($a->mou != '') {
              $button = '<a href="' . $a->mou . '" target="_blank">Lihat</a>';
            }
            return $button;
          })
          ->addCOlumn('wilayah', function ($a) {
            $wilayah = 'Kota Surabaya';
            if ($a->tingkat == 'kecamatan') {
              $wilayah = 'Kecamatan ' . $a->kecamatan_dispenduk;
            } else if ($a->tingkat == 'kelurahan') {
              $wilayah = 'Kelurahan ' . $a->kelurahan_dispenduk;
            }

            return $wilayah;
          })
          ->rawColumns(['file_mou'])
          ->make(true);
        break;

      default:
        return '404 - Halaman tidak ditemukan';
        break;
    }
  }

  public static function anomali_nama(Request $request)
  {
    // return $request->all();
    $kecamatan = $request->kecamatan ?? '';
    $kecamatan_kode = $request->kecamatan_kode ?? '';
    $kelurahan = $request->kelurahan ?? '';
    $kelurahan_kode = $request->kelurahan_kode ?? '';

    $arr_kecamatan = [];
    $arr_kelurahan = [];

    if ($kecamatan != '' && $kecamatan != 'All') {
      $data_kecamatan = DB::table('public.kecamatan')->where('kecamatan_dispenduk', $kecamatan)->first();
      $arr_kecamatan = [$data_kecamatan->kecamatan_dispenduk, $data_kecamatan->dispenduk_baru];
    }

    if ($kelurahan != '' && $kelurahan != 'All') {
      $data_kelurahan = DB::table('public.kelurahan')->where('kelurahan_dispenduk', $kelurahan)->first();
      $arr_kelurahan = [$data_kelurahan->kelurahan_dispenduk, $data_kelurahan->dispenduk_baru];
    }

    return ['arr_kecamatan' => $arr_kecamatan, 'arr_kelurahan' => $arr_kelurahan];
  }
}
