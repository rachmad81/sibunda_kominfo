<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ProfilSekolah\Kelurahan;
use App\Models\ProfilSekolah\Kecamatan;

class Ijop extends Model
{
    use HasFactory;
    protected $connection = 'pgsql';
    public $timestamps = false;
    protected $table = 'ijops';
    protected $fillable = [
        'no_izin_operasional',
        'tgl_izin_operasional',
        'mulai_berlaku',
        'selesai_berlaku',
        'izin_online',
        'nama_sekolah',
        'npsn',
        'kecamatan_name',
        'kecamatan_kode',
        'kelurahan_name',
        'kelurahan_kode',
        'order_izin_online',
        'str_izin_online',
        'order_mati_ijob_2',
        'str_mati_ijob_2',
        'order_mati_ijob_1',
        'str_mati_ijob_1',
        'order_akan_mati',
        'str_akan_mati',
    ];

    protected $cast = [
        'tgl_izin_operasional'  => 'date',
        'mulai_berlaku'         => 'date',
        'selesai_berlaku'       => 'date'
    ];

    public function kecamatan(){
        return $this->belongsTo(Kecamatan::class, 'kecamatan_kode','kecamatan_kode');
    }

    public function kelurahan(){
        return $this->belongsTo(Kelurahan::class, 'kelurahan_kode','kelurahan_kode');
    }

    public function getStatus(){
        if (!$this->izin_online) {
            return 'Tidak Terdaftar Ijop';
        }
        else if( strlen($this->str_mati_ijob_2) > 1 ){
            return 'Non-Aktif > 1 Th';
        }
        else if( strlen($this->str_mati_ijob_1) > 1 ){
            return 'Tidak Aktif';
        }
        else if( strlen($this->str_akan_mati) > 1 ){
            return 'Akan Mati < 4 Bln';
        }
        else{
            return 'Aktif';
        }
    }

    public function getStatusClass(){
        if (!$this->izin_online || strlen($this->str_mati_ijob_2) > 1 || strlen($this->str_mati_ijob_1) > 1) {
            return 'bg-danger blink';
        }
        else if( strlen($this->str_akan_mati) > 1 ){
            return 'bg-warning blink';
        }
        else{
            return '';
        }
    }

    public function monitoringIjop(){
        return $this->hasMany(MonitoringIjop::class,'npsn','npsn');
    }

    public function hasilMonitoring($keterangan = null){
        if ( count($this->monitoringIjop) > 0 ) {
            $data = $this->monitoringIjop()->orderBy('id','desc')->first();
            return $keterangan ? $data->getKeterangan() : $data->hasil;
        }
        else{
            return '-';
        }
    }
}
