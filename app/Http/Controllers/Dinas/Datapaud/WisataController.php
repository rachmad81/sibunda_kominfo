<?php

namespace App\Http\Controllers\Dinas\Datapaud;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Edu\Tempat;
use App\Models\Edu\Opd;
use App\Models\User;
use DataTables, DB;

class WisataController extends Controller
{
  function index(Request $request)
  {
    $data = [];
    return view('dinas.datapaud.wisata.index', $data);
  }

  function data(Request $request)
  {
    $tempat = Tempat::orderBy('nama', 'ASC')->get();

    return DataTables()->of($tempat)->addIndexColumn()
      ->addColumn('aksi', function ($j) {
        $button = '<a href="javascript:void(0)" onclick="form(\'' . $j->id . '\')" class="btn btn-sm btn-primary">Edit</a>';
        return $button;
      })->addColumn('status', function ($j) {
        if ($j->status_tampil == true) {
          $button = '<a href="javascript:void(0)" class="badge badge-success">Ditampilkan</a>';
        } else {
          $button = '<a href="javascript:void(0)" class="badge badge-danger">Disembunyikan</a>';
        }
        return $button;
      })
      ->rawColumns(['tembusan', 'aksi', 'status'])
      ->make(true);
  }

  function form(Request $request)
  {
    $id = $request->id;
    $wisata = Tempat::find($id);
    // $opd = Opd::where('id','!=','4')->orderBy('nama_opd')->get();
    $opd = User::where('role', 'opd')->where('id', '!=', '740')->orderBy('nama')->get();
    $kecamatan = DB::table('public.kecamatan as kec')
      ->join('public.kelurahan as kel', 'kel.kecamatan_kode', 'kec.kecamatan_kode')
      ->selectRaw("kec.kecamatan_dispenduk, kel.kelurahan_dispenduk")
      ->orderByRaw("kec.kecamatan_dispenduk")
      ->get();

    $kecamatan = collect($kecamatan)->groupBy('kecamatan_dispenduk')->map(function ($item, $kec) {
      return [
        'kecamatan' => $kec,
        'kelurahan' => $item->sortBy('kelurahan_dispenduk')->values(),
      ];
    })->values();

    $data = [
      'wisata' => $wisata,
      'opd' => $opd,
      'kecamatan' => $kecamatan,
      'id_opd' => (json_decode($wisata->id_opd, true) == null) ? [] : json_decode($wisata->id_opd, true),
    ];

    $content = view('dinas.datapaud.wisata.form', $data)->render();

    return ['content' => $content];
  }

  function simpan(Request $request)
  {
    $alamat = $request->alamat; //"Jl. Jemursari Tim. II No.2, Jemur Wonosari, Kec. Wonocolo"
    $kapasitas = $request->kapasitas; //"35"
    $kecamatan = $request->kecamatan; //"WONOCOLO"
    $kelurahan = $request->kelurahan; //"JEMUR WONOSARI"
    $nama_wisata = $request->nama; //"BPBD (Badan Penanggulangan Bencana Daerah) Sana Sini (Edukasi Bencana Usia Dini)"
    $status = $request->status; //"true"
    $id = $request->id;
    $opd = isset($request->opd) ? $request->opd : [];
    $hari_buka = isset($request->hari_buka) ? $request->hari_buka : []; //["1", "2", "3", "4"]
    array_push($opd, '740');
    $data_opd = User::whereIn('id', $opd)->get();

    $str_hari = '';
    if (count($hari_buka) > 0) {
      $str_hari = '[';
      $str_hari .= implode(',', $hari_buka);
      $str_hari .= ']';
    }

    $tembusan = '<ol>';
    for ($i = 0; $i < count($opd); $i++) {
      $nama = $data_opd->where('id', $opd[$i])->first();
      $tembusan .= '<li>' . $nama->nama . '</li>';
    }
    $tembusan .= '</ol>';

    $tempat = Tempat::find($id);
    $tempat->nama = $nama_wisata;
    $tempat->kapasitas_pd = $kapasitas;
    $tempat->hari_buka = $str_hari;
    $tempat->alamat = $alamat;
    $tempat->kecamatan = $kecamatan;
    $tempat->kelurahan = $kelurahan;
    $tempat->status_tampil = $status;
    $tempat->tembusan = $tembusan;
    $tempat->id_opd = json_encode($opd);
    $tempat->save();

    if ($tempat) {
      return ['code' => '200'];
    } else {
      return ['code' => '250'];
    }
  }
}
