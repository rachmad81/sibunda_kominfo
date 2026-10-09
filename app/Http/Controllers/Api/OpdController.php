<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

class OpdController extends Controller
{

  public function master_api(Request $request)
  {
    $aksi = $request->aksi;
    switch ($aksi) {
      case 'get_kecamatan':
        return $this->get_kecamatan($request);
        break;
      case 'get_kelurahan':
        return $this->get_kelurahan($request);
        break;
      case 'get_data':
        return $this->get_data($request);
        break;
      case 'get_survey_bunda':
        return $this->get_survey($request);
        break;
      case 'api_sibunda':
        return $this->api_sibunda($request);
        break;
      case 'api_sibunda_v1':
        return $this->api_sibunda_v1($request);
        break;
      case 'api_rekap_anak':
        return $this->rekap_anak($request);
        break;
      case 'siswa_paud':
        return $this->siswa_paud($request);
        break;

      default:
        return ['code' => '404', 'pesan' => '404 - Halaman tidak ditemukan'];
        break;
    }
  }

  function get_kecamatan(Request $request)
  {
    $kecamatan = DB::connection('pgsql_paud')->table('public.kecamatan')
      ->selectRaw("kecamatan_dispenduk as kecamatan")
      ->orderBy('kecamatan', 'ASC')
      ->get();
    $kecamatan = collect($kecamatan)->map(function ($r) {
      return $r->kecamatan;
    })->toArray();

    return ['code' => 200, 'kecamatan' => $kecamatan];
  }

  function get_kelurahan(Request $request)
  {
    try {
      $kecamatan = $request->kecamatan;
      $kecamatan = DB::connection('pgsql_paud')->table('public.kecamatan')
        ->selectRaw("kecamatan_kode,kecamatan_dispenduk as kecamatan")
        ->where('kecamatan_dispenduk', $kecamatan)
        ->first();
      $kelurahan = DB::connection('pgsql_paud')->table('public.kelurahan')
        ->selectRaw("kelurahan_kode,kelurahan_dispenduk as kelurahan")
        ->where('kecamatan_kode', $kecamatan->kecamatan_kode)
        ->get();
      $kelurahan = collect($kelurahan)->map(function ($q) {
        return $q->kelurahan;
      })->toArray();

      $return = [
        'code' => 200,
        'kecamatan' => $kecamatan->kecamatan,
        'kelurahan' => $kelurahan,
      ];
    } catch (\Throwable $th) {
      $return = ['code' => 250, 'message' => 'Periksa kembali isian parameter kamu'];
    }
    return $return;
  }

  function get_data(Request $request)
  {
    $limit = $request->limit ?? '';
    $jenis = $request->jenis ?? '';
    $kelurahan = $request->kelurahan ?? '';
    $kecamatan = $request->kecamatan ?? '';
    $page = $request->page ?? 1;
    $nik = $request->nik ?? '';
    $tgl_update = $request->tgl_update ?? '';

    $dinsos = DB::connection('db_cutt_off')->table('public.penduduk_intervensi')->selectRaw("nik")->groupByRaw("nik")->havingRaw("COUNT(*)>1");

    $paginator = DB::connection('db_cutt_off')->table('public.penduduk_inject as a')
      ->leftjoin('public.survey_bunda as b', 'b.nik', 'a.nik')
      ->leftjoin('public.penduduk_intervensi as c', 'c.nik', 'a.nik')
      ->when($kecamatan != '', function ($q) use ($kecamatan) {
        return $q->where('a.kecamatan', $kecamatan);
      })
      ->when($kelurahan != '', function ($q) use ($kelurahan) {
        return $q->where('a.kelurahan', $kelurahan);
      })
      ->when($nik != '', function ($q) use ($nik) {
        return $q->where('a.nik', $nik);
      })
      ->when($tgl_update != '', function ($q) use ($tgl_update) {
        return $q->where('a.updated_at', $tgl_update);
      })
      ->selectRaw("
      --CEKIN WARGA
      a.nik,a.nama_sekolah,a.status_sekolah,a.nama_sekolah,a.npsn_sekolah,

      --SURVEY BUNDA
      b.nik as nik_sibunda,b.status as sibunda_status_profil_sekolah,b.status_alamat,b.detail_alamat,b.status_sekolah as sibunda_status_sekolah,b.detail_sekolah,b.detail_tidak_sekolah,b.pilihan_detail_tidak_sekolah,b.status_verifikasi,b.status_gamis,b.tandai_tidak_sekolah,b.bukti_survei,

      --DINSOS
      c.nik as nik_dinsos,c.jenis_bantuan,c.ket_bantuan,c.tahun_anggaran
      ")
      ->whereRaw("EXTRACT(YEAR FROM AGE(now(),a.tgl_Lahir)) BETWEEN 5 AND 6")
      ->whereIn('a.nik', $dinsos)
      ->paginate($limit);

    $collection = collect($paginator->items());
    $data = $collection->groupBy('nik')
      ->map(function ($items, $nik) {
        $first = $items->first();

        return [
          'nik' => $nik,

          'data_cekin' => [
            'nik'             => $first->nik,
            'jenjang_sekolah' => $first->jenjang_sekolah,
            'status_sekolah'  => $first->status_sekolah,
            'nama_sekolah'    => $first->nama_sekolah,
            'npsn_sekolah'    => $first->npsn_sekolah,
          ],

          'data_sibunda' => $first->nik_sibunda ? (object) [
            'status' => $first->sibunda_status_profil_sekolah,
            'status_alamat' => $first->status_alamat,
            'detail_alamat' => $first->detail_alamat,
            'status_sekolah' => $first->sibunda_status_sekolah,
            'detail_sekolah' => $first->detail_sekolah,
            'detail_tidak_sekolah' => $first->detail_tidak_sekolah,
            'pilihan_detail_tidak_sekolah' => $first->pilihan_detail_tidak_sekolah,
            'status_verifikasi' => $first->status_verifikasi,
            'status_gamis' => $first->status_gamis,
            'tandai_tidak_sekolah' => $first->tandai_tidak_sekolah,
            'bukti_survei' => $first->bukti_survei,
          ] : (object)[],

          'data_dinsos' => $first->nik_dinsos ? $items->map(function ($row) {
            return [
              'jenis_bantuan' => $row->jenis_bantuan,
              'ket_bantuan' => $row->ket_bantuan,
              'tahun_anggaran' => $row->tahun_anggaran
            ];
          })->values() : [],
        ];
      })->values();

    $next_url = ($page >= $paginator->lastPage()) ? null : url()->current() . '?page=' . ($page + 1);
    $prev_url = ($page <= 1) ? null : url()->current() . '?page=' . ($page - 1);
    $links = $this->buildPaginationLinks($page, $paginator->lastPage(), url()->current());

    return response()->json([
      'current_page' => $page,
      'data' => $data,
      'first_page_url' => url()->current() . '?page=1',
      'from' => ($page * $limit) - ($limit - 1),
      'last_page' => $paginator->lastPage(),
      'last_page_url' => url()->current() . '?page=' . $paginator->lastPage(),
      'links' => $links,
      'next_page_url' => $next_url,
      'path' => url()->current(),
      'per_page' => $limit,
      'prev_page_url' => $prev_url,
      'to' => ($page * $limit),
      'total' => $paginator->total(),
    ]);
  }

  function get_survey(Request $request)
  {
    try {
      $limit = $request->limit ?? '';
      $jenis = $request->jenis ?? '';
      $kelurahan = $request->kelurahan ?? '';
      $kecamatan = $request->kecamatan ?? '';
      $page = $request->page ?? 1;
      $nik = $request->nik ?? '';
      $tgl_update = $request->tgl_update ?? '';

      $sudah_sekolah = "((COALESCE(b.status_sekolah,'') IN ('Sudah','Akan sekolah')) OR (COALESCE(a.nama_sekolah,'')<>'' AND a.is_aktif_pendidikan is true))";
      $tidak_sekolah = "((COALESCE(b.status_sekolah,'') NOT IN ('Sudah','Akan sekolah')) AND (COALESCE(a.nama_sekolah,'')='' OR a.is_aktif_pendidikan is not true))";
      $alamat_sesuai = "(COALESCE(b.status_alamat,'')='Sesuai')";

      $penduduk = DB::connection('db_cutt_off')->table('public.penduduk_inject as a')
        ->join('public.survey_bunda as b', 'b.nik', 'a.nik')
        ->selectRaw("
          a.nik,a.updated_at,a.nama_sekolah,a.is_aktif_pendidikan,a.nama_sekolah,a.status_sekolah,
          b.status_verifikasi as status_survey,

          CASE WHEN $sudah_sekolah THEN 'Sudah' ELSE
          CASE WHEN $tidak_sekolah THEN 'Tidak sekolah' ELSE
          COALESCE(b.status_sekolah,'') END END as hasil_survey_status_sekolah,

          CASE WHEN $sudah_sekolah THEN '' ELSE CONCAT('(',COALESCE(b.detail_tidak_sekolah,''),') ',COALESCE(b.pilihan_detail_tidak_sekolah)) END as alasan_tidak_sekolah,

          CASE WHEN $alamat_sesuai THEN 'Sesuai' ELSE COALESCE(b.status_alamat,'') END as hasil_survey_status_alamat,
          CASE WHEN $alamat_sesuai THEN '' ELSE COALESCE(b.detail_alamat,'') END as alasan_tidak_sesuai_alamat,
          COALESCE(b.bukti_survei,'') as bukti_survey
          ")
        ->when($kecamatan != '', function ($w) use ($kecamatan) {
          return $w->where('a.kecamatan', $kecamatan);
        })
        ->when($kelurahan != '', function ($w) use ($kelurahan) {
          return $w->where('a.kelurahan', $kelurahan);
        })
        ->whereRaw("(EXTRACT(YEAR FROM AGE(NOW(),a.tgl_Lahir)) BETWEEN 5 AND 6) AND (COALESCE(a.nama_sekolah,'')='' OR a.is_aktif_pendidikan is NOT TRUE)")->paginate($limit);

      // $total = $penduduk->count();
      // $total_sudah_sekolah = collect($penduduk)->where('hasil_survey_status_sekolah', 'Sudah')->count();
      // $total_tidak_sekolah = collect($penduduk)->where('hasil_survey_status_sekolah', 'Tidak sekolah')->count();
      // $total_alamat_sesuai = collect($penduduk)->where('hasil_survey_status_alamat', 'Sesuai')->count();
      // $total_alamat_tidak_sesuai = collect($penduduk)->where('hasil_survey_status_alamat', '<>', 'Sesuai')->count();
      // $data = [
      //   'total' => $total,
      //   'total_sekolah' => $total_sudah_sekolah,
      //   'total_tidak_sekolah' => $total_tidak_sekolah,
      //   'total_alamat_sesuai' => $total_alamat_sesuai,
      //   'total_alamat_tidak_sesuai' => $total_alamat_tidak_sesuai,
      // ];
      // return $data;

      return $penduduk;
    } catch (\Throwable $th) {
      throw $th;
    }
  }

  function buildPaginationLinks($currentPage, $lastPage, $baseUrl)
  {
    $links = [];

    // =====================
    // PREVIOUS
    // =====================
    $links[] = [
      'url' => $currentPage > 1 ? $baseUrl . '?page=' . ($currentPage - 1) : null,
      'label' => '&laquo; Previous',
      'active' => false,
      'disabled' => $currentPage == 1
    ];

    $pages = collect();

    // =====================
    // 1. Awal (1,2,3)
    // =====================
    for ($i = 1; $i <= min(3, $lastPage); $i++) {
      $pages->push($i);
    }

    // =====================
    // 2. Tengah (current-2 ... current+2)
    // =====================
    for ($i = $currentPage - 2; $i <= $currentPage + 2; $i++) {
      if ($i > 0 && $i <= $lastPage) {
        $pages->push($i);
      }
    }

    // =====================
    // 3. Akhir (last-2,last-1,last)
    // =====================
    for ($i = max($lastPage - 2, 1); $i <= $lastPage; $i++) {
      $pages->push($i);
    }

    // =====================
    // Bersihkan & urutkan
    // =====================
    $pages = $pages->unique()->sort()->values();

    // =====================
    // Sisipkan ...
    // =====================
    $last = null;
    foreach ($pages as $page) {
      if ($last !== null && $page - $last > 1) {
        $links[] = [
          'url' => null,
          'label' => '...',
          'active' => false,
          'disabled' => true
        ];
      }

      $links[] = [
        'url' => $baseUrl . '?page=' . $page,
        'label' => $page,
        'active' => $page == $currentPage,
        'disabled' => false
      ];

      $last = $page;
    }

    // =====================
    // NEXT
    // =====================
    $links[] = [
      'url' => $currentPage < $lastPage ? $baseUrl . '?page=' . ($currentPage + 1) : null,
      'label' => 'Next &raquo;',
      'active' => false,
      'disabled' => $currentPage == $lastPage
    ];

    return $links;
  }

  function api_sibunda(Request $request)
  {
    try {
      //code...
      $limit = $request->limit; //200,
      $kecamatan = strtoupper($request->kecamatan) ?? ''; //"KARANG PILANG",
      $kelurahan = strtoupper($request->kelurahan) ?? ''; //null,
      $nik = $request->nik; //null,
      $tgl_update = $request->tgl_update; //null

      if ($limit < 1 || $limit > 1000) {
        return ['code' => 400, 'pesan' => 'Batas limit antara 1-1000'];
      }

      if ($kecamatan <> '') {
        $cari_kecamatan = DB::connection('pgsql_paud')->table('public.kecamatan')->where(DB::raw("UPPER(kecamatan_dispenduk)"), $kecamatan)->first();
        if (empty($cari_kecamatan)) {
          return ['code' => '404', 'pesan' => 'Nama Kecamatan tidak sesuai dukcapil'];
        }
      }
      if ($kelurahan <> '') {
        $cari_kelurahan = DB::connection('pgsql_paud')->table('public.kelurahan')->where(DB::raw("UPPER(kelurahan_dispenduk)"), $kelurahan)->first();
        if (empty($cari_kelurahan)) {
          return ['code' => '404', 'pesan' => 'Nama Kelurahan tidak sesuai dukcapil'];
        }
      }

      $data_sekolah = DB::connection('pgsql_paud')->table('public.sekolah as s')
        ->join('public.master_user as u', 'u.username', 's.npsn')
        ->join('public.kecamatan as kec', 'kec.kecamatan_kode', 's.kec_id')
        ->selectRaw("s.npsn,s.nama,s.kec_id,kec.kecamatan_dispenduk")
        ->where('u.user_enable', true)
        ->whereIn('s.jenjang', ['KB', 'TK', 'RA', 'SPS', 'TPA'])
        ->get();

      $data_sibunda = DB::connection('db_cutt_off')
        ->table('public.penduduk_inject as a')
        ->leftJoin('public.survey_bunda as b', 'b.nik', 'a.nik')
        ->selectRaw("
          COALESCE(a.nik,'') as nik,
          COALESCE(b.nik,'') as nik_survey,
          COALESCE(a.npsn_sekolah,'') as npsn_sekolah,
          COALESCE(a.nama_sekolah,'') as jenjang_sekolah,
          COALESCE(a.nama_sekolah,'') as nama_sekolah,
          COALESCE(a.status_sekolah,'') as status_sekolah,
          COALESCE(a.kab_kot_sekolah,'') as kabupaten_sekolah,
          COALESCE(a.provinsi_sekolah,'') as provinsi_sekolah,
          a.is_aktif_pendidikan,

          COALESCE(b.status_alamat,'') as hasil_survey_alamat,
          COALESCE(b.detail_alamat,'') as hasil_survey_alamat_deskripsi,

          COALESCE(b.status_sekolah,'') as hasil_survey_sekolah,
          COALESCE(b.detail_sekolah,'') as hasil_survey_sekolah_deskripsi,

          COALESCE(b.detail_tidak_sekolah,'') as hasil_survey_alasan_kategori_tidak_sekolah,
          COALESCE(b.pilihan_detail_tidak_sekolah,'') as hasil_survey_deskripsi_tidak_sekolah,
          b.updated_at as survey_updated_at
        ")
        ->whereRaw("(EXTRACT(YEAR FROM AGE(now(),a.tgl_lahir)) BETWEEN 5 AND 6)")
        ->where(function ($w) {
          return $w->whereNull('a.nama_sekolah')
            ->orWhere('a.is_aktif_pendidikan', '<>', true);
        })
        ->when($kecamatan != '', function ($q) use ($kecamatan) {
          return $q->where('a.kecamatan', $kecamatan);
        })
        ->when($kelurahan != '', function ($q) use ($kelurahan) {
          return $q->where('a.kelurahan', $kelurahan);
        })
        ->when($nik != '', function ($q) use ($nik) {
          return $q->where('a.nik', $nik);
        })
        ->when($tgl_update != '', function ($q) use ($tgl_update) {
          return $q->where(DB::raw("DATE(b.updated_at)"), $tgl_update)->whereNotNull('b.updated_at');
        })
        ->paginate($limit);

      $data_sibunda->setCollection(
        $data_sibunda->getCollection()->map(function ($item) use ($data_sekolah) {
          // STATUS SURVEY
          if ($item->nik_survey != '') {
            $status_survey = 'Sudah disurvey';
            if ($item->hasil_survey_sekolah == 'Sudah' or ($item->jenjang_sekolah <> '' and $item->is_aktif_pendidikan == true)) {
              $status_bersekolah = 'Bersekolah';
            } else if ($item->hasil_survey_sekolah <> 'Sudah' and ($item->jenjang_sekolah == '' or $item->is_aktif_pendidikan != true)) {
              $status_bersekolah = 'Tidak / Belum Bersekolah';
            }
          } else {
            $status_survey = 'Belum disurvey';
            if ($item->jenjang_sekolah <> '' and $item->is_aktif_pendidikan == true) {
              $status_bersekolah = 'Bersekolah';
            } else if ($item->jenjang_sekolah == '' or $item->is_aktif_pendidikan != true) {
              $status_bersekolah = 'Tidak / Belum Bersekolah';
            }
          }
          // END STATUS SURVEY

          // CARI SEKOLAH
          $kecamatan_sekolah = '';
          if ($item->npsn_sekolah <> '') {
            $sekolah = collect($data_sekolah)->where('npsn', $item->npsn_sekolah)->first();
            $kecamatan_sekolah = (!empty($sekolah)) ? $sekolah->kecamatan_dispenduk : '';
          }
          // END CARI SEKOLAH

          $data_baru = [
            'nik' => $item->nik,
            'npsn_sekolah' => $item->npsn_sekolah,
            'jenjang_sekolah' => $item->jenjang_sekolah,
            'nama_sekolah' => $item->nama_sekolah,
            'status_sekolah' => $item->status_sekolah,
            'kecamatan_sekolah' => $kecamatan_sekolah,
            'kabupaten_sekolah' => $item->kabupaten_sekolah,
            'provinsi_sekolah' => $item->provinsi_sekolah,
            'status_bersekolah' => $status_bersekolah,
            'status_survey' => $status_survey,
            'hasil_survey_alamat' => $item->hasil_survey_alamat,
            'hasil_survey_alamat_deskripsi' => $item->hasil_survey_alamat_deskripsi,
            'hasil_survey_sekolah' => $item->hasil_survey_sekolah,
            'hasil_survey_sekolah_deskripsi' => $item->hasil_survey_sekolah_deskripsi,
            'hasil_survey_alasan_kategori_tidak_sekolah' => $item->hasil_survey_alasan_kategori_tidak_sekolah,
            'hasil_survey_deskripsi_tidak_sekolah' => $item->hasil_survey_deskripsi_tidak_sekolah,
            'updated_at' => $item->survey_updated_at,
          ];
          return $data_baru;
        })
      );

      return ['code' => 200, 'pesan' => 'sukses', 'data' => $data_sibunda];
    } catch (\Exception $th) {
      return ['code' => $th->getCode(), 'pesan' => $th->getMessage()];
    }
  }

  function api_sibunda_v1(Request $request)
  {
    // return $request->all();
    $anomali = $this->anomali_nama($request);
    $limit = $request->limit; //1000,
    if ($limit > 1000) {
      return ['code' => 250, 'pesan' => 'Batas maksimal limit adlah 1000'];
    }
    $kecamatan = $request->kecamatan; //"DUKUH PAKIS",
    $kelurahan = $request->kelurahan; //"GUNUNG SARI",
    $nik = $request->nik; //"3578217112190001",
    $tgl_update = ($request->tgl_update != '') ? date('Y-m-d', strtotime($request->tgl_update)) : $request->tgl_update; //null
    $status_sekolah = $request->status_sekolah;
    $status_survey = $request->status_survey;

    try {
      //code...
      $data = DB::connection('db_cutt_off')->table('public.penduduk_inject as a')
        ->leftJoin('public.survey_bunda as b', 'b.nik', 'a.nik')
        ->selectRaw("
          COALESCE(a.nik,'') as nik,
          EXTRACT(YEAR FROM AGE(NOW(),a.tgl_Lahir)) as usia,
          COALESCE(a.nama_lengkap,'') as nama_lengkap,
          COALESCE(a.kecamatan,'') as kecamatan,
          COALESCE(a.kelurahan,'') as kelurahan,
          EXTRACT(YEAR FROM AGE(NOW(),a.tgl_lahir)) as usia,
          COALESCE(a.nama_sekolah,'') as jenjang_sekolah,
          COALESCE(a.nama_sekolah,'') as nama_sekolah,
          a.is_aktif_pendidikan,
          a.last_update_sekolah,
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
          COALESCE(a.status_sekolah) as status_sekolah,
          COALESCE(a.npsn_sekolah) as npsn_sekolah,
          COALESCE(a.kab_kot_sekolah) as kab_kota_sekolah,
          COALESCE(a.provinsi_sekolah) as provinsi_sekolah,
          COALESCE(b.nik,'') as nik_survey,
          COALESCE(b.status_sekolah,'') as status_sekolah_survey,
          COALESCE(b.detail_sekolah,'') as detail_sekolah_survey,
          COALESCE(b.detail_tidak_sekolah,'') as detail_tidak_sekolah_survey,
          COALESCE(b.pilihan_detail_tidak_sekolah,'') as pilihan_detail_tidak_sekolah_survey,
          COALESCE(b.status_alamat,'') as status_alamat_survey,
          COALESCE(b.detail_alamat,'') as detail_alamat_survey,
          COALESCE(b.status_verifikasi,'') as status_survey
          ")
        ->whereRaw("((EXTRACT(YEAR FROM AGE(NOW(),a.tgl_lahir)) BETWEEN 0 AND 6) AND a.is_aktif_kependudukan IS TRUE AND a.status_keberadaan='ADA')")
        ->when($kecamatan != '', function ($w) use ($kecamatan, $anomali) {
          return $w->whereIn(
            DB::raw("COALESCE(NULLIF(a.kecamatan_dom,''),a.kecamatan)"),
            $anomali['arr_kecamatan']
          );
        })
        ->when($kelurahan != '', function ($w) use ($kelurahan, $anomali) {
          return $w->whereIn(
            DB::raw("COALESCE(NULLIF(a.kelurahan_dom,''),a.kelurahan)"),
            $anomali['arr_kelurahan']
          );
        })
        ->when($nik != '', function ($w) use ($nik) {
          return $w->where('a.nik', $nik);
        })
        ->when($tgl_update != '', function ($w) use ($tgl_update) {
          return $w->where(DB::raw("DATE(a.last_update_sekolah,)"), $tgl_update);
        })
        ->when($status_sekolah != '', function ($w) use ($status_sekolah) {
          if ($status_sekolah == 'Bersekolah') {
            return $w->whereRaw("
              CASE WHEN COALESCE(b.nik,'')<>''
              THEN b.status_sekolah IN ('Sudah','sudah_paud','sudah_sd','Akan sekolah','akan_daftar') OR (COALESCE(a.nama_sekolah,'')<>'' AND a.is_aktif_pendidikan is TRUE)
              ELSE (COALESCE(a.nama_sekolah,'')<>'' AND a.is_aktif_pendidikan is TRUE)
              END
            ");
          } else if ($status_sekolah == 'Tidak / Belum Bersekolah') {
            return $w->whereRaw("
              CASE WHEN COALESCE(b.nik,'')<>''
              THEN COALESCE(b.status_sekolah,'') NOT IN ('Sudah','sudah_paud','sudah_sd','Akan sekolah','akan_daftar') AND (COALESCE(a.nama_sekolah,'')='' OR a.is_aktif_pendidikan is not TRUE)
              ELSE (COALESCE(a.nama_sekolah,'')='' OR a.is_aktif_pendidikan is not TRUE)
              END
            ");
          }
        })
        ->when($status_survey != '', function ($w) use ($status_survey) {
          if ($status_survey == 'Sudah Disurvey') {
            return $w->whereRaw("
              CASE WHEN COALESCE(b.nik,'')<>''
              THEN (COALESCE(b.status_sekolah,'') NOT IN ('Sudah','sudah_paud','sudah_sd','Akan sekolah','akan_daftar') AND (COALESCE(a.nama_sekolah,'')='' OR a.is_aktif_pendidikan is not TRUE))
              ELSE (COALESCE(a.nama_sekolah,'')='' OR a.is_aktif_pendidikan is not TRUE)
              END
              AND COALESCE(b.nik,'')<>''
            ");
          } else if ($status_survey == 'Belum Disurvey') {
            return $w->whereRaw("
              CASE WHEN COALESCE(b.nik,'')<>''
              THEN (COALESCE(b.status_sekolah,'') NOT IN ('Sudah','sudah_paud','sudah_sd','Akan sekolah','akan_daftar') AND (COALESCE(a.nama_sekolah,'')='' OR a.is_aktif_pendidikan is not TRUE))
              ELSE (COALESCE(a.nama_sekolah,'')='' OR a.is_aktif_pendidikan is not TRUE)
              END
              AND COALESCE(b.nik,'')=''
            ");
          }
        })
        ->paginate($limit);

      $nik = $data->pluck('nik')->toArray();
      $data_siswa = DB::connection('pgsql_paud')
        ->table('public.siswa as s')
        ->join('public.sekolah as sek', 'sek.npsn', 's.npsn')
        ->join('public.kecamatan as kec', 'kec.kecamatan_kode', 'sek.kec_id')
        ->join('public.kelurahan as kel', 'kel.kelurahan_kode', 'sek.desa')
        ->selectRaw("s.nik,s.nama,sek.nama as nama_sekolah,s.npsn,kec.kecamatan_dispenduk,kel.kelurahan_dispenduk")
        ->where('s.status_siswa', 'Aktif')
        ->whereRaw("s.alumni is not true")
        ->whereIn('s.nik', $nik)
        ->get();

      $data->setCollection(
        $data->getCollection()->map(function ($item) use ($data_siswa) {
          $arr_sudah = ['Sudah', 'sudah_paud', 'sudah_sd', 'Akan sekolah', 'akan_daftar'];
          // CEK SURVEY
          $status_survey = 'Belum Disurvey';
          if ($item->nik_survey != '' && $item->status_survey == 'Selesai') {
            $status_survey = 'Sudah Disurvey';
          }
          // END CEK SURVEY

          // CEK SURVEY
          $status_bersekolah = 'Tidak / Belum Bersekolah';
          if ($item->nik_survey != '') {
            if (in_array($item->status_sekolah_survey, $arr_sudah) or ($item->jenjang_sekolah != '' and $item->is_aktif_pendidikan == true)) {
              $status_bersekolah = 'Bersekolah';
            }
          } else {
            if ($item->jenjang_sekolah != '' and $item->is_aktif_pendidikan == true) {
              $status_bersekolah = 'Bersekolah';
            }
          }
          // END CEK SURVEY

          // CEK PROFIL
          $cari_profil = collect($data_siswa)->where('nik', $item->nik)->first();
          $status_profil = 'Tidak ada';
          $kecamatan_sekolah_profil = '';
          $kelurahan_sekolah_profil = '';
          $npsn_sekolah_profil = '';
          $nama_sekolah_profil = '';
          if (!empty($cari_profil)) {
            $status_profil = 'Ada';
            $kecamatan_sekolah_profil = $cari_profil->kecamatan_dispenduk;
            $kelurahan_sekolah_profil = $cari_profil->kelurahan_dispenduk;
            $npsn_sekolah_profil = $cari_profil->npsn;
            $nama_sekolah_profil = $cari_profil->nama_sekolah;
          }
          // END CEK PROFIL

          $data_baru = [
            'nik' => $item->nik,
            'usia' => $item->usia,
            'nama_lengkap' => $item->nama_lengkap,
            'status_bersekolah' => $status_bersekolah,
            'status_profil_sekolah' => $status_profil,
            'npsn_sekolah' => $item->npsn_sekolah,
            'jenjang_sekolah' => $item->jenjang_sekolah,
            'nama_sekolah' => $item->nama_sekolah,
            'status_sekolah' => $item->status_sekolah,
            'kelurahan_sekolah' => $kelurahan_sekolah_profil,
            'kecamatan_sekolah' => $kecamatan_sekolah_profil,
            'kabupaten_sekolah' => $item->kab_kota_sekolah,
            'provinsi_sekolah' => $item->provinsi_sekolah,
            'status_survey' => $status_survey,
            'hasil_survey_alamat' => $item->status_alamat_survey,
            'hasil_survey_alamat_deskripsi' => $item->detail_alamat_survey,
            'hasil_survey_sekolah' => $item->status_sekolah_survey,
            'hasil_survey_sekolah_deskripsi' => $item->detail_sekolah_survey,
            'hasil_survey_alasan_kategori_tidak_sekolah' => $item->detail_tidak_sekolah_survey,
            'hasil_survey_deskripsi_tidak_sekolah' => $item->pilihan_detail_tidak_sekolah_survey,
            'updated_at' => $item->last_update_sekolah,
            'alamat_dom_siswa' => $item->alamat_dom,
            'rt_dom_siswa' => $item->rt_dom,
            'rw_dom_siswa' => $item->rw_dom,
            'kelurahan_dom_siswa' => $item->kelurahan_dom,
            'kecamatan_dom_siswa' => $item->kecamatan_dom,
            'alamat_kk_siswa' => $item->alamat,
            'rt_kk_siswa' => $item->rt,
            'rw_kk_siswa' => $item->rw,
            'kelurahan_kk_siswa' => $item->kelurahan,
            'kecamatan_kk_siswa' => $item->kecamatan,
          ];
          return $data_baru;
        }),
      );

      if ($data->count() > 0) {
        return ['code' => 200, 'pesan' => 'Sukses', 'data' => $data];
      } else {
        return ['code' => 404, 'pesan' => 'Data tidak ditemukan'];
      }
    } catch (\Throwable $th) {
      //throw $th;
      return ['code' => $th->getCode(), 'pesah' => $th->getMessage()];
    }
  }

  function rekap_anak(Request $request)
  {
    // return $request->all();
    $anomali = $this->anomali_nama($request);
    $limit = $request->limit; //1000,
    if ($limit > 1000) {
      return ['code' => 250, 'pesan' => 'Batas maksimal limit adlah 1000'];
    }
    $kecamatan = strtoupper($request->kecamatan); //"DUKUH PAKIS",
    $kelurahan = strtoupper($request->kelurahan); //"GUNUNG SARI",
    $nik = $request->nik; //"3578217112190001",
    $tgl_update = ($request->tgl_update != '') ? date('Y-m-d', strtotime($request->tgl_update)) : $request->tgl_update; //null
    $status_sekolah = $request->status_sekolah;
    $status_survey = $request->status_survey;

    // 1. Ambil data master kecamatan & kelurahan
    $dt_kecamatan = DB::table('kecamatan as kec')
      ->join('kelurahan as kel', 'kel.kecamatan_kode', '=', 'kec.kecamatan_kode')
      ->selectRaw("
              kec.kecamatan_dispenduk,
              kec.dispenduk_baru as kec_baru,
              kel.kelurahan_dispenduk,
              kel.dispenduk_baru as kel_baru
          ")
      ->when(($kecamatan != '' && $kecamatan != 'ALL'), function ($w) use ($kecamatan) {
        return $w->where('kec.kecamatan_dispenduk', $kecamatan);
      })
      ->when(($kelurahan != '' && $kelurahan != 'ALL'), function ($w) use ($kelurahan) {
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

    try {
      //code...
      $data = DB::connection('db_cutt_off')->table('public.penduduk_inject as a')
        ->leftJoin('public.survey_bunda as b', 'b.nik', 'a.nik')
        ->selectRaw("
            EXTRACT(YEAR FROM AGE(NOW(),a.tgl_lahir)) as usia,
                COUNT(*) as jumlah_penduduk,
            COUNT(*) FILTER (
              WHERE
                CASE
                  WHEN COALESCE(b.nik,'')<>'' THEN COALESCE(b.status_sekolah,'') IN ('Sudah','sudah_paud','sudah_sd','Akan sekolah','akan_daftar') OR (COALESCE(a.nama_sekolah,'')<>'' AND a.is_aktif_pendidikan is true)
                  ELSE (COALESCE(a.nama_sekolah,'')<>'' AND a.is_aktif_pendidikan is true)
                END
            ) as jumlah_bersekolah,
            COUNT(*) FILTER (
              WHERE
                CASE
                  WHEN COALESCE(b.nik,'')<>'' THEN COALESCE(b.status_sekolah,'') NOT IN ('Sudah','sudah_paud','sudah_sd','Akan sekolah','akan_daftar') AND (COALESCE(a.nama_sekolah,'')='' OR a.is_aktif_pendidikan is not true)
                  ELSE (COALESCE(a.nama_sekolah,'')='' OR a.is_aktif_pendidikan is not true)
                END
            ) as jumlah_belum_sekolah,
            COUNT(*) FILTER (
              WHERE
                CASE
                  WHEN COALESCE(b.nik,'')<>'' THEN COALESCE(b.status_sekolah,'') NOT IN ('Sudah','sudah_paud','sudah_sd','Akan sekolah','akan_daftar') AND (COALESCE(a.nama_sekolah,'')='' OR a.is_aktif_pendidikan is not true)
                  ELSE (COALESCE(a.nama_sekolah,'')='' OR a.is_aktif_pendidikan is not true)
                END AND (COALESCE(b.nik,'')<>'' AND COALESCE(b.status_verifikasi,'')='Selesai')
            ) as jumlah_sudah_survey,
            COUNT(*) FILTER (
              WHERE
                CASE
                  WHEN COALESCE(b.nik,'')<>'' THEN COALESCE(b.status_sekolah,'') NOT IN ('Sudah','sudah_paud','sudah_sd','Akan sekolah','akan_daftar') AND (COALESCE(a.nama_sekolah,'')='' OR a.is_aktif_pendidikan is not true)
                  ELSE (COALESCE(a.nama_sekolah,'')='' OR a.is_aktif_pendidikan is not true)
                END AND (COALESCE(b.nik,'')='' OR COALESCE(b.status_verifikasi,'')<>'Selesai')
            ) as jumlah_belum_survey
          ")
        ->whereRaw("((EXTRACT(YEAR FROM AGE(NOW(),a.tgl_lahir)) BETWEEN 0 AND 6) AND a.is_aktif_kependudukan IS TRUE AND a.status_keberadaan='ADA')")
        ->groupByRaw("EXTRACT(YEAR FROM AGE(NOW(),a.tgl_lahir))")
        ->orderByRaw("usia")
        ->when($kecamatan != '', function ($w) use ($kecamatan, $kelurahan, $anomali) {
          return $w->whereIn(
            DB::raw("COALESCE(NULLIF(a.kecamatan_dom,''),a.kecamatan)"),
            $anomali['arr_kecamatan']
          );
        })
        ->when($kelurahan != '', function ($w) use ($kelurahan, $anomali) {
          return $w->whereIn(
            DB::raw("COALESCE(NULLIF(a.kelurahan_dom,''),a.kelurahan)"),
            $anomali['arr_kelurahan']
          );
        })
        ->whereIn(DB::raw("COALESCE(NULLIF(a.kecamatan_dom,''), a.kecamatan)"), $arr_kec)
        ->whereIn(DB::raw("COALESCE(NULLIF(a.kelurahan_dom,''), a.kelurahan)"), $arr_kel)
        ->get();

      $hasil = [
        'kelurahan' => $kelurahan,
        'kecamatan' => $kecamatan,
        'rekap' => $data,
      ];

      if ($data->count() > 0) {
        return ['code' => 200, 'pesan' => 'Sukses', 'data' => $hasil];
      } else {
        return ['code' => 404, 'pesan' => 'Data tidak ditemukan'];
      }
    } catch (\Throwable $th) {
      //throw $th;
      return ['code' => $th->getCode(), 'pesah' => $th->getMessage()];
    }
  }

  function anomali_nama(Request $request)
  {
    // return $request->all();
    $kecamatan = strtoupper($request->kecamatan) ?? '';
    $kecamatan_kode = $request->kecamatan_kode ?? '';
    $kelurahan = strtoupper($request->kelurahan) ?? '';
    $kelurahan_kode = $request->kelurahan_kode ?? '';

    $arr_kecamatan = [];
    $arr_kelurahan = [];

    if ($kecamatan != '' && $kecamatan != 'ALL') {
      $data_kecamatan = DB::table('public.kecamatan')->where('kecamatan_dispenduk', $kecamatan)->first();
      $arr_kecamatan = [$data_kecamatan->kecamatan_dispenduk, $data_kecamatan->dispenduk_baru];
    }

    if ($kelurahan != '' && $kelurahan != 'ALL') {
      $data_kelurahan = DB::table('public.kelurahan')->where('kelurahan_dispenduk', $kelurahan)->first();
      $arr_kelurahan = [$data_kelurahan->kelurahan_dispenduk, $data_kelurahan->dispenduk_baru];
    }

    return ['arr_kecamatan' => $arr_kecamatan, 'arr_kelurahan' => $arr_kelurahan];
  }

  function siswa_paud(Request $request)
  {
    $nisn = $request->nisn ?? null;
    $limit = $request->limit ?? 10;
    $limit = ($limit <= 0) ? 10 : $limit;
    try {
      $siswa = DB::connection('pgsql_paud')
        ->table('public.siswa as s')
        ->join('public.sekolah as se', 'se.npsn', 's.npsn')
        ->join('public.master_user as u', 'u.username', 'se.npsn')
        ->selectRaw("s.nama,s.nik,s.nisn,se.npsn,se.nama as nama_sekolah,s.kelas,s.rombel")
        ->where('u.user_enable', true)
        ->where('s.status_siswa', 'Aktif')
        ->whereIn('se.jenjang', ['SPS', 'TPA', 'KB', 'TK', 'RA'])
        ->where(function ($w) {
          return $w->whereNull('s.alumni')->orWhere('s.alumni', false);
        })
        ->when($nisn !== null, function ($w) use ($nisn) {
          return $w->where('s.nisn', $nisn);
        })
        ->paginate($limit);

      $paginated = $siswa;
      $hasData = count($paginated->items()) > 0;

      return response()->json([
        'status' => $hasData ? 'success' : 'failed',
        'data'   => $paginated->items(),
        'meta'   => [
          'current_page' => $paginated->currentPage(),
          'last_page'    => $paginated->lastPage(),
          'per_page'     => $paginated->perPage(),
          'total'        => $paginated->total(),
          'next_page'    => $paginated->nextPageUrl(),
          'previous_page' => $paginated->previousPageUrl(),
        ]
      ]);
    } catch (\Throwable $th) {
      return ['status' => 'failed', 'message' => $th->getMessage()];
    }
  }
}
