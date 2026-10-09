<?php

namespace App\Http\Libraries;

use Illuminate\Http\Request;

class UraianUtamaLomba
{
  public static function cek_tahun($tahun)
  {
    switch ($tahun) {
      case '2024':
        $uraian = UraianUtamaLomba::uraian2024();
        break;
      case '2025':
        $uraian = UraianUtamaLomba::uraian2025();
        break;
      case '2026':
        $uraian = UraianUtamaLomba::uraian2026();
        break;

      default:
        // code...
        break;
    }
    return $uraian;
  }

  public static function uraian2024()
  {
    $uraian_kriteria_utama = [
      [
        'number' => "A",
        'name' => 'inovasi',
        'title' => "<b>Memiliki Inovasi dalam Program Yang Mendukung Gerakan Program Transisi PAUD ke SD Yang Menyenangkan;</b><br>(Bukti dilengkapi dengan pendukung KAK, Undangan, Absensi, Foto, tanggal/waktu, tempat, link sosial media, dll)",
        'subtitle' => [
          [
            'number' => '1',
            'title' => "Memiliki inovasi dalam program dukungan Gerakan Transisi PAUD ke SD yang Menyenangkan pada program kerja yang diusulkan baik secara eksplisit maupun implisit;",
          ],
          [
            'number' => '2',
            'title' => "Tersedianya beragam strategi dukungan Gerakan Transisi PAUD ke SD yang Menyenangkan;",
          ],
          [
            'number' => '3',
            'title' => "Output dari inovasi program dukungan Gerakan Transisi PAUD ke SD yang menyenangkan",
          ],
          [
            'number' => '4',
            'title' => "Perencanaan program dukungan yang menunjukan beragram strategi dengan metode pendekatan perorangan dan/atau kelompok sesuai dengan karakteristik sasaran sosialisasi, pendampingan dan penguatan;",
          ],
          [
            'number' => '5',
            'title' => "Dokumentasi Pelaksanaan strategi dan metode/cara sosialisasi, pendampingan dan penguatan yang telah direncanakan;",
          ],
        ],
      ],
      [
        'number' => "B",
        'name' => 'partisipasi',
        'title' => "<b>Mendorong partisipasi aktif orang tua dan masyarakat dan pemangku kepentingan lainnya, dalam proses Sosialisasi, Pendampingan, Pembimbingan;</b><br>(Bukti dilengkapi dengan pendukung KAK, Undangan, Absensi, Foto, tanggal/waktu, tempat, link sosial media, dll)",
        'subtitle' => [
          [
            'number' => '1',
            'title' => "Program yang diidentifikasi dari tingkat Kecamatan sampai Kelurahan;",
          ],
          [
            'number' => '2',
            'title' => "Daftar nama institusi/kelompok/anggota/peserta sasaran obyek sosialisasi, pendampingan dan penguatan",
          ],
          [
            'number' => '3',
            'title' => "Jadwal dan materi sosialisasi, pendampingan dan penguatan yang melibatkan sasaran.",
          ],
          [
            'number' => '4',
            'title' => "Pelibatan tokoh, organisasi mitra, dan kelompok masyarakat yang ada (termasuk TP PKK, Dharmawanita, IGTKI, IGRA, HIMPAUDI, LSM, dll).",
          ],
          [
            'number' => '5',
            'title' => "Jadwal dan tema sosialisasi, pendampingan dan penguatan yang melibatkan pemangku kepentingan baik yang telah maupun yang akan dilaksanakan.",
          ],
          [
            'number' => '6',
            'title' => "Produk bentuk media sosialisasi, pendampingan dan penguatan yang dibuat;",
          ],
          [
            'number' => '7',
            'title' => "Dokumentasi publikasi media kepada sasaran sosialisasi, pendampingan dan penguatan; ",
          ],
          [
            'number' => '8',
            'title' => "Output publikasi media yang telah dilakukan berupa respon dari sasaran sosialisasi, pendampingan dan penguatan.",
          ],
        ],
      ],
      [
        'number' => "C",
        'name' => 'forum',
        'title' => "<b>Mendorong pembentukan Forum Komunikasi PAUD-SD</b><br>(Bukti dilengkapi dengan pendukung KAK, Undangan, Absensi, Foto, tanggal/waktu, tempat, link sosial media, dll)",
        'subtitle' => [
          [
            'number' => '1',
            'title' => "Perencanaan kegiatan yang mendorong pembentukan dan/atau pengaktifan kembali Forkom PAUD-SD;",
          ],
          [
            'number' => '2',
            'title' => "Tersedianya SK Pembentukan/Pengaktifan kembali Forkom PAUD-SD.",
          ],
          [
            'number' => '3',
            'title' => "Tersedianya pedoman pelaksanaan Forum Komunikasi PAUD ke SD terkait metode/cara sosialisasi, pendampingan dan penguatan yang digunakan;",
          ],
          [
            'number' => '4',
            'title' => "Dokumentasi Pelaksanaan kegiatan yang mendorong pembentukan dan/atau pengaktifan kembali Forkom PAUD-SD;",
          ],
        ],
      ],
      [
        'number' => "D",
        'name' => 'pelaksanaan_program',
        'title' => "<b>Menyusun dan melaksanakan program kerja dan kegiatan;</b><br>(Bukti dilengkapi dengan pendukung KAK, Undangan, Absensi, Foto, tanggal/waktu, tempat, link sosial media, dll)",
        'subtitle' => [
          [
            'number' => '1',
            'title' => "Tersedianya rencana program kerja dan kegiatan Forkom PAUD-SD",
          ],
          [
            'number' => '2',
            'title' => "Dukungan anggaran dalam pelaksanaan kegiatan Forkom PAUD-SD;",
          ],
          [
            'number' => '3',
            'title' => "Dokumentasi Pelaksanaan kegiatan Forkom PAUD-SD sesuai dengan program kerja yang dirancang;",
          ],
        ],
      ],
      [
        'number' => "E",
        'name' => 'program_transisi',
        'title' => "<b>Memberikan motivasi, melaksanakan pemantauan dan evaluasi Implementasi Gerakan Program Transisi PAUD ke SD Yang Menyenangkan di wilayahnya masing-masing</b><br>(Bukti dilengkapi dengan pendukung KAK, Undangan, Absensi, Foto, tanggal/waktu, tempat, link sosial media, dll)",
        'subtitle' => [
          [
            'number' => '1',
            'title' => "Adanya program motivasi, pemantuan dan evaluasi implementasi Gerakan transisi PAUD ke SD yang mencakup 3 target perubahan (menghilangkan tes calistung, menerapkan masa perkenalan bagi peserta didik baru selama dua minggu pertama di SD, dan menerapkan pembelajaran yang membangun enam kemampuan fondasi awal anak) dalam program kerja baik secara implisit maupun eksplisit;",
          ],
          [
            'number' => '2',
            'title' => "Penjadwalan waktu pelaksanaan 3 target perubahan dalam program transisi PAUD ke SD;",
          ],
          [
            'number' => '3',
            'title' => "Pemanfaatan alat bantu transisi PAUD ke SD yang telah disediakan oleh Kemendikbudristek (Modul, Booklet, Video, infografis);",
          ],
          [
            'number' => '4',
            'title' => "Dokumentasi proses penyusunan materi dan media/dokumen/naskah sebagai produk materi sosialisasi, pendampingan dan penguatan program yang mengacu pada 3 target perubahan (menghilangkan tes calistung, menerapkan masa perkenalan bagi peserta didik baru selama dua minggu pertama di SD, dan menerapkan pembelajaran yang membangun enam kemampuan fondasi awal anak);",
          ],
          [
            'number' => '5',
            'title' => "Bukti implementasi materi dalam proses sosialisasi, pendampingan dan penguatan program (dapat berupa media sosial/website maupun foto produk materi saat diaplikasikan di dunia nyata).",
          ],
        ],
      ],
      [
        'number' => "F",
        'name' => 'implementasi_program_transisi',
        'title' => "<b>Prosentase terpenuhinya Indikator Implementasi Program Gerakan Transisi PAUD ke SD yang Menyenangkan;</b>",
        'subtitle' => [
          [
            'number' => '1',
            'title' => "<b>Bunda Kecamatan :</b><br>Prosentase Bunda PAUD Kelurahan yang memenuhi indikator 3 target perubahan program Gerakan Transisi PAUD Ke SD yang menyenangkan",
          ],
          [
            'number' => '2',
            'title' => "<b>Bunda Kelurahan :</b><br>Prosentase Satuan Pendidikan yang melaksanakan PPDB tanpa calistung",
          ],
          [
            'number' => '3',
            'title' => "Prosentase Satuan Pendidikan yang melaksanakan Masa Pengenalan Lingkungan Sekolah (MPLS) 2 Minggu",
          ],
          [
            'number' => '4',
            'title' => "Prosentase Satuan Pendidikan yang melaksanakan penerapan pembelajaran menyenangkan untuk membangun 6 kemampuan fondasi anak",
          ],
        ],
      ],
      [
        'number' => "G",
        'name' => 'koordinasi_program_transisi',
        'title' => "<b>Melakukan koordinasi dan sinkronisasi Gerakan Program Transisi PAUD ke SD Yang Menyenangkan untuk seluruh Kelurahan di wilayah Kecamatan setempat. (Khusus Bunda PAUD Kecamatan)</b><br>(Bukti dilengkapi dengan pendukung KAK, Undangan, Absensi, Foto, tanggal/waktu, tempat, link sosial media, dll)",
        'subtitle' => [
          [
            'number' => '1',
            'title' => "Dokumen pendukung kegiatan;",
          ],
          [
            'number' => '2',
            'title' => "Dokumentasi pelaksanaan kegiatan;",
          ],
          [
            'number' => '3',
            'title' => "Output yang dihasilkan.<br>Kegiatan koordinasi dan sinkronisasi berupa :",
          ],
          [
            'number' => 'a',
            'title' => "Bukti Rapat koordinasi internal dan eksternal",
          ],
          [
            'number' => 'b',
            'title' => "Bukti mendukung peningkatan mutu guru dan tenaga kependidikan PAUD (kualifikasi, kompetensi, kesejahteraan, dan perlindungan):",
          ],
          [
            'number' => 'c',
            'title' => "Bukti Pelaksanaan Pemantauan Praktik PPDB di SD tanpa tes Calistung",
          ],
          [
            'number' => 'd',
            'title' => "Bukti Pelaksanaan Pemantauan Masa Pengenalan Lingkungan Sekolah (MPLS)",
          ],
          [
            'number' => 'e',
            'title' => "Bukti pendampingan pengunaan asesmen untuk siswa baru SD",
          ],
          [
            'number' => 'f',
            'title' => "Bukti pendampingan praktik baik Guru PAUD dan SD menggunakan alat bantu pembelajaran",
          ],
        ],
      ],
    ];

    return $uraian_kriteria_utama;
  }

  public static function uraian2025()
  {
    $uraian_kriteria_utama = [
      [
        'number' => "A",
        'name' => 'inovasi',
        'title' => "<b>Inovasi dalam melakukan advokasi terkait kebijakan Kemendikdasmen menuju PAUD Bermutu dengan mendorong Perluasan Akses Pendidikan Anak Usia Dini, Peningkatan Mutu Pendidikan Anak Usia Dini, dan Peningkatan Strategi Tata Kelola Pendidikan Anak Usia;</b>",
        'subtitle' => [
          [
            'number' => '1',
            'title' => "Kebaruan program sosialisasi dan advokasi yang dikemas dalam kerangka Narasi Publik Program Bunda PAUD (bentuk penamaan program yang dianggap memiliki kekuatan ajakan yang efektif) dalam perluasan akses, peningkatan mutu pendidikan, dan peningkatan strategi tata kelola PAUD;",
          ],
          [
            'number' => '2',
            'title' => "Strategi dan metode/cara sosialisasi dan advokasi terutama dalam pemanfaatan teknologi digital untuk sosialisasi dan advokasi dalam memfasilitasi implementasi kebijakan Kemendikdasmen menuju layanan PAUD Bermutu.;",
          ],
          [
            'number' => '3',
            'title' => "Mendorong penyusunan regulasi turunan yang mendukung kebijakan Kemendikdasmen menuju PAUD Bermutu",
          ],
          [
            'number' => '4',
            'title' => "Narasi Publik Program Bunda PAUD dalam perluasan akses, peningkatan mutu pendidikan, dan peningkatan strategi tata kelola PAUD;",
          ],
        ],
      ],
      [
        'number' => "B",
        'name' => 'partisipasi',
        'title' => "<b>Keterjangkauan advokasi terkait kebijakan Kemendikdasmen menuju PAUD Bermutu dengan mendorong Perluasan Akses Pendidikan Anak Usia Dini, Peningkatan Mutu Pendidikan Anak Usia Dini, dan Peningkatan Strategi Tata Kelola Pendidikan Anak Usia Dini;</b>",
        'subtitle' => [
          [
            'number' => '1',
            'title' => "Mendorong partisipasi aktif orang tua dan masyarakat;",
          ],
          [
            'number' => '2',
            'title' => "Keterlibatan pemangku kepentingan dalam proses advokasi",
          ],
          [
            'number' => '3',
            'title' => "Produk dalam bentuk media advokasi yang dihasilkan.",
          ],
          [
            'number' => '4',
            'title' => "Bukti kegiatan mendorong pembentukan, pengaktifan kembali dan/atau penguatan Forum Komunikasi PAUD-SD",
          ],
          [
            'number' => '5',
            'title' => "Mendorong perubahan pembelajaran di SD kelas awal;dan.",
          ],
          [
            'number' => '6',
            'title' => "Mendorong transformasi pembelajaran dengan menerapkan pendekatan Pembelajaran Mendalam.;",
          ],
        ],
      ],
      [
        'number' => "C",
        'name' => 'forum',
        'title' => "<b>Kualitas relevansi Program Kerja dengan implementasi kebijakan Kemendikdasmen menuju PAUD Bermutu;</b>",
        'subtitle' => [
          [
            'number' => '1',
            'title' => "Dokumen Program kerja yang dibuat sebagai output dari Bimbingan Teknis Pokja Bunda PAUD dengan program mendukung kebijakan Kemendikdasmen menuju PAUD Bermutu;",
          ],
          [
            'number' => '2',
            'title' => "Dokumen materi advokasi yang relevan.",
          ],
          [
            'number' => '3',
            'title' => "Hasil evaluasi program kerja Pokja PAUD secara berkelanjutan terhadap target program;",
          ],
        ],
      ],
    ];

    return $uraian_kriteria_utama;
  }

  public static function uraian2026()
  {
    $uraian_kriteria_utama = [
      [
        'number' => "A",
        'name' => 'inovasi',
        'title' => "<b>Inovasi dalam melakukan advokasi terkait kebijakan Kemendikdasmen menuju PAUD Bermutu dengan mendorong Perluasan Akses Pendidikan Anak Usia Dini, Peningkatan Mutu Pendidikan Anak Usia Dini, dan Peningkatan Strategi Tata Kelola Pendidikan Anak Usia;</b>",
        'subtitle' => [
          [
            'number' => '1',
            'title' => "Kebaruan program sosialisasi dan advokasi yang dikemas dalam kerangka Narasi Publik Program Bunda PAUD (bentuk penamaan program yang dianggap memiliki kekuatan ajakan yang efektif) dalam perluasan akses, peningkatan mutu pendidikan, dan peningkatan strategi tata kelola PAUD;",
          ],
          [
            'number' => '2',
            'title' => "Strategi dan metode/cara sosialisasi dan advokasi terutama dalam pemanfaatan teknologi digital untuk sosialisasi dan advokasi dalam memfasilitasi implementasi kebijakan Kemendikdasmen menuju layanan PAUD Bermutu.;",
          ],
          [
            'number' => '3',
            'title' => "Mendorong penyusunan regulasi turunan yang mendukung kebijakan Kemendikdasmen menuju PAUD Bermutu",
          ],
          [
            'number' => '4',
            'title' => "Narasi Publik Program Bunda PAUD dalam perluasan akses, peningkatan mutu pendidikan, dan peningkatan strategi tata kelola PAUD;",
          ],
        ],
      ],
      [
        'number' => "B",
        'name' => 'partisipasi',
        'title' => "<b>Keterjangkauan advokasi terkait kebijakan Kemendikdasmen menuju PAUD Bermutu dengan mendorong Perluasan Akses Pendidikan Anak Usia Dini, Peningkatan Mutu Pendidikan Anak Usia Dini, dan Peningkatan Strategi Tata Kelola Pendidikan Anak Usia Dini;</b>",
        'subtitle' => [
          [
            'number' => '1',
            'title' => "Mendorong partisipasi aktif orang tua dan masyarakat;",
          ],
          [
            'number' => '2',
            'title' => "Keterlibatan pemangku kepentingan dalam proses advokasi",
          ],
          [
            'number' => '3',
            'title' => "Produk dalam bentuk media advokasi yang dihasilkan.",
          ],
          [
            'number' => '4',
            'title' => "Bukti kegiatan mendorong pembentukan, pengaktifan kembali dan/atau penguatan Forum Komunikasi PAUD-SD",
          ],
          [
            'number' => '5',
            'title' => "Mendorong perubahan pembelajaran di SD kelas awal;dan.",
          ],
          [
            'number' => '6',
            'title' => "Mendorong transformasi pembelajaran dengan menerapkan pendekatan Pembelajaran Mendalam.;",
          ],
        ],
      ],
      [
        'number' => "C",
        'name' => 'forum',
        'title' => "<b>Kualitas relevansi Program Kerja dengan implementasi kebijakan Kemendikdasmen menuju PAUD Bermutu;</b>",
        'subtitle' => [
          [
            'number' => '1',
            'title' => "Dokumen Program kerja yang dibuat sebagai output dari Bimbingan Teknis Pokja Bunda PAUD dengan program mendukung kebijakan Kemendikdasmen menuju PAUD Bermutu;",
          ],
          [
            'number' => '2',
            'title' => "Dokumen materi advokasi yang relevan.",
          ],
          [
            'number' => '3',
            'title' => "Hasil evaluasi program kerja Pokja PAUD secara berkelanjutan terhadap target program;",
          ],
        ],
      ],
    ];

    return $uraian_kriteria_utama;
  }
}
