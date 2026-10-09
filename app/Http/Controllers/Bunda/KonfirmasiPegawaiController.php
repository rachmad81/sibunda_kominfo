<?php

namespace App\Http\Controllers\Bunda;

use App\Http\Controllers\Controller;
use App\Http\Libraries\GetData;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KonfirmasiPegawaiController extends Controller
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

    return view('bunda.konfirmasi-pegawai.index', compact('kecamatans'));
  }

  function get_data()
  {
    $sekolahs = DB::connection('pgsql_paud')
      ->table('public.sekolah')
      ->whereIn('sekolah.jenjang', ['SPS'])
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
        // 'sekolah.jenjang',
        'sekolah.npsn',
        // 'sekolah.nama',
        // 'kecamatan.kecamatan_dispenduk',
        // 'kelurahan.kelurahan_dispenduk',
      )
      ->get();

    $datas = DB::connection('db_siagus')
      ->table('pg_transaksi_gtk')
      ->join('pg_jenis', 'id_pg_jenis', '=', 'pg_jenis_id')
      ->where('pg_transaksi_gtk.status_pengajuan', 'Pengajuan Bunda') //Pengajuan Bunda harusnya
      ->whereIn('pg_transaksi_gtk.npsn_tujuan', $sekolahs->pluck('npsn'))
      ->select(
        'pg_transaksi_gtk.id_pg_transaksi_gtk',
        'pg_transaksi_gtk.jenjang_tujuan',
        'pg_transaksi_gtk.npsn_tujuan',
        'pg_transaksi_gtk.lembaga_tujuan',
        // 'pg_transaksi_gtk.nik',
        'pg_transaksi_gtk.nama_pegawai',
      );

    return DataTables()->of($datas)
      ->addIndexColumn()
      ->addColumn('aksi', function ($data) {
        return '<a href="javascript:void(0)" class="btn btn-sm btn-primary" onclick="modal(`' . $data->id_pg_transaksi_gtk . '`)"> Konfirmasi </a>';
      })
      // ->editColumn('nik', function ($data) {
      //     return substr($data->nik, 0, -8) . 'xxxxxxxx';
      // })
      ->rawColumns(['aksi'])
      ->make(true);
  }

  function modal()
  {
    request()->validate([
      'id' => 'required',
    ]);

    $sekolahs = DB::connection('pgsql_paud')
      ->table('public.sekolah')
      ->whereIn('sekolah.jenjang', ['SPS'])
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
        // 'sekolah.jenjang',
        'sekolah.npsn',
        // 'sekolah.nama',
        // 'kecamatan.kecamatan_dispenduk',
        // 'kelurahan.kelurahan_dispenduk',
      )
      ->get();

    $data = DB::connection('db_siagus')
      ->table('pg_transaksi_gtk')
      ->join('pg_jenis', 'id_pg_jenis', '=', 'pg_jenis_id')
      ->where('pg_transaksi_gtk.status_pengajuan', 'Pengajuan Bunda') //Pengajuan Bunda harusnya
      ->where('id_pg_transaksi_gtk', request('id'))
      ->whereIn('pg_transaksi_gtk.npsn_tujuan', $sekolahs->pluck('npsn'))
      // ->select(
      //     'pg_transaksi_gtk.id_pg_transaksi_gtk',
      //     'pg_transaksi_gtk.jenjang_tujuan',
      //     'pg_transaksi_gtk.npsn_tujuan',
      //     'pg_transaksi_gtk.lembaga_tujuan',
      //     'pg_transaksi_gtk.nik',
      //     'pg_transaksi_gtk.nama_pegawai',
      // )
      ->first();

    if (!$data) {
      return false;
    }

    $lampiran = DB::connection('db_siagus')
      ->table('pg_berkas')
      ->leftJoin('pg_transaksi_berkas', function ($query) {
        $query->on('pg_transaksi_berkas.pg_berkas_id', '=', 'pg_berkas.id_pg_berkas')
          ->where('pg_transaksi_gtk_id', '=', request('id'));
      })
      // ->whereIn('pg_berkas.nama_jenis', ['Surat Keterangan Menerima ', 'SK GTT'])
      ->whereRaw('pg_jenis_id = ? and jenis_berkas = ?', array($data->pg_jenis_id, $data->status_kepegawaian))
      ->get();

    $content = view('bunda.konfirmasi-pegawai.modal-konfirmasi', compact('data', 'lampiran'))->render();

    return ['content' => $content];
  }

  function konfirmasi()
  {
    DB::connection('db_siagus')->beginTransaction();
    try {
      request()->validate([
        'id' => 'required',
      ]);

      // Untuk Pengecekan apakah Bunda yang sedang login memiliiki wewenang untuk verifikasi
      $sekolahs = DB::connection('pgsql_paud')
        ->table('public.sekolah')
        ->whereIn('sekolah.jenjang', ['SPS'])
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
          'sekolah.npsn',
        )
        ->get();

      $pengajuan = DB::connection('db_siagus')
        ->table('pg_transaksi_gtk')
        ->join('pg_jenis', 'id_pg_jenis', '=', 'pg_jenis_id')
        ->where('pg_transaksi_gtk.status_pengajuan', 'Pengajuan Bunda') //Pengajuan Bunda harusnya
        ->where('id_pg_transaksi_gtk', request('id'))
        ->whereIn('pg_transaksi_gtk.npsn_tujuan', $sekolahs->pluck('npsn')) // Pengecekan wewenang Bunda
        ->first();

      if (!$pengajuan) {
        return false;
      }

      $lampirans = DB::connection('db_siagus')
        ->table('pg_berkas')
        ->leftJoin('pg_transaksi_berkas', function ($query) use ($pengajuan) {
          $query->on('pg_transaksi_berkas.pg_berkas_id', '=', 'pg_berkas.id_pg_berkas')
            ->where('pg_transaksi_gtk_id', '=', $pengajuan->id_pg_transaksi_gtk);
        })
        // ->whereIn('pg_berkas.nama_jenis', ['Surat Keterangan Menerima ', 'SK GTT'])
        ->whereRaw('pg_jenis_id = ? and jenis_berkas = ?', array($pengajuan->pg_jenis_id, $pengajuan->status_kepegawaian))
        ->get();

      if (request('kesesuaian') == "true") {
        DB::connection('db_siagus')
          ->table('pg_transaksi_gtk')
          ->where('id_pg_transaksi_gtk', $pengajuan->id_pg_transaksi_gtk)
          ->update([
            'status_pengajuan' => 'Diajukan',
            'id_bunda' => request()->user()->id
          ]);
      } else {
        DB::connection('db_siagus')
          ->table('pg_transaksi_gtk')
          ->where('id_pg_transaksi_gtk', $pengajuan->id_pg_transaksi_gtk)
          ->update([
            'status_pengajuan' => 'Ditolak',
            'catatan_verifikator' => request('catatan'),
            'id_bunda' => request()->user()->id
          ]);
        foreach ($lampirans as $lampiran) {
          $kesesuaian = request('kesesuaian_' . $lampiran->id_pg_transaksi_berkas) == "true" ? "Iya" : "Tidak";
          $catatan = request('catatan_' . $lampiran->id_pg_transaksi_berkas);
          if (empty($kesesuaian)) {
            throw new Exception("Data verifikasi tidak lengkap");
          }
          DB::connection('db_siagus')
            ->table('pg_transaksi_berkas')
            ->where('pg_transaksi_gtk_id', $pengajuan->id_pg_transaksi_gtk)
            ->where('id_pg_transaksi_berkas', $lampiran->id_pg_transaksi_berkas)
            ->update([
              'kesesuaian' => $kesesuaian,
              'catatan' => $catatan
            ]);
        }
      }
      DB::connection('db_siagus')->commit();
      return redirect()->route('bunda-konfirmasi-pegawai-index');
    } catch (\Exception $e) {
      DB::connection('db_siagus')->rollBack();
      return redirect()->route('bunda-konfirmasi-pegawai-index');
    }
  }

  // function get_data_modal()
  // {
  //     request()->validate([
  //         'jenis' => 'required',
  //         'npsn' => 'required'
  //     ]);

  //     switch (request('jenis')) {
  //         case 'pegawai':
  //             $return = $this->dataPegawai();
  //             break;
  //         case 'siswa':
  //             $return = $this->dataSiswa();
  //             break;
  //         default:
  //             return;
  //             break;
  //     }
  //     return $return;
  // }

  // function dataPegawai()
  // {
  //     $pegawais = DB::connection('pgsql_paud')
  //         ->table('public.pegawai')
  //         ->where('sekolah.npsn', request('npsn'))
  //         ->where('keterangan', 'Aktif')
  //         ->leftJoin('jabatan_ptk', 'jabatan_ptk.jabatan_ptk_id', DB::raw("cast(pegawai.jabatan AS INTEGER)"))
  //         ->join('public.sekolah', 'sekolah.npsn', 'pegawai.npsn')
  //         ->whereIn('sekolah.jenjang', ['KB', 'TK', 'SPS', 'TPA'])
  //         ->join('public.master_user', function ($q) {
  //             $q->on('master_user.npsn', 'sekolah.npsn');
  //             $q->whereRaw('user_enable is true');
  //         })
  //         ->join('public.kecamatan', function ($q) {
  //             $q->on('kecamatan.kecamatan_kode', 'sekolah.kec_id');
  //             if (request()->user()->tingkat == 'kecamatan') {
  //                 $q->where('kecamatan.kecamatan_id', request()->user()->id_kecamatan);
  //             }
  //         })
  //         ->join('public.kelurahan', function ($q) {
  //             $q->on('kelurahan.kelurahan_kode', 'sekolah.desa');
  //             if (request()->user()->tingkat == 'kelurahan') {
  //                 $q->where('kelurahan.kelurahan_id', request()->user()->id_kelurahan);
  //             }
  //         })
  //         ->select(
  //             'sekolah.jenjang',
  //             'sekolah.npsn',
  //             'sekolah.nama as nama_sekolah',
  //             'pegawai.no_ktp',
  //             'pegawai.nama',
  //             'jabatan_ptk.nama as jabatan',
  //             'pegawai.tgl_lahir'
  //         );

  //     return DataTables()->of($pegawais)
  //         ->editColumn('no_ktp', function ($data) {
  //             return substr($data->no_ktp, 0, -8) . 'xxxxxxxx';
  //         })
  //         ->addIndexColumn()
  //         ->make(true);
  // }

  // function dataSiswa()
  // {
  //     $siswas = DB::connection('pgsql_paud')
  //         ->table('public.siswa')
  //         ->where('sekolah.npsn', request('npsn'))
  //         ->where('siswa.status_siswa', 'Aktif')
  //         ->whereRaw('siswa.alumni is not true')
  //         ->join('public.sekolah', 'sekolah.npsn', 'siswa.npsn')
  //         ->whereIn('sekolah.jenjang', ['KB', 'TK', 'SPS', 'TPA'])
  //         ->join('public.master_user', function ($q) {
  //             $q->on('master_user.npsn', 'sekolah.npsn');
  //             $q->whereRaw('master_user.user_enable is true');
  //         })
  //         ->join('public.kecamatan', function ($q) {
  //             $q->on('kecamatan.kecamatan_kode', 'sekolah.kec_id');
  //             if (request()->user()->tingkat == 'kecamatan') {
  //                 $q->where('kecamatan.kecamatan_id', request()->user()->id_kecamatan);
  //             }
  //         })
  //         ->join('public.kelurahan', function ($q) {
  //             $q->on('kelurahan.kelurahan_kode', 'sekolah.desa');
  //             if (request()->user()->tingkat == 'kelurahan') {
  //                 $q->where('kelurahan.kelurahan_id', request()->user()->id_kelurahan);
  //             }
  //         })
  //         ->select(
  //             'sekolah.jenjang',
  //             'sekolah.npsn',
  //             'sekolah.nama as nama_sekolah',
  //             'siswa.nik',
  //             'siswa.nama',
  //             'siswa.tgl_lahir'
  //         );

  //     return DataTables()->of($siswas)
  //         ->editColumn('nik', function ($data) {
  //             return substr($data->nik, 0, -8) . 'xxxxxxxx';
  //         })
  //         ->addIndexColumn()
  //         ->make(true);
  // }
}
