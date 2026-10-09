<?php

namespace App\Http\Controllers\Bunda\BelumSekolah;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use DB, Session, Auth;

class SurveyController extends Controller
{
  function halaman(Request $request)
  {
    $halaman = $request->halaman;

    switch ($halaman) {
      case 'main':
        $nik = $request->nik;

        $data_anak = DB::connection('db_cutt_off')->table('public.penduduk_inject as a')
          ->selectRaw("
            a.nik,
            a.nama_lengkap,
            a.jenis_kelamin,
            a.tempat_lahir,
            a.tgl_lahir,
            EXTRACT(YEAR FROM AGE(NOW(),a.tgl_lahir)) as usia,
            a.nama_ayah,
            a.nama_ibu,
            COALESCE(NULLIF(a.alamat_dom,''),a.alamat) as alamat,
            COALESCE(NULLIF(a.rt_dom,''),a.rt) as rt,
            COALESCE(NULLIF(a.rw_dom,''),a.rw) as rw,
            COALESCE(NULLIF(a.kelurahan_dom,''),a.kelurahan) as kelurahan,
            COALESCE(NULLIF(a.kecamatan_dom,''),a.kecamatan) as kecamatan,
            a.desil_nasional
          ")
          ->where('a.nik', $nik)
          ->first();

        $data_ttd = $this->get_ttd($request);
        $ttd = $data_ttd['ttd'] ?? [];
        $nik_bunda = $data_ttd['nik_bunda'] ?? [];
        $user_bunda = $data_ttd['user_bunda'] ?? [];
        $nama_bunda = $data_ttd['nama_bunda'] ?? [];

        $tingkat = Auth::getUser()->tingkat;

        $petugas = DB::table('public.users as u')
          ->join('public.jabatan_pokja as j', 'u.id_jabatan', 'j.id')
          ->join('public.profil_pokja as p', 'p.updated_by', 'u.id')
          ->when($tingkat !== '', function ($w) use ($tingkat) {
            if ($tingkat == 'kecamatan') {
              return $w->join('public.kecamatan as kec', 'kec.kecamatan_id', 'u.id_kecamatan')
                ->addSelect(
                  DB::raw("'' as kelurhan_kode"),
                  DB::raw("'' as kelurahan_dispenduk"),
                  'kec.kecamatan_kode',
                  'kec.kecamatan_dispenduk'
                );
            } else if ($tingkat == 'kelurahan') {
              return $w->join('public.kelurahan as kel', 'kel.kelurahan_id', 'u.id_kelurahan')
                ->join('public.kecamatan as kec', 'kec.kecamatan_kode', 'kel.kecamatan_kode')
                ->addSelect(
                  'kel.kelurahan_kode',
                  'kel.kelurahan_dispenduk',
                  'kec.kecamatan_kode',
                  'kec.kecamatan_dispenduk'
                );
            }
          })
          ->selectRaw("u.nama as nama_user,j.nama_jabatan,p.nama,p.no_telp")
          ->where('u.id', Auth::getUser()->id)
          ->whereIn('p.id_jabatan', [3, 33, 18])
          ->whereNull('p.deleted_at')
          ->first();

        $survey = DB::connection('db_cutt_off')->table('public.survey_bunda as s')
          ->leftJoin('public.penduduk_inject as p', 's.nik', 'p.nik')
          ->selectRaw("
            s.*,
            p.nama_lengkap,
            p.jenis_kelamin,
            p.tempat_lahir,
            p.tgl_lahir,
            EXTRACT(YEAR FROM AGE(NOW(),p.tgl_lahir)) as usia,
            p.nama_ayah,
            p.nama_ibu,
            p.alamat_dom,
            p.rt_dom,
            p.rw_dom,
            p.kelurahan_dom,
            p.kecamatan_dom
          ")
          ->where('s.nik', $nik)
          ->first();

        $data = [
          'anak' => $data_anak,
          'petugas' => $petugas,
          'survey' => $survey,
          'ttd' => $ttd,
          'nama_bunda' => $nama_bunda,
          'nik_bunda' => $nik_bunda,
          'user_bunda' => $user_bunda,
        ];
        return view('bunda.belum_sekolah.halaman_survey.main', $data);
        break;

      case 'cari_sekolah':
        $cari = $request->cari;
        $sekolah = DB::connection('pgsql_paud')->table('public.sekolah as s')
          ->join('public.master_user as u', 'u.username', 's.npsn')
          ->leftJoin('public.kecamatan as kec', 's.kec_id', 'kec.kecamatan_kode')
          ->leftJoin('public.kelurahan as kel', 's.desa', 'kel.kelurahan_kode')
          ->selectRaw("s.nama,s.npsn,s.jenjang,
            CONCAT(
              s.alamat,
              ', RT ',s.rt,
              ', RW ',s.rw,
              ', Kel. ',kel.kelurahan_dispenduk,
              ', Kec. ',kec.kecamatan_dispenduk
            ) as alamat,
            kec.kecamatan_dispenduk,kel.kelurahan_dispenduk")
          ->where('u.user_enable', true)
          ->when($cari, function ($w) use ($cari) {
            return $w->where('s.nama', 'ilike', '%' . $cari . '%')->orWhere('s.npsn', 'ilike', '%' . $cari . '%');
          })
          ->whereIn('s.jenjang', ['TK', 'RA', 'KB', 'SPS', 'TPA'])
          ->orderByRaw("s.nama ASC,LENGTH(s.nama) ASC")
          ->limit(10)
          ->get();

        return $sekolah;
        break;

      case 'simpan':
        // return $request->all();
        $nik = $request->nik;

        $data_ttd = $this->get_ttd($request);
        $ttd = $data_ttd['ttd'] ?? [];
        $nik_bunda = $data_ttd['nik_bunda'] ?? [];
        $user_bunda = $data_ttd['user_bunda'] ?? [];
        $nama_bunda = $data_ttd['nama_bunda'] ?? [];

        $data_kecamatan = collect($ttd)->where('nama_unit', $user_bunda[0])->first();
        $data_kelurahan = collect($ttd)->where('nama_unit', $user_bunda[1])->first();

        $tanggal_kunjungan = $request->tanggal_kunjungan ?? '';
        $nama_bunda_paud = $request->nama_bunda_paud ?? '';
        $unsur_jabatan = $request->unsur_jabatan ?? '';
        $nomor_hp_petugas = $request->nomor_hp_petugas ?? '';
        $kelurahan_petugas = $request->kelurahan_petugas ?? '';
        $kecamatan_petugas = $request->kecamatan_petugas ?? '';

        $nomor_hp_orang_tua = $request->nomor_hp_orang_tua ?? '';

        $domisili = $request->domisili ?? ''; //Status_alamat
        $detail_alamat = $this->detail_survey_alamat($request, $domisili);

        $status_paud = $request->status_paud ?? '';
        $detail_sekolah = $this->detail_survey_sekolah($request, $status_paud);

        $selected_factors = implode('|', $request->selected_factors ?? []);
        $detail_tidak_sekolah = $selected_factors;
        $catatan_tidak_sekolah = $this->detail_tidak_sekolah($request);

        $edukasi_ortu = $this->edukasi_ortu($request)['edukasi'];
        $tanggapan_ortu = $this->edukasi_ortu($request)['tanggapan'];

        $rtl = $this->rtl($request)['rtl'];
        $abk = $this->rtl($request)['abk'];

        $dokumentasi = $request->link_google_drive ?? '';

        $data = [
          'nik' => $nik,
          'updated_at' => date('Y-m-d H:i:s'),
          'updated_by' => Auth::getUser()->nama,
          'status_alamat' => $domisili,
          'detail_alamat' => $detail_alamat,
          'status_sekolah' => $status_paud,
          'detail_sekolah' => $detail_sekolah,
          'detail_tidak_sekolah' => $detail_tidak_sekolah,
          'pilihan_detail_tidak_sekolah' => $catatan_tidak_sekolah,
          'bukti_survei' => $dokumentasi,
          'tgl_survey' => date('Y-m-d', strtotime($tanggal_kunjungan)),
          'nama_petugas' => $nama_bunda_paud,
          'jabatan_petugas' => $unsur_jabatan,
          'telp_petugas' => $nomor_hp_petugas,
          'kelurahan' => $kelurahan_petugas,
          'kecamatan' => $kecamatan_petugas,
          'telp_anak' => $nomor_hp_orang_tua,
          'edukasi_ortu' => $edukasi_ortu,
          'tanggapan_ortu' => $tanggapan_ortu,
          'tindak_lanjut' => $rtl,
          'penanganan_abk' => $abk,
          'bunda_kelurahan' => $nama_bunda[1],
          'bunda_kecamatan' => $nama_bunda[0],
          'ttd_kelurahan' => $data_kelurahan->link_ttd ?? '',
          'ttd_kecamatan' => $data_kecamatan->link_ttd ?? '',
          'status_verifikasi' => 'Selesai',
        ];

        $simpan = DB::connection('db_cutt_off')->table('public.survey_bunda')->updateOrInsert(['nik' => $nik], $data);
        if ($simpan) {
          return ['code' => 200];
        } else {
          return ['code' => 230];
        }
        break;

      default:
        return '404 - Halaman tidak ditemukan';
        break;
    }
  }

  function detail_survey_alamat(Request $request, $domisili)
  {
    $alamat_baru_dalam_kota = $request->alamat_baru_dalam_kota ?? '';
    $alamat_baru_luar_kota = $request->alamat_baru_luar_kota ?? '';
    $alasan_meninggal = $request->alasan_meninggal ?? '';
    $alasan_pindah_luar = $request->alasan_pindah_luar ?? '';
    $domisili_lainnya = $request->domisili_lainnya ?? '';
    $informasi_tidak_ditemukan = $request->informasi_tidak_ditemukan ?? '';
    $kab_kota_baru = $request->kab_kota_baru ?? '';
    $penjelasan_data_ganda = $request->penjelasan_data_ganda ?? '';
    $tanggal_meninggal = $request->tanggal_meninggal ?? '';

    switch ($domisili) {
      case 'pindah_dalam':
        $detail_alamat = '<b>Alamat baru</b> = ' . $alamat_baru_dalam_kota;
        break;
      case 'pindah_luar':
        $detail_alamat = '<b>Alamat baru</b> = ' . $alamat_baru_luar_kota . ', <br><br><b>Kab/Kota</b> = ' . $kab_kota_baru . ', <br><br><b>Alasan pindah</b> = ' . $alasan_pindah_luar;
        break;
      case 'tidak_ditemukan':
        $detail_alamat = '<b>Catatan</b> = ' . $informasi_tidak_ditemukan;
        break;
      case 'data_ganda':
        $detail_alamat = '<b>Catatan</b> = ' . $penjelasan_data_ganda;
        break;
      case 'meninggal':
        $detail_alamat = '<b>Alasan meninggal</b> = ' . $alasan_meninggal . ', <br><br><b>Pada tanggal</b> = ' . $tanggal_meninggal;
        break;
      case 'lainnya':
        $detail_alamat = '<b>Catatan</b> = ' . $domisili_lainnya;
        break;

      default:
        $detail_alamat = '';
        break;
    }
    return $detail_alamat;
  }

  function detail_survey_sekolah(Request $request, $status_paud)
  {
    $alamat_satuan_saat_ini = $request->alamat_satuan_saat_ini ?? '';
    $alamat_satuan_tujuan = $request->alamat_satuan_tujuan ?? '';
    $alasan_pilihan_paud = $request->alasan_pilihan_paud ?? '';
    $nama_satuan_saat_ini = $request->nama_satuan_saat_ini ?? '';
    $nama_satuan_tujuan = $request->nama_satuan_tujuan ?? '';
    $npsn_saat_ini = $request->npsn_saat_ini ?? '';
    $npsn_tujuan = $request->npsn_tujuan ?? '';
    $wilayah_paud_saat_ini = $request->wilayah_paud_saat_ini ?? '';
    $wilayah_paud_tujuan = $request->wilayah_paud_tujuan ?? '';
    $punya_gambaran_sekolah = $request->punya_gambaran_sekolah ?? '';
    $jenis_layanan = $request->jenis_layanan ?? '';

    switch ($status_paud) {
      case 'sudah_paud':
        if ($wilayah_paud_saat_ini == 'luar_kota') {
          $detail_sekolah = '<b>Lokasi</b> = Luar Surabaya<br>';
          $detail_sekolah .= '<b>Nama Sekolah</b> = ' . $nama_satuan_saat_ini . '<br>';
          $detail_sekolah .= '<b>Alamat</b> = ' . $alamat_satuan_saat_ini;
        } else {
          $detail_sekolah = '<b>Lokasi</b> = Dalam Surabaya<br>';
          $detail_sekolah .= '<b>Nama Sekolah</b> = ' . $nama_satuan_saat_ini . ' (' . $npsn_saat_ini . ')<br>';
          $detail_sekolah .= '<b>Alamat</b> = ' . $alamat_satuan_saat_ini;
        }

        break;
      case 'akan_daftar':
        $detail_sekolah = '';
        $detail_sekolah .= '<b>Sudah ada gambaran</b> = ' . $punya_gambaran_sekolah . '<br>';
        if ($wilayah_paud_tujuan == 'luar_kota') {
          $detail_sekolah .= '<b>Lokasi</b> = Luar Surabaya<br>';
          $detail_sekolah .= '<b>Nama Sekolah</b> = ' . $nama_satuan_tujuan . '<br>';
          $detail_sekolah .= '<b>Alamat</b> = ' . $alamat_satuan_tujuan . '<br>';
        } else {
          $detail_sekolah .= '<b>Lokasi</b> = Dalam Surabaya<br>';
          $detail_sekolah .= '<b>Nama Sekolah</b> = ' . $nama_satuan_tujuan . '(' . $npsn_tujuan . ')<br>';
          $detail_sekolah .= '<b>Alamat</b> = ' . $alamat_satuan_tujuan . '<br>';
        }
        $detail .= '<b>Alasan pilihan</b> = ' . $alasan_pilihan_paud;
        break;

      default:
        $detail_sekolah = '';
        break;
    }
    return $detail_sekolah;
  }

  function detail_tidak_sekolah(Request $request)
  {
    $selected_factors = $request->selected_factors ?? [];

    $catatan_tidak = '';

    if (in_array('Faktor 1: Ekonomi & Kerentanan', $selected_factors)) {
      $f1_catatan = $request->f1_catatan ?? '';
      $f1_desil = $request->f1_desil ?? '';
      $catatan_tidak .= '<b>Faktor 1: Ekonomi & Kerentanan</b><br>';
      $catatan_tidak .= 'Desil: ' . $f1_desil . '<br>';
      $catatan_tidak .= 'Catatan: ' . $f1_catatan . '<br><br>';
    }

    if (in_array('Faktor 2: Pengasuhan & Dukungan', $selected_factors)) {
      $f2_catatan = $request->f2_catatan ?? '';
      $f2_options = $request->f2_options ?? [];

      $t_f2_options = '';
      if (count($f2_options) > 0) {
        $t_f2_options = '-';
        $t_f2_options .= implode(';<br>-', $f2_options);
        $t_f2_options .= ';';
      }

      $catatan_tidak .= '<b>Faktor 2: Pengasuhan & Dukungan</b><br>';
      $catatan_tidak .= 'Options: <br>' . $t_f2_options . '<br>';
      $catatan_tidak .= 'Catatan: ' . $f2_catatan . '<br><br>';
    }

    if (in_array('Faktor 3: Kondisi Anak & Kesehatan', $selected_factors)) {
      $f3_catatan = $request->f3_catatan ?? '';
      $f3_options = $request->f3_options ?? [];

      $t_f3_options = '';
      if (count($f3_options) > 0) {
        $t_f3_options = '-';
        $t_f3_options .= implode(';<br>-', $f3_options);
        $t_f3_options .= ';';
      }

      $catatan_tidak .= '<b>Faktor 3: Kondisi Anak & Kesehatan</b><br>';
      $catatan_tidak .= 'Options: <br>' . $t_f3_options . '<br>';
      $catatan_tidak .= 'Catatan: ' . $f3_catatan . '<br><br>';
    }

    if (in_array('Faktor 4: Administrasi', $selected_factors)) {
      $f4_catatan = $request->f4_catatan ?? '';
      $f4_options = $request->f4_options ?? [];

      $t_f4_options = '';
      if (count($f4_options) > 0) {
        $t_f4_options = '-';
        $t_f4_options .= implode(';<br>-', $f4_options);
        $t_f4_options .= ';';
      }

      $catatan_tidak .= '<b>Faktor 4: Administrasi</b><br>';
      $catatan_tidak .= 'Options: <br>' . $t_f4_options . '<br>';
      $catatan_tidak .= 'Catatan: ' . $f4_catatan . '<br><br>';
    }

    if (in_array('Faktor 5: Lainnya', $selected_factors)) {
      $f5_detail = $request->f5_detail ?? '';
      $catatan_tidak .= '<b>Faktor 5: Lainnya</b><br>';
      $catatan_tidak .= 'Catatan: ' . $f5_detail . '<br><br>';
    }

    return $catatan_tidak;
  }

  function edukasi_ortu(Request $request)
  {
    $edukasi = $request->edukasi ?? [];
    $edukasi_lainnya = $request->edukasi_lainnya ?? '';
    $edukasi_lainnya = $edukasi_lainnya != '' ? array_push($edukasi, $edukasi_lainnya) : '';

    $t_edukasi = '';
    if (count($edukasi) > 0) {
      $t_edukasi = '-';
      $t_edukasi .= implode(';<br>-', $edukasi);
      $t_edukasi .= ';';
    }

    $tanggapan_edukasi = $request->tanggapan_edukasi ?? '';
    $tanggapan_lainnya = $request->tanggapan_lainnya ?? '';
    $tanggapan_edukasi = $tanggapan_lainnya != '' ? $tanggapan_lainnya : $tanggapan_edukasi;

    return ['edukasi' => $t_edukasi, 'tanggapan' => $tanggapan_edukasi];
  }

  function rtl(Request $request)
  {
    $lokasi_rap = $request->lokasi_rap ?? [];
    $rtl = $request->rtl ?? [];
    $rtl_detail = $request->rtl_detail ?? [];
    $rtl_lainnya = $request->rtl_lainnya ?? '';

    $rtl = implode('|', $rtl);

    $abk = '';
    $t_rtl_detail = '-';
    if (count($rtl_detail) > 0) {
      $t_rtl_detail .= implode(';<br>-', $rtl_detail);
      $t_rtl_detail .= ';';
      $abk .= '<br><b>Penangan ABK</b>: <br>' . $t_rtl_detail;
    }

    $t_lokasi_rap = '-';
    if (count($lokasi_rap) > 0) {
      $t_lokasi_rap .= implode(';<br>-', $lokasi_rap);
      $t_lokasi_rap .= ';';
      $abk .= '<br><b>Rujukan ABK</b>: <br>' . $t_lokasi_rap;
    }

    $abk .= $rtl_lainnya != '' ? '<br><b>Catatan</b> : ' . $rtl_lainnya : '';

    return ['rtl' => $rtl, 'abk' => $abk];
  }

  function get_ttd(Request $request)
  {
    $nik = $request->nik;

    $data_anak = DB::connection('db_cutt_off')->table('public.penduduk_inject as a')
      ->selectRaw("
            a.nik,
            a.nama_lengkap,
            a.jenis_kelamin,
            a.tempat_lahir,
            a.tgl_lahir,
            EXTRACT(YEAR FROM AGE(NOW(),a.tgl_lahir)) as usia,
            a.nama_ayah,
            a.nama_ibu,
            COALESCE(NULLIF(a.alamat_dom,''),a.alamat) as alamat,
            COALESCE(NULLIF(a.rt_dom,''),a.rt) as rt,
            COALESCE(NULLIF(a.rw_dom,''),a.rw) as rw,
            COALESCE(NULLIF(a.kelurahan_dom,''),a.kelurahan) as kelurahan,
            COALESCE(NULLIF(a.kecamatan_dom,''),a.kecamatan) as kecamatan,
            a.desil_nasional
          ")
      ->where('a.nik', $nik)
      ->first();

    $bunda_kecamatan = DB::table('public.users as u')
      ->join('public.profil_pokja as p', 'p.updated_by', 'u.id')
      ->join('public.kecamatan as kec', 'kec.kecamatan_id', 'u.id_kecamatan')
      ->selectRaw("UPPER(u.nama) as nama_user,u.tingkat,p.nama,p.nik")
      ->where(function ($w) use ($data_anak) {
        return $w->where('kec.kecamatan_dispenduk', 'ilike', '%' . $data_anak->kecamatan . '%')
          ->orWhere('kec.dispenduk_baru', 'ilike', '%' . $data_anak->kecamatan . '%');
      })
      ->where('u.verified', true)
      ->where('p.id_jabatan', '18')
      ->whereNull('p.deleted_at')
      ->whereNull('u.deleted_at')
      ->orderByRaw("LENGTH(kec.kecamatan_dispenduk)")
      ->first();

    $bunda_kelurahan = DB::table('public.users as u')
      ->join('public.profil_pokja as p', 'p.updated_by', 'u.id')
      ->join('public.kelurahan as kel', 'kel.kelurahan_id', 'u.id_kelurahan')
      ->join('public.kecamatan as kec', 'kec.kecamatan_kode', 'kel.kecamatan_kode')
      ->selectRaw("UPPER(u.nama) as nama_user,u.tingkat,p.nama,p.nik")
      ->where(function ($w) use ($data_anak) {
        return $w->where('kel.kelurahan_dispenduk', 'ilike', '%' . $data_anak->kelurahan . '%')
          ->orWhere('kel.dispenduk_baru', 'ilike', '%' . $data_anak->kelurahan . '%');
      })
      ->where(function ($w) use ($data_anak) {
        return $w->where('kec.kecamatan_dispenduk', 'ilike', '%' . $data_anak->kecamatan . '%')
          ->orWhere('kec.dispenduk_baru', 'ilike', '%' . $data_anak->kecamatan . '%');
      })
      ->where('u.verified', true)
      ->where('p.id_jabatan', '33')
      ->whereNull('p.deleted_at')
      ->whereNull('u.deleted_at')
      ->orderByRaw("LENGTH(kel.kelurahan_dispenduk)")
      ->first();

    $nik_bunda = [$bunda_kecamatan->nik ?? '', $bunda_kelurahan->nik ?? ''];
    $user_bunda = [$bunda_kecamatan->nama_user ?? '', $bunda_kelurahan->nama_user ?? ''];
    $nama_bunda = [$bunda_kecamatan->nama ?? '', $bunda_kelurahan->nama ?? ''];
    $ttd = DB::connection('db_cutt_off')->table('public.api_bunda_paud')
      ->where(function ($w) use ($nik_bunda, $user_bunda) {
        return $w->whereIn('nik', $nik_bunda)
          ->orWhereIn('nama_unit', $user_bunda);
      })
      ->get();

    return [
      'nik_bunda' => $nik_bunda,
      'user_bunda' => $user_bunda,
      'nama_bunda' => $nama_bunda,
      'ttd' => $ttd
    ];
  }
}
