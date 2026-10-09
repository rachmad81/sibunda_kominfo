<?php

namespace App\Http\Controllers\Bunda;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Tools\Bantuan;

use DB, Session, Auth, Hash, DataTables;

class KerjasamaMitraController extends Controller
{
  function halaman(Request $request)
  {
    $halaman = $request->halaman;

    switch ($halaman) {
      case 'main':
        $data = [
          'judul' => 'Kerjasama Mitra',
        ];
        return view('bunda.kerjasama_mitra.main', $data);
        break;

      case 'get_data':
        $role = Auth::getUser()->role;
        $data = DB::table('public.kerjasama_mitra as ks')
          ->join('public.mitra as m', 'm.id_mitra', 'ks.mitra_id')
          ->join('public.users as u', 'u.id', 'ks.user_id')
          ->selectRaw("ks.id,m.nama_mitra,ks.sebagai,ks.mou,u.nama as nama_bunda")
          ->whereNull('ks.deleted_at')
          ->when($role === 'bunda', function ($w) {
            return $w->where('ks.user_id', Auth::getUser()->id);
          });

        return DataTables::queryBuilder($data)
          ->addIndexColumn()
          ->addColumn('aksi', function ($a) {
            $button = '<a href="javascript:void(0)" class="btn btn-sm btn-warning" onclick="edit(\'' . $a->id . '\',\'' . $a->nama_mitra . '\',\'' . $a->sebagai . '\',\'' . $a->mou . '\')">Edit</a>';
            $button .= '<a href="javascript:void(0)" class="btn btn-sm btn-danger" onclick="hapus(\'' . $a->id . '\')">Hapus</a>';
            return $button;
          })
          ->addColumn('file_mou', function ($a) {
            $button = 'tidak ada mou';
            if ($a->mou != '') {
              $button = '<a href="' . $a->mou . '" target="_blank" class="btn btn-sm btn-primary">Lihat</a>';
            }
            return $button;
          })
          ->rawColumns(['aksi', 'file_mou'])
          ->make(true);
        break;

      case 'cari_mitra':
        $cari = $request->cari;
        return $mitra = DB::table('public.mitra')->selectRaw("nama_mitra")
          ->whereRaw("nama_mitra ilike '%$cari%' AND status_aktif='Setujui'")
          ->orderByRaw("nama_mitra ASC,LENGTH(nama_mitra) ASC")
          ->limit(10)
          ->get();
        break;

      case 'simpan':
        // return $request->all();
        $id_kerjasama = $request->id_kerjasama;
        $mou = $request->mou;
        $nama_mitra = $request->nama_mitra;
        $sebagai = $request->sebagai;
        $bunda_id = Auth::getUser()->id;

        $cek_gdrive = 'PUBLIC';
        if ($mou != '') {
          $cek_gdrive = Bantuan::checkDrivePermission($mou);
          if ($cek_gdrive != 'PUBLIC') {
            return ['code' => '350', 'message' => $cek_gdrive];
          }
        }

        if ($id_kerjasama == 0) {
          // BARU INSERT
          $mitra = DB::table('public.mitra')->where('nama_mitra', $nama_mitra)->first();
          if (empty($mitra)) {
            $username = $this->generate_username(10);
            $data_user = [
              'username' => $username,
              'password' => Hash::make('123456'),
              'role' => 'mitra',
              'tingkat' => 'kota',
              'nama' => $nama_mitra,
              'updated_at' => date('Y-m-d H:i:s'),
            ];

            $id_user_mitra = DB::table('public.users')->insertGetId($data_user, 'id');
            $id_kecamatan = Auth::getUser()->id_kecamatan;
            $id_kelurahan = Auth::getUser()->id_kelurahan;
            $tingkat = Auth::getUser()->tingkat;

            if (in_array($tingkat, ['kecamatan', 'kelurahan'])) {
              $wilayah = DB::table('public.kecamatan as kec')
                ->join('public.kelurahan as kel', 'kel.kecamatan_kode', 'kec.kecamatan_kode')
                ->selectRaw("kec.kecamatan_kode,kel.kelurahan_kode")
                ->when($id_kecamatan !== null, function ($w) use ($id_kecamatan) {
                  return $w->where('kec.kecamatan_id', $id_kecamatan);
                })
                ->when($id_kelurahan !== null, function ($w) use ($id_kelurahan) {
                  return $w->where('kel.kelurahan_id', $id_kelurahan);
                })
                ->first();
            }

            $data_mitra = [
              'nama_mitra' => $nama_mitra,
              'kecamatan' => $wilayah->kecamatan_kode ?? '',
              'kelurahan' => $wilayah->kelurahan_kode ?? '',
              'user_id' => $id_user_mitra,
              'status_aktif' => 'Pengajuan',
              'updated_by' => $bunda_id,
              'updated_at' => date('Y-m-d H:i:s'),
            ];

            $id_mitra = DB::table('public.mitra')->insertGetId($data_mitra, 'id_mitra');
          } else {
            $id_mitra = $mitra->id_mitra;
          }

          $data_kerjasama = [
            'mitra_id' => $id_mitra,
            'user_id' => $bunda_id,
            'sebagai' => $sebagai,
            'mou' => $mou,
            'updated_at' => date('Y-m-d H:i:s'),
          ];

          $simpan = DB::table('public.kerjasama_mitra')->updateOrInsert(['mitra_id' => $id_mitra, 'user_id' => $bunda_id, 'sebagai' => $sebagai], $data_kerjasama);
        } else {
          // UPDATE
          $mitra = DB::table('public.kerjasama_mitra')->where('id', $id_kerjasama)->first();
          $data_kerjasama = [
            'mitra_id' => $mitra->mitra_id,
            'user_id' => $mitra->user_id,
            'sebagai' => $sebagai,
            'mou' => $mou,
            'updated_at' => date('Y-m-d H:i:s'),
          ];

          $simpan = DB::table('public.kerjasama_mitra')->where('id', $id_kerjasama)->update($data_kerjasama);
        }

        if ($simpan) {
          return ['code' => '200'];
        } else {
          return ['code' => '250', 'message' => 'Gagal menyimpan'];
        }
        break;

      case 'hapus':
        $id = $request->id;
        $hapus = DB::table('public.kerjasama_mitra')->where('id', $id)->update(['deleted_at' => date('Y-m-d H:i:s')]);
        if ($hapus) {
          return ['code' => '200'];
        } else {
          return ['code' => '250'];
        }
        break;

      default:
        return '404 - Halaman tidak ditemukan';
        break;
    }
  }

  function generate_username($length = 8)
  {
    do {
      $bytes = random_bytes(ceil($length / 2));
      $username = substr(bin2hex($bytes), 0, $length);

      // Cek apakah username sudah ada di database
      $exists = DB::table('public.users')->where('username', $username)->count();
    } while ($exists > 0);

    return $username;
  }
}
