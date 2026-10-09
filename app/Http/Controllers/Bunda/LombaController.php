<?php

namespace App\Http\Controllers\Bunda;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Pelatihan\Jadwal;
use App\Models\Pelatihan\Materi;
use App\Models\Kegiatan;
use App\Http\Libraries\UraianUtamaLomba;
use App\Http\Libraries\UraianPendukungLomba;
use App\Services\GoogleDriveService;

use DB, Session, Auth;

class LombaController extends Controller
{
  function main(Request $request)
  {
    $tahun = $request->tahun;
    $peserta = DB::table('cutoff_rekap.peserta_lomba')
      ->whereRaw("tahun='$tahun' AND user_id='" . Auth::getUser()->id . "'")->first();

    $identitas = DB::table('cutoff_rekap.peserta_lomba')->whereRaw("tahun=? AND user_id=?", [$tahun, Auth::User()->id])->first();
    $status = (!empty($identitas)) ? 'ada' : 'kosong';
    $data = [
      'status_iden' => $status,
      'tahun' => $tahun,
    ];

    // if(!empty($peserta)){
    // 	if($peserta->nilai_akhir!=0){
    // 		return view('bunda.lomba.berakhir',$data);
    // 	}else{
    // 		return view('bunda.lomba.index',$data);
    // 	}
    // }else{
    return view('bunda.lomba.index', $data);
    // }
  }

  function get_data(Request $request)
  {
    return $request->all();
  }

  function get_form(Request $request)
  {
    $kelas = $request->form;
    $tahun = $request->tahun;
    if (Auth::User()->tingkat == 'kecamatan') {
      $where = "CAST(id_kecamatan as varchar)='" . Auth::User()->id_kecamatan . "'";
      $where_jabatan = " AND id_jabatan='18'";
    } else if (Auth::User()->tingkat == 'kelurahan') {
      $where = "CAST(id_kelurahan as varchar)='" . Auth::User()->id_kelurahan . "'";
      $where_jabatan = " AND id_jabatan='33'";
    } else {
      $where = "id_kecamatan is null AND id_kelurahan is null";
      $where_jabatan = " AND id_jabatan='3'";
    }
    $berkas = DB::table('public.berkas_pokja')
      ->whereRaw("$where")->first();
    if (empty($berkas)) {
      return ['code' => '404', 'message' => 'Silahkan upload berkas pokja pada menu Berkas Pokja'];
    }

    $profil = DB::table('public.profil_pokja')
      ->whereRaw("$where $where_jabatan")->first();

    $data = [];

    if ($kelas == 'form1') {
      $pokja = DB::table('public.profil_pokja as p')
        ->join('public.berkas_pokja as b', function ($j) {
          return $j
            ->on(DB::raw('COALESCE(b.id_kecamatan,0)'), '=', DB::raw('COALESCE(p.id_kecamatan,0)'))
            ->on(DB::raw('COALESCE(b.id_kelurahan,0)'), '=', DB::raw('COALESCE(p.id_kelurahan,0)'));
        })
        ->selectRaw("p.id,p.nama,p.alamat_kerja,p.unit_kerja,p.no_telp,b.no_sk_penetapan_bunda,b.tgl_sk_penetapan_bunda")
        ->whereRaw("p.deleted_at is null AND
				(
				(p.id_jabatan='18' AND CAST(p.id_kecamatan as varchar)='" . Auth::User()->id_kecamatan . "') OR
				(p.id_jabatan='33' AND CAST(p.id_kelurahan as varchar)='" . Auth::User()->id_kelurahan . "') OR
				(p.id_jabatan='3')
			)")->first();

      $identitas = DB::table('cutoff_rekap.peserta_lomba')->whereRaw("tahun=? AND user_id=?", [$tahun, Auth::User()->id])->first();

      $data = ['identitas' => $identitas, 'pokja' => $pokja];

      $view = "form1";
    } elseif ($kelas == 'form2') {
      $berkas = DB::table('cutoff_rekap.berkas_identitas')->whereRaw("tahun=? AND user_id=?", [$tahun, Auth::User()->id])->first();

      $data = ['berkas' => $berkas];
      $view = "form2";
    } elseif ($kelas == 'form3') {
      $berkas = DB::table('cutoff_rekap.kriteria_utama')->whereRaw("tahun=? AND user_id=?", [$tahun, Auth::User()->id])->first();
      $uraian_kriteria_utama = UraianUtamaLomba::cek_tahun($tahun);

      $th = $tahun;
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

      $where_ka = [
        'tahun_lomba' => $tahun,
        'user_id' => Auth::User()->id,
        'deleted_at' => null,
      ];
      $kegiatan_apresiasi = DB::table('cutoff_rekap.kegiatan_apresiasi as ka')
        ->join('public.kegiatan as k', 'k.id_kegiatan', 'ka.kegiatan_id')
        ->join('public.program as p', 'p.id_program', 'k.program_id')
        ->selectRaw("ka.id,k.id_kegiatan,k.nama_kegiatan,ka.sub,p.tahun")
        ->where($where_ka)->get();

      $id_sudah = $kegiatan_apresiasi->pluck('id_kegiatan')->toArray();

      $kegiatan = Kegiatan::join('public.program as p', 'kegiatan.program_id', 'p.id_program')
        ->selectRaw("kegiatan.id_kegiatan,p.nama_kegiatan as nama_program,kegiatan.nama_kegiatan,p.tahun")
        ->when($tahun, function ($query) use ($tahun) {
          if ($tahun == '2024') {
            return $query->whereRaw("tahun='2024'");
          } else if ($tahun == '2025') {
            return $query->whereRaw("tahun in ('2024','2025')");
          }
        })
        ->whereRaw(Auth::getUser()->id . " is not null $where")
        // ->whereNotIn('kegiatan.id_kegiatan',$id_sudah)
        ->orderByRaw("p.tahun,p.nama_kegiatan,kegiatan.nama_kegiatan")->get();

      $data = ['berkas' => $berkas, 'kegiatan' => $kegiatan, 'kegiatan_apresiasi' => $kegiatan_apresiasi, 'uraian_kriteria_utama' => $uraian_kriteria_utama];
      $view = "form3";
    } elseif ($kelas == 'form4') {
      $berkas = DB::table('cutoff_rekap.kriteria_pendukung')->whereRaw("tahun=? AND user_id=?", [$tahun, Auth::User()->id])->first();
      $uraian_kriteria_pendukung = UraianPendukungLomba::cek_tahun($tahun);

      $th = $tahun;
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

      $where_ma = [
        'ma.tahun_lomba' => $tahun,
        'ma.user_id' => Auth::User()->id,
        'ma.deleted_at' => null,
      ];

      $kegiatan_apresiasi = DB::table('cutoff_rekap.mitra_apresiasi as ma')
        ->join('public.kegiatan as k', 'k.id_kegiatan', 'ma.mitra_id')
        ->join('public.program as p', 'p.id_program', 'k.program_id')
        ->selectRaw("ma.id,k.nama_kegiatan as nama_mitra,k.id_kegiatan as id_mitra,ma.sub,ma.jenis,CAST(p.tahun as INTEGER) as tahun")
        ->where($where_ma)->whereRaw("jenis='kegiatan'");

      $mitra_apresiasi = DB::table('cutoff_rekap.mitra_apresiasi as ma')
        ->join('cutoff_rekap.mitra_daftar as md', 'md.id_mitra', 'ma.mitra_id')
        ->selectRaw("ma.id,md.nama_mitra,md.id_mitra,ma.sub,ma.jenis," . date('Y') . " as tahun")
        ->where($where_ma)->whereRaw("jenis='mitra'")->union($kegiatan_apresiasi)->orderBy('jenis', 'DESC')->get();

      $id_sudah = $mitra_apresiasi->pluck('id_mitra')->toArray();

      $where_ka = [
        'deleted_at' => null,
      ];
      $mitra_daftar = DB::table('cutoff_rekap.mitra_daftar as ka')
        ->where($where_ka)
        // ->whereNotIn('id_mitra',$id_sudah)
        ->get();

      $kegiatan = Kegiatan::join('public.program as p', 'kegiatan.program_id', 'p.id_program')
        ->selectRaw("kegiatan.id_kegiatan,p.nama_kegiatan as nama_program,kegiatan.nama_kegiatan,p.tahun")
        ->when($tahun == '2024', function ($query, $tahun) {
          return $query->whereRaw("tahun='2024'");
        })
        ->when($tahun == '2025', function ($query, $tahun) {
          return $query->whereRaw("tahun in ('2024','2025')");
        })
        ->whereRaw(Auth::getUser()->id . " is not null $where")
        // ->whereNotIn('kegiatan.id_kegiatan',$id_sudah)
        ->orderByRaw("p.tahun,p.nama_kegiatan,kegiatan.nama_kegiatan")->get();

      $data = ['berkas' => $berkas, 'kegiatan' => $kegiatan, 'mitra_daftar' => $mitra_daftar, 'mitra_apresiasi' => $mitra_apresiasi, 'uraian_kriteria_pendukung' => $uraian_kriteria_pendukung];
      $view = "form4";
    } else {
      $data = [];
      $view = 'panduan_isi';
    }

    $data = array_merge($data, [
      'berkas' => $berkas,
      'profil' => $profil,
      'tahun' => $tahun,
    ]);

    $content = view('bunda.lomba.' . $tahun . '.' . $view, $data)->render();

    return ['content' => $content, 'kelas' => $kelas];
  }

  function simpan_identitas(Request $request)
  {
    $alamat_instansi = $request->alamat_instansi;
    $email_pengusul = $request->email_pengusul;
    $instansi = $request->instansi;
    $kecamatan_instansi = $request->kecamatan_instansi;
    $kelurahan_instansi = $request->kelurahan_instansi;
    $nama = $request->nama;
    $no_sk = $request->no_sk;
    $tgl_sk = $request->tgl_sk;
    $no_sk_pengukuhan = $request->no_sk_pengukuhan;
    $tgl_sk_pengukuhan = $request->tgl_sk_pengukuhan;
    $telp_bunda = $request->telp_bunda;
    $telp_pengusul = $request->telp_pengusul;
    $tahun = $request->tahun;
    $id_user = Auth::User()->id;

    $where = [
      'user_id' => $id_user,
      'tahun' => $tahun,
    ];

    $data_insert = [
      'user_id' => $id_user,
      'tahun' => $tahun,
      'nama' => $nama,
      'no_sk' => $no_sk,
      'tgl_sk' => $tgl_sk,
      'no_sk_pengukuhan' => $no_sk_pengukuhan,
      'tgl_sk_pengukuhan' => $tgl_sk_pengukuhan,
      'telp_peserta' => $telp_bunda,
      'instansi' => $instansi,
      'alamat_instansi' => $alamat_instansi,
      'kel_instansi' => $kelurahan_instansi,
      'kec_instansi' => $kecamatan_instansi,
      'telp_pengusul' => $telp_pengusul,
      'email_pengusul' => $email_pengusul,
      'updated_at' => date('Y-m-d H:i:s'),
    ];

    $simpan = DB::table('cutoff_rekap.peserta_lomba')->updateOrInsert($where, $data_insert);

    if ($simpan) {
      return ['code' => 200, 'message' => 'Berhasil disimpan'];
    } else {
      return ['code' => 250, 'message' => 'Gagal disimpan'];
    }
  }

  function simpan_berkas(Request $request)
  {
    $table = $request->table; //"kriteria_utama"
    $form = $request->form; //"form3"
    $berkas = $request->berkas; //"inovasi||1"
    $file = $request->file; //"asd"
    $tahun = $request->tahun; //"asd"

    $arr_kolom = explode('___', $berkas);
    $kolom = $arr_kolom[0];

    if (count($arr_kolom) > 1) {
      $number = $arr_kolom[1];
      $id_kegiatan = $arr_kolom[2];
      $sub = $arr_kolom[3];
      $berkas = DB::table('cutoff_rekap.' . $table)->whereRaw("tahun=? AND user_id=?", [$tahun, Auth::User()->id])->first();

      $data_insert = [];
      if (!empty($berkas)) {
        if (!is_null($berkas->$kolom)) {
          $exist = json_decode($berkas->$kolom);
          foreach ($exist as $ex) {
            if ($ex->number == $number && $ex->id_kegiatan == $id_kegiatan) {
            } else {
              $data_lama = [
                'number' => isset($ex->number) ? $ex->number : '',
                'id_kegiatan' => isset($ex->id_kegiatan) ? $ex->id_kegiatan : '',
                'sub' => isset($ex->sub) ? $ex->sub : '',
                'file' => isset($ex->file) ? $ex->file : '',
              ];
              array_push($data_insert, $data_lama);
            }
          }
        }
        if ($file == '') {
          $cek_gdrive = 'PUBLIC';
        } else {
          $cek_gdrive = $this->checkDrivePermission($file);
        }
        array_push($data_insert, ['number' => $number, 'id_kegiatan' => $id_kegiatan, 'sub' => $sub, 'file' => $file]);
      } else {
        if ($file == '') {
          $cek_gdrive = 'PUBLIC';
        } else {
          $cek_gdrive = $this->checkDrivePermission($file);
        }
        array_push($data_insert, ['number' => $number, 'id_kegiatan' => $id_kegiatan, 'sub' => $sub, 'file' => $file]);
      }
      if ($cek_gdrive == 'PUBLIC') {
      } else {
        return ['code' => 250, 'message' => "ℹ️ Status tidak diketahui (HTTP Code: $cek_gdrive), <br>pastikan link gdrive sudah dibagikan dengan settingan \"Siapa saja yang memiliki link/Anyone with the link\""];
      }

      $data_insert = collect($data_insert)->sortBy('number')->all();
      $data_insert = array_values($data_insert);
      $data_insert = json_encode($data_insert);
    } else {
      $data_insert = $file;
      if (in_array($file, ['Selesai', 'Draft'])) {
      } else {
        if ($file == '') {
          $cek_gdrive = 'PUBLIC';
        } else {
          $cek_gdrive = $this->checkDrivePermission($file);
        }

        if ($cek_gdrive == 'PUBLIC') {
        } else {
          return ['code' => 250, 'message' => "ℹ️ Status tidak diketahui (HTTP Code: $cek_gdrive), <br>pastikan link gdrive sudah dibagikan dengan settingan \"Siapa saja yang memiliki link/Anyone with the link\""];
        }
      }
    }


    $where = [
      'user_id' => Auth::User()->id,
      'tahun' => $tahun,
    ];

    $data = [
      $kolom => $data_insert,
      'updated_at' => date('Y-m-d H:i:s'),
    ];

    $simpan = DB::table('cutoff_rekap.' . $table)->updateOrInsert($where, $data);

    if ($simpan) {
      return ['code' => 200, 'message' => 'Berhasil disimpan', 'form' => $form];
    } else {
      return ['code' => 250, 'message' => 'Gagal disimpan'];
    }
  }

  function simpan_kegiatan(Request $request)
  {
    $arr_kegiatan = explode('--', $request->kegiatan_id);
    $sub = $arr_kegiatan[0] . '_' . $arr_kegiatan[1];
    $kegiatan_id = $arr_kegiatan[2];
    $user_id = Auth::User()->id;
    $tahun = $request->tahun;
    $updated_at = date('Y-m-d H:i:s');

    $data_simpan = [
      'kegiatan_id' => $kegiatan_id,
      'sub' => $sub,
      'user_id' => $user_id,
      'tahun_lomba' => $tahun,
      'updated_at' => $updated_at,
    ];

    $where = [
      'kegiatan_id' => $kegiatan_id,
      'sub' => $sub,
      'user_id' => $user_id,
      'tahun_lomba' => $tahun,
      'deleted_at' => null,
    ];

    $simpan = DB::table('cutoff_rekap.kegiatan_apresiasi')->updateOrInsert($where, $data_simpan);

    if ($simpan) {
      return ['code' => '200'];
    } else {
      return ['code' => '250'];
    }
  }

  function hapus_kegiatan(Request $request)
  {
    $id = $request->id;
    $updated_at = date('Y-m-d H:i:s');

    $data_simpan = [
      'updated_at' => $updated_at,
      'deleted_at' => $updated_at,
    ];

    $where = [
      'id' => $id,
    ];

    $simpan = DB::table('cutoff_rekap.kegiatan_apresiasi')->updateOrInsert($where, $data_simpan);

    if ($simpan) {
      return ['code' => '200'];
    } else {
      return ['code' => '250'];
    }
  }

  function modal_kegiatan(Request $request)
  {
    $id_kegiatan = $request->id_kegiatan;
    $tahun = $request->tahun;

    $kegiatan = DB::table('public.kegiatan as k')
      ->join('public.program as p', 'p.id_program', 'k.program_id')
      ->selectRaw("p.nama_kegiatan as nama_program,k.*")
      ->whereRaw("k.id_kegiatan='$id_kegiatan'")
      ->first();

    $data = ['kegiatan' => $kegiatan];

    $content = view('bunda.lomba.' . $tahun . '.modal_kegiatan', $data)->render();

    return ['content' => $content];
  }

  function simpan_mitra(Request $request)
  {
    $arr_mitra = explode('--', $request->mitra_id);
    $sub = $arr_mitra[0] . '_' . $arr_mitra[1];
    $mitra_id = $arr_mitra[2];
    $mitra_lain = $request->mitra_lain;
    $user_id = Auth::User()->id;
    $tahun = $request->tahun;
    $jenis = $request->jenis;
    $updated_at = date('Y-m-d H:i:s');

    if ($mitra_id == 'lainnya') {
      $exist = DB::table('cutoff_rekap.mitra_daftar')->where(
        [['nama_mitra', 'like', '%' . $mitra_lain . '%']]
      )->first();
      if (!empty($exist)) {
        $mitra_id = $exist->id_mitra;
      } else {
        $mitra_id = DB::table('cutoff_rekap.mitra_daftar')->insertGetId(['nama_mitra' => $mitra_lain], 'id_mitra');
      }
    }

    $data_simpan = [
      'mitra_id' => $mitra_id,
      'sub' => $sub,
      'user_id' => $user_id,
      'tahun_lomba' => $tahun,
      'updated_at' => $updated_at,
      'jenis' => $jenis,
    ];

    $where = [
      'mitra_id' => $mitra_id,
      'sub' => $sub,
      'user_id' => $user_id,
      'tahun_lomba' => $tahun,
      'jenis' => $jenis,
      'deleted_at' => null,
    ];

    $simpan = DB::table('cutoff_rekap.mitra_apresiasi')->updateOrInsert($where, $data_simpan);

    if ($simpan) {
      return ['code' => '200'];
    } else {
      return ['code' => '250'];
    }
  }

  function hapus_mitra(Request $request)
  {
    $id = $request->id;
    $updated_at = date('Y-m-d H:i:s');

    $data_simpan = [
      'updated_at' => $updated_at,
      'deleted_at' => $updated_at,
    ];

    $where = [
      'id' => $id,
    ];

    $simpan = DB::table('cutoff_rekap.mitra_apresiasi')->updateOrInsert($where, $data_simpan);

    if ($simpan) {
      return ['code' => '200'];
    } else {
      return ['code' => '250'];
    }
  }

  function checkDrivePermission_satu($url)
  {
    // extract FILE_ID dari link gdrive
    preg_match('/[-\w]{25,}/', $url, $matches);
    $fileId = $matches[0] ?? null;

    if (!$fileId) {
      return "File ID tidak ditemukan di link";
    }

    $apiKey = env('google_api_key'); // simpan di .env
    $apiUrl = "https://www.googleapis.com/drive/v3/files/$fileId?fields=permissions&key=$apiKey";

    $response = Http::get($apiUrl);

    if ($response->status() === 200) {
      return "PUBLIC";
    } elseif ($response->status() === 403 || $response->status() === 401) {
      return "RESTRICTED (butuh login/izin khusus)";
    } else {
      return "UNKNOWN (status: " . $response->status() . ")";
    }
  }

  function checkDrivePermission($url)
  {
    $ch = curl_init($url);

    curl_setopt_array($ch, [
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_HEADER         => true,
      CURLOPT_NOBODY         => true,  // jangan ambil body, cukup header
      CURLOPT_FOLLOWLOCATION => false, // jangan auto redirect
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $redirect = curl_getinfo($ch, CURLINFO_REDIRECT_URL);

    curl_close($ch);

    if ($httpCode == 200) {
      return "PUBLIC";
    } elseif ($httpCode == 302 || $httpCode == 301) {
      if (strpos($response, "accounts.google.com") !== false) {
        return "Restricted / Butuh login";
      }
    }

    return "Link rusak atau tidak valid";
  }
}
