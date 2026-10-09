<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApresiasiPengajuanDetail extends Model
{
    use HasFactory;
    public $connection = 'pgsql';
    protected $table = 'apresiasi_pengajuan_detail';
    
    // public $timestamps = false;
    protected $fillable = [
        'id_pengajuan',
        'id_komponen'
    ];

    public function Komponen(){
        return $this->belongsTo(ApresiasiKomponenTeknik::class,'id_komponen');
    }

    public function Detail(){
        return $this->hasMany(ApresiasiPengajuanDetailKegiatan::class,'id_pengajuan_detail');
    }
}
