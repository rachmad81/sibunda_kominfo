<?php

namespace App\Http\Controllers\K3_PAUD;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Libraries\Convert;

use DB, Session, Auth, DataTables;

class AnggotaController extends Controller
{
  function halaman(Request $request)
  {
    $halaman = $request->halaman;

    switch ($halaman) {
      case 'main':
        $data = [];
        return view('k3_paud_sd.anggota.index', $data);
        break;

      case 'get_data':
        $user_id = Auth::getUser()->id;
        $anggota = DB::table('public.keanggotaan')
          ->where('user_id', $user_id)
          ->whereNull('deleted_at');
        return DataTables::queryBuilder($anggota)
          ->addIndexColumn()
          ->addColumn('berkas', function ($i) {
            $berkas = 'Tidak ada berkas';
            if ($i->berkas_tambahan != '') {
              $berkas = '<a href="' . $i->berkas_tambahan . '" class="btn btn-sm btn-primary" target="_blank">Lihat</a>';
            }
            return $berkas;
          })
          ->addColumn('aksi', function ($i) {
            $aksi = '<a href="javascript:void(0)" class="btn btn-outline-info btn-sm" onclick="f_form_anggota(\'' . $i->id . '\')"><i class="fa fa-edit"></i> Edit</a>';
            $aksi .= ' <a href="javascript:void(0)" class="btn btn-outline-danger btn-sm" onclick="f_hapus(\'' . $i->id . '\')"><i class="fa fa-trash"></i> Hapus</a>';
            return $aksi;
          })
          ->rawColumns(['aksi', 'berkas'])
          ->make(true);
        break;

      case 'form_anggota':
        $id = $request->id;
        $anggota = DB::table('public.keanggotaan')
          ->where('id', $id)->first();

        $data = [
          'judul' => $id == 0 ? 'Tambah Anggota' : 'Edit Anggota',
          'id' => $id,
          'anggota' => $anggota,
        ];

        $content = view('k3_paud_sd.anggota.modal_form', $data)->render();
        return ['content' => $content];
        break;

      case 'simpan':
        // return $request->all();
        $id = $request->id; //"0"
        $jabatan = $request->jabatan; //null
        $berkas_tambahan = $request->berkas_tambahan; //null
        $nama = $request->nama; //"a"
        $no_telp = $request->no_telp; //"0857"
        $user_id = Auth::getUser()->id;

        if ($berkas_tambahan != '') {
          $cek_drive = Convert::checkDrivePermission($berkas_tambahan);
          if ($cek_drive != 'PUBLIC') {
            return ['code' => 400, 'pesan' => $cek_drive];
          }
        }

        $data_simpan = [
          'nama' => $nama,
          'jabatan' => $jabatan,
          'no_telp' => $no_telp,
          'berkas_tambahan' => $berkas_tambahan,
          'updated_at' => date('Y-m-d H:i:s'),
          'user_id' => $user_id,
        ];

        if ($id == 0) {
          $simpan = DB::table('public.keanggotaan')->insert($data_simpan);
        } else {
          $simpan = DB::table('public.keanggotaan')->where('id', $id)->update($data_simpan);
        }

        if ($simpan) {
          return ['code' => 200];
        } else {
          return ['code' => 250];
        }
        break;

      case 'hapus':
        $id = $request->id; //"0"
        $simpan = DB::table('public.keanggotaan')->where('id', $id)->update(['deleted_at' => date('Y-m-d H:i:s')]);
        if ($simpan) {
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
