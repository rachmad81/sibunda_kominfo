<?php

namespace App\Http\Controllers\Rekap;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Kegiatan;
use App\Models\Program;

use Auth, Storage, DB, Datatables;

class KegiatanController extends Controller
{
  function main()
  {
    $tahun = DB::table('public.program')->select('tahun')->groupBy('tahun')->orderBy('tahun', 'DESC')->get();
    $tingkat = DB::table('public.program')->select('tingkat')->groupBy('tingkat')->orderBy('tingkat', 'DESC')->get();
    $bidang = DB::table('public.program')->select('bidang')->groupBy('bidang')->orderBy('bidang', 'DESC')->get();
    $kecamatan = DB::connection('pgsql_paud')->table('public.kecamatan')->orderBy('kecamatan_dispenduk', 'ASC')->get();
    $opd = DB::table('public.users')->whereRaw("role='opd'")->orderBy('nama', 'ASC')->get();

    $data = [
      'tahun' => $tahun,
      'tingkat' => $tingkat,
      'bidang' => $bidang,
      'kecamatan' => $kecamatan,
      'opd' => $opd,
    ];

    return view('rekap.kegiatan.main', $data);
  }

  function data(Request $request)
  {
    // return $request->all();
    $tahun = $request->tahun;
    $tingkat = $request->tingkat;
    $bidang = $request->bidang;
    $opd = $request->opd;
    $kecamatan = $request->kecamatan;
    $kelurahan = $request->kelurahan;

    if ($tahun == '') {
      $program = [];
    } else {
      $program = DB::table('public.kegiatan as k')
        ->join('public.program as p', 'p.id_program', 'k.program_id')
        ->join('public.users as u', 'u.username', 'p.updated_by')
        ->selectRaw("
          p.nama_kegiatan as nama_program,
          k.nama_kegiatan,
          p.bidang,
          p.tingkat,
          k.tempat_kegiatan,
          k.tgl_kegiatan,
          k.waktu_kegiatan,
          k.bentuk_keg,
          k.materi,
          k.narsum,
          k.sumber_dana,
          k.foto,
          k.url_video,
          k.sasaran_kegiatan,
          k.tujuan_kegiatan,
          k.hasil_kegiatan,
          k.target_out_kegiatan,
          k.file_pendukung,

          p.waktu as waktu_program,
          p.sasaran as sasaran_program,
          p.tujuan as tujuan_program,
          p.tahun as tahun_program,
          p.jumlah_sasaran as jumlah_sasaran_program,

          u.nama
        ")
        ->when($bidang != 'all', function ($w) use ($bidang) {
          return $w->where('p.bidang', $bidang);
        })
        ->when($tingkat, function ($w) use ($tingkat, $opd, $kecamatan, $kelurahan) {
          if ($tingkat == 'opd') {
            if ($opd != 'all') {
              return $w->where('p.updated_by', $opd);
            } else {
              return $w->where('u.role', 'opd');
            }
          } else if (in_array($tingkat, ['k3_paud', 'paud_sd'])) {
            return $w->where('u.role', $tingkat);
          } else if (in_array($tingkat, ['kecamatan', 'kelurahan'])) {
            // array_push($where, "u.role='bunda'");
            if ($tingkat == 'kecamatan') {
              if ($kecamatan != 'all') {
                return $w->where('u.id_kecamatan', $kecamatan);
              }
            } else if ($tingkat == 'kelurahan') {
              if ($kelurahan != 'all') {
                return $w->where('u.id_kelurahan', $kelurahan);
              } else {
                if ($kecamatan != 'all') {
                  $kec = DB::connection('pgsql_paud')->table('public.kecamatan')->where('kecamatan_id', $kecamatan)->first()->kecamatan_kode;
                  $kel = DB::connection('pgsql_paud')->table('public.kelurahan')->where('kecamatan_kode', $kec)->get();

                  return $w->whereIn('u.id_kelurahan', collect($kel)->pluck('kelurahan_id')->toArray());
                }
              }
            }
          } else if ($tingkat == 'mitra') {
            return $w->where('u.role', 'mitra');
          } else {
            if ($tingkat != 'all') {
              return $w->where('p.tingkat', $tingkat);
            }
          }
        })
        ->where('p.tahun', $tahun)
        ->whereNull('u.deleted_at')
        ->orderByRaw("k.tgl_kegiatan DESC");
    }

    return DataTables::of($program)
      ->addIndexColumn()
      ->addColumn('kak', function ($i) {
        $kak = json_decode($i->file_pendukung, true) ?? [];
        return isset($kak['kak']) ? $kak['kak'] : '';
      })
      ->addColumn('notulen', function ($i) {
        $kak = json_decode($i->file_pendukung, true) ?? [];
        return isset($kak['notulen']) ? $kak['notulen'] : '';
      })
      ->addColumn('absensi', function ($i) {
        $kak = json_decode($i->file_pendukung, true) ?? [];
        return isset($kak['absensi']) ? $kak['absensi'] : '';
      })
      ->addColumn('foto_geo', function ($i) {
        $kak = json_decode($i->file_pendukung, true) ?? [];
        return isset($kak['foto_geo']) ? $kak['foto_geo'] : '';
      })
      ->addColumn('undangan', function ($i) {
        $kak = json_decode($i->file_pendukung, true) ?? [];
        return isset($kak['undangan']) ? $kak['undangan'] : '';
      })
      ->rawColumns([])
      ->make(true);
  }

  function export(Request $request)
  {
    $bidang = $request->bidang; //"all"
    $kecamatan = $request->kecamatan; //"all"
    $kelurahan = $request->kelurahan; //"all"
    $opd = $request->opd; //"all"
    $tahun = $request->tahun; //null
    $tingkat = $request->tingkat; //"all"

    $kegiatan = DB::table('public.kegiatan as k')
      ->join('public.program as p', 'p.id_program', 'k.program_id')
      ->join('public.users as u', 'u.username', 'p.updated_by')
      ->selectRaw("
        p.nama_kegiatan as nama_program,
        k.nama_kegiatan,
        k.materi,
        k.narsum,
        k.sumber_dana,
        k.tempat_kegiatan,
        k.waktu_kegiatan,
        k.tgl_kegiatan,
        k.sasaran_kegiatan,
        k.tujuan_kegiatan,
        k.target_out_kegiatan,
        k.hasil_kegiatan,
        k.file_pendukung,
        k.foto,k.url_video
      ")
      // ->where('p.tahun', $tahun)
      ->whereIn('k.id_kegiatan', ['1980', '414'])
      ->whereNull('u.deleted_at')
      ->orderByRaw("k.tgl_kegiatan DESC")
      ->get();

    $kegiatan = collect($kegiatan)->map(function ($m) {
      $file_pendukung = json_decode($m->file_pendukung, true) ?? [];
      return collect($m)->merge($file_pendukung);
    });

    return $kegiatan;
  }

  function dataOld(Request $request)
  {
    // return $request->all();
    $tahun = $request->tahun;
    $tingkat = $request->tingkat;
    $bidang = $request->bidang;
    $opd = $request->opd;
    $kecamatan = $request->kecamatan;
    $kelurahan = $request->kelurahan;

    if ($tahun == '') {
      $program = [];
    } else {
      $where = [];
      $str_where = '';

      array_push($where, "p.tahun='$tahun'");
      if ($bidang != 'all') {
        array_push($where, "p.bidang='$bidang'");
      }

      if ($tingkat == 'opd') {
        array_push($where, "u.role='opd'");
        if ($opd != 'all') {
          array_push($where, "p.updated_by='$opd'");
        }
      } elseif (in_array($tingkat, ['k3_paud', 'paud_sd'])) {
        array_push($where, "u.role='$tingkat'");
      } else {
        if ($tingkat != 'all') {
          array_push($where, "p.tingkat='$tingkat'");
        }
        // array_push($where, "u.role='bunda'");
        if ($tingkat == 'kecamatan') {
          if ($kecamatan != 'all') {
            array_push($where, "u.id_kecamatan='$kecamatan'");
          }
        } else if ($tingkat == 'kelurahan') {
          if ($kelurahan != 'all') {
            array_push($where, "u.id_kelurahan='$kelurahan'");
          } else {
            if ($kecamatan != 'all') {
              $kec = DB::connection('pgsql_paud')->table('public.kecamatan')->where('kecamatan_id', $kecamatan)->first()->kecamatan_kode;
              $kel = DB::connection('pgsql_paud')->table('public.kelurahan')->where('kecamatan_kode', $kec)->get();

              $kel_id = [];
              $str_kel_id = '';
              if ($kel->count() != 0) {
                foreach ($kel as $ke) {
                  array_push($kel_id, "'" . $ke->kelurahan_id . "'");
                }
              }

              $str_kel_id = join(',', $kel_id);
              if ($str_kel_id != '') {
                array_push($where, "u.id_kelurahan IN ($str_kel_id)");
              }
            }
          }
        }
      }

      $str_where = join(" AND ", $where);

      if ($str_where == '') {
        $program = DB::connection('pgsql')->table('public.kegiatan as k')
          ->leftjoin('public.program as p', 'k.program_id', 'p.id_program')
          ->leftjoin('public.users as u', 'u.username', 'p.updated_by')
          ->selectRaw("k.*,p.tingkat,p.nama_kegiatan as nama_program,u.role,u.nama as pembuat,u.id_kecamatan,u.id_kelurahan")->orderBy('p.nama_kegiatan', 'ASC');
      } else {
        $program = DB::connection('pgsql')->table('public.kegiatan as k')
          ->leftjoin('public.program as p', 'k.program_id', 'p.id_program')
          ->leftjoin('public.users as u', 'u.username', 'p.updated_by')
          ->selectRaw("k.*,p.tingkat,p.nama_kegiatan as nama_program,u.role,u.nama as pembuat,u.id_kecamatan,u.id_kelurahan")->whereRaw($str_where)->orderBy('p.nama_kegiatan', 'ASC');
      }
    }

    return DataTables::of($program)
      ->addColumn('nama_kecamatan', function ($j) {
        $nama_kecamatan = '';
        if ($j->tingkat == 'kelurahan') {
          $kel = DB::connection('pgsql_paud')->table('public.kelurahan')->where('kelurahan_id', $j->id_kelurahan)->first();
          $nama_kelurahan = '';
          if (!empty($kel)) {
            $kec = DB::connection('pgsql_paud')->table('public.kecamatan')->where('kecamatan_kode', $kel->kecamatan_kode)->first();
            if (!empty($kec)) {
              $nama_kecamatan = $kec->kecamatan_dispenduk;
            }
          }
        } else {
          $kec = DB::connection('pgsql_paud')->table('public.kecamatan')->where('kecamatan_id', $j->id_kecamatan)->first();
          if (!empty($kec)) {
            $nama_kecamatan = $kec->kecamatan_dispenduk;
          }
        }

        return $nama_kecamatan;
      })
      ->addColumn('nama_kelurahan', function ($j) {
        $kel = DB::connection('pgsql_paud')->table('public.kelurahan')->where('kelurahan_id', $j->id_kelurahan)->first();
        $nama_kelurahan = '';
        if (!empty($kel)) {
          $nama_kelurahan = $kel->kelurahan_dispenduk;
        }

        return $nama_kelurahan;
      })
      ->addColumn('detail_kegiatan', function ($j) {
        $det = $j->tempat_kegiatan . '<br>' . $j->tgl_kegiatan . ' ' . $j->waktu_kegiatan . '<br>' . $j->bentuk_keg;
        return $det;
      })
      ->addColumn('detail_materi', function ($j) {
        $det = $j->materi . '<br><br>(<b>' . $j->narsum . '</b>)';
        return $det;
      })
      ->addColumn('detail_sasaran', function ($j) {
        $det = '<b>Sasaran : </b>' . $j->sasaran_kegiatan . '<br><br><b>Tujuan : </b>' . $j->tujuan_kegiatan;
        return $det;
      })
      ->addColumn('dokumentasi', function ($j) {
        $det = '';
        $url = asset('logo/image-icon.png');
        if ($j->foto != '') {
          if (Storage::exists($j->foto)) {
            $url = route('storage', [base64_encode($j->foto), '1']);
            $foto_exists = 1;
          } else {
            $url = $j->foto;
          }
          $det .= '<b>Foto : <a href="' . $url . '" target="_blank">' . $url . '</a>';
        }
        if ($j->url_video != '') {
          $det .= '<br><br><b>Video : </b><a href="' . $j->url_video . '" target="_blank">' . $j->url_video . '</a>';
        }
        return $det;
      })
      ->addIndexColumn()
      ->rawColumns(['detail_kegiatan', 'detail_materi', 'detail_sasaran', 'dokumentasi'])
      ->make(true);
  }
}
