<?php 
namespace App\Http\Controllers\Dinas\Datapaud;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Pelatihan\Materi;
use App\Models\Edu\Opd;
use App\Models\User;
use DataTables;

class PelatihanController extends Controller
{
	function index(Request $request){
		$data = [];
		return view('dinas.datapaud.pelatihan.index',$data);
	}

	function data(Request $request){
		$tempat = Materi::orderBy('nama','ASC')->get();

		return DataTables()->of($tempat)->addIndexColumn()
		->addColumn('aksi',function($j){
			$button = '<a href="javascript:void(0)" onclick="form(\''.$j->id.'\')" class="btn btn-sm btn-primary">Edit</a>';
			return $button;
		})
		->rawColumns(['tembusan','aksi'])
		->make(true);
	}

	function form(Request $request){
		$id = $request->id;
		$wisata = Materi::find($id);
		// $opd = Opd::orderBy('nama_opd')->get();
		$opd = User::where('role','opd')->orderBy('nama')->get();
		$data = [
			'wisata'=>$wisata,
			'opd'=>$opd,
			'id_opd'=>(json_decode($wisata->id_opd,true)==null) ? [] : json_decode($wisata->id_opd,true),
		];

		$content = view('dinas.datapaud.pelatihan.form',$data)->render();

		return ['content'=>$content];
	}

	function simpan(Request $request){
		$id = $request->id;
		$nama = $request->nama;
		$deskripsi = $request->deskripsi;
		$opd = isset($request->opd) ? $request->opd : [];

		$data_opd = User::whereIn('id',$opd)->get();

		$tembusan=null;
		if(count($opd)!=0){
			$tembusan = '<ol>';
			for($i=0;$i<count($opd);$i++){
				$nama_opd = $data_opd->where('id',$opd[$i])->first();
				$tembusan .= '<li>'.$nama_opd->nama.'</li>';
			}
			$tembusan .= '</ol>';
		}

		$tempat = Materi::find($id);
		$tempat->nama = $nama;
		$tempat->deskripsi = $deskripsi;
		$tempat->tembusan = $tembusan;
		$tempat->id_opd = json_encode($opd);
		$tempat->save();

		if($tempat){
			return ['code'=>'200'];
		}else{
			return ['code'=>'250'];
		}
	}
}
?>