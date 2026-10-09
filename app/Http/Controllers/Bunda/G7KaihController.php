<?php

namespace App\Http\Controllers\Bunda;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB, Session, Auth;

class G7KaihController extends Controller
{
  function main(Request $request)
  {
    $tahun_all = DB::connection('pgsql_paud')->table('history.kebiasaan_anak_rumah')->select('tahun')->distinct()->get();
    $tahun_all1 = DB::connection('pgsql_paud')->table('history.kebiasaan_anak_sekolah')->select('tahun')->distinct()->get();
    $tahun1 = collect($tahun_all)->pluck('tahun')->unique()->toArray();
    $tahun2 = collect($tahun_all1)->pluck('tahun')->unique()->toArray();
    $tahun = array_merge($tahun1, $tahun2);
    rsort($tahun);
    $judul = 'JURNAL REFLEKSI 7 + 2 KEBIASAAN ANAK INDONESIA HEBAT AKTIVITAS DI RUMAH dan SEKOLAH';
    return view('bunda.g7kaih.main', ['tahun' => $tahun, 'judul' => $judul]);
  }

  function cari_sekolah(Request $request)
  {
    $cari = $request->cari;
    $conn = 'pgsql_paud';
    $arr_jenjang = "'TK','RA','TPA','SPS','KB'";
    // $where = '';
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

  function get_siswa(Request $request)
  {
    $bulan = $request->bulan;
    $tahun = $request->tahun;
    $npsn = $request->npsn;
    $conn = 'pgsql_paud';

    $sekolah = DB::connection($conn)->table('public.siswa as s')
      ->leftjoin('history.kebiasaan_anak_sekolah as ks', function ($join) use ($tahun, $bulan) {
        return $join->on('s.siswa_id', '=', 'ks.siswa_id')
          ->whereRaw("ks.tahun='$tahun' AND ks.bulan='$bulan'");
      })
      ->leftjoin('history.kebiasaan_anak_rumah as kr', function ($join) use ($tahun, $bulan) {
        return $join->on('s.siswa_id', '=', 'kr.siswa_id')
          ->whereRaw("kr.tahun='$tahun' AND kr.bulan='$bulan'");
      })
      ->selectRaw("s.siswa_id,s.nama,s.kelas,s.status_siswa,s.alumni,ks.siswa_id as ks_id,kr.siswa_id as kr_id,s.rombel")
      ->whereRaw("s.status_siswa='Aktif' AND alumni is not true AND s.npsn='$npsn'")
      ->orderByRaw("s.kelas ASC,s.rombel ASC,s.nama ASC")
      ->get();

    $content = view('bunda.g7kaih.baris', ['siswa' => $sekolah])->render();

    return ['content' => $content];
  }

  function tampilkan(Request $request)
  {
    $lokasi = $request->lokasi;

    if ($lokasi == 'rumah') {
      $hasil = $this->tampilkan_rumah($request);
    } else {
      $hasil = $this->tampilkan_sekolah($request);
    }

    return $hasil;
  }

  function tampilkan_rumah(Request $request)
  {
    $siswa_id = $request->siswa_id;
    $tahun = $request->tahun;
    $bulan = $request->bulan;
    $npsn = $request->npsn;
    $conn = 'pgsql_paud';

    $data_kebiasaan = DB::connection($conn)->table('history.kebiasaan_anak_rumah')->where('siswa_id', $siswa_id)
      ->where('tahun', $tahun)
      ->where('bulan', $bulan)
      ->first();

    if (empty($data_kebiasaan)) {
      return 'Siswa belum melakukan pengisian';
    }

    $macam_kebiasaan = [
      "BANGUN PAGI" => [
        'bangun_sebelum' => 'Bangun sebelum jam 05.30 WIB',
        'mandi_pakai_sabun' => 'Mandi pakai sabun mandi',
        'cuci_rambut_pakai_sampo' => 'Cuci rambut pakai sampo',
        'gosok_gigi' => 'Gosok gigi',
        'merapikan_tempat_tidur' => 'Merapikan tempat tidur'
      ],
      "BERIBADAH" => [
        'berdoa_sebelum_tidur' => 'Berdoa sebelum tidur',
        'berdoa_bangun_tidur' => 'Berdoa bangun tidur',
        'berdoa_sebelum_makan' => 'Berdoa sebelum makan',
        'berdoa_sesudah_makan' => 'Berdoa sesudah makan',
        'membaca_kitab_suci' => 'Membaca Kitab Suci',
        'beribadah_ketempat_ibadah' => 'Beribadah ke tempat Ibadah'
      ],
      "BEROLAHRAGA" => ['berolahraga' => 'Lari/Bersepeda/Senam/dll'],

      "GEMAR BELAJAR" => [
        'membaca' => 'Membaca/mendengar buku cerita',
        'menulis' => 'Latihan Menulis',
        'mengaji' => 'Mengaji'
      ],
      "MAKANAN SEHAT BERGIZI" => [
        'sarapan_bergizi' => 'Makan sarapan bergizi',
        'siang_bergizi' => 'Makan siang bergizi',
        'malam_bergizi' => 'Makan malam bergizi'
      ],
      "BERMASYARAKAT" => ['bermain_bersama_teman' => 'MBermain bersama teman'],
      "TIDUR CEPAT" => ['tidur_sebelum_sembilan' => 'Tidur sebelum jam 21.00 WIB'],

      "BERANI" => [
        'bercerita' => 'Bercerita kepada orang tua',
        'menyampaikan_pendapat' => 'Menyampaikan pendapat',
        'bertemu_tamu' => 'Bertemu tamu'
      ],
      "GOTONG ROYONG" => [
        'membantu_merapikan_tempat_tidur' => 'Membantu merapikan tempat tidur',
        'membantu_pekerjaan_rumah' => 'Membantu pekerjaaan rumah (menyapu, mencuci motor, dll)',
        'merapikan_tempat_tidur_gt' => 'Merapikan mainan'
      ],
    ];

    $siswa = DB::connection('pgsql_paud')->table('public.siswa as s')
      ->join('public.sekolah as se', 'se.npsn', 's.npsn')
      ->selectRaw("s.nama,s.kelas,s.rombel,se.npsn,se.nama as nama_sekolah")
      ->where('s.siswa_id', $siswa_id)->first();

    $data = [
      'data_kebiasaan' => (array)$data_kebiasaan,
      'macam_kebiasaan' => $macam_kebiasaan,
      'siswa' => $siswa,
      'param' => $request->all(),
    ];

    return view('bunda.g7kaih.rumah', $data);
  }

  function tampilkan_sekolah(Request $request)
  {
    $siswa_id = $request->siswa_id;
    $tahun = $request->tahun;
    $bulan = $request->bulan;
    $npsn = $request->npsn;
    $conn = 'pgsql_paud';

    $data_kebiasaan = DB::connection($conn)->table('history.kebiasaan_anak_sekolah')->where('siswa_id', $siswa_id)
      ->where('tahun', $tahun)
      ->where('bulan', $bulan)
      ->first();

    if (empty($data_kebiasaan)) {
      return 'Siswa belum melakukan pengisian';
    }

    $macam_kebiasaan = [
      "GOTONG ROYONG" => ['gotong_royong' => ['Merapikan mainan', 'Membantu orang lain', 'Piket bersama membersihkan kelas']],
      "BERANI" => ['berani' => ['Tampill / Memimpin']],
      "TIDUR CEPAT" => ['tidur_cepat' => ['Tidur sebelum jam 21.00 WIB']],
      "BERMASYARAKAT" => ['bermasyarakat' => ['Bermain bersama teman (dari kelas lain)']],
      "MAKANAN SEHAT BERGIZI" => ['makan_sehat_bergizi' => ['Makan bekal sehat', 'Minum air putih']],
      "GEMAR BELAJAR" => ['gemar_belajar' => ['Aktif dalam Pembelajaran']],
      "BEROLAHRAGA" => ['berolahraga' => ['Upacara hari Senin', 'Senam Pagi']],
      "BERIBADAH" => ['beribadah' => ['Berdoa Khusyuk']],
      "BANGUN PAGI" => ['bangun_pagi' => ['Datang Pagi']],
    ];

    $siswa = DB::connection('pgsql_paud')->table('public.siswa as s')
      ->join('public.sekolah as se', 'se.npsn', 's.npsn')
      ->selectRaw("s.nama,s.kelas,s.rombel,se.npsn,se.nama as nama_sekolah")
      ->where('s.siswa_id', $siswa_id)->first();

    $data = [
      'data_kebiasaan' => (array)$data_kebiasaan,
      'macam_kebiasaan' => $macam_kebiasaan,
      'siswa' => $siswa,
      'param' => $request->all(),
    ];

    return view('bunda.g7kaih.sekolah', $data);
  }
}
