<?php

namespace App\Models\Edu;

use Illuminate\Database\Eloquent\Model;

class Pendamping extends Model
{
  protected $table = 'kegiatan_pendukung.edu_city_tour_pendampings';
  protected $primaryKey = 'id';
  protected $connection = 'pgsql_paud';
}
