<?php

namespace App\Http\Controllers\Bunda;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;
use DB, Auth;

class DiagramLembagaController extends Controller
{
  function main()
  {
    $kecamatan = DB::table('public.kecamatan')->orderBy('kecamatan_dispenduk')->get();
    $data = [
      'kecamatan' => $kecamatan,
    ];
    return view('bunda.diagram_lembaga.index', $data);
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

    $judul = 'Data Diagram Akreditasi Satuan Pendidikan';
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

    $content = view('bunda.diagram_lembaga.tabelnya', $data)->render();
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

    $jumlah = DB::connection('pgsql_paud')->table('public.sekolah as s')
      ->join('public.master_user as u', 'u.username', 's.npsn')
      ->join('public.kecamatan as kec', 'kec.kecamatan_kode', 's.kec_id')
      ->join('public.kelurahan as kel', 'kel.kelurahan_kode', 's.desa')
      ->selectRaw("s.nama,s.npsn,s.jenjang,s.status,s.alamat,s.rt,s.rw,kec.kecamatan_dispenduk,kel.kelurahan_dispenduk,s.akreditasi")
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

    $jumlah_total = collect($jumlah)->count();
    $jumlah_a = collect($jumlah)->where('akreditasi', 'A')->count();
    $jumlah_b = collect($jumlah)->where('akreditasi', 'B')->count();
    $jumlah_c = collect($jumlah)->where('akreditasi', 'C')->count();
    $jumlah_lainnya = collect($jumlah)->whereNotIn('akreditasi', ['A', 'B', 'C'])->count();

    $dril = [
      [
        'kategori' => $jenjang,
        'name' => 'Semua',
        'y' => $jumlah_total,
      ],
      [
        'kategori' => $jenjang,
        'name' => 'Akreditasi A',
        'y' => $jumlah_a,
      ],
      [
        'kategori' => $jenjang,
        'name' => 'Akreditasi B',
        'y' => $jumlah_b,
      ],
      [
        'kategori' => $jenjang,
        'name' => 'Akreditasi C',
        'y' => $jumlah_c,
      ],
      [
        'kategori' => $jenjang,
        'name' => 'Lainnya',
        'y' => $jumlah_lainnya,
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
      'Nama Sekolah',
      'NPSN',
      'Akreditasi',
      'Jenjang',
      'Alamat',
      'RT',
      'RW',
      'Kelurahan',
      'Kecamatan',
      'Kota/Kabupaten'
    ];

    $kolom_body = [
      ['data' => 'DT_RowIndex', 'name' => 'DT_RowIndex', 'title' => 'No', 'orderable' => false, 'searchable' => false],
      ['data' => 'nama', 'name' => 's.nama', 'title' => 'Nama Sekolah'],
      ['data' => 'npsn', 'name' => 's.npsn', 'title' => 'NPSN'],
      ['data' => 'akreditasi', 'name' => 's.akreditasi', 'title' => 'Akreditasi'],
      ['data' => 'jenjang', 'name' => 's.jenjang', 'title' => 'Jenjang'],
      ['data' => 'alamat', 'name' => 's.alamat', 'title' => 'Alamat'],
      ['data' => 'rt', 'name' => 's.rt', 'title' => 'RT'],
      ['data' => 'rw', 'name' => 's.rw', 'title' => 'RW'],
      ['data' => 'kelurahan_dispenduk', 'name' => 'kel.kelurahan_dispenduk', 'title' => 'Kelurahan'],
      ['data' => 'kecamatan_dispenduk', 'name' => 'kec.kecamatan_dispenduk', 'title' => 'Kecamatan'],
      ['data' => 'kab_id', 'name' => 'kab_id', 'title' => 'Kota/Kabupaten']
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

    $content = view('bunda.diagram_lembaga.detail', $data)->render();
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

    $jumlah = DB::connection('pgsql_paud')->table('public.sekolah as s')
      ->join('public.master_user as u', 'u.username', 's.npsn')
      ->join('public.kecamatan as kec', 'kec.kecamatan_kode', 's.kec_id')
      ->join('public.kelurahan as kel', 'kel.kelurahan_kode', 's.desa')
      ->selectRaw("
			s.nama,s.npsn,s.jenjang,s.status,s.alamat,s.rt,s.rw,kec.kecamatan_dispenduk,kel.kelurahan_dispenduk,s.akreditasi,'SURABAYA' as kab_id,
			CASE WHEN s.akreditasi IN ('A','B','C') THEN s.akreditasi ELSE 'XX' END as akreditasi_baru
			")
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
      ->when($jenis == 'Akreditasi A', function ($query) {
        return $query->where('s.akreditasi', 'A');
      })
      ->when($jenis == 'Akreditasi B', function ($query) {
        return $query->where('s.akreditasi', 'B');
      })
      ->when($jenis == 'Akreditasi C', function ($query) {
        return $query->where('s.akreditasi', 'C');
      })
      ->when($jenis == 'Lainnya', function ($query) {
        return $query->havingRaw("CASE WHEN s.akreditasi IN ('A','B','C') THEN s.akreditasi ELSE 'XX' END NOT IN ('A','B','C')");
      })
      ->groupByRaw("s.nama,s.npsn,s.jenjang,s.status,s.alamat,s.rt,s.rw,kec.kecamatan_dispenduk,kel.kelurahan_dispenduk,s.akreditasi");

    return DataTables::of($jumlah)->addIndexColumn()->make(true);
  }
}
