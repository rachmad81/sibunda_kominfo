<?php

namespace App\Models\Pelatihan;

use Illuminate\Database\Eloquent\Model;

class Jadwal extends Model
{
  protected $table = 'kegiatan_pendukung.pelatihan_jadwals';
  protected $primaryKey = 'id';
  protected $connection = 'pgsql_paud';

  public $timestamps = false;
}
