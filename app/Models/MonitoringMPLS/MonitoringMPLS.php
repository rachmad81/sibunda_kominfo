<?php

namespace App\Models\MonitoringMPLS;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MonitoringMPLS extends Model
{
  use SoftDeletes;
  protected $table = "public.monitoring_mpls";
  protected $fillable = [
    'id',
    'tipe',
    'jenjang',
    'urutan',
    'parent_id',
    'is_shown_when_parent_is',
    'nomor',
    'pertanyaan',
    'list_jawaban',
    // 'jawaban',
    // 'keterangan',
    // 'bukti_dokumentasi',
    // 'skor',
    'checkbox',
    'created_at',
    'updated_at',
    'deleted_at',
  ];

  public function data()
  {
    return $this->hasOne(MonitoringMPLSData::class, 'tipe', 'tipe');
  }
  public function jawabanMonitor()
  {
    return $this->hasOne(MonitoringMPLSJawaban::class, 'monitoring_mpls_id', 'id');
  }
}
