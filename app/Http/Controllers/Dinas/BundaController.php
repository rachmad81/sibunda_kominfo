<?php

namespace App\Http\Controllers\Dinas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProfilSekolah\Kecamatan;
use App\Models\ProfilSekolah\Kelurahan;
use App\Models\ProfilPokja;
use App\Models\RiwayatPokja;

use Redirect,DB;

class BundaController extends Controller
{
    public function index(){
        $list_kecamatan = Kecamatan::orderBy('kecamatan_name')->get();
        $list_kelurahan = Kelurahan::with('kecamatan')->get()
                            ->sortBy(function($q){
                                return [$q->kecamatan->kecamatan_name,$q->kelurahan_name];
                            });

        $data[] = [
            'tingkat'       => 'Kota',
            'id'            => null,
            'kecamatan'     => 'Kota Surabaya',
            'id_kelurahan'  => null,
            'kelurahan'     => 'Kota Surabaya'
        ];

        foreach ($list_kecamatan as $key => $value) {
            array_push($data, [
                'tingkat'       => 'Kecamatan',
                'id'            => $value->kecamatan_id,
                'kecamatan'     => $value->kecamatan_name,
                'kelurahan'     => null
            ]);
        }

        foreach ($list_kelurahan as $key => $value) {
            array_push($data, [
                'tingkat'       => 'Kelurahan',
                'id'            => $value->kelurahan_id,
                'kecamatan'     => $value->kecamatan->kecamatan_name,
                'kelurahan'     => $value->kelurahan_name
            ]);
        }

        return view('dinas.daftar-bunda.index',compact('data'));
    }

    public function getList(Request $request, $jenis){
        $data = ProfilPokja::when($jenis == 'bunda', function($q){
                        return $q->whereIn('id_jabatan',[3, 18, 33]);
                    })
                    ->where('tingkat', strtolower($request->tingkat))
                    ->when($request->tingkat == 'Kecamatan', function($q) use ($request){
                        return $q->where('id_kecamatan', $request->id);
                    })
                    ->when($request->tingkat == 'Kelurahan', function($q) use ($request){
                        return $q->where('id_kelurahan', $request->id);
                    })
                    ->first();

        return Response()->json(['status'=>'success','obj'=>$data ? $data : '-']);
    }
}
