<?php

namespace App\Http\Controllers\Dinas;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Bunda\LombaController as LombaBunda;
use Illuminate\Http\Request;
use App\Models\Kegiatan;
use App\Http\Libraries\UraianUtamaLomba;
use App\Http\Libraries\UraianPendukungLomba;
use DB, Auth, Session;

class LombaController extends Controller
{
  function index()
  {
    return view('dinas.lomba.index');
  }

  function get_data(Request $request)
  {
    $tahun = $request->tahun;

    $data = DB::table('cutoff_rekap.peserta_lomba')
      ->whereRaw("tahun='$tahun'")->get();
    $nilai_utama = DB::table('cutoff_rekap.nilai_kriteria_utama')
      ->whereRaw("tahun='$tahun' AND status='Selesai'")->get();
    $nilai_pendukung = DB::table('cutoff_rekap.nilai_kriteria_pendukung')
      ->whereRaw("tahun='$tahun' AND status='Selesai'")->get();
    $nilai_tahap2 = DB::table('cutoff_rekap.nilai_kriteria_tahap2')
      ->whereRaw("tahun='$tahun' AND status='Selesai'")->get();
    $kriteria_utama = DB::table('cutoff_rekap.kriteria_utama')
      ->whereRaw("tahun='$tahun'")->get();
    $kriteria_pendukung = DB::table('cutoff_rekap.kriteria_pendukung')
      ->whereRaw("tahun='$tahun'")->get();

    $uraian_utama = UraianUtamaLomba::cek_tahun($tahun);
    $nama_utama = collect($uraian_utama)->pluck('name');
    $uraian_pendukung = UraianPendukungLomba::cek_tahun($tahun);
    $nama_pendukung = collect($uraian_pendukung)->pluck('name');

    foreach ($data as $k => $d) {
      $d->no = ($k + 1);
      $d->identitas = '<a href="javascript:void(0)" class="btn btn-sm btn-warning" onclick="modal_identitas(\'' . $d->user_id . '\')">Identitas Peserta</a>';
      $d->tingkat = ($d->kec_instansi == null) ? 'Kelurahan' : 'Kecamatan';

      $status_utama = 0;
      $status_pendukung = 0;
      $status_tahap2 = 0;
      $st_kriteria_utama = '';
      $st_kriteria_pendukung = '';

      // KRITERIA UTAMA
      $data_nilai_utama = $nilai_utama->where('user_id', $d->user_id);
      $nilai = 0;
      foreach ($data_nilai_utama as $nu) {
        for ($i = 0; $i < count($nama_utama); $i++) {
          $kolom = 'nilai_' . $nama_utama[$i];
          if (!is_null($nu->$kolom)) {
            $nilai += collect(json_decode($nu->$kolom))->sum('file');
          }
        }

        $status_utama = ($nilai * 4);
      }

      // KRITERIA PENDUKUNG
      $data_nilai_pendukung = $nilai_pendukung->where('user_id', $d->user_id);
      $nilai = 0;
      foreach ($data_nilai_pendukung as $np) {
        for ($i = 0; $i < count($nama_pendukung); $i++) {
          $kolom = 'nilai_' . $nama_pendukung[$i];
          if (!is_null($np->$kolom)) {
            $nilai += collect(json_decode($np->$kolom))->sum('file');
          }
        }

        $status_pendukung = ($nilai * 2);
      }

      // TAHAP 2
      $data_nilai_tahap2 = $nilai_tahap2->where('user_id', $d->user_id);
      $nilai = 0;
      foreach ($data_nilai_tahap2 as $nth) {
        $hasil = json_decode($nth->hasil);
        $nilai += collect($hasil)->sum('nilai');
      }
      switch ($tahun) {
        case '2024':
          $status_tahap2 = $nilai * 10;
          break;
        case '2025':
          $status_tahap2 = $nilai;
          break;
        case '2026':
          $status_tahap2 = $nilai;
          break;

        default:
          $status_tahap2 = $nilai * 10;
          break;
      }

      $data_kriteria_utama = $kriteria_utama->where('user_id', $d->user_id)->first();
      $data_kriteria_pendukung = $kriteria_pendukung->where('user_id', $d->user_id)->first();
      $st_kriteria_utama = '';
      $st_kriteria_pendukung = '';
      if (!empty($data_kriteria_utama)) {
        $st_kriteria_utama .= '<a href="javascript:void(0)" class="btn btn-success" onclick="detail(\'' . $d->user_id . '\',\'utama\')">Lihat</a>';
        if ($data_kriteria_utama->status == 'Selesai') {
          $st_kriteria_utama .= '<br><a href="javascript:void(0)" onclick="reset(\'' . $data_kriteria_utama->id . '\',\'kriteria_utama\')" class="btn btn-sm btn-info">Reset Pengisian</a>';
        }
      }
      if (!empty($data_kriteria_pendukung)) {
        $st_kriteria_pendukung .= '<a href="javascript:void(0)" class="btn btn-success" onclick="detail(\'' . $d->user_id . '\',\'pendukung\')">Lihat</a>';
        if ($data_kriteria_pendukung->status == 'Selesai') {
          $st_kriteria_pendukung .= '<br><a href="javascript:void(0)" onclick="reset(\'' . $data_kriteria_pendukung->id . '\',\'kriteria_pendukung\')" class="btn btn-sm btn-info">Reset Pengisian</a>';
        }
      }

      $d->nilai = '<a href="javascript:void(0)" onclick="modal_nilai(\'' . $d->user_id . '\')">' . ($status_utama + $status_pendukung) . ' Point</a>';
      $d->nilai_tahap2 = '<a href="javascript:void(0)" onclick="modal_nilai2(\'' . $d->user_id . '\')">' . ($status_tahap2) . ' Point</a>';
      $d->nilai_angka = ($status_utama + $status_pendukung);
      $d->nilai_angka2 = $status_tahap2;
      $d->nilai_akhir = number_format(((40 / 100) * ($status_utama + $status_pendukung)) + ((60 / 100) * $status_tahap2), 2, '.', '') . ' Point';
      $d->st_kriteria_utama = $st_kriteria_utama;
      $d->st_kriteria_pendukung = $st_kriteria_pendukung;
    }

    $data = collect($data)->sortBy([
      ['tingkat', 'asc'],
      ['nilai_akhir', 'desc'],
    ])->all();

    return ['data' => $data];
  }

  function modal_nilai(Request $request)
  {
    $user_id = $request->user_id;
    $tahun = $request->tahun;
    $judul = 'Rincian penilaian dan status penilaian';

    $tahun = $request->tahun;

    $penilai = DB::table('users')->where('role', 'penilai')->where('verified', 'true')->orderBy('username')->get();

    $nilai_utama = DB::table('cutoff_rekap.nilai_kriteria_utama')
      ->whereRaw("tahun='$tahun' AND user_id='$user_id'")->get();
    $nilai_pendukung = DB::table('cutoff_rekap.nilai_kriteria_pendukung')
      ->whereRaw("tahun='$tahun' AND user_id='$user_id'")->get();
    $kriteria_utama = DB::table('cutoff_rekap.kriteria_utama')
      ->whereRaw("tahun='$tahun' AND user_id='$user_id'")->first();
    $kriteria_pendukung = DB::table('cutoff_rekap.kriteria_pendukung')
      ->whereRaw("tahun='$tahun' AND user_id='$user_id'")->first();

    $uraian_utama = UraianUtamaLomba::cek_tahun($tahun);
    $nama_utama = collect($uraian_utama)->pluck('name');
    $uraian_pendukung = UraianPendukungLomba::cek_tahun($tahun);
    $nama_pendukung = collect($uraian_pendukung)->pluck('name');

    if (!empty($kriteria_utama) && !is_null($kriteria_utama->status)) {
      $status_utama = $kriteria_utama->status;
    } else {
      $status_utama = 'Belum diajukan oleh bunda';
    }
    if (!empty($kriteria_pendukung) && !is_null($kriteria_pendukung->status)) {
      $status_pendukung = $kriteria_pendukung->status;
    } else {
      $status_pendukung = 'Belum diajukan oleh bunda';
    }

    foreach ($penilai as $p) {
      $penilaian_utama =  collect($nilai_utama)->where('penilai_id', $p->id)->first();
      $nilai_angka_utama = 0;
      $nilai_angka_selesai_utama = 0;
      if (!empty($penilaian_utama)) {
        for ($i = 0; $i < count($nama_utama); $i++) {
          $kolom = 'nilai_' . $nama_utama[$i];
          if (!is_null($penilaian_utama->$kolom)) {
            $nilai_angka_utama += collect(json_decode($penilaian_utama->$kolom))->sum('file');
            if ($penilaian_utama->status == 'Selesai') {
              $nilai_angka_selesai_utama += collect(json_decode($penilaian_utama->$kolom))->sum('file');
            }
          }
        }
      }
      if (!empty($penilaian_utama)) {
        $status_utama = $penilaian_utama->status . ' (' . $nilai_angka_utama . ' Point)';
        if ($penilaian_utama->status == 'Selesai') {
          $status_utama .= '&nbsp;<a href="javascript:void(0)" onclick="reset(\'' . $penilaian_utama->id . '\',\'nilai_kriteria_utama\')"><i class="fa fa-check" style="font-size: 13pt;color: green"></i></a>';
        }
        $p->status_utama = $status_utama;
      } else {
        $p->status_utama = 'Belum dinilai';
      }


      $penilaian_pendukung =  collect($nilai_pendukung)->where('penilai_id', $p->id)->first();
      $nilai_angka_pendukung = 0;
      $nilai_angka_selesai_pendukung = 0;
      if (!empty($penilaian_pendukung)) {
        for ($i = 0; $i < count($nama_pendukung); $i++) {
          $kolom = 'nilai_' . $nama_pendukung[$i];
          if (!is_null($penilaian_pendukung->$kolom)) {
            $nilai_angka_pendukung += collect(json_decode($penilaian_pendukung->$kolom))->sum('file');
            if ($penilaian_pendukung->status == 'Selesai') {
              $nilai_angka_selesai_pendukung += collect(json_decode($penilaian_pendukung->$kolom))->sum('file');
            }
          }
        }
      }
      if (!empty($penilaian_pendukung)) {
        $status_pendukung = $penilaian_pendukung->status . ' (' . $nilai_angka_pendukung . ' Point)';
        if ($penilaian_pendukung->status == 'Selesai') {
          $status_pendukung .= '&nbsp;<a href="javascript:void(0)" onclick="reset(\'' . $penilaian_pendukung->id . '\',\'nilai_kriteria_pendukung\')"><i class="fa fa-check" style="font-size: 13pt;color: green"></i></a>';
        }
        $p->status_pendukung = $status_pendukung;
      } else {
        $p->status_pendukung = 'Belum dinilai';
      }
      $p->jumlah_utama = $nilai_angka_utama;
      $p->jumlah_pendukung = $nilai_angka_pendukung;
      $p->jumlah_utama_selesai = $nilai_angka_selesai_utama;
      $p->jumlah_pendukung_selesai = $nilai_angka_selesai_pendukung;
    }

    $data = [
      'judul' => $judul,
      'penilai' => $penilai,
    ];
    $content = view('dinas.lomba.' . $tahun . '.modal_nilai', $data)->render();

    return ['content' => $content];
  }

  function modal_nilai2(Request $request)
  {
    $user_id = $request->user_id;
    $tahun = $request->tahun;
    $judul = 'Rincian penilaian dan status penilaian';

    $tahun = $request->tahun;

    $penilai = DB::table('users')->where('role', 'penilai')->where('verified', 'true')->orderBy('username')->get();

    $nilai_utama = DB::table('cutoff_rekap.nilai_kriteria_tahap2')
      ->whereRaw("tahun='$tahun' AND user_id='$user_id'")->get();

    foreach ($penilai as $p) {
      $penilaian_utama =  collect($nilai_utama)->where('penilai_id', $p->id)->first();
      $nilai_angka_utama = 0;
      $nilai_angka_selesai_utama = 0;
      if (!empty($penilaian_utama)) {
        $kolom = 'hasil';
        if (!is_null($penilaian_utama->$kolom)) {
          $nilai_angka_utama += collect(json_decode($penilaian_utama->$kolom))->sum('nilai');
          if ($penilaian_utama->status == 'Selesai') {
            $nilai_angka_selesai_utama += collect(json_decode($penilaian_utama->$kolom))->sum('nilai');
          }
        }
      }

      if (!empty($penilaian_utama)) {
        $status_utama = $penilaian_utama->status . ' (' . $nilai_angka_utama . ' Point)';
        if ($penilaian_utama->status == 'Selesai') {
          $status_utama .= '&nbsp;<a href="javascript:void(0)" onclick="reset(\'' . $penilaian_utama->id . '\',\'nilai_kriteria_tahap2\')"><i class="fa fa-check" style="font-size: 13pt;color: green"></i></a>';
        }
        $p->status_utama = $status_utama;
      } else {
        $p->status_utama = 'Belum dinilai';
      }

      $p->jumlah_utama = $nilai_angka_utama;
      $p->jumlah_utama_selesai = $nilai_angka_selesai_utama;
    }

    $data = [
      'judul' => $judul,
      'penilai' => $penilai,
    ];
    $content = view('dinas.lomba.' . $tahun . '.modal_nilai2', $data)->render();

    return ['content' => $content];
  }

  function reset(Request $request)
  {
    $id = $request->id;
    $tabel = $request->tabel;

    $cek = DB::table('cutoff_rekap.' . $tabel)->where('id', $id)->first();
    if (!empty($cek)) {
      $data_update = ['status' => 'Draft'];
      if ($tabel == 'nilai_kriteria_utama') {
        $nama_kolom = [
          'nilai_inovasi' => null,
          'nilai_partisipasi' => null,
          'nilai_forum' => null,
          'nilai_pelaksanaan_program' => null,
          'nilai_implementasi_program_transisi' => null,
          'nilai_koordinasi_program_transisi' => null,
        ];
        $data_update = array_merge($data_update, $nama_kolom);
      } else if ($tabel == 'nilai_kriteria_pendukung') {
        $nama_kolom = [
          'nilai_kolaborasi' => null,
          'nilai_kegiatan_parenting' => null,
          'nilai_pembinaan_transisi' => null,
          'nilai_sosis_transisi' => null,
          'nilai_monitoring' => null,
          'nilai_kolom_f' => null,
          'nilai_kolom_g' => null,
          'nilai_kolom_h' => null,
          'nilai_kolom_i' => null,
        ];
        $data_update = array_merge($data_update, $nama_kolom);
      }
      $update = DB::table('cutoff_rekap.' . $tabel)->where('id', $id)->update($data_update);
      if ($update) {
        return ['code' => '200', 'message' => 'Berhasil reset', 'user_id' => $cek->user_id];
      } else {
        return ['code' => '250', 'message' => 'Gagal reset'];
      }
    } else {
      return ['code' => '250', 'message' => 'Data tidak ditemukan'];
    }

    return ['code' => '250', 'message' => 'Data tidak ditemukan'];
  }

  function detail(Request $request)
  {
    $return = '';
    if ($request->jenis == 'utama') {
      $return = $this->utama($request);
    } else {
      $return = $this->pendukung($request);
    }

    return $return;
  }

  function utama(Request $request)
  {
    $tahun = $request->tahun;
    $user_id = $request->user_id;
    $user = DB::table('users as u')->where('id', $user_id)->first();
    $berkas = DB::table('cutoff_rekap.kriteria_utama')->whereRaw("tahun=? AND user_id=?", [$tahun, $user_id])->first();

    $th = $tahun;
    $where = '';
    if ($user->id_kecamatan != null) {
      $where .= " AND p.id_kecamatan='" . $user->id_kecamatan . "'";
    }
    if ($user->id_kelurahan != null) {
      $where .= " AND p.id_kelurahan='" . $user->id_kelurahan . "'";
    }
    if ($user->tingkat != null) {
      $where .= " AND p.tingkat='" . $user->tingkat . "'";
    }

    $where_ka = [
      'tahun_lomba' => $tahun,
      'user_id' => $user_id,
      'deleted_at' => null,
    ];
    $kegiatan_apresiasi = DB::table('cutoff_rekap.kegiatan_apresiasi as ka')
      ->join('public.kegiatan as k', 'k.id_kegiatan', 'ka.kegiatan_id')
      ->selectRaw("ka.id,k.id_kegiatan,k.nama_kegiatan,ka.sub")
      ->where($where_ka)->get();

    $id_sudah = $kegiatan_apresiasi->pluck('id_kegiatan')->toArray();

    $kegiatan = Kegiatan::join('public.program as p', 'kegiatan.program_id', 'p.id_program')
      ->selectRaw("kegiatan.id_kegiatan,p.nama_kegiatan as nama_program,kegiatan.nama_kegiatan")
      ->whereRaw("p.tahun='$th' $where")
      // ->whereNotIn('kegiatan.id_kegiatan',$id_sudah)
      ->orderBy('p.nama_kegiatan', 'asc')->get();

    $uraian_kriteria_utama = UraianUtamaLomba::cek_tahun($tahun);;

    $data = [
      'judul' => 'Kriteria Utama',
      'kriteria' => $uraian_kriteria_utama,
      'berkas' => $berkas,
      'kegiatan' => $kegiatan,
      'kegiatan_apresiasi' => $kegiatan_apresiasi,
    ];

    $content = view('dinas.lomba.' . $tahun . '.detail_utama', $data)->render();

    return ['code' => '200', 'content' => $content];
  }

  function pendukung(Request $request)
  {
    $tahun = $request->tahun;
    $user_id = $request->user_id;
    $user = DB::table('users as u')->where('id', $user_id)->first();
    $berkas = DB::table('cutoff_rekap.kriteria_pendukung')->whereRaw("tahun=? AND user_id=?", [$tahun, $user_id])->first();

    $th = $tahun;
    $where = '';
    if ($user->id_kecamatan != null) {
      $where .= " AND p.id_kecamatan='" . $user->id_kecamatan . "'";
    }
    if ($user->id_kelurahan != null) {
      $where .= " AND p.id_kelurahan='" . $user->id_kelurahan . "'";
    }
    if ($user->tingkat != null) {
      $where .= " AND p.tingkat='" . $user->tingkat . "'";
    }

    $where_ma = [
      'ma.tahun_lomba' => $tahun,
      'ma.user_id' => $user_id,
      'ma.deleted_at' => null,
    ];

    $kegiatan_apresiasi = DB::table('cutoff_rekap.mitra_apresiasi as ma')
      ->join('public.kegiatan as k', 'k.id_kegiatan', 'ma.mitra_id')
      ->selectRaw("ma.id,k.nama_kegiatan,k.id_kegiatan,ma.sub,ma.jenis")
      ->where($where_ma)->whereRaw("jenis='kegiatan'");

    $mitra_apresiasi = DB::table('cutoff_rekap.mitra_apresiasi as ma')
      ->join('cutoff_rekap.mitra_daftar as md', 'md.id_mitra', 'ma.mitra_id')
      ->selectRaw("ma.id,md.nama_mitra as nama_kegiatan,md.id_mitra as id_kegiatan,ma.sub,ma.jenis")
      ->where($where_ma)->whereRaw("jenis='mitra'")->union($kegiatan_apresiasi)->orderBy('jenis', 'DESC')->get();

    $id_sudah = $mitra_apresiasi->pluck('id_mitra')->toArray();

    $where_ka = [
      'deleted_at' => null,
    ];
    $mitra_daftar = DB::table('cutoff_rekap.mitra_daftar as ka')
      ->where($where_ka)
      // ->whereNotIn('id_mitra',$id_sudah)
      ->get();

    $kegiatan = Kegiatan::join('public.program as p', 'kegiatan.program_id', 'p.id_program')
      ->selectRaw("kegiatan.id_kegiatan,p.nama_kegiatan as nama_program,kegiatan.nama_kegiatan")
      ->whereRaw("p.tahun='$th' $where")
      // ->whereNotIn('kegiatan.id_kegiatan',$id_sudah)
      ->orderBy('p.nama_kegiatan', 'asc')->get();

    $uraian_kriteria_pendukung = UraianPendukungLomba::cek_tahun($tahun);;

    $data = [
      'judul' => 'Kriteria Pendukung',
      'kriteria' => $uraian_kriteria_pendukung,
      'berkas' => $berkas,
      'kegiatan' => $kegiatan,
      'mitra_daftar' => $mitra_daftar,
      'kegiatan_apresiasi' => $mitra_apresiasi,
    ];

    $content = view('dinas.lomba.' . $tahun . '.detail_pendukung', $data)->render();

    return ['code' => '200', 'content' => $content];
  }
}
