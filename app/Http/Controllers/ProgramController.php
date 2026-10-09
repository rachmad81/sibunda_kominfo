<?php 
namespace App\Http\Controllers;

date_default_timezone_set('Asia/Jakarta');

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use App\Models\ProfilSekolah\Sekolah;
use App\Models\ProfilSekolah\Kecamatan;
use App\Models\ProfilSekolah\Kelurahan;
use App\Models\User;
use App\Models\Ijop;
use App\Models\Program;
use DB,Auth,Datatables,Waktu;

class ProgramController extends Controller{
	function index(){
		return view('program.index');
	}

	function data(Request $request){
		if(isset($request->th)){
			$tahun = $request->th;
		}else{
			$tahun = 'xxxx';
		}

		$data = $this->get_program($tahun,Auth::getUser()->id_kecamatan,Auth::getUser()->id_kelurahan,Auth::getUser()->tingkat);

		if(isset($request->get_program)){
			return $data;
		}else{
			$content = view('program.data',['data'=>$data])->render();

			return['content'=>$content];
		}
	}

	public static function get_program($tahun,$id_kecamatan,$id_kelurahan,$tingkat){

		$where = '';
		if($id_kecamatan!=null){$where .= "AND id_kecamatan='".$id_kecamatan."'";}
		if($id_kelurahan!=null){$where .= "AND id_kelurahan='".$id_kelurahan."'";}
		if($tingkat!=null){$where .= "AND tingkat='".$tingkat."'";}

		$data = Program::whereRaw("tahun='$tahun' $where")
        	->where('updated_by', request()->user()->username)->orderBy('nama_kegiatan')->get();

		return $data;
	}

	function form(Request $request){
		$id_program = $request->id_program;

		if($id_program==null){
			$id_program = 0;
			$title = 'Tambah Program';
		}else{
			$title = 'Edit Program';
		}
		$program = Program::whereRaw("id_program='$id_program'")->first();

		$data = [
			'title'=>$title,
			'program'=>$program,
		];

		$content = view('program.form',$data)->render();

		return ['content'=>$content];
	}

	function simpan(Request $request){
		$id_program = $request->id_program;
		$nama_kegiatan = $request->nama_kegiatan;
		$sasaran = $request->sasaran;
		$jumlah_sasaran = $request->jumlah_sasaran;
		$tahun = $request->tahun;
		$tujuan = $request->tujuan;
		$bidang = $request->bidang;
		$waktu = null;

		if(isset($request->waktu)){
			$waktu = join(',',$request->waktu);
		}

		$data = [
			'bidang'=>$bidang,
			'nama_kegiatan'=>$nama_kegiatan,
			'sasaran'=>$sasaran,
			'jumlah_sasaran'=>$jumlah_sasaran,
			'tujuan'=>$tujuan,
			'tahun'=>$tahun,
			'id_kecamatan'=>Auth::getUser()->id_kecamatan,
			'id_kelurahan'=>Auth::getUser()->id_kelurahan,
			'tingkat'=>Auth::getUser()->tingkat,
			'waktu'=>$waktu,
			'updated_by'=>Auth::getUser()->username,
			'updated_at'=>date('Y-m-d H:i:s'),
		];

		if($id_program==null){
			$simpan = Program::insert($data);
		}else{
			$simpan = Program::where('id_program',$id_program)->update($data);
		}

		if($simpan){
			$return = ['code'=>'200','message'=>'Berhasil disimpan','title'=>'Success','type'=>'success'];
		}else{
			$return = ['code'=>'250','message'=>'Gagal disimpan','title'=>'Whooops','type'=>'error'];
		}

		return $return;
	}

	function hapus(Request $request){
		$id_program = $request->id_program;
		$hapus = Program::where('id_program',$id_program)->delete();

		if($hapus){
			$return = ['code'=>'200','message'=>'Berhasil dihapus','title'=>'Success','type'=>'success'];
		}else{
			$return = ['code'=>'250','message'=>'Gagal dihapus','title'=>'Whooops','type'=>'error'];
		}

		return $return;
	}
}
?>