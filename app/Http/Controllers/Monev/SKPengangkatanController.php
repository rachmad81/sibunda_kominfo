<?php

namespace App\Http\Controllers\Monev;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth, Redirect, Session, DB, DataTables;

class SKPengangkatanController extends Controller
{
  function halaman(Request $request)
  {
    $halaman = $request->halaman;

    switch ($halaman) {
      case 'main':
        $kecamatan = MasterController::data_kecamatan($request);

        $data = [
          'kecamatan' => $kecamatan['kecamatan'],
          'data_kecamatan' => $kecamatan['semua'],
        ];
        return view('monev_baru.sk_pengangkatan.main', $data);
        break;

      case 'get_data':
        $periode_monev = $request->periode_monev;
        $periode_monev = $request->periode_monev;
        $status_monev = $request->status_monev;

        $monev = DB::table('cutoff_rekap.sk_pengangkatan as sk')
          ->whereNull('deleted_at')
          ->get();

        $npsn = collect($monev)->pluck('npsn')->toArray();

        $pengajuan = DB::connection('db_siagus')->table('public.pg_transaksi_gtk as p')
          ->join('pg_transaksi_berkas as tb', 'tb.pg_transaksi_gtk_id', 'p.id_pg_transaksi_gtk')
          ->join('pg_berkas as b', 'b.id_pg_berkas', 'tb.pg_berkas_id')
          ->selectRaw("
            p.npsn_tujuan as npsn,
            COUNT(DISTINCT p.npsn_tujuan) as jumlah
          ")
          ->where('p.status_pengajuan', 'Pengajuan')
          ->groupBy('p.npsn_tujuan')
          ->get();

        $npsn_pengajuan = collect($pengajuan)->pluck('npsn')->toArray();

        $sekolah = MasterController::data_sekolah($request);
        $sekolah = $sekolah
          ->when($status_monev != 'all', function ($w) use ($status_monev, $npsn) {
            if ($status_monev == 'Belum') {
              return $w->whereNotIn('s.npsn', $npsn);
            } else {
              return $w->whereIn('s.npsn', $npsn);
            }
          });

        return DataTables::queryBuilder($sekolah)
          ->addIndexColumn()
          ->addColumn('pengajuan', function ($i) use ($npsn_pengajuan, $pengajuan) {
            if (in_array($i->npsn, $npsn_pengajuan)) {
              $dt_pengajuan = $pengajuan->where('npsn', $i->npsn)->first();
              return $dt_pengajuan->jumlah ?? 0;
            }
          })
          ->addColumn('aksi', function ($i) use ($npsn, $periode_monev) {
            $tombol = ' <a href="javascript:void(0)" onclick="monev(\'' . $i->npsn . '\')" class="btn btn-outline-primary">Monev</a>';
            if (in_array($i->npsn, $npsn)) {
              $tombol .= ' <a href="' . route('monev_baru-sk_pengangkatan', ['halaman' => 'cetak', 'npsn' => $i->npsn]) . '" target="_blank" class="btn btn-outline-success">Cetak Monev</a>';
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

        $monev = DB::table('cutoff_rekap.sk_pengangkatan as jp')
          ->where('npsn', $npsn)
          ->where('status_sk_pengangkatan', 'Disetujui')
          ->whereNull('deleted_at')
          ->first();

        $sekolah = DB::connection('pgsql_paud')->table('public.sekolah as s')
          ->join('public.master_user as u', 'u.username', 's.npsn')
          ->join('public.kecamatan as kec', 'kec.kecamatan_kode', 's.kec_id')
          ->join('public.kelurahan as kel', 'kel.kelurahan_kode', 's.desa')
          ->selectRaw("s.alamat,s.npsn,s.nama as nama_lembaga,s.rt,s.rw,kec.kecamatan_dispenduk,kel.kelurahan_dispenduk,s.akreditasi")
          ->where('s.npsn', $npsn)->first();

        $gtk = DB::connection('pgsql_paud')->table('public.pegawai')
          ->where('npsn', $npsn)
          ->where('keterangan', 'Aktif')
          ->selectRaw("nama,no_ktp,jenis_pegawai,pengajuan_gtk_id")
          ->orderByRaw("CASE WHEN jenis_pegawai='KS' THEN 0 ELSE CASE WHEN jenis_pegawai='Guru' THEN 1 ELSE 2 END END ASC")
          ->get();

        $periode = DB::connection('db_simpaud')->table('jaspel.jaspel_pendidik as jp')
          ->join('jaspel.jaspel_usulan as ju', 'ju.id', 'jp.id_pengajuan')
          ->join('jaspel.jaspel_periode as pe', 'pe.id', 'ju.periode_id')
          ->where('ju.npsn', $npsn)
          ->where('jp.status', 'Disetujui')
          ->selectRaw("pe.id")
          ->orderBy('pe.id', 'DESC')
          ->first();

        $arr_jaspel = DB::connection('db_simpaud')->table('jaspel.jaspel_pendidik as jp')
          ->join('jaspel.jaspel_usulan as ju', 'ju.id', 'jp.id_pengajuan')
          ->join('jaspel.jaspel_periode as pe', 'pe.id', 'ju.periode_id')
          ->where('ju.npsn', $npsn)
          ->where('jp.status', 'Disetujui')
          ->where('pe.id', $periode->id)
          ->selectRaw("DISTINCT(jp.peg_id) as peg_id")
          ->get()->pluck('peg_id')->toArray();

        $jaspel = DB::connection('pgsql_paud')->table('public.pegawai')
          ->whereIn('peg_id', $arr_jaspel)
          ->selectRaw("nama,no_ktp,jenis_pegawai,pengajuan_gtk_id")
          ->orderByRaw("CASE WHEN jenis_pegawai='KS' THEN 0 ELSE CASE WHEN jenis_pegawai='Guru' THEN 1 ELSE 2 END END ASC")
          ->get();

        $pengajuan_id = collect($gtk)->pluck('pengajuan_gtk_id')->toArray();

        $siagus = DB::connection('db_siagus')->table('public.pg_transaksi_gtk as p')
          ->join('pg_transaksi_berkas as tb', 'tb.pg_transaksi_gtk_id', 'p.id_pg_transaksi_gtk')
          ->join('pg_berkas as b', 'b.id_pg_berkas', 'tb.pg_berkas_id')
          ->selectRaw("
            p.id_pg_transaksi_gtk,b.nama_jenis as jenis_berkas,tb.kesesuaian,tb.link,tb.file,tb.catatan,
            p.nama_pegawai,p.nik,p.status_pengajuan,p.tgl_pengajuan,p.catatan_dinas
          ")
          ->where('p.status_pengajuan', 'Disetujui')
          ->whereIn('id_pg_transaksi_gtk', $pengajuan_id)
          ->get();

        $pengajuan = DB::connection('db_siagus')->table('public.pg_transaksi_gtk as p')
          ->join('pg_transaksi_berkas as tb', 'tb.pg_transaksi_gtk_id', 'p.id_pg_transaksi_gtk')
          ->join('pg_berkas as b', 'b.id_pg_berkas', 'tb.pg_berkas_id')
          ->selectRaw("
            p.id_pg_transaksi_gtk,b.nama_jenis as jenis_berkas,tb.kesesuaian,tb.link,tb.file,tb.catatan,
            p.nama_pegawai,p.nik,p.status_pengajuan,p.tgl_pengajuan,p.catatan_dinas
          ")
          ->where('p.npsn_tujuan', $npsn)
          ->get();

        $data = [
          'monev' => $monev,
          'sekolah' => $sekolah,
          'gtk' => $gtk,
          'siagus' => $siagus,
          'pengajuan' => $pengajuan,
          'jaspel' => $jaspel,
        ];

        $content =  view('monev_baru.sk_pengangkatan.modal_form', $data)->render();
        return ['content' => $content];
        break;

      case 'simpan':
        $where = [
          'npsn' => $request->npsn,
          'status_sk_pengangkatan' => 'Disetujui',
          'deleted_at' => null,
        ];
        $data_simpan = $request->all();
        $data_simpan = array_merge($data_simpan, ['updated_at' => date('Y-m-d H:i:s')]);
        $simpan = DB::table('cutoff_rekap.sk_pengangkatan')->updateOrInsert($where, $data_simpan);
        if ($simpan) {
          return ['code' => '200'];
        } else {
          return ['code' => '250'];
        }
        break;

      case 'cetak':
        $npsn = $request->npsn;
        $generate = isset($request->generate) ? $request->generate : '';

        $monev = DB::table('cutoff_rekap.sk_pengangkatan')
          ->where('npsn', $npsn)
          ->where('status_sk_pengangkatan', 'Disetujui')
          ->whereNull('deleted_at')
          ->first();

        if (empty($monev)) {
          return '404 - Halaman tidak ditemukan';
        } else {
          if ($generate == 'true') {
            $sekolah = DB::connection('pgsql_paud')->table('public.sekolah as s')
              ->join('public.master_user as u', 'u.username', 's.npsn')
              ->join('public.kecamatan as kec', 'kec.kecamatan_kode', 's.kec_id')
              ->join('public.kelurahan as kel', 'kel.kelurahan_kode', 's.desa')
              ->selectRaw("s.alamat,s.npsn,s.nama as nama_lembaga,s.rt,s.rw,kec.kecamatan_dispenduk,kel.kelurahan_dispenduk,s.akreditasi")
              ->where('s.npsn', $npsn)->first();

            $bunda_kecamatan = DB::table('public.users as u')
              ->join('public.kecamatan as kel', 'kel.kecamatan_id', 'u.id_kecamatan')
              ->join('public.profil_pokja as p', 'p.updated_by', 'u.id')
              ->where('kel.kecamatan_dispenduk', $sekolah->kecamatan_dispenduk)
              ->where('p.id_jabatan', 18)
              ->whereNull('p.deleted_at')
              ->selectRaw("p.nama,p.email,p.nik,kel.kecamatan_dispenduk,kel.kecamatan_kode")
              ->first();

            $gtk = DB::connection('pgsql_paud')->table('public.pegawai as p')
              ->leftjoin('public.pendidikan as pe', 'p.pendidikan_terakhir', 'pe.jenjang_pendidikan_id')
              ->leftjoin('public.jabatan_ptk as j', DB::raw("CAST(j.jabatan_ptk_id as varchar)"), 'p.jabatan')
              ->where('p.npsn', $npsn)
              ->where('p.keterangan', 'Aktif')
              ->selectRaw("p.nama,p.no_ktp as nik,p.alamat,p.tempat_lahir,p.tgl_lahir,p.jenis_pegawai,p.pengajuan_gtk_id,pe.nama as pend_akhir,j.nama as nama_jabatan,p.jenis_ptk")
              ->orderByRaw("CASE WHEN jenis_pegawai='KS' THEN 0 ELSE CASE WHEN jenis_pegawai='Guru' THEN 1 ELSE 2 END END ASC")
              ->get();

            $periode = DB::connection('db_simpaud')->table('jaspel.jaspel_pendidik as jp')
              ->join('jaspel.jaspel_usulan as ju', 'ju.id', 'jp.id_pengajuan')
              ->join('jaspel.jaspel_periode as pe', 'pe.id', 'ju.periode_id')
              ->where('ju.npsn', $npsn)
              ->where('jp.status', 'Disetujui')
              ->selectRaw("pe.id")
              ->orderBy('pe.id', 'DESC')
              ->first();

            $arr_jaspel = DB::connection('db_simpaud')->table('jaspel.jaspel_pendidik as jp')
              ->join('jaspel.jaspel_usulan as ju', 'ju.id', 'jp.id_pengajuan')
              ->join('jaspel.jaspel_periode as pe', 'pe.id', 'ju.periode_id')
              ->where('ju.npsn', $npsn)
              ->where('jp.status', 'Disetujui')
              ->where('pe.id', $periode->id)
              ->selectRaw("DISTINCT(jp.peg_id) as peg_id")
              ->get()->pluck('peg_id')->toArray();

            $jaspel = DB::connection('pgsql_paud')->table('public.pegawai as p')
              ->leftjoin('public.pendidikan as pe', 'p.pendidikan_terakhir', 'pe.jenjang_pendidikan_id')
              ->leftjoin('public.jabatan_ptk as j', DB::raw("CAST(j.jabatan_ptk_id as varchar)"), 'p.jabatan')
              ->where('p.npsn', $npsn)
              ->where('p.keterangan', 'Aktif')
              ->selectRaw("p.nama,p.no_ktp as nik,p.alamat,p.tempat_lahir,p.tgl_lahir,p.jenis_pegawai,p.pengajuan_gtk_id,pe.nama as pend_akhir,j.nama as nama_jabatan,p.jenis_ptk")
              ->whereIn('peg_id', $arr_jaspel)
              ->orderByRaw("CASE WHEN jenis_pegawai='KS' THEN 0 ELSE CASE WHEN jenis_pegawai='Guru' THEN 1 ELSE 2 END END ASC")
              ->get();

            $ttd_bunda = DB::connection('db_cutt_off')->table('public.api_bunda_paud')
              ->whereIn('nik', [$bunda_kecamatan->nik])
              ->selectRaw("nik,link_ttd")
              ->first();

            $data = [
              'monev' => $monev,
              'sekolah' => $sekolah,
              'npsn' => $npsn,
              'bunda_kecamatan' => $bunda_kecamatan,
              'ttd_bunda' => $ttd_bunda,
              'gtk' => $gtk,
              'jaspel' => $jaspel,
            ];

            $content = view('monev_baru.sk_pengangkatan.cetak', $data)->render();

            DB::table('cutoff_rekap.sk_pengangkatan')
              ->where('npsn', $npsn)
              ->where('status_sk_pengangkatan', 'Disetujui')
              ->whereNull('deleted_at')
              ->update(['sk_pengangkatan' => $content]);

            return $content;
          } else {
            return $monev->sk_pengangkatan;
          }
        }
        break;

      default:
        return '404 - Halaman tidak ditemukan';
        break;
    }
  }
}
