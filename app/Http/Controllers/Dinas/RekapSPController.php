<?php

namespace App\Http\Controllers\Dinas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth, DB, DataTables;

class RekapSPController extends Controller
{
  function halaman(Request $request)
  {
    $halaman = $request->halaman;

    switch ($halaman) {
      case 'main':
        $kecamatan = DB::table('public.kecamatan')->orderBy('kecamatan_dispenduk')->get();
        $data = [
          'kecamatan' => $kecamatan,
        ];
        return view('dinas.rekap_sp.main', $data);
        break;

      case 'get_data':

        $kecamatan = $request->kecamatan ?? '';
        $kecamatan_kode = $request->kecamatan_kode ?? '';
        $kelurahan = $request->kelurahan ?? '';
        $kelurahan_kode = $request->kelurahan_kode ?? 'All';
        $pencarian = $request->pencarian ?? '';
        $arr_jenjang = $request->jenjang ?? [];
        $arr_jenjang = array_diff($arr_jenjang, ['All']);

        $str_jenjang = '\'' . implode('\',\'', $arr_jenjang) . '\'';

        $from = "
          FROM public.sekolah as se
          JOIN public.master_user as u on u.username=se.npsn
          JOIN public.kecamatan as kec on kec.kecamatan_kode=se.kec_id
          JOIN public.kelurahan as kel on kel.kelurahan_kode=se.desa
          ";

        $baseSekolahQuery = DB::connection('pgsql_paud')->table('public.sekolah as se')
          ->join('public.master_user as u', 'u.username', 'se.npsn')
          ->join('public.kecamatan as kec', 'kec.kecamatan_kode', 'se.kec_id')
          ->join('public.kelurahan as kel', 'kel.kelurahan_kode', 'se.desa')
          ->whereRaw("u.user_enable is true");

        $penerima_bop = DB::connection('db_simpaud')->table('public.bop_master_view')->selectRaw("npsn")
          ->where('jenis_anggaran', 'BOP')->groupBy('npsn')->get();
        $penerima_bop_kinerja = DB::connection('db_simpaud')->table('public.bop_master_view')->selectRaw("npsn")
          ->where('jenis_anggaran', 'BOP KINERJA')->groupBy('npsn')->get();
        $penerima_bopda = DB::connection('db_simpaud')->table('public.bop_master_view')->selectRaw("npsn")
          ->where('jenis_anggaran', 'BOPDA')->groupBy('npsn')->get();

        $arr_bop = collect($penerima_bop)->pluck('npsn')->toArray();
        $arr_bop_kinerja = collect($penerima_bop_kinerja)->pluck('npsn')->toArray();
        $arr_bopda = collect($penerima_bopda)->pluck('npsn')->toArray();

        $str_bop = '\'' . implode($arr_bop, '\',\'') . '\'';
        $str_bop_kinerja = '\'' . implode($arr_bop_kinerja, '\',\'') . '\'';
        $str_bopda = '\'' . implode($arr_bopda, '\',\'') . '\'';

        $select = "
        kec.kecamatan_dispenduk as kecamatan,
        kec.kecamatan_kode,
        kel.kelurahan_dispenduk as kelurahan,
        kel.kelurahan_kode,
        se.jenjang,
        ";
        // $tanggal = date('Y-m-d', strtotime('-1 days', strtotime(date('Y-m-d'))));
        $tanggal = date('Y-m-d');
        $update_view = 0;

        $cek = DB::connection('pgsql_paud')->select("SELECT table_name
          FROM information_schema.views
          WHERE table_name = 'v_rekap_sp';
          ");
        if (!empty($cek)) {
          $data_sekarang = DB::connection('pgsql_paud')->table('history.v_rekap_sp')
            ->where('tgl_update', $tanggal)->count();
          if (empty($data_sekarang)) {
            $update_view = 1;
          }
        } else {
          $update_view = 1;
        }

        if ($update_view == 1) {
          DB::connection('pgsql_paud')->statement("
              CREATE OR REPLACE VIEW history.v_rekap_sp as
              WITH
                data_siswa as (
                  SELECT
                  $select
                  COUNT(DISTINCT(CONCAT(s.npsn,s.rombel))) as jumlah_rombel
                  $from
                  JOIN public.siswa as s on s.npsn=se.npsn
                  JOIN public.wali_murid as wm on wm.id_siswa=s.id_siswa AND wm.npsn=s.npsn
                  WHERE u.user_enable is true
                  AND s.status_siswa='Aktif' AND s.alumni is not true
                  GROUP BY kec.kecamatan_dispenduk,kel.kelurahan_dispenduk,kec.kecamatan_kode,kel.kelurahan_kode,se.jenjang
                ),
                data_ape as (
                  SELECT
                  $select
                  COUNT(ape.npsn) as jumlah_ape
                  $from
                  JOIN public.pengisian_ape as ape ON ape.npsn=se.npsn
                  WHERE u.user_enable is true
                  GROUP BY kec.kecamatan_dispenduk,kel.kelurahan_dispenduk,kec.kecamatan_kode,kel.kelurahan_kode,se.jenjang
                ),
                data_bop as (
                  SELECT
                  $select
                  COUNT(*) FILTER(WHERE (se.npsn IN ($str_bop))) as jumlah_bop,
                  COUNT(*) FILTER(WHERE (se.npsn IN ($str_bop_kinerja))) as jumlah_bop_kinerja,
                  COUNT(*) FILTER(WHERE (se.npsn IN ($str_bopda))) as jumlah_bopda
                  $from
                  WHERE u.user_enable is true
                  GROUP BY kec.kecamatan_dispenduk,kel.kelurahan_dispenduk,kec.kecamatan_kode,kel.kelurahan_kode,se.jenjang
                ),
                data_paud_hi as (
                  SELECT
                  $select
                  COUNT(*) FILTER(WHERE (se.npsn IN ($str_bop))) as jumlah_paud_hi
                  $from
                  JOIN paud_hi.pengajuan_paud_hi as hi ON hi.npsn=se.npsn
                  WHERE u.user_enable is true
                  GROUP BY kec.kecamatan_dispenduk,kel.kelurahan_dispenduk,kec.kecamatan_kode,kel.kelurahan_kode,se.jenjang
                ),
                data_revit as (
                  SELECT
                  $select
                  COUNT(*) FILTER(WHERE (se.npsn IN ($str_bop))) as jumlah_revit
                  $from
                  JOIN history.rehabilitasi_bangunan as rev ON rev.npsn=se.npsn
                  WHERE u.user_enable is true
                  GROUP BY kec.kecamatan_dispenduk,kel.kelurahan_dispenduk,kec.kecamatan_kode,kel.kelurahan_kode,se.jenjang
                )
              SELECT
              $select
              '" . $tanggal . "' as tgl_update,
              COUNT(*) as jumlah_lembaga,
              COUNT(*) FILTER(WHERE (se.jenjang='TPA')) as jumlah_tpa,
              COUNT(*) FILTER(WHERE (se.jenjang='SPS')) as jumlah_sps,
              COUNT(*) FILTER(WHERE (se.jenjang='KB')) as jumlah_kb,
              COUNT(*) FILTER(WHERE (se.jenjang='TK')) as jumlah_tk,
              COUNT(*) FILTER(WHERE (se.jenjang='RA')) as jumlah_ra,
              COUNT(*) FILTER(WHERE (se.status='N')) as jumlah_negeri,
              COUNT(*) FILTER(WHERE (se.status<>'N')) as jumlah_swasta,
              SUM(kuota) as jumlah_kuota,
              COALESCE(MAX(ds.jumlah_rombel),0) as jumlah_rombel,
              COALESCE(MAX(ape.jumlah_ape),0) as jumlah_ape,
              COALESCE(MAX(bop.jumlah_bop),0) as jumlah_bop,
              COALESCE(MAX(bop.jumlah_bop_kinerja),0) as jumlah_bop_kinerja,
              COALESCE(MAX(bop.jumlah_bopda),0) as jumlah_bopda,
              COUNT(*) FILTER(WHERE (se.akreditasi='A')) as jumlah_akreditasi_a,
              COUNT(*) FILTER(WHERE (se.akreditasi='B')) as jumlah_akreditasi_b,
              COUNT(*) FILTER(WHERE (se.akreditasi='C')) as jumlah_akreditasi_c,
              COUNT(*) FILTER(WHERE (se.akreditasi NOT IN ('A','B','C'))) as jumlah_akreditasi_lain,
              COALESCE(MAX(hi.jumlah_paud_hi),0) as jumlah_paud_hi,
              COALESCE(MAX(rev.jumlah_revit),0) as jumlah_revit
              $from
              left join data_siswa as ds on ds.kecamatan_kode=kec.kecamatan_kode AND ds.kelurahan_kode=kel.kelurahan_kode AND ds.jenjang=se.jenjang
              left join data_ape as ape on ape.kecamatan_kode=kec.kecamatan_kode AND ape.kelurahan_kode=kel.kelurahan_kode AND ape.jenjang=se.jenjang
              left join data_bop as bop on bop.kecamatan_kode=kec.kecamatan_kode AND bop.kelurahan_kode=kel.kelurahan_kode AND bop.jenjang=se.jenjang
              left join data_paud_hi as hi on hi.kecamatan_kode=kec.kecamatan_kode AND hi.kelurahan_kode=kel.kelurahan_kode AND hi.jenjang=se.jenjang
              left join data_revit as rev on rev.kecamatan_kode=kec.kecamatan_kode AND rev.kelurahan_kode=kel.kelurahan_kode AND rev.jenjang=se.jenjang
              WHERE u.user_enable is true
              GROUP BY kec.kecamatan_dispenduk,kel.kelurahan_dispenduk,kec.kecamatan_kode,kel.kelurahan_kode,se.jenjang
          ");
        }

        $data = DB::connection('pgsql_paud')->table('history.v_rekap_sp')
          ->selectRaw("
            kecamatan,
            kecamatan_kode,
            kelurahan,
            kelurahan_kode,
            SUM(COALESCE(jumlah_lembaga,0)) as jumlah_lembaga,
            SUM(COALESCE(jumlah_tpa,0)) as jumlah_tpa,
            SUM(COALESCE(jumlah_sps,0)) as jumlah_sps,
            SUM(COALESCE(jumlah_kb,0)) as jumlah_kb,
            SUM(COALESCE(jumlah_tk,0)) as jumlah_tk,
            SUM(COALESCE(jumlah_ra,0)) as jumlah_ra,
            SUM(COALESCE(jumlah_negeri,0)) as jumlah_negeri,
            SUM(COALESCE(jumlah_swasta,0)) as jumlah_swasta,
            SUM(COALESCE(jumlah_kuota,0)) as jumlah_kuota,
            SUM(COALESCE(jumlah_rombel,0)) as jumlah_rombel,
            SUM(COALESCE(jumlah_ape,0)) as jumlah_ape,
            SUM(COALESCE(jumlah_bop,0)) as jumlah_bop,
            SUM(COALESCE(jumlah_bop_kinerja,0)) as jumlah_bop_kinerja,
            SUM(COALESCE(jumlah_bopda,0)) as jumlah_bopda,
            SUM(COALESCE(jumlah_akreditasi_a,0)) as jumlah_akreditasi_a,
            SUM(COALESCE(jumlah_akreditasi_b,0)) as jumlah_akreditasi_b,
            SUM(COALESCE(jumlah_akreditasi_c,0)) as jumlah_akreditasi_c,
            SUM(COALESCE(jumlah_akreditasi_lain,0)) as jumlah_akreditasi_lain,
            SUM(COALESCE(jumlah_paud_hi,0)) as jumlah_paud_hi,
            SUM(COALESCE(jumlah_revit,0)) as jumlah_revit
          ")
          ->whereIn('jenjang', $arr_jenjang)
          ->when($kecamatan_kode !== 'All', function ($w) use ($kecamatan_kode) {
            return $w->where('kecamatan_kode', $kecamatan_kode);
          })
          ->when($kelurahan_kode !== 'All', function ($w) use ($kelurahan_kode) {
            return $w->where('kelurahan_kode', $kelurahan_kode);
          })
          ->groupBy(
            'kecamatan',
            'kecamatan_kode',
            'kelurahan',
            'kelurahan_kode'
          );

        if ($pencarian == 'baris') {
          return DataTables::queryBuilder($data)
            ->addIndexColumn()
            ->addColumn('jumlah_lembaga', function ($a) {
              return '<a href="javascript:void(0)" onclick="modal(\'' . $a->kecamatan_kode . '\',\'' . $a->kelurahan_kode . '\',\'jumlah_lembaga\')">' . $a->jumlah_lembaga . '</a>';
            })
            ->addColumn('jumlah_tpa', function ($a) {
              return '<a href="javascript:void(0)" onclick="modal(\'' . $a->kecamatan_kode . '\',\'' . $a->kelurahan_kode . '\',\'jumlah_tpa\')">' . $a->jumlah_tpa . '</a>';
            })
            ->addColumn('jumlah_sps', function ($a) {
              return '<a href="javascript:void(0)" onclick="modal(\'' . $a->kecamatan_kode . '\',\'' . $a->kelurahan_kode . '\',\'jumlah_sps\')">' . $a->jumlah_sps . '</a>';
            })
            ->addColumn('jumlah_kb', function ($a) {
              return '<a href="javascript:void(0)" onclick="modal(\'' . $a->kecamatan_kode . '\',\'' . $a->kelurahan_kode . '\',\'jumlah_kb\')">' . $a->jumlah_kb . '</a>';
            })
            ->addColumn('jumlah_tk', function ($a) {
              return '<a href="javascript:void(0)" onclick="modal(\'' . $a->kecamatan_kode . '\',\'' . $a->kelurahan_kode . '\',\'jumlah_tk\')">' . $a->jumlah_tk . '</a>';
            })
            ->addColumn('jumlah_ra', function ($a) {
              return '<a href="javascript:void(0)" onclick="modal(\'' . $a->kecamatan_kode . '\',\'' . $a->kelurahan_kode . '\',\'jumlah_ra\')">' . $a->jumlah_ra . '</a>';
            })
            ->addColumn('jumlah_negeri', function ($a) {
              return '<a href="javascript:void(0)" onclick="modal(\'' . $a->kecamatan_kode . '\',\'' . $a->kelurahan_kode . '\',\'jumlah_negeri\')">' . $a->jumlah_negeri . '</a>';
            })
            ->addColumn('jumlah_swasta', function ($a) {
              return '<a href="javascript:void(0)" onclick="modal(\'' . $a->kecamatan_kode . '\',\'' . $a->kelurahan_kode . '\',\'jumlah_swasta\')">' . $a->jumlah_swasta . '</a>';
            })
            ->addColumn('jumlah_kuota', function ($a) {
              return '<a href="javascript:void(0)" onclick="modal(\'' . $a->kecamatan_kode . '\',\'' . $a->kelurahan_kode . '\',\'jumlah_kuota\')">' . $a->jumlah_kuota . '</a>';
            })
            ->addColumn('jumlah_rombel', function ($a) {
              return '<a href="javascript:void(0)" onclick="modal(\'' . $a->kecamatan_kode . '\',\'' . $a->kelurahan_kode . '\',\'jumlah_rombel\')">' . $a->jumlah_rombel . '</a>';
            })
            ->addColumn('jumlah_ape', function ($a) {
              return '<a href="javascript:void(0)" onclick="modal(\'' . $a->kecamatan_kode . '\',\'' . $a->kelurahan_kode . '\',\'jumlah_ape\')">' . $a->jumlah_ape . '</a>';
            })
            ->addColumn('jumlah_bop', function ($a) {
              return '<a href="javascript:void(0)" onclick="modal(\'' . $a->kecamatan_kode . '\',\'' . $a->kelurahan_kode . '\',\'jumlah_bop\')">' . $a->jumlah_bop . '</a>';
            })
            ->addColumn('jumlah_bop_kinerja', function ($a) {
              return '<a href="javascript:void(0)" onclick="modal(\'' . $a->kecamatan_kode . '\',\'' . $a->kelurahan_kode . '\',\'jumlah_bop_kinerja\')">' . $a->jumlah_bop_kinerja . '</a>';
            })
            ->addColumn('jumlah_bopda', function ($a) {
              return '<a href="javascript:void(0)" onclick="modal(\'' . $a->kecamatan_kode . '\',\'' . $a->kelurahan_kode . '\',\'jumlah_bopda\')">' . $a->jumlah_bopda . '</a>';
            })
            ->addColumn('jumlah_akreditasi_a', function ($a) {
              return '<a href="javascript:void(0)" onclick="modal(\'' . $a->kecamatan_kode . '\',\'' . $a->kelurahan_kode . '\',\'jumlah_akreditasi_a\')">' . $a->jumlah_akreditasi_a . '</a>';
            })
            ->addColumn('jumlah_akreditasi_b', function ($a) {
              return '<a href="javascript:void(0)" onclick="modal(\'' . $a->kecamatan_kode . '\',\'' . $a->kelurahan_kode . '\',\'jumlah_akreditasi_b\')">' . $a->jumlah_akreditasi_b . '</a>';
            })
            ->addColumn('jumlah_akreditasi_c', function ($a) {
              return '<a href="javascript:void(0)" onclick="modal(\'' . $a->kecamatan_kode . '\',\'' . $a->kelurahan_kode . '\',\'jumlah_akreditasi_c\')">' . $a->jumlah_akreditasi_c . '</a>';
            })
            ->addColumn('jumlah_akreditasi_lain', function ($a) {
              return '<a href="javascript:void(0)" onclick="modal(\'' . $a->kecamatan_kode . '\',\'' . $a->kelurahan_kode . '\',\'jumlah_akreditasi_lain\')">' . $a->jumlah_akreditasi_lain . '</a>';
            })
            ->addColumn('jumlah_paud_hi', function ($a) {
              return '<a href="javascript:void(0)" onclick="modal(\'' . $a->kecamatan_kode . '\',\'' . $a->kelurahan_kode . '\',\'jumlah_paud_hi\')">' . $a->jumlah_paud_hi . '</a>';
            })
            ->addColumn('jumlah_revit', function ($a) {
              return '<a href="javascript:void(0)" onclick="modal(\'' . $a->kecamatan_kode . '\',\'' . $a->kelurahan_kode . '\',\'jumlah_revit\')">' . $a->jumlah_revit . '</a>';
            })
            ->rawColumns([
              'jumlah_lembaga',
              'jumlah_tpa',
              'jumlah_sps',
              'jumlah_kb',
              'jumlah_tk',
              'jumlah_ra',
              'jumlah_negeri',
              'jumlah_swasta',
              'jumlah_kuota',
              'jumlah_rombel',
              'jumlah_ape',
              'jumlah_bop',
              'jumlah_bop_kinerja',
              'jumlah_bopda',
              'jumlah_akreditasi_a',
              'jumlah_akreditasi_b',
              'jumlah_akreditasi_c',
              'jumlah_akreditasi_lain',
              'jumlah_paud_hi',
              'jumlah_revit',
            ])
            ->make(true);
        } else {
          $baris = collect((clone $data)->get());
          $total = [
            'jumlah_lembaga' => $baris->sum('jumlah_lembaga'),
            'jumlah_tpa' => $baris->sum('jumlah_tpa'),
            'jumlah_sps' => $baris->sum('jumlah_sps'),
            'jumlah_kb' => $baris->sum('jumlah_kb'),
            'jumlah_tk' => $baris->sum('jumlah_tk'),
            'jumlah_ra' => $baris->sum('jumlah_ra'),
            'jumlah_negeri' => $baris->sum('jumlah_negeri'),
            'jumlah_swasta' => $baris->sum('jumlah_swasta'),
            'jumlah_rombel' => $baris->sum('jumlah_rombel'),
            'jumlah_kuota' => $baris->sum('jumlah_kuota'),
            'jumlah_ape' => $baris->sum('jumlah_ape'),
            'jumlah_bop' => $baris->sum('jumlah_bop'),
            'jumlah_bop_kinerja' => $baris->sum('jumlah_bop_kinerja'),
            'jumlah_bopda' => $baris->sum('jumlah_bopda'),
            'jumlah_akreditasi_a' => $baris->sum('jumlah_akreditasi_a'),
            'jumlah_akreditasi_b' => $baris->sum('jumlah_akreditasi_b'),
            'jumlah_akreditasi_c' => $baris->sum('jumlah_akreditasi_c'),
            'jumlah_akreditasi_lain' => $baris->sum('jumlah_akreditasi_lain'),
            'jumlah_paud_hi' => $baris->sum('jumlah_paud_hi'),
            'jumlah_revit' => $baris->sum('jumlah_revit'),
          ];

          $data = [
            'total' => $total,
          ];

          return view('dinas.rekap_sp.total', $data)->render();
        }
        break;

      case 'modal':
        // return $request->all();
        $kategori = $request->kategori ?? '';
        $jenjang = $request->jenjang ?? [];
        $kecamatan_kode = $request->kecamatan_kode ?? 'All';
        $kelurahan_kode = $request->kelurahan_kode ?? 'All';
        $jenjang = array_diff($jenjang, ['All']);

        $judul = 'Data ';
        switch ($kategori) {
          case 'jumlah_lembaga':
            $judul .= 'Lembaga';
            break;

          case 'jumlah_tpa':
            $judul .= 'Lembaga TPA ';
            break;

          case 'jumlah_sps':
            $judul .= 'Lembaga SPS/PPT ';
            break;

          case 'jumlah_kb':
            $judul .= 'Lembaga KB ';
            break;

          case 'jumlah_tk':
            $judul .= 'Lembaga TK ';
            break;

          case 'jumlah_ra':
            $judul .= 'Lembaga RA ';
            break;

          case 'jumlah_negeri':
            $judul .= 'Lembaga Negeri ';
            break;

          case 'jumlah_swasta':
            $judul .= 'Lembaga Swasta ';
            break;

          case 'jumlah_rombel':
            $judul .= 'Jumlah Rombel Lembaga ';
            break;

          case 'jumlah_kuota':
            $judul .= 'Jumlah Pagu/Kuota Lembaga ';
            break;

          case 'jumlah_ape':
            $judul .= 'Lembaga mengisi APE ';
            break;

          case 'jumlah_bop':
            $judul .= 'Lembaga menerima BOP ';
            break;

          case 'jumlah_bop_kinerja':
            $judul .= 'Lembaga menerima BOP KINERJA ';
            break;

          case 'jumlah_bopda':
            $judul .= 'Lembaga menerima BOPDA';
            break;

          case 'jumlah_akreditasi_a':
            $judul .= 'Lembaga Akreditasi A ';
            break;

          case 'jumlah_akreditasi_b':
            $judul .= 'Lembaga Akreditasi B ';
            break;

          case 'jumlah_akreditasi_c':
            $judul .= 'Lembaga Akreditasi C ';
            break;

          case 'jumlah_akreditasi_lain':
            $judul .= 'Lembaga Akreditasi Lain ';
            break;

          case 'jumlah_paud_hi':
            $judul .= 'Lembaga Mengisi PAUD HI ';
            break;

          default:
            $judul .= '';
            break;
        }

        if ($kecamatan_kode != 'All') {
          $kecamatan = DB::connection('pgsql_paud')->table('public.kecamatan as kec')
            ->join('public.kelurahan as kel', 'kel.kecamatan_kode', 'kec.kecamatan_kode')
            ->selectRaw("kec.kecamatan_dispenduk,kel.kelurahan_dispenduk")
            ->where('kec.kecamatan_kode', $kecamatan_kode)
            ->when($kelurahan_kode !== 'All', function ($w) use ($kelurahan_kode) {
              $w->where('kel.kelurahan_kode', $kelurahan_kode);
            })->first();
          $judul .= 'Kecamatan ' . $kecamatan->kecamatan_dispenduk ?? '';
          if ($kelurahan_kode !== 'All') {
            $judul .= ', Kelurahan ' . $kecamatan->kelurahan_dispenduk ?? '';
          }
        } else {
          $judul .= 'Kota Surabaya';
        }

        $data = [
          'judul' => $judul,
          'kategori' => $kategori,
          'jenjang' => $jenjang,
          'kecamatan_kode' => $kecamatan_kode,
          'kelurahan_kode' => $kelurahan_kode,
        ];

        return view('dinas.rekap_sp.modal', $data)->render();
        break;

      case 'data_modal':
        // return $request->all();
        $kategori = $request->kategori ?? '';
        $jenjang = $request->jenjang ?? [];
        $kecamatan_kode = $request->kecamatan_kode ?? 'All';
        $kelurahan_kode = $request->kelurahan_kode ?? 'All';
        $jenjang = array_diff($jenjang, ['All']);

        $base = DB::connection('pgsql_paud')->table('public.sekolah as se')
          ->join('public.master_user as u', 'u.username', 'se.npsn')
          ->join('public.kecamatan as kec', 'kec.kecamatan_kode', 'se.kec_id')
          ->join('public.kelurahan as kel', 'kel.kelurahan_kode', 'se.desa')
          ->whereRaw("u.user_enable is true")
          ->whereIn('se.jenjang', $jenjang)
          ->when($kecamatan_kode !== 'All', function ($w) use ($kecamatan_kode) {
            $w->where('kec.kecamatan_kode', $kecamatan_kode);
          })
          ->when($kelurahan_kode !== 'All', function ($w) use ($kelurahan_kode) {
            $w->where('kel.kelurahan_kode', $kelurahan_kode);
          });

        $data_siswa = (clone $base)
          ->join('public.siswa as s', 's.npsn', 'se.npsn')
          ->join('public.wali_murid as wm', DB::raw("CONCAT(s.npsn,s.id_siswa)"), DB::raw("CONCAT(wm.npsn,wm.id_siswa)"))
          ->selectRaw("
            se.npsn,
            COUNT(*) as jumlah_siswa,
            COUNT(DISTINCT(CONCAT(s.npsn,s.rombel))) as jumlah_rombel
            ")
          ->where('s.status_siswa', 'Aktif')
          ->whereRaw("s.alumni is not true")
          ->groupBy('se.npsn');
        $data_pegawai = (clone $base)
          ->join('public.pegawai as s', 's.npsn', 'se.npsn')
          ->selectRaw("se.npsn,COUNT(*) as jumlah_pegawai")
          ->where('s.keterangan', 'Aktif')
          ->whereRaw("s.induk is true")
          ->groupBy('se.npsn');

        $baseSekolahQuery = (clone $base)
          ->selectRaw("
            se.npsn,se.nama,se.alamat,kec.kecamatan_dispenduk,kel.kelurahan_dispenduk,se.status,se.jenjang,se.kuota,
            MAX(ds.jumlah_siswa) as jumlah_siswa,
            MAX(ds.jumlah_rombel) as jumlah_rombel,
            MAX(dp.jumlah_pegawai) as jumlah_pegawai
          ")
          ->leftJoinSub($data_siswa, 'ds', function ($j) {
            return $j->on('se.npsn', '=', 'ds.npsn');
          })
          ->leftJoinSub($data_pegawai, 'dp', function ($j) {
            return $j->on('se.npsn', '=', 'dp.npsn');
          })
          ->groupByRaw("
          se.npsn,se.nama,se.alamat,kec.kecamatan_dispenduk,kel.kelurahan_dispenduk,se.status,se.jenjang,se.kuota
          ");

        switch ($kategori) {
          case 'jumlah_lembaga':
            $data = (clone $baseSekolahQuery);
            break;

          case 'jumlah_tpa':
            $data = (clone $baseSekolahQuery)
              ->where('se.jenjang', 'TPA');
            break;

          case 'jumlah_sps':
            $data = (clone $baseSekolahQuery)
              ->where('se.jenjang', 'SPS');
            break;

          case 'jumlah_kb':
            $data = (clone $baseSekolahQuery)
              ->where('se.jenjang', 'KB');
            break;

          case 'jumlah_tk':
            $data = (clone $baseSekolahQuery)
              ->where('se.jenjang', 'TK');
            break;

          case 'jumlah_ra':
            $data = (clone $baseSekolahQuery)
              ->where('se.jenjang', 'RA');
            break;

          case 'jumlah_negeri':
            $data = (clone $baseSekolahQuery)
              ->where('se.status', 'N');
            break;

          case 'jumlah_swasta':
            $data = (clone $baseSekolahQuery)
              ->where('se.status', '<>', 'N');
            break;

          case 'jumlah_rombel':
            $data = (clone $baseSekolahQuery);
            break;

          case 'jumlah_kuota':
            $data = (clone $baseSekolahQuery);
            break;

          case 'jumlah_ape':
            $data = (clone $baseSekolahQuery)
              ->join('public.pengisian_ape as ape', 'ape.npsn', 'se.npsn');
            break;

          case 'jumlah_bop':
            $penerima_bop = DB::connection('db_simpaud')->table('public.bop_master_view')->selectRaw("npsn")
              ->where('jenis_anggaran', 'BOP')->groupBy('npsn')->get();
            $arr_bop = collect($penerima_bop)->pluck('npsn')->toArray();

            $data = (clone $baseSekolahQuery)->whereIn('se.npsn', $arr_bop);
            break;

          case 'jumlah_bop_kinerja':
            $penerima_bop_kinerja = DB::connection('db_simpaud')->table('public.bop_master_view')->selectRaw("npsn")
              ->where('jenis_anggaran', 'BOP KINERJA')->groupBy('npsn')->get();
            $arr_bop_kinerja = collect($penerima_bop_kinerja)->pluck('npsn')->toArray();

            $data = (clone $baseSekolahQuery)->whereIn('se.npsn', $arr_bop_kinerja);
            break;

          case 'jumlah_bopda':
            $penerima_bopda = DB::connection('db_simpaud')->table('public.bop_master_view')->selectRaw("npsn")
              ->where('jenis_anggaran', 'BOPDA')->groupBy('npsn')->get();
            $arr_bopda = collect($penerima_bopda)->pluck('npsn')->toArray();

            $data = (clone $baseSekolahQuery)->whereIn('se.npsn', $arr_bopda);
            break;

          case 'jumlah_akreditasi_a':
            $data = (clone $baseSekolahQuery)->where('se.akreditasi', 'A');
            break;

          case 'jumlah_akreditasi_b':
            $data = (clone $baseSekolahQuery)->where('se.akreditasi', 'B');
            break;

          case 'jumlah_akreditasi_c':
            $data = (clone $baseSekolahQuery)->where('se.akreditasi', 'C');
            break;

          case 'jumlah_akreditasi_lain':
            $data = (clone $baseSekolahQuery)->whereNotIn('se.akreditasi', ['A', 'B', 'C']);
            break;

          case 'jumlah_paud_hi':
            $data = (clone $baseSekolahQuery)
              ->join('paud_hi.pengajuan_paud_hi as hi', 'hi.npsn', 'se.npsn');
            break;

          case 'jumlah_revit':
            $data = (clone $baseSekolahQuery)
              ->join('history.rehabilitasi_bangunan as rev', 'rev.npsn', 'se.npsn');
            break;

          default:
            $data .= [];
            break;
        }

        return DataTables::queryBuilder($data)
          ->addIndexColumn()
          ->make(true);

        break;

      default:
        return '404 - Halaman tidak ditemukan';
        break;
    }
  }

  public static function anomali_nama(Request $request)
  {
    // return $request->all();
    $kecamatan = $request->kecamatan ?? '';
    $kecamatan_kode = $request->kecamatan_kode ?? '';
    $kelurahan = $request->kelurahan ?? '';
    $kelurahan_kode = $request->kelurahan_kode ?? '';

    $arr_kecamatan = [];
    $arr_kelurahan = [];

    if ($kecamatan != '' && $kecamatan != 'All') {
      $data_kecamatan = DB::table('public.kecamatan')->where('kecamatan_dispenduk', $kecamatan)->first();
      $arr_kecamatan = [$data_kecamatan->kecamatan_dispenduk, $data_kecamatan->dispenduk_baru];
    }

    if ($kelurahan != '' && $kelurahan != 'All') {
      $data_kelurahan = DB::table('public.kelurahan')->where('kelurahan_dispenduk', $kelurahan)->first();
      $arr_kelurahan = [$data_kelurahan->kelurahan_dispenduk, $data_kelurahan->dispenduk_baru];
    }

    return ['arr_kecamatan' => $arr_kecamatan, 'arr_kelurahan' => $arr_kelurahan];
  }
}
