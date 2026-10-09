<?php

namespace App\Http\Controllers\Dinas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB, Session, DataTables;

class UserControllerBaru extends Controller
{
  function halaman(Request $request)
  {
    $halaman = $request->halaman;

    switch ($halaman) {
      case 'main':
        $data = [];
        return view('dinas.user_baru.main', $data);
        break;

      case 'get_data':
        $role = $request->role;


        $user = DB::table('public.users as u')
          ->leftJoin('public.kecamatan as kec', 'kec.kecamatan_id', 'u.id_kecamatan')
          ->leftJoin('public.kelurahan as kel', 'kel.kelurahan_id', 'u.id_kelurahan')
          ->selectRaw("u.id,u.nama,u.username,u.role,u.tingkat,u.last_login,kec.kecamatan_name,kel.kelurahan_name")
          ->when($role, function ($w) use ($role) {
            $tingkat = \Auth::getUser()->tingkat;
            $auth_role = \Auth::getUser()->role;
            if ($auth_role == 'dinas') {
              return $w->whereNotIn('role', ['mitra', 'pokja']);
            } else if ($auth_role == 'bunda') {
              return $w->where('role', ['mitra', 'pokja']);
            }
          })
          ->whereNull('u.deleted_at');

        return DataTables::queryBuilder($user)
          ->addIndexColumn()
          ->addColumn('aksi', function ($i) {
            $button = '<button class="btn btn-sm btn-outline-warning"
                          data-toggle="modal" data-target="#modal_ubah" onclick="updateData(\'' . $i->id . '\')">
                          Ubah
                      </button>';
            $button .= '<button class="btn btn-sm btn-outline-danger" onclick="confirmDeleteAlert(\'' . $i->id . '\')">Hapus</button>
                        <button class="btn btn-sm btn-outline-primary" onclick="loginAs(\'' . $i->id . '\')">Login</button>';

            return $button;
          })
          ->addColumn('role', function ($i) {
            switch ($i->role) {
              case 'bunda':
                $role = '<span class="badge badge-primary">Bunda</span>';
                break;

              case 'opd':
                $role = '<span class="badge badge-success">Opd (Dinas)</span>';
                break;

              case 'pokja':
                $role = '<span class="badge badge-warning">Pokja</span>';
                break;

              case 'penilai':
                $role = '<span class="badge badge-info">Penilai</span>';
                break;

              case 'k3_paud':
                $role = '<span class="badge" style="background: #cea48e">K3 PAUD</span>';
                break;

              case 'paud_sd':
                $role = '<span class="badge" style="background: #fd5500">Forum PAUD SD</span>';
                break;

              default:
                $role = '';
                break;
            }
            return $role;
          })
          ->addColumn('wilayah', function ($i) {
            if ($i->tingkat == 'kota') {
              $wilayah = 'Kota Surabaya';
            } else if ($i->tingkat == 'kecamatan') {
              $wilayah = $i->kecamatan_name;
            } else if ($i->tingkat == 'kelurahan') {
              $wilayah = $i->kelurahan_name;
            } else {
              $wilayah = '';
            }
            return $wilayah;
          })
          ->rawColumns(['aksi', 'role'])
          ->make(true);
        break;

      case 'login_as':
        break;

      default:
        # code...
        break;
    }
  }
}
