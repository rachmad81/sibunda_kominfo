<?php 
namespace App\Http\Controllers\Bunda;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use DB;

class RekapBesarController extends Controller
{
	function index(){
		$kecamatans =  DB::connection('db_sd')
		->table('public.kecamatan')
		->join('public.kelurahan', 'kelurahan.kecamatan_kode', 'kecamatan.kecamatan_kode')
		->when(request()->user()->tingkat == 'kecamatan', function ($q) {
			$q->where('kecamatan.kecamatan_id', request()->user()->id_kecamatan);
		})
		->when(request()->user()->tingkat == 'kelurahan', function ($q) {
			$q->where('kelurahan.kelurahan_id', request()->user()->id_kelurahan);
		})
		->select(
			'kecamatan.kecamatan_id',
			'kecamatan.kecamatan_dispenduk',
			'kecamatan.kecamatan_kode',
			DB::raw("JSON_AGG(
				JSON_BUILD_OBJECT(
				'kelurahan_id', kelurahan.kelurahan_id,
				'kelurahan_dispenduk', kelurahan.kelurahan_dispenduk,
				'kelurahan_kode', kelurahan.kelurahan_kode
				)
			) AS kelurahans")
		)
		->groupBy(
			'kecamatan.kecamatan_id',
			'kecamatan.kecamatan_dispenduk',
			'kecamatan.kecamatan_kode'
		)
		->orderBy('kecamatan.kecamatan_dispenduk')
		->get();

		$data = [
			'kecamatans' => $kecamatans,			
		];
		return view('rekap_besar.main',$data);
	}

	function get_data(Request $request){
		$kecamatan = $request->kecamatan;
		$kelurahan = $request->kelurahan;
		$load = $request->load;
		$tahun_anggaran = 2023;

		if($kecamatan=='all'){
			$where_kecamatan_profil = "";
			$where_kecamatan_data = "";
		}else{
			$where_kecamatan_profil = " AND s.kec_id='$kecamatan'";
			$where_kecamatan_data = " AND kec_id='$kecamatan'";
		}

		if($kelurahan=='all'){
			$where_kelurahan_profil = "";
			$where_kelurahan_data = "";
			$where_kelurahan_data_lembaga = "";
		}else{
			$where_kelurahan_profil = " AND s.desa='$kelurahan'";
			$where_kelurahan_data = " AND desa='$kelurahan'";
			$where_kelurahan_data_lembaga = " AND kel_id='$kelurahan'";
		}

		$lembaga = DB::connection('pgsql')->table('cutoff_rekap.rekap_lembaga')->whereRaw("tgl_simpan='".date('Y-m-d')."' $where_kecamatan_data $where_kelurahan_data_lembaga")
		->selectRaw("
			jenjang,
			status,
			COUNT(DISTINCT(npsn)) as jumlah_lembaga,
			COUNT(DISTINCT(CASE WHEN akreditasi='A' THEN npsn ELSE null END)) as akreditasi_a,
			COUNT(DISTINCT(CASE WHEN akreditasi='B' THEN npsn ELSE null END)) as akreditasi_b,
			COUNT(DISTINCT(CASE WHEN akreditasi not in ('A','B','C') OR akreditasi is null THEN npsn ELSE null END)) as akreditasi_belum,
			COUNT(DISTINCT(CASE WHEN akreditasi='C' THEN npsn ELSE null END)) as akreditasi_c
			")
		->groupByRaw("jenjang,status")
		->orderByRaw("CASE jenjang WHEN 'TPA' THEN 1 WHEN 'KB' THEN 2 WHEN 'SPS' THEN 3 ELSE 4 END,status")
		->get();

		$siswa = DB::connection('pgsql')->table('cutoff_rekap.rekap_siswa')->whereRaw("tgl_simpan='".date('Y-m-d')."' $where_kecamatan_data $where_kelurahan_data")
		->selectRaw("
			jenjang,
			status,
			SUM(jumlah_siswa) as jumlah_siswa,
			SUM(jumlah_sudah_tb_bb) as jumlah_sudah_tb_bb,
			SUM(jumlah_sudah_polio_dosis_1) as jumlah_sudah_polio_dosis_1,
			SUM(jumlah_sudah_polio_dosis_2) as jumlah_sudah_polio_dosis_2,
			SUM(jumlah_educitytour) as jumlah_educitytour,
			SUM(jumlah_talenta) as jumlah_talenta,
			SUM(jumlah_pmt) as jumlah_pmt,
			SUM(jumlah_prestasi) as jumlah_prestasi,
			SUM(jumlah_kitab_suci) as jumlah_kitab_suci,
			SUM(jumlah_gakin_pramis) as jumlah_gakin_pramis,
			SUM(jumlah_talenta) as jumlah_talenta,
			SUM(jumlah_manasik) as jumlah_manasik
			")
		->groupByRaw("jenjang,status")
		->orderByRaw("CASE jenjang WHEN 'TPA' THEN 1 WHEN 'KB' THEN 2 WHEN 'SPS' THEN 3 ELSE 4 END,status")
		->get();

		$gtk = DB::connection('pgsql')->table('cutoff_rekap.rekap_gtk')->whereRaw("tgl_simpan='".date('Y-m-d')."' $where_kecamatan_data $where_kelurahan_data")
		->selectRaw("
			jenjang,
			status,
			SUM(jumlah_gtk) as jumlah_gtk,
			SUM(jumlah_belum_nuptk) as jumlah_belum_nuptk,
			SUM(jumlah_pelatihan) as jumlah_pelatihan,
			SUM(jumlah_jaspel) as jumlah_jaspel,
			SUM(jumlah_tpg) as jumlah_tpg,
			SUM(jumlah_tamsil) as jumlah_tamsil,
			SUM(jumlah_tukin) as jumlah_tukin,
			SUM(jumlah_guru_penggerak) as jumlah_guru_penggerak,
			SUM(jumlah_kenkat_4) as jumlah_kenkat_4,
			SUM(jumlah_kenkat_lebih_4) as jumlah_kenkat_lebih_4,
			SUM(jumlah_nonpns) as jumlah_nonpns,
			SUM(jumlah_pensiun_tahun) as jumlah_pensiun_tahun,
			SUM(jumlah_pns) as jumlah_pns,
			SUM(jumlah_pppk) as jumlah_pppk,
			SUM(jumlah_prestasi) as jumlah_prestasi,
			SUM(jumlah_s1) as jumlah_s1,
			SUM(jumlah_s2) as jumlah_s2,
			SUM(jumlah_s3) as jumlah_s3,
			SUM(jumlah_sinaubareng) as jumlah_sinaubareng,
			SUM(jumlah_tfg) as jumlah_tfg,
			SUM(jumlah_tpp) as jumlah_tpp
			")
		->groupByRaw("jenjang,status")
		->orderByRaw("CASE jenjang WHEN 'TPA' THEN 1 WHEN 'KB' THEN 2 WHEN 'SPS' THEN 3 ELSE 4 END,status")
		->get();

		$data = [
			'lembaga'=>$lembaga,
			'siswa'=>$siswa,
			'gtk'=>$gtk,
		];

		$content = view('rekap_besar.data',$data)->render();

		return ['content'=>$content];
	}
}
?>