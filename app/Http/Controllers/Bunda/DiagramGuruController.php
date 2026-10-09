<?php

namespace App\Http\Controllers\Bunda;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;
use DB, Auth;

class DiagramGuruController extends Controller
{
  function main()
  {
    $kecamatan = DB::table('public.kecamatan')->orderBy('kecamatan_dispenduk')->get();
    $data = [
      'kecamatan' => $kecamatan,
    ];
    return view('bunda.diagram_guru.index', $data);
  }

  function get_kelurahan(Request $request)
  {
    $kode = $request->kode;
    $dukcapil = $request->dukcapil;

    if ($kode == 'All') {
      return ['code' => 250];
    } else {
      if (Auth::getUser()->tingkat == 'kelurahan') {
        $kelurahan = DB::table('public.kelurahan')->where('kelurahan_id', Auth::getUser()->id_kelurahan)->orderBy('kelurahan_dispenduk')->get();
      } else {
        $kelurahan = DB::table('public.kelurahan')->where('kecamatan_kode', $kode)->orderBy('kelurahan_dispenduk')->get();
      }

      return ['code' => 200, 'kelurahan' => $kelurahan];
    }
  }

  function get_data(Request $request)
  {
    $kecamatan = $request->kecamatan; //"All"
    $kelurahan = $request->kelurahan; //"All"
    $kecamatan_kode = $request->kecamatan_kode;
    $kelurahan_kode = $request->kelurahan_kode;

    $judul = 'Data Diagram Kualifikasi Guru';
    if ($kecamatan == 'All') {
      $judul .= ' se-Kota Surabaya';
    } else {
      if ($kelurahan == 'All') {
        $judul .= ' se-Kecamatan ' . $kecamatan;
      } else {
        if (Auth::getUser()->tingkat == 'kelurahan') {
          $data_kecamatan = DB::table('public.kelurahan as kel')->join('public.kecamatan as kec', 'kec.kecamatan_kode', 'kel.kecamatan_kode')->where('kelurahan_id', Auth::getUser()->id_kelurahan)->first();
          $kecamatan = $data_kecamatan->kecamatan_dispenduk;
          $kecamatan_kode = $data_kecamatan->kecamatan_kode;
          $judul .= ' se-Kelurahan ' . $kelurahan . ', Kecamatan ' . $kecamatan;
        } else {
          $judul .= ' se-Kelurahan ' . $kelurahan . ', Kecamatan ' . $kecamatan;
        }
      }
    }

    $data = [
      'judul' => $judul,
      'kecamatan' => $kecamatan,
      'kelurahan' => $kelurahan,
      'kecamatan_kode' => $kecamatan_kode,
      'kelurahan_kode' => $kelurahan_kode,
    ];

    $content = view('bunda.diagram_guru.tabelnya', $data)->render();
    return ['code' => 200, 'content' => $content];
  }

  function get_jumlah(Request $request)
  {
    $jenjang = $request->jenjang;
    $ket = json_decode($request->keterangan);
    $kecamatan = $request->kecamatan;
    $kelurahan = $request->kelurahan;
    $kecamatan_kode = $request->kecamatan_kode;
    $kelurahan_kode = $request->kelurahan_kode;

    $tahun_awal = date('Y') - 7;
    $tahun_akhir = date('Y') - 5;

    $where0 = "id is not null";
    $where2 = "s.npsn is not null";
    if ($kecamatan == 'All') {
      $where0 .= "";
      $where2 .= "";
    } else {
      if ($kelurahan == 'All') {
        $where0 .= " AND kecamatan='$kecamatan'";
        $where2 .= " AND s.kec_id='$kecamatan_kode'";
      } else {
        $where0 .= " AND kelurahan='$kelurahan'";
        $where2 .= " AND s.desa='$kelurahan_kode'";
      }
    }

    $jumlah_lembaga = DB::connection('pgsql_paud')->table('public.sekolah as s')
      ->join('public.master_user as u', 'u.username', 's.npsn')
      ->join('public.kecamatan as kec', 'kec.kecamatan_kode', 's.kec_id')
      ->join('public.kelurahan as kel', 'kel.kelurahan_kode', 's.desa')
      ->selectRaw("s.nama,s.npsn,s.jenjang,s.status,s.alamat,s.rt,s.rw,kec.kecamatan_dispenduk,kel.kelurahan_dispenduk")
      ->whereRaw("$where2 AND s.nama not ilike ALL(ARRAY['%terbuka%','%selalu%','WUSTHA%','ula%','pps%','pdf%','WUSTHO%']) AND u.user_enable is true")
      ->whereRaw("u.user_enable is true")
      ->whereIn('s.jenjang', ['TPA', 'SPS', 'RA', 'KB', 'TK'])
      ->when($jenjang != 'Semua', function ($query) use ($jenjang) {
        if ($jenjang == 'SPS/PPT') {
          return $query->where('s.jenjang', 'SPS');
        } else {
          return $query->where('s.jenjang', $jenjang);
        }
      })
      ->get();

    $npsn = collect($jumlah_lembaga)->pluck('npsn')->toArray();
    $jumlah = DB::connection('pgsql_paud')->table('public.pegawai as p')
      ->leftjoin('public.pendidikan as pend', 'pend.jenjang_pendidikan_id', 'p.pendidikan_terakhir')
      ->leftjoin('public.kecamatan as kec', 'kec.kecamatan_kode', 'p.kec_id')
      ->leftjoin('public.kelurahan as kel', 'kel.kelurahan_kode', 'p.kelurahan')
      ->join('public.sekolah as se', 'se.npsn', 'p.npsn')
      ->selectRaw("
			p.nama,pend.nama as pendidikan,p.no_ktp,p.pendidikan_terakhir,se.npsn,se.nama as nama_sekolah,p.jenis_pegawai,
			p.alamat,p.rt,p.rw,p.kab_id,
			CASE WHEN kec.kecamatan_dispenduk is null THEN p.kec_id ELSE kec.kecamatan_dispenduk END as kecamatan_dispenduk,
			CASE WHEN kel.kelurahan_dispenduk is null THEN p.kelurahan ELSE kel.kelurahan_dispenduk END as kelurahan_dispenduk
			")
      ->whereIn('p.npsn', $npsn)
      ->whereRaw("p.keterangan='Aktif' AND induk is true AND p.jabatan IN ('1','2','3','4','5','7','8','9','11','12','13','19','33','34','35','36','37','38','39','40','41','42','43','46','47','48','49','50','51','52','53')")
      ->get();

    $jumlah_total = collect($jumlah)->count();
    $jumlah_lain = collect($jumlah)->whereIn('pendidikan_terakhir', [1, 2, 3, 4])->count();
    $jumlah_sma = collect($jumlah)->whereIn('pendidikan_terakhir', [5])->count();
    $jumlah_d = collect($jumlah)->whereIn('pendidikan_terakhir', [6, 7, 8])->count();
    $jumlah_s = collect($jumlah)->whereIn('pendidikan_terakhir', [9, 10, 11])->count();

    $dril = [
      [
        'kategori' => $jenjang,
        'name' => 'Semua Lulusan',
        'y' => $jumlah_total,
      ],
      [
        'kategori' => $jenjang,
        'name' => 'Lulusan SMA',
        'y' => $jumlah_sma,
      ],
      [
        'kategori' => $jenjang,
        'name' => 'Lulusan D1 - D3',
        'y' => $jumlah_d,
      ],
      [
        'kategori' => $jenjang,
        'name' => 'Lulusan S1 - S3',
        'y' => $jumlah_s,
      ],
      [
        'kategori' => $jenjang,
        'name' => 'Lulusan Lainnya',
        'y' => $jumlah_lain,
      ],
    ];

    return ['i' => $ket->index, 'jumlah_display' => number_format($jumlah_total, 0, '', '.'), 'jumlah_murni' => $jumlah_total, 'dril' => $dril];
  }

  function form_detail(Request $request)
  {
    $jenis = $request->jenis;
    $kategori = $request->kategori;
    $kecamatan = $request->kecamatan;
    $kelurahan = $request->kelurahan;
    $kecamatan_kode = $request->kecamatan_kode;
    $kelurahan_kode = $request->kelurahan_kode;

    $kolom = [
      'No',
      'Nama',
      'NIK',
      'Nama Sekolah',
      'NPSN',
      'Pendidikan Terakhir',
      'Alamat',
      'RT',
      'RW',
      'Kelurahan',
      'Kecamatan',
      'Kota/Kabupaten'
    ];

    $kolom_body = [
      ['data' => 'DT_RowIndex', 'name' => 'DT_RowIndex', 'title' => 'No', 'orderable' => false, 'searchable' => false],
      ['data' => 'nama', 'name' => 'p.nama', 'title' => 'Nama'],
      ['data' => 'no_ktp', 'name' => 'p.no_ktp', 'title' => 'NIK'],
      ['data' => 'nama_sekolah', 'name' => 'se.nama', 'title' => 'Nama Sekolah'],
      ['data' => 'npsn', 'name' => 'se.npsn', 'title' => 'NPSN'],
      ['data' => 'pendidikan', 'name' => 'pend.nama', 'title' => 'Pendidikan'],
      ['data' => 'alamat', 'name' => 'p.alamat', 'title' => 'Alamat'],
      ['data' => 'rt', 'name' => 'p.rt', 'title' => 'RT'],
      ['data' => 'rw', 'name' => 'p.rw', 'title' => 'RW'],
      ['data' => 'kelurahan_dispenduk', 'name' => 'kelurahan_dispenduk', 'title' => 'Kelurahan'],
      ['data' => 'kecamatan_dispenduk', 'name' => 'kecamatan_dispenduk', 'title' => 'Kecamatan'],
      ['data' => 'kab_id', 'name' => 'p.kab_id', 'title' => 'Kota/Kabupaten']
    ];

    $judul = 'Data Kualifikasi Guru Jenjang ' . $kategori;
    $judul .= ' (' . $jenis . ')';

    $data = [
      'kolom' => $kolom,
      'kolom_body' => json_encode($kolom_body),
      'judul' => $judul,
      'jenis' => $jenis,
      'kategori' => $kategori,
      'kecamatan' => $kecamatan,
      'kelurahan' => $kelurahan,
      'kecamatan_kode' => $kecamatan_kode,
      'kelurahan_kode' => $kelurahan_kode,
    ];

    $content = view('bunda.diagram_guru.detail', $data)->render();
    return ['content' => $content];
  }

  function get_detail(Request $request)
  {
    $jenis = $request->jenis; //Jenis Jumlah
    $kategori = $request->kategori; //Jenjang
    $kecamatan = $request->kecamatan;
    $kelurahan = $request->kelurahan;
    $kecamatan_kode = $request->kecamatan_kode;
    $kelurahan_kode = $request->kelurahan_kode;

    $where0 = "id is not null";
    $where2 = "s.npsn is not null";
    if ($kecamatan == 'All') {
      $where0 .= "";
      $where2 .= "";
    } else {
      if ($kelurahan == 'All') {
        $where0 .= " AND kecamatan='$kecamatan'";
        $where2 .= " AND s.kec_id='$kecamatan_kode'";
      } else {
        $where0 .= " AND kelurahan='$kelurahan'";
        $where2 .= " AND s.desa='$kelurahan_kode'";
      }
    }

    $where0 = "id is not null";
    $where2 = "s.npsn is not null";
    if ($kecamatan == 'All') {
      $where0 .= "";
      $where2 .= "";
    } else {
      if ($kelurahan == 'All') {
        $where0 .= " AND kecamatan='$kecamatan'";
        $where2 .= " AND s.kec_id='$kecamatan_kode'";
      } else {
        $where0 .= " AND kelurahan='$kelurahan'";
        $where2 .= " AND s.desa='$kelurahan_kode'";
      }
    }

    $jumlah_lembaga = DB::connection('pgsql_paud')->table('public.sekolah as s')
      ->join('public.master_user as u', 'u.username', 's.npsn')
      ->join('public.kecamatan as kec', 'kec.kecamatan_kode', 's.kec_id')
      ->join('public.kelurahan as kel', 'kel.kelurahan_kode', 's.desa')
      ->selectRaw("s.nama,s.npsn,s.jenjang,s.status,s.alamat,s.rt,s.rw,kec.kecamatan_dispenduk,kel.kelurahan_dispenduk")
      ->whereRaw("$where2 AND s.nama not ilike ALL(ARRAY['%terbuka%','%selalu%','WUSTHA%','ula%','pps%','pdf%','WUSTHO%']) AND u.user_enable is true")
      ->whereRaw("u.user_enable is true")
      ->whereIn('s.jenjang', ['TPA', 'SPS', 'RA', 'KB', 'TK'])
      ->when($kategori != 'Semua', function ($query) use ($kategori) {
        if ($kategori == 'SPS/PPT') {
          return $query->where('s.jenjang', 'SPS');
        } else {
          return $query->where('s.jenjang', $kategori);
        }
      })
      ->get();

    $npsn = collect($jumlah_lembaga)->pluck('npsn')->toArray();
    $jumlah = DB::connection('pgsql_paud')->table('public.pegawai as p')
      ->leftjoin('public.pendidikan as pend', 'pend.jenjang_pendidikan_id', 'p.pendidikan_terakhir')
      ->leftjoin('public.kecamatan as kec', 'kec.kecamatan_kode', 'p.kec_id')
      ->leftjoin('public.kelurahan as kel', 'kel.kelurahan_kode', 'p.kelurahan')
      ->join('public.sekolah as se', 'se.npsn', 'p.npsn')
      ->selectRaw("
			p.nama,pend.nama as pendidikan,p.no_ktp,p.pendidikan_terakhir,se.npsn,se.nama as nama_sekolah,p.jenis_pegawai,
			p.alamat,p.rt,p.rw,p.kab_id,
			CASE WHEN kec.kecamatan_dispenduk is null THEN p.kec_id ELSE kec.kecamatan_dispenduk END as kecamatan_dispenduk,
			CASE WHEN kel.kelurahan_dispenduk is null THEN p.kelurahan ELSE kel.kelurahan_dispenduk END as kelurahan_dispenduk
			")
      ->whereIn('p.npsn', $npsn)
      ->whereRaw("p.keterangan='Aktif' AND induk is true AND p.jabatan IN ('1','2','3','4','5','7','8','9','11','12','13','19','33','34','35','36','37','38','39','40','41','42','43','46','47','48','49','50','51','52','53')")
      ->when($jenis == 'Lulusan SMA', function ($query) {
        return $query->whereIn('p.pendidikan_terakhir', [5]);
      })
      ->when($jenis == 'Lulusan D1 - D3', function ($query) {
        return $query->whereIn('p.pendidikan_terakhir', [6, 7, 8]);
      })
      ->when($jenis == 'Lulusan S1 - S3', function ($query) {
        return $query->whereIn('p.pendidikan_terakhir', [9, 10, 11]);
      })
      ->when($jenis == 'Lulusan Lainnya', function ($query) {
        return $query->whereIn('p.pendidikan_terakhir', [1, 2, 3, 4]);
      });

    return DataTables::of($jumlah)->addIndexColumn()->make(true);
  }
}
