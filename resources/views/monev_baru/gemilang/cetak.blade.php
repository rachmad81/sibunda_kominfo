<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cetak Monev Gemilang</title>
</head>
<style>
    table tr td {
        vertical-align: top;
    }
</style>

<body>
    <div style="width: 21cm;margin: auto">
        <div style="font-size: 12pt;font-weight:bold;text-align: center">
            INSTRUMEN MONITORING<br>
            GERAKAN MAGANG ANTAR LINGKUNGAN (GEMILANG) TAHUN 2026
        </div>
        <div style="font-size: 12pt;margin-top: 0.5cm">
            <table style="width:100%;border-collapse: collapse" border="1">
                <tr>
                    <td style="width: 0.5cm">1</td>
                    <td>Tanggal Pelaksanaan</td>
                    <td>
                        @php
                            $tgl_pelaksanaan = App\Http\Libraries\Convert::fullDate($monev->tanggal_pelaksanaan ?? '');
                        @endphp
                        {{ $tgl_pelaksanaan['hari'] }}
                        {{ $tgl_pelaksanaan['bulan'] }}
                        {{ $tgl_pelaksanaan['tahun'] }}
                    </td>
                </tr>
                <tr>
                    <td style="width: 0.5cm">2</td>
                    <td>Waktu Pelaksanaan</td>
                    <td>
                        {{ $monev->mulai ?? '' }} s.d {{ $monev->selesai }}
                    </td>
                </tr>
                <tr>
                    <td style="width: 0.5cm">3</td>
                    <td>TK yang Magang</td>
                    <td>
                    </td>
                </tr>
                <tr>
                    <td style="width: 0.5cm">a</td>
                    <td>NPSN TK</td>
                    <td>
                        {{ $monev->npsn_tk ?? '' }}
                    </td>
                </tr>
                <tr>
                    <td style="width: 0.5cm">b</td>
                    <td>Nama TK</td>
                    <td>
                        {{ $monev->nama_tk ?? '' }}
                    </td>
                </tr>
                <tr>
                    <td style="width: 0.5cm">c</td>
                    <td>Alamat TK</td>
                    <td>
                        {{ $monev->alamat_tk ?? '' }}
                    </td>
                </tr>
                <tr>
                    <td style="width: 0.5cm">d</td>
                    <td>Jumlah Murid TK Terlibat</td>
                    <td>
                        {{ $monev->murid_tk ?? '' }}
                    </td>
                </tr>
                <tr>
                    <td style="width: 0.5cm">e</td>
                    <td>Jumlah Guru TK Terlibat</td>
                    <td>
                        {{ $monev->guru_tk ?? '' }}
                    </td>
                </tr>
                <tr>
                    <td style="width: 0.5cm">f</td>
                    <td>Nama Kepala TK</td>
                    <td>
                        {{ $monev->nama_ks_tk ?? '' }}
                    </td>
                </tr>
                <tr>
                    <td style="width: 0.5cm">g</td>
                    <td>Telp KS TK</td>
                    <td>
                        {{ $monev->telp_ks_tk ?? '' }}
                    </td>
                </tr>

                <tr>
                    <td style="width: 0.5cm">4</td>
                    <td>SD Tujuan Kegiatan</td>
                    <td>
                    </td>
                </tr>
                <tr>
                    <td style="width: 0.5cm">a</td>
                    <td>NPSN SD</td>
                    <td>
                        {{ $monev->npsn_sd ?? '' }}
                    </td>
                </tr>
                <tr>
                    <td style="width: 0.5cm">b</td>
                    <td>Nama SD</td>
                    <td>
                        {{ $monev->nama_sd ?? '' }}
                    </td>
                </tr>
                <tr>
                    <td style="width: 0.5cm">c</td>
                    <td>Alamat SD</td>
                    <td>
                        {{ $monev->alamat_sd ?? '' }}
                    </td>
                </tr>
                <tr>
                    <td style="width: 0.5cm">d</td>
                    <td>Jumlah Murid SD Terlibat</td>
                    <td>
                        {{ $monev->murid_sd ?? '' }}
                    </td>
                </tr>
                <tr>
                    <td style="width: 0.5cm">e</td>
                    <td>Jumlah Guru SD Terlibat</td>
                    <td>
                        {{ $monev->guru_sd ?? '' }}
                    </td>
                </tr>
                <tr>
                    <td style="width: 0.5cm">f</td>
                    <td>Nama Kepala SD</td>
                    <td>
                        {{ $monev->nama_ks_sd ?? '' }}
                    </td>
                </tr>
                <tr>
                    <td style="width: 0.5cm">g</td>
                    <td>Telp KS SD</td>
                    <td>
                        {{ $monev->telp_ks_sd ?? '' }}
                    </td>
                </tr>

                <tr>
                    <td style="width: 0.5cm">5</td>
                    <td>Nama Bunda PAUD</td>
                    <td>
                        {{ $monev->nama_bunda ?? '' }}
                    </td>
                </tr>
                <tr>
                    <td style="width: 0.5cm">6</td>
                    <td>Jabatan</td>
                    <td>
                        {{ $monev->jabatan ?? '' }}
                    </td>
                </tr>
                <tr>
                    <td style="width: 0.5cm">7</td>
                    <td>Telp Bunda PAUD</td>
                    <td>
                        {{ $monev->hp_bunda ?? '' }}
                    </td>
                </tr>
            </table>
        </div>
        <div style="font-size: 12pt;margin-top: 0.5cm">
            <table style="width: 100%;border-collapse: collapse" border="1">
                @php
                    $no = 1;
                @endphp
                <tr>
                    <th colspan="3">Pertanaayn</th>
                    <th>#</th>
                </tr>
                @foreach ($pertanyaan as $p)
                    <tr>
                        <td style="font-weight: bold" colspan="2">{{ $no++ }}</td>
                        <td style="font-weight: bold" colspan="4">{{ $p['kelompok'] }}</td>
                    </tr>
                    @foreach ($p['pertanyaan'] as $pt)
                        <tr>
                            <td>&nbsp;</td>
                            <td>{{ $pt['kode'] }}</td>
                            <td>{{ $pt['pertanyaan'] }}</td>
                            <td>
                                @php
                                    $kolom = 'kelompok_' . $p['kode'];
                                    $jawaban = json_decode($monev->$kolom);
                                    $jawaban = collect($jawaban)->toArray();
                                    echo !empty($jawaban[$p['kode'] . '_' . $pt['kode']])
                                        ? $jawaban[$p['kode'] . '_' . $pt['kode']]
                                        : '';
                                @endphp
                            </td>
                        </tr>
                    @endforeach
                @endforeach
                <tr>
                    <td colspan="3">Kesan</td>
                    <td colspan="4">
                        {{ $monev->kesan ?? '' }}
                    </td>
                </tr>
                <tr>
                    <td colspan="3">Saran</td>
                    <td colspan="4">
                        {{ $monev->saran ?? '' }}
                    </td>
                </tr>
            </table>
        </div>

        <div style="font-size: 12pt;margin-top: 0.5cm">
            <table>
                <tr>
                    <td style="width:15cm">&nbsp;</td>
                    <td>
                        @php
                            $tgl_monev = App\Http\Libraries\Convert::fullDate($monev->tanggal_monev ?? '');
                        @endphp
                        Surabaya,
                        {{ $tgl_monev['hari'] }}
                        {{ $tgl_monev['bulan'] }}
                        {{ $tgl_monev['tahun'] }}
                    </td>
                </tr>
            </table>
        </div>
        <div style="font-size: 12pt;margin-top: 0.5cm">
            <table>
                <tr>
                    <td style="text-align: center;width:10cm">
                        Kepala {{ $monev->nama_sd ?? '' }}
                        <div style="height: 3cm">&nbsp;</div>
                        {{ $monev->nama_ks_sd ?? '' }}
                    </td>
                    <td style="text-align: center;;width:10cm">
                        Kepala {{ $monev->nama_tk ?? '' }}
                        <div style="height: 3cm">&nbsp;</div>
                        {{ $monev->nama_ks_tk ?? '' }}
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>

</html>
