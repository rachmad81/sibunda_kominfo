<?php

namespace App\Http\Controllers\Dinas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth, DB, DataTables;
use Illuminate\Support\Facades\Schema;

class RekapSiswaController extends Controller
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
        return view('dinas.rekap_siswa.main', $data);
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

        $select = "
          kec.kecamatan_dispenduk as kecamatan,
          kec.kecamatan_kode,
          kel.kelurahan_dispenduk as kelurahan,
          kel.kelurahan_kode,
          se.jenjang,
        ";

        $from = "
          FROM public.sekolah as se
          JOIN public.master_user as u on u.username=se.npsn
          JOIN public.kecamatan as kec on kec.kecamatan_kode=se.kec_id
          JOIN public.kelurahan as kel on kel.kelurahan_kode=se.desa
          JOIN public.siswa as s on s.npsn=se.npsn
          JOIN public.wali_murid as wm on wm.id_siswa=s.id_siswa AND wm.npsn=s.npsn
          ";

        $where = "u.user_enable is true AND se.jenjang IN ($jenjang) AND s.status_siswa='Aktif' AND s.alumni is not true";

        $groupby = "
          kec.kecamatan_dispenduk,kel.kelurahan_dispenduk,kec.kecamatan_kode,kel.kelurahan_kode,se.jenjang
          ";

        $siswa_talenta = DB::table('public.talenta_seni_jadwal as j')
          ->join('public.talenta_seni_peserta as p', 'j.id', 'p.jadwal_id')
          ->selectRaw("p.siswa_id")
          ->where('j.status_jadwal', 'Setujui')
          ->whereIn('j.jenjang', $arr_jenjang)
          ->whereBetween('j.tanggal', [date('Y') . '-01-01', date('Y') . '-12-31'])
          ->groupBy('p.siswa_id')
          ->get();
        $arr_siswa_id = $siswa_talenta->pluck('siswa_id')->unique()->values()->toArray();
        $str_siswa_talenta = '\'' . implode('\',\'', $arr_siswa_id) . '\'';
        $arr_siswa_id_genmas = [];

        try {
          DB::connection('db_genmas')->getPdo();
          $genmas = DB::connection('db_genmas')->table('data_pendaftar')
            ->selectRaw("nik")
            ->where('valid_online', 'ya')
            ->where('status_penerima', '1')
            ->groupBy('nik')
            ->get();
          $arr_siswa_id_genmas = $genmas->pluck('nik')->unique()->values()->toArray();
        } catch (\Throwable $th) {
        }
        $str_genmas = '\'' . implode('\',\'', $arr_siswa_id_genmas) . '\'';

        // $tanggal = date('Y-m-d', strtotime('-1 days', strtotime(date('Y-m-d'))));
        $tanggal = date('Y-m-d');
        $update_view = 0;

        $cek = DB::connection('pgsql_paud')->select("SELECT table_name
          FROM information_schema.views
          WHERE table_name = 'v_rekap_siswa';
          ");
        if (!empty($cek)) {
          $data_sekarang = DB::connection('pgsql_paud')->table('history.v_rekap_siswa')
            ->where('tgl_update', $tanggal)->count();
          if (empty($data_sekarang)) {
            $update_view = 1;
          }
        } else {
          $update_view = 1;
        }

        if ($update_view == 1) {
          DB::connection('pgsql_paud')->select("
            CREATE OR REPLACE VIEW history.v_rekap_siswa as
            WITH
              talenta as (
                SELECT
                $select
                COUNT(*) as jumlah_talenta
                $from
                WHERE $where AND s.siswa_id IN ($str_siswa_talenta)
                GROUP BY $groupby
              ),
              beasiswa as (
                SELECT
                $select
                COUNT(DISTINCT(s.siswa_id)) as jumlah_beasiswa
                $from
                JOIN public.detail_konfirmasi_beasiswa as b ON b.siswa_id=s.siswa_id
                JOIN public.konfirmasi_beasiswa as kb ON kb.id=b.konfirmasi_beasiswa_id
                WHERE $where AND kb.status_pengajuan='Disetujui'
                GROUP BY $groupby
              ),
              manasik as (
                SELECT
                $select
                COUNT(DISTINCT(s.siswa_id)) as jumlah_manasik
                $from
                JOIN kegiatan_pendukung.manasik_haji_peserta as b ON b.siswa_id=s.siswa_id
                JOIN kegiatan_pendukung.manasik_haji_jadwal as kb ON kb.id=b.jadwal_id
                WHERE $where AND kb.status_jadwal='Setujui'
                GROUP BY $groupby
              ),
              kaih_rumah as (
                SELECT
                $select
                COUNT(DISTINCT(s.siswa_id)) as jumlah_kaih_rumah
                $from
                JOIN history.kebiasaan_anak_rumah as b ON b.siswa_id=s.siswa_id
                WHERE $where
                GROUP BY $groupby
              ),
              kaih_sekolah as (
                SELECT
                $select
                COUNT(*) as jumlah_kaih_sekolah
                $from
                JOIN history.kebiasaan_anak_sekolah as b ON b.siswa_id=s.siswa_id
                WHERE $where
                GROUP BY $groupby
              ),
              genmas as (
                SELECT
                $select
                COUNT(DISTINCT(s.nik)) as jumlah_genmas
                $from
                WHERE $where AND s.nik IN ($str_genmas)
                GROUP BY $groupby
              ),
              mbg as (
                SELECT
                $select
                COUNT(*) as jumlah_mbg
                $from
                JOIN history.mbg_lembaga as mbg ON mbg.npsn=se.npsn
                WHERE $where
                GROUP BY $groupby
              ),
              edu as (
                WITH data_peserta AS (
                    SELECT
                        kec.kecamatan_dispenduk AS kecamatan,
                        kec.kecamatan_kode,
                        kel.kelurahan_dispenduk AS kelurahan,
                        kel.kelurahan_kode,
                        se.jenjang,
                        j.npsn,
                        -- Ekstrak ID siswa (elemen pertama sebelum koma) dari string/JSON peserta
                        CAST(
                            (regexp_match(
                                jsonb_array_elements_text(j.peserta::jsonb),
                                '^\s*\[?\s*([0-9]+)'
                            ))[1] AS BIGINT
                        ) AS id_siswa
                    FROM kegiatan_pendukung.edu_city_tour_jadwals j
                    JOIN public.sekolah se ON se.npsn = j.npsn
                    JOIN public.kecamatan kec ON kec.kecamatan_kode = se.kec_id
                    JOIN public.kelurahan kel ON kel.kelurahan_kode = se.desa
                    WHERE j.status_jadwal = 'Setujui'
                )
                SELECT
                    dp.kecamatan,
                    dp.kecamatan_kode,
                    dp.kelurahan,
                    dp.kelurahan_kode,
                    dp.jenjang,
                    -- Hitung total siswa aktif yang berstatus unik per kelompok
                    COUNT(DISTINCT dp.id_siswa) AS jumlah_edu
                FROM data_peserta dp
                JOIN public.siswa s ON s.id_siswa = dp.id_siswa AND dp.npsn=s.npsn
                JOIN public.wali_murid wm ON wm.id_siswa = s.id_siswa AND wm.npsn=s.npsn
                WHERE s.status_siswa = 'Aktif'
                  AND (s.alumni IS FALSE OR s.alumni IS NULL)
                GROUP BY
                    dp.kecamatan,
                    dp.kecamatan_kode,
                    dp.kelurahan,
                    dp.kelurahan_kode,
                    dp.jenjang
              )
            SELECT
            $select
            '$tanggal' as tgl_update,
            COUNT(*) as jumlah_siswa,
            COUNT(*) FILTER(WHERE (COALESCE(LOWER(wm.kab_id),'') like '%surabaya%')) as jumlah_kk_surabaya,
            COUNT(*) FILTER(WHERE (COALESCE(LOWER(wm.kab_id),'') not like '%surabaya%')) as jumlah_kk_non_surabaya,
            COUNT(*) FILTER(WHERE (EXTRACT(YEAR FROM AGE(NOW(),s.tgl_lahir))='0')) as jumlah_usia_0,
            COUNT(*) FILTER(WHERE (EXTRACT(YEAR FROM AGE(NOW(),s.tgl_lahir))='1')) as jumlah_usia_1,
            COUNT(*) FILTER(WHERE (EXTRACT(YEAR FROM AGE(NOW(),s.tgl_lahir))='2')) as jumlah_usia_2,
            COUNT(*) FILTER(WHERE (EXTRACT(YEAR FROM AGE(NOW(),s.tgl_lahir))='3')) as jumlah_usia_3,
            COUNT(*) FILTER(WHERE (EXTRACT(YEAR FROM AGE(NOW(),s.tgl_lahir))='4')) as jumlah_usia_4,
            COUNT(*) FILTER(WHERE (EXTRACT(YEAR FROM AGE(NOW(),s.tgl_lahir))='5')) as jumlah_usia_5,
            COUNT(*) FILTER(WHERE (EXTRACT(YEAR FROM AGE(NOW(),s.tgl_lahir))='6')) as jumlah_usia_6,
            COUNT(*) FILTER(WHERE (EXTRACT(YEAR FROM AGE(NOW(),s.tgl_lahir)) not in ('0','1','2','3','4','5','6'))) as jumlah_usia_lain,
            COUNT(*) FILTER(WHERE (s.verified_gakin is true)) as jumlah_pramis,
            COUNT(*) FILTER(WHERE (s.verified_gakin2 is true)) as jumlah_gamis,
            COUNT(*) FILTER(WHERE (s.verified_siswa is true)) as jumlah_kk,
            COALESCE(MAX(bea.jumlah_beasiswa),0) as jumlah_beasiswa,
            COALESCE(MAX(rumah.jumlah_kaih_rumah),0) as jumlah_kaih_rumah,
            COALESCE(MAX(sekolah.jumlah_kaih_sekolah),0) as jumlah_kaih_sekolah,
            COALESCE(MAX(manasik.jumlah_manasik),0) as jumlah_manasik,
            COALESCE(MAX(talenta.jumlah_talenta),0) as jumlah_talenta,
            COALESCE(MAX(genmas.jumlah_genmas),0) as jumlah_genmas,
            COALESCE(MAX(mbg.jumlah_mbg),0) as jumlah_mbg,
            COALESCE(MAX(edu.jumlah_edu),0) as jumlah_edu
            $from
            LEFT JOIN talenta as talenta ON talenta.kecamatan=kec.kecamatan_dispenduk AND talenta.kelurahan=kel.kelurahan_dispenduk AND talenta.jenjang=se.jenjang
            LEFT JOIN beasiswa as bea ON bea.kecamatan=kec.kecamatan_dispenduk AND bea.kelurahan=kel.kelurahan_dispenduk AND bea.jenjang=se.jenjang
            LEFT JOIN manasik as manasik ON manasik.kecamatan=kec.kecamatan_dispenduk AND manasik.kelurahan=kel.kelurahan_dispenduk AND manasik.jenjang=se.jenjang
            LEFT JOIN kaih_rumah as rumah ON rumah.kecamatan=kec.kecamatan_dispenduk AND rumah.kelurahan=kel.kelurahan_dispenduk AND rumah.jenjang=se.jenjang
            LEFT JOIN kaih_sekolah as sekolah ON sekolah.kecamatan=kec.kecamatan_dispenduk AND sekolah.kelurahan=kel.kelurahan_dispenduk AND sekolah.jenjang=se.jenjang
            LEFT JOIN genmas as genmas ON genmas.kecamatan=kec.kecamatan_dispenduk AND genmas.kelurahan=kel.kelurahan_dispenduk AND genmas.jenjang=se.jenjang
            LEFT JOIN mbg as mbg ON mbg.kecamatan=kec.kecamatan_dispenduk AND mbg.kelurahan=kel.kelurahan_dispenduk AND mbg.jenjang=se.jenjang
            LEFT JOIN edu as edu ON edu.kecamatan=kec.kecamatan_dispenduk AND edu.kelurahan=kel.kelurahan_dispenduk AND edu.jenjang=se.jenjang
            WHERE $where
            GROUP BY $groupby
          ");
        }

        $data = DB::connection('pgsql_paud')->table('history.v_rekap_siswa')
          ->selectRaw("
            kecamatan,
            kecamatan_kode,
            kelurahan,
            kelurahan_kode,
            SUM(jumlah_siswa) as jumlah_siswa,
            SUM(jumlah_kk_surabaya) as jumlah_kk_surabaya,
            SUM(jumlah_kk_non_surabaya) as jumlah_kk_non_surabaya,
            SUM(jumlah_usia_0) as jumlah_usia_0,
            SUM(jumlah_usia_1) as jumlah_usia_1,
            SUM(jumlah_usia_2) as jumlah_usia_2,
            SUM(jumlah_usia_3) as jumlah_usia_3,
            SUM(jumlah_usia_4) as jumlah_usia_4,
            SUM(jumlah_usia_5) as jumlah_usia_5,
            SUM(jumlah_usia_6) as jumlah_usia_6,
            SUM(jumlah_usia_lain) as jumlah_usia_lain,
            SUM(jumlah_pramis) as jumlah_pramis,
            SUM(jumlah_gamis) as jumlah_gamis,
            SUM(jumlah_kk) as jumlah_kk,
            SUM(jumlah_beasiswa) as jumlah_beasiswa,
            SUM(jumlah_kaih_rumah) as jumlah_kaih_rumah,
            SUM(jumlah_kaih_sekolah) as jumlah_kaih_sekolah,
            SUM(jumlah_manasik) as jumlah_manasik,
            SUM(jumlah_talenta) as jumlah_talenta,
            SUM(jumlah_genmas) as jumlah_genmas,
            SUM(jumlah_mbg) as jumlah_mbg,
            SUM(jumlah_edu) as jumlah_edu
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
          // return DataTables::queryBuilder($data)
          return DataTables::queryBuilder((clone $data))
            ->addIndexColumn()
            ->addColumn('jumlah_siswa', function ($a) {
              return '<a href="javascript:void(0)" onclick="modal(\'' . $a->kecamatan_kode . '\',\'' . $a->kelurahan_kode . '\',\'jumlah_siswa\')">' . $a->jumlah_siswa . '</a>';
            })
            ->addColumn('jumlah_kk_surabaya', function ($a) {
              return '<a href="javascript:void(0)" onclick="modal(\'' . $a->kecamatan_kode . '\',\'' . $a->kelurahan_kode . '\',\'jumlah_kk_surabaya\')">' . $a->jumlah_kk_surabaya . '</a>';
            })
            ->addColumn('jumlah_kk_non_surabaya', function ($a) {
              return '<a href="javascript:void(0)" onclick="modal(\'' . $a->kecamatan_kode . '\',\'' . $a->kelurahan_kode . '\',\'jumlah_kk_non_surabaya\')">' . $a->jumlah_kk_non_surabaya . '</a>';
            })
            ->addColumn('jumlah_usia_0', function ($a) {
              return '<a href="javascript:void(0)" onclick="modal(\'' . $a->kecamatan_kode . '\',\'' . $a->kelurahan_kode . '\',\'jumlah_usia_0\')">' . $a->jumlah_usia_0 . '</a>';
            })
            ->addColumn('jumlah_usia_1', function ($a) {
              return '<a href="javascript:void(0)" onclick="modal(\'' . $a->kecamatan_kode . '\',\'' . $a->kelurahan_kode . '\',\'jumlah_usia_1\')">' . $a->jumlah_usia_1 . '</a>';
            })
            ->addColumn('jumlah_usia_2', function ($a) {
              return '<a href="javascript:void(0)" onclick="modal(\'' . $a->kecamatan_kode . '\',\'' . $a->kelurahan_kode . '\',\'jumlah_usia_2\')">' . $a->jumlah_usia_2 . '</a>';
            })
            ->addColumn('jumlah_usia_3', function ($a) {
              return '<a href="javascript:void(0)" onclick="modal(\'' . $a->kecamatan_kode . '\',\'' . $a->kelurahan_kode . '\',\'jumlah_usia_3\')">' . $a->jumlah_usia_3 . '</a>';
            })
            ->addColumn('jumlah_usia_4', function ($a) {
              return '<a href="javascript:void(0)" onclick="modal(\'' . $a->kecamatan_kode . '\',\'' . $a->kelurahan_kode . '\',\'jumlah_usia_4\')">' . $a->jumlah_usia_4 . '</a>';
            })
            ->addColumn('jumlah_usia_5', function ($a) {
              return '<a href="javascript:void(0)" onclick="modal(\'' . $a->kecamatan_kode . '\',\'' . $a->kelurahan_kode . '\',\'jumlah_usia_5\')">' . $a->jumlah_usia_5 . '</a>';
            })
            ->addColumn('jumlah_usia_6', function ($a) {
              return '<a href="javascript:void(0)" onclick="modal(\'' . $a->kecamatan_kode . '\',\'' . $a->kelurahan_kode . '\',\'jumlah_usia_6\')">' . $a->jumlah_usia_6 . '</a>';
            })
            ->addColumn('jumlah_usia_lain', function ($a) {
              return '<a href="javascript:void(0)" onclick="modal(\'' . $a->kecamatan_kode . '\',\'' . $a->kelurahan_kode . '\',\'jumlah_usia_lain\')">' . $a->jumlah_usia_lain . '</a>';
            })
            ->addColumn('jumlah_pramis', function ($a) {
              return '<a href="javascript:void(0)" onclick="modal(\'' . $a->kecamatan_kode . '\',\'' . $a->kelurahan_kode . '\',\'jumlah_pramis\')">' . $a->jumlah_pramis . '</a>';
            })
            ->addColumn('jumlah_gamis', function ($a) {
              return '<a href="javascript:void(0)" onclick="modal(\'' . $a->kecamatan_kode . '\',\'' . $a->kelurahan_kode . '\',\'jumlah_gamis\')">' . $a->jumlah_gamis . '</a>';
            })
            ->addColumn('jumlah_beasiswa', function ($a) {
              return '<a href="javascript:void(0)" onclick="modal(\'' . $a->kecamatan_kode . '\',\'' . $a->kelurahan_kode . '\',\'jumlah_beasiswa\')">' . $a->jumlah_beasiswa . '</a>';
            })
            ->addColumn('jumlah_kaih_rumah', function ($a) {
              return '<a href="javascript:void(0)" onclick="modal(\'' . $a->kecamatan_kode . '\',\'' . $a->kelurahan_kode . '\',\'jumlah_kaih_rumah\')">' . $a->jumlah_kaih_rumah . '</a>';
            })
            ->addColumn('jumlah_kaih_sekolah', function ($a) {
              return '<a href="javascript:void(0)" onclick="modal(\'' . $a->kecamatan_kode . '\',\'' . $a->kelurahan_kode . '\',\'jumlah_kaih_sekolah\')">' . $a->jumlah_kaih_sekolah . '</a>';
            })
            ->addColumn('jumlah_manasik', function ($a) {
              return '<a href="javascript:void(0)" onclick="modal(\'' . $a->kecamatan_kode . '\',\'' . $a->kelurahan_kode . '\',\'jumlah_manasik\')">' . $a->jumlah_manasik . '</a>';
            })
            ->addColumn('jumlah_talenta', function ($a) {
              return '<a href="javascript:void(0)" onclick="modal(\'' . $a->kecamatan_kode . '\',\'' . $a->kelurahan_kode . '\',\'jumlah_talenta\')">' . $a->jumlah_talenta . '</a>';
            })
            ->addColumn('jumlah_kk', function ($a) {
              return '<a href="javascript:void(0)" onclick="modal(\'' . $a->kecamatan_kode . '\',\'' . $a->kelurahan_kode . '\',\'jumlah_kk\')">' . $a->jumlah_kk . '</a>';
            })
            ->addColumn('jumlah_genmas', function ($a) {
              return '<a href="javascript:void(0)" onclick="modal(\'' . $a->kecamatan_kode . '\',\'' . $a->kelurahan_kode . '\',\'jumlah_genmas\')">' . $a->jumlah_genmas . '</a>';
            })
            ->addColumn('jumlah_mbg', function ($a) {
              return '<a href="javascript:void(0)" onclick="modal(\'' . $a->kecamatan_kode . '\',\'' . $a->kelurahan_kode . '\',\'jumlah_mbg\')">' . $a->jumlah_mbg . '</a>';
            })
            ->addColumn('jumlah_edu', function ($a) {
              return '<a href="javascript:void(0)" onclick="modal(\'' . $a->kecamatan_kode . '\',\'' . $a->kelurahan_kode . '\',\'jumlah_edu\')">' . $a->jumlah_edu . '</a>';
            })
            ->rawColumns([
              'jumlah_siswa',
              'jumlah_kk_surabaya',
              'jumlah_kk_non_surabaya',
              'jumlah_usia_0',
              'jumlah_usia_1',
              'jumlah_usia_2',
              'jumlah_usia_3',
              'jumlah_usia_4',
              'jumlah_usia_5',
              'jumlah_usia_6',
              'jumlah_usia_lain',
              'jumlah_pramis',
              'jumlah_gamis',
              'jumlah_beasiswa',
              'jumlah_kaih_rumah',
              'jumlah_kaih_sekolah',
              'jumlah_manasik',
              'jumlah_talenta',
              'jumlah_genmas',
              'jumlah_kk',
              'jumlah_mbg',
              'jumlah_edu',
            ])
            ->make(true);
        } else {
          $baris = collect((clone $data)->get());
          $total = [
            'jumlah_siswa' => $baris->sum('jumlah_siswa'),
            'jumlah_kk_surabaya' => $baris->sum('jumlah_kk_surabaya'),
            'jumlah_kk_non_surabaya' => $baris->sum('jumlah_kk_non_surabaya'),
            'jumlah_usia_0' => $baris->sum('jumlah_usia_0'),
            'jumlah_usia_1' => $baris->sum('jumlah_usia_1'),
            'jumlah_usia_2' => $baris->sum('jumlah_usia_2'),
            'jumlah_usia_3' => $baris->sum('jumlah_usia_3'),
            'jumlah_usia_4' => $baris->sum('jumlah_usia_4'),
            'jumlah_usia_5' => $baris->sum('jumlah_usia_5'),
            'jumlah_usia_6' => $baris->sum('jumlah_usia_6'),
            'jumlah_usia_lain' => $baris->sum('jumlah_usia_lain'),
            'jumlah_pramis' => $baris->sum('jumlah_pramis'),
            'jumlah_gamis' => $baris->sum('jumlah_gamis'),
            'jumlah_beasiswa' => $baris->sum('jumlah_beasiswa'),
            'jumlah_manasik' => $baris->sum('jumlah_manasik'),
            'jumlah_talenta' => $baris->sum('jumlah_talenta'),
            'jumlah_genmas' => $baris->sum('jumlah_genmas'),
            'jumlah_kaih_rumah' => $baris->sum('jumlah_kaih_rumah'),
            'jumlah_kaih_sekolah' => $baris->sum('jumlah_kaih_sekolah'),
            'jumlah_kk' => $baris->sum('jumlah_kk'),
            'jumlah_mbg' => $baris->sum('jumlah_mbg'),
            'jumlah_edu' => $baris->sum('jumlah_edu'),
          ];

          $data = [
            'total' => $total,
          ];

          return view('dinas.rekap_siswa.total', $data)->render();
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
          case 'jumlah_siswa':
            $judul .= 'Siswa ';
            break;

          case 'jumlah_kk_surabaya':
            $judul .= 'Siswa KK Surabaya ';
            break;

          case 'jumlah_kk_non_surabaya':
            $judul .= 'Siswa KK Non Surabaya ';
            break;

          case 'jumlah_usia_0':
            $judul .= 'Siswa Usia 0 tahun ';
            break;

          case 'jumlah_usia_1':
            $judul .= 'Siswa Usia 1 tahun ';
            break;

          case 'jumlah_usia_2':
            $judul .= 'Siswa Usia 2 tahun ';
            break;

          case 'jumlah_usia_3':
            $judul .= 'Siswa Usia 3 tahun ';
            break;

          case 'jumlah_usia_4':
            $judul .= 'Siswa Usia 4 tahun ';
            break;

          case 'jumlah_usia_5':
            $judul .= 'Siswa Usia 5 tahun ';
            break;

          case 'jumlah_usia_6':
            $judul .= 'Siswa Usia 6 tahun ';
            break;

          case 'jumlah_usia_lain':
            $judul .= 'Siswa Usia lain ';
            break;

          case 'jumlah_pramis':
            $judul .= 'Siswa Pramis ';
            break;

          case 'jumlah_gamis':
            $judul .= 'Siswa Gamis ';
            break;

          case 'jumlah_beasiswa':
            $judul .= 'Siswa penerima Beasiswa ';
            break;

          case 'jumlah_kaih_rumah':
            $judul .= 'Siswa melakukan 7 KAIH di Rumah ';
            break;

          case 'jumlah_kaih_sekolah':
            $judul .= 'Siswa melakukan 7 KAIH di Sekolah ';
            break;

          case 'jumlah_manasik':
            $judul .= 'Siswa ikut manasik haji ';
            break;

          case 'jumlah_talenta':
            $judul .= 'Siswa ikut Talenta Seni ';
            break;

          case 'jumlah_genmas':
            $judul .= 'Siswa Penerima Penghafal Kitab Suci ';
            break;

          case 'jumlah_kk':
            $judul .= 'Siswa Memiliki KK, Akte Kelahiran ';
            break;

          case 'jumlah_mbg':
            $judul .= 'Siswa Menerima MBG ';
            break;

          case 'jumlah_edu':
            $judul .= 'Siswa Mengikuti Edu City Tour ';
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

        return view('dinas.rekap_siswa.modal', $data)->render();
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
          ->join('public.siswa as s', 's.npsn', 'se.npsn')
          ->join('public.wali_murid as wm', function ($j) {
            $j->on('wm.id_siswa', '=', 's.id_siswa')->on('wm.npsn', '=', 's.npsn');
          })
          ->leftJoin('public.kecamatan as kec_s', 'kec_s.kecamatan_kode', 'wm.kec_id')
          ->leftJoin('public.kelurahan as kel_s', 'kel_s.kelurahan_kode', 'wm.kel_id')
          ->selectRaw("
            s.npsn,se.nama as nama_lembaga,se.jenjang,se.status,
            se.alamat,kec.kecamatan_dispenduk,kel.kelurahan_dispenduk,
            s.nama,s.nik,EXTRACT(YEAR FROM AGE(NOW(),s.tgl_lahir)) as usia,
            wm.alamat_rumah,
            COALESCE(NULLIF(kec_s.kecamatan_dispenduk,''),wm.kec_id) as kecamatan_siswa,
            COALESCE(NULLIF(kel_s.kelurahan_dispenduk,''),wm.kel_id) as kelurahan_siswa
            ")
          ->where('s.status_siswa', 'Aktif')
          ->where(function ($w) {
            $w->whereNull('s.alumni')->orWhere('s.alumni', false);
          })
          ->where('u.user_enable', true)
          ->whereIn('se.jenjang', $jenjang)
          ->when($kecamatan_kode !== 'All', function ($w) use ($kecamatan_kode) {
            $w->where('kec.kecamatan_kode', $kecamatan_kode);
          })
          ->when($kelurahan_kode !== 'All', function ($w) use ($kelurahan_kode) {
            $w->where('kel.kelurahan_kode', $kelurahan_kode);
          });

        switch ($kategori) {
          case 'jumlah_siswa':
            $data = (clone $base);
            break;

          case 'jumlah_kk_surabaya':
            $data = (clone $base)->whereRaw("COALESCE(LOWER(wm.kab_id),'') like '%surabaya%'");
            break;

          case 'jumlah_kk_non_surabaya':
            $data = (clone $base)->whereRaw("COALESCE(LOWER(wm.kab_id),'') not like '%surabaya%'");
            break;

          case 'jumlah_usia_0':
            $data = (clone $base)->whereRaw("EXTRACT(YEAR FROM AGE(NOW(),s.tgl_Lahir))='0'");
            break;

          case 'jumlah_usia_1':
            $data = (clone $base)->whereRaw("EXTRACT(YEAR FROM AGE(NOW(),s.tgl_Lahir))='1'");
            break;

          case 'jumlah_usia_2':
            $data = (clone $base)->whereRaw("EXTRACT(YEAR FROM AGE(NOW(),s.tgl_Lahir))='2'");
            break;

          case 'jumlah_usia_3':
            $data = (clone $base)->whereRaw("EXTRACT(YEAR FROM AGE(NOW(),s.tgl_Lahir))='3'");
            break;

          case 'jumlah_usia_4':
            $data = (clone $base)->whereRaw("EXTRACT(YEAR FROM AGE(NOW(),s.tgl_Lahir))='4'");
            break;

          case 'jumlah_usia_5':
            $data = (clone $base)->whereRaw("EXTRACT(YEAR FROM AGE(NOW(),s.tgl_Lahir))='5'");
            break;

          case 'jumlah_usia_6':
            $data = (clone $base)->whereRaw("EXTRACT(YEAR FROM AGE(NOW(),s.tgl_Lahir))='6'");
            break;

          case 'jumlah_usia_lain':
            $data = (clone $base)->whereRaw("EXTRACT(YEAR FROM AGE(NOW(),s.tgl_Lahir)) NOT IN ('0','1','2','3','4','5','6')");
            break;

          case 'jumlah_pramis':
            $data = (clone $base)->whereRaw("s.verified_gakin is true");
            break;

          case 'jumlah_gamis':
            $data = (clone $base)->whereRaw("s.verified_gakin2 is true");
            break;

          case 'jumlah_beasiswa':
            $data = (clone $base)
              ->join('public.detail_konfirmasi_beasiswa as b', 'b.siswa_id', 's.siswa_id')
              ->join('public.konfirmasi_beasiswa as kb', function ($j) {
                return $j->on('kb.id', '=', 'b.konfirmasi_beasiswa_id');
              })
              ->where('kb.status_pengajuan', 'Disetujui')
              ->groupByRaw("
                s.npsn,se.nama,se.jenjang,se.status,
                se.alamat,kec.kecamatan_dispenduk,kel.kelurahan_dispenduk,
                s.nama,s.nik,s.tgl_lahir,
                wm.alamat_rumah,
                kec_s.kecamatan_dispenduk,
                kel_s.kelurahan_dispenduk,wm.kec_id,wm.kel_id
              ");
            break;

          case 'jumlah_kaih_rumah':
            $data = (clone $base)
              ->join('history.kebiasaan_anak_rumah as b', 'b.siswa_id', 's.siswa_id')
              ->groupByRaw("
                s.npsn,se.nama,se.jenjang,se.status,
                se.alamat,kec.kecamatan_dispenduk,kel.kelurahan_dispenduk,
                s.nama,s.nik,s.tgl_lahir,
                wm.alamat_rumah,
                kec_s.kecamatan_dispenduk,
                kel_s.kelurahan_dispenduk,wm.kec_id,wm.kel_id
              ");
            break;

          case 'jumlah_kaih_sekolah':
            $data = (clone $base)
              ->join('history.kebiasaan_anak_sekolah as b', 'b.siswa_id', 's.siswa_id')
              ->groupByRaw("
                s.npsn,se.nama,se.jenjang,se.status,
                se.alamat,kec.kecamatan_dispenduk,kel.kelurahan_dispenduk,
                s.nama,s.nik,s.tgl_lahir,
                wm.alamat_rumah,
                kec_s.kecamatan_dispenduk,
                kel_s.kelurahan_dispenduk,wm.kec_id,wm.kel_id
              ");
            break;

          case 'jumlah_manasik':
            $data = (clone $base)
              ->join('kegiatan_pendukung.manasik_haji_peserta as b', 'b.siswa_id', 's.siswa_id')
              ->join('kegiatan_pendukung.manasik_haji_jadwal as kb', function ($j) {
                return $j->on('kb.id', '=', 'b.jadwal_id');
              })
              ->where('kb.status_jadwal', 'Setujui')
              ->groupByRaw("
                s.npsn,se.nama,se.jenjang,se.status,
                se.alamat,kec.kecamatan_dispenduk,kel.kelurahan_dispenduk,
                s.nama,s.nik,s.tgl_lahir,
                wm.alamat_rumah,
                kec_s.kecamatan_dispenduk,
                kel_s.kelurahan_dispenduk,wm.kec_id,wm.kel_id
              ");
            break;

          case 'jumlah_talenta':
            $siswa_talenta = DB::table('public.talenta_seni_jadwal as j')
              ->join('public.talenta_seni_peserta as p', 'j.id', 'p.jadwal_id')
              ->selectRaw("p.siswa_id")
              ->where('j.status_jadwal', 'Setujui')
              ->whereIn('j.jenjang', $jenjang)
              ->whereBetween('j.tanggal', [date('Y') . '-01-01', date('Y') . '-12-31'])
              ->groupBy('p.siswa_id')
              ->get();
            $arr_siswa_id = $siswa_talenta->pluck('siswa_id')->unique()->values()->toArray();

            $data = (clone $base);
            if (count($arr_siswa_id) > 0) {
              $str_siswa_id = '\'' . implode('\',\'', $arr_siswa_id) . '\'';
              $data->whereRaw("s.siswa_id IN ($str_siswa_id)");
            } else {
              $data->whereRaw("1=0");
            }
            $data
              ->groupByRaw("
                s.npsn,se.nama,se.jenjang,se.status,
                se.alamat,kec.kecamatan_dispenduk,kel.kelurahan_dispenduk,
                s.nama,s.nik,s.tgl_lahir,
                wm.alamat_rumah,
                kec_s.kecamatan_dispenduk,
                kel_s.kelurahan_dispenduk,wm.kec_id,wm.kel_id
              ");
            break;

          case 'jumlah_genmas':
            $genmas = DB::connection('db_genmas')->table('data_pendaftar')
              ->selectRaw("nik")
              ->where('valid_online', 'ya')
              ->where('status_penerima', '1')
              ->groupBy('nik')
              ->get();
            $arr_siswa_id_genmas = $genmas->pluck('nik')->unique()->values()->toArray();

            $data = (clone $base)
              ->whereIn('s.nik', $arr_siswa_id_genmas)
              ->groupByRaw("
                s.npsn,se.nama,se.jenjang,se.status,
                se.alamat,kec.kecamatan_dispenduk,kel.kelurahan_dispenduk,
                s.nama,s.nik,s.tgl_lahir,
                wm.alamat_rumah,
                kec_s.kecamatan_dispenduk,
                kel_s.kelurahan_dispenduk,wm.kec_id,wm.kel_id
              ");
            break;

          case 'jumlah_kk':
            $data = (clone $base)->where('s.verified_siswa', true);
            break;

          case 'jumlah_mbg':
            $data = (clone $base)->join('history.mbg_lembaga as mbg', 'mbg.npsn', 'se.npsn');
            break;

          case 'jumlah_edu':
            $eduSubquery = DB::connection('pgsql_paud')
              ->query()
              ->fromRaw("
                  (
                      WITH data_peserta AS (
                          SELECT
                              j.npsn,
                              CAST(
                                  (regexp_match(
                                      jsonb_array_elements_text(j.peserta::jsonb),
                                      '^\s*\[?\s*([0-9]+)'
                                  ))[1] AS BIGINT
                              ) AS id_siswa
                          FROM kegiatan_pendukung.edu_city_tour_jadwals j
                          WHERE j.status_jadwal = 'Setujui'
                      )
                      SELECT DISTINCT id_siswa, npsn
                      FROM data_peserta
                      WHERE id_siswa IS NOT NULL
                  ) as edu
              ");
            $data = (clone $base)
              ->joinSub($eduSubquery, 'edu', function ($join) {
                $join->on('edu.id_siswa', '=', 's.id_siswa')->on('edu.npsn', '=', 's.npsn');
              })
              ->groupByRaw("
                s.npsn,se.nama,se.jenjang,se.status,
                se.alamat,kec.kecamatan_dispenduk,kel.kelurahan_dispenduk,
                s.nama,s.nik,s.tgl_lahir,
                wm.alamat_rumah,
                kec_s.kecamatan_dispenduk,
                kel_s.kelurahan_dispenduk,wm.kec_id,wm.kel_id
              ");
            break;

          default:
            $data = '';
            break;
        }

        return DataTables::queryBuilder($data)
          ->addIndexColumn()
          ->addCOlumn('nik', function ($a) {
            return '<a href="' . route('bunda-dashboard_asw-detail_informasi', ['nik' => ($a->nik ?? 'aa')]) . '" target="_blank">' . $a->nik . '</a>';
          })
          ->rawColumns(['nik'])
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
