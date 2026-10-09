<?php

namespace App\Http\Controllers\Bunda;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CommonController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\JabatanPokja;
use App\Models\Paudhi\Parenting;
use App\Models\Paudhi\AbsenParenting;
use App\Models\ProfilSekolah\Sekolah;
use App\Http\Libraries\GetData;
use DataTables, DB, Session, Auth;
use App\Tools\Bantuan;
use Carbon\Carbon;

class BkbController extends Controller
{
  function __construct()
  {
    date_default_timezone_set('Asia/Jakarta');
  }

  function index()
  {
    $th = Parenting::on('db_simpaud')->selectRaw("EXTRACT(YEAR from tgl_pelaksanaan) as tahun")->whereNotNull('tanggal')->groupBy('tahun')->get();
    $kecamatan = [];
    $kelurahan = [];
    $kode_kecamatan = 'semua';
    $kode_kelurahan = 'semua';
    $jenjang = 'semua';

    $kecamatan = DB::connection('pgsql_paud')->table('public.kecamatan')->orderBy('kecamatan_dispenduk', 'ASC')->get();
    if (Auth::getUser()->tingkat == 'kelurahan') {
      $gk = DB::connection('pgsql_paud')->table('public.kelurahan')->where('kelurahan_id', Auth::getUser()->id_kelurahan)->first();
      $kelurahan = GetData::get_kelurahan($gk->kecamatan_kode);
      $kode_kelurahan = $gk->kelurahan_kode;
      $kode_kecamatan = $gk->kecamatan_kode;
    } else {
      if (Auth::getUser()->tingkat == 'kecamatan') {
        $gk = DB::connection('pgsql_paud')->table('public.kecamatan')->where('kecamatan_id', Auth::getUser()->id_kecamatan)->first();
        $kode_kecamatan = $gk->kecamatan_kode;
      }
    }

    $data = [
      'thn' => $th,
      'tahun' => date('Y'),
      'kecamatan' => $kecamatan,
      'kelurahan' => $kelurahan,
      'kode_kecamatan' => $kode_kecamatan,
      'kode_kelurahan' => $kode_kelurahan,
      'jenjang' => $jenjang,
    ];
    return view('bunda.bkb.index', $data);
  }

  function get_data(Request $request)
  {
    $tahun = $request->tahun;
    $kec = $request->kec;
    $kel = $request->kel;
    $jenjang = $request->jenjang;

    $where_lokasi = "";
    if ($kec != 'semua') {
      $where_lokasi .= " AND kec_id='$kec'";
    }
    if ($kel != 'semua') {
      $where_lokasi .= " AND desa='$kel'";
    }
    if ($jenjang != 'semua') {
      $where_lokasi .= " AND jenjang='$jenjang'";
    }

    $sekolah = Sekolah::on('pgsql_paud')->join('public.master_user as u', 'u.npsn', 'sekolah.npsn')
      ->leftjoin('public.kecamatan as kec', 'kec.kecamatan_kode', 'sekolah.kec_id')
      ->leftjoin('public.kelurahan as kel', 'kel.kelurahan_kode', 'sekolah.desa')
      ->selectRaw("sekolah.npsn,sekolah.nama,kec.kecamatan_dispenduk,kel.kelurahan_dispenduk")->whereRaw("u.user_enable is true $where_lokasi")->get();
    $npsn = $sekolah->pluck('npsn');

    $bkb = Parenting::on('db_simpaud')->whereRaw("EXTRACT(YEAR FROM tgl_pelaksanaan) = '" . $tahun . "'")->whereIn('npsn', $npsn)->get();

    return DataTables()->of($bkb)
      ->addIndexColumn()
      ->addColumn('aksi', function ($akun) use ($tahun) {
        $button = '';
        $button .= '<a style="margin-left: 5px" href="javascript:void(0)" onclick="presensi(\'' . $akun->id . '\')" class="btn btn-sm btn-primary">Presensi</a>';
        return $button;
      })
      ->addColumn('kehadiran', function ($akun) {
        $kehadiran = AbsenParenting::on('db_simpaud')->where('id_parenting', $akun->id)->count();
        $akun->kehadiran = $kehadiran;
        return $kehadiran;
      })
      ->addColumn('persentase', function ($akun) {
        if ($akun->jml_siswa_satuan == 0) {
          $persentase = 0;
        } else {
          $persentase = ($akun->kehadiran / $akun->jml_siswa_satuan) * 100;
        }
        $persentase = number_format($persentase, 2, ',', '.');
        return $persentase;
      })
      ->rawColumns(['aksi'])
      ->make(true);
  }

  function presensi(Request $request)
  {
    $id = $request->id;

    $parenting = Parenting::on('db_simpaud')->find($id);
    $presensi = AbsenParenting::on('db_simpaud')->where('id_parenting', $id)->get();

    $data = [
      'parenting' => $parenting,
      'presensi' => $presensi,
    ];

    $content = view('bunda.bkb.presensi', $data)->render();
    return ['content' => $content];
  }
}
