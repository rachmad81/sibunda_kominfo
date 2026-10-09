<?php

namespace App\Http\Controllers\Dinas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth, DB, DataTables;

class RekapPegawaiController extends Controller
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
        return view('dinas.rekap_pegawai.main', $data);
        break;

      case 'get_data':

        $kecamatan = $request->kecamatan ?? '';
        $kecamatan_kode = $request->kecamatan_kode ?? 'All';
        $kelurahan = $request->kelurahan ?? '';
        $kelurahan_kode = $request->kelurahan_kode ?? 'All';
        $pencarian = $request->pencarian ?? '';
        $arr_jenjang = $request->jenjang ?? [];
        $arr_jenjang = array_diff($arr_jenjang, ['All']);

        $jenjang = '\'' . implode('\',\'', $arr_jenjang) . '\'';

        $from = "
          FROM public.sekolah as se
          JOIN public.master_user as u on u.username=se.npsn
          JOIN public.kecamatan as kec on kec.kecamatan_kode=se.kec_id
          JOIN public.kelurahan as kel on kel.kelurahan_kode=se.desa
          JOIN public.pegawai as s on s.npsn=se.npsn
          ";

        $arr_where = [];
        if ($kecamatan != 'All') {
          array_push($arr_where, "kec.kecamatan_kode='$kecamatan_kode'");
        }
        if ($kelurahan != 'All') {
          array_push($arr_where, "kel.kelurahan_kode='$kelurahan_kode'");
        }
        $where = '';
        if (count($arr_where) > 0) {
          $where = ' AND ' . implode(' AND ', $arr_where);
        }

        $rpl = DB::table('cutoff_rekap.rpl_ptk')->select('nik')->get()->pluck('nik')->unique()->values()->toArray();
        if (count($rpl) > 0) {
          $str_rpl = 's.no_ktp in (\'' . implode('\',\'', $rpl) . '\')';
        } else {
          $str_rpl = '1=0';
        }

        // $tanggal = date('Y-m-d', strtotime('-1 days', strtotime(date('Y-m-d'))));
        $tanggal = date('Y-m-d');
        $update_view = 0;

        $cek = DB::connection('pgsql_paud')->select("SELECT table_name
          FROM information_schema.views
          WHERE table_name = 'v_rekap_pegawai';
          ");
        if (!empty($cek)) {
          $data_sekarang = DB::connection('pgsql_paud')->table('history.v_rekap_pegawai')
            ->where('tgl_update', $tanggal)->count();
          if (empty($data_sekarang)) {
            $update_view = 1;
          }
        } else {
          $update_view = 1;
        }

        if ($update_view == 1) {
          DB::connection('pgsql_paud')->select("
            CREATE OR REPLACE VIEW history.v_rekap_pegawai as
            WITH
              koding as (
                SELECT
                kec.kecamatan_dispenduk as kecamatan,
                kec.kecamatan_kode,
                kel.kelurahan_dispenduk as kelurahan,
                kel.kelurahan_kode,
                se.jenjang,
                COUNT(*) as jumlah_koding
                $from
                JOIN history.pelatihan_koding as ko ON ko.nik=s.no_ktp
                WHERE u.user_enable is true AND se.jenjang IN ($jenjang)
                AND s.keterangan='Aktif' AND s.induk is true
                $where
                GROUP BY kec.kecamatan_dispenduk,kel.kelurahan_dispenduk,kec.kecamatan_kode,kel.kelurahan_kode,se.jenjang
              ),
              deep_learning as (
                SELECT
                kec.kecamatan_dispenduk as kecamatan,
                kec.kecamatan_kode,
                kel.kelurahan_dispenduk as kelurahan,
                kel.kelurahan_kode,
                se.jenjang,
                COUNT(*) as jumlah_deep
                $from
                JOIN history.pelatihan_pembelajaran_mendalam as ko ON ko.nik=s.no_ktp
                WHERE u.user_enable is true AND se.jenjang IN ($jenjang)
                AND s.keterangan='Aktif' AND s.induk is true
                $where
                GROUP BY kec.kecamatan_dispenduk,kel.kelurahan_dispenduk,kec.kecamatan_kode,kel.kelurahan_kode,se.jenjang
              ),
              sra as (
                SELECT
                kec.kecamatan_dispenduk as kecamatan,
                kec.kecamatan_kode,
                kel.kelurahan_dispenduk as kelurahan,
                kel.kelurahan_kode,
                se.jenjang,
                COUNT(*) as jumlah_sra
                $from
                JOIN history.pelatihan_sra as sra ON sra.nik=s.no_ktp
                WHERE u.user_enable is true AND se.jenjang IN ($jenjang)
                AND s.keterangan='Aktif' AND s.induk is true
                $where
                GROUP BY kec.kecamatan_dispenduk,kel.kelurahan_dispenduk,kec.kecamatan_kode,kel.kelurahan_kode,se.jenjang
              )
            SELECT
            kec.kecamatan_dispenduk as kecamatan,
            kec.kecamatan_kode,
            kel.kelurahan_dispenduk as kelurahan,
            kel.kelurahan_kode,
            se.jenjang,
            '$tanggal' as tgl_update,
            COUNT(s.peg_id) as jumlah_pegawai,
            COUNT(s.peg_id) FILTER(WHERE(COALESCE(s.status_pegawai,'')='PNS')) as jumlah_pns,
            COUNT(s.peg_id) FILTER(WHERE(COALESCE(s.status_pegawai,'')='GTY')) as jumlah_gty,
            COUNT(s.peg_id) FILTER(WHERE(COALESCE(s.status_pegawai,'') NOT IN ('PNS','GTY'))) as jumlah_lain,
            COUNT(s.peg_id) FILTER(WHERE(COALESCE(s.pendidikan_terakhir,null)=5)) as jumlah_lulusan_sma,
            COUNT(s.peg_id) FILTER(WHERE(COALESCE(s.pendidikan_terakhir,null) IN (6,7,8))) as jumlah_lulusan_diploma,
            COUNT(s.peg_id) FILTER(WHERE(COALESCE(s.pendidikan_terakhir,null) = 9)) as jumlah_lulusan_s1,
            COUNT(s.peg_id) FILTER(WHERE(COALESCE(s.pendidikan_terakhir,null) = 10)) as jumlah_lulusan_s2,
            COUNT(s.peg_id) FILTER(WHERE(COALESCE(s.pendidikan_terakhir,null) NOT IN (5,6,7,8,9,10))) as jumlah_lulusan_lain,
            COUNT(s.peg_id) FILTER(WHERE(COALESCE(s.jk,'') = 'L')) as jumlah_laki,
            COUNT(s.peg_id) FILTER(WHERE(COALESCE(s.jk,'') <> 'L')) as jumlah_perempuan,
            COUNT(s.peg_id) FILTER(WHERE(EXTRACT(YEAR FROM AGE(NOW(),s.tgl_lahir)) < 20)) as jumlah_usia_20_kurang,
            COUNT(s.peg_id) FILTER(WHERE(EXTRACT(YEAR FROM AGE(NOW(),s.tgl_lahir)) BETWEEN 20 AND 30)) as jumlah_usia_20,
            COUNT(s.peg_id) FILTER(WHERE(EXTRACT(YEAR FROM AGE(NOW(),s.tgl_lahir)) BETWEEN 31 AND 40)) as jumlah_usia_30,
            COUNT(s.peg_id) FILTER(WHERE(EXTRACT(YEAR FROM AGE(NOW(),s.tgl_lahir)) BETWEEN 41 AND 50)) as jumlah_usia_40,
            COUNT(s.peg_id) FILTER(WHERE(EXTRACT(YEAR FROM AGE(NOW(),s.tgl_lahir)) > 50)) as jumlah_usia_50_lebih,
            COUNT(s.peg_id) FILTER(WHERE($str_rpl)) as jumlah_rpl,
            COUNT(s.peg_id) FILTER(WHERE(COALESCE(s.sertifikasi_bidang_studi,'')<>'')) as jumlah_sertifikasi,
            COALESCE(MAX(ko.jumlah_koding),0) as jumlah_koding,
            COALESCE(MAX(dl.jumlah_deep),0) as jumlah_deep,
            COALESCE(MAX(sra.jumlah_sra),0) as jumlah_sra
            $from
            LEFT JOIN koding as ko ON ko.kecamatan_kode=kec.kecamatan_kode AND ko.kelurahan_kode=kel.kelurahan_kode
            LEFT JOIN deep_learning as dl ON dl.kecamatan_kode=kec.kecamatan_kode AND dl.kelurahan_kode=kel.kelurahan_kode
            LEFT JOIN sra as sra ON sra.kecamatan_kode=kec.kecamatan_kode AND sra.kelurahan_kode=kel.kelurahan_kode
            WHERE u.user_enable is true AND se.jenjang IN ($jenjang)
            AND s.keterangan='Aktif' AND s.induk is true
            $where
            GROUP BY kec.kecamatan_dispenduk,kel.kelurahan_dispenduk,kec.kecamatan_kode,kel.kelurahan_kode,se.jenjang
          ");
        }

        $data = DB::connection('pgsql_paud')->table('history.v_rekap_pegawai')
          ->selectRaw("
            kecamatan,
            kecamatan_kode,
            kelurahan,
            kelurahan_kode,
            SUM(jumlah_pegawai) as jumlah_pegawai,
            SUM(jumlah_pns) as jumlah_pns,
            SUM(jumlah_gty) as jumlah_gty,
            SUM(jumlah_lain) as jumlah_lain,
            SUM(jumlah_lulusan_sma) as jumlah_lulusan_sma,
            SUM(jumlah_lulusan_diploma) as jumlah_lulusan_diploma,
            SUM(jumlah_lulusan_s1) as jumlah_lulusan_s1,
            SUM(jumlah_lulusan_s2) as jumlah_lulusan_s2,
            SUM(jumlah_lulusan_lain) as jumlah_lulusan_lain,
            SUM(jumlah_laki) as jumlah_laki,
            SUM(jumlah_perempuan) as jumlah_perempuan,
            SUM(jumlah_usia_20_kurang) as jumlah_usia_20_kurang,
            SUM(jumlah_usia_20) as jumlah_usia_20,
            SUM(jumlah_usia_30) as jumlah_usia_30,
            SUM(jumlah_usia_40) as jumlah_usia_40,
            SUM(jumlah_usia_50_lebih) as jumlah_usia_50_lebih,
            SUM(jumlah_rpl) as jumlah_rpl,
            SUM(jumlah_sertifikasi) as jumlah_sertifikasi,
            SUM(jumlah_koding) as jumlah_koding,
            SUM(jumlah_deep) as jumlah_deep,
            SUM(jumlah_sra) as jumlah_sra
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
          return DataTables::queryBuilder((clone $data))
            ->addIndexColumn()
            ->addColumn('jumlah_pegawai', function ($a) {
              return '<a href="javascript:void(0)" onclick="modal(\'' . $a->kecamatan_kode . '\',\'' . $a->kelurahan_kode . '\',\'jumlah_pegawai\')">' . $a->jumlah_pegawai . '</a>';
            })
            ->addColumn('jumlah_pns', function ($a) {
              return '<a href="javascript:void(0)" onclick="modal(\'' . $a->kecamatan_kode . '\',\'' . $a->kelurahan_kode . '\',\'jumlah_pns\')">' . $a->jumlah_pns . '</a>';
            })
            ->addColumn('jumlah_gty', function ($a) {
              return '<a href="javascript:void(0)" onclick="modal(\'' . $a->kecamatan_kode . '\',\'' . $a->kelurahan_kode . '\',\'jumlah_gty\')">' . $a->jumlah_gty . '</a>';
            })
            ->addColumn('jumlah_lain', function ($a) {
              return '<a href="javascript:void(0)" onclick="modal(\'' . $a->kecamatan_kode . '\',\'' . $a->kelurahan_kode . '\',\'jumlah_lain\')">' . $a->jumlah_lain . '</a>';
            })
            ->addColumn('jumlah_lulusan_sma', function ($a) {
              return '<a href="javascript:void(0)" onclick="modal(\'' . $a->kecamatan_kode . '\',\'' . $a->kelurahan_kode . '\',\'jumlah_lulusan_sma\')">' . $a->jumlah_lulusan_sma . '</a>';
            })
            ->addColumn('jumlah_lulusan_diploma', function ($a) {
              return '<a href="javascript:void(0)" onclick="modal(\'' . $a->kecamatan_kode . '\',\'' . $a->kelurahan_kode . '\',\'jumlah_lulusan_diploma\')">' . $a->jumlah_lulusan_diploma . '</a>';
            })
            ->addColumn('jumlah_lulusan_s1', function ($a) {
              return '<a href="javascript:void(0)" onclick="modal(\'' . $a->kecamatan_kode . '\',\'' . $a->kelurahan_kode . '\',\'jumlah_lulusan_s1\')">' . $a->jumlah_lulusan_s1 . '</a>';
            })
            ->addColumn('jumlah_lulusan_s2', function ($a) {
              return '<a href="javascript:void(0)" onclick="modal(\'' . $a->kecamatan_kode . '\',\'' . $a->kelurahan_kode . '\',\'jumlah_lulusan_s2\')">' . $a->jumlah_lulusan_s2 . '</a>';
            })
            ->addColumn('jumlah_lulusan_lain', function ($a) {
              return '<a href="javascript:void(0)" onclick="modal(\'' . $a->kecamatan_kode . '\',\'' . $a->kelurahan_kode . '\',\'jumlah_lulusan_lain\')">' . $a->jumlah_lulusan_lain . '</a>';
            })
            ->addColumn('jumlah_laki', function ($a) {
              return '<a href="javascript:void(0)" onclick="modal(\'' . $a->kecamatan_kode . '\',\'' . $a->kelurahan_kode . '\',\'jumlah_laki\')">' . $a->jumlah_laki . '</a>';
            })
            ->addColumn('jumlah_perempuan', function ($a) {
              return '<a href="javascript:void(0)" onclick="modal(\'' . $a->kecamatan_kode . '\',\'' . $a->kelurahan_kode . '\',\'jumlah_perempuan\')">' . $a->jumlah_perempuan . '</a>';
            })
            ->addColumn('jumlah_usia_20_kurang', function ($a) {
              return '<a href="javascript:void(0)" onclick="modal(\'' . $a->kecamatan_kode . '\',\'' . $a->kelurahan_kode . '\',\'jumlah_usia_20_kurang\')">' . $a->jumlah_usia_20_kurang . '</a>';
            })
            ->addColumn('jumlah_usia_20', function ($a) {
              return '<a href="javascript:void(0)" onclick="modal(\'' . $a->kecamatan_kode . '\',\'' . $a->kelurahan_kode . '\',\'jumlah_usia_20\')">' . $a->jumlah_usia_20 . '</a>';
            })
            ->addColumn('jumlah_usia_30', function ($a) {
              return '<a href="javascript:void(0)" onclick="modal(\'' . $a->kecamatan_kode . '\',\'' . $a->kelurahan_kode . '\',\'jumlah_usia_30\')">' . $a->jumlah_usia_30 . '</a>';
            })
            ->addColumn('jumlah_usia_40', function ($a) {
              return '<a href="javascript:void(0)" onclick="modal(\'' . $a->kecamatan_kode . '\',\'' . $a->kelurahan_kode . '\',\'jumlah_usia_40\')">' . $a->jumlah_usia_40 . '</a>';
            })
            ->addColumn('jumlah_usia_50_lebih', function ($a) {
              return '<a href="javascript:void(0)" onclick="modal(\'' . $a->kecamatan_kode . '\',\'' . $a->kelurahan_kode . '\',\'jumlah_usia_50_lebih\')">' . $a->jumlah_usia_50_lebih . '</a>';
            })
            ->addColumn('jumlah_rpl', function ($a) {
              return '<a href="javascript:void(0)" onclick="modal(\'' . $a->kecamatan_kode . '\',\'' . $a->kelurahan_kode . '\',\'jumlah_rpl\')">' . $a->jumlah_rpl . '</a>';
            })
            ->addColumn('jumlah_koding', function ($a) {
              return '<a href="javascript:void(0)" onclick="modal(\'' . $a->kecamatan_kode . '\',\'' . $a->kelurahan_kode . '\',\'jumlah_koding\')">' . $a->jumlah_koding . '</a>';
            })
            ->addColumn('jumlah_deep', function ($a) {
              return '<a href="javascript:void(0)" onclick="modal(\'' . $a->kecamatan_kode . '\',\'' . $a->kelurahan_kode . '\',\'jumlah_deep\')">' . $a->jumlah_deep . '</a>';
            })
            ->addColumn('jumlah_sra', function ($a) {
              return '<a href="javascript:void(0)" onclick="modal(\'' . $a->kecamatan_kode . '\',\'' . $a->kelurahan_kode . '\',\'jumlah_sra\')">' . $a->jumlah_sra . '</a>';
            })
            ->addColumn('jumlah_sertifikasi', function ($a) {
              return '<a href="javascript:void(0)" onclick="modal(\'' . $a->kecamatan_kode . '\',\'' . $a->kelurahan_kode . '\',\'jumlah_sertifikasi\')">' . $a->jumlah_sertifikasi . '</a>';
            })
            ->rawColumns([
              'jumlah_pegawai',
              'jumlah_pns',
              'jumlah_gty',
              'jumlah_lain',
              'jumlah_lulusan_sma',
              'jumlah_lulusan_diploma',
              'jumlah_lulusan_s1',
              'jumlah_lulusan_s2',
              'jumlah_lulusan_lain',
              'jumlah_laki',
              'jumlah_perempuan',
              'jumlah_usia_20_kurang',
              'jumlah_usia_20',
              'jumlah_usia_30',
              'jumlah_usia_40',
              'jumlah_usia_50_lebih',
              'jumlah_rpl',
              'jumlah_koding',
              'jumlah_deep',
              'jumlah_sra',
              'jumlah_sertifikasi',
            ])
            ->make(true);
        } else {
          $baris = collect((clone $data)->get());
          $total = [
            'jumlah_pegawai' => $baris->sum('jumlah_pegawai'),
            'jumlah_pns' => $baris->sum('jumlah_pns'),
            'jumlah_gty' => $baris->sum('jumlah_gty'),
            'jumlah_lain' => $baris->sum('jumlah_lain'),
            'jumlah_lulusan_sma' => $baris->sum('jumlah_lulusan_sma'),
            'jumlah_lulusan_diploma' => $baris->sum('jumlah_lulusan_diploma'),
            'jumlah_lulusan_s1' => $baris->sum('jumlah_lulusan_s1'),
            'jumlah_lulusan_s2' => $baris->sum('jumlah_lulusan_s2'),
            'jumlah_lulusan_lain' => $baris->sum('jumlah_lulusan_lain'),
            'jumlah_laki' => $baris->sum('jumlah_laki'),
            'jumlah_perempuan' => $baris->sum('jumlah_perempuan'),
            'jumlah_usia_20_kurang' => $baris->sum('jumlah_usia_20_kurang'),
            'jumlah_usia_20' => $baris->sum('jumlah_usia_20'),
            'jumlah_usia_30' => $baris->sum('jumlah_usia_30'),
            'jumlah_usia_40' => $baris->sum('jumlah_usia_40'),
            'jumlah_usia_50_lebih' => $baris->sum('jumlah_usia_50_lebih'),
            'jumlah_rpl' => $baris->sum('jumlah_rpl'),
            'jumlah_koding' => $baris->sum('jumlah_koding'),
            'jumlah_deep' => $baris->sum('jumlah_deep'),
            'jumlah_sra' => $baris->sum('jumlah_sra'),
            'jumlah_sertifikasi' => $baris->sum('jumlah_sertifikasi'),
          ];

          $data = [
            'total' => $total,
          ];

          return view('dinas.rekap_pegawai.total', $data)->render();
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
          case 'jumlah_pegawai':
            $judul .= 'Jumlah Pegawai ';
            break;

          case 'jumlah_pns':
            $judul .= 'Jumlah Pegawai PNS ';
            break;

          case 'jumlah_gty':
            $judul .= 'Jumlah Pegawai GTY ';
            break;

          case 'jumlah_lain':
            $judul .= 'Jumlah Pegawai Status Lain ';
            break;

          case 'jumlah_lulusan_sma':
            $judul .= 'Jumlah Pegawai Lulusan SMA ';
            break;

          case 'jumlah_lulusan_diploma':
            $judul .= 'Jumlah Pegawai Lulusan Diploma ';
            break;

          case 'jumlah_lulusan_s1':
            $judul .= 'Jumlah Pegawai Lulusan S1';
            break;

          case 'jumlah_lulusan_s2':
            $judul .= 'Jumlah Pegawai Lulusan S2 ';
            break;

          case 'jumlah_lulusan_lain':
            $judul .= 'Jumlah Pegawai Lulusan Lain ';
            break;

          case 'jumlah_laki':
            $judul .= 'Jumlah Pegawai Laki-laki ';
            break;

          case 'jumlah_perempuan':
            $judul .= 'Jumlah Pegawai Perempuan ';
            break;

          case 'jumlah_usia_20_kurang':
            $judul .= 'Jumlah Pegawai Usia < 20 ';
            break;

          case 'jumlah_usia_20':
            $judul .= 'Jumlah Pegawai Usia 20 - 30 ';
            break;

          case 'jumlah_usia_30':
            $judul .= 'Jumlah Pegawai Usia 31 - 40 ';
            break;

          case 'jumlah_usia_40':
            $judul .= 'Jumlah Pegawai Usia 41 - 50 ';
            break;

          case 'jumlah_usia_50_lebih':
            $judul .= 'Jumlah Pegawai Usia > 50 ';
            break;

          case 'jumlah_rpl':
            $judul .= 'Jumlah Pegawai Penerima RPL S1 ';
            break;

          case 'jumlah_koding':
            $judul .= 'Jumlah Pegawai Ikut Pelatihan Koding ';
            break;

          case 'jumlah_deep':
            $judul .= 'Jumlah Pegawai Ikut Pelatihan Pembelajaran Mendalam ';
            break;

          case 'jumlah_sra':
            $judul .= 'Jumlah Pegawai Ikut Pelatihan Sekolah Ramah Anak ';
            break;

          case 'jumlah_sertifikasi':
            $judul .= 'Jumlah Pegawai Sertifikasi ';
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

        return view('dinas.rekap_pegawai.modal', $data)->render();
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
          ->join('public.pegawai as s', 's.npsn', 'se.npsn')
          ->leftJoin('public.pendidikan as pe', 'pe.jenjang_pendidikan_id', 's.pendidikan_terakhir')
          ->leftJoin('public.kecamatan as kec_s', 'kec_s.kecamatan_kode', 's.kec_id')
          ->leftJoin('public.kelurahan as kel_s', 'kel_s.kelurahan_kode', 's.kelurahan')
          ->selectRaw("
            s.npsn,se.nama as nama_lembaga,se.jenjang,se.status,
            se.alamat,kec.kecamatan_dispenduk,kel.kelurahan_dispenduk,
            s.nama,s.no_ktp,EXTRACT(YEAR FROM AGE(NOW(),s.tgl_lahir)) as usia,
            s.alamat as alamat_pegawai,s.tgl_lahir,
            pe.nama as nama_pendidikan,
            s.status_pegawai,s.jk,
            COALESCE(NULLIF(kec_s.kecamatan_dispenduk,''),s.kec_id) as kecamatan_pegawai,
            COALESCE(NULLIF(kel_s.kelurahan_dispenduk,''),s.kelurahan) as kelurahan_pegawai
            ")
          ->where('s.keterangan', 'Aktif')
          ->whereRaw("s.induk is true")
          ->whereRaw("u.user_enable is true")
          ->whereIn('se.jenjang', $jenjang)
          ->when($kecamatan_kode !== 'All', function ($w) use ($kecamatan_kode) {
            $w->where('kec.kecamatan_kode', $kecamatan_kode);
          })
          ->when($kelurahan_kode !== 'All', function ($w) use ($kelurahan_kode) {
            $w->where('kel.kelurahan_kode', $kelurahan_kode);
          });

        switch ($kategori) {
          case 'jumlah_pegawai':
            $data = (clone $base);
            break;

          case 'jumlah_pns':
            $data = (clone $base)
              ->where('s.status_pegawai', 'PNS');
            break;

          case 'jumlah_gty':
            $data = (clone $base)
              ->where('s.status_pegawai', 'GTY');
            break;

          case 'jumlah_lain':
            $data = (clone $base)
              ->whereNotIn('s.status_pegawai', ['PNS', 'GTY']);
            break;

          case 'jumlah_lulusan_sma':
            $data = (clone $base)
              ->where('s.pendidikan_terakhir', 5);
            break;

          case 'jumlah_lulusan_diploma':
            $data = (clone $base)
              ->whereIn('s.pendidikan_terakhir', [6, 7, 8]);
            break;

          case 'jumlah_lulusan_s1':
            $data = (clone $base)
              ->where('s.pendidikan_terakhir', 9);
            break;

          case 'jumlah_lulusan_s2':
            $data = (clone $base)
              ->where('s.pendidikan_terakhir', 10);
            break;

          case 'jumlah_lulusan_lain':
            $data = (clone $base)
              ->whereNotIn('s.pendidikan_terakhir', [5, 6, 7, 8, 9, 10]);
            break;

          case 'jumlah_laki':
            $data = (clone $base)
              ->where('s.jk', 'L');
            break;

          case 'jumlah_perempuan':
            $data = (clone $base)
              ->where('s.jk', '<>', 'L');
            break;

          case 'jumlah_usia_20_kurang':
            $data = (clone $base)
              ->where(DB::raw("EXTRACT(YEAR FROM AGE(NOW(),s.tgl_lahir))"), '<', 20);
            break;

          case 'jumlah_usia_20':
            $data = (clone $base)
              ->whereBetween(DB::raw("EXTRACT(YEAR FROM AGE(NOW(),s.tgl_lahir))"), [20, 30]);
            break;

          case 'jumlah_usia_30':
            $data = (clone $base)
              ->whereBetween(DB::raw("EXTRACT(YEAR FROM AGE(NOW(),s.tgl_lahir))"), [31, 40]);
            break;

          case 'jumlah_usia_40':
            $data = (clone $base)
              ->whereBetween(DB::raw("EXTRACT(YEAR FROM AGE(NOW(),s.tgl_lahir))"), [41, 50]);
            break;

          case 'jumlah_usia_50_lebih':
            $data = (clone $base)
              ->where(DB::raw("EXTRACT(YEAR FROM AGE(NOW(),s.tgl_lahir))"), '>', 50);
            break;

          case 'jumlah_rpl':
            $rpl = DB::table('cutoff_rekap.rpl_ptk')->select('nik')->get()->pluck('nik')->unique()->values()->toArray();
            if (count($rpl) > 0) {
              $str_rpl = 's.no_ktp in (\'' . implode('\',\'', $rpl) . '\')';
            } else {
              $str_rpl = '1=0';
            }

            $data = (clone $base)
              ->whereRaw($str_rpl);
            break;

          case 'jumlah_koding':
            $data = (clone $base)
              ->join('history.pelatihan_koding as de', 'de.nik', 's.no_ktp');
            break;

          case 'jumlah_deep':
            $data = (clone $base)
              ->join('history.pelatihan_pembelajaran_mendalam as de', 'de.nik', 's.no_ktp');
            break;

          case 'jumlah_sra':
            $data = (clone $base)
              ->join('history.pelatihan_sra as sra', 'sra.nik', 's.no_ktp');
            break;

          case 'jumlah_sertifikasi':
            $data = (clone $base)
              ->where(DB::raw("COALESCE(s.sertifikasi_bidang_studi,'')"), '<>', '');
            break;

          default:
            $data = [];
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
