<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApresiasiIndikatorSkor extends Model
{
    use HasFactory;
    public $connection = 'pgsql';
    protected $table = 'apresiasi_indikator_skor';
    
    public $timestamps = false;
    protected $fillable = [
        'tahun',
        'indikator',
        'skor'
    ];
}
