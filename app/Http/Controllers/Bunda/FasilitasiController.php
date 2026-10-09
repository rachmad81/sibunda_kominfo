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
use App\Models\Fasilitasi\Jadwal;
use App\Models\Fasilitasi\Pendamping;
use App\Models\Fasilitasi\Materi;
use App\Http\Libraries\GetData;
use DataTables, DB, Session, Auth;
use App\Tools\Bantuan;
use Carbon\Carbon;

class FasilitasiController extends Controller
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

    $materi = Materi::orderBy('nama', 'ASC')->get();

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
    return view('bunda.fasilitasi.index', $data);
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

    $where_lokasi = "";
    $where = "true";

    if ($status != 'Semua') {
      $where .= " AND status_jadwal='$status'";
    }

    if ($kec != 'semua') {
      $where_lokasi .= " AND kec_id='$kec'";
    }
    if ($kel != 'semua') {
      $where_lokasi .= " AND desa='$kel'";
    }
    if ($jenjang != 'semua') {
      $where_lokasi .= " AND jenjang='$jenjang'";
    }

    if ($bulan == 'semua') {
      $where .= " AND (CAST(tanggal as varchar) ilike '%$tahun-%' OR tanggal is null)";
    } else {
      $where .= " AND (CAST(tanggal as varchar) ilike '%$tahun-$bulan%' OR tanggal is null)";
    }

    if ($materi != 'semua') {
      $where .= " AND materi_id='$materi'";
    }

    if ($jenis != 'semua') {
      $where .= " AND jenis='$jenis'";
    }

    $sekolah = Sekolah::on('pgsql_paud')->join('public.master_user as u', 'u.npsn', 'sekolah.npsn')
      ->leftjoin('public.kecamatan as kec', 'kec.kecamatan_kode', 'sekolah.kec_id')
      ->leftjoin('public.kelurahan as kel', 'kel.kelurahan_kode', 'sekolah.desa')
      ->selectRaw("sekolah.npsn,sekolah.nama,kec.kecamatan_dispenduk,kel.kelurahan_dispenduk")->whereRaw("u.user_enable is true $where_lokasi")->get();
    $npsn = $sekolah->pluck('npsn');

    if ($tahun == '') {
      $bkb = [];
    } else {
      $bkb = Jadwal::on('pgsql_paud')
        ->join('kegiatan_pendukung.permohonan_fasilitasi_materis as m', 'm.id', 'permohonan_fasilitasi_jadwals.materi_id')
        ->selectRaw("permohonan_fasilitasi_jadwals.*,m.nama as nama_materi,m.id as id_materi")
        ->whereRaw("$where")->whereIn('npsn', $npsn)->get();
    }

    return DataTables()->of($bkb)
      ->addIndexColumn()
      ->editColumn('npsn', function ($akun) {
        $npsn = $akun->npsn;
        return '`' . $npsn;
      })
      ->editColumn('no_hp_penanggung_jawab', function ($akun) {
        $no_hp_penanggung_jawab = $akun->no_hp_penanggung_jawab;
        return '`' . $no_hp_penanggung_jawab;
      })
      ->addColumn('nama_sekolah', function ($akun) use ($sekolah) {
        $data_sekolah = $sekolah->where('npsn', $akun->npsn)->first();
        $nama_sekolah = $data_sekolah->nama;
        $akun->nama_kecamatan = $data_sekolah->kecamatan_dispenduk;
        $akun->nama_kelurahan = $data_sekolah->kelurahan_dispenduk;
        return $nama_sekolah;
      })
      ->addColumn('nama_kecamatan', function ($akun) {
        return $akun->nama_kecamatan;
      })
      ->addColumn('nama_kelurahan', function ($akun) {
        return $akun->nama_kelurahan;
      })
      ->addColumn('pendampings', function ($akun) {
        $pendamping = Pendamping::on('pgsql_paud')->where('permohonan_fasilitasi_jadwal_id', $akun->id)->orderBy('id', 'ASC')->get();
        $namanya = '';
        $jabatan = '';
        $no_hp = '';
        if ($pendamping->count() != 0) {
          foreach ($pendamping as $p) {
            $namanya .= $p->nama_pegawai . ' - ' . $p->jabatan . ' - (' . $p->hp . ' / ' . $p->hp2 . ') <br><br>';
            // $jabatan .= $p->jabatan.' <br><br>';
            // $no_hp .= $p->hp.' / '.$p->hp2.'<br><br>';
          }
        }

        // $akun->jabatan_pendamping = $jabatan;
        // $akun->no_hp_p = $no_hp;

        return $namanya;
      })
      // ->addColumn('jabatan_pendamping', function ($akun){
      // 	return $akun->jabatan_pendamping;
      // })
      // ->addColumn('no_hp_p', function ($akun){
      // 	return $akun->no_hp_p;
      // })
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
      ->addColumn('aksi', function ($akun) use ($tahun) {
        $button = '';

        if ($akun->status_jadwal == 'Pengajuan' || $akun->status_jadwal == 'Setujui') {
          $button .= '<a style="margin-left: 5px" href="javascript:void(0)" onclick="modal_konfirmasi(\'' . $akun->id . '\')" class="btn btn-sm btn-warning">Konfirmasi</a>';
        }
        return $button;
      })
      ->addColumn('data_klien', function ($j) {
        $klien = json_decode($j->klien);
        // $data_klien = '';
        // foreach($klien as $k=>$v){
        // 	$data_klien .= '=> '.$v.'<br><br>';
        // }
        return $j->jumlah_peserta;
      })
      ->addColumn('keterangan', function ($j) {
        if ($j->id_materi == '1') {
          $ket = '<b>Kegiatan</b><br>' . $j->kegiatan . '<br><br>';
          $ket .= '<b>Penanggungjawab</b><br>' . $j->nama_penanggung_jawab . '<br><br>';
          $ket .= '<b>Jumlah Bis</b><br>' . $j->jumlah_bis . '<br><br>';
          $ket .= '<b>Lokasi Jemput</b><br>' . $j->lokasi_jemput . '<br>';
          $ket .= '<b>Lokasi Antar</b><br>' . $j->lokasi_antar . '<br><br>';
          $ket .= '<b>Jam Mulai</b><br>' . $j->jam_mulai . '<br>';
          $ket .= '<b>Jam Selesai</b><br>' . $j->jam_selesai;
        } else if ($j->id_materi == '3') {
          $ket = '<b>Kegiatan</b><br>' . $j->kegiatan;
        } else if ($j->id_materi == '2') {
          // $url = 'https://filesdispendik.surabaya.go.id/profilsekolah_file/'.$j->file_bukti_kerusakan;
          $url = 'https://dispendik.surabaya.go.id/profilsekolah_file/' . $j->file_bukti_kerusakan;
          $ket = '<b>Kerusakan</b><br>' . $j->kerusakan . '<br><br>';
          $ket .= '<a href="' . $url . '" target="_blank" class="btn btn-sm btn-success">Bukti Kerusakan</a>';
        } else {
          $ket = 'Keterangan';
        }

        return $ket;
      })
      ->rawColumns(['aksi', 'pendampings', 'berkas', 'status', 'tanggal_pelaksanaan', 'data_klien', 'jabatan_pendamping', 'no_hp_p', 'keterangan'])
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

    return view('bunda.fasilitasi.kehadiran', $data);
  }

  function modal_konfirmasi(Request $request)
  {
    $id = $request->id;

    $jadwal = Jadwal::on('pgsql_paud')->join('kegiatan_pendukung.permohonan_fasilitasi_materis as m', 'm.id', 'permohonan_fasilitasi_jadwals.materi_id')
      ->selectRaw("permohonan_fasilitasi_jadwals.*,m.nama as nama_materi,m.id as id_materi")->where('permohonan_fasilitasi_jadwals.id', $id)->first();

    $sekolah = DB::connection('pgsql_paud')->table('public.sekolah as s')
      ->leftjoin('public.kecamatan as kec', 'kec.kecamatan_kode', 's.kec_id')
      ->leftjoin('public.kelurahan as kel', 'kel.kelurahan_kode', 's.desa')
      ->selectRaw("s.*,kec.kecamatan_dispenduk,kel.kelurahan_dispenduk")->where('npsn', $jadwal->npsn)->first();

    $pendampings = Pendamping::on('pgsql_paud')->where('permohonan_fasilitasi_jadwal_id', $id)->get();

    $data = [
      'jadwal' => $jadwal,
      'sekolah' => $sekolah,
      'pendampings' => $pendampings,
    ];

    $content = view('bunda.fasilitasi.modal_konfirmasi', $data)->render();

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
}
