<?php

namespace App\Models\Paudhi;

use Illuminate\Database\Eloquent\Model;

class PengajuanPaudHI extends Model
{

  protected $table = 'paud_hi.pengajuan_paud_hi';
  protected $connection = 'pgsql_paud';
  public $timestamps = true;

  protected $fillable = ['tahun_pengajuan', 'npsn', 'nama_sekolah', 'alamat', 'rt', 'rw', 'kelurahan', 'kecamatan', 'kepala_sekolah', 'tahun_berdiri', 'status_kepemilikan_lahan', 'akreditasi_tahun', 'akreditasi_nilai', 'tgl_berakhir_izin', 'kurikulum', 'kurikulum_khusus', 'jumlah_siswa_kel_a_laki', 'jumlah_siswa_kel_a_perempuan', 'jumlah_siswa_kel_b_laki', 'jumlah_siswa_kel_b_perempuan', 'jumlah_siswa_abk_laki', 'jumlah_siswa_abk_perempuan', 'jumlah_gtk_laki', 'jumlah_gtk_perempuan', 'jumlah_pendiidkan_gtk_smp', 'jumlah_pendidikan_gtk_sma', 'jumlah_pendidikan_gtk_sarjana', 'pegawai_pns', 'pegawai_yayasan', 'pegawai_lainnya', 'gedung_sekolah', 'kondisi_gedung', 'status_pengajuan', 'catatan_penilik', 'catatan_sibunda', 'tanggal_pengajuan', 'tanggal_disetujui'];

  public function InstrumenPengajuan()
  {
    return $this->hasMany(InstrumenPengajuan::class, 'pengajuan_paud_hi_id', 'id');
  }
}
