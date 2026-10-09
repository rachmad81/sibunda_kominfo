<?php

namespace App\Models\Pelatihan;

use Illuminate\Database\Eloquent\Model;

class Pendamping extends Model
{
  protected $table = 'kegiatan_pendukung.pelatihan_pendampings';
  protected $primaryKey = 'id';
  protected $connection = 'pgsql_paud';

  public $timestamps = false;
}
