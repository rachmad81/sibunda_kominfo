<?php

namespace App\Models\ProfilSekolah;

use Illuminate\Database\Eloquent\Model;

class Agama extends Model
{
  protected $connection = 'pgsql_paud';
  protected $table = 'agama';
  protected $fillable = array('aga_id', 'aga_nama', 'kode_dikdas', 'kode_dikmen');
  public $timestamps = false;
  protected $primaryKey = 'aga_id';
}
