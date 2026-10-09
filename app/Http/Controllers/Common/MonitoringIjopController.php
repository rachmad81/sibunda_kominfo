<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MonitoringIjop;
use App\Models\Ijop;
use App\Models\ProfilPokja;
use App\Models\ProfilSekolah\Kecamatan;
use App\Models\ProfilSekolah\Kelurahan;
use DB, Storage;
use App\Tools\Waktu;
use App\Tools\Bantuan;


class MonitoringIjopController extends Controller
{
    public function index(Request $request){
        $list_sekolah = Ijop::when(\Auth::User()->id_kecamatan, function($q){
                                $kec = Kecamatan::where('kecamatan_id',\Auth::User()->id_kecamatan)->first();
                                return $q->where('kecamatan_kode',$kec->kecamatan_kode);
                            })
                            ->when(\Auth::User()->id_kelurahan, function($q){
                                $kel = Kelurahan::where('kelurahan_id',\Auth::User()->id_kelurahan)->first();
                                return $q->where('kelurahan_kode',$kel->kelurahan_kode);
                            })
                            ->whereRaw("(order_izin_online = '0' OR order_mati_ijob_2 = '0' OR order_mati_ijob_1 = '0')")
                            ->select('npsn','nama_sekolah','jenjang')
                            ->orderByRaw("jenjang, nama_sekolah")
                            ->get();

        $list_petugas = ProfilPokja::when(\Auth::User()->id_kecamatan, function($q){
                                return $q->where('id_kecamatan',\Auth::User()->id_kecamatan);
                            })
                            ->when(\Auth::User()->id_kelurahan, function($q){
                                return $q->where('id_kelurahan',\Auth::User()->id_kelurahan);
                            })
                            ->orderByRaw("id_jabatan, nama")
                            ->with('jabatanPokja')
                            ->get();

        if ($request->ajax()) {
            $data = MonitoringIjop::where('tingkat',\Auth::User()->tingkat)
                        ->when(\Auth::User()->id_kecamatan, function($q){
                            $q->where('id_kecamatan',\Auth::User()->id_kecamatan);
                        })
                        ->when(\Auth::User()->id_kelurahan, function($q){
                            $q->where('id_kelurahan',\Auth::User()->id_kelurahan);
                        })
                        ->with('Sekolah',"Petugas")
                        ->get();

            return DataTables()->of($data)->addIndexColumn()
                    ->addColumn('aksi', function ($value) {
                        // $button = '<button class="btn btn-sm btn-outline-warning"
                                            // data-toggle="modal" data-target="#modal_ubah" onclick="vm.updateData(`'.str_replace('"', '&quot;', json_encode($value) ).'`)">
                                        //     Ubah
                                        // </button>';
                        $button = '<button class="btn btn-sm btn-outline-danger" onclick="vm.confirmDeleteAlert('.$value->id.')">Hapus</button>';
                        
                        return $button;
                    })
                    ->editColumn('tanggal', function ($value) {
                        return Waktu::parseFullDate($value->tanggal);
                    })
                    ->addColumn('sekolah', function ($value) {
                        return $value->npsn.' - '.$value->Sekolah->nama;
                    })
                    ->addColumn('petugas', function ($value) {
                        return $value->Petugas->nama;
                    })
                    ->editColumn('keterangan', function ($value) {
                        return $value->getKeterangan();
                    })
                    ->rawColumns(['aksi','keterangan'])
                    ->toJson();

        }

        return view('common.monitoring-ijop.index', compact('list_sekolah','list_petugas'));
    }

    public function store(Request $request){
        // dd($request->all());
        try {
            DB::beginTransaction();
            $addQuery = \Auth::User()->tingkat == 'kecamatan' ? " and id_kecamatan = ".\Auth::User()->id_kecamatan : "";
            $addQuery .= \Auth::User()->tingkat == 'kelurahan' ? " and id_kelurahan = ".\Auth::User()->id_kelurahan : "";
            
            $tmp = [
                'tingkat'      => \Auth::User()->tingkat,
                'id_kecamatan' => \Auth::User()->id_kecamatan,
                'id_kelurahan' => \Auth::User()->id_kelurahan,
                'tanggal'      => $request->tanggal,
                'id_petugas'   => $request->petugas,
                'npsn'         => $request->npsn,
                'hasil'        => $request->hasil,
                'keterangan'   => $request->keterangan ? $request->keterangan : null,
                'created_at'   => date('Y-m-d H:i:s'),
            ];

            if ($request->hasFile('pernyataan_tutup')) {
                $file = $request->file('pernyataan_tutup');

                $a = Bantuan::validate_file($request->file('pernyataan_tutup'),'gambar');

                if($a){
                    $nama_file = "file-monitoring/ijop_".date('YmdHis')."-".uniqid().".".$file->extension();

                    Storage::put($nama_file, file_get_contents($file));

                    $tmp = array_merge($tmp,['path_pernyataan_tutup'=>$nama_file]);
                }
            }

            $id = DB::table('monitoring_ijop')->insert($tmp);

            DB::commit();
            return redirect()->back()->with('alert-success','Berhasil menambah hasil monitoring !');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('alert-fail','Gagal menambah hasil monitoring! Pesan : '.$e->getMessage());
        }
    }

    public function update(Request $request){
        try{
            DB::beginTransaction();


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

            $data = MonitoringIjop::findOrFail($request->id);

            if ($data->path_pernyataan_tutup) {
                if (Storage::exists($data->path_pernyataan_tutup) ) {
                    Storage::delete($data->path_pernyataan_tutup);
                }
            }
            $data->delete();

            DB::commit();
            return Response()->json(['status'=>'success','message'=>'Berhasil menghapus data monitoring ijop!']);
        }catch(\Exception $e){
            DB::rollback();
            return Response()->json(['status'=>'failed','message'=>'Gagal menghapus data monitoring ijop! pesan : '.$e->getMessage()]);
        }
    }
}
