<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfilPokjaN extends Model
{
    public $connection = 'pgsql';
    protected $table = 'profil_pokja';
    public $timestamps = false;
}
