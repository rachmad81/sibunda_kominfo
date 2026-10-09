<?php

namespace App\Models\SurveyTransisi;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SurveyTransisi extends Model
{
  use SoftDeletes;
  public $connection = 'pgsql';
  protected $table = "public.survey_transisi";
  protected $fillable = [
    'id',
    'tipe',
    'urutan',
    'jenjang',
    'pertanyaan',
    'jawaban',
    'keterangan',
    'bukti_dokumentasi',
    'skor',
    'checkbox',
    'created_at',
    'updated_at',
    'deleted_at',
  ];

  public function jawabanSurvey()
  {
    return $this->hasOne(SurveyTransisiJawaban::class, 'survey_transisi_id', 'id');
  }
}
