<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JabatanPokja extends Model
{
    use HasFactory;
    protected $connection = 'pgsql';
    public $timestamps = false;
    protected $table = 'jabatan_pokja';
    protected $fillable = [
        'tingkat',
        'nama_jabatan'
    ];
}
