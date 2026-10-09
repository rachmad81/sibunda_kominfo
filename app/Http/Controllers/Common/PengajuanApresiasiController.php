<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ApresiasiPengajuan;
use App\Models\ApresiasiPengajuanDetail;
use App\Models\ProfilPokja;
use DB;

class PengajuanApresiasiController extends Controller
{
    public function index(Request $request)
    {
        $data = ApresiasiPengajuan::where('tingkat',\Auth::User()->tingkat)
                        ->when(\Auth::User()->id_kecamatan, function($q){
                            $q->where('id_kecamatan',\Auth::User()->id_kecamatan);
                        })
                        ->when(\Auth::User()->id_kelurahan, function($q){
                            $q->where('id_kelurahan',\Auth::User()->id_kelurahan);
                        })
                        ->get();

        return view('common.apresiasi.index',compact('data'));
    }

    public function kelola(Request $request, $aksi){
        $data = '';

        if ($request->has('id')) {
            $data = ApresiasiPengajuan::with('Detail')->find($request->id);
        }
        else{
            $data = ProfilPokja::where('tingkat',\Auth::User()->tingkat)
                        ->when(\Auth::User()->id_kecamatan, function($q){
                            $q->where('id_kecamatan',\Auth::User()->id_kecamatan);
                        })
                        ->when(\Auth::User()->id_kelurahan, function($q){
                            $q->where('id_kelurahan',\Auth::User()->id_kelurahan);
                        })
                        ->whereIn('id_jabatan',[3,18,33])->first();

            if ($data) {
                $data->id_bunda = $data->id;
            }
        }

        return view('common.apresiasi.kelola',compact('aksi','data'));
    }

    public function store(Request $request){
        try {
            DB::beginTransaction();

            $cek = ApresiasiPengajuan::where('tahun',$request->tahun)
                        ->where('tingkat',\Auth::User()->tingkat)
                        ->when(\Auth::User()->id_kecamatan, function($q){
                            $q->where('id_kecamatan',\Auth::User()->id_kecamatan);
                        })
                        ->when(\Auth::User()->id_kelurahan, function($q){
                            $q->where('id_kelurahan',\Auth::User()->id_kelurahan);
                        })->exists();

            if ($cek) {
                throw new \Exception("Wilayah Anda telah mengajukan Apresiasi di tahun ".$request->tahun, 403);
            }

            $data = [
                'tahun'                 => $request->tahun,
                'id_bunda'              => $request->id_bunda,
                'nama'                  => $request->nama,
                'email'                 => $request->email,
                'no_telp'               => $request->no_telp,
                'alamat'                => $request->alamat,
                'created_by'            => \Auth::User()->id,
                'tingkat'               => \Auth::User()->tingkat,
                'id_kecamatan'          => \Auth::User()->id_kecamatan,
                'id_kelurahan'          => \Auth::User()->id_kelurahan,
            ];

            ApresiasiPengajuan::create($data);
            
            DB::commit();
            return redirect(route('common-apresiasi-index'))->with('alert-success','Berhasil menyimpan pengajuan Apresiasi!');
        }  catch (\Exception $e) {
            DB::rollback();
            // LogError::create(['fungsi'=>'updateProfil','lokasi'=>'CommonController','pesan'=>$e->getMessage().' Oleh : '.\Auth::User()->username]);
            return redirect(route('common-apresiasi-index'))->with('alert-fail','Gagal menyimpan pengajuan Apresiasi! Error : '.$e->getMessage());
        }
    }

    public function update(Request $request){
        try {
            DB::beginTransaction();

            $cek = ApresiasiPengajuan::where('tahun',$request->tahun)
                        ->where('tingkat',\Auth::User()->tingkat)
                        ->when(\Auth::User()->id_kecamatan, function($q){
                            $q->where('id_kecamatan',\Auth::User()->id_kecamatan);
                        })
                        ->when(\Auth::User()->id_kelurahan, function($q){
                            $q->where('id_kelurahan',\Auth::User()->id_kelurahan);
                        })
                        ->where('id','!=',$request->id)->exists();

            if ($cek) {
                throw new \Exception("Wilayah Anda telah mengajukan Apresiasi di tahun ".$request->tahun, 403);
            }

            $data = [
                'tahun'                 => $request->tahun,
                'nama'                  => $request->nama,
                'email'                 => $request->email,
                'no_telp'               => $request->no_telp,
                'alamat'                => $request->alamat,
            ];

            ApresiasiPengajuan::where('id',$request->id)->update($data);
            
            DB::commit();
            return redirect(route('common-apresiasi-index'))->with('alert-success','Berhasil mengubah pengajuan Apresiasi!');
        }  catch (\Exception $e) {
            DB::rollback();
            // LogError::create(['fungsi'=>'updateProfil','lokasi'=>'CommonController','pesan'=>$e->getMessage().' Oleh : '.\Auth::User()->username]);
            return redirect(route('common-apresiasi-index'))->with('alert-fail','Gagal mengubah pengajuan Apresiasi! Error : '.$e->getMessage());
        }
    }

    public function delete(Request $request){
        try {
            DB::beginTransaction();

            ApresiasiPengajuan::where('id',$request->id)->delete();
            
            DB::commit();
            return Response()->json(['status'=> 'success','message' => 'Data pengajuan berhasil dihapus!']);
        }  catch (\Exception $e) {
            DB::rollback();
            // LogError::create(['fungsi'=>'updateProfil','lokasi'=>'CommonController','pesan'=>$e->getMessage().' Oleh : '.\Auth::User()->username]);
            return Response()->json(['status'=> 'failed','message' => 'Gagal menghapus pengajuan! Error : '.$e->getMessage()]);
        }
    }

    public function kelolaKegiatan(Request $request, $id){

        $data = ApresiasiPengajuanDetail::with('Komponen','Detail')->where('id_pengajuan', $request->id);

        return view('common.apresiasi.kelola',compact('aksi','data'));
    }
}
