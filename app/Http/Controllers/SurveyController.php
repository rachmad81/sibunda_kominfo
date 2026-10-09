<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB,Session,Auth;

class SurveyController extends Controller
{
	function cek_survey(Request $request){
		$id = Auth::user()->id;
		$tahun = date('Y');

		$survey = DB::table('public.survey')->whereRaw("user_id='$id' AND tahun='$tahun'")->count();
		if($tahun=='2022'){
			if(date('Y-m-d')<=$tahun.'-05-10' && $survey==1){
				return ['code'=>200];
			}else if(date('Y-m-d')<=$tahun.'-06-30' && $survey==2){
				return ['code'=>200];
			}else if(date('Y-m-d')<=$tahun.'-12-31' && $survey==3){
				return ['code'=>200];
			}else{
				return ['code'=>250];
			}
		}else{
			if(date('Y-m-d')<=$tahun.'-06-30' && $survey==1){
				return ['code'=>200];
			}else if(date('Y-m-d')<=$tahun.'-12-31' && $survey>=2){
				return ['code'=>200];
			}else{
				return ['code'=>250];
			}
		}
	}

	function simpan(Request $request){
		$bintang = $request->bintang; //: "3"
		$saran = $request->saran; //: "Hello dude"
		$id = Auth::user()->id;
		$tahun = date('Y');

		$izinkan = 1;

		$survey = DB::table('public.survey')->whereRaw("user_id='$id' AND tahun='$tahun'")->count();
		if($tahun=='2022'){
			if(date('Y-m-d')<=$tahun.'-05-10' && $survey==0){
				$smt = 'Mei';
			}else if(date('Y-m-d')<=$tahun.'-06-30' && $survey==1){
				$smt = '1';
			}else if(date('Y-m-d')<=$tahun.'-12-31' && $survey==2){
				$smt = '2';
			}else{
				$smt = 'Mei';
			}
		}else{
			if(date('Y-m-d')<=$tahun.'-06-30' && $survey==0){
				$smt = '1';
			}else if(date('Y-m-d')<=$tahun.'-12-31' && $survey==1){
				$smt = '2';
			}else{
				$smt = '1';
			}
		}

		if($izinkan==1){
			$data_insert = [
				'user_id'=>$id,
				'created_at'=>date('Y-m-d H:i:s'),
				'bintang'=>$bintang,
				'saran'=>$saran,
				'tahun'=>$tahun,
				'semester'=>$smt,
			];
			$simpan = DB::table('public.survey')->insert($data_insert);
			if($simpan){
				return ['code'=>200];
			}else{
				return ['code'=>250];
			}
		}

		return ['code'=>200];
	}
}
