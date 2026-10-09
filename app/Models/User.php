<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Storage;
use App\Models\ProfilSekolah\Kelurahan;
use App\Models\ProfilSekolah\Kecamatan;

class User extends Authenticatable
{
  use HasApiTokens, HasFactory, Notifiable, softDeletes;

  public $connection = 'pgsql';
  protected $fillable = [
    'username',
    'nama',
    'password',
    'role',
    'tingkat',
    'id_kecamatan',
    'id_kelurahan',
    'id_jabatan',
    'verified',
    'last_login',
    'created_at',
    'updated_at',
    'deleted_at',
    'remember_token'
  ];

  /**
   * The attributes that should be hidden for serialization.
   *
   * @var array
   */
  protected $hidden = [
    'password',
    'remember_token',
  ];

  public function kecamatan()
  {
    return $this->belongsTo(Kecamatan::class, 'id_kecamatan', 'kecamatan_id')->select('kecamatan_id', 'kecamatan_name', 'kecamatan_kode', 'kecamatan_dispenduk');
  }

  public function kelurahan()
  {
    return $this->belongsTo(Kelurahan::class, 'id_kelurahan', 'kelurahan_id')->select('kelurahan_id', 'kelurahan_name', 'kecamatan_kode', 'kelurahan_kode', 'kelurahan_dispenduk');;
  }

  public function jabatanPokja()
  {
    return $this->belongsTo(JabatanPokja::class, 'id_jabatan');;
  }

  public function jabatanTingkatBunda()
  {
    if ($this->tingkat == 'kota') {
      return 'Bunda PAUD Kota Surabaya';
    }
    if ($this->tingkat == 'kecamatan') {
      return 'Bunda PAUD ' . $this->kecamatan->kecamatan_name;
    } else {
      return 'Bunda PAUD Kel. ' . $this->kelurahan->kelurahan_name . ' (' . $this->kelurahan->kecamatan->kecamatan_name . ')';
    }
  }

  public function getTingkat()
  {
    if ($this->tingkat == 'kota') {
      return 'Kota Surabaya';
    }
    if ($this->tingkat == 'kecamatan') {
      return $this->kecamatan->kecamatan_name;
    } else {
      return 'Kel. ' . $this->kelurahan->kelurahan_name . '<br>(' . $this->kelurahan->kecamatan->kecamatan_name . ')';
    }
  }

  public function getRole()
  {
    if ($this->role == 'dinas') {
      return 'Dinas Pendidikan';
    } else if (in_array($this->role, ['k3_paud', 'paud_sd', 'opd', 'penilai', 'mitra'])) {
      return $this->nama;
    } else {
      return $this->jabatanPokja->nama_jabatan;
    }
  }

  public function getFotoProfil()
  {
    if (in_array($this->role, ['penilai', 'opd', 'k3_paud', 'paud_sd'])) {
      return null;
    }
    $data = ProfilPokja::whereRaw("id_jabatan = ? and tingkat = ?", [$this->id_jabatan, $this->tingkat])
      ->when($this->id_kecamatan, function ($q) {
        $q->where('id_kecamatan', $this->id_kecamatan);
      })
      ->when($this->id_kelurahan, function ($q) {
        $q->where('id_kelurahan', $this->id_kelurahan);
      })
      ->first();

    if ($data && $data->path_foto) {
      $header = get_headers('https://dispendik.surabaya.go.id/sibunda_file/' . $data->path_foto, 1);
      $header1 = get_headers('http://103.76.17.11/sibunda_file/' . $data->path_foto, 1);
      try {
        if ($header[0] == 'HTTP/1.1 200 OK') {
          return 'https://dispendik.surabaya.go.id/sibunda_file/' . $data->path_foto;
        } else if ($header1[0] == 'HTTP/1.1 200 OK') {
          return 'data:image/*;base64,' . base64_encode(file_get_contents('http://103.76.17.11/sibunda_file/' . $data->path_foto));
        } else {
          try {
            if (!Storage::exists($data->path_foto)) {
              return null;
            }
            $file = 'data:image/*;base64,' . base64_encode(Storage::get($data->path_foto));
            return $file;
          } catch (\Exception $e) {
            return url('assets/media/avatars/avatar15.jpg');
          }
        }
      } catch (\Throwable $th) {
        return url('assets/media/avatars/avatar15.jpg');
      }
    } else {
      return null;
    }
  }

  public function getNamaProfil()
  {
    if (in_array($this->role, ['penilai', 'opd', 'k3_paud', 'paud_sd'])) {
      return $this->nama;
    }
    $data = ProfilPokja::whereRaw("id_jabatan = ? and tingkat = ?", [$this->id_jabatan, $this->tingkat])
      ->when($this->id_kecamatan, function ($q) {
        $q->where('id_kecamatan', $this->id_kecamatan);
      })
      ->when($this->id_kelurahan, function ($q) {
        $q->where('id_kelurahan', $this->id_kelurahan);
      })
      ->first();

    if ($data) {
      return $data->nama;
    } else {
      return null;
    }
  }

  public function toEncoded()
  {
    $data = [
      'id'                    => $this->id,
      'username'              => $this->username,
      'nama'                  => $this->nama,
      'no_telp'               => $this->no_telp,
      'alamat'                => $this->alamat,
      'role'                  => $this->role,
      'tingkat'               => $this->tingkat,
      'id_kecamatan'          => $this->id_kecamatan,
      'id_kelurahan'          => $this->id_kelurahan,
      'id_jabatan'            => $this->id_jabatan,
      'nama'                  => $this->nama,
      'tempat_lahir'          => $this->tempat_lahir,
      'tgl_lahir'             => $this->tgl_lahir,
      'kelamin'               => $this->kelamin,
      'no_telp'               => $this->no_telp,
      'alamat'                => $this->alamat,
      'path_foto'             => $this->path_foto,
      'no_sk_penetapan'       => $this->no_sk_penetapan,
      'tgl_sk_penetapan'      => $this->tgl_sk_penetapan,
      'path_sk_penetapan'     => $this->path_sk_penetapan,
      'pendidikan_terakhir'   => $this->pendidikan_terakhir,
      'jurusan_pendidikan'    => $this->jurusan_pendidikan,
      'kata_sambutan'         => $this->kata_sambutan,
    ];

    return json_encode($data);
  }
}
