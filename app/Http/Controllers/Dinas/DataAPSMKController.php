<?php

namespace App\Http\Controllers\Dinas;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use DB, Auth, DataTables;

class DataAPSMKController extends Controller
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
        return view('dinas.data_apsmk.main', $data);
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
          ->selectRaw("
            COUNT(*) as total,
            COUNT(*) FILTER(WHERE (EXTRACT(YEAR FROM AGE(NOW(),a.tgl_lahir)) BETWEEN 3 AND 6)) as usia_paud,
            COUNT(*) FILTER(WHERE(
              CASE WHEN COALESCE(b.nik,'')<>''
              THEN COALESCE(b.status_sekolah,'') IN ('Sudah','sudah_paud','sudah_sd','Akan sekolah','akan_daftar') OR (COALESCE(a.nama_sekolah,'')<>'' AND a.is_aktif_pendidikan is true)
              ELSE (COALESCE(a.nama_sekolah,'')<>'' AND a.is_aktif_pendidikan is true)
              END
            )) as jumlah_bersekolah,
            COUNT(*) FILTER(WHERE(
              CASE WHEN COALESCE(b.nik,'')<>''
              THEN COALESCE(b.status_sekolah,'') IN ('Sudah','sudah_paud','sudah_sd','Akan sekolah','akan_daftar') OR (COALESCE(a.nama_sekolah,'')<>'' AND a.is_aktif_pendidikan is true)
              ELSE (COALESCE(a.nama_sekolah,'')<>'' AND a.is_aktif_pendidikan is true)
              END AND (EXTRACT(YEAR FROM AGE(NOW(),a.tgl_lahir)) BETWEEN 3 AND 6)
            )) as jumlah_bersekolah_paud
          ")
          ->when($pencarian, function ($w) use ($pencarian, $arr_kec, $arr_kel) {
            if ($pencarian == 'kelurahan') {
              return $w->selectRaw("
                    COALESCE(NULLIF(a.kecamatan_dom,''), a.kecamatan) as kec_dom,
                    COALESCE(NULLIF(a.kelurahan_dom,''), a.kelurahan) as kel_dom
                ")
                ->whereIn(DB::raw("COALESCE(NULLIF(a.kecamatan_dom,''), a.kecamatan)"), $arr_kec)
                ->whereIn(DB::raw("COALESCE(NULLIF(a.kelurahan_dom,''), a.kelurahan)"), $arr_kel)
                ->groupByRaw("
                    COALESCE(NULLIF(a.kecamatan_dom,''), a.kecamatan),
                    COALESCE(NULLIF(a.kelurahan_dom,''), a.kelurahan)
                ");
            } else {
              return $w->selectRaw("
                    COALESCE(NULLIF(a.kecamatan_dom,''), a.kecamatan) as kec_dom
                ")
                ->whereIn(DB::raw("COALESCE(NULLIF(a.kecamatan_dom,''), a.kecamatan)"), $arr_kec)
                ->groupByRaw("
                    COALESCE(NULLIF(a.kecamatan_dom,''), a.kecamatan)
                ");
            }
          })
          ->whereRaw("
            ((EXTRACT(YEAR FROM AGE(NOW(),a.tgl_lahir)) BETWEEN 0 AND 6) AND
            a.is_aktif_kependudukan IS TRUE AND a.status_keberadaan='ADA')
          ")
          ->get();

        // 3. Petakan hasil query ke dalam Array HashMap (O(1) Access Time)
        $total = [];
        $usia_paud = [];
        $bersekolah = [];
        $bersekolah_paud = [];
        foreach ($raw_belum as $row) {
          if ($pencarian == 'kelurahan') {
            $key = $row->kec_dom . '|' . $row->kel_dom;
          } else {
            $key = $row->kec_dom;
          }
          $total[$key] = $row->total;
          $usia_paud[$key] = $row->usia_paud;
          $bersekolah[$key] = $row->jumlah_bersekolah;
          $bersekolah_paud[$key] = $row->jumlah_bersekolah_paud;
        }


        $total;
        $usia_paud;
        $bersekolah;
        $bersekolah_paud;

        // 4. Gabungkan ke struktur $dt_kecamatan tanpa query tambahan
        $data_belum = $dt_kecamatan->map(function ($k) use ($pencarian, $total, $usia_paud, $bersekolah, $bersekolah_paud) {
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
          $jumlah_total = 0;
          $jumlah_bersekolah = 0;
          $jumlah_usia_paud = 0;
          $jumlah_bersekolah_paud = 0;

          foreach ($unique_keys as $key) {
            $jumlah_total += $total[$key] ?? 0;
            $jumlah_bersekolah += $bersekolah[$key] ?? 0;
            $jumlah_usia_paud += $usia_paud[$key] ?? 0;
            $jumlah_bersekolah_paud += $bersekolah_paud[$key] ?? 0;
          }

          if ($pencarian == 'kelurahan') {
            return [
              'kecamatan'               => $k->kecamatan_dispenduk,
              'kelurahan'               => $k->kelurahan_dispenduk,
              'total'                   => $jumlah_total,
              'usia_paud'               => $jumlah_usia_paud,
              'jumlah_bersekolah'       => $jumlah_bersekolah,
              'jumlah_bersekolah_paud'  => $jumlah_bersekolah_paud,
            ];
          } else {
            return [
              'kecamatan'               => $k->kecamatan_dispenduk,
              'total'                   => $jumlah_total,
              'usia_paud'               => $jumlah_usia_paud,
              'jumlah_bersekolah'       => $jumlah_bersekolah,
              'jumlah_bersekolah_paud'  => $jumlah_bersekolah_paud,
            ];
          }
        })->sortBy('kecamatan')->values()->toArray();

        $isKelurahan = ($pencarian == 'kelurahan');

        return DataTables::of($data_belum)
          ->addIndexColumn()
          ->addColumn('total', function ($r) use ($isKelurahan) {
            $key = $isKelurahan ? $r['kecamatan'] . '_' . $r['kelurahan'] : $r['kecamatan'];

            $param = [
              'key' => $key,
              'kategori' => 'total',
              'usia' => '06',
            ];
            $param = base64_encode(json_encode($param));
            $tombol = '<a href="javascript:void(0)" onclick="modal_penduduk(\'' . $param . '\')">' . number_format($r['total'], 0, '', '.') . '</a>';
            return $tombol;
          })
          ->addColumn('usia_paud', function ($r) use ($isKelurahan) {
            $key = $isKelurahan ? $r['kecamatan'] . '_' . $r['kelurahan'] : $r['kecamatan'];

            $param = [
              'key' => $key,
              'kategori' => 'total',
              'usia' => '36',
            ];
            $param = base64_encode(json_encode($param));
            $tombol = '<a href="javascript:void(0)" onclick="modal_penduduk(\'' . $param . '\')">' . number_format($r['usia_paud'], 0, '', '.') . '</a>';
            return $tombol;
          })
          ->addColumn('jumlah_bersekolah', function ($r) use ($isKelurahan) {
            $key = $isKelurahan ? $r['kecamatan'] . '_' . $r['kelurahan'] : $r['kecamatan'];

            $param = [
              'key' => $key,
              'kategori' => 'jumlah_bersekolah',
              'usia' => '06',
            ];
            $param = base64_encode(json_encode($param));
            $tombol = '<a href="javascript:void(0)" onclick="modal_penduduk(\'' . $param . '\')">' . number_format($r['jumlah_bersekolah'], 0, '', '.') . '</a>';
            return $tombol;
          })
          ->addColumn('jumlah_bersekolah_paud', function ($r) use ($isKelurahan) {
            $key = $isKelurahan ? $r['kecamatan'] . '_' . $r['kelurahan'] : $r['kecamatan'];

            $param = [
              'key' => $key,
              'kategori' => 'jumlah_bersekolah',
              'usia' => '36',
            ];
            $param = base64_encode(json_encode($param));
            $tombol = '<a href="javascript:void(0)" onclick="modal_penduduk(\'' . $param . '\')">' . number_format($r['jumlah_bersekolah_paud'], 0, '', '.') . '</a>';
            return $tombol;
          })
          ->addColumn('aps', function ($r) {
            $aps = ($r['jumlah_bersekolah'] / $r['total']) * 100;
            $tombol = number_format($aps, 3, ',', '.') . ' %';
            return $tombol;
          })
          ->addColumn('apm', function ($r) {
            $apm = ($r['jumlah_bersekolah_paud'] / $r['usia_paud']) * 100;
            $tombol = number_format($apm, 3, ',', '.') . ' %';
            return $tombol;
          })
          ->addColumn('apk', function ($r) {
            $apk = ($r['jumlah_bersekolah'] / $r['usia_paud']) * 100;
            $tombol = number_format($apk, 3, ',', '.') . ' %';
            return $tombol;
          })
          ->rawColumns([
            'total',
            'usia_paud',
            'jumlah_bersekolah',
            'jumlah_bersekolah_paud',
            'aps',
            'apm',
            'apk',
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

        // 1. Ambil data master kecamatan & kelurahan
        $dt_kecamatan = DB::table('kecamatan as kec')
          ->join('kelurahan as kel', 'kel.kecamatan_kode', '=', 'kec.kecamatan_kode')

          ->selectRaw("
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
          ->selectRaw("
            COUNT(*) as total,
            COUNT(*) FILTER(WHERE (EXTRACT(YEAR FROM AGE(NOW(),a.tgl_lahir)) BETWEEN 3 AND 6)) as usia_paud,
            COUNT(*) FILTER(WHERE(
              CASE WHEN COALESCE(b.nik,'')<>''
              THEN COALESCE(b.status_sekolah,'') IN ('Sudah','sudah_paud','sudah_sd','Akan sekolah','akan_daftar') OR (COALESCE(a.nama_sekolah,'')<>'' AND a.is_aktif_pendidikan is true)
              ELSE (COALESCE(a.nama_sekolah,'')<>'' AND a.is_aktif_pendidikan is true)
              END
            )) as jumlah_bersekolah,
            COUNT(*) FILTER(WHERE(
              CASE WHEN COALESCE(b.nik,'')<>''
              THEN COALESCE(b.status_sekolah,'') IN ('Sudah','sudah_paud','sudah_sd','Akan sekolah','akan_daftar') OR (COALESCE(a.nama_sekolah,'')<>'' AND a.is_aktif_pendidikan is true)
              ELSE (COALESCE(a.nama_sekolah,'')<>'' AND a.is_aktif_pendidikan is true)
              END AND (EXTRACT(YEAR FROM AGE(NOW(),a.tgl_lahir)) BETWEEN 3 AND 6)
            )) as jumlah_bersekolah_paud
          ")
          ->whereIn(DB::raw("COALESCE(NULLIF(a.kecamatan_dom,''), a.kecamatan)"), $arr_kec)
          ->whereIn(DB::raw("COALESCE(NULLIF(a.kelurahan_dom,''), a.kelurahan)"), $arr_kel)
          ->whereRaw("
            ((EXTRACT(YEAR FROM AGE(NOW(),a.tgl_lahir)) BETWEEN 0 AND 6) AND
            a.is_aktif_kependudukan IS TRUE AND a.status_keberadaan='ADA')
          ")
          ->get();

        return DataTables::of($raw_belum)
          ->addIndexColumn()
          ->addColumn('total', function ($r) {
            $key = 'All_All';

            $param = [
              'key' => $key,
              'kategori' => 'total',
              'usia' => '06',
            ];
            $param = base64_encode(json_encode($param));

            $tombol = '<a href="javascript:void(0)" onclick="modal_penduduk(\'' . $param . '\')">' . number_format($r->total, 0, '', '.') . '</a>';
            return $tombol;
          })
          ->addColumn('usia_paud', function ($r) {
            $key = 'All_All';

            $param = [
              'key' => $key,
              'kategori' => 'total',
              'usia' => '36',
            ];
            $param = base64_encode(json_encode($param));
            $tombol = '<a href="javascript:void(0)" onclick="modal_penduduk(\'' . $param . '\')">' . number_format($r->usia_paud, 0, '', '.') . '</a>';
            return $tombol;
          })
          ->addColumn('jumlah_bersekolah', function ($r) {
            $key = 'All_All';

            $param = [
              'key' => $key,
              'kategori' => 'jumlah_bersekolah',
              'usia' => '06',
            ];
            $param = base64_encode(json_encode($param));
            $tombol = '<a href="javascript:void(0)" onclick="modal_penduduk(\'' . $param . '\')">' . number_format($r->jumlah_bersekolah, 0, '', '.') . '</a>';
            return $tombol;
          })
          ->addColumn('jumlah_bersekolah_paud', function ($r) {
            $key = 'All_All';

            $param = [
              'key' => $key,
              'kategori' => 'jumlah_bersekolah',
              'usia' => '36',
            ];
            $param = base64_encode(json_encode($param));
            $tombol = '<a href="javascript:void(0)" onclick="modal_penduduk(\'' . $param . '\')">' . number_format($r->jumlah_bersekolah_paud, 0, '', '.') . '</a>';
            return $tombol;
          })
          ->addColumn('aps', function ($r) {
            $aps = ($r->jumlah_bersekolah / $r->total) * 100;
            $tombol = number_format($aps, 3, ',', '.') . ' %';
            return $tombol;
          })
          ->addColumn('apm', function ($r) {
            $apm = ($r->jumlah_bersekolah_paud / $r->usia_paud) * 100;
            $tombol = number_format($apm, 3, ',', '.') . ' %';
            return $tombol;
          })
          ->addColumn('apk', function ($r) {
            $apk = ($r->jumlah_bersekolah / $r->usia_paud) * 100;
            $tombol = number_format($apk, 3, ',', '.') . ' %';
            return $tombol;
          })
          ->rawColumns([
            'total',
            'usia_paud',
            'jumlah_bersekolah',
            'jumlah_bersekolah_paud',
            'aps',
            'apm',
            'apk',
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
          $kecamatan = $key[0] == 'All' ? '' : ' Kecamatan ' . $key[0];
          $kelurahan = $key[1] == 'All' ? '' : ' Kelurahan ' . $key[1];
        } else {
          $kecamatan = $key[0] == 'All' ? '' : ' Kecamatan ' . $key[0];
          $kelurahan = '';
        }

        if ($kecamatan == '' && $kelurahan == '') {
          $kecamatan = ' Kota Surabaya';
        }

        $dt_usia = ($param->usia == 06) ? ' Usia 0 - 6 Tahun ' : ' Usia 3 - 6 Tahun ';

        switch ($kategori) {
          case 'total':
            $judul = 'Data Anak' . $dt_usia . $kecamatan . $kelurahan;
            break;
          case 'jumlah_bersekolah':
            $judul = 'Data Anak Bersekolah' . $dt_usia . $kecamatan . $kelurahan;
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
