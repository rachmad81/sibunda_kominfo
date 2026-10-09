<?php

namespace App\Http\Controllers\Bunda;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB, DataTables;

class TalentaTempatController extends Controller
{
  function halaman(Request $request)
  {
    $halaman = $request->halaman;

    switch ($halaman) {
      case 'main':
        $data = [];
        return view('bunda.talenta.tempat.main', $data);
        break;

      case 'get_data':
        $tempat = DB::table('public.talenta_seni_tempat')
          ->selectRaw("id,nama,alamat,kecamatan,kontak,area");

        return DataTables::queryBuilder($tempat)
          ->addIndexCOlumn()
          ->addColumn('aksi', function ($i) {
            $html = '<a href="javascript:void(0)" onclick="form_modal(\'' . $i->id . '\')" class="btn btn-info">Edit</a>';
            $html .= ' <a href="javascript:void(0)" class="btn btn-danger" onclick="hapus(\'' . $i->id . '\')">Hapus</a>';
            return $html;
          })
          ->rawColumns(['aksi'])
          ->make(true);
        break;

      case 'form_modal':
        $id = $request->id;
        $tempat = DB::table('public.talenta_seni_tempat')
          ->selectRaw("id,nama,alamat,kecamatan,kontak,area")
          ->where('id', $id)->first();

        $kecamatan = DB::table('public.kecamatan')->selectRaw("kecamatan_kode,kecamatan_dispenduk")
          ->orderBy('kecamatan_dispenduk')->get();

        $data = [
          'tempat' => $tempat,
          'id' => $id,
          'kecamatan' => $kecamatan,
        ];

        $content = view('bunda.talenta.tempat.modal_form', $data)->render();
        return ['content' => $content];
        break;

      case 'simpan':
        // return $request->all();
        $alamat = $request->alamat; //"Jl. KH Abdul Wahab Siamin Surabaya"
        $area = $request->area; //"1. Atrium UG 2. Atrium F1"
        $id = $request->id; //"5"
        $kecamatan = $request->kecamatan; //["LAKARSANTRI,DUKUH PAKIS,WIYUNG,KARANG PILANG,SAWAHAN",…]
        $kontak = $request->kontak; //"Thoriq 0898-0007-389"
        $nama = $request->nama; //"Lagoon Avenue Mall Sungkono Surabaya"

        $data_simpan = [
          'nama' => $nama,
          'alamat' => $alamat,
          'kecamatan' => implode(',', $kecamatan),
          'kontak' => $kontak,
          'area' => $area,
        ];

        if ($id == 0) {
          $simpan = DB::table('public.talenta_seni_tempat')->insert($data_simpan);
        } else {
          $simpan = DB::table('public.talenta_seni_tempat')->updateOrInsert(['id' => $id], $data_simpan);
        }

        if ($simpan) {
          return ['code' => 200];
        } else {
          return ['code' => 250];
        }
        break;

      case 'hapus':
        // return $request->all();
        $id = $request->id;
        $hapus = DB::table('public.talenta_seni_tempat')->where('id', $id)->delete();

        if ($hapus) {
          return ['code' => 200];
        } else {
          return ['code' => 250];
        }
        break;

      default:
        return '404 - Halaman tidak ditemukan';
        break;
    }
  }
}
