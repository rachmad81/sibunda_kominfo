<?php

namespace App\Http\Controllers\Penilai\Penilaian;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Libraries\IndikatorTahap2;
use App\Http\Libraries\UraianUtamaLomba;
use App\Http\Libraries\UraianPendukungLomba;

use DB, Auth;

class Tahap2Controller extends Controller
{
  function index(Request $request)
  {
    $tahun = $request->tahun;
    return view('penilai.penilaian.' . $tahun . '.tahap2.index', ['tahun' => $tahun]);
  }

  function get_data(Request $request)
  {
    $tahun = $request->tahun;
    switch ($tahun) {
      case '2024':
        $penilai_kec = [907, 909, 910];
        $penilai_kel = [908, 911, 912, 913];
        break;
      case '2025':
        $penilai_kec = [907, 1005, 947, 1006];
        $penilai_kel = [1007, 908, 911, 913];
        break;
      case '2026':
        $penilai_kec = [908, 911, 1008, 1007];
        $penilai_kel = [907, 947, 1006, 1076];
        break;

      default:
        $penilai_kec = [908, 911, 1008, 1007];
        $penilai_kel = [907, 947, 1006, 1076];
        break;
    }

    $where = "";
    if (in_array(Auth::getUser()->id, $penilai_kec)) {
      $where = " AND kec_instansi is not null";
      $data = DB::table('cutoff_rekap.peserta_lomba')
        ->whereRaw("tahun='$tahun' $where")
        ->orderByRaw("nilai_akhir_tahap1 DESC")
        ->limit('9')
        ->get();
    } elseif (in_array(Auth::getUser()->id, $penilai_kel)) {
      $where = " AND kel_instansi is not null";
      $data = DB::table('cutoff_rekap.peserta_lomba')
        ->whereRaw("tahun='$tahun' $where")
        ->orderByRaw("nilai_akhir_tahap1 DESC")
        ->limit('12')
        ->get();
    } else {
      $data_kec = DB::table('cutoff_rekap.peserta_lomba')
        ->whereRaw("tahun='$tahun' AND kec_instansi is not null")
        ->orderByRaw("nilai_akhir_tahap1 DESC")
        ->limit('9')
        ->get();

      $data_kel = DB::table('cutoff_rekap.peserta_lomba')
        ->whereRaw("tahun='$tahun' AND kel_instansi is not null")
        ->orderByRaw("nilai_akhir_tahap1 DESC")
        ->limit('12')
        ->get();

      $data = array_merge($data_kec->toArray(), $data_kel->toArray());
    }

    if ($tahun == '2025') {
      $kriteria_utama = DB::table('cutoff_rekap.kriteria_utama')
        ->whereRaw("tahun='$tahun'")->get();
      $kriteria_pendukung = DB::table('cutoff_rekap.kriteria_pendukung')
        ->whereRaw("tahun='$tahun'")->get();

      $uraian_utama = UraianUtamaLomba::cek_tahun($tahun);
      $nama_utama = collect($uraian_utama)->pluck('name');
      $uraian_pendukung = UraianPendukungLomba::cek_tahun($tahun);
      $nama_pendukung = collect($uraian_pendukung)->pluck('name');
    } else if ($tahun == '2026') {
      $kriteria_utama = DB::table('cutoff_rekap.kriteria_utama')
        ->whereRaw("tahun='$tahun'")->get();
      $kriteria_pendukung = DB::table('cutoff_rekap.kriteria_pendukung')
        ->whereRaw("tahun='$tahun'")->get();

      $uraian_utama = UraianUtamaLomba::cek_tahun($tahun);
      $nama_utama = collect($uraian_utama)->pluck('name');
      $uraian_pendukung = UraianPendukungLomba::cek_tahun($tahun);
      $nama_pendukung = collect($uraian_pendukung)->pluck('name');
    }

    $nilai_tahap2 = DB::table('cutoff_rekap.nilai_kriteria_tahap2')
      ->whereRaw("tahun='$tahun' AND penilai_id='" . Auth::User()->id . "'")->get();

    foreach ($data as $k => $d) {
      $d->identitas = '<a href="javascript:void(0)" class="btn btn-sm btn-warning" onclick="modal_identitas(\'' . $d->user_id . '\')">Identitas Peserta</a>';
      $d->tingkat = ($d->kec_instansi == null) ? 'Kelurahan' : 'Kecamatan';

      $status_tahap2 = '';
      $dt_nilai = $nilai_tahap2->where('user_id', $d->user_id)->first();
      if (!empty($dt_nilai)) {
        $arr_nilai = json_decode($dt_nilai->hasil);
        switch ($tahun) {
          case '2024':
            $nilai = (collect($arr_nilai)->sum('nilai')) * 10;
            break;
          case '2025':
            $nilai = (collect($arr_nilai)->sum('nilai'));
            break;
          case '2026':
            $nilai = (collect($arr_nilai)->sum('nilai'));
            break;
          default:
            $nilai = (collect($arr_nilai)->sum('nilai')) * 10;
            break;
        }
      } else {
        $nilai = 0;
      }

      $data_utama = $nilai_tahap2->where('user_id', $d->user_id)->first();
      if (!empty($data_utama)) {
        $status_tahap2 = ($data_utama->status == 'Selesai') ? 'Selesai<br><a href="javascript:void(0)" onclick="modal_penilaian(\'' . $d->user_id . '\')" class="btn btn-sm btn-warning">Lihat Penilaian</a>' : 'Draft<br><a href="javascript:void(0)" onclick="modal_penilaian(\'' . $d->user_id . '\')" class="btn btn-sm btn-success">Beri Penilaian</a>';
      } else {
        $status_tahap2 = 'Belum dinilai<br><a href="javascript:void(0)" onclick="modal_penilaian(\'' . $d->user_id . '\')" class="btn btn-sm btn-success">Beri Penilaian</a>';
      }

      $d->status_tahap2 = $status_tahap2;
      $d->nilai = $nilai;

      if ($tahun == '2025') {
        $data_kriteria_utama = $kriteria_utama->where('user_id', $d->user_id)->first();
        $data_kriteria_pendukung = $kriteria_pendukung->where('user_id', $d->user_id)->first();
        $st_kriteria_utama = '';
        $st_kriteria_pendukung = '';

        if (!empty($data_kriteria_utama)) {
          $st_kriteria_utama .= '<a href="javascript:void(0)" class="btn btn-success" onclick="detail(\'' . $d->user_id . '\',\'utama\')">Lihat</a>';
        }
        if (!empty($data_kriteria_pendukung)) {
          $st_kriteria_pendukung .= '<a href="javascript:void(0)" class="btn btn-success" onclick="detail(\'' . $d->user_id . '\',\'pendukung\')">Lihat</a>';
        }

        $d->nilai_tahgap1 = '<a href="javascript:void(0)" style="font-size: 12pt" onclick="modal_nilai(\'' . $d->user_id . '\')">' . ($d->nilai_akhir_tahap1) . ' Point</a>';
        // $d->nilai_tahgap1 = ''.($d->nilai_akhir_tahap1).' Point';
        $d->status_tahap1_utama = $st_kriteria_utama;
        $d->status_tahap1_pendukung = $st_kriteria_pendukung;
      } elseif ($tahun == '2026') {
        $data_kriteria_utama = $kriteria_utama->where('user_id', $d->user_id)->first();
        $data_kriteria_pendukung = $kriteria_pendukung->where('user_id', $d->user_id)->first();
        $st_kriteria_utama = '';
        $st_kriteria_pendukung = '';

        if (!empty($data_kriteria_utama)) {
          $st_kriteria_utama .= '<a href="javascript:void(0)" class="btn btn-success" onclick="detail(\'' . $d->user_id . '\',\'utama\')">Lihat</a>';
        }
        if (!empty($data_kriteria_pendukung)) {
          $st_kriteria_pendukung .= '<a href="javascript:void(0)" class="btn btn-success" onclick="detail(\'' . $d->user_id . '\',\'pendukung\')">Lihat</a>';
        }

        $d->nilai_tahgap1 = '<a href="javascript:void(0)" style="font-size: 12pt" onclick="modal_nilai(\'' . $d->user_id . '\')">' . ($d->nilai_akhir_tahap1) . ' Point</a>';
        // $d->nilai_tahgap1 = ''.($d->nilai_akhir_tahap1).' Point';
        $d->status_tahap1_utama = $st_kriteria_utama;
        $d->status_tahap1_pendukung = $st_kriteria_pendukung;
      }
    }

    $data = collect($data)->sortBy([
      ['tingkat', 'asc'],
      ['nilai_akhir_tahap1', 'desc'],
      ['nilai', 'desc'],
    ]);

    $param = [
      'data' => $data,
    ];

    $content = view('penilai.penilaian.' . $tahun . '.tahap2.baris', $param)->render();

    return ['content' => $content];
  }

  function modal_penilaian(Request $request)
  {
    $user_id = $request->user_id;
    $tahun = $request->tahun;

    $tahap2 = IndikatorTahap2::cek_tahun($tahun);

    $identitas = DB::table('cutoff_rekap.peserta_lomba as l')
      ->join('public.users as u', 'u.id', 'l.user_id')
      ->selectRaw("l.*,u.tingkat")
      ->whereRaw("user_id='$user_id' AND tahun='$tahun'")->first();
    $nilai = DB::table('cutoff_rekap.nilai_kriteria_tahap2')->whereRaw("user_id='$user_id' AND tahun='$tahun' AND penilai_id='" . Auth::User()->id . "'")->first();

    $data = [
      'identitas' => $identitas,
      'nilai' => $nilai,
      'tahap2' => $tahap2,
      'tahun' => $tahun,
    ];
    $content = view('penilai.penilaian.' . $tahun . '.tahap2.penilaian', $data)->render();

    return ['content' => $content];
  }

  function simpan_nilai(Request $request)
  {
    $table = $request->table; //nilai_kriteria_tahap2"
    $bunda_id = $request->bunda_id; //52"
    $berkas = $request->berkas; //"nilai_0___0"
    $file = $request->file; //"5"
    $tahun = $request->tahun; //"5"

    $arr_kolom = explode('___', $berkas);

    if ($berkas == 'status') {
      $kolom = 'status';
    } else {
      $kolom = 'hasil';
    }

    $where = [
      'user_id' => $bunda_id,
      'tahun' => $tahun,
      'penilai_id' => Auth::User()->id,
    ];

    $berkas = DB::table('cutoff_rekap.' . $table)->where($where)->first();

    $data_insert = [];

    if ($kolom == 'status') {
      $data_insert = $file;
    } else {
      $id_kompetensi = $arr_kolom[0];
      $id_indikator = $arr_kolom[1];
      if (!empty($berkas)) {
        if (!is_null($berkas->$kolom)) {
          $exist = json_decode($berkas->$kolom);
          foreach ($exist as $ex) {
            if ($ex->id_kompetensi == $id_kompetensi && $ex->id_indikator == $id_indikator) {
            } else {
              $data_lama = [
                'id_kompetensi' => isset($ex->id_kompetensi) ? $ex->id_kompetensi : '',
                'id_indikator' => isset($ex->id_indikator) ? $ex->id_indikator : '',
                'nilai' => isset($ex->nilai) ? $ex->nilai : '',
              ];
              array_push($data_insert, $data_lama);
            }
          }
        }
        array_push($data_insert, [
          'id_kompetensi' => $id_kompetensi,
          'id_indikator' => $id_indikator,
          'nilai' => $file,
        ]);
      } else {
        array_push($data_insert, [
          'id_kompetensi' => $id_kompetensi,
          'id_indikator' => $id_indikator,
          'nilai' => $file,
        ]);
      }

      $data_insert = collect($data_insert)->sortBy([
        ['id_kompetensi', 'asc'],
        ['id_indikator', 'asc'],
      ])->all();
      $data_insert = array_values($data_insert);
      $data_insert = json_encode($data_insert);
    }

    $data = [
      $kolom => $data_insert,
      'updated_at' => date('Y-m-d H:i:s'),
    ];

    // return $data;

    $simpan = DB::table('cutoff_rekap.' . $table)->updateOrInsert($where, $data);

    if ($simpan) {
      return ['code' => 200, 'message' => 'Berhasil disimpan'];
    } else {
      return ['code' => 250, 'message' => 'Gagal disimpan'];
    }
  }
}
