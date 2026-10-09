<?php

namespace App\Http\Controllers\Bunda;

use App\Http\Controllers\Controller;
use App\Models\ProfilSekolah\Sekolah;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use stdClass;

class RekapSatuanController extends Controller
{
  function index()
  {
    $kecamatans =  DB::connection('db_sd')
      ->table('public.kecamatan')
      ->join('public.kelurahan', 'kelurahan.kecamatan_kode', 'kecamatan.kecamatan_kode')
      ->when(request()->user()->tingkat == 'kecamatan', function ($q) {
        $q->where('kecamatan.kecamatan_id', request()->user()->id_kecamatan);
      })
      ->when(request()->user()->tingkat == 'kelurahan', function ($q) {
        $q->where('kelurahan.kelurahan_id', request()->user()->id_kelurahan);
      })
      ->select(
        'kecamatan.kecamatan_id',
        'kecamatan.kecamatan_dispenduk',
        'kecamatan.kecamatan_kode',
        DB::raw("JSON_AGG(
				JSON_BUILD_OBJECT(
				'kelurahan_id', kelurahan.kelurahan_id,
				'kelurahan_dispenduk', kelurahan.kelurahan_dispenduk,
				'kelurahan_kode', kelurahan.kelurahan_kode
				)
			) AS kelurahans")
      )
      ->groupBy(
        'kecamatan.kecamatan_id',
        'kecamatan.kecamatan_dispenduk',
        'kecamatan.kecamatan_kode'
      )
      ->orderBy('kecamatan.kecamatan_dispenduk')
      ->get();

    $data = [
      'kecamatans' => $kecamatans,
    ];
    return view('rekap_satuan.main', $data);
  }

  function get_data(Request $request)
  {
    $jenis = $request->jenis;

    if ($jenis == 'siswa') {
      $content = $this->get_siswa($request);
    } else if ($jenis == 'gtk') {
      $content = $this->get_gtk($request);
    } else if ($jenis == 'sarpras') {
      $content = $this->get_sarpras($request);
    } else if ($jenis == 'keuangan') {
      $content = $this->get_keuangan($request);
    } else if ($jenis == 'lembaga') {
      $content = $this->get_lembaga($request);
    } else if ($jenis == 'lembaga_sd') {
      $content = $this->get_lembaga_sd($request);
    } else if ($jenis == 'monitoring_lembaga') {
      $content = $this->get_monitoring_lembaga($request);
    } else if ($jenis == 'monitoring_lembaga_sd') {
      $content = $this->get_monitoring_lembaga_sd($request);
    } else if ($jenis == 'siswa_sd') {
      $content = $this->get_siswa($request, 'sd');
    }

    return $content;
  }

  // DETAIL DATA
  function detail_data(Request $request)
  {
    $jenis = $request->jenis;

    if ($jenis == 'siswa') {
      $content = $this->detail_siswa($request);
    } else if ($jenis == 'gtk') {
      $content = $this->detail_gtk($request);;
    } else if ($jenis == 'keuangan') {
      $content = $this->detail_keuangan($request);;
    } else if ($jenis == 'sarpras') {
      $content = $this->detail_sarpras($request);;
    } else if ($jenis == 'lembaga') {
      $content = $this->detail_siswa($request);
    } else if ($jenis == 'monitoring') {
      $content = $this->detail_monitoring($request);
    } else if ($jenis == 'siswa_sd') {
      $content = $this->detail_siswa($request, 'sd');
    } else if ($jenis == 'lembaga_sd') {
      $content = $this->detail_siswa_sd($request);
    }

    return $content;
  }

  function query_siswa($jenis)
  {
    switch ($jenis) {
      case 'lembaga':
        $query = DB::connection('pgsql_paud')->table('sekolah')
          ->join('master_user', function ($query) {
            $query->on('sekolah.npsn', '=', 'master_user.npsn')->whereRaw('master_user.user_enable is true');
          })
          ->join('public.kecamatan', function ($q) {
            $q->on('kecamatan.kecamatan_kode', 'sekolah.kec_id');
            if (request()->user()->tingkat == 'kecamatan') {
              $q->where('kecamatan.kecamatan_id', request()->user()->id_kecamatan);
            }
            if (request('kecamatan') && request('kecamatan') != 'all') {
              $q->where('kecamatan.kecamatan_kode', request('kecamatan'));
            }
          })
          ->join('public.kelurahan', function ($q) {
            $q->on('kelurahan.kelurahan_kode', 'sekolah.desa');
            if (request()->user()->tingkat == 'kelurahan') {
              $q->where('kelurahan.kelurahan_id', request()->user()->id_kelurahan);
            }
            if (request('kelurahan') && request('kelurahan') != 'all') {
              $q->where('kelurahan.kelurahan_kode', request('kelurahan'));
            }
          })
          ->join('siswa', function ($query) {
            $query->on('siswa.npsn', 'sekolah.npsn')
              ->whereRaw('alumni is not true')
              ->wherein('status_siswa', ['Aktif']);
          })
          ->leftJoin('wali_murid as wm', function ($query) {
            $query->on('wm.id_siswa', '=', 'siswa.id_siswa')
              ->whereRaw('wm.npsn = siswa.npsn');
          })
          ->where(function ($q) {
            $jenjangKelas = explode(' ', request('jenjang'), 2);
            if (request('jenjang') && request('jenjang') == 'all') {
              $q->whereIn('sekolah.jenjang', ['TPA', 'SPS', 'TK', 'KB', 'RA']);
            } else if (count($jenjangKelas) > 1) {
              $q->where('sekolah.jenjang', $jenjangKelas[0]);
              $q->where('siswa.kelas', $jenjangKelas[1]);
            } else {
              $q->where('sekolah.jenjang', request('jenjang'));
            }
            if (request('npsn')) {
              $q->where('sekolah.npsn', request('npsn'));
            }
            if (request('status')) {
              $q->where('sekolah.status', request('status'));
            }
          })
          ->whereRaw("sekolah.nama not ilike '%terbuka%'");
        break;
      case 'pmt':
        $query = DB::connection('db_simpaud')
          ->table('pmt_usulans')
          ->join('pmt_pds', 'pmt_pds.pmt_usulan_id', 'pmt_usulans.id')
          ->join('periodes', function ($q) {
            $q->on('periodes.id', 'pmt_usulans.periode_id');
            $q->where('periodes.program', 'pmt');
          })
          ->where('pmt_usulans.acc_usulan', '1')
          ->where('pmt_pds.sent_pd', '1')
          ->select(
            'pmt_pds.id',
            'pmt_usulans.npsn',
            'pmt_pds.siswa_id'
          )
          ->groupBy(
            'pmt_pds.id',
            'pmt_usulans.npsn',
            'pmt_pds.siswa_id'
          );
        break;
      case 'educitytour':
        $query = DB::connection('pgsql_paud')
          ->table('kegiatan_pendukung.edu_city_tour_jadwals')
          ->where('deleted_at', null)
          ->whereRaw('EXTRACT(YEAR FROM tanggal) = ' . now()->year)
          ->where('status_jadwal', 'Setujui')
          ->select('tanggal', 'npsn', 'status_jadwal', 'peserta');
        break;
      case 'kitab_suci':
        $query = DB::connection('db_genmas')
          ->table('data_pendaftar')
          ->where('tahun_pendaftaran', now()->year)
          ->where('status_penerima', 1)
          ->where('lolos_administrasi', 1)
          ->where('is_lock', 1)
          ->select(
            'nik',
            'nama',
            'npsn',
            'nama_sekolah',
            'jenjang',
            'jenis_kitab_suci'
          );
        break;
      case 'lembaga_sd':
        $query = DB::connection('db_sd')->table('sekolah')
          ->join('master_user', function ($query) {
            $query->on('sekolah.npsn', '=', 'master_user.npsn')->whereRaw('master_user.user_enable is true');
          })
          ->join('public.kecamatan', function ($q) {
            $q->on('kecamatan.kecamatan_kode', 'sekolah.kec_id');
            if (request()->user()->tingkat == 'kecamatan') {
              $q->where('kecamatan.kecamatan_id', request()->user()->id_kecamatan);
            }
            if (request('kecamatan') && request('kecamatan') != 'all') {
              $q->where('kecamatan.kecamatan_kode', request('kecamatan'));
            }
          })
          ->join('public.kelurahan', function ($q) {
            $q->on('kelurahan.kelurahan_kode', 'sekolah.desa');
            if (request()->user()->tingkat == 'kelurahan') {
              $q->where('kelurahan.kelurahan_id', request()->user()->id_kelurahan);
            }
            if (request('kelurahan') && request('kelurahan') != 'all') {
              $q->where('kelurahan.kelurahan_kode', request('kelurahan'));
            }
          })
          ->join('siswa', function ($query) {
            $query->on('siswa.npsn', 'sekolah.npsn')
              ->whereRaw('alumni is not true')
              ->wherein('status_siswa', ['Aktif'])
              ->whereIn('kelas', ['1', '2']);
          })
          ->leftJoin('wali_murid as wm', function ($query) {
            $query->on('wm.id_siswa', '=', 'siswa.id_siswa')
              ->whereRaw('wm.npsn = siswa.npsn');
          })
          ->where(function ($q) {
            $jenjangKelas = explode(' ', request('jenjang'), 2);
            if (request('jenjang') && request('jenjang') == 'all') {
              $q->whereIn('sekolah.jenjang', ['SD', 'MI']);
            } else if (count($jenjangKelas) > 1) {
              $q->where('sekolah.jenjang', $jenjangKelas[0]);
              $q->where('siswa.kelas', $jenjangKelas[1]);
            } else {
              $q->where('sekolah.jenjang', request('jenjang'));
            }

            if (request('npsn')) {
              $q->where('sekolah.npsn', request('npsn'));
            }
            if (request('status')) {
              $q->where('sekolah.status', request('status'));
            }
          })
          ->whereRaw("sekolah.nama not ilike '%terbuka%'");
        break;
      case 'talenta':
        $query = DB::connection('pgsql')
          ->table('talenta_seni_jadwal')
          ->where('deleted_at', null)
          ->whereRaw('EXTRACT(YEAR FROM tanggal) = ' . now()->year)
          ->where('status_jadwal', 'Setujui')
          ->selectRaw("tanggal,npsn,status_jadwal,SUM(jumlah_pd) as jumlah_pd")
          ->groupByRaw("tanggal,npsn,status_jadwal");
        break;
      default:
        $query = '';
        break;
    }
    return $query;
  }

  function generate_siswa()
  {
    $lembagas = $this->query_siswa('lembaga')
      ->leftJoin('history.riwayat_vaksinasi', 'riwayat_vaksinasi.siswa_id', 'siswa.siswa_id')
      ->leftJoin('history.history_tb_bb_siswa', 'history_tb_bb_siswa.siswa_id', 'siswa.siswa_id')
      ->leftJoin('prestasi_siswa', function ($query) {
        $query->on('prestasi_siswa.id_siswa', '=', 'siswa.id_siswa')
          ->whereRaw('prestasi_siswa.npsn = siswa.npsn');
      })
      ->selectRaw("sekolah.status, sekolah.jenjang, siswa.kelas,
			sekolah.npsn, sekolah.nama,  sekolah.kec_id, sekolah.desa, sekolah.alamat, kecamatan.kecamatan_dispenduk, kelurahan.kelurahan_dispenduk,
			STRING_AGG(DISTINCT(CASE WHEN siswa.status_siswa = 'Aktif' and siswa.alumni is not true THEN siswa.id_siswa::text ELSE NULL END), ',') as id_siswas,
			STRING_AGG(DISTINCT(siswa.siswa_id::text), ',') as siswa_ids,
			STRING_AGG(DISTINCT(siswa.nik::text), ',') as niks,
			COUNT(DISTINCT(CASE WHEN siswa.status_siswa = 'Aktif' and siswa.alumni is not true then siswa.siswa_id ELSE NULL END)) as jumlah_siswa,
			COUNT(DISTINCT(CASE WHEN siswa.status_siswa = 'Aktif' and siswa.alumni is not true and history_tb_bb_siswa.tahun = " . now()->year . " AND history_tb_bb_siswa.bulan = " . now()->month . " AND history_tb_bb_siswa.bb IS NOT NULL AND history_tb_bb_siswa.tb IS NOT NULL THEN siswa.siswa_id ELSE NULL END)) AS jumlah_sudah_tb_bb,
			COUNT(DISTINCT(CASE WHEN siswa.status_siswa = 'Aktif' and siswa.alumni is not true and riwayat_vaksinasi.jenis = 'Polio' AND riwayat_vaksinasi.dosis = 1 AND riwayat_vaksinasi.status = 'Sudah' THEN siswa.siswa_id ELSE NULL END)) AS jumlah_sudah_polio_dosis_1,
			COUNT(DISTINCT(CASE WHEN siswa.status_siswa = 'Aktif' and siswa.alumni is not true and riwayat_vaksinasi.jenis = 'Polio' AND riwayat_vaksinasi.dosis = 2 AND riwayat_vaksinasi.status = 'Sudah' THEN siswa.siswa_id ELSE NULL END)) AS jumlah_sudah_polio_dosis_2,
			COALESCE(null,0) as jumlah_pmt,
			COALESCE(null,0) as jumlah_educitytour,
			COUNT(distinct case when siswa.status_siswa = 'Aktif' AND prestasi_siswa.id_siswa is not null then siswa.siswa_id else null end) as jumlah_prestasi,
			COALESCE(null,0) as jumlah_talenta,
			COALESCE(null,0) as jumlah_manasik,
			COALESCE(null,0) as jumlah_kitab_suci,
			COUNT(distinct case when siswa.status_siswa = 'Aktif' and wm.kab_id ilike '%surabaya%' and verified_siswa is true and status_mbr is true then siswa.siswa_id else null end) as jumlah_gakin_pramis
			")
      ->groupBy('sekolah.status', 'sekolah.jenjang', 'siswa.kelas', 'sekolah.npsn', 'sekolah.nama', 'sekolah.kec_id', 'sekolah.desa', 'sekolah.alamat', 'kecamatan.kecamatan_dispenduk', 'kelurahan.kelurahan_dispenduk')
      ->orderBy('status')->get();

    $pmts = $this->query_siswa('pmt')
      ->where('periodes.bulan_angka', now()->month)
      ->where('periodes.tahun', now()->year)
      ->whereIn('pmt_usulans.npsn', $lembagas->pluck('npsn'))
      ->get();

    $educitytours = $this->query_siswa('educitytour')
      ->whereIn('npsn', $lembagas->pluck('npsn'))
      ->get();

    $kitab_sucis = $this->query_siswa('kitab_suci')
      ->whereIn('npsn', $lembagas->pluck('npsn'))
      ->get();

    $talentas = $this->query_siswa('talenta')
      ->whereIn('npsn', $lembagas->pluck('npsn'))
      ->get();

    foreach ($lembagas as $lembaga) {
      // Data PMT =========================================================================
      // PMT menggunakan id_siswa di profil dengan nama siswa_id
      // $siswa_id = explode(',', $lembaga->siswa_ids);
      $id_siswa = explode(',', $lembaga->id_siswas);
      $pmt = $pmts->where('npsn', $lembaga->npsn)
        ->whereIn('siswa_id', $id_siswa)
        ->unique()
        ->values();
      $lembaga->jumlah_pmt = count($pmt);

      // Data Educity Tour =========================================================================
      $educitytour = $educitytours
        ->where('npsn', $lembaga->npsn)
        ->values();
      $pesertas = collect([]);
      foreach ($educitytour as $edu) {
        if (!is_string($edu->peserta)) {
          continue;
        }
        $edu->peserta = json_decode($edu->peserta, true);
        foreach ($edu->peserta as $encryptedPeserta) {
          $decryptedPeserta = json_decode(trim(preg_replace('/\t+/', '', $encryptedPeserta)));
          // if (empty($decryptedPeserta)) {
          // 	dd($decryptedPeserta, $encryptedPeserta, trim(preg_replace('/\t+/', '', $encryptedPeserta)), $edu);
          // } else {
          if (!empty($decryptedPeserta)) {
            $peserta = new stdClass;
            $peserta->id_siswa = $decryptedPeserta[0];
            $peserta->npsn = $decryptedPeserta[1];
            $peserta->nama = $decryptedPeserta[2];
            $pesertas->push($peserta);
          }
        }
      }
      $pesertas = $pesertas
        ->whereIn('id_siswa', $id_siswa)
        ->unique()
        ->values();
      $lembaga->jumlah_educitytour = count($pesertas);

      // Data Penghafal Kitab Suci =========================================================================
      $nik = explode(',', $lembaga->niks);
      $kitab_suci = $kitab_sucis
        ->where('npsn', $lembaga->npsn)
        ->whereIn('nik', $nik)
        ->unique()
        ->values();
      $lembaga->jumlah_kitab_suci = count($kitab_suci);

      // Data Talenta Seni =========================================================================
      $talenta = $talentas
        ->where('npsn', $lembaga->npsn)
        ->sum('jumlah_pd');
      $lembaga->jumlah_talenta = $talenta;
    }
    foreach ($lembagas as $lembaga) {
      unset($lembaga->id_siswas);
      unset($lembaga->siswa_ids);
      unset($lembaga->niks);
      $lembaga->tgl_simpan = date('Y-m-d');
    }
    $inserts = $lembagas->toArray();
    foreach ($inserts as $key => $insert) {
      $inserts[$key] = (array) $insert;
    }

    $splitInserts = array_chunk($inserts, 40);
    //Generate Cutoff
    DB::connection('pgsql')->beginTransaction();
    try {
      DB::connection('pgsql')
        ->table('cutoff_rekap.rekap_siswa')
        ->where('tgl_simpan', date('Y-m-d'))
        ->whereIn('npsn', $lembagas->pluck('npsn'))
        ->delete();

      foreach ($splitInserts as $splitInsert) {
        DB::connection('pgsql')
          ->table('cutoff_rekap.rekap_siswa')
          ->insert($splitInsert);
      }

      DB::connection('pgsql')->commit();
    } catch (\Exception $e) {
      DB::connection('pgsql')->rollBack();
      return "Terjadi kesalahan. Pesan : {$e->getMessage()}";
    }

    return $lembagas;
  }

  function get_siswa(Request $request, $jenjang_awal = null)
  {
    $bulan = $request->bulan; //"11"
    $jenis = $request->jenis; //"siswa"
    $jenjang = $request->jenjang; //"all"
    $kecamatan = $request->kecamatan; //"all"
    $kelurahan = $request->kelurahan; //"all"
    $load = $request->load; //null
    $date = date('Y-m-d');

    if ($jenjang_awal == 'sd') {
      $tabelnya = 'rekap_siswa_sd';
      $conn = 'db_sd';
      $judul = 'SD/MI';
      $tab = 'siswa_sd';
    } else {
      $tabelnya = 'rekap_siswa';
      $conn = 'pgsql_paud';
      $judul = 'PAUD';
      $tab = 'siswa';
    }

    $lembagas = DB::connection('pgsql')
      ->table('cutoff_rekap.' . $tabelnya)
      ->where('tgl_simpan', $date);

    if ($lembagas->count() == 0) {
      $load = 'load';
    }

    if ($load != '') {
      $lembagas = DB::connection('pgsql')
        ->table('cutoff_rekap.' . $tabelnya)
        ->where('tgl_simpan', $date)->delete();

      $bulan = date('m');
      if ($bulan < 7) {
        $nama_tahun_ajaran = (date('Y') - 1) . '/' . date('Y');
        $semester = 2;
      } else {
        $nama_tahun_ajaran = date('Y') . '/' . (date('Y') + 1);
        $semester = 1;
      }
      $id_tahun_ajaran = DB::connection('db_sd')->table('public.tahun_ajaran')->where('nama_tahun_ajaran', $nama_tahun_ajaran)->first();

      $jumlah_rombel = DB::connection($conn)->table('public.rombongan_belajar as rb')
        ->selectRaw("npsn,COUNT(rombel) as jumlah_rombel")
        ->when($jenjang_awal == 'sd', function ($q) {
          return $q->whereIn('kelas', ['1', '2']);
        })
        ->where('tahun_ajaran_id', $id_tahun_ajaran->id_tahun_ajaran)
        ->where('soft_delete', '0')
        ->where('semester', $semester)
        ->groupByRaw("npsn");

      $jumlah_siswa = DB::connection($conn)->table('public.sekolah as se')
        ->selectRaw("
				se.npsn,s.kelas,
				COUNT(DISTINCT CASE WHEN (s.verified_gakin is true OR s.verified_gakin2 is true) THEN s.siswa_id END) as jumlah_gamis,
				COUNT(DISTINCT s.siswa_id) as jumlah_siswa,
				SUM(CASE WHEN (v.jenis = 'Polio' AND v.dosis = 1) THEN 1 ELSE 0 END) as jumlah_sudah_polio_dosis_1,
				--0 as jumlah_sudah_polio_dosis_1,

				SUM(CASE WHEN v.jenis = 'Polio' AND v.dosis = 2 THEN 1 ELSE 0 END) as jumlah_sudah_polio_dosis_2
				--0 as jumlah_sudah_polio_dosis_2
				")
        ->join('public.master_user as u', 'u.username', 'se.npsn')
        ->join('public.siswa as s', function ($join) use ($jenjang_awal) {
          $join->on('s.npsn', '=', 'se.npsn')
            ->where('s.status_siswa', 'Aktif')
            ->whereRaw("s.alumni IS NOT TRUE");
          if ($jenjang_awal == 'sd') {
            $join->whereIn('s.kelas', ['1', '2']);
          }
          return $join;
        })
        ->leftJoin('history.riwayat_vaksinasi as v', 'v.siswa_id', 's.siswa_id')
        ->whereRaw("u.user_enable IS true")
        ->when($jenjang, function ($q) use ($jenjang, $jenjang_awal) {
          if ($jenjang == 'all') {
            if ($jenjang_awal == 'sd') {
              return $q->whereIn('se.jenjang', ['SD', 'MI']);
            } else {
              return $q->whereIn('se.jenjang', ['SPS', 'TPA', 'KB', 'TK', 'RA']);
            }
          } else {
            if ($jenjang_awal == 'sd') {
              return $q->where('se.jenjang', $jenjang);
            } else {
              return $q->where('se.jenjang', $jenjang);
            }
          }
        })
        ->groupByRaw("se.npsn,s.kelas");

      $lembagas = DB::connection($conn)->table('public.sekolah as se')
        ->selectRaw("
				se.npsn,se.nama,se.jenjang,
				kec.kecamatan_dispenduk,
				kel.kelurahan_dispenduk,
				'$date' as tgl_simpan,
				se.alamat,se.status,
				COALESCE(MAX(s.jumlah_siswa),0) as jumlah_siswa,
				COALESCE(MAX(s.jumlah_sudah_polio_dosis_1),0) as jumlah_sudah_polio_dosis_1,
				COALESCE(MAX(s.jumlah_sudah_polio_dosis_2),0) as jumlah_sudah_polio_dosis_2,
				0 as jumlah_pmt,
				0 as jumlah_educitytour,
				0 as jumlah_prestasi,
				0 as jumlah_talenta,
				0 as jumlah_manasik,
				0 as jumlah_kitab_suci,
				COALESCE(MAX(s.jumlah_gamis),0) as jumlah_gakin_pramis,
				s.kelas as kelas,
				0 as jumlah_sudah_tb_bb,
				COALESCE(MAX(rb.jumlah_rombel),0) as jumlah_rombel
				")
        ->join('public.master_user as u', 'u.username', 'se.npsn')
        ->leftjoin('public.kecamatan as kec', 'kec.kecamatan_kode', 'se.kec_id')
        ->leftjoin('public.kelurahan as kel', 'kel.kelurahan_kode', 'se.desa')
        ->leftjoinSub($jumlah_siswa, 's', function ($j) {
          return $j->on('s.npsn', '=', 'se.npsn');
        })
        ->leftJoinSub($jumlah_rombel, 'rb', function ($join) {
          return $join->on('rb.npsn', '=', 'se.npsn');
        })
        ->when($jenjang, function ($q) use ($jenjang, $jenjang_awal) {
          if ($jenjang == 'all') {
            if ($jenjang_awal == 'sd') {
              return $q->whereIn('se.jenjang', ['SD', 'MI'])->whereIn('s.kelas', ['1', '2']);
            } else {
              return $q->whereIn('se.jenjang', ['SPS', 'TPA', 'KB', 'TK', 'RA']);
            }
          } else {
            if ($jenjang_awal == 'sd') {
              return $q->where('se.jenjang', $jenjang)->whereIn('s.kelas', ['1', '2']);
            } else {
              return $q->where('se.jenjang', $jenjang);
            }
          }
        })
        ->when($kecamatan != 'all', function ($q) use ($kecamatan) {
          return $q->where('se.kec_id', $kecamatan);
        })
        ->when($kelurahan != 'all', function ($q) use ($kelurahan) {
          return $q->where('se.desa', $kelurahan);
        })
        ->where('u.user_enable', true)
        ->whereRaw("se.nama not ilike '%terbuka%'")
        ->groupByRaw("se.npsn,kec.kecamatan_dispenduk,kel.kelurahan_dispenduk,s.kelas")
        ->get();

      $data_insert = collect($lembagas)
        ->map(fn($row) => (array) $row)
        ->chunk(500)
        ->each(function ($chunk) use ($tabelnya) {
          DB::connection('pgsql')
            ->table('cutoff_rekap.' . $tabelnya)
            ->insert($chunk->toArray());
        });
    }

    $lembagas = DB::connection('pgsql')
      ->table('cutoff_rekap.' . $tabelnya)
      ->selectRaw("
			npsn,
			nama,
			status,
			jenjang,
			SUM(jumlah_siswa) as jumlah_siswa,
			SUM(jumlah_sudah_polio_dosis_1) as jumlah_sudah_polio_dosis_1,
			SUM(jumlah_sudah_polio_dosis_2) as jumlah_sudah_polio_dosis_2,
			SUM(jumlah_pmt) as jumlah_pmt,
			SUM(jumlah_educitytour) as jumlah_educitytour,
			SUM(jumlah_prestasi) as jumlah_prestasi,
			SUM(jumlah_talenta) as jumlah_talenta,
			SUM(jumlah_manasik) as jumlah_manasik,
			SUM(jumlah_kitab_suci) as jumlah_kitab_suci,
			SUM(jumlah_gakin_pramis) as jumlah_gakin_pramis,
			SUM(jumlah_sudah_tb_bb) as jumlah_sudah_tb_bb,
			MAX(jumlah_rombel) as jumlah_rombel
			")
      ->where('tgl_simpan', $date)
      ->when($jenjang, function ($q) use ($jenjang, $jenjang_awal) {
        if ($jenjang == 'all') {
          if ($jenjang_awal == 'sd') {
            return $q->whereIn('jenjang', ['SD', 'MI'])->whereIn('kelas', ['1', '2']);
          } else {
            return $q->whereIn('jenjang', ['SPS', 'TPA', 'KB', 'TK', 'RA']);
          }
        } else {
          if ($jenjang_awal == 'sd') {
            return $q->where('jenjang', $jenjang)->whereIn('kelas', ['1', '2']);
          } else {
            return $q->where('jenjang', $jenjang);
          }
        }
      })
      ->when($kecamatan != 'all', function ($q) use ($kecamatan) {
        return $q->where('kec_id', $kecamatan);
      })
      ->when($kelurahan != 'all', function ($q) use ($kelurahan) {
        return $q->where('desa', $kelurahan);
      })
      ->orderByRaw("CASE WHEN jenjang='TPA' THEN 1 ELSE CASE WHEN jenjang='SPS' THEN 2 ELSE CASE WHEN jenjang='KB' THEN 3 ELSE 4 END END END ASC,jenjang DESC")
      ->groupByRaw("npsn,status,nama,jenjang")
      ->get();

    $data = [
      'jumlah' => $lembagas,
      'judul' => $judul,
      'tab' => $tab,
    ];
    $content = view('rekap_satuan.data_siswa', $data)->render();
    return ['content' => $content];
  }

  function get_siswa_old(Request $request)
  {
    if (request('bulan') == now()->month || empty(request('bulan'))) {
      $date = now();
    } else {
      $lastDate = DB::connection('pgsql')
        ->table('cutoff_rekap.rekap_siswa')
        ->whereYear('tgl_simpan', now()->year)
        ->whereMonth('tgl_simpan', request('bulan'))
        ->select(DB::raw('MAX(tgl_simpan)'))
        ->first();
      if ($lastDate) {
        $date = now()->parse($lastDate->max);
      } else {
        // tanggal null, mencegah error ketika $date dipakaikan fungsi carbon now biasa tanpa memberikan default tanggal valid
        $date = now()->parse('1111-11-11');
      }
    }
    $lembagas = DB::connection('pgsql')
      ->table('cutoff_rekap.rekap_siswa')
      ->where('tgl_simpan', $date);

    if (request()->user()->tingkat == 'kecamatan') {
      $lembagas->where('kec_id', request()->user()->kecamatan->kecamatan_kode);
    }
    if (request('kecamatan') && request('kecamatan') != 'all') {
      $lembagas->where('kec_id', request('kecamatan'));
    }
    if (request()->user()->tingkat == 'kelurahan') {
      $lembagas->where('desa', request()->user()->kelurahan->kelurahan_kode);
    }
    if (request('kelurahan') && request('kelurahan') != 'all') {
      $lembagas->where('desa', request('kelurahan'));
    }
    if (request('jenjang') && request('jenjang') == 'all') {
      $lembagas->whereIn('jenjang', ['TPA', 'SPS', 'TK', 'KB', 'RA']);
    } else {
      $lembagas->where('jenjang', request('jenjang'));
    }
    if (request('npsn')) {
      $lembagas->where('npsn', request('npsn'));
    }
    $lembagas = $lembagas->get();
    if (($lembagas->isEmpty() || request('load')) && ($date->startOfDay() == now()->startOfDay())) {
      $lembagas = $this->generate_siswa();
      if (is_string($lembagas)) {
        return $lembagas;
      }
    }
    // if (request()->user()->tingkat == 'kota') {
    $groupedLembagas = collect([]);
    foreach ($lembagas->pluck('jenjang')->unique() as $jenjang) {
      foreach ($lembagas->where('jenjang', $jenjang)->pluck('status')->unique() as $status) {
        if ($jenjang == 'TK') {
          $kelases = ['A', 'B'];
        } else {
          $kelases = [''];
        }
        foreach ($kelases as $kelas) {
          $filteredLembagas = $lembagas
            ->where('jenjang', $jenjang)
            ->where('status', $status)
            ->filter(function ($filteredLembaga) use ($jenjang, $kelas) {
              if ($jenjang == 'TK') {
                return $filteredLembaga->kelas == $kelas;
              } else {
                return true;
              }
            })
            ->values();
          $groupedLembaga = new stdClass;
          $groupedLembaga->jenjang = $jenjang == 'TK' ? "{$jenjang} {$kelas}" : $jenjang;
          switch ($groupedLembaga->jenjang) {
            case 'TPA':
              $groupedLembaga->jenjang_order = 1;
              break;
            case 'KB':
              $groupedLembaga->jenjang_order = 2;
              break;
            case 'SPS':
              $groupedLembaga->jenjang_order = 3;
              break;
            // case 'TK A':
            // 	$groupedLembaga->jenjang_order = 4;
            // 	break;
            // case 'TK B':
            // 	$groupedLembaga->jenjang_order = 5;
            // 	break;
            default:
              $groupedLembaga->jenjang_order = 4;
              break;
          }
          $groupedLembaga->status = $status;
          switch ($groupedLembaga->status) {
            case 'N':
              $groupedLembaga->status_order = 1;
              break;
            case 'S':
              $groupedLembaga->status_order = 2;
          }
          $groupedLembaga->jumlah_lembaga = count($filteredLembagas);
          $groupedLembaga->jumlah_sudah_tb_bb = $filteredLembagas->sum('jumlah_sudah_tb_bb');
          $groupedLembaga->jumlah_siswa = $filteredLembagas->sum('jumlah_siswa');
          $groupedLembaga->jumlah_sudah_polio_dosis_1 = $filteredLembagas->sum('jumlah_sudah_polio_dosis_1');
          $groupedLembaga->jumlah_sudah_polio_dosis_2 = $filteredLembagas->sum('jumlah_sudah_polio_dosis_2');
          $groupedLembaga->jumlah_pmt = $filteredLembagas->sum('jumlah_pmt');
          $groupedLembaga->jumlah_educitytour = $filteredLembagas->sum('jumlah_educitytour');
          $groupedLembaga->jumlah_prestasi = $filteredLembagas->sum('jumlah_prestasi');
          $groupedLembaga->jumlah_talenta = $filteredLembagas->sum('jumlah_talenta');
          $groupedLembaga->jumlah_manasik = $filteredLembagas->sum('jumlah_manasik');
          $groupedLembaga->jumlah_kitab_suci = $filteredLembagas->sum('jumlah_kitab_suci');
          $groupedLembaga->jumlah_gakin_pramis = $filteredLembagas->sum('jumlah_gakin_pramis');
          $groupedLembagas->push($groupedLembaga);
        }
      }
    }
    $lembagas = $groupedLembagas->sortBy(['jenjang_order', SORT_NATURAL], ['status_order', SORT_NATURAL])->values();
    // }

    // DATA LEMBAGA
    $jenjang = $request->jenjang;
    $kecamatan = $request->kecamatan;
    $kelurahan = $request->kelurahan;
    $load = $request->load;
    $tahun_anggaran = 2023;

    if ($jenjang == 'all') {
      $where_jenjang_profil = " AND s.jenjang in ('TPA','SPS','TK','KB','RA')";
      $where_jenjang_data = " AND jenjang in ('TPA','SPS','TK','KB','RA')";
    } else {
      $where_jenjang_profil = " AND s.jenjang='$jenjang'";
      $where_jenjang_data = " AND jenjang='$jenjang'";
    }

    if ($kecamatan == 'all') {
      $where_kecamatan_profil = "";
      $where_kecamatan_data = "";
    } else {
      $where_kecamatan_profil = " AND s.kec_id='$kecamatan'";
      $where_kecamatan_data = " AND kec_id='$kecamatan'";
    }

    if ($kelurahan == 'all') {
      $where_kelurahan_profil = "";
      $where_kelurahan_data = "";
    } else {
      $where_kelurahan_profil = " AND s.desa='$kelurahan'";
      $where_kelurahan_data = " AND desa='$kelurahan'";
    }

    $lembaga = DB::connection('pgsql')->table('cutoff_rekap.rekap_lembaga')->selectRaw("jenjang,status,COUNT(*) as jumlah")
      ->whereRaw("tgl_simpan='" . $date . "' $where_kecamatan_data $where_kelurahan_data $where_jenjang_data")
      ->groupByRaw("jenjang,status")
      ->get();

    $data = [
      'siswa' => $lembagas,
      'lembaga' => $lembaga,
    ];
    $content = view('rekap_satuan.data_siswa', $data)->render();
    return ['content' => $content];
  }

  function detail_siswa(Request $request, $jenjang_awal = null)
  {
    $bulan = $request->bulan; //"11"
    $jenis = $request->jenis; //"siswa"
    $jenjang = $request->jenjang; //"all"
    $kecamatan = $request->kecamatan; //"1210"
    $kelurahan = $request->kelurahan; //"all"
    $param = $request->param; //["70001017", "jumlah_siswa"]
    $npsn = $param[0];
    $kategori = $param[1];

    if ($jenjang_awal == 'sd') {
      $tabelnya = 'rekap_siswa_sd';
      $conn = 'db_sd';
      $judul = 'SD/MI';
      $tab = 'siswa_sd';
      $where = " AND kelas IN ('1','2')";
    } else {
      $tabelnya = 'rekap_siswa';
      $conn = 'pgsql_paud';
      $judul = 'PAUD';
      $tab = 'siswa';
      $where = "";
    }

    // $pmt = DB::connection('db_simpaud')
    // ->table('public.pmt_usulans')
    // ->join('public.pmt_pds', 'pmt_pds.pmt_usulan_id', 'pmt_usulans.id')
    // ->join('public.periodes', function ($q) {
    // 	$q->on('periodes.id', 'pmt_usulans.periode_id');
    // 	$q->where('periodes.program', 'pmt');
    // })
    // ->where('pmt_usulans.acc_usulan', '1')
    // ->where('pmt_pds.sent_pd', '1')
    // ->where('pmt_usulans.tahun', now()->year)
    // ->where('periodes.bulan_angka', now()->month)
    // ->where('pmt_usulans.npsn',$npsn)
    // ->selectRaw("pmt_pds.siswa_id")
    // ->groupBy(
    // 	'pmt_pds.siswa_id'
    // )->get();
    // $siswa_id = collect($pmt)->pluck('siswa_id')->toArray();
    $siswa_id = [];

    if ($kategori == 'jumlah_rombel') {
      $bulan = date('m');
      if ($bulan < 7) {
        $nama_tahun_ajaran = date('Y') . '/' . (date('Y') - 1);
        $semester = 2;
      } else {
        $nama_tahun_ajaran = date('Y') . '/' . (date('Y') + 1);
        $semester = 1;
      }
      $id_tahun_ajaran = DB::connection('db_sd')->table('public.tahun_ajaran')->selectRaw("id_tahun_ajaran,nama_tahun_ajaran")->where('nama_tahun_ajaran', $nama_tahun_ajaran)->first();

      $siswa = DB::connection($conn)
        ->table('public.rombongan_belajar as rb')
        ->join('public.sekolah as se', 'se.npsn', 'rb.npsn')
        ->selectRaw("se.nama,rb.npsn,rb.kelas,rb.rombel,'" . $id_tahun_ajaran->nama_tahun_ajaran . "' as nama_tahun_ajaran")
        ->when($jenjang_awal == 'sd', function ($q) {
          return $q->whereIn('rb.kelas', ['1', '2']);
        })
        ->where('rb.soft_delete', '0')
        ->where('rb.semester', $semester)
        ->where('rb.npsn', $npsn)
        ->where('rb.tahun_ajaran_id', $id_tahun_ajaran->id_tahun_ajaran)
        ->orderByRaw("rb.kelas ASC,rb.rombel ASC")->get();

      $data = [
        'judul' => strtoupper(str_replace('_', ' ', $kategori)),
        'lembagas' => $siswa,
      ];

      $content = view('rekap_satuan.detail_rombel', $data)->render();
    } else {
      $siswa = DB::connection($conn)->table('public.siswa as s')
        ->selectRaw("s.nama,s.nik,s.kelas,s.rombel,s.tgl_lahir,s.npsn,
				CASE WHEN s.verified_gakin is true THEN 'PRAMIS' ELSE CASE WHEN s.verified_gakin2 is true THEN 'GAMIS' ELSE '-' END END as status_gamis")
        ->whereRaw("s.npsn='$npsn' AND s.status_siswa='Aktif' AND s.alumni is not true $where")
        ->when($kategori == 'jumlah_gakin_pramis', function ($q) {
          return $q->whereRaw("(s.verified_gakin is true or s.verified_gakin2 is true)");
        })
        ->when($kategori == 'jumlah_sudah_tb_bb', function ($q) use ($npsn) {
          return $q->leftjoin('history.history_tb_bb_siswa as tt', 'tt.npsn', 's.npsn')
            ->whereRaw("tt.tahun='" . now()->year . "'");
        })
        ->when($kategori == 'jumlah_sudah_polio_dosis_1', function ($q) {
          return $q->leftjoin('history.riwayat_vaksinasi as v', 'v.siswa_id', '=', 's.siswa_id')
            ->whereRaw("v.jenis = 'Polio' AND v.dosis = 1");
        })
        ->when($kategori == 'jumlah_sudah_polio_dosis_2', function ($q) {
          return $q->leftjoin('history.riwayat_vaksinasi as v', 'v.siswa_id', '=', 's.siswa_id')
            ->whereRaw("v.jenis = 'Polio' AND v.dosis = 2");
        })
        ->when($kategori == 'jumlah_educitytour', function ($q) use ($npsn) {
          $educity = DB::connection('pgsql_paud')
            ->table('kegiatan_pendukung.edu_city_tour_jadwals as j')
            ->selectRaw("j.npsn,SUM(j.jumlah_pd_profil) as jumlah_siswa_educity")
            ->where('j.npsn', $npsn)
            ->where('j.deleted_at', null)
            ->whereRaw('EXTRACT(YEAR FROM j.tanggal) = ' . now()->year)
            ->where('j.status_jadwal', 'Setujui')
            ->groupByRaw("j.npsn")->first();
          $jumlah = !empty($educity) ? $educity->jumlah_siswa_educity : 0;

          return $q->limit($jumlah);
        })
        ->when($kategori == 'jumlah_pmt', function ($q) use ($siswa_id) {
          return $q->limit(count($siswa_id));
        })
        ->when(in_array($kategori, ['jumlah_prestasi', 'jumlah_talenta', 'jumlah_manasik', 'jumlah_kitab_suci']), function ($q) {
          return $q->limit(0);
        })
        ->groupByRaw("s.nama,s.nik,s.kelas,s.rombel,s.tgl_lahir,s.npsn,s.verified_gakin,s.verified_gakin2")
        ->orderByRaw("s.kelas,s.nama")
        ->get();

      $data = [
        'judul' => strtoupper(str_replace('_', ' ', $kategori)),
        'lembagas' => $siswa,
      ];

      $content = view('rekap_satuan.detail_siswa', $data)->render();
    }

    return ['content' => $content];
  }

  function detail_siswa_old(Request $request)
  {
    if (request('bulan') == now()->month || empty(request('bulan'))) {
      $date = now();
    } else {
      $lastDate = DB::connection('pgsql')
        ->table('cutoff_rekap.rekap_siswa')
        ->whereYear('tgl_simpan', now()->year)
        ->whereMonth('tgl_simpan', request('bulan'))
        ->select(DB::raw('MAX(tgl_simpan)'))
        ->first();
      if ($lastDate) {
        $date = now()->parse($lastDate->max);
      } else {
        // tanggal null, mencegah error ketika $date dipakaikan fungsi carbon now biasa tanpa memberikan default tanggal valid
        $date = now()->parse('1111-11-11');
      }
    }
    $lembagas = $this->query_siswa('lembaga')
      ->select('sekolah.jenjang', 'sekolah.npsn', 'siswa.id_siswa', 'siswa.nik', 'sekolah.nama as nama_sekolah', 'siswa.nama', 'siswa.tgl_lahir', DB::raw("EXTRACT(YEAR FROM(AGE(now(),siswa.tgl_lahir))) as umur"));
    switch (request('kolom')) {
      case 'jumlah_siswa':
        $lembagas = $lembagas->get();
        break;
      case 'jumlah_sudah_tb_bb':
        $lembagas->join('history.history_tb_bb_siswa', function ($q) use ($date) {
          $q->on('history_tb_bb_siswa.siswa_id', 'siswa.siswa_id');
          $q->where('history_tb_bb_siswa.tahun', $date->year);
          $q->where('history_tb_bb_siswa.bulan', $date->month);
          $q->where('history_tb_bb_siswa.bb', '!=', null);
          $q->where('history_tb_bb_siswa.tb', '!=', null);
        });
        $lembagas->select(
          'sekolah.jenjang',
          'sekolah.npsn',
          'siswa.id_siswa',
          'siswa.nik',
          'sekolah.nama as nama_sekolah',
          'siswa.nama',
          'siswa.tgl_lahir',
          DB::raw("EXTRACT(YEAR FROM(AGE(now(),siswa.tgl_lahir))) as umur"),
          DB::raw("STRING_AGG(CASE WHEN history_tb_bb_siswa.tahun = " . $date->year . " AND history_tb_bb_siswa.bulan = " . $date->month . "THEN history_tb_bb_siswa.tb::text ELSE NULL END, ',') AS tb"),
          DB::raw("STRING_AGG(CASE WHEN history_tb_bb_siswa.tahun = " . $date->year . " AND history_tb_bb_siswa.bulan = " . $date->month . " THEN history_tb_bb_siswa.bb::text ELSE NULL END, ',') AS bb"),
        );
        $lembagas->groupBy(
          'sekolah.jenjang',
          'sekolah.npsn',
          'siswa.id_siswa',
          'siswa.nik',
          'sekolah.nama',
          'siswa.nama',
          'siswa.tgl_lahir'
        );
        $lembagas = $lembagas->get();
        break;
      case 'jumlah_belum_tb_bb':
        $lembagas->leftJoin('history.history_tb_bb_siswa', function ($q) use ($date) {
          $q->on('history_tb_bb_siswa.siswa_id', 'siswa.siswa_id');
          $q->where('history_tb_bb_siswa.tahun', $date->year);
          $q->where('history_tb_bb_siswa.bulan', $date->month);
        });
        $lembagas->where(function ($q) {
          $q->orwhere('history_tb_bb_siswa.tb', null);
          $q->orwhere('history_tb_bb_siswa.bb', null);
          $q->orWhereRaw('history_tb_bb_siswa.id_history_tb_bb is null');
        });
        $lembagas->select(
          'sekolah.jenjang',
          'sekolah.npsn',
          'siswa.id_siswa',
          'siswa.nik',
          'sekolah.nama as nama_sekolah',
          'siswa.nama',
          'siswa.tgl_lahir',
          DB::raw("EXTRACT(YEAR FROM(AGE(now(),siswa.tgl_lahir))) as umur"),
          DB::raw("STRING_AGG(CASE WHEN history_tb_bb_siswa.tahun = " . $date->year . " AND history_tb_bb_siswa.bulan = " . $date->month . "THEN history_tb_bb_siswa.tb::text ELSE NULL END, ',') AS tb"),
          DB::raw("STRING_AGG(CASE WHEN history_tb_bb_siswa.tahun = " . $date->year . " AND history_tb_bb_siswa.bulan = " . $date->month . " THEN history_tb_bb_siswa.bb::text ELSE NULL END, ',') AS bb"),
        );
        $lembagas->groupBy(
          'sekolah.jenjang',
          'sekolah.npsn',
          'siswa.id_siswa',
          'siswa.nik',
          'sekolah.nama',
          'siswa.nama',
          'siswa.tgl_lahir'
        );
        $lembagas = $lembagas->get();
        break;
      case 'jumlah_sudah_polio_dosis_1':
        $lembagas->join('history.riwayat_vaksinasi', function ($q) {
          $q->on('riwayat_vaksinasi.siswa_id', 'siswa.siswa_id');
          $q->where('riwayat_vaksinasi.jenis', 'Polio');
          $q->where('riwayat_vaksinasi.dosis', 1);
          $q->where('riwayat_vaksinasi.status', 'Sudah');
        });
        $lembagas = $lembagas->get();
        break;
      case 'jumlah_belum_polio_dosis_1':
        $lembagas->leftJoin('history.riwayat_vaksinasi', function ($q) {
          $q->on('riwayat_vaksinasi.siswa_id', 'siswa.siswa_id');
          $q->where('riwayat_vaksinasi.jenis', 'Polio');
          $q->where('riwayat_vaksinasi.dosis', 1);
          $q->where('riwayat_vaksinasi.status', 'Sudah');
        });
        $lembagas->where(function ($q) {
          $q->orwhere('riwayat_vaksinasi.status', '!=', 'Sudah');
          $q->orWhereRaw('riwayat_vaksinasi.id is null');
        });
        $lembagas = $lembagas->get();
        break;
      case 'jumlah_sudah_polio_dosis_2':
        $lembagas->join('history.riwayat_vaksinasi', function ($q) {
          $q->on('riwayat_vaksinasi.siswa_id', 'siswa.siswa_id');
          $q->where('riwayat_vaksinasi.jenis', 'Polio');
          $q->where('riwayat_vaksinasi.dosis', 2);
          $q->where('riwayat_vaksinasi.status', 'Sudah');
        });
        $lembagas = $lembagas->get();
        break;
      case 'jumlah_belum_polio_dosis_2':
        $lembagas->leftJoin('history.riwayat_vaksinasi', function ($q) {
          $q->on('riwayat_vaksinasi.siswa_id', 'siswa.siswa_id');
          $q->where('riwayat_vaksinasi.jenis', 'Polio');
          $q->where('riwayat_vaksinasi.dosis', 2);
          $q->where('riwayat_vaksinasi.status', 'Sudah');
        });
        $lembagas->whereRaw('riwayat_vaksinasi.id is null');
        $lembagas = $lembagas->get();
        break;
      case 'jumlah_educitytour':
        $lembagas = $lembagas->get();
        foreach ($lembagas as $lembaga) {
          $lembaga->npsn_id_siswa = "{$lembaga->npsn}_{$lembaga->id_siswa}";
        }
        $educitytours = $this->query_siswa('educitytour')
          ->whereIn('npsn', $lembagas->pluck('npsn')->unique()->values())
          ->get();
        $edu_npsn_id_siswas = [];
        foreach ($educitytours as $educitytour) {
          $educitytour->peserta = json_decode($educitytour->peserta);
          foreach ($educitytour->peserta as $encryptedPeserta) {
            $decryptedPeserta = json_decode(trim(preg_replace('/\t+/', '', $encryptedPeserta)));
            if (!empty($decryptedPeserta)) {
              $edu_npsn_id_siswas[] = "{$decryptedPeserta[1]}_{$decryptedPeserta[0]}";
            }
          }
        }
        $lembagas = $lembagas->whereIn('npsn_id_siswa', $edu_npsn_id_siswas)->values();
        break;
      case 'jumlah_pmt':
        $lembagas = $lembagas->get();
        foreach ($lembagas as $lembaga) {
          $lembaga->npsn_id_siswa = "{$lembaga->npsn}_{$lembaga->id_siswa}";
        }
        $pmts = $this->query_siswa('pmt')
          ->where('periodes.tahun', $date->year)
          ->where('periodes.bulan_angka', $date->month)
          ->whereIn('pmt_usulans.npsn', $lembagas->pluck('npsn')->unique()->values())
          ->get();
        $pmt_npsn_id_siswas = [];
        foreach ($pmts as $pmt) {
          $pmt_npsn_id_siswas[] = "{$pmt->npsn}_{$pmt->siswa_id}";
        }
        $lembagas = $lembagas->whereIn('npsn_id_siswa', $pmt_npsn_id_siswas)->values();
        break;
      case 'jumlah_prestasi':
        $lembagas->join('prestasi_siswa', function ($query) {
          $query->on('prestasi_siswa.id_siswa', '=', 'siswa.id_siswa')
            ->whereRaw('prestasi_siswa.npsn = siswa.npsn');
        });
        $lembagas = $lembagas->get();
        break;
      case 'jumlah_kitab_suci':
        $lembagas = $lembagas->get();
        foreach ($lembagas as $lembaga) {
          $lembaga->npsn_nik = "{$lembaga->npsn}_{$lembaga->nik}";
        }
        $kitab_sucis = $this->query_siswa('kitab_suci')
          ->whereIn('npsn', $lembagas->pluck('npsn')->unique()->values())
          ->get();
        $suci_npsn_niks = [];
        foreach ($kitab_sucis as $kitab_suci) {
          $suci_npsn_niks[] = "{$kitab_suci->npsn}_{$kitab_suci->nik}";
        }
        $lembagas = $lembagas->whereIn('npsn_nik', $suci_npsn_niks)->values();
        break;
      case 'jumlah_gakin_pramis':
        $lembagas->where('status_mbr', true);
        $lembagas = $lembagas->get();
        break;
      default:
        if (in_array(request('kolom'), ['jumlah_lembaga', 'jumlah_lembaga_sudah_educitytour', 'jumlah_lembaga_belum_educitytour'])) {
          $jenjang = (in_array($request->jenjang, ['TK A', 'TK B'])) ? 'TK' : $request->jenjang;
          $kecamatan = $request->kecamatan;
          $status = $request->status;
          $kelurahan = $request->kelurahan;
          $load = $request->load;
          $tahun_anggaran = 2023;

          if ($kecamatan == 'all') {
            $where_kecamatan_profil = "";
            $where_kecamatan_data = "";
          } else {
            $where_kecamatan_profil = " AND s.kec_id='$kecamatan'";
            $where_kecamatan_data = " AND rekap_lembaga.kec_id='$kecamatan'";
          }

          if ($kelurahan == 'all') {
            $where_kelurahan_profil = "";
            $where_kelurahan_data = "";
          } else {
            $where_kelurahan_profil = " AND s.desa='$kelurahan'";
            $where_kelurahan_data = " AND rekap_lembaga.kel_id='$kelurahan'";
          }

          if (!in_array($jenjang, ['SD', 'MI'])) {
            $rekap_lembaga = 'cutoff_rekap.rekap_lembaga';
            $rekap_siswa = 'cutoff_rekap.rekap_siswa';
          } else {
            $rekap_lembaga = 'cutoff_rekap.rekap_lembaga_sd';
            $rekap_siswa = 'cutoff_rekap.rekap_siswa_sd';
          }

          $lembagas = DB::connection('pgsql')
            ->table("$rekap_lembaga as rekap_lembaga")
            ->when(in_array(request('kolom'), ['jumlah_lembaga_sudah_educitytour', 'jumlah_lembaga_belum_educitytour']), function ($q) use ($rekap_siswa) {
              $q->leftJoin("$rekap_siswa as educitytour", function ($q) {
                $q->on('rekap_lembaga.npsn', 'educitytour.npsn');
                $q->where('educitytour.tgl_simpan', now());
                $q->where('educitytour.jumlah_educitytour', '>', 0);
                $q->whereNotNull('educitytour.jumlah_educitytour');
              });
              if (request('kolom') == 'jumlah_lembaga_sudah_educitytour') {
                $q->whereNotNull('educitytour.npsn');
              } else {
                $q->whereNull('educitytour.npsn');
              }
            })
            ->selectRaw("rekap_lembaga.nama_lembaga as nama_sekolah,rekap_lembaga.npsn,rekap_lembaga.jenjang")
            ->where('rekap_lembaga.tgl_simpan', $date)
            ->whereRaw("rekap_lembaga.status='$status' and rekap_lembaga.jenjang='$jenjang' $where_kecamatan_data $where_kelurahan_data")->get();
          $data = [
            'judul' => request('judul'),
            'lembagas' => $lembagas,
          ];

          $content = view('rekap_satuan.detail_lembaga', $data)->render();

          return ['content' => $content];
        } else if (in_array(request('kolom'), ['jumlah_lembaga_sudah_talenta', 'jumlah_lembaga_belum_talenta'])) {
          $jenjang = (in_array($request->jenjang, ['TK A', 'TK B'])) ? 'TK' : $request->jenjang;
          $kecamatan = $request->kecamatan;
          $status = $request->status;
          $kelurahan = $request->kelurahan;
          $load = $request->load;
          $tahun_anggaran = 2023;

          if ($kecamatan == 'all') {
            $where_kecamatan_profil = "";
            $where_kecamatan_data = "";
          } else {
            $where_kecamatan_profil = " AND s.kec_id='$kecamatan'";
            $where_kecamatan_data = " AND rekap_lembaga.kec_id='$kecamatan'";
          }

          if ($kelurahan == 'all') {
            $where_kelurahan_profil = "";
            $where_kelurahan_data = "";
          } else {
            $where_kelurahan_profil = " AND s.desa='$kelurahan'";
            $where_kelurahan_data = " AND rekap_lembaga.kel_id='$kelurahan'";
          }

          if (!in_array($jenjang, ['SD', 'MI'])) {
            $rekap_lembaga = 'cutoff_rekap.rekap_lembaga';
            $rekap_siswa = 'cutoff_rekap.rekap_siswa';
          } else {
            $rekap_lembaga = 'cutoff_rekap.rekap_lembaga_sd';
            $rekap_siswa = 'cutoff_rekap.rekap_siswa_sd';
          }

          $lembagas = DB::connection('pgsql')
            ->table("$rekap_lembaga as rekap_lembaga")
            ->when(in_array(request('kolom'), ['jumlah_lembaga_sudah_talenta', 'jumlah_lembaga_belum_talenta']), function ($q) use ($rekap_siswa) {
              $q->leftJoin("$rekap_siswa as talenta", function ($q) {
                $q->on('rekap_lembaga.npsn', 'talenta.npsn');
                $q->where('talenta.tgl_simpan', now());
                $q->where('talenta.jumlah_talenta', '>', 0);
                $q->whereNotNull('talenta.jumlah_talenta');
              });
              if (request('kolom') == 'jumlah_lembaga_sudah_talenta') {
                $q->whereNotNull('talenta.npsn');
              } else {
                $q->whereNull('talenta.npsn');
              }
            })
            ->selectRaw("rekap_lembaga.nama_lembaga as nama_sekolah,rekap_lembaga.npsn,rekap_lembaga.jenjang")
            ->where('rekap_lembaga.tgl_simpan', $date)
            ->whereRaw("rekap_lembaga.status='$status' and rekap_lembaga.jenjang='$jenjang' $where_kecamatan_data $where_kelurahan_data")->get();
          $data = [
            'judul' => request('judul'),
            'lembagas' => $lembagas,
          ];

          $content = view('rekap_satuan.detail_lembaga', $data)->render();

          return ['content' => $content];
        }
        break;
    }

    $data = [
      'judul' => request('judul'),
      'lembagas' => $lembagas,
    ];

    $content = view('rekap_satuan.detail_siswa', $data)->render();

    return ['content' => $content];
  }

  function query_keuangan($jenis)
  {
    switch ($jenis) {
      case 'keuangan':
        $query = DB::connection('db_simpaud')
          ->table('public.bop_master_view')
          ->when(request('jenis_anggaran'), function ($q) {
            $q->where('jenis_anggaran', request('jenis_anggaran'));
          })
          ->selectRaw("
				npsn,
				jenis_anggaran,
				round(SUM(operasional)) as jumlah_anggaran_operasional,
				round(SUM(r_tw1_op)) as realisasi_operasional_tw1,
				round(SUM(r_tw2_op)) as realisasi_operasional_tw2,
				round(SUM(r_tw3_op)) as realisasi_operasional_tw3,
				round(SUM(r_tw4_op)) as realisasi_operasional_tw4,
				round(SUM(total_anggaran)) as jumlah_total_anggaran,
				round(SUM(total_realisasi)) as jumlah_total_realisasi,
				count(distinct npsn) as jumlah_lembaga
				")
          ->groupBy('npsn', 'jenis_anggaran');
        break;
      default:
        $query = '';
        break;
    }
    return $query;
  }

  function get_keuangan()
  {
    $lembagas = $this->query_siswa('lembaga')
      ->select('sekolah.npsn')
      ->get();
    $bop_masters = $this->query_keuangan('keuangan')
      ->whereIn('npsn', $lembagas->pluck('npsn')->unique()->values())
      ->get();
    $keuangans = collect([]);
    foreach ($bop_masters->pluck('jenis_anggaran')->unique()->values() as $jenis_anggaran) {
      $bop_master = $bop_masters->where('jenis_anggaran', $jenis_anggaran)->values();
      $keuangan = new stdClass;
      $keuangan->jenis_anggaran = $jenis_anggaran;
      $keuangan->jumlah_anggaran_operasional = $bop_master->sum('jumlah_anggaran_operasional');
      $keuangan->realisasi_operasional_tw1 = $bop_master->sum('realisasi_operasional_tw1');
      $keuangan->realisasi_operasional_tw2 = $bop_master->sum('realisasi_operasional_tw2');
      $keuangan->realisasi_operasional_tw3 = $bop_master->sum('realisasi_operasional_tw3');
      $keuangan->realisasi_operasional_tw4 = $bop_master->sum('realisasi_operasional_tw4');
      $keuangan->jumlah_anggaran_personal = $bop_master->sum('jumlah_anggaran_personal');
      $keuangan->jumlah_total_anggaran = $bop_master->sum('jumlah_total_anggaran');
      $keuangan->jumlah_total_realisasi = $bop_master->sum('jumlah_total_realisasi');
      $keuangan->jumlah_lembaga = $bop_master->sum('jumlah_lembaga');
      $keuangans->push($keuangan);
    }

    $data = [
      'keuangans' => $keuangans,
    ];

    $content = view('rekap_satuan.data_keuangan', $data)->render();
    return ['content' => $content];
  }

  function detail_keuangan()
  {
    $judul = ucwords(str_replace('_', ' ', request('kolom')));
    $lembagas = $this->query_siswa('lembaga');
    $bop_masters = $this->query_keuangan('keuangan')
      ->get();
    $lembagas->select('sekolah.status', 'sekolah.jenjang', 'sekolah.npsn', 'sekolah.nama as nama_sekolah',  'sekolah.kec_id', 'sekolah.desa', 'sekolah.alamat', 'kecamatan.kecamatan_dispenduk', 'kelurahan.kelurahan_dispenduk');
    $lembagas->groupBy('sekolah.status', 'sekolah.jenjang', 'sekolah.npsn', 'sekolah.nama',  'sekolah.kec_id', 'sekolah.desa', 'sekolah.alamat', 'kecamatan.kecamatan_dispenduk', 'kelurahan.kelurahan_dispenduk');
    $lembagas->whereIn('sekolah.npsn', $bop_masters->pluck('npsn')->unique()->values());
    $lembagas = $lembagas->get();
    if (request('kolom') != 'jumlah_lembaga' && request()->user()->tingkat != 'kota') {
      return;
    }
    foreach ($lembagas as $lembaga) {
      switch (request('kolom')) {
        case 'jumlah_lembaga':
          break;
        case 'jumlah_total_anggaran':
          $lembaga->jumlah_total_anggaran = $bop_masters->where('npsn', $lembaga->npsn)->first()->jumlah_total_anggaran;
          break;
        case 'jumlah_anggaran_operasional':
          $lembaga->jumlah_anggaran_operasional = $bop_masters->where('npsn', $lembaga->npsn)->first()->jumlah_anggaran_operasional;
          break;
        case 'realisasi_operasional_tw1':
          $lembaga->realisasi_operasional_tw1 = $bop_masters->where('npsn', $lembaga->npsn)->first()->realisasi_operasional_tw1;
          break;
        case 'realisasi_operasional_tw2':
          $lembaga->realisasi_operasional_tw2 = $bop_masters->where('npsn', $lembaga->npsn)->first()->realisasi_operasional_tw2;
          break;
        case 'realisasi_operasional_tw3':
          $lembaga->realisasi_operasional_tw3 = $bop_masters->where('npsn', $lembaga->npsn)->first()->realisasi_operasional_tw3;
          break;
        case 'realisasi_operasional_tw4':
          $lembaga->realisasi_operasional_tw4 = $bop_masters->where('npsn', $lembaga->npsn)->first()->realisasi_operasional_tw4;
          break;
        case 'jumlah_total_realisasi':
          $lembaga->jumlah_total_realisasi = $bop_masters->where('npsn', $lembaga->npsn)->first()->jumlah_total_realisasi;
          break;
      }
    }

    $data = [
      'judul' => $judul,
      'lembagas' => $lembagas,
    ];

    $content = view('rekap_satuan.detail_keuangan', $data)->render();

    return ['content' => $content];
  }

  function generate_siswa_sd()
  {
    $lembagas = $this->query_siswa('lembaga_sd')
      ->leftJoin('history.riwayat_vaksinasi', 'riwayat_vaksinasi.siswa_id', 'siswa.siswa_id')
      ->leftJoin('history.history_tb_bb_siswa', 'history_tb_bb_siswa.siswa_id', 'siswa.siswa_id')
      ->leftJoin('prestasi_siswa', function ($query) {
        $query->on('prestasi_siswa.id_siswa', '=', 'siswa.id_siswa')
          ->whereRaw('prestasi_siswa.npsn = siswa.npsn');
      })
      ->selectRaw("sekolah.status, sekolah.jenjang, siswa.kelas,
			sekolah.npsn, sekolah.nama,  sekolah.kec_id, sekolah.desa, sekolah.alamat, kecamatan.kecamatan_dispenduk, kelurahan.kelurahan_dispenduk,
			STRING_AGG(DISTINCT(CASE WHEN siswa.status_siswa = 'Aktif' and siswa.alumni is not true THEN siswa.id_siswa::text ELSE NULL END), ',') as id_siswas,
			STRING_AGG(DISTINCT(siswa.siswa_id::text), ',') as siswa_ids,
			STRING_AGG(DISTINCT(siswa.nik::text), ',') as niks,
			COUNT(DISTINCT(CASE WHEN siswa.status_siswa = 'Aktif' and siswa.alumni is not true then siswa.siswa_id ELSE NULL END)) as jumlah_siswa,
			COUNT(DISTINCT(CASE WHEN siswa.status_siswa = 'Aktif' and siswa.alumni is not true and history_tb_bb_siswa.tahun = " . now()->year . " AND history_tb_bb_siswa.bulan = " . now()->month . " AND history_tb_bb_siswa.bb IS NOT NULL AND history_tb_bb_siswa.tb IS NOT NULL THEN siswa.siswa_id ELSE NULL END)) AS jumlah_sudah_tb_bb,
			COUNT(DISTINCT(CASE WHEN siswa.status_siswa = 'Aktif' and siswa.alumni is not true and riwayat_vaksinasi.jenis = 'Polio' AND riwayat_vaksinasi.dosis = 1 AND riwayat_vaksinasi.status = 'Sudah' THEN siswa.siswa_id ELSE NULL END)) AS jumlah_sudah_polio_dosis_1,
			COUNT(DISTINCT(CASE WHEN siswa.status_siswa = 'Aktif' and siswa.alumni is not true and riwayat_vaksinasi.jenis = 'Polio' AND riwayat_vaksinasi.dosis = 2 AND riwayat_vaksinasi.status = 'Sudah' THEN siswa.siswa_id ELSE NULL END)) AS jumlah_sudah_polio_dosis_2,
			COALESCE(null,0) as jumlah_pmt,
			COALESCE(null,0) as jumlah_educitytour,
			COUNT(distinct case when siswa.status_siswa = 'Aktif' AND prestasi_siswa.id_siswa is not null then siswa.siswa_id else null end) as jumlah_prestasi,
			COALESCE(null,0) as jumlah_talenta,
			COALESCE(null,0) as jumlah_manasik,
			COALESCE(null,0) as jumlah_kitab_suci,
			COUNT(distinct case when siswa.status_siswa = 'Aktif' and wm.kab_id ilike '%surabaya%' and verified_siswa is true and status_mbr is true then siswa.siswa_id else null end) as jumlah_gakin_pramis
			")
      ->groupBy('sekolah.status', 'sekolah.jenjang', 'siswa.kelas', 'sekolah.npsn', 'sekolah.nama', 'sekolah.kec_id', 'sekolah.desa', 'sekolah.alamat', 'kecamatan.kecamatan_dispenduk', 'kelurahan.kelurahan_dispenduk')
      ->orderBy('status')->get();

    $kitab_sucis = $this->query_siswa('kitab_suci')
      ->whereIn('npsn', $lembagas->pluck('npsn'))
      ->get();

    $talentas = $this->query_siswa('talenta')
      ->whereIn('npsn', $lembagas->pluck('npsn'))
      ->get();

    foreach ($lembagas as $lembaga) {
      // Data PMT =========================================================================
      // PMT menggunakan id_siswa di profil dengan nama siswa_id
      // $siswa_id = explode(',', $lembaga->siswa_ids);
      $id_siswa = explode(',', $lembaga->id_siswas);
      $lembaga->jumlah_pmt = '0';
      $lembaga->jumlah_educitytour = '0';

      // Data Penghafal Kitab Suci =========================================================================
      $nik = explode(',', $lembaga->niks);
      $kitab_suci = $kitab_sucis
        ->where('npsn', $lembaga->npsn)
        ->whereIn('nik', $nik)
        ->unique()
        ->values();
      $lembaga->jumlah_kitab_suci = count($kitab_suci);

      // Data Talenta Seni =========================================================================
      $talenta = $talentas
        ->where('npsn', $lembaga->npsn)
        ->sum('jumlah_pd');
      $lembaga->jumlah_talenta = $talenta;
    }
    foreach ($lembagas as $lembaga) {
      unset($lembaga->id_siswas);
      unset($lembaga->siswa_ids);
      unset($lembaga->niks);
      $lembaga->tgl_simpan = date('Y-m-d');
    }
    $inserts = $lembagas->toArray();
    foreach ($inserts as $key => $insert) {
      $inserts[$key] = (array) $insert;
    }

    $splitInserts = array_chunk($inserts, 40);
    //Generate Cutoff
    DB::connection('pgsql')->beginTransaction();
    try {
      DB::connection('pgsql')
        ->table('cutoff_rekap.rekap_siswa_sd')
        ->where('tgl_simpan', date('Y-m-d'))
        ->whereIn('npsn', $lembagas->pluck('npsn'))
        ->delete();

      foreach ($splitInserts as $splitInsert) {
        DB::connection('pgsql')
          ->table('cutoff_rekap.rekap_siswa_sd')
          ->insert($splitInsert);
      }

      DB::connection('pgsql')->commit();
    } catch (\Exception $e) {
      DB::connection('pgsql')->rollBack();
      return "Terjadi kesalahan. Pesan : {$e->getMessage()}";
    }

    return $lembagas;
  }

  function get_siswa_sd(Request $request)
  {
    if (request('bulan') == now()->month || empty(request('bulan'))) {
      $date = now();
    } else {
      $lastDate = DB::connection('pgsql')
        ->table('cutoff_rekap.rekap_siswa_sd')
        ->whereYear('tgl_simpan', now()->year)
        ->whereMonth('tgl_simpan', request('bulan'))
        ->select(DB::raw('MAX(tgl_simpan)'))
        ->first();
      if ($lastDate) {
        $date = now()->parse($lastDate->max);
      } else {
        // tanggal null, mencegah error ketika $date dipakaikan fungsi carbon now biasa tanpa memberikan default tanggal valid
        $date = now()->parse('1111-11-11');
      }
    }
    $lembagas = DB::connection('pgsql')
      ->table('cutoff_rekap.rekap_siswa_sd')
      ->where('tgl_simpan', $date);

    if (request()->user()->tingkat == 'kecamatan') {
      $lembagas->where('kec_id', request()->user()->kecamatan->kecamatan_kode);
    }
    if (request('kecamatan') && request('kecamatan') != 'all') {
      $lembagas->where('kec_id', request('kecamatan'));
    }
    if (request()->user()->tingkat == 'kelurahan') {
      $lembagas->where('desa', request()->user()->kelurahan->kelurahan_kode);
    }
    if (request('kelurahan') && request('kelurahan') != 'all') {
      $lembagas->where('desa', request('kelurahan'));
    }

    $lembagas->whereIn('jenjang', ['SD', 'MI']);

    if (request('npsn')) {
      $lembagas->where('npsn', request('npsn'));
    }
    $lembagas = $lembagas->get();

    if ($lembagas->isEmpty() || request('load')) {
      $lembagas = $this->generate_siswa_sd();
      if (is_string($lembagas)) {
        return $lembagas;
      }
    }
    // if (request()->user()->tingkat == 'kota') {
    $groupedLembagas = collect([]);
    foreach ($lembagas->pluck('jenjang')->unique() as $jenjang) {
      foreach ($lembagas->where('jenjang', $jenjang)->pluck('status')->unique() as $status) {
        $filteredLembagas = $lembagas
          ->where('jenjang', $jenjang)
          ->where('status', $status)
          ->values();
        $groupedLembaga = new stdClass;
        $groupedLembaga->jenjang = $jenjang == 'TK' ? "{$jenjang}" : $jenjang;
        switch ($groupedLembaga->jenjang) {
          case 'TPA':
            $groupedLembaga->jenjang_order = 1;
            break;
          case 'KB':
            $groupedLembaga->jenjang_order = 2;
            break;
          case 'SPS':
            $groupedLembaga->jenjang_order = 3;
            break;
          // case 'TK A':
          // 	$groupedLembaga->jenjang_order = 4;
          // 	break;
          // case 'TK B':
          // 	$groupedLembaga->jenjang_order = 5;
          // 	break;
          default:
            $groupedLembaga->jenjang_order = 4;
            break;
        }
        $groupedLembaga->status = $status;
        switch ($groupedLembaga->status) {
          case 'N':
            $groupedLembaga->status_order = 1;
            break;
          case 'S':
            $groupedLembaga->status_order = 2;
        }
        $groupedLembaga->jumlah_lembaga = count($filteredLembagas);
        $groupedLembaga->jumlah_sudah_tb_bb = $filteredLembagas->sum('jumlah_sudah_tb_bb');
        $groupedLembaga->jumlah_siswa = $filteredLembagas->sum('jumlah_siswa');
        $groupedLembaga->jumlah_sudah_polio_dosis_1 = $filteredLembagas->sum('jumlah_sudah_polio_dosis_1');
        $groupedLembaga->jumlah_sudah_polio_dosis_2 = $filteredLembagas->sum('jumlah_sudah_polio_dosis_2');
        $groupedLembaga->jumlah_pmt = $filteredLembagas->sum('jumlah_pmt');
        $groupedLembaga->jumlah_educitytour = $filteredLembagas->sum('jumlah_educitytour');
        $groupedLembaga->jumlah_prestasi = $filteredLembagas->sum('jumlah_prestasi');
        $groupedLembaga->jumlah_talenta = $filteredLembagas->sum('jumlah_talenta');
        $groupedLembaga->jumlah_manasik = $filteredLembagas->sum('jumlah_manasik');
        $groupedLembaga->jumlah_kitab_suci = $filteredLembagas->sum('jumlah_kitab_suci');
        $groupedLembaga->jumlah_gakin_pramis = $filteredLembagas->sum('jumlah_gakin_pramis');
        $groupedLembagas->push($groupedLembaga);
      }
    }
    $lembagas = $groupedLembagas->sortBy(['jenjang_order', SORT_NATURAL], ['status_order', SORT_NATURAL])->values();
    // }

    // DATA LEMBAGA
    $jenjang = $request->jenjang;
    $kecamatan = $request->kecamatan;
    $kelurahan = $request->kelurahan;
    $load = $request->load;
    $tahun_anggaran = 2023;

    $where_jenjang_profil = " AND s.jenjang in ('SD','MI')";
    $where_jenjang_data = " AND jenjang in ('SD','MI')";

    if ($kecamatan == 'all') {
      $where_kecamatan_profil = "";
      $where_kecamatan_data = "";
    } else {
      $where_kecamatan_profil = " AND s.kec_id='$kecamatan'";
      $where_kecamatan_data = " AND kec_id='$kecamatan'";
    }

    if ($kelurahan == 'all') {
      $where_kelurahan_profil = "";
      $where_kelurahan_data = "";
    } else {
      $where_kelurahan_profil = " AND s.desa='$kelurahan'";
      $where_kelurahan_data = " AND desa='$kelurahan'";
    }

    $lembaga = DB::connection('pgsql')->table('cutoff_rekap.rekap_lembaga_sd')->selectRaw("jenjang,status,COUNT(*) as jumlah")
      ->whereRaw("tgl_simpan='" . $date . "' $where_kecamatan_data $where_kelurahan_data $where_jenjang_data")
      ->groupByRaw("jenjang,status")
      ->get();

    $data = [
      'siswa' => $lembagas,
      'lembaga' => $lembaga,
    ];
    $content = view('rekap_satuan.data_siswa_sd', $data)->render();
    return ['content' => $content];
  }

  function detail_siswa_sd(Request $request)
  {
    if (request('bulan') == now()->month || empty(request('bulan'))) {
      $date = now();
    } else {
      $lastDate = DB::connection('pgsql')
        ->table('cutoff_rekap.rekap_siswa_sd')
        ->whereYear('tgl_simpan', now()->year)
        ->whereMonth('tgl_simpan', request('bulan'))
        ->select(DB::raw('MAX(tgl_simpan)'))
        ->first();
      if ($lastDate) {
        $date = now()->parse($lastDate->max);
      } else {
        // tanggal null, mencegah error ketika $date dipakaikan fungsi carbon now biasa tanpa memberikan default tanggal valid
        $date = now()->parse('1111-11-11');
      }
    }
    $lembagas = $this->query_siswa('lembaga_sd')
      ->select('sekolah.jenjang', 'sekolah.npsn', 'siswa.kelas', 'siswa.id_siswa', 'siswa.nik', 'sekolah.nama as nama_sekolah', 'siswa.nama', 'siswa.tgl_lahir', DB::raw("EXTRACT(YEAR FROM(AGE(now(),siswa.tgl_lahir))) as umur"));

    switch (request('kolom')) {
      case 'jumlah_siswa':
        $lembagas = $lembagas->get();
        break;
      case 'jumlah_sudah_tb_bb':
        $lembagas->join('history.history_tb_bb_siswa', function ($q) use ($date) {
          $q->on('history_tb_bb_siswa.siswa_id', 'siswa.siswa_id');
          $q->where('history_tb_bb_siswa.tahun', $date->year);
          $q->where('history_tb_bb_siswa.bulan', $date->month);
          $q->where('history_tb_bb_siswa.bb', '!=', null);
          $q->where('history_tb_bb_siswa.tb', '!=', null);
        });
        $lembagas->select(
          'sekolah.jenjang',
          'sekolah.npsn',
          'siswa.id_siswa',
          'siswa.nik',
          'sekolah.nama as nama_sekolah',
          'siswa.nama',
          'siswa.tgl_lahir',
          DB::raw("EXTRACT(YEAR FROM(AGE(now(),siswa.tgl_lahir))) as umur"),
          DB::raw("STRING_AGG(CASE WHEN history_tb_bb_siswa.tahun = " . $date->year . " AND history_tb_bb_siswa.bulan = " . $date->month . "THEN history_tb_bb_siswa.tb::text ELSE NULL END, ',') AS tb"),
          DB::raw("STRING_AGG(CASE WHEN history_tb_bb_siswa.tahun = " . $date->year . " AND history_tb_bb_siswa.bulan = " . $date->month . " THEN history_tb_bb_siswa.bb::text ELSE NULL END, ',') AS bb"),
        );
        $lembagas->groupBy(
          'sekolah.jenjang',
          'sekolah.npsn',
          'siswa.id_siswa',
          'siswa.nik',
          'sekolah.nama',
          'siswa.nama',
          'siswa.tgl_lahir'
        );
        $lembagas = $lembagas->get();
        break;
      case 'jumlah_belum_tb_bb':
        $lembagas->leftJoin('history.history_tb_bb_siswa', function ($q) use ($date) {
          $q->on('history_tb_bb_siswa.siswa_id', 'siswa.siswa_id');
          $q->where('history_tb_bb_siswa.tahun', $date->year);
          $q->where('history_tb_bb_siswa.bulan', $date->month);
        });
        $lembagas->where(function ($q) {
          $q->orwhere('history_tb_bb_siswa.tb', null);
          $q->orwhere('history_tb_bb_siswa.bb', null);
          $q->orWhereRaw('history_tb_bb_siswa.id_history_tb_bb is null');
        });
        $lembagas->select(
          'sekolah.jenjang',
          'sekolah.npsn',
          'siswa.id_siswa',
          'siswa.nik',
          'sekolah.nama as nama_sekolah',
          'siswa.nama',
          'siswa.tgl_lahir',
          DB::raw("EXTRACT(YEAR FROM(AGE(now(),siswa.tgl_lahir))) as umur"),
          DB::raw("STRING_AGG(CASE WHEN history_tb_bb_siswa.tahun = " . $date->year . " AND history_tb_bb_siswa.bulan = " . $date->month . "THEN history_tb_bb_siswa.tb::text ELSE NULL END, ',') AS tb"),
          DB::raw("STRING_AGG(CASE WHEN history_tb_bb_siswa.tahun = " . $date->year . " AND history_tb_bb_siswa.bulan = " . $date->month . " THEN history_tb_bb_siswa.bb::text ELSE NULL END, ',') AS bb"),
        );
        $lembagas->groupBy(
          'sekolah.jenjang',
          'sekolah.npsn',
          'siswa.id_siswa',
          'siswa.nik',
          'sekolah.nama',
          'siswa.nama',
          'siswa.tgl_lahir'
        );
        $lembagas = $lembagas->get();
        break;
      case 'jumlah_sudah_polio_dosis_1':
        $lembagas->join('history.riwayat_vaksinasi', function ($q) {
          $q->on('riwayat_vaksinasi.siswa_id', 'siswa.siswa_id');
          $q->where('riwayat_vaksinasi.jenis', 'Polio');
          $q->where('riwayat_vaksinasi.dosis', 1);
          $q->where('riwayat_vaksinasi.status', 'Sudah');
        });
        $lembagas = $lembagas->get();
        break;
      case 'jumlah_belum_polio_dosis_1':
        $lembagas->leftJoin('history.riwayat_vaksinasi', function ($q) {
          $q->on('riwayat_vaksinasi.siswa_id', 'siswa.siswa_id');
          $q->where('riwayat_vaksinasi.jenis', 'Polio');
          $q->where('riwayat_vaksinasi.dosis', 1);
          $q->where('riwayat_vaksinasi.status', 'Sudah');
        });
        $lembagas->where(function ($q) {
          $q->orwhere('riwayat_vaksinasi.status', '!=', 'Sudah');
          $q->orWhereRaw('riwayat_vaksinasi.id is null');
        });
        $lembagas = $lembagas->get();
        break;
      case 'jumlah_sudah_polio_dosis_2':
        $lembagas->join('history.riwayat_vaksinasi', function ($q) {
          $q->on('riwayat_vaksinasi.siswa_id', 'siswa.siswa_id');
          $q->where('riwayat_vaksinasi.jenis', 'Polio');
          $q->where('riwayat_vaksinasi.dosis', 2);
          $q->where('riwayat_vaksinasi.status', 'Sudah');
        });
        $lembagas = $lembagas->get();
        break;
      case 'jumlah_belum_polio_dosis_2':
        $lembagas->leftJoin('history.riwayat_vaksinasi', function ($q) {
          $q->on('riwayat_vaksinasi.siswa_id', 'siswa.siswa_id');
          $q->where('riwayat_vaksinasi.jenis', 'Polio');
          $q->where('riwayat_vaksinasi.dosis', 2);
          $q->where('riwayat_vaksinasi.status', 'Sudah');
        });
        $lembagas->whereRaw('riwayat_vaksinasi.id is null');
        $lembagas = $lembagas->get();
        break;
      case 'jumlah_educitytour':
        $lembagas = $lembagas->get();
        foreach ($lembagas as $lembaga) {
          $lembaga->npsn_id_siswa = "{$lembaga->npsn}_{$lembaga->id_siswa}";
        }
        $educitytours = $this->query_siswa('educitytour')
          ->whereIn('npsn', $lembagas->pluck('npsn')->unique()->values())
          ->get();
        $edu_npsn_id_siswas = [];
        foreach ($educitytours as $educitytour) {
          $educitytour->peserta = json_decode($educitytour->peserta);
          foreach ($educitytour->peserta as $encryptedPeserta) {
            $decryptedPeserta = json_decode(trim(preg_replace('/\t+/', '', $encryptedPeserta)));
            if (!empty($decryptedPeserta)) {
              $edu_npsn_id_siswas[] = "{$decryptedPeserta[1]}_{$decryptedPeserta[0]}";
            }
          }
        }
        $lembagas = $lembagas->whereIn('npsn_id_siswa', $edu_npsn_id_siswas)->values();
        break;
      case 'jumlah_pmt':
        $lembagas = $lembagas->get();
        foreach ($lembagas as $lembaga) {
          $lembaga->npsn_id_siswa = "{$lembaga->npsn}_{$lembaga->id_siswa}";
        }
        $pmts = $this->query_siswa('pmt')
          ->where('periodes.tahun', $date->year)
          ->where('periodes.bulan_angka', $date->month)
          ->whereIn('pmt_usulans.npsn', $lembagas->pluck('npsn')->unique()->values())
          ->get();
        $pmt_npsn_id_siswas = [];
        foreach ($pmts as $pmt) {
          $pmt_npsn_id_siswas[] = "{$pmt->npsn}_{$pmt->siswa_id}";
        }
        $lembagas = $lembagas->whereIn('npsn_id_siswa', $pmt_npsn_id_siswas)->values();
        break;
      case 'jumlah_prestasi':
        $lembagas->join('prestasi_siswa', function ($query) {
          $query->on('prestasi_siswa.id_siswa', '=', 'siswa.id_siswa')
            ->whereRaw('prestasi_siswa.npsn = siswa.npsn');
        });
        $lembagas = $lembagas->get();
        break;
      case 'jumlah_kitab_suci':
        $lembagas = $lembagas->get();
        foreach ($lembagas as $lembaga) {
          $lembaga->npsn_nik = "{$lembaga->npsn}_{$lembaga->nik}";
        }
        $kitab_sucis = $this->query_siswa('kitab_suci')
          ->whereIn('npsn', $lembagas->pluck('npsn')->unique()->values())
          ->get();
        $suci_npsn_niks = [];
        foreach ($kitab_sucis as $kitab_suci) {
          $suci_npsn_niks[] = "{$kitab_suci->npsn}_{$kitab_suci->nik}";
        }
        $lembagas = $lembagas->whereIn('npsn_nik', $suci_npsn_niks)->values();
        break;
      case 'jumlah_gakin_pramis':
        $lembagas->where('status_mbr', true);
        $lembagas = $lembagas->get();
        break;
      case 'jumlah_lembaga':
        $jenjang = $request->jenjang;
        $kecamatan = $request->kecamatan;
        $status = $request->status;
        $kelurahan = $request->kelurahan;
        $load = $request->load;
        $tahun_anggaran = 2023;

        if ($kecamatan == 'all') {
          $where_kecamatan_profil = "";
          $where_kecamatan_data = "";
        } else {
          $where_kecamatan_profil = " AND s.kec_id='$kecamatan'";
          $where_kecamatan_data = " AND kec_id='$kecamatan'";
        }

        if ($kelurahan == 'all') {
          $where_kelurahan_profil = "";
          $where_kelurahan_data = "";
        } else {
          $where_kelurahan_profil = " AND s.desa='$kelurahan'";
          $where_kelurahan_data = " AND kel_id='$kelurahan'";
        }

        $lembagas = DB::connection('pgsql')->table('cutoff_rekap.rekap_lembaga_sd')
          ->selectRaw("nama_lembaga as nama_sekolah,npsn,jenjang")
          ->where('tgl_simpan', $date)
          ->whereRaw("status='$status' and jenjang='$jenjang' $where_kecamatan_data $where_kelurahan_data")->get();
        $data = [
          'judul' => request('judul'),
          'lembagas' => $lembagas,
        ];

        $content = view('rekap_satuan.detail_lembaga', $data)->render();

        return ['content' => $content];
        break;
    }


    $data = [
      'judul' => request('judul'),
      'lembagas' => $lembagas,
    ];

    $content = view('rekap_satuan.detail_siswa', $data)->render();

    return ['content' => $content];
  }

  function get_gtk_old(Request $request)
  {
    return $request->all();
    if (request('bulan') == now()->month || empty(request('bulan'))) {
      $date = now();
    } else {
      $lastDate = DB::connection('pgsql')
        ->table('cutoff_rekap.rekap_gtk')
        ->whereYear('tgl_simpan', now()->year)
        ->whereMonth('tgl_simpan', request('bulan'))
        ->select(DB::raw('MAX(tgl_simpan)'))
        ->first();
      if ($lastDate) {
        $date = now()->parse($lastDate->max);
      } else {
        // tanggal null, mencegah error ketika $date dipakaikan fungsi carbon now biasa tanpa memberikan default tanggal valid
        $date = now()->parse('1111-11-11');
      }
    }
    $jenjang = $request->jenjang;
    $kecamatan = $request->kecamatan;
    $kelurahan = $request->kelurahan;
    $load = $request->load;
    $tahun_anggaran = 2023;

    if ($jenjang == 'all') {
      $where_jenjang_profil = " AND s.jenjang in ('TPA','SPS','TK','KB','RA')";
      $where_jenjang_data = " AND jenjang in ('TPA','SPS','TK','KB','RA')";
    } else {
      $where_jenjang_profil = " AND s.jenjang='$jenjang'";
      $where_jenjang_data = " AND jenjang='$jenjang'";
    }

    if ($kecamatan == 'all') {
      $where_kecamatan_profil = "";
      $where_kecamatan_data = "";
    } else {
      $where_kecamatan_profil = " AND s.kec_id='$kecamatan'";
      $where_kecamatan_data = " AND kec_id='$kecamatan'";
    }

    if ($kelurahan == 'all') {
      $where_kelurahan_profil = "";
      $where_kelurahan_data = "";
    } else {
      $where_kelurahan_profil = " AND s.desa='$kelurahan'";
      $where_kelurahan_data = " AND desa='$kelurahan'";
    }

    $cutoff = DB::connection('pgsql')->table('cutoff_rekap.rekap_gtk')->select('jenjang')->whereRaw("tgl_simpan='" . $date . "' $where_kecamatan_data $where_kelurahan_data $where_jenjang_data")->count('jenjang');

    if (($cutoff == 0 || $load != '') && ($date->startOfDay() == now()->startOfDay())) {
      $tfg = DB::connection('db_siagus')->table('tfg_pengajuan_pegawai as tfg')
        ->join('skpbm_jadwal_pegawai as sjp', function ($query) {
          $query->on('tfg.skpbm_jadwal_pegawai_id', '=', 'sjp.id_skpbm_jadwal_pegawai');
        })
        ->join('tfg_transaksi as tt', 'tt.id_tfg_transaksi', '=', 'tfg.tfg_transaksi_id')
        ->whereRaw("setujui = ? and tt.tahun = ?", ['Ya', $tahun_anggaran])->distinct()->pluck('nik')->toArray();

      $tfg = implode("','", $tfg);

      $tunjangan = DB::connection('db_siagus')->table('tjn_pengajuan_pegawai as tjn')
        ->join('skpbm_jadwal_pegawai as sjp', function ($query) {
          $query->on('tjn.skpbm_jadwal_pegawai_id', '=', 'sjp.id_skpbm_jadwal_pegawai');
        })
        ->join('tjn_transaksi as tt', 'tt.id_tjn_transaksi', '=', 'tjn.tjn_transaksi_id')
        ->whereRaw(
          "tt.tahun = ? and (
				(jenis_tunjangan= 'Tpg' and tjn.status = 'Diusulkan') or
				(jenis_tunjangan = 'Tamsil' and tjn.status = 'Diusulkan') or
				(jenis_tunjangan = 'Tod' and tjn.status = 'Diterima'))  and sjp.is_aktif is true",
          [$tahun_anggaran]
        )->select('nik', 'jenis_tunjangan')->distinct()->get();

      $tamsil = $tunjangan->where('jenis_tunjangan', 'Tamsil')->pluck('nik')->unique()->toArray();
      $tpg = $tunjangan->where('jenis_tunjangan', 'Tpg')->pluck('nik')->unique()->toArray();
      $tod = $tunjangan->where('jenis_tunjangan', 'Tod')->pluck('nik')->unique()->toArray();

      $tamsil = implode("','", $tamsil);
      $tpg = implode("','", $tpg);
      $tod = implode("','", $tod);

      $jaspel = DB::connection('db_simpaud')
        ->table('jaspel.jaspel_pendidik as pendidik')
        ->join('jaspel.jaspel_usulan as usulan', 'usulan.id', 'pendidik.id_pengajuan')
        ->join('jaspel.jaspel_periode as periode', 'periode.id', 'usulan.periode_id')
        ->join('jaspel.jaspel_periode_bulan_tahun as periode_bulan_tahun', function ($q) {
          $q->on('periode_bulan_tahun.periode_id', 'periode.id');
          $q->where('periode_bulan_tahun.tahun', now()->year);
          $q->where('periode_bulan_tahun.bulan', now()->month);
        })
        ->where('pendidik.status', 'Disetujui')
        ->distinct()->pluck('nik')->toArray();
      $jaspel = implode("','", $jaspel);

      $sinau_bareng = DB::connection('db_sinau_bareng')
        ->table('jadwal')->selectRaw("nik")
        ->whereRaw("EXTRACT(YEAR FROM tanggal)='" . $tahun_anggaran . "'")->distinct()->pluck('nik')->toArray();
      $sinau_bareng = implode("','", $sinau_bareng);


      $pelatihan = DB::connection('db_siagus')
        ->table('rw_pelatihan')->selectRaw("nik")
        ->distinct()->pluck('nik')->toArray();
      $pelatihan = implode("','", $pelatihan);

      $guru_penggerak = DB::connection('db_siagus')
        ->table('temporary.guru_penggerak')->join('skpbm_jadwal_pegawai', 'nopes', '=', 'no_ukg')
        ->selectRaw("nik")
        ->whereRaw('status_lulus = ?', ['10 - Lulus Diklat'])
        ->distinct()->pluck('nik')->toArray();
      $guru_penggerak = implode("','", $guru_penggerak);

      $lembaga = DB::connection('pgsql_paud')->table('sekolah as s')
        ->join('master_user as mu', function ($query) {
          $query->on('s.npsn', '=', 'mu.npsn')->whereRaw('mu.user_enable is true');
        })
        ->join('pegawai as p', function ($query) {
          $query->on('p.npsn', 's.npsn')
            ->whereRaw('keterangan = ? and induk is true', ['Aktif']);
          // ->wherein('jabatan',['1','2','3','11','12','19','33','34','35','36','37','38','39','40','41','42','43','46','47','48','50','51','53']);
        })
        ->leftJoin('kecamatan as kec', function ($query) {
          $query->on('kec.kecamatan_kode', '=', 's.kec_id');
        })
        ->leftJoin('kelurahan as kel', function ($query) {
          $query->on('kel.kelurahan_kode', '=', 's.desa');
        })
        ->leftJoin('prestasi_guru as pg', function ($query) {
          $query->on('pg.peg_id', '=', 'p.peg_id')
            ->whereRaw('pg.peg_id = p.peg_id');
        })
        ->whereRaw("s.nama not ilike '%terbuka%' $where_kecamatan_profil $where_kelurahan_profil $where_jenjang_profil")
        ->selectRaw("
				s.nama as nama,
				--'' as nama,
				s.npsn,
				CONCAT(s.alamat) as alamat,
				--'' as alamat,
				s.status, s.jenjang,s.desa,s.kec_id,kel.kelurahan_dispenduk, kec.kecamatan_dispenduk,
				count(distinct p.peg_id) as jumlah_gtk,

				count(distinct case when status_guru in ('PNS','CPNS') then p.peg_id else null end) as jumlah_pns,
				count(distinct case when status_guru in ('PPPK') then p.peg_id else null end) as jumlah_pppk,
				count(distinct case when status_guru not in ('PNS','CPNS','PPPK') then p.peg_id else null end) as jumlah_nonpns,

				count(distinct case when p.pendidikan_terakhir = 9 then p.peg_id else null end) as jumlah_s1,
				count(distinct case when p.pendidikan_terakhir = 10 then p.peg_id else null end) as jumlah_s2,
				count(distinct case when p.pendidikan_terakhir = 11 then p.peg_id else null end) as jumlah_s3,


				count(distinct pg.peg_id) as jumlah_prestasi,
				count(distinct case when (nuptk is null or trim(nuptk) = '' or nuptk = '-' ) then p.peg_id else null end ) as jumlah_belum_nuptk,
				count(distinct case when p.no_ktp in ('" . $pelatihan . "')  then p.peg_id else null end ) as jumlah_pelatihan,
				count(distinct case when p.no_ktp in ('" . $guru_penggerak . "')  then p.peg_id else null end ) as jumlah_guru_penggerak,


				count(distinct case when p.no_ktp in ('" . $tfg . "') and p.jenis_pegawai in ('KS','Guru') then p.peg_id else null end ) as jumlah_tfg,
				count(distinct case when p.no_ktp in ('" . $tpg . "') and p.jenis_pegawai in ('KS','Guru') then p.peg_id else null end ) as jumlah_tpg,
				count(distinct case when p.no_ktp in ('" . $tamsil . "') and p.jenis_pegawai in ('KS','Guru') then p.peg_id else null end ) as jumlah_tamsil,
				count(distinct case when p.no_ktp in ('" . $jaspel . "') and p.jenis_pegawai in ('KS','Guru') then p.peg_id else null end ) as jumlah_jaspel,
				count(distinct case when s.status = 'N' and p.status_guru in ('PNS','PPPK') and p.jenis_pegawai in ('KS','Guru') then p.peg_id else null end ) as jumlah_tukin,
				count(distinct case when p.no_ktp in ('" . $sinau_bareng . "') then p.peg_id else null end ) as jumlah_sinaubareng,


				count(distinct case when p.jenis_pegawai in ('KS','Guru') and EXTRACT(YEAR FROM tgl_lahir) = " . (date('Y') - 60) . " then p.peg_id
				when p.jenis_pegawai in ('Tendik') and EXTRACT(YEAR FROM tgl_lahir) = " . (date('Y') - 58) . " then p.peg_id
				else null end ) as jumlah_pensiun_tahun,
				count(distinct case when p.keterangan='Aktif' AND p.status_guru in ('PNS','CPNS') AND (p.tmt_gol is not null AND (" . date('Y') . "-EXTRACT(YEAR FROM p.tmt_gol))=4) then p.peg_id else null end) as jumlah_kenkat_4,
				count(distinct case when p.keterangan='Aktif' AND p.status_guru in ('PNS','CPNS') AND (p.tmt_gol is not null AND (" . date('Y') . "-EXTRACT(YEAR FROM p.tmt_gol))>4) then p.peg_id else null end) as jumlah_kenkat_lebih_4,
				count(distinct case when p.sertifikasi_bidang_studi is not null AND p.sertifikasi_bidang_studi!='' AND p.sertifikasi_bidang_studi!='-' then p.peg_id else null end) as jumlah_sertifikasi,


				concat('" . date('Y-m-d') . "') as tgl_simpan

				")->groupBy('s.nama', 's.npsn', 's.alamat', 's.status', 's.jenjang', 'kec.kecamatan_dispenduk', 'kel.kelurahan_dispenduk')->orderBy('status')->get();

      $temp = [];
      foreach ($lembaga as $key => $rr) {
        foreach ($rr as $kolom => $r) {
          $temp[$key][$kolom] = $r;
        }
      }

      $arr_temp = array_chunk($temp, 2);

      DB::connection('pgsql')->table('cutoff_rekap.rekap_gtk')->whereRaw("tgl_simpan='" . date('Y-m-d') . "' $where_kecamatan_data $where_kelurahan_data $where_jenjang_data")->delete();
      for ($i = 0; $i < count($arr_temp); $i++) {
        $simpan = DB::connection('pgsql')->table('cutoff_rekap.rekap_gtk')->insert(collect($arr_temp[$i])->toArray());
      }
    }

    $gtk = DB::connection('pgsql')->table('cutoff_rekap.rekap_gtk')
      ->selectRaw("
			jenjang,
			status,
			COUNT(DISTINCT(npsn)) as jumlah_lembaga,
			SUM(jumlah_gtk) as jumlah_gtk,
			SUM(jumlah_nonpns) as jumlah_nonpns,
			SUM(jumlah_s1) as jumlah_s1,
			SUM(jumlah_s2) as jumlah_s2,
			SUM(jumlah_s3) as jumlah_s3,
			SUM(jumlah_prestasi) as jumlah_prestasi,
			SUM(jumlah_belum_nuptk) as jumlah_belum_nuptk,
			SUM(jumlah_pelatihan) as jumlah_pelatihan,
			SUM(jumlah_guru_penggerak) as jumlah_guru_penggerak,
			SUM(jumlah_tfg) as jumlah_tfg,
			SUM(jumlah_tpg) as jumlah_tpg,
			SUM(jumlah_tamsil) as jumlah_tamsil,
			SUM(jumlah_jaspel) as jumlah_jaspel,
			SUM(jumlah_tukin) as jumlah_tukin,
			SUM(jumlah_sinaubareng) as jumlah_sinaubareng,
			SUM(jumlah_pensiun_tahun) as jumlah_pensiun_tahun,
			SUM(jumlah_kenkat_4) as jumlah_kenkat_4,
			SUM(jumlah_kenkat_lebih_4) as jumlah_kenkat_lebih_4,
			SUM(jumlah_pns) as jumlah_pns,
			SUM(jumlah_pppk) as jumlah_pppk,
			SUM(jumlah_sertifikasi) as jumlah_sertifikasi
			")
      ->whereRaw("tgl_simpan='" . $date . "' $where_kecamatan_data $where_kelurahan_data $where_jenjang_data")
      ->groupByRaw("jenjang,status")
      ->orderByRaw("CASE jenjang WHEN 'TPA' THEN 1 WHEN 'KB' THEN 2 WHEN 'SPS' THEN 3 ELSE 4 END,status")
      ->get();

    $lembaga = DB::connection('pgsql')->table('cutoff_rekap.rekap_lembaga')->selectRaw("jenjang,status,COUNT(*) as jumlah")->whereRaw("tgl_simpan='" . $date . "' $where_kecamatan_data $where_kelurahan_data $where_jenjang_data")
      ->groupByRaw("jenjang,status")
      ->get();

    $data = [
      'lembaga' => $lembaga,
      'gtk' => $gtk,
    ];

    $content = view('rekap_satuan.data_gtk', $data)->render();
    return ['content' => $content];
  }

  function get_gtk(Request $request, $jenjang_awal = null)
  {
    $bulan = $request->bulan; //"11"
    $jenis = $request->jenis; //"siswa"
    $jenjang = $request->jenjang; //"all"
    $kecamatan = $request->kecamatan; //"all"
    $kelurahan = $request->kelurahan; //"all"
    $load = $request->load; //null
    $date = date('Y-m-d');
    $tahun_anggaran = 2024;

    $lembagas = DB::connection('pgsql')
      ->table('cutoff_rekap.rekap_gtk')
      ->where('tgl_simpan', $date);

    if ($lembagas->count() == 0) {
      $load = 'load';
    }

    if ($load != '') {
      $lembagas = DB::connection('pgsql')
        ->table('cutoff_rekap.rekap_gtk')
        ->where('tgl_simpan', $date)->delete();

      $tfg = DB::connection('db_siagus')->table('tfg_pengajuan_pegawai as tfg')
        ->join('skpbm_jadwal_pegawai as sjp', function ($query) {
          $query->on('tfg.skpbm_jadwal_pegawai_id', '=', 'sjp.id_skpbm_jadwal_pegawai');
        })
        ->join('tfg_transaksi as tt', 'tt.id_tfg_transaksi', '=', 'tfg.tfg_transaksi_id')
        ->whereRaw("setujui = ? and tt.tahun = ?", ['Ya', $tahun_anggaran])->distinct()->pluck('nik')->toArray();

      $tfg = implode("','", $tfg);

      $tunjangan = DB::connection('db_siagus')->table('tjn_pengajuan_pegawai as tjn')
        ->join('skpbm_jadwal_pegawai as sjp', function ($query) {
          $query->on('tjn.skpbm_jadwal_pegawai_id', '=', 'sjp.id_skpbm_jadwal_pegawai');
        })
        ->join('tjn_transaksi as tt', 'tt.id_tjn_transaksi', '=', 'tjn.tjn_transaksi_id')
        ->whereRaw(
          "tt.tahun = ? and (
				(jenis_tunjangan= 'Tpg' and tjn.status = 'Diusulkan') or
				(jenis_tunjangan = 'Tamsil' and tjn.status = 'Diusulkan') or
				(jenis_tunjangan = 'Tod' and tjn.status = 'Diterima'))  and sjp.is_aktif is true",
          [$tahun_anggaran]
        )->select('nik', 'jenis_tunjangan')->distinct()->get();

      $tamsil = $tunjangan->where('jenis_tunjangan', 'Tamsil')->pluck('nik')->unique()->toArray();
      $tpg = $tunjangan->where('jenis_tunjangan', 'Tpg')->pluck('nik')->unique()->toArray();
      $tod = $tunjangan->where('jenis_tunjangan', 'Tod')->pluck('nik')->unique()->toArray();

      $tamsil = implode("','", $tamsil);
      $tpg = implode("','", $tpg);
      $tod = implode("','", $tod);

      $jaspel = DB::connection('db_simpaud')
        ->table('jaspel.jaspel_pendidik as pendidik')
        ->join('jaspel.jaspel_usulan as usulan', 'usulan.id', 'pendidik.id_pengajuan')
        ->join('jaspel.jaspel_periode as periode', 'periode.id', 'usulan.periode_id')
        ->join('jaspel.jaspel_periode_bulan_tahun as periode_bulan_tahun', function ($q) {
          $q->on('periode_bulan_tahun.periode_id', 'periode.id');
          $q->where('periode_bulan_tahun.tahun', now()->year);
          $q->where('periode_bulan_tahun.bulan', now()->month);
        })
        ->where('pendidik.status', 'Disetujui')
        ->distinct()->pluck('nik')->toArray();
      $jaspel = implode("','", $jaspel);

      $sinau_bareng = DB::connection('db_sinau_bareng')
        ->table('jadwal')->selectRaw("nik")
        ->whereRaw("EXTRACT(YEAR FROM tanggal)='" . $tahun_anggaran . "'")->distinct()->pluck('nik')->toArray();
      $sinau_bareng = implode("','", $sinau_bareng);


      $pelatihan = DB::connection('db_siagus')
        ->table('rw_pelatihan')->selectRaw("nik")
        ->distinct()->pluck('nik')->toArray();
      $pelatihan = implode("','", $pelatihan);

      $guru_penggerak = DB::connection('db_siagus')
        ->table('temporary.guru_penggerak')->join('skpbm_jadwal_pegawai', 'nopes', '=', 'no_ukg')
        ->selectRaw("nik")
        ->whereRaw('status_lulus = ?', ['10 - Lulus Diklat'])
        ->distinct()->pluck('nik')->toArray();
      $guru_penggerak = implode("','", $guru_penggerak);

      $lembagas = DB::connection('pgsql_paud')
        ->table('public.sekolah as se')
        ->selectRaw("
				se.npsn,se.nama,se.jenjang as jenjang,se.kec_id,se.desa,'$date' as tgl_simpan,se.alamat,se.status,
				kec.kecamatan_dispenduk,kel.kelurahan_dispenduk,
				COUNT(DISTINCT s.peg_id) as jumlah_gtk,
				COUNT(DISTINCT CASE WHEN s.status_guru NOT IN ('PNS','PPPK') THEN s.peg_id  END) as jumlah_nonpns,
				COUNT(DISTINCT CASE WHEN s.pendidikan_terakhir in (9) THEN s.peg_id  END) as jumlah_s1,
				COUNT(DISTINCT CASE WHEN s.pendidikan_terakhir in (10) THEN s.peg_id  END) as jumlah_s2,
				COUNT(DISTINCT CASE WHEN s.pendidikan_terakhir in (11) THEN s.peg_id  END) as jumlah_s3,
				0 as jumlah_prestasi,
				COUNT(DISTINCT CASE WHEN (s.nuptk is null or s.nuptk='') THEN s.peg_id END) as jumlah_belum_nuptk,
				COUNT(DISTINCT CASE WHEN (s.no_ktp IN ('$pelatihan')) THEN s.peg_id END) as jumlah_pelatihan,
				COUNT(DISTINCT CASE WHEN (s.no_ktp IN ('$guru_penggerak')) THEN s.peg_id END) as jumlah_guru_penggerak,
				COUNT(DISTINCT CASE WHEN (s.no_ktp IN ('$tfg')) THEN s.peg_id END) as jumlah_tfg,
				COUNT(DISTINCT CASE WHEN (s.no_ktp IN ('$tpg')) THEN s.peg_id END) as jumlah_tpg,
				COUNT(DISTINCT CASE WHEN (s.no_ktp IN ('$tamsil')) THEN s.peg_id END) as jumlah_tamsil,
				COUNT(DISTINCT CASE WHEN (s.no_ktp IN ('$jaspel')) THEN s.peg_id END) as jumlah_jaspel,
				0 as jumlah_tukin,
				COUNT(DISTINCT CASE WHEN (s.no_ktp IN ('$sinau_bareng')) THEN s.peg_id END) as jumlah_sinaubareng,
				0 as jumlah_pensiun_tahun,
				0 as jumlah_kenkat_4,
				0 as jumlah_kenkat_lebih_4,
				COUNT(DISTINCT CASE WHEN s.status_guru IN ('PNS') THEN s.peg_id END) as jumlah_pns,
				COUNT(DISTINCT CASE WHEN s.status_guru IN ('PPPK') THEN s.peg_id END) as jumlah_pppk,
				0 as jumlah_tpp,
				0 as jumlah_sertifikasi
				")
        ->join('public.master_user as u', 'u.username', '=', 'se.npsn')
        ->leftjoin('public.kecamatan as kec', 'kec.kecamatan_kode', '=', 'se.kec_id')
        ->leftjoin('public.kelurahan as kel', 'kel.kelurahan_kode', '=', 'se.desa')
        ->leftjoin('public.pegawai as s', function ($j) {
          return $j->on('s.npsn', '=', 'se.npsn')->where('s.keterangan', 'Aktif')->whereRaw("s.induk is true");
        })
        ->when($jenjang, function ($q) use ($jenjang) {
          if ($jenjang == 'all') {
            return $q->whereIn('se.jenjang', ['SPS', 'TPA', 'KB', 'TK', 'RA']);
          } else {
            return $q->where('se.jenjang', $jenjang);
          }
        })
        ->when($kecamatan != 'all', function ($q) use ($kecamatan) {
          return $q->where('se.kec_id', $kecamatan);
        })
        ->when($kelurahan != 'all', function ($q) use ($kelurahan) {
          return $q->where('se.desa', $kelurahan);
        })
        ->whereRaw("u.user_enable is true")
        ->groupByRaw("
				se.npsn,kec.kecamatan_dispenduk,kel.kelurahan_dispenduk
				")
        ->orderByRaw("CASE WHEN se.jenjang='TPA' THEN 1 ELSE CASE WHEN se.jenjang='SPS' THEN 2 ELSE CASE WHEN se.jenjang='KB' THEN 3 ELSE 4 END END END ASC,se.jenjang DESC")->get();

      $data_insert = collect($lembagas)
        ->map(fn($row) => (array) $row)
        ->chunk(500)
        ->each(function ($chunk) {
          DB::connection('pgsql')
            ->table('cutoff_rekap.rekap_gtk')
            ->insert($chunk->toArray());
        });
    }

    $lembagas = DB::connection('pgsql')
      ->table('cutoff_rekap.rekap_gtk')
      ->selectRaw("
			npsn,
			nama,
			status,
			jenjang,
			SUM(jumlah_gtk) as jumlah_gtk,
			SUM(jumlah_nonpns) as jumlah_nonpns,
			SUM(jumlah_s1) as jumlah_s1,
			SUM(jumlah_s2) as jumlah_s2,
			SUM(jumlah_s3) as jumlah_s3,
			SUM(jumlah_belum_nuptk) as jumlah_belum_nuptk,
			SUM(jumlah_pelatihan) as jumlah_pelatihan,
			SUM(jumlah_guru_penggerak) as jumlah_guru_penggerak,
			SUM(jumlah_tfg) as jumlah_tfg,
			SUM(jumlah_tpg) as jumlah_tpg,
			SUM(jumlah_tamsil) as jumlah_tamsil,
			SUM(jumlah_jaspel) as jumlah_jaspel,
			SUM(jumlah_sinaubareng) as jumlah_sinaubareng,
			SUM(jumlah_pns) as jumlah_pns,
			SUM(jumlah_pppk) as jumlah_pppk
			")
      ->where('tgl_simpan', $date)
      ->when($jenjang, function ($q) use ($jenjang) {
        if ($jenjang == 'all') {
          return $q->whereIn('jenjang', ['SPS', 'TPA', 'KB', 'TK', 'RA']);
        } else {
          return $q->where('jenjang', $jenjang);
        }
      })
      ->when($kecamatan != 'all', function ($q) use ($kecamatan) {
        return $q->where('kec_id', $kecamatan);
      })
      ->when($kelurahan != 'all', function ($q) use ($kelurahan) {
        return $q->where('desa', $kelurahan);
      })
      ->orderByRaw("CASE WHEN jenjang='TPA' THEN 1 ELSE CASE WHEN jenjang='SPS' THEN 2 ELSE CASE WHEN jenjang='KB' THEN 3 ELSE 4 END END END ASC,jenjang DESC")
      ->groupByRaw("npsn,nama,status,jenjang")
      ->get();

    $data = [
      'jumlah' => $lembagas,
    ];
    $content = view('rekap_satuan.data_gtk', $data)->render();
    return ['content' => $content];
  }

  function get_sarpras(Request $request)
  {
    if (request('bulan') == now()->month || empty(request('bulan'))) {
      $date = now();
    } else {
      $lastDate = DB::connection('pgsql')
        ->table('cutoff_rekap.rekap_sarpras')
        ->whereYear('tgl_simpan', now()->year)
        ->whereMonth('tgl_simpan', request('bulan'))
        ->select(DB::raw('MAX(tgl_simpan)'))
        ->first();
      if ($lastDate) {
        $date = now()->parse($lastDate->max);
      } else {
        // tanggal null, mencegah error ketika $date dipakaikan fungsi carbon now biasa tanpa memberikan default tanggal valid
        $date = now()->parse('1111-11-11');
      }
    }
    $jenjang = $request->jenjang;
    $kecamatan = $request->kecamatan;
    $kelurahan = $request->kelurahan;
    $load = $request->load;
    $tahun_anggaran = 2023;

    if ($jenjang == 'all') {
      $where_jenjang_profil = " AND s.jenjang in ('TPA','SPS','TK','KB','RA')";
      $where_jenjang_data = " AND jenjang in ('TPA','SPS','TK','KB','RA')";
    } else {
      $where_jenjang_profil = " AND s.jenjang='$jenjang'";
      $where_jenjang_data = " AND jenjang='$jenjang'";
    }

    if ($kecamatan == 'all') {
      $where_kecamatan_profil = "";
      $where_kecamatan_data = "";
    } else {
      $where_kecamatan_profil = " AND s.kec_id='$kecamatan'";
      $where_kecamatan_data = " AND kec_id='$kecamatan'";
    }

    if ($kelurahan == 'all') {
      $where_kelurahan_profil = "";
      $where_kelurahan_data = "";
    } else {
      $where_kelurahan_profil = " AND s.desa='$kelurahan'";
      $where_kelurahan_data = " AND desa='$kelurahan'";
    }

    $tahun = date('Y');
    $bulan = date('m');
    if ($bulan < 7) {
      $tahun_cari = ($tahun - 1) . '/' . $tahun;
      $semester = 2;
      $antara = "'$tahun-01-01' AND '$tahun-06-30'";
    } else {
      $tahun_cari = $tahun . '/' . ($tahun + 1);
      $semester = 1;
      $antara = "'$tahun-07-01' AND '$tahun-12-31'";
    }
    $tahun_ajaran = DB::connection('db_sd')->table('tahun_ajaran')->where('nama_tahun_ajaran', $tahun_cari)->first();
    $id_tahun_ajaran = $tahun_ajaran->id_tahun_ajaran;
    $semester = 1;

    $cutoff = DB::connection('pgsql')->table('cutoff_rekap.rekap_sarpras')->select('jenjang')->whereRaw("tgl_simpan='" . $date . "' $where_kecamatan_data $where_kelurahan_data $where_jenjang_data")->count('jenjang');

    $sekolah = DB::connection('pgsql_paud')->table('sekolah as s')->join('master_user as u', 'u.username', 's.npsn')
      ->selectRaw("s.npsn,s.status,s.jenjang,s.kec_id,s.desa")
      ->whereRaw("u.user_enable is true $where_kecamatan_profil $where_kelurahan_profil $where_jenjang_profil")->get();
    $npsn = $sekolah->pluck('npsn')->toArray();

    if (($cutoff == 0 || $load != '') && ($date->startOfDay() == now()->startOfDay())) {

      $data = [];
      $data = DB::connection('db_simbas')->table('lokasi_ruang')->join('sekolah as s', function ($query) {
        $query->on('s.npsn', '=', 'lokasi_ruang.npsn');
      })
        ->whereraw("is_delete = ?", ['0'])
        ->whereIn('s.npsn', $npsn)
        ->whereIn('id_prasarana', [
          1,
          23,
          10,
          11,
          2,
          3,
          4,
          5,
          6,
          7,
          8,
          9,
          42,
          26,
          27,
          28,
          29,
          14,
          13,
          31,
          41,
          49,
          21,
          22,
          24,
          49
        ])
        ->selectRaw("
				s.jenjang,
				s.npsn,
				s.status,
				s.kec_id as kec_id,
				case when id_prasarana = 1 then 'Ruang Kelas'
				when id_prasarana = 23 then 'Ruang Guru'
				when id_prasarana in (10,11) then 'Perpustakaan'
				when id_prasarana in (2,3,4,5,6,7,8,9,42) then 'Laboratorium'
				when id_prasarana in (26,27,28,29) then 'Kamar Mandi'
				when id_prasarana in (14) then 'Ruang UKS'
				when id_prasarana in (13) then 'Ruang Aula'
				when id_prasarana in (31) then 'Tempat Ibadah'
				when id_prasarana in (41,49) then 'Sarana Olahraga'
				when id_prasarana in (21) then 'Ruang BK'
				when id_prasarana in (22) then 'Ruang Kepala Sekolah / Lembaga'
				when id_prasarana in (24) then 'Ruang TU'
				when id_prasarana in (49) then 'Lapangan Upacara / Olahraga'
				end as jenis_ruangan,
				sum(case when kondisi = 'Baik' then 1 else 0 end) as jumlah_baik,
				sum(case when kondisi = 'Rusak Berat' then 1 else 0 end) as jumlah_buruk,
				sum(case when kondisi in ('Rusak Ringan','Rusak Sedang') then 1 else 0 end) as jumlah_sedang,
				count(id_prasarana) jumlah_ruang,
				CONCAT('" . date('Y-m-d') . "') as tgl_simpan
				")
        ->groupbyRaw("s.npsn, s.jenjang, s.status, s.kec_id, jenis_ruangan")->get();

      if ($data->count() > 0) {
        $temp = [];
        $data = $data->toArray();
        foreach ($data as $key => $rr) {
          $desa = $sekolah->where('npsn', $rr->npsn)->first();
          if (!empty($desa)) {
            $temp[$key]['desa'] = $desa->desa;
          } else {
            $temp[$key]['desa'] = '';
          }
          foreach ($rr as $kolom => $r) {
            $temp[$key][$kolom] = $r;
          }
        }
        // dd($temp);
        DB::connection('pgsql')->table('cutoff_rekap.rekap_sarpras')
          ->whereRaw("tgl_simpan='" . date('Y-m-d') . "' $where_kecamatan_data $where_kelurahan_data $where_jenjang_data")
          ->delete();
        DB::connection('pgsql')->table('cutoff_rekap.rekap_sarpras')->insert($temp);
      }
    }

    $rombel = DB::connection('pgsql_paud')->table('rombongan_belajar')->whereIn('npsn', $npsn)->whereraw("tahun_ajaran_id=? and semester=? and soft_delete=?", [$id_tahun_ajaran, $semester, 0])
      ->selectraw('kelas,npsn, count(*) jumlah')
      ->orderBy('kelas', 'ASC')
      ->groupby('kelas', 'npsn')->get();

    $lembaga = DB::connection('pgsql')->table('cutoff_rekap.rekap_sarpras')
      ->selectRaw("
			jenjang,status,jenis_ruangan,
			SUM(jumlah_ruang) as jumlah_ruang,
			SUM(jumlah_baik) as jumlah_baik,
			SUM(jumlah_buruk) as jumlah_buruk,
			SUM(jumlah_sedang) as jumlah_sedang
			")
      ->whereRaw("tgl_simpan='" . $date . "' $where_kecamatan_data $where_kelurahan_data $where_jenjang_data")
      ->groupByRaw("jenjang,status,jenis_ruangan")
      ->get();

    $data = [
      'lembaga' => $lembaga,
      'rombel' => $rombel
    ];

    $content = view('rekap_satuan.data_sarpras', $data)->render();

    return ['content' => $content];
  }

  function get_lembaga(Request $request)
  {
    if (request('bulan') == now()->month || empty(request('bulan'))) {
      $date = now();
    } else {
      $lastDate = DB::connection('pgsql')
        ->table('cutoff_rekap.rekap_lembaga')
        ->whereYear('tgl_simpan', now()->year)
        ->whereMonth('tgl_simpan', request('bulan'))
        ->select(DB::raw('MAX(tgl_simpan)'))
        ->first();
      if ($lastDate) {
        $date = now()->parse($lastDate->max);
      } else {
        // tanggal null, mencegah error ketika $date dipakaikan fungsi carbon now biasa tanpa memberikan default tanggal valid
        $date = now()->parse('1111-11-11');
      }
    }
    $jenjang = $request->jenjang;
    $kecamatan = $request->kecamatan;
    $kelurahan = $request->kelurahan;
    $load = $request->load;
    $tahun_anggaran = 2023;

    if ($jenjang == 'all') {
      $where_jenjang_profil = " AND s.jenjang in ('TPA','SPS','TK','KB','RA')";
      $where_jenjang_data = " AND rekap_lembaga.jenjang in ('TPA','SPS','TK','KB','RA')";
    } else {
      $where_jenjang_profil = " AND s.jenjang='$jenjang'";
      $where_jenjang_data = " AND rekap_lembaga.jenjang='$jenjang'";
    }

    if ($kecamatan == 'all') {
      $where_kecamatan_profil = "";
      $where_kecamatan_data = "";
    } else {
      $where_kecamatan_profil = " AND s.kec_id='$kecamatan'";
      $where_kecamatan_data = " AND rekap_lembaga.kec_id='$kecamatan'";
    }

    if ($kelurahan == 'all') {
      $where_kelurahan_profil = "";
      $where_kelurahan_data = "";
    } else {
      $where_kelurahan_profil = " AND s.desa='$kelurahan'";
      $where_kelurahan_data = " AND rekap_lembaga.kel_id='$kelurahan'";
    }

    $cutoff = DB::connection('pgsql')->table('cutoff_rekap.rekap_lembaga')->whereRaw("tgl_simpan='" . $date . "' $where_kecamatan_data $where_kelurahan_data $where_jenjang_data")->count('jenjang');

    if (($cutoff == 0 || $load != '') && ($date->startOfDay() == now()->startOfDay())) {
      $lembaga = DB::connection('pgsql_paud')->table('sekolah as s')
        ->join('master_user as mu', function ($query) {
          $query->on('s.npsn', '=', 'mu.npsn')->whereRaw('mu.user_enable is true');
        })
        ->leftJoin('kecamatan as kec', function ($query) {
          $query->on('kec.kecamatan_kode', '=', 's.kec_id');
        })
        ->leftJoin('kelurahan as kel', function ($query) {
          $query->on('kel.kelurahan_kode', '=', 's.desa');
        })
        ->whereRaw("s.nama not ilike '%terbuka%' $where_kecamatan_profil $where_kelurahan_profil $where_jenjang_profil")
        ->selectRaw("
				s.nama as nama_lembaga,
				s.npsn,
				s.status,
				CONCAT(s.alamat) as alamat,
				s.desa as kel_id,
				s.kec_id,
				kel.kelurahan_dispenduk,
				kec.kecamatan_dispenduk,
				s.akreditasi,
				s.jenjang,
				s.status,
				concat('" . date('Y-m-d') . "') as tgl_simpan
				")->groupBy('s.nama', 's.npsn', 's.alamat', 's.status', 's.jenjang', 'kec.kecamatan_dispenduk', 'kel.kelurahan_dispenduk')->orderBy('status')->get();

      $temp = [];
      foreach ($lembaga as $key => $rr) {
        foreach ($rr as $kolom => $r) {
          $temp[$key][$kolom] = $r;
        }
      }

      $arr_temp = array_chunk($temp, 2);

      DB::connection('pgsql')->table('cutoff_rekap.rekap_lembaga')->whereRaw("tgl_simpan='" . date('Y-m-d') . "' $where_kecamatan_data $where_kelurahan_data $where_jenjang_data")->delete();
      for ($i = 0; $i < count($arr_temp); $i++) {
        $simpan = DB::connection('pgsql')->table('cutoff_rekap.rekap_lembaga')->insert(collect($arr_temp[$i])->toArray());
      }
    }

    $lembaga = DB::connection('pgsql')->table('cutoff_rekap.rekap_lembaga')
      ->selectRaw("
			EXTRACT(YEAR FROM rekap_lembaga.tgl_simpan) as tahun,
			EXTRACT(MONTH FROM rekap_lembaga.tgl_simpan) as bulan,
			rekap_lembaga.npsn,
			rekap_lembaga.nama_lembaga,
			rekap_lembaga.alamat,
			rekap_lembaga.jenjang,
			rekap_lembaga.status,
			rekap_lembaga.akreditasi,
			rekap_lembaga.kel_id,
			rekap_lembaga.kec_id,
			rekap_lembaga.kelurahan_dispenduk,
			rekap_lembaga.kecamatan_dispenduk,
			monitoring_lembaga.status_ppdb,
			monitoring_lembaga.status_mpls,
			monitoring_lembaga.status_manasik_haji_cilik,
			monitoring_lembaga.status_educity_tour,
			monitoring_lembaga.status_talenta_seni,
			monitoring_lembaga.status_edugreen_living,
			monitoring_lembaga.status_gen_mitigasi_bencana,
			monitoring_lembaga.status_sekolahku_tempat_menyenangkan,
			monitoring_lembaga.file_ppdb,
			monitoring_lembaga.file_mpls,
			monitoring_lembaga.file_manasik_haji_cilik,
			monitoring_lembaga.file_educity_tour,
			monitoring_lembaga.file_talenta_seni,
			monitoring_lembaga.file_edugreen_living,
			monitoring_lembaga.file_gen_mitigasi_bencana,
			monitoring_lembaga.file_sekolahku_tempat_menyenangkan
			")
      ->leftJoin('monitoring_lembaga', function ($q) {
        $q->on('monitoring_lembaga.npsn', 'rekap_lembaga.npsn');
        $q->on('monitoring_lembaga.tahun', DB::raw('EXTRACT(YEAR FROM rekap_lembaga.tgl_simpan)'));
        $q->on('monitoring_lembaga.bulan', DB::raw('EXTRACT(MONTH FROM rekap_lembaga.tgl_simpan)'));
      })
      ->whereRaw("rekap_lembaga.tgl_simpan='" . $date . "' $where_kecamatan_data $where_kelurahan_data $where_jenjang_data")
      ->orderByRaw("CASE rekap_lembaga.jenjang WHEN 'TPA' THEN 1 WHEN 'KB' THEN 2 WHEN 'SPS' THEN 3 ELSE 4 END,status")
      ->get();


    $data = [
      'judul' => 'Data Lembaga PAUD',
      'lembaga' => $lembaga,
      'jenis' => $request->jenis,
    ];

    $content = view('rekap_satuan.data_lembaga', $data)->render();
    return ['content' => $content];
  }

  function get_lembaga_sd(Request $request)
  {
    if (request('bulan') == now()->month || empty(request('bulan'))) {
      $date = now();
    } else {
      $lastDate = DB::connection('pgsql')
        ->table('cutoff_rekap.rekap_lembaga_sd')
        ->whereYear('tgl_simpan', now()->year)
        ->whereMonth('tgl_simpan', request('bulan'))
        ->select(DB::raw('MAX(tgl_simpan)'))
        ->first();
      if ($lastDate) {
        $date = now()->parse($lastDate->max);
      } else {
        // tanggal null, mencegah error ketika $date dipakaikan fungsi carbon now biasa tanpa memberikan default tanggal valid
        $date = now()->parse('1111-11-11');
      }
    }
    $jenjang = $request->jenjang;
    $kecamatan = $request->kecamatan;
    $kelurahan = $request->kelurahan;
    $load = $request->load;
    $tahun_anggaran = 2023;

    $where_jenjang_profil = " AND s.jenjang in ('SD','MI')";
    $where_jenjang_data = " AND rekap_lembaga_sd.jenjang in ('SD','MI')";

    if ($kecamatan == 'all') {
      $where_kecamatan_profil = "";
      $where_kecamatan_data = "";
    } else {
      $where_kecamatan_profil = " AND s.kec_id='$kecamatan'";
      $where_kecamatan_data = " AND rekap_lembaga_sd.kec_id='$kecamatan'";
    }

    if ($kelurahan == 'all') {
      $where_kelurahan_profil = "";
      $where_kelurahan_data = "";
    } else {
      $where_kelurahan_profil = " AND s.desa='$kelurahan'";
      $where_kelurahan_data = " AND rekap_lembaga_sd.kel_id='$kelurahan'";
    }

    $cutoff = DB::connection('pgsql')->table('cutoff_rekap.rekap_lembaga_sd')->whereRaw("tgl_simpan='" . $date . "' $where_kecamatan_data $where_kelurahan_data $where_jenjang_data")->count('jenjang');

    if (($cutoff == 0 || $load != '') && ($date->startOfDay() == now()->startOfDay())) {
      $lembaga = DB::connection('db_sd')->table('sekolah as s')
        ->join('master_user as mu', function ($query) {
          $query->on('s.npsn', '=', 'mu.npsn')->whereRaw('mu.user_enable is true');
        })
        ->leftJoin('kecamatan as kec', function ($query) {
          $query->on('kec.kecamatan_kode', '=', 's.kec_id');
        })
        ->leftJoin('kelurahan as kel', function ($query) {
          $query->on('kel.kelurahan_kode', '=', 's.desa');
        })
        ->leftJoin('siswa', function ($query) {
          $query->on('siswa.npsn', 's.npsn')
            ->whereRaw('alumni is not true')
            ->wherein('status_siswa', ['Aktif'])
            ->whereIn('kelas', ['1', '2']);
        })
        ->leftJoin('history.riwayat_vaksinasi', 'riwayat_vaksinasi.siswa_id', 'siswa.siswa_id')
        ->whereRaw("s.nama not ilike '%terbuka%' $where_kecamatan_profil $where_kelurahan_profil $where_jenjang_profil")
        ->selectRaw("
				s.nama as nama_lembaga,
				s.npsn,
				s.status,
				CONCAT(s.alamat) as alamat,
				s.desa as kel_id,
				s.kec_id,
				kel.kelurahan_dispenduk,
				kec.kecamatan_dispenduk,
				s.akreditasi,
				s.jenjang,
				s.status,
				concat('" . date('Y-m-d') . "') as tgl_simpan,
				COUNT(DISTINCT(CASE WHEN siswa.status_siswa = 'Aktif' and siswa.alumni is not true then siswa.siswa_id ELSE NULL END)) as jumlah_siswa,
				COUNT(DISTINCT(CASE WHEN siswa.status_siswa = 'Aktif' and siswa.alumni is not true and riwayat_vaksinasi.jenis = 'Polio' AND riwayat_vaksinasi.dosis = 1 AND riwayat_vaksinasi.status = 'Sudah' THEN siswa.siswa_id ELSE NULL END)) AS jumlah_sudah_polio_dosis_1,
				COUNT(DISTINCT(CASE WHEN siswa.status_siswa = 'Aktif' and siswa.alumni is not true and riwayat_vaksinasi.jenis = 'Polio' AND riwayat_vaksinasi.dosis = 2 AND riwayat_vaksinasi.status = 'Sudah' THEN siswa.siswa_id ELSE NULL END)) AS jumlah_sudah_polio_dosis_2
				")->groupBy('s.nama', 's.npsn', 's.alamat', 's.status', 's.jenjang', 'kec.kecamatan_dispenduk', 'kel.kelurahan_dispenduk')->orderBy('status')->get();
      //disiniii
      $temp = [];
      foreach ($lembaga as $key => $rr) {
        foreach ($rr as $kolom => $r) {
          $temp[$key][$kolom] = $r;
        }
      }

      $arr_temp = array_chunk($temp, 2);

      DB::connection('pgsql')->table('cutoff_rekap.rekap_lembaga_sd')->whereRaw("tgl_simpan='" . date('Y-m-d') . "' $where_kecamatan_data $where_kelurahan_data $where_jenjang_data")->delete();
      for ($i = 0; $i < count($arr_temp); $i++) {
        $simpan = DB::connection('pgsql')->table('cutoff_rekap.rekap_lembaga_sd')->insert(collect($arr_temp[$i])->toArray());
      }
    }

    $lembaga = DB::connection('pgsql')->table('cutoff_rekap.rekap_lembaga_sd')
      ->selectRaw("
			EXTRACT(YEAR FROM rekap_lembaga_sd.tgl_simpan) as tahun,
			EXTRACT(MONTH FROM rekap_lembaga_sd.tgl_simpan) as bulan,
			rekap_lembaga_sd.npsn,
			rekap_lembaga_sd.nama_lembaga,
			rekap_lembaga_sd.alamat,
			rekap_lembaga_sd.jenjang,
			rekap_lembaga_sd.status,
			rekap_lembaga_sd.akreditasi,
			rekap_lembaga_sd.kel_id,
			rekap_lembaga_sd.kec_id,
			rekap_lembaga_sd.kelurahan_dispenduk,
			rekap_lembaga_sd.kecamatan_dispenduk,
			rekap_lembaga_sd.jumlah_siswa,
			rekap_lembaga_sd.jumlah_sudah_polio_dosis_1,
			rekap_lembaga_sd.jumlah_sudah_polio_dosis_2,
			monitoring_lembaga.status_ppdb,
			monitoring_lembaga.status_mpls,
			monitoring_lembaga.status_manasik_haji_cilik,
			monitoring_lembaga.status_educity_tour,
			monitoring_lembaga.status_talenta_seni,
			monitoring_lembaga.status_edugreen_living,
			monitoring_lembaga.status_gen_mitigasi_bencana,
			monitoring_lembaga.status_sekolahku_tempat_menyenangkan,
			monitoring_lembaga.file_ppdb,
			monitoring_lembaga.file_mpls,
			monitoring_lembaga.file_manasik_haji_cilik,
			monitoring_lembaga.file_educity_tour,
			monitoring_lembaga.file_talenta_seni,
			monitoring_lembaga.file_edugreen_living,
			monitoring_lembaga.file_gen_mitigasi_bencana,
			monitoring_lembaga.file_sekolahku_tempat_menyenangkan
			")
      ->leftJoin('monitoring_lembaga', function ($q) {
        $q->on('monitoring_lembaga.npsn', 'rekap_lembaga_sd.npsn');
        $q->on('monitoring_lembaga.tahun', DB::raw('EXTRACT(YEAR FROM rekap_lembaga_sd.tgl_simpan)'));
        $q->on('monitoring_lembaga.bulan', DB::raw('EXTRACT(MONTH FROM rekap_lembaga_sd.tgl_simpan)'));
      })
      ->whereRaw("rekap_lembaga_sd.tgl_simpan='" . $date . "' $where_kecamatan_data $where_kelurahan_data $where_jenjang_data")
      ->orderByRaw("CASE rekap_lembaga_sd.jenjang WHEN 'TPA' THEN 1 WHEN 'KB' THEN 2 WHEN 'SPS' THEN 3 ELSE 4 END,status")
      ->get();

    $data = [
      'judul' => 'Data Lembaga SD/MI',
      'lembaga' => $lembaga,
      'jenis' => $request->jenis,
    ];

    $content = view('rekap_satuan.data_lembaga', $data)->render();
    return ['content' => $content];
  }

  function get_monitoring_lembaga(Request $request)
  {
    if (request('bulan') == now()->month || empty(request('bulan'))) {
      $date = now();
    } else {
      $lastDate = DB::connection('pgsql')
        ->table('cutoff_rekap.rekap_lembaga')
        ->whereYear('tgl_simpan', now()->year)
        ->whereMonth('tgl_simpan', request('bulan'))
        ->select(DB::raw('MAX(tgl_simpan)'))
        ->first();
      if ($lastDate) {
        $date = now()->parse($lastDate->max);
      } else {
        // tanggal null, mencegah error ketika $date dipakaikan fungsi carbon now biasa tanpa memberikan default tanggal valid
        $date = now()->parse('1111-11-11');
      }
    }
    $jenjang = $request->jenjang;
    $kecamatan = $request->kecamatan;
    $kelurahan = $request->kelurahan;

    if ($jenjang == 'all') {
      $where_jenjang_data = " AND rekap_lembaga.jenjang in ('TPA','SPS','TK','KB','RA')";
    } else {
      $where_jenjang_data = " AND rekap_lembaga.jenjang='$jenjang'";
    }

    if ($kecamatan == 'all') {
      $where_kecamatan_data = "";
    } else {
      $where_kecamatan_data = " AND rekap_lembaga.kec_id='$kecamatan'";
    }

    if ($kelurahan == 'all') {
      $where_kelurahan_data = "";
    } else {
      $where_kelurahan_data = " AND rekap_lembaga.kel_id='$kelurahan'";
    }

    $lembaga = $lembaga = DB::connection('pgsql')->table('cutoff_rekap.rekap_lembaga')
      ->leftJoin('cutoff_rekap.rekap_siswa as educitytour', function ($q) use ($date) {
        $q->on('rekap_lembaga.npsn', 'educitytour.npsn');
        $q->where('educitytour.tgl_simpan', $date);
        $q->where('educitytour.jumlah_educitytour', '>', 0);
        $q->whereNotNull('educitytour.jumlah_educitytour');
      })
      ->leftJoin('cutoff_rekap.rekap_siswa as talenta', function ($q) use ($date) {
        $q->on('rekap_lembaga.npsn', 'talenta.npsn');
        $q->where('talenta.tgl_simpan', $date);
        $q->where('talenta.jumlah_talenta', '>', 0);
        $q->whereNotNull('talenta.jumlah_talenta');
      })
      ->selectRaw("
			rekap_lembaga.jenjang,
			rekap_lembaga.status,
			COUNT(DISTINCT(rekap_lembaga.npsn)) as jumlah_lembaga,
			COUNT(DISTINCT(CASE WHEN rekap_lembaga.jenjang not in ('TPA','KB') THEN rekap_lembaga.npsn ELSE null END)) as ppdb_belum,
			0 as ppdb_sudah,
			0 as ppdb_monev,
			COUNT(DISTINCT(CASE WHEN rekap_lembaga.jenjang not in ('TPA','KB') THEN rekap_lembaga.npsn ELSE null END)) as mpls_belum,
			0 as mpls_sudah,
			0 as mpls_monev,
			COUNT(DISTINCT(rekap_lembaga.npsn)) as satu_belum,
			0 as satu_sudah,
			0 as satu_monev,
			COUNT(DISTINCT(CASE WHEN educitytour.npsn IS NULL THEN rekap_lembaga.npsn ELSE NULL END)) as dua_belum,
			COUNT(DISTINCT(CASE WHEN educitytour.npsn IS NOT NULL THEN rekap_lembaga.npsn ELSE NULL END)) as dua_sudah,
			COUNT(DISTINCT(CASE WHEN educitytour.npsn IS NOT NULL THEN rekap_lembaga.npsn ELSE NULL END)) as dua_monev,
			COUNT(DISTINCT(CASE WHEN talenta.npsn IS NULL THEN rekap_lembaga.npsn ELSE NULL END)) as tiga_belum,
			COUNT(DISTINCT(CASE WHEN talenta.npsn IS NOT NULL THEN rekap_lembaga.npsn ELSE NULL END)) as tiga_sudah,
			COUNT(DISTINCT(CASE WHEN talenta.npsn IS NOT NULL THEN rekap_lembaga.npsn ELSE NULL END)) as tiga_monev,
			COUNT(DISTINCT(rekap_lembaga.npsn)) as empat_belum,
			0 as empat_sudah,
			0 as empat_monev,
			COUNT(DISTINCT(rekap_lembaga.npsn)) as lima_belum,
			0 as lima_sudah,
			0 as lima_monev,
			COUNT(DISTINCT(rekap_lembaga.npsn)) as enam_belum,
			0 as enam_sudah,
			0 as enam_monev
			")
      ->whereRaw("rekap_lembaga.tgl_simpan='" . $date . "' $where_kecamatan_data $where_kelurahan_data $where_jenjang_data")
      ->groupByRaw("rekap_lembaga.jenjang,rekap_lembaga.status")
      ->orderByRaw("CASE rekap_lembaga.jenjang WHEN 'TPA' THEN 1 WHEN 'KB' THEN 2 WHEN 'SPS' THEN 3 ELSE 4 END,status")
      ->get();

    $data = [
      'judul' => 'Data Monitoring Lembaga PAUD',
      'lembaga' => $lembaga,
    ];

    $content = view('rekap_satuan.data_monitoring_lembaga', $data)->render();
    return ['content' => $content];
  }

  function get_monitoring_lembaga_sd(Request $request)
  {
    if (request('bulan') == now()->month || empty(request('bulan'))) {
      $date = now();
    } else {
      $lastDate = DB::connection('pgsql')
        ->table('cutoff_rekap.rekap_lembaga_sd')
        ->whereYear('tgl_simpan', now()->year)
        ->whereMonth('tgl_simpan', request('bulan'))
        ->select(DB::raw('MAX(tgl_simpan)'))
        ->first();
      if ($lastDate) {
        $date = now()->parse($lastDate->max);
      } else {
        // tanggal null, mencegah error ketika $date dipakaikan fungsi carbon now biasa tanpa memberikan default tanggal valid
        $date = now()->parse('1111-11-11');
      }
    }
    $jenjang = $request->jenjang;
    $kecamatan = $request->kecamatan;
    $kelurahan = $request->kelurahan;

    $where_jenjang_profil = " AND s.jenjang in ('SD','MI')";
    $where_jenjang_data = " AND rekap_lembaga_sd.jenjang in ('SD','MI')";

    if ($kecamatan == 'all') {
      $where_kecamatan_profil = "";
      $where_kecamatan_data = "";
    } else {
      $where_kecamatan_profil = " AND s.kec_id='$kecamatan'";
      $where_kecamatan_data = " AND rekap_lembaga_sd.kec_id='$kecamatan'";
    }

    if ($kelurahan == 'all') {
      $where_kelurahan_profil = "";
      $where_kelurahan_data = "";
    } else {
      $where_kelurahan_profil = " AND s.desa='$kelurahan'";
      $where_kelurahan_data = " AND rekap_lembaga_sd.kel_id='$kelurahan'";
    }

    $lembaga = $lembaga = DB::connection('pgsql')->table('cutoff_rekap.rekap_lembaga_sd')
      ->leftJoin('cutoff_rekap.rekap_siswa_sd as talenta', function ($q) use ($date) {
        $q->on('rekap_lembaga_sd.npsn', 'talenta.npsn');
        $q->where('talenta.tgl_simpan', $date);
        $q->where('talenta.jumlah_talenta', '>', 0);
        $q->whereNotNull('talenta.jumlah_talenta');
      })
      ->selectRaw("
			rekap_lembaga_sd.jenjang,
			rekap_lembaga_sd.status,
			COUNT(DISTINCT(rekap_lembaga_sd.npsn)) as jumlah_lembaga,
			COUNT(DISTINCT(CASE WHEN rekap_lembaga_sd.jenjang not in ('TPA','KB') THEN rekap_lembaga_sd.npsn ELSE null END)) as ppdb_belum,
			0 as ppdb_sudah,
			0 as ppdb_monev,
			COUNT(DISTINCT(CASE WHEN rekap_lembaga_sd.jenjang not in ('TPA','KB') THEN rekap_lembaga_sd.npsn ELSE null END)) as mpls_belum,
			0 as mpls_sudah,
			0 as mpls_monev,
			COUNT(DISTINCT(rekap_lembaga_sd.npsn)) as satu_belum,
			0 as satu_sudah,
			0 as satu_monev,
			COUNT(DISTINCT(rekap_lembaga_sd.npsn)) as dua_belum,
			0 as dua_sudah,
			0 as dua_monev,
			COUNT(DISTINCT(CASE WHEN talenta.npsn IS NULL THEN rekap_lembaga_sd.npsn ELSE NULL END)) as tiga_belum,
			COUNT(DISTINCT(CASE WHEN talenta.npsn IS NOT NULL THEN rekap_lembaga_sd.npsn ELSE NULL END)) as tiga_sudah,
			COUNT(DISTINCT(CASE WHEN talenta.npsn IS NOT NULL THEN rekap_lembaga_sd.npsn ELSE NULL END)) as tiga_monev,
			COUNT(DISTINCT(rekap_lembaga_sd.npsn)) as empat_belum,
			0 as empat_sudah,
			0 as empat_monev,
			COUNT(DISTINCT(rekap_lembaga_sd.npsn)) as lima_belum,
			0 as lima_sudah,
			0 as lima_monev,
			COUNT(DISTINCT(rekap_lembaga_sd.npsn)) as enam_belum,
			0 as enam_sudah,
			0 as enam_monev
			")
      ->whereRaw("rekap_lembaga_sd.tgl_simpan='" . $date . "' $where_kecamatan_data $where_kelurahan_data $where_jenjang_data")
      ->groupByRaw("rekap_lembaga_sd.jenjang,rekap_lembaga_sd.status")
      ->orderByRaw("CASE rekap_lembaga_sd.jenjang WHEN 'TPA' THEN 1 WHEN 'KB' THEN 2 WHEN 'SPS' THEN 3 ELSE 4 END,status")
      ->get();

    $data = [
      'judul' => 'Data Monitoring Lembaga SD/MI',
      'lembaga' => $lembaga,
    ];

    $content = view('rekap_satuan.data_monitoring_lembaga', $data)->render();
    return ['content' => $content];
  }

  function detail_gtk_old(Request $request)
  {
    $kecamatan = $request->kecamatan;
    $kelurahan = $request->kelurahan;
    $param = $request->param;
    $jenjang = $param[0];
    $status = $param[1];
    $kolom = $param[2];
    $judul = $param[3];

    $tahun_anggaran = 2023;

    if ($kecamatan == 'all') {
      $where_kecamatan_profil = "";
    } else {
      $where_kecamatan_profil = " AND s.kec_id='$kecamatan'";
    }

    if ($kelurahan == 'all') {
      $where_kelurahan_profil = "";
    } else {
      $where_kelurahan_profil = " AND s.desa='$kelurahan'";
    }

    $tunjangan = DB::connection('db_siagus')->table('tjn_pengajuan_pegawai as tjn')
      ->join('skpbm_jadwal_pegawai as sjp', function ($query) {
        $query->on('tjn.skpbm_jadwal_pegawai_id', '=', 'sjp.id_skpbm_jadwal_pegawai');
      })
      ->join('tjn_transaksi as tt', 'tt.id_tjn_transaksi', '=', 'tjn.tjn_transaksi_id')
      ->whereRaw(
        "tt.tahun = ? and ( (jenis_tunjangan= 'Tpg' and tjn.status = 'Diusulkan') or
			(jenis_tunjangan = 'Tamsil' and tjn.status = 'Diusulkan') or
			(jenis_tunjangan = 'Tod' and tjn.status = 'Diterima'))  and sjp.is_aktif is true",
        [$tahun_anggaran]
      )->select('nik', 'jenis_tunjangan')->distinct()->get();

    $tfg = "";
    $tpp = "";
    $tamsil = "";
    $tpg = "";
    $tod = "";
    $pelatihan = "";
    $sinau_bareng = "";
    $guru_penggerak = "";
    $jaspel = "";

    if ($kolom == 'jumlah_tfg') {
      $tfg = DB::connection('db_siagus')->table('tfg_pengajuan_pegawai as tfg')
        ->join('skpbm_jadwal_pegawai as sjp', function ($query) {
          $query->on('tfg.skpbm_jadwal_pegawai_id', '=', 'sjp.id_skpbm_jadwal_pegawai');
        })
        ->join('tfg_transaksi as tt', 'tt.id_tfg_transaksi', '=', 'tfg.tfg_transaksi_id')
        ->whereRaw("setujui = ? and tt.tahun = ?", ['Ya', $tahun_anggaran])->distinct()->pluck('nik')->toArray();

      $tfg = implode("','", $tfg);
    } else if ($kolom == 'jumlah_tamsil') {
      $tamsil = $tunjangan->where('jenis_tunjangan', 'Tamsil')->pluck('nik')->unique()->toArray();
      $tamsil = implode("','", $tamsil);
    } else if ($kolom == 'jumlah_tpg') {
      $tpg = $tunjangan->where('jenis_tunjangan', 'Tpg')->pluck('nik')->unique()->toArray();
      $tpg = implode("','", $tpg);
    } else if ($kolom == 'jumlah_tog') {
      $tod = $tunjangan->where('jenis_tunjangan', 'Tod')->pluck('nik')->unique()->toArray();
      $tod = implode("','", $tod);
    } else if ($kolom == 'jumlah_pelatihan') {
      $pelatihan = DB::connection('db_siagus')
        ->table('rw_pelatihan')->selectRaw("nik")
        ->distinct()->pluck('nik')->toArray();
      $pelatihan = implode("','", $pelatihan);
    } else if ($kolom == 'jumlah_sinau_bareng') {
      $sinau_bareng = DB::connection('db_sinau_bareng')
        ->table('jadwal')->selectRaw("nik")
        ->whereRaw("EXTRACT(YEAR FROM tanggal)='" . $tahun_anggaran . "'")->distinct()->pluck('nik')->toArray();
      $sinau_bareng = implode("','", $sinau_bareng);
    } else if ($kolom == 'jumlah_penggerak') {
      $guru_penggerak = DB::connection('db_siagus')
        ->table('temporary.guru_penggerak')->join('skpbm_jadwal_pegawai', 'nopes', '=', 'no_ukg')
        ->selectRaw("nik")
        ->whereRaw('status_lulus = ?', ['10 - Lulus Diklat'])
        ->distinct()->pluck('nik')->toArray();
      $guru_penggerak = implode("','", $guru_penggerak);
    } else if ($kolom == 'jumlah_jaspel') {
      $jaspel = DB::connection('db_simpaud')
        ->table('jaspel.jaspel_pendidik as pendidik')
        ->join('jaspel.jaspel_usulan as usulan', 'usulan.id', 'pendidik.id_pengajuan')
        ->join('jaspel.jaspel_periode as periode', 'periode.id', 'usulan.periode_id')
        ->join('jaspel.jaspel_periode_bulan_tahun as periode_bulan_tahun', function ($q) {
          $q->on('periode_bulan_tahun.periode_id', 'periode.id');
          $q->where('periode_bulan_tahun.tahun', now()->year);
          $q->where('periode_bulan_tahun.bulan', request('bulan') ? request('bulan') : now()->month);
        })
        ->where('pendidik.status', 'Disetujui')
        ->distinct()->pluck('nik')->toArray();
      $jaspel = implode("','", $jaspel);
    } else {
    }

    $lembaga = DB::connection('pgsql_paud')->table('sekolah as s')
      ->join('master_user as mu', function ($query) {
        $query->on('s.npsn', '=', 'mu.npsn')->whereRaw('mu.user_enable is true');
      })
      ->join('pegawai as p', function ($query) {
        $query->on('p.npsn', 's.npsn')
          ->whereRaw('keterangan = ? and induk is true', ['Aktif']);
        // ->wherein('jabatan',['1','2','3','11','12','19','33','34','35','36','37','38','39','40','41','42','43','46','47','48','50','51','53']);
      })
      ->leftJoin('kecamatan as kec', function ($query) {
        $query->on('kec.kecamatan_kode', '=', 's.kec_id');
      })
      ->leftJoin('kelurahan as kel', function ($query) {
        $query->on('kel.kelurahan_kode', '=', 's.desa');
      })
      ->leftJoin('prestasi_guru as pg', function ($query) {
        $query->on('pg.peg_id', '=', 'p.peg_id')
          ->whereRaw('pg.peg_id = p.peg_id');
      })
      ->whereRaw("s.nama not ilike '%terbuka%' and s.jenjang='$jenjang' AND s.status='$status' $where_kecamatan_profil $where_kelurahan_profil")
      ->selectRaw("kec.kecamatan_dispenduk,kel.kelurahan_dispenduk,s.npsn,s.nama as nama_sekolah,p.peg_id,p.no_ktp as nik,p.nama,p.status_guru,p.jenis_pegawai")
      ->when($kolom == 'jumlah_s1', function ($j) {
        return $j->whereRaw("p.pendidikan_terakhir = 9");
      })
      ->when($kolom == 'jumlah_s2', function ($j) {
        return $j->whereRaw("p.pendidikan_terakhir = 10");
      })
      ->when($kolom == 'jumlah_s3', function ($j) {
        return $j->whereRaw("p.pendidikan_terakhir = 11");
      })
      ->when($kolom == 'jumlah_belum_nuptk', function ($j) {
        return $j->whereRaw("(nuptk is null or trim(nuptk) = '' or nuptk = '-' )");
      })
      ->when($kolom == 'jumlah_pelatihan', function ($j) use ($pelatihan) {
        return $j->whereRaw("p.no_ktp in ('" . $pelatihan . "')");
      })
      ->when($kolom == 'jumlah_jaspel', function ($j) use ($jaspel) {
        return $j->whereRaw("p.no_ktp in ('" . $jaspel . "')");
      })
      ->when($kolom == 'jumlah_tpg', function ($j) use ($tpg) {
        return $j->whereRaw("p.no_ktp in ('" . $tpg . "')");
      })
      ->when($kolom == 'jumlah_tamsil', function ($j) use ($tamsil) {
        return $j->whereRaw("p.no_ktp in ('" . $tamsil . "')");
      })
      ->when($kolom == 'jumlah_tukin', function ($j) {
        return $j->whereRaw("s.status = 'N' and p.status_guru in ('PNS','PPPK') and p.jenis_pegawai in ('KS','Guru')");
      })
      ->when($kolom == 'jumlah_sertifikasi', function ($j) {
        return $j->whereRaw("p.sertifikasi_bidang_studi is not null AND p.sertifikasi_bidang_studi!='' AND p.sertifikasi_bidang_studi!='-'");
      })
      ->groupByRaw("kec.kecamatan_dispenduk,kel.kelurahan_dispenduk,s.npsn,s.nama,p.peg_id,p.no_ktp,p.nama,p.status_guru,p.jenis_pegawai")
      ->orderByRaw("kec.kecamatan_dispenduk,kel.kelurahan_dispenduk,s.nama,p.nama,p.status_guru,p.jenis_pegawai")->get();

    $judul .= ' Jenjang ' . $jenjang . ', Status ' . $status;

    $data = [
      'judul' => $judul,
      'lembaga' => $lembaga,
    ];

    $content = view('rekap_satuan.detail_gtk', $data)->render();

    return ['content' => $content];
  }

  function detail_gtk(Request $request, $jenjang_awal = null)
  {
    $bulan = $request->bulan; //"11"
    $jenis = $request->jenis; //"siswa"
    $jenjang = $request->jenjang; //"all"
    $kecamatan = $request->kecamatan; //"all"
    $kelurahan = $request->kelurahan; //"all"
    $date = date('Y-m-d');
    $tahun_anggaran = 2024;
    $param = $request->param; //["70001017", "jumlah_siswa"]
    $npsn = $param[0];
    $kategori = $param[1];

    $lembagas = DB::connection('pgsql_paud')
      ->table('public.sekolah as se')
      ->selectRaw("
			s.nama,s.no_ktp,s.status_guru,
			s.nuptk,
			CASE WHEN s.pendidikan_terakhir='9' THEN 'S1' ELSE CASE WHEN s.pendidikan_terakhir='10' THEN 'S2' ELSE CASE WHEN s.pendidikan_terakhir='11' THEN 'S3' ELSE 'Lainnya' END END END as pendidikan
			")
      ->join('public.master_user as u', 'u.username', '=', 'se.npsn')
      ->join('public.kecamatan as kec', 'kec.kecamatan_kode', '=', 'se.kec_id')
      ->join('public.kelurahan as kel', 'kel.kelurahan_kode', '=', 'se.desa')
      ->join('public.pegawai as s', 's.npsn', '=', 'se.npsn')
      ->when($kategori == 'jumlah_nonpns', function ($q) {
        return $q->whereRaw("s.status_guru NOT IN ('PNS','PPPK')");
      })
      ->when($kategori == 'jumlah_s1', function ($q) {
        return $q->whereRaw("s.pendidikan_terakhir='9'");
      })
      ->when($kategori == 'jumlah_s2', function ($q) {
        return $q->whereRaw("s.pendidikan_terakhir='10'");
      })
      ->when($kategori == 'jumlah_s3', function ($q) {
        return $q->whereRaw("s.pendidikan_terakhir='11'");
      })
      ->when($kategori == 'jumlah_belum_nuptk', function ($q) {
        return $q->whereRaw("(s.nuptk is null OR s.nuptk='')");
      })
      ->when($kategori == 'jumlah_pelatihan', function ($q) {
        $pelatihan = DB::connection('db_siagus')
          ->table('rw_pelatihan')->selectRaw("nik")
          ->distinct()->pluck('nik')->toArray();
        $pelatihan = implode("','", $pelatihan);

        return $q->whereRaw("s.no_ktp IN ('$pelatihan')");
      })
      ->when($kategori == 'jumlah_guru_penggerak', function ($q) {
        $guru_penggerak = DB::connection('db_siagus')
          ->table('temporary.guru_penggerak')->join('skpbm_jadwal_pegawai', 'nopes', '=', 'no_ukg')
          ->selectRaw("nik")
          ->whereRaw('status_lulus = ?', ['10 - Lulus Diklat'])
          ->distinct()->pluck('nik')->toArray();
        $guru_penggerak = implode("','", $guru_penggerak);

        return $q->whereRaw("s.no_ktp IN ('$guru_penggerak')");
      })
      ->when($kategori == 'jumlah_tfg', function ($q) use ($tahun_anggaran) {
        $tfg = DB::connection('db_siagus')->table('tfg_pengajuan_pegawai as tfg')
          ->join('skpbm_jadwal_pegawai as sjp', function ($query) {
            $query->on('tfg.skpbm_jadwal_pegawai_id', '=', 'sjp.id_skpbm_jadwal_pegawai');
          })
          ->join('tfg_transaksi as tt', 'tt.id_tfg_transaksi', '=', 'tfg.tfg_transaksi_id')
          ->whereRaw("setujui = ? and tt.tahun = ?", ['Ya', $tahun_anggaran])->distinct()->pluck('nik')->toArray();

        $tfg = implode("','", $tfg);

        return $q->whereRaw("s.no_ktp IN ('$tfg')");
      })
      ->when($kategori == 'jumlah_tpg', function ($q) use ($tahun_anggaran) {
        $tunjangan = DB::connection('db_siagus')->table('tjn_pengajuan_pegawai as tjn')
          ->join('skpbm_jadwal_pegawai as sjp', function ($query) {
            $query->on('tjn.skpbm_jadwal_pegawai_id', '=', 'sjp.id_skpbm_jadwal_pegawai');
          })
          ->join('tjn_transaksi as tt', 'tt.id_tjn_transaksi', '=', 'tjn.tjn_transaksi_id')
          ->whereRaw(
            "tt.tahun = ? and (
				(jenis_tunjangan= 'Tpg' and tjn.status = 'Diusulkan') or
				(jenis_tunjangan = 'Tamsil' and tjn.status = 'Diusulkan') or
				(jenis_tunjangan = 'Tod' and tjn.status = 'Diterima'))  and sjp.is_aktif is true",
            [$tahun_anggaran]
          )->select('nik', 'jenis_tunjangan')->distinct()->get();

        $tpg = $tunjangan->where('jenis_tunjangan', 'Tpg')->pluck('nik')->unique()->toArray();

        $tpg = implode("','", $tpg);

        return $q->whereRaw("s.no_ktp IN ('$tpg')");
      })
      ->when($kategori == 'jumlah_tamsil', function ($q) use ($tahun_anggaran) {
        $tunjangan = DB::connection('db_siagus')->table('tjn_pengajuan_pegawai as tjn')
          ->join('skpbm_jadwal_pegawai as sjp', function ($query) {
            $query->on('tjn.skpbm_jadwal_pegawai_id', '=', 'sjp.id_skpbm_jadwal_pegawai');
          })
          ->join('tjn_transaksi as tt', 'tt.id_tjn_transaksi', '=', 'tjn.tjn_transaksi_id')
          ->whereRaw(
            "tt.tahun = ? and (
				(jenis_tunjangan= 'Tpg' and tjn.status = 'Diusulkan') or
				(jenis_tunjangan = 'Tamsil' and tjn.status = 'Diusulkan') or
				(jenis_tunjangan = 'Tod' and tjn.status = 'Diterima'))  and sjp.is_aktif is true",
            [$tahun_anggaran]
          )->select('nik', 'jenis_tunjangan')->distinct()->get();

        $tamsil = $tunjangan->where('jenis_tunjangan', 'Tamsil')->pluck('nik')->unique()->toArray();

        $tamsil = implode("','", $tamsil);

        return $q->whereRaw("s.no_ktp IN ('$tamsil')");
      })
      ->when($kategori == 'jumlah_jaspel', function ($q) {
        $jaspel = DB::connection('db_simpaud')
          ->table('jaspel.jaspel_pendidik as pendidik')
          ->join('jaspel.jaspel_usulan as usulan', 'usulan.id', 'pendidik.id_pengajuan')
          ->join('jaspel.jaspel_periode as periode', 'periode.id', 'usulan.periode_id')
          ->join('jaspel.jaspel_periode_bulan_tahun as periode_bulan_tahun', function ($q) {
            $q->on('periode_bulan_tahun.periode_id', 'periode.id');
            $q->where('periode_bulan_tahun.tahun', now()->year);
            $q->where('periode_bulan_tahun.bulan', now()->month);
          })
          ->where('pendidik.status', 'Disetujui')
          ->distinct()->pluck('nik')->toArray();
        $jaspel = implode("','", $jaspel);

        return $q->whereRaw("s.no_ktp IN ('$jaspel')");
      })
      ->when($kategori == 'jumlah_sinaubareng', function ($q) use ($tahun_anggaran) {
        $sinau_bareng = DB::connection('db_sinau_bareng')
          ->table('jadwal')->selectRaw("nik")
          ->whereRaw("EXTRACT(YEAR FROM tanggal)='" . $tahun_anggaran . "'")->distinct()->pluck('nik')->toArray();
        $sinau_bareng = implode("','", $sinau_bareng);

        return $q->whereRaw("s.no_ktp IN ('$sinau_bareng')");
      })
      ->when($kategori == 'jumlah_pns', function ($q) {
        return $q->whereRaw("s.status_guru IN ('PNS')");
      })
      ->when($kategori == 'jumlah_pppk', function ($q) {
        return $q->whereRaw("s.status_guru IN ('PPPK')");
      })
      ->whereRaw("u.user_enable is true AND s.keterangan='Aktif' AND s.induk is true AND se.npsn='$npsn'")
      ->groupByRaw("s.nama,s.no_ktp,s.status_guru,s.nuptk,s.pendidikan_terakhir")
      ->orderByRaw("s.nama")->get();

    $data = [
      'lembaga' => $lembagas,
      'judul' => strtoupper(str_replace('_', ' ', $kategori)),
    ];
    $content = view('rekap_satuan.detail_gtk', $data)->render();
    return ['content' => $content];
  }

  function detail_sarpras(Request $request)
  {
    $jenjang = $request->jenjang;
    $kecamatan = $request->kecamatan;
    $kelurahan = $request->kelurahan;
    $param = $request->param;
    $ruangan = $param[0];
    $kolom = $param[1];
    $judul = $param[2];
    $tahun_anggaran = 2023;

    if ($jenjang == 'all') {
      $where_jenjang_profil = " AND s.jenjang in ('TPA','SPS','TK','KB','RA')";
      $where_jenjang_data = " AND jenjang in ('TPA','SPS','TK','KB','RA')";
    } else {
      $where_jenjang_profil = " AND s.jenjang='$jenjang'";
      $where_jenjang_data = " AND jenjang='$jenjang'";
    }

    if ($kecamatan == 'all') {
      $where_kecamatan_profil = "";
      $where_kecamatan_data = "";
    } else {
      $where_kecamatan_profil = " AND s.kec_id='$kecamatan'";
      $where_kecamatan_data = " AND kec_id='$kecamatan'";
    }

    if ($kelurahan == 'all') {
      $where_kelurahan_profil = "";
      $where_kelurahan_data = "";
    } else {
      $where_kelurahan_profil = " AND s.desa='$kelurahan'";
      $where_kelurahan_data = " AND desa='$kelurahan'";
    }

    $tahun = date('Y');
    $bulan = date('m');
    if ($bulan < 7) {
      $tahun_cari = ($tahun - 1) . '/' . $tahun;
      $semester = 2;
      $antara = "'$tahun-01-01' AND '$tahun-06-30'";
    } else {
      $tahun_cari = $tahun . '/' . ($tahun + 1);
      $semester = 1;
      $antara = "'$tahun-07-01' AND '$tahun-12-31'";
    }
    $tahun_ajaran = DB::connection('db_sd')->table('tahun_ajaran')->where('nama_tahun_ajaran', $tahun_cari)->first();
    $id_tahun_ajaran = $tahun_ajaran->id_tahun_ajaran;
    $semester = 1;

    $sekolah = DB::connection('pgsql_paud')->table('sekolah as s')
      ->join('master_user as u', 'u.username', 's.npsn')
      ->join('kecamatan as kec', 'kec.kecamatan_kode', 's.kec_id')
      ->join('kelurahan as kel', 'kel.kelurahan_kode', 's.desa')
      ->selectRaw("s.npsn,s.status,s.jenjang,s.kec_id,s.desa,kec.kecamatan_dispenduk,kel.kelurahan_dispenduk")
      ->whereRaw("u.user_enable is true $where_kecamatan_profil $where_kelurahan_profil $where_jenjang_profil")->get();
    $npsn = $sekolah->pluck('npsn')->toArray();

    $data = [];
    $data = DB::connection('db_simbas')->table('lokasi_ruang')->join('sekolah as s', function ($query) {
      $query->on('s.npsn', '=', 'lokasi_ruang.npsn');
    })
      ->whereraw("is_delete = ?", ['0'])
      ->whereIn('s.npsn', $npsn)
      ->whereIn('id_prasarana', [
        1,
        23,
        10,
        11,
        2,
        3,
        4,
        5,
        6,
        7,
        8,
        9,
        42,
        26,
        27,
        28,
        29,
        14,
        13,
        31,
        41,
        49,
        21,
        22,
        24,
        49
      ])
      ->selectRaw("
			s.jenjang,
			s.npsn,
			s.nama as nama_sekolah,
			s.status,
			s.kec_id as kec_id,
			case when id_prasarana = 1 then 'Ruang Kelas'
			when id_prasarana = 23 then 'Ruang Guru'
			when id_prasarana in (10,11) then 'Perpustakaan'
			when id_prasarana in (2,3,4,5,6,7,8,9,42) then 'Laboratorium'
			when id_prasarana in (26,27,28,29) then 'Kamar Mandi'
			when id_prasarana in (14) then 'Ruang UKS'
			when id_prasarana in (13) then 'Ruang Aula'
			when id_prasarana in (31) then 'Tempat Ibadah'
			when id_prasarana in (41,49) then 'Sarana Olahraga'
			when id_prasarana in (21) then 'Ruang BK'
			when id_prasarana in (22) then 'Ruang Kepala Sekolah / Lembaga'
			when id_prasarana in (24) then 'Ruang TU'
			when id_prasarana in (49) then 'Lapangan Upacara / Olahraga'
			end as jenis_ruangan,
			sum(case when kondisi = 'Baik' then 1 else 0 end) as jumlah_baik,
			sum(case when kondisi = 'Rusak Berat' then 1 else 0 end) as jumlah_buruk,
			sum(case when kondisi in ('Rusak Ringan','Rusak Sedang') then 1 else 0 end) as jumlah_sedang,
			count(id_prasarana) jumlah_ruang,
			CONCAT('" . date('Y-m-d') . "') as tgl_simpan
			")
      ->groupbyRaw("s.npsn, s.nama, s.jenjang, s.status, s.kec_id, jenis_ruangan")->get();

    $lembaga = $data->where('jenis_ruangan', $ruangan);
    foreach ($lembaga as $k => $l) {
      $data = $sekolah->where('npsn', $l->npsn)->first();
      $l->kecamatan_dispenduk = $data->kecamatan_dispenduk;
      $l->kelurahan_dispenduk = $data->kelurahan_dispenduk;
    }

    $judul .= ' (Ruangan ' . $ruangan . ')';

    $data = [
      'lembaga' => $lembaga,
      'judul' => $judul,
      'kolom' => $kolom,
    ];

    $content = view('rekap_satuan.detail_sarpras', $data)->render();

    return ['content' => $content];
  }

  function detail_monitoring(Request $request)
  {
    $param = $request->param;
    $npsn = $param[0];
    $nama_lembaga = $param[1];
    $jenis = $param[2];
    $jenjang = $param[3];
    $tahun = $param[4];
    $bulan = $param[5];
    $status = $param[6];
    switch ($jenis) {
      case 'ppdb':
        $judul = 'Monitoring PPDB ' . $nama_lembaga;
        break;
      case 'mpls':
        $judul = 'Monitoring MPLS ' . $nama_lembaga;
        break;
      case 'manasik_haji_cilik':
        $judul = 'Monitoring Manasik Haji Cilik ' . $nama_lembaga;
        break;
      case 'educity_tour':
        $judul = 'Monitoring Educity Tour ' . $nama_lembaga;
        break;
      case 'talenta_seni':
        $judul = 'Monitoring Talenta Siswa ' . $nama_lembaga;
        break;
      case 'edugreen_living':
        $judul = 'Monitoring Edugreen Living ' . $nama_lembaga;
        break;
      case 'gen_mitigasi_bencana':
        $judul = 'Monitoring Gen Mitigasi Bencana ' . $nama_lembaga;
        break;
      case 'sekolahku_tempat_menyenangkan':
        $judul = 'Monitoring Sekolahku Tempat Menyenangkan ' . $nama_lembaga;
        break;

      default:
        $judul = '';
        break;
    }

    $data = [
      'judul' => $judul,
      'jenis' => $jenis,
      'npsn' => $npsn,
      'tahun' => $tahun,
      'bulan' => $bulan,
      'status' => $status,
      'jenjang' => $jenjang
    ];

    $content = view('rekap_satuan.monitoring', $data)->render();

    return ['content' => $content];
  }

  public function fotoStore()
  {
    DB::beginTransaction();
    try {
      request()->validate([
        'status' => 'required',
        'jenis' => 'required',
        'npsn' => 'required',
        'tahun' => 'required',
        'bulan' => 'required',
        'status_sekolah' => 'required',
        'file' => 'mimetypes:image/jpeg,image/png,image/gif,image/bmp,image/webp,image/svg+xml,application/pdf',
      ]);

      $columnStatus = "status_" . request('jenis');
      $columnFile = "file_" . request('jenis');
      $columnTimestamp = "timestamp_" . request('jenis');
      $existed = DB::table('monitoring_lembaga')
        ->where('npsn', request('npsn'))
        ->where('tahun', request('tahun'))
        ->where('bulan', request('bulan'))
        ->whereNotNull($columnFile)
        ->first();

      if ($existed) {
        Storage::delete($existed->$columnFile);
      }

      if (request('file')) {
        $tujuan = 'monitoring_lembaga/';
        $filename = request('npsn') . '_tahun_' . request('tahun') . '_bulan_' . request('bulan') .  '_' . request('jenis') . '_' . now()->format('d-m-Y_H-i-s') . '.' . request()->file('file')->getClientOriginalExtension();
        $file = file_get_contents(request()->file('file'));
        Storage::put($tujuan . $filename, $file);
      }

      DB::table('monitoring_lembaga')
        ->where('npsn', request('npsn'))
        ->where('tahun', request('tahun'))
        ->where('bulan', request('bulan'))
        ->updateOrInsert(
          [
            'npsn' => request('npsn'),
            'tahun' => request('tahun'),
            'bulan' => request('bulan'),
          ],
          [
            'jenjang' => request('jenjang'),
            'status' => request('status_sekolah'),
            $columnStatus => request('status'),
            $columnFile => request('file') ? $tujuan . $filename : null,
            $columnTimestamp => now(),
          ]
        );

      DB::commit();
      return redirect()->route('bunda-rekap_profil-index');
    } catch (\Exception $e) {
      DB::rollBack();
      dd($e->getMessage());
      return redirect()->route('bunda-rekap_profil-index');
    }
  }
}
