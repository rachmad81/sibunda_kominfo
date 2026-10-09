<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApresiasiPengajuanDetailKegiatan extends Model
{
    use HasFactory;
    public $connection = 'pgsql';
    protected $table = 'apresiasi_pengajuan_detail_kegiatan';
    
    // public $timestamps = false;
    protected $fillable = [
        'id_pengajuan_detail',
        'id_kegiatan'
    ];

    public function Kegiatan(){
        return $this->belongsTo(Kegiatan::class,'id_kegiatan','id_kegiatan');
    }
}
