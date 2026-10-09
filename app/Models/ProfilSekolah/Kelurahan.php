<?php

namespace App\Models\ProfilSekolah;

use Illuminate\Database\Eloquent\Model;

class Kelurahan extends Model
{
    protected $connection = 'db_sd';
    protected $table = 'kelurahan';
    protected $fillable = array('kelurahan_id', 'kelurahan_name', 'kelurahan_kode', 'kecamatan_kode');
    public $timestamps = false;
    protected $primaryKey = 'kelurahan_kode';
    public $keyType = 'string';

    public function kecamatan(){
        return $this->belongsTo(Kecamatan::class, 'kecamatan_kode', 'kecamatan_kode');;
    }
}
