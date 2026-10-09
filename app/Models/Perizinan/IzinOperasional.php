<?php

namespace App\Models\Perizinan;

use Illuminate\Database\Eloquent\Model;

class IzinOperasional extends Model
{
    protected $connection = 'perijinan';
    protected $table = 'izin_operasional';
    protected $fillable = array('id', 'npsn','npsn_sekarang', 'no_sk_pendirian', 'tgl_tetapkan', 'tgl_berlaku_sk_pendirian', 'tgl_berakhir_sk_pendirian', 'no_izin_operasional_lama', 'tgl_izin_operasional_lama', 'akreditasi', 'tgl_akreditasi', 'peruntukan_skrk', 'status_tanah', 'status_tanah_catatan', 'tgl_surat_supervisor', 'bukti_supervisi', 'catatan_instrumen_supervisi', 'status_pengawas', 'catatan_rekom_pengawas', 'no_akte_yayasan', 'tgl_akte_yayasan', 'status_kasi', 'catatan_kasi', 'tgl_catatan_kasi', 'tindak_lanjut_kasi', 'tgl_tindak_lanjut_kasi', 'status_kadis', 'catatan_kadis', 'tgl_catatan_kadis', 'izin_status', 'no_sk_baru', 'tgl_penetapan_sk_baru', 'tgl_berlaku_sk_baru', 'tgl_berakhir_sk_baru', 'pengambil_sk', 'tgl_pengambil_sk', 'created_at', 'update_at', 'jenjang', 'status_kabid', 'catatan_kabid', 'tgl_catatan_kabid', 'id_yayasan', 'nama_sekolah', 'unique_id', 'input_date', 'no_akreditasi', 'tgl_berakhir_izin_operasional_lama', 'status_verifikator', 'id_verifikator', 'catatan_verifikator', 'tgl_catatan_verifikator', 'tgl_disetujui', 'tgl_berakhir_baru', 'tgl_operasional_baru', 'cetak', 'ttd', 'ambil', 'tgl_cetak', 'tgl_ttd', 'tgl_ambil', 'sk_pendirian', 'no_surat_pemohon', 'nama_kepala', 'foto_pimpinan', 'jenis_sekolah', 'no_izin_lama', 'no_surat_permohonan_revisi', 'tgl_surat');
    public $timestamps = false;
    // protected $primaryKey = 'id';
}