<?php

namespace App\Http\Controllers\Bunda;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CommonController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\JabatanPokja;
use App\Models\ProfilSekolah\Sekolah;
use App\Models\ProfilSekolah\Siswa;
use App\Models\Edu\Jadwal;
use App\Models\Edu\Pendamping;
use App\Models\Edu\Tempat;
use App\Http\Libraries\GetData;
use App\Models\Fasilitasi\Jadwal as FasilitasiJadwal;
use DataTables, DB, Session, Auth, Storage;
use App\Tools\Bantuan;
use Carbon\Carbon;

class JadwalTalentaController extends Controller
{
  function __construct()
  {
    date_default_timezone_set('Asia/Jakarta');
  }

  function index()
  {
    return view('bunda.jadwal_talenta.index');
  }

  function get_data(Request $request)
  {
    $jadwal = DB::table('public.talenta_seni_jadwal_tempat as j')
      ->where('j.tahun', now()->year)
      ->join('public.talenta_seni_tempat as t', 't.id', 'j.tempat_id')
      ->selectRaw("j.*,t.nama,t.alamat")
      ->get();

    return DataTables()->of($jadwal)
      ->addIndexColumn()
      ->addColumn('jam', function ($akun) {
        return $akun->mulai . ' s.d ' . $akun->selesai;
      })
      ->addColumn('aksi', function ($akun) {
        $button = '<a href="javascript:void(0)" class="btn btn-sm btn-info" onclick="form(\'' . $akun->id . '\')">Edit</a>';
        $button .= ' <a href="javascript:void(0)" class="btn btn-sm btn-danger" onclick="hapus(\'' . $akun->id . '\')">Hapus</a>';
        return $button;
      })
      ->rawColumns(['aksi', 'jam'])
      ->make(true);
  }

  function modal_form(Request $request)
  {
    $id = $request->id;
    $tempat = DB::table('public.talenta_seni_tempat')->get();
    $kecamatan = DB::connection('pgsql_paud')->table('public.kecamatan')->orderBy('kecamatan_dispenduk')->get();

    if ($id != 0) {
      $jadwal = DB::table('public.talenta_seni_jadwal_tempat')->where('id', $id)
        ->where('tahun', now()->year)->first();
    } else {
      $jadwal = new Request;
      $jadwal->tempat_id = null;
      $jadwal->mulai = null;
      $jadwal->selesai = null;
      $jadwal->jumlah_lembaga = null;
      $jadwal->kecamatan = null;
    }

    $data = [
      'tempat' => $tempat,
      'jadwal' => $jadwal,
      'kecamatan' => $kecamatan,
      'id' => $id,
    ];

    $content = view('bunda.jadwal_talenta.modal_form', $data)->render();

    return ['content' => $content];
  }

  function konfirmasi(Request $request)
  {
    $id = $request->id;
    $data_insert = $request->toArray();
    $data_insert['tahun'] = date('Y');

    unset($data_insert['id']);
    if ($id == 0) {
      $data_insert['created_at'] = now();
      $simpan = DB::table('public.talenta_seni_jadwal_tempat')->insert($data_insert);
    } else {
      $data_insert['updated_at'] = now();
      $simpan = DB::table('public.talenta_seni_jadwal_tempat')
        ->where('tahun', now()->year)->where('id', $id)->update($data_insert);
    }

    if ($simpan) {
      return ['code' => 200, 'message' => 'Berhasil disimpan'];
    } else {
      return ['code' => 250, 'message' => 'Gagal disimpan'];
    }
  }

  function hapus(Request $request)
  {
    $id = $request->id;
    $simpan = DB::table('public.talenta_seni_jadwal_tempat')
      ->where('tahun', now()->year)->where('id', $id)->delete();

    if ($simpan) {
      return ['code' => 200, 'message' => 'Berhasil dihapus'];
    } else {
      return ['code' => 250, 'message' => 'Gagal dihapus'];
    }
  }
}
