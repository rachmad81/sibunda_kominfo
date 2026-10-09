<?php

namespace App\Http\Libraries;

use Illuminate\Http\Request;

class UraianPendukungLomba
{
  public static function cek_tahun($tahun)
  {
    switch ($tahun) {
      case '2024':
        $uraian = UraianPendukungLomba::uraian2024();
        break;
      case '2025':
        $uraian = UraianPendukungLomba::uraian2025();
        break;
      case '2026':
        $uraian = UraianPendukungLomba::uraian2026();
        break;

      default:
        // code...
        break;
    }
    return $uraian;
  }

  public static function uraian2024()
  {
    $uraian_kriteria_pendukung = [
      [
        'number' => "A",
        'name' => 'kolaborasi',
        'title' => "<b>Bukti kolaborasi dengan organisasi mitra PAUD dan SD serta organisasi lainnya yang mendukung Gerakan Transisi PAUD ke SD yang Menyenangkan;</b><br>Bukti dilengkapi dengan pendukung KAK, Undangan, Absensi, Foto, tanggal/waktu, tempat, link sosial media, dll",
        'subtitle' => [
          [
            'number' => '1',
            'title' => "Daftar kemitraan kerjasama/kolaborasi;",
          ],
          [
            'number' => '2',
            'title' => "Dokumen pendukung kegiatan;",
          ],
          [
            'number' => '3',
            'title' => "Dokumentasi pelaksanaan kegiatan;",
          ],
          [
            'number' => '4',
            'title' => "Output yang dihasilkan",
          ],
        ],
      ],
      [
        'number' => "B",
        'name' => 'kegiatan_parenting',
        'title' => "<b>Bukti mendorong penyelenggaraan kegiatan keorangtuaan (parenting) termasuk kelas orang tua di satuan pendidikan.</b><br>(Bukti dilengkapi dengan pendukung KAK, Undangan, Absensi, Foto, tanggal/waktu, tempat, link sosial media, dll)",
        'subtitle' => [
          [
            'number' => '1',
            'title' => "Mengoptimalkan peran orang tua di Satuan Pendidikan dengan memanfaatkan alat bantu gerakan transisi PAUD ke SD yang menyenangkan yang telah disediakan oleh Kemendikbudristek;",
          ],
          [
            'number' => '2',
            'title' => "Dokumentasi pelaksanaan kegiatan;",
          ],
          [
            'number' => '3',
            'title' => "Output yang dihasilkan.",
          ],
        ],
      ],
      [
        'number' => "C",
        'name' => 'pembinaan_transisi',
        'title' => "<b>Bukti mendorong peran serta masyarakat dalam pembinaan, penyelenggaraan, dan pengembangan gerakan Transisi PAUD ke SD yang Menyenangkan.</b><br>(Bukti dilengkapi dengan pendukung KAK, Undangan, Absensi, Foto, tanggal/waktu, tempat, link sosial media, dll)",
        'subtitle' => [
          [
            'number' => '1',
            'title' => "Mengoptimalkan peran serta masyarakat dengan memanfaatkan alat bantu gerakan transisi PAUD ke SD yang menyenangkan yang telah disediakan oleh Kemendikbudristek;",
          ],
          [
            'number' => '2',
            'title' => "Dokumentasi pelaksanaan kegiatan;",
          ],
          [
            'number' => '3',
            'title' => "Output yang dihasilkan.",
          ],
        ],
      ],
      [
        'number' => "D",
        'name' => 'sosis_transisi',
        'title' => "<b>Bukti membantu mensosialisasikan  gerakan Transisi PAUD ke SD yang Menyenangkan melalui berbagai kanal media komunikasi</b>",
        'subtitle' => [
          [
            'number' => '1',
            'title' => "Bukti publikasi dan sosialisasi gerakan transisi PAUD ke SD yang menyenangkan melalui berbagai kanal atau bentuk media komunikasi lainnya. (kualitas dan kuantitas publikasi mempengaruhi penilaian)",
          ],
        ],
      ],
      [
        'number' => "E",
        'name' => 'monitoring',
        'title' => "<b>Bukti Melakukan monitoring dan motivasi  penerapan PAUD Holistik Integratif di satuan Pendidikan;</b><br>(Bukti dilengkapi dengan pendukung KAK, Undangan, Absensi, Foto, tanggal/waktu, tempat, link sosial media, dll)",
        'subtitle' => [
          [
            'number' => '1',
            'title' => "Satuan PAUD mengisi monitoring PAUD HI di Profil Sekolah",
          ],
          [
            'number' => '2',
            'title' => "Bunda PAUD meverifikasi usulan PAUD HI di sibunda",
          ],
          [
            'number' => '3',
            'title' => "Bukti kegiatan monitoring dan motivasi  (seperti Gebyar, lomba, apresiasi dan sebagainya) kepada penyelenggara satuan pendidikan",
          ],
        ],
      ],
    ];

    return $uraian_kriteria_pendukung;
  }

  public static function uraian2025()
  {
    $uraian_kriteria_pendukung = [
      [
        'number' => "A",
        'name' => 'kolaborasi',
        'title' => "<b>Kolaborasi dengan organisasi mitra PAUD serta organisasi lainnya yang mendukung kebijakan Kemendikdasmen menuju PAUD Bermutu;</b>",
        'subtitle' => [
          [
            'number' => '1',
            'title' => "Kolaborasi dengan organisasi mitra PAUD serta organisasi lainnya yang mendukung kebijakan Kemendikdasmen menuju PAUD Bermutu;",
          ],
        ],
      ],
      [
        'number' => "B",
        'name' => 'kegiatan_parenting',
        'title' => "<b>Dorongan penyelenggaraan kegiatan keorangtuaan (pendidikan keluarga) di setiap satuan pendidikan.</b>",
        'subtitle' => [
          [
            'number' => '1',
            'title' => "Dorongan penyelenggaraan kegiatan keorangtuaan (pendidikan keluarga) di setiap satuan pendidikan.",
          ],
        ],
      ],
      [
        'number' => "C",
        'name' => 'pembinaan_transisi',
        'title' => "<b>Membantu mensosialisasikan kebijakan Kemendikdasmen menuju PAUD Bermutu melalui berbagai kanal media komunikasi.</b>",
        'subtitle' => [
          [
            'number' => '1',
            'title' => "Membantu mensosialisasikan kebijakan Kemendikdasmen menuju PAUD Bermutu melalui berbagai kanal media komunikasi.",
          ],
        ],
      ],
      [
        'number' => "D",
        'name' => 'sosis_transisi',
        'title' => "<b>Dorongan peran serta masyarakat untuk membangun kepedulian dan optimalisasi peran serta masyarakat dalam implementasi kebijakan Kemendikdasmen menuju PAUD Bermutu</b>",
        'subtitle' => [
          [
            'number' => '1',
            'title' => "Dorongan peran serta masyarakat untuk membangun kepedulian dan optimalisasi peran serta masyarakat dalam implementasi kebijakan Kemendikdasmen menuju PAUD Bermutu",
          ],
        ],
      ],
      [
        'number' => "E",
        'name' => 'monitoring',
        'title' => "<b>Memfasilitasi rapat koordinasi Bunda PAUD di wilayah kerjanya</b>",
        'subtitle' => [
          [
            'number' => '1',
            'title' => "Memfasilitasi rapat koordinasi Bunda PAUD di wilayah kerjanya",
          ],
        ],
      ],
      [
        'number' => "F",
        'name' => 'kolom_f',
        'title' => "<b>Melaksanakan monitoring dan motivasi kepada penyelenggara satuan pendidikan</b>",
        'subtitle' => [
          [
            'number' => '1',
            'title' => "Melaksanakan monitoring dan motivasi kepada penyelenggara satuan pendidikan",
          ],
        ],
      ],
      [
        'number' => "G",
        'name' => 'kolom_g',
        'title' => "<b>Dukungan peningkatan mutu guru dan tenaga kependidikan PAUD (kualifikasi, kompetensi, kesejahteraan, dan perlindungan)</b>",
        'subtitle' => [
          [
            'number' => '1',
            'title' => "Dukungan peningkatan mutu guru dan tenaga kependidikan PAUD (kualifikasi, kompetensi, kesejahteraan, dan perlindungan)",
          ],
        ],
      ],
      [
        'number' => "H",
        'name' => 'kolom_h',
        'title' => "<b>Dukungan Kecamatan/Kelurahan dalam mewujudkan penyediaan layanan PAUD minimal 1 (satu) tahun prasekolah</b>",
        'subtitle' => [
          [
            'number' => '1',
            'title' => "Dukungan Kecamatan/Kelurahan dalam mewujudkan penyediaan layanan PAUD minimal 1 (satu) tahun prasekolah",
          ],
        ],
      ],
      [
        'number' => "I",
        'name' => 'kolom_i',
        'title' => "<b>Praktik Baik Dalam Penyelenggaraan PAUD, Deskripsi pengalaman satuan PAUD/komunitas yang dapat dijadikan contoh/ diunggulkan dari daerah </b>",
        'subtitle' => [
          [
            'number' => '1',
            'title' => "Praktik Baik Dalam Penyelenggaraan PAUD, Deskripsi pengalaman satuan PAUD/komunitas yang dapat dijadikan contoh/ diunggulkan dari daerah ",
          ],
        ],
      ],
    ];

    return $uraian_kriteria_pendukung;
  }

  public static function uraian2026()
  {
    $uraian_kriteria_pendukung = [
      [
        'number' => "A",
        'name' => 'kolaborasi',
        'title' => "<b>Kolaborasi dengan organisasi mitra PAUD serta organisasi lainnya yang mendukung kebijakan Kemendikdasmen menuju PAUD Bermutu;</b>",
        'subtitle' => [
          [
            'number' => '1',
            'title' => "Kolaborasi dengan organisasi mitra PAUD serta organisasi lainnya yang mendukung kebijakan Kemendikdasmen menuju PAUD Bermutu;",
          ],
        ],
      ],
      [
        'number' => "B",
        'name' => 'kegiatan_parenting',
        'title' => "<b>Mendorong penyelenggaraan kegiatan pendidikan keluarga, termasuk kelas orang tua;</b>",
        'subtitle' => [
          [
            'number' => '1',
            'title' => "Mendorong penyelenggaraan kegiatan pendidikan keluarga, termasuk kelas orang tua;",
          ],
        ],
      ],
      [
        'number' => "C",
        'name' => 'pembinaan_transisi',
        'title' => "<b>Membantu mensosialisasikan kebijakan Kemendikdasmen menuju PAUD Bermutu melalui berbagai kanal media komunikasi.</b>",
        'subtitle' => [
          [
            'number' => '1',
            'title' => "Membantu mensosialisasikan kebijakan Kemendikdasmen menuju PAUD Bermutu melalui berbagai kanal media komunikasi.",
          ],
        ],
      ],
      [
        'number' => "D",
        'name' => 'sosis_transisi',
        'title' => "<b>Mendorong peran serta masyarakat dalam pembinaan, penyelenggaraan, dan pengembangan kebijakan Kemendikdasmen menuju PAUD Bermutu;</b>",
        'subtitle' => [
          [
            'number' => '1',
            'title' => "Mendorong peran serta masyarakat dalam pembinaan, penyelenggaraan, dan pengembangan kebijakan Kemendikdasmen menuju PAUD Bermutu;",
          ],
        ],
      ],
      [
        'number' => "E",
        'name' => 'monitoring',
        'title' => "<b>Memfasilitasi rapat koordinasi Bunda PAUD di wilayah kerjanya</b>",
        'subtitle' => [
          [
            'number' => '1',
            'title' => "Memfasilitasi rapat koordinasi Bunda PAUD di wilayah kerjanya",
          ],
        ],
      ],
      [
        'number' => "F",
        'name' => 'kolom_f',
        'title' => "<b>Melaksanakan monitoring dan motivasi kepada penyelenggara satuan pendidikan</b>",
        'subtitle' => [
          [
            'number' => '1',
            'title' => "Melaksanakan monitoring dan motivasi kepada penyelenggara satuan pendidikan",
          ],
        ],
      ],
      [
        'number' => "G",
        'name' => 'kolom_g',
        'title' => "<b>Menguraikan saran, masukan, dan rekomendasi kepada Pemerintah Daerah untuk mendukung pembinaan dan pembiayaan pelaksanaan kebijakan Kemendikdasmen menuju PAUD Bermutu (termasuk memfasilitasi rapat koordinasi Bunda PAUD di wilayah kerjanya</b>",
        'subtitle' => [
          [
            'number' => '1',
            'title' => "Menguraikan saran, masukan, dan rekomendasi kepada Pemerintah Daerah untuk mendukung pembinaan dan pembiayaan pelaksanaan kebijakan Kemendikdasmen menuju PAUD Bermutu (termasuk memfasilitasi rapat koordinasi Bunda PAUD di wilayah kerjanya",
          ],
        ],
      ],
    ];

    return $uraian_kriteria_pendukung;
  }
}
