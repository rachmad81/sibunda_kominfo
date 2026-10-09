<?php

namespace App\Http\Controllers\Rekap;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Kegiatan;
use App\Models\Program;
use App\Http\Controllers\ProgramController;

use Auth, Storage, DB, Datatables;

class SatuanController extends Controller
{
  function main()
  {
    $kecamatan = DB::connection('pgsql_paud')->table('public.kecamatan')->orderBy('kecamatan_dispenduk', 'ASC')->get();

    $data = [
      'kecamatan' => $kecamatan,
    ];

    return view('rekap.satuan.main', $data);
  }

  function data(Request $request)
  {
    // return $request->all();
    $jenjang = $request->jenjang; //: "all"
    $kecamatan = $request->kecamatan; //: "all"
    $kelurahan = $request->kelurahan; //: "all"

    if ($jenjang == '') {
      $sekolah = [];
    } else {
      $where = [];
      $str_where = '';

      if ($jenjang != 'all') {
        array_push($where, "s.jenjang='$jenjang'");
      }

      if ($kecamatan != 'all') {
        $kode_kecamatan = DB::connection('pgsql_paud')->table('public.kecamatan')->where('kecamatan_id', $kecamatan)->first()->kecamatan_kode;
        array_push($where, "s.kec_id='$kode_kecamatan'");
      }

      if ($kelurahan != 'all') {
        $kode_kelurahan = DB::connection('pgsql_paud')->table('public.kelurahan')->where('kelurahan_id', $kelurahan)->first()->kelurahan_kode;
        array_push($where, "s.desa='$kode_kelurahan'");
      }

      $str_where = join(" AND ", $where);

      if ($str_where == '') {
        $sekolah = DB::connection('pgsql_paud')->table('public.sekolah as s')
          ->join('public.master_user as u', 'u.npsn', 's.npsn')
          ->leftjoin('public.kecamatan as kec', 'kec.kecamatan_kode', 's.kec_id')
          ->leftjoin('public.kelurahan as kel', 'kel.kelurahan_kode', 's.desa')
          ->leftjoin('public.pegawai as peg', function ($join) {
            return $join->on('peg.npsn', '=', 's.npsn')->orOn('peg.sekolah_plt', '=', 's.npsn')->where('peg.jabatan', '2')->where('peg.keterangan', 'Aktif');
          })
          ->selectRaw("s.*,kec.kecamatan_dispenduk,kel.kelurahan_dispenduk,'0' as jumlah_pagu,peg.nama as nama_ks,peg.hp as hp_ks")->whereRaw("u.user_enable is true")->orderBy('nama', 'ASC')->get();
      } else {
        $sekolah = DB::connection('pgsql_paud')->table('public.sekolah as s')
          ->join('public.master_user as u', 'u.npsn', 's.npsn')
          ->leftjoin('public.kecamatan as kec', 'kec.kecamatan_kode', 's.kec_id')
          ->leftjoin('public.kelurahan as kel', 'kel.kelurahan_kode', 's.desa')
          ->leftjoin('public.pegawai as peg', function ($join) {
            return $join->on('peg.npsn', '=', 's.npsn')->orOn('peg.sekolah_plt', '=', 's.npsn')->where('peg.jabatan', '2')->where('peg.keterangan', 'Aktif');
          })
          ->selectRaw("s.*,kec.kecamatan_dispenduk,kel.kelurahan_dispenduk,'0' as jumlah_pagu,peg.nama as nama_ks,peg.hp as hp_ks")->whereRaw("u.user_enable is true AND " . $str_where)->orderBy('nama', 'ASC')->get();
      }
    }

    return DataTables::of($sekolah)
      ->addColumn('jumlah_siswa', function ($j) {
        $jumlah = DB::connection('pgsql_paud')->table('public.siswa')->whereRaw("npsn='" . $j->npsn . "' AND status_siswa='Aktif' AND alumni is not true")->get()->count();
        return $jumlah;
      })
      ->addColumn('jumlah_rombel', function ($j) {
        $jumlah = DB::connection('pgsql_paud')->table('public.siswa')->selectRaw("kelas,rombel")->whereRaw("npsn='" . $j->npsn . "' AND status_siswa='Aktif' AND alumni is not true")->groupBy('kelas', 'rombel')->get()->count();
        return $jumlah;
      })
      ->addColumn('jumlah_pegawai', function ($j) {
        $jumlah = DB::connection('pgsql_paud')->table('public.pegawai')->whereRaw("npsn='" . $j->npsn . "' AND keterangan='Aktif'")->get()->count();
        return $jumlah;
      })
      ->addColumn('jumlah_rt', function ($j) {
        $jumlah = DB::connection('pgsql_paud')->table('public.sekolah as s')
          ->join('public.master_user as u', 'u.npsn', 's.npsn')
          ->selectRaw("COUNT(rt) as juml_rt")
          ->whereRaw("u.user_enable is true and desa='" . $j->desa . "'")->groupByRaw("rt")->first();

        return $jumlah->juml_rt;
      })
      ->addColumn('jumlah_rw', function ($j) {
        $jumlah = DB::connection('pgsql_paud')->table('public.sekolah as s')
          ->join('public.master_user as u', 'u.npsn', 's.npsn')
          ->selectRaw("COUNT(rw) as juml_rw")
          ->whereRaw("u.user_enable is true and desa='" . $j->desa . "'")->groupByRaw("rw")->first();

        return $jumlah->juml_rw;
      })
      ->addIndexColumn()
      ->rawColumns([])
      ->make(true);
  }
}
