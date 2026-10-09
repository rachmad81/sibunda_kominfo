<?php

namespace App\Models\Fasilitasi;

use Illuminate\Database\Eloquent\Model;

class Jadwal extends Model
{
  protected $table = 'kegiatan_pendukung.permohonan_fasilitasi_jadwals';
  protected $primaryKey = 'id';
  protected $connection = 'pgsql_paud';

  public $timestamps = false;
}
