<?php

namespace App\Http\Controllers\Bunda;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CommonController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\JabatanPokja;
use App\Models\ProfilSekolah\Sekolah;
use App\Models\ProfilSekolah\Siswa;
use App\Models\Edu\Jadwal;
use App\Models\Edu\Pendamping;
use App\Models\Edu\Tempat;
use App\Http\Libraries\GetData;
use App\Models\Fasilitasi\Jadwal as FasilitasiJadwal;
use DataTables, DB, Session, Auth, Storage, Str;
use App\Tools\Bantuan;
use Carbon\Carbon;

class TalentaController extends Controller
{
  function __construct()
  {
    date_default_timezone_set('Asia/Jakarta');
  }

  function index()
  {
    return view('bunda.talenta.index');
  }

  function load_page(Request $request)
  {
    $jenis = $request->jenis;

    if ($jenis == 'penjadwalan') {
      $content = $this->load_penjadwalan($request);
    } else if ($jenis == 'dokumen') {
      $content = $this->load_dokumen($request);;
    }
    return $content;
  }

  function load_penjadwalan()
  {
    $th = DB::table('talenta_seni_jadwal')->selectRaw("tahun")->whereNull('deleted_at')->whereNotNull('tanggal')->groupBy('tahun')->get();
    if ($th->count() == 0) {
      $th = [];
    }
    $kecamatan = [];
    $kelurahan = [];
    $kode_kecamatan = 'semua';
    $kode_kelurahan = 'semua';
    $jenjang = 'semua';

    $kecamatan = DB::connection('pgsql_paud')->table('public.kecamatan')->orderBy('kecamatan_dispenduk', 'ASC')->get();
    if (Auth::getUser()->tingkat == 'kelurahan') {
      $gk = DB::connection('pgsql_paud')->table('public.kelurahan')->where('kelurahan_id', Auth::getUser()->id_kelurahan)->first();
      $kelurahan = GetData::get_kelurahan($gk->kecamatan_kode);
      $kode_kelurahan = $gk->kelurahan_kode;
      $kode_kecamatan = $gk->kecamatan_kode;
    } else {
      if (Auth::getUser()->tingkat == 'kecamatan') {
        $gk = DB::connection('pgsql_paud')->table('public.kecamatan')->where('kecamatan_id', Auth::getUser()->id_kecamatan)->first();
        $kode_kecamatan = $gk->kecamatan_kode;
      }
    }

    if (Auth::getUser()->role == 'opd') {
      $wisata = DB::table('public.talenta_seni_tempat')->whereraw("id_opd ilike '%\"" . Auth::getUser()->id . "\"%'")->orderBy('nama', 'ASC')->get();
    } else {
      $wisata = DB::table('public.talenta_seni_tempat')->orderBy('nama', 'ASC')->get();
    }

    $data = [
      'thn' => $th,
      'tahun' => date('Y'),
      'kecamatan' => $kecamatan,
      'kelurahan' => $kelurahan,
      'kode_kecamatan' => $kode_kecamatan,
      'kode_kelurahan' => $kode_kelurahan,
      'jenjang' => $jenjang,
      'wisata' => $wisata,
    ];

    $content = view('bunda.talenta.penjadwalan', $data)->render();
    return ['content' => $content];
  }

  function load_dokumen()
  {
    $content = view('bunda.talenta.dokumen')->render();
    return ['content' => $content];
  }

  function get_data(Request $request)
  {
    // return $request->all();
    $tahun = $request->tahun;
    $kec = $request->kec;
    // $kel = $request->kel;
    $jenjang = $request->jenjang;
    $wisata = $request->wisata;
    $bulan = $request->bulan;
    $status = $request->status;

    $where_lokasi = "";
    $where = "true";

    if ($status != 'Semua') {
      $where .= " AND jadwal.status_jadwal='$status'";
    }

    if ($kec != 'semua') {
      $kecamatan = DB::connection('pgsql_paud')->table('public.kecamatan')->where('kecamatan_kode', $kec)->first();
      $where .= " AND jadwal.kecamatan='$kecamatan->kecamatan_dispenduk'";
    }
    // if ($kel != 'semua') {
    //     $where_lokasi .= " AND desa='$kel'";
    // }
    if ($jenjang != 'semua') {
      $where_lokasi .= " AND sekolah.jenjang='$jenjang'";
    }

    if ($bulan == 'semua') {
      $where .= " AND (CAST(jadwal.tanggal as varchar) ilike '%$tahun-%' OR jadwal.tanggal is null)";
    } else {
      $where .= " AND CAST(jadwal.tanggal as varchar) ilike '%$tahun-$bulan%'";
    }

    if ($wisata != 'semua') {
      $where .= " AND jadwal.tempat_id='$wisata'";
    }

    $where_opd = [];
    if (Auth::getUser()->role == 'opd') {
      $opd = DB::table('public.talenta_seni_tempat')->whereRaw("id_opd ilike '%\"" . Auth::getUser()->id . "\"%'")->get();
      $where_opd = $opd->pluck('id')->toArray();
    }

    $sekolah = Sekolah::on('pgsql_paud')->join('public.master_user as u', 'u.npsn', 'sekolah.npsn')
      ->leftjoin('public.kecamatan as kec', 'kec.kecamatan_kode', 'sekolah.kec_id')
      ->leftjoin('public.kelurahan as kel', 'kel.kelurahan_kode', 'sekolah.desa')
      ->selectRaw("sekolah.npsn,sekolah.nama,kec.kecamatan_dispenduk,kel.kelurahan_dispenduk")->whereRaw("u.user_enable is true $where_lokasi")->get();
    $npsn = $sekolah->pluck('npsn');

    if ($tahun == '') {
      $bkb = [];
    } else {
      $bkb = DB::table('public.talenta_seni_jadwal as jadwal')
        ->leftjoin('public.talenta_seni_tempat as tempat', 'tempat.id', 'jadwal.tempat_id')
        ->selectRaw("jadwal.*,tempat.nama as nama_tempat,tempat.alamat as alamat_tempat,tempat.htm as htm_tempat")
        ->whereRaw("$where")
        ->whereNull('jadwal.deleted_at')
        // ->whereIn('jadwal.kecamatan', $sekolah->pluck('kecamatan_dispenduk'))
        // ->whereIn('jadwal.kelurahan', $sekolah->pluck('kelurahan_dispenduk'))
      ;

      if (Auth::getUser()->role == 'opd') {
        if (count($where_opd) != 0) {
          $bkb = DB::table('public.talenta_seni_jadwal as jadwal')
            ->leftjoin('public.talenta_seni_tempat as tempat', 'tempat.id', 'jadwal.tempat_id')
            ->selectRaw("jadwal.*,tempat.nama as nama_tempat,tempat.alamat as alamat_tempat,tempat.htm as htm_tempat")
            ->whereRaw("$where")
            ->whereNull('jadwal.deleted_at')
            ->whereIn('tempat_id', $where_opd)
            // ->whereIn('kecamatan', $sekolah->pluck('kecamatan_dispenduk'))
            // ->whereIn('kelurahan', $sekolah->pluck('kelurahan_dispenduk'))
          ;
        } else {
          $bkb = [];
        }
      }
    }

    return DataTables()->queryBuilder($bkb)
      ->addIndexColumn()
      ->addColumn('npsn', function ($akun) {
        $npsn = $akun->npsn;
        return $npsn;
      })
      ->addColumn('nama_tempat', function ($akun) {
        $nama_tempat = $akun->nama_tempat ? $akun->nama_tempat : '<b class="badge badge-danger">X</b>';
        return $nama_tempat;
      })
      ->addColumn('alamat_tempat', function ($akun) {
        $alamat_tempat = $akun->alamat_tempat ? $akun->alamat_tempat : '<b class="badge badge-danger">X</b>';
        return $alamat_tempat;
      })
      ->addColumn('no_hp_penanggung_jawab', function ($akun) {
        $no_hp_penanggung_jawab = $akun->no_hp_penanggung_jawab;
        return '`' . $no_hp_penanggung_jawab;
      })
      ->addColumn('nama_sekolah', function ($akun) use ($sekolah) {
        // $data_sekolah = $sekolah->where('npsn', $akun->npsn)->first();
        $nama_sekolah = $akun->nama_sekolah;
        // $akun->nama_kecamatan = (!empty($data_sekolah)) ? $data_sekolah->kecamatan_dispenduk : '';
        // $akun->nama_kelurahan = (!empty($data_sekolah)) ? $data_sekolah->kelurahan_dispenduk : '';
        return $nama_sekolah;
      })
      ->filterColumn('nama_sekolah', function ($akun, $keyword) use ($sekolah) {
        $data_sekolah = collect($sekolah)->filter(function ($item) use ($keyword) {
          $nama = data_get($item, 'nama', '');

          // Ubah kedua string ke huruf kecil agar case-insensitive
          return Str::contains(Str::lower($nama), Str::lower($keyword));
        })->pluck('npsn')->toArray();

        return $akun->whereIn('jadwal.npsn', $data_sekolah);
      })
      ->addColumn('nama_kecamatan', function ($akun) {
        return $akun->kecamatan;
      })
      ->addColumn('nama_kelurahan', function ($akun) {
        return $akun->kelurahan;
      })
      ->addColumn('pendampings', function ($akun) {
        $pendamping = DB::table('public.talenta_seni_pendamping')->where('jadwal_id', $akun->id)->orderBy('id', 'ASC')->get();
        $namanya = '';
        // foreach($pendamping as $p){
        // 	$namanya .= $p->nama_pegawai.' <b>('.$p->jabatan.')</b><br><br>';
        // }

        return $pendamping->count();
      })
      ->addColumn('tanggal_pelaksanaan', function ($akun) {
        $tgl = $akun->tanggal ? date('d-m-Y', strtotime($akun->tanggal)) : '<b class="badge badge-danger">X</b>';

        return $tgl;
      })
      ->addColumn('jam_mulai', function ($akun) {
        $tgl = $akun->jam_mulai ? date('H:i', strtotime($akun->jam_mulai)) : '<b class="badge badge-danger">X</b>';

        return $tgl;
      })
      ->addColumn('jam_selesai', function ($akun) {
        $tgl = $akun->jam_selesai ? date('H:i', strtotime($akun->jam_selesai)) : '<b class="badge badge-danger">X</b>';

        return $tgl;
      })
      ->addColumn('berkas', function ($akun) use ($tahun) {
        $button = '';
        if ($akun->file_surat_permohonan != '') {
          $button .= '<a style="margin-left: 5px" href="https://dispendik.surabaya.go.id/profilsekolah_file/' . $akun->file_surat_permohonan . '" target="_blank" class="btn btn-sm btn-primary">Surat Permohonan</a>';
        } else {
          $button .= 'Belum upload permohonan';
        }

        if ($akun->file_surat_permohonan_dinas != '') {
          $button .= '<br> <a style="margin-left: 5px" href="https://dispendik.surabaya.go.id/profilsekolah_file/' . $akun->file_surat_permohonan_dinas . '" target="_blank" class="btn btn-sm btn-primary">Surat Permohonan Dinas</a>';
        } else {
          $button .= '<br> Belum upload permohonan Dinas';
        }

        if ($akun->file_surat_kehadiran != '') {
          $button .= '<br> <a style="margin-left: 5px" href="https://dispendik.surabaya.go.id/profilsekolah_file/' . $akun->file_surat_kehadiran . '" target="_blank" class="btn btn-sm btn-primary">Daftar kehadiran</a>';
        } else {
          $button .= '<br> Belum upload daftar kehadiran';
        }

        if ($akun->file_surat_tugas != '') {
          $button .= '<br> <a style="margin-left: 5px" href="https://dispendik.surabaya.go.id/profilsekolah_file/' . $akun->file_surat_tugas . '" target="_blank" class="btn btn-sm btn-primary">Surat Tugas</a>';
        } else {
          $button .= '<br> Belum upload File Surat Tugas';
        }

        if ($akun->file_laporan_kegiatan != '') {
          $button .= '<br> <a style="margin-left: 5px" href="https://dispendik.surabaya.go.id/profilsekolah_file/' . $akun->file_laporan_kegiatan . '" target="_blank" class="btn btn-sm btn-primary">Laporan Kegiatan</a>';
        } else {
          $button .= '<br> Belum upload File Laporan Kegiatan';
        }

        if ($akun->is_serah_terima_barang != '') {
          //local
          // $url = "http://profilsekolahbaru.test";
          //live
          $url = "https://profilsekolahdispendik.surabaya.go.id";
          $id = base64_encode($akun->id);
          $npsn = base64_encode($akun->npsn);
          $jenjang = base64_encode($akun->jenjang);

          //Code untuk token yang berlaku 6 jam saja
          //Untuk mengamankan akses dokumen
          $timestamp = time(); // waktu saat ini
          $secret = 'my_super_secret_key';

          $data = "$timestamp|$id|$npsn|$jenjang";
          $token = hash_hmac('sha256', $data, $secret);

          $auth = $timestamp . '-' . $token;


          $button .= '<br> <a style="margin-left: 5px" href="' . "$url/berkas-berita-acara/$id/$npsn/$jenjang/$auth" . '" target="_blank" class="btn btn-sm btn-primary">Berita Acara Serah Terima Barang</a>';
        } else {
          $button .= '<br> Belum upload Berita Acara Serah Terima Barang';
        }
        return $button;
      })
      ->addColumn('status', function ($akun) {
        $button = '';
        switch ($akun->status_jadwal) {
          case 'Setujui':
            $badge = 'badge-success';
            break;
          case 'Reschedule':
            $badge = 'badge-danger';
            break;
          default:
            $badge = 'badge-warning';
            break;
        }
        $button = '<a class="badge ' . $badge . '">' . $akun->status_jadwal . '</a>';
        return $button;
      })
      ->addColumn('aksi', function ($akun) use ($tahun) {
        $button = '';

        if ($akun->status_jadwal == 'Pengajuan' || $akun->status_jadwal == 'Setujui') {
          $button .= '<a style="margin-left: 5px" href="javascript:void(0)" onclick="modal_konfirmasi(\'' . $akun->id . '\')" class="btn btn-sm btn-warning">Konfirmasi</a>';
        }
        $button .= '<a href="' . route('bunda-talenta-data_peserta', ['id_jadwal' => $akun->id]) . '" target="_blank" class="btn btn-sm btn-primary">Data Peserta</a>';
        return $button;
      })
      ->rawColumns(['aksi', 'pendampings', 'berkas', 'status', 'tanggal_pelaksanaan', 'nama_tempat', 'jam_mulai', 'jam_selesai', 'alamat_tempat'])
      ->make(true);
  }

  function get_data_dokumen(Request $request)
  {
    $tahun = $request->tahun;
    $kelurahan = '';
    $kecamatan = '';
    if (Auth::getUser()->tingkat == 'kelurahan') {
      $kelurahan = DB::connection('pgsql_paud')->table('public.kelurahan')->where('kelurahan_id', Auth::getUser()->id_kelurahan)->first();
      $kecamatan = DB::connection('pgsql_paud')->table('public.kecamatan')->where('kecamatan_id', Auth::getUser()->id_kecamatan)->first();
      $kecamatan = $kecamatan->kecamatan_dispenduk;
      $kelurahan = $kelurahan->kelurahan_dispenduk;
    } else if (Auth::getUser()->tingkat == 'kecamatan') {
      $kecamatan = DB::connection('pgsql_paud')->table('public.kecamatan')->where('kecamatan_id', Auth::getUser()->id_kecamatan)->first();
      $kecamatan = $kecamatan->kecamatan_dispenduk;
    }

    $tempat = DB::table('public.talenta_seni_tempat as tempat')
      ->selectRaw("tempat.id,tempat.nama,tempat.alamat,tempat.kontak,jt.kecamatan")
      ->join('public.talenta_seni_jadwal_tempat as jt', function ($join) use ($tahun) {
        return $join->on('jt.tempat_id', '=', 'tempat.id')
          ->where('jt.tahun', $tahun);
      })
      ->where(function ($q) use ($kelurahan, $kecamatan) {
        if (Auth::getUser()->tingkat == 'kelurahan') {
          $q->where('kelurahan', $kelurahan);
        } else if (Auth::getUser()->tingkat == 'kecamatan') {
          $q->whereRaw("jt.kecamatan='$kecamatan'");
        }
      })
      ->groupByRaw("tempat.id,tempat.nama,tempat.alamat,tempat.kontak,jt.kecamatan")
      ->orderBy('nama')->get();

    $jadwal = DB::table('public.talenta_seni_jadwal as jadwal')->join('public.talenta_seni_peserta as peserta', 'jadwal.id', 'peserta.jadwal_id')
      ->selectRaw("jadwal.tanggal,jadwal.kecamatan,jadwal.tempat_id,COUNT(peserta.id) as jumlah_peserta")
      ->whereRaw("jadwal.status_jadwal='Setujui'")
      ->where('jadwal.tahun', $tahun)
      ->where(function ($q) use ($kelurahan, $kecamatan) {
        if (Auth::getUser()->tingkat == 'kelurahan') {
          $q->where('jadwal.kelurahan', $kelurahan);
        } else if (Auth::getUser()->tingkat == 'kecamatan') {
          $q->where('jadwal.kecamatan', $kecamatan);
        }
      })
      ->whereNull('jadwal.deleted_at')
      ->groupByRaw("jadwal.tanggal,jadwal.kecamatan,jadwal.tempat_id")
      ->get();

    $pendamping = DB::table('public.talenta_seni_jadwal as jadwal')->join('public.talenta_seni_pendamping as peserta', 'jadwal.id', 'peserta.jadwal_id')
      ->selectRaw("jadwal.tanggal,jadwal.kecamatan,jadwal.tempat_id,COUNT(peserta.id) as jumlah_pendamping")
      ->whereRaw("jadwal.status_jadwal='Setujui'")
      ->where('jadwal.tahun', $tahun)
      ->where(function ($q) use ($kelurahan, $kecamatan) {
        if (Auth::getUser()->tingkat == 'kelurahan') {
          $q->where('jadwal.kelurahan', $kelurahan);
        } else if (Auth::getUser()->tingkat == 'kecamatan') {
          $q->where('jadwal.kecamatan', $kecamatan);
        }
      })
      ->whereNull('jadwal.deleted_at')
      ->groupByRaw("jadwal.tanggal,jadwal.kecamatan,jadwal.tempat_id")
      ->get();

    $berkas = DB::table('public.talenta_seni_berkas_bunda')->where('tahun', $tahun)->get();

    $hasil = [];

    foreach ($tempat as $i => $t) {
      $buka_hari = DB::table('talenta_seni_jadwal_tempat')->whereNull('deleted_at')->where('tempat_id', $t->id)->where('tahun', $tahun)->where('kecamatan', $t->kecamatan)->get();
      if (Auth::getUser()->id_kecamatan != '') {
        $kecamatan = collect($buka_hari)->where('kecamatan', $kecamatan);
      }

      $kecamatan = collect($buka_hari)->unique('kecamatan');

      foreach ($kecamatan as $b) {
        $b->kecamatan_us = str_replace(' ', '_', $b->kecamatan);
        $baris = collect($jadwal)->where('kecamatan', $b->kecamatan)->where('tempat_id', $t->id);
        $baris_pendamping = collect($pendamping)->where('kecamatan', $b->kecamatan)->where('tempat_id', $t->id);
        $berkas_bunda = collect($berkas)->where('kecamatan', $b->kecamatan)->where('tempat_id', $t->id)->first();
        $data = [];
        $data['nama'] = $t->nama;
        $data['kecamatan'] = $b->kecamatan;
        $data['jumlah_peserta'] = $baris->sum('jumlah_peserta') . ' (peserta)<br>' . $baris_pendamping->sum('jumlah_pendamping') . ' (Pendamping)';

        $param = [
          'kecamatan' => $b->kecamatan_us,
          'tempat_id' => $t->id,
          'tahun' => $tahun,
        ];

        $param_panitia = array_merge($param, ['kolom' => 'berkas_panitia']);
        $param_susunan_acara = array_merge($param, ['kolom' => 'berkas_susunan_acara']);
        $param_laporan = array_merge($param, ['kolom' => 'berkas_laporan']);
        $param_panitia = json_encode($param_panitia);
        $param_susunan_acara = json_encode($param_susunan_acara);
        $param_laporan = json_encode($param_laporan);
        $param_panitia = str_replace(['"'], ['\''], $param_panitia);
        $param_susunan_acara = str_replace(['"'], ['\''], $param_susunan_acara);
        $param_laporan = str_replace(['"'], ['\''], $param_laporan);

        $data['berkas_panitia'] = '<input type="file" accept=".pdf,image/*" name="berkas_panitia_' . $t->id . '_' . $b->kecamatan_us . '" class="form-control berkas_panitia_' . $t->id . '_' . $b->kecamatan_us . '" onchange="upload_dokumen_bunda(' . $param_panitia . ')"><br>';
        $data['berkas_susunan_acara'] = '<input type="file" accept=".pdf,image/*" name="berkas_susunan_acara_' . $t->id . '_' . $b->kecamatan_us . '" class="form-control berkas_susunan_acara_' . $t->id . '_' . $b->kecamatan_us . '" onchange="upload_dokumen_bunda(' . $param_susunan_acara . ')"><br>';
        $data['berkas_laporan'] = '<input type="file" accept=".pdf,image/*" name="berkas_laporan_' . $t->id . '_' . $b->kecamatan_us . '" class="form-control berkas_laporan_' . $t->id . '_' . $b->kecamatan_us . '" onchange="upload_dokumen_bunda(' . $param_laporan . ')"><br>';

        if (!empty($berkas_bunda)) {
          if ($berkas_bunda->berkas_panitia != '') {
            $data['berkas_panitia'] .= '<a href="javascript:void(0)" onclick="lihat_file(\'' . $berkas_bunda->berkas_panitia . '\')" style="font-size: 12pt" class="btn btn-sm btn-info" target="_blank"><i class="fa fa-eye"></i> Lihat file</a>';
          }
          if ($berkas_bunda->berkas_susunan_acara != '') {
            $data['berkas_susunan_acara'] .= '<a javascript:void(0)" onclick="lihat_file(\'' . $berkas_bunda->berkas_susunan_acara . '\')" style="font-size: 12pt" class="btn btn-sm btn-info" target="_blank"><i class="fa fa-eye"></i> Lihat file</a>';
          }
          if ($berkas_bunda->berkas_laporan != '') {
            $data['berkas_laporan'] .= '<a href="javascript:void(0)" onclick="lihat_file(\'' . $berkas_bunda->berkas_laporan . '\')" style="font-size: 12pt" class="btn btn-sm btn-info" target="_blank"><i class="fa fa-eye"></i> Lihat file</a>';
          }
        }

        $data['cetak'] = '';
        foreach ($buka_hari as $buka) {
          $tgl_baris = collect($jadwal)->where('tanggal', $buka->tanggal)->where('kecamatan', $buka->kecamatan)->where('tempat_id', $t->id);
          $tgl_baris_pendamping = collect($pendamping)->where('tanggal', $buka->tanggal)->where('kecamatan', $buka->kecamatan)->where('tempat_id', $t->id);
          $data['cetak'] .= '<a href="' . route('bunda-talenta-cetak', ['kecamatan' => $buka->kecamatan, 'tempat_id' => $t->id, 'tanggal' => $buka->tanggal]) . '" target="_blank" class="btn btn-info" title="Cetak Daftar Peserta">Cetak tanggal ' . $buka->tanggal . ' ' . $tgl_baris->sum('jumlah_peserta') . ' (Peserta) ' . $tgl_baris_pendamping->sum('jumlah_pendamping') . ' (Pendamping)</a><br>';
        }

        array_push($hasil, $data);
      }
    }

    return DataTables()->of($hasil)->addIndexColumn()
      ->rawColumns(['berkas_panitia', 'berkas_susunan_acara', 'berkas_laporan', 'cetak', 'jumlah_peserta'])->make(true);
  }

  function presensi(Request $request)
  {
    $id = $request->id;
    $jadwal = Jadwal::on('pgsql_paud')
      ->join('kegiatan_pendukung.edu_city_tour_tempats as tempat', 'tempat.id', 'edu_city_tour_jadwals.tempat_id')
      ->selectRaw("edu_city_tour_jadwals.*,tempat.nama as nama_tempat,tempat.alamat as alamat_tempat,tempat.htm as htm_tempat")
      ->find($id);

    $npsn = $jadwal->npsn;
    $sekolah = Sekolah::on('pgsql_paud')->where('npsn', $npsn)->first();
    $siswa = Siswa::on('pgsql_paud')->where('npsn', $npsn)->whereRaw("status_siswa='Aktif' and alumni is not true")->get();
    $data = [
      'jadwal' => $jadwal,
      'sekolah' => $sekolah,
      'siswa' => $siswa,
    ];

    return view('bunda.edu.kehadiran', $data);
  }

  function modal_konfirmasi(Request $request)
  {
    $id = $request->id;

    $jadwal = DB::table('public.talenta_seni_jadwal as jadwal')
      ->leftjoin('public.talenta_seni_tempat as tempat', 'tempat.id', 'jadwal.tempat_id')
      ->selectRaw("jadwal.*,tempat.nama as nama_tempat,tempat.alamat as alamat_tempat,tempat.htm as htm_tempat,tempat.kapasitas_pd")
      ->whereNull('jadwal.deleted_at')
      ->where('jadwal.id', $id)->first();
    $mall = DB::table('public.talenta_seni_jadwal_tempat as jadwal')
      ->join('public.talenta_seni_tempat as tempat', 'tempat.id', 'jadwal.tempat_id')->selectRaw("tempat.id,tempat.nama,tempat.alamat")->whereRaw("jadwal.kecamatan ilike '%$jadwal->kecamatan%'")->where('jadwal.tahun', $jadwal->tahun)->orderBy('tempat.nama', 'ASC')->groupByRaw("tempat.id,tempat.nama,tempat.alamat")->get();

    $data = [
      'jadwal' => $jadwal,
      'mall' => $mall,
    ];

    $content = view('bunda.talenta.modal_konfirmasi', $data)->render();

    return ['content' => $content];
  }

  function get_jadwal(Request $request)
  {
    $tempat_id = $request->tempat_id;
    $jadwal_id = $request->jadwal_id;

    $jadwal = DB::table('public.talenta_seni_jadwal')->whereNull('deleted_at')->where('id', $jadwal_id)->first();
    $tempat = DB::table('public.talenta_seni_tempat')->where('id', $tempat_id)->first();


    $hari_buka = '';
    $baris = '';

    $data_hari_buka = DB::table('talenta_seni_jadwal_tempat')->whereNull('deleted_at')->where('tempat_id', $tempat_id)->where('tahun', $jadwal->tahun)->get();
    $hari_buka = collect($data_hari_buka)->where('kecamatan', $jadwal->kecamatan)->toArray();
    $hari_buka = array_values($hari_buka);

    $tanggal = (is_null($request->tanggal)) ? $hari_buka[0]->tanggal : $request->tanggal;

    $baris = collect($hari_buka)->where('tanggal', $tanggal)->first();

    $jadwal_sama = DB::table('public.talenta_seni_jadwal as jadwal')
      ->join('public.talenta_seni_peserta as peserta', 'peserta.jadwal_id', 'jadwal.id')
      ->selectRaw("jadwal.npsn,jadwal.nama_sekolah,jadwal.status_jadwal,count(peserta.jadwal_id) as jumlah_peserta")
      ->whereRaw("tempat_id='$tempat_id' AND tanggal='" . date('Y-m-d', strtotime($tanggal)) . "' AND status_jadwal='Setujui'")
      ->whereNull('jadwal.deleted_at')
      ->groupByRaw("jadwal.npsn,jadwal.nama_sekolah,jadwal.status_jadwal,peserta.jadwal_id")->get();

    $jumlah_peserta = $jadwal_sama->sum('jumlah_peserta');
    $jumlah_lembaga = $jadwal_sama->count();

    return ['tempat' => $tempat, 'hari_buka' => $hari_buka, 'jadwal_sama' => $jadwal_sama, 'jumlah_peserta' => $jumlah_peserta, 'jumlah_lembaga' => $jumlah_lembaga, 'baris' => $baris];
  }

  function konfirmasi(Request $request)
  {
    $catatan = $request->catatan;
    $id = $request->id;
    $persetujuan = $request->persetujuan;
    $tempat_id = $request->tempat_id;
    $tgl = date('Y-m-d', strtotime($request->tgl));
    $mulai = date('H:i:s', strtotime($request->mulai));
    $selesai = date('H:i:s', strtotime($request->selesai));

    if ($persetujuan == 'Setujui') {
      $catatan = '';
      $data_update = [
        'status_jadwal' => $persetujuan,
        'tanggal' => $tgl,
        'catatan_reschedule' => $catatan,
        'jam_mulai' => $mulai,
        'jam_selesai' => $selesai,
        'tempat_id' => $tempat_id,
        'accepted_at' => now(),
      ];
    } else {
      $data_update = [
        'status_jadwal' => $persetujuan,
        'catatan_reschedule' => $catatan,
        'accepted_at' => null,
      ];
    }

    $dateCheck = now()->createFromFormat('Y-m-d', $tgl);

    // if ((int)$dateCheck->format('d') < 10 || (int)$dateCheck->format('d') > 26) {
    //     return ['code' => '403', 'message' => 'Tanggal pengajuan diluar tgl 11-26'];
    // }

    // if ((int)$dateCheck->format('m') == 6 || (int)$dateCheck->format('m') == 7) {
    //     return ['code' => '403', 'message' => 'Bulan pengajuan tidak boleh Juni dan Juli'];
    // }

    // $akhirTahun = now()->createFromFormat('Y-m-d', '2023-11-15')->endOfDay();

    // if ($dateCheck > $akhirTahun) {
    //     return ['code' => '403'];
    // }

    $jadwal = DB::table('public.talenta_seni_jadwal')->whereNull('deleted_at')->where('id', $id)->update($data_update);

    if ($jadwal) {
      return ['code' => '200', 'message' => 'Berhasil disimpan'];
    } else {
      return ['code' => '250', 'message' => 'Gagal disimpan'];
    }
  }

  function cetak(Request $request)
  {
    $kecamatan = $request->kecamatan;
    $tempat_id = $request->tempat_id;
    $tanggal = date('Y-m-d', strtotime($request->tanggal));
    $tahun = date('Y-m-d', strtotime($tanggal));

    $tempat = DB::table('public.talenta_seni_tempat')->whereRaw("id='$tempat_id'")->first();
    $hari_buka = DB::table('talenta_seni_jadwal_tempat')->whereNull('deleted_at')->where('tempat_id', $tempat_id)->where('tanggal', $tahun)->get();;
    $keterangan = collect($hari_buka)->where('kecamatan', $kecamatan)->first();

    $jadwal = DB::table('public.talenta_seni_jadwal')->whereNull('deleted_at')->whereRaw("tempat_id='$tempat_id' AND kecamatan='$kecamatan' AND tanggal='$tanggal' AND status_jadwal='Setujui'")->get();
    $id_jadwal = $jadwal->pluck('id')->toArray();

    $peserta = DB::table('public.talenta_seni_peserta')->whereIn('jadwal_id', $id_jadwal)->get();
    $pendamping = DB::table('public.talenta_seni_pendamping')->whereIn('jadwal_id', $id_jadwal)->get();

    $data = [
      'jadwal' => $jadwal,
      'peserta' => $peserta,
      'pendamping' => $pendamping,
      'keterangan' => $keterangan,
      'tempat' => $tempat,
    ];

    return view('bunda.talenta.cetak_permohonan', $data);
  }

  function upload_berkas(Request $request)
  {
    $param = json_decode($request->param);
    $berkas = $request->file('berkas');
    $kecamatan = str_replace("_", " ", $param->kecamatan);
    $kolom = $param->kolom;
    $tahun = $param->tahun;
    $tempat_id = $param->tempat_id;

    $where = [
      'tempat_id' => $tempat_id,
      'kecamatan' => $kecamatan,
      'tahun' => $tahun,
    ];

    $data_insert = array_merge($where, ['edited_at' => date('Y-m-d')]);
    $berkas_bunda = DB::table('talenta_seni_berkas_bunda')->where($where)->first();

    if ($request->hasFile('berkas')) {
      $file = $request->file('berkas');

      $size = $file->getSize();

      if ($size > 1000000) {
        return ['code' => 250, 'pesan' => 'Ukuran file melebihi 1MB'];
      }

      $a = Bantuan::validate_file($request->file('berkas'), 'pdf');

      if ($a) {
        if (!empty($berkas_bunda) && $berkas_bunda->$kolom != '') {
          Storage::delete($kolom . '/' . $berkas_bunda->$kolom);
        }

        $nama_file = "$kolom/bunda" . date('YmdHis') . "-" . uniqid() . "." . $file->getClientOriginalExtension();

        Storage::put($nama_file, file_get_contents($file));

        $data = array_merge($data_insert, [$kolom => $nama_file]);
        $simpan = DB::table('talenta_seni_berkas_bunda')->updateOrInsert($where, $data);

        if ($simpan) {
          return ['code' => '200', 'pesan' => 'Berhasil menyimpan'];
        } else {
          return ['code' => '250', 'pesan' => 'Gagal menyimpan'];
        }
      }
      return ['code' => '250', 'pesan' => 'Format tidak sesuai'];
    }
    return ['code' => '250', 'pesan' => 'tidak mengisi berkas'];
  }

  function format()
  {
    $hasil = [];
    $tempat = DB::table('public.talenta_seni_tempat')->selectRaw("id,kecamatan,nama,hari_buka")->orderBy('id')->get();
    foreach ($tempat as $t) {
      // $baris = [];
      $hari_buka = json_decode($t->hari_buka);
      $t->jadwal = $hari_buka;

      foreach ($hari_buka as $h) {
        $tanggal = date('Y-m-d', strtotime($h->tanggal));
        $mulai = collect($hari_buka)->where('kecamatan', $h->kecamatan)->min('mulai');
        $selesai = collect($hari_buka)->where('kecamatan', $h->kecamatan)->max('selesai');
        $jumlah_lembaga = collect($hari_buka)->where('kecamatan', $h->kecamatan)->max('jumlah_lembaga');

        $baris = [
          'tempat_id' => $t->id,
          'kecamatan' => $h->kecamatan,
          'tanggal' => $tanggal,
          'mulai' => $mulai,
          'selesai' => $selesai,
          'jumlah_lembaga' => $jumlah_lembaga,
          'created_at' => date('Y-m-d H:i:s'),
          'updated_at' => now(),
        ];
        // $update = DB::table('public.talenta_seni_jadwal_tempat')->insert($baris);
        array_push($hasil, $baris);
      }
    }

    return $hasil;
  }

  function data_peserta(Request $request)
  {
    // return $request->all();
    $id_jadwal = $request->id_jadwal;

    $talenta = DB::table('public.talenta_seni_jadwal as j')
      ->join('public.talenta_seni_tempat as t', 't.id', 'j.tempat_id')
      ->join('public.talenta_seni_peserta as p', 'p.jadwal_id', 'j.id')
      ->selectRaw("j.judul,j.tanggal,t.nama as nama_tempat,j.npsn,j.nama_sekolah,p.nik,p.nama")
      ->where('j.id', $id_jadwal);

    $data = [
      'talenta' => (clone $talenta)->first(),
      'peserta' => (clone $talenta)->get(),
    ];

    return view('bunda.talenta.peserta', $data);
  }
}
