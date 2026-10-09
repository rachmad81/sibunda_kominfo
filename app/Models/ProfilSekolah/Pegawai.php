<?php

namespace App\Models\ProfilSekolah;

use Illuminate\Database\Eloquent\Model;
use DB;

class Pegawai extends Model
{
  protected $connection = 'pgsql_paud';
  protected $table = 'pegawai';
  protected $fillable = array('peg_id', 'nama', 'gelar', 'gelar2', 'nip', 'keterangan', 'tempat_lahir', 'tgl_lahir', 'jk', 'aga_id', 'status_kawin', 'jumlah_anak', 'pns', 'gol_id', 'tmt_gol', 'tmt_pns', 'tmt_tugas', 'no_ktp', 'alamat', 'kelurahan', 'kodepos', 'kec_id', 'kab_id', 'telp', 'hp', 'pasangan', 'pasangan_pns', 'pasangan_nip', 'nama_ibu', 'tgl_pengisian', 'bank_norek', 'bank_nama', 'bank_cabang', 'foto', 'npsn', 'luar_kota', 'nik', 'nuptk', 'jabatan', 'sertifikasi_tahun', 'sertifikasi_no', 'sertifikasi_nrg', 'gelar_depan_dapodik', 'gelar_belakang_dapodik', 'sertifikasi_nopeserta', 'sertifikasi_bidang_studi', 'pasangan_gol', 'pendidikan_terakhir', 'thn_lulus_pendidikan', 'npwp', 'sk_inpassing', 'tmt_sk_inpassing', 'masa_kerja_tahun', 'masa_kerja_bulan', 'bank_namarek', 'jurusan', 'status_guru', 'status_plt', 'sekolah_plt', 'tmt_plt', 'hapus', 'idx', 'user_rapor', 'passwds', 'namatk', 'last_update', 'tgl_insert', 'username_login', 'email', 'rw_sma', 'rw_jur_sma', 'rw_smk', 'rw_jur_smk', 'rw_spg', 'rw_jur_spg', 'rw_sgo', 'rw_jur_sgo', 'rw_lain', 'rw_jur_lain', 'rw_d1', 'rw_jur_d1', 'rw_d2', 'rw_jur_d2', 'rw_d3', 'rw_jur_d3', 'rw_d4', 'rw_jur_d4', 'rw_s1', 'rw_jur_s1', 'rw_s2', 'rw_jur_s2', 'rw__tahun_cpns', 'rw_inst_cpns', 'rw_mapel_cpns', 'rw__tahun_pns', 'rw_inst_pns', 'rw_mapel_pns', 'rw__tahun_mutasi', 'rw_inst_mutasi', 'rw_mapel_mutasi', 'rw__tahun_sk', 'rw_inst_sk', 'rw_mapel_sk', 'induk', 'jenis_kepegawaian', 'no_kk', 'karpeg', 'verified_nik', 'catatan_verified_nik', 'nama_ktp', 'no_akta_lahir', 'nama_ayah', 'rt', 'rw');
  public $timestamps = false;
  protected $primaryKey = 'peg_id';

  protected $casts = [
    'jabatan' => 'integer',
    'gol_id' => 'integer',
    'npsn' => 'string'
  ];

  public function sekolah()
  {
    return $this->belongsTo(Sekolah::class, DB::raw('npsn'))->select('npsn', 'nama');
  }

  public function jabatan_ptk()
  {
    return $this->belongsTo('App\Models\ProfilSekolah\JabatanPtk', 'jabatan');
  }

  public function pangkat_golongan()
  {
    return $this->belongsTo('App\Models\ProfilSekolah\PangkatGolongan', 'gol_id');
  }

  public function jenjang_pendidikan()
  {
    return $this->belongsTo('App\Models\ProfilSekolah\JenjangPendidikan', 'pendidikan_terakhir');
  }

  public function agama()
  {
    return $this->belongsTo('App\Models\ProfilSekolah\Agama', 'aga_id');
  }
}
