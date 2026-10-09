<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kegiatan extends Model
{
    protected $connection = 'pgsql';
    protected $table = 'kegiatan';
    protected $primaryKey = 'id_kegiatan';
    public $timestamps = false;
    protected $fillable = [
        'id_kegiatan',
        'program_id',
        'tgl_kegiatan',
        'nama_kegiatan',
        'bentuk_keg',
        'materi',
        'narsum',
        'sumber_dana',
        'foto',
        'url_video',
        'updated_by',
        'updated_at'
    ];

    public function Pembuat(){
        return $this->belongsTo(User::class,'updated_by','username');
    }
}
