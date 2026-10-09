<?php

namespace App\Http\Controllers\Bunda;

use App\Http\Controllers\Controller;
use App\Http\Libraries\GetData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SekolahController extends Controller
{

  public function index()
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

    return view('bunda.sekolah.index', compact('kecamatans'));
  }

  function get_data(Request $request)
  {
    $sekolahs = DB::connection('pgsql_paud')
      ->table('public.sekolah')
      ->whereIn('sekolah.jenjang', ['KB', 'TK', 'SPS', 'TPA', 'RA'])
      ->join('public.master_user', function ($q) {
        $q->on('master_user.npsn', 'sekolah.npsn');
        $q->whereRaw('user_enable is true');
      })
      ->join('public.kecamatan', function ($q) {
        $q->on('kecamatan.kecamatan_kode', 'sekolah.kec_id');
        if (request()->user()->tingkat == 'kecamatan') {
          $q->where('kecamatan.kecamatan_id', request()->user()->id_kecamatan);
        }
        if (request('kec')) {
          $q->where('kecamatan.kecamatan_kode', request('kec'));
        }
      })
      ->join('public.kelurahan', function ($q) {
        $q->on('kelurahan.kelurahan_kode', 'sekolah.desa');
        if (request()->user()->tingkat == 'kelurahan') {
          $q->where('kelurahan.kelurahan_id', request()->user()->id_kelurahan);
        }
        if (request('kel')) {
          $q->where('kelurahan.kelurahan_kode', request('kel'));
        }
      })
      ->when(request('jenjang'), function ($q) {
        $q->where('sekolah.jenjang', request('jenjang'));
      })
      ->select(
        'sekolah.jenjang',
        'sekolah.npsn',
        'sekolah.nama',
        'sekolah.alamat',
        'kecamatan.kecamatan_dispenduk',
        'kelurahan.kelurahan_dispenduk',
      );

    $jumlah_pegawai = DB::connection('pgsql_paud')->table('public.pegawai')->selectRaw("npsn,COUNT(npsn) as jumlah")->whereIn('npsn', $sekolahs->pluck('npsn'))->whereRaw("keterangan='Aktif'")->groupBy('npsn')->get();
    $jumlah_siswa = DB::connection('pgsql_paud')->table('public.siswa')->selectRaw("siswa.npsn,COUNT(siswa.npsn) as jumlah")
      ->join('public.wali_murid as wm', function ($join) {
        return $join->on('wm.id_siswa', '=', 'siswa.id_siswa')->on('wm.npsn', '=', 'siswa.npsn');
      })->whereIn('siswa.npsn', $sekolahs->pluck('npsn'))->whereRaw("siswa.status_siswa='Aktif' AND siswa.alumni is not true")->groupBy('siswa.npsn')->get();

    if (isset($request->total) && $request->total == 'total') {
      return ['jumlah_lembaga' => $sekolahs->count(), 'jumlah_siswa' => $jumlah_siswa->sum('jumlah'), 'jumlah_pegawai' => $jumlah_pegawai->sum('jumlah')];
    } else {
      return DataTables()->of($sekolahs)
        ->addIndexColumn()
        ->addColumn('jumlah_pegawai', function ($sekolah) use ($jumlah_pegawai) {
          $jumlah = $jumlah_pegawai->where('npsn', $sekolah->npsn)->first();
          $jumlah = $jumlah ? $jumlah->jumlah : 0;
          return '<a href="javascript:void(0)" onclick="modal(`pegawai`,`' . $sekolah->npsn . '`)">' . $jumlah . '</a>';
        })
        ->addColumn('jumlah_siswa', function ($sekolah) use ($jumlah_siswa) {
          $jumlah = $jumlah_siswa->where('npsn', $sekolah->npsn)->first();
          $jumlah = $jumlah ? $jumlah->jumlah : 0;
          return '<a href="javascript:void(0)" onclick="modal(`siswa`,`' . $sekolah->npsn . '`)">' . $jumlah . '</a>';
        })
        ->rawColumns(['jumlah_pegawai', 'jumlah_siswa'])
        ->make(true);
    }
  }

  function modal()
  {
    request()->validate([
      'modal' => 'required',
      'npsn' => 'required'
    ]);

    $npsn = request('npsn');

    switch (request('modal')) {
      case 'pegawai':
        $content = view('bunda.sekolah.modal-pegawai', compact('npsn'))->render();
        break;
      case 'siswa':
        $content = view('bunda.sekolah.modal-siswa', compact('npsn'))->render();
        break;
      default:
        return;
        break;
    }

    return ['content' => $content];
  }

  function get_data_modal()
  {
    request()->validate([
      'jenis' => 'required',
      'npsn' => 'required'
    ]);

    switch (request('jenis')) {
      case 'pegawai':
        $return = $this->dataPegawai();
        break;
      case 'siswa':
        $return = $this->dataSiswa();
        break;
      default:
        return;
        break;
    }
    return $return;
  }

  function dataPegawai()
  {
    $pegawais = DB::connection('pgsql_paud')
      ->table('public.pegawai')
      ->where('sekolah.npsn', request('npsn'))
      ->where('keterangan', 'Aktif')
      ->leftJoin('jabatan_ptk', 'jabatan_ptk.jabatan_ptk_id', DB::raw("cast(pegawai.jabatan AS INTEGER)"))
      ->join('public.sekolah', 'sekolah.npsn', 'pegawai.npsn')
      ->whereIn('sekolah.jenjang', ['KB', 'TK', 'SPS', 'TPA', 'RA'])
      ->join('public.master_user', function ($q) {
        $q->on('master_user.npsn', 'sekolah.npsn');
        $q->whereRaw('user_enable is true');
      })
      ->join('public.kecamatan', function ($q) {
        $q->on('kecamatan.kecamatan_kode', 'sekolah.kec_id');
        if (request()->user()->tingkat == 'kecamatan') {
          $q->where('kecamatan.kecamatan_id', request()->user()->id_kecamatan);
        }
      })
      ->join('public.kelurahan', function ($q) {
        $q->on('kelurahan.kelurahan_kode', 'sekolah.desa');
        if (request()->user()->tingkat == 'kelurahan') {
          $q->where('kelurahan.kelurahan_id', request()->user()->id_kelurahan);
        }
      })
      ->select(
        'sekolah.jenjang',
        'sekolah.npsn',
        'sekolah.nama as nama_sekolah',
        'pegawai.no_ktp',
        'pegawai.nama',
        'jabatan_ptk.nama as jabatan',
        'pegawai.tgl_lahir'
      );

    return DataTables()->of($pegawais)
      // ->editColumn('no_ktp', function ($data) {
      //     return substr($data->no_ktp, 0, -8) . 'xxxxxxxx';
      // })
      ->addIndexColumn()
      ->make(true);
  }

  function dataSiswa()
  {
    $siswas = DB::connection('pgsql_paud')
      ->table('public.siswa')
      ->where('sekolah.npsn', request('npsn'))
      ->where('siswa.status_siswa', 'Aktif')
      ->whereRaw('siswa.alumni is not true')
      ->join('public.sekolah', 'sekolah.npsn', 'siswa.npsn')
      ->whereIn('sekolah.jenjang', ['KB', 'TK', 'SPS', 'TPA', 'RA'])
      ->join('public.master_user', function ($q) {
        $q->on('master_user.npsn', 'sekolah.npsn');
        $q->whereRaw('master_user.user_enable is true');
      })
      ->join('public.kecamatan', function ($q) {
        $q->on('kecamatan.kecamatan_kode', 'sekolah.kec_id');
        if (request()->user()->tingkat == 'kecamatan') {
          $q->where('kecamatan.kecamatan_id', request()->user()->id_kecamatan);
        }
      })
      ->join('public.kelurahan', function ($q) {
        $q->on('kelurahan.kelurahan_kode', 'sekolah.desa');
        if (request()->user()->tingkat == 'kelurahan') {
          $q->where('kelurahan.kelurahan_id', request()->user()->id_kelurahan);
        }
      })
      ->leftjoin('history.history_tb_bb_siswa', function ($q) {
        $q->on('siswa.siswa_id', 'history_tb_bb_siswa.siswa_id');
        $q->where('history_tb_bb_siswa.tahun', now()->year);
        $q->where('history_tb_bb_siswa.bulan', now()->month);
      })
      ->leftjoin('history.kartu_kembang_anak', function ($q) {
        $q->on('siswa.siswa_id', 'kartu_kembang_anak.siswa_id');
        $q->where('kartu_kembang_anak.tahun', now()->year);
        $q->where('kartu_kembang_anak.bulan', now()->month);
      })
      ->select(
        'sekolah.jenjang',
        'sekolah.npsn',
        'sekolah.nama as nama_sekolah',
        'siswa.nik',
        'siswa.nama',
        'siswa.tgl_lahir',
        DB::raw("EXTRACT(YEAR FROM AGE(NOW(), siswa.tgl_lahir)) AS umur"),
        'history_tb_bb_siswa.tb',
        'history_tb_bb_siswa.bb',
        'history_tb_bb_siswa.status_gizi',
        'kartu_kembang_anak.kesimpulan_kka'
      );

    return DataTables()->of($siswas)
      // ->editColumn('nik', function ($data) {
      //     return substr($data->nik, 0, -8) . 'xxxxxxxx';
      // })
      ->addIndexColumn()
      ->make(true);
  }
}
