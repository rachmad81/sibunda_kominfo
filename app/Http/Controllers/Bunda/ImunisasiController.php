<?php

namespace App\Http\Controllers\Bunda;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB, Session, Auth;

class ImunisasiController extends Controller
{
  function main(Request $request)
  {
    $tahun_all = DB::connection('pgsql_paud')->table('history.kebiasaan_anak_rumah')->select('tahun')->distinct()->get();
    $tahun_all1 = DB::connection('pgsql_paud')->table('history.kebiasaan_anak_sekolah')->select('tahun')->distinct()->get();
    $tahun1 = collect($tahun_all)->pluck('tahun')->unique()->toArray();
    $tahun2 = collect($tahun_all1)->pluck('tahun')->unique()->toArray();
    $tahun = array_merge($tahun1, $tahun2);
    rsort($tahun);
    $judul = 'Riwayat Imunisasi Murid';
    return view('bunda.imunisasi.main', ['tahun' => $tahun, 'judul' => $judul]);
  }

  function cari_sekolah(Request $request)
  {
    $jenjang = $request->jenjang;
    $cari = $request->cari;
    if ($jenjang == 'PAUD') {
      $conn = 'pgsql_paud';
      $arr_jenjang = "'TK','RA','TPA','SPS','KB'";
    } else {
      $conn = 'db_sd';
      $arr_jenjang = "'SD','MI'";
    }
    // $where = '';
    if (Auth::getUser()->tingkat == 'kecamatan') {
      $where = " AND kec.kecamatan_id='" . Auth::getUser()->id_kecamatan . "'";
    } elseif (Auth::getUser()->tingkat == 'kelurahan') {
      $where = " AND kel.kelurahan_id='" . Auth::getUser()->id_kelurahan . "'";
    } else {
      $where = "";
    }
    $sekolah = DB::connection($conn)->table('public.sekolah as s')
      ->join('public.master_user as u', 'u.username', 's.npsn')
      ->join('public.kecamatan as kec', 'kec.kecamatan_kode', 's.kec_id')
      ->join('public.kelurahan as kel', 'kel.kelurahan_kode', 's.desa')
      ->selectRaw("s.nama,s.npsn,kec.kecamatan_dispenduk as kecamatan,kel.kelurahan_dispenduk as kelurahan,s.kepala as ks")
      ->whereRaw("u.user_enable is true AND s.jenjang in ($arr_jenjang) AND (s.nama ilike '%$cari%' OR s.npsn ilike '%$cari%') $where")
      ->limit(10)->get();

    foreach ($sekolah as $s) {
      $siswa = DB::connection($conn)->table('siswa as s')->selectRaw("kelas,rombel")->whereRaw("npsn='$s->npsn' AND status_siswa='Aktif' AND alumni is not true")->groupByRaw("kelas,rombel")->orderByRaw("kelas ASC,rombel ASC")->get();
      $s->kelas = base64_encode(json_encode($siswa));
    }

    return $sekolah;
  }

  function get_siswa(Request $request)
  {
    $jenjang = $request->jenjang;
    $npsn = $request->npsn;
    $kelas = $request->kelas;
    $rombel = $request->rombel;
    if ($jenjang == 'PAUD') {
      $conn = 'pgsql_paud';
    } else {
      $conn = 'db_sd';
    }

    $sekolah = DB::connection($conn)->table('public.siswa as s')
      ->selectRaw("s.nama,s.nik,s.siswa_id,s.id_siswa,s.status_siswa,s.alumni,s.kelas,s.rombel")
      ->whereRaw("s.status_siswa='Aktif' AND alumni is not true AND s.npsn='$npsn' AND kelas='$kelas' AND rombel='$rombel'")
      ->orderByRaw("s.nama ASC")
      ->get();

    $nik = collect($sekolah)->pluck('nik')->toArray();

    $imunisasi = DB::connection('db_cutt_off')->table('public.history_imunisasi')->whereIn('nik', $nik)->get();

    $content = view('bunda.imunisasi.baris', ['siswa' => $sekolah, 'imunisasi' => $imunisasi])->render();

    return ['content' => $content];
  }

  function tampilkan(Request $request)
  {
    $jenjang = $request->jenjang;
    $npsn = $request->npsn;
    $kelas = $request->kelas;
    $rombel = $request->rombel;
    $nik = $request->nik;
    if ($jenjang == 'PAUD') {
      $conn = 'pgsql_paud';
    } else {
      $conn = 'db_sd';
    }

    $sekolah = DB::connection($conn)->table('public.siswa as s')
      ->join('public.sekolah as se', 'se.npsn', 's.npsn')
      ->selectRaw("s.nama,se.nama as sekolah,s.nik,s.siswa_id,s.id_siswa,s.status_siswa,s.alumni,s.kelas,s.rombel")
      ->whereRaw("s.status_siswa='Aktif' AND s.nik='$nik'")
      ->orderByRaw("s.nama ASC")
      ->first();

    $imunisasi = DB::connection('db_cutt_off')->table('public.history_imunisasi')->where('nik', $nik)->first();

    if (!empty($imunisasi)) {
      $temp = str_replace('""', '"', $imunisasi->imunisasi);
      $temp = json_decode($temp, true);
      $temp = collect($temp)->sortBy('tanggal')->values();
      $sekolah->imunisasi = $temp;
    } else {
      $sekolah->imunisasi = '';
    }

    $content = view('bunda.imunisasi.modal', ['s' => $sekolah])->render();

    return ['content' => $content];
  }
}
