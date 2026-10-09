<?php

namespace App\Http\Controllers\Rekap;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Kegiatan;
use App\Models\Program;

use Auth, Storage, DB, Datatables;

class ProgramController extends Controller
{
  function main()
  {
    $tahun = DB::table('public.program')->select('tahun')->groupBy('tahun')->orderBy('tahun', 'DESC')->get();
    $tingkat = DB::table('public.program')->select('tingkat')->groupBy('tingkat')->orderBy('tingkat', 'DESC')->get();
    $bidang = DB::table('public.program')->select('bidang')->groupBy('bidang')->orderBy('bidang', 'DESC')->get();
    $kecamatan = DB::connection('pgsql_paud')->table('public.kecamatan')->orderBy('kecamatan_dispenduk', 'ASC')->get();

    $data = [
      'tahun' => $tahun,
      'tingkat' => $tingkat,
      'bidang' => $bidang,
      'kecamatan' => $kecamatan,
    ];

    return view('rekap.program.main', $data);
  }

  function data(Request $request)
  {
    // return $request->all();
    $tahun = $request->tahun;
    $tingkat = $request->tingkat;
    $bidang = $request->bidang;
    $kecamatan = $request->kecamatan;
    $kelurahan = $request->kelurahan;

    if ($tahun == '') {
      $program = [];
    } else {
      $where = [];
      $str_where = '';

      array_push($where, "tahun='$tahun'");
      if ($tingkat != 'all') {
        array_push($where, "p.tingkat='$tingkat'");
      }
      if ($bidang != 'all') {
        array_push($where, "bidang='$bidang'");
      }
      if ($tingkat == 'kecamatan') {
        if ($kecamatan != 'all') {
          array_push($where, "u.id_kecamatan='$kecamatan'");
        }
      } else if ($tingkat == 'kelurahan') {
        if ($kelurahan != 'all') {
          array_push($where, "u.id_kelurahan='$kelurahan'");
        } else {
          if ($kecamatan != 'all') {
            $kec = DB::connection('pgsql_paud')->table('public.kecamatan')->where('kecamatan_id', $kecamatan)->first()->kecamatan_kode;
            $kel = DB::connection('pgsql_paud')->table('public.kelurahan')->where('kecamatan_kode', $kec)->get();

            $kel_id = [];
            $str_kel_id = '';
            if ($kel->count() != 0) {
              foreach ($kel as $ke) {
                array_push($kel_id, "'" . $ke->kelurahan_id . "'");
              }
            }

            $str_kel_id = join(',', $kel_id);
            if ($str_kel_id != '') {
              array_push($where, "u.id_kelurahan IN ($str_kel_id)");
            }
          }
        }
      }

      $str_where = join(" AND ", $where);

      if ($str_where == '') {
        $program = DB::connection('pgsql')->table('public.program as p')
          ->leftjoin('public.users as u', 'u.username', 'p.updated_by')->selectRaw("p.*,u.role,u.nama as pembuat,u.id_kecamatan,u.id_kelurahan")->orderBy('nama_kegiatan', 'ASC');
      } else {
        $program = DB::connection('pgsql')->table('public.program as p')
          ->leftjoin('public.users as u', 'u.username', 'p.updated_by')->selectRaw("p.*,u.role,u.nama as pembuat,u.id_kecamatan,u.id_kelurahan")->whereRaw($str_where)->orderBy('nama_kegiatan', 'ASC');
      }
    }

    return DataTables::of($program)
      ->addColumn('nama_kecamatan', function ($j) {
        $nama_kecamatan = '';
        if ($j->tingkat == 'kelurahan') {
          $kel = DB::connection('pgsql_paud')->table('public.kelurahan')->where('kelurahan_id', $j->id_kelurahan)->first();
          $nama_kelurahan = '';
          if (!empty($kel)) {
            $kec = DB::connection('pgsql_paud')->table('public.kecamatan')->where('kecamatan_kode', $kel->kecamatan_kode)->first();
            if (!empty($kec)) {
              $nama_kecamatan = $kec->kecamatan_dispenduk;
            }
          }
        } else {
          $kec = DB::connection('pgsql_paud')->table('public.kecamatan')->where('kecamatan_id', $j->id_kecamatan)->first();
          if (!empty($kec)) {
            $nama_kecamatan = $kec->kecamatan_dispenduk;
          }
        }

        return $nama_kecamatan;
      })
      ->addColumn('nama_kelurahan', function ($j) {
        $kel = DB::connection('pgsql_paud')->table('public.kelurahan')->where('kelurahan_id', $j->id_kelurahan)->first();
        $nama_kelurahan = '';
        if (!empty($kel)) {
          $nama_kelurahan = $kel->kelurahan_dispenduk;
        }

        return $nama_kelurahan;
      })
      ->addColumn('kol_1', function ($j) {
        $arr_waktu = explode(',', $j->waktu);
        if (in_array('1', $arr_waktu)) {
          $ada = '<div style="margin: 0px;width:100%;height:100%;background:green"> V </div>';
        } else {
          $ada = '';
        }
        return $ada;
      })->addColumn('kol_2', function ($j) {
        $arr_waktu = explode(',', $j->waktu);
        if (in_array('2', $arr_waktu)) {
          $ada = '<div style="margin: 0px;width:100%;height:100%;background:green"> V </div>';
        } else {
          $ada = '';
        }
        return $ada;
      })->addColumn('kol_3', function ($j) {
        $arr_waktu = explode(',', $j->waktu);
        if (in_array('3', $arr_waktu)) {
          $ada = '<div style="margin: 0px;width:100%;height:100%;background:green"> V </div>';
        } else {
          $ada = '';
        }
        return $ada;
      })->addColumn('kol_4', function ($j) {
        $arr_waktu = explode(',', $j->waktu);
        if (in_array('4', $arr_waktu)) {
          $ada = '<div style="margin: 0px;width:100%;height:100%;background:green"> V </div>';
        } else {
          $ada = '';
        }
        return $ada;
      })->addColumn('kol_5', function ($j) {
        $arr_waktu = explode(',', $j->waktu);
        if (in_array('5', $arr_waktu)) {
          $ada = '<div style="margin: 0px;width:100%;height:100%;background:green"> V </div>';
        } else {
          $ada = '';
        }
        return $ada;
      })->addColumn('kol_6', function ($j) {
        $arr_waktu = explode(',', $j->waktu);
        if (in_array('6', $arr_waktu)) {
          $ada = '<div style="margin: 0px;width:100%;height:100%;background:green"> V </div>';
        } else {
          $ada = '';
        }
        return $ada;
      })->addColumn('kol_7', function ($j) {
        $arr_waktu = explode(',', $j->waktu);
        if (in_array('7', $arr_waktu)) {
          $ada = '<div style="margin: 0px;width:100%;height:100%;background:green"> V </div>';
        } else {
          $ada = '';
        }
        return $ada;
      })->addColumn('kol_8', function ($j) {
        $arr_waktu = explode(',', $j->waktu);
        if (in_array('8', $arr_waktu)) {
          $ada = '<div style="margin: 0px;width:100%;height:100%;background:green"> V </div>';
        } else {
          $ada = '';
        }
        return $ada;
      })->addColumn('kol_9', function ($j) {
        $arr_waktu = explode(',', $j->waktu);
        if (in_array('9', $arr_waktu)) {
          $ada = '<div style="margin: 0px;width:100%;height:100%;background:green"> V </div>';
        } else {
          $ada = '';
        }
        return $ada;
      })->addColumn('kol_10', function ($j) {
        $arr_waktu = explode(',', $j->waktu);
        if (in_array('10', $arr_waktu)) {
          $ada = '<div style="margin: 0px;width:100%;height:100%;background:green"> V </div>';
        } else {
          $ada = '';
        }
        return $ada;
      })->addColumn('kol_11', function ($j) {
        $arr_waktu = explode(',', $j->waktu);
        if (in_array('11', $arr_waktu)) {
          $ada = '<div style="margin: 0px;width:100%;height:100%;background:green"> V </div>';
        } else {
          $ada = '';
        }
        return $ada;
      })->addColumn('kol_12', function ($j) {
        $arr_waktu = explode(',', $j->waktu);
        if (in_array('12', $arr_waktu)) {
          $ada = '<div style="margin: 0px;width:100%;height:100%;background:green"> V </div>';
        } else {
          $ada = '';
        }
        return $ada;
      })
      ->addIndexColumn()
      ->rawColumns(['kol_1', 'kol_2', 'kol_3', 'kol_4', 'kol_5', 'kol_6', 'kol_7', 'kol_8', 'kol_9', 'kol_10', 'kol_11', 'kol_12',])
      ->make(true);
  }
}
