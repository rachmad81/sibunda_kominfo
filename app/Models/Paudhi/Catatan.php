<?php

namespace App\Models\Paudhi;

use Illuminate\Database\Eloquent\Model;

class Catatan extends Model
{
  protected $table = 'paud_hi.catatan_sibunda';
  protected $primaryKey = 'id';
  protected $connection = 'pgsql_paud';

  public $timestamps = false;
}
