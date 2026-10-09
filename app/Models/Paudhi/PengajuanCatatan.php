<?php

namespace App\Models\Paudhi;

use Illuminate\Database\Eloquent\Model;

class PengajuanCatatan extends Model
{
  protected $table = 'paud_hi.riwayat_catatan_pengajuan';
  protected $primaryKey = 'id';
  protected $connection = 'pgsql_paud';

  public $timestamps = false;
}
