<?php

namespace App\Http\Controllers\Opd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Monev\MasterController;

use DB, Session, DataTables, Auth;

class KKAController extends Controller
{
  function halaman(Request $request)
  {
    $halaman = $request->halaman;

    switch ($halaman) {
      case 'main':
        $tahun = DB::connection('pgsql_paud')->table('history.kartu_kembang_anak')->selectRaw("tahun")->groupBy('tahun')->orderBy('tahun', 'DESC')->pluck('tahun')->toArray();

        $kecamatan = MasterController::data_kecamatan($request);

        $data = [
          'tahun' => $tahun,
          'kecamatan' => $kecamatan['kecamatan'],
          'data_kecamatan' => $kecamatan['semua'],
        ];
        return view('opd.kka.main', $data);
        break;

      case 'get_data':
        $tahun = $request->tahun_monev;
        $bulan = $request->bulan_monev;
        $kecamatan = $request->wadah_kecamatan;
        $kelurahan = $request->wadah_kelurahan;

        $tingkat = Auth::getUser()->tingkat;
        $role = Auth::getUser()->role;

        $kka = DB::connection('pgsql_paud')->table('history.kartu_kembang_anak as kka')
          ->join('public.siswa as s', 'kka.siswa_id', 's.siswa_id')
          ->join('public.sekolah as se', 'se.npsn', 's.npsn')
          ->join('public.master_user as u', 'u.username', 'se.npsn')
          ->leftJoin('history.rujukan as r', 'r.kka_id', 'kka.id')
          ->leftJoin('history.history_tb_bb_siswa as tbbb', 'tbbb.siswa_id', 's.siswa_id')
          ->leftJoin('public.kecamatan as kec', 'kec.kecamatan_kode', 'se.kec_id')
          ->leftJoin('public.kelurahan as kel', 'kel.kelurahan_kode', 'se.desa')
          ->selectRaw("
            s.nik,s.status_siswa,s.alumni, s.nama, s.tgl_lahir, se.npsn, se.nama as nama_sekolah, kka.tahun, kka.bulan, kka.kesimpulan_kka, kka.rekomendasi,tbbb.tb,tbbb.bb,tbbb.status_gizi,COALESCE(CAST(r.id_rujukan as varchar),'') as id_rujuk,kec.kecamatan_dispenduk as kecamatan_sekolah,kel.kelurahan_dispenduk as kelurahan_sekolah
          ")
          ->whereNotNull('kka.kesimpulan_kka')
          ->when($kecamatan != 'all', function ($w) use ($kecamatan) {
            return $w->where('kec.kecamatan_dispenduk', $kecamatan);
          })
          ->when($kelurahan != 'all', function ($w) use ($kelurahan) {
            return $w->where('kel.kelurahan_dispenduk', $kelurahan);
          })
          ->when($tahun !== null, function ($w) use ($tahun) {
            return $w->where('kka.tahun', $tahun);
          })
          ->when($bulan !== null, function ($w) use ($bulan) {
            return $w->where('kka.bulan', $bulan);
          })->when($tahun !== null, function ($w) use ($tahun) {
            return $w->where('tbbb.tahun', $tahun);
          })
          ->when($bulan !== null, function ($w) use ($bulan) {
            return $w->where('tbbb.bulan', $bulan);
          });

        return DataTables::queryBuilder($kka)
          ->addIndexColumn()
          ->addColumn('rujukan', function ($i) {
            $tombol = '';
            if ($i->id_rujuk != '') {
              $tombol = '<a href="' . route('opd-kka', ['halaman' => 'cetak_rujukan', 'id' => $i->id_rujuk]) . '" target="_blank" class="btn btn-success">Cetak Rujukan</a>';
            }
            return $tombol;
          })
          ->addColumn('status_alumni', function ($i) {
            if ($i->alumni == true) {
              return 'Lulus';
            }
          })
          ->filterColumn('status_alumni', function ($w, $keyword) {
            return $w->whereRaw("CASE WHEN s.alumni is true THEN 'Lulus' ELSE '' END ilike '%$keyword%'");
          })
          ->orderColumn('status_alumni', function ($w, $order) {
            return $w->orderByRaw("CASE WHEN s.alumni is true THEN 0 ELSE 1 END $order");
          })
          ->rawColumns(['rujukan'])
          ->make(true);
        break;

      case 'hitung_jumlah':
        $tahun = $request->tahun_monev;
        $bulan = $request->bulan_monev;
        $kecamatan = $request->wadah_kecamatan;
        $kelurahan = $request->wadah_kelurahan;

        $jumlah_sudah = DB::connection('pgsql_paud')->table('history.kartu_kembang_anak as kka')
          ->join('public.siswa as s', 'kka.siswa_id', 's.siswa_id')
          ->join('public.sekolah as se', 'se.npsn', 's.npsn')
          ->join('public.master_user as u', 'u.username', 'se.npsn')
          ->leftJoin('history.rujukan as r', 'r.kka_id', 'kka.id')
          ->leftJoin('history.history_tb_bb_siswa as tbbb', 'tbbb.siswa_id', 's.siswa_id')
          ->leftJoin('public.kecamatan as kec', 'kec.kecamatan_kode', 'se.kec_id')
          ->leftJoin('public.kelurahan as kel', 'kel.kelurahan_kode', 'se.desa')
          ->selectRaw("
            s.nik,s.status_siswa,s.alumni, s.nama, s.tgl_lahir, se.npsn, se.nama as nama_sekolah, kka.tahun, kka.bulan, kka.kesimpulan_kka, kka.rekomendasi,tbbb.tb,tbbb.bb,tbbb.status_gizi,COALESCE(CAST(r.id_rujukan as varchar),'') as id_rujuk,kec.kecamatan_dispenduk as kecamatan_sekolah,kel.kelurahan_dispenduk as kelurahan_sekolah
          ")
          ->whereNotNull('kka.kesimpulan_kka')
          ->when($kecamatan != 'all', function ($w) use ($kecamatan) {
            return $w->where('kec.kecamatan_dispenduk', $kecamatan);
          })
          ->when($kelurahan != 'all', function ($w) use ($kelurahan) {
            return $w->where('kel.kelurahan_dispenduk', $kelurahan);
          })
          ->when($tahun !== null, function ($w) use ($tahun) {
            return $w->where('kka.tahun', $tahun);
          })
          ->when($bulan !== null, function ($w) use ($bulan) {
            return $w->where('kka.bulan', $bulan);
          })
          ->when($tahun !== null, function ($w) use ($tahun) {
            return $w->where('tbbb.tahun', $tahun);
          })
          ->when($bulan !== null, function ($w) use ($bulan) {
            return $w->where('tbbb.bulan', $bulan);
          })->count();

        $jumlah_siswa = DB::connection('pgsql_paud')->table('public.siswa as s')
          ->join('public.sekolah as se', 'se.npsn', 's.npsn')
          ->join('public.master_user as u', 'u.username', 'se.npsn')
          ->leftJoin('public.kecamatan as kec', 'kec.kecamatan_kode', 'se.kec_id')
          ->leftJoin('public.kelurahan as kel', 'kel.kelurahan_kode', 'se.desa')
          ->selectRaw("s.nama")
          ->whereRaw("u.user_enable is true AND s.alumni is not true")
          ->when($kecamatan != 'all', function ($w) use ($kecamatan) {
            return $w->where('kec.kecamatan_dispenduk', $kecamatan);
          })
          ->when($kelurahan != 'all', function ($w) use ($kelurahan) {
            return $w->where('kel.kelurahan_dispenduk', $kelurahan);
          })
          ->where('s.status_siswa', 'Aktif')->count();

        return ['jumlah_siswa' => $jumlah_siswa, 'jumlah_siswa_sudah' => $jumlah_sudah];
        break;

      case 'cetak_rujukan':
        $id = $request->id;
        $data =  DB::connection('pgsql_paud')->table('history.rujukan')->where('id_rujukan', $id)->first();
        return view('opd.kka.cetak_rujukan', ['data' => $data]);
        break;

      default:
        return '404 - Halaman tidak ditemukan';
        break;
    }
  }
}
