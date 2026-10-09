<?php 
namespace App\Http\Controllers\Dinas;

date_default_timezone_set('Asia/Jakarta');

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use App\Models\ProfilSekolah\Sekolah;
use App\Models\ProfilSekolah\Kecamatan;
use App\Models\ProfilSekolah\Kelurahan;
use App\Models\User;
use App\Models\Ijop;
use App\Models\Program;
use DB,Auth,Datatables,Waktu;

class SurveyController extends Controller{
	function index(){
		$survey = DB::table('public.survey')->selectRaw("tahun,semester")->groupBy('tahun','semester')->get();
		$tahun = date('Y');
		if(date('Y')=='2022'){
			$smt='Mei';
		}else{
			$smt='1';
		}

		$data = [
			'tahun'=>$tahun.'|'.$smt,
			'survey'=>$survey,
		];
		return view('dinas.survey.index',$data);
	}

	function data(Request $request){
		$tahun = $request->tahun;

		$th = explode('|',$tahun);

		$survey = DB::table('public.survey as s')->join('public.users as u','u.id','s.user_id')
		->selectRaw("s.*,u.nama,u.tingkat")
		->whereRaw("tahun='$th[0]' AND semester='$th[1]'")->get();


		return DataTables()->of($survey)
		->editColumn('tgl_pengisian', function ($akun) {
			return date('d-m-Y',strtotime($akun->created_at));
		})
		->addIndexColumn()
		->make();
	}
}
?>