<?php

namespace App\Models\Fasilitasi;

use Illuminate\Database\Eloquent\Model;

class Materi extends Model
{
  protected $table = 'kegiatan_pendukung.permohonan_fasilitasi_materis';
  protected $primaryKey = 'id';
  protected $connection = 'pgsql_paud';

  public $timestamps = false;
}
