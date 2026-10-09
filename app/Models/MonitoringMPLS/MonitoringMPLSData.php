<?php

namespace App\Models\MonitoringMPLS;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MonitoringMPLSData extends Model
{
  use SoftDeletes;
  protected $table = "public.monitoring_mpls_data";
  protected $fillable = [
    'id',
    'tipe',
    'jenjang',
    'npsn',
    'nama',
    'kec_id',
    'kel_id',
    'kecamatan',
    'kelurahan',
    'timestamp_submit',
    'status_verifikasi',
    'timestamp_verifikasi',
    'catatan_verifikasi',
    'verifikator_id',
    'verifikator_nama',
    'created_at',
    'updated_at',
    'deleted_at',
  ];

  public function monitor()
  {
    return $this->belongsTo(MonitoringMPLS::class, 'monitoring_mpls_id', 'id');
  }
}
