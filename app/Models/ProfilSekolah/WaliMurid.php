<?php

namespace App\Models\ProfilSekolah;

use Illuminate\Database\Eloquent\Model;

class WaliMurid extends Model
{
  protected $connection = 'pgsql_paud';
  protected $table = 'wali_murid';
  protected $primaryKey = 'id_siswa';
  public $timestamps = false;
  protected $fillable = array('nama_ayah', 'thn_lahir_ayah', 'pekerjaan_ayah', 'pendidikan_ayah', 'penghasilan_ayah', 'nama_ibu', 'thn_lahir_ibu', 'pendidikan_ibu', 'penghasilan_ibu', 'pekerjaan_ibu', 'nama_wali', 'thn_lahir_wali', 'pekerjaan_wali', 'pendidikan_wali', 'penghasilan_wali', 'jenis_tinggal', 'alamat_rumah', 'rt', 'rw', 'kel_id', 'kec_id', 'kab_id', 'kodepos', 'id_siswa', 'npsn', 'alumni', 'nik_ayah', 'nik_ibu', 'id_wali', 'no_kk', 'nik_wali');
}
