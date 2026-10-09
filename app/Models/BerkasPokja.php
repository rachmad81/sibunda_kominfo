<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BerkasPokja extends Model
{
    use HasFactory;
    public $connection = 'pgsql';
    protected $table = 'berkas_pokja';
    
    public $timestamps = false;
    protected $fillable = [
        'tingkat',
        'id_kecamatan',
        'id_kelurahan',
        'no_sk_penetapan_bunda',
        'tgl_sk_penetapan_bunda',
        'pejabat_sk_penetapan_bunda',
        'path_sk_penetapan_bunda',
        'no_sk_penetapan_pokja',
        'tgl_sk_penetapan_pokja',
        'pejabat_sk_penetapan_pokja',
        'path_sk_penetapan_pokja',
        'updated_by',
        'updated_at',
    ];
}
