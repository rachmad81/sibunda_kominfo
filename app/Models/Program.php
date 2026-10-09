<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    use HasFactory;
    protected $table = 'public.program';
    protected $primaryKey = 'id_program';
    public $timestamps = false;
    protected $connection = 'pgsql';
    protected $fillable = [
        'bidang',
        'id_program',
        'waktu',
        'nama_kegiatan',
        'sasaran',
        'tujuan',
        'tahun',
        'updated_by',
        'updated_at',
        'id_kecamatan',
        'id_kelurahan',
        'tingkat'
    ];
}
