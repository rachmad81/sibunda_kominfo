<?php

namespace App\Http\Controllers\Monev;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth, Redirect, Session, DB, DataTables;

class BospController extends Controller
{
  function halaman(Request $request)
  {
    $halaman = $request->halaman;

    switch ($halaman) {
      case 'main':
        $tahun = DB::connection('db_simpaud')->table('public.bop_penerimaans as b')
          ->join('public.periodes as p', 'p.id', 'b.periode_id')
          ->selectRaw("b.tahun,p.lainnya as tahap")
          ->groupByRaw("b.tahun,p.lainnya")
          ->orderByRaw("tahun DESC,tahap ASC")
          ->get();

        $tahun = collect($tahun)->groupBy('tahun')->map(function ($a) {
          return (object)$a;
        });

        $kecamatan = MasterController::data_kecamatan($request);

        $data = [
          'tahun' => $tahun,
          'kecamatan' => $kecamatan['kecamatan'],
          'data_kecamatan' => $kecamatan['semua'],
        ];
        return view('monev_baru.bosp.main', $data);
        break;

      case 'get_data':
        // return $request->all();
        $tingkat = Auth::getUser()->tingkat;
        $kecamatan = $request->kecamatan ?? '';
        $kelurahan = $request->kelurahan ?? '';
        $tahun = $request->tahun ?? '';
        $tahap = $request->tahap ?? '';

        $anomali = $this->anomali_nama($request);

        $sekolah = DB::connection('pgsql_paud')
          ->table('public.sekolah as s')
          ->join('public.master_user as u', 'u.username', 's.npsn')
          ->join('public.kecamatan as kec', 'kec.kecamatan_kode', 's.kec_id')
          ->join('public.kelurahan as kel', 'kel.kelurahan_kode', 's.desa')
          ->selectRaw("s.npsn,kec.kecamatan_dispenduk,kel.kelurahan_dispenduk")
          ->whereRaw("u.user_enable is true")
          ->whereIn('s.jenjang', ['KB', 'SPS', 'TK', 'KB', 'RA'])
          ->when($kecamatan != 'all', function ($w) use ($kecamatan) {
            $w->where('kec.kecamatan_dispenduk', $kecamatan);
          })
          ->when($kelurahan != 'all', function ($w) use ($kelurahan) {
            $w->where('kel.kelurahan_dispenduk', $kelurahan);
          })
          ->get();

        if ($kecamatan == 'all') {
          $npsn = [];
        } else {
          $npsn = collect($sekolah)->pluck('npsn')->toARray();
        }

        $data = DB::connection('db_simpaud')
          ->table('public.bop_penerimaans as b')
          ->join('public.periodes as p', 'p.id', 'b.periode_id')
          ->selectRaw("b.npsn,b.nama,b.nominal,b.tahun,p.lainnya as tahap")
          ->whereNotNull('nominal')
          ->when($tahun, function ($w) use ($tahun) {
            $w->where('b.tahun', $tahun);
          })
          ->when($tahap, function ($w) use ($tahap) {
            $w->where('p.lainnya', $tahap);
          })
          ->when(count($npsn) > 0, function ($q) use ($npsn) {
            $q->whereIn('b.npsn', $npsn);
          });

        return DataTables::queryBuilder($data)
          ->addIndexColumn()
          ->filterColumn('tahap', function ($w, $keyword) {
            $w->where('b.tahun', 'ilike', '%' . $keyword . '%');
          })
          ->orderColumn('tahap', function ($w, $order) {
            $w->orderBy('b.tahun', $order);
          })
          ->editColumn('nominal', function ($i) {
            return number_format($i->nominal, 0, '.', ',');
          })
          ->filterColumn('nominal', function ($w, $keyword) {
            $w->where('b.nominal', 'ilike', '%' . $keyword . '%');
          })
          ->orderColumn('nominal', function ($w, $order) {
            $w->orderBy('b.nominal', $order);
          })
          ->addColumn('kecamatan', function ($i) use ($sekolah) {
            $dt_sekolah = collect($sekolah)->where('npsn', $i->npsn)->first();
            return $dt_sekolah->kecamatan_dispenduk ?? '';
          })
          ->filterColumn('kecamatan', function ($w, $keyword) use ($sekolah) {
            $dt_sekolah = collect($sekolah)->filter(function ($item) use ($keyword) {
              return str_contains($item->kecamatan_dispenduk, $keyword);
            })->pluck('npsn')->toArray();

            return $w->whereIn('npsn', $dt_sekolah);
          })
          ->addColumn('kelurahan', function ($i) use ($sekolah) {
            $dt_sekolah = collect($sekolah)->where('npsn', $i->npsn)->first();
            return $dt_sekolah->kelurahan_dispenduk ?? '';
          })
          ->filterColumn('kelurahan', function ($w, $keyword) use ($sekolah) {
            $dt_sekolah = collect($sekolah)->filter(function ($item) use ($keyword) {
              return str_contains($item->kelurahan_dispenduk, $keyword);
            })->pluck('npsn')->toArray();

            return $w->whereIn('npsn', $dt_sekolah);
          })
          ->make(true);
        break;

      default:
        return '404 - Halaman tidka ditemukan';
        break;
    }
  }

  function anomali_nama(Request $request)
  {
    // return $request->all();
    $kecamatan = $request->kecamatan ?? '';
    $kelurahan = $request->kelurahan ?? '';

    $arr_kecamatan = [];
    $arr_kelurahan = [];

    if ($kecamatan != '' && $kecamatan != 'all') {
      $data_kecamatan = DB::table('public.kecamatan')->where('kecamatan_dispenduk', $kecamatan)->first();
      $arr_kecamatan = [$data_kecamatan->kecamatan_dispenduk, $data_kecamatan->dispenduk_baru];
    }

    if ($kelurahan != '' && $kelurahan != 'all') {
      $data_kelurahan = DB::table('public.kelurahan')->where('kelurahan_dispenduk', $kelurahan)->first();
      $arr_kelurahan = [$data_kelurahan->kelurahan_dispenduk, $data_kelurahan->dispenduk_baru];
    }

    return ['arr_kecamatan' => $arr_kecamatan, 'arr_kelurahan' => $arr_kelurahan];
  }
}
