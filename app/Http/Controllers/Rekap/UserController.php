<?php

namespace App\Http\Controllers\Rekap;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Kegiatan;
use App\Models\Program;
use App\Http\Controllers\ProgramController;

use Auth, Storage, DB, Datatables;

class UserController extends Controller
{
  function main()
  {
    $kecamatan = DB::connection('pgsql_paud')->table('public.kecamatan')->orderBy('kecamatan_dispenduk', 'ASC')->get();

    $data = [
      'kecamatan' => $kecamatan,
    ];

    return view('rekap.user.main', $data);
  }

  function data(Request $request)
  {
    $role = $request->role; //: "all"
    $tingkat = $request->tingkat; //: "all"
    $kecamatan = $request->kecamatan; //: "all"
    $kelurahan = $request->kelurahan; //: "all"

    $where = [];

    if ($role == 'all') {
    } else {
      array_push($where, "u.tingkat='$role'");
    }

    if ($tingkat == 'all') {
    } else {
      array_push($where, "u.tingkat='$tingkat'");
      if ($tingkat == 'kecamatan') {
        if ($kecamatan == 'all') {
        } else {
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
      } else {
      }
    }

    $str_where = join(" AND ", $where);

    if ($role == '') {
      $user = [];
    } else {
      if ($str_where != '') {
        $user = DB::table('public.users as u')
          ->leftjoin('public.jabatan_pokja as j', 'j.id', 'u.id_jabatan')
          ->leftjoin('public.berkas_pokja as b', 'b.updated_by', 'u.id')
          ->selectRaw("u.*,j.nama_jabatan,b.no_sk_penetapan_bunda,b.path_sk_penetapan_bunda,b.tgl_sk_penetapan_bunda,b.pejabat_sk_penetapan_bunda,b.no_sk_penetapan_pokja,b.path_sk_penetapan_pokja,b.tgl_sk_penetapan_pokja,b.pejabat_sk_penetapan_pokja")->whereRaw("u.deleted_at is null AND " . $str_where)->orderBy('nama', 'ASC')->get();
      } else {
        $user = DB::table('public.users as u')
          ->leftjoin('public.jabatan_pokja as j', 'j.id', 'u.id_jabatan')
          ->leftjoin('public.berkas_pokja as b', 'b.updated_by', 'u.id')
          ->selectRaw("u.*,j.nama_jabatan,b.no_sk_penetapan_bunda,b.path_sk_penetapan_bunda,b.tgl_sk_penetapan_bunda,b.pejabat_sk_penetapan_bunda,b.no_sk_penetapan_pokja,b.path_sk_penetapan_pokja,b.tgl_sk_penetapan_pokja,b.pejabat_sk_penetapan_pokja")->whereRaw("u.deleted_at is null")->orderBy('nama', 'ASC')->get();
      }
    }

    return DataTables::of($user)
      ->addIndexColumn()
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
      ->addColumn('wilayah', function ($j) {
        if ($j->tingkat == 'kecamatan') {
          $kec = DB::connection('pgsql_paud')->table('public.kecamatan')->whereRaw("kecamatan_id='$j->id_kecamatan'")->first();
          $wilayah = 'KEC. ' . $kec->kecamatan_dispenduk;
        } else if ($j->tingkat == 'kelurahan') {
          $kec = DB::connection('pgsql_paud')->table('public.kelurahan')->whereRaw("kelurahan_id='$j->id_kelurahan'")->first();
          $wilayah = 'KEL. ' . $kec->kelurahan_dispenduk;
        } else {
          $wilayah = 'Kota Surabaya';
        }
        return $wilayah;
      })
      ->addColumn('detail_sk_bunda', function ($j) {
        $detail = '';
        $detail .= '<b>SK Penetapan Bunda</b><br>';
        $detail .= $j->no_sk_penetapan_bunda . '<br>';
        $detail .= '<b>Tgl SK Penetapan Bunda</b><br>';
        $detail .= $j->tgl_sk_penetapan_bunda . '<br>';
        $detail .= '<b>Pejabat SK Penetapan Bunda</b><br>';
        $detail .= $j->pejabat_sk_penetapan_bunda . '<br>';
        $detail .= '<b>File SK</b><br>';
        $detail .= '<a href="https://dispendik.surabaya.go.id/sibunda_file/' . $j->path_sk_penetapan_bunda . '" target="_blank">https://dispendik.surabaya.go.id/sibunda_file/' . $j->path_sk_penetapan_bunda . '</a><br>';

        return $detail;
      })
      ->addColumn('detail_sk_pokja', function ($j) {
        $detail = '';
        $detail .= '<b>SK Penetapan Pokja</b><br>';
        $detail .= $j->no_sk_penetapan_pokja . '<br>';
        $detail .= '<b>Tgl SK Penetapan Pokja</b><br>';
        $detail .= $j->tgl_sk_penetapan_pokja . '<br>';
        $detail .= '<b>Pejabat SK Penetapan Pokja</b><br>';
        $detail .= $j->pejabat_sk_penetapan_pokja . '<br>';
        $detail .= '<b>File SK</b><br>';
        $detail .= '<a href="https://dispendik.surabaya.go.id/sibunda_file/' . $j->path_sk_penetapan_pokja . '" target="_blank">https://dispendik.surabaya.go.id/sibunda_file/' . $j->path_sk_penetapan_pokja . '</a><br>';

        return $detail;
      })
      ->rawColumns(['detail_sk_bunda', 'detail_sk_pokja'])
      ->make(true);
  }
}
