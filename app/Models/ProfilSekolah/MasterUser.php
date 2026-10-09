<?php

namespace App\Models\ProfilSekolah;

use Illuminate\Database\Eloquent\Model;

class MasterUser extends Model
{
  protected $connection = 'pgsql_paud';
  protected $table = 'master_user';
  protected $fillable = array('npsn', 'username', 'default_password', 'user_password', 'level_id', 'insert_date', 'update_date', 'last_login', 'user_enable', 'nama_sekolah', 'agama_sekolah');
  public $timestamps = false;
  protected $primaryKey = 'npsn';
}
