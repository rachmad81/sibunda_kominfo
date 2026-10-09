<?php

namespace App\Http\Controllers\Bunda;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB, Session, Auth;

class HitungMplsController extends Controller
{
  function main(Request $request)
  {
    $jenjang = $request->jenjang;
    return view('bunda.hitung_mpls.main', compact('jenjang'));
  }

  function cari_sekolah(Request $request)
  {
    $jenjang = $request->jenjang;
    $cari = $request->cari;
    if ($jenjang == 'sd') {
      $conn = 'db_sd';
      $arr_jenjang = "'SD','MI'";
    } else {
      $conn = 'pgsql_paud';
      $arr_jenjang = "'TK','RA','TPA','SPS','KB'";
    }
    if (Auth::getUser()->tingkat == 'kecamatan') {
      $where = " AND kec.kecamatan_id='" . Auth::getUser()->id_kecamatan . "'";
    } elseif (Auth::getUser()->tingkat == 'kelurahan') {
      $where = " AND kel.kelurahan_id='" . Auth::getUser()->id_kelurahan . "'";
    } else {
      $where = "";
    }
    return $sekolah = DB::connection($conn)->table('public.sekolah as s')
      ->join('public.master_user as u', 'u.username', 's.npsn')
      ->join('public.kecamatan as kec', 'kec.kecamatan_kode', 's.kec_id')
      ->join('public.kelurahan as kel', 'kel.kelurahan_kode', 's.desa')
      ->selectRaw("s.nama,s.npsn,kec.kecamatan_dispenduk as kecamatan,kel.kelurahan_dispenduk as kelurahan,s.kepala as ks")
      ->whereRaw("u.user_enable is true AND s.jenjang in ($arr_jenjang) AND (s.nama ilike '%$cari%' OR s.npsn ilike '%$cari%') $where")
      ->limit(10)->get();
  }

  function get_sekolah(Request $request)
  {
    $jenjang = $request->jenjang;
    $npsn = $request->npsn;
    $conn = ($jenjang == 'sd') ? 'db_sd' : 'pgsql_paud';

    return $sekolah = DB::connection($conn)->table('public.sekolah')->selectRaw("jenjang,status,nama,npsn")->where('npsn', $npsn)->first();
  }

  function form_identitas(Request $request)
  {
    $jenis_form = $request->jenis_form;
    $jenjang = $request->jenjang;
    $npsn = $request->npsn;
    $user_bunda = Auth::getUser()->id;
    $sekolah = $this->get_sekolah($request);

    switch ($jenis_form) {
      case 'A':
        $judul = '';
        $batas_urut = '10';
        break;
      case 'B':
        $judul = 'B. PERENCANAAN KEGIATAN';
        $batas_urut = '20';
        break;
      case 'C':
        $judul = 'C. PELAKSANAAN MPLS RAMAH (DAPAT DILAKUKAN KROSCEK DENGAN JADWAL MPLS SATUAN PENDIDIKAN)';
        $batas_urut = '30';
        break;
      case 'C1':
        if ($jenjang == 'sd') {
          $judul = 'C.1. PELAKSANAAN MPLS RAMAH HARI 1 - SEKOLAHKU MENGGEMBIRAKAN';
        } else {
          $judul = 'C.1. PELAKSANAAN MPLS RAMAH HARI 1 - PAUDKU MENGGEMBIRAKAN';
        }
        $batas_urut = '40';
        break;
      case 'C2':
        $judul = 'C.2. PELAKSANAAN MPLS RAMAH HARI 2 - AKU MENGENAL TEMAN DAN GURU';
        $batas_urut = '50';
        break;
      case 'C3':
        if ($jenjang == 'sd') {
          $judul = 'C.3. PELAKSANAAN MPLS RAMAH HARI 3 - AKU BERBAGI CERITA';
        } else {
          $judul = 'C.3. PELAKSANAAN MPLS RAMAH HARI 3 - AKU BISA MENJAGA DIRI DAN BARANGKU';
        }
        $batas_urut = '60';
        break;
      case 'C4':
        if ($jenjang == 'sd') {
          $judul = 'C.4. PELAKSANAAN MPLS RAMAH HARI 4 - AKU BERSIH DAN SEHAT';
        } else {
          $judul = 'C.4. PELAKSANAAN MPLS RAMAH HARI 4 - AKU DAN TEMANKU, HEBAT';
        }
        $batas_urut = '70';
        break;
      case 'C5':
        if ($jenjang == 'sd') {
          $judul = 'C.5. PELAKSANAAN MPLS RAMAH HARI 5 - CERIA BERSAMA DI SEKOLAH';
        } else {
          $judul = 'C.5. PELAKSANAAN MPLS RAMAH HARI 5 - CERIA BERSAMA DI PAUD';
        }
        $batas_urut = '80';
        break;
      case 'D':
        $judul = 'D. LINGKUNGAN, KESEHATAN, & KEAMANAN';
        $batas_urut = '90';
        break;
      case 'E':
        $judul = 'E. PARTISIPASI ORANG TUA & LINGKUNGAN';
        $batas_urut = '100';
        break;
      case 'F':
        $judul = 'F. PERLINDUNGAN ANAK';
        $batas_urut = '110';
        break;
      case 'G':
        $judul = 'G. PERAN DAN SOSIALISASI BUNDA PAUD';
        $batas_urut = '120';
        break;
      case 'H':
        $judul = 'H. DUKUNGAN DAN TINDAK LANJUT';
        $batas_urut = '130';
        break;

      default:
        $judul = '';
        $batas_urut = '10';
        break;
    }

    $pertanyaan_identitas = DB::table('public.monitoring_mpls')->whereRaw("jenjang ilike '%" . $sekolah->jenjang . "%' AND tipe='satdik2025' AND urutan BETWEEN " . ($batas_urut - 10) . " AND $batas_urut")
      ->selectRaw("*,CASE WHEN (parent_id is null) THEN id ELSE parent_id END as ortu")
      ->orderByRaw("CAST(urutan as INTEGER) ASC,nomor ASC")->get();

    $get_jawaban = DB::table('public.monitoring_mpls_jawaban')->whereRaw("tipe='satdik" . date('Y') . "' AND user_bunda='$user_bunda' AND npsn='$npsn'")->first();
    $jawaban = [];
    if (!empty($get_jawaban)) {
      $jawaban = json_decode($get_jawaban->jawaban);
      if (isset($jawaban->$jenis_form)) {
        $jawaban = collect($jawaban->$jenis_form)->toArray();
      } else {
        $jawaban = [];
      }
    }

    $data = [
      'judul' => $judul,
      'pertanyaan_identitas' => $pertanyaan_identitas,
      'jenis_form' => $jenis_form,
      'npsn' => $npsn,
      'jawaban' => $jawaban,
    ];
    $content = view('bunda.hitung_mpls.identitas', $data)->render();

    return ['content' => $content];
  }

  function simpan(Request $request)
  {
    $data = $request->all();
    $npsn = $request->sekolah;
    $jenis_form = $request->jenis_form;
    $user_bunda = Auth::getUser()->id;

    unset($data['sekolah']);
    unset($data['jenis_form']);

    $data_simpan = [
      'tipe' => 'satdik' . date('Y'),
      'monitoring_mpls_id' => '0',
      'npsn' => $npsn,
      'user_bunda' => $user_bunda,
      'updated_at' => date('Y-m-d H:i:s'),
    ];

    $get_jawaban = DB::table('public.monitoring_mpls_jawaban')->whereRaw("tipe='satdik" . date('Y') . "' AND user_bunda='$user_bunda' AND npsn='$npsn'")->first();

    $jawaban = [];
    if (!empty($get_jawaban)) {
      $jawaban = json_decode($get_jawaban->jawaban);
      $jawaban = collect($jawaban)->toArray();
      if (isset($jawaban[$jenis_form])) {
        $jawaban[$jenis_form] = $data;
      } else {
        $jawaban = array_merge($jawaban, [$jenis_form => $data]);
      }
    } else {
      $jawaban = [$jenis_form => $data];
    }

    $jawaban = json_encode($jawaban);

    $data_simpan = array_merge($data_simpan, ['jawaban' => $jawaban]);
    $where_update = [
      'tipe' => 'satdik' . date('Y'),
      'npsn' => $npsn,
      'user_bunda' => $user_bunda,
    ];

    $simpan = DB::table('public.monitoring_mpls_jawaban')->updateOrInsert($where_update, $data_simpan);
    if ($simpan) {
      return ['code' => 200];
    } else {
      return ['code' => 250];
    }
  }

  function cetak(Request $request)
  {
    // http://localhost/sibunda/public/cetak_monitoring_mpls?param=eyJ1c2VyX2lkIjoiMjIiLCJucHNuIjoiMjA1MzM0OTQiLCJqZW5qYW5nIjoic2QifQ==
    $param = json_decode(base64_decode($request->param));
    $user_bunda = $param->user_id;
    $npsn = $param->npsn;
    $jenjang = $param->jenjang;

    if ($jenjang == 'sd') {
      $conn = 'db_sd';
      $arr_jenjang = "'SD','MI'";
    } else {
      $conn = 'pgsql_paud';
      $arr_jenjang = "'SPS','TPA','KB','TK','RA'";
    }

    $sekolah = DB::connection($conn)->table('public.sekolah as s')
      ->join('public.master_user as u', 'u.username', 's.npsn')
      ->join('public.kecamatan as kec', 'kec.kecamatan_kode', 's.kec_id')
      ->join('public.kelurahan as kel', 'kel.kelurahan_kode', 's.desa')
      ->selectRaw("s.nama,s.npsn,kec.kecamatan_dispenduk as kecamatan,kel.kelurahan_dispenduk as kelurahan,s.kepala as ks,s.jenjang")
      ->whereRaw("u.user_enable is true AND s.jenjang in ($arr_jenjang) AND s.npsn='$npsn'")
      ->first();
    $user = DB::table('public.users')->selectRaw("id,nama")->where('id', $user_bunda)->first();
    $mpls = DB::table('public.monitoring_mpls_jawaban')->whereRaw("npsn='$npsn' AND user_bunda='$user_bunda'")->first();

    $data = [
      'mpls' => $mpls,
      'user' => $user,
      'sekolah' => $sekolah,
      'jenjang' => $jenjang,
    ];

    return view('bunda.hitung_mpls.cetak', $data);
  }

  function form_cetak_monitoring_mpls(Request $request)
  {
    $jenis_form = $request->jenis_form;
    $jenjang = $request->jenjang;
    $npsn = $request->npsn;
    $user_bunda = $request->user_id;
    $sekolah = $this->get_sekolah($request);

    switch ($jenis_form) {
      case 'A':
        $judul = '';
        $batas_urut = '10';
        break;
      case 'B':
        $judul = 'B. PERENCANAAN KEGIATAN';
        $batas_urut = '20';
        break;
      case 'C':
        $judul = 'C. PELAKSANAAN MPLS RAMAH (DAPAT DILAKUKAN KROSCEK DENGAN JADWAL MPLS SATUAN PENDIDIKAN)';
        $batas_urut = '30';
        break;
      case 'C1':
        if ($jenjang == 'sd') {
          $judul = 'C.1. PELAKSANAAN MPLS RAMAH HARI 1 - SEKOLAHKU MENGGEMBIRAKAN';
        } else {
          $judul = 'C.1. PELAKSANAAN MPLS RAMAH HARI 1 - PAUDKU MENGGEMBIRAKAN';
        }
        $batas_urut = '40';
        break;
      case 'C2':
        $judul = 'C.2. PELAKSANAAN MPLS RAMAH HARI 2 - AKU MENGENAL TEMAN DAN GURU';
        $batas_urut = '50';
        break;
      case 'C3':
        if ($jenjang == 'sd') {
          $judul = 'C.3. PELAKSANAAN MPLS RAMAH HARI 3 - AKU BERBAGI CERITA';
        } else {
          $judul = 'C.3. PELAKSANAAN MPLS RAMAH HARI 3 - AKU BISA MENJAGA DIRI DAN BARANGKU';
        }
        $batas_urut = '60';
        break;
      case 'C4':
        if ($jenjang == 'sd') {
          $judul = 'C.4. PELAKSANAAN MPLS RAMAH HARI 4 - AKU BERSIH DAN SEHAT';
        } else {
          $judul = 'C.4. PELAKSANAAN MPLS RAMAH HARI 4 - AKU DAN TEMANKU, HEBAT';
        }
        $batas_urut = '70';
        break;
      case 'C5':
        if ($jenjang == 'sd') {
          $judul = 'C.5. PELAKSANAAN MPLS RAMAH HARI 5 - CERIA BERSAMA DI SEKOLAH';
        } else {
          $judul = 'C.5. PELAKSANAAN MPLS RAMAH HARI 5 - CERIA BERSAMA DI PAUD';
        }
        $batas_urut = '80';
        break;
      case 'D':
        $judul = 'D. LINGKUNGAN, KESEHATAN, & KEAMANAN';
        $batas_urut = '90';
        break;
      case 'E':
        $judul = 'E. PARTISIPASI ORANG TUA & LINGKUNGAN';
        $batas_urut = '100';
        break;
      case 'F':
        $judul = 'F. PERLINDUNGAN ANAK';
        $batas_urut = '110';
        break;
      case 'G':
        $judul = 'G. PERAN DAN SOSIALISASI BUNDA PAUD';
        $batas_urut = '120';
        break;
      case 'H':
        $judul = 'H. DUKUNGAN DAN TINDAK LANJUT';
        $batas_urut = '130';
        break;

      default:
        $judul = '';
        $batas_urut = '10';
        break;
    }

    $pertanyaan_identitas = DB::table('public.monitoring_mpls')->whereRaw("jenjang ilike '%" . $sekolah->jenjang . "%' AND tipe='satdik" . date('Y') . "' AND urutan BETWEEN " . ($batas_urut - 10) . " AND $batas_urut")
      ->selectRaw("*,CASE WHEN (parent_id is null) THEN id ELSE parent_id END as ortu")
      ->orderByRaw("CAST(urutan as INTEGER) ASC,nomor ASC")->get();

    $get_jawaban = DB::table('public.monitoring_mpls_jawaban')->whereRaw("tipe='satdik" . date('Y') . "' AND user_bunda='$user_bunda' AND npsn='$npsn'")->first();
    $jawaban = [];
    if (!empty($get_jawaban)) {
      $jawaban = json_decode($get_jawaban->jawaban);
      if (isset($jawaban->$jenis_form)) {
        $jawaban = collect($jawaban->$jenis_form)->toArray();
      } else {
        $jawaban = [];
      }
    }

    $data = [
      'judul' => $judul,
      'pertanyaan_identitas' => $pertanyaan_identitas,
      'jenis_form' => $jenis_form,
      'npsn' => $npsn,
      'jawaban' => $jawaban,
    ];
    $content = view('bunda.hitung_mpls.identitas_cetak', $data)->render();

    return ['content' => $content];
  }
}
