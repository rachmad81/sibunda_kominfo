<?php

namespace App\Models\Edu;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Jadwal extends Model
{
  use SoftDeletes;
  protected $table = 'kegiatan_pendukung.edu_city_tour_jadwals';
  protected $primaryKey = 'id';
  protected $connection = 'pgsql_paud';
}
