<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\ProfilSekolah\Kecamatan;
use App\Models\ProfilSekolah\Pegawai;
use App\Models\ProfilSekolah\Siswa;
use App\Models\ProfilSekolah\Sekolah;
use App\Models\User;
use App\Models\BerkasPokja;
use App\Models\ProfilPokja;
use App\Models\LogError;
use App\Models\JabatanPokja;
use App\Models\Materi;
use App\Tools\Bantuan;
use DB, Hash, Response, Storage, Redirect, Auth;

class CommonController extends Controller
{
  public function indexManajemenData()
  {
    switch (\Auth::User()->role) {
      case "dinas":
        return redirect()->route('dinas-user-index');
        break;

      case "bunda":
        return redirect()->route('bunda-beranda-index');
        break;

      case "pokja":
        return redirect()->route('common-berkas-index');
        break;

      case "sekolah":
        return redirect()->route('sekolah-dashboard-index');
        break;

      case "opd":
        return redirect()->route('bunda-beranda-index');
        break;

      case "penilai":
        return redirect()->route('penilai-dashboard-index');
        break;

      case "k3_paud":
        return redirect()->route('k3_paud-dashboard-index');
        break;

      case "paud_sd":
        return redirect()->route('paud_sd-dashboard-index');
        break;

      case "gugus":
        return redirect()->route('gugus-dashboard-index');
        break;

      case "mitra":
        return redirect()->route('mitra-dashboard-index');
        break;
    }
  }

  public function indexBerkas()
  {
    if (in_array(\Auth::User()->role, ['k3_paud', 'paud_sd'])) {
      $data = BerkasPokja::where('updated_by', \Auth::User()->id)
        ->first();
    } else {
      $data = BerkasPokja::where('tingkat', \Auth::User()->tingkat)
        ->where('updated_by', \Auth::User()->id)
        ->when(\Auth::User()->id_kecamatan, function ($q) {
          $q->where('id_kecamatan', \Auth::User()->id_kecamatan);
        })
        ->when(\Auth::User()->id_kelurahan, function ($q) {
          $q->where('id_kelurahan', \Auth::User()->id_kelurahan);
        })
        ->first();
    }

    return view('common.berkas-pokja.index', compact('data'));
  }

  public function storeBerkas(Request $request)
  {
    try {
      DB::beginTransaction();

      $data = [
        'no_sk_penetapan_bunda'       => $request->no_sk_penetapan_bunda ? $request->no_sk_penetapan_bunda : null,
        'tgl_sk_penetapan_bunda'      => $request->tgl_sk_penetapan_bunda ? $request->tgl_sk_penetapan_bunda : null,
        'pejabat_sk_penetapan_bunda'  => $request->pejabat_sk_penetapan_bunda ? $request->pejabat_sk_penetapan_bunda : null,
        'no_sk_penetapan_pokja'       => $request->no_sk_penetapan_pokja ? $request->no_sk_penetapan_pokja : null,
        'tgl_sk_penetapan_pokja'      => $request->tgl_sk_penetapan_pokja ? $request->tgl_sk_penetapan_pokja : null,
        'pejabat_sk_penetapan_pokja'  => $request->pejabat_sk_penetapan_pokja ? $request->pejabat_sk_penetapan_pokja : null,
        'updated_at' => date('Y-m-d H:i:s'),
        'updated_by' => \Auth::User()->id,
        'tingkat' => \Auth::User()->tingkat,
        'id_kecamatan' => \Auth::User()->id_kecamatan,
        'id_kelurahan' => \Auth::User()->id_kelurahan,
      ];

      if (in_array(Auth::User()->role, ['k3_paud', 'paud_sd', 'mitra'])) {
        unset($data['id_kecmatan']);
        unset($data['id_kelurahan']);
      }

      if ($request->hasFile('file_sk_penetapan_bunda')) {
        $file = $request->file('file_sk_penetapan_bunda');

        $a = Bantuan::validate_file($request->file('file_sk_penetapan_bunda'), 'gambar');

        if ($a) {
          if (\Auth::User()->path_sk_penetapan_bunda && Storage::exists(\Auth::User()->path_sk_penetapan_bunda)) {
            Storage::delete(\Auth::User()->path_sk_penetapan_bunda);
          }

          $nama_file = "file-sk/bunda" . date('YmdHis') . "-" . uniqid() . "." . $file->extension();

          Storage::put($nama_file, file_get_contents($file));

          $data = array_merge($data, ['path_sk_penetapan_bunda' => $nama_file]);
        }
      }


      if ($request->hasFile('file_sk_penetapan_pokja')) {
        $file = $request->file('file_sk_penetapan_pokja');

        $a = Bantuan::validate_file($request->file('file_sk_penetapan_pokja'), 'gambar');

        if ($a) {
          if (\Auth::User()->path_sk_penetapan_pokja && Storage::exists(\Auth::User()->path_sk_penetapan_pokja)) {
            Storage::delete(\Auth::User()->path_sk_penetapan_pokja);
          }

          $nama_file = "file-sk/pokja" . date('YmdHis') . "-" . uniqid() . "." . $file->extension();

          Storage::put($nama_file, file_get_contents($file));

          $data = array_merge($data, ['path_sk_penetapan_pokja' => $nama_file]);
        }
      } else {
        $data = array_merge($data, ['path_sk_penetapan_pokja' => '']);
      }

      if (in_array(Auth::User()->role, ['k3_paud', 'paud_sd', 'mitra'])) {
        $cek = BerkasPokja::where('updated_by', \Auth::User()->id)
          ->exists();
      } else {
        $cek = BerkasPokja::where('tingkat', \Auth::User()->tingkat)
          ->where('updated_by', \Auth::User()->id)
          ->when(\Auth::User()->id_kecamatan, function ($q) {
            $q->where('id_kecamatan', \Auth::User()->id_kecamatan);
          })
          ->when(\Auth::User()->id_kelurahan, function ($q) {
            $q->where('id_kelurahan', \Auth::User()->id_kelurahan);
          })
          ->exists();
      }
      if ($cek) {
        if (in_array(Auth::User()->role, ['k3_paud', 'paud_sd', 'mitra'])) {
          BerkasPokja::where('updated_by', \Auth::User()->id)
            ->update($data);
        } else {
          BerkasPokja::where('tingkat', \Auth::User()->tingkat)
            ->where('updated_by', \Auth::User()->id)
            ->when(\Auth::User()->id_kecamatan, function ($q) {
              $q->where('id_kecamatan', \Auth::User()->id_kecamatan);
            })
            ->when(\Auth::User()->id_kelurahan, function ($q) {
              $q->where('id_kelurahan', \Auth::User()->id_kelurahan);
            })
            ->update($data);
        }
      } else {
        BerkasPokja::create($data);
      }

      DB::commit();
      return redirect(route('common-berkas-index'))->with('alert-success', 'Data Berkas berhasil diperbarui!');
    } catch (\Exception $e) {
      DB::rollback();
      // LogError::create(['fungsi'=>'updateProfil','lokasi'=>'CommonController','pesan'=>$e->getMessage().' Oleh : '.\Auth::User()->username]);
      return redirect(route('common-berkas-index'))->with('alert-fail', 'Gagal mengupdate data! Error : ' . $e->getMessage());
    }
  }

  public function indexPokja()
  {
    if (in_array(Auth::User()->role, ['k3_paud', 'paud_sd'])) {
      $data = ProfilPokja::where('updated_by', \Auth::User()->id)
        ->with('jabatanPokja')->get();
    } else {
      $data = ProfilPokja::where('tingkat', \Auth::User()->tingkat)
        ->where('updated_by', \Auth::User()->id)
        ->when(\Auth::User()->id_kecamatan, function ($q) {
          $q->where('id_kecamatan', \Auth::User()->id_kecamatan);
        })
        ->when(\Auth::User()->id_kelurahan, function ($q) {
          $q->where('id_kelurahan', \Auth::User()->id_kelurahan);
        })
        ->with('jabatanPokja')->get();
    }

    return view('common.profil-pokja.index', compact('data'));
  }

  public function kelolaPokja(Request $request, $aksi)
  {
    $list_jabatan = JabatanPokja::where('tingkat', \Auth::User()->tingkat)->get();
    $list_pendidikan = DB::connection('pgsql_paud')->table('jenjang_pendidikan')->select('nama')->get();
    $data = '';

    if ($request->has('id')) {
      $data = ProfilPokja::find($request->id);
    }

    return view('common.profil-pokja.kelola', compact('list_pendidikan', 'aksi', 'data', 'list_jabatan'));
  }

  public function storePokja(Request $request)
  {
    try {
      DB::beginTransaction();

      $data = [
        'nama'                  => $request->nama,
        'id_jabatan'            => $request->jabatan,
        'email'                 => $request->email,
        'agama'                 => $request->agama,
        'no_telp'               => $request->no_telp,
        'alamat'                => $request->alamat,
        'tempat_lahir'          => $request->tempat_lahir,
        'tgl_lahir'             => $request->tgl_lahir,
        'kelamin'               => $request->kelamin,
        'pendidikan_terakhir'   => $request->pendidikan_terakhir,
        'jurusan_pendidikan'    => $request->jurusan_pendidikan,
        'profesi'               => $request->profesi,
        'unit_kerja'            => $request->unit_kerja,
        'alamat_kerja'          => $request->alamat_kerja,
        'nik'                   => $request->nik,
        'kata_sambutan'         => $request->kata_sambutan ? $request->kata_sambutan : null,
        'updated_at'            => date('Y-m-d H:i:s'),
        'updated_by'            => \Auth::User()->id,
        'tingkat'               => \Auth::User()->tingkat,
        'id_kecamatan'          => \Auth::User()->id_kecamatan,
        'id_kelurahan'          => \Auth::User()->id_kelurahan,
      ];

      if ($request->hasFile('file_foto')) {
        $file = $request->file('file_foto');

        $a = Bantuan::validate_file($request->file('file_foto'), 'gambar');

        if ($a) {
          // if (\Auth::User()->path_foto && Storage::exists(\Auth::User()->path_foto)) {
          //     Storage::delete(\Auth::User()->path_foto);
          // }

          $nama_file = "foto-bunda/" . date('YmdHis') . "-" . uniqid() . "." . $file->extension();

          Storage::put($nama_file, file_get_contents($file));

          $data = array_merge($data, ['path_foto' => $nama_file]);
        }
      }

      ProfilPokja::create($data);

      DB::commit();
      return redirect(route('common-pokja-index'))->with('alert-success', 'Data Pokja berhasil disimpan!');
    } catch (\Exception $e) {
      DB::rollback();
      // LogError::create(['fungsi'=>'updateProfil','lokasi'=>'CommonController','pesan'=>$e->getMessage().' Oleh : '.\Auth::User()->username]);
      return redirect(route('common-pokja-index'))->with('alert-fail', 'Gagal menyimpan data! Error : ' . $e->getMessage());
    }
  }

  public function updatePokja(Request $request)
  {
    try {
      DB::beginTransaction();

      $data = [
        'nama'                  => $request->nama,
        'id_jabatan'            => $request->jabatan,
        'email'                 => $request->email,
        'agama'                 => $request->agama,
        'no_telp'               => $request->no_telp,
        'alamat'                => $request->alamat,
        'tempat_lahir'          => $request->tempat_lahir,
        'tgl_lahir'             => $request->tgl_lahir,
        'kelamin'               => $request->kelamin,
        'pendidikan_terakhir'   => $request->pendidikan_terakhir,
        'jurusan_pendidikan'    => $request->jurusan_pendidikan,
        'profesi'               => $request->profesi,
        'unit_kerja'            => $request->unit_kerja,
        'alamat_kerja'          => $request->alamat_kerja,
        'nik'                   => $request->nik,
        'kata_sambutan'         => $request->kata_sambutan ? $request->kata_sambutan : null,
        'updated_at'            => date('Y-m-d H:i:s'),
        'updated_by'            => \Auth::User()->id,
      ];

      if ($request->hasFile('file_foto')) {
        $file = $request->file('file_foto');

        $a = Bantuan::validate_file($request->file('file_foto'), 'gambar');

        if ($a) {
          $nama_file = "foto-bunda/" . date('YmdHis') . "-" . uniqid() . "." . $file->extension();

          Storage::put($nama_file, file_get_contents($file));

          $data = array_merge($data, ['path_foto' => $nama_file]);
        }
      }

      ProfilPokja::where('id', $request->id)->update($data);

      DB::commit();
      return redirect(route('common-pokja-index'))->with('alert-success', 'Data Pokja berhasil diperbarui!');
    } catch (\Exception $e) {
      DB::rollback();
      // LogError::create(['fungsi'=>'updateProfil','lokasi'=>'CommonController','pesan'=>$e->getMessage().' Oleh : '.\Auth::User()->username]);
      return redirect(route('common-pokja-index'))->with('alert-fail', 'Gagal menyimpan data! Error : ' . $e->getMessage());
    }
  }

  public function deletePokja(Request $request)
  {
    try {
      DB::beginTransaction();

      ProfilPokja::where('id', $request->id)->delete();

      DB::commit();
      return Response()->json(['status' => 'success', 'message' => 'Data Pokja berhasil dihapus!']);
    } catch (\Exception $e) {
      DB::rollback();
      // LogError::create(['fungsi'=>'updateProfil','lokasi'=>'CommonController','pesan'=>$e->getMessage().' Oleh : '.\Auth::User()->username]);
      return Response()->json(['status' => 'failed', 'message' => 'Gagal menghapus data! Error : ' . $e->getMessage()]);
    }
  }

  // BUAT PROFIL POKJA

  public function createProfilPokja($data)
  {
    try {

      $tmp = [
        'nama'                  => $data->nama,
        'id_jabatan'            => $data->id_jabatan,
        'updated_at'            => date('Y-m-d H:i:s'),
        'updated_by'            => \Auth::User()->id,
        'tingkat'               => $data->tingkat,
        'id_kecamatan'          => $data->id_kecamatan,
        'id_kelurahan'          => $data->id_kelurahan,
      ];

      ProfilPokja::create($tmp);

      return true;
    } catch (\Exception $e) {
      return false;
    }
  }

  public function indexProfil()
  {
    $list_pendidikan = DB::connection('pgsql_paud')->table('jenjang_pendidikan')->select('nama')->get();

    return view('profil.index', compact('list_pendidikan'));
  }

  public function updateProfil(Request $request)
  {
    try {
      DB::beginTransaction();

      $data = [
        'nama'                  => $request->nama,
        'email'                 => $request->email,
        'agama'                 => $request->agama,
        'no_telp'               => $request->no_telp,
        'alamat'                => $request->alamat,
        'tempat_lahir'          => $request->tempat_lahir,
        'tgl_lahir'             => $request->tgl_lahir,
        'kelamin'               => $request->kelamin,
        'no_sk_penetapan'       => $request->no_sk_penetapan ? $request->no_sk_penetapan : null,
        'tgl_sk_penetapan'      => $request->tgl_sk_penetapan ? $request->tgl_sk_penetapan : null,
        'pejabat_sk_penetapan'  => $request->pejabat_sk_penetapan ? $request->pejabat_sk_penetapan : null,
        'pendidikan_terakhir'   => $request->pendidikan_terakhir ? $request->pendidikan_terakhir : null,
        'jurusan_pendidikan'    => $request->jurusan_pendidikan ? $request->jurusan_pendidikan : null,
        'kata_sambutan'         => $request->kata_sambutan ? $request->kata_sambutan : null,
        'verified'              => true
      ];

      if ($request->hasFile('file_sk_penetapan')) {
        $file = $request->file('file_sk_penetapan');

        $a = Bantuan::validate_file($request->file('file_sk_penetapan'), 'gambar');

        if ($a) {
          if (\Auth::User()->path_sk_penetapan && Storage::exists(\Auth::User()->path_sk_penetapan)) {
            Storage::delete(\Auth::User()->path_sk_penetapan);
          }

          $nama_file = "file-sk/" . date('YmdHis') . "-" . uniqid() . "." . $file->extension();

          Storage::put($nama_file, file_get_contents($file));

          $data = array_merge($data, ['path_sk_penetapan' => $nama_file]);
        }
      }

      DB::table('users')->where('id', \Auth::User()->id)->update($data);

      DB::commit();
      return redirect(route('common-profil-index'))->with('alert-success', 'Data user berhasil diperbarui!');
    } catch (ModelNotFoundException $e) {
      DB::rollback();
      LogError::create(['fungsi' => 'updateProfil', 'lokasi' => 'CommonController', 'pesan' => $e->getMessage() . ' Oleh : ' . \Auth::User()->username]);
      return redirect(route('common-profil-index'))->with('alert-fail', 'Data user tidak ditemukan!');
    } catch (\Exception $e) {
      DB::rollback();
      LogError::create(['fungsi' => 'updateProfil', 'lokasi' => 'CommonController', 'pesan' => $e->getMessage() . ' Oleh : ' . \Auth::User()->username]);
      return redirect(route('common-profil-index'))->with('alert-fail', 'Gagal mengupdate data! Error : ' . $e->getMessage());
    }
  }

  public function updateAkun(Request $request)
  {
    $request->validate(
      [
        'password' => 'required|min:6',
        'repassword' => 'required|min:6|same:password'
      ],
      [
        'min' => 'Password minimal 6 karakter!',
        'same' => 'Password harus sama!',
        'required' => 'Password tidak boleh kosong!'
      ]
    );

    try {
      if ($request->ajax()) {
        $data = User::findOrFail(\Auth::User()->id);
        if (Hash::check($request->oldpassword, $data->password)) {
          $data->password = Hash::make($request->password);
          $data->save();
        } else {
          throw new \Exception('Password lama salah!', 403);
        }
        return Response()->json(['status' => 'success', 'message' => 'Data user berhasil diperbarui!']);
      } else {
        throw new \Exception('Metode tidak diizinkan!', 403);
      }
    } catch (ModelNotFoundException $e) {
      DB::rollback();
      LogError::create(['fungsi' => 'updateAkun', 'lokasi' => 'CommonController', 'pesan' => $e->getMessage() . ' Oleh : ' . \Auth::User()->username]);
      return Response()->json(['status' => 'failed', 'message' => 'Data user tidak ditemukan!']);
    } catch (\Exception $e) {
      DB::rollback();
      LogError::create(['fungsi' => 'updateAkun', 'lokasi' => 'CommonController', 'pesan' => $e->getMessage() . ' Oleh : ' . \Auth::User()->username]);
      return Response()->json(['status' => 'failed', 'message' => 'Gagal memperbarui user! Error : ' . $e->getMessage()]);
    }
  }

  public function updateFoto(Request $request)
  {
    $request->validate(
      [
        'file' => 'required|max:550'
      ],
      [
        'max' => 'Ukuran file maksimal 500kb!',
        'required' => 'File tidak boleh kosong!'
      ]
    );

    try {
      if ($request->ajax()) {
        $data = ProfilPokja::find($request->id);
        $foto = $request->file('file');

        $a = Bantuan::validate_file($request->file('file'), 'gambar');

        if ($a) {
          if ($data->path_foto && Storage::exists($data->path_foto)) {
            Storage::delete($data->path_foto);
          }

          $nama_file = "foto-profil/" . date('Ymd') . "-" . uniqid() . "." . $foto->extension();
          Storage::put($nama_file, file_get_contents($foto));

          $data->path_foto = $nama_file;
          $data->save();

          return Response()->json(['status' => 'success', 'message' => 'Data user berhasil diperbarui!']);
        } else {
          return 'Format file tidak didukung';
        }
      } else {
        throw new \Exception('Metode tidak diizinkan!', 403);
      }
    } catch (\Exception $e) {
      LogError::create(['fungsi' => 'updateFoto', 'lokasi' => 'CommonController', 'pesan' => $e->getMessage(), 'created_by' => \Auth::User()->id]);
      return Response()->json(['status' => 'failed', 'message' => 'Gagal memperbarui user! Error : ' . $e->getMessage()]);
    }
  }

  public function storage($path, $render)
  {
    try {
      $path = base64_decode($path);
      if ($render == 1) {
        $header = get_headers('https://dispendik.surabaya.go.id/sibunda_file/' . $path, 1);
        $header1 = get_headers('http://103.76.17.11/sibunda_file/' . $path, 1);
        if ($path) {
          if ($header[0] == 'HTTP/1.1 200 OK') {
            $file = Redirect::to('https://dispendik.surabaya.go.id/sibunda_file/' . $path);
          } else if ($header1[0] == 'HTTP/1.1 200 OK') {
            $file = Redirect::to('http://103.76.17.11/sibunda_file/' . $path);
          } else {
            try {
              if (Storage::exists($path)) {
                $file = Storage::get($path);
                $type = Storage::mimeType($path);
                $file = Response::make($file, 200);

                $file->header("Content-Type", $type);
              } else {
                $file = "<h1>File Tidak ditemukan</h1>";
              }
            } catch (\Exception $e) {
              $file = "<h1>Koneksi server bermasalah</h1>";
            }
          }
        }
      } else if ($render == 2) {
        try {
          if (Storage::disk('ftp_profil')->exists($path)) {
            $file = Storage::disk('ftp_profil')->get($path);
            $type = Storage::disk('ftp_profil')->mimeType($path);
            $file = Response::make($file, 200);

            $file->header("Content-Type", $type);
          } else {
            $file = "<h1>File Tidak ditemukan</h1>";
          }
        } catch (\Exception $e) {
          $file = "<h1>Koneksi server bermasalah</h1>";
        }
      } else {
        try {
          if (Storage::exists($path)) {
            $file = Storage::get($path);
          } else {
            $file = "<h1>File Tidak ditemukan</h1>";
          }
        } catch (\Exception $e) {
          $file = "<h1>Koneksi server bermasalah</h1>";
        }
      }

      return $file;
    } catch (\Exception $e) {
      return $file = "<h1>File Tidak ditemukan</h1>";
    }
  }

  public function getList(Request $request)
  {
    try {
      if ($request->ajax()) {
        $sekolah = Sekolah::where('npsn', $request->npsn)->select('npsn', 'nama')->first();
        if ($request->jenis == 'pd') {
          $data = Siswa::whereRaw("alumni is not true and status_siswa = 'Aktif'")->where('npsn', $request->npsn)
            ->select('nik', 'nama', 'no_akta', 'tgl_lahir', 'kota', 'kelas', 'aga_id', 'tinggi_badan', 'berat_badan', 'kelamin')
            ->orderBy('kelas')
            ->orderBy('nama')
            ->with('agama')
            ->get();
          $view = view('landing.daftar-sekolah.detail_pd', compact('data', 'sekolah'))->render();
        } else {
          $data = Pegawai::whereRaw("keterangan = 'Aktif'")->where('npsn', $request->npsn)
            ->select('no_ktp as nik', 'nama', 'tgl_lahir', 'pendidikan_terakhir', 'jabatan', 'induk', 'luar_kota')
            ->orderBy('nama')
            ->with('jenjang_pendidikan', 'jabatan_ptk')
            ->get();
          $view = view('landing.daftar-sekolah.detail_ptk', compact('data', 'sekolah'))->render();
        }
        return Response()->json(['status' => 'success', 'view' => $view]);
      } else {
        throw new \Exception('Metode tidak diizinkan!', 403);
      }
    } catch (\Exception $e) {
      // LogError::create(['fungsi'=>'getList','lokasi'=>'CommonController','pesan'=>$e->getMessage().' Oleh : '.\Auth::User()->username]);
      return Response()->json(['status' => 'failed', 'message' => 'Gagal mencari data! Error : ' . $e->getMessage()]);
    }
  }

  public function materi()
  {
    $materis = Materi::get();
    return view('common.materi.index', compact('materis'));
  }
}
