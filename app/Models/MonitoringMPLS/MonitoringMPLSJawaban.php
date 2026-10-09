<?php

namespace App\Models\MonitoringMPLS;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MonitoringMPLSJawaban extends Model
{
  use SoftDeletes;
  protected $table = "public.monitoring_mpls_jawaban";
  protected $fillable = [
    'id',
    'tipe',
    'monitoring_mpls_id',
    'npsn',
    'jawaban',
    'catatan_verifikasi',
    'created_at',
    'updated_at',
    'deleted_at',
  ];

  public function monitor()
  {
    return $this->belongsTo(MonitoringMPLS::class, 'monitoring_mpls_id', 'id');
  }
}
