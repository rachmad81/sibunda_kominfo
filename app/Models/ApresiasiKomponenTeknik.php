<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApresiasiKomponenTeknik extends Model
{
    use HasFactory;
    public $connection = 'pgsql';
    protected $table = 'apresiasi_komponen_teknik';
    
    public $timestamps = false;
    protected $fillable = [
        'tahun',
        'komponen',
        'bobot'
    ];
}
