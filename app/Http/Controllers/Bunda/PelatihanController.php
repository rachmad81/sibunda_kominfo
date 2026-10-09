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
use App\Models\Pelatihan\Jadwal;
use App\Models\Pelatihan\Pendamping;
use App\Models\Pelatihan\Materi;
use App\Http\Libraries\GetData;
use DataTables, DB, Session, Auth;
use App\Tools\Bantuan;
use Carbon\Carbon;

class PelatihanController extends Controller
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
      $materi = Materi::whereRaw("id_opd ilike '%\"" . Auth::getUser()->id . "\"%'")->orderBy('nama', 'ASC')->get();
    } else {
      $materi = Materi::orderBy('nama', 'ASC')->get();
    }

    $data = [
      'thn' => $th,
      'tahun' => date('Y'),
      'kecamatan' => $kecamatan,
      'kelurahan' => $kelurahan,
      'kode_kecamatan' => $kode_kecamatan,
      'kode_kelurahan' => $kode_kelurahan,
      'jenjang' => $jenjang,
      'materi' => $materi,
    ];
    return view('bunda.pelatihan.index', $data);
  }

  function get_data(Request $request)
  {
    $tahun = $request->tahun;
    $kec = $request->kec;
    $kel = $request->kel;
    $jenjang = $request->jenjang;
    $materi = $request->materi;
    $bulan = $request->bulan;
    $jenis = $request->jenis;
    $status = $request->status;

    $tahun = ($tahun == '') ? 'kosong' : $tahun;

    $bkb = DB::connection('pgsql_paud')
      ->table('kegiatan_pendukung.pelatihan_jadwals as j')
      ->join('kegiatan_pendukung.pelatihan_materis as m', 'm.id', 'j.materi_id')
      ->join('public.sekolah as s', 's.npsn', 'j.npsn')
      ->leftjoin('public.kecamatan as kec', 'kec.kecamatan_kode', 's.kec_id')
      ->leftjoin('public.kelurahan as kel', 'kel.kelurahan_kode', 's.desa')
      ->selectRaw("j.*,m.nama as nama_materi,s.nama as nama_sekolah,kec.kecamatan_dispenduk as nama_kecamatan,kel.kelurahan_dispenduk as nama_kelurahan")
      ->when($kec != 'semua', function ($w) use ($kec) {
        return $w->where('s.kec_id', $kec);
      })
      ->when($kel != 'semua', function ($w) use ($kel) {
        return $w->where('s.desa', $kel);
      })
      ->when($jenjang != 'semua', function ($w) use ($jenjang) {
        return $w->where('s.jenjang', $jenjang);
      })
      ->when($tahun !== null, function ($w) use ($bulan, $tahun) {
        if ($tahun == 'kosong') {
          return $where = $w->where('j.npsn', 'kosong saja');
        } else {
          $where = $w;
          if ($bulan == 'semua') {
            $where->whereRaw("CAST(j.tanggal as varchar) ilike '%$tahun-%'");
          } else {
            $where->whereRaw("CAST(j.tanggal as varchar) ilike '%$tahun-$bulan-%'");
          }
        }
        return $where;
      })
      ->when($status != 'Semua', function ($w) use ($status) {
        return $w->where('j.status_jadwal', $status);
      })
      ->when($materi != 'semua', function ($w) use ($materi) {
        return $w->where('j.materi_id', $materi);
      })
      ->when($jenis != 'semua', function ($w) use ($jenis) {
        return $w->where('jenis', $jenis);
      })
      ->when(Auth::getUser()->role == 'opd', function ($w) {
        $opd = Materi::whereRaw("id_opd ilike '%\"" . Auth::getUser()->id . "\"%'")->get();
        $where_opd = $opd->pluck('id')->toArray();
        return $w->whereIn('j.materi_id', $where_opd);
      });

    $id_pelatihan = $bkb->pluck('id')->toArray();

    $rekap_absen = DB::table('public.pelatihan_absensi')
      ->selectRaw("pelatihan_id")
      ->whereIn('pelatihan_id', $id_pelatihan)
      ->whereNull('deleted_at')
      ->groupBy('pelatihan_id')->get();

    return DataTables::queryBuilder($bkb)
      ->addIndexColumn()
      ->addColumn('pendampings', function ($akun) {
        $pendamping = Pendamping::on('pgsql_paud')->where('pelatihan_jadwal_id', $akun->id)->orderBy('id', 'ASC')->get();
        $namanya = '';
        $jabatan = '';
        $no_hp = '';
        if ($pendamping->count() != 0) {
          foreach ($pendamping as $p) {
            $namanya .= $p->nama_pegawai . ' - ' . $p->jabatan . ' - (' . $p->hp . ' / ' . $p->hp2 . ') <br><br>';
          }
        }

        return $namanya;
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
          // $url = 'https://filesdispendik.surabaya.go.id/profilsekolah_file/'.$akun->file_surat_permohonan;
          $url = 'https://dispendik.surabaya.go.id/profilsekolah_file/' . $akun->file_surat_permohonan;
          $button .= '<a style="margin-left: 5px" href="' . $url . '" target="_blank" class="btn btn-sm btn-primary">Lihat</a>';
        } else {
          $button = 'Belum upload permohonan';
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

        if (Auth::getUser()->role == 'dinas' && $akun->status_jadwal == 'Pengajuan') {
          $button .= '<a style="margin-left: 5px" href="javascript:void(0)" onclick="modal_konfirmasi(\'' . $akun->id . '\')" class="btn btn-sm btn-warning">Konfirmasi</a>';
        }
        $button .= '<a style="margin-left: 5px" href="' . route('bunda-pelatihan-halaman_absensi', ['halaman' => 'main', 'id_pelatihan' => $akun->id]) . '" target="_blank" class="btn btn-sm btn-success">Absensi</a>';

        $cek = collect($rekap_absen)->where('pelatihan_id', $akun->id)->first();
        if (!empty($cek)) {
          $button .= '<a style="margin-left: 5px" href="' . route('bunda-pelatihan-halaman_absensi', ['halaman' => 'rekap_absensi', 'id_pelatihan' => $akun->id]) . '" target="_blank" class="btn btn-sm btn-outline-success">Rekap Absensi</a>';
        }
        return $button;
      })
      ->addColumn('data_klien', function ($j) {
        return $j->jumlah_peserta;
      })
      ->addColumn('lokasi', function ($j) {
        if ($j->lokasi == 'BLC') {
          $lokasi = 'BLC';
        } else {
          $lokasi = '';
          $lokasi = $j->lokasi;
          if ($j->alamat_lokasi != '') {
            $lokasi .= ' <br>Alamat: ' . $j->alamat_lokasi;
          }
          if ($j->kecamatan != '') {
            $lokasi .= '; Kecamatan. ' . $j->kecamatan;
          }
          if ($j->kelurahan != '') {
            $lokasi .= '; Kelurahan. ' . $j->kelurahan;
          }
        }
        return $lokasi;
      })
      ->filterColumn('lokasi', function ($query, $keyword) {
        $query->where(function ($q) use ($keyword) {
          $q->where('j.alamat_lokasi', 'ilike', "%{$keyword}%")
            ->orWhere('j.lokasi', 'ilike', "%{$keyword}%");
        });
      })
      ->rawColumns(['aksi', 'pendampings', 'berkas', 'status', 'tanggal_pelaksanaan', 'data_klien', 'jabatan_pendamping', 'no_hp_p', 'lokasi'])
      ->make(true);
  }

  function presensi(Request $request)
  {
    $id = $request->id;
    $jadwal = Jadwal::on('pgsql_paud')
      ->join('public.edu_city_tour_tempats as tempat', 'tempat.id', 'edu_city_tour_jadwals.tempat_id')
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

    return view('bunda.pelatihan.kehadiran', $data);
  }

  function modal_konfirmasi(Request $request)
  {
    $id = $request->id;

    $jadwal = Jadwal::on('pgsql_paud')->join('kegiatan_pendukung.pelatihan_materis as m', 'm.id', 'pelatihan_jadwals.materi_id')
      ->selectRaw("pelatihan_jadwals.*,m.nama as nama_materi")->where('pelatihan_jadwals.id', $id)->first();

    $sekolah = DB::connection('pgsql_paud')->table('public.sekolah as s')
      ->leftjoin('public.kecamatan as kec', 'kec.kecamatan_kode', 's.kec_id')
      ->leftjoin('public.kelurahan as kel', 'kel.kelurahan_kode', 's.desa')
      ->selectRaw("s.*,kec.kecamatan_dispenduk,kel.kelurahan_dispenduk")->where('npsn', $jadwal->npsn)->first();

    $pendampings = Pendamping::on('pgsql_paud')->where('pelatihan_jadwal_id', $id)->get();

    $data = [
      'jadwal' => $jadwal,
      'sekolah' => $sekolah,
      'pendampings' => $pendampings,
    ];

    $content = view('bunda.pelatihan.modal_konfirmasi', $data)->render();

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

    $jadwal = Jadwal::find($id);
    $jadwal->status_jadwal = $persetujuan;
    $jadwal->tanggal = $tgl;
    $jadwal->catatan_reschedule = $catatan;
    $jadwal->save();

    if ($jadwal) {
      return ['code' => '200'];
    } else {
      return ['code' => '250'];
    }
  }

  function halaman_absensi(Request $request)
  {
    $halaman = $request->halaman;

    switch ($halaman) {
      case 'main':
        if (Auth::check()) {
          $id_pelatihan = $request->id_pelatihan;
          $pelatihan = DB::connection('pgsql_paud')->table('kegiatan_pendukung.pelatihan_jadwals as j')
            ->join('kegiatan_pendukung.pelatihan_materis as m', 'm.id', 'j.materi_id')
            ->join('public.sekolah as s', 'j.npsn', 's.npsn')
            ->where('j.id', $id_pelatihan)
            ->selectRaw("j.*,m.nama as nama_materi,s.nama as nama_sekolah")
            ->first();

          $data = [
            'pelatihan' => $pelatihan,
            'url' => route('bunda-pelatihan-halaman_absensi', ['halaman' => 'form_absensi', 'id_pelatihan' => $id_pelatihan]),
          ];
          return view('bunda.pelatihan.absensi', $data);
        } else {
          return 'Anda tidak memiliki akses';
        }
        break;

      case 'form_absensi':
        $id_pelatihan = $request->id_pelatihan;
        $pelatihan = DB::connection('pgsql_paud')->table('kegiatan_pendukung.pelatihan_jadwals as j')
          ->join('kegiatan_pendukung.pelatihan_materis as m', 'm.id', 'j.materi_id')
          ->join('public.sekolah as s', 'j.npsn', 's.npsn')
          ->where('j.id', $id_pelatihan)
          ->selectRaw("j.*,m.nama as nama_materi,s.nama as nama_sekolah")
          ->first();

        $data = [
          'pelatihan' => $pelatihan,
        ];
        return view('bunda.pelatihan.form_absensi', $data);
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
          'pelatihan_id' => $id_kegiatan,
        ];

        $cari = DB::table('public.pelatihan_absensi')->where('nik', $nik)->where('pelatihan_id', $id_kegiatan)->whereNull('deleted_at')->first();
        if (!empty($cari)) {
          return ['code' => 250, 'pesan' => 'Data ' . $nik . ', Sudah melakukan absensi'];
        } else {
          $simpan = DB::table('public.pelatihan_absensi')->insert($data_absensi);
          if ($simpan) {
            return ['code' => 200, 'data' => 'Berhasil disimpan'];
          } else {
            return ['code' => 250, 'data' => 'Gagal menyimpan'];
          }
        }
        break;

      case 'rekap_absensi':
        $id_pelatihan = $request->id_pelatihan;
        $pelatihan = DB::connection('pgsql_paud')->table('kegiatan_pendukung.pelatihan_jadwals as j')
          ->join('kegiatan_pendukung.pelatihan_materis as m', 'm.id', 'j.materi_id')
          ->join('public.sekolah as s', 'j.npsn', 's.npsn')
          ->where('j.id', $id_pelatihan)
          ->selectRaw("j.*,m.nama as nama_materi,s.nama as nama_sekolah")
          ->first();

        $absensi = DB::table('public.pelatihan_absensi')->whereNull('deleted_at')
          ->where('pelatihan_id', $id_pelatihan)->get();

        $data = [
          'pelatihan' => $pelatihan,
          'absensi' => $absensi
        ];

        return view('bunda.pelatihan.rekap_absensi', $data);
        break;

      default:
        return '404 - Halaman tidak ditemukan';
        break;
    }
  }
}
