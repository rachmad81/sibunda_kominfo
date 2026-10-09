<?php 
namespace App\Http\Controllers\Penilai;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB,DataTables,Auth;

class PenilaianController extends Controller
{
	function index(){
		return view('penilai.penilaian.tahap1.index');
	}

	function modal_identitas(Request $request){
		$user_id = $request->user_id;
		$tahun = $request->tahun;

		$identitas = DB::table('cutoff_rekap.peserta_lomba as l')
		->join('public.users as u','u.id','l.user_id')
		->selectRaw("l.*,u.tingkat")
		->whereRaw("user_id='$user_id' AND tahun='$tahun'")->first();
		$berkas = DB::table('cutoff_rekap.berkas_identitas')->whereRaw("user_id='$user_id' AND tahun='$tahun'")->first();

		$data = [
			'identitas'=>$identitas,
			'berkas'=>$berkas,
		];
		$content = view('penilai.penilaian.'.$tahun.'.modal_identitas',$data)->render();

		return ['content'=>$content];
	}

	function simpan_nilai(Request $request){
		$table = $request->table;
		$bunda_id = $request->bunda_id;
		$berkas = $request->berkas;
		$file = $request->file;
		
		$arr_kolom = explode('___', $berkas);
		$kolom = $arr_kolom[0];

		$where = [
			'user_id'=>$bunda_id,
			'tahun'=>date('Y'),
			'penilai_id'=>Auth::User()->id,
		];

		if(count($arr_kolom)>1){
			$number = $arr_kolom[1];
			$id_kegiatan = $arr_kolom[2];
			$sub = $arr_kolom[3];
			$berkas = DB::table('cutoff_rekap.'.$table)->where($where)->first();
			
			$data_insert = [];
			if(!empty($berkas)){
				if(!is_null($berkas->$kolom)){
					$exist = json_decode($berkas->$kolom);
					foreach($exist as $ex){
						if($ex->number==$number){
						}else{
							$data_lama = [
								'number'=>isset($ex->number) ? $ex->number : '',
								'id_kegiatan'=>isset($ex->id_kegiatan) ? $ex->id_kegiatan : '',
								'sub'=>isset($ex->sub) ? $ex->sub : '',
								'file'=>isset($ex->file) ? $ex->file : '',
							];
							array_push($data_insert,$data_lama);
						}
					}
				}
				array_push($data_insert,['number'=>$number,'id_kegiatan'=>$id_kegiatan,'sub'=>$sub,'file'=>$file]);
			}else{
				array_push($data_insert,['number'=>$number,'id_kegiatan'=>$id_kegiatan,'sub'=>$sub,'file'=>$file]);
			}

			$data_insert = collect($data_insert)->sortBy('number')->all();
			$data_insert = array_values($data_insert);
			$data_insert = json_encode($data_insert);
		}else{
			$data_insert = $file;
		}

		$data = [
			$kolom=>$data_insert,
			'updated_at'=>date('Y-m-d H:i:s'),
		];

		$simpan = DB::table('cutoff_rekap.'.$table)->updateOrInsert($where,$data);

		if($simpan){
			return ['code'=>200,'message'=>'Berhasil disimpan'];
		}else{
			return ['code'=>250,'message'=>'Gagal disimpan'];
		}
	}
}
?>