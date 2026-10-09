<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cetak Monev Jaspel</title>
</head>
<style>
    table tr td {
        vertical-align: top;
    }
</style>

<body>
    <div style="width: 21cm;margin: auto">
        <div style="font-size: 12pt;font-weight:bold;text-align: center">
            BERITA ACARA MONITORING JASA PELAYANAN MASYARAKAT PADA SATUAN PAUD
        </div>
        @php
            $bulan = App\Http\Libraries\Convert::fullDate($monev->tgl_monev ?? '');
        @endphp
        <div style="font-size: 12pt;margin-top:0.5cm">
            Pada hari {{ App\Http\Libraries\Convert::getHari(date('D', strtotime($monev->tgl_monev ?? ''))) }} tanggal
            {{ $bulan['hari'] }} Bulan {{ $bulan['bulan'] }} Tahun {{ $bulan['tahun'] }} telah dilakukan Monitoring
            Kegiatan Belajar Mengajar dengan hasil sebagai berikut :
        </div>
        <div style="font-size: 12pt;margin-top:0.5cm">
            <table style="width: 60%">
                <tr>
                    <td style="width: 4cm">Nama Lembaga</td>
                    <td>:</td>
                    <td>{{ $monev->nama_lembaga ?? '' }}</td>
                </tr>
                <tr>
                    <td>NPSN</td>
                    <td>:</td>
                    <td>{{ $monev->npsn ?? '' }}</td>
                </tr>
                <tr>
                    <td>Akreditasi</td>
                    <td>:</td>
                    <td>{{ $monev->akreditasi ?? '' }}</td>
                </tr>
                <tr>
                    <td>Alamat</td>
                    <td>:</td>
                    <td>{{ $monev->alamat_lembaga ?? '' }}</td>
                </tr>
                <tr>
                    <td>Kecamatan</td>
                    <td>:</td>
                    <td>{{ $monev->kecamatan ?? '' }}</td>
                </tr>
                <tr>
                    <td>Nama Kepala Lebaga</td>
                    <td>:</td>
                    <td>{{ $monev->nama_ks ?? '' }}</td>
                </tr>
            </table>
        </div>
        <div style="font-size: 12pt;margin-top:0.5cm">
            <table style="width: 60%;border-collapse: collapse" border="1">
                <tr>
                    <td>Izin Operasional</td>
                    <td style="font-weight: bold">{{ $monev->ijop ?? '' }}</td>
                </tr>
                <tr>
                    <td>Tanggal Berakhir Ijop</td>
                    <td style="font-weight: bold">
                        @php
                            $bulan1 = App\Http\Libraries\Convert::fullDate($monev->tgl_berakhir_ijop ?? '');
                        @endphp
                        {{ $bulan1['hari'] }} {{ $bulan1['bulan'] }} {{ $bulan1['tahun'] }}
                    </td>
                </tr>
                <tr>
                    <td>Kegiatan Belajar Mengajar</td>
                    <td style="font-weight: bold">{{ $monev->kbm ?? '' }}</td>
                </tr>
            </table>
        </div>
        <div style="font-size: 12pt;margin-top:0.5cm">
            <table class="table table-bordered">
                <tr>
                    <td>1. Berapa jumlah murid di Profil Sekolah?</td>
                </tr>
                <tr>
                    <td style="padding-left: 0.5cm">
                        {{ $monev->jumlah_murid_profil ?? '' }}
                    </td>
                </tr>

                <tr>
                    <td>2. Berapa jumlah murid di Dapodik?</td>
                </tr>
                <tr>
                    <td style="padding-left: 0.5cm">
                        {{ $monev->jumlah_murid_dapodik ?? '' }}
                    </td>
                </tr>

                <tr>
                    <td>3. Apakah ada daftar hadir murid?</td>
                </tr>
                <tr>
                    <td style="padding-left: 0.5cm">
                        {{ $monev->daftar_hadir_siswa ?? '' }}
                    </td>
                </tr>

                <tr>
                    <td>4. Berapa jumlah murid di daftar kehadiran lembaga?</td>
                </tr>
                <tr>
                    <td style="padding-left: 0.5cm">
                        {{ $monev->jumlah_murid_daftar ?? '' }}
                    </td>
                </tr>

                <tr>
                    <td>5. Berapa jumlah murid riil saat pelaksanaan KBM?</td>
                </tr>
                <tr>
                    <td style="padding-left: 0.5cm">
                        {{ $monev->jumlah_murid_riil ?? '' }}
                    </td>
                </tr>

                <tr>
                    <td>6. Apakah jumlah murid Dapodik dan Profil Sekolah sesuai?</td>
                </tr>
                <tr>
                    <td style="padding-left: 0.5cm">
                        {{ $monev->status_jumlah_siswa ?? '' }}
                    </td>
                </tr>

                <tr>
                    <td>7. Jika tidak sesuai, sebutkan alasannya!</td>
                </tr>
                <tr>
                    <td style="padding-left: 0.5cm">
                        {{ $monev->alasan_status ?? '' }}
                    </td>
                </tr>

                <tr>
                    <td>8. Berapa jumlah (Rp) penerimaan Jaspel bulan Lalu?</td>
                </tr>
                <tr>
                    <td style="padding-left: 0.5cm">
                        {{ $monev->penerimaan_jaspel ?? '' }}
                    </td>
                </tr>

                <tr>
                    <td>9. Berapa penerimaan Jaspel tiap orang?</td>
                </tr>
                <tr>
                    <td style="padding-left: 0.5cm">
                        {{ $monev->penerimaan_jaspel_orang ?? '' }}
                    </td>
                </tr>

                <tr>
                    <td>10. Apakah ada pemotongan jaspel langsung dari Kepala Sekolah atau pihak lain?</td>
                </tr>
                <tr>
                    <td style="padding-left: 0.5cm">
                        {{ $monev->pemotongan_jaspel ?? '' }}
                    </td>
                </tr>

                <tr>
                    <td>11. Apakah tiap penerima Jaspel sudah tanda tangan konfirmasi penerimaan Jaspel bulan lalu?</td>
                </tr>
                <tr>
                    <td style="padding-left: 0.5cm">
                        {{ $monev->ttd_penerimaan ?? '' }}
                    </td>
                </tr>

                <tr>
                    <td>Catatan</td>
                </tr>
                <tr>
                    <td style="padding-left: 0.5cm">
                        {{ $monev->catatan ?? '' }}
                    </td>
                </tr>

            </table>
        </div>
        <div style="font-size: 12pt;margin-top:0.5cm">
            <table style="width: 100%">
                <tr>
                    <td style="width: 70%"></td>
                    <td>
                        Surabaya,
                        {{ $bulan['hari'] }} {{ $bulan['bulan'] }} {{ $bulan['tahun'] }}
                    </td>
                </tr>
            </table>
        </div>
        <div style="font-size: 12pt;margin-top:0.5cm">
            <table style="width: 100%">
                <tr>
                    <td style="text-align: center;width: 50%">
                        Mengetahui,<br>
                        Kepala {{ $monev->nama_lembaga }}
                    </td>
                    <td style="text-align: center;">
                        Petugas Survey,
                    </td>
                </tr>
                <tr>
                    <td style="text-align: center">
                        <div style="height: 3cm"></div>
                        {{ $monev->nama_ks }}

                    </td>
                    <td style="text-align: center">
                        <div style="height: 3cm"></div>
                        {{ $monev->petugas_survey }}
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>

</html>
