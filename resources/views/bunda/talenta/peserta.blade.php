<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Data Peserta Talenta Seni</title>
</head>

<body>
    <div style="width: 100%;margin: auto">
        <h1>Data Peserta Talenta Seni</h1>
    </div>
    <div style="width: 100%;margin: auto">
        <table style="width: 100%">
            <tr>
                <td>Nama Pentas</td>
                <td>{{ $talenta->judul }}</td>
            </tr>
            <tr>
                <td>Tanggal Pelaksanaan</td>
                <td>{{ App\Tools\Waktu::parseFullDate(date('Y-m-d', strtotime($talenta->tanggal))) }}</td>
            </tr>
            <tr>
                <td>Tempat Pelaksanaan</td>
                <td>{{ $talenta->nama_tempat }}</td>
            </tr>
            <tr>
                <td>Asal Sekolah</td>
                <td>{{ $talenta->nama_sekolah }} ({{ $talenta->npsn }})</td>
            </tr>
        </table>
    </div>
    <div style="width: 100%;margin: auto">
        <table style="width: 100%;border-collapse: collapse" border="1">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>NIK</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($peserta as $i => $p)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $p->nama }}</td>
                        <td>{{ $p->nik }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>

</html>
