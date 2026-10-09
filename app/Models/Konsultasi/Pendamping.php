<?php

namespace App\Models\Konsultasi;

use Illuminate\Database\Eloquent\Model;

class Pendamping extends Model
{
  protected $table = 'kegiatan_pendukung.konsultasi_paud_hi_pendampings';
  protected $primaryKey = 'id';
  protected $connection = 'pgsql_paud';

  public $timestamps = false;
}
