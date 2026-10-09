<?php

namespace App\Models\Puskesmas;

use Illuminate\Database\Eloquent\Model;

class Peserta extends Model
{
  protected $table = 'kegiatan_pendukung.kunjungan_puskesmas_peserta';
  protected $primaryKey = 'id';
  protected $connection = 'pgsql_paud';

  public $timestamps = false;

  public function kunjungan()
  {
    return $this->belongsTo(Kunjungan::class, 'kunjungan_puskesmas_id');
  }
}
