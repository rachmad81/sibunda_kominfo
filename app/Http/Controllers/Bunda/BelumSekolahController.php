<?php

namespace App\Http\Controllers\Bunda;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Tools\Bantuan;
use App\Http\Controllers\Bunda\DashboardASWController;
use DB, Session, Auth;

class BelumSekolahController extends Controller
{
  function main(Request $request)
  {
    $jenjang = $request->jenjang;
    return view('bunda.belum_sekolah.main', compact('jenjang'));
  }

  function get_data(Request $request)
  {
    // return $request->all();
    $status_alamat = $request->status_alamat ?? '';
    $status_sekolah = $request->status_sekolah ?? '';
    $status_verifikasi = $request->status_verifikasi ?? '';
    $status_usia = $request->status_usia ?? '';

    $tingkat = Auth::getUser()->tingkat;

    $kecamatan_kode = '';
    $kelurahan_kode = '';
    $kecamatan = '';
    $kelurahan = '';
    $m_kecamatan = DB::table('public.kecamatan')
      ->selectRaw("kecamatan_kode,kecamatan_dispenduk,dispenduk_baru");
    if ($tingkat == 'kecamatan') {
      $id_kecamatan = Auth::getUser()->id_kecamatan;
      $dt_kecamatan = (clone $m_kecamatan)->where('kecamatan_id', $id_kecamatan)->first();

      $kecamatan = $dt_kecamatan->kecamatan_dispenduk;
      $kecamatan_kode = $dt_kecamatan->kecamatan_kode;
      $kelurahan = '';
      $kelurahan_kode = '';
    } else if ($tingkat == 'kelurahan') {
      $id_kelurahan = Auth::getUser()->id_kelurahan;
      $dt_kelurahan = DB::table('public.kelurahan as kel')
        ->join('public.kecamatan as kec', 'kec.kecamatan_kode', 'kel.kecamatan_kode')
        ->selectRaw("kec.kecamatan_kode,kec.kecamatan_dispenduk,kel.kelurahan_kode,kel.kelurahan_dispenduk")
        ->where('kel.kelurahan_id', $id_kelurahan)
        ->first();

      $kecamatan = $dt_kelurahan->kecamatan_dispenduk;
      $kecamatan_kode = $dt_kelurahan->kecamatan_kode;
      $kelurahan = $dt_kelurahan->kelurahan_dispenduk;
      $kelurahan_kode = $dt_kelurahan->kelurahan_kode;
    }

    $master_kecamatan = (clone $m_kecamatan)->get();
    $kecamatan_dispenduk = collect($master_kecamatan)->pluck('kecamatan_dispenduk')->values()->toARray();
    $dispenduk_baru = collect($master_kecamatan)->pluck('dispenduk_baru')->values()->toARray();
    $arr_kecamatan = array_merge($kecamatan_dispenduk, $dispenduk_baru);
    $arr_kecamatan = collect($arr_kecamatan)->unique()->values()->toArray();

    $request = $request->merge([
      'kecamatan' => $kecamatan,
      'kecamatan_kode' => $kecamatan_kode,
      'kelurahan' => $kelurahan,
      'kelurahan_kode' => $kelurahan_kode,
    ]);

    $anomali = DashboardASWController::anomali_nama($request);

    $siswa = DB::connection('db_cutt_off')
      ->table('public.penduduk_inject as a')
      ->leftjoin('public.survey_bunda as b', 'b.nik', 'a.nik')
      ->selectRaw("a.nik,a.nama_lengkap,a.alamat,a.rt,a.rw,a.kelurahan,a.kecamatan,
      COALESCE(NULLIF(a.alamat_dom,''),a.alamat) as alamat_dom,
      COALESCE(NULLIF(a.rt_dom,''),a.rt) as rt_dom,
      COALESCE(NULLIF(a.rw_dom,''),a.rw) as rw_dom,
      COALESCE(NULLIF(a.kelurahan_dom,''),a.kelurahan) as kelurahan_dom,
      COALESCE(NULLIF(a.kecamatan_dom,''),a.kecamatan) as kecamatan_dom,
      b.status_verifikasi,b.status_alamat,b.status_sekolah,b.detail_alamat,b.detail_sekolah,b.detail_tidak_sekolah,b.pilihan_detail_tidak_sekolah,b.bukti_survei,EXTRACT(YEAR FROM AGE(NOW(),a.tgl_lahir)) as usia,a.jenis_kelamin,b.edukasi_ortu,b.tanggapan_ortu,b.tindak_lanjut,b.penanganan_abk")
      // ->whereRaw("COALESCE(a.kelurahan_dom,'')<>'' AND COALESCE(a.kecamatan_dom,'')<>''")
      ->whereIn(DB::raw("COALESCE(NULLIF(a.kecamatan_dom,''),a.kecamatan)"), $arr_kecamatan)
      ->when(!empty($kecamatan) && $kecamatan != 'All', function ($q) use ($kecamatan, $anomali) {
        $q->whereIn(
          DB::raw("COALESCE(NULLIF(a.kecamatan_dom, ''), a.kecamatan)"),
          $anomali['arr_kecamatan']
        );
      })
      ->when(!empty($kelurahan) && $kelurahan != 'All', function ($q) use ($kelurahan, $anomali) {
        $q->whereIn(
          DB::raw("COALESCE(NULLIF(a.kelurahan_dom,''),a.kelurahan)"),
          $anomali['arr_kelurahan']
        );
      })
      ->when($status_alamat != 'Semua', function ($w) use ($status_alamat) {
        if ($status_alamat == 'Sesuai') {
          return $w->whereIn(DB::raw("COALESCE(b.status_alamat,'')"), ['Sesuai', 'sesuai']);
        } else {
          return $w->whereNotIn(DB::raw("COALESCE(b.status_alamat,'')"), ['Sesuai', 'sesuai']);
        }
      })
      // ->when($status_sekolah, function ($w) use ($status_sekolah) {
      //   if ($status_sekolah == 'Tidak sekolah') {
      //     return $w->whereRaw("
      //       CASE
      //       WHEN COALESCE(b.nik,'') <> '' THEN
      //           (
      //             COALESCE(b.status_sekolah,'') NOT IN ('Sudah','sudah_paud','sudah_sd','Akan sekolah','akan_daftar')
      //             AND (COALESCE(a.nama_sekolah,'') = ''
      //             OR a.is_aktif_pendidikan IS NOT TRUE)
      //           )
      //       ELSE
      //           (
      //             COALESCE(a.nama_sekolah,'') = ''
      //             OR a.is_aktif_pendidikan IS NOT TRUE
      //           )
      //       END
      //     ");
      //   } else if ($status_sekolah == 'Sudah') {
      //     return $w->whereRaw("
      //       COALESCE(b.nik,'') <> '' AND
      //       (
      //         COALESCE(b.status_sekolah,'') IN ('Sudah','sudah_paud','sudah_sd')
      //       )
      //     ");
      //   } else if ($status_sekolah == 'Akan sekolah') {
      //     return $w->whereRaw("
      //       COALESCE(b.nik,'') <> '' AND
      //       (
      //         COALESCE(b.status_sekolah,'') IN ('Akan sekolah','akan_daftar')
      //       )
      //     ");
      //   }
      // })
      ->when($status_verifikasi != 'Semua', function ($w) use ($status_verifikasi) {
        if ($status_verifikasi == 'Belum') {
          return $w->whereRaw("COALESCE(b.status_verifikasi,'') = ''");
        } else {
          return $w->where('b.status_verifikasi', $status_verifikasi);
        }
      })
      ->when($status_usia != 'Semua', function ($w) use ($status_usia) {
        return $w->whereRaw("EXTRACT(YEAR FROM AGE(NOW(),a.tgl_lahir))='$status_usia'");
      })
      ->whereRaw("
        ((EXTRACT(YEAR FROM AGE(now(),a.tgl_lahir)) BETWEEN 0 AND 6) AND a.is_aktif_kependudukan IS TRUE AND a.status_keberadaan='ADA')
        AND
        CASE
          WHEN COALESCE(b.nik,'') <> '' THEN
              (
                COALESCE(b.status_sekolah,'') NOT IN ('Sudah','sudah_paud','sudah_sd','Akan sekolah','akan_daftar')
                AND (COALESCE(a.nama_sekolah,'') = ''
                OR a.is_aktif_pendidikan IS NOT TRUE)
              )
          ELSE
              (
                COALESCE(a.nama_sekolah,'') = ''
                OR a.is_aktif_pendidikan IS NOT TRUE
              )
        END");

    return DataTables()->queryBuilder($siswa)
      ->addIndexColumn()
      ->addColumn('status_verif', function ($akun) {
        $button = (is_null($akun->status_verifikasi)) ? 'Belum Verifikasi' : $akun->status_verifikasi;
        return $button;
      })
      ->addColumn('aksi', function ($akun) {
        $button = '';
        // $button .= '<a href="javascript:void(0)" class="btn btn-sm btn-primary" onclick="ganti(\'' . $akun->nik . '\')">Verifikasi</a>';
        $button .= '<a href="' . route('bunda-monitoring-blm_sekolah-survey', ['halaman' => 'main', 'nik' => $akun->nik]) . '" target="_blank" class="btn btn-sm btn-primary">Verifikasi Baru</a>';
        // $button .= '<a href="javascript:void(0)" onclick="alert(\'Mohon maaf kami sedang proses perubahan form\')" class="btn btn-sm btn-primary">Verifikasi Baru</a>';

        return $button;
      })
      ->addColumn('tombol_bukti_survei', function ($akun) {
        $button = '';
        if (!is_null($akun->bukti_survei) && $akun->bukti_survei != '') {
          $button = '<a href="' . $akun->bukti_survei . '" target="_blank" class="btn btn-sm btn-warning" >Lihat</a>';
        }

        return $button;
      })
      ->addColumn('status_domisili', function ($w) {
        $domisili = \App\Tools\ParameterATS::data_domisili();
        $dt = collect($domisili)->where('value', $w->status_alamat)->first();
        return ($dt['text'] ?? $w->status_alamat);
      })
      ->addColumn('status_sekolah_n', function ($w) {
        $sekolah = \App\Tools\ParameterATS::data_sekolah();
        $dt = collect($sekolah)->where('value', $w->status_sekolah)->first();
        return $dt['text'] ?? $w->status_sekolah;
      })
      ->addColumn('status_faktor', function ($w) {
        $faktor = \App\Tools\ParameterATS::data_faktor();
        $arr_tidak_Sekolah = explode('|', $w->detail_tidak_sekolah);
        $dt = collect($faktor)->whereIn('value', $arr_tidak_Sekolah);

        $hasil_faktor = '<ol>';
        foreach ($dt as $d) {
          $hasil_faktor .= '<li>- ' . $d['text'] . '</li>';
        }
        $hasil_faktor .= '</ol>';
        return $hasil_faktor;
      })
      ->addColumn('status_edukasi', function ($w) {
        $edukasi = \App\Tools\ParameterATS::data_edukasi();

        $edukasi_ortu = str_replace(['-', ';'], ['', ''], $w->edukasi_ortu);
        $arr_edukasi = collect($edukasi)->pluck('value')->values()->toArray();
        $edukasi_ortu = explode('|', $edukasi_ortu ?? '') ?? [];
        $edukasi_lainnya = array_diff($edukasi_ortu, $arr_edukasi);

        $edukasi_lainnya = implode('', $edukasi_lainnya);

        $dt = collect($edukasi)->whereIn('value', $edukasi_ortu);

        $hasil_faktor = '<ol>';
        foreach ($dt as $d) {
          $hasil_faktor .= '<li>- ' . $d['text'] . '</li>';
        }
        $hasil_faktor .= '<li>- ' . $edukasi_lainnya . '</li>';
        $hasil_faktor .= '</ol>';
        return $hasil_faktor;
      })
      ->addColumn('status_tanggapan', function ($w) {
        $sekolah = \App\Tools\ParameterATS::data_tanggapan();
        $dt = collect($sekolah)->where('value', $w->tanggapan_ortu)->first();
        return $dt['text'] ?? $w->tanggapan_ortu;
      })
      ->addColumn('status_rtl', function ($w) {
        $faktor = \App\Tools\ParameterATS::data_rtl();
        $arr_tidak_Sekolah = explode('|', $w->tindak_lanjut);
        $dt = collect($faktor)->whereIn('value', $arr_tidak_Sekolah);

        $hasil_faktor = '';
        if ($w->tindak_lanjut != '') {
          $hasil_faktor .= '<b>Rencana Tindak Lanjut</b>';
          $hasil_faktor .= '<ol>';
          foreach ($dt as $d) {
            $hasil_faktor .= '<li>- ' . $d['text'] . '</li>';
          }
          $hasil_faktor .= '</ol>';
        }

        if ($w->penanganan_abk != '') {
          $hasil_faktor .= $w->penanganan_abk;
        }
        return $hasil_faktor;
      })
      ->rawColumns(['aksi', 'tombol_bukti_survei', 'status_domisili', 'status_sekolah_n', 'status_faktor', 'detail_alamat', 'detail_sekolah', 'pilihan_detail_tidak_sekolah', 'status_edukasi', 'status_tanggapan', 'status_rtl'])
      ->make(true);
  }

  function ganti(Request $request)
  {
    $nik = $request->nik;

    $siswa = DB::connection('db_cutt_off')->table('public.penduduk_inject as a')
      ->leftjoin('public.survey_bunda as b', 'b.nik', 'a.nik')
      ->selectRaw("b.status,b.hasil_cari,b.created_at,b.updated_at,b.updated_by,b.status_alamat,b.detail_alamat,b.status_sekolah,b.detail_sekolah,b.detail_tidak_sekolah,b.pilihan_detail_tidak_sekolah,b.status_verifikasi,b.status_gamis,b.tandai_tidak_sekolah,b.bukti_survei,a.nik,a.no_kk,a.nama_lengkap,a.jenis_kelamin,a.tempat_lahir,a.tgl_lahir,a.provinsi,a.kabupaten,a.alamat,a.rt,a.rw,a.kelurahan,a.kecamatan")
      ->where('a.nik', $nik)
      ->first();

    $data = [
      'siswa' => $siswa,
    ];

    $content = view('bunda.belum_sekolah.identitas', $data)->render();

    return ['code' => 200, 'content' => $content];
  }

  function simpan(Request $request)
  {
    $nik = $request->nik;
    $status_alamat = $request->status_alamat;
    $detail_alamat = $request->detail_alamat;
    $status_sekolah = $request->status_sekolah;
    $detail_sekolah = $request->detail_sekolah;
    $detail_tidak_sekolah = $request->detail_tidak_sekolah;
    $detail_tidak_sekolah_lainnya = $request->detail_tidak_sekolah_lainnya;
    $detail_alasan_tidak_sekolah = $request->detail_alasan_tidak_sekolah;
    $detail_alasan_tidak_sekolah_lainnya = $request->detail_alasan_tidak_sekolah_lainnya;
    $status_verifikasi = $request->status_verifikasi;
    $bukti_survei = $request->bukti_survei;

    $cek_gdrive = 'PUBLIC';
    if ($bukti_survei != '') {
      $cek_gdrive = Bantuan::checkDrivePermission($bukti_survei);
      if ($cek_gdrive != 'PUBLIC') {
        return ['code' => '350', 'message' => $cek_gdrive];
      }
    }

    if ($detail_tidak_sekolah == 'Lainnya') {
      $detail_tidak_sekolah = $detail_tidak_sekolah_lainnya;
    }
    if ($detail_alasan_tidak_sekolah == 'Lainnya') {
      $detail_alasan_tidak_sekolah = $detail_alasan_tidak_sekolah_lainnya;
    }

    if ($status_sekolah != 'Tidak sekolah') {
      $detail_tidak_sekolah = null;
      $detail_alasan_tidak_sekolah = null;
    } else {
      $detail_sekolah = null;
    }

    $data = [
      'status_alamat' => $status_alamat,
      'detail_alamat' => $detail_alamat,
      'status_sekolah' => $status_sekolah,
      'detail_sekolah' => $detail_sekolah,
      'detail_tidak_sekolah' => $detail_tidak_sekolah,
      'pilihan_detail_tidak_sekolah' => $detail_alasan_tidak_sekolah,
      'status_verifikasi' => $status_verifikasi,
      'bukti_survei' => $bukti_survei,
      'updated_at' => date('Y-m-d H:i:s'),
      'updated_by' => Auth::getUser()->nama,
    ];
    $simpan = DB::connection('db_cutt_off')->table('public.survey_bunda')->where('nik', $nik)->updateOrInsert(['nik' => $nik], $data);
    if ($simpan) {
      return ['code' => '200'];
    } else {
      return ['code' => '250'];
    }
  }

  function tambah()
  {
    $data = [];
    $content = view('bunda.belum_sekolah.tambah', $data)->render();

    return ['code' => 200, 'content' => $content];
  }

  function cari_nik(Request $request)
  {
    $nik = $request->nik;
    $data_anak_belum_sekolah = DB::connection('pgsql_paud')->table('history.anak_belum_sekolah')
      ->selectRaw("nama,tempat_lahir,tgl_lahir,alamat,rt,rw,kelurahan,kecamatan,status,jk as kelamin")
      ->where('nik', $nik)->get();

    $data_anak_lengkap = DB::connection('pgsql_paud')->table('history.anak_belum_sekolah')
      ->where('nik', $nik)->get();

    $data_paud = DB::connection('pgsql_paud')->table('public.siswa as s')
      ->leftjoin('wali_murid as wm', function ($join) {
        $join->on(function ($j) {
          $j->on(DB::raw('CAST(wm.id_siswa as INTEGER)'), '=', DB::raw('CAST(s.id_siswa as INTEGER)'))->on('wm.npsn', '=', 's.npsn');
        })->orOn('wm.siswa_id', 's.siswa_id');
      })
      ->leftjoin('public.sekolah as se', 'se.npsn', 's.npsn')
      ->leftjoin('kelurahan as kel', 'kel.kelurahan_kode', 'wm.kel_id')
      ->leftjoin('kecamatan as kec', 'kec.kecamatan_kode', 'wm.kec_id')
      ->selectRaw("s.nama,CASE WHEN (s.kelamin='L') THEN 'LAKI-LAKI' ELSE 'PEREMPUAN' END as kelamin,s.tempat_lahir,s.tgl_lahir,s.status_siswa,CASE WHEN (s.alumni is true) THEN 'Lulus' ELSE '' END as alumni,s.tahun_lulus,se.nama as nama_sekolah,se.npsn,s.kelas,s.rombel,wm.alamat_rumah as alamat,wm.rt,wm.rw,kel.kelurahan_dispenduk as kelurahan,kec.kecamatan_dispenduk as kecamatan")
      ->whereRaw("s.nik='$nik'")->get();
    $data_sd = DB::connection('db_sd')->table('public.siswa as s')
      ->leftjoin('wali_murid as wm', function ($join) {
        $join->on(function ($j) {
          $j->on(DB::raw('CAST(wm.id_siswa as INTEGER)'), '=', DB::raw('CAST(s.id_siswa as INTEGER)'))->on('wm.npsn', '=', 's.npsn');
        })->orOn('wm.siswa_id', 's.siswa_id');
      })
      ->leftjoin('public.sekolah as se', 'se.npsn', 's.npsn')
      ->leftjoin('kelurahan as kel', 'kel.kelurahan_kode', 'wm.kel_id')
      ->leftjoin('kecamatan as kec', 'kec.kecamatan_kode', 'wm.kec_id')
      ->selectRaw("s.nama,CASE WHEN (s.kelamin='L') THEN 'LAKI-LAKI' ELSE 'PEREMPUAN' END as kelamin,s.tempat_lahir,s.tgl_lahir,s.status_siswa,CASE WHEN (s.alumni is true) THEN 'Lulus' ELSE '' END as alumni,se.nama as nama_sekolah,se.npsn,s.kelas,s.rombel,s.tahun_lulus,wm.alamat_rumah as alamat,wm.rt,wm.rw,kel.kelurahan_dispenduk as kelurahan,kec.kecamatan_dispenduk as kecamatan")
      ->whereRaw("s.nik='$nik'")->get();

    $data = [
      'anak_belum_sekolah' => $data_anak_belum_sekolah,
      'data_paud' => $data_paud,
      'data_sd' => $data_sd,
    ];

    $content = view('bunda.belum_sekolah.hasil', $data)->render();

    return ['content' => $content, 'anak_lengkap' => $data_anak_lengkap];
  }

  function simpan_tambah(Request $request)
  {
    $alamat = $request->alamat;
    $detail_alamat = $request->detail_alamat;
    $detail_alasan_tidak_sekolah = $request->detail_alasan_tidak_sekolah;
    $detail_alasan_tidak_sekolah_lainnya = $request->detail_alasan_tidak_sekolah_lainnya;
    $detail_sekolah = $request->detail_sekolah;
    $detail_tidak_sekolah = $request->detail_tidak_sekolah;
    $detail_tidak_sekolah_lainnya = $request->detail_tidak_sekolah_lainnya;
    $json_param = $request->json_param;
    $kecamatan = $request->kecamatan;
    $kelamin = $request->kelamin;
    $kelurahan = $request->kelurahan;
    $nama = $request->nama;
    $nik = $request->nik;
    $rt = $request->rt;
    $rw = $request->rw;
    $status_alamat = $request->status_alamat;
    $status_sekolah = $request->status_sekolah;
    $status_verifikasi = $request->status_verifikasi;
    $tempat_lahir = $request->tempat_lahir;
    $tgl_lahir = $request->tgl_lahir;
    $bukti_survei = $request->bukti_survei;

    if ($detail_tidak_sekolah == 'Lainnya') {
      $detail_tidak_sekolah = $detail_tidak_sekolah_lainnya;
    }
    if ($detail_alasan_tidak_sekolah == 'Lainnya') {
      $detail_alasan_tidak_sekolah = $detail_alasan_tidak_sekolah_lainnya;
    }

    if ($status_sekolah != 'Tidak sekolah') {
      $detail_tidak_sekolah = null;
      $detail_alasan_tidak_sekolah = null;
    } else {
      $detail_sekolah = null;
    }

    $data = [
      'nik' => $nik,
      'tempat_lahir' => $tempat_lahir,
      'nama' => $nama,
      'tgl_lahir' => $tgl_lahir,
      'alamat' => $alamat,
      'rt' => $rt,
      'rw' => $rw,
      'kelurahan' => $kelurahan,
      'kecamatan' => $kecamatan,
      'status_alamat' => $status_alamat,
      'detail_alamat' => $detail_alamat,
      'status_sekolah' => $status_sekolah,
      'detail_sekolah' => $detail_sekolah,
      'detail_tidak_sekolah' => $detail_tidak_sekolah,
      'pilihan_detail_tidak_sekolah' => $detail_alasan_tidak_sekolah,
      'status_verifikasi' => $status_verifikasi,
      'bukti_survei' => $bukti_survei,
      'updated_at' => date('Y-m-d H:i:s'),
      'updated_by' => Auth::getUser()->nama,
    ];
    $simpan = DB::connection('pgsql_paud')->table('history.anak_belum_sekolah')->updateOrInsert(['nik' => $nik], $data);
    if ($simpan) {
      return ['code' => '200'];
    } else {
      return ['code' => '250'];
    }
  }
}
