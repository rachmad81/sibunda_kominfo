<?php

namespace App\Http\Controllers\K3_PAUD;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session, Auth, DB;

class DashboardController extends Controller
{
  function index(Request $request)
  {
    $jenis = $request->segment(1);
    $data = [
      'jenis' => $jenis
    ];
    return view('k3_paud_sd.dashboard.index', $data);
  }

  function get_widget(Request $request)
  {
    $tahun_cari = $request->tahun_cari;
    $tingkat = Auth::getUser()->tingkat;
    $kecamatan = Auth::getUser()->id_kecamatan;
    $kelurahan = Auth::getUser()->id_kelurahan;

    $username = Auth::getUSer()->username;

    $program = DB::table('public.program')
      ->where('tahun', $tahun_cari)
      ->where('updated_by', $username)
      ->selectRaw("COUNT(*) as jumlah")->count();

    $kegiatan = DB::table('public.program as p')
      ->join('public.kegiatan as k', 'k.program_id', 'p.id_program')
      ->where('p.tahun', $tahun_cari)
      ->where('k.updated_by', $username)
      ->selectRaw("
        CASE WHEN (COALESCE(k.foto,'')<>'' OR COALESCE(k.hasil_kegiatan,'')<>'' OR COALESCE(k.url_video,'')<>'')
        THEN 'Sudah'
        ELSE 'Belum' END as status_laporan,
        COUNT(*) as jumlah
      ")
      ->groupByRaw("status_laporan")->get();

    $judul = 'Rekap Kegiatan Tahun ' . $tahun_cari;

    $data = [
      'judul' => $judul,
      'program' => $program,
      'kegiatan' => $kegiatan,
    ];

    $content = view('bunda.dashboard.widget', $data)->render();
    return ['content' => $content];
  }
}
