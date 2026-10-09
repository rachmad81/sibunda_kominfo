<?php

namespace App\Http\Controllers\Dinas;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use DB, Auth, DataTables;

class RekapSidiaController extends Controller
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
        return view('dinas.rekap_sidia.main', $data);
        break;

      case 'get_data':
        // return $request->all();

        $anomali = $this->anomali_nama($request);
        $kecamatan = $request->kecamatan;
        $kecamatan_kode = $request->kecamatan_kode;
        $kelurahan = $request->kelurahan;
        $kelurahan_kode = $request->kelurahan_kode;
        $usia = $request->usia;
        $pencarian = $request->pencarian;

        // 1. Ambil data master kecamatan & kelurahan
        $dt_kecamatan = DB::table('kecamatan as kec')
          ->join('kelurahan as kel', 'kel.kecamatan_kode', '=', 'kec.kecamatan_kode')

          ->when($pencarian, function ($w) use ($pencarian, $kecamatan, $kelurahan, $kecamatan_kode, $kelurahan_kode) {
            if ($pencarian == 'kelurahan') {
              return $w->selectRaw("
                    kec.kecamatan_dispenduk,
                    kec.kecamatan_kode,
                    kec.dispenduk_baru as kec_baru,
                    kel.kelurahan_dispenduk,
                    kel.kelurahan_kode,
                    kel.dispenduk_baru as kel_baru
                ")
                ->groupByRaw("
                    kec.kecamatan_dispenduk,kec.kecamatan_kode,kec.dispenduk_baru,
                    kel.kelurahan_dispenduk,kel.kelurahan_kode,kel.dispenduk_baru
                ")
                ->when($kecamatan != 'All', function ($w) use ($kecamatan_kode) {
                  return $w->where('kec.kecamatan_kode', $kecamatan_kode);
                })
                ->when($kelurahan != 'All', function ($w) use ($kelurahan_kode) {
                  return $w->where('kel.kelurahan_kode', $kelurahan_kode);
                });
            } else {
              return $w->selectRaw("
                    kec.kecamatan_dispenduk,
                    kec.dispenduk_baru as kec_baru,
                    kec.kecamatan_kode
                ")
                ->groupByRaw("
                    kec.kecamatan_dispenduk,kec.dispenduk_baru,
                    kec.kecamatan_kode
                ")
                ->when($kecamatan != 'All', function ($w) use ($kecamatan_kode) {
                  return $w->where('kec.kecamatan_kode', $kecamatan_kode);
                });
            }
          })
          ->get();

        // Kumpulkan seluruh nama kecamatan & kelurahan (lama & baru) untuk filter whereIn tunggal
        $arr_kec = $dt_kecamatan->pluck('kecamatan_dispenduk')
          ->merge($dt_kecamatan->pluck('kec_baru'))
          ->filter()->unique()->values()->toArray();

        $arr_kel = $dt_kecamatan->pluck('kelurahan_dispenduk')
          ->merge($dt_kecamatan->pluck('kel_baru'))
          ->filter()->unique()->values()->toArray();

        // 2. Query tunggal ke db_cutt_off menggunakan GROUP BY
        $raw_belum = DB::connection('db_cutt_off')->table('public.penduduk_inject as a')
          ->leftJoin('public.survey_bunda as b', 'b.nik', '=', 'a.nik')
          ->when($pencarian, function ($w) use ($pencarian, $arr_kec, $arr_kel) {
            if ($pencarian == 'kelurahan') {
              return $w->selectRaw("
                    COALESCE(NULLIF(a.kecamatan_dom,''), a.kecamatan) as kec_dom,
                    COALESCE(NULLIF(a.kelurahan_dom,''), a.kelurahan) as kel_dom,
                    COUNT(*) as total,
                    COUNT(*) FILTER (WHERE (COALESCE(b.nik)<>'' AND COALESCE(b.status_verifikasi,'')='Selesai')) as jumlah_survey,
                    COUNT(*) FILTER (WHERE ((COALESCE(b.nik)<>'' AND COALESCE(b.status_verifikasi,'')<>'Selesai') OR COALESCE(b.nik,'')='')) as jumlah_belum,
                    COUNT(*) FILTER (WHERE (((COALESCE(b.nik)<>'' AND COALESCE(b.status_verifikasi,'')<>'Selesai') OR COALESCE(b.nik,'')='')) AND sidia_status is not null) as jumlah_belum_sidia
                ")
                ->whereIn(DB::raw("COALESCE(NULLIF(a.kecamatan_dom,''), a.kecamatan)"), $arr_kec)
                ->whereIn(DB::raw("COALESCE(NULLIF(a.kelurahan_dom,''), a.kelurahan)"), $arr_kel)
                ->groupByRaw("
                    COALESCE(NULLIF(a.kecamatan_dom,''), a.kecamatan),
                    COALESCE(NULLIF(a.kelurahan_dom,''), a.kelurahan)
                ");
            } else {
              return $w->selectRaw("
                    COALESCE(NULLIF(a.kecamatan_dom,''), a.kecamatan) as kec_dom,
                    COUNT(*) as total,
                    COUNT(*) FILTER (WHERE (COALESCE(b.nik)<>'' AND COALESCE(b.status_verifikasi,'')='Selesai')) as jumlah_survey,
                    COUNT(*) FILTER (WHERE ((COALESCE(b.nik)<>'' AND COALESCE(b.status_verifikasi,'')<>'Selesai') OR COALESCE(b.nik,'')='')) as jumlah_belum,
                    COUNT(*) FILTER (WHERE (((COALESCE(b.nik)<>'' AND COALESCE(b.status_verifikasi,'')<>'Selesai') OR COALESCE(b.nik,'')='')) AND sidia_status is not null) as jumlah_belum_sidia
                ")
                ->whereIn(DB::raw("COALESCE(NULLIF(a.kecamatan_dom,''), a.kecamatan)"), $arr_kec)
                ->groupByRaw("
                    COALESCE(NULLIF(a.kecamatan_dom,''), a.kecamatan)
                ");
            }
          })
          ->whereRaw("
              CASE WHEN COALESCE(b.nik,'')<>''
                THEN COALESCE(b.status_sekolah,'') NOT IN ('Sudah','sudah_paud','sudah_sd','Akan sekolah','akan_daftar') AND (COALESCE(a.nama_sekolah,'')='' OR a.is_aktif_pendidikan is not true)
                ELSE (COALESCE(a.nama_sekolah,'')='' OR a.is_aktif_pendidikan is not true)
                END
              AND (a.is_aktif_kependudukan IS TRUE AND a.status_keberadaan='ADA')
          ")
          ->when($usia, function ($w) use ($usia) {
            if ($usia == '1') {
              return $w->whereRaw("(EXTRACT(YEAR FROM AGE(NOW(),a.tgl_lahir)) BETWEEN 2 AND 3)");
            } else {
              return $w->whereRaw("(EXTRACT(YEAR FROM AGE(NOW(),a.tgl_lahir)) BETWEEN 4 AND 6)");
            }
          })
          ->get();

        // 3. Petakan hasil query ke dalam Array HashMap (O(1) Access Time)
        $lookup = [];
        $lookup_belum = [];
        $lookup_sudah = [];
        $lookup_belum_sidia = [];
        foreach ($raw_belum as $row) {
          if ($pencarian == 'kelurahan') {
            $key = $row->kec_dom . '|' . $row->kel_dom;
          } else {
            $key = $row->kec_dom;
          }
          $lookup[$key] = $row->total;
          $lookup_belum[$key] = $row->jumlah_belum;
          $lookup_belum_sidia[$key] = $row->jumlah_belum_sidia;
          $lookup_sudah[$key] = $row->jumlah_survey;
        }

        // 4. Gabungkan ke struktur $dt_kecamatan tanpa query tambahan
        $data_belum = $dt_kecamatan->map(function ($k) use ($pencarian, $lookup, $lookup_belum, $lookup_sudah, $lookup_belum_sidia) {
          // 1. Kumpulkan semua kemungkinan kombinasi kunci
          if ($pencarian == 'kelurahan') {
            $keys = [
              $k->kecamatan_dispenduk . '|' . $k->kelurahan_dispenduk,
              $k->kec_baru . '|' . $k->kel_baru,
              $k->kecamatan_dispenduk . '|' . $k->kel_baru,
              $k->kec_baru . '|' . $k->kelurahan_dispenduk,
            ];
          } else {
            $keys = [
              $k->kecamatan_dispenduk,
              $k->kec_baru,
            ];
          }

          // 2. Saring string kosong & hilangkan duplikasi kunci yang sama!
          $unique_keys = array_unique(array_filter($keys));

          // 3. Lakukan kalkulasi hanya pada key unik
          $jumlah = 0;
          $jumlah_sudah = 0;
          $jumlah_belum = 0;
          $jumlah_belum_sidia = 0;

          foreach ($unique_keys as $key) {
            $jumlah += $lookup[$key] ?? 0;
            $jumlah_sudah += $lookup_sudah[$key] ?? 0;
            $jumlah_belum += $lookup_belum[$key] ?? 0;
            $jumlah_belum_sidia += $lookup_belum_sidia[$key] ?? 0;
          }

          if ($pencarian == 'kelurahan') {
            return [
              'kecamatan'    => $k->kecamatan_dispenduk,
              'kelurahan'    => $k->kelurahan_dispenduk,
              'kecamatan_kode'    => $k->kecamatan_kode,
              'kelurahan_kode'    => $k->kelurahan_kode,
              'jumlah_tidak' => $jumlah,
              'jumlah_sudah' => $jumlah_sudah,
              'jumlah_belum' => $jumlah_belum,
              'jumlah_belum_sidia' => $jumlah_belum_sidia,
            ];
          } else {
            return [
              'kecamatan'    => $k->kecamatan_dispenduk,
              'kecamatan_kode'    => $k->kecamatan_kode,
              'jumlah_tidak' => $jumlah,
              'jumlah_sudah' => $jumlah_sudah,
              'jumlah_belum' => $jumlah_belum,
              'jumlah_belum_sidia' => $jumlah_belum_sidia,
            ];
          }
        })->sortBy('kecamatan')->values()->toArray();

        $applyFilter = function ($query) use ($usia, $kecamatan, $kecamatan_kode, $kelurahan, $kelurahan_kode, $pencarian) {
          return $query
            ->when($pencarian == 'kelurahan', function ($w) use ($kecamatan, $kelurahan, $kecamatan_kode, $kelurahan_kode) {
              return $w->when($kecamatan != 'All', fn($q) => $q->where('kec.kode', $kecamatan_kode))
                ->when($kelurahan != 'All', fn($q) => $q->where('kel.kode', $kelurahan_kode));
            }, function ($w) use ($kecamatan, $kecamatan_kode) {
              return $w->when($kecamatan != 'All', fn($q) => $q->where('kec.kode', $kecamatan_kode));
            })
            ->when($usia, function ($w) use ($usia) {
              if ($usia == '1') {
                return $w->whereRaw("(EXTRACT(YEAR FROM AGE(NOW(),s.tgl_lahir)) BETWEEN 2 AND 3)");
              } else {
                return $w->whereRaw("(EXTRACT(YEAR FROM AGE(NOW(),s.tgl_lahir)) BETWEEN 4 AND 6)");
              }
            })
            ->whereNotIn('l.npsn', ['2020', 'AI9999'])
            ->where('s.keterangan', 'Aktif')
            ->whereRaw("u.aktif IS true");
        };

        // Query Utama Agregasi (Kecamatan / Kelurahan)
        $isKelurahan = ($pencarian == 'kelurahan');

        $selectFields = [
          'l.kecamatan',
          'kec.kode as kecamatan_kode',
          DB::raw('COUNT(DISTINCT s.npsn) as jumlah_lembaga'),
          DB::raw('COUNT(s.npsn) as jumlah_siswa')
        ];

        $groupByFields = ['l.kecamatan', 'kec.kode'];

        if ($isKelurahan) {
          array_splice($selectFields, 1, 0, 'l.kelurahan');
          array_splice($selectFields, 1, 0, 'kel.kode as kelurahan_kode');
          $groupByFields[] = 'l.kelurahan';
          $groupByFields[] = 'kel.kode';
        }

        $sidia = DB::connection('db_sidia')->table('public.lembagas as l')
          ->join('public.siswas as s', 's.npsn', 'l.npsn')
          ->join('public.users as u', 'u.username', 'l.npsn')
          ->join('public.kecamatan as kec', 'kec.kecamatan_dispenduk', 'l.kecamatan')
          ->join('public.kelurahan as kel', 'kel.kelurahan_dispenduk', 'l.kelurahan')
          ->select($selectFields)
          ->groupBy($groupByFields)
          ->tap($applyFilter)
          ->get();

        // return $data_belum;
        // Formatter DataTables
        $dataBelumKeyed = collect($data_belum)->keyBy(function ($item) use ($isKelurahan) {
          return $isKelurahan
            ? $item['kecamatan'] . '_' . ($item['kelurahan'] ?? '')
            : $item['kecamatan'];
        });

        return DataTables::of($data_belum)
          ->addIndexColumn()
          ->addColumn('jumlah_tidak', function ($w) use ($dataBelumKeyed, $isKelurahan, $usia) {
            $key = $isKelurahan ? $w['kecamatan'] . '_' . $w['kelurahan'] : $w['kecamatan'];

            $param = [
              'key' => $key,
              'kategori' => 'jumlah_tidak',
              'usia' => $usia,
            ];
            $param = base64_encode(json_encode($param));

            return '<a href="javascript:void(0)" onclick="modal_penduduk(\'' . $param . '\')">' . ($dataBelumKeyed[$key]['jumlah_tidak'] ?? 0) . '</a>';
          })
          ->addColumn('jumlah_belum', function ($w) use ($dataBelumKeyed, $isKelurahan, $usia) {
            $key = $isKelurahan ? $w['kecamatan'] . '_' . $w['kelurahan'] : $w['kecamatan'];
            $param = [
              'key' => $key,
              'kategori' => 'jumlah_belum',
              'usia' => $usia,
            ];
            $param = base64_encode(json_encode($param));

            return '<a href="javascript:void(0)" onclick="modal_penduduk(\'' . $param . '\')">' . ($dataBelumKeyed[$key]['jumlah_belum'] ?? 0) . '</a>';
          })
          ->addColumn('jumlah_belum_sidia', function ($w) use ($dataBelumKeyed, $isKelurahan, $usia) {
            $key = $isKelurahan ? $w['kecamatan'] . '_' . $w['kelurahan'] : $w['kecamatan'];
            $param = [
              'key' => $key,
              'kategori' => 'jumlah_belum_sidia',
              'usia' => $usia,
            ];
            $param = base64_encode(json_encode($param));

            return '<a href="javascript:void(0)" onclick="modal_penduduk(\'' . $param . '\')">' . ($dataBelumKeyed[$key]['jumlah_belum_sidia'] ?? 0) . '</a>';
          })
          ->addColumn('jumlah_sudah', function ($w) use ($dataBelumKeyed, $isKelurahan, $usia) {
            $key = $isKelurahan ? $w['kecamatan'] . '_' . $w['kelurahan'] : $w['kecamatan'];
            $param = [
              'key' => $key,
              'kategori' => 'jumlah_sudah',
              'usia' => $usia,
            ];
            $param = base64_encode(json_encode($param));

            return '<a href="javascript:void(0)" onclick="modal_penduduk(\'' . $param . '\')">' . ($dataBelumKeyed[$key]['jumlah_sudah'] ?? 0) . '</a>';
          })
          ->addColumn('jumlah_lembaga_sidia', function ($w) use ($sidia, $usia, $isKelurahan) {
            $key = $isKelurahan ? $w['kecamatan'] . '_' . $w['kelurahan'] : $w['kecamatan'];
            $param = [
              'key' => $key,
              'kategori' => 'jumlah_lembaga_sidia',
              'usia' => $usia,
            ];
            $param = base64_encode(json_encode($param));

            if ($isKelurahan) {
              $data_sidia = collect($sidia)->where('kecamatan_kode', $w['kecamatan_kode'])->where('kelurahan_kode', $w['kelurahan_kode'])->first();
            } else {
              $data_sidia = collect($sidia)->where('kecamatan_kode', $w['kecamatan_kode'])->first();
            }
            return '<a href="javascript:void(0)" onclick="modal_sidia(\'' . $param . '\')">' . ($data_sidia->jumlah_lembaga ?? 0) . '</a>';
          })
          ->addColumn('jumlah_siswa_sidia', function ($w) use ($sidia, $usia, $isKelurahan) {
            $key = $isKelurahan ? $w['kecamatan'] . '_' . $w['kelurahan'] : $w['kecamatan'];
            $param = [
              'key' => $key,
              'kategori' => 'jumlah_siswa_sidia',
              'usia' => $usia,
            ];
            $param = base64_encode(json_encode($param));

            if ($isKelurahan) {
              $data_sidia = collect($sidia)->where('kecamatan_kode', $w['kecamatan_kode'])->where('kelurahan_kode', $w['kelurahan_kode'])->first();
            } else {
              $data_sidia = collect($sidia)->where('kecamatan_kode', $w['kecamatan_kode'])->first();
            }
            return '<a href="javascript:void(0)" onclick="modal_sidia(\'' . $param . '\')">' . ($data_sidia->jumlah_siswa ?? 0) . '</a>';
          })
          ->addColumn('persen', function ($w) use ($dataBelumKeyed, $isKelurahan) {
            $key = $isKelurahan ? $w['kecamatan'] . '_' . $w['kelurahan'] : $w['kecamatan'];
            $belum = $dataBelumKeyed[$key]['jumlah_belum'] ?? 0;
            $belum_sidia = $dataBelumKeyed[$key]['jumlah_belum_sidia'] ?? 0;
            $persen = ($belum != 0) ? (($belum_sidia / $belum) * 100) : 0;

            return number_format($persen, 2, '.', '.') . ' %';
          })
          ->rawColumns([
            'persen',
            'jumlah_tidak',
            'jumlah_belum',
            'jumlah_belum_sidia',
            'jumlah_sudah',
            'jumlah_lembaga_sidia',
            'jumlah_siswa_sidia'
          ])
          ->make(true);
        break;

      case 'get_rekap_besar':
        // return $request->all();

        $anomali = $this->anomali_nama($request);
        $kecamatan = $request->kecamatan;
        $kecamatan_kode = $request->kecamatan_kode;
        $kelurahan = $request->kelurahan;
        $kelurahan_kode = $request->kelurahan_kode;
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

        $nama_usia = '';
        switch ($usia) {
          case '1':
            $nama_usia = '2-3 th 11 bulan';
            break;
          case '2':
            $nama_usia = '4-6';
            break;

          default:
            # code...
            break;
        }

        // 2. Query tunggal ke db_cutt_off menggunakan GROUP BY
        $raw_belum = DB::connection('db_cutt_off')->table('public.penduduk_inject as a')
          ->leftJoin('public.survey_bunda as b', 'b.nik', '=', 'a.nik')
          ->selectRaw("
              '$nama_usia' as usia,
              COUNT(*) as total,
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
                  END AND (COALESCE(b.nik,'') = '' OR COALESCE(b.status_verifikasi,'')<>'Selesai') AND COALESCE(a.sidia_status,'')='Ada'
                ) as jumlah_belum_survey_sidia
          ")
          ->whereRaw("
              (a.is_aktif_kependudukan IS TRUE AND a.status_keberadaan='ADA')
          ")
          ->when($usia, function ($w) use ($usia) {
            if ($usia == '1') {
              return $w->whereRaw("(EXTRACT(YEAR FROM AGE(NOW(),a.tgl_lahir)) BETWEEN 2 AND 3)");
            } else {
              return $w->whereRaw("(EXTRACT(YEAR FROM AGE(NOW(),a.tgl_lahir)) BETWEEN 4 AND 6)");
            }
          })
          ->whereIn(DB::raw("COALESCE(NULLIF(a.kecamatan_dom,''), a.kecamatan)"), $arr_kec)
          ->whereIn(DB::raw("COALESCE(NULLIF(a.kelurahan_dom,''), a.kelurahan)"), $arr_kel)
          ->groupByRaw("
            CASE WHEN EXTRACT(YEAR FROM AGE(NOW(),a.tgl_Lahir)) IN ('2','3') THEN '1' ELSE '2' END
          ")
          ->get();

        return DataTables::of($raw_belum)
          ->addColumn('persen_sudah', function ($w) {
            $belum = $w->jumlah_belum_bersekolah ?? 0;
            $sudah = $w->jumlah_survey ?? 0;
            $persen = ($belum != 0) ? ($sudah / $belum) * 100 : 0;
            return number_format($persen, 2) . ' %';
          })
          ->addColumn('persen_belum', function ($w) {
            $belum = $w->jumlah_belum_bersekolah ?? 0;
            $belum_s = $w->jumlah_belum_survey ?? 0;
            $persen = ($belum != 0) ? ($belum_s / $belum) * 100 : 0;
            return number_format($persen, 2) . ' %';
          })
          ->addColumn('persen_belum_sidia', function ($w) {
            $belum = $w->jumlah_belum_survey_sidia ?? 0;
            $belum_s = $w->jumlah_belum_survey ?? 0;
            $persen = ($belum_s != 0) ? ($belum / $belum_s) * 100 : 0;
            return number_format($persen, 2) . ' %';
          })
          ->addColumn('jumlah_belum_bersekolah', function ($w) use ($usia) {
            $key = 'All_All';
            $param = [
              'key' => $key,
              'kategori' => 'jumlah_tidak',
              'usia' => $usia,
            ];
            $param = base64_encode(json_encode($param));
            return '<a href="javascript:void(0)" onclick="modal_penduduk(\'' . $param . '\')">' . number_format(($w->jumlah_belum_bersekolah ?? 0), 0, ',', '.') . '</a>';
          })
          ->addColumn('jumlah_survey', function ($w) use ($usia) {
            $key = 'All_All';
            $param = [
              'key' => $key,
              'kategori' => 'jumlah_sudah',
              'usia' => $usia,
            ];
            $param = base64_encode(json_encode($param));
            return '<a href="javascript:void(0)" onclick="modal_penduduk(\'' . $param . '\')">' . number_format(($w->jumlah_survey ?? 0), 0, ',', '.') . '</a>';
          })
          ->addColumn('jumlah_belum_survey', function ($w) use ($usia) {
            $key = 'All_All';
            $param = [
              'key' => $key,
              'kategori' => 'jumlah_belum',
              'usia' => $usia,
            ];
            $param = base64_encode(json_encode($param));
            return '<a href="javascript:void(0)" onclick="modal_penduduk(\'' . $param . '\')">' . number_format(($w->jumlah_belum_survey ?? 0), 0, ',', '.') . '</a>';
          })
          ->addColumn('jumlah_belum_survey_sidia', function ($w) use ($usia) {
            $key = 'All_All';
            $param = [
              'key' => $key,
              'kategori' => 'jumlah_belum_sidia',
              'usia' => $usia,
            ];
            $param = base64_encode(json_encode($param));
            return '<a href="javascript:void(0)" onclick="modal_penduduk(\'' . $param . '\')">' . number_format(($w->jumlah_belum_survey_sidia ?? 0), 0, ',', '.') . '</a>';
          })
          ->rawColumns([
            'total',
            'jumlah_bersekolah',
            'jumlah_belum_bersekolah',
            'jumlah_survey',
            'jumlah_belum_survey',
            'jumlah_belum_survey_sidia'
          ])
          ->make(true);
        break;

      case 'modal_penduduk':
        $param = $request->param;
        $jenis = $request->jenis;
        $param = json_decode(base64_decode($param));
        $kategori = $param->kategori;
        $key = explode('_', $param->key);
        if (count($key) > 1) {
          $kecamatan = ' Kecamatan ' . $key[0];
          $kelurahan = ' Kelurahan ' . $key[1];
        } else {
          $kecamatan = ' Kecamatan ' . $key[0];
          $kelurahan = '';
        }
        $dat_usia = ($param->usia == 1) ? 'Usia 2-3 th 11 bulan' : '4-6 tahun';

        switch ($kategori) {
          case 'jumlah_tidak':
            $judul = 'Data Anak Belum Bersekolah ' . $dat_usia . $kecamatan . $kelurahan;
            break;
          case 'jumlah_belum':
            $judul = 'Data Anak Belum Bersekolah Belum Disurvey ' . $dat_usia . $kecamatan . $kelurahan;
            break;
          case 'jumlah_belum_sidia':
            $judul = 'Data Anak Belum Bersekolah Belum Disurvey Ada di aplikasi sidia ' . $dat_usia . $kecamatan . $kelurahan;
            break;
          case 'jumlah_sudah':
            $judul = 'Data Anak Belum Bersekolah Sudah Disurvey ' . $dat_usia . $kecamatan . $kelurahan;
            break;
          case 'jumlah_siswa_sidia':
            $judul = 'Data Siswa Aktif Sidia ' . $dat_usia . $kecamatan . $kelurahan;
            break;
          case 'jumlah_lembaga_sidia':
            $judul = 'Data Lembaga Sidia ' . $kecamatan . $kelurahan;
            break;

          default:
            $judul = '';
            break;
        }
        $data = [
          'judul' => $judul,
          'param' => $request->param,
        ];

        if ($jenis == 'penduduk') {
          $content = view('dinas.rekap_sidia.modal_penduduk', $data)->render();
        } else {
          $content = view('dinas.rekap_sidia.modal_sidia', $data)->render();
        }
        return ['content' => $content];
        break;

      case 'data_penduduk':
        // return $request->all();
        $param = $request->param;
        $param = json_decode(base64_decode($param));
        $usia = $param->usia;
        $kategori = $param->kategori;
        $key = explode('_', $param->key);
        if (count($key) > 1) {
          $kecamatan = $key[0];
          $kelurahan = $key[1];
        } else {
          $kecamatan = $key[0];
          $kelurahan = 'All';
        }

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

        $request->merge(['kecamatan' => $kecamatan, 'kelurahan' => $kelurahan]);
        $anomali = $this->anomali_nama($request);

        $data = DB::connection('db_cutt_off')->table('public.penduduk_inject as a')
          ->leftJoin('public.survey_bunda as b', 'a.nik', 'b.nik')
          ->whereRaw("(a.is_aktif_kependudukan is true AND a.status_keberadaan='ADA')")
          ->whereIn(DB::raw("COALESCE(NULLIF(a.kecamatan_dom,''),a.kecamatan)"), $arr_kec)
          ->whereIn(DB::raw("COALESCE(NULLIF(a.kelurahan_dom,''),a.kelurahan)"), $arr_kel)
          ->selectRaw("
          a.nama_lengkap,
          a.nik,
          a.jenis_kelamin,
          EXTRACT(YEAR FROM AGE(NOW(),a.tgl_lahir)) as usia,
          a.alamat_dom,
          a.alamat,
          a.rt,
          a.rw,
          a.kecamatan,
          a.kecamatan_dom,
          a.kelurahan_dom,
          a.kelurahan,
          b.status_alamat,
          b.detail_alamat,
          b.status_sekolah,
          b.detail_sekolah,
          b.pilihan_detail_tidak_sekolah
          ")
          ->when($kecamatan !== 'All', function ($w) use ($anomali) {
            return $w->whereIn(DB::raw("COALESCE(NULLIF(a.kecamatan_dom,''),a.kecamatan)"), $anomali['arr_kecamatan']);
          })
          ->when($kelurahan !== 'All', function ($w) use ($anomali) {
            return $w->whereIn(DB::raw("COALESCE(NULLIF(a.kelurahan_dom,''),a.kelurahan)"), $anomali['arr_kelurahan']);
          })
          ->when($usia, function ($w) use ($usia) {
            if ($usia == '1') {
              return $w->whereBetween(DB::raw("EXTRACT(YEAR FROM AGE(NOW(),a.tgl_lahir))"), [2, 3]);
            } else if ($usia == '06') {
              return $w->whereBetween(DB::raw("EXTRACT(YEAR FROM AGE(NOW(),a.tgl_lahir))"), [0, 6]);
            } else if ($usia == '36') {
              return $w->whereBetween(DB::raw("EXTRACT(YEAR FROM AGE(NOW(),a.tgl_lahir))"), [3, 6]);
            } else {
              return $w->whereBetween(DB::raw("EXTRACT(YEAR FROM AGE(NOW(),a.tgl_lahir))"), [4, 6]);
            }
          })
          ->when($kategori, function ($w) use ($kategori) {
            if (in_array($kategori, [
              'jumlah_tidak',
              'jumlah_belum',
              'jumlah_belum_sidia',
              'jumlah_sudah'
            ])) {
              $w->whereRaw("
                CASE WHEN COALESCE(b.nik,'')<>''
                THEN (COALESCE(b.status_sekolah,'') NOT IN ('Sudah','sudah_paud','sudah_sd','Akan sekolah','akan_daftar') AND (COALESCE(a.nama_sekolah,'')='' OR a.is_aktif_pendidikan is not true))
                ELSE
                (COALESCE(a.nama_sekolah,'')='' OR a.is_aktif_pendidikan is not true)
                END
              ");

              switch ($kategori) {
                case 'jumlah_belum':
                  return $w->whereRaw("((COALESCE(b.nik)<>'' AND COALESCE(b.status_verifikasi,'')<>'Selesai') OR COALESCE(b.nik,'')='')");
                  break;
                case 'jumlah_belum_sidia':
                  return $w->whereRaw("(((COALESCE(b.nik)<>'' AND COALESCE(b.status_verifikasi,'')<>'Selesai') OR COALESCE(b.nik,'')='')) AND sidia_status is not null");
                  break;
                case 'jumlah_sudah':
                  return $w->whereRaw("(COALESCE(b.nik)<>'' AND COALESCE(b.status_verifikasi,'')='Selesai')");
                  break;

                default:
                  return $w;
                  break;
              }
            } elseif (in_array($kategori, ['jumlah_bersekolah'])) {
              return $w->whereRaw("
                CASE WHEN COALESCE(b.nik,'')<>''
                THEN COALESCE(b.status_sekolah,'') IN ('Sudah','sudah_paud','sudah_sd','Akan sekolah','akan_daftar') OR (COALESCE(a.nama_sekolah,'')<>'' AND a.is_aktif_pendidikan is true)
                ELSE (COALESCE(a.nama_sekolah,'')<>'' AND a.is_aktif_pendidikan is true)
                END
              ");
            } else {
            }
          });

        $sidia = [];
        if ($kategori == 'jumlah_belum_sidia') {
          $nik_sidia = (clone $data)->pluck('nik')->toArray();
          if (!empty($nik_sidia)) {
            $sidia = DB::connection('db_sidia')->table('lembagas as l')
              ->join('public.users as u', 'u.username', 'l.npsn')
              ->join('public.siswas as s', 's.npsn', 'l.npsn')
              ->selectRaw("
              s.nik,
              l.kecamatan as kecamatan_lembaga,
              l.kelurahan as kelurahan_lembaga,
              l.nama as nama_lembaga,
              s.nama as nama_siswa,
              l.npsn,
              s.keterangan")
              ->whereIn('s.nik', $nik_sidia)->get();
          }
        }

        try {
          return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('npsn', function ($w) use ($sidia) {
              $baris = '';
              if (!empty($sidia)) {
                $baris = collect($sidia)->where('nik', $w->nik)->first()->npsn ?? '';
              }
              return $baris;
            })
            ->addColumn('nama_lembaga', function ($w) use ($sidia) {
              $baris = '';
              if (!empty($sidia)) {
                $baris = collect($sidia)->where('nik', $w->nik)->first()->nama_lembaga ?? '';
              }
              return $baris;
            })
            ->addColumn('keterangan', function ($w) use ($sidia) {
              $baris = '';
              if (!empty($sidia)) {
                $baris = collect($sidia)->where('nik', $w->nik)->first()->keterangan ?? '';
              }
              return $baris;
            })
            ->addColumn('nama_siswa', function ($w) use ($sidia) {
              $baris = '';
              if (!empty($sidia)) {
                $baris = collect($sidia)->where('nik', $w->nik)->first()->nama_siswa ?? '';
              }
              return $baris;
            })
            ->addColumn('kecamatan_lembaga', function ($w) use ($sidia) {
              $baris = '';
              if (!empty($sidia)) {
                $baris = collect($sidia)->where('nik', $w->nik)->first()->kecamatan_lembaga ?? '';
              }
              return $baris;
            })
            ->addColumn('kelurahan_lembaga', function ($w) use ($sidia) {
              $baris = '';
              if (!empty($sidia)) {
                $baris = collect($sidia)->where('nik', $w->nik)->first()->kelurahan_lembaga ?? '';
              }
              return $baris;
            })
            ->rawColumns([
              'status_alamat',
              'detail_alamat',
              'status_sekolah',
              'detail_sekolah',
              'pilihan_detail_tidak_sekolah'
            ])
            ->toJson();
        } catch (\Throwable $th) {
          return $th;
        }
        break;

      case 'data_sidia':
        $param = $request->param;
        $param = json_decode(base64_decode($param));
        $usia = $param->usia;
        $kategori = $param->kategori;
        $key = explode('_', $param->key);
        if (count($key) > 1) {
          $kecamatan = $key[0];
          $kelurahan = $key[1];
        } else {
          $kecamatan = $key[0];
          $kelurahan = 'All';
        }

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

        $request->merge(['kecamatan' => $kecamatan, 'kelurahan' => $kelurahan]);
        $anomali = $this->anomali_nama($request);

        $data = DB::connection('db_sidia')->table('public.lembagas as l')
          ->join('public.siswas as s', 's.npsn', 'l.npsn')
          ->when($kecamatan !== 'All', function ($w) use ($anomali) {
            return $w->whereIn(DB::raw("COALESCE(l.kecamatan,'')"), $anomali['arr_kecamatan']);
          })
          ->when($kelurahan !== 'All', function ($w) use ($anomali) {
            return $w->whereIn(DB::raw("COALESCE(l.kelurahan,'')"), $anomali['arr_kelurahan']);
          })
          ->when($usia, function ($w) use ($usia) {
            if ($usia == '1') {
              return $w->whereBetween(DB::raw("EXTRACT(YEAR FROM AGE(NOW(),s.tgl_lahir))"), [2, 3]);
            } else {
              return $w->whereBetween(DB::raw("EXTRACT(YEAR FROM AGE(NOW(),s.tgl_lahir))"), [4, 6]);
            }
          })
          ->when($kategori, function ($w) use ($kategori) {
            if ($kategori == 'jumlah_siswa_sidia') {
              $w->selectRaw("
                l.npsn,
                l.nama as nama_lembaga,
                l.alamat as alamat_lembaga,
                l.kelurahan as kelurahan_lembaga,
                l.kecamatan as kecamatan_lembaga,
                s.nama as nama_siswa,
                s.keterangan
              ");
            } else {
              $w->selectRaw("
                l.npsn,
                l.nama as nama_lembaga,
                l.alamat as alamat_lembaga,
                l.kelurahan as kelurahan_lembaga,
                l.kecamatan as kecamatan_lembaga
              ")
                ->groupByRaw("
                l.npsn,
                l.nama,
                l.alamat,
                l.kelurahan,
                l.kecamatan
              ");
            }
          });

        return DataTables::queryBuilder($data)
          ->addIndexColumn()
          ->make(true);
        break;

      case 'data_sekolah':
        $param = $request->param;
        $param = json_decode(base64_decode($param));
        $usia = $param->usia;
        $kategori = $param->kategori;
        $key = explode('_', $param->key);
        if (count($key) > 1) {
          $kecamatan = $key[0];
          $kelurahan = $key[1];
        } else {
          $kecamatan = $key[0];
          $kelurahan = 'All';
        }

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

        $request->merge(['kecamatan' => $kecamatan, 'kelurahan' => $kelurahan]);
        $anomali = $this->anomali_nama($request);

        $data = DB::connection('pgsql_paud')->table('public.sekolah as se')
          ->join('public.master_user as u', 'u.username', '=', 'se.npsn')
          ->join('public.siswa as s', 's.npsn', '=', 'se.npsn')
          ->join('public.kecamatan as kec', 'kec.kecamatan_kode', '=', 'se.kec_id')
          ->join('public.kelurahan as kel', 'kel.kelurahan_kode', '=', 'se.desa')
          ->when($usia, function ($w) use ($usia) {
            return $usia == '1'
              ? $w->whereIn('se.jenjang', ['KB', 'SPS'])
              : $w->whereIn('se.jenjang', ['TK', 'RA']);
          })
          ->where('u.user_enable', true)
          ->where('s.status_siswa', 'Aktif')
          ->whereRaw("s.alumni is NOT TRUE")
          ->when($kecamatan !== 'All', function ($w) use ($anomali) {
            return $w->whereIn(DB::raw("COALESCE(kec.kecamatan_dispenduk,'')"), $anomali['arr_kecamatan']);
          })
          ->when($kelurahan !== 'All', function ($w) use ($anomali) {
            return $w->whereIn(DB::raw("COALESCE(kel.kelurahan_dispenduk,'')"), $anomali['arr_kelurahan']);
          })
          ->when($kategori, function ($w) use ($kategori) {
            if ($kategori == 'jumlah_siswa') {
              $w->selectRaw("
                se.npsn,
                se.nama as nama_lembaga,
                se.alamat as alamat_lembaga,
                se.kuota,
                kel.kelurahan_dispenduk as kelurahan_lembaga,
                kec.kecamatan_dispenduk as kecamatan_lembaga,
                s.nama as nama_siswa
              ");
            } else if (in_array($kategori, ['jumlah_lembaga', 'jumlah_kuota'])) {
              $w->selectRaw("
                se.npsn,
                se.nama as nama_lembaga,
                se.alamat as alamat_lembaga,
                se.kuota,
                kel.kelurahan_dispenduk as kelurahan_lembaga,
                kec.kecamatan_dispenduk as kecamatan_lembaga
              ")
                ->groupByRaw("
                se.npsn,
                se.nama,
                se.alamat,
                se.kuota,
                kel.kelurahan_dispenduk,
                kec.kecamatan_dispenduk
              ");;
            }
          });

        return DataTables::queryBuilder($data)
          ->addIndexColumn()
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
