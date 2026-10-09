<?php

namespace App\Http\Controllers\Bunda;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CommonController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\JabatanPokja;
use DataTables, DB;
use App\Tools\Bantuan;
use Carbon\Carbon;

class UserController extends Controller
{

    private $commonController;

    public function __construct(CommonController $common){
        $this->commonController = $common;
    }

    public function index(Request $request){
        $list_jabatan = JabatanPokja::where('tingkat',\Auth::User()->tingkat)->whereIn('id',[4,19,34])->get();

        if ($request->ajax()) {
            $user = User::where('id','!=',\Auth::User()->id)
                        ->where('tingkat',\Auth::User()->tingkat)
                        ->when(\Auth::User()->id_kecamatan, function($q){
                            $q->where('id_kecamatan',\Auth::User()->id_kecamatan);
                        })
                        ->when(\Auth::User()->id_kelurahan, function($q){
                            $q->where('id_kelurahan',\Auth::User()->id_kelurahan);
                        })
                        ->whereNotIn('role',['dinas','opd'])
                        ->select('id','nama','username','role','id_jabatan','verified','last_login')
                        ->orderBy('role','desc')
                        ->orderBy('id_jabatan')
                        ->get();

            return DataTables()->of($user)->addIndexColumn()
                    ->addColumn('aksi', function ($akun) {
                        $button = '<button class="btn btn-sm btn-outline-warning"
                                            data-toggle="modal" data-target="#modal_ubah" onclick="vm.updateData(`'.str_replace('"', '&quot;', $akun->toEncoded()).'`)">
                                            Ubah
                                        </button>';
                        $button .= '<button class="btn btn-sm btn-outline-danger" onclick="vm.confirmDeleteAlert('.$akun->id.')">Hapus</button>';
                        
                        return $button;
                    })
                    ->editColumn('role', function ($akun) {
                        return Bantuan::getRoleLabel($akun->role);
                    })
                    ->editColumn('last_login', function ($akun) {
                        return is_null($akun->last_login)?'Belum pernah masuk':$akun->last_login;
                    })
                    ->editColumn('jabatan', function ($akun) {
                        return (!empty($akun->jabatanPokja)) ? $akun->jabatanPokja->nama_jabatan : '';
                    })
                    ->addColumn('status', function ($akun) {
                        return $akun->verified ? 'Sudah Update' : 'Belum Update';
                    })
                    ->rawColumns(['aksi','role'])
                    ->toJson();

        }

        return view('bunda.user.index', compact('list_jabatan'));
    }

    public function store(Request $request){
        // dd($request->all());
        try {
            DB::beginTransaction();
            $addQuery = \Auth::User()->tingkat == 'kecamatan' ? " and id_kecamatan = ".\Auth::User()->id_kecamatan : "";
            $addQuery .= \Auth::User()->tingkat == 'kelurahan' ? " and id_kelurahan = ".\Auth::User()->id_kelurahan : "";
            $cek = DB::table('users')
                        ->whereRaw("role = 'pokja' and tingkat = ? and id_jabatan = ? and deleted_at is null".$addQuery,[\Auth::User()->tingkat, $request->jabatan])
                        ->exists();
            if ($cek) {
                throw new \Exception("Jabatan sudah terisi!", 503);
            }
            
            $tmp = [
                'nama'         => $request->nama,
                'username'     => $request->username,
                'role'         => 'pokja',
                'id_jabatan'   => $request->jabatan,
                'tingkat'      => \Auth::User()->tingkat,
                'id_kecamatan' => \Auth::User()->id_kecamatan,
                'id_kelurahan' => \Auth::User()->id_kelurahan,
                'password'     => Hash::make($request->password),
                'created_at'   => date('Y-m-d H:i:s'),
                'verified'     => false
            ];

            $insertProfil = $this->commonController->createProfilPokja((object) $tmp);

            if (!$insertProfil) {
                throw new \Exception("Gagal menambah profil pokja!", 403);
            }

            $id = DB::table('users')->insertGetId($tmp);

            DB::commit();
            return redirect()->back()->with('alert-success','Berhasil menambah User a.n "<b>'.$request->nama.'</b>"!');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('alert-fail','Gagal menambah user! Pesan : '.$e->getMessage());
        }
    }

    public function update(Request $request){
        $request->validate([
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
        ]);
        try{
            DB::beginTransaction();

            $user = User::findOrFail($request->id,['username','password']);

            DB::table('users')->where('id',$request->id)->update([
                'nama' => $request->nama,
                // 'username' => $request->username,
                'password' => $request->has('password')?Hash::make($request->password):$user->password,
                'updated_at' => Carbon::now()
            ]);

            DB::commit();
            return redirect(route('bunda-user-index'))->with('alert-success','Berhasil mengubah akun pengguna!');
        }catch(\Exception $e){
            DB::rollback();
            return redirect(route('bunda-user-index'))->with('alert-fail','Gagal mengubah akun pengguna! pesan : '.$e->getMessage());
        }
    }

    public function delete(Request $request){
        
        try{
            DB::beginTransaction();

            $user = User::findOrFail($request->id);
            $user->delete();

            DB::commit();
            return Response()->json(['status'=>'success','message'=>'Berhasil menghapus akun pengguna a.n '.$user->nama.'!']);
        }catch(\Exception $e){
            DB::rollback();
            return Response()->json(['status'=>'failed','message'=>'Gagal menghapus akun pengguna a.n '.$user->nama.'! pesan : '.$e->getMessage()]);
        }
    }
}
