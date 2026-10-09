<?php

namespace App\Http\Controllers\Bunda;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use DB, Auth;

class DiagramPendudukController extends Controller
{
  function main()
  {
    $kecamatan = DB::table('public.kecamatan')->orderBy('kecamatan_dispenduk')->get();
    $data = [
      'kecamatan' => $kecamatan,
    ];
    return view('bunda.diagram_penduduk.index', $data);
  }

  function get_kelurahan(Request $request)
  {
    $kode = $request->kode;
    $dukcapil = $request->dukcapil;

    if ($kode == 'All') {
      return ['code' => 250];
    } else {
      if (Auth::getUser()->tingkat == 'kelurahan') {
        $kelurahan = DB::table('public.kelurahan')->where('kelurahan_id', Auth::getUser()->id_kelurahan)->orderBy('kelurahan_dispenduk')->get();
      } else {
        $kelurahan = DB::table('public.kelurahan')->where('kecamatan_kode', $kode)->orderBy('kelurahan_dispenduk')->get();
      }

      return ['code' => 200, 'kelurahan' => $kelurahan];
    }
  }

  function keyword()
  {
    $keywords = [
      'pindah',
      'meninggal',
      'kesehatan',
      'kosong',
      'cacat',
      'tahui',
      'posisi',
      'abk',
      'urus',
      'Faktor Ekonomi',
      'Faktor Aksesibilitas / Jarak',
      'Faktor Sosial Budaya',
      'Faktor kondisi khusus anak (kesehatan/disabilitas/inklusi)',
      'Alasan Administratif',
    ];

    return $keywords;
  }

  function get_data(Request $request)
  {
    $kecamatan = $request->kecamatan; //"All"
    $kelurahan = $request->kelurahan; //"All"
    $kecamatan_kode = $request->kecamatan_kode;
    $kelurahan_kode = $request->kelurahan_kode;

    $judul = 'Data Diagram Penduduk Usia 5-6 Tahun (Cekin Warga)';
    if ($kecamatan == 'All') {
      $judul .= ' se-Kota Surabaya';
    } else {
      if ($kelurahan == 'All') {
        $judul .= ' se-Kecamatan ' . $kecamatan;
      } else {
        if (Auth::getUser()->tingkat == 'kelurahan') {
          $data_kecamatan = DB::table('public.kelurahan as kel')->join('public.kecamatan as kec', 'kec.kecamatan_kode', 'kel.kecamatan_kode')->where('kelurahan_id', Auth::getUser()->id_kelurahan)->first();
          $kecamatan = $data_kecamatan->kecamatan_dispenduk;
          $kecamatan_kode = $data_kecamatan->kecamatan_kode;
          $judul .= ' se-Kelurahan ' . $kelurahan . ', Kecamatan ' . $kecamatan;
        } else {
          $judul .= ' se-Kelurahan ' . $kelurahan . ', Kecamatan ' . $kecamatan;
        }
      }
    }

    $data = [
      'judul' => $judul,
      'kecamatan' => $kecamatan,
      'kelurahan' => $kelurahan,
      'kecamatan_kode' => $kecamatan_kode,
      'kelurahan_kode' => $kelurahan_kode,
    ];

    $content = view('bunda.diagram_penduduk.tabelnya', $data)->render();
    return ['code' => 200, 'content' => $content];
  }

  function get_jumlah(Request $request)
  {
    $anomali = DashboardASWController::anomali_nama($request);
    $i = $request->i;
    $kecamatan = $request->kecamatan;
    $kelurahan = $request->kelurahan;
    $kecamatan_kode = $request->kecamatan_kode ?? 'All';
    $kelurahan_kode = $request->kelurahan_kode ?? 'All';

    // 1. Ambil data master kecamatan & kelurahan
    $dt_kecamatan = DB::table('kecamatan as kec')
      ->join('kelurahan as kel', 'kel.kecamatan_kode', '=', 'kec.kecamatan_kode')
      ->selectRaw("
              kec.kecamatan_dispenduk,
              kec.dispenduk_baru as kec_baru,
              kel.kelurahan_dispenduk,
              kel.dispenduk_baru as kel_baru
          ")
      ->when($kecamatan != 'All', function ($w) use ($kecamatan_kode) {
        return $w->where('kec.kecamatan_kode', $kecamatan_kode);
      })
      ->when($kelurahan != 'All', function ($w) use ($kelurahan_kode) {
        return $w->where('kel.kelurahan_kode', $kelurahan_kode);
      })
      ->get();

    // Kumpulkan seluruh nama kecamatan & kelurahan (lama & baru) untuk filter whereIn tunggal
    $arr_kec = $dt_kecamatan->pluck('kecamatan_dispenduk')
      ->merge($dt_kecamatan->pluck('kec_baru'))
      ->filter()->unique()->values()->toArray();

    $arr_kel = $dt_kecamatan->pluck('kelurahan_dispenduk')
      ->merge($dt_kecamatan->pluck('kel_baru'))
      ->filter()->unique()->values()->toArray();

    $where0 = "id is not null";
    $where2 = "s.npsn is not null";
    if ($kecamatan == 'All') {
      $where0 .= "";
      $where2 .= "";
    } else {
      if ($kelurahan == 'All') {
        $where0 .= " AND kecamatan='$kecamatan'";
        $where2 .= " AND s.kec_id='$kecamatan_kode'";
      } else {
        $where0 .= " AND kelurahan='$kelurahan'";
        $where2 .= " AND s.desa='$kelurahan_kode'";
      }
    }

    $jumlah_lembaga = DB::connection('pgsql_paud')->table('public.sekolah as s')
      ->join('public.master_user as u', 'u.username', 's.npsn')
      ->join('public.kecamatan as kec', 'kec.kecamatan_kode', 's.kec_id')
      ->join('public.kelurahan as kel', 'kel.kelurahan_kode', 's.desa')
      ->selectRaw("s.nama,s.npsn,s.jenjang,s.status,s.alamat,s.rt,s.rw,kec.kecamatan_dispenduk,kel.kelurahan_dispenduk")
      ->whereRaw("$where2 AND s.nama not ilike ALL(ARRAY['%terbuka%','%selalu%','WUSTHA%','ula%','pps%','pdf%','WUSTHO%']) AND u.user_enable is true")
      ->whereRaw("u.user_enable is true")
      ->whereIn('s.jenjang', ['TPA', 'SPS', 'RA', 'KB', 'TK'])
      ->get();

    $keywords = $this->keyword();

    switch ($i) {
      case '0':
        // 'Jumlah Anak usia 5-6 tahun',
        $jumlah_anak = DB::connection('db_cutt_off')->table('public.penduduk_inject as a')
          ->selectRaw("COUNT(*) as jumlah_total,
          COUNT(*) FILTER (
            WHERE CASE WHEN COALESCE(b.nik,'')<>''
            THEN COALESCE(b.status_sekolah,'') IN ('Sudah','sudah_paud','sudah_sd','Akan sekolah','akan_daftar') OR (COALESCE(a.nama_sekolah,'')<>'' AND a.is_aktif_pendidikan is true)
            ELSE (COALESCE(a.nama_sekolah,'')<>'' AND a.is_aktif_pendidikan is true)
            END
          ) as jumlah_sudah_sekolah,
          COUNT(*) FILTER (
            WHERE CASE WHEN COALESCE(b.nik,'')<>''
            THEN COALESCE(b.status_sekolah,'') NOT IN ('Sudah','sudah_paud','sudah_sd','Akan sekolah','akan_daftar') AND (COALESCE(a.nama_sekolah,'')='' OR a.is_aktif_pendidikan is not true)
            ELSE (COALESCE(a.nama_sekolah,'')='' OR a.is_aktif_pendidikan is not true)
            END
          ) as jumlah_belum_sekolah,
          COUNT(*) FILTER(
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
          COUNT(*) FILTER(
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
          ) as jumlah_belum_survey,

          COUNT(*) FILTER(
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
            END AND ((COALESCE(b.nik,'') <> '' AND COALESCE(b.status_verifikasi,'')='Selesai') AND COALESCE(b.status_alamat,'') IN ('Sesuai','sesuai'))
          ) as jumlah_sudah_survey_alamat_sesuai,

          COUNT(*) FILTER(
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
            END AND ((COALESCE(b.nik,'') <> '' AND COALESCE(b.status_verifikasi,'')='Selesai') AND COALESCE(b.status_alamat,'') IN ('pindah_luar','pindah_dalam'))
          ) as jumlah_sudah_survey_alamat_pindah,

          COUNT(*) FILTER(
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
            END AND ((COALESCE(b.nik,'') <> '' AND COALESCE(b.status_verifikasi,'')='Selesai') AND COALESCE(b.status_alamat,'') NOT IN ('Sesuai','sesuai','pindah_luar','pindah_dalam'))
          ) as jumlah_sudah_survey_alamat_lainnya,

          COUNT(*) FILTER(
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
            END AND ((COALESCE(b.nik,'') <> '' AND COALESCE(b.status_verifikasi,'')='Selesai') AND COALESCE(b.detail_tidak_sekolah,'') ILIKE '%Faktor 1%')
          ) as jumlah_sudah_survey_faktor1,

          COUNT(*) FILTER(
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
            END AND ((COALESCE(b.nik,'') <> '' AND COALESCE(b.status_verifikasi,'')='Selesai') AND COALESCE(b.detail_tidak_sekolah,'') ILIKE '%Faktor 2%')
          ) as jumlah_sudah_survey_faktor2,

          COUNT(*) FILTER(
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
            END AND ((COALESCE(b.nik,'') <> '' AND COALESCE(b.status_verifikasi,'')='Selesai') AND COALESCE(b.detail_tidak_sekolah,'') ILIKE '%Faktor 3%')
          ) as jumlah_sudah_survey_faktor3,

          COUNT(*) FILTER(
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
            END AND ((COALESCE(b.nik,'') <> '' AND COALESCE(b.status_verifikasi,'')='Selesai') AND COALESCE(b.detail_tidak_sekolah,'') ILIKE '%Faktor 4%')
          ) as jumlah_sudah_survey_faktor4,

          COUNT(*) FILTER(
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
            END AND ((COALESCE(b.nik,'') <> '' AND COALESCE(b.status_verifikasi,'')='Selesai') AND COALESCE(b.detail_tidak_sekolah,'') ILIKE '%Faktor 5%')
          ) as jumlah_sudah_survey_faktor5,

          COUNT(*) FILTER(
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
            END AND ((COALESCE(b.nik,'') <> '' AND COALESCE(b.status_verifikasi,'')='Selesai') AND COALESCE(b.detail_tidak_sekolah,'') NOT ILIKE ALL(ARRAY['%Faktor 5%','%Faktor 4%','%Faktor 3%','%Faktor 2%','%Faktor 1%']))
          ) as jumlah_sudah_survey_lain_faktor
          ")
          ->leftjoin('public.survey_bunda as b', 'b.nik', 'a.nik')
          ->when(!empty($kecamatan) && $kecamatan != 'All', function ($q) use ($kecamatan, $anomali) {
            $q->whereIn('a.kecamatan_dom', $anomali['arr_kecamatan']);
          })
          ->when(!empty($kelurahan) && $kelurahan != 'All', function ($q) use ($kelurahan, $anomali) {
            $q->whereIn('a.kelurahan_dom', $anomali['arr_kelurahan']);
          })
          ->where(function ($w) {
            $w->where('a.is_aktif_kependudukan', true)->where('a.status_keberadaan', 'ADA');
          })
          ->whereRaw("(EXTRACT(YEAR FROM AGE(now(),tgl_lahir)) BETWEEN 5 AND 6)")
          ->whereIn(DB::raw("COALESCE(NULLIF(a.kecamatan_dom,''), a.kecamatan)"), $arr_kec)
          ->whereIn(DB::raw("COALESCE(NULLIF(a.kelurahan_dom,''), a.kelurahan)"), $arr_kel)->first();

        $id = 'penduduk';

        $dril = [
          [
            'name' => 'Jumlah Total',
            'y' => $jumlah_anak->jumlah_total,
            'kategori' => $id,
            'color' => '#544fc5'
          ],
          [
            'name' => 'Jumlah Sudah Sekolah',
            'y' => $jumlah_anak->jumlah_sudah_sekolah,
            'kategori' => $id,
            'color' => '#544fc5'
          ],
          [
            'name' => 'Jumlah Belum Sekolah',
            'y' => $jumlah_anak->jumlah_belum_sekolah,
            'kategori' => $id,
            'color' => '#dd6f6fff'
          ],
          [
            'name' => 'Jumlah Belum Survey',
            'y' => $jumlah_anak->jumlah_belum_survey,
            'kategori' => $id,
            'color' => '#ff4343ff'
          ],
          [
            'name' => 'Jumlah Sudah Survey',
            'y' => $jumlah_anak->jumlah_sudah_survey,
            'kategori' => $id,
            'color' => '#81e6d0ff'
          ],
          [
            'name' => 'Jumlah Sudah Survey (Alamat Sesuai)',
            'y' => $jumlah_anak->jumlah_sudah_survey_alamat_sesuai,
            'kategori' => $id,
            'color' => '#f5b46aff'
          ],
          [
            'name' => 'Jumlah Sudah Survey (Alamat Pindah)',
            'y' => $jumlah_anak->jumlah_sudah_survey_alamat_pindah,
            'kategori' => $id,
            'color' => '#f5b46aff'
          ],
          [
            'name' => 'Jumlah Sudah Survey (Alamat Lainnya)',
            'y' => $jumlah_anak->jumlah_sudah_survey_alamat_lainnya,
            'kategori' => $id,
            'color' => '#f5b46aff'
          ],
          [
            'name' => 'Jumlah Sudah Survey (Faktor 1 : Ekonomi & Kerentanan)',
            'y' => $jumlah_anak->jumlah_sudah_survey_faktor1,
            'kategori' => $id,
            'color' => '#f56af5'
          ],
          [
            'name' => 'Jumlah Sudah Survey (Faktor 2 : Pengasuhan & Dukungan)',
            'y' => $jumlah_anak->jumlah_sudah_survey_faktor2,
            'kategori' => $id,
            'color' => '#f56af5'
          ],
          [
            'name' => 'Jumlah Sudah Survey (Faktor 3 : Kondisi Anak & Kesehatan)',
            'y' => $jumlah_anak->jumlah_sudah_survey_faktor3,
            'kategori' => $id,
            'color' => '#f56af5'
          ],
          [
            'name' => 'Jumlah Sudah Survey (Faktor 4 : Administrasi)',
            'y' => $jumlah_anak->jumlah_sudah_survey_faktor4,
            'kategori' => $id,
            'color' => '#f56af5'
          ],
          [
            'name' => 'Jumlah Sudah Survey (Faktor 5 : Lainnya)',
            'y' => $jumlah_anak->jumlah_sudah_survey_faktor5,
            'kategori' => $id,
            'color' => '#f56af5'
          ],
          [
            'name' => 'Jumlah Sudah Survey (Diluar 5 Faktor)',
            'y' => $jumlah_anak->jumlah_sudah_survey_lain_faktor,
            'kategori' => $id,
            'color' => '#f56af5'
          ],
        ];

        return ['i' => $i, 'jumlah_display' => number_format($jumlah_anak->jumlah_total, 0, '', '.'), 'jumlah' => $jumlah_anak->jumlah_total, 'dril' => $dril, 'id_kolom' => 'penduduk'];
        break;

      default:
        $jumlah = 0;
        break;
    }

    return ['i' => $i, 'jumlah' => number_format($jumlah, 0, '', '.'), 'jumlah_murni' => $jumlah];
  }

  function form_detail(Request $request)
  {
    // return 'sini';
    $jenis = $request->jenis;
    $kategori = $request->kategori;
    $kecamatan = $request->kecamatan;
    $kelurahan = $request->kelurahan;
    $kecamatan_kode = $request->kecamatan_kode;
    $kelurahan_kode = $request->kelurahan_kode;
    $tabel_detail = '';

    $keywords = $this->keyword();;
    $str_keyword = "'%";
    $str_keyword .= implode("%','%", $keywords);
    $str_keyword .= "%'";

    $where0 = "id is not null";
    if ($kecamatan == 'All') {
      $where0 .= "";
    } else {
      if ($kelurahan == 'All') {
        $where0 .= " AND kecamatan='$kecamatan'";
      } else {
        $where0 .= " AND kelurahan='$kelurahan'";
      }
    }

    $kategori_tidak_sekolah = [
      'Faktor Ekonomi',
      'Faktor Aksesibilitas / Jarak',
      'Faktor Sosial Budaya',
      'Faktor kondisi khusus anak (kesehatan/disabilitas/inklusi)',
      'Alasan Administratif',
    ];
    $str_kategori = "'%";
    $str_kategori .= implode("%','%", $kategori_tidak_sekolah);
    $str_kategori .= "%'";

    switch ($kategori) {
      case 'penduduk':
        $kolom_body = [
          ['data' => 'DT_RowIndex', 'name' => 'DT_RowIndex', 'title' => 'No', 'orderable' => false, 'searchable' => false, 'defaultContent' => ''],
          ['data' => 'nama', 'name' => 'a.nama_lengkap', 'title' => 'Nama', 'defaultContent' => ''],
          ['data' => 'data_nik', 'name' => 'data_nik', 'title' => 'NIK', 'defaultContent' => ''],
          ['data' => 'tempat_lahir', 'name' => 'a.tempat_lahir', 'title' => 'Tempat Lahir', 'defaultContent' => ''],
          ['data' => 'tgl_lahir', 'name' => 'a.tgl_lahir', 'title' => 'Tanggal Lahir', 'defaultContent' => ''],
          ['data' => 'alamat', 'name' => 'a.alamat', 'title' => 'Alamat', 'defaultContent' => ''],
          ['data' => 'rt', 'name' => 'a.rt', 'title' => 'RT', 'defaultContent' => ''],
          ['data' => 'rw', 'name' => 'a.rw', 'title' => 'RW', 'defaultContent' => ''],
          ['data' => 'kelurahan', 'name' => 'a.kelurahan', 'title' => 'Kelurahan', 'defaultContent' => ''],
          ['data' => 'kecamatan', 'name' => 'a.kecamatan', 'title' => 'Kecamatan', 'defaultContent' => ''],
          ['data' => 'alamat_dom', 'name' => 'alamat_dom', 'title' => 'Alamat Domisili', 'defaultContent' => ''],
          ['data' => 'rt_dom', 'name' => 'rt_dom', 'title' => 'RT Domisili', 'defaultContent' => ''],
          ['data' => 'rw_dom', 'name' => 'rw_dom', 'title' => 'RW Domisili', 'defaultContent' => ''],
          ['data' => 'kelurahan_dom', 'name' => 'kelurahan_dom', 'title' => 'Kelurahan Domisili', 'defaultContent' => ''],
          ['data' => 'kecamatan_dom', 'name' => 'kecamatan_dom', 'title' => 'Kecamatan Domisili', 'defaultContent' => ''],
          ['data' => 'profil', 'name' => 'a.nama_sekolah', 'title' => 'Data Sekolah', 'defaultContent' => ''],
          ['data' => 'status_aktif_pendidikan', 'name' => 'status_aktif_pendidikan', 'title' => 'Status Aktif Pendidikan', 'defaultContent' => ''],
          ['data' => 'status_survey', 'name' => 'b.status_verifikasi', 'title' => 'Status Survey', 'defaultContent' => ''],
          ['data' => 'status_domisili', 'name' => 'status_domisili', 'title' => 'Status Alamat', 'defaultContent' => ''],
          ['data' => 'detail_alamat', 'name' => 'detail_alamat', 'title' => 'Catatan Alamat', 'defaultContent' => ''],
          ['data' => 'status_sekolah_n', 'name' => 'status_sekolah_n', 'title' => 'Status Sekolah', 'defaultContent' => ''],
          ['data' => 'detail_sekolah', 'name' => 'detail_sekolah', 'title' => 'Catatan Sekolah', 'defaultContent' => ''],
          ['data' => 'status_faktor', 'name' => 'status_faktor', 'title' => 'Faktor Tidak Bersekolah', 'defaultContent' => ''],
          ['data' => 'pilihan_detail_tidak_sekolah', 'name' => 'pilihan_detail_tidak_sekolah', 'title' => 'Catatan Tidak Bersekolah', 'defaultContent' => ''],
          ['data' => 'status_edukasi', 'name' => 'status_edukasi', 'title' => 'Catatan Tidak Bersekolah', 'defaultContent' => ''],
          ['data' => 'status_tanggapan', 'name' => 'status_tanggapan', 'title' => 'Tanggapan Orang Tua', 'defaultContent' => ''],
          ['data' => 'status_rtl', 'name' => 'status_rtl', 'title' => 'Rencana Tindak Lanjut', 'defaultContent' => ''],
        ];

        $judul = 'Data terindikasi ATS usia 5-6 tahun';
        $judul .= ' (' . $jenis . ')';
        break;

      default:
        $jumlah = [];
        break;
    }

    $data = [
      'kolom_body' => json_encode($kolom_body),
      'judul' => $judul,
      'jenis' => $jenis,
      'kategori' => $kategori,
      'kecamatan' => $kecamatan,
      'kelurahan' => $kelurahan,
      'kecamatan_kode' => $kecamatan_kode,
      'kelurahan_kode' => $kelurahan_kode,
      'tabel_detail' => $tabel_detail,
    ];

    $content = view('bunda.diagram_penduduk.detail', $data)->render();
    return ['content' => $content];
  }

  function get_detail(Request $request)
  {
    $jenis = $request->jenis;
    $kategori = $request->kategori;
    $kecamatan = $request->kecamatan;
    $kelurahan = $request->kelurahan;
    $kecamatan_kode = $request->kecamatan_kode ?? 'All';
    $kelurahan_kode = $request->kelurahan_kode ?? 'All';

    // 1. Ambil data master kecamatan & kelurahan
    $dt_kecamatan = DB::table('kecamatan as kec')
      ->join('kelurahan as kel', 'kel.kecamatan_kode', '=', 'kec.kecamatan_kode')
      ->selectRaw("
              kec.kecamatan_dispenduk,
              kec.dispenduk_baru as kec_baru,
              kel.kelurahan_dispenduk,
              kel.dispenduk_baru as kel_baru
          ")
      ->when($kecamatan != 'All', function ($w) use ($kecamatan_kode) {
        return $w->where('kec.kecamatan_kode', $kecamatan_kode);
      })
      ->when($kelurahan != 'All', function ($w) use ($kelurahan_kode) {
        return $w->where('kel.kelurahan_kode', $kelurahan_kode);
      })
      ->get();

    // Kumpulkan seluruh nama kecamatan & kelurahan (lama & baru) untuk filter whereIn tunggal
    $arr_kec = $dt_kecamatan->pluck('kecamatan_dispenduk')
      ->merge($dt_kecamatan->pluck('kec_baru'))
      ->filter()->unique()->values()->toArray();

    $arr_kel = $dt_kecamatan->pluck('kelurahan_dispenduk')
      ->merge($dt_kecamatan->pluck('kel_baru'))
      ->filter()->unique()->values()->toArray();

    switch ($kategori) {
      case 'penduduk':
        $dril = [
          [
            'name' => 'Jumlah Total',
          ],
          [
            'name' => 'Jumlah Sudah Sekolah',
          ],
          [
            'name' => 'Jumlah Belum Sekolah',
          ],
          [
            'name' => 'Jumlah Belum Survey',
          ],
          [
            'name' => 'Jumlah Sudah Survey',
          ],
          [
            'name' => 'Jumlah Sudah Survey (Alamat Sesuai)',
          ],
          [
            'name' => 'Jumlah Sudah Survey (Alamat Pindah)',
          ],
          [
            'name' => 'Jumlah Sudah Survey (Alamat Lainnya)',
          ],
          [
            'name' => 'Jumlah Sudah Survey (Faktor 1 : Ekonomi & Kerentanan)',
          ],
          [
            'name' => 'Jumlah Sudah Survey (Faktor 2 : Pengasuhan & Dukungan)',
          ],
          [
            'name' => 'Jumlah Sudah Survey (Faktor 3 : Kondisi Anak & Kesehatan)',
          ],
          [
            'name' => 'Jumlah Sudah Survey (Faktor 4 : Administrasi)',
          ],
          [
            'name' => 'Jumlah Sudah Survey (Faktor 5 : Lainnya)',
          ],
          [
            'name' => 'Jumlah Sudah Survey (Diluar 5 Faktor)',
          ],
        ];

        $nik = [];
        if ($jenis == 'Jumlah Belum Survey') {
          // $nik = DB::connection('db_cutt_off')->table('')
        }

        $jumlah = DB::connection('db_cutt_off')->table('public.penduduk_inject as a')
          ->selectRaw("
          a.nik,a.nama_lengkap as nama,a.tempat_lahir,a.tgl_lahir,a.alamat,a.rt,a.rw,a.kelurahan,a.kecamatan,a.jenjang_sekolah,a.nama_sekolah,a.nama_sekolah,b.status,b.status_alamat,b.status_sekolah,b.status_verifikasi,b.detail_sekolah,b.detail_alamat,a.is_aktif_pendidikan,b.detail_tidak_sekolah,b.pilihan_detail_tidak_sekolah,
          COALESCE(NULLIF(a.alamat_dom,''),a.alamat) as alamat_dom,COALESCE(NULLIF(a.rt_dom,''),a.rt) as rt_dom,COALESCE(NULLIF(a.rw_dom,''),a.rw) as rw_dom,
          COALESCE(NULLIF(a.kelurahan_dom,''),a.kelurahan) as kelurahan_dom,COALESCE(NULLIF(a.kecamatan_dom,''),a.kecamatan) as kecamatan_dom,
          b.edukasi_ortu,b.tanggapan_ortu,b.tindak_lanjut,b.penanganan_abk
          ")
          ->leftjoin('public.survey_bunda as b', 'b.nik', 'a.nik')
          ->when($kecamatan != 'All', function ($w) use ($kecamatan) {
            return $w->where('a.kecamatan', $kecamatan);
          })
          ->when($kelurahan != 'All', function ($w) use ($kelurahan) {
            return $w->where('a.kelurahan', $kelurahan);
          })
          ->when($jenis, function ($q) use ($jenis) {
            switch ($jenis) {
              case 'Jumlah Total':
                break;
              case 'Jumlah Sudah Sekolah':
                $q->whereRaw("
                  CASE WHEN COALESCE(b.nik,'')<>''
                  THEN COALESCE(b.status_sekolah,'') IN ('Sudah','sudah_paud','sudah_sd','Akan sekolah','akan_daftar') OR (COALESCE(a.nama_sekolah,'')<>'' AND a.is_aktif_pendidikan is true)
                  ELSE (COALESCE(a.nama_sekolah,'')<>'' AND a.is_aktif_pendidikan is true)
                  END
                ");
                break;
              case 'Jumlah Belum Sekolah':
                $q->whereRaw("
                  CASE WHEN COALESCE(b.nik,'')<>''
                  THEN COALESCE(b.status_sekolah,'') NOT IN ('Sudah','sudah_paud','sudah_sd','Akan sekolah','akan_daftar') AND (COALESCE(a.nama_sekolah,'')='' OR a.is_aktif_pendidikan is not true)
                  ELSE (COALESCE(a.nama_sekolah,'')='' OR a.is_aktif_pendidikan is not true)
                  END
                ");
                break;
              case 'Jumlah Belum Survey':
                $q->whereRaw("
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
                ");
                break;
              case 'Jumlah Sudah Survey':
                $q->whereRaw("
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
                ");
                break;
              case 'Jumlah Sudah Survey (Alamat Sesuai)':
                $q->whereRaw("
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
                  END AND ((COALESCE(b.nik,'') <> '' AND COALESCE(b.status_verifikasi,'')='Selesai') AND COALESCE(b.status_alamat,'') IN ('Sesuai','sesuai'))
                ");
                break;
              case 'Jumlah Sudah Survey (Alamat Pindah)':
                $q->whereRaw("
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
                  END AND ((COALESCE(b.nik,'') <> '' AND COALESCE(b.status_verifikasi,'')='Selesai') AND COALESCE(b.status_alamat,'') IN ('pindah_luar','pindah_dalam'))
                ");
                break;
              case 'Jumlah Sudah Survey (Alamat Lainnya)':
                $q->whereRaw("
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
                  END AND ((COALESCE(b.nik,'') <> '' AND COALESCE(b.status_verifikasi,'')='Selesai') AND COALESCE(b.status_alamat,'') NOT IN ('Sesuai','sesuai','pindah_luar','pindah_dalam'))
                ");
                break;
              case 'Jumlah Sudah Survey (Faktor 1 : Ekonomi &amp; Kerentanan)':
                $q->whereRaw("
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
                  END AND ((COALESCE(b.nik,'') <> '' AND COALESCE(b.status_verifikasi,'')='Selesai') AND COALESCE(b.detail_tidak_sekolah,'') ILIKE '%Faktor 1%')
                ");
                break;
              case 'Jumlah Sudah Survey (Faktor 2 : Pengasuhan &amp; Dukungan)':
                $q->whereRaw("
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
                  END AND ((COALESCE(b.nik,'') <> '' AND COALESCE(b.status_verifikasi,'')='Selesai') AND COALESCE(b.detail_tidak_sekolah,'') ILIKE '%Faktor 2%')
                ");
                break;
              case 'Jumlah Sudah Survey (Faktor 3 : Kondisi Anak &amp; Kesehatan)':
                $q->whereRaw("
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
                  END AND ((COALESCE(b.nik,'') <> '' AND COALESCE(b.status_verifikasi,'')='Selesai') AND COALESCE(b.detail_tidak_sekolah,'') ILIKE '%Faktor 3%')
                ");
                break;
              case 'Jumlah Sudah Survey (Faktor 4 : Administrasi)':
                $q->whereRaw("
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
                  END AND ((COALESCE(b.nik,'') <> '' AND COALESCE(b.status_verifikasi,'')='Selesai') AND COALESCE(b.detail_tidak_sekolah,'') ILIKE '%Faktor 4%')
                ");
                break;
              case 'Jumlah Sudah Survey (Faktor 5 : Lainnya)':
                $q->whereRaw("
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
                  END AND ((COALESCE(b.nik,'') <> '' AND COALESCE(b.status_verifikasi,'')='Selesai') AND COALESCE(b.detail_tidak_sekolah,'') ILIKE '%Faktor 5%')
                ");
                break;
              case 'Jumlah Sudah Survey (Diluar 5 Faktor)':
                $q->whereRaw("
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
                  END AND ((COALESCE(b.nik,'') <> '' AND COALESCE(b.status_verifikasi,'')='Selesai') AND COALESCE(b.detail_tidak_sekolah,'') NOT ILIKE ALL(ARRAY['%Faktor 5%','%Faktor 4%','%Faktor 3%','%Faktor 2%','%Faktor 1%']))
                ");
                break;
              default:
                break;
            }
          })
          ->where(function ($w) {
            $w->where('a.is_aktif_kependudukan', true)->where('a.status_keberadaan', 'ADA');
          })
          ->whereRaw("(EXTRACT(YEAR FROM AGE(now(),tgl_lahir)) BETWEEN 5 AND 6)")
          ->whereIn(DB::raw("COALESCE(NULLIF(a.kecamatan_dom,''), a.kecamatan)"), $arr_kec)
          ->whereIn(DB::raw("COALESCE(NULLIF(a.kelurahan_dom,''), a.kelurahan)"), $arr_kel);

        return DataTables::of($jumlah)->addIndexColumn()
          ->addColumn('profil', function ($r) {
            $hasil = '';
            if ($r->nama_sekolah ?? '' != '') {
              $hasil .= '<span class="alert-primary">' . $r->nama_sekolah . ' <b>(' . ($r->jenjang_sekolah ?? '') . ')</b></span>';
            }

            return $hasil;
          })
          ->addColumn('data_nik', function ($i) {
            return '<a href="' . route('bunda-dashboard_asw-detail_informasi', ['nik' => $i->nik]) . '" target="_blank">' . $i->nik . '</a>';
          })
          ->addColumn('status_aktif_pendidikan', function ($r) {
            $hasil = $r->is_aktif_pendidikan == true ? 'Aktif' : 'Pernah,Belum atau Tidak Aktif';

            return $hasil;
          })
          ->addColumn('status_survey', function ($r) {
            if ($r->status_verifikasi == 'Selesai') {
              $hasil = '<span class="alert-success">' . $r->status_verifikasi . '</span>';
            } else {
              $hasil = '<span class="alert-default">' . $r->status_verifikasi . '</span>';
            }
            return $hasil;
          })
          ->addColumn('status_domisili', function ($w) {
            $domisili = \App\Tools\ParameterATS::data_domisili();
            $dt = collect($domisili)->where('value', $w->status_alamat)->first();
            return ($dt['text'] ?? $w->status_alamat);
          })
          ->addColumn('status_sekolah_n', function ($w) {
            $sekolah = \App\Tools\ParameterATS::data_sekolah();
            $dt = collect($sekolah)->where('value', $w->status_sekolah)->first();
            return $dt['text'] ?? $w->status_sekolah;
          })
          ->addColumn('status_faktor', function ($w) {
            $faktor = \App\Tools\ParameterATS::data_faktor();
            $arr_tidak_Sekolah = explode('|', $w->detail_tidak_sekolah);
            $dt = collect($faktor)->whereIn('value', $arr_tidak_Sekolah);

            $hasil_faktor = '<ol>';
            foreach ($dt as $d) {
              $hasil_faktor .= '<li>- ' . $d['text'] . '</li>';
            }
            $hasil_faktor .= '</ol>';
            return $hasil_faktor;
          })
          ->addColumn('status_edukasi', function ($w) {
            $edukasi = \App\Tools\ParameterATS::data_edukasi();

            $edukasi_ortu = str_replace(['-', ';'], ['', ''], $w->edukasi_ortu);
            $arr_edukasi = collect($edukasi)->pluck('value')->values()->toArray();
            $edukasi_ortu = explode('|', $edukasi_ortu ?? '') ?? [];
            $edukasi_lainnya = array_diff($edukasi_ortu, $arr_edukasi);

            $edukasi_lainnya = implode('', $edukasi_lainnya);

            $dt = collect($edukasi)->whereIn('value', $edukasi_ortu);

            $hasil_faktor = '<ol>';
            foreach ($dt as $d) {
              $hasil_faktor .= '<li>- ' . $d['text'] . '</li>';
            }
            $hasil_faktor .= '<li>- ' . $edukasi_lainnya . '</li>';
            $hasil_faktor .= '</ol>';
            return $hasil_faktor;
          })
          ->addColumn('status_tanggapan', function ($w) {
            $sekolah = \App\Tools\ParameterATS::data_tanggapan();
            $dt = collect($sekolah)->where('value', $w->tanggapan_ortu)->first();
            return $dt['text'] ?? $w->tanggapan_ortu;
          })
          ->addColumn('status_rtl', function ($w) {
            $faktor = \App\Tools\ParameterATS::data_rtl();
            $arr_tidak_Sekolah = explode('|', $w->tindak_lanjut);
            $dt = collect($faktor)->whereIn('value', $arr_tidak_Sekolah);

            $hasil_faktor = '';
            if ($w->tindak_lanjut != '') {
              $hasil_faktor .= '<b>Rencana Tindak Lanjut</b>';
              $hasil_faktor .= '<ol>';
              foreach ($dt as $d) {
                $hasil_faktor .= '<li>- ' . $d['text'] . '</li>';
              }
              $hasil_faktor .= '</ol>';
            }

            if ($w->penanganan_abk != '') {
              $hasil_faktor .= $w->penanganan_abk;
            }
            return $hasil_faktor;
          })
          ->rawColumns(['status_domisili', 'status_sekolah_n', 'status_faktor', 'detail_alamat', 'detail_sekolah', 'pilihan_detail_tidak_sekolah', 'status_edukasi', 'status_tanggapan', 'status_rtl', 'data_nik', 'profil', 'hasil_survey', 'hasil_alamat', 'hasil_sekolah', 'status_survey'])
          ->make(true);
        break;

      default:
        $jumlah = [];
        break;
    }

    return DataTables::of($jumlah)->addIndexColumn()->make(true);
  }
}
