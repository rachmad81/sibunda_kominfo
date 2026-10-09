<?php

namespace App\Models\ProfilSekolah;

use Illuminate\Database\Eloquent\Model;

class JenjangPendidikan extends Model
{
  protected $connection = 'pgsql_paud';
  protected $table = 'jenjang_pendidikan';
  protected $fillable = array('jenjang_pendidikan_id', 'kode', 'nama');
  public $timestamps = false;
  protected $primaryKey = 'jenjang_pendidikan_id';
}
