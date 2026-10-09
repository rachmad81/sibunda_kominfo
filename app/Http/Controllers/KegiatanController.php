<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Kegiatan;
use App\Models\Program;
use App\Tools\Bantuan;
use App\Http\Controllers\ProgramController;

use Auth, Storage, DB;

class KegiatanController extends Controller
{
  function index()
  {
    return view('kegiatan.index');
  }

  function data(Request $request)
  {
    $th = $request->th;
    $status = $request->status;
    $where = '';
    if (Auth::getUser()->id_kecamatan != null) {
      $where .= " AND p.id_kecamatan='" . Auth::getUser()->id_kecamatan . "'";
    }
    if (Auth::getUser()->id_kelurahan != null) {
      $where .= " AND p.id_kelurahan='" . Auth::getUser()->id_kelurahan . "'";
    }
    if (Auth::getUser()->tingkat != null) {
      $where .= " AND p.tingkat='" . Auth::getUser()->tingkat . "'";
    }

    $data = Kegiatan::join('public.program as p', 'kegiatan.program_id', 'p.id_program')
      ->selectRaw("*,p.nama_kegiatan as nama_program,kegiatan.nama_kegiatan as kegiatan")
      // ->where('program.updated_by', request()->user()->username)
      ->where('kegiatan.updated_by', request()->user()->username)
      ->when($status != 'all', function ($w) use ($status) {
        if ($status == 'sudah') {
          return $w->whereRaw("(COALESCE(kegiatan.hasil_kegiatan,'')<>'' OR COALESCE(kegiatan.foto,'')<>'' OR COALESCE(kegiatan.url_video,'')<>'')");
        } else {
          return $w->whereRaw("(COALESCE(kegiatan.hasil_kegiatan,'')='' AND COALESCE(kegiatan.foto,'')='' AND COALESCE(kegiatan.urL_video,'')='')");
        }
      })
      ->whereRaw("p.tahun='$th' $where")->orderBy('p.nama_kegiatan', 'asc');

    $id_kegiatan = $data->pluck('id_kegiatan')->unique()->values();

    $absensi = DB::table('public.kegiatan_absensi')->whereIn('kegiatan_id', $id_kegiatan)->whereNull('deleted_at')
      ->selectRaw("kegiatan_id,COUNT(*) as jumlah")
      ->groupBy('kegiatan_id');

    $data = $data->leftJoinSub($absensi, 'a', function ($j) {
      return $j->on('kegiatan.id_kegiatan', '=', DB::raw('CAST(a.kegiatan_id as INTEGER)'));
    })->get();

    $content = view('kegiatan.data', ['data' => $data])->render();

    return ['content' => $content];
  }

  function form(Request $request)
  {
    $id_kegiatan = $request->id_kegiatan;

    if ($id_kegiatan == null) {
      $id_kegiatan = 0;
      $title = 'Tambah Kegiatan';
    } else {
      $title = 'Edit Kegiatan';
    }
    $kegiatan = Kegiatan::join('public.program as p', 'kegiatan.program_id', 'p.id_program')
      ->selectRaw("*,p.nama_kegiatan as nama_program,kegiatan.nama_kegiatan as kegiatan")
      ->whereRaw("id_kegiatan='$id_kegiatan'")->first();

    if (!empty($kegiatan)) {
      $program = ProgramController::get_program($kegiatan->tahun, $kegiatan->id_kecamatan, $kegiatan->id_kelurahan, $kegiatan->tingkat);
    } else {
      $program = '';
    }


    $data = [
      'title' => $title,
      'kegiatan' => $kegiatan,
      'program' => $program,
    ];

    $content = view('kegiatan.form', $data)->render();

    return ['content' => $content];
  }

  function form_laporan(Request $request)
  {
    $id_kegiatan = $request->id_kegiatan;

    if ($id_kegiatan == null) {
      $id_kegiatan = 0;
      $title = 'Tambah Kegiatan';
    } else {
      $title = 'Laporan Kegiatan';
    }
    $kegiatan = Kegiatan::join('public.program as p', 'kegiatan.program_id', 'p.id_program')
      ->selectRaw("*,p.nama_kegiatan as nama_program,kegiatan.nama_kegiatan as kegiatan")
      ->whereRaw("id_kegiatan='$id_kegiatan'")->first();

    if (!empty($kegiatan)) {
      $program = ProgramController::get_program($kegiatan->tahun, $kegiatan->id_kecamatan, $kegiatan->id_kelurahan, $kegiatan->tingkat);
    } else {
      $program = '';
    }


    $data = [
      'title' => $title,
      'kegiatan' => $kegiatan,
      'program' => $program,
    ];

    $content = view('kegiatan.form_laporan', $data)->render();

    return ['content' => $content];
  }

  function simpan(Request $request)
  {
    $data_insert = (array)$request->all();
    unset($data_insert['tahun_form']);
    unset($data_insert['id_kegiatan']);
    $data_insert = array_merge($data_insert, [
      'updated_by' => Auth::getUser()->username,
      'updated_at' => date('Y-m-d H:i:s'),
    ]);
    $id_kegiatan = ($request->id_kegiatan == '') ? null : $request->id_kegiatan;

    if ($id_kegiatan == null) {
      $simpan = DB::table('public.kegiatan')->insert($data_insert);
    } else {
      $simpan = DB::table('public.kegiatan')->where('id_kegiatan', $id_kegiatan)->update($data_insert);
    }

    if ($simpan) {
      $return = ['code' => '200', 'message' => 'Berhasil disimpan', 'title' => 'Success', 'type' => 'success'];
    } else {
      $return = ['code' => '250', 'message' => 'Gagal disimpan', 'title' => 'Whooops', 'type' => 'error'];
    }

    return $return;
  }

  function simpan_laporan(Request $request)
  {
    // $foto = $request->file('foto');
    $foto = $request->foto;

    $data_insert = (array)$request->all();
    // unset($data_insert['foto']);
    unset($data_insert['id_kegiatan']);
    $data_insert = array_merge($data_insert, [
      'updated_by' => Auth::getUser()->username,
      'updated_at' => date('Y-m-d H:i:s'),
    ]);
    $id_kegiatan = $request->id_kegiatan;

    $izinkan = '1';
    // if(!empty($foto)){

    //     $a = Bantuan::validate_file($request->file('foto'),'gambar');

    //     if($a){
    //         $ukuran = $foto->getSize();
    //         if($ukuran>300000){
    //             $izinkan = '0';
    //         }else{
    //             $kegiatan = Kegiatan::where('id_kegiatan',$id_kegiatan)->first();
    //             if($kegiatan->foto!=''){
    //                 if (Storage::exists($kegiatan->foto)) {
    //                     Storage::delete($kegiatan->foto);
    //                 }
    //             }

    //             $file = $request->file('foto');
    //             $nama_file = "kegiatan/kegiatan_".date('YmdHis')."-".uniqid().".".$file->extension();

    //             Storage::put($nama_file, file_get_contents($file));

    //             $data_insert = array_merge($data_insert,[
    //                 'foto'=>$nama_file,
    //             ]);

    //             $izinkan = '1';
    //         }
    //     }else{
    //         $izinkan = '0';
    //     }
    // }

    if ($izinkan == '1') {
      $data_pendukung = [
        'kak' => $data_insert['kak'],
        'notulen' => $data_insert['notulen'],
        'absensi' => $data_insert['absensi'],
        'foto_geo' => $data_insert['foto_geo'],
        'undangan' => $data_insert['undangan'],
      ];

      $file_pendukung = json_encode($data_pendukung);
      unset($data_insert['kak']);
      unset($data_insert['notulen']);
      unset($data_insert['absensi']);
      unset($data_insert['foto_geo']);
      unset($data_insert['undangan']);
      $data_insert['file_pendukung'] = $file_pendukung;

      $simpan = Kegiatan::where('id_kegiatan', $id_kegiatan)->update($data_insert);

      if ($simpan) {
        $return = ['code' => '200', 'message' => 'Berhasil disimpan', 'title' => 'Success', 'type' => 'success'];
      } else {
        $return = ['code' => '250', 'message' => 'Gagal disimpan', 'title' => 'Whooops', 'type' => 'error'];
      }
    } else {
      $return = ['code' => '350', 'message' => 'Ukuran file > 3ooKB/Format file tidak didukung', 'title' => 'Whooops', 'type' => 'error'];
    }

    return $return;
  }

  function hapus(Request $request)
  {
    $id_kegiatan = $request->id_kegiatan;
    $kegiatan = Kegiatan::where('id_kegiatan', $id_kegiatan)->first();
    if ($kegiatan->foto != '') {
      if (Storage::exists($kegiatan->foto)) {
        Storage::delete($kegiatan->foto);
      }
    }

    $hapus = Kegiatan::where('id_kegiatan', $id_kegiatan)->delete();

    if ($hapus) {
      $return = ['code' => '200', 'message' => 'Berhasil dihapus', 'title' => 'Success', 'type' => 'success'];
    } else {
      $return = ['code' => '250', 'message' => 'Gagal dihapus', 'title' => 'Whooops', 'type' => 'error'];
    }

    return $return;
  }

  function absensi(Request $request)
  {
    $halaman = $request->halaman;

    switch ($halaman) {
      case 'absensi':
        if (Auth::check()) {
          $id_kegiatan = base64_decode($request->code);
          $url = route('kegiatan-absensi', ['halaman' => 'form_absensi', 'code' => base64_encode($id_kegiatan)]);

          $kegiatan = DB::table('kegiatan')->where('id_kegiatan', $id_kegiatan)->first();

          $data = [
            'url' => $url,
            'kegiatan' => $kegiatan
          ];
          return view('kegiatan.absensi', $data);
        } else {
          return 'Mohon maaf anda tidak memiliki akses ke halaman ini';
        }
        break;

      case 'form_absensi':
        $id_kegiatan = base64_decode($request->code);

        $kegiatan = DB::table('kegiatan')->where('id_kegiatan', $id_kegiatan)->first();

        $data = [
          'kegiatan' => $kegiatan,
        ];
        return view('kegiatan.form_absensi', $data);
        break;

      case 'simpan':
        $id_kegiatan = $request->id_kegiatan; //"18456"
        $instansi = $request->instansi; //"sdasdasd"
        $nama = $request->nama; //"sadada"
        $nik = $request->nik; //"asdadad"
        $signature = $request->signature; //"data:image/png;

        $data_absensi = [
          'nik' => $nik,
          'instansi' => $instansi,
          'nama' => $nama,
          'ttd' => $signature,
          'kegiatan_id' => $id_kegiatan,
        ];

        $cari = DB::table('public.kegiatan_absensi')->where('nik', $nik)->where('kegiatan_id', $id_kegiatan)->whereNull('deleted_at')->first();
        if (!empty($cari)) {
          return ['code' => 250, 'pesan' => 'Data ' . $nik . ', Sudah melakukan absensi'];
        } else {
          $simpan = DB::table('public.kegiatan_absensi')->insert($data_absensi);
          if ($simpan) {
            return ['code' => 200, 'data' => 'Berhasil disimpan'];
          } else {
            return ['code' => 250, 'data' => 'Gagal menyimpan'];
          }
        }
        break;

      case 'rekap_absensi':
        if (Auth::check()) {
          $id_kegiatan = base64_decode($request->code);
          $kegiatan = DB::table('kegiatan')->where('id_kegiatan', $id_kegiatan)->first();
          $absensi = DB::table('public.kegiatan_absensi')->where('kegiatan_id', $id_kegiatan)->whereNull('deleted_at')->get();

          $data = [
            'kegiatan' => $kegiatan,
            'absensi' => $absensi,
          ];

          return view('kegiatan.rekap_absensi', $data);
        } else {
          return 'Anda tidak memiliki akses di halaman ini';
        }
        break;

      default:
        return '404 - Halaman tidak ditemukan';
        break;
    }
  }
}
