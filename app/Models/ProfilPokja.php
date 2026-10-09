<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProfilPokja extends Model
{
    use HasFactory, SoftDeletes;
    public $connection = 'pgsql';
    protected $table = 'profil_pokja';
    public $timestamps = false;
    protected $fillable = [
        'tingkat',
        'id_kecamatan',
        'id_kelurahan',
        'nama',
        'id_jabatan',
        'email',
        'agama',
        'no_telp',
        'alamat',
        'tempat_lahir',
        'tgl_lahir',
        'kelamin',
        'path_foto',
        'pendidikan_terakhir',
        'jurusan_pendidikan',
        'kata_sambutan',
        'profesi',
        'unit_kerja',
        'alamat_kerja',
        'updated_by',
        'updated_at',
    ];


    public function jabatanPokja(){
        return $this->belongsTo(JabatanPokja::class, 'id_jabatan');;
    }
}
