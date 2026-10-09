<?php

namespace App\Models\Paudhi;

use Illuminate\Database\Eloquent\Model;

class AbsenParenting extends Model
{

  protected $table = 'public.presensi_parenting_satuan';
  protected $primaryKey = 'id';
  protected $connection = 'pgsql_paud';
  public $timestamps = false;
}
