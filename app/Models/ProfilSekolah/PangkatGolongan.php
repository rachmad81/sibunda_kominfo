<?php

namespace App\Models\ProfilSekolah;

use Illuminate\Database\Eloquent\Model;

class PangkatGolongan extends Model
{
  protected $connection = 'pgsql_paud';
  protected $table = 'pangkat_golongan';
  protected $fillable = array('pangkat_golongan_id', 'kode', 'nama', 'kode_dikdas', 'kode_dikmen', 'golongan');
  public $timestamps = false;
  protected $primaryKey = 'pangkat_golongan_id';
}
