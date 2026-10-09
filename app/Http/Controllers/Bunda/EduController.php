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
use DataTables, DB, Session, Auth;
use App\Tools\Bantuan;
use Carbon\Carbon;
use App\Exports\EduCityTourPesertaExport;
use Maatwebsite\Excel\Facades\Excel;

class EduController extends Controller
{
  function __construct()
  {
    date_default_timezone_set('Asia/Jakarta');
  }

  function index()
  {
    $th = Jadwal::on('pgsql_paud')->selectRaw("EXTRACT(YEAR from tanggal) as tahun")->whereNotNull('tanggal')->groupBy('tahun')->get();
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
      $wisata = Tempat::whereraw("id_opd ilike '%\"" . Auth::getUser()->id . "\"%'")->orderBy('nama', 'ASC')->get();
    } else {
      $wisata = Tempat::orderBy('nama', 'ASC')->get();
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
    return view('bunda.edu.index', $data);
  }

  function get_data(Request $request)
  {
    $tahun = $request->tahun;
    $kec = $request->kec;
    $kel = $request->kel;
    $jenjang = $request->jenjang;
    $wisata = $request->wisata;
    $bulan = $request->bulan;
    $status = $request->status;

    $tahun = ($tahun == '') ? 'kosong' : $tahun;

    $where_lokasi = "";
    $where = "true";



    $where_opd = [];
    if (Auth::getUser()->role == 'opd') {
      $opd = Tempat::whereRaw("id_opd ilike '%\"" . Auth::getUser()->id . "\"%'")->get();
      $where_opd = $opd->pluck('id')->toArray();
    }

    // $sekolah = Sekolah::on('pgsql_paud')->join('public.master_user as u', 'u.npsn', 'sekolah.npsn')
    //   ->leftjoin('public.kecamatan as kec', 'kec.kecamatan_kode', 'sekolah.kec_id')
    //   ->leftjoin('public.kelurahan as kel', 'kel.kelurahan_kode', 'sekolah.desa')
    //   ->selectRaw("sekolah.npsn,sekolah.nama,kec.kecamatan_dispenduk,kel.kelurahan_dispenduk")->whereRaw("u.user_enable is true $where_lokasi")->get();
    // $npsn = $sekolah->pluck('npsn');

    // if ($tahun == '') {
    //   $bkb = [];
    // } else {
    //   $bkb = Jadwal::on('pgsql_paud')
    //     ->join('kegiatan_pendukung.edu_city_tour_tempats as tempat', 'tempat.id', 'edu_city_tour_jadwals.tempat_id')
    //     ->selectRaw("edu_city_tour_jadwals.*,tempat.nama as nama_tempat,tempat.alamat as alamat_tempat,tempat.htm as htm_tempat")
    //     ->whereRaw("$where")->whereIn('npsn', $npsn)
    //     ->get();

    //   if (Auth::getUser()->role == 'opd') {
    //     if (count($where_opd) != 0) {
    //       $bkb = Jadwal::on('pgsql_paud')
    //         ->join('kegiatan_pendukung.edu_city_tour_tempats as tempat', 'tempat.id', 'edu_city_tour_jadwals.tempat_id')
    //         ->selectRaw("edu_city_tour_jadwals.*,tempat.nama as nama_tempat,tempat.alamat as alamat_tempat,tempat.htm as htm_tempat")
    //         ->whereRaw("$where")->whereIn('npsn', $npsn)
    //         ->whereIn('tempat_id', $where_opd)
    //         ->get();
    //     } else {
    //       $bkb = [];
    //     }
    //   }
    // }

    $bkb = DB::connection('pgsql_paud')
      ->table('kegiatan_pendukung.edu_city_tour_jadwals as j')
      ->join('kegiatan_pendukung.edu_city_tour_tempats as t', 't.id', 'j.tempat_id')
      ->join('public.sekolah as s', 's.npsn', 'j.npsn')
      ->leftJoin('public.kecamatan as kec', 'kec.kecamatan_kode', 's.kec_id')
      ->leftJoin('public.kelurahan as kel', 'kel.kelurahan_kode', 's.desa')
      ->selectRaw("j.*,t.nama as nama_tempat,t.alamat,t.kapasitas_pd,s.nama as nama_sekolah, kec.kecamatan_dispenduk as nama_kecamatan, kel.kelurahan_dispenduk as nama_kelurahan")
      ->when($status != 'Semua', function ($w) use ($status) {
        return $w->where('j.status_jadwal', $status);
      })
      ->when($kec != 'semua', function ($w) use ($kec) {
        $w->where('s.kec_id', $kec);
      })
      ->when($kel != 'semua', function ($w) use ($kel) {
        $w->where('s.desa', $kel);
      })
      ->when($jenjang != 'semua', function ($w) use ($jenjang) {
        $w->where('s.jenjang', $jenjang);
      })
      ->when($tahun !== null, function ($w) use ($tahun, $bulan) {
        $where = $w;
        if ($tahun == 'kosong') {
        } else {
          if ($bulan == 'semua') {
            $where->whereRaw("CAST(tanggal as varchar) ilike '%$tahun-%'");
          } else {
            $where->whereRaw("CAST(tanggal as varchar) ilike '%$tahun-$bulan%'");
          }
        }
        return $where;
      })
      ->when($wisata != 'semua', function ($w) use ($wisata) {
        return $w->where('tempat_id', $wisata);
      });

    $id_educity = $bkb->pluck('id')->toArray();

    $rekap_absen = DB::table('public.educity_absensi')
      ->selectRaw("educity_id")
      ->whereIn('educity_id', $id_educity)
      ->whereNull('deleted_at')
      ->groupBy('educity_id')->get();

    return DataTables::queryBuilder($bkb)
      ->addIndexColumn()
      ->addColumn('pendampings', function ($akun) {
        $pendamping = Pendamping::on('pgsql_paud')->where('edu_city_tour_jadwal_id', $akun->id)->orderBy('id', 'ASC')->get();
        $namanya = '';
        // foreach($pendamping as $p){
        // 	$namanya .= $p->nama_pegawai.' <b>('.$p->jabatan.')</b><br><br>';
        // }

        return $pendamping->count();
      })
      ->addColumn('tanggal_pelaksanaan', function ($akun) {
        $tgl = $akun->tanggal ? date('d-m-Y', strtotime($akun->tanggal)) : '-';

        return $tgl;
      })
      ->addColumn('jam_mulai', function ($akun) {
        $tgl = date('H:i', strtotime($akun->jam_mulai));

        return $tgl;
      })
      ->addColumn('jam_selesai', function ($akun) {
        $tgl = date('H:i', strtotime($akun->jam_selesai));

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
          $button .= '<br> Belum upload Surat Tugas';
        }

        if ($akun->file_laporan_kegiatan != '') {
          $button .= '<br> <a style="margin-left: 5px" href="https://dispendik.surabaya.go.id/profilsekolah_file/' . $akun->file_laporan_kegiatan . '" target="_blank" class="btn btn-sm btn-primary">Laporan Kegiatan</a>';
        } else {
          $button .= '<br> Belum upload File Laporan Kegiatan';
        }

        if ($akun->file_berita_acara != '') {
          $button .= '<br> <a style="margin-left: 5px" href="https://dispendik.surabaya.go.id/profilsekolah_file/' . $akun->file_berita_acara . '" target="_blank" class="btn btn-sm btn-primary">Berita Acara</a>';
        } else {
          $button .= '<br> Belum upload File Berita Acara';
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
      ->addColumn('aksi', function ($akun) use ($rekap_absen) {
        $button = '';

        if (Auth::getUser()->role == 'dinas') {
          $button .= '<a style="margin-left: 5px" href="javascript:void(0)" onclick="modal_konfirmasi(\'' . $akun->id . '\')" class="btn btn-sm btn-warning">Konfirmasi</a>';
        }

        $button .= '<a style="margin-left: 5px" href="' . route('bunda-edu-halaman_absensi', ['halaman' => 'main', 'id_educity' => $akun->id]) . '" target="_blank" class="btn btn-sm btn-success">Absensi</a>';

        $cek = collect($rekap_absen)->where('educity_id', $akun->id)->first();
        if (!empty($cek)) {
          $button .= '<a style="margin-left: 5px" href="' . route('bunda-edu-halaman_absensi', ['halaman' => 'rekap_absensi', 'id_educity' => $akun->id]) . '" target="_blank" class="btn btn-sm btn-outline-success">Rekap Absensi</a>';
        }

        $button .= '<a style="margin-left: 5px" href="' . route('bunda-edu-data_peserta', ['id_edu' => $akun->id]) . '" target="_blank" class="btn btn-sm btn-primary">Data Peserta</a>';
        return $button;
      })
      ->rawColumns(['aksi', 'pendampings', 'berkas', 'status', 'tanggal_pelaksanaan'])
      ->make(true);
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

    $jadwal = Jadwal::join('kegiatan_pendukung.edu_city_tour_tempats as tempat', 'tempat.id', 'edu_city_tour_jadwals.tempat_id')
      ->selectRaw("edu_city_tour_jadwals.*,tempat.nama as nama_tempat,tempat.alamat as alamat_tempat,tempat.htm as htm_tempat,tempat.kapasitas_pd")->where('edu_city_tour_jadwals.id', $id)->first();
    $jadwal_bis = FasilitasiJadwal::where('materi_id', 1)
      ->where('tanggal', $jadwal->tanggal)
      ->where('npsn', $jadwal->npsn)
      ->select('jumlah_bis')
      ->first();

    $sekolah = DB::connection('pgsql_paud')->table('public.sekolah as s')
      ->leftjoin('public.kecamatan as kec', 'kec.kecamatan_kode', 's.kec_id')
      ->leftjoin('public.kelurahan as kel', 'kel.kelurahan_kode', 's.desa')
      ->selectRaw("s.*,kec.kecamatan_dispenduk,kel.kelurahan_dispenduk")->where('npsn', $jadwal->npsn)->first();

    $sama = Jadwal::whereRaw("tempat_id='$jadwal->tempat_id' AND tanggal='$jadwal->tanggal'")->get();
    $npsn = $sama->pluck('npsn');
    $sekolah_lain = DB::connection('pgsql_paud')->table('public.sekolah as s')
      ->leftjoin('public.kecamatan as kec', 'kec.kecamatan_kode', 's.kec_id')
      ->leftjoin('public.kelurahan as kel', 'kel.kelurahan_kode', 's.desa')
      ->selectRaw("s.*,kec.kecamatan_dispenduk,kel.kelurahan_dispenduk")->whereIn('npsn', $npsn)->get();

    $data = [
      'jadwal' => $jadwal,
      'jadwal_bis' => $jadwal_bis,
      'sekolah' => $sekolah,
      'sama' => $sama,
      'sekolah_lain' => $sekolah_lain,
    ];

    $content = view('bunda.edu.modal_konfirmasi', $data)->render();

    return ['content' => $content];
  }

  function get_jadwal(Request $request)
  {
    $tempat_id = $request->tempat_id;
    $tgl = $request->tgl;

    $sama = Jadwal::whereRaw("tempat_id='$tempat_id' AND tanggal='$tgl'")->get();
    $npsn = $sama->pluck('npsn');
    $sekolah_lain = DB::connection('pgsql_paud')->table('public.sekolah as s')
      ->leftjoin('public.kecamatan as kec', 'kec.kecamatan_kode', 's.kec_id')
      ->leftjoin('public.kelurahan as kel', 'kel.kelurahan_kode', 's.desa')
      ->selectRaw("s.*,kec.kecamatan_dispenduk,kel.kelurahan_dispenduk")->whereIn('npsn', $npsn)->get();

    foreach ($sama as $s) {
      $s->nama_sekolah = $sekolah_lain->where('npsn', $s->npsn)->first()->nama;
      $s->jam_pelaksanaan = date('H:i', strtotime($s->jam_mulai)) . ' s/d ' . date('H:i', strtotime($s->jam_selesai));
    }

    return ['data' => $sama, 'tgl' => date('d-m-Y', strtotime($tgl))];
  }

  function konfirmasi(Request $request)
  {
    $catatan = $request->catatan;
    $id = $request->id;
    $persetujuan = $request->persetujuan;
    $tgl = $request->tgl;

    if ($persetujuan == 'Setujui') {
      $catatan = '';
    }

    $dateCheck = now()->createFromFormat('Y-m-d', $tgl);

    if ((int)$dateCheck->format('d') < 10 || (int)$dateCheck->format('d') > 26) {
      return ['code' => '403', 'message' => 'Tanggal pengajuan diluar tgl 11-26'];
    }

    // if ((int)$dateCheck->format('m') == 6 || (int)$dateCheck->format('m') == 7) {
    //     return ['code' => '403','message'=>'Bulan pengajuan tidak boleh Juni dan Juli'];
    // }

    // $akhirTahun = now()->createFromFormat('Y-m-d', '2023-11-15')->endOfDay();

    // if ($dateCheck > $akhirTahun) {
    //     return ['code' => '403'];
    // }

    $jadwal = Jadwal::find($id);
    $jadwal->status_jadwal = $persetujuan;
    $jadwal->tanggal = $tgl;
    $jadwal->catatan_reschedule = $catatan;
    $jadwal->save();

    if ($jadwal) {
      return ['code' => '200', 'message' => 'Berhasil disimpan'];
    } else {
      return ['code' => '250', 'message' => 'Gagal disimpan'];
    }
  }

  function halaman_absensi(Request $request)
  {
    $halaman = $request->halaman;

    switch ($halaman) {
      case 'main':
        if (Auth::check()) {
          $id_educity = $request->id_educity;
          $educity = DB::connection('pgsql_paud')->table('kegiatan_pendukung.edu_city_tour_jadwals as j')
            ->join('kegiatan_pendukung.edu_city_tour_tempats as m', 'm.id', 'j.tempat_id')
            ->join('public.sekolah as s', 'j.npsn', 's.npsn')
            ->where('j.id', $id_educity)
            ->selectRaw("j.*,m.nama as nama_tempat,m.alamat,s.nama as nama_sekolah")
            ->first();

          $data = [
            'educity' => $educity,
            'url' => route('bunda-edu-halaman_absensi', ['halaman' => 'form_absensi', 'id_educity' => $id_educity]),
          ];
          return view('bunda.edu.absensi', $data);
        } else {
          return 'Anda tidak memiliki akses';
        }
        break;

      case 'form_absensi':
        $id_educity = $request->id_educity;
        $educity = DB::connection('pgsql_paud')->table('kegiatan_pendukung.edu_city_tour_jadwals as j')
          ->join('kegiatan_pendukung.edu_city_tour_tempats as m', 'm.id', 'j.tempat_id')
          ->join('public.sekolah as s', 'j.npsn', 's.npsn')
          ->where('j.id', $id_educity)
          ->selectRaw("j.*,m.nama as nama_tempat,m.alamat,s.nama as nama_sekolah")
          ->first();

        $data = [
          'educity' => $educity,
        ];
        return view('bunda.edu.form_absensi', $data);
        break;

      case 'simpan':
        // return $request->all();
        $id_kegiatan = $request->id_kegiatan; //"18456"
        $instansi = $request->instansi; //"sdasdasd"
        $nama = $request->nama; //"sadada"
        $nik = $request->nik; //"asdadad"
        $signature = $request->signature; //"data:image/png;

        $data_absensi = [
          'nik' => $nik,
          'instansi' => $instansi,
          'nama' => $nama,
          'ttd' => $signature,
          'educity_id' => $id_kegiatan,
        ];

        $cari = DB::table('public.educity_absensi')->where('nik', $nik)->where('educity_id', $id_kegiatan)->whereNull('deleted_at')->first();
        if (!empty($cari)) {
          return ['code' => 250, 'pesan' => 'Data ' . $nik . ', Sudah melakukan absensi'];
        } else {
          $simpan = DB::table('public.educity_absensi')->insert($data_absensi);
          if ($simpan) {
            return ['code' => 200, 'data' => 'Berhasil disimpan'];
          } else {
            return ['code' => 250, 'data' => 'Gagal menyimpan'];
          }
        }
        break;

      case 'rekap_absensi':
        $id_educity = $request->id_educity;
        $educity = DB::connection('pgsql_paud')->table('kegiatan_pendukung.edu_city_tour_jadwals as j')
          ->join('kegiatan_pendukung.edu_city_tour_tempats as m', 'm.id', 'j.tempat_id')
          ->join('public.sekolah as s', 'j.npsn', 's.npsn')
          ->where('j.id', $id_educity)
          ->selectRaw("j.*,m.nama as nama_tempat,m.alamat,s.nama as nama_sekolah")
          ->first();

        $absensi = DB::table('public.educity_absensi')->whereNull('deleted_at')
          ->where('educity_id', $id_educity)->get();

        $data = [
          'educity' => $educity,
          'absensi' => $absensi
        ];

        return view('bunda.edu.rekap_absensi', $data);
        break;

      default:
        return '404 - Halaman tidak ditemukan';
        break;
    }
  }

  function data_peserta(Request $request)
  {
    // return $request->all();
    $id_jadwal = $request->id_edu;

    $talenta = DB::connection('pgsql_paud')->table('kegiatan_pendukung.edu_city_tour_jadwals as j')
      ->join('kegiatan_pendukung.edu_city_tour_tempats as t', 't.id', 'j.tempat_id')
      ->leftjoin('kegiatan_pendukung.edu_city_tour_pesertas as p', 'p.jadwal_id', 'j.id')
      ->leftjoin('public.sekolah as se', 'j.npsn', 'se.npsn')
      ->selectRaw("j.tanggal,t.nama as nama_tempat,j.npsn,se.nama as nama_sekolah,j.peserta")
      ->where('j.id', $id_jadwal);

    $data = [
      'talenta' => (clone $talenta)->first(),
    ];

    return view('bunda.edu.peserta', $data);
  }

  function export_peserta(Request $request)
  {
    $tahun = $request->tahun ?: 2025;

    $jadwals = Jadwal::on('pgsql_paud')
      ->join('kegiatan_pendukung.edu_city_tour_tempats as t', 't.id', 'edu_city_tour_jadwals.tempat_id')
      ->whereRaw('EXTRACT(YEAR FROM tanggal) = ?', [$tahun])
      ->where('status_jadwal', 'Setujui')
      ->whereNotNull('peserta')
      ->select('edu_city_tour_jadwals.id', 'edu_city_tour_jadwals.npsn', 'edu_city_tour_jadwals.tanggal', 'edu_city_tour_jadwals.peserta', 't.nama as nama_tempat')
      ->get();

    // Kumpulkan & agregasi peserta per (id_siswa, npsn) dari kolom `peserta`
    // (JSON di-encode dobel: array berisi string, tiap string = JSON array [id_siswa, npsn, nama])
    $agregat = [];
    foreach ($jadwals as $j) {
      $list = json_decode($j->peserta, true);
      if (!is_array($list)) {
        continue;
      }

      foreach ($list as $item) {
        $parsed = is_string($item) ? json_decode($item, true) : $item;
        if (!is_array($parsed) || count($parsed) < 3) {
          continue;
        }

        [$idSiswa, $npsnPeserta, $namaPeserta] = $parsed;
        $idSiswa = (int) $idSiswa;
        $npsnPeserta = (string) $npsnPeserta;
        $key = $idSiswa . '|' . $npsnPeserta;

        if (!isset($agregat[$key])) {
          $agregat[$key] = [
            'id_siswa' => $idSiswa,
            'npsn' => $npsnPeserta,
            'nama_json' => $namaPeserta,
            'tanggal' => [],
            'nama_tempat' => $j->nama_tempat,
          ];
        }
        $agregat[$key]['tanggal'][$j->tanggal] = true;
      }
    }

    $idSiswaList = collect($agregat)->pluck('id_siswa')->unique()->values()->all();
    $npsnList = collect($agregat)->pluck('npsn')->unique()->values()->all();

    // Filter siswa pakai kombinasi id_siswa + npsn sesuai arahan (siswa.id_siswa = ... and siswa.npsn = ...)
    $siswaMap = Siswa::on('pgsql_paud')
      ->whereIn('id_siswa', $idSiswaList)
      ->whereIn('npsn', $npsnList)
      ->get(['id_siswa', 'npsn', 'nama', 'nik', 'kelas'])
      ->keyBy(function ($s) {
        return $s->id_siswa . '|' . $s->npsn;
      });

    $sekolahMap = Sekolah::on('pgsql_paud')
      ->whereIn('npsn', $npsnList)
      ->pluck('nama', 'npsn');

    $rows = collect($agregat)->map(function ($a) use ($siswaMap, $sekolahMap) {
      $siswa = $siswaMap->get($a['id_siswa'] . '|' . $a['npsn']);

      $tanggal = collect(array_keys($a['tanggal']))
        ->sort()
        ->map(function ($t) {
          return date('d-m-Y', strtotime($t));
        })
        ->implode(', ');

      return [
        $siswa->nama ?? $a['nama_json'],
        $siswa && $siswa->nik ? "'" . $siswa->nik : '-',
        $siswa->kelas ?? '-',
        $a['npsn'],
        $sekolahMap->get($a['npsn']) ?? '-',
        $tanggal,
        $a['nama_tempat'],
      ];
    })
      ->sortBy(function ($r) {
        return $r[4] . '|' . $r[0];
      })
      ->values();

    return Excel::download(new EduCityTourPesertaExport($rows), "Rekap_Peserta_Edu_City_Tour_{$tahun}.xlsx");
  }

  function get_grafik(Request $request)
  {
    // return $request->all();
    $bulan = $request->bulan;
    $jenjang = $request->jenjang;
    $kec = $request->kec;
    $kel = $request->kel;
    $status = $request->status;
    $tahun = $request->tahun;
    $wisata = $request->wisata;

    $angka_bulan = [
      1 => 'Januari',
      2 => 'Februari',
      3 => 'Maret',
      4 => 'April',
      5 => 'Mei',
      6 => 'Juni',
      7 => 'Juli',
      8 => 'Agustus',
      9 => 'September',
      10 => 'Oktober',
      11 => 'November',
      12 => 'Desember',
    ];

    $judul = 'Grafik Data Kunjungan Edu City Tour Tahun ' . $tahun;
    $judul .= $bulan == 'semua' ? '' : ' Bulan ' . $angka_bulan[(int)$bulan];
    $judul .= $jenjang == 'semua' ? ' Untuk Semua Jenjang ' : ' Untuk Jenjang ' . $jenjang;
    $judul .= $status == 'Semua' ? ' Dengan Semua Status Usulan ' : ' Dengan Status Usulan ' . $status;

    $jadwal = DB::connection('pgsql_paud')
      ->table('kegiatan_pendukung.edu_city_tour_jadwals as j')
      ->join('kegiatan_pendukung.edu_city_tour_tempats as t', 't.id', 'j.tempat_id')
      ->join('public.sekolah as se', 'se.npsn', 'j.npsn')
      ->selectRaw("
        j.status_jadwal,j.tempat_id,t.nama,
        COUNT(*) as jumlah_kunjungan
        ")
      ->when($tahun !== null, function ($w) use ($tahun, $bulan) {
        $where = $w;
        if ($tahun == 'kosong') {
        } else {
          if ($bulan == 'semua') {
            $where->whereRaw("CAST(tanggal as varchar) ilike '%$tahun-%'");
          } else {
            $where->whereRaw("CAST(tanggal as varchar) ilike '%$tahun-$bulan%'");
          }
        }
        return $where;
      })
      ->when($jenjang, function ($w) use ($jenjang) {
        if ($jenjang == 'semua') {
          $w->whereIn('se.jenjang', ['TK', 'KB', 'SPS', 'TPA', 'RA']);
        } else {
          $w->where('se.jenjang', $jenjang);
        }
      })
      ->when($kec !== 'semua', function ($w) use ($kec) {
        $w->where('se.kec_id', $kec);
      })
      ->when($kel !== 'semua', function ($w) use ($kel) {
        $w->where('se.desa', $kel);
      })
      ->when($status !== 'Semua', function ($w) use ($status) {
        $w->where('j.status_jadwal', $status);
      })
      ->when($wisata !== 'semua', function ($w) use ($wisata) {
        $w->where('j.tempat_id', $wisata);
      })
      ->groupByRaw("j.status_jadwal,j.tempat_id,t.nama")->get();

    if ($wisata == 'semua') {
      $judul .= ' di Semua Tempat Wisata ';
    } else {
      $wst = DB::connection('pgsql_paud')->table('kegiatan_pendukung.edu_city_tour_tempats')->where('id', $wisata)->first();
      $judul .= ' di Tempat Wisata ' . ($wst->nama ?? '-');
    }

    if ($kec != 'semua') {
      $kec = DB::connection('pgsql_paud')->table('public.kecamatan')->where('kecamatan_kode', $kec)->first();
      $judul .= ' Kunjungan dari Kecamatan ' . ($kec->kecamatan_dispenduk ?? '');
    }
    if ($kel != 'semua') {
      $kel = DB::connection('pgsql_paud')->table('public.kelurahan')->where('kelurahan_kode', $kel)->first();
      $judul .= ' Kelurahan ' . ($kel->kelurahan_dispenduk ?? '');
    }

    // 1. Ambil list unik nama tempat wisata & urutkan dengan benar
    $kategori = collect($jadwal)->pluck('nama')->unique()->sort()->values();

    // 2. Petak data series agar posisinya SAMA PERSIS dengan array $kategori
    $series = collect($jadwal)
      ->groupBy('status_jadwal')
      ->map(function ($items, $status) use ($kategori) {
        // Kelompokkan data status ini berdasarkan 'nama'
        $groupedByNama = $items->groupBy('nama');
        $warnaStatus = [
          'Draft'    => '#aae3fa', // Biru Muda (Sky Blue)
          'Reschedule' => '#f5a0a0', // Merah
          'Setujui'  => '#65f09f', // Hijau
          'Pengajuan'  => '#e7f150', // Hijau
        ];

        // Loop array $kategori agar setiap posisi indeks di 'data' cocok dengan posisi di 'kategori'
        $data = $kategori->map(function ($nama) use ($groupedByNama) {
          if ($groupedByNama->has($nama)) {
            return (int) $groupedByNama->get($nama)->sum('jumlah_kunjungan');
          }
          return 0; // Jika status ini tidak ada di wisata tersebut, beri angka 0
        })->toArray();

        return [
          'name' => $status,
          'data' => $data,
          'color' => $warnaStatus[$status] ?? '#CCCCCC',
        ];
      })
      ->values()
      ->toArray();

    return ['judul' => $judul, 'series' => $series, 'kategori' => $kategori];
  }
}
