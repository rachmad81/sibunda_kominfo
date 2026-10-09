<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApresiasiPengajuan extends Model
{
    use HasFactory;
    public $connection = 'pgsql';
    protected $table = 'apresiasi_pengajuan';
    
    // public $timestamps = false;
    protected $fillable = [
        'tingkat',
        'id_kecamatan',
        'id_kelurahan',
        'tahun',
        'id_bunda',
        'nama',
        'email',
        'alamat',
        'no_telp',
        'status',
        'keterangan',
        'created_by',
        'created_at',
        'updated_at',
    ];

    public function Detail(){
        return $this->hasMany(ApresiasiPengajuanDetail::class,'id_pengajuan');
    }
}
