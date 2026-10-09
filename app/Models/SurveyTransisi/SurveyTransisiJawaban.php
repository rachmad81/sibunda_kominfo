<?php

namespace App\Models\SurveyTransisi;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SurveyTransisiJawaban extends Model
{
  use SoftDeletes;
  public $connection = 'pgsql';
  protected $table = "public.survey_transisi_jawaban";
  protected $fillable = [
    'id',
    'survey_transisi_id',
    // 'jenjang',
    // 'npsn',
    'nama',
    'kec_id',
    'kel_id',
    'jawaban',
    'keterangan',
    'bukti_dokumentasi',
    'verifikator_id',
    'verifikator_nama',
    'catatan_verifikator',
    'status_verifikasi',
    'timestamp_verifikasi',
    'created_at',
    'updated_at',
    'deleted_at',
    'bunda_id',
  ];

  public function survey()
  {
    return $this->belongsTo(SurveyTransisi::class, 'survey_transisi_id', 'id');
  }
}
