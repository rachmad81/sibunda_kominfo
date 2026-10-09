<?php

namespace App\Http\Controllers\Penilai\Penilaian;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Bunda\LombaController;
use Illuminate\Http\Request;
use App\Http\Libraries\UraianUtamaLomba;
use App\Http\Libraries\UraianPendukungLomba;
use DB, DataTables, Auth;

class Tahap1Controller extends Controller
{
  function index(Request $request)
  {
    $tahun = $request->tahun;
    return view('penilai.penilaian.' . $tahun . '.tahap1.index', ['tahun' => $tahun]);
    // return view('penilai.penilaian.berakhir');
  }

  function pembagian_penilaian()
  {
    $id = Auth::getUser()->id;
    if (in_array($id, [908, 1008])) {
      $kec_kelompok = [
        'ASEM ROWO',
        'BENOWO',
        'BUBUTAN',
        'BULAK',
        'DUKUH PAKIS',
        'GAYUNGAN',
        'GENTENG',
        'GUBENG',
        'GUNUNG ANYAR',
      ];
    } else if (in_array($id, [911, 1007])) {
      $kec_kelompok = [
        'JAMBANGAN',
        'KARANG PILANG',
        'KENJERAN',
        'KREMBANGAN',
        'LAKARSANTRI',
        'MULYOREJO',
        'PABEAN CANTIAN',
        'PAKAL',
      ];
    } else if (in_array($id, [907, 1006])) {
      $kec_kelompok = [
        'kec_instansi',
        'RUNGKUT',
        'SAMBIKEREP',
        'SAWAHAN',
        'SEMAMPIR',
        'SIMOKERTO',
        'SUKOLILO',
        'SUKOMANUNGGAL',
      ];
    } else if (in_array($id, [947, 1076])) {
      $kec_kelompok = [
        'TAMBAKSARI',
        'TANDES',
        'TEGALSARI',
        'TENGGILIS MEJOYO',
        'WIYUNG',
        'WONOCOLO',
        'WONOKROMO',
      ];
    }

    $kelompok = DB::table('public.kelurahan as kel')
      ->join('public.kecamatan as kec', 'kec.kecamatan_kode', 'kel.kecamatan_kode')->select('kelurahan_dispenduk')->whereIn('kec.kecamatan_dispenduk', $kec_kelompok)->get();
    $kel_kelompok = collect($kelompok)->pluck('kelurahan_dispenduk')->toArray();

    $datanya = [
      'kecamatan' => $kec_kelompok,
      'kelurahan' => $kel_kelompok,
    ];

    return $datanya;
  }

  function get_data(Request $request)
  {
    $tahun = $request->tahun;
    $utama = $request->utama;
    $pendukung = $request->pendukung;

    $where = "";

    if ($tahun == '2024') {
      if (in_array(Auth::getUser()->id, [907, 909, 910])) {
        $where = " AND kec_instansi is not null";
      } elseif (in_array(Auth::getUser()->id, [908, 911, 912, 913])) {
        $where = " AND kel_instansi is not null";
      }
    } else if ($tahun == '2025') {
      $pembagian_penilaian = $this->pembagian_penilaian();
      $data_kecamatan = "'" . implode("','", $pembagian_penilaian['kecamatan']) . "'";
      $data_kelurahan = "'" . implode("','", $pembagian_penilaian['kelurahan']) . "'";
      $where .= " AND (kec_instansi in ($data_kecamatan)";
      $where .= " OR kel_instansi in ($data_kelurahan))";
    } else if ($tahun == '2026') {
      $pembagian_penilaian = $this->pembagian_penilaian();
      $data_kecamatan = "'" . implode("','", $pembagian_penilaian['kecamatan']) . "'";
      $data_kelurahan = "'" . implode("','", $pembagian_penilaian['kelurahan']) . "'";
      $where .= " AND (kec_instansi in ($data_kecamatan)";
      $where .= " OR kel_instansi in ($data_kelurahan))";
    }

    $data = DB::table('cutoff_rekap.peserta_lomba')
      ->whereRaw("tahun='$tahun' $where")->get();
    $nilai_utama = DB::table('cutoff_rekap.nilai_kriteria_utama')
      ->whereRaw("tahun='$tahun' AND penilai_id='" . Auth::User()->id . "'")->get();
    $nilai_pendukung = DB::table('cutoff_rekap.nilai_kriteria_pendukung')
      ->whereRaw("tahun='$tahun' AND penilai_id='" . Auth::User()->id . "'")->get();
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

      $status_utama = '';
      $status_pendukung = '';

      $data_utama = $kriteria_utama->where('user_id', $d->user_id)->first();
      if (!empty($data_utama)) {
        $status_utama = ($data_utama->status == 'Selesai') ? 'Belum dinilai' : 'Belum diajukan oleh Bunda';
      } else {
        $status_utama = 'Belum diajukan oleh Bunda';
      }

      $data_pendukung = $kriteria_pendukung->where('user_id', $d->user_id)->first();
      if (!empty($data_pendukung)) {
        $status_pendukung = ($data_pendukung->status == 'Selesai') ? 'Belum dinilai' : 'Belum diajukan oleh Bunda';
      } else {
        $status_pendukung = 'Belum diajukan oleh Bunda';
      }

      $data_nilai_utama = $nilai_utama->where('user_id', $d->user_id)->first();
      if (!empty($data_nilai_utama)) {

        $nilai = 0;
        for ($i = 0; $i < count($nama_utama); $i++) {
          $kolom = 'nilai_' . $nama_utama[$i];
          if (!is_null($data_nilai_utama->$kolom)) {
            $nilai += collect(json_decode($data_nilai_utama->$kolom))->sum('file');
          }
        }

        $status_utama = $data_nilai_utama->status . ' (' . $nilai . ' Point)';
      }

      $data_nilai_pendukung = $nilai_pendukung->where('user_id', $d->user_id)->first();
      if (!empty($data_nilai_pendukung)) {
        $nilai = 0;
        for ($i = 0; $i < count($nama_pendukung); $i++) {
          $kolom = 'nilai_' . $nama_pendukung[$i];
          if (!is_null($data_nilai_pendukung->$kolom)) {
            $nilai += collect(json_decode($data_nilai_pendukung->$kolom))->sum('file');
          }
        }

        $status_pendukung = $data_nilai_pendukung->status . ' (' . $nilai . ' Point)';
      }

      $d->status_utama = ($status_utama == 'Belum diajukan oleh Bunda') ? $status_utama : $status_utama . '<br><a href="javascript:void(0)" class="btn btn-sm btn-success" onclick="penilaian_utama(\'' . $d->user_id . '\')">Isi Penilaian Utama</a>';
      $d->status_pendukung = ($status_pendukung == 'Belum diajukan oleh Bunda') ? $status_pendukung : $status_pendukung . '<br><a href="javascript:void(0)" class="btn btn-sm btn-success" onclick="penilaian_pendukung(\'' . $d->user_id . '\')">Isi Penilaian Pendukung</a>';
      // $d->status_utama = ($status_utama=='Belum diajukan oleh Bunda') ? $status_utama : $status_utama;
      // $d->status_pendukung = ($status_pendukung=='Belum diajukan oleh Bunda') ? $status_pendukung : $status_pendukung;
    }

    $data = collect($data)->sortBy([
      ['tingkat', 'asc']
    ])->all();

    $param = [
      'data' => $data,
    ];

    $content = view('penilai.penilaian.' . $tahun . '.tahap1.baris', $param)->render();

    return ['content' => $content];
  }

  function penilaian_utama(Request $request)
  {
    $user_id = $request->user_id;
    $tahun = $request->tahun;

    $uraian_kriteria_utama = UraianUtamaLomba::cek_tahun($tahun);

    $identitas = DB::table('cutoff_rekap.peserta_lomba as l')
      ->join('public.users as u', 'u.id', 'l.user_id')
      ->selectRaw("l.*,u.tingkat")
      ->whereRaw("user_id='$user_id' AND tahun='$tahun'")->first();
    $berkas = DB::table('cutoff_rekap.kriteria_utama')->whereRaw("user_id='$user_id' AND tahun='$tahun'")->first();
    $nilai = DB::table('cutoff_rekap.nilai_kriteria_utama')->whereRaw("user_id='$user_id' AND tahun='$tahun' AND penilai_id='" . Auth::User()->id . "'")->first();

    $where_ka = [
      'tahun_lomba' => $tahun,
      'user_id' => $user_id,
      'deleted_at' => null,
    ];
    $kegiatan_apresiasi = DB::table('cutoff_rekap.kegiatan_apresiasi as ka')
      ->join('public.kegiatan as k', 'k.id_kegiatan', 'ka.kegiatan_id')
      ->join('public.program as p', 'p.id_program', 'k.program_id')
      ->selectRaw("ka.id,k.id_kegiatan,k.nama_kegiatan,ka.sub,p.tahun")
      ->where($where_ka)->get();

    $data = [
      'identitas' => $identitas,
      'berkas' => $berkas,
      'nilai' => $nilai,
      'tahun' => $tahun,
      'uraian_kriteria_utama' => $uraian_kriteria_utama,
      'kegiatan_apresiasi' => $kegiatan_apresiasi,
    ];
    $content = view('penilai.penilaian.' . $tahun . '.tahap1.penilaian_utama', $data)->render();

    return ['content' => $content];
  }

  function penilaian_pendukung(Request $request)
  {
    $user_id = $request->user_id;
    $tahun = $request->tahun;

    $uraian_kriteria_pendukung = UraianPendukungLomba::cek_tahun($tahun);

    $identitas = DB::table('cutoff_rekap.peserta_lomba as l')
      ->join('public.users as u', 'u.id', 'l.user_id')
      ->selectRaw("l.*,u.tingkat")
      ->whereRaw("user_id='$user_id' AND tahun='$tahun'")->first();
    $berkas = DB::table('cutoff_rekap.kriteria_pendukung')->whereRaw("user_id='$user_id' AND tahun='$tahun'")->first();
    $nilai = DB::table('cutoff_rekap.nilai_kriteria_pendukung')->whereRaw("user_id='$user_id' AND tahun='$tahun' AND penilai_id='" . Auth::User()->id . "'")->first();

    $where_ka = [
      'ka.tahun_lomba' => $tahun,
      'ka.user_id' => $user_id,
      'ka.deleted_at' => null,
    ];

    $kegiatan_apresiasi = DB::table('cutoff_rekap.mitra_apresiasi as ka')
      ->join('public.kegiatan as k', 'k.id_kegiatan', 'ka.mitra_id')
      ->join('public.program as p', 'p.id_program', 'k.program_id')
      ->selectRaw("ka.id,k.nama_kegiatan as nama_mitra,k.id_kegiatan as id_mitra,ka.sub,ka.jenis,CAST(p.tahun as INTEGER) as tahun")
      ->where($where_ka)->where('jenis', 'kegiatan');

    $mitra_apresiasi = DB::table('cutoff_rekap.mitra_apresiasi as ka')
      ->join('cutoff_rekap.mitra_daftar as md', 'md.id_mitra', 'ka.mitra_id')
      ->selectRaw("ka.id,md.nama_mitra,md.id_mitra,ka.sub,ka.jenis," . date('Y') . " as tahun")
      ->where($where_ka)->where('jenis', 'mitra')->union($kegiatan_apresiasi)->orderBy('jenis', 'DESC')->get();

    $data = [
      'identitas' => $identitas,
      'berkas' => $berkas,
      'nilai' => $nilai,
      'tahun' => $tahun,
      'uraian_kriteria_pendukung' => $uraian_kriteria_pendukung,
      'mitra_apresiasi' => $mitra_apresiasi,
    ];
    $content = view('penilai.penilaian.' . $tahun . '.tahap1.penilaian_pendukung', $data)->render();

    return ['content' => $content];
  }
}
