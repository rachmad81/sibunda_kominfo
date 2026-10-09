<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cetak SK Pengangkatan</title>
</head>
<style>
    table tr td {
        vertical-align: top;
    }

    * {
        font-size: 12pt;
    }
</style>

<body>
    <div style="width: 21cm;margin: auto">
        <div style="height: 5cm">&nbsp;</div>
        <div style="text-align: center;font-weight: bold">
            KEPUTUSAN<br>
            BUNDA PAUD KECAMATAN {{ strtoupper($sekolah->kecamatan_dispenduk) }}<br>
            NOMOR : {{ $monev->nomor ?? '' }}<br>
            <br>
            TENTANG<br>
            PENGANGKATAN PENDIDIK DAN TENAGA KEPENDIDIKAN<br>
            POS PAUD TERPADU (PPT) {{ $sekolah->nama_lembaga }}<br>
            KELURAHAN {{ strtoupper($sekolah->kelurahan_dispenduk) }} KECAMATAN
            {{ strtoupper($sekolah->kecamatan_dispenduk) }}<br>
            KOTA SURABAYA<br>
            TAHUN {{ date('Y', strtotime($monev->tanggal_verifikasi ?? date('Y-m-d'))) }}
        </div>
        <div style="width: 100%;margin-top:0.5cm">
            <table style="width: 100%">
                <tr>
                    <td>Menimbang</td>
                    <td>:</td>
                    <td>a.</td>
                    <td>bahwa dalam rangka menunjang kelancaran proses pembelajaran serta meningkatkan mutu layanan
                        pendidikan anak usia dini secara holistik integratif pada Pos PAUD Terpadu (PPT), perlu
                        mengangkat Pendidik dan Tenaga Kependidikan yang memenuhi persyaratan;</td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td>b.</td>
                    <td>bahwa Pendidik dan Tenaga Kependidikan yang diangkat harus mampu berperan sebagai teladan,
                        perancang pembelajaran, fasilitator, dan motivator dalam mendukung perkembangan anak usia dini
                        melalui pembelajaran yang berkesadaran, bermakna, dan menggembirakan sesuai tahapan perkembangan
                        anak;</td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td>c.</td>
                    <td>bahwa berdasarkan pertimbangan sebagaimana dimaksud dalam huruf a dan huruf b, perlu menetapkan
                        Keputusan Bunda PAUD Kecamatan Wonokromo tentang Pengangkatan Pendidik dan Tenaga Kependidikan
                        Pos PAUD Terpadu (PPT) {{ $sekolah->nama_lembaga }} Kelurahan
                        {{ ucwords(strtolower($sekolah->kelurahan_dispenduk)) }} Kecamatan
                        {{ ucwords(strtolower($sekolah->kecamatan_dispenduk)) }}
                        Kota Surabaya Tahun {{ date('Y', strtotime($monev->tanggal_verifikasi ?? date('Y-m-d'))) }};
                    </td>
                </tr>
                <tr>
                    <td>Mengingat</td>
                    <td>:</td>
                    <td>1.</td>
                    <td>Mengingat : 1. Undang-Undang Nomor 20 Tahun 2003 tentang Sistem Pendidikan Nasional (Lembaran
                        Negara Republik Indonesia Republik Indonesia Tahun 2003 Nomor 78, Tambahan Lembaran Negara
                        Republik Indonesia Nomor 4301);</td>
                <tr>
                    <td></td>
                    <td></td>
                    <td>2.</td>
                    <td>Undang-Undang Nomor 14 Tahun 2005 tentang Guru dan Dosen (Lembaran Negara Republik Indonesia
                        Tahun 2005 Nomor 157, Tambahan Lembaran Negara Republik Indonesia Nomor 4586);</td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td>3.</td>
                    <td>Peraturan Pemerintah Nomor 74 Tahun 2008 tentang Guru (Lembaran Negara Republik Indonesia Tahun
                        2008
                        Nomor 194, Tambahan Lembaran Negara Republik Indonesia Nomor 4941) sebagaimana telah diubah
                        dengan PP
                        No. 19 Tahun 2017 (Lembaran Negara Republik Indonesia Tahun 2017 Nomor 107, Tambahan Lembaran
                        Negara
                        Republik Indonesia Nomor 6058);
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td>4.</td>
                    <td>Peraturan Pemerintah Nomor 17 Tahun 2010 tentang Pengelolaan dan Penyelenggaraan Pendidikan
                        (Lembaran
                        Negara Republik Indonesia Tahun 2010 Nomor 23 Tambahan Lembaran Negara Republik Indonesia
                        Republik
                        Indonesia Nomor 5105);
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td>5.</td>
                    <td>Peraturan Pemerintah Nomor 57 Tahun 2021 tentang Standar Nasional Pendidikan (Lembaran Negara
                        Republik Indonesia Tahun 2021 Nomor 87, Tambahan Lembaran Negara Republik Indonesia Nomor 6676);
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td>6.</td>
                    <td>Peraturan Presiden Nomor 60 Tahun 2013 tentang Pengembangan Anak Usia Dini Holistik Integratif
                        (Lembaran Negara Republik Indonesia Tahun 2013 Nomor 146);
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td>7.</td>
                    <td>Peraturan Menteri Pendidikan dan Kebudayaan Nomor 84 Tahun 2014 tentang Pendirian Satuan
                        Pendidikan
                        Anak Usia Dini (Berita Negara Republik Indonesia Tahun 2014 Nomor 1279);
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td>8.</td>
                    <td>Peraturan Menteri Pendidikan Dasar dan Menengah Nomor 11 Tahun 2025 tentang Pemenuhan Beban
                        Kerja
                        Guru (Berita Negara Republik Indonesia Tahun 2025 Nomor 463);
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td>9.</td>
                    <td>Peraturan Menteri Pendidikan Dasar dan Menengah Nomor 12 Tahun 2025 tentang Standar Isi pada
                        Pendidikan Anak Usia Dini, Jenjang Pendidikan Dasar, dan Jenjang Pendidikan Menengah (Berita
                        Negara
                        Republik Indonesia Tahun 2025 Nomor 502);
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td>10.</td>
                    <td>Peraturan Menteri Pendidikan Dasar dan Menengah Nomor 21 Tahun 2025 tentang Standar Tenaga
                        Kependidikan pada Pendidikan Anak Usia Dini, Jenjang Pendidikan Dasar, dan Jenjang Pendidikan
                        Menengah
                        (Berita Negara Republik Indonesia Tahun 2025 Nomor 895);
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td>11.</td>
                    <td>Peraturan Menteri Pendidikan Dasar dan Menengah Nomor 26 Tahun 2025 tentang Standar Pengelolaan
                        pada
                        Pendidikan Anak Usia Dini, Jenjang Pendidikan Dasar, dan Jenjang Pendidikan Menengah (Berita
                        Negara
                        Republik Indonesia Tahun 2025 Nomor 1079);
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td>12.</td>
                    <td>Peraturan Menteri Pendidikan Dasar dan Menengah Nomor 1 Tahun 2026 tentang Standar Proses pada
                        Pendidikan Anak Usia Dini, Jenjang Pendidikan Dasar, dan Jenjang Pendidikan Menengah (Berita
                        Negara
                        Republik Indonesia Tahun 2026 Nomor 1);
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td>13.</td>
                    <td>Peraturan Daerah Kota Surabaya Nomor 16 Tahun 2012 tentang Penyelenggaraan Pendidikan (Lembaran
                        Daerah Tahun 2012 Nomor 16 Tambahan Lembaran Daerah Nomor 15);
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td>14.</td>
                    <td>Peraturan Walikota Surabaya Nomor 47 Tahun 2013 tentang Penyelenggaraan dan Pengelolaan
                        Pendidikan
                        di Kota Surabaya (Berita Daerah Tahun 2013 Nomor 47) sebagaimana telah diubah terakhir dengan
                        Peraturan
                        Walikota Nomor 9 Tahun 2023 (Berita Daerah Tahun 2023 Nomor 09);
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td>15.</td>
                    <td>Peraturan Walikota Surabaya Nomor 8 Tahun 2022 tentang Penyelenggaraan Pengembangan Anak Usia
                        Dini
                        Holistik Integratif (Berita Daerah Tahun 2022 Nomor 9) sebagaimana telah diubah terakhir dengan
                        Peraturan Walikota Nomor 13 Tahun 2024 (Berita Daerah Tahun 2024 Nomor 14);
                </tr>
            </table>
        </div>
        <div style="page-break-before: always">&nbsp;</div>
        <div style="width: 100%;text-align: center">
            MEMUTUSKAN
        </div>
        <div style="width: 100%;margin-top: 0.5cm">
            <table style="width: 100%">
                <tr>
                    <td>Menetapkan</td>
                    <td></td>
                    <td>:</td>
                    <td>KEPUTUSAN BUNDA PAUD KECAMATAN {{ $sekolah->kecamatan_dispenduk }} TENTANG PENGANGKATAN
                        PENDIDIK DAN TENAGA KEPENDIDIKAN
                        POS PAUD TERPADU (PPT) {{ $sekolah->nama_lembaga }}. KELURAHAN
                        {{ $sekolah->kelurahan_dispenduk }} KECAMATAN {{ $sekolah->kecamatan_dispenduk }}
                        KOTA SURABAYA TAHUN {{ date('Y', strtotime($monev->tanggal_verifikasi ?? date('Y-m-d'))) }}
                    </td>
                </tr>
                <tr>
                    <td>KESATU</td>
                    <td></td>
                    <td>:</td>
                    <td>Menetapkan Pendidik dan Tenaga Kependidikan Pos PAUD Terpadu (PPT) {{ $sekolah->nama_lembaga }}
                        Kelurahan {{ ucwords(strtolower($sekolah->kelurahan_dispenduk)) }} Kecamatan
                        {{ ucwords(strtolower($sekolah->kecamatan_dispenduk)) }} sebagaimana tercantum dalam Lampiran
                        yang merupakan bagian tidak terpisahkan
                        dari Keputusan ini.</td>
                </tr>
                <tr>
                    <td>KEDUA</td>
                    <td></td>
                    <td>:</td>
                    <td>Pendidik Pos PAUD Terpadu (PPT) sebagaimana dimaksud dalam diktum Kesatu mempunyai tugas sebagai
                        berikut:</td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>
                        <ol type="a" style="margin:0px;padding-inline-start: 20px">
                            <li>melaksanakan tugas sebagai pendidik profesional yang meliputi kegiatan mendidik,
                                mengajar,
                                membimbing, melatih, mengasuh, dan melindungi murid;</li>
                            <li>mengembangkan dan menyampaikan muatan pembelajaran PAUD yang mencakup aspek nilai agama
                                dan
                                moral, nilai Pancasila, fisik motorik, kognitif, bahasa, sosial emosional, dan seni
                                sesuai
                                capaian perkembangan anak;</li>
                            <li>mengidentifikasi kebutuhan belajar, merencanakan, melaksanakan, dan mengevaluasi proses
                                pembelajaran sesuai Kurikulum yang meliputi penyusunan program semester (Prosem),
                                Rencana
                                Program Pembelajaran Mingguan (RPPM), dan Rencana Program Pembelajaran Harian (RPPH)
                                dengan
                                menerapkan prinsip pembelajaran berkesadaran, bermakna, dan menggembirakan;</li>
                            <li>melakukan penilaian perkembangan anak dan Deteksi Dini Tumbuh Kembang Anak (DDTK) secara
                                berkala serta melakukan pencatatan hasilnya;</li>
                            <li>menyiapkan dan menata Alat Permainan Edukatif (APE) sesuai kelompok usia anak;</li>
                            <li>mendukung penyelenggaraan layanan PAUD Holistik Integratif yang mencakup aspek
                                pendidikan,
                                kesehatan, gizi, pengasuhan, perlindungan, dan kesejahteraan anak;</li>
                            <li>melaksanakan kegiatan pembinaan orang tua (parenting) serta membangun kemitraan dengan
                                orang
                                tua dan masyarakat untuk mendukung tumbuh kembang anak;</li>
                            <li>meningkatkan kompetensi pedagogik, kepribadian, sosial, dan profesional secara
                                berkelanjutan;</li>
                            <li>melakukan evaluasi kegiatan pembelajaran dan program layanan PAUD secara berkala sebagai
                                bagian dari peningkatan mutu layanan.</li>
                        </ol>
                    </td>
                </tr>
                <tr>
                    <td>KETIGA</td>
                    <td></td>
                    <td>:</td>
                    <td>
                        Tenaga Kependidikan sebagaimana dimaksud dalam diktum Kesatu mempunyai tugas menunjang proses
                        pendidikan, dengan rincian sebagai berikut:
                        <ol type="a" style="margin:0px;padding-inline-start: 20px">
                            <LI>kepala/pengelola Satuan PAUD, memimpin dan mengelola satuan pendidikan dengan fungsi
                                manajerial, pengembangan kewirausahaan, serta supervisi kepada pendidik dan tenaga
                                kependidikan serta bertanggung jawab atas kelancaran penyelenggaraan kegiatan
                                pendidikan;</LI>
                            <LI>bendahara, mengelola administrasi keuangan yang meliputi buku kas, penyusunan Rencana
                                Anggaran Pendapatan Belanja Sekolah (RAPBS), pembelanjaan barang dan jasa serta laporan
                                keuangan;</LI>
                            <LI>tenaga administrasi, mengelola administrasi umum meliputi surat-menyurat, buku induk,
                                daftar hadir, notulen rapat, dan laporan kegiatan;</LI>
                            <LI>pengelola dapat merangkap tugas sebagai Pendidik sesuai ketentuan yang berlaku.</LI>
                        </ol>
                    </td>
                </tr>
                <tr>
                    <td>KEEMPAT</td>
                    <td></td>
                    <td>:</td>
                    <td>
                        Pendidik dan Tenaga Kependidikan wajib menjamin perlindungan anak serta menciptakan lingkungan
                        belajar yang aman, nyaman, inklusif, dan ramah anak.
                    </td>
                </tr>
                <tr>
                    <td>KELIMA</td>
                    <td></td>
                    <td>:</td>
                    <td>
                        Keputusan ini mulai berlaku pada tanggal ditetapkan dan apabila di kemudian hari terdapat
                        kekeliruan akan dilakukan perbaikan sebagaimana mestinya.
                    </td>
                </tr>
            </table>
            <table style="width: 100%;margin-top:0.5cm">
                <tr>
                    <td style="width:60%"></td>
                    <td>
                        Ditetapkan di Surabaya<br>
                        Pada Tanggal {{ date('d-m-Y', strtotime($monev->tanggal_verifikasi)) }}<br>
                        <br>
                        Bunda Pendidikan Anak Usia Dini<br>
                        Kecamatan {{ ucwords(strtolower($sekolah->kecamatan_dispenduk)) }}<br>
                        <img style="height: 3cm" src="{{ $ttd_bunda->link_ttd ?? '' }}" alt="TTD BUNDA KECAMATAN"
                            onerror="this.onerror=null;this.src='https://png.pngtree.com/png-vector/20190917/ourmid/pngtree-not-found-circle-icon-vectors-png-image_1737851.jpg'"><br>
                        {{ $bunda_kecamatan->nama }}
                    </td>
                </tr>
            </table>
            <div style="width: 100%;margin-top:0.5cm">
                Tembusan :<br>
                Yth.<br>
                1. Kepala Dinas Pendidikan Kota Surabaya<br>
                2. Bunda PAUD Kota Surabaya<br>
                3. Bunda PAUD Kelurahan {{ ucwords(strtolower($sekolah->kelurahan_dispenduk)) }}
            </div>
        </div>

        @include('monev_baru.sk_pengangkatan.gtk')
        @include('monev_baru.sk_pengangkatan.jaspel')
    </div>
</body>

</html>
