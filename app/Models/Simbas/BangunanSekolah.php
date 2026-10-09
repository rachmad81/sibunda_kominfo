<?php

namespace App\Models\Simbas;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BangunanSekolah extends Model
{
    use HasFactory;
    protected $connection = 'db_simbas';
    public $timestamps = false;
    protected $table = 'bangunan_sekolah';
    protected $fillable = [
        'id_gedung',
        'no_imb',
        'tgl_imb',
        'an_dok_gedung',
        'jenis_dokumen_gedung',
        'kontruksi',
        'npsn',
        'bahan',
        'jumlah_lantai',
        'luas_lantai',
        'luas_bangunan',
        'tinggi_bangunan',
        'panjang_bangunan',
        'foto_depan',
        'is_delete',
    ];
}
