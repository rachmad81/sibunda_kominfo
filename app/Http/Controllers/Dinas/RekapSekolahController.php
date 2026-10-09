<?php

namespace App\Http\Controllers\Dinas;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use DB, Auth, DataTables;

class RekapSekolahController extends Controller
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
        return view('dinas.rekap_sekolah.main', $data);
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
                    kec.dispenduk_baru as kec_baru,
                    kel.kelurahan_dispenduk,
                    kel.dispenduk_baru as kel_baru
                ")
                ->groupByRaw("
                    kec.kecamatan_dispenduk,kec.dispenduk_baru,
                    kel.kelurahan_dispenduk,kel.dispenduk_baru
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
                    kec.dispenduk_baru as kec_baru
                ")
                ->groupByRaw("
                    kec.kecamatan_dispenduk,kec.dispenduk_baru
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
                    COUNT(*) FILTER (WHERE ((COALESCE(b.nik)<>'' AND COALESCE(b.status_verifikasi,'')<>'Selesai') OR COALESCE(b.nik,'')='')) as jumlah_belum
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
                    COUNT(*) FILTER (WHERE ((COALESCE(b.nik)<>'' AND COALESCE(b.status_verifikasi,'')<>'Selesai') OR COALESCE(b.nik,'')='')) as jumlah_belum
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
        foreach ($raw_belum as $row) {
          if ($pencarian == 'kelurahan') {
            $key = $row->kec_dom . '|' . $row->kel_dom;
          } else {
            $key = $row->kec_dom;
          }
          $lookup[$key] = $row->total;
          $lookup_belum[$key] = $row->jumlah_belum;
          $lookup_sudah[$key] = $row->jumlah_survey;
        }


        $lookup;
        $lookup_belum;
        $lookup_sudah;

        // 4. Gabungkan ke struktur $dt_kecamatan tanpa query tambahan
        $data_belum = $dt_kecamatan->map(function ($k) use ($pencarian, $lookup, $lookup_belum, $lookup_sudah) {
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

          foreach ($unique_keys as $key) {
            $jumlah += $lookup[$key] ?? 0;
            $jumlah_sudah += $lookup_sudah[$key] ?? 0;
            $jumlah_belum += $lookup_belum[$key] ?? 0;
          }

          if ($pencarian == 'kelurahan') {
            return [
              'kecamatan'    => $k->kecamatan_dispenduk,
              'kelurahan'    => $k->kelurahan_dispenduk,
              'jumlah_tidak' => $jumlah,
              'jumlah_sudah' => $jumlah_sudah,
              'jumlah_belum' => $jumlah_belum,
            ];
          } else {
            return [
              'kecamatan'    => $k->kecamatan_dispenduk,
              'jumlah_tidak' => $jumlah,
              'jumlah_sudah' => $jumlah_sudah,
              'jumlah_belum' => $jumlah_belum,
            ];
          }
        })->sortBy('kecamatan')->values()->toArray();

        $applyFilter = function ($query) use ($usia, $kecamatan, $kecamatan_kode, $kelurahan, $kelurahan_kode, $pencarian) {
          return $query
            ->when($usia, function ($w) use ($usia) {
              return $usia == '1'
                ? $w->whereIn('se.jenjang', ['KB', 'SPS'])
                : $w->whereIn('se.jenjang', ['TK', 'RA']);
            })
            ->when($pencarian == 'kelurahan', function ($w) use ($kecamatan, $kelurahan, $kecamatan_kode, $kelurahan_kode) {
              return $w->when($kecamatan != 'All', fn($q) => $q->where('kec.kecamatan_kode', $kecamatan_kode))
                ->when($kelurahan != 'All', fn($q) => $q->where('kel.kelurahan_kode', $kelurahan_kode));
            }, function ($w) use ($kecamatan, $kecamatan_kode) {
              return $w->when($kecamatan != 'All', fn($q) => $q->where('kec.kecamatan_kode', $kecamatan_kode));
            })
            ->where('u.user_enable', true);
        };

        // Subquery hitung siswa aktif per sekolah agar tidak meledak saat JOIN
        $subSiswa = DB::connection('pgsql_paud')->table('public.siswa')
          ->select('npsn', DB::raw('COUNT(*) as total_siswa'))
          ->where('status_siswa', 'Aktif')
          ->whereRaw('alumni IS NOT TRUE')
          ->groupBy('npsn');

        // return $subSiswa->get();

        // Query Utama Agregasi (Kecamatan / Kelurahan)
        $isKelurahan = ($pencarian == 'kelurahan');

        $selectFields = [
          'kec.kecamatan_dispenduk',
          DB::raw('COUNT(DISTINCT se.npsn) as jumlah_lembaga'),
          DB::raw('COALESCE(SUM(se.kuota), 0) as jumlah_kuota'),
          DB::raw('COALESCE(SUM(s.total_siswa), 0) as jumlah_siswa')
        ];

        $groupByFields = ['kec.kecamatan_dispenduk'];

        if ($isKelurahan) {
          array_splice($selectFields, 1, 0, 'kel.kelurahan_dispenduk');
          $groupByFields[] = 'kel.kelurahan_dispenduk';
        }

        $data = DB::connection('pgsql_paud')->table('public.sekolah as se')
          ->join('public.master_user as u', 'u.username', '=', 'se.npsn')
          ->join('public.kecamatan as kec', 'kec.kecamatan_kode', '=', 'se.kec_id')
          ->join('public.kelurahan as kel', 'kel.kelurahan_kode', '=', 'se.desa')
          ->leftJoinSub($subSiswa, 's', 's.npsn', '=', 'se.npsn')
          ->select($selectFields)
          ->groupBy($groupByFields)
          ->tap($applyFilter)
          ->get();

        // Formatter DataTables
        $dataBelumKeyed = collect($data_belum)->keyBy(function ($item) use ($isKelurahan) {
          return $isKelurahan
            ? $item['kecamatan'] . '_' . ($item['kelurahan'] ?? '')
            : $item['kecamatan'];
        });

        return DataTables::of($data)
          ->addIndexColumn()
          ->addColumn('jumlah_tidak', function ($w) use ($dataBelumKeyed, $isKelurahan, $usia) {
            $key = $isKelurahan ? $w->kecamatan_dispenduk . '_' . $w->kelurahan_dispenduk : $w->kecamatan_dispenduk;

            $param = [
              'key' => $key,
              'kategori' => 'jumlah_tidak',
              'usia' => $usia,
            ];
            $param = base64_encode(json_encode($param));

            return '<a href="javascript:void(0)" onclick="modal_penduduk(\'' . $param . '\')">' . ($dataBelumKeyed[$key]['jumlah_tidak'] ?? 0) . '</a>';
          })
          ->addColumn('jumlah_belum', function ($w) use ($dataBelumKeyed, $isKelurahan, $usia) {
            $key = $isKelurahan ? $w->kecamatan_dispenduk . '_' . $w->kelurahan_dispenduk : $w->kecamatan_dispenduk;

            $param = [
              'key' => $key,
              'kategori' => 'jumlah_belum',
              'usia' => $usia,
            ];
            $param = base64_encode(json_encode($param));

            return '<a href="javascript:void(0)" onclick="modal_penduduk(\'' . $param . '\')">' . ($dataBelumKeyed[$key]['jumlah_belum'] ?? 0) . '</a>';
          })
          ->addColumn('jumlah_sudah', function ($w) use ($dataBelumKeyed, $isKelurahan, $usia) {
            $key = $isKelurahan ? $w->kecamatan_dispenduk . '_' . $w->kelurahan_dispenduk : $w->kecamatan_dispenduk;

            $param = [
              'key' => $key,
              'kategori' => 'jumlah_sudah',
              'usia' => $usia,
            ];
            $param = base64_encode(json_encode($param));

            return '<a href="javascript:void(0)" onclick="modal_penduduk(\'' . $param . '\')">' . ($dataBelumKeyed[$key]['jumlah_sudah'] ?? 0) . '</a>';
          })
          ->addColumn('jumlah_lembaga', function ($w) use ($dataBelumKeyed, $isKelurahan, $usia) {
            $key = $isKelurahan ? $w->kecamatan_dispenduk . '_' . $w->kelurahan_dispenduk : $w->kecamatan_dispenduk;

            $param = [
              'key' => $key,
              'kategori' => 'jumlah_lembaga',
              'usia' => $usia,
            ];
            $param = base64_encode(json_encode($param));

            return '<a href="javascript:void(0)" onclick="modal_sekolah(\'' . $param . '\')">' . ($w->jumlah_lembaga ?? 0) . '</a>';
          })
          ->addColumn('jumlah_kuota', function ($w) use ($dataBelumKeyed, $isKelurahan, $usia) {
            $key = $isKelurahan ? $w->kecamatan_dispenduk . '_' . $w->kelurahan_dispenduk : $w->kecamatan_dispenduk;

            $param = [
              'key' => $key,
              'kategori' => 'jumlah_kuota',
              'usia' => $usia,
            ];
            $param = base64_encode(json_encode($param));

            $kuota = $w->jumlah_kuota != 0 ? $w->jumlah_kuota : 'Proses pengisian';

            return '<a href="javascript:void(0)" onclick="modal_sekolah(\'' . $param . '\')">' . $kuota . '</a>';
          })
          ->addColumn('jumlah_siswa', function ($w) use ($dataBelumKeyed, $isKelurahan, $usia) {
            $key = $isKelurahan ? $w->kecamatan_dispenduk . '_' . $w->kelurahan_dispenduk : $w->kecamatan_dispenduk;

            $param = [
              'key' => $key,
              'kategori' => 'jumlah_siswa',
              'usia' => $usia,
            ];
            $param = base64_encode(json_encode($param));

            return '<a href="javascript:void(0)" onclick="modal_sekolah(\'' . $param . '\')">' . ($w->jumlah_siswa ?? 0) . '</a>';
          })
          ->addColumn('selisih', fn($w) => ($w->jumlah_kuota ?? 0) - ($w->jumlah_siswa ?? 0))
          ->addColumn('hasil', function ($w) use ($usia) {
            $batas = 12;
            if ($usia == '2') {
              $batas = 15;
            }

            $selisih = ($w->jumlah_kuota ?? 0) - ($w->jumlah_siswa ?? 0);
            $badge = $selisih <= 0 ? 'success' : ($selisih <= $batas ? 'warning' : 'danger');
            $label = $selisih <= 0 ? 'Memenuhi Kuota' : ($selisih <= $batas ? 'Disarankan untuk masuk ke wilayah lain yang terdekat' : 'Dipertimbangkan Satdik baru');

            return "<a href='javascript:void(0)' class='badget badge-{$badge} p-5'>&nbsp;</a> {$label}";
          })
          ->rawColumns([
            'hasil',
            'jumlah_tidak',
            'jumlah_belum',
            'jumlah_sudah',
            'jumlah_lembaga',
            'jumlah_kuota',
            'jumlah_siswa'
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
        $pencarian = $request->pencarian;

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

        $isKelurahan = ($pencarian == 'kelurahan');

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
                ) as jumlah_belum_survey
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
          ->addColumn('total', function ($w) use ($isKelurahan, $usia, $kecamatan, $kelurahan) {
            $key = $isKelurahan ? $kecamatan . '_' . $kelurahan : $kecamatan;

            $param = [
              'key' => $key,
              'kategori' => 'total',
              'usia' => $usia,
            ];
            $param = base64_encode(json_encode($param));

            return '<a href="javascript:void(0)" onclick="modal_penduduk(\'' . $param . '\')">' . number_format(($w->total ?? 0), 0, ',', '.') . '</a>';
          })
          ->addColumn('jumlah_bersekolah', function ($w) use ($isKelurahan, $usia, $kecamatan, $kelurahan) {
            $key = $isKelurahan ? $kecamatan . '_' . $kelurahan : $kecamatan;

            $param = [
              'key' => $key,
              'kategori' => 'jumlah_bersekolah',
              'usia' => $usia,
            ];
            $param = base64_encode(json_encode($param));

            return '<a href="javascript:void(0)" onclick="modal_penduduk(\'' . $param . '\')">' . number_format(($w->jumlah_bersekolah ?? 0), 0, ',', '.') . '</a>';
          })
          ->addColumn('jumlah_belum_bersekolah', function ($w) use ($isKelurahan, $usia, $kecamatan, $kelurahan) {
            $key = $isKelurahan ? $kecamatan . '_' . $kelurahan : $kecamatan;

            $param = [
              'key' => $key,
              'kategori' => 'jumlah_tidak',
              'usia' => $usia,
            ];
            $param = base64_encode(json_encode($param));

            return '<a href="javascript:void(0)" onclick="modal_penduduk(\'' . $param . '\')">' . number_format(($w->jumlah_belum_bersekolah ?? 0), 0, ',', '.') . '</a>';
          })
          ->addColumn('jumlah_survey', function ($w) use ($isKelurahan, $usia, $kecamatan, $kelurahan) {
            $key = $isKelurahan ? $kecamatan . '_' . $kelurahan : $kecamatan;

            $param = [
              'key' => $key,
              'kategori' => 'jumlah_sudah',
              'usia' => $usia,
            ];
            $param = base64_encode(json_encode($param));

            return '<a href="javascript:void(0)" onclick="modal_penduduk(\'' . $param . '\')">' . number_format(($w->jumlah_survey ?? 0), 0, ',', '.') . '</a>';
          })
          ->addColumn('jumlah_belum_survey', function ($w) use ($isKelurahan, $usia, $kecamatan, $kelurahan) {
            $key = $isKelurahan ? $kecamatan . '_' . $kelurahan : $kecamatan;

            $param = [
              'key' => $key,
              'kategori' => 'jumlah_belum',
              'usia' => $usia,
            ];
            $param = base64_encode(json_encode($param));

            return '<a href="javascript:void(0)" onclick="modal_penduduk(\'' . $param . '\')">' . number_format(($w->jumlah_belum_survey ?? 0), 0, ',', '.') . '</a>';
          })
          ->rawColumns([
            'total',
            'jumlah_bersekolah',
            'jumlah_belum_bersekolah',
            'jumlah_survey',
            'jumlah_belum_survey'
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
          case 'total':
            $judul = 'Data Anak ' . $dat_usia . $kecamatan . $kelurahan;
            break;
          case 'jumlah_bersekolah':
            $judul = 'Data Anak Sudah Bersekolah ' . $dat_usia . $kecamatan . $kelurahan;
            break;
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
          case 'jumlah_lembaga':
            $judul = 'Data Lembaga ' . $kecamatan . $kelurahan;;
            break;
          case 'jumlah_kuota':
            $judul = 'Data Kuota Lembaga ' . $kecamatan . $kelurahan;;
            break;
          case 'jumlah_siswa':
            $judul = 'Data Siswa Aktif ' . $kecamatan . $kelurahan;;
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
          $content = view('dinas.rekap_sidia.modal_sekolah', $data)->render();
        }
        return ['content' => $content];
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
