<?php

namespace App\Models\ProfilSekolah;

use Illuminate\Database\Eloquent\Model;
use App\Models\Perizinan\IzinOperasional;
use App\Models\Simbas\BangunanSekolah;

class Sekolah extends Model
{
  protected $connection = 'pgsql_paud';
  protected $table = 'sekolah';
  protected $fillable = array('npsn', 'nss', 'nis', 'nama', 'kepala', 'tgl_pendirian', 'geo_id', 'klp_id', 'status', 'akreditasi', 'yayasan_nama', 'yayasan_alamat', 'yayasan_kelompok', 'yayasan_pimpinan', 'jml_rombel', 'jml_guru', 'jml_tendik', 'jml_murid', 'alamat', 'kab_id', 'kec_id', 'desa', 'kodepos', 'telp', 'fax', 'tis_kode', 'last_update', 'email', 'website', 'tgl_pengisian', 'pilih', 'notes1', 'notes2', 'notes3', 'last_impor', 'sync', 'sync_gen', 'sync_last_down', 'sync_last_up', 'apr_status', 'apr_date', 'apr_by', 'peta', 'logo', 'sertifikasi', 'visi', 'misi', 'jml_pegawai', 'id_sekolah', 'no_sekolah', 'jenjang', 'excel_opt', 'sekolah_id', 'kode_registrasi', 'inklusi', 'rt', 'rw', 'tingkat', 'kawasan', 'is_gaji', 'rek_sekolah', 'is_kurtilas', 'excel_guru', 'is_bos', 'gugus_inti', 'kepemilikan', 'id_yayasan', 'operator_sekolah', 'is_spk', 'tahun_akreditasi', 'tahun_akreditasi', 'lat_long', 'npwp');
  public $timestamps = false;
  protected $primaryKey = 'npsn';
  public $incrementing = false;
  public $keyType = 'string';

  public function getJumlahSiswa()
  {
    return count($this->siswa);
  }

  public function getJumlahPegawai()
  {
    return count($this->pegawai);
  }

  public function getFormatedTelp()
  {
    $tmp = str_replace("-", "", trim($this->telp));
    $tmp = strtr($tmp, array('(' => '', ')' => ''));
    return $tmp;
  }

  public function Bangunan()
  {
    return $this->hasOne(BangunanSekolah::class, 'npsn')->select('id_gedung', 'npsn', 'foto_depan');
  }

  public function pegawai()
  {
    return $this->hasMany('App\Models\ProfilSekolah\Pegawai', 'npsn')->where('keterangan', 'Aktif')
      ->select('peg_id', 'nama', 'keterangan', 'tgl_lahir', 'no_ktp', 'hp', 'npsn', 'jabatan', 'pendidikan_terakhir', 'induk', 'pns', 'luar_kota')
      ->orderBy('nama');
  }

  public function siswa()
  {
    return $this->hasMany('App\Models\ProfilSekolah\Siswa', 'npsn')->whereRaw("status_siswa=? and alumni is not true", array('Aktif'))
      ->select('id_siswa', 'nama', 'kelamin', 'tempat_lahir', 'tgl_lahir', 'kelas', 'npsn', 'nik', 'rombel', 'kebutuhan_khusus', 'status_siswa')
      ->orderBy('nama');
  }

  public function kecamatan()
  {
    return $this->belongsTo(Kecamatan::class, 'kec_id')->select('kecamatan_kode', 'kecamatan_name');
  }

  public function kelurahan()
  {
    return $this->belongsTo(Kelurahan::class, 'desa')->select('kelurahan_kode', 'kelurahan_name');;
  }

  public function kepsek()
  {
    return $this->hasOne(Pegawai::class, 'npsn', 'npsn')->where('keterangan', 'Aktif')->where('jabatan', '2')
      ->select('peg_id', 'nama', 'keterangan', 'tgl_lahir', 'no_ktp as nik', 'hp', 'npsn', 'jabatan', 'pendidikan_terakhir', 'alamat', 'pns', 'luar_kota');
  }

  public function kepsek_plt()
  {
    return $this->hasOne(Pegawai::class, 'sekolah_plt', 'npsn')->where('keterangan', 'Aktif')->where('jabatan', '2')->where('status_plt', true)
      ->select('peg_id', 'nama', 'keterangan', 'tgl_lahir', 'no_ktp as nik', 'hp', 'npsn', 'jabatan', 'pendidikan_terakhir', 'alamat', 'pns', 'luar_kota', 'sekolah_plt');
  }

  public function master_user()
  {
    return $this->belongsTo(MasterUser::class, 'npsn');
  }

  public function perijinan_npsn_lama()
  {
    return $this->hasMany(IzinOperasional::class, 'npsn')
      ->whereRaw("izin_status='setuju' and tgl_berakhir_baru is not null")
      ->select('npsn', 'no_sk_perubahan', 'no_sk_baru', 'tgl_sk_perubahan', 'tgl_disetujui', 'tgl_operasional_baru', 'tgl_berakhir_baru')
      ->orderBy('tgl_berakhir_baru', 'desc');
  }

  public function perijinan_npsn_baru()
  {
    return $this->hasMany(IzinOperasional::class, 'npsn_sekarang')
      ->whereRaw("izin_status='setuju' and tgl_berakhir_baru is not null")
      ->select('npsn', 'no_sk_perubahan', 'no_sk_baru', 'tgl_sk_perubahan', 'tgl_disetujui', 'tgl_operasional_baru', 'tgl_berakhir_baru')
      ->orderBy('tgl_berakhir_baru', 'desc');
  }

  public function perijinan()
  {
    if (count($this->perijinan_npsn_lama) < 1) {
      return $this->perijinan_npsn_baru()->first();
    }
    return $this->perijinan_npsn_lama()->first();
  }

  public function getIjop()
  {
    $data = [];
    $data['no_izin_operasional'] = null;
    $data['tgl_izin_operasional'] = null;
    $data['mulai_berlaku'] = null;
    $data['selesai_berlaku'] = null;
    $data['izin_online'] = false;

    if ($this->perijinan()) {
      $ijin = $this->perijinan();

      if ($ijin->no_sk_baru) {
        if ($ijin->no_sk_perubahan) {
          $data['no_izin_operasional'] = $ijin->no_sk_perubahan;
        } else {
          $data['no_izin_operasional'] = $ijin->no_sk_baru;
        }
      } else {
        $data['no_izin_operasional'] = null;
      }
      if ($ijin->no_sk_baru) {
        if ($ijin->tgl_sk_perubahan) {
          $data['tgl_izin_operasional'] = $ijin->tgl_sk_perubahan;
        } else {
          $data['tgl_izin_operasional'] = $ijin->tgl_disetujui;
        }
      } else {
        $data['tgl_izin_operasional'] = null;
      }
      $data['mulai_berlaku'] = $ijin->tgl_operasional_baru;
      $data['selesai_berlaku'] = $ijin->tgl_berakhir_baru;
      $data['izin_online'] = true;
    }

    return $data;
  }
}
