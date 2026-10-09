<?php

namespace App\Http\Controllers\Bunda;

use App\Http\Controllers\Controller;
use App\Http\Libraries\GetData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VaksinasiController extends Controller
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

    return view('bunda.vaksinasi.index', compact('kecamatans'));
  }

  function get_data()
  {
    if (in_array(request('jenjang'), ['KB', 'TK', 'SPS', 'TPA', 'RA'])) {
      $connections = ['pgsql_paud'];
    } else if (in_array(request('jenjang'), ['SD', 'MI'])) {
      $connections = ['db_sd'];
    }
    $sekolahsAll = collect([]);
    foreach ($connections as $connection) {
      $sekolahs = DB::connection($connection)
        ->table('public.sekolah')
        ->join('public.siswa', function ($q) use ($connection) {
          $q->on('siswa.npsn', 'sekolah.npsn');
          $q->where('siswa.status_siswa', 'Aktif');
          $q->whereRaw('siswa.alumni is not true');
          $q->when($connection == 'db_sd', function ($q) {
            $q->where(function ($q) {
              $q->whereIn('siswa.kelas', ['1', '2']);
            });
          });
        })
        ->when($connection == 'pgsql_paud', function ($q) {
          $q->whereIn('sekolah.jenjang', ['KB', 'TK', 'SPS', 'TPA', 'RA']);
        })
        ->when($connection == 'db_sd', function ($q) {
          $q->whereIn('sekolah.jenjang', ['SD', 'MI']);
        })
        ->leftJoin('history.riwayat_vaksinasi', 'riwayat_vaksinasi.siswa_id', 'siswa.siswa_id')
        ->join('public.master_user', function ($q) {
          $q->on('master_user.npsn', 'sekolah.npsn');
          $q->whereRaw('master_user.user_enable is true');
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
          DB::raw("COUNT(DISTINCT(siswa.siswa_id)) as jumlah_siswa"),
          DB::raw("COUNT(DISTINCT(CASE WHEN riwayat_vaksinasi.jenis = 'Polio' AND riwayat_vaksinasi.dosis = 1 AND riwayat_vaksinasi.status = 'Sudah' THEN siswa.siswa_id ELSE NULL END)) AS sudah_polio_dosis_1"),
          DB::raw("COUNT(DISTINCT(CASE WHEN riwayat_vaksinasi.jenis = 'Polio' AND riwayat_vaksinasi.dosis = 2 AND riwayat_vaksinasi.status = 'Sudah' THEN siswa.siswa_id ELSE NULL END)) AS sudah_polio_dosis_2"),
        )
        ->groupBy(
          'sekolah.jenjang',
          'sekolah.npsn',
          'sekolah.nama',
          'sekolah.alamat',
          'kecamatan.kecamatan_dispenduk',
          'kelurahan.kelurahan_dispenduk',
        )
        ->get();
      $sekolahsAll = $sekolahsAll->merge($sekolahs);
    }
    $sekolahs = $sekolahsAll;

    return DataTables()->of($sekolahs)
      ->addIndexColumn()
      ->editColumn('jumlah_siswa', function ($sekolah) {
        $jumlah = $sekolah->jumlah_siswa;
        if ($jumlah != 0) {
          $temp = '<a href="javascript:void(0)" onclick="modal(`vaksinasi`,`' . $sekolah->jenjang . '`,`' . $sekolah->npsn . '`,``,``,``)">' . $jumlah . '</a>';
        } else {
          $temp = $jumlah;
        }
        return $temp;
      })
      ->editColumn('sudah_polio_dosis_1', function ($sekolah) {
        $jumlah = $sekolah->sudah_polio_dosis_1;
        if ($jumlah != 0) {
          $temp = '<a href="javascript:void(0)" onclick="modal(`vaksinasi`,`' . $sekolah->jenjang . '`,`' . $sekolah->npsn . '`,`Polio`,`Sudah`,`1`)">' . $jumlah . '</a>';
        } else {
          $temp = $jumlah;
        }
        return $temp;
      })
      ->addColumn('belum_polio_dosis_1', function ($sekolah) {
        $jumlah = $sekolah->jumlah_siswa - $sekolah->sudah_polio_dosis_1;
        if ($jumlah != 0) {
          $temp = '<a href="javascript:void(0)" onclick="modal(`vaksinasi`,`' . $sekolah->jenjang . '`,`' . $sekolah->npsn . '`,`Polio`,`Belum`,`1`)">' . $jumlah . '</a>';
        } else {
          $temp = $jumlah;
        }
        return $temp;
      })
      ->editColumn('sudah_polio_dosis_2', function ($sekolah) {
        $jumlah = $sekolah->sudah_polio_dosis_2;
        if ($jumlah != 0) {
          $temp = '<a href="javascript:void(0)" onclick="modal(`vaksinasi`,`' . $sekolah->jenjang . '`,`' . $sekolah->npsn . '`,`Polio`,`Sudah`,`2`)">' . $jumlah . '</a>';
        } else {
          $temp = $jumlah;
        }
        return $temp;
      })
      ->addColumn('belum_polio_dosis_2', function ($sekolah) {
        $jumlah = $sekolah->jumlah_siswa - $sekolah->sudah_polio_dosis_2;
        if ($jumlah != 0) {
          $temp = '<a href="javascript:void(0)" onclick="modal(`vaksinasi`,`' . $sekolah->jenjang . '`,`' . $sekolah->npsn . '`,`Polio`,`Belum`,`2`)">' . $jumlah . '</a>';
        } else {
          $temp = $jumlah;
        }
        return $temp;
      })
      ->rawColumns(['jumlah_siswa', 'sudah_polio_dosis_1', 'belum_polio_dosis_1', 'sudah_polio_dosis_2', 'belum_polio_dosis_2'])
      ->make(true);
  }

  function modal()
  {
    request()->validate([
      'modal' => 'required',
      'jenjang' => 'required',
      'npsn' => 'required'
    ]);

    $modal = request('modal');
    $jenjang = request('jenjang');
    $npsn = request('npsn');
    $jenis = request('jenis');
    $status = request('status');
    $dosis = request('dosis');

    $npsn = request('npsn');

    switch ($modal) {
      case 'vaksinasi':
        $content = view('bunda.vaksinasi.modal-vaksinasi', compact('modal', 'jenjang', 'npsn', 'jenis', 'status', 'dosis'))->render();
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
      'modal' => 'required',
      'npsn' => 'required'
    ]);

    $modal = request('modal');

    switch ($modal) {
      case 'vaksinasi':
        $return = $this->dataVaksinasi();
        break;
      default:
        return;
        break;
    }
    return $return;
  }

  function dataVaksinasi()
  {
    request()->validate([
      'npsn' => 'required',
      'jenjang' => 'required'
    ]);

    if (in_array(request('jenjang'), ['KB', 'TK', 'SPS', 'TPA', 'RA'])) {
      $connections = ['pgsql_paud'];
    } else if (in_array(request('jenjang'), ['SD', 'MI'])) {
      $connections = ['db_sd'];
    }
    $siswasAll = collect([]);
    foreach ($connections as $connection) {
      $siswas = DB::connection($connection)
        ->table('public.siswa')
        ->where('siswa.status_siswa', 'Aktif')
        ->whereRaw('siswa.alumni is not true')
        ->join('public.sekolah', function ($q) use ($connection) {
          $q->on('sekolah.npsn', 'siswa.npsn');
          $q->where('sekolah.npsn', request('npsn'));
          if ($connection == 'pgsql_paud') {
            $q->whereIn('sekolah.jenjang', ['KB', 'TK', 'SPS', 'TPA', 'RA']);
          } elseif ($connection == 'db_sd') {
            $q->whereIn('sekolah.jenjang', ['SD', 'MI']);
          } else {
            return;
          }
          $q->join('public.master_user', function ($q) {
            $q->on('master_user.npsn', 'sekolah.npsn');
            $q->whereRaw('master_user.user_enable is true');
          });
          $q->join('public.kecamatan', function ($q) {
            $q->on('kecamatan.kecamatan_kode', 'sekolah.kec_id');
            if (request()->user()->tingkat == 'kecamatan') {
              $q->where('kecamatan.kecamatan_id', request()->user()->id_kecamatan);
            }
            if (request('kec')) {
              $q->where('kecamatan.kecamatan_kode', request('kec'));
            }
          });
          $q->join('public.kelurahan', function ($q) {
            $q->on('kelurahan.kelurahan_kode', 'sekolah.desa');
            if (request()->user()->tingkat == 'kelurahan') {
              $q->where('kelurahan.kelurahan_id', request()->user()->id_kelurahan);
            }
            if (request('kel')) {
              $q->where('kelurahan.kelurahan_kode', request('kel'));
            }
          });
        })
        ->when(request('jenjang'), function ($q) {
          $q->where('sekolah.jenjang', request('jenjang'));
        })
        ->leftJoin('history.riwayat_vaksinasi', 'riwayat_vaksinasi.siswa_id', 'siswa.siswa_id')
        ->when(request('jenis'), function ($q) {
          $q->where(function ($q) {
            $q->where('riwayat_vaksinasi.jenis', request('jenis'));
            $q->orWhereRaw('riwayat_vaksinasi.id is null');
          });
        })
        ->when(request('status'), function ($q) {
          if (request('status') == 'Sudah') {
            $q->where('riwayat_vaksinasi.status', 'Sudah');
          } else {
            $q->where(function ($q) {
              $q->orwhere('riwayat_vaksinasi.status', '!=', 'Sudah');
              $q->orWhereRaw('riwayat_vaksinasi.id is null');
            });
          }
        })
        ->when(request('dosis'), function ($q) {
          $q->where(function ($q) {
            $q->where('riwayat_vaksinasi.dosis', request('dosis'));
            $q->orWhereRaw('riwayat_vaksinasi.id is null');
          });
        })
        ->when($connection == 'db_sd', function ($q) {
          $q->where(function ($q) {
            $q->whereIn('siswa.kelas', ['1', '2']);
          });
        })
        ->select(
          'sekolah.jenjang',
          'sekolah.npsn',
          'sekolah.nama as nama_sekolah',
          // 'siswa.siswa_id',
          // 'siswa.nik',
          'siswa.nama',
          'siswa.kelas',
          'siswa.tgl_lahir',
          DB::raw("EXTRACT(YEAR FROM AGE(NOW(), siswa.tgl_lahir)) AS umur"),
          DB::raw("STRING_AGG(CASE WHEN riwayat_vaksinasi.jenis = 'Polio' AND riwayat_vaksinasi.dosis = 1 THEN COALESCE(riwayat_vaksinasi.status::text, '') || '/*&@/' || COALESCE(riwayat_vaksinasi.keterangan::text,'') || '/*&@/' || COALESCE(riwayat_vaksinasi.tanggal::text,'') || '/*&@/' || COALESCE(riwayat_vaksinasi.tempat,'') ELSE NULL END, ',') AS polio_dosis_1"),
          DB::raw("STRING_AGG(CASE WHEN riwayat_vaksinasi.jenis = 'Polio' AND riwayat_vaksinasi.dosis = 2 THEN COALESCE(riwayat_vaksinasi.status::text, '') || '/*&@/' || COALESCE(riwayat_vaksinasi.keterangan::text,'') || '/*&@/' || COALESCE(riwayat_vaksinasi.tanggal::text,'') || '/*&@/' || COALESCE(riwayat_vaksinasi.tempat,'') ELSE NULL END, ',') AS polio_dosis_2"),
          // DB::raw("STRING_AGG(CASE WHEN riwayat_vaksinasi.jenis = 'Polio' AND riwayat_vaksinasi.dosis = 3 THEN COALESCE(riwayat_vaksinasi.status::text, '') || '/*&@/' || COALESCE(riwayat_vaksinasi.keterangan::text,'') || '/*&@/' || COALESCE(riwayat_vaksinasi.tanggal::text,'') || '/*&@/' || COALESCE(riwayat_vaksinasi.tempat,'') ELSE NULL END, ',') AS polio_dosis_3"),
        )
        ->groupBy(
          'sekolah.jenjang',
          'sekolah.npsn',
          'sekolah.nama',
          // 'siswa.siswa_id',
          'siswa.npsn',
          // 'siswa.nik',
          'siswa.nama',
          'siswa.kelas',
          'siswa.tgl_lahir',
          'umur'
        )
        ->get();
      $siswasAll = $siswasAll->merge($siswas);
    }
    $siswas = $siswasAll;

    return DataTables()->of($siswas)
      ->addIndexColumn()
      // ->editColumn('nik', function ($data) {
      //     return substr($data->nik, 0, -8) . 'xxxxxxxx';
      // })
      ->editColumn('polio_dosis_1', function ($data) {
        $temp = '';
        if ($data->polio_dosis_1 != null) {
          $array = explode("/*&@/", $data->polio_dosis_1);
          $temp .= $array[0]; //status
          $temp .= '<br>';
          if (!empty($array[1])) {
            $temp .= $array[1]; //keterangan
            $temp .= '<br>';
          }
          if (!empty($array[2])) {
            $temp .= now()->createFromFormat('Y-m-d', $array[2])->format('d-m-Y'); //tanggal
            $temp .= '<br>';
          }
          $temp .= $array[3]; //tempat
        } else {
          $temp = 'Belum';
        }
        return $temp;
      })
      ->editColumn('polio_dosis_2', function ($data) {
        $temp = '';
        if ($data->polio_dosis_2 != null) {
          $array = explode("/*&@/", $data->polio_dosis_2);
          $temp .= $array[0]; //status
          $temp .= '<br>';
          if (!empty($array[1])) {
            $temp .= $array[1]; //keterangan
            $temp .= '<br>';
          }
          if (!empty($array[2])) {
            $temp .= now()->createFromFormat('Y-m-d', $array[2])->format('d-m-Y'); //tanggal
            $temp .= '<br>';
          }
          $temp .= $array[3]; //tempat
        } else {
          $temp = 'Belum';
        }
        return $temp;
      })
      ->rawColumns(['polio_dosis_1', 'polio_dosis_2', 'action'])
      ->make(true);
  }
}
