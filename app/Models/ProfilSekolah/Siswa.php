<?php

namespace App\Models\ProfilSekolah;

use Illuminate\Database\Eloquent\Model;
use DB;

class Siswa extends Model
{
  protected $connection = 'pgsql_paud';
  protected $table = 'siswa';
  protected $fillable = array('id_siswa', 'nisn', 'nama', 'kelamin', 'tempat_lahir', 'tgl_lahir', 'alamat', 'kelas', 'npsn', 'tgl_insert', 'tgl_update', 'nss', 'foto', 'kota', 'jurusan', 'status_asli', 'status_mutasi', 'status_lainnya', 'prestasi', 'nama_ortu', 'alamat_ortu', 'nis', 'nik', 'aga_id', 'rombel', 'tinggi_badan', 'berat_badan', 'kebutuhan_khusus', 'telpon', 'seluler', 'jarak_rmh_sekolah', 'jarak_rmh_sekolah_km', 'transportasi', 'email', 'kel_siswa', 'kec_siswa', 'kab_siswa', 'passwd', 'level_id', 'last_login', 'user_enable', 'nisn_sementara', 'asal_sekolah', 'no_ijasah', 'tahun_ijasah', 'program_pengajaran_id', 'status_siswa', 'alasan_keluar', 'sekolah_mutasi', 'npsn_asal', 'password_rapor', 'ukuran', 'sktm', 'sepatu', 'kps', 'gakin', 'anakke', 'alumni', 'konselor', 'alamat_domisili', 'kel_domisili', 'kec_domisili', 'kab_domisili', 'rt_domisili', 'rw_domisili', 'kodepos_domisili', 'tahun_lulus', 'sekolah_lanjutan', 'no_akta', 'siswa_id', 'keterangan_nik', 'status_anak', 'keterangan_gakin', 'isklk', 'ver_foto', 'tempat_ibadah', 'surat_keterangan_lahir');
  public $timestamps = false;
  protected $primaryKey = 'id_siswa';

  public function sekolah()
  {
    return $this->belongsTo('App\Models\ProfilSekolah\Sekolah', 'npsn')->select('npsn', 'nama', 'kec_id', 'desa', 'alamat')->with('kecamatan', 'kelurahan');
  }

  public function agama()
  {
    return $this->belongsTo('App\Models\ProfilSekolah\Agama', 'aga_id');
  }

  public function wali_murid()
  {
    return $this->hasOne('App\Models\ProfilSekolah\WaliMurid', 'id_siswa');
  }

  public function kecamatan()
  {
    return $this->belongsTo(Kecamatan::class, 'kec_siswa')->select('kecamatan_kode', 'kecamatan_name');
  }

  public function kelurahan()
  {
    return $this->belongsTo(Kelurahan::class, 'kel_siswa')->select('kelurahan_kode', 'kelurahan_name');;
  }

  public function wali()
  {
    return $this->hasOne('App\Models\ProfilSekolah\WaliMurid', 'id_siswa')
      ->select('id_siswa', DB::raw("REPLACE(nama_ayah,'&apos;','`') as nama_ayah"), DB::raw("REPLACE(nama_ibu,'&apos;','`') as nama_ibu"), DB::raw("REPLACE(nama_wali,'&apos;','`') as nama_wali"));
  }

  public function getOrangTua()
  {
    if ($this->wali) {
      $data = [
        'ayah'  => $this->wali->nama_ayah,
        'ibu'  => $this->wali->nama_ibu,
      ];
    } else {
      $data = [
        'ayah'  => '-',
        'ibu'  => '-',
      ];
    }

    return $data;
  }
}
