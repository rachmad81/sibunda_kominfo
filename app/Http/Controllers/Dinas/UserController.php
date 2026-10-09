<?php

namespace App\Http\Controllers\Dinas;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CommonController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use DataTables, DB;
use App\Tools\Bantuan;

class UserController extends Controller
{

  private $commonController;

  public function __construct(CommonController $common)
  {
    $this->commonController = $common;
  }

  public function index(Request $request)
  {
    if ($request->ajax()) {
      $user = User::where('id', '!=', \Auth::User()->id)
        ->when($request->has('jenis') && $request->jenis != 'all', function ($q) use ($request) {
          return $q->where('role', $request->jenis);
        })
        ->where('role', '!=', 'pokja')
        ->select('id', 'nama', 'username', 'role', 'tingkat', 'id_kecamatan', 'id_kelurahan', 'last_login')
        ->with('kecamatan', 'kelurahan')
        ->orderByRaw("array_position(array['kota'::character varying,'kecamatan'::character varying,'kelurahan'::character varying], tingkat)")
        ->orderBy('role', 'desc')
        ->get()
        ->sortBy(function ($q) {
          return [$q->kelurahan ? $q->kelurahan->kelurahan_name : '', $q->kecamatan ? $q->kecamatan->kecamatan_name : ''];
        });

      return DataTables()->of($user)->addIndexColumn()
        ->addColumn('aksi', function ($akun) {
          $button = '<button class="btn btn-sm btn-outline-warning"
                                            data-toggle="modal" data-target="#modal_ubah" onclick="vm.updateData(`' . str_replace('"', '&quot;', $akun->toEncoded()) . '`)">
                                            Ubah
                                        </button>';
          $button .= '<button class="btn btn-sm btn-outline-danger" onclick="vm.confirmDeleteAlert(' . $akun->id . ')">Hapus</button>
                                    <button class="btn btn-sm btn-outline-primary" onclick="vm.loginAs(' . $akun->id . ')">Login</button>';

          return $button;
        })
        ->editColumn('role', function ($akun) {
          return Bantuan::getRoleLabel($akun->role);
        })
        ->editColumn('last_login', function ($akun) {
          return is_null($akun->last_login) ? 'Belum pernah masuk' : $akun->last_login;
        })
        ->editColumn('wilayah', function ($akun) {
          if ($akun->tingkat == 'kota') {
            return 'Kota Surabaya';
          } elseif ($akun->tingkat == 'kecamatan') {
            return $akun->kecamatan->kecamatan_name;
          } else {
            if ($akun->kelurahan) {
              return 'Kel. ' . $akun->kelurahan->kelurahan_name;
            } else {
              return '';
            }
          }
        })
        ->rawColumns(['aksi', 'role', 'tingkat'])
        ->toJson();
    }

    return view('dinas.user.index');
  }

  public function getWilayah(Request $request)
  {
    $tmp = DB::connection('pgsql_paud')->table($request->jenis)->orderBy($request->jenis . '_name')->get();

    $data = [];
    if ($request->jenis == 'kecamatan') {
      foreach ($tmp as $key => $value) {
        array_push($data, ['id' => $value->{$request->jenis . '_id'}, 'nama' => $value->{$request->jenis . '_name'}]);
      }
    } else {
      foreach ($tmp as $key => $value) {
        $kec = DB::connection('pgsql_paud')->table('kecamatan')->where('kecamatan_kode', $value->kecamatan_kode)->select('kecamatan_name')->first();
        array_push($data, ['id' => $value->{$request->jenis . '_id'}, 'nama' => $kec->kecamatan_name . ' - ' . $value->{$request->jenis . '_name'}]);
      }
    }
    $data1 = collect($data)->sortBy('nama')->values();
    return Response()->json(['status' => 'success', 'obj' => $data1]);
  }

  public function store(Request $request)
  {
    // dd($request->all());
    try {
      DB::beginTransaction();

      $tmp = [
        'nama'         => $request->nama,
        'username'     => $request->username,
        'role'         => $request->role,
        'password'     => Hash::make($request->password),
        'created_at'   => date('Y-m-d H:i:s'),
        'id_kecamatan' => null,
        'id_kelurahan' => null,
        'verified'  => true
      ];

      if ($request->role == 'dinas') {
        $tmp['tingkat']      = 'kota';
      } elseif ($request->tingkat == 'kecamatan') {
        $tmp['id_kecamatan']    = $request->wilayah;
        $tmp['tingkat']         = $request->tingkat;
        $tmp['id_jabatan']      = 18;
      } elseif ($request->tingkat == 'kelurahan') {
        $tmp['id_kelurahan']    = $request->wilayah;
        $tmp['tingkat']         = $request->tingkat;
        $tmp['id_jabatan']      = 33;
      } else {
        $tmp['tingkat']         = $request->tingkat;
        $tmp['id_jabatan']      = 3;
      }

      $cek = DB::table('users')
        ->whereRaw("username = ? AND deleted_at is null", [$tmp['username']])
        ->exists();
      if ($cek) {
        throw new \Exception("Username sudah digunakan!", 503);
      }

      if ($request->role == 'bunda') {
        $addQuery = $tmp['tingkat'] == 'kecamatan' ? " and id_kecamatan = " . $tmp['id_kecamatan'] : "";
        $addQuery = $tmp['tingkat'] == 'kelurahan' ? " and id_kelurahan = " . $tmp['id_kelurahan'] : "";
        $cek = DB::table('users')
          ->whereRaw("role = '" . $request->role . "' and tingkat = ? and id_jabatan = ? and deleted_at is null" . $addQuery, [$tmp['tingkat'], $tmp['id_jabatan']])
          ->exists();
        if ($cek) {
          throw new \Exception("Jabatan sudah terisi!", 503);
        }
        $insertProfil = $this->commonController->createProfilPokja((object) $tmp);

        if (!$insertProfil) {
          throw new \Exception("Gagal menambah profil pokja!", 403);
        }
      }


      $id = DB::table('users')->insertGetId($tmp);

      DB::commit();
      return redirect()->back()->with('alert-success', 'Berhasil menambah User a.n "<b>' . $request->nama . '</b>"!');
    } catch (\Exception $e) {
      DB::rollback();
      return redirect()->back()->with('alert-fail', 'Gagal menambah user! Pesan : ' . $e->getMessage());
    }
  }

  public function update(Request $request)
  {
    $request->validate(
      [
        // 'username' => 'required|regex:/^[a-zA-Z0-9]+$/i|min:4',
        'nama' => 'required',
        'password' => 'required_if:ganti_pass,on',
        're-password' => 'required_if:ganti_pass,on|same:password',
      ],
      [
        // 'username.required' => 'Username tidak boleh kosong!',
        // 'username.min' => 'Username minimal 4 karakter',
        'nama.required' => 'Nama tidak boleh kosong',
        // 'username.regex' => 'Username tidak boleh mengandung spasi',
        're-password.same' => 'Kata sandi harus sama!',
      ]
    );
    try {
      DB::beginTransaction();

      $user = User::findOrFail($request->id, ['username', 'password']);

      $tmp = [
        'nama'         => $request->nama,
        // 'username'     => $request->username,
        'role'         => $request->role,
        'password'     => $request->has('password') ? Hash::make($request->password) : $user->password,
        'created_at'   => date('Y-m-d H:i:s'),
      ];
      if ($request->role == 'dinas') {
        $tmp['tingkat']      = 'kota';
      } elseif ($request->tingkat == 'kecamatan') {
        $tmp['id_kecamatan']    = $request->wilayah;
        $tmp['tingkat']         = $request->tingkat;
        $tmp['id_jabatan']      = 18;
      } elseif ($request->tingkat == 'kelurahan') {
        $tmp['id_kelurahan']    = $request->wilayah;
        $tmp['tingkat']         = $request->tingkat;
        $tmp['id_jabatan']      = 33;
      } else {
        $tmp['tingkat']         = $request->tingkat;
        $tmp['id_jabatan']      = 3;
      }

      if ($request->role == 'bunda') {
        $addQuery = $tmp['tingkat'] == 'kecamatan' ? " and id_kecamatan = " . $tmp['id_kecamatan'] : "";
        $addQuery = $tmp['tingkat'] == 'kelurahan' ? " and id_kelurahan = " . $tmp['id_kelurahan'] : "";
        $cek = DB::table('users')
          ->whereRaw("id != ? and role = '" . $request->role . "' and tingkat = ? and id_jabatan = ? AND deleted_at is null" . $addQuery, [$user->id, $tmp['tingkat'], $tmp['id_jabatan']])
          ->exists();
        if ($cek) {
          throw new \Exception("Jabatan sudah terisi!", 503);
        }
      }

      DB::table('users')->where('id', $request->id)->update($tmp);

      DB::commit();
      return redirect(route('dinas-user-index'))->with('alert-success', 'Berhasil mengubah akun pengguna!');
    } catch (\Exception $e) {
      DB::rollback();
      return redirect(route('dinas-user-index'))->with('alert-fail', 'Gagal mengubah akun pengguna! pesan : ' . $e->getMessage());
    }
  }

  public function delete(Request $request)
  {

    try {
      DB::beginTransaction();

      $user = User::findOrFail($request->id);
      $user->delete();

      DB::commit();
      return Response()->json(['status' => 'success', 'message' => 'Berhasil menghapus akun pengguna a.n ' . $user->nama . '!']);
    } catch (\Exception $e) {
      DB::rollback();
      return Response()->json(['status' => 'failed', 'message' => 'Gagal menghapus akun pengguna a.n ' . $user->nama . '! pesan : ' . $e->getMessage()]);
    }
  }
}
