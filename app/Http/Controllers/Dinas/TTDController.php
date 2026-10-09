<?php

namespace App\Http\Controllers\Dinas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth, DB, DataTables;

class TTDController extends Controller
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
        return view('dinas.rekap_ttd.main', $data);
        break;

      case 'get_data':

        $kecamatan = $request->kecamatan ?? '';
        $kecamatan_kode = $request->kecamatan_kode ?? '';
        $kelurahan = $request->kelurahan ?? '';
        $kelurahan_kode = $request->kelurahan_kode ?? 'All';
        $pencarian = $request->pencarian ?? '';
        $arr_jenjang = $request->jenjang ?? [];
        $arr_jenjang = array_diff($arr_jenjang, ['All']);
        $tingkat = $request->tingkat ?? 'PAUD';

        $str_jenjang = '\'' . implode('\',\'', $arr_jenjang) . '\'';

        switch ($tingkat) {
          case 'PAUD':
            $conn = 'pgsql_paud';
            break;
          case 'SD':
            $conn = 'db_sd';
            break;
          case 'SMP':
            $conn = 'db_smp';
            break;

          default:
            $conn = 'pgsql_paud';
            break;
        }

        $siswa = DB::connection($conn)->table('iko.tablet_tambah_darah as l')
          ->join('public.sekolah as se', 'se.npsn', 'l.npsn')
          ->join('public.siswa as s', 's.npsn', 'se.npsn')
          ->join('public.kecamatan as kec', 'kec.kecamatan_kode', 'se.kec_id')
          ->join('public.kelurahan as kel', 'kel.kelurahan_kode', 'se.desa')
          ->selectRaw("
            kec.kecamatan_dispenduk as kecamatan,
            kec.kecamatan_kode,
            kel.kelurahan_dispenduk as kelurahan,
            kel.kelurahan_kode,
            COUNT(DISTINCT s.siswa_id) as jumlah_siswa
          ")
          ->where('s.status_siswa', 'Aktif')
          ->where('s.kelamin', 'P')
          ->where(function ($w) {
            return $w->whereNull('s.alumni')->orWhere('s.alumni', false);
          })
          ->whereIn('se.jenjang', $arr_jenjang)
          ->when($kecamatan_kode !== 'All', function ($w) use ($kecamatan_kode) {
            return $w->where('kec.kecamatan_kode', $kecamatan_kode);
          })
          ->when($kelurahan_kode !== 'All', function ($w) use ($kelurahan_kode) {
            return $w->where('kel.kelurahan_kode', $kelurahan_kode);
          })
          ->groupBy(
            'kec.kecamatan_dispenduk',
            'kec.kecamatan_kode',
            'kel.kelurahan_dispenduk',
            'kel.kelurahan_kode',
          );

        $data = DB::connection($conn)->table('iko.tablet_tambah_darah as l')
          ->join('public.sekolah as se', 'se.npsn', 'l.npsn')
          ->join('public.kecamatan as kec', 'kec.kecamatan_kode', 'se.kec_id')
          ->join('public.kelurahan as kel', 'kel.kelurahan_kode', 'se.desa')
          ->leftJoinSub($siswa, 's', function ($j) {
            return $j->on('s.kecamatan_kode', '=', 'kec.kecamatan_kode')->on('s.kelurahan_kode', '=', 'kel.kelurahan_kode');
          })
          ->selectRaw("
            kec.kecamatan_dispenduk as kecamatan,
            kec.kecamatan_kode,
            kel.kelurahan_dispenduk as kelurahan,
            kel.kelurahan_kode,
            COUNT(DISTINCT l.npsn) as jumlah_lembaga,
            MAX(s.jumlah_siswa) as jumlah_siswa
          ")
          ->whereIn('se.jenjang', $arr_jenjang)
          ->when($kecamatan_kode !== 'All', function ($w) use ($kecamatan_kode) {
            return $w->where('kec.kecamatan_kode', $kecamatan_kode);
          })
          ->when($kelurahan_kode !== 'All', function ($w) use ($kelurahan_kode) {
            return $w->where('kel.kelurahan_kode', $kelurahan_kode);
          })
          ->groupBy(
            'kec.kecamatan_dispenduk',
            'kec.kecamatan_kode',
            'kel.kelurahan_dispenduk',
            'kel.kelurahan_kode',
          );

        if ($pencarian == 'baris') {
          return DataTables::queryBuilder($data)
            ->addIndexColumn()
            ->addColumn('jumlah_lembaga', function ($a) {
              return '<a href="javascript:void(0)" onclick="modal(\'' . $a->kecamatan_kode . '\',\'' . $a->kelurahan_kode . '\',\'jumlah_lembaga\')">' . $a->jumlah_lembaga . '</a>';
            })
            ->addColumn('jumlah_siswa', function ($a) {
              return '<a href="javascript:void(0)" onclick="modal(\'' . $a->kecamatan_kode . '\',\'' . $a->kelurahan_kode . '\',\'jumlah_siswa\')">' . $a->jumlah_siswa . '</a>';
            })
            ->rawColumns(['jumlah_siswa', 'jumlah_lembaga'])
            ->make(true);
        } else {
          $baris = collect((clone $data)->get());
          $total = [
            'jumlah_lembaga' => $baris->sum('jumlah_lembaga'),
            'jumlah_siswa' => $baris->sum('jumlah_siswa'),
          ];

          $data = [
            'total' => $total,
          ];

          return view('dinas.rekap_ttd.total', $data)->render();
        }

        break;

      case 'modal':
        // return $request->all();
        $kategori = $request->kategori ?? '';
        $jenjang = $request->jenjang ?? [];
        $kecamatan_kode = $request->kecamatan_kode ?? 'All';
        $kelurahan_kode = $request->kelurahan_kode ?? 'All';
        $jenjang = array_diff($jenjang, ['All']);
        $tingkat = $request->tingkat ?? 'PAUD';

        switch ($tingkat) {
          case 'PAUD':
            $conn = 'pgsql_paud';
            break;
          case 'SD':
            $conn = 'db_sd';
            break;
          case 'SMP':
            $conn = 'db_smp';
            break;

          default:
            $conn = 'pgsql_paud';
            break;
        }

        $judul = 'Data ';
        switch ($kategori) {
          case 'jumlah_lembaga':
            $judul .= 'Lembaga ';
            break;

          case 'jumlah_siswa':
            $judul .= 'Siswa ';
            break;

          default:
            $judul .= '';
            break;
        }

        if ($kecamatan_kode != 'All') {
          $kecamatan = DB::connection($conn)->table('public.kecamatan as kec')
            ->join('public.kelurahan as kel', 'kel.kecamatan_kode', 'kec.kecamatan_kode')
            ->selectRaw("kec.kecamatan_dispenduk,kel.kelurahan_dispenduk")
            ->where('kec.kecamatan_kode', $kecamatan_kode)
            ->when($kelurahan_kode !== 'All', function ($w) use ($kelurahan_kode) {
              $w->where('kel.kelurahan_kode', $kelurahan_kode);
            })->first();
          $judul .= 'Kecamatan ' . $kecamatan->kecamatan_dispenduk ?? '';
          if ($kelurahan_kode !== 'All') {
            $judul .= ', Kelurahan ' . $kecamatan->kelurahan_dispenduk ?? '';
          }
        } else {
          $judul .= 'Kota Surabaya';
        }

        $data = [
          'judul' => $judul,
          'kategori' => $kategori,
          'jenjang' => $jenjang,
          'kecamatan_kode' => $kecamatan_kode,
          'kelurahan_kode' => $kelurahan_kode,
          'tingkat' => $tingkat,
        ];

        return view('dinas.rekap_ttd.modal', $data)->render();
        break;

      case 'data_modal':
        // return $request->all();
        $kategori = $request->kategori ?? '';
        $jenjang = $request->jenjang ?? [];
        $kecamatan_kode = $request->kecamatan_kode ?? 'All';
        $kelurahan_kode = $request->kelurahan_kode ?? 'All';
        $arr_jenjang = array_diff($jenjang, ['All']);
        $tingkat = $request->tingkat ?? 'PAUD';

        switch ($tingkat) {
          case 'PAUD':
            $conn = 'pgsql_paud';
            break;
          case 'SD':
            $conn = 'db_sd';
            break;
          case 'SMP':
            $conn = 'db_smp';
            break;

          default:
            $conn = 'pgsql_paud';
            break;
        }

        $base = DB::connection($conn)->table('iko.tablet_tambah_darah as l')
          ->join('public.sekolah as se', 'se.npsn', 'l.npsn')
          ->join('public.kecamatan as kec', 'kec.kecamatan_kode', 'se.kec_id')
          ->join('public.kelurahan as kel', 'kel.kelurahan_kode', 'se.desa')
          ->whereIn('se.jenjang', $arr_jenjang)
          ->when($kecamatan_kode !== 'All', function ($w) use ($kecamatan_kode) {
            return $w->where('kec.kecamatan_kode', $kecamatan_kode);
          })
          ->when($kelurahan_kode !== 'All', function ($w) use ($kelurahan_kode) {
            return $w->where('kel.kelurahan_kode', $kelurahan_kode);
          });

        switch ($kategori) {
          case 'jumlah_lembaga':
            $data = (clone $base)
              ->selectRaw("se.npsn,se.nama as nama_sekolah,kel.kelurahan_dispenduk,kec.kecamatan_dispenduk")
              ->groupByRaw("se.npsn,se.nama,kel.kelurahan_dispenduk,kec.kecamatan_dispenduk")
              ->orderByRaw("se.npsn");
            break;

          case 'jumlah_siswa':
            $data = (clone $base)
              ->join('public.siswa as s', 's.npsn', 'se.npsn')
              ->where('s.status_siswa', 'Aktif')
              ->where('s.kelamin', 'P')
              ->where(function ($w) {
                return $w->whereNull('s.alumni')->orWhere('s.alumni', false);
              })
              ->selectRaw("se.npsn,se.nama as nama_sekolah,kel.kelurahan_dispenduk,kec.kecamatan_dispenduk,s.nama as nama_siswa,s.kelas,s.rombel")
              ->groupByRaw("se.npsn,se.nama,kel.kelurahan_dispenduk,kec.kecamatan_dispenduk,s.nama,s.kelas,s.rombel")
              ->orderByRaw("se.npsn,s.kelas,s.rombel,s.nama");
            break;

          default:
            $data .= [];
            break;
        }

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
