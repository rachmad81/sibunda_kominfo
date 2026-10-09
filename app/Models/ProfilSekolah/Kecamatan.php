<?php

namespace App\Models\ProfilSekolah;

use Illuminate\Database\Eloquent\Model;

class Kecamatan extends Model
{
    protected $connection = 'db_sd';
    protected $table = 'kecamatan';
    protected $fillable = array('kecamatan_id', 'kecamatan_name', 'kecamatan_kode', 'wilayah');
    public $timestamps = false;
    protected $primaryKey = 'kecamatan_kode';
    public $keyType = 'string';

    // protected $casts = [
    //     'kecamatan_kode'=>'string'
    // ];

    public function kelurahan()
    {
        return $this->hasMany('App\Models\ProfilSekolah\Kelurahan', 'kecamatan_kode')
            ->orderBy('kelurahan_name');
    }
}
