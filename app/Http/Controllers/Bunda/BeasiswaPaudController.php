<?php

namespace App\Http\Controllers\Bunda;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use DB, Auth, DataTables;

class BeasiswaPaudController extends Controller
{
  function halaman(Request $request)
  {
    $halaman = $request->halaman;

    switch ($halaman) {
      case 'main':
        $tahun_beasiswa = DB::connection('pgsql_paud')->table('public.konfirmasi_beasiswa')->selectRaw("tahun")->where('status_pengajuan', 'Disetujui')->orderBy('tahun', 'DESC')->pluck('tahun')->unique()->toArray();

        $kecamatan = DB::connection('pgsql_paud')->table('public.kecamatan as kec')
          ->join('public.kelurahan as kel', 'kel.kecamatan_kode', 'kec.kecamatan_kode')
          ->selectRaw("kec.kecamatan_id,kec.kecamatan_kode,kec.kecamatan_dispenduk,kel.kelurahan_kode,kel.kelurahan_dispenduk,kel.kelurahan_id")
          ->orderByRaw("kec.kecamatan_dispenduk,kel.kelurahan_dispenduk")
          ->get();

        $data_kecamatan = collect($kecamatan)->groupBy('kecamatan_kode')->map(function ($m) {
          $baris = $m->first();
          return [
            'kecamatan_dispenduk' => $baris->kecamatan_dispenduk,
            'kecamatan_id' => $baris->kecamatan_id,
            'kecamatan_kode' => $baris->kecamatan_kode,
            'kelurahan' => $m->where('kecamatan_kode', $baris->kecamatan_kode)->values(),
          ];
        })->values();

        $data_kelurahan = '';
        if (Auth::getUser()->tingkat == 'kelurahan') {
          $data_kelurahan = collect($kecamatan)->where('kelurahan_id', Auth::getUser()->id_kelurahan)->first();
        }

        $data = [
          'tahun_beasiswa' => $tahun_beasiswa,
          'data_kecamatan' => $data_kecamatan,
          'data_kelurahan' => $data_kelurahan,
        ];
        return view('bunda.beasiswa_paud.main', $data);
        break;

      case 'get_data':
        $kecamatan = $request->kecamatan; //"semua"
        $kelurahan = $request->kelurahan; //null
        $periode = $request->periode; //"1"
        $tahun = $request->tahun; //"2026"
        $tingkat = Auth::getUser()->tingkat;
        $role = Auth::getUser()->role;

        $data = DB::connection('pgsql_paud')->table('public.konfirmasi_beasiswa as b')
          ->join('public.detail_konfirmasi_beasiswa as d', 'd.konfirmasi_beasiswa_id', 'b.id')
          ->join('public.sekolah as s', 's.npsn', 'b.npsn')
          ->selectRaw("
            b.id,b.npsn,b.jenjang,b.tanggal_konfirmasi,b.periode,b.tahun,b.file_surat_pengajuan,
            d.nama_siswa,d.nik_siswa,d.no_kk,d.alamat,d.kelurahan,d.kecamatan,d.tanggal_lahir,
            s.nama as nama_sekolah
            ")
          ->where('b.tahun', $tahun)
          ->where('b.periode', $periode)
          ->where('d.status_menerima', 'Ya')
          ->where('b.status_pengajuan', 'Disetujui')
          ->when($kecamatan != 'semua', function ($w) use ($kecamatan) {
            $w->where('d.kecamatan', $kecamatan);
          })
          ->when($kelurahan != 'semua', function ($w) use ($kelurahan) {
            $w->where('d.kelurahan', $kelurahan);
          });

        return DataTables::queryBuilder($data)
          ->addIndexColumn()
          ->addColumn('surat_pengajuan', function ($i) {
            if ($i->file_surat_pengajuan != '') {
              $url = 'https://dispendik.surabaya.go.id/profilsekolah_file/beasiswapaud/konfirmasi/' . $i->id . '/' . $i->file_surat_pengajuan;
              return '<a href="' . $url . '" target="_blank" class="btn btn-sm btn-success">Lihat</a>';
            }
            return '';
          })
          ->rawColumns(['surat_pengajuan'])
          ->make(true);
        break;

      default:
        # code...
        break;
    }
  }
}
