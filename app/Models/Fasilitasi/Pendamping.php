<?php

namespace App\Models\Fasilitasi;

use Illuminate\Database\Eloquent\Model;

class Pendamping extends Model
{
  protected $table = 'kegiatan_pendukung.permohonan_fasilitasi_pendampings';
  protected $primaryKey = 'id';
  protected $connection = 'pgsql_paud';

  public $timestamps = false;
}
