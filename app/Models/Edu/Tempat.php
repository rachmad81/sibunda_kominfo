<?php

namespace App\Models\Edu;

use Illuminate\Database\Eloquent\Model;

class Tempat extends Model
{
  protected $table = 'kegiatan_pendukung.edu_city_tour_tempats';
  protected $primaryKey = 'id';
  protected $connection = 'pgsql_paud';

  public $timestamps = false;
}
