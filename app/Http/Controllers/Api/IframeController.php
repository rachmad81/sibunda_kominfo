<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use DB, DataTables;

class IframeController extends Controller
{
  function anomali(Request $request)
  {
    $kecamatan = strtoupper($request->kecamatan);
    $kelurahan = strtoupper($request->kelurahan);
    $hasil = [];

    if ($kecamatan != '') {
      $data_kecamatan = DB::table('public.kecamatan')
        ->selectRaw("kecamatan_dispenduk,dispenduk_baru as kec_baru")
        ->where('kecamatan_dispenduk', $kecamatan)->first();
      $hasil = [
        'kecamatan' => [$data_kecamatan->kecamatan_dispenduk, $data_kecamatan->kec_baru],
      ];
    }
    if ($kelurahan != '') {
      $data_kelurahan = DB::table('public.kelurahan as kel')
        ->join('public.kecamatan as kec', 'kec.kecamatan_kode', 'kel.kecamatan_kode')
        ->selectRaw("kec.kecamatan_dispenduk,kec.dispenduk_baru as kec_baru,kel.kelurahan_dispenduk,kel.dispenduk_baru as kel_baru")
        ->where('kelurahan_dispenduk', $kelurahan)->first();
      $hasil = [
        'kecamatan' => [$data_kelurahan->kecamatan_dispenduk, $data_kelurahan->kec_baru],
        'kelurahan' => [$data_kelurahan->kelurahan_dispenduk, $data_kelurahan->kel_baru],
      ];
    }

    return $hasil;
  }

  function penduduk(Request $request)
  {
    $kecamatan = strtoupper($request->kecamatan);
    $kelurahan = strtoupper($request->kelurahan);

    if ($kelurahan != '') {
      if ($kecamatan == '') {
        return 'Kecamatan harus diisi';
      }
    }

    $anomali = $this->anomali($request);

    $penduduk = DB::connection('db_cutt_off')->table('public.penduduk_inject as a')
      ->leftJoin("public.survey_bunda as b", 'b.nik', 'a.nik')
      ->selectRaw("
        EXTRACT(YEAR FROM AGE(NOW(),a.tgl_lahir)) as usia,
        COUNT(*) as jumlah_total,
        COUNT(*) FILTER (WHERE
          CASE WHEN COALESCE(b.nik,'')<>''
          THEN COALESCE(b.status_sekolah,'')='Sudah' OR (COALESCE(a.nama_sekolah,'')<>'' AND a.is_aktif_pendidikan is true)
          ELSE (COALESCE(a.nama_sekolah,'')<>'' AND a.is_aktif_pendidikan is true)
          END
        ) as jumlah_bersekolah,
        COUNT(*) FILTER (WHERE
          CASE WHEN COALESCE(b.nik,'')<>''
          THEN COALESCE(b.status_sekolah,'')<>'Sudah' AND (COALESCE(a.nama_sekolah,'')='' OR a.is_aktif_pendidikan is not true)
          ELSE (COALESCE(a.nama_sekolah,'')='' OR a.is_aktif_pendidikan is not true)
          END
        ) as jumlah_belum_bersekolah,
        COUNT(*) FILTER (WHERE
          CASE WHEN COALESCE(b.nik,'')<>''
          THEN COALESCE(b.status_sekolah,'')<>'Sudah' AND (COALESCE(a.nama_sekolah,'')='' OR a.is_aktif_pendidikan is not true)
          ELSE (COALESCE(a.nama_sekolah,'')='' OR a.is_aktif_pendidikan is not true)
          END AND (COALESCE(b.nik,'') <> '' AND COALESCE(b.status_verifikasi,'')='Selesai')
        ) as jumlah_belum_bersekolah_sudah_survey,

        COUNT(*) FILTER (WHERE
          CASE WHEN COALESCE(b.nik,'')<>''
          THEN COALESCE(b.status_sekolah,'')<>'Sudah' AND (COALESCE(a.nama_sekolah,'')='' OR a.is_aktif_pendidikan is not true)
          ELSE (COALESCE(a.nama_sekolah,'')='' OR a.is_aktif_pendidikan is not true)
          END AND (COALESCE(b.nik,'') = '' OR COALESCE(b.status_verifikasi,'')<>'Selesai')
        ) as jumlah_belum_bersekolah_belum_survey
      ")
      ->whereRaw("(EXTRACT(YEAR FROM AGE(NOW(),a.tgl_lahir)) BETWEEN 0 AND 6)")
      ->when($kecamatan !== '', function ($w) use ($anomali) {
        $w->whereIn('a.kecamatan_dom', $anomali['kecamatan']);
      })
      ->when($kelurahan !== '', function ($w) use ($anomali) {
        $w->whereIn('a.kelurahan_dom', $anomali['kelurahan']);
      })
      ->groupByRaw("EXTRACT(YEAR FROM AGE(NOW(),a.tgl_lahir))")
      ->get();

    $total = [
      'usia' => 'Total',
      'jumlah_total' => collect($penduduk)->sum('jumlah_total'),
      'jumlah_bersekolah' => collect($penduduk)->sum('jumlah_bersekolah'),
      'jumlah_belum_bersekolah' => collect($penduduk)->sum('jumlah_belum_bersekolah'),
      'jumlah_belum_bersekolah_sudah_survey' => collect($penduduk)->sum('jumlah_belum_bersekolah_sudah_survey'),
      'jumlah_belum_bersekolah_belum_survey' => collect($penduduk)->sum('jumlah_belum_bersekolah_belum_survey'),
    ];

    $penduduk = collect($penduduk)->push((object)$total);

    $data = [
      'penduduk' => $penduduk,
    ];
    return view('iframe.penduduk', $data);
  }

  function detail_penduduk(Request $request)
  {
    $usia = $request->usia;
    $kategori = $request->kategori;
    $kecamatan = strtoupper($request->kecamatan);
    $kelurahan = strtoupper($request->kelurahan);

    if ($kelurahan != '') {
      if ($kecamatan == '') {
        return 'Kecamatan harus diisi';
      }
    }

    if ($usia == '' || $kategori == '') {
      return 'Mohon lengkapi isian usia dan kategorinya';
    }
    $data = $request->all();
    return view('iframe.detail_penduduk', $data);
  }

  function get_detail_penduduk(Request $request)
  {
    $kecamatan = strtoupper($request->kecamatan);
    $kelurahan = strtoupper($request->kelurahan);
    $usia = $request->usia;
    $kategori = $request->kategori;

    if ($kelurahan != '') {
      if ($kecamatan == '') {
        return 'Kecamatan harus diisi';
      }
    }

    $anomali = $this->anomali($request);

    $penduduk = DB::connection('db_cutt_off')->table('public.penduduk_inject as a')
      ->leftJoin("public.survey_bunda as b", 'b.nik', 'a.nik')
      ->selectRaw("
        a.nama_lengkap,
        a.nik,
        a.tgl_lahir,
        EXTRACT(YEAR FROM AGE(NOW(),a.tgl_lahir)) as usia,
        a.alamat,a.kecamatan,a.kelurahan,
        a.alamat_dom,a.kecamatan_dom,a.kelurahan_dom,
        a.is_aktif_pendidikan,
        COALESCE(a.nama_sekolah,'') as jenjang_sekolah,
        COALESCE(a.nama_sekolah,'') as nama_sekolah,
        COALESCE(b.status_sekolah,'') as status_sekolah,
        COALESCE(b.status_alamat,'') as status_alamat,
        COALESCE(b.detail_sekolah,'') as detail_sekolah,
        COALESCE(b.detail_alamat,'') as detail_alamat,
        COALESCE(b.detail_tidak_sekolah,'') as detail_tidak_sekolah,
        COALESCE(b.pilihan_detail_tidak_sekolah,'') as pilihan_detail_tidak_sekolah,
        COALESCE(b.status_verifikasi,'') as status_verifikasi
      ")
      ->when($kecamatan !== '', function ($w) use ($anomali) {
        $w->whereIn('a.kecamatan_dom', $anomali['kecamatan']);
      })
      ->when($kelurahan !== '', function ($w) use ($anomali) {
        $w->whereIn('a.kelurahan_dom', $anomali['kelurahan']);
      })
      ->when($usia !== '', function ($w) use ($usia) {
        if ($usia != 'semua') {
          $w->whereRaw("(EXTRACT(YEAR FROM AGE(NOW(),a.tgl_lahir))=$usia)");
        } else {
          $w->whereRaw("(EXTRACT(YEAR FROM AGE(NOW(),a.tgl_lahir)) BETWEEN 0 AND 6)");
        }
      })
      ->when($kategori !== '', function ($w) use ($kategori) {
        if ($kategori == 'bersekolah') {
          $w->whereRaw("(
            CASE WHEN COALESCE(b.nik,'')<>''
            THEN COALESCE(b.status_sekolah,'')='Sudah' OR (COALESCE(a.nama_sekolah,'')<>'' AND a.is_aktif_pendidikan is true)
            ELSE (COALESCE(a.nama_sekolah,'')<>'' AND a.is_aktif_pendidikan is true)
            END
          )");
        } else if ($kategori == 'belum_bersekolah') {
          $w->whereRaw("(
            CASE WHEN COALESCE(b.nik,'')<>''
            THEN COALESCE(b.status_sekolah,'')<>'Sudah' AND (COALESCE(a.nama_sekolah,'')='' OR a.is_aktif_pendidikan is not true)
            ELSE (COALESCE(a.nama_sekolah,'')='' OR a.is_aktif_pendidikan is not true)
            END
          )");
        } else if ($kategori == 'sudah_disurvey') {
          $w->whereRaw("(
            CASE WHEN COALESCE(b.nik,'')<>''
            THEN COALESCE(b.status_sekolah,'')<>'Sudah' AND (COALESCE(a.nama_sekolah,'')='' OR a.is_aktif_pendidikan is not true)
            ELSE (COALESCE(a.nama_sekolah,'')='' OR a.is_aktif_pendidikan is not true)
            END AND (COALESCE(b.nik,'') <> '' AND COALESCE(b.status_verifikasi,'')='Selesai')
          )");
        } else if ($kategori == 'belum_disurvey') {
          $w->whereRaw("(
            CASE WHEN COALESCE(b.nik,'')<>''
            THEN COALESCE(b.status_sekolah,'')<>'Sudah' AND (COALESCE(a.nama_sekolah,'')='' OR a.is_aktif_pendidikan is not true)
            ELSE (COALESCE(a.nama_sekolah,'')='' OR a.is_aktif_pendidikan is not true)
            END AND (COALESCE(b.nik,'') = '' OR COALESCE(b.status_verifikasi,'')<>'Selesai')
          )");
        } else if ($kategori == 'total') {
        }
      });

    return DataTables::queryBuilder($penduduk)
      ->addColumn('status_bersekolah', function ($a) {
        if ($a->status_sekolah == 'Sudah' || ($a->jenjang_sekolah != '' and $a->is_aktif_pendidikan == true)) {
          return 'Sudah Bersekolah';
        } else {
          return 'Belum Bersekolah';
        }
      })
      ->addColumn('catatan', function ($a) {
        $hasil = '';
        if ($a->detail_sekolah != '') {
          $hasil .= '<b>Status Sekolah</b><br>' . $a->detail_sekolah . '<br><br>';
        }
        if ($a->detail_alamat != '') {
          $hasil .= '<b>Status Alamat</b><br>' . $a->detail_alamat . '<br><br>';
        }
        if ($a->detail_tidak_sekolah != '') {
          $hasil .= '<b>Catatan </b><br><b>' . $a->detail_tidak_sekolah . '</b><br>';
          if ($a->pilihan_detail_tidak_sekolah != '') {
            $hasil .= $a->pilihan_detail_tidak_sekolah . '<br>';
          }
        }
        return $hasil;
      })
      ->rawColumns(['catatan'])
      ->make(true);
  }
}
