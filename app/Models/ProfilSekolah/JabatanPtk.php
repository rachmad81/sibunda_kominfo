<?php

namespace App\Models\ProfilSekolah;

use Illuminate\Database\Eloquent\Model;

class JabatanPtk extends Model
{
  protected $connection = 'pgsql_paud';
  protected $table = 'jabatan_ptk';
  protected $fillable = array('jabatan_ptk_id', 'kode_dikdas', 'nama', 'kode_dikmen', 'jenis_ketenagaan', 'jabatan_pegawai', 'tenaga_administrasi');
  public $timestamps = false;
  protected $primaryKey = 'jabatan_ptk_id';
}
