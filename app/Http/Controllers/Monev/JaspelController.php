<?php

namespace App\Http\Controllers\Monev;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth, Redirect, Session, DB, DataTables;

class JaspelController extends Controller
{
  function halaman(Request $request)
  {
    $halaman = $request->halaman;

    switch ($halaman) {
      case 'main':
        $tahun = DB::table('cutoff_rekap.monev_jaspel')->pluck('tahun')->unique()->toArray();
        array_push($tahun, date('Y'));
        $tahun = collect($tahun)->unique()->values()->toArray();
        arsort($tahun);

        $kecamatan = MasterController::data_kecamatan($request);

        $data = [
          'tahun' => $tahun,
          'kecamatan' => $kecamatan['kecamatan'],
          'data_kecamatan' => $kecamatan['semua'],
        ];
        return view('monev_baru.jaspel.main', $data);
        break;

      case 'get_data':
        $tahun_monev = $request->tahun_monev;
        $status_monev = $request->status_monev;

        $monev = DB::table('cutoff_rekap.monev_jaspel as jp')
          ->where('tahun', $tahun_monev)
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
          ->addColumn('aksi', function ($i) use ($npsn, $tahun_monev) {
            $tombol = ' <a href="javascript:void(0)" onclick="monev(\'' . $i->npsn . '\',\'' . $tahun_monev . '\')" class="btn btn-outline-primary">Monev</a>';
            if (in_array($i->npsn, $npsn)) {
              $tombol .= ' <a href="' . route('monev_baru-jaspel', ['halaman' => 'cetak', 'npsn' => $i->npsn, 'tahun' => $tahun_monev]) . '" target="_blank" class="btn btn-outline-success">Cetak Monev</a>';
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

        $monev = DB::table('cutoff_rekap.monev_jaspel as jp')
          ->where('tahun', $tahun)
          ->where('npsn', $npsn)
          ->first();

        $sekolah = DB::connection('pgsql_paud')->table('public.sekolah as s')
          ->join('public.master_user as u', 'u.username', 's.npsn')
          ->join('public.kecamatan as kec', 'kec.kecamatan_kode', 's.kec_id')
          ->join('public.kelurahan as kel', 'kel.kelurahan_kode', 's.desa')
          ->selectRaw("s.alamat,s.npsn,s.nama as nama_lembaga,s.rt,s.rw,kec.kecamatan_dispenduk,kel.kelurahan_dispenduk,s.akreditasi")
          ->where('s.npsn', $npsn)->first();

        $data_ks = DB::connection('pgsql_paud')->table('public.pegawai')
          ->selectRaw("nama")
          ->where('npsn', $npsn)
          ->where('jabatan', 2)
          ->where('keterangan', 'Aktif')
          ->first();

        $jumlah_siswa = DB::connection('pgsql_paud')
          ->table('public.siswa as s')
          ->select('s.siswa_id')
          ->where('s.status_siswa', 'Aktif')
          ->where('s.npsn', $npsn)
          ->whereRaw("s.alumni is not true")
          ->count();

        $data_ijop = DB::table('public.ijops')
          ->where('npsn', $npsn)
          ->first();

        $data = [
          'monev' => $monev,
          'sekolah' => $sekolah,
          'data_ks' => $data_ks,
          'data_ijop' => $data_ijop,
          'jumlah_siswa' => $jumlah_siswa,
          'tahun' => $tahun,
        ];

        $content =  view('monev_baru.jaspel.modal_form', $data)->render();
        return ['content' => $content];
        break;

      case 'simpan':
        $tahun = $request->tahun; // "2026",
        $npsn = $request->npsn; // "69764101",
        $nama_lembaga = $request->nama_lembaga; // "PPT ANANDA",
        $akreditasi = $request->akreditasi; // "B",
        $alamat = $request->alamat; // "JL.KAMPUNG MALANG UTARA 7 NO.7, RT 8, RW 4, Kelurahan TEGALSARI, Kecmaatan TEGALSARI",
        $kecamatan = $request->kecamatan; // "TEGALSARI",
        $nama_ks = $request->nama_ks; // "NURNIAYATI",
        $status_ijop = $request->status_ijop; // "Ada",
        $tgl_ijop = $request->tgl_ijop; // "2026-04-06",
        $tgl_monev = $request->tgl_monev; // "2026-04-06",
        $status_kbm = $request->status_kbm; // "Ada",
        $jumlah_murid_profil = $request->jumlah_murid_profil; // "60",
        $jumlah_murid_dapodik = $request->jumlah_murid_dapodik; // "60",
        $daftar_hadir_siswa = $request->daftar_hadir_siswa; // "Ada",
        $jumlah_murid_daftar = $request->jumlah_murid_daftar; // "55",
        $jumlah_murid_riil = $request->jumlah_murid_riil; // "55",
        $status_jumlah_siswa = $request->status_jumlah_siswa; // "Sesuai",
        $alasan_status = $request->alasan_status; // "Diprofil belum dikeluarkan",
        $penerimaan_jaspel = $request->penerimaan_jaspel; //" VARCHAR NULL DEFAULT NULL,
        $penerimaan_jaspel_orang = $request->penerimaan_jaspel_orang; //" VARCHAR NULL DEFAULT NULL,
        $pemotongan_jaspel = $request->pemotongan_jaspel; //" VARCHAR NULL DEFAULT NULL,
        $ttd_penerimaan = $request->ttd_penerimaan; //" VARCHAR NULL DEFAULT NULL,
        $petugas_survey = $request->petugas_survey; // "Suprijanti"
        $catatan = $request->catatan; // "Suprijanti"

        $data_simpan = [
          'tahun' => $tahun,
          'npsn' => $npsn,
          'tgl_monev' => $tgl_monev,
          'updated_at' => date('Y-m-d H:i:s'),
          'nama_lembaga' => $nama_lembaga,
          'alamat_lembaga' => $alamat,
          'kecamatan' => $kecamatan,
          'nama_ks' => $nama_ks,
          'ijop' => $status_ijop,
          'tgl_berakhir_ijop' => $tgl_ijop,
          'kbm' => $status_kbm,
          'jumlah_murid_profil' => $jumlah_murid_profil,
          'jumlah_murid_dapodik' => $jumlah_murid_dapodik,
          'daftar_hadir_siswa' => $daftar_hadir_siswa,
          'jumlah_murid_daftar' => $jumlah_murid_daftar,
          'jumlah_murid_riil' => $jumlah_murid_riil,
          'status_jumlah_siswa' => $status_jumlah_siswa,
          'alasan_status' => $alasan_status,
          'penerimaan_jaspel' => $penerimaan_jaspel,
          'penerimaan_jaspel_orang' => $penerimaan_jaspel_orang,
          'pemotongan_jaspel' => $pemotongan_jaspel,
          'ttd_penerimaan' => $ttd_penerimaan,
          'catatan' => $catatan,
          'petugas_survey' => $petugas_survey,
          'user_id' => Auth::getUser()->id,
          'akreditasi' => $akreditasi,
        ];

        $simpan = DB::table('cutoff_rekap.monev_jaspel')->updateOrInsert(['npsn' => $npsn, 'tahun' => $tahun], $data_simpan);
        if ($simpan) {
          return ['code' => '200'];
        } else {
          return ['code' => '250'];
        }
        break;

      case 'cetak':
        $npsn = $request->npsn;
        $tahun = $request->tahun;

        $monev = DB::table('cutoff_rekap.monev_jaspel as jp')
          ->where('tahun', $tahun)
          ->where('npsn', $npsn)
          ->first();

        if (empty($monev)) {
          return '404 - Halaman tidak ditemukan';
        } else {
          $data = [
            'monev' => $monev,
          ];
          return view('monev_baru.jaspel.cetak', $data);
        }
        break;

      default:
        return '404 - Halaman tidak ditemukan';
        break;
    }
  }
}
