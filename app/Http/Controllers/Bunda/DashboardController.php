<?php

namespace App\Http\Controllers\Bunda;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB, Auth, Session;

class DashboardController extends Controller
{
  function index(Request $request)
  {
    return view('bunda.dashboard.index');
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
        CASE WHEN (COALESCE(k.foto,'')<>'' OR COALESCE(k.url_video,'')<>'' OR COALESCE(k.hasil_kegiatan,'')<>'')
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

  function get_survey(Request $request)
  {
    $judul = 'Rekap Penduduk usia 0 - 6 tahun';
    $tingkat = Auth::getUser()->tingkat;

    $arr_kecamatan = [];
    $arr_kelurahan = [];
    $kelurahan = '';
    $kecamatan = '';

    if ($tingkat == 'kecamatan') {
      $kecamatan = DB::table('public.kecamatan')->where('kecamatan_dispenduk', Auth::getUser()->kecamatan->kecamatan_dispenduk)->first();
      $arr_kecamatan = [$kecamatan->kecamatan_dispenduk, $kecamatan->dispenduk_baru];
    } else if ($tingkat == 'kelurahan') {
      $kecamatan = DB::table('public.kecamatan')->where('kecamatan_kode', Auth::getUser()->kelurahan->kecamatan_kode)->first();
      $arr_kecamatan = [$kecamatan->kecamatan_dispenduk, $kecamatan->dispenduk_baru];
      $kelurahan = DB::table('public.kelurahan')->where('kelurahan_dispenduk', Auth::getUser()->kelurahan->kelurahan_dispenduk)->first();
      $arr_kelurahan = [$kelurahan->kelurahan_dispenduk, $kelurahan->dispenduk_baru];
    }

    $penduduk = DB::connection('db_cutt_off')->table('public.penduduk_inject as a')
      ->leftJoin('public.survey_bunda as b', 'b.nik', 'a.nik')
      ->selectRaw("
        EXTRACT(YEAR FROM AGE(NOW(),a.tgl_lahir)) as usia,
        COUNT(*) as jumlah_penduduk,
        COUNT(*) FILTER (
          WHERE
            CASE WHEN COALESCE(b.nik,'')<>''
            THEN COALESCE(b.status_sekolah,'') IN ('Sudah','sudah_paud','sudah_sd') OR (COALESCE(a.nama_sekolah,'')<>'' AND a.is_aktif_pendidikan is true)
            ELSE (COALESCE(a.nama_sekolah,'')<>'' AND a.is_aktif_pendidikan is true)
            END AND (COALESCE(b.status_sekolah,'') NOT IN ('Akan sekolah','akan_daftar'))
        ) as jumlah_bersekolah,
        COUNT(*) FILTER (
          WHERE
            CASE WHEN COALESCE(b.nik,'')<>''
            THEN COALESCE(b.status_sekolah,'') NOT IN ('Sudah','sudah_paud','sudah_sd','Akan sekolah','akan_daftar') AND (COALESCE(a.nama_sekolah,'')='' OR a.is_aktif_pendidikan is not true)
            ELSE (COALESCE(a.nama_sekolah,'')='' OR a.is_aktif_pendidikan is not true)
            END
        ) as jumlah_belum_sekolah,
        COUNT(*) FILTER (
          WHERE
            CASE
            WHEN COALESCE(b.nik,'') <> '' THEN
                (
                  COALESCE(b.status_sekolah,'') NOT IN ('Sudah','sudah_paud','sudah_sd','Akan sekolah','akan_daftar')
                  AND (COALESCE(a.nama_sekolah,'') = ''
                  OR a.is_aktif_pendidikan IS NOT TRUE)
                )
            ELSE
                (
                  COALESCE(a.nama_sekolah,'') = ''
                  OR a.is_aktif_pendidikan IS NOT TRUE
                )
          END AND (COALESCE(b.nik,'') <> '' AND COALESCE(b.status_verifikasi,'')='Selesai')
        ) as jumlah_sudah_survey,
        COUNT(*) FILTER (
          WHERE
            CASE
            WHEN COALESCE(b.nik,'') <> '' THEN
                (
                  COALESCE(b.status_sekolah,'') NOT IN ('Sudah','sudah_paud','sudah_sd','Akan sekolah','akan_daftar')
                  AND (COALESCE(a.nama_sekolah,'') = ''
                  OR a.is_aktif_pendidikan IS NOT TRUE)
                )
            ELSE
                (
                  COALESCE(a.nama_sekolah,'') = ''
                  OR a.is_aktif_pendidikan IS NOT TRUE
                )
          END AND (COALESCE(b.nik,'') = '' OR COALESCE(b.status_verifikasi,'')<>'Selesai')
        ) as jumlah_belum_survey
        ")
      ->when($kecamatan != '', function ($w) use ($kecamatan, $arr_kecamatan) {
        return $w->whereIn(DB::raw("COALESCE(NULLIF(a.kecamatan_dom,''),a.kecamatan)"), $arr_kecamatan);
      })
      ->when($kelurahan != '', function ($w) use ($kelurahan, $arr_kelurahan) {
        return $w->whereIn(DB::raw("COALESCE(NULLIF(a.kelurahan_dom,''),a.kelurahan)"), $arr_kelurahan);
      })
      ->whereRaw("(EXTRACT(YEAR FROM AGE(NOW(),a.tgl_lahir)) BETWEEN 0 AND 6)")
      ->where(function ($w) {
        $w->where('a.is_aktif_kependudukan', true)->where('status_keberadaan', 'ADA');
      })
      ->groupByRaw("EXTRACT(YEAR FROM AGE(NOW(),a.tgl_lahir))")
      ->orderByRaw("usia")
      ->get();

    $data = [
      'judul' => $judul,
      'penduduk' => $penduduk,
    ];
    $content = view('bunda.dashboard.survey', $data)->render();
    return ['content' => $content];
  }
}
