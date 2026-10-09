<?php

namespace App\Http\Controllers\Monev;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth, Redirect, Session, DB, DataTables;

class KelembagaanPaudController extends Controller
{
  function halaman(Request $request)
  {
    $halaman = $request->halaman;

    switch ($halaman) {
      case 'main':
        $tahun = DB::table('cutoff_rekap.monev_bop')->pluck('tahun')->unique()->toArray();
        array_push($tahun, date('Y'));
        $tahun = collect($tahun)->unique()->values()->toArray();
        arsort($tahun);

        $kecamatan = MasterController::data_kecamatan($request);

        $data = [
          'tahun' => $tahun,
          'kecamatan' => $kecamatan['kecamatan'],
          'data_kecamatan' => $kecamatan['semua'],
        ];
        return view('monev_baru.kelembagaan_paud.main', $data);
        break;

      case 'get_data':
        $tahun_monev = $request->tahun_monev;
        $periode_monev = $request->periode_monev;
        $periode_monev = $request->periode_monev;
        $status_monev = $request->status_monev;

        $monev = DB::table('cutoff_rekap.monitor_kelembagaan as jp')
          ->where('tahun', $tahun_monev)
          ->where('periode', $periode_monev)
          ->get();

        $npsn = collect($monev)->pluck('npsn')->toArray();

        $sekolah = MasterController::data_sekolah($request);
        $sekolah = $sekolah->when($status_monev != 'all', function ($w) use ($status_monev, $npsn) {
          if ($status_monev == 'Belum') {
            return $w->whereNotIn('s.npsn', $npsn);
          } else {
            return $w->whereIn('s.npsn', $npsn);
          }
        });

        return DataTables::queryBuilder($sekolah)
          ->addIndexColumn()
          ->addColumn('aksi', function ($i) use ($npsn, $tahun_monev, $periode_monev) {
            $tombol = ' <a href="' . route('monev_baru-kelembagaan_paud', ['halaman' => 'form_monev', 'npsn' => $i->npsn, 'tahun' => $tahun_monev, 'periode' => $periode_monev]) . '" target="_blank" class="btn btn-outline-primary">Instrumen</a>';
            if (in_array($i->npsn, $npsn)) {
              $tombol .= ' <a href="' . route('monev_baru-kelembagaan_paud', ['halaman' => 'cetak', 'npsn' => $i->npsn, 'tahun' => $tahun_monev, 'periode' => $periode_monev]) . '" target="_blank" class="btn btn-outline-success">Cetak Instrumen</a>';
            }
            return $tombol;
          })
          ->addColumn('status_monev', function ($i) use ($npsn) {
            if (in_array($i->npsn, $npsn)) {
              return '<a href="javascript:void(0)" class="badge badge-success">Sudah</a>';
            }
          })
          ->orderColumn('status_monev', function ($w, $order) use ($npsn) {
            $list = implode("','", $npsn);

            $w->orderByRaw("
              CASE
                  WHEN s.npsn IN ('$list') THEN 1
                  ELSE 0
              END $order
            ");
          })
          ->rawColumns(['aksi', 'status_monev'])
          ->make(true);
        break;

      case 'form_monev':
        $npsn = $request->npsn;
        $tahun = $request->tahun;
        $periode = $request->periode;

        $monev = DB::table('cutoff_rekap.monitor_kelembagaan as jp')
          ->where('tahun', $tahun)
          ->where('periode', $periode)
          ->where('npsn', $npsn)
          ->first();

        $sekolah = DB::connection('pgsql_paud')->table('public.sekolah as s')
          ->join('public.master_user as u', 'u.username', 's.npsn')
          ->join('public.kecamatan as kec', 'kec.kecamatan_kode', 's.kec_id')
          ->join('public.kelurahan as kel', 'kel.kelurahan_kode', 's.desa')
          ->selectRaw("
            s.jenjang,s.alamat,s.npsn,s.nama as nama_lembaga,s.rt,s.rw,kec.kecamatan_dispenduk,kel.kelurahan_dispenduk,s.akreditasi,
            CASE WHEN (s.akreditasi is not null AND s.akreditasi in ('A','B','C')) THEN  '-' ELSE s.akreditasi END as akreditasi,
            s.tahun_akreditasi,s.tgl_akreditasi_berakhir,s.lat_long,s.ttd_ks,s.stempel
          ")
          ->where('s.npsn', $npsn)->first();

        $data_ks = DB::connection('pgsql_paud')->table('public.pegawai')
          ->selectRaw("nama,hp")
          ->where('npsn', $npsn)
          ->where('jabatan', 2)
          ->where('keterangan', 'Aktif')
          ->first();

        $jumlah_siswa = DB::connection('pgsql_paud')
          ->table('public.siswa as s')
          ->where('s.status_siswa', 'Aktif')
          ->where('s.npsn', $npsn)
          ->whereRaw("s.alumni is not true")
          ->selectRaw("
            COUNT(*) as jumlah,
            COUNT(*) FILTER(WHERE (foto is not null)) as jumlah_foto,
            COUNT(*) FILTER(WHERE (no_akta is not null)) as jumlah_akta
          ")
          ->first();

        $data_ptk = DB::connection('pgsql_paud')
          ->table('public.pegawai as s')
          ->where('s.npsn', $npsn)
          ->where('s.keterangan', 'Aktif')
          ->whereRaw("s.induk IS TRUE")
          ->selectRaw("
            s.no_ktp
          ")
          ->get();

        $nik_pegawai = collect($data_ptk)->pluck('no_ktp')->unique()->toArray();
        $jumlah_ptk = (object)[
          'jumlah' => collect($data_ptk)->count(),
        ];

        $bunda_kecamatan = DB::table('public.users as u')
          ->join('public.kecamatan as kel', 'kel.kecamatan_id', 'u.id_kecamatan')
          ->join('public.profil_pokja as p', 'p.updated_by', 'u.id')
          ->where('kel.kecamatan_dispenduk', $sekolah->kecamatan_dispenduk)
          ->where('p.id_jabatan', 18)
          ->whereNull('p.deleted_at')
          ->selectRaw("p.nama,p.email,p.nik,kel.kecamatan_dispenduk,kel.kecamatan_kode")
          ->first();

        $kode_kecamatan = $bunda_kecamatan->kecamatan_kode ?? '';

        $bunda_kelurahan = DB::table('public.users as u')
          ->join('public.kelurahan as kel', 'kel.kelurahan_id', 'u.id_kelurahan')
          ->join('public.profil_pokja as p', 'p.updated_by', 'u.id')
          ->where('kel.kelurahan_dispenduk', $sekolah->kelurahan_dispenduk)
          ->where('kel.kecamatan_kode', $kode_kecamatan)
          ->where('p.id_jabatan', 33)
          ->whereNull('p.deleted_at')
          ->selectRaw("p.nama,p.email,p.nik,kel.kelurahan_dispenduk")
          ->first();

        $ttd_bunda = DB::connection('db_cutt_off')->table('public.api_bunda_paud')
          ->whereIn('nik', [$bunda_kelurahan->nik, $bunda_kecamatan->nik])
          ->selectRaw("nik,link_ttd")
          ->get();

        $data_ijop = DB::connection('perijinan')->table('public.izin_operasional')
          ->whereRaw("(npsn='$npsn' OR npsn_sekarang='$npsn')")
          ->where('izin_status', 'setuju')
          ->where('jenis_operasional', '5')
          ->selectRaw("no_sk_baru,tgl_disetujui,tgl_berakhir_baru,no_akreditasi,tgl_akreditasi_berakhir,akreditasi,alamat_sekolah,kecamatan_sekolah,kelurahan_sekolah,rt_sekolah,rw_sekolah")
          ->orderBy('tgl_berakhir_baru', 'DESC')
          ->first();

        $bulan = (int)date('m', strtotime('-1 month', strtotime(date('Y-m-d'))));
        $hurufbulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $profil_sekolah = DB::connection('pgsql_paud')->table('public.sekolah')->selectRaw("'sekolah' as jenis,npsn,last_update as updated_at")->where('npsn', $npsn)->orderBy('last_update', 'DESC')->first();
        $profil_siswa = DB::connection('pgsql_paud')->table('public.siswa')->selectRaw("'siswa' as jenis,npsn,tgl_update as updated_at")->where('npsn', $npsn)->orderBy('tgl_update', 'DESC')->first();
        $profil_pegawai = DB::connection('pgsql_paud')->table('public.pegawai')->selectRaw("'pegawai' as jenis,npsn,last_update as updated_at")->where('npsn', $npsn)->orderBy('last_update', 'DESC')->first();

        $update_profil = collect()->push($profil_sekolah)->push($profil_siswa)->push($profil_pegawai)->sortByDesc('updated_at')->values()->first();

        $jaspel = DB::connection('db_simpaud')->table('jaspel.jaspel_pendidik as jp')
          ->join('jaspel.jaspel_usulan as ju', 'ju.id', 'jp.id_pengajuan')
          ->join('jaspel.jaspel_periode as pe', 'pe.id', 'ju.periode_id')
          ->where('ju.npsn', $npsn)
          ->where('jp.status', 'Disetujui')
          ->selectRaw("COUNT(*) as jumlah")
          ->groupBy('pe.id')
          ->orderBy('pe.id', 'DESC')
          ->first();

        $pmt = DB::connection('db_simpaud')->table('public.pmt_usulans as u')
          ->join('public.pmt_laporans as l', 'l.pmt_usulan_id', 'u.id')
          ->where('u.tahun', $tahun)
          ->where('u.acc_usulan', '1')
          ->where('u.npsn', $npsn)
          ->orderBy('u.id', 'DESC')
          ->selectRaw("u.jml_pd,u.no_sk_pendirian,u.no_sk_operasional,u.tgl_pendirian,u.tgl_operasional,u.mulai_berlaku,u.selesai_berlaku,l.menu_keg_1,l.menu_keg_2")
          ->first();

        $rpl = DB::table('cutoff_rekap.rpl_ptk')->whereIn('nik', $nik_pegawai)->count();
        $dapodik = DB::table('cutoff_rekap.data_dapodik')->where('tahun', $tahun)->where('npsn', $npsn)->first();

        $bosp = DB::connection('db_simpaud')
          ->table('public.bop_penerimaans as b')
          ->join('public.periodes as p', 'p.id', 'b.periode_id')
          ->whereNotNull('nominal')
          ->where('b.tahun', ($tahun - 1))
          ->where('b.npsn', $npsn)
          ->selectRaw("b.npsn,b.nama,SUM(b.nominal) as nominal,b.tahun,p.lainnya as tahap")
          ->groupByRaw("b.npsn,b.nama,b.tahun,p.lainnya")
          ->first();

        $beasiswa = DB::connection('pgsql_paud')->table('public.konfirmasi_beasiswa as b')
          ->join('public.detail_konfirmasi_beasiswa as d', 'd.konfirmasi_beasiswa_id', 'b.id')
          ->join('public.sekolah as s', 's.npsn', 'b.npsn')
          ->selectRaw("
            b.id,b.npsn,b.jenjang,b.tanggal_konfirmasi,b.periode,b.tahun,b.file_surat_pengajuan,
            d.nama_siswa,d.nik_siswa,d.no_kk,d.alamat,d.kelurahan,d.kecamatan,d.tanggal_lahir,
            s.nama as nama_sekolah
            ")
          ->where('b.tahun', $tahun)
          ->where('b.npsn', $npsn)
          ->where('d.status_menerima', 'Ya')
          ->where('b.status_pengajuan', 'Disetujui')
          ->get();

        $data = [
          'monev' => $monev,
          'sekolah' => $sekolah,
          'data_ks' => $data_ks,
          'ijop' => $data_ijop,
          'jumlah_siswa' => $jumlah_siswa,
          'jumlah_ptk' => $jumlah_ptk,
          'bunda_kelurahan' => $bunda_kelurahan,
          'bunda_kecamatan' => $bunda_kecamatan,
          'tahun' => $tahun,
          'periode' => $periode,
          'npsn' => $npsn,
          'jaspel' => $jaspel,
          'pmt' => $pmt,
          'rpl' => $rpl,
          'bosp' => $bosp,
          'ttd_bunda' => $ttd_bunda,
          'dapodik' => $dapodik,
          'update_profil' => $update_profil,
          'beasiswa' => $beasiswa,
        ];

        return view('monev_baru.kelembagaan_paud.modal_form', $data);
        break;

      case 'simpan':
        $npsn = $request->npsn_monev;
        $tahun_monev = $request->tahun_monev;
        $periode_monev = $request->periode_monev;
        $data_simpan = $request->all();
        unset($data_simpan['npsn_monev']);
        unset($data_simpan['tahun_monev']);
        unset($data_simpan['periode_monev']);

        $where = [
          'tahun' => $tahun_monev,
          'periode' => $periode_monev,
          'npsn' => $npsn,
        ];

        // return $data_simpan;

        $simpan = DB::table('cutoff_rekap.monitor_kelembagaan')->updateOrInsert($where, $data_simpan);
        if ($simpan) {
          return ['code' => '200'];
        } else {
          return ['code' => '250'];
        }
        break;

      case 'cetak':
        $npsn = $request->npsn;
        $tahun = $request->tahun;
        $periode = $request->periode;

        $monev = DB::table('cutoff_rekap.monitor_kelembagaan as jp')
          ->where('tahun', $tahun)
          ->where('periode', $periode)
          ->where('npsn', $npsn)
          ->first();

        if (empty($monev)) {
          return '404 - Halaman tidak ditemukan';
        } else {
          $data = [
            'monev' => $monev,
            'tahun' => $tahun,
            'periode' => $periode,
            'npsn' => $npsn,
          ];

          return view('monev_baru.kelembagaan_paud.cetak', $data);
        }
        break;

      case 'detail':
        $kategori = $request->kategori;
        $npsn = $request->npsn;
        $data = [
          'kategori' => $kategori,
          'npsn' => $npsn
        ];
        $content = view('monev_baru.kelembagaan_paud.detail', $data)->render();
        return ['content' => $content];
        break;

      case 'detail_baris':
        $kategori = $request->kategori;
        $npsn = $request->npsn;

        if (str_contains($kategori, 'siswa')) {
          $nik = '';
          if ($kategori == 'beasiswa_paud') {
            $nik = DB::connection('pgsql_paud')->table('public.konfirmasi_beasiswa as b')
              ->join('public.detail_konfirmasi_beasiswa as d', 'd.konfirmasi_beasiswa_id', 'b.id')
              ->join('public.sekolah as s', 's.npsn', 'b.npsn')
              ->selectRaw("d.nik_siswa")
              ->where('b.tahun', date('Y'))
              ->where('d.status_menerima', 'Ya')
              ->where('b.status_pengajuan', 'Disetujui')
              ->where('b.npsn', $npsn);
          }

          $data = DB::connection('pgsql_paud')
            ->table('public.siswa as s')
            ->leftjoin('public.wali_murid as wm', DB::raw("CONCAT(wm.id_siswa,wm.npsn)"), DB::raw("CONCAT(s.id_siswa,s.npsn)"))
            ->leftjoin('public.kecamatan as kec', 'wm.kec_id', 'kec.kecamatan_kode')
            ->leftjoin('public.kelurahan as kel', 'wm.kel_id', 'kel.kelurahan_kode')
            ->leftjoin('public.kecamatan as kec_dom', 's.kec_domisili', 'kec_dom.kecamatan_kode')
            ->leftjoin('public.kelurahan as kel_dom', 's.kel_domisili', 'kel_dom.kelurahan_kode')
            ->selectRaw("
              s.nama,s.nik,wm.alamat_rumah as alamat_kk,
              CASE WHEN kel.kelurahan_dispenduk is null THEN wm.kel_id ELSE kel.kelurahan_dispenduk END as kelurahan_kk,
              CASE WHEN kec.kecamatan_dispenduk is null THEN wm.kec_id ELSE kec.kecamatan_dispenduk END as kecamatan_kk,
              s.alamat_domisili as alamat_dom,
              CASE WHEN kel_dom.kelurahan_dispenduk is null THEN s.kel_domisili ELSE kel_dom.kelurahan_dispenduk END as kelurahan_dom,
              CASE WHEN kec_dom.kecamatan_dispenduk is null THEN s.kec_domisili ELSE kec_dom.kecamatan_dispenduk END as kecamatan_dom
            ")
            ->where('s.status_siswa', 'Aktif')
            ->whereRaw("s.alumni is not true")
            ->where('s.npsn', $npsn)
            ->when($kategori, function ($w) use ($kategori, $nik) {
              if ($kategori == 'siswa_kk') {
                return $w->whereNotNull('s.no_akta');
              } else if ($kategori == 'beasiswa_paud') {
                return $w->whereIn('s.nik', $nik);
              }
            });

          return DataTables::queryBuilder($data)
            ->addIndexColumn()
            ->orderColumn('nama', function ($q, $order) {
              return $q->orderByRaw("s.nama $order");
            })
            ->filterColumn('nama', function ($q, $keyword) {
              return $q->where('s.nama', 'ilike', '%' . $keyword . '%');
            })
            ->orderColumn('nik', function ($q, $order) {
              return $q->orderByRaw("s.nik $order");
            })
            ->filterColumn('nik', function ($q, $keyword) {
              return $q->where('s.nik', 'ilike', '%' . $keyword . '%');
            })
            ->orderColumn('alamat_kk', function ($q, $order) {
              return $q->orderByRaw("wm.alamat_rumah $order");
            })
            ->filterColumn('alamat_kk', function ($q, $keyword) {
              return $q->where('wm.alamat_rumah', 'ilike', '%' . $keyword . '%');
            })
            ->orderColumn('kelurahan_kk', function ($q, $order) {
              return $q->orderByRaw("CASE WHEN kel.kelurahan_dispenduk is null THEN wm.kel_id ELSE kel.kelurahan_dispenduk END $order");
            })
            ->filterColumn('kelurahan_kk', function ($q, $keyword) {
              return $q->whereRaw("CASE WHEN kel.kelurahan_dispenduk is null THEN wm.kel_id ELSE kel.kelurahan_dispenduk END ILIKE '%$keyword%'");
            })
            ->orderColumn('kecamatan_kk', function ($q, $order) {
              return $q->orderByRaw("CASE WHEN kec.kecamatan_dispenduk is null THEN wm.kec_id ELSE kec.kecamatan_dispenduk END $order");
            })
            ->filterColumn('kecamatan_kk', function ($q, $keyword) {
              return $q->whereRaw("CASE WHEN kec.kecamatan_dispenduk is null THEN wm.kec_id ELSE kec.kecamatan_dispenduk END ILIKE '%$keyword%'");
            })
            ->orderColumn('alamat_dom', function ($q, $order) {
              return $q->orderByRaw("s.alamat_domisili $order");
            })
            ->filterColumn('alamat_dom', function ($q, $keyword) {
              return $q->where('s.alamat_domisili', 'ilike', '%' . $keyword . '%');
            })
            ->orderColumn('kelurahan_dom', function ($q, $order) {
              return $q->orderByRaw("CASE WHEN kel_dom.kelurahan_dispenduk is null THEN s.kel_domisili ELSE kel_dom.kelurahan_dispenduk END $order");
            })
            ->filterColumn('kelurahan_dom', function ($q, $keyword) {
              return $q->whereRaw("CASE WHEN kel_dom.kelurahan_dispenduk is null THEN s.kel_domisili ELSE kel_dom.kelurahan_dispenduk END ILIKE '%$keyword%'");
            })
            ->orderColumn('kecamatan_dom', function ($q, $order) {
              return $q->orderByRaw("CASE WHEN kec_dom.kecamatan_dispenduk is null THEN s.kec_domisili ELSE kec_dom.kecamatan_dispenduk END $order");
            })
            ->filterColumn('kecamatan_dom', function ($q, $keyword) {
              return $q->whereRaw("CASE WHEN kec_dom.kecamatan_dispenduk is null THEN s.kec_domisili ELSE kec_dom.kecamatan_dispenduk END ILIKE '%$keyword%'");
            })
            ->make(true);
        } else if (str_contains($kategori, 'pegawai')) {
          $data = DB::connection('pgsql_paud')
            ->table('public.pegawai as s')
            ->leftjoin('public.kecamatan as kec', 's.kec_id', 'kec.kecamatan_kode')
            ->leftjoin('public.kelurahan as kel', 's.kelurahan', 'kel.kelurahan_kode')
            ->leftjoin('public.kecamatan as kec_dom', 's.kec_domisili', 'kec_dom.kecamatan_kode')
            ->leftjoin('public.kelurahan as kel_dom', 's.kel_domisili', 'kel_dom.kelurahan_kode')
            ->selectRaw("
              s.nama,s.no_ktp as nik,s.alamat as alamat_kk,
              CASE WHEN kel.kelurahan_dispenduk is null THEN s.kelurahan ELSE kel.kelurahan_dispenduk END as kelurahan_kk,
              CASE WHEN kec.kecamatan_dispenduk is null THEN s.kec_id ELSE kec.kecamatan_dispenduk END as kecamatan_kk,
              s.alamat_domisili as alamat_dom,
              CASE WHEN kel_dom.kelurahan_dispenduk is null THEN s.kel_domisili ELSE kel_dom.kelurahan_dispenduk END as kelurahan_dom,
              CASE WHEN kec_dom.kecamatan_dispenduk is null THEN s.kec_domisili ELSE kec_dom.kecamatan_dispenduk END as kecamatan_dom
            ")
            ->where('s.keterangan', 'Aktif')
            ->whereRaw("s.induk is true")
            ->where('s.npsn', $npsn);

          return DataTables::queryBuilder($data)
            ->addIndexColumn()
            ->orderColumn('nama', function ($q, $order) {
              return $q->orderByRaw("s.nama $order");
            })
            ->filterColumn('nama', function ($q, $keyword) {
              return $q->where('s.nama', 'ilike', '%' . $keyword . '%');
            })
            ->orderColumn('nik', function ($q, $order) {
              return $q->orderByRaw("s.no_ktp $order");
            })
            ->filterColumn('nik', function ($q, $keyword) {
              return $q->where('s.no_ktp', 'ilike', '%' . $keyword . '%');
            })
            ->orderColumn('alamat_kk', function ($q, $order) {
              return $q->orderByRaw("s.alamat $order");
            })
            ->filterColumn('alamat_kk', function ($q, $keyword) {
              return $q->where('s.alamat', 'ilike', '%' . $keyword . '%');
            })
            ->orderColumn('kelurahan_kk', function ($q, $order) {
              return $q->orderByRaw("CASE WHEN kel.kelurahan_dispenduk is null THEN s.kelurahan ELSE kel.kelurahan_dispenduk END $order");
            })
            ->filterColumn('kelurahan_kk', function ($q, $keyword) {
              return $q->whereRaw("CASE WHEN kel.kelurahan_dispenduk is null THEN s.kelurahan ELSE kel.kelurahan_dispenduk END ILIKE '%$keyword%'");
            })
            ->orderColumn('kecamatan_kk', function ($q, $order) {
              return $q->orderByRaw("CASE WHEN kec.kecamatan_dispenduk is null THEN s.kec_id ELSE kec.kecamatan_dispenduk END $order");
            })
            ->filterColumn('kecamatan_kk', function ($q, $keyword) {
              return $q->whereRaw("CASE WHEN kec.kecamatan_dispenduk is null THEN s.kec_id ELSE kec.kecamatan_dispenduk END ILIKE '%$keyword%'");
            })
            ->orderColumn('alamat_dom', function ($q, $order) {
              return $q->orderByRaw("s.alamat_domisili $order");
            })
            ->filterColumn('alamat_dom', function ($q, $keyword) {
              return $q->where('s.alamat_domisili', 'ilike', '%' . $keyword . '%');
            })
            ->orderColumn('kelurahan_dom', function ($q, $order) {
              return $q->orderByRaw("CASE WHEN kel_dom.kelurahan_dispenduk is null THEN s.kel_domisili ELSE kel_dom.kelurahan_dispenduk END $order");
            })
            ->filterColumn('kelurahan_dom', function ($q, $keyword) {
              return $q->whereRaw("CASE WHEN kel_dom.kelurahan_dispenduk is null THEN s.kel_domisili ELSE kel_dom.kelurahan_dispenduk END ILIKE '%$keyword%'");
            })
            ->orderColumn('kecamatan_dom', function ($q, $order) {
              return $q->orderByRaw("CASE WHEN kec_dom.kecamatan_dispenduk is null THEN s.kec_domisili ELSE kec_dom.kecamatan_dispenduk END $order");
            })
            ->filterColumn('kecamatan_dom', function ($q, $keyword) {
              return $q->whereRaw("CASE WHEN kec_dom.kecamatan_dispenduk is null THEN s.kec_domisili ELSE kec_dom.kecamatan_dispenduk END ILIKE '%$keyword%'");
            })
            ->make(true);
        } else if (str_contains($kategori, 'jaspel')) {
          $maxPeriode = DB::connection('db_simpaud')->table('jaspel.jaspel_pendidik as jp')
            ->join('jaspel.jaspel_usulan as ju', 'ju.id', 'jp.id_pengajuan')
            ->join('jaspel.jaspel_periode as pe', 'pe.id', 'ju.periode_id')
            ->where('ju.npsn', $npsn)
            ->where('jp.status', 'Disetujui')
            ->groupByRaw("pe.id,jp.nik,ju.npsn")
            ->orderBy('pe.id', 'DESC')
            ->max('pe.id');

          $jaspel = DB::connection('db_simpaud')->table('jaspel.jaspel_pendidik as jp')
            ->join('jaspel.jaspel_usulan as ju', 'ju.id', 'jp.id_pengajuan')
            ->join('jaspel.jaspel_periode as pe', 'pe.id', 'ju.periode_id')
            ->where('ju.npsn', $npsn)
            ->where('jp.status', 'Disetujui')
            ->where('pe.id', $maxPeriode)
            ->selectRaw("
              jp.nik
            ")
            ->groupByRaw("pe.id,jp.nik,ju.npsn")
            ->orderBy('pe.id', 'DESC')
            ->get();

          $nik = collect($jaspel)->pluck('nik')->toArray();

          $data = DB::connection('pgsql_paud')
            ->table('public.pegawai as s')
            ->leftjoin('public.kecamatan as kec', 's.kec_id', 'kec.kecamatan_kode')
            ->leftjoin('public.kelurahan as kel', 's.kelurahan', 'kel.kelurahan_kode')
            ->leftjoin('public.kecamatan as kec_dom', 's.kec_domisili', 'kec_dom.kecamatan_kode')
            ->leftjoin('public.kelurahan as kel_dom', 's.kel_domisili', 'kel_dom.kelurahan_kode')
            ->selectRaw("
              s.nama,s.no_ktp as nik,s.alamat as alamat_kk,
              CASE WHEN kel.kelurahan_dispenduk is null THEN s.kelurahan ELSE kel.kelurahan_dispenduk END as kelurahan_kk,
              CASE WHEN kec.kecamatan_dispenduk is null THEN s.kec_id ELSE kec.kecamatan_dispenduk END as kecamatan_kk,
              s.alamat_domisili as alamat_dom,
              CASE WHEN kel_dom.kelurahan_dispenduk is null THEN s.kel_domisili ELSE kel_dom.kelurahan_dispenduk END as kelurahan_dom,
              CASE WHEN kec_dom.kecamatan_dispenduk is null THEN s.kec_domisili ELSE kec_dom.kecamatan_dispenduk END as kecamatan_dom
            ")
            ->where('s.keterangan', 'Aktif')
            ->whereRaw("s.induk is true")
            ->where('s.npsn', $npsn)
            ->whereIn('s.no_ktp', $nik);

          return DataTables::queryBuilder($data)
            ->addIndexColumn()
            ->orderColumn('nama', function ($q, $order) {
              return $q->orderByRaw("s.nama $order");
            })
            ->filterColumn('nama', function ($q, $keyword) {
              return $q->where('s.nama', 'ilike', '%' . $keyword . '%');
            })
            ->orderColumn('nik', function ($q, $order) {
              return $q->orderByRaw("s.no_ktp $order");
            })
            ->filterColumn('nik', function ($q, $keyword) {
              return $q->where('s.no_ktp', 'ilike', '%' . $keyword . '%');
            })
            ->orderColumn('alamat_kk', function ($q, $order) {
              return $q->orderByRaw("s.alamat $order");
            })
            ->filterColumn('alamat_kk', function ($q, $keyword) {
              return $q->where('s.alamat', 'ilike', '%' . $keyword . '%');
            })
            ->orderColumn('kelurahan_kk', function ($q, $order) {
              return $q->orderByRaw("CASE WHEN kel.kelurahan_dispenduk is null THEN s.kelurahan ELSE kel.kelurahan_dispenduk END $order");
            })
            ->filterColumn('kelurahan_kk', function ($q, $keyword) {
              return $q->whereRaw("CASE WHEN kel.kelurahan_dispenduk is null THEN s.kelurahan ELSE kel.kelurahan_dispenduk END ILIKE '%$keyword%'");
            })
            ->orderColumn('kecamatan_kk', function ($q, $order) {
              return $q->orderByRaw("CASE WHEN kec.kecamatan_dispenduk is null THEN s.kec_id ELSE kec.kecamatan_dispenduk END $order");
            })
            ->filterColumn('kecamatan_kk', function ($q, $keyword) {
              return $q->whereRaw("CASE WHEN kec.kecamatan_dispenduk is null THEN s.kec_id ELSE kec.kecamatan_dispenduk END ILIKE '%$keyword%'");
            })
            ->orderColumn('alamat_dom', function ($q, $order) {
              return $q->orderByRaw("s.alamat_domisili $order");
            })
            ->filterColumn('alamat_dom', function ($q, $keyword) {
              return $q->where('s.alamat_domisili', 'ilike', '%' . $keyword . '%');
            })
            ->orderColumn('kelurahan_dom', function ($q, $order) {
              return $q->orderByRaw("CASE WHEN kel_dom.kelurahan_dispenduk is null THEN s.kel_domisili ELSE kel_dom.kelurahan_dispenduk END $order");
            })
            ->filterColumn('kelurahan_dom', function ($q, $keyword) {
              return $q->whereRaw("CASE WHEN kel_dom.kelurahan_dispenduk is null THEN s.kel_domisili ELSE kel_dom.kelurahan_dispenduk END ILIKE '%$keyword%'");
            })
            ->orderColumn('kecamatan_dom', function ($q, $order) {
              return $q->orderByRaw("CASE WHEN kec_dom.kecamatan_dispenduk is null THEN s.kec_domisili ELSE kec_dom.kecamatan_dispenduk END $order");
            })
            ->filterColumn('kecamatan_dom', function ($q, $keyword) {
              return $q->whereRaw("CASE WHEN kec_dom.kecamatan_dispenduk is null THEN s.kec_domisili ELSE kec_dom.kecamatan_dispenduk END ILIKE '%$keyword%'");
            })
            ->make(true);
        } else if (str_contains($kategori, 'pmt')) {
          $pmt = DB::connection('db_simpaud')
            ->table('public.pmt_usulans as u')
            ->join('public.pmt_laporans as l', 'l.pmt_usulan_id', 'u.id')
            ->join('public.pmt_pds as p', 'p.pmt_usulan_id', 'u.id')
            ->where('u.tahun', date('Y'))
            ->where('u.acc_usulan', '1')
            ->where('u.npsn', $npsn)
            ->orderBy('u.id', 'DESC')
            ->selectRaw("p.siswa_id,p.nik")
            ->get();

          $nik = collect($pmt)->pluck('nik')->toArray();

          $data =  DB::connection('pgsql_paud')
            ->table('public.siswa as s')
            ->leftjoin('public.wali_murid as wm', DB::raw("CONCAT(wm.id_siswa,wm.npsn)"), DB::raw("CONCAT(s.id_siswa,s.npsn)"))
            ->leftjoin('public.kecamatan as kec', 'wm.kec_id', 'kec.kecamatan_kode')
            ->leftjoin('public.kelurahan as kel', 'wm.kel_id', 'kel.kelurahan_kode')
            ->leftjoin('public.kecamatan as kec_dom', 's.kec_domisili', 'kec_dom.kecamatan_kode')
            ->leftjoin('public.kelurahan as kel_dom', 's.kel_domisili', 'kel_dom.kelurahan_kode')
            ->selectRaw("
              s.nama,s.nik,wm.alamat_rumah as alamat_kk,
              CASE WHEN kel.kelurahan_dispenduk is null THEN wm.kel_id ELSE kel.kelurahan_dispenduk END as kelurahan_kk,
              CASE WHEN kec.kecamatan_dispenduk is null THEN wm.kec_id ELSE kec.kecamatan_dispenduk END as kecamatan_kk,
              s.alamat_domisili as alamat_dom,
              CASE WHEN kel_dom.kelurahan_dispenduk is null THEN s.kel_domisili ELSE kel_dom.kelurahan_dispenduk END as kelurahan_dom,
              CASE WHEN kec_dom.kecamatan_dispenduk is null THEN s.kec_domisili ELSE kec_dom.kecamatan_dispenduk END as kecamatan_dom
            ")
            ->where('s.status_siswa', 'Aktif')
            ->whereRaw("s.alumni is not true")
            ->where('s.npsn', $npsn)
            ->whereIn('s.nik', $nik)
            ->when($kategori == 'siswa_kk', function ($w) {
              return $w->whereNotNull('s.no_akta');
            });

          return DataTables::queryBuilder($data)
            ->addIndexColumn()
            ->orderColumn('nama', function ($q, $order) {
              return $q->orderByRaw("s.nama $order");
            })
            ->filterColumn('nama', function ($q, $keyword) {
              return $q->where('s.nama', 'ilike', '%' . $keyword . '%');
            })
            ->orderColumn('nik', function ($q, $order) {
              return $q->orderByRaw("s.no_ktp $order");
            })
            ->filterColumn('nik', function ($q, $keyword) {
              return $q->where('s.no_ktp', 'ilike', '%' . $keyword . '%');
            })
            ->orderColumn('alamat_kk', function ($q, $order) {
              return $q->orderByRaw("s.alamat $order");
            })
            ->filterColumn('alamat_kk', function ($q, $keyword) {
              return $q->where('s.alamat', 'ilike', '%' . $keyword . '%');
            })
            ->orderColumn('kelurahan_kk', function ($q, $order) {
              return $q->orderByRaw("CASE WHEN kel.kelurahan_dispenduk is null THEN s.kelurahan ELSE kel.kelurahan_dispenduk END $order");
            })
            ->filterColumn('kelurahan_kk', function ($q, $keyword) {
              return $q->whereRaw("CASE WHEN kel.kelurahan_dispenduk is null THEN s.kelurahan ELSE kel.kelurahan_dispenduk END ILIKE '%$keyword%'");
            })
            ->orderColumn('kecamatan_kk', function ($q, $order) {
              return $q->orderByRaw("CASE WHEN kec.kecamatan_dispenduk is null THEN s.kec_id ELSE kec.kecamatan_dispenduk END $order");
            })
            ->filterColumn('kecamatan_kk', function ($q, $keyword) {
              return $q->whereRaw("CASE WHEN kec.kecamatan_dispenduk is null THEN s.kec_id ELSE kec.kecamatan_dispenduk END ILIKE '%$keyword%'");
            })
            ->orderColumn('alamat_dom', function ($q, $order) {
              return $q->orderByRaw("s.alamat_domisili $order");
            })
            ->filterColumn('alamat_dom', function ($q, $keyword) {
              return $q->where('s.alamat_domisili', 'ilike', '%' . $keyword . '%');
            })
            ->orderColumn('kelurahan_dom', function ($q, $order) {
              return $q->orderByRaw("CASE WHEN kel_dom.kelurahan_dispenduk is null THEN s.kel_domisili ELSE kel_dom.kelurahan_dispenduk END $order");
            })
            ->filterColumn('kelurahan_dom', function ($q, $keyword) {
              return $q->whereRaw("CASE WHEN kel_dom.kelurahan_dispenduk is null THEN s.kel_domisili ELSE kel_dom.kelurahan_dispenduk END ILIKE '%$keyword%'");
            })
            ->orderColumn('kecamatan_dom', function ($q, $order) {
              return $q->orderByRaw("CASE WHEN kec_dom.kecamatan_dispenduk is null THEN s.kec_domisili ELSE kec_dom.kecamatan_dispenduk END $order");
            })
            ->filterColumn('kecamatan_dom', function ($q, $keyword) {
              return $q->whereRaw("CASE WHEN kec_dom.kecamatan_dispenduk is null THEN s.kec_domisili ELSE kec_dom.kecamatan_dispenduk END ILIKE '%$keyword%'");
            })
            ->make(true);
        } else if (str_contains($kategori, 'rpl')) {
          $pegawai = DB::connection('pgsql_paud')
            ->table('public.pegawai as s')
            ->selectRaw("
              s.no_ktp
            ")
            ->where('s.keterangan', 'Aktif')
            ->whereRaw("s.induk is true")
            ->where('s.npsn', $npsn)->get();

          $nik_pegawai = collect($pegawai)->pluck('no_ktp')->toArray();

          $rpl = DB::table('cutoff_rekap.rpl_ptk')->whereIn('nik', $nik_pegawai)->select('nik')->get();

          $nik = collect($rpl)->pluck('nik')->toArray();

          $data = DB::connection('pgsql_paud')
            ->table('public.pegawai as s')
            ->leftjoin('public.kecamatan as kec', 's.kec_id', 'kec.kecamatan_kode')
            ->leftjoin('public.kelurahan as kel', 's.kelurahan', 'kel.kelurahan_kode')
            ->leftjoin('public.kecamatan as kec_dom', 's.kec_domisili', 'kec_dom.kecamatan_kode')
            ->leftjoin('public.kelurahan as kel_dom', 's.kel_domisili', 'kel_dom.kelurahan_kode')
            ->selectRaw("
              s.nama,s.no_ktp as nik,s.alamat as alamat_kk,
              CASE WHEN kel.kelurahan_dispenduk is null THEN s.kelurahan ELSE kel.kelurahan_dispenduk END as kelurahan_kk,
              CASE WHEN kec.kecamatan_dispenduk is null THEN s.kec_id ELSE kec.kecamatan_dispenduk END as kecamatan_kk,
              s.alamat_domisili as alamat_dom,
              CASE WHEN kel_dom.kelurahan_dispenduk is null THEN s.kel_domisili ELSE kel_dom.kelurahan_dispenduk END as kelurahan_dom,
              CASE WHEN kec_dom.kecamatan_dispenduk is null THEN s.kec_domisili ELSE kec_dom.kecamatan_dispenduk END as kecamatan_dom
            ")
            ->where('s.keterangan', 'Aktif')
            ->whereRaw("s.induk is true")
            ->where('s.npsn', $npsn)
            ->whereIn('s.no_ktp', $nik);

          return DataTables::queryBuilder($data)
            ->addIndexColumn()
            ->orderColumn('nama', function ($q, $order) {
              return $q->orderByRaw("s.nama $order");
            })
            ->filterColumn('nama', function ($q, $keyword) {
              return $q->where('s.nama', 'ilike', '%' . $keyword . '%');
            })
            ->orderColumn('nik', function ($q, $order) {
              return $q->orderByRaw("s.no_ktp $order");
            })
            ->filterColumn('nik', function ($q, $keyword) {
              return $q->where('s.no_ktp', 'ilike', '%' . $keyword . '%');
            })
            ->orderColumn('alamat_kk', function ($q, $order) {
              return $q->orderByRaw("s.alamat $order");
            })
            ->filterColumn('alamat_kk', function ($q, $keyword) {
              return $q->where('s.alamat', 'ilike', '%' . $keyword . '%');
            })
            ->orderColumn('kelurahan_kk', function ($q, $order) {
              return $q->orderByRaw("CASE WHEN kel.kelurahan_dispenduk is null THEN s.kelurahan ELSE kel.kelurahan_dispenduk END $order");
            })
            ->filterColumn('kelurahan_kk', function ($q, $keyword) {
              return $q->whereRaw("CASE WHEN kel.kelurahan_dispenduk is null THEN s.kelurahan ELSE kel.kelurahan_dispenduk END ILIKE '%$keyword%'");
            })
            ->orderColumn('kecamatan_kk', function ($q, $order) {
              return $q->orderByRaw("CASE WHEN kec.kecamatan_dispenduk is null THEN s.kec_id ELSE kec.kecamatan_dispenduk END $order");
            })
            ->filterColumn('kecamatan_kk', function ($q, $keyword) {
              return $q->whereRaw("CASE WHEN kec.kecamatan_dispenduk is null THEN s.kec_id ELSE kec.kecamatan_dispenduk END ILIKE '%$keyword%'");
            })
            ->orderColumn('alamat_dom', function ($q, $order) {
              return $q->orderByRaw("s.alamat_domisili $order");
            })
            ->filterColumn('alamat_dom', function ($q, $keyword) {
              return $q->where('s.alamat_domisili', 'ilike', '%' . $keyword . '%');
            })
            ->orderColumn('kelurahan_dom', function ($q, $order) {
              return $q->orderByRaw("CASE WHEN kel_dom.kelurahan_dispenduk is null THEN s.kel_domisili ELSE kel_dom.kelurahan_dispenduk END $order");
            })
            ->filterColumn('kelurahan_dom', function ($q, $keyword) {
              return $q->whereRaw("CASE WHEN kel_dom.kelurahan_dispenduk is null THEN s.kel_domisili ELSE kel_dom.kelurahan_dispenduk END ILIKE '%$keyword%'");
            })
            ->orderColumn('kecamatan_dom', function ($q, $order) {
              return $q->orderByRaw("CASE WHEN kec_dom.kecamatan_dispenduk is null THEN s.kec_domisili ELSE kec_dom.kecamatan_dispenduk END $order");
            })
            ->filterColumn('kecamatan_dom', function ($q, $keyword) {
              return $q->whereRaw("CASE WHEN kec_dom.kecamatan_dispenduk is null THEN s.kec_domisili ELSE kec_dom.kecamatan_dispenduk END ILIKE '%$keyword%'");
            })
            ->make(true);
        }
        break;

      default:
        return '404 - Halaman tidak ditemukan';
        break;
    }
  }
}
