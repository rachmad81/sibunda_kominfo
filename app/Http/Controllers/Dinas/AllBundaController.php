<?php

namespace App\Http\Controllers\Dinas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProfilSekolah\Kecamatan;
use App\Models\ProfilSekolah\Kelurahan;
use App\Models\ProfilPokja;
use App\Models\ProfilPokjaN;
use App\Models\RiwayatPokja;
use App\Models\JabatanPokja;

use Redirect, DB, Datatables;

class AllBundaController extends Controller
{
  public function index()
  {
    return view('dinas.bunda.index');
  }

  public function getList(Request $request)
  {
    // return $request->all();
    $profil = ProfilPokjaN::selectRaw("nama,id_kecamatan,id_kelurahan,deleted_at,profil_pokja.id,profil_pokja.tingkat,j.nama_jabatan")->leftjoin('public.jabatan_pokja as j', 'j.id', 'profil_pokja.id_jabatan');
    $kecamatan = DB::connection('pgsql_paud')->table('public.kecamatan')->selectRaw("kecamatan_id,kecamatan_name")->get();
    $kelurahan = DB::connection('pgsql_paud')->table('public.kelurahan as kel')->leftjoin('public.kecamatan as kec', 'kec.kecamatan_kode', 'kel.kecamatan_kode')->selectRaw("kec.kecamatan_id,kec.kecamatan_name,kel.kelurahan_id,kel.kelurahan_name")->get();

    return DataTables::of($profil)
      ->addColumn('kecamatan', function ($q) use ($kecamatan, $kelurahan) {
        $dt_kecamatan = $kecamatan->where('kecamatan_id', $q->id_kecamatan)->first();
        $dt_kelurahan = $kelurahan->where('kelurahan_id', $q->id_kelurahan)->first();

        $nama_kecamatan = '';
        if ($q->tingkat == 'kecamatan') {
          $nama_kecamatan = (!empty($dt_kecamatan)) ? $dt_kecamatan->kecamatan_name : '';
        } else if ($q->tingkat == 'kelurahan') {
          $nama_kecamatan = (!empty($dt_kelurahan)) ? $dt_kelurahan->kecamatan_name : '';
        }
        return $nama_kecamatan;
      })
      ->addColumn('kelurahan', function ($q) use ($kelurahan) {
        $dt_kelurahan = $kelurahan->where('kelurahan_id', $q->id_kelurahan)->first();

        $nama_kelurahan = '';
        if ($q->tingkat == 'kelurahan') {
          $nama_kelurahan = (!empty($dt_kelurahan)) ? $dt_kelurahan->kelurahan_name : '';
        }
        return $nama_kelurahan;
      })
      ->addColumn('aksi', function ($q) {
        $aksi = '<a href="javascript:void(0)" class="btn btn-sm btn-warning" onclick="ubah(\'' . $q->id . '\')">Ubah</a>';
        $aksi .= '<a href="javascript:void(0)" class="btn btn-sm btn-info" onclick="riwayat(\'' . $q->id . '\')">Riwayat</a>';

        return $aksi;
      })
      ->addColumn('status', function ($q) {
        $status = ($q->deleted_at == '' || $q->deleted_at == null) ? 'Masih Aktif' : 'Non Aktif';
        return $status;
      })
      ->addIndexColumn()
      ->rawColumns(['aksi'])
      ->make(true);
  }

  function jabatan(Request $request)
  {
    $tingkat = $request->tingkat;
    $jabatan = JabatanPokja::where('tingkat', $tingkat)->get();

    return $jabatan;
  }

  function modal(Request $request)
  {
    $id = $request->id;
    $profil = ProfilPokjaN::selectRaw("profil_pokja.*")->whereRaw("profil_pokja.id='$id'")->first();
    $data = [
      'profil' => $profil,
    ];
    if (!empty($profil)) {
      $content = view('dinas.bunda.modal', $data)->render();
      return ['code' => '200', 'content' => $content];
    } else {
      return ['code' => '250'];
    }
  }

  function riwayat(Request $request)
  {
    $id = $request->id;
    $profil = ProfilPokjaN::leftjoin('users as u', 'u.id', 'profil_pokja.updated_by')->selectRaw("profil_pokja.*,u.role,u.tingkat")->whereRaw("profil_pokja.id='$id'")->first();
    $riwayat = RiwayatPokja::whereRaw("profil_pokja_id='$id'")->orderBy('id_riwayat', 'DESC')->get();

    foreach ($riwayat as $r) {
      $kecamatan = DB::connection('pgsql_paud')->table('public.kecamatan')->where('kecamatan_id', $r->kecamatan_id)->first();
      $kelurahan = DB::connection('pgsql_paud')->table('public.kelurahan as kel')->leftjoin('public.kecamatan as kec', 'kec.kecamatan_kode', 'kel.kecamatan_kode')->where('kelurahan_id', $r->kelurahan_id)->first();

      $r->kecamatan = (!empty($kecamatan)) ? $kecamatan->kecamatan_name : '';
      $r->kelurahan = (!empty($kelurahan)) ? $kelurahan->kecamatan_name . ' ' . $kelurahan->kelurahan_name : '';
    }

    $data = [
      'profil' => $profil,
      'riwayat' => $riwayat,
    ];
    if (!empty($profil)) {
      $content = view('dinas.bunda.riwayat', $data)->render();
      return ['code' => '200', 'content' => $content];
    } else {
      return ['code' => '250'];
    }
  }

  function simpan(Request $request)
  {
    // return $request->all();
    date_default_timezone_set('Asia/Jakarta');
    $id = $request->id;
    $nama = $request->nama;
    $tingkat = $request->tingkat;
    $wilayah = $request->wilayah;
    $aksi = $request->aksi;
    $jabatan = $request->jabatan;

    $profil = ProfilPokjaN::find($id);
    $profil->nama = $nama;
    $profil->tingkat = $tingkat;
    $profil->id_jabatan = $jabatan;
    if ($tingkat == 'kecamatan') {
      $profil->id_kecamatan = $wilayah;
      $profil->id_kelurahan = null;
      $profil->id_jabatan = '18';
    } else if ($tingkat == 'kelurahan') {
      $profil->id_kelurahan = $wilayah;
      $profil->id_jabatan = '33';
    }
    if ($aksi == 'aktif') {
      $profil->deleted_at = null;
    } else if (in_array($aksi, ['non-aktif', 'pensiun'])) {
      $profil->deleted_at = date('Y-m-d H:i:s');
    }

    $profil->save();

    $riwayat = new RiwayatPokja;
    $riwayat->profil_pokja_id = $id;
    $riwayat->tingkat = $tingkat;
    $riwayat->role = 'bunda';
    $riwayat->aksi = $aksi;
    if ($tingkat == 'kecamatan') {
      $riwayat->kecamatan_id = $wilayah;
    } else if ($tingkat == 'kelurahan') {
      $riwayat->kelurahan_id = $wilayah;
    }

    $riwayat->save();

    if ($riwayat) {
      $code = '200';
    } else {
      $code = '250';
    }

    return ['code' => $code];
  }
}
