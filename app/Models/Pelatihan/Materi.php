<?php

namespace App\Models\Pelatihan;

use Illuminate\Database\Eloquent\Model;

class Materi extends Model
{
  protected $table = 'kegiatan_pendukung.pelatihan_materis';
  protected $primaryKey = 'id';
  protected $connection = 'pgsql_paud';

  public $timestamps = false;
}
