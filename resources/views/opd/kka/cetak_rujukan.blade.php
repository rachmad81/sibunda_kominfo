<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SURAT RUJUKAN KENARI</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            width: 210mm;
            height: 200mm;
            margin: auto;
            padding: 20px;
            border: 1px solid #000;
        }

        .header {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .content {
            font-size: 14px;
            line-height: 1.5;
        }

        .ttd {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
        }

        .ttd .left {
            text-align: left;
        }
    </style>
</head>

<body>
    <div class="header">
        SURAT RUJUKAN KENARI
        <br> No:{{ $data->no_surat }}
    </div>
    <div class="content">
        <p><strong>Kelompok BKB/PAUD</strong> {{ $data->nama_sekolah }}</p>
        <p><strong>Kelurahan:</strong> {{ $data->kelurahan }}</p>
        <p><strong>Kecamatan:</strong> {{ $data->kecamatan }}</p>

        <br>
        <p><strong>Surabaya, Tanggal</strong> {{ date('d/m/Y', strtotime($data->tanggal)) }}</p>
        <p><strong>Kepada Yth:</strong></p>
        <p><strong>Ka Puskesmas:</strong> {{ $data->kecamatan }}</p>
        <p><strong>Di Surabaya</strong></p>
        <p><strong>Bersama Ini Kami Kirimkan:</strong></p>
        <p>
        <ul>
            <li>Nama anak : {{ $data->nama_anak }}</li>
            <li>NIK/BPJS : {{ $data->nik }}</li>
            <li>Nama Ibu : {{ $data->nama_ibu }}</li>
            <li>Jenis Kelamin : {{ $data->jk }}</li>
            <li>Umur : {{ $data->umur }} bulan (Tanggal Lahir {{ date('d/m/Y', strtotime($data->tgl_lahir)) }})</li>
        </ul>
        </p>
        <p><strong>Masalah:</strong></p>
        <p>
        <ol>
            <li>{{ $data->lambat_tugas }}</li>
            <li>Masalah Lain : <br>{{ $data->masalah_lain }}</li>
        </ol>
        </p>
    </div>
    <div class="ttd">
        <div class="left">
            <p>Sebagai Kelengkapa rujukan kami sertakan Buku KIA. Demikian yang dapat kami sampaikan, terima Kasih.</p>
            <p>Ka BKB/PAUD,</p>
            <br><br>
            <p><u>{{ $data->kepala_sekolah }}</u></p>
        </div>
    </div>
</body>

</html>
