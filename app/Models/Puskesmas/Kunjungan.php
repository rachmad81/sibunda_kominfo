<?php

namespace App\Models\Puskesmas;

use Illuminate\Database\Eloquent\Model;

class Kunjungan extends Model
{
  protected $table = 'kegiatan_pendukung.kunjungan_puskesmas';
  protected $primaryKey = 'id';
  protected $connection = 'pgsql_paud';

  public $timestamps = false;

  public function peserta()
  {
    return $this->hasMany(Peserta::class, 'kunjungan_puskesmas_id');
  }
}
