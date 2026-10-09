<?php

namespace App\Http\Controllers\Bunda;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Auth, DB, DataTables;

class DashboardASWController extends Controller
{
  public function main()
  {
    $kecamatan = DB::table('public.kecamatan')->orderBy('kecamatan_dispenduk')->get();
    $data = [
      'kecamatan' => $kecamatan,
    ];
    return view('bunda.dashboard_asw.main', $data);
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

  function get_data(Request $request)
  {
    $kecamatan = $request->kecamatan ?? '';
    $kecamatan_kode = $request->kecamatan_kode ?? '';
    $kelurahan = $request->kelurahan ?? '';
    $kelurahan_kode = $request->kelurahan_kode ?? '';
    $jenis = $request->jenis ?? '';

    switch ($jenis) {
      case 'cekin_warga':
        $content = $this->cekin_warga($request);
        break;
      case 'sibunda':
        $content = $this->sibunda($request);
        break;
      case 'dinsos':
        $content = $this->dinsos($request);
        break;
      case 'rutilahu':
        $content = $this->rutilahu($request);
        break;
      case 'dp3a':
        $content = $this->dp3a($request);
        break;
      case 'posyandu':
        $content = $this->posyandu($request);
        break;
      case 'dispusip':
        $content = $this->dispusip($request);
        break;
      case 'soth':
        $content = $this->soth($request);
        break;

      case 'tanpa_intervensi':
        $content = $this->tanpa_intervensi($request);
        break;

      case 'phbs':
        $content = $this->phbs($request);
        break;

      default:
        $content = '<h3>Mohon maaf belum dapat digunakan</h3>';
        break;
    }

    return $content;
  }

  function modal(Request $request)
  {
    $menu = $request->menu;

    $param = $request->param;
    $arr_param = json_decode(base64_decode($param));
    $judul = $arr_param->judul;
    $kelamin = $arr_param->kelamin;
    $kategori = $arr_param->kategori;

    $kecamatan = $request->kecamatan;
    $kelurahan = $request->kelurahan;
    $kecamatan_kode = $request->kecamatan_kode;
    $kelurahan_kode = $request->kelurahan_kode;

    switch ($menu) {
      case 'cekin_warga':
        $usia = $arr_param->usia ?? 0;
        $data = [
          'judul' => $judul,
          'kelamin' => $kelamin,
          'kategori' => $kategori,
          'jenis' => $menu,
          'kecamatan' => $kecamatan,
          'kelurahan' => $kelurahan,
          'usia' => $usia,
        ];
        $content = view('bunda.dashboard_asw.detail.detail_cekin', $data)->render();
        break;
      case 'sibunda':
        $usia = $arr_param->usia ?? 0;
        $data = [
          'judul' => $judul,
          'kelamin' => $kelamin,
          'kategori' => $kategori,
          'jenis' => $menu,
          'kecamatan' => $kecamatan,
          'kelurahan' => $kelurahan,
          'usia' => $usia,
        ];
        $content = view('bunda.dashboard_asw.detail.detail_sibunda', $data)->render();
        break;
      case 'dinsos':
        $data = [
          'judul' => $judul,
          'kelamin' => $kelamin,
          'kategori' => $kategori,
          'jenis' => $menu,
          'kecamatan' => $kecamatan,
          'kelurahan' => $kelurahan,
        ];
        $content = view('bunda.dashboard_asw.detail.detail_dinsos', $data)->render();
        break;
      case 'rutilahu':
        $tahun = $arr_param->tahun;
        $sumber_dana = $arr_param->sumber_dana;
        $instansi = $arr_param->instansi;
        $data = [
          'judul' => $judul,
          'kelamin' => $kelamin,
          'kategori' => $kategori,
          'tahun' => $tahun,
          'sumber_dana' => $sumber_dana,
          'instansi' => $instansi,
          'jenis' => $menu,
          'kecamatan' => $kecamatan,
          'kelurahan' => $kelurahan,
        ];
        $content = view('bunda.dashboard_asw.detail.detail_rutilahu', $data)->render();
        break;
      case 'dp3a':
        $tahun = $request->tahun;
        $data = [
          'judul' => $judul,
          'kelamin' => $kelamin,
          'kategori' => $kategori,
          'jenis' => $menu,
          'kecamatan' => $kecamatan,
          'kelurahan' => $kelurahan,
          'tahun' => $tahun,
        ];
        $content = view('bunda.dashboard_asw.detail.detail_dp3a', $data)->render();
        break;
      case 'posyandu':
        $tahun = $request->tahun;
        $data = [
          'judul' => $judul,
          'kelamin' => $kelamin,
          'kategori' => $kategori,
          'jenis' => $menu,
          'kecamatan' => $kecamatan,
          'kelurahan' => $kelurahan,
          'tahun' => $tahun,
        ];
        $content = view('bunda.dashboard_asw.detail.detail_posyandu_imunisasi', $data)->render();
        break;
      case 'posyandu_imunisasi':
        $tahun = $request->tahun;
        $data = [
          'judul' => $judul,
          'kelamin' => $kelamin,
          'kategori' => $kategori,
          'jenis' => $menu,
          'kecamatan' => $kecamatan,
          'kelurahan' => $kelurahan,
          'tahun' => $tahun,
        ];
        $content = view('bunda.dashboard_asw.detail.detail_posyandu_imunisasi', $data)->render();
        break;
      case 'posyandu_timbang':
        $tahun = $request->tahun;
        $data = [
          'judul' => $judul,
          'kelamin' => $kelamin,
          'kategori' => $kategori,
          'jenis' => $menu,
          'kecamatan' => $kecamatan,
          'kelurahan' => $kelurahan,
          'tahun' => $tahun,
        ];
        $content = view('bunda.dashboard_asw.detail.detail_posyandu_timbang', $data)->render();
        break;
      case 'dispusip':
        $tahun = $request->tahun;
        $data = [
          'judul' => $judul,
          'kelamin' => $kelamin,
          'kategori' => $kategori,
          'jenis' => $menu,
          'kecamatan' => $kecamatan,
          'kelurahan' => $kelurahan,
          'tahun' => $tahun,
        ];
        $content = view('bunda.dashboard_asw.detail.detail_dispusip', $data)->render();
        break;

      case 'soth':
        $usia = $arr_param->usia ?? 0;
        $data = [
          'judul' => $judul,
          'kelamin' => $kelamin,
          'kategori' => $kategori,
          'jenis' => $menu,
          'kecamatan' => $kecamatan,
          'kelurahan' => $kelurahan,
          'usia' => $usia,
        ];
        $content = view('bunda.dashboard_asw.detail.detail_soth', $data)->render();
        break;

      case 'tanpa_intervensi':
        $usia = $arr_param->usia ?? 0;
        $data = [
          'judul' => $judul,
          'kelamin' => $kelamin,
          'kategori' => $kategori,
          'jenis' => $menu,
          'kecamatan' => $kecamatan,
          'kelurahan' => $kelurahan,
          'usia' => $usia,
        ];
        $content = view('bunda.dashboard_asw.detail.detail_tanpa_intervensi', $data)->render();
        break;

      case 'phbs':
        $usia = $arr_param->usia ?? 0;
        $data = [
          'judul' => $judul,
          'kelamin' => $kelamin,
          'kategori' => $kategori,
          'jenis' => $menu,
          'kecamatan' => $kecamatan,
          'kelurahan' => $kelurahan,
          'usia' => $usia,
        ];
        $content = view('bunda.dashboard_asw.detail.detail_phbs', $data)->render();
        break;

      default:
        $content = '<h3>Mohon maaf belum dapat digunakan</h3>';
        break;
    }

    return $content;
  }

  function detail_data(Request $request)
  {
    $menu = $request->menu;

    switch ($menu) {
      case 'cekin_warga':
        $content = $this->detail_cekin_warga($request);
        break;
      case 'sibunda':
        $content = $this->detail_sibunda($request);
        break;
      case 'dinsos':
        $content = $this->detail_dinsos($request);
        break;
      case 'rutilahu':
        $content = $this->detail_rutilahu($request);
        break;
      case 'dp3a':
        $content = $this->detail_dp3a($request);
        break;
      case 'posyandu':
        $content = $this->detail_posyandu($request);
        break;
      case 'posyandu_timbang':
        $content = $this->detail_posyandu($request);
        break;
      case 'dispusip':
        $content = $this->detail_dispusip($request);
        break;
      case 'soth':
        $content = $this->detail_soth($request);
        break;

      case 'tanpa_intervensi':
        $content = $this->detail_tanpa_intervensi($request);
        break;

      case 'phbs':
        $content = $this->detail_phbs($request);
        break;

      default:
        $content = '<h3>Mohon maaf belum dapat digunakan</h3>';
        break;
    }

    return $content;
  }

  function cekin_warga_lama(Request $request)
  {
    $kecamatan = $request->kecamatan ?? '';
    $kecamatan_kode = $request->kecamatan_kode ?? '';
    $kelurahan = $request->kelurahan ?? '';
    $kelurahan_kode = $request->kelurahan_kode ?? '';
    $jenis = $request->jenis ?? '';
    $judul = 'Data Penduduk Usia 5-6 tahun (Cekin Warga)';
    if ($kelurahan != 'All') {
      $judul .= ' Kecamatan ' . $kecamatan;
      $judul .= ', Kelurahan ' . $kelurahan;
    } else if ($kecamatan != 'All') {
      $judul .= ' Kecamatan ' . $kecamatan;
    } else {
      $judul .= ' Kota Surabaya ';
    }

    $tingkat = Auth::getUser()->tingkat;

    $data_penduduk = DB::connection('db_cutt_off')->table('public.penduduk_inject as a')
      ->leftJoin('public.survey_bunda as b', 'b.nik', 'a.nik')
      ->when($tingkat, function ($w) use ($tingkat, $kecamatan, $kelurahan) {
        if ($tingkat == 'kecamatan') {
          $where = $w
            ->when($kecamatan != 'All', function ($q) use ($kecamatan) {
              $q->where('kecamatan', $kecamatan);
            });
        } else if ($tingkat == 'kelurahan') {
          $where = $w
            ->when($kelurahan != 'All', function ($q) use ($kelurahan) {
              $q->where('kelurahan', $kelurahan);
            });
        } else {
          $where = $w
            ->when($kecamatan != 'All', function ($q) use ($kecamatan) {
              $q->where('kecamatan', $kecamatan);
            })
            ->when($kelurahan != 'All', function ($q) use ($kelurahan) {
              $q->where('kelurahan', $kelurahan);
            });
        }
        return $where;
      })
      ->selectRaw("
        COUNT(*) AS jumlah_total,

        COUNT(*) FILTER (WHERE a.jenis_kelamin = 'LAKI-LAKI') AS jumlah_total_laki,
        COUNT(*) FILTER (WHERE a.jenis_kelamin = 'PEREMPUAN') AS jumlah_total_perempuan,

        COUNT(*) FILTER (
          WHERE
            CASE
              WHEN COALESCE(b.nik,'') <> '' THEN
                  (
                    b.status_sekolah = 'Sudah'
                    OR (COALESCE(a.nama_sekolah,'')<>'' AND a.is_aktif_pendidikan IS TRUE)
                  )
              ELSE
                  (
                    COALESCE(a.nama_sekolah,'') <> ''
                    AND a.is_aktif_pendidikan IS TRUE
                  )
            END
        ) AS jumlah_sudah_sekolah,

        COUNT(*) FILTER (
          WHERE (
            CASE
              WHEN COALESCE(b.nik,'') <> '' THEN
                  (
                    b.status_sekolah = 'Sudah'
                    OR (COALESCE(a.nama_sekolah,'')<>'' AND a.is_aktif_pendidikan IS TRUE)
                  )
              ELSE
                  (
                    COALESCE(a.nama_sekolah,'') <> ''
                    AND a.is_aktif_pendidikan IS TRUE
                  )
            END
            AND a.jenis_kelamin = 'LAKI-LAKI')
        ) AS jumlah_sudah_sekolah_laki,

        COUNT(*) FILTER (
          WHERE (
            CASE
              WHEN COALESCE(b.nik,'') <> '' THEN
                  (
                    b.status_sekolah = 'Sudah'
                    OR (COALESCE(a.nama_sekolah,'')<>'' AND a.is_aktif_pendidikan IS TRUE)
                  )
              ELSE
                  (
                    COALESCE(a.nama_sekolah,'') <> ''
                    AND a.is_aktif_pendidikan IS TRUE
                  )
            END
            AND a.jenis_kelamin = 'PEREMPUAN')
        ) AS jumlah_sudah_sekolah_perempuan,

        COUNT(*) FILTER (
          WHERE
            CASE
              WHEN COALESCE(b.nik,'') <> '' THEN
                  (
                    COALESCE(b.status_sekolah,'') <> 'Sudah'
                    AND (COALESCE(a.nama_sekolah,'') = ''
                    OR a.is_aktif_pendidikan IS NOT TRUE)
                  )
              ELSE
                  (
                    COALESCE(a.nama_sekolah,'') = ''
                    OR a.is_aktif_pendidikan IS NOT TRUE
                  )
            END
        ) AS jumlah_belum_sekolah,

        COUNT(*) FILTER (
          WHERE (
            CASE
              WHEN COALESCE(b.nik,'') <> '' THEN
                  (
                    COALESCE(b.status_sekolah,'') <> 'Sudah'
                    AND (COALESCE(a.nama_sekolah,'') = ''
                    OR a.is_aktif_pendidikan IS NOT TRUE)
                  )
              ELSE
                  (
                    COALESCE(a.nama_sekolah,'') = ''
                    OR a.is_aktif_pendidikan IS NOT TRUE
                  )
            END
            )
            AND a.jenis_kelamin = 'LAKI-LAKI'
        ) AS jumlah_belum_sekolah_laki,

        COUNT(*) FILTER (
          WHERE (
            CASE
              WHEN COALESCE(b.nik,'') <> '' THEN
                  (
                    COALESCE(b.status_sekolah,'') <> 'Sudah'
                    AND (COALESCE(a.nama_sekolah,'') = ''
                    OR a.is_aktif_pendidikan IS NOT TRUE)
                  )
              ELSE
                  (
                    COALESCE(a.nama_sekolah,'') = ''
                    OR a.is_aktif_pendidikan IS NOT TRUE
                  )
            END
            )
            AND a.jenis_kelamin = 'PEREMPUAN'
        ) AS jumlah_belum_sekolah_perempuan
      ")
      ->whereRaw("(EXTRACT(YEAR FROM AGE(now(),tgl_lahir)) BETWEEN 0 and 6)")
      ->first();

    $data = [
      'judul' => $judul,
      'data' => $data_penduduk,
      'jenis' => $jenis,
    ];
    $content = view('bunda.dashboard_asw.cekin_warga', $data)->render();
    return $content;
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

  function cekin_warga(Request $request)
  {
    $anomali = $this->anomali_nama($request);
    $kecamatan = $request->kecamatan ?? '';
    $kecamatan_kode = $request->kecamatan_kode ?? '';
    $kelurahan = $request->kelurahan ?? '';
    $kelurahan_kode = $request->kelurahan_kode ?? '';
    $jenis = $request->jenis ?? '';

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

    $judul = 'Rekapitulasi Data Penduduk Usia 0-6 Tahun Berdasarkan "Status Sekolah"';
    if ($kelurahan != 'All') {
      $judul .= ' Kecamatan ' . $kecamatan;
      $judul .= ', Kelurahan ' . $kelurahan;
    } else if ($kecamatan != 'All') {
      $judul .= ' Kecamatan ' . $kecamatan;
    } else {
      $judul .= ' Kota Surabaya ';
    }

    $tingkat = Auth::getUser()->tingkat;

    $data_penduduk = DB::connection('db_cutt_off')->table('public.penduduk_inject as a')
      ->leftJoin('public.survey_bunda as b', 'b.nik', 'a.nik')
      ->when(!empty($kecamatan) && $kecamatan != 'All', function ($q) use ($kecamatan, $anomali) {
        $q->whereIn(
          DB::raw("COALESCE(NULLIF(a.kecamatan_dom,''),a.kecamatan)"),
          $anomali['arr_kecamatan']
        );
      })
      ->when(!empty($kelurahan) && $kelurahan != 'All', function ($q) use ($kelurahan, $anomali) {
        $q->whereIn(
          DB::raw("COALESCE(NULLIF(a.kelurahan_dom,''),a.kelurahan)"),
          $anomali['arr_kelurahan']
        );
      })
      ->selectRaw("
        EXTRACT(YEAR FROM AGE(now(),tgl_lahir)) as usia,
        COUNT(*) FILTER (
          WHERE CASE WHEN COALESCE(b.nik,'')<>''
          THEN COALESCE(b.status_sekolah,'') IN ('Sudah','sudah_paud','sudah_sd') OR (COALESCE(a.nama_sekolah,'')<>'' AND a.is_aktif_pendidikan is true)
          ELSE (COALESCE(a.nama_sekolah,'')<>'' AND a.is_aktif_pendidikan is true)
          END AND (COALESCE(b.status_sekolah,'') NOT IN ('Akan sekolah','akan_daftar'))
        ) as jumlah_bersekolah,
        COUNT(*) FILTER (
          WHERE CASE WHEN COALESCE(b.nik,'')<>''
          THEN COALESCE(b.status_sekolah,'') NOT IN ('Sudah','sudah_paud','sudah_sd','Akan sekolah','akan_daftar') AND (COALESCE(a.nama_sekolah,'')='' OR a.is_aktif_pendidikan is not true)
          ELSE (COALESCE(a.nama_sekolah,'')='' OR a.is_aktif_pendidikan is not true)
          END
        ) as jumlah_belum_bersekolah,
        COUNT(*) FILTER (
          WHERE (COALESCE(b.status_sekolah,'') IN ('Akan sekolah','akan_daftar'))
        ) as jumlah_akan_bersekolah,
        COUNT(*) as jumlah_total
      ")
      ->whereRaw("((EXTRACT(YEAR FROM AGE(now(),tgl_lahir)) BETWEEN 0 AND 6) AND a.is_aktif_kependudukan IS TRUE AND a.status_keberadaan='ADA')")
      ->whereIn(DB::raw("COALESCE(NULLIF(a.kecamatan_dom,''), a.kecamatan)"), $arr_kec)
      ->whereIn(DB::raw("COALESCE(NULLIF(a.kelurahan_dom,''), a.kelurahan)"), $arr_kel)
      ->groupByRaw("EXTRACT(YEAR FROM AGE(now(),tgl_lahir))")
      ->orderByRaw("usia")
      ->get();

    $data = [
      'judul' => $judul,
      'data' => $data_penduduk,
      'jenis' => $jenis,
    ];
    $content = view('bunda.dashboard_asw.cekin_warga', $data)->render();
    return $content;
  }

  function detail_cekin_warga_lama(Request $request)
  {
    // return $request->all();
    $kategori = $request->kategori; //"total"
    $kelamin = $request->kelamin; //null
    $menu = $request->menu; //"cekin_warga"
    $kecamatan = $request->kecamatan;
    $kelurahan = $request->kelurahan;

    $tingkat = Auth::getUser()->tingkat;

    $data_penduduk = DB::connection('db_cutt_off')->table('public.penduduk_inject as a')
      ->leftJoin('public.survey_bunda as b', 'b.nik', 'a.nik')
      ->when($kecamatan != 'All', function ($q) use ($kecamatan, $anomali) {
        $q->whereIn('a.kecamatan_dom', $anomali['arr_kecamatan']);
      })
      ->when($kelurahan != 'All', function ($q) use ($kelurahan, $anomali) {
        $q->whereIn('a.kelurahan_dom', $anomali['arr_kelurahan']);
      })
      ->when($kategori != 'total', function ($q) use ($kategori) {
        if ($kategori == 'bersekolah') {
          return $q->whereRaw("
            CASE
              WHEN COALESCE(b.nik,'') <> '' THEN
                  (
                    b.status_sekolah = 'Sudah'
                    OR (COALESCE(a.nama_sekolah,'')<>'' AND a.is_aktif_pendidikan IS TRUE)
                  )
              ELSE
                  (
                    COALESCE(a.nama_sekolah,'') <> ''
                    AND a.is_aktif_pendidikan IS TRUE
                  )
            END
          ");
        } else {
          return $q->whereRaw("
            CASE
              WHEN COALESCE(b.nik,'') <> '' THEN
                  (
                    COALESCE(b.status_sekolah,'') <> 'Sudah'
                    AND (COALESCE(a.nama_sekolah,'') = ''
                    OR a.is_aktif_pendidikan IS NOT TRUE)
                  )
              ELSE
                  (
                    COALESCE(a.nama_sekolah,'') = ''
                    OR a.is_aktif_pendidikan IS NOT TRUE
                  )
            END
          ");
        }
      })
      ->when($kelamin != '', function ($q) use ($kelamin) {
        if ($kelamin == 'L') {
          return $q->where('a.jenis_kelamin', 'LAKI-LAKI');
        } else {
          return $q->where('a.jenis_kelamin', 'PEREMPUAN');
        }
      })
      ->selectRaw("
        a.nik,
        a.nama_lengkap,
        a.jenis_kelamin,
        a.alamat,
        a.kecamatan,
        a.kelurahan,
        a.rt,
        a.rw,
        a.nama_sekolah,
        COALESCE(b.nik,'') as nik_survey,
        COALESCE(b.status_sekolah,'') as status_sekolah_survey,
        COALESCE(a.nama_sekolah,'') as jenjang_sekolah,
        COALESCE(a.nama_sekolah,'') as nama_sekolah,
        COALESCE(a.npsn_sekolah,'') as npsn_sekolah,
        a.is_aktif_pendidikan
      ")
      ->whereRaw("(EXTRACT(YEAR FROM AGE(now(),a.tgl_lahir)) BETWEEN 0 and 6)");

    $nik = $data_penduduk->pluck('nik')->toArray();
    $nik_chunks = array_chunk($nik, 1000);

    $data_profil = collect();

    foreach ($nik_chunks as $chunk) {
      $result = DB::connection('pgsql_paud')->table('public.siswa as s')
        ->join('public.sekolah as se', 'se.npsn', '=', 's.npsn')
        ->selectRaw("s.nik,s.status_siswa,se.jenjang,se.nama as nama_sekolah,se.npsn")
        ->where('s.status_siswa', 'Aktif')
        ->whereRaw("s.alumni is not true")
        ->whereIn('s.nik', $chunk)
        ->get();

      $data_profil = $data_profil->merge($result);
    }

    $data_profil = $data_profil->keyBy('nik');

    return DataTables::queryBuilder($data_penduduk)
      ->addIndexColumn()
      ->addColumn('status_bersekolah', function ($i) {
        $html = '';
        if ($i->nik_survey != '') {
          if ($i->status_sekolah_survey == 'Sudah' or ($i->jenjang_sekolah <> '' and $i->is_aktif_pendidikan == true)) {
            $html = 'Bersekolah';
          } else if ($i->status_sekolah_survey <> 'Sudah' and ($i->jenjang_sekolah == '' or $i->is_aktif_pendidikan != true)) {
            $html = 'Tidak / Belum Bersekolah';
          }
        } else {
          if ($i->jenjang_sekolah <> '' and $i->is_aktif_pendidikan == true) {
            $html = 'Bersekolah';
          } else if ($i->jenjang_sekolah == '' or $i->is_aktif_pendidikan != true) {
            $html = 'Tidak / Belum Bersekolah';
          }
        }
        return $html;
      })
      ->addColumn('status_keaktifan', function ($i) {
        return $i->is_aktif_pendidikan == true ? 'Aktif' : 'Non Aktif';
      })
      ->addColumn('status_profil', function ($i) use ($data_profil) {
        return (isset($data_profil[$i->nik])) ? 'Ada' : 'Tidak ada';
      })
      ->addColumn('sekolah_profil', function ($i) use ($data_profil) {
        return $data_profil[$i->nik]->nama_sekolah ?? null;
      })
      ->addColumn('npsn_profil', function ($i) use ($data_profil) {
        return $data_profil[$i->nik]->npsn ?? null;
      })
      ->addColumn('status_keaktifan_profil', function ($i) use ($data_profil) {
        return $data_profil[$i->nik]->status_siswa ?? null;
      })
      ->addColumn('jenjang_profil', function ($i) use ($data_profil) {
        return $data_profil[$i->nik]->jenjang ?? null;
      })
      ->rawColumns(['status_sekolah'])
      ->make(true);
  }

  function detail_cekin_warga(Request $request)
  {
    $anomali = $this->anomali_nama($request);
    $kategori = $request->kategori; //"total"
    $kelamin = $request->kelamin; //null
    $menu = $request->menu; //"cekin_warga"
    $kecamatan = $request->kecamatan;
    $kelurahan = $request->kelurahan;
    $usia = $request->usia;

    // 1. Ambil data master kecamatan & kelurahan
    $dt_kecamatan = DB::table('kecamatan as kec')
      ->join('kelurahan as kel', 'kel.kecamatan_kode', '=', 'kec.kecamatan_kode')
      ->selectRaw("
              kec.kecamatan_dispenduk,
              kec.dispenduk_baru as kec_baru,
              kel.kelurahan_dispenduk,
              kel.dispenduk_baru as kel_baru
          ")
      ->when($kecamatan != 'All', function ($w) use ($kecamatan) {
        return $w->where('kec.kecamatan_dispenduk', $kecamatan);
      })
      ->when($kelurahan != 'All', function ($w) use ($kelurahan) {
        return $w->where('kel.kelurahan_dispenduk', $kelurahan);
      })
      ->get();

    // Kumpulkan seluruh nama kecamatan & kelurahan (lama & baru) untuk filter whereIn tunggal
    $arr_kec = $dt_kecamatan->pluck('kecamatan_dispenduk')
      ->merge($dt_kecamatan->pluck('kec_baru'))
      ->filter()->unique()->values()->toArray();

    $arr_kel = $dt_kecamatan->pluck('kelurahan_dispenduk')
      ->merge($dt_kecamatan->pluck('kel_baru'))
      ->filter()->unique()->values()->toArray();

    $tingkat = Auth::getUser()->tingkat;

    $data_penduduk = DB::connection('db_cutt_off')->table('public.penduduk_inject as a')
      ->leftJoin('public.survey_bunda as b', 'b.nik', 'a.nik')
      ->when(!empty($kecamatan) && $kecamatan != 'All', function ($q) use ($kecamatan, $anomali) {
        $q->whereIn(
          DB::raw("COALESCE(NULLIF(a.kecamatan_dom,''),a.kecamatan)"),
          $anomali['arr_kecamatan']
        );
      })
      ->when(!empty($kelurahan) && $kelurahan != 'All', function ($q) use ($kelurahan, $anomali) {
        $q->whereIn(
          DB::raw("COALESCE(NULLIF(a.kelurahan_dom,''),a.kelurahan)"),
          $anomali['arr_kelurahan']
        );
      })
      ->when($kategori != 'total', function ($q) use ($kategori) {
        if ($kategori == 'bersekolah') {
          return $q->whereRaw("
            CASE
              WHEN COALESCE(b.nik,'') <> '' THEN
                  (
                    b.status_sekolah IN ('Sudah','sudah_paud','sudah_sd')
                    OR (COALESCE(a.nama_sekolah,'')<>'' AND a.is_aktif_pendidikan IS TRUE)
                  )
              ELSE
                  (
                    COALESCE(a.nama_sekolah,'') <> ''
                    AND a.is_aktif_pendidikan IS TRUE
                  )
            END AND (COALESCE(b.status_sekolah,'') NOT IN ('Akan sekolah','akan_daftar'))
          ");
        } else if ($kategori == 'akan_bersekolah') {
          return $q->whereRaw("
            (
              b.status_sekolah IN ('Akan sekolah','akan_daftar')
            )
          ");
        } else {
          return $q->whereRaw("
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
            END
          ");
        }
      })
      ->when($kelamin != '', function ($q) use ($kelamin) {
        if ($kelamin == 'L') {
          return $q->where('a.jenis_kelamin', 'LAKI-LAKI');
        } else {
          return $q->where('a.jenis_kelamin', 'PEREMPUAN');
        }
      })
      ->selectRaw("
        a.nik,a.nama_lengkap as nama,a.tempat_lahir,a.tgl_lahir,a.alamat,a.rt,a.rw,a.kelurahan,a.kecamatan,a.jenjang_sekolah,a.nama_sekolah,a.nama_sekolah,b.status,b.status_alamat,b.status_sekolah,b.status_verifikasi,b.detail_sekolah,b.detail_alamat,a.is_aktif_pendidikan,b.detail_tidak_sekolah,b.pilihan_detail_tidak_sekolah,
        COALESCE(NULLIF(a.alamat_dom,''),a.alamat) as alamat_dom,COALESCE(NULLIF(a.rt_dom,''),a.rt) as rt_dom,COALESCE(NULLIF(a.rw_dom,''),a.rw) as rw_dom,
        COALESCE(NULLIF(a.kelurahan_dom,''),a.kelurahan) as kelurahan_dom,COALESCE(NULLIF(a.kecamatan_dom,''),a.kecamatan) as kecamatan_dom,
        b.edukasi_ortu,b.tanggapan_ortu,b.tindak_lanjut,b.penanganan_abk,a.kelas,a.rombel
      ")
      ->when($usia !== null, function ($w) use ($usia) {
        if ($usia === 'semua') {
          $w->whereRaw("EXTRACT(YEAR FROM AGE(NOW(), a.tgl_lahir)) BETWEEN 0 AND 6");
        } elseif (is_numeric($usia)) {
          $w->whereRaw("EXTRACT(YEAR FROM AGE(NOW(), a.tgl_lahir)) = ?", [$usia]);
        }
      })
      ->whereRaw("(a.is_aktif_kependudukan IS TRUE AND a.status_keberadaan='ADA')")
      ->whereIn(DB::raw("COALESCE(NULLIF(a.kecamatan_dom,''), a.kecamatan)"), $arr_kec)
      ->whereIn(DB::raw("COALESCE(NULLIF(a.kelurahan_dom,''), a.kelurahan)"), $arr_kel);

    $datatable = DataTables::queryBuilder($data_penduduk);

    return $datatable
      ->addIndexColumn()
      ->addColumn('profil', function ($r) {
        $hasil = '';
        if ($r->is_aktif_pendidikan != true) {
        } else {
          if ($r->nama_sekolah ?? '' != '') {
            $hasil .= '<span class="alert-primary">' . $r->nama_sekolah . ' <b>(' . ($r->jenjang_sekolah ?? '') . ')</b></span>';
          }
        }

        return $hasil;
      })
      ->addColumn('kelas_rombel', function ($r) {
        $hasil = '';
        if ($r->is_aktif_pendidikan != true) {
        } else {
          if ($r->nama_sekolah ?? '' != '') {
            $hasil .= '<span class="alert-primary">' . $r->kelas . ' <b>(' . ($r->rombel ?? '') . ')</b></span>';
          }
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
      ->rawColumns(['status_domisili', 'status_sekolah_n', 'status_faktor', 'detail_alamat', 'detail_sekolah', 'pilihan_detail_tidak_sekolah', 'status_edukasi', 'status_tanggapan', 'status_rtl', 'data_nik', 'profil', 'hasil_survey', 'hasil_alamat', 'hasil_sekolah', 'status_survey', 'kelas_rombel'])
      ->make(true);
  }

  function sibunda_lama(Request $request)
  {
    $kecamatan = $request->kecamatan ?? '';
    $kecamatan_kode = $request->kecamatan_kode ?? '';
    $kelurahan = $request->kelurahan ?? '';
    $kelurahan_kode = $request->kelurahan_kode ?? '';
    $jenis = $request->jenis ?? '';
    $judul = 'Data Hasil Survey Penduduk Usia 5-6 tahun';
    if ($kelurahan != 'All') {
      $judul .= ' Kecamatan ' . $kecamatan;
      $judul .= ', Kelurahan ' . $kelurahan;
    } else if ($kecamatan != 'All') {
      $judul .= ' Kecamatan ' . $kecamatan;
    } else {
      $judul .= ' Kota Surabaya ';
    }

    $tingkat = Auth::getUser()->tingkat;

    $data_penduduk = DB::connection('db_cutt_off')->table('public.penduduk_inject as a')
      ->leftjoin('public.survey_bunda as b', 'a.nik', 'b.nik')
      ->when($tingkat, function ($w) use ($tingkat, $kecamatan, $kelurahan) {
        if ($tingkat == 'kecamatan') {
          $where = $w
            ->when($kecamatan != 'All', function ($q) use ($kecamatan) {
              $q->where('kecamatan', $kecamatan);
            });
        } else if ($tingkat == 'kelurahan') {
          $where = $w
            ->when($kelurahan != 'All', function ($q) use ($kelurahan) {
              $q->where('kelurahan', $kelurahan);
            });
        } else {
          $where = $w
            ->when($kecamatan != 'All', function ($q) use ($kecamatan) {
              $q->where('kecamatan', $kecamatan);
            })
            ->when($kelurahan != 'All', function ($q) use ($kelurahan) {
              $q->where('kelurahan', $kelurahan);
            });
        }
        return $where;
      })
      ->selectRaw("
        COUNT(*) FILTER(
        WHERE
          CASE
            WHEN COALESCE(b.nik,'') <> '' THEN
                (
                  COALESCE(b.status_sekolah,'') <> 'Sudah'
                  AND (COALESCE(a.nama_sekolah,'') = ''
                  OR a.is_aktif_pendidikan IS NOT TRUE)
                )
            ELSE
                (
                  COALESCE(a.nama_sekolah,'') = ''
                  OR a.is_aktif_pendidikan IS NOT TRUE
                )
          END
        ) as jumlah_total,
        COUNT(*) FILTER(
          WHERE
            CASE
              WHEN COALESCE(b.nik,'') <> '' THEN
                  (
                    COALESCE(b.status_sekolah,'') <> 'Sudah'
                    AND (COALESCE(a.nama_sekolah,'') = ''
                    OR a.is_aktif_pendidikan IS NOT TRUE)
                  )
              ELSE
                  (
                    COALESCE(a.nama_sekolah,'') = ''
                    OR a.is_aktif_pendidikan IS NOT TRUE
                  )
            END
          AND COALESCE(a.jenis_kelamin,'')='LAKI-LAKI'
        ) as jumlah_total_laki,
        COUNT(*) FILTER(
          WHERE
            CASE
              WHEN COALESCE(b.nik,'') <> '' THEN
                  (
                    COALESCE(b.status_sekolah,'') <> 'Sudah'
                    AND (COALESCE(a.nama_sekolah,'') = ''
                    OR a.is_aktif_pendidikan IS NOT TRUE)
                  )
              ELSE
                  (
                    COALESCE(a.nama_sekolah,'') = ''
                    OR a.is_aktif_pendidikan IS NOT TRUE
                  )
            END
          AND COALESCE(a.jenis_kelamin,'')='PEREMPUAN'
        ) as jumlah_total_perempuan,

        COUNT(*) FILTER(
          WHERE
            CASE
              WHEN COALESCE(b.nik,'') <> '' THEN
                  (
                    COALESCE(b.status_sekolah,'') <> 'Sudah'
                    AND (COALESCE(a.nama_sekolah,'') = ''
                    OR a.is_aktif_pendidikan IS NOT TRUE)
                  )
              ELSE
                  (
                    COALESCE(a.nama_sekolah,'') = ''
                    OR a.is_aktif_pendidikan IS NOT TRUE
                  )
            END
          AND (COALESCE(b.nik,'') <> '' AND COALESCE(b.status_verifikasi,'')='Selesai')
        ) as jumlah_sudah_survey,
        COUNT(*) FILTER(
          WHERE
            (CASE
              WHEN COALESCE(b.nik,'') <> '' THEN
                  (
                    COALESCE(b.status_sekolah,'') <> 'Sudah'
                    AND (COALESCE(a.nama_sekolah,'') = ''
                    OR a.is_aktif_pendidikan IS NOT TRUE)
                  )
              ELSE
                  (
                    COALESCE(a.nama_sekolah,'') = ''
                    OR a.is_aktif_pendidikan IS NOT TRUE
                  )
              END
            AND (COALESCE(b.nik,'') <> '' AND COALESCE(b.status_verifikasi,'')='Selesai')) AND COALESCE(a.jenis_kelamin,'')='LAKI-LAKI'
        ) as jumlah_sudah_survey_laki,
        COUNT(*) FILTER(
          WHERE
            (CASE
              WHEN COALESCE(b.nik,'') <> '' THEN
                  (
                    COALESCE(b.status_sekolah,'') <> 'Sudah'
                    AND (COALESCE(a.nama_sekolah,'') = ''
                    OR a.is_aktif_pendidikan IS NOT TRUE)
                  )
              ELSE
                  (
                    COALESCE(a.nama_sekolah,'') = ''
                    OR a.is_aktif_pendidikan IS NOT TRUE
                  )
              END
          AND (COALESCE(b.nik,'') <> '' AND COALESCE(b.status_verifikasi,'')='Selesai')) AND COALESCE(a.jenis_kelamin,'')='PEREMPUAN'
        ) as jumlah_sudah_survey_perempuan,

        COUNT(*) FILTER(
          WHERE
            CASE
              WHEN COALESCE(b.nik,'') <> '' THEN
                  (
                    COALESCE(b.status_sekolah,'') <> 'Sudah'
                    AND (COALESCE(a.nama_sekolah,'') = ''
                    OR a.is_aktif_pendidikan IS NOT TRUE)
                  )
              ELSE
                  (
                    COALESCE(a.nama_sekolah,'') = ''
                    OR a.is_aktif_pendidikan IS NOT TRUE
                  )
              END
          AND (COALESCE(b.nik,'') = '' OR COALESCE(b.status_verifikasi,'')!='Selesai')
        ) as jumlah_belum_survey,
        COUNT(*) FILTER(
          WHERE
            (CASE
              WHEN COALESCE(b.nik,'') <> '' THEN
                  (
                    COALESCE(b.status_sekolah,'') <> 'Sudah'
                    AND (COALESCE(a.nama_sekolah,'') = ''
                    OR a.is_aktif_pendidikan IS NOT TRUE)
                  )
              ELSE
                  (
                    COALESCE(a.nama_sekolah,'') = ''
                    OR a.is_aktif_pendidikan IS NOT TRUE
                  )
              END
          AND (COALESCE(b.nik,'') = '' OR COALESCE(b.status_verifikasi,'')!='Selesai')) AND COALESCE(a.jenis_kelamin,'')='LAKI-LAKI'
        ) as jumlah_belum_survey_laki,
        COUNT(*) FILTER(
          WHERE
            (CASE
              WHEN COALESCE(b.nik,'') <> '' THEN
                  (
                    COALESCE(b.status_sekolah,'') <> 'Sudah'
                    AND (COALESCE(a.nama_sekolah,'') = ''
                    OR a.is_aktif_pendidikan IS NOT TRUE)
                  )
              ELSE
                  (
                    COALESCE(a.nama_sekolah,'') = ''
                    OR a.is_aktif_pendidikan IS NOT TRUE
                  )
              END
          AND (COALESCE(b.nik,'') = '' OR COALESCE(b.status_verifikasi,'')!='Selesai')) AND COALESCE(a.jenis_kelamin,'')='PEREMPUAN'
        ) as jumlah_belum_survey_perempuan
      ")
      ->whereRaw("(EXTRACT(YEAR FROM AGE(now(),a.tgl_lahir)) BETWEEN 0 and 6)")
      ->first();

    $data = [
      'judul' => $judul,
      'data' => $data_penduduk,
      'jenis' => $jenis,
    ];
    $content = view('bunda.dashboard_asw.sibunda', $data)->render();
    return $content;
  }

  function sibunda(Request $request)
  {
    $anomali = $this->anomali_nama($request);
    $kecamatan = $request->kecamatan ?? '';
    $kecamatan_kode = $request->kecamatan_kode ?? '';
    $kelurahan = $request->kelurahan ?? '';
    $kelurahan_kode = $request->kelurahan_kode ?? '';

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

    $jenis = $request->jenis ?? '';
    $judul = 'Data Penduduk Tidak Sekolah Usia 0-6 Tahun Berdasarkan Hasil Survei';
    if ($kelurahan != 'All') {
      $judul .= ' Kecamatan ' . $kecamatan;
      $judul .= ', Kelurahan ' . $kelurahan;
    } else if ($kecamatan != 'All') {
      $judul .= ' Kecamatan ' . $kecamatan;
    } else {
      $judul .= ' Kota Surabaya ';
    }

    $tingkat = Auth::getUser()->tingkat;

    $data_penduduk = DB::connection('db_cutt_off')->table('public.penduduk_inject as a')
      ->leftjoin('public.survey_bunda as b', 'a.nik', 'b.nik')
      ->when(!empty($kecamatan) && $kecamatan != 'All', function ($q) use ($kecamatan, $anomali) {
        $q->whereIn(
          DB::raw("COALESCE(NULLIF(a.kecamatan_dom,''),a.kecamatan)"),
          $anomali['arr_kecamatan']
        );
      })
      ->when(!empty($kelurahan) && $kelurahan != 'All', function ($q) use ($kelurahan, $anomali) {
        $q->whereIn(
          DB::raw("COALESCE(NULLIF(a.kelurahan_dom,''),a.kelurahan)"),
          $anomali['arr_kelurahan']
        );
      })
      ->selectRaw("
        EXTRACT(YEAR FROM AGE(NOW(),a.tgl_lahir)) as usia,
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
        ) as jumlah_survey,
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
          END
        ) as jumlah_total,

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
          END AND (COALESCE(b.nik,'') <> '' AND COALESCE(b.status_verifikasi,'')='Selesai') AND COALESCE(b.status_alamat,'') ilike 'sesuai'
        ) as jumlah_survey_alamat_sesuai,
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
          END AND (COALESCE(b.nik,'') <> '' AND COALESCE(b.status_verifikasi,'')='Selesai') AND COALESCE(b.status_alamat,'') ilike any(array['pindah_luar%'])
        ) as jumlah_survey_alamat_pindah_luar,
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
          END AND (COALESCE(b.nik,'') <> '' AND COALESCE(b.status_verifikasi,'')='Selesai') AND COALESCE(b.status_alamat,'') ilike any(array['pindah_dalam%'])
        ) as jumlah_survey_alamat_pindah_dalam,
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
          END AND (COALESCE(b.nik,'') <> '' AND COALESCE(b.status_verifikasi,'')='Selesai') AND COALESCE(b.status_alamat,'') ilike any(array['tidak_ditemukan%'])
        ) as jumlah_survey_alamat_tidak_ditemukan,
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
          END AND (COALESCE(b.nik,'') <> '' AND COALESCE(b.status_verifikasi,'')='Selesai') AND COALESCE(b.status_alamat,'') ilike any(array['data_ganda%'])
        ) as jumlah_survey_alamat_ganda,
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
          END AND (COALESCE(b.nik,'') <> '' AND COALESCE(b.status_verifikasi,'')='Selesai') AND COALESCE(b.status_alamat,'') ilike any(array['meninggal'])
        ) as jumlah_survey_alamat_meninggal,
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
          END AND (COALESCE(b.nik,'') <> '' AND COALESCE(b.status_verifikasi,'')='Selesai') AND COALESCE(b.status_alamat,'') not ilike all(array['sesuai','pindah_%','meninggal','data_ganda','tidak_ditemukan'])
        ) as jumlah_survey_alamat_dg_catatan,
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
          END AND COALESCE(b.status_alamat,'') not ilike all(array['pindah_luar','meninggal'])
        ) as jumlah_survey_alamat_tanpa_meninggal,
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
          END AND (COALESCE(b.nik,'') <> '' AND COALESCE(b.status_verifikasi,'')='Selesai') AND COALESCE(b.status_alamat,'') not ilike all(array['pindah%','sesuai%'])
        ) as jumlah_survey_alamat_lainnya,

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
          END AND ((COALESCE(b.nik,'') <> '' AND COALESCE(b.status_verifikasi,'')='Selesai') AND COALESCE(b.status_alamat,'') ilike 'sesuai' AND COALESCE(b.detail_tidak_sekolah,'') ILIKE '%Faktor 1%')
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
          END AND ((COALESCE(b.nik,'') <> '' AND COALESCE(b.status_verifikasi,'')='Selesai') AND COALESCE(b.status_alamat,'') ilike 'sesuai' AND COALESCE(b.detail_tidak_sekolah,'') ILIKE '%Faktor 2%')
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
          END AND ((COALESCE(b.nik,'') <> '' AND COALESCE(b.status_verifikasi,'')='Selesai') AND COALESCE(b.status_alamat,'') ilike 'sesuai' AND COALESCE(b.detail_tidak_sekolah,'') ILIKE '%Faktor 3%')
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
          END AND ((COALESCE(b.nik,'') <> '' AND COALESCE(b.status_verifikasi,'')='Selesai') AND COALESCE(b.status_alamat,'') ilike 'sesuai' AND COALESCE(b.detail_tidak_sekolah,'') ILIKE '%Faktor 4%')
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
          END AND ((COALESCE(b.nik,'') <> '' AND COALESCE(b.status_verifikasi,'')='Selesai') AND COALESCE(b.status_alamat,'') ilike 'sesuai' AND COALESCE(b.detail_tidak_sekolah,'') ILIKE '%Faktor 5%')
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
          END AND ((COALESCE(b.nik,'') <> '' AND COALESCE(b.status_verifikasi,'')='Selesai') AND COALESCE(b.status_alamat,'') ilike 'sesuai' AND COALESCE(b.detail_tidak_sekolah,'') NOT ILIKE ALL(ARRAY['%Faktor 5%','%Faktor 4%','%Faktor 3%','%Faktor 2%','%Faktor 1%']))
        ) as jumlah_sudah_survey_lain_faktor
      ")
      ->whereRaw("((EXTRACT(YEAR FROM AGE(now(),a.tgl_lahir)) BETWEEN 0 AND 6) AND a.is_aktif_kependudukan IS TRUE AND a.status_keberadaan='ADA')")
      ->whereIn(DB::raw("COALESCE(NULLIF(a.kecamatan_dom,''), a.kecamatan)"), $arr_kec)
      ->whereIn(DB::raw("COALESCE(NULLIF(a.kelurahan_dom,''), a.kelurahan)"), $arr_kel)
      ->groupByRaw("EXTRACT(YEAR FROM AGE(NOW(),a.tgl_lahir))")
      ->orderByRaw("usia")
      ->get();

    foreach ($data_penduduk as $r) {
      $r->persentase_survey = $r->jumlah_total == 0 ? 0 : ($r->jumlah_survey / $r->jumlah_total * 100);
      $r->persentase_belum_survey = $r->jumlah_total == 0 ? 0 : ($r->jumlah_belum_survey / $r->jumlah_total * 100);
    }

    $data = [
      'judul' => $judul,
      'data' => $data_penduduk,
      'jenis' => $jenis,
    ];
    $content = view('bunda.dashboard_asw.sibunda', $data)->render();
    return $content;
  }

  function get_grafik(Request $request)
  {
    // return $request->all();
    $anomali = $this->anomali_nama($request);
    $kecamatan = $request->kecamatan ?? '';
    $kecamatan_kode = $request->kecamatan_kode ?? '';
    $kelurahan = $request->kelurahan ?? '';
    $kelurahan_kode = $request->kelurahan_kode ?? '';

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

    $jenis = $request->jenis ?? '';
    $judul = 'Grafik Hasil Survey, Alamat Sesuai dan Belum Bersekolah';
    if ($kelurahan != 'All') {
      $judul .= ' Kecamatan ' . $kecamatan;
      $judul .= ', Kelurahan ' . $kelurahan;
    } else if ($kecamatan != 'All') {
      $judul .= ' Kecamatan ' . $kecamatan;
    } else {
      $judul .= ' Kota Surabaya ';
    }

    $tingkat = Auth::getUser()->tingkat;

    $data_penduduk = DB::connection('db_cutt_off')->table('public.penduduk_inject as a')
      ->leftjoin('public.survey_bunda as b', 'a.nik', 'b.nik')
      ->when(!empty($kecamatan) && $kecamatan != 'All', function ($q) use ($kecamatan, $anomali) {
        $q->whereIn(
          DB::raw("COALESCE(NULLIF(a.kecamatan_dom,''),a.kecamatan)"),
          $anomali['arr_kecamatan']
        );
      })
      ->when(!empty($kelurahan) && $kelurahan != 'All', function ($q) use ($kelurahan, $anomali) {
        $q->whereIn(
          DB::raw("COALESCE(NULLIF(a.kelurahan_dom,''),a.kelurahan)"),
          $anomali['arr_kelurahan']
        );
      })
      ->selectRaw("
        EXTRACT(YEAR FROM AGE(NOW(),a.tgl_lahir)) as usia,
        COUNT(*) as jumlah_total,
        COUNT(*) FILTER (
          WHERE CASE WHEN COALESCE(b.nik,'')<>''
          THEN COALESCE(b.status_sekolah,'') IN ('Sudah','sudah_paud','sudah_sd','Akan sekolah','akan_daftar') OR (COALESCE(a.nama_sekolah,'')<>'' AND a.is_aktif_pendidikan is true)
          ELSE (COALESCE(a.nama_sekolah,'')<>'' AND a.is_aktif_pendidikan is true)
          END
        ) as jumlah_bersekolah,
        COUNT(*) FILTER (
          WHERE CASE WHEN COALESCE(b.nik,'')<>''
          THEN COALESCE(b.status_sekolah,'') NOT IN ('Sudah','sudah_paud','sudah_sd','Akan sekolah','akan_daftar') AND (COALESCE(a.nama_sekolah,'')='' OR a.is_aktif_pendidikan is not true)
          ELSE (COALESCE(a.nama_sekolah,'')='' OR a.is_aktif_pendidikan is not true)
          END
        ) as jumlah_belum_bersekolah,
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
          END AND (COALESCE(b.nik,'') <> '' AND COALESCE(b.status_verifikasi,'')='Selesai')
        ) as jumlah_survey,
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
          END AND (COALESCE(b.nik,'') <> '' AND COALESCE(b.status_verifikasi,'')='Selesai') AND COALESCE(b.status_alamat,'') ilike 'sesuai'
        ) as jumlah_survey_alamat_sesuai,
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
          END AND (COALESCE(b.nik,'') <> '' AND COALESCE(b.status_verifikasi,'')='Selesai') AND COALESCE(b.status_alamat,'') ilike any(array['pindah%'])
        ) as jumlah_survey_alamat_pindah,
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
          END AND (COALESCE(b.nik,'') <> '' AND COALESCE(b.status_verifikasi,'')='Selesai') AND COALESCE(b.status_alamat,'') not ilike all(array['pindah%','sesuai%'])
        ) as jumlah_survey_alamat_lainnya,

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
          END AND ((COALESCE(b.nik,'') <> '' AND COALESCE(b.status_verifikasi,'')='Selesai') AND COALESCE(b.status_alamat,'') ilike 'sesuai%' AND COALESCE(b.detail_tidak_sekolah,'') ILIKE '%Faktor 1%')
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
          END AND ((COALESCE(b.nik,'') <> '' AND COALESCE(b.status_verifikasi,'')='Selesai') AND COALESCE(b.status_alamat,'') ilike 'sesuai%' AND COALESCE(b.detail_tidak_sekolah,'') ILIKE '%Faktor 2%')
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
          END AND ((COALESCE(b.nik,'') <> '' AND COALESCE(b.status_verifikasi,'')='Selesai') AND COALESCE(b.status_alamat,'') ilike 'sesuai%' AND COALESCE(b.detail_tidak_sekolah,'') ILIKE '%Faktor 3%')
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
          END AND ((COALESCE(b.nik,'') <> '' AND COALESCE(b.status_verifikasi,'')='Selesai') AND COALESCE(b.status_alamat,'') ilike 'sesuai%' AND COALESCE(b.detail_tidak_sekolah,'') ILIKE '%Faktor 4%')
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
          END AND ((COALESCE(b.nik,'') <> '' AND COALESCE(b.status_verifikasi,'')='Selesai') AND COALESCE(b.status_alamat,'') ilike 'sesuai%' AND COALESCE(b.detail_tidak_sekolah,'') ILIKE '%Faktor 5%')
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
          END AND ((COALESCE(b.nik,'') <> '' AND COALESCE(b.status_verifikasi,'')='Selesai') AND COALESCE(b.status_alamat,'') ilike 'sesuai%' AND COALESCE(b.detail_tidak_sekolah,'') NOT ILIKE ALL(ARRAY['%Faktor 5%','%Faktor 4%','%Faktor 3%','%Faktor 2%','%Faktor 1%']))
        ) as jumlah_sudah_survey_lain_faktor
      ")
      ->whereRaw("((EXTRACT(YEAR FROM AGE(now(),a.tgl_lahir)) BETWEEN 0 AND 6) AND a.is_aktif_kependudukan IS TRUE AND a.status_keberadaan='ADA')")
      ->whereIn(DB::raw("COALESCE(NULLIF(a.kecamatan_dom,''), a.kecamatan)"), $arr_kec)
      ->whereIn(DB::raw("COALESCE(NULLIF(a.kelurahan_dom,''), a.kelurahan)"), $arr_kel)
      ->groupByRaw("EXTRACT(YEAR FROM AGE(NOW(),a.tgl_lahir))")
      ->orderByRaw("usia")
      ->get();

    $semua_usia = (object)[
      'usia' => 'Semua',
      // 'jumlah_total' => $data_penduduk->sum('jumlah_total'),
      // 'jumlah_bersekolah' => $data_penduduk->sum('jumlah_bersekolah'),
      // 'jumlah_belum_bersekolah' => $data_penduduk->sum('jumlah_belum_bersekolah'),
      // 'jumlah_belum_survey' => $data_penduduk->sum('jumlah_belum_survey'),
      // 'jumlah_survey' => $data_penduduk->sum('jumlah_survey'),
      'jumlah_survey_alamat_sesuai' => $data_penduduk->sum('jumlah_survey_alamat_sesuai'),
      // 'jumlah_survey_alamat_pindah' => $data_penduduk->sum('jumlah_survey_alamat_pindah'),
      // 'jumlah_survey_alamat_lainnya' => $data_penduduk->sum('jumlah_survey_alamat_lainnya'),
      'jumlah_sudah_survey_faktor1' => $data_penduduk->sum('jumlah_sudah_survey_faktor1'),
      'jumlah_sudah_survey_faktor2' => $data_penduduk->sum('jumlah_sudah_survey_faktor2'),
      'jumlah_sudah_survey_faktor3' => $data_penduduk->sum('jumlah_sudah_survey_faktor3'),
      'jumlah_sudah_survey_faktor4' => $data_penduduk->sum('jumlah_sudah_survey_faktor4'),
      'jumlah_sudah_survey_faktor5' => $data_penduduk->sum('jumlah_sudah_survey_faktor5'),
      'jumlah_sudah_survey_lain_faktor' => $data_penduduk->sum('jumlah_sudah_survey_lain_faktor'),
    ];

    $data_penduduk = collect($data_penduduk)->prepend($semua_usia);

    $kategori = [
      // 'Total',
      // 'Bersekolah',
      // 'Belum Bersekolah',
      // 'Belum Bersekolah Belum Survey',
      // 'Belum Bersekolah Sudah Survey',
      'Sudah Survey Alamat Sesuai',
      // 'Sudah Survey Pindah Alamat',
      // 'Sudah Survey Status Alamat Lain',
      'Jumlah Sudah Survey (Faktor 1 : Ekonomi & Kerentanan)',
      'Jumlah Sudah Survey (Faktor 2 : Pengasuhan & Dukungan)',
      'Jumlah Sudah Survey (Faktor 3 : Kondisi Anak & Kesehatan)',
      'Jumlah Sudah Survey (Faktor 4 : Administrasi)',
      'Jumlah Sudah Survey (Faktor 5 : Lainnya)',
      'Jumlah Sudah Survey (Diluar 5 Faktor)'
    ];

    $series = [];
    $usia = collect($data_penduduk)->pluck('usia')->toArray();
    for ($i = 0; $i < count($usia); $i++) {
      $dt_angka = collect($data_penduduk)->where('usia', $usia[$i])->first();

      $dt_series = [
        'name' => (is_numeric($usia[$i])) ? 'Usia ' . $usia[$i] : $usia[$i],
        'data' => [
          // (int) $dt_angka->jumlah_total,
          // (int) $dt_angka->jumlah_bersekolah,
          // (int) $dt_angka->jumlah_belum_bersekolah,
          // (int) $dt_angka->jumlah_belum_survey,
          // (int) $dt_angka->jumlah_survey,
          (int) $dt_angka->jumlah_survey_alamat_sesuai,
          // (int) $dt_angka->jumlah_survey_alamat_pindah,
          // (int) $dt_angka->jumlah_survey_alamat_lainnya,
          (int) $dt_angka->jumlah_sudah_survey_faktor1,
          (int) $dt_angka->jumlah_sudah_survey_faktor2,
          (int) $dt_angka->jumlah_sudah_survey_faktor3,
          (int) $dt_angka->jumlah_sudah_survey_faktor4,
          (int) $dt_angka->jumlah_sudah_survey_faktor5,
          (int) $dt_angka->jumlah_sudah_survey_lain_faktor,
        ],
      ];
      array_push($series, $dt_series);
    }

    $datatabel = [
      'Team' => $kategori,
    ];
    foreach ($series as $se) {
      $datatabel = array_merge($datatabel, [$se['name'] => $se['data']]);
    }

    $data = [
      'judul' => $judul,
      'data' => $data_penduduk,
      'kategori' => $kategori,
      'jenis' => $jenis,
      'series' => $series,
      'datatabel' => $datatabel,
    ];
    return $data;
  }

  function detail_sibunda(Request $request)
  {
    // return $request->all();
    $anomali = $this->anomali_nama($request);
    $kategori = $request->kategori; //"total"
    $kelamin = $request->kelamin; //null
    $menu = $request->menu; //"cekin_warga"
    $kecamatan = $request->kecamatan;
    $kelurahan = $request->kelurahan;
    $usia = $request->usia;

    $tingkat = Auth::getUser()->tingkat;

    // 1. Ambil data master kecamatan & kelurahan
    $dt_kecamatan = DB::table('kecamatan as kec')
      ->join('kelurahan as kel', 'kel.kecamatan_kode', '=', 'kec.kecamatan_kode')
      ->selectRaw("
              kec.kecamatan_dispenduk,
              kec.dispenduk_baru as kec_baru,
              kel.kelurahan_dispenduk,
              kel.dispenduk_baru as kel_baru
          ")
      ->when($kecamatan != 'All', function ($w) use ($kecamatan) {
        return $w->where('kec.kecamatan_dispenduk', $kecamatan);
      })
      ->when($kelurahan != 'All', function ($w) use ($kelurahan) {
        return $w->where('kel.kelurahan_dispenduk', $kelurahan);
      })
      ->get();

    // Kumpulkan seluruh nama kecamatan & kelurahan (lama & baru) untuk filter whereIn tunggal
    $arr_kec = $dt_kecamatan->pluck('kecamatan_dispenduk')
      ->merge($dt_kecamatan->pluck('kec_baru'))
      ->filter()->unique()->values()->toArray();

    $arr_kel = $dt_kecamatan->pluck('kelurahan_dispenduk')
      ->merge($dt_kecamatan->pluck('kel_baru'))
      ->filter()->unique()->values()->toArray();

    $data_penduduk = DB::connection('db_cutt_off')
      ->table('public.penduduk_inject as a')
      ->leftjoin('public.survey_bunda as b', 'a.nik', 'b.nik')
      ->when(!empty($kecamatan) && $kecamatan != 'All', function ($q) use ($kecamatan, $anomali) {
        $q->whereIn(
          DB::raw("COALESCE(NULLIF(a.kecamatan_dom,''),a.kecamatan)"),
          $anomali['arr_kecamatan']
        );
      })
      ->when(!empty($kelurahan) && $kelurahan != 'All', function ($q) use ($kelurahan, $anomali) {
        $q->whereIn(
          DB::raw("COALESCE(NULLIF(a.kelurahan_dom,''),a.kelurahan)"),
          $anomali['arr_kelurahan']
        );
      })
      ->when($kategori, function ($q) use ($kategori) {
        if ($kategori == 'sudah') {
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
              END
              AND (COALESCE(b.nik,'') <> '' AND COALESCE(b.status_verifikasi,'')='Selesai')
          ");
        } elseif ($kategori == 'total') {
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
              END
          ");
        } else {
          switch ($kategori) {
            case 'jumlah_survey_alamat_sesuai':
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
                END AND (COALESCE(b.nik,'') <> '' AND COALESCE(b.status_verifikasi,'')='Selesai') AND COALESCE(b.status_alamat,'') ilike 'sesuai'
              ");
              break;
            case 'jumlah_survey_alamat_pindah_dalam':
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
                END AND (COALESCE(b.nik,'') <> '' AND COALESCE(b.status_verifikasi,'')='Selesai') AND COALESCE(b.status_alamat,'') ilike 'pindah_dalam'
              ");
              break;
            case 'jumlah_survey_alamat_pindah_luar':
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
                END AND (COALESCE(b.nik,'') <> '' AND COALESCE(b.status_verifikasi,'')='Selesai') AND COALESCE(b.status_alamat,'') ilike 'pindah_luar'
              ");
              break;
            case 'jumlah_survey_alamat_tidak_ditemukan':
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
                END AND (COALESCE(b.nik,'') <> '' AND COALESCE(b.status_verifikasi,'')='Selesai') AND COALESCE(b.status_alamat,'') ilike 'tidak_ditemukan'
              ");
              break;
            case 'jumlah_survey_alamat_ganda':
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
                END AND (COALESCE(b.nik,'') <> '' AND COALESCE(b.status_verifikasi,'')='Selesai') AND COALESCE(b.status_alamat,'') ilike 'data_ganda'
              ");
              break;
            case 'jumlah_survey_alamat_meninggal':
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
                END AND (COALESCE(b.nik,'') <> '' AND COALESCE(b.status_verifikasi,'')='Selesai') AND COALESCE(b.status_alamat,'') ilike 'meninggal'
              ");
              break;
            case 'jumlah_survey_alamat_dg_catatan':
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
                END AND (COALESCE(b.nik,'') <> '' AND COALESCE(b.status_verifikasi,'')='Selesai') AND COALESCE(b.status_alamat,'') not ilike all(array['sesuai','pindah_%','tidak_ditemukan','data_ganda','meninggal'])
              ");
              break;
            case 'jumlah_survey_alamat_tanpa_meninggal':
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
                END AND COALESCE(b.status_alamat,'') not ilike all(array['pindah_luar','meninggal'])
              ");
              break;

            case 'jumlah_sudah_survey_faktor1':
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
                END AND ((COALESCE(b.nik,'') <> '' AND COALESCE(b.status_verifikasi,'')='Selesai') AND COALESCE(b.status_alamat,'') ilike 'sesuai' AND COALESCE(b.detail_tidak_sekolah,'') ILIKE '%Faktor 1%')
              ");
              break;
            case 'jumlah_sudah_survey_faktor2':
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
                END AND ((COALESCE(b.nik,'') <> '' AND COALESCE(b.status_verifikasi,'')='Selesai') AND COALESCE(b.status_alamat,'') ilike 'sesuai' AND COALESCE(b.detail_tidak_sekolah,'') ILIKE '%Faktor 2%')
              ");
              break;
            case 'jumlah_sudah_survey_faktor3':
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
                END AND ((COALESCE(b.nik,'') <> '' AND COALESCE(b.status_verifikasi,'')='Selesai') AND COALESCE(b.status_alamat,'') ilike 'sesuai' AND COALESCE(b.detail_tidak_sekolah,'') ILIKE '%Faktor 3%')
              ");
              break;
            case 'jumlah_sudah_survey_faktor4':
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
                END AND ((COALESCE(b.nik,'') <> '' AND COALESCE(b.status_verifikasi,'')='Selesai') AND COALESCE(b.status_alamat,'') ilike 'sesuai' AND COALESCE(b.detail_tidak_sekolah,'') ILIKE '%Faktor 4%')
              ");
              break;
            case 'jumlah_sudah_survey_faktor5':
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
                END AND ((COALESCE(b.nik,'') <> '' AND COALESCE(b.status_verifikasi,'')='Selesai') AND COALESCE(b.status_alamat,'') ilike 'sesuai' AND COALESCE(b.detail_tidak_sekolah,'') ILIKE '%Faktor 5%')
              ");
              break;
            case 'jumlah_sudah_survey_lain_faktor':
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
                END AND ((COALESCE(b.nik,'') <> '' AND COALESCE(b.status_verifikasi,'')='Selesai') AND COALESCE(b.status_alamat,'') ilike 'sesuai' AND COALESCE(b.detail_tidak_sekolah,'') NOT ILIKE ALL(ARRAY['%Faktor 5%','%Faktor 4%','%Faktor 3%','%Faktor 2%','%Faktor 1%']))
              ");
              break;
            default:
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
                  END
                  AND (COALESCE(b.nik,'') = '' OR COALESCE(b.status_verifikasi,'')!='Selesai')
              ");
              break;
          }
        }
      })
      ->when($kelamin != '', function ($q) use ($kelamin) {
        if ($kelamin == 'L') {
          return $q->where('a.jenis_kelamin', 'LAKI-LAKI');
        } else {
          return $q->where('a.jenis_kelamin', 'PEREMPUAN');
        }
      })
      ->when($usia !== null, function ($w) use ($usia) {
        if ($usia === 'semua') {
          $w->whereRaw("EXTRACT(YEAR FROM AGE(NOW(), a.tgl_lahir)) BETWEEN 0 AND 6");
        } elseif (is_numeric($usia)) {
          $w->whereRaw("EXTRACT(YEAR FROM AGE(NOW(), a.tgl_lahir)) = ?", [$usia]);
        }
      })
      ->selectRaw("
        a.nik,a.nama_lengkap as nama,a.tempat_lahir,a.tgl_lahir,a.alamat,a.rt,a.rw,a.kelurahan,a.kecamatan,a.jenjang_sekolah,a.nama_sekolah,a.nama_sekolah,b.status,b.status_alamat,b.status_sekolah,b.status_verifikasi,b.detail_sekolah,b.detail_alamat,a.is_aktif_pendidikan,b.detail_tidak_sekolah,b.pilihan_detail_tidak_sekolah,
        COALESCE(NULLIF(a.alamat_dom,''),a.alamat) as alamat_dom,COALESCE(NULLIF(a.rt_dom,''),a.rt) as rt_dom,COALESCE(NULLIF(a.rw_dom,''),a.rw) as rw_dom,
        COALESCE(NULLIF(a.kelurahan_dom,''),a.kelurahan) as kelurahan_dom,COALESCE(NULLIF(a.kecamatan_dom,''),a.kecamatan) as kecamatan_dom,
        b.edukasi_ortu,b.tanggapan_ortu,b.tindak_lanjut,b.penanganan_abk
      ")
      ->whereRaw("(a.is_aktif_kependudukan IS TRUE AND a.status_keberadaan='ADA')")
      ->whereIn(DB::raw("COALESCE(NULLIF(a.kecamatan_dom,''), a.kecamatan)"), $arr_kec)
      ->whereIn(DB::raw("COALESCE(NULLIF(a.kelurahan_dom,''), a.kelurahan)"), $arr_kel);

    return DataTables::of($data_penduduk)
      ->addIndexColumn()

      ->addColumn('profil', function ($r) {
        $hasil = '';
        if ($r->is_aktif_pendidikan != true) {
        } else {
          if ($r->nama_sekolah ?? '' != '') {
            $hasil .= '<span class="alert-primary">' . $r->nama_sekolah . ' <b>(' . ($r->jenjang_sekolah ?? '') . ')</b></span>';
          }
        }

        return $hasil;
      })
      ->addColumn('kelas_rombel', function ($r) {
        $hasil = '';
        if ($r->is_aktif_pendidikan != true) {
        } else {
          if ($r->nama_sekolah ?? '' != '') {
            $hasil .= '<span class="alert-primary">' . $r->kelas . ' <b>(' . ($r->rombel ?? '') . ')</b></span>';
          }
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
      ->rawColumns(['status_domisili', 'status_sekolah_n', 'status_faktor', 'detail_alamat', 'detail_sekolah', 'pilihan_detail_tidak_sekolah', 'status_edukasi', 'status_tanggapan', 'status_rtl', 'data_nik', 'profil', 'hasil_survey', 'hasil_alamat', 'hasil_sekolah', 'status_survey', 'kelas_rombel'])
      ->make(true);
  }

  function dinsos(Request $request)
  {
    $anomali = $this->anomali_nama($request);
    $kecamatan = $request->kecamatan ?? '';
    $kecamatan_kode = $request->kecamatan_kode ?? '';
    $kelurahan = $request->kelurahan ?? '';
    $kelurahan_kode = $request->kelurahan_kode ?? '';
    $jenis = $request->jenis ?? '';
    $judul = 'Data Penduduk Usia 0-6 tahun yang menerima Bantuan dari Dinas Sosial di';
    if ($kelurahan != 'All') {
      $judul .= ' Kecamatan ' . $kecamatan;
      $judul .= ', Kelurahan ' . $kelurahan;
    } else if ($kecamatan != 'All') {
      $judul .= ' Kecamatan ' . $kecamatan;
    } else {
      $judul .= ' Kota Surabaya ';
    }

    $tingkat = Auth::getUser()->tingkat;

    $data_penduduk = DB::connection('db_cutt_off')->table('public.penduduk_inject as a')
      ->join('public.penduduk_intervensi as b', 'a.nik', 'b.nik')

      ->when(!empty($kecamatan) && $kecamatan != 'All', function ($q) use ($kecamatan, $anomali) {
        $q->whereIn('a.kecamatan_dom', $anomali['arr_kecamatan']);
      })
      ->when(!empty($kelurahan) && $kelurahan != 'All', function ($q) use ($kelurahan, $anomali) {
        $q->whereIn('a.kelurahan_dom', $anomali['arr_kelurahan']);
      })
      ->selectRaw("
      --COUNT(*) FILTER(WHERE COALESCE(b.nik,'')<>'') as jumlah_total,
      --COUNT(*) FILTER(WHERE (COALESCE(b.nik,'')<>'' AND COALESCE(a.jenis_kelamin,'')='LAKI-LAKI')) as jumlah_total_laki,
      --COUNT(*) FILTER(WHERE (COALESCE(b.nik,'')<>'' AND COALESCE(a.jenis_kelamin,'')<>'LAKI-LAKI')) as jumlah_total_perempuan,
      COUNT(*) FILTER(WHERE (COALESCE(b.nik,'')<>'' AND jenis_bantuan='PKH')) as jumlah_penerima_pkh,
      COUNT(*) FILTER(WHERE (COALESCE(b.nik,'')<>'' AND jenis_bantuan='PKH' AND COALESCE(a.jenis_kelamin,'')='LAKI-LAKI')) as jumlah_penerima_pkh_laki,
      COUNT(*) FILTER(WHERE (COALESCE(b.nik,'')<>'' AND jenis_bantuan='PKH' AND COALESCE(a.jenis_kelamin,'')<>'LAKI-LAKI')) as jumlah_penerima_pkh_perempuan,
      COUNT(*) FILTER(WHERE (COALESCE(b.nik,'')<>'' AND jenis_bantuan='ALAT BANTU DISABILITAS')) as jumlah_penerima_alat_bantu,
      COUNT(*) FILTER(WHERE (COALESCE(b.nik,'')<>'' AND jenis_bantuan='ALAT BANTU DISABILITAS' AND COALESCE(a.jenis_kelamin,'')='LAKI-LAKI')) as jumlah_penerima_alat_bantu_laki,
      COUNT(*) FILTER(WHERE (COALESCE(b.nik,'')<>'' AND jenis_bantuan='ALAT BANTU DISABILITAS' AND COALESCE(a.jenis_kelamin,'')<>'LAKI-LAKI')) as jumlah_penerima_alat_bantu_perempuan
      ")
      ->whereRaw("(EXTRACT(YEAR FROM AGE(now(),a.tgl_lahir)) BETWEEN 0 AND 6)")
      ->whereRaw("(a.is_aktif_kependudukan IS TRUE AND a.status_keberadaan='ADA')")
      ->first();

    $data_inklusi = DB::connection('db_cutt_off')->table('public.penduduk_inject as a')
      ->join('public.penduduk_dinsos_inklusi as b', 'a.nik', 'b.nik')
      ->when(!empty($kecamatan) && $kecamatan != 'All', function ($q) use ($kecamatan, $anomali) {
        $q->whereIn('a.kecamatan_dom', $anomali['arr_kecamatan']);
      })
      ->when(!empty($kelurahan) && $kelurahan != 'All', function ($q) use ($kelurahan, $anomali) {
        $q->whereIn('a.kelurahan_dom', $anomali['arr_kelurahan']);
      })
      ->selectRaw("
      COUNT(*) FILTER(WHERE (COALESCE(b.nik,'')<>'' AND COALESCE(jenis_disabilitas,'')<>'')) as jumlah_inklusi,
      COUNT(*) FILTER(WHERE (COALESCE(b.nik,'')<>'' AND COALESCE(jenis_disabilitas,'')<>'' AND COALESCE(a.jenis_kelamin,'')='LAKI-LAKI')) as jumlah_inklusi_laki,
      COUNT(*) FILTER(WHERE (COALESCE(b.nik,'')<>'' AND COALESCE(jenis_disabilitas,'')<>'' AND COALESCE(a.jenis_kelamin,'')<>'LAKI-LAKI')) as jumlah_inklusi_perempuan
      ")
      ->whereRaw("(EXTRACT(YEAR FROM AGE(now(),a.tgl_lahir)) BETWEEN 0 AND 6)")
      ->whereRaw("(a.is_aktif_kependudukan IS TRUE AND a.status_keberadaan='ADA')")
      ->first();

    $data = [
      'judul' => $judul,
      'data' => $data_penduduk,
      'data_inklusi' => $data_inklusi,
      'jenis' => $jenis,
    ];
    $content = view('bunda.dashboard_asw.dinsos', $data)->render();
    return $content;
  }

  function detail_dinsos(Request $request)
  {
    // return $request->all();
    $anomali = $this->anomali_nama($request);
    $kategori = $request->kategori; //"belum"
    $kecamatan = $request->kecamatan; //"BULAK"
    $kelamin = $request->kelamin; //null
    $kelurahan = $request->kelurahan; //"BULAK"
    $menu = $request->menu; //"dinsos"

    $tingkat = Auth::getUser()->tingkat;

    if ($kategori != 'disabilitas') {
      $data = DB::connection('db_cutt_off')->table('public.penduduk_intervensi as b')
        ->join('public.penduduk_inject as a', 'a.nik', 'b.nik')
        ->when(!empty($kecamatan) && $kecamatan != 'All', function ($q) use ($kecamatan, $anomali) {
          $q->whereIn('a.kecamatan_dom', $anomali['arr_kecamatan']);
        })
        ->when(!empty($kelurahan) && $kelurahan != 'All', function ($q) use ($kelurahan, $anomali) {
          $q->whereIn('a.kelurahan_dom', $anomali['arr_kelurahan']);
        })

        ->when($kategori, function ($q) use ($kategori) {
          if ($kategori == 'pkh') {
            $q->where('b.jenis_bantuan', 'PKH');
          } else if ($kategori == 'alat_bantu') {
            $q->where('b.jenis_bantuan', 'ALAT BANTU DISABILITAS');
          }
        })
        ->when($kelamin != '', function ($q) use ($kelamin) {
          if ($kelamin == 'L') {
            return $q->where('a.jenis_kelamin', 'LAKI-LAKI');
          } else {
            return $q->where('a.jenis_kelamin', 'PEREMPUAN');
          }
        })
        ->selectRaw("a.nama_lengkap,a.jenis_kelamin,a.nik,a.alamat,a.rt,a.rw,a.kelurahan,a.kecamatan,b.jenis_bantuan,b.ket_bantuan,b.tahun_anggaran,
        EXTRACT(YEAR FROM AGE(NOW(),a.tgl_lahir)) as usia,
        COALESCE(NULLIF(a.alamat_dom,''),a.alamat) as alamat_dom,
        COALESCE(NULLIF(a.rt_dom,''),a.rt) as rt_dom,
        COALESCE(NULLIF(a.rw_dom,''),a.rw) as rw_dom,
        COALESCE(NULLIF(a.kelurahan_dom,''),a.kelurahan) as kelurahan_dom,
        COALESCE(NULLIF(a.kecamatan_dom,''),a.kecamatan) as kecamatan_dom")
        ->whereRaw("(EXTRACT(YEAR FROM AGE(NOW(),a.tgl_lahir))) BETWEEN 0 AND 6")
        ->whereRaw("(a.is_aktif_kependudukan IS TRUE AND a.status_keberadaan='ADA')");
    } else {
      $data = DB::connection('db_cutt_off')->table('public.penduduk_dinsos_inklusi as b')
        ->join('public.penduduk_inject as a', 'a.nik', 'b.nik')
        ->when(!empty($kecamatan) && $kecamatan != 'All', function ($q) use ($kecamatan, $anomali) {
          $q->whereIn('a.kecamatan_dom', $anomali['arr_kecamatan']);
        })
        ->when(!empty($kelurahan) && $kelurahan != 'All', function ($q) use ($kelurahan, $anomali) {
          $q->whereIn('a.kelurahan_dom', $anomali['arr_kelurahan']);
        })
        ->when($kelamin != '', function ($q) use ($kelamin) {
          if ($kelamin == 'L') {
            return $q->where('a.jenis_kelamin', 'LAKI-LAKI');
          } else {
            return $q->where('a.jenis_kelamin', 'PEREMPUAN');
          }
        })
        ->selectRaw("a.nama_lengkap,a.jenis_kelamin,a.nik,a.alamat,a.rt,a.rw,a.kelurahan,a.kecamatan,b.jenis_disabilitas as jenis_bantuan,'' as ket_bantuan,'' as tahun_anggaran,
        EXTRACT(YEAR FROM AGE(NOW(),a.tgl_lahir)) as usia,
        COALESCE(NULLIF(a.alamat_dom,''),a.alamat) as alamat_dom,
        COALESCE(NULLIF(a.rt_dom,''),a.rt) as rt_dom,
        COALESCE(NULLIF(a.rw_dom,''),a.rw) as rw_dom,
        COALESCE(NULLIF(a.kelurahan_dom,''),a.kelurahan) as kelurahan_dom,
        COALESCE(NULLIF(a.kecamatan_dom,''),a.kecamatan) as kecamatan_dom")
        ->whereRaw("(EXTRACT(YEAR FROM AGE(NOW(),a.tgl_lahir))) BETWEEN 0 AND 6")
        ->whereRaw("(a.is_aktif_kependudukan IS TRUE AND a.status_keberadaan='ADA')");
    }
    return DataTables::queryBuilder($data)
      ->addIndexColumn()
      ->addColumn('nik_baru', function ($i) {
        return '<a href="' . route('bunda-dashboard_asw-detail_informasi', ['nik' => $i->nik]) . '" target="_blank">' . $i->nik . '</a>';
      })
      ->rawColumns(['nik_baru'])
      ->make(true);
  }

  function rutilahu(Request $request)
  {
    $anomali = $this->anomali_nama($request);
    $kecamatan = $request->kecamatan ?? '';
    $kecamatan_kode = $request->kecamatan_kode ?? '';
    $kelurahan = $request->kelurahan ?? '';
    $kelurahan_kode = $request->kelurahan_kode ?? '';
    $jenis = $request->jenis ?? '';
    $judul = 'Data Penduduk Usia 0-6 tahun yang menerima Bantuan Rutilahu di';
    if ($kelurahan != 'All') {
      $judul .= ' Kecamatan ' . $kecamatan;
      $judul .= ', Kelurahan ' . $kelurahan;
    } else if ($kecamatan != 'All') {
      $judul .= ' Kecamatan ' . $kecamatan;
    } else {
      $judul .= ' Kota Surabaya ';
    }

    $tingkat = Auth::getUser()->tingkat;

    $data_penduduk = DB::connection('db_cutt_off')->table('public.penduduk_inject as a')
      ->join('public.pengerjaan as b', 'a.nik', 'b.nik')
      ->when(!empty($kecamatan) && $kecamatan != 'All', function ($q) use ($kecamatan, $anomali) {
        $q->whereIn('a.kecamatan_dom', $anomali['arr_kecamatan']);
      })
      ->when(!empty($kelurahan) && $kelurahan != 'All', function ($q) use ($kelurahan, $anomali) {
        $q->whereIn('a.kelurahan_dom', $anomali['arr_kelurahan']);
      })
      ->selectRaw("
      b.tahun_pengerjaan,
      b.sumber_dana,
      b.instansi,
      COUNT(*) FILTER(WHERE COALESCE(b.nik,'')<>'') as jumlah_total
      ")
      ->whereRaw("(EXTRACT(YEAR FROM AGE(now(),a.tgl_lahir)) BETWEEN 0 AND 6)")
      ->whereRaw("(a.is_aktif_kependudukan IS TRUE AND a.status_keberadaan='ADA')")
      ->groupByRaw("b.tahun_pengerjaan,b.sumber_dana,b.instansi")
      ->get();

    $tahun = collect($data_penduduk)->sortByDesc('tahun_pengerjaan')->pluck('tahun_pengerjaan')->unique()->values();

    $data_penduduk = collect($data_penduduk)->groupBy('tahun_pengerjaan')->map(function ($m) {
      return $m->values();
    });

    $data = [
      'judul' => $judul,
      'data' => $data_penduduk,
      'jenis' => $jenis,
      'tahun' => $tahun,
    ];
    $content = view('bunda.dashboard_asw.rutilahu', $data)->render();
    return $content;
  }

  function detail_rutilahu(Request $request)
  {
    // return $request->all();
    $anomali = $this->anomali_nama($request);
    $kategori = $request->kategori; //"belum"
    $tahun = $request->tahun; //"belum"
    $sumber_dana = $request->sumber_dana; //"belum"
    $instansi = $request->instansi; //"belum"
    $kecamatan = $request->kecamatan; //"BULAK"
    $kelamin = $request->kelamin; //null
    $kelurahan = $request->kelurahan; //"BULAK"
    $menu = $request->menu; //"dinsos"

    $tingkat = Auth::getUser()->tingkat;

    $data = DB::connection('db_cutt_off')->table('public.pengerjaan as b')
      ->join('public.penduduk_inject as a', 'a.nik', 'b.nik')
      ->when(!empty($kecamatan) && $kecamatan != 'All', function ($q) use ($kecamatan, $anomali) {
        $q->whereIn('a.kecamatan_dom', $anomali['arr_kecamatan']);
      })
      ->when(!empty($kelurahan) && $kelurahan != 'All', function ($q) use ($kelurahan, $anomali) {
        $q->whereIn('a.kelurahan_dom', $anomali['arr_kelurahan']);
      })
      ->when($sumber_dana !== null, function ($w) use ($sumber_dana) {
        return $w->where('sumber_dana', $sumber_dana);
      })
      ->when($tahun !== null, function ($w) use ($tahun) {
        return $w->where('tahun_pengerjaan', $tahun);
      })
      ->when($instansi !== null, function ($w) use ($instansi) {
        return $w->where('instansi', $instansi);
      })
      ->whereRaw("(a.is_aktif_kependudukan IS TRUE AND a.status_keberadaan='ADA')")
      ->selectRaw("a.nama_lengkap,a.jenis_kelamin,a.nik,a.alamat,a.rt,a.rw,a.kelurahan,a.kecamatan,
        b.tahun_pengerjaan,b.sumber_dana,b.instansi,b.status_pekerjaan,
        EXTRACT(YEAR FROM AGE(NOW(),a.tgl_lahir)) as usia,
        COALESCE(NULLIF(a.alamat_dom,''),a.alamat) as alamat_dom,
        COALESCE(NULLIF(a.rt_dom,''),a.rt) as rt_dom,
        COALESCE(NULLIF(a.rw_dom,''),a.rw) as rw_dom,
        COALESCE(NULLIF(a.kelurahan_dom,''),a.kelurahan) as kelurahan_dom,
        COALESCE(NULLIF(a.kecamatan_dom,''),a.kecamatan) as kecamatan_dom")
      ->whereRaw("(EXTRACT(YEAR FROM AGE(NOW(),a.tgl_lahir))) BETWEEN 0 AND 6");

    return DataTables::queryBuilder($data)
      ->addIndexColumn()
      ->addColumn('nik_baru', function ($i) {
        return '<a href="' . route('bunda-dashboard_asw-detail_informasi', ['nik' => $i->nik]) . '" target="_blank">' . $i->nik . '</a>';
      })
      ->rawColumns(['nik_baru'])
      ->make(true);
  }

  function dp3a(Request $request)
  {
    $anomali = $this->anomali_nama($request);
    $kecamatan = $request->kecamatan ?? '';
    $kecamatan_kode = $request->kecamatan_kode ?? '';
    $kelurahan = $request->kelurahan ?? '';
    $kelurahan_kode = $request->kelurahan_kode ?? '';
    $jenis = $request->jenis ?? '';
    $judul = 'Data Pengaduan DP3A';
    if ($kelurahan != 'All') {
      $judul .= ' Kecamatan ' . $kecamatan;
      $judul .= ', Kelurahan ' . $kelurahan;
    } else if ($kecamatan != 'All') {
      $judul .= ' Kecamatan ' . $kecamatan;
    } else {
      $judul .= ' se-Kota Surabaya ';
    }

    $tingkat = Auth::getUser()->tingkat;

    $data_penduduk = DB::connection('db_cutt_off')->table('public.data_pengaduan_paud')
      ->when(!empty($kecamatan) && $kecamatan != 'All', function ($q) use ($kecamatan, $anomali) {
        $q->whereIn(DB::raw("UPPER(kecamatan)"), $anomali['arr_kecamatan']);
      })
      ->when(!empty($kelurahan) && $kelurahan != 'All', function ($q) use ($kelurahan, $anomali) {
        $q->whereIn(DB::raw("UPPER(kelurahan)"), $anomali['arr_kelurahan']);
      })
      ->selectRaw("
      EXTRACT(YEAR FROM tgl_pengaduan) as tahun,
      COUNT(*) as jumlah_total,
      COUNT(*) FILTER(WHERE penanganan='PUSPAGA') as jumlah_puspaga,
      COUNT(*) FILTER(WHERE penanganan='UPTD PPA') as jumlah_ppa
      ")
      ->groupByRaw("tahun")
      ->orderByRaw("tahun DESC")
      ->get();

    $semua = [
      'tahun' => 'Semua',
      'jumlah_total'   => $data_penduduk->sum('jumlah_total'),
      'jumlah_puspaga' => $data_penduduk->sum('jumlah_puspaga'),
      'jumlah_ppa'     => $data_penduduk->sum('jumlah_ppa'),
    ];

    // taruh di paling atas
    $data_penduduk = collect([$semua])->concat($data_penduduk->values());

    $data_penduduk = collect($data_penduduk)->groupBy('tahun')->map(function ($item) {
      $isi = $item->first();
      return $isi;
    });

    $data = [
      'judul' => $judul,
      'data' => $data_penduduk,
      'jenis' => $jenis,
    ];
    $content = view('bunda.dashboard_asw.dp3a', $data)->render();
    return $content;
  }

  function detail_dp3a(Request $request)
  {
    // return $request->all();
    $anomali = $this->anomali_nama($request);
    $kategori = $request->kategori; //"total"
    $kecamatan = $request->kecamatan; //"GAYUNGAN"
    $kelamin = $request->kelamin; //null
    $kelurahan = $request->kelurahan; //"GAYUNGAN"
    $menu = $request->menu; //"dp3a"
    $tahun = $request->tahun; //"Semua"

    $tingkat = Auth::getUser()->tingkat;

    $data = DB::connection('db_cutt_off')->table('public.data_pengaduan_paud')
      ->when(!empty($kecamatan) && $kecamatan != 'All', function ($q) use ($kecamatan, $anomali) {
        $q->whereIn(DB::raw("UPPER(kecamatan)"), $anomali['arr_kecamatan']);
      })
      ->when(!empty($kelurahan) && $kelurahan != 'All', function ($q) use ($kelurahan, $anomali) {
        $q->whereIn(DB::raw("UPPER(kelurahan)"), $anomali['arr_kelurahan']);
      })
      ->when($kategori, function ($w) use ($kategori) {
        if ($kategori == 'puspaga') {
          $w->where('penanganan', 'PUSPAGA');
        } else if ($kategori == 'ppa') {
          $w->where('penanganan', 'UPTD PPA');
        }
      })
      ->when($tahun != 'Semua', function ($w) use ($tahun) {
        $w->whereRaw("EXTRACT(YEAR FROM tgl_pengaduan)='$tahun'");
      })
      ->selectRaw("nama,usia,kelurahan,kecamatan,tipe_permasalahan,kategori_kasus,jenis_kasus,tgl_pengaduan,penanganan");

    return DataTables::queryBuilder($data)
      ->addIndexColumn()
      ->make(true);
  }

  function posyandu(Request $request)
  {
    $anomali = $this->anomali_nama($request);
    $kecamatan = $request->kecamatan ?? '';
    $kecamatan_kode = $request->kecamatan_kode ?? '';
    $kelurahan = $request->kelurahan ?? '';
    $kelurahan_kode = $request->kelurahan_kode ?? '';
    $jenis = $request->jenis ?? '';
    $judul = 'Data Posyandu';
    if ($kelurahan != 'All') {
      $judul .= ' Kecamatan ' . $kecamatan;
      $judul .= ', Kelurahan ' . $kelurahan;
    } else if ($kecamatan != 'All') {
      $judul .= ' Kecamatan ' . $kecamatan;
    } else {
      $judul .= ' se-Kota Surabaya ';
    }

    $tingkat = Auth::getUser()->tingkat;

    $data_posyandu = DB::connection('db_cutt_off')->table('public.data_posyandu_kominfo as a')
      ->leftjoin('public.penduduk_inject as b', 'b.nik', 'a.nik')
      ->selectRaw("
        a.kategori,
        COUNT(*) as jumlah,
        COUNT(*) FILTER(WHERE(COALESCE(b.nik,'')<>'')) as jumlah_surabaya,
        COUNT(*) FILTER(WHERE(COALESCE(b.nik,'')='')) as jumlah_luar_surabaya
      ")
      ->when(!empty($kecamatan) && $kecamatan != 'All', function ($q) use ($kecamatan, $anomali) {
        $q->whereIn('a.kecamatan', $anomali['arr_kecamatan']);
      })
      ->when(!empty($kelurahan) && $kelurahan != 'All', function ($q) use ($kelurahan, $anomali) {
        $q->whereIn('a.kelurahan', $anomali['arr_kelurahan']);
      })
      ->whereIn('a.kategori', ['GIZI BURUK', 'STUNTING', 'PRA STUNTING', 'PMT'])
      ->groupByRaw("a.kategori")
      ->orderByRaw("a.kategori")
      ->get();

    $data = [
      'judul' => $judul,
      'data_posyandu' => $data_posyandu,
      'jenis' => $jenis,
    ];
    $content = view('bunda.dashboard_asw.posyandu', $data)->render();
    return $content;
  }

  function detail_posyandu(Request $request)
  {
    // return $request->all();
    $anomali = $this->anomali_nama($request);
    $kategori = $request->kategori; //"total"
    $kecamatan = $request->kecamatan; //"GAYUNGAN"
    $kelamin = $request->kelamin; //null
    $kelurahan = $request->kelurahan; //"GAYUNGAN"
    $menu = $request->menu; //"dp3a"
    $tahun = (isset($request->tahun)) ? $request->tahun : date('Y'); //"Semua"

    $tingkat = Auth::getUser()->tingkat;

    $data_posyandu = DB::connection('db_cutt_off')
      ->table('public.data_posyandu_kominfo as a')
      ->leftjoin('public.penduduk_inject as b', DB::raw("CAST(b.nik as varchar)"), DB::raw("CAST(a.nik as varchar)"))
      ->selectRaw("
        a.kategori,
        a.nik,
        COALESCE(a.nama,'') as nama,
        COALESCE(b.nama_lengkap,'') as nama_lengkap,
        COALESCE(b.alamat,'') as alamat,
        COALESCE(b.rt,'') as rt,
        COALESCE(b.rw,'') as rw,
        COALESCE(b.kelurahan,'') as kelurahan,
        COALESCE(b.kecamatan,'') as kecamatan,
        COALESCE(NULLIF(b.alamat_dom,''),b.alamat) as alamat_dom,
        COALESCE(NULLIF(b.rt_dom,''),b.rt) as rt_dom,
        COALESCE(NULLIF(b.rw_dom,''),b.rw) as rw_dom,
        COALESCE(NULLIF(b.kelurahan_dom,''),b.kelurahan) as kelurahan_dom,
        COALESCE(NULLIF(b.kecamatan_dom,''),b.kecamatan) as kecamatan_dom
      ")
      ->when(!empty($kecamatan) && $kecamatan != 'All', function ($q) use ($kecamatan, $anomali) {
        $q->whereIn('a.kecamatan', $anomali['arr_kecamatan']);
      })
      ->when(!empty($kelurahan) && $kelurahan != 'All', function ($q) use ($kelurahan, $anomali) {
        $q->whereIn('a.kelurahan', $anomali['arr_kelurahan']);
      })
      ->when($kategori !== '', function ($w) use ($kategori) {
        $w->where('a.kategori', $kategori);
      });

    return DataTables::queryBuilder($data_posyandu)
      ->addIndexColumn()
      ->addColumn('nik_baru', function ($i) {
        return '<a href="' . route('bunda-dashboard_asw-detail_informasi', ['nik' => $i->nik]) . '" target="_blank">' . $i->nik . '</a>';
      })
      ->rawColumns(['nik_baru'])
      ->make(true);
  }

  function dispusip(Request $request)
  {
    $anomali = $this->anomali_nama($request);
    $kecamatan = $request->kecamatan ?? '';
    $kecamatan_kode = $request->kecamatan_kode ?? '';
    $kelurahan = $request->kelurahan ?? '';
    $kelurahan_kode = $request->kelurahan_kode ?? '';
    $jenis = $request->jenis ?? '';
    $judul = 'Data Kunjungan Perpustakaan (Dispusip)';
    if ($kelurahan != 'All') {
      $judul .= ' Kecamatan ' . $kecamatan;
      $judul .= ', Kelurahan ' . $kelurahan;
    } else if ($kecamatan != 'All') {
      $judul .= ' Kecamatan ' . $kecamatan;
    } else {
      $judul .= ' se-Kota Surabaya ';
    }

    $tingkat = Auth::getUser()->tingkat;

    $get_tempat_perpus = DB::connection('db_cutt_off')->table('public.kunjungan_sipus')
      ->selectRaw("nama_lokasi,EXTRACT(YEAR FROM tanggal_entri) as tahun")
      ->groupByRaw("nama_lokasi,tahun")->get();

    $tahun = collect($get_tempat_perpus)->pluck('tahun')->sortByDesc('tahun')->unique()->values();

    $data_penduduk = DB::connection('db_cutt_off')->table('public.kunjungan_sipus')
      ->when(!empty($kecamatan) && $kecamatan != 'All', function ($q) use ($kecamatan, $anomali) {
        $q->whereIn('nama_kecamatanktp', $anomali['arr_kecamatan']);
      })
      ->when(!empty($kelurahan) && $kelurahan != 'All', function ($q) use ($kelurahan, $anomali) {
        $q->whereIn('nama_kelurahanktp', $anomali['arr_kelurahan']);
      })
      ->selectRaw("
        CONCAT(EXTRACT(YEAR FROM tanggal_entri),'-',EXTRACT(MONTH FROM tanggal_entri)) as bulan,
        CASE WHEN nama_lokasi='PERPUSTAKAAN BALAI PEMUDA' THEN 'pemuda'
        ELSE
        CASE WHEN nama_lokasi='PERPUSTAKAAN RUNGKUT' THEN 'rungkut'
        ELSE 'lainnya'
        END END
        as tempat,
        COUNT(DISTINCT(nama_lengkap,nama_kecamatanktp,nama_lokasi)) as jumlah_total
      ")
      ->groupByRaw("bulan,tempat")
      ->orderByRaw("bulan DESC")
      ->get();

    $data_penduduk = collect($data_penduduk)->groupBy('bulan')->map(function ($item) {
      return $item;
    });

    $data = [
      'judul' => $judul,
      'data' => $data_penduduk,
      'jenis' => $jenis,
      'tahun' => $tahun,
    ];
    $content = view('bunda.dashboard_asw.dispusip', $data)->render();
    return $content;
  }

  function detail_dispusip(Request $request)
  {
    // return $request->all();
    $anomali = $this->anomali_nama($request);
    $kategori = $request->kategori; //"total"
    $kecamatan = $request->kecamatan; //"GAYUNGAN"
    $kelamin = $request->kelamin; //null
    $kelurahan = $request->kelurahan; //"GAYUNGAN"
    $menu = $request->menu; //"dp3a"
    $tahun = $request->tahun; //"Semua"

    $tingkat = Auth::getUser()->tingkat;

    $tahun = date('Y-m', strtotime($tahun));

    $last = DB::connection('db_cutt_off')->table('public.kunjungan_sipus')
      ->when(!empty($kecamatan) && $kecamatan != 'All', function ($q) use ($kecamatan, $anomali) {
        $q->whereIn('nama_kecamatanktp', $anomali['arr_kecamatan']);
      })
      ->when(!empty($kelurahan) && $kelurahan != 'All', function ($q) use ($kelurahan, $anomali) {
        $q->whereIn('nama_kelurahanktp', $anomali['arr_kelurahan']);
      })
      ->when($kategori, function ($w) use ($kategori) {
        if ($kategori == 'pemuda') {
          $w->where('nama_lokasi', 'PERPUSTAKAAN BALAI PEMUDA');
        } else if ($kategori == 'rungkut') {
          $w->where('nama_lokasi', 'PERPUSTAKAAN RUNGKUT');
        } else if ($kategori == 'lainnya') {
          $w->whereNotIn('nama_lokasi', ['PERPUSTAKAAN RUNGKUT', 'PERPUSTAKAAN BALAI PEMUDA']);
        }
      })
      ->when($tahun != 'Semua', function ($w) use ($tahun) {
        $w->whereRaw("CAST(tanggal_entri as varchar) ILIKE '$tahun%'");
      })
      ->selectRaw("CONCAT(nama_lengkap,nama_kecamatanktp) as nama,nama_lokasi,MAX(tanggal_entri) as tanggal_entri")
      ->groupByRaw("nama,nama_lokasi");

    $data = DB::connection('db_cutt_off')->table('public.kunjungan_sipus as a')
      ->JoinSub($last, 'x', function ($j) {
        return $j->on('x.nama', '=', DB::raw('CONCAT(a.nama_lengkap,a.nama_kecamatanktp)'))
          ->on('x.tanggal_entri', '=', 'a.tanggal_entri');
      })
      ->when(!empty($kecamatan) && $kecamatan != 'All', function ($q) use ($kecamatan, $anomali) {
        $q->whereIn('nama_kecamatanktp', $anomali['arr_kecamatan']);
      })
      ->when(!empty($kelurahan) && $kelurahan != 'All', function ($q) use ($kelurahan, $anomali) {
        $q->whereIn('nama_kelurahanktp', $anomali['arr_kelurahan']);
      })
      ->when($kategori, function ($w) use ($kategori) {
        if ($kategori == 'pemuda') {
          $w->where('a.nama_lokasi', 'PERPUSTAKAAN BALAI PEMUDA');
        } else if ($kategori == 'rungkut') {
          $w->where('a.nama_lokasi', 'PERPUSTAKAAN RUNGKUT');
        } else if ($kategori == 'lainnya') {
          $w->whereNotIn('a.nama_lokasi', ['PERPUSTAKAAN RUNGKUT', 'PERPUSTAKAAN BALAI PEMUDA']);
        }
      })
      ->when($tahun != 'Semua', function ($w) use ($tahun) {
        $w->whereRaw("CAST(a.tanggal_entri as varchar) ILIKE '$tahun%'");
      })
      ->selectRaw("a.nama_lengkap,a.usia,a.alamat,a.nama_kelurahanktp,a.nama_kabkotktp,a.nama_provinsiktp,a.nama_kecamatanktp,a.nama_lokasi,a.tanggal_entri");

    return DataTables::queryBuilder($data)
      ->addIndexColumn()
      ->make(true);
  }

  function generate_token_kominfo()
  {
    $response = Http::attach('username', 'dispendik')
      ->attach('password', 'SURABAYA@d15d1k2026')
      ->withHeaders([
        'Accept' => '*/*',
        'User-Agent' => 'Thunder Client (https://www.thunderclient.com)',
      ])
      ->post('https://kantorku.surabaya.go.id/api/login');

    return json_decode($response);
  }

  function halaman_sinkron(Request $request)
  {
    $nik = $request->nik;
    $data = [
      'nik' => $nik
    ];
    return view('bunda.dashboard_asw.halaman_sinkron', $data);
  }

  function proses_sinkron(Request $request)
  {
    $nik = $request->nik;
    // POSYANDU KANTORKU
    try {
      $token = DB::table('public.token_api')->get();
      $token_kominfo_posyandu = collect($token)->where('dinas', 'kominfo_posyandu')->first()->token ?? '';

      $status_sinkron_posyandu = false;
      $percobaan_sinkron = 0;
      do {
        $response = Http::withHeaders([
          'Accept' => '*/*',
          'User-Agent' => 'Thunder Client (https://www.thunderclient.com)',
          'Authorization' => 'Bearer ' . $token_kominfo_posyandu,
        ])
          ->get('https://kantorku.surabaya.go.id/api/integrasi/asw-posyandu?data=USIA%20PAUD&nik=' . $nik);

        // Langsung decode response sebagai object/array
        $dt_posyandu = $response->object();

        // Cek status respon (apakah HTTP Status 401 atau pesan "Unauthorized access")
        if ($response->status() == 401 || ($dt_posyandu->message ?? '') === 'Unauthorized access') {
          // Generate token baru
          $generate_token_kominfo = $this->generate_token_kominfo();

          // Simpan token baru ke database
          DB::table('public.token_api')->updateOrInsert(
            ['dinas' => 'kominfo_posyandu'],
            ['token' => $generate_token_kominfo->token]
          );

          // Perbarui variabel token untuk digunakan di iterasi do...while berikutnya
          $token_kominfo_posyandu = $generate_token_kominfo->token;
        } else {
          // Jika tidak error unauthorized (berhasil), ubah status menjadi true
          $status_sinkron_posyandu = true;
        }

        $percobaan_sinkron++;

        // Ulangi JIKA status sinkron masih FALSE DAN percobaan sinkron KURANG DARI 3
      } while (!$status_sinkron_posyandu && $percobaan_sinkron < 3);

      if ($response->status() == 200 && $dt_posyandu->success == true) {
        $timbang = collect(collect($dt_posyandu->data)[0]->detail_timbang_posyandu)->values()->toArray();
        $imunisasi = collect(collect($dt_posyandu->data)[0]->detail_imunisasi)->values()->toArray();

        if (count($timbang) > 0) {
          foreach ($timbang as $item) {
            // Cast item ke array jika datanya berupa Object (stdClass)
            $row = (array) $item;

            DB::connection('db_cutt_off')->table('public.detail_timbang')->updateOrInsert(
              // Parameter 1: Key unik untuk pencarian
              [
                'nik'              => $row['nik'],
                'tanggal_posyandu' => $row['tanggal_posyandu'],
                'posyandu'         => $row['posyandu'],
              ],
              // Parameter 2: Kolom yang akan di-update atau di-insert
              [
                'beratbadan'     => $row['beratbadan'],
                'tinggibadan'    => $row['tinggibadan'],
                'lila'           => $row['lila'],
                'status_tb_usia' => $row['status_tb_usia'],
                'status_bb_usia' => $row['status_bb_usia'],
                'status_bb_tb'   => $row['status_bb_tb'],
              ]
            );
          }
        }

        if (count($imunisasi) > 0) {
          foreach ($imunisasi as $item) {
            // Cast item ke array jika datanya berupa Object (stdClass)
            $row = (array) $item;

            DB::connection('db_cutt_off')->table('public.detail_imunisasi')->updateOrInsert(
              // Parameter 1: Key unik untuk pencarian
              [
                'nik'              => $row['nik'],
                'tanggal_imunisasi' => $row['tanggal_imunisasi'],
                'imunisasi'         => $row['imunisasi'],
              ],
              // Parameter 2: Kolom yang akan di-update atau di-insert
              [
                'imunisasike'     => $row['imunisasike'],
              ]
            );
          }
        }
      }
    } catch (\Throwable $th) {
    }
    // END POSYANDU KANTORKU

    // SEKOLAH
    try {
      $data_paud = DB::connection('pgsql_paud')->table('public.siswa as s')
        ->join('public.sekolah as se', 'se.npsn', 's.npsn')
        ->selectRaw("s.nik,s.nama,se.npsn,se.nama as nama_sekolah,se.jenjang,CASE WHEN se.status='N' THEN 'Negeri' ELSE 'Swasta' END as status,s.status_siswa,CASE WHEN s.alumni is true THEN 'ALUMNI' ELSE '' END as status_alumni,CASE WHEN se.jenjang in ('SPS','TPA') THEN 0 ELSE CASE WHEN se.jenjang IN ('KB') THEN 1 ELSE 3 END END as urutan,s.kebutuhan_khusus,s.kategori_inklusi,s.berkas_inklusi_psikolog,s.siswa_id,s.anakke,s.foto,s.id_siswa")
        ->where('s.nik', $nik)->get();
      $data_sd = DB::connection('db_sd')->table('public.siswa as s')
        ->join('public.sekolah as se', 'se.npsn', 's.npsn')
        ->selectRaw("s.nik,s.nama,se.npsn,se.nama as nama_sekolah,se.jenjang,CASE WHEN se.status='N' THEN 'Negeri' ELSE 'Swasta' END as status,s.status_siswa,CASE WHEN s.alumni is true THEN 'ALUMNI' ELSE '' END as status_alumni,4 as urutan,s.kebutuhan_khusus,s.kategori_inklusi,s.berkas_inklusi_psikolog,s.siswa_id,s.anakke,s.foto,s.id_siswa")
        ->where('s.nik', $nik)->get();
      $data_smp = DB::connection('db_smp')->table('public.siswa as s')
        ->join('public.sekolah as se', 'se.npsn', 's.npsn')
        ->selectRaw("s.nik,s.nama,se.npsn,se.nama as nama_sekolah,se.jenjang,CASE WHEN se.status='N' THEN 'Negeri' ELSE 'Swasta' END as status,s.status_siswa,CASE WHEN s.alumni is true THEN 'ALUMNI' ELSE '' END as status_alumni,5 as urutan,s.kebutuhan_khusus,s.kategori_inklusi,s.berkas_inklusi_psikolog,s.siswa_id,s.anakke,s.foto,s.id_siswa")
        ->where('s.nik', $nik)->get();

      $data_profil = $data_paud->merge($data_sd)->merge($data_smp)->sortBy('urutan');

      $data_sekarang = collect($data_profil)->where('status_siswa', 'Aktif')->where('status_alumni', '')->first();
      $data_update = [
        'jenjang_sekolah' => $data_sekarang->jenjang,
        'status_sekolah' => substr($data_sekarang->status, 0, 1),
        'nama_sekolah' => $data_sekarang->nama_sekolah,
        'npsn_sekolah' => $data_sekarang->npsn,
      ];
      if (!empty($data_sekarang)) {
        DB::connection('db_cutt_off')->table('public.penduduk_inject')->where('nik', $nik)
          ->where(function ($w) {
            $w->where('is_aktif_pendidikan', false)->orWhereNull('is_aktif_pendidikan');
          })
          ->update($data_update);
      }
    } catch (\Throwable $th) {
    }
    // END SEKOLAH

    // RIWAYAT KESEHATAN DINKES
    try {
      $response = Http::withBody(
        '{
          "page": 1,
          "max_size": 10,
          "nik": "' . $nik . '"
        }',
        'json'
      )
        ->withHeaders([
          'Accept' => '*/*',
          'User-Agent' => 'Thunder Client (https://www.thunderclient.com)',
          'Authorization' => 'Basic ZGlzZGlrOnJ0NDMy',
          'Content-Type' => 'application/json',
        ])
        ->post('https://dinkes.surabaya.go.id/api-dinkes/disdik/imunisasi');

      $dt_dinkes = $response->object();
      if ($response->status() == 200 && $dt_dinkes->success == true) {
        $vitamin = json_encode(collect($dt_dinkes->data)[0]->intervensi ?? []);
        $imunisasi = json_encode(collect($dt_dinkes->data)[0]->vaksin ?? []);
        $konginetal = json_encode(collect($dt_dinkes->data)[0]->konginetal ?? []);

        $data_update = [
          'imunisasi' => $imunisasi,
          'vitamin' => $vitamin,
          'konginetal' => $konginetal,
          'beratbadan' => collect($dt_dinkes->data)[0]->beratbadan,
          'tinggibadan' => collect($dt_dinkes->data)[0]->tinggibadan,
          'tb_u' => collect($dt_dinkes->data)[0]->tb_u,
          'bb_u' => collect($dt_dinkes->data)[0]->bb_u,
          'bbtb' => collect($dt_dinkes->data)[0]->bbtb,
        ];
        DB::connection('db_cutt_off')->table('public.history_imunisasi')
          ->updateOrInsert(['nik' => $nik], $data_update);
      }
    } catch (\Throwable $th) {
    }
    // END RIWAYAT KESEHATAN DINKES

    // INTERVENSI DINSOS (PKH & ALBAMED)
    try {
      for ($i = (date('Y') - 2); $i <= date('Y'); $i++) {
        $response = Http::withBody(
          '{
              "access_key": "7,by4dOHxhhMg7ad0yBhkinHeHIuEuC9yMVmSQCRG8",
              "access_id": "7,OzA03fh6NASsGLTMTsMLCUkEqpzL02o1lWBHwI8q",
              "tahun_anggaran": "' . $i . '",
              "nik":"' . $nik . '"
          }',
          'json'
        )
          ->withHeaders([
            'Accept' => '*/*',
            'User-Agent' => 'Thunder Client (https://www.thunderclient.com)',
            'Content-Type' => 'application/json',
          ])
          ->post('https://sikeluargamiskin.surabaya.go.id/api/dispendik/bantuan_pkh');

        $response_almed = Http::withBody(
          '{
              "access_key": "7,by4dOHxhhMg7ad0yBhkinHeHIuEuC9yMVmSQCRG8",
              "access_id": "7,OzA03fh6NASsGLTMTsMLCUkEqpzL02o1lWBHwI8q",
              "tahun_anggaran": "' . $i . '",
              "nik":"' . $nik . '"
          }',
          'json'
        )
          ->withHeaders([
            'Accept' => '*/*',
            'User-Agent' => 'Thunder Client (https://www.thunderclient.com)',
            'Content-Type' => 'application/json',
          ])
          ->post('https://sikeluargamiskin.surabaya.go.id/api/dispendik/bantuan_albandis');

        $dt_response_pkh = $response->object();
        if ($response->status() == 200 && $dt_response_pkh->status == 1 && count(collect($dt_response_pkh->data)) > 0) {
          $where = [
            'nik' => collect($dt_response_pkh->data)[0]->nik,
            'tahun_anggaran' => $i,
            'jenis_bantuan' => 'PKH'
          ];
          $data_update = [
            'nik' => collect($dt_response_pkh->data)[0]->nik,
            'jenis_bantuan' => 'PKH',
            'ket_bantuan' => collect($dt_response_pkh->data)[0]->tahap,
            'tahun_anggaran' => collect($dt_response_pkh->data)[0]->tahun_anggaran,
            'updated_at' => NOW(),
          ];
          DB::connection('db_cutt_off')->table('public.penduduk_intervensi')->updateOrInsert($where, $data_update);
        }

        $dt_response_almed = $response_almed->object();
        if ($response->status() == 200 && $dt_response_almed->status == 1 && count(collect($dt_response_almed->data)) > 0) {
          $where = [
            'nik' => collect($dt_response_pkh->data)[0]->nik,
            'tahun_anggaran' => $i,
            'jenis_bantuan' => collect($dt_response_almed->data)[0]->jenis_bantuan
          ];
          $data_update = [
            'nik' => collect($dt_response_almed->data)[0]->nik,
            'jenis_bantuan' => collect($dt_response_almed->data)[0]->jenis_bantuan,
            'ket_bantuan' => collect($dt_response_almed->data)[0]->ket_bansos,
            'tahun_anggaran' => $i,
            'updated_at' => NOW(),
          ];
          DB::connection('db_cutt_off')->table('public.penduduk_intervensi')->updateOrInsert($where, $data_update);
        }
      }
    } catch (\Throwable $th) {
    }
    // END INTERVENSI DINSOS (PKH & ALBAMED)

    // INKLUSI DINSOS
    try {
      $response = Http::withBody(
        '{
            "nik":"' . $nik . '"
        }',
        'json'
      )
        ->withHeaders([
          'Accept' => '*/*',
          'User-Agent' => 'Thunder Client (https://www.thunderclient.com)',
          'Access-Key' => '23,0aqtlAqIUdr8NVmy8GSxMmNMzLr1qWqd9OHlyEnl',
          'Access-Id' => '23,xBF9EEoeDJk5SAf5F1wj08TpFu76BGDZD8n6VRoz',
          'Content-Type' => 'application/json',
        ])
        ->post('https://sikeluargamiskin.surabaya.go.id/api/splp/get_data_anggota_rap');

      $data_inklusi_dinsos = $response->object();

      if ($response->status() == 200 && $data_inklusi_dinsos->success && count(collect($data_inklusi_dinsos->data)) > 0) {
        $data_update = [
          'nik' => collect($data_inklusi_dinsos->data)[0]->nik,
          'nama' => collect($data_inklusi_dinsos->data)[0]->nama,
          'alamat' => collect($data_inklusi_dinsos->data)[0]->alamat,
          'jenis_kelamin' => collect($data_inklusi_dinsos->data)[0]->jenis_kelamin,
          'kecamatan' => collect($data_inklusi_dinsos->data)[0]->kecamatan,
          'kelurahan' => collect($data_inklusi_dinsos->data)[0]->kelurahan,
          'rt' => collect($data_inklusi_dinsos->data)[0]->rt,
          'rw' => collect($data_inklusi_dinsos->data)[0]->rw,
          'jenis_disabilitas' => collect($data_inklusi_dinsos->data)[0]->jenis_disabilitas,
          'nama_kelas' => collect($data_inklusi_dinsos->data)[0]->nama_kelas,
          'id_tempat' => collect($data_inklusi_dinsos->data)[0]->id_tempat,
          'nama_tempat' => collect($data_inklusi_dinsos->data)[0]->nama_tempat,
          'updated_at' => NOW(),
          'tempat_lahir' => collect($data_inklusi_dinsos->data)[0]->tempat_lahir,
          'tanggal_lahir' => collect($data_inklusi_dinsos->data)[0]->tanggal_lahir,
        ];

        DB::connection('db_cutt_off')->table('public.penduduk_dinsos_inklusi')
          ->updateOrInsert(['nik', collect($data_inklusi_dinsos->data)[0]->nik], $data_update);
      }
    } catch (\Throwable $th) {
    }
    // END INKLUSI DINSOS

    // DESIL NASIONAL
    try {
      $response = Http::withBody(
        '{
            "nik":"' . $nik . '"
        }',
        'json'
      )
        ->withHeaders([
          'Accept' => '*/*',
          'User-Agent' => 'Thunder Client (https://www.thunderclient.com)',
          'Access-Key' => '23,0aqtlAqIUdr8NVmy8GSxMmNMzLr1qWqd9OHlyEnl',
          'Access-Id' => '23,xBF9EEoeDJk5SAf5F1wj08TpFu76BGDZD8n6VRoz',
          'Content-Type' => 'application/json',
        ])
        ->post('https://sikeluargamiskin.surabaya.go.id/api/splp/get_data_warga');

      $data_inklusi_dinsos = $response->object();

      if ($response->status() == 200 && $data_inklusi_dinsos->status) {
        $where = [
          'nik' => $data_inklusi_dinsos->data->nik,
        ];
        $data_update = [
          'desil_nasional' => $data_inklusi_dinsos->data->desil_nasional,
        ];

        DB::connection('db_cutt_off')->table('public.penduduk_inject')
          ->updateOrInsert($where, $data_update);
      }
    } catch (\Throwable $th) {
    }
    // END DESIL NASIONAL

    // SOTH
    try {
      $response = Http::withHeaders([
        'Accept' => '*/*',
        'User-Agent' => 'Thunder Client (https://www.thunderclient.com)',
        'Authorization' => 'Bearer ' . $token_kominfo_posyandu,
      ])
        ->get('https://kantorku.surabaya.go.id/api/integrasi/asw-soth?nik_ibu=&nik_balita=' . $nik . '&page=1');

      $dt_response = $response->object();
      if ($response->status() == 200 && $dt_response->success == true) {
        $data_update = [
          'id_unik' => collect($dt_response->data)[0]->nik_ibu . '_' . (collect($dt_response->data)[0]->id_balita_soth ?? 'None'),
          'id_soth' => collect($dt_response->data)[0]->id_soth,
          'nama_soth' => collect($dt_response->data)[0]->nama_soth,
          'alamat_soth' => collect($dt_response->data)[0]->alamat_soth,
          'rt_soth' => collect($dt_response->data)[0]->rt_soth,
          'rw_soth' => collect($dt_response->data)[0]->rw_soth,
          'id_kel_soth' => collect($dt_response->data)[0]->id_kel_soth,
          'nama_kel_soth' => collect($dt_response->data)[0]->nama_kel_soth,
          'id_kec_soth' => collect($dt_response->data)[0]->id_kec_soth,
          'nama_kec_soth' => collect($dt_response->data)[0]->nama_kec_soth,
          'status' => collect($dt_response->data)[0]->status,
          'no_kk' => collect($dt_response->data)[0]->no_kk,
          'nik_ibu' => collect($dt_response->data)[0]->nik_ibu,
          'id_balita_soth' => collect($dt_response->data)[0]->id_balita_soth,
          'nama_ibu' => collect($dt_response->data)[0]->nama_ibu,
          'nik_balita' => collect($dt_response->data)[0]->nik_balita,
          'nama_balita' => collect($dt_response->data)[0]->nama_balita,
          'tgl_lahir' => collect($dt_response->data)[0]->tgl_lahir,
          'berat_badan' => collect($dt_response->data)[0]->berat_badan,
          'tinggi_badan' => collect($dt_response->data)[0]->tinggi_badan,
          'usia_tahun' => collect($dt_response->data)[0]->usia_tahun,
          'usia_bulan' => collect($dt_response->data)[0]->usia_bulan,
          'is_stunting' => collect($dt_response->data)[0]->is_stunting,
          'is_prastunting' => collect($dt_response->data)[0]->is_prastunting,
          'is_gibur' => collect($dt_response->data)[0]->is_gibur,
        ];
        DB::connection('db_cutt_off')->table('public.api_data_soth as b')
          ->updateOrInsert(['id_unik' => collect($dt_response->data)[0]->nik_ibu . '_' . (collect($dt_response->data)[0]->id_balita_soth ?? 'None')], $data_update);
      }
    } catch (\Throwable $th) {
    }
    // END SOTH

    // DATA KEPENDUDUKAN
    try {
      $response = Http::withHeaders([
        'Accept' => '*/*',
        'User-Agent' => 'Thunder Client (https://www.thunderclient.com)',
        'Authorization' => 'Basic Y2VraW5fZGlzcGVuZGlrOmFMNDJSa3R5',
      ])
        ->get('https://api.surabaya.go.id/integrasi/api/cekin/calon-murid-usia-produktif?usia_awal=0&usia_akhir=6&nik=' . $nik);

      $dt_kependudukan = $response->object();

      if ($response->status() == 200 && $dt_kependudukan == true) {
        $where = [
          'nik' => collect($dt_kependudukan->data)[0]->nik,
        ];

        $data_update = [
          'no_kk' => collect($dt_kependudukan->data)[0]->no_kk,
          'nama_lengkap' => collect($dt_kependudukan->data)[0]->nama_lengkap,
          'status_kawin' => collect($dt_kependudukan->data)[0]->status_kawin,
          'jenis_kelamin' => collect($dt_kependudukan->data)[0]->jenis_kelamin,
          'tempat_lahir' => collect($dt_kependudukan->data)[0]->tempat_lahir,
          'tgl_lahir' => collect($dt_kependudukan->data)[0]->tgl_lahir,
          'usia' => collect($dt_kependudukan->data)[0]->usia,
          'agama' => collect($dt_kependudukan->data)[0]->agama,
          'pekerjaan' => collect($dt_kependudukan->data)[0]->pekerjaan,
          'pendidikan' => collect($dt_kependudukan->data)[0]->pendidikan,
          'gol_darah' => collect($dt_kependudukan->data)[0]->gol_darah,
          'hub_keluarga' => collect($dt_kependudukan->data)[0]->hub_keluarga,
          'nama_ayah' => collect($dt_kependudukan->data)[0]->nama_ayah,
          'nama_ibu' => collect($dt_kependudukan->data)[0]->nama_ibu,
          'provinsi' => collect($dt_kependudukan->data)[0]->provinsi,
          'kabupaten' => collect($dt_kependudukan->data)[0]->kabupaten,
          'alamat' => collect($dt_kependudukan->data)[0]->alamat,
          'kecamatan' => collect($dt_kependudukan->data)[0]->kecamatan,
          'kelurahan' => collect($dt_kependudukan->data)[0]->kelurahan,
          'rw' => collect($dt_kependudukan->data)[0]->rw,
          'rt' => collect($dt_kependudukan->data)[0]->rt,
          'alamat_dom' => collect($dt_kependudukan->data)[0]->alamat_dom,
          'kecamatan_dom' => collect($dt_kependudukan->data)[0]->kecamatan_dom,
          'kelurahan_dom' => collect($dt_kependudukan->data)[0]->kelurahan_dom,
          'rw_dom' => collect($dt_kependudukan->data)[0]->rw_dom,
          'rt_dom' => collect($dt_kependudukan->data)[0]->rt_dom,
          'status_keberadaan' => collect($dt_kependudukan->data)[0]->status_keberadaan,
          'status_kependudukan' => collect($dt_kependudukan->data)[0]->status_kependudukan,
          'tanggal_kedatangan' => collect($dt_kependudukan->data)[0]->tanggal_kedatangan,
          'status_kedatangan' => collect($dt_kependudukan->data)[0]->status_kedatangan,
          'lama_thn_kedatangan' => collect($dt_kependudukan->data)[0]->lama_thn_kedatangan,
          'tanggal_update' => collect($dt_kependudukan->data)[0]->tanggal_update,
          'status_kepemilikan_kia' => collect($dt_kependudukan->data)[0]->status_kepemilikan_kia,
          'status_phbs' => collect($dt_kependudukan->data)[0]->status_phbs,
          'no_akta_lahir' => collect($dt_kependudukan->data)[0]->no_akta_lahir,
        ];
        DB::connection('db_cutt_off')->table('public.penduduk_inject')->updateOrInsert($where, $data_update);
      }
    } catch (\Throwable $th) {
    }
    // END DATA KEPENDUDUKAN
    return ['code' => 200];
  }

  function detail_informasi(Request $request)
  {
    $nik = $request->nik;
    $cekin_warga = DB::connection('db_cutt_off')->table('public.penduduk_inject')->where('nik', $nik)->first();

    $anggota_keluarga = DB::connection('db_cutt_off')->table('public.penduduk_inject')->where('no_kk', ($cekin_warga->no_kk ?? ''))
      ->orderByRaw("
        CASE WHEN hub_keluarga ilike 'kepala keluarga' THEN 0 ELSE
        CASE WHEN hub_keluarga ilike ANY(ARRAY['istri','suami']) THEN 1 ELSE 2 END END ASC,tgl_lahir DESC
      ")
      ->get();

    if (empty($cekin_warga)) {
      return 'Mohon maaf data tidak ditemukan di data cekin warga.<br><br><br><a href="javascript:void(0)" onclick="return window.close()">Tutup</a>';
    }
    $imunisasi = DB::connection('db_cutt_off')->table('public.detail_imunisasi')->where('nik', $nik)->orderByRaw("imunisasi,tanggal_imunisasi DESC")->get();
    $bantuan = DB::connection('db_cutt_off')->table('public.penduduk_intervensi')->where('nik', $nik)->orderByRaw("tahun_anggaran DESC")->get();
    $survey = DB::connection('db_cutt_off')->table('public.survey_bunda')->where('nik', $nik)->first();
    $gizi = DB::connection('db_cutt_off')->table('public.detail_timbang')
      ->selectRaw("tanggal_posyandu,posyandu,beratbadan,tinggibadan,lila,status_bb_tb,'posyandu' as lokasi")
      ->where('nik', $nik)->orderByRaw("tanggal_posyandu DESC")->get();
    $kka = DB::connection('pgsql_paud')->table('public.siswa as s')
      ->join('public.sekolah as se', 'se.npsn', 's.npsn')
      ->join('public.master_user as u', 'u.username', 'se.npsn')
      ->Join('history.history_tb_bb_siswa as tbbb', 'tbbb.siswa_id', 's.siswa_id')
      // ->leftJoin('history.kartu_kembang_anak as kka', 'kka.siswa_id', 's.siswa_id')
      ->selectRaw("'kka.tgl_pengecekan' as tanggal_posyandu,se.nama as posyandu,tbbb.tahun, tbbb.bulan, tbbb.tb as tinggibadan,tbbb.bb as beratbadan,tbbb.status_gizi as status_bb_tb,'sekolah' as lokasi")
      ->where('s.nik', $nik)
      // ->whereNotNull('tbbb.kesimpulan_kka')
      ->orderByRaw("tbbb.tahun DESC,tbbb.bulan DESC")->get();

    $kka = collect($kka)->map(function ($w) {
      $bulan = $w->bulan;  // Agustus
      $tahun = $w->tahun;

      // Format string: "first friday of YYYY-MM"
      $tanggal_posyandu = date('Y-m-d', strtotime("first friday of {$tahun}-{$bulan}"));
      $w->tanggal_posyandu = $tanggal_posyandu;
      $w->lila = '-';
      unset($w->bulan);
      unset($w->tahun);
      return $w;
    });

    $hasil_timbang = $kka->merge($gizi)->sortByDesc('tanggal_posyandu')->values();

    $data_paud = DB::connection('pgsql_paud')->table('public.siswa as s')
      ->join('public.sekolah as se', 'se.npsn', 's.npsn')
      ->selectRaw("s.nik,s.nama,se.npsn,se.nama as nama_sekolah,se.jenjang,CASE WHEN se.status='N' THEN 'Negeri' ELSE 'Swasta' END as status,s.status_siswa,CASE WHEN s.alumni is true THEN 'ALUMNI' ELSE '' END as status_alumni,CASE WHEN se.jenjang in ('SPS','TPA') THEN 0 ELSE CASE WHEN se.jenjang IN ('KB') THEN 1 ELSE 3 END END as urutan,s.kebutuhan_khusus,s.kategori_inklusi,s.berkas_inklusi_psikolog,s.siswa_id,s.anakke,s.foto,s.id_siswa,s.kelas,s.rombel")
      ->where('s.nik', $nik)->get();
    $data_sd = DB::connection('db_sd')->table('public.siswa as s')
      ->join('public.sekolah as se', 'se.npsn', 's.npsn')
      ->selectRaw("s.nik,s.nama,se.npsn,se.nama as nama_sekolah,se.jenjang,CASE WHEN se.status='N' THEN 'Negeri' ELSE 'Swasta' END as status,s.status_siswa,CASE WHEN s.alumni is true THEN 'ALUMNI' ELSE '' END as status_alumni,4 as urutan,s.kebutuhan_khusus,s.kategori_inklusi,s.berkas_inklusi_psikolog,s.siswa_id,s.anakke,s.foto,s.id_siswa,s.kelas,s.rombel")
      ->where('s.nik', $nik)->get();
    $data_smp = DB::connection('db_smp')->table('public.siswa as s')
      ->join('public.sekolah as se', 'se.npsn', 's.npsn')
      ->selectRaw("s.nik,s.nama,se.npsn,se.nama as nama_sekolah,se.jenjang,CASE WHEN se.status='N' THEN 'Negeri' ELSE 'Swasta' END as status,s.status_siswa,CASE WHEN s.alumni is true THEN 'ALUMNI' ELSE '' END as status_alumni,5 as urutan,s.kebutuhan_khusus,s.kategori_inklusi,s.berkas_inklusi_psikolog,s.siswa_id,s.anakke,s.foto,s.id_siswa,s.kelas,s.rombel")
      ->where('s.nik', $nik)->get();

    $data_profil = $data_paud->merge($data_sd)->merge($data_smp)->sortBy('urutan');

    $data_sekarang = collect($data_profil)->where('status_siswa', 'Aktif')->where('status_alumni', '')->first();
    $mbg = DB::connection('pgsql_paud')->table('history.mbg_lembaga')->where('npsn', ($data_sekarang->npsn ?? ''))->first();

    $data_kembang_inklusi = [];
    if (!empty($data_sekarang)) {
      $conn = 'pgsql_paud';
      if (in_array($data_sekarang->jenjang, ['SMP', 'MTS'])) {
        $conn = 'db_smp';
      } else if (in_array($data_sekarang->jenjang, ['SD', 'MI'])) {
        $conn = 'db_sd';
      }

      $data_kembang_inklusi = DB::connection($conn)->table('history.log_perkembangan_inklusi as l')
        ->join('public.indikator_perkembangan_inklusi as i', 'i.id', 'l.id_indikator')
        ->join('public.kategori_inklusi as k', 'k.id', 'i.kategori_inklusi_id')
        ->join('public.siswa as s', 's.siswa_id', 'l.siswa_id')
        ->join('public.sekolah as se', 'se.npsn', 's.npsn')
        ->selectRaw("l.*,i.nama_indikator,i.deskripsi,s.nama,s.nik,s.kategori_inklusi,se.npsn,se.nama as nama_sekolah,k.isi")
        ->where('s.siswa_id', $data_sekarang->siswa_id)
        ->orderBy('tanggal', 'DESC')->get();
    }

    // $genmas = DB::connection('db_genmas')->table('data_pendaftar')
    //   ->selectRaw("nik,nama_sekolah,tahun_pendaftaran as tahun")
    //   ->where('valid_online', 'ya')
    //   ->where('status_penerima', '1')
    //   ->where('nik', $nik)
    //   ->get();

    $beasiswa = DB::connection('pgsql_paud')->table('public.konfirmasi_beasiswa as b')
      ->join('public.detail_konfirmasi_beasiswa as d', 'd.konfirmasi_beasiswa_id', 'b.id')
      ->join('public.sekolah as s', 's.npsn', 'b.npsn')
      ->selectRaw("
            b.id,b.npsn,b.jenjang,b.tanggal_konfirmasi,b.periode,b.tahun,b.file_surat_pengajuan,
            d.nama_siswa,d.nik_siswa,d.no_kk,d.alamat,d.kelurahan,d.kecamatan,d.tanggal_lahir,
            s.nama as nama_sekolah
            ")
      ->where('d.status_menerima', 'Ya')
      ->where('b.status_pengajuan', 'Disetujui')
      ->where('d.nik_siswa', $nik)
      ->get();

    $inklusi_dinsos = DB::connection('db_cutt_off')->table('public.penduduk_dinsos_inklusi as b')
      ->join('public.penduduk_inject as a', 'a.nik', 'b.nik')
      ->selectRaw("a.nama_lengkap,b.jenis_disabilitas")
      ->where('a.nik', $nik)
      ->get();

    $judul = 'Data diri ' . $cekin_warga->nama_lengkap;

    $riwayat_kesehatan = DB::connection('db_cutt_off')->table('public.history_imunisasi as b')
      ->join('public.penduduk_inject as a', 'a.nik', 'b.nik')
      ->selectRaw("a.nama_lengkap,b.imunisasi,b.vitamin,b.konginetal,b.beratbadan,b.tinggibadan,b.tb_u,b.bb_u,b.bbtb")
      ->where('a.nik', $nik)
      ->first();

    $rutilahu = DB::connection('db_cutt_off')->table('public.pengerjaan as b')
      ->join('public.penduduk_inject as a', 'a.nik', 'b.nik')
      ->selectRaw("a.nama_lengkap,b.*")
      ->where('a.nik', $nik)
      ->get();

    $soth = DB::connection('db_cutt_off')->table('public.api_data_soth as b')
      ->join('public.penduduk_inject as a', 'a.nik', 'b.nik_balita')
      ->selectRaw("a.nama_lengkap,a.nik,b.nama_soth,b.alamat_soth,b.nama_ibu")
      ->where('a.nik', $nik)
      ->get();

    $siswa_id = $data_profil->pluck('siswa_id')->unique()->values()->toArray();
    $id_siswa = $data_profil->pluck('id_siswa')->unique()->values()->toArray();

    $pmt = collect([]);
    $manasik = collect([]);
    $talenta = collect([]);
    $edu = collect([]);
    if (count($siswa_id) > 0) {
      $pmt = DB::connection('db_simpaud')->table('public.pmt_pds as pd')
        ->join('public.pmt_usulans as u', 'u.id', 'pd.pmt_usulan_id')
        ->join('public.periodes as per', 'per.id', 'u.periode_id')
        ->selectRaw("pd.nik,pd.nama,u.npsn,u.nama as nama_sekolah,per.tahun,per.bulan,per.bulan_angka,CASE WHEN u.acc_usulan='1' THEN 'Setujui' ELSE '' END as status_acc")
        ->whereIn('pd.siswa_id_profil', $siswa_id)->get();

      $manasik = DB::connection('pgsql_paud')->table('kegiatan_pendukung.manasik_haji_peserta as p')
        ->join('kegiatan_pendukung.manasik_haji_jadwal as j', 'j.id', 'p.jadwal_id')
        ->selectRaw("j.tanggal_pengajuan,j.npsn,j.nama_sekolah,j.jenjang")
        ->whereIn('p.siswa_id', $siswa_id)
        ->where('j.status_jadwal', 'Setujui')
        ->orderByRaw("j.tanggal_pengajuan DESC")
        ->get();

      $talenta = DB::table('public.talenta_seni_peserta as p')
        ->join('public.talenta_seni_jadwal as j', 'j.id', 'p.jadwal_id')
        ->leftJoin('public.talenta_seni_tempat as t', 't.id', 'j.tempat_id')
        ->selectRaw("t.nama as nama_tempat,j.tanggal,j.judul,j.npsn,j.nama_sekolah,j.jenjang")
        ->whereIn('p.siswa_id', $siswa_id)
        ->where('j.status_jadwal', 'Setujui')
        ->orderByRaw("j.tanggal DESC")
        ->get();

      $id_siswanya = array_map(function ($v) {
        return '[' . $v;
      }, $id_siswa);

      $str_id_siswa = "'%" . implode("%','%", $id_siswanya) . "%'";

      $edu = DB::connection('pgsql_paud')
        ->table('kegiatan_pendukung.edu_city_tour_jadwals as j')
        ->join('kegiatan_pendukung.edu_city_tour_tempats as t', 't.id', 'j.tempat_id')
        ->join('public.sekolah as se', 'se.npsn', 'j.npsn')
        ->selectRaw("t.nama,j.npsn,se.jenjang,se.nama as nama_sekolah,j.tanggal,t.nama as nama_wisata")
        ->where('j.status_jadwal', 'Setujui')
        ->whereRaw("j.peserta ILIKE ANY(ARRAY[$str_id_siswa])")
        ->get();
    }


    $data = [
      'judul' => $judul,
      'cekin_warga' => $cekin_warga,
      'imunisasi' => $imunisasi,
      'gizi' => $hasil_timbang,
      'bantuan' => $bantuan,
      'survey' => $survey,
      'kka' => $kka,
      'data_profil' => $data_profil,
      'data_sekarang' => $data_sekarang,
      'data_kembang_inklusi' => $data_kembang_inklusi,
      'beasiswa' => $beasiswa,
      'inklusi_dinsos' => $inklusi_dinsos,
      'riwayat_kesehatan' => $riwayat_kesehatan,
      'rutilahu' => $rutilahu,
      'soth' => $soth,
      'pmt' => $pmt,
      'manasik' => $manasik,
      'talenta' => $talenta,
      'edu' => $edu,
      'mbg' => $mbg,
      'anggota_keluarga' => $anggota_keluarga,
      // 'genmas' => $genmas,
    ];

    return view('bunda.dashboard_asw.detail_informasi', $data);
  }

  function riwayat_kesehatan(Request $request)
  {
    $nik = $request->nik;
    $curl = curl_init();

    $token = $this->gen_token_dinkes();

    if ($token->success) {
      $postFields = json_encode([
        'page' => 1,
        'max_size' => 10,
        'nik' => $nik,
      ]);

      curl_setopt_array($curl, [
        CURLOPT_URL => "https://dinkes.surabaya.go.id/api-dinkes/disdik/imunisasi",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => $postFields,
        CURLOPT_HTTPHEADER => [
          "Accept: */*",
          "User-Agent: Thunder Client (https://www.thunderclient.com)",
          "Authorization: Bearer " . $token->data->access_token,
          "Content-Type: application/json",
        ],
      ]);

      $response = curl_exec($curl);
      $err = curl_error($curl);

      curl_close($curl);

      if ($err) {
        return "cURL Error #:" . $err;
      } else {
        return json_decode($response);
      }
    } else {
      return '403';
    }
  }

  function gen_token_dinkes()
  {
    $curl = curl_init();

    $postFields = json_encode([
      'username' => 'disdik',
      'password' => 'rt432'
    ]);

    curl_setopt_array($curl, [
      CURLOPT_URL => "https://dinkes.surabaya.go.id/api-dinkes/login",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => $postFields,
      CURLOPT_HTTPHEADER => [
        "Accept: */*",
        "User-Agent: Thunder Client (https://www.thunderclient.com)",
        "Content-Type: application/json",
      ],
    ]);

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
      return "cURL Error #:" . $err;
    } else {
      return json_decode($response);
    }
  }

  function soth(Request $request)
  {
    $anomali = $this->anomali_nama($request);
    $kecamatan = $request->kecamatan ?? '';
    $kecamatan_kode = $request->kecamatan_kode ?? '';
    $kelurahan = $request->kelurahan ?? '';
    $kelurahan_kode = $request->kelurahan_kode ?? '';
    $jenis = $request->jenis ?? '';
    $judul = 'Data Penduduk Usia 0-6 tahun yang mengikuti SOTH di ';
    if ($kelurahan != 'All') {
      $judul .= ' Kecamatan ' . $kecamatan;
      $judul .= ', Kelurahan ' . $kelurahan;
    } else if ($kecamatan != 'All') {
      $judul .= ' Kecamatan ' . $kecamatan;
    } else {
      $judul .= ' Kota Surabaya ';
    }

    $tingkat = Auth::getUser()->tingkat;

    $data_penduduk = DB::connection('db_cutt_off')->table('public.penduduk_inject as a')
      ->join('public.api_data_soth as b', 'a.nik', 'b.nik_balita')
      ->when(!empty($kecamatan) && $kecamatan != 'All', function ($q) use ($kecamatan, $anomali) {
        $q->whereIn('a.kecamatan_dom', $anomali['arr_kecamatan']);
      })
      ->when(!empty($kelurahan) && $kelurahan != 'All', function ($q) use ($kelurahan, $anomali) {
        $q->whereIn('a.kelurahan_dom', $anomali['arr_kelurahan']);
      })
      ->selectRaw("
        EXTRACT(YEAR FROM AGE(NOW(),a.tgl_lahir)) as usia,
        COUNT(*)  as jumlah
      ")
      ->whereRaw("(EXTRACT(YEAR FROM AGE(now(),a.tgl_lahir)) BETWEEN 0 AND 6)")
      ->whereRaw("(a.is_aktif_kependudukan IS TRUE AND a.status_keberadaan='ADA')")
      ->groupByRaw("EXTRACT(YEAR FROM AGE(NOW(),a.tgl_lahir))")
      ->orderByRaw("usia")
      ->get();

    $data = [
      'judul' => $judul,
      'data' => $data_penduduk,
      'jenis' => $jenis,
    ];
    $content = view('bunda.dashboard_asw.soth', $data)->render();
    return $content;
  }

  function detail_soth(Request $request)
  {
    // return $request->all();
    $anomali = $this->anomali_nama($request);
    $kategori = $request->kategori; //"total"
    $kelamin = $request->kelamin; //null
    $menu = $request->menu; //"cekin_warga"
    $kecamatan = $request->kecamatan;
    $kelurahan = $request->kelurahan;
    $usia = $request->usia;

    $tingkat = Auth::getUser()->tingkat;

    $data_penduduk = DB::connection('db_cutt_off')
      ->table('public.penduduk_inject as a')
      ->join('public.api_data_soth as b', 'a.nik', 'b.nik_balita')
      ->when(!empty($kecamatan) && $kecamatan != 'All', function ($q) use ($kecamatan, $anomali) {
        $q->whereIn('a.kecamatan_dom', $anomali['arr_kecamatan']);
      })
      ->when(!empty($kelurahan) && $kelurahan != 'All', function ($q) use ($kelurahan, $anomali) {
        $q->whereIn('a.kelurahan_dom', $anomali['arr_kelurahan']);
      })
      ->when($usia !== null, function ($w) use ($usia) {
        if ($usia === 'semua') {
          $w->whereRaw("EXTRACT(YEAR FROM AGE(NOW(), a.tgl_lahir)) BETWEEN 0 AND 6");
        } elseif (is_numeric($usia)) {
          $w->whereRaw("EXTRACT(YEAR FROM AGE(NOW(), a.tgl_lahir)) = ?", [$usia]);
        }
      })
      ->selectRaw("
        a.nik,
        a.nama_lengkap,
        a.jenis_kelamin,
        a.alamat,
        a.rt,
        a.rw,
        a.kecamatan,
        a.kelurahan,
        COALESCE(NULLIF(a.alamat_dom,''),a.alamat) as alamat_dom,
        COALESCE(NULLIF(a.rt_dom,''),a.rt) as rt_dom,
        COALESCE(NULLIF(a.rw_dom,''),a.rw) as rw_dom,
        COALESCE(NULLIF(a.kecamatan_dom,''),a.kecamatan) as kecamatan_dom,
        COALESCE(NULLIF(a.kelurahan_dom,''),a.kelurahan) as kelurahan_dom,
        b.nama_soth,
        b.nama_ibu,
        EXTRACT(YEAR FROM AGE(NOW(),a.tgl_Lahir)) as usia,
        b.alamat_soth
      ")
      ->whereRaw("(a.is_aktif_kependudukan IS TRUE AND a.status_keberadaan='ADA')");

    return DataTables::queryBuilder($data_penduduk)
      ->addIndexColumn()
      ->addColumn('nik_baru', function ($i) {
        return '<a href="' . route('bunda-dashboard_asw-detail_informasi', ['nik' => $i->nik]) . '" target="_blank">' . $i->nik . '</a>';
      })
      ->rawColumns(['status_sekolah', 'detail_catatan_tidak_sekolah', 'nik_baru'])
      ->make(true);
  }

  function tanpa_intervensi(Request $request)
  {
    $anomali = $this->anomali_nama($request);
    $kecamatan = $request->kecamatan ?? 'All';
    $kecamatan_kode = $request->kecamatan_kode ?? '';
    $kelurahan = $request->kelurahan ?? 'All';
    $kelurahan_kode = $request->kelurahan_kode ?? '';
    $jenis = $request->jenis ?? '';
    $judul = 'Data Penduduk Usia 0-6 tahun yang Belum ada riwayat intervensi kesehatan ';
    if ($kelurahan != 'All') {
      $judul .= ' Kecamatan ' . $kecamatan;
      $judul .= ', Kelurahan ' . $kelurahan;
    } else if ($kecamatan != 'All') {
      $judul .= ' Kecamatan ' . $kecamatan;
    } else {
      $judul .= ' Kota Surabaya ';
    }

    $tingkat = Auth::getUser()->tingkat;

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

    $data_penduduk = DB::connection('db_cutt_off')->table('public.penduduk_inject as a')
      ->leftJoin('public.detail_timbang as dt', 'dt.nik', 'a.nik')
      ->leftJoin('public.detail_imunisasi as di', 'di.nik', 'a.nik')
      ->leftJoin('public.history_imunisasi as hi', 'hi.nik', 'a.nik')
      ->when(!empty($kecamatan) && $kecamatan != 'All', function ($q) use ($kecamatan, $anomali) {
        $q->whereIn('a.kecamatan_dom', $anomali['arr_kecamatan']);
      })
      ->when(!empty($kelurahan) && $kelurahan != 'All', function ($q) use ($kelurahan, $anomali) {
        $q->whereIn('a.kelurahan_dom', $anomali['arr_kelurahan']);
      })
      ->selectRaw("
        EXTRACT(YEAR FROM AGE(NOW(),a.tgl_lahir)) as usia,
        COUNT(DISTINCT a.nik)  as jumlah
      ")
      ->whereRaw("(EXTRACT(YEAR FROM AGE(now(),a.tgl_lahir)) BETWEEN 0 AND 6)")
      ->whereRaw("(a.is_aktif_kependudukan IS TRUE AND a.status_keberadaan='ADA')")
      ->whereNull('a.status_phbs')
      ->whereNull('dt.nik')
      ->whereNull('di.nik')
      ->whereNull('hi.nik')
      ->whereIn(DB::raw("COALESCE(NULLIF(a.kecamatan_dom,''), a.kecamatan)"), $arr_kec)
      ->whereIn(DB::raw("COALESCE(NULLIF(a.kelurahan_dom,''), a.kelurahan)"), $arr_kel)
      ->groupByRaw("EXTRACT(YEAR FROM AGE(NOW(),a.tgl_lahir))")
      ->orderByRaw("usia")
      ->get();

    $data = [
      'judul' => $judul,
      'data' => $data_penduduk,
      'jenis' => $jenis,
    ];
    $content = view('bunda.dashboard_asw.tanpa_intervensi', $data)->render();
    return $content;
  }

  function detail_tanpa_intervensi(Request $request)
  {
    // return $request->all();
    $anomali = $this->anomali_nama($request);
    $kategori = $request->kategori; //"total"
    $kelamin = $request->kelamin; //null
    $menu = $request->menu; //"cekin_warga"
    $kecamatan = $request->kecamatan;
    $kelurahan = $request->kelurahan;
    $usia = $request->usia;

    $tingkat = Auth::getUser()->tingkat;

    // 1. Ambil data master kecamatan & kelurahan
    $dt_kecamatan = DB::table('kecamatan as kec')
      ->join('kelurahan as kel', 'kel.kecamatan_kode', '=', 'kec.kecamatan_kode')
      ->selectRaw("
              kec.kecamatan_dispenduk,
              kec.dispenduk_baru as kec_baru,
              kel.kelurahan_dispenduk,
              kel.dispenduk_baru as kel_baru
          ")
      ->when($kecamatan != 'All', function ($w) use ($kecamatan) {
        return $w->where('kec.kecamatan_dispenduk', $kecamatan);
      })
      ->when($kelurahan != 'All', function ($w) use ($kelurahan) {
        return $w->where('kel.kelurahan_dispenduk', $kelurahan);
      })
      ->get();

    // Kumpulkan seluruh nama kecamatan & kelurahan (lama & baru) untuk filter whereIn tunggal
    $arr_kec = $dt_kecamatan->pluck('kecamatan_dispenduk')
      ->merge($dt_kecamatan->pluck('kec_baru'))
      ->filter()->unique()->values()->toArray();

    $arr_kel = $dt_kecamatan->pluck('kelurahan_dispenduk')
      ->merge($dt_kecamatan->pluck('kel_baru'))
      ->filter()->unique()->values()->toArray();

    $data_penduduk = DB::connection('db_cutt_off')
      ->table('public.penduduk_inject as a')
      ->leftJoin('public.detail_timbang as dt', 'dt.nik', 'a.nik')
      ->leftJoin('public.detail_imunisasi as di', 'di.nik', 'a.nik')
      ->leftJoin('public.history_imunisasi as hi', 'hi.nik', 'a.nik')
      ->when(!empty($kecamatan) && $kecamatan != 'All', function ($q) use ($kecamatan, $anomali) {
        $q->whereIn('a.kecamatan_dom', $anomali['arr_kecamatan']);
      })
      ->when(!empty($kelurahan) && $kelurahan != 'All', function ($q) use ($kelurahan, $anomali) {
        $q->whereIn('a.kelurahan_dom', $anomali['arr_kelurahan']);
      })
      ->when($usia !== null, function ($w) use ($usia) {
        if ($usia === 'semua') {
          $w->whereRaw("EXTRACT(YEAR FROM AGE(NOW(), a.tgl_lahir)) BETWEEN 0 AND 6");
        } elseif (is_numeric($usia)) {
          $w->whereRaw("EXTRACT(YEAR FROM AGE(NOW(), a.tgl_lahir)) = ?", [$usia]);
        }
      })
      ->selectRaw("
        a.nik,
        a.nama_lengkap,
        a.jenis_kelamin,
        EXTRACT(YEAR FROM AGE(now(),a.tgl_lahir)) as usia,
        a.alamat,
        a.rt,
        a.rw,
        a.kelurahan,
        a.kecamatan,
        COALESCE(NULLIF(a.alamat_dom,''),a.alamat) as alamat_dom,
        COALESCE(NULLIF(a.rt_dom,''),a.rt) as rt_dom,
        COALESCE(NULLIF(a.rw_dom,''),a.rw) as rw_dom,
        COALESCE(NULLIF(a.kelurahan_dom,''),a.kelurahan) as kelurahan_dom,
        COALESCE(NULLIF(a.kecamatan_dom,''),a.kecamatan) as kecamatan_dom
      ")
      ->whereRaw("(EXTRACT(YEAR FROM AGE(now(),a.tgl_lahir)) BETWEEN 0 AND 6)")
      ->whereRaw("(a.is_aktif_kependudukan IS TRUE AND a.status_keberadaan='ADA')")
      ->whereNull('a.status_phbs')
      ->whereNull('dt.nik')
      ->whereNull('di.nik')
      ->whereNull('hi.nik')
      ->whereIn(DB::raw("COALESCE(NULLIF(a.kecamatan_dom,''), a.kecamatan)"), $arr_kec)
      ->whereIn(DB::raw("COALESCE(NULLIF(a.kelurahan_dom,''), a.kelurahan)"), $arr_kel);

    return DataTables::queryBuilder($data_penduduk)
      ->addIndexColumn()
      ->addColumn('nik_baru', function ($i) {
        return '<a href="' . route('bunda-dashboard_asw-detail_informasi', ['nik' => $i->nik]) . '" target="_blank">' . $i->nik . '</a>';
      })
      ->rawColumns(['status_sekolah', 'detail_catatan_tidak_sekolah', 'nik_baru'])
      ->make(true);
  }

  function phbs(Request $request)
  {
    $anomali = $this->anomali_nama($request);
    $kecamatan = $request->kecamatan ?? 'All';
    $kecamatan_kode = $request->kecamatan_kode ?? '';
    $kelurahan = $request->kelurahan ?? 'All';
    $kelurahan_kode = $request->kelurahan_kode ?? '';
    $jenis = $request->jenis ?? '';
    $judul = 'Data Penduduk Usia 0-6 tahun yang melaksanakan PHBS ';
    if ($kelurahan != 'All') {
      $judul .= ' Kecamatan ' . $kecamatan;
      $judul .= ', Kelurahan ' . $kelurahan;
    } else if ($kecamatan != 'All') {
      $judul .= ' Kecamatan ' . $kecamatan;
    } else {
      $judul .= ' Kota Surabaya ';
    }

    $tingkat = Auth::getUser()->tingkat;

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

    $data_penduduk = DB::connection('db_cutt_off')->table('public.penduduk_inject as a')
      ->when(!empty($kecamatan) && $kecamatan != 'All', function ($q) use ($kecamatan, $anomali) {
        $q->whereIn('a.kecamatan_dom', $anomali['arr_kecamatan']);
      })
      ->when(!empty($kelurahan) && $kelurahan != 'All', function ($q) use ($kelurahan, $anomali) {
        $q->whereIn('a.kelurahan_dom', $anomali['arr_kelurahan']);
      })
      ->selectRaw("
        EXTRACT(YEAR FROM AGE(NOW(),a.tgl_lahir)) as usia,
        COUNT(*) FILTER(WHERE COALESCE(a.status_phbs,'')='Perilaku Sehat') as jumlah_sehat,
        COUNT(*) FILTER(WHERE COALESCE(a.status_phbs,'')='Perilaku Tidak Sehat') as jumlah_tidak_sehat,
        COUNT(*) FILTER(WHERE COALESCE(a.status_phbs,'') NOT IN ('Perilaku Sehat','Perilaku Tidak Sehat')) as jumlah_lain
      ")
      ->whereRaw("(EXTRACT(YEAR FROM AGE(now(),a.tgl_lahir)) BETWEEN 0 AND 6)")
      ->whereRaw("(a.is_aktif_kependudukan IS TRUE AND a.status_keberadaan='ADA')")
      ->whereIn(DB::raw("COALESCE(NULLIF(a.kecamatan_dom,''), a.kecamatan)"), $arr_kec)
      ->whereIn(DB::raw("COALESCE(NULLIF(a.kelurahan_dom,''), a.kelurahan)"), $arr_kel)
      ->groupByRaw("EXTRACT(YEAR FROM AGE(NOW(),a.tgl_lahir))")
      ->orderByRaw("usia")
      ->get();

    $data = [
      'judul' => $judul,
      'data' => $data_penduduk,
      'jenis' => $jenis,
    ];
    $content = view('bunda.dashboard_asw.phbs', $data)->render();
    return $content;
  }

  function detail_phbs(Request $request)
  {
    // return $request->all();
    $anomali = $this->anomali_nama($request);
    $kategori = $request->kategori; //"total"
    $kelamin = $request->kelamin; //null
    $menu = $request->menu; //"cekin_warga"
    $kecamatan = $request->kecamatan;
    $kelurahan = $request->kelurahan;
    $usia = $request->usia;

    $tingkat = Auth::getUser()->tingkat;

    // 1. Ambil data master kecamatan & kelurahan
    $dt_kecamatan = DB::table('kecamatan as kec')
      ->join('kelurahan as kel', 'kel.kecamatan_kode', '=', 'kec.kecamatan_kode')
      ->selectRaw("
              kec.kecamatan_dispenduk,
              kec.dispenduk_baru as kec_baru,
              kel.kelurahan_dispenduk,
              kel.dispenduk_baru as kel_baru
          ")
      ->when($kecamatan != 'All', function ($w) use ($kecamatan) {
        return $w->where('kec.kecamatan_dispenduk', $kecamatan);
      })
      ->when($kelurahan != 'All', function ($w) use ($kelurahan) {
        return $w->where('kel.kelurahan_dispenduk', $kelurahan);
      })
      ->get();

    // Kumpulkan seluruh nama kecamatan & kelurahan (lama & baru) untuk filter whereIn tunggal
    $arr_kec = $dt_kecamatan->pluck('kecamatan_dispenduk')
      ->merge($dt_kecamatan->pluck('kec_baru'))
      ->filter()->unique()->values()->toArray();

    $arr_kel = $dt_kecamatan->pluck('kelurahan_dispenduk')
      ->merge($dt_kecamatan->pluck('kel_baru'))
      ->filter()->unique()->values()->toArray();

    $data_penduduk = DB::connection('db_cutt_off')
      ->table('public.penduduk_inject as a')
      ->when(!empty($kecamatan) && $kecamatan != 'All', function ($q) use ($kecamatan, $anomali) {
        $q->whereIn('a.kecamatan_dom', $anomali['arr_kecamatan']);
      })
      ->when(!empty($kelurahan) && $kelurahan != 'All', function ($q) use ($kelurahan, $anomali) {
        $q->whereIn('a.kelurahan_dom', $anomali['arr_kelurahan']);
      })
      ->when($usia !== null, function ($w) use ($usia) {
        if ($usia === 'semua') {
          $w->whereRaw("EXTRACT(YEAR FROM AGE(NOW(), a.tgl_lahir)) BETWEEN 0 AND 6");
        } elseif (is_numeric($usia)) {
          $w->whereRaw("EXTRACT(YEAR FROM AGE(NOW(), a.tgl_lahir)) = ?", [$usia]);
        }
      })
      ->selectRaw("
        a.nik,
        a.nama_lengkap,
        a.jenis_kelamin,
        EXTRACT(YEAR FROM AGE(now(),a.tgl_lahir)) as usia,
        a.alamat,
        a.rt,
        a.rw,
        a.kelurahan,
        a.kecamatan,
        COALESCE(NULLIF(a.alamat_dom,''),a.alamat) as alamat_dom,
        COALESCE(NULLIF(a.rt_dom,''),a.rt) as rt_dom,
        COALESCE(NULLIF(a.rw_dom,''),a.rw) as rw_dom,
        COALESCE(NULLIF(a.kelurahan_dom,''),a.kelurahan) as kelurahan_dom,
        COALESCE(NULLIF(a.kecamatan_dom,''),a.kecamatan) as kecamatan_dom,
        COALESCE(a.status_phbs,'') as status_phbs
      ")
      ->whereRaw("(EXTRACT(YEAR FROM AGE(now(),a.tgl_lahir)) BETWEEN 0 AND 6)")
      ->whereRaw("(a.is_aktif_kependudukan IS TRUE AND a.status_keberadaan='ADA')")
      ->when($kategori !== null, function ($w) use ($kategori) {
        switch ($kategori) {
          case 'jumlah_sehat':
            return $w->where(DB::raw("COALESCE(a.status_phbs,'')"), 'Perilaku Sehat');
            break;

          case 'jumlah_tidak_sehat':
            return $w->where(DB::raw("COALESCE(a.status_phbs,'')"), 'Perilaku Tidak Sehat');
            break;

          case 'jumlah_lain':
            return $w->whereNotIn(DB::raw("COALESCE(a.status_phbs,'')"), ['Perilaku Sehat', 'Perilaku Tidak Sehat']);
            break;

          default:
            break;
        }
      })
      ->whereIn(DB::raw("COALESCE(NULLIF(a.kecamatan_dom,''), a.kecamatan)"), $arr_kec)
      ->whereIn(DB::raw("COALESCE(NULLIF(a.kelurahan_dom,''), a.kelurahan)"), $arr_kel);

    return DataTables::queryBuilder($data_penduduk)
      ->addIndexColumn()
      ->addColumn('nik_baru', function ($i) {
        return '<a href="' . route('bunda-dashboard_asw-detail_informasi', ['nik' => $i->nik]) . '" target="_blank">' . $i->nik . '</a>';
      })
      ->rawColumns(['status_sekolah', 'detail_catatan_tidak_sekolah', 'nik_baru'])
      ->make(true);
  }
}
