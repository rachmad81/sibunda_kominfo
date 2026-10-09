<?php

namespace App\Http\Controllers\Rekap;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Kegiatan;
use App\Models\Program;
use App\Http\Controllers\ProgramController;

use Auth, Storage, DB;

class UmumController extends Controller
{
  function get_kelurahan(Request $request)
  {
    $kode_kec = $request->kec_kode;

    $kecamatan = DB::connection('pgsql_paud')->table('public.kecamatan')->whereRaw("kecamatan_id='$kode_kec'")->first();
    if (!empty($kecamatan)) {
      $kelurahan = DB::connection('pgsql_paud')->table('public.kelurahan')->whereRaw("kecamatan_kode='" . $kecamatan->kecamatan_kode . "'")->get();
    } else {
      $kelurahan = [];
    }

    return $kelurahan;
  }
}
