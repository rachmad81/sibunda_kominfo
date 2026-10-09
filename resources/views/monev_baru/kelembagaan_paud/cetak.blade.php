<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cetak Monitoring Kelembagaan PAUD</title>
</head>
<style>
    table tr td {
        vertical-align: top;
    }

    .ttd tr td {
        text-align: center !important;
        vertical-align: top;
    }
</style>

<body>
    <div style="width:21cm;margin: auto;font-size: 12pt">
        <div style="width:100%;text-align: center;font-weight: bold">
            INSTRUMEN PEMBINAAN KELEMBAGAAN SATUAN PAUD TAHUN {{ $monev->tahun }}
        </div>

        <div style="width:100%;">
            <table style="width: 100%;border-collapse: collapse" border="1">
                <tr>
                    <td style="widtH: 2cm">NPSN</td>
                    <td style="width: 0.5cm">:</td>
                    <td>{{ $monev->npsn }}</td>
                </tr>
                <tr>
                    <td style="widtH: 2cm">Bentuk Layanan</td>
                    <td style="width: 0.5cm">:</td>
                    <td>{{ $monev->jenis_satuan }}</td>
                </tr>
                <tr>
                    <td style="widtH: 2cm">Nama Satuan PAUD</td>
                    <td style="width: 0.5cm">:</td>
                    <td>{{ $monev->nama_satuan }}</td>
                </tr>
                <tr>
                    <td style="widtH: 2cm">Alamat Satuan PAUD</td>
                    <td style="width: 0.5cm">:</td>
                    <td>{{ $monev->alamat_satuan }}</td>
                </tr>
                <tr>
                    <td style="widtH: 2cm">Kelurahan/Kecamatan</td>
                    <td style="width: 0.5cm">:</td>
                    <td>{{ $monev->kelurahan }} / {{ $monev->kecamatan }}</td>
                </tr>
                <tr>
                    <td style="widtH: 2cm">Nama Kepala Sekolah</td>
                    <td style="width: 0.5cm">:</td>
                    <td>{{ $monev->nama_ks }}</td>
                </tr>
                <tr>
                    <td style="widtH: 2cm">Nomor Telepon KS</td>
                    <td style="width: 0.5cm">:</td>
                    <td>{{ $monev->no_ks }}</td>
                </tr>
                <tr>
                    <td style="widtH: 2cm">Periode</td>
                    <td style="width: 0.5cm">:</td>
                    <td>{{ $monev->periode }}</td>
                </tr>
            </table>
        </div>
        <div style="width:100%;margin-top: 0.5cm">
            <table style="width:100%;border-collapse: collapse" border="1">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Uraian</th>
                        <th>Status</th>
                        <th>Keterangan</th>
                        <th>Catatan Potensi Resiko</th>
                        <th>Catatan Bunda PAUD Kel/kec</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>Izin Operasional yang berlaku</td>
                        <td>{{ $monev->status_izin_operasional == 'true' ? 'Berlaku' : 'Tidak berlaku' }}</td>
                        <td>
                            Nomor Izin Operasional {{ $monev->no_izin_operasional }}<br>
                            Masa Berlaku : {{ $monev->tanggal_terbit }} s.d {{ $monev->tanggal_berakhir }}<br>
                            <br><b>Catatan:</b>
                            <br>{{ $monev->keterangan_izin_operasional }}
                        </td>
                        <td>
                            a. Sekolah habis izin operasional<br>
                            b. sekolah tidak berizin
                        </td>
                        <td>
                            Status Validasi
                            {{ $monev->validasi_izin_operasional == 'true' ? 'Sesuai' : 'Tidak sesuai' }}<br>
                            <br><b>Catatan :</b>
                            <br>{{ $monev->catatan_validasi_izin_operasional }}
                        </td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>Kesesuaian alamat sekolah Izin Operasional</td>
                        <td>{{ $monev->status_alamat_ijop == 'true' ? 'Sesuai' : 'Tidak sesuai' }}</td>
                        <td>
                            Alamat sesuai izin: {{ $monev->alamat_ijop }}<br>
                            Alamat dilapangan: {{ $monev->alamat_riil_sp }}
                        </td>
                        <td>
                            a. Beroperasi tidak sesuai dalam ijin operasional<br>
                            b. Satu lokasi dengan sekolah lain : ................. dengan status izin :
                            .......................
                        </td>
                        <td>
                            {{ $monev->catatan_alamat_ijop }}
                        </td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td>Akreditasi yang berlaku</td>
                        <td>{{ $monev->akreditasi }}</td>
                        <td>
                            Nomor SK: {{ $monev->no_akreditasi }}<br>
                            Masa Berlaku: {{ $monev->berlaku_akreditasi }}<br>
                        </td>
                        <td>
                            a. Akreditasi sudah mati<br>
                            b. Akreditasi ditingkatkan
                        </td>
                        <td>
                            Status Akreditasi:
                            {{ $monev->status_akreditasi == 'true' ? 'Sesuai' : 'Tidak sesuai' }}<br>
                            <br><b>Catatan:</b><br>
                            - {{ $monev->catatan_akreditasi }}<br>
                            - {{ $monev->catatan_status_akreditasi }}
                        </td>
                    </tr>
                    <tr>
                        <td rowspan="4">4</td>
                        <td>
                            Jumlah Data Murid:<br>
                            a. DAPODIK {{ $monev->jumlah_murid_dapodik }} anak<br>
                            b. Profil {{ $monev->jumlah_murid_profil }} anak<br>
                            c. Riil {{ $monev->jumlah_murid_riil }} anak<br>
                            d. Jumlah Rombel {{ $monev->jumlah_rombel }} rombel/kelas<br>
                            e. Ketersediaan dokumen pendukung
                        </td>
                        <td>{{ $monev->status_jumlah_murid == 'true' ? 'Sesuai' : 'Tidak sesuai' }}</td>
                        <td rowspan="4">
                            Kesesuaian Data dengan riil:<br>
                            a. Dapodik : {{ $monev->status_jumlah_murid == 'true' ? 'Sesuai' : 'Tidak sesuai' }}<br>
                            b. Profil : {{ $monev->status_jumlah_murid == 'true' ? 'Sesuai' : 'Tidak sesuai' }}<br>
                            Alasan Tidak sesuai:{{ $monev->catatan_jumlah_murid }}
                        </td>
                        <td rowspan="4">
                            a. Data murid yang dientrykan pada Dapodik dan/atau profil > data riil sehingga berpotensi
                            kelebihan pencairan BOSP dan/atau jaspel<br>
                            b. Pelanggaran Standar Pengelolaan (Permendikbud 26/2025)
                        </td>
                        <td rowspan="4">
                            {{ $monev->catatan_jumlah_murid }}
                        </td>
                    </tr>
                    <tr>
                        <td>Daftar hadir harian / absensi</td>
                        <td>{{ $monev->daftar_hadir_murid == 'true' ? 'Ada' : 'Tidak ada' }}</td>
                    </tr>
                    <tr>
                        <td>Buku induk + foto siswa</td>
                        <td>
                            {{ $monev->buku_induk_murid == 'true' ? 'Ada' : 'Tidak ada' }} +
                            {{ $monev->foto_murid == 'true' ? 'Ada' : 'Tidak ada' }}
                        </td>
                    </tr>
                    <tr>
                        <td>KK dan Akte</td>
                        <td>
                            @switch($monev->kk_akte_murid)
                                @case('ada')
                                    Ada Lengkap
                                @break

                                @case('sebagian')
                                    Sebagian
                                @break

                                @case('tidakada')
                                    Tidak Ada
                                @break

                                @default
                            @endswitch
                        </td>
                    </tr>
                    <tr>
                        <td>5</td>
                        <td>
                            Jumlah Data Guru dan Tenaga Kependidikan (PTK) sesuai SK Pengangkatan:<br>
                            a. DAPODIK : {{ $monev->jumlah_ptk_dapodik }} orang<br>
                            b. Profil : {{ $monev->jumlah_ptk_profil }} orang<br>
                            c. Riil : {{ $monev->jumlah_ptk_riil }} orang<br>
                            d. Jadwal Kegiatan Belajar Mengajar (KBM/SKPBM)
                            {{ $monev->status_skpbm_ptk == 'true' ? 'Ada' : 'Tidak ada' }}
                        </td>
                        <td>
                            {{ $monev->status_jumlah_ptk == 'true' ? 'Sesuai' : 'Tidak sesuai' }}
                        </td>
                        <td>{{ $monev->catatan_jumlah_ptk }}</td>
                        <td>
                            a. Data murid yang dientrykan pada Dapodik dan/atau profil > data riil sehingga berpotensi
                            kelebihan pencairan jaspel pada PTK (tidak sesuai rasio riil)<br>
                            b. PTK tidak aktif namun terdata<br>
                            c. SK Pengangkatan fiktif/ tidak sah<br>
                            d. Hubungan kekerabatan Guru dan Tenaga Kependidikan dengan Kepala sekolah/Ketua RW/Ketua
                            Yayasan:<br>
                            Nama :<br>
                            Hubungan kekerabatan:<br>
                            Kompetensi dan kualifikasi :<br>
                            (Dapat ditambahkan sesuai jumlah Guru dan Tenaga Kependidikan yang mempunyai hubungan
                            kekerabatan dengan Kepala sekolah/Ketua RW/Ketua Yayasan)
                        </td>
                        <td>
                            <b>Catatan SKPBM:</b><br>
                            Status: {{ $monev->status_skpbm_ptk == 'true' ? 'Ada' : 'Tidak ada' }}<br>
                            Catatan: {{ $monev->catatan_skpbm_ptk }}<br><br>

                            <b>Catatan SK Pengankatan:</b><br>
                            Status:
                            {{ $monev->status_sk_pengangkatakan_ptk == 'true' ? 'Sah' : 'Tidak sah/Tidak ada' }}<br>
                            Catatan: {{ $monev->catatan_sk_pengangkatan_ptk }}<br><br>

                            <b>Catatan Hubungan Kekerabatan:</b><br>
                            Catatan: {{ $monev->catatan_hubungan_kekerabatan }}<br><br>
                        </td>
                    </tr>
                    <tr>
                        <td>6</td>
                        <td>
                            Penerimaan Jasa Pelayanan<br>
                            a. Jumlah Guru penerima: {{ $monev->jumlah_ptk_jaspel }} orang<br>
                            b. Jumlah Guru Non Penerima {{ $monev->jumlah_ptk_jaspel_riil }} orang<br>
                            c. Pemenuhan rasio guru: {{ $monev->pemenuhan_rasio }}
                        </td>
                        <td>
                            {{ $monev->status_penerima_jaspel == 'true' ? 'Sesuai' : 'TIdak sesuai' }}
                        </td>
                        <td>
                            {{ $monev->catatan_jaspel }}
                        </td>
                        <td>
                            a. Guru penerima fiktif.<br>
                            b. Potensi kelebihan pembayaran Jaspel melampaui rasio riil murid
                        </td>
                        <td>
                            Rasio: {{ $monev->pemenuhan_rasio }}<br>
                            @php
                                $status_bukti = [
                                    '0' => '',
                                    'ada' => 'Ada',
                                    'belum' => 'Sebagian',
                                    'tidakada' => 'Tidak ada',
                                ];
                            @endphp
                            <br>
                            Bukti Penerimaan: {!! $status_bukti[$monev->status_bukti_penerimaan_jaspel ?? 0] !!}
                        </td>
                    </tr>
                    <tr>
                        <td>7</td>
                        <td>Pengelolaan Dana BOSP</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>a</td>
                        <td>RKAS disusun sesuai juknis BOP dan disahkan</td>
                        <td>{{ $monev->status_rkas == 'true' ? 'Ada' : 'Tidak ada' }}</td>
                        <td></td>
                        <td>Ketidakpatuhan administrasi</td>
                        <td>{{ $monev->catatan_rkas }}</td>
                    </tr>
                    <tr>
                        <td>b</td>
                        <td>Belanja Dana BOSP Sesuai dengan Item di RKAS Sem II 2025</td>
                        <td>{{ $monev->status_nota_rkas == 'true' ? 'Sesuai' : 'Tidak sesuai' }}</td>
                        <td></td>
                        <td></td>
                        <td>{{ $monev->catatan_nota_rkas }}</td>
                    </tr>
                    <tr>
                        <td>c</td>
                        <td>Kelengkapan Bukti Pembelian (Invoice, Nota/Faktur, Surat Pesanan, BAST, Bukti Bayar Bank)
                        </td>
                        <td>{{ $monev->status_pembelian_rkas == 'true' ? 'Lengkap' : 'Tidak lengkap' }}</td>
                        <td></td>
                        <td></td>
                        <td>{{ $monev->catatan_pembelian_rkas }}</td>
                    </tr>
                    <tr>
                        <td>d</td>
                        <td>Terdapat pelabelan permanen pada barang modal dari BOSP</td>
                        <td>{{ $monev->status_pelabelan == 'true' ? 'Sudah' : 'Belum' }}</td>
                        <td></td>
                        <td></td>
                        <td>{{ $monev->catatan_pelabelan }}</td>
                    </tr>
                    <tr>
                        <td>e</td>
                        <td>Mendapatkan Pembinaan dari Dinas Pendidikan Terkait BOSP / Pengelolaan</td>
                        <td>{{ $monev->status_monitoring_dinas == 'true' ? 'Pernah' : 'Belum pernah' }}</td>
                        <td></td>
                        <td></td>
                        <td>{{ $monev->catatan_monitoring_dinas }}</td>
                    </tr>
                    <tr>
                        <td>8</td>
                        <td>Jejak digital sistem</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>a</td>
                        <td>Tgl. terakhir sinkronisasi Dapodik (SPTJM Dapodik)</td>
                        <td>{{ $monev->tanggal_sinkron_dapodik }}</td>
                        <td>{{ $monev->tanggal_sinkron_dapodik_riil }}</td>
                        <td>Data tidak mutakhir</td>
                        <td>{{ $monev->catatan_tanggal_sinkron_dapodik }}</td>
                    </tr>
                    <tr>
                        <td>b</td>
                        <td>Tgl terakhir update profil</td>
                        <td>{{ $monev->tanggal_update_profil }}</td>
                        <td>{{ $monev->tanggal_update_profil_riil }}</td>
                        <td>Data tidak mutakhir</td>
                        <td>{{ $monev->catatan_tanggal_update_profil }}</td>
                    </tr>
                    <tr>
                        <td>9</td>
                        <td>Khusus PPT</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>a</td>
                        <td>Berlokasi di balai RW</td>
                        <td>{{ $monev->status_ppt_balai_rw == 'true' ? 'Ya' : 'Tidak' }}</td>
                        <td>{{ $monev->catatan_kondisi_balai_rw }}</td>
                        <td>Lokasi fiktif atau pindah ke rumah pribadi</td>
                        <td>{{ $monev->catatan_ppt_balai_rw }}</td>
                    </tr>
                    <tr>
                        <td>b</td>
                        <td>
                            Penerimaan PMT<br>
                            • Jumlah Murid Penerima: {{ $monev->jumlah_murid_pmt }} anak<br>
                            • Jumlah Murid Rill : {{ $monev->jumlah_murid_pmt_riil }} anak sesuai 4c<br>
                            • Jadwal Pemberian PMT: {{ $monev->jadwal_pmt }}<br>
                            • c. Detail Menu Makanan PMT : {{ $monev->menu_pmt }}
                        </td>
                        <td>{{ $monev->status_murid_pmt == 'true' ? 'Sesuai' : 'Tidak sesuai' }}</td>
                        <td>
                            Jumlah Tidak Sesuai:<br>
                            Alasan Tidak sesuai:<br>
                        </td>
                        <td>
                            a. pemotongan dana PMT<br>
                            b. Murid penerima fiktif.<br>
                            c. Asupan gizi tidak sesuai standar
                        </td>
                        <td>
                            {{ $monev->catatan_murid_pmt }}
                        </td>
                    </tr>
                    <tr>
                        <td>c</td>
                        <td>Penerima Beasiswa S1 RPL PAUD</td>
                        <td>{{ $monev->status_penerima_beasiswa_s1 == 'true' ? 'Ada' : 'Tidak ada' }}</td>
                        <td>
                            Jumlah penerima = {{ $monev->jumlah_penerima_beasiswa_s1 }}<br>
                            Jumlah penerima riil = {{ $monev->jumlah_penerima_beasiswa_s1_riil }}
                        </td>
                        <td>PTK penerima fiktif/pindah instansi</td>
                        <td>{{ $monev->catatan_penerima_beasiswa_s1 }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div style="width:100%;margin-top: 0.5cm">
            <table style="width:100%;border-collapse: collapse" border="1">
                <tr>
                    <td>KESIMPULAN HASIL PEMBINAAN</td>
                </tr>
                <tr>
                    <td>
                        @if ($monev->kesimpulan_rekomendasi == 'sesuai')
                            &#10004;
                        @else
                            ☐
                        @endif Data dan dokumen sesuai kondisi riil<br>
                        @if ($monev->kesimpulan_rekomendasi == 'admin')
                            &#10004;
                        @else
                            ☐
                        @endif Terdapat ketidaksesuaian administratif (perlu pembinaan)<br>
                        @if ($monev->kesimpulan_rekomendasi == 'signifikan')
                            &#10004;
                        @else
                            ☐
                        @endif Terdapat ketidaksesuaian signifikan (perlu klarifikasi lanjutan)<br>
                    </td>
                </tr>
            </table>
        </div>
        <div style="width:100%;margin-top:0.5cm">
            <b>Rekomendasi Tindak Lanjut</b><br>
            {{ $monev->rekomendasi_tindak_lanjut }}
        </div>
        <div style="width:100%;margin-top:0.5cm">
            <b>PERNYATAAN VALIDASI</b><br>
            Kami yang bertanda tangan di bawah ini menyatakan bahwa seluruh data dan dokumen yang diperiksa dalam
            pembinaan ini telah sesuai kondisi riil. Apabila di kemudian hari ditemukan ketidaksesuaian, pemalsuan data,
            atau penyalahgunaan anggaran, maka menjadi tanggung jawab pihak satuan PAUD sesuai ketentuan peraturan
            perundang-undangan.
        </div>
        <div style="width:100%;margin-top:0.5cm">
            <table style="width:100%" class="ttd">
                <tr>
                    <td style="width:7cm"></td>
                    <td style="width:7cm"></td>
                    <td style="width:7cm">Surabaya, {{ $monev->tanggal_kunjungan }}</td>
                </tr>
                <tr>
                    <td>
                        BUNDA KELURAHAN {{ $monev->kelurahan }}<br>
                        <img onerror="this.onerror=null;this.src='https://png.pngtree.com/png-vector/20190917/ourmid/pngtree-not-found-circle-icon-vectors-png-image_1737851.jpg'"
                            src="{{ $monev->ttd_bunda_kelurahan }}" style="height:3cm" alt="TTD Bunda Kelurahan"><br>
                        {{ $monev->nama_bunda_kelurahan }}
                    </td>
                    <td></td>
                    <td>
                        Kepala {{ $monev->nama_satuan }}<br>
                        <img onerror="this.onerror=null;this.src='https://png.pngtree.com/png-vector/20190917/ourmid/pngtree-not-found-circle-icon-vectors-png-image_1737851.jpg'"
                            src="{{ $monev->ttd_ks }}" style="height:3cm" alt="TTD KS"><br>
                        {{ $monev->nama_ks }}
                    </td>
                </tr>

                <tr>
                    <td></td>
                    <td>
                        BUNDA KECMAATAN {{ $monev->kecamatan }}<br>
                        <img onerror="this.onerror=null;this.src='https://png.pngtree.com/png-vector/20190917/ourmid/pngtree-not-found-circle-icon-vectors-png-image_1737851.jpg'"
                            src="{{ $monev->ttd_bunda_kecamatan }}" style="height:3cm" alt="TTD Bunda Kecamatan"><br>
                        {{ $monev->nama_bunda_kecamatan }}
                    </td>
                    <td></td>
                </tr>
            </table>
        </div>
    </div>
</body>

</html>
