<?php

namespace App\Models\Paudhi;

use Illuminate\Database\Eloquent\Model;

class Parenting extends Model
{

  protected $table = 'public.parenting_satuan';
  protected $primaryKey = 'id';
  protected $connection = 'pgsql_paud';
}
