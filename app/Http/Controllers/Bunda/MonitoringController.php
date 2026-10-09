<?php

namespace App\Http\Controllers\Bunda;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use stdClass;

class MonitoringController extends Controller
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
    return view('monitoring.main', $data);
  }

  function get_data(Request $request)
  {
    $jenis = $request->jenis;

    if ($jenis == 'lembaga') {
      $content = $this->get_lembaga($request);
    } else if ($jenis == 'lembaga_sd') {
      $content = $this->get_lembaga($request);
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
    }

    return $content;
  }

  function query_siswa($jenis)
  {
    if (in_array($jenis, ['lembaga', 'lembaga_sd'])) {
      if ($jenis == 'lembaga') {
        $connection = 'pgsql_paud';
      } else {
        $connection = 'db_sd';
      }
      $query = DB::connection($connection)->table('sekolah')
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
        ->leftJoin('siswa', function ($query) {
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
            $q->whereIn('sekolah.jenjang', ['TPA', 'SPS', 'TK', 'KB']);
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
    }
    return $query;
  }
}
