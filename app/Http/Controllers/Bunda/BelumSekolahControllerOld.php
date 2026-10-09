<?php

namespace App\Http\Controllers\Bunda;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Tools\Bantuan;
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
    $status_alamat = $request->status_alamat;
    $status_api = $request->status_api;
    $status_sekolah = $request->status_sekolah;
    $status_verifikasi = $request->status_verifikasi;

    $where_cari = "status='$status_api'";
    if ($status_alamat != 'Semua') {
      $where_cari .= " AND status_alamat='$status_alamat'";
    }
    if ($status_sekolah != 'Semua') {
      $where_cari .= " AND status_sekolah='$status_sekolah'";
    }
    if ($status_verifikasi != 'Semua') {
      $where_cari .= " AND status_verifikasi='$status_verifikasi'";
    }

    $tingkat = Auth::getUser()->tingkat;
    $where = '';
    if ($tingkat == 'kecamatan') {
      $kecamatan = DB::table('public.kecamatan')->where('kecamatan_id', Auth::getUser()->id_kecamatan)->first()->kecamatan_dispenduk;
      $where = " AND kecamatan ilike '%$kecamatan%'";
    } else if ($tingkat == 'kelurahan') {
      $kelurahan = DB::table('public.kelurahan')->where('kelurahan_id', Auth::getUser()->id_kelurahan)->first()->kelurahan_dispenduk;
      $where = " AND kelurahan ilike '%$kelurahan%'";
    }

    $siswa = DB::connection('pgsql_paud')->table('history.anak_belum_sekolah')->whereRaw("$where_cari $where")->get();
    return DataTables()->of($siswa)->addIndexColumn()
      ->addColumn('status_verif', function ($akun) {
        $button = (is_null($akun->status_verifikasi)) ? 'Belum Verifikasi' : $akun->status_verifikasi;
        return $button;
      })->addColumn('aksi', function ($akun) {
        $button = '<a href="javascript:void(0)" class="btn btn-sm btn-primary" onclick="ganti(\'' . $akun->nik . '\')">Verifikasi</a>';

        return $button;
      })->addColumn('detail_alasan', function ($akun) {
        $button = '';
        if (!is_null($akun->detail_alamat)) {
          $button .= '<b>Alasan alamat tidak sesuai KK:</b><br>';
          $button .= $akun->detail_alamat . '<br><br>';
        }
        if (!is_null($akun->detail_sekolah)) {
          if ($akun->status_sekolah == 'Sudah') {
            $button .= '<b>Bersekolah di:</b><br>';
          } else {
            $button .= '<b>Akan bersekolah di:</b><br>';
          }
          $button .= $akun->detail_sekolah . '<br><br>';
        }
        if (!is_null($akun->detail_tidak_sekolah)) {
          $button .= '<b>Alasan tidak Bersekolah:</b><br>';
          $button .= $akun->detail_tidak_sekolah;
          if (!is_null($akun->pilihan_detail_tidak_sekolah)) {
            $button .= ' ( ' . $akun->pilihan_detail_tidak_sekolah . ' )<br><br>';
          }
        }

        return $button;
      })->addColumn('tombol_bukti_survei', function ($akun) {
        $button = '';
        if (!is_null($akun->bukti_survei)) {
          $button = '<a href="' . $akun->bukti_survei . '" target="_blank" class="btn btn-sm btn-warning" >Lihat</a>';
        }

        return $button;
      })
      ->rawColumns(['aksi', 'detail_alasan', 'tombol_bukti_survei'])
      ->toJson();
  }

  function ganti(Request $request)
  {
    $nik = $request->nik;

    $siswa = DB::connection('pgsql_paud')->table('history.anak_belum_sekolah')->where('nik', $nik)->first();

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
    $simpan = DB::connection('pgsql_paud')->table('history.anak_belum_sekolah')->where('nik', $nik)->update($data);
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
