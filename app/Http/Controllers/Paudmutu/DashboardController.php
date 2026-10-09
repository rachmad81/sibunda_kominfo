<?php

namespace App\Http\Controllers\Paudmutu;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB, Session;

class DashboardController extends Controller
{
  function halaman(Request $request)
  {
    // return $request->All();
    $halaman = $request->halaman;
    $npsn = $request->npsn;
    $tahun = date('Y');

    switch ($halaman) {
      case 'main':
        $sekolah = DB::connection('pgsql_paud')->table('public.sekolah as s')
          ->leftJoin('public.kecamatan as kec', 'kec.kecamatan_kode', 's.kec_id')
          ->leftJoin('public.kelurahan as kel', 'kel.kelurahan_kode', 's.desa')
          ->leftJoin('public.pegawai as p', function ($j) {
            $j->on('p.npsn', '=', 's.npsn')
              ->where('p.keterangan', 'Aktif')
              ->where('p.jabatan', '2')
              ->limit(1);
          })
          ->selectRaw("s.nama,s.npsn,s.no_sk_operasional,s.jenjang,s.status,s.alamat,kec.kecamatan_dispenduk,kel.kelurahan_dispenduk,s.rt,s.rw,s.email,s.telp,p.nama as nama_ks,p.telp as telp_ks,p.hp as hp_ks,s.email")
          ->where('s.npsn', $npsn)
          ->first();

        $ada_berkas = DB::connection('pgsql_paud')
          ->table('paud_bermutu.jenis_berkas as j')
          ->join('paud_bermutu.berkas_pendaftar as b', 'b.jenis_berkas_id', 'j.id')
          ->join('paud_bermutu.data_pendaftar as p', 'p.id', 'b.pendaftar_id')
          ->selectRaw("
            'ada' as kategori,
            COUNT(j.id) FILTER(WHERE (j.group IN ('Administrasi Pendaftaran'))) as jumlah_daftar_ada,
            COUNT(j.id) FILTER(WHERE (j.group IN ('Adminitrasi Lembaga','Adminitrasi Pembelajaran','Adminitrasi Sarpras','Adminitrasi Guru dan Tenaga Kependidikan (GTK)','Adminitrasi Keuangan'))) as jumlah_tahap1_ada,
            COUNT(j.id) FILTER(WHERE (j.group IN ('Tahap II'))) as jumlah_tahap2_ada
          ")
          ->where('j.tahun', $tahun)
          ->where('p.npsn', $npsn);

        $berkas = DB::connection('pgsql_paud')
          ->table('paud_bermutu.jenis_berkas as j')
          ->unionAll($ada_berkas)
          ->selectRaw("
            'berkas' as kategori,
            COUNT(j.id) FILTER(WHERE (j.group IN ('Administrasi Pendaftaran'))) as jumlah_daftar,
            COUNT(j.id) FILTER(WHERE (j.group IN ('Adminitrasi Lembaga','Adminitrasi Pembelajaran','Adminitrasi Sarpras','Adminitrasi Guru dan Tenaga Kependidikan (GTK)','Adminitrasi Keuangan'))) as jumlah_tahap1,
            COUNT(j.id) FILTER(WHERE (j.group IN ('Tahap II'))) as jumlah_tahap2
          ")
          ->where('j.tahun', $tahun)
          ->get();

        $data = [
          'active_menu' => 'dashboard',
          'sekolah' => $sekolah,
          'berkas' => $berkas,
          'npsn' => $npsn,
          'tahun' => $tahun,
        ];
        return view('paudmutu.dashboard.main', $data);
        break;

      default:
        return '404 - Halaman tidak ditemukan';
        break;
    }
  }
}
