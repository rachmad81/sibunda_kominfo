<?php

namespace App\Http\Controllers\Dinas;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

use DB, DataTables, Hash, Auth;

class MitraController extends Controller
{
  function halaman(Request $request)
  {
    $halaman = $request->halaman;
    switch ($halaman) {
      case 'index':
        $data = [];
        return view('dinas.mitra.index', $data);
        break;
      case 'get_data':
        $status = $request->status;
        $mitra = DB::table('public.mitra as m')
          ->join('public.users as u', 'u.id', 'm.user_id')
          ->when($status != '', function ($w) use ($status) {
            return $w->where('m.status_aktif', $status);
          })
          ->selectRaw("m.id_mitra,m.nama_kepala,m.kontak,m.nama_mitra as nama,'' as aksi,u.username,m.status_aktif,u.id");

        return DataTables::of($mitra)
          ->addIndexColumn()
          ->addColumn('status', function ($item) {
            switch ($item->status_aktif) {
              case 'Pengajuan':
                $label = 'alert-warning';
                break;
              case 'Setujui':
                $label = 'alert-success';
                break;
              case 'Block':
                $label = 'alert-danger';
                break;

              default:
                $label = 'alert-warning';
                break;
            }

            return '<a href="javascript:void(0)" class="alert alert-sm ' . $label . '">' . $item->status_aktif . '</a>';
          })
          ->addColumn('aksi', function ($item) {
            $html = '<a class="btn btn-sm btn-outline-info" href="javascript:void(0)" onclick="form_mitra(\'' . $item->id_mitra . '\')"><i class="fa fa-edit"></i> Edit</a>';
            $html .= ' <button class="btn btn-sm btn-outline-primary" onclick="loginAs(' . $item->id . ')">Login</button>';
            return $html;
          })
          ->rawColumns(['aksi', 'status'])
          ->make(true);
        break;
      case 'form_mitra':
        $id = $request->id;
        $mitra = DB::table('public.mitra as m')
          ->join('public.users as u', 'u.id', 'm.user_id')
          ->where('m.id_mitra', $id)
          ->selectRaw("m.*,u.username,u.nama")
          ->first();

        $kecamatan = DB::table('public.kecamatan as kec')
          ->join('public.kelurahan as kel', 'kel.kecamatan_kode', 'kec.kecamatan_kode')
          ->selectRaw("kec.kecamatan_kode,kec.kecamatan_dispenduk,kel.kelurahan_kode,kel.kelurahan_dispenduk")
          ->orderBy('kec.kecamatan_dispenduk')
          ->get();

        $kecamatan = collect($kecamatan)->groupBy('kecamatan_kode')->map(function ($item, $kecamatan) {
          $baris = $item->first();
          return (object)[
            'kecamatan_kode' => $baris->kecamatan_kode,
            'kecamatan_dispenduk' => $baris->kecamatan_dispenduk,
            'kelurahan' => collect($item)->sortBy('kelurahan_dispenduk')->values(),
          ];
        })->values();

        $data = [
          'mitra' => $mitra,
          'kecamatan' => $kecamatan,
          'id' => $id,
        ];

        $content = view('dinas.mitra.modal_mitra', $data)->render();
        return ['content' => $content];
        break;

      case 'simpan_mitra':
        $id = $request->id; //"1"

        if ($id == 0) {
          $unique = Rule::unique('pgsql.public.users', 'username');
        } else {
          $mitra = DB::table('public.mitra')->where('id_mitra', $id)->first();
          $unique = Rule::unique('pgsql.public.users', 'username')->ignore($mitra->user_id, 'id');
        }
        $validator = Validator::make(
          $request->all(),
          [
            'username' => [
              $unique,
            ],
          ],
          [
            // custom pesan
            'username.unique'   => 'Username sudah terdaftar, silakan gunakan username lain.',
          ]
        );

        if ($validator->fails()) {
          return ['code' => 300, 'pesan_errors' => $validator->messages()];
        }

        $kecamatan = $request->kecamatan; //"1205"
        $kelurahan = $request->kelurahan; //"120503"
        $nama_mitra = $request->nama_mitra; //"Indomaret Kedung Cowek"
        $password = $request->password; //null
        $status = $request->status; //"Pengajuan"
        $username = $request->username; //"indomaret50"
        $alamat = $request->alamat; //"indomaret50"
        $nama_kepala = $request->nama_kepala; //"indomaret50"
        $kontak = $request->kontak; //"indomaret50"

        $data_user = [
          'username' => $username,
          'nama' => $nama_mitra,
          'updated_at' => date('Y-m-d H:i:s'),
          'role' => 'mitra',
          'tingkat' => 'kota',
        ];

        if ($password != '') {
          $data_user = array_merge($data_user, ['password' => Hash::make($password)]);
        }

        $data_mitra = [
          'nama_mitra' => $nama_mitra,
          'alamat' => $alamat,
          'status_aktif' => $status,
          'kecamatan' => $kecamatan,
          'kelurahan' => $kelurahan,
          'nama_kepala' => $nama_kepala,
          'kontak' => $kontak,
          'updated_at' => date('Y-m-d H:i:s'),
          'updated_by' => Auth::getUser()->id,
        ];

        if ($id == 0) {
          $id_user = DB::table('public.users')->insertGetId($data_user, 'id');
          $data_mitra = array_merge($data_mitra, ['user_id' => $id_user]);
          $simpan = DB::table('public.mitra')->insert($data_mitra);
        } else {
          $mitra = DB::table('public.mitra')->where('id_mitra', $id)->first();
          DB::table('public.users')->updateOrInsert(['id' => $mitra->user_id], $data_user);
          $data_mitra = array_merge($data_mitra, ['user_id' => $mitra->user_id]);
          $simpan = DB::table('public.mitra')->updateOrInsert(['id_mitra' => $id], $data_mitra);
        }

        if ($simpan) {
          return ['code' => 200, 'pesan' => 'Berhasil disimpan'];
        }
        break;

      default:
        return '404 - Halaman tidak ditemukan';
        break;
    }
  }
}
