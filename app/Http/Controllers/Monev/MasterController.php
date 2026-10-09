<?php

namespace App\Http\Controllers\Monev;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB, Auth;

class MasterController extends Controller
{
  public function data_sekolah(Request $request)
  {
    $kecamatan = $request->kecamatan;
    $kelurahan = $request->kelurahan;
    $jenjang = $request->jenjang ?? 'paud';

    if ($jenjang == 'paud') {
      $conn = 'pgsql_paud';
      $jenjang_sekolah = ['TK', 'KB', 'SPS', 'TPA'];
    } else {
      $conn = 'db_sd';
      $jenjang_sekolah = ['SD', 'MI'];
    }

    $jumlah_murid = DB::connection($conn)->table('public.siswa as s')
      ->join('public.wali_murid as wm', DB::raw("CONCAT(wm.id_siswa,wm.npsn)"), DB::raw("CONCAT(s.id_siswa,s.npsn)"))
      ->join('public.sekolah as se', 's.npsn', 'se.npsn')
      ->join('public.master_user as u', 'u.username', 's.npsn')
      ->join('public.kecamatan as kec', 'kec.kecamatan_kode', 'se.kec_id')
      ->join('public.kelurahan as kel', 'kel.kelurahan_kode', 'se.desa')
      ->selectRaw("
            s.npsn as npsn_siswa,
            COUNT(*) as jumlah_murid,
            COUNT(*) FILTER(WHERE(COALESCE(s.kategori_inklusi,'')<>'' AND COALESCE(kebutuhan_khusus,'')='YA')) as jumlah_murid_inklusi
          ")
      ->whereRaw('s.alumni is not true')
      ->where('status_siswa', 'Aktif')
      ->when($kecamatan != 'all', function ($w) use ($kecamatan) {
        $w->where('kec.kecamatan_dispenduk', $kecamatan);
      })
      ->when($kelurahan != 'all', function ($w) use ($kelurahan) {
        $w->where('kel.kelurahan_dispenduk', $kelurahan);
      })
      ->groupByRaw("s.npsn");

    $jumlah_pegawai = DB::connection($conn)->table('public.pegawai as s')
      ->join('public.sekolah as se', 's.npsn', 'se.npsn')
      ->join('public.master_user as u', 'u.username', 'se.npsn')
      ->join('public.kecamatan as kec', 'kec.kecamatan_kode', 'se.kec_id')
      ->join('public.kelurahan as kel', 'kel.kelurahan_kode', 'se.desa')
      ->selectRaw("
            s.npsn as npsn_pegawai,
            COUNT(*) FILTER(WHERE (s.jenis_pegawai='KS')) as jumlah_ks,
            COUNT(*) FILTER(WHERE (s.jenis_pegawai='Guru')) as jumlah_guru,
            COUNT(*) FILTER(WHERE (s.jenis_pegawai='Tendik')) as jumlah_tendik,
            COUNT(*) FILTER(WHERE (s.jenis_pegawai NOT IN ('Tendik','Guru','KS'))) as jumlah_lain
          ")
      ->where('s.keterangan', 'Aktif')
      ->when($kecamatan != 'all', function ($w) use ($kecamatan) {
        $w->where('kec.kecamatan_dispenduk', $kecamatan);
      })
      ->when($kelurahan != 'all', function ($w) use ($kelurahan) {
        $w->where('kel.kelurahan_dispenduk', $kelurahan);
      })
      ->groupByRaw("s.npsn");

    $sekolah = DB::connection($conn)->table('public.sekolah as s')
      ->join('public.master_user as u', 'u.username', 's.npsn')
      ->join('public.kecamatan as kec', 'kec.kecamatan_kode', 's.kec_id')
      ->join('public.kelurahan as kel', 'kel.kelurahan_kode', 's.desa')
      ->leftJoinSub($jumlah_murid, 'js', function ($j) {
        return $j->on('js.npsn_siswa', '=', 's.npsn');
      })
      ->leftJoinSub($jumlah_pegawai, 'jp', function ($j) {
        return $j->on('jp.npsn_pegawai', '=', 's.npsn');
      })
      ->selectRaw("
        s.npsn,s.nama as nama_lembaga,s.alamat,s.status,s.jenjang,s.akreditasi,
        kec.kecamatan_dispenduk,
        kel.kelurahan_dispenduk,
        js.jumlah_murid_inklusi as jumlah_murid_inklusi,js.jumlah_murid as jumlah_murid,jp.jumlah_ks as jumlah_ks,jp.jumlah_guru as jumlah_guru,jp.jumlah_tendik as jumlah_tendik,jp.jumlah_lain as jumlah_lain
      ")
      ->whereRaw("u.user_enable is true")
      ->whereIn('s.jenjang', $jenjang_sekolah)
      ->when($kecamatan != 'all', function ($w) use ($kecamatan) {
        $w->where('kec.kecamatan_dispenduk', $kecamatan);
      })
      ->when($kelurahan != 'all', function ($w) use ($kelurahan) {
        $w->where('kel.kelurahan_dispenduk', $kelurahan);
      });

    return $sekolah;
  }

  public function data_kecamatan(Request $request)
  {
    $data_kecamatan = DB::table('public.kecamatan as kec')
      ->join('public.kelurahan as kel', 'kel.kecamatan_kode', 'kec.kecamatan_kode')
      ->selectRaw("kec.kecamatan_id as id_kecamatan,kec.kecamatan_dispenduk,kel.kelurahan_id as id_kelurahan,kel.kelurahan_dispenduk,kec.kecamatan_kode")
      ->orderByRaw("kec.kecamatan_dispenduk,kel.kelurahan_dispenduk")->get();

    $kecamatan = collect($data_kecamatan)->groupBy('kecamatan_dispenduk')->map(function ($m) {
      $baris = $m->first();
      return [
        'kecamatan' => $baris->kecamatan_dispenduk,
        'id_kecamatan' => $baris->id_kecamatan,
        'kelurahan' => $m,
      ];
    });

    return ['kecamatan' => $kecamatan, 'semua' => $data_kecamatan];
  }
}
