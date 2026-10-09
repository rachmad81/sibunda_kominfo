<?php

namespace App\Tools;

class ParameterATS
{
  public static function data_domisili()
  {
    return [
      [
        'text' => 'Ya, anak tinggal sesuai alamat',
        'value' => 'sesuai',
      ],
      [
        'text' => 'Tidak, anak pindah dalam Kota Surabaya',
        'value' => 'pindah_dalam',
      ],
      [
        'text' => 'Tidak, anak pindah luar Kota Surabaya',
        'value' => 'pindah_luar',
      ],
      [
        'text' => 'Anak tidak ditemukan di alamat',
        'value' => 'tidak_ditemukan',
      ],
      [
        'text' => 'Data ganda / salah sasaran',
        'value' => 'data_ganda',
      ],
      [
        'text' => 'Meninggal dunia',
        'value' => 'meninggal',
      ],
      [
        'text' => 'Lainnya',
        'value' => 'lainnya',
      ],
    ];
  }

  public static function data_sekolah()
  {
    return [
      [
        'text' => 'Sudah mengikuti PAUD <b>(lanjut Poin 1)</b>',
        'value' => 'sudah_paud',
      ],
      [
        'text' => 'Akan mendaftar tahun ajaran 2026/2027 <b>(lanjut Poin 2)</b>',
        'value' => 'akan_daftar',
      ],
      [
        'text' => 'Sudah masuk SD/MI atau sederajat',
        'value' => 'sudah_sd',
      ],
      [
        'text' => 'Belum mengikuti PAUD',
        'value' => 'belum_paud',
      ],
      [
        'text' => 'Pernah mengikuti PAUD tetapi berhenti',
        'value' => 'pernah_berhenti',
      ],
      [
        'text' => 'Tidak diketahui/perlu verifikasi ulang',
        'value' => 'verifikasi_ulang',
      ],
    ];
  }

  public static function data_faktor()
  {
    return [
      [
        'text' => '1. Ekonomi & Kerentanan',
        'value' => 'Faktor 1: Ekonomi & Kerentanan',
        'form' => 'f1',
      ],
      [
        'text' => '2. Pengasuhan & Dukungan',
        'value' => 'Faktor 2: Pengasuhan & Dukungan',
        'form' => 'f2',
      ],
      [
        'text' => '3. Kondisi Anak & Kesehatan',
        'value' => 'Faktor 3: Kondisi Anak & Kesehatan',
        'form' => 'f3',
      ],
      [
        'text' => '4. Administrasi',
        'value' => 'Faktor 4: Administrasi',
        'form' => 'f4',
      ],
      [
        'text' => '5. Lainnya (Rincian)',
        'value' => 'Faktor 5: Lainnya',
        'form' => 'f5',
      ],
    ];
  }

  public static function data_edukasi()
  {
    return [
      [
        'text' => 'Pentingnya PAUD bagi tumbuh kembang anak',
        'value' => 'pentingnya_paud',
      ],
      [
        'text' => 'Kebijakan Wajib Belajar 1 Tahun Prasekolah',
        'value' => 'wajib_belajar',
      ],
      [
        'text' => 'Informasi satuan PAUD terdekat yang tersedia',
        'value' => 'paud_terdekat',
      ],
      [
        'text' => 'Informasi bantuan kesehatan/beasiswa PAUD',
        'value' => 'bantuan_beasiswa',
      ],
      [
        'text' => 'Informasi layanan PAUD inklusi atau dukungan bagi ABK',
        'value' => 'layanan_inklusi',
      ],
      [
        'text' => 'Informasi pengurusan dokumen administrasi kependudukan',
        'value' => 'adminduk',
      ],
    ];
  }

  public static function data_tanggapan()
  {
    return [
      [
        'text' => 'Bersedia mendaftarkan anak ke PAUD',
        'value' => 'bersedia',
      ],
      [
        'text' => 'Masih mempertimbangkan',
        'value' => 'mempertimbangkan',
      ],
      [
        'text' => 'Lainnya',
        'value' => 'lainnya',
      ],
    ];
  }

  public static function data_rtl()
  {
    return [
      [
        'text' => 'Dilakukan edukasi atau kunjungan lanjutan kepada orang tua/wali',
        'value' => 'edukasi_lanjutan',
      ],
      [
        'text' => 'Difasilitasi mendaftar ke satuan PAUD terdekat',
        'value' => 'fasilitasi_daftar',
      ],
      [
        'text' => 'Dikoordinasikan dengan Dinas Pendidikan/OPD terkait',
        'value' => 'koordinasi_opd',
      ],
      [
        'text' => 'Diusulkan bantuan/beasiswa/dukungan sosial sesuai ketentuan',
        'value' => 'bantuan',
      ],
      [
        'text' => 'Dirujuk ke layanan administrasi kependudukan (Disdukcapil)',
        'value' => 'disdukcapil',
      ],
      [
        'text' => 'Dirujuk ke layanan kesehatan/ABK/inklusi (Rumah ABK/PUSPAGA/RAP)',
        'value' => 'kesehatan_abk',
      ],
    ];
  }
}
