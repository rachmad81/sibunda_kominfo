<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Rekap Absensi {{ $educity->nama_tempat }}</title>
</head>

<body>
    <div style="width:100%;display: flex;justify-content: center">
        <h1>Absensi {{ $educity->nama_tempat }}</h1>
    </div>
    <div style="width:100%;">
        <h2>Lokasi = {{ $educity->alamat }}</h2>
        <h2>Waktu = {{ App\Tools\Waktu::parseFullDate($educity->tanggal) }} ({{ $educity->jam_mulai }} s.d
            {{ $educity->jam_selesai }} WIB)
        </h2>
    </div>
    <div style="width: 100%;">
        <table style="width: 100%;border-collapse: collapse;" border="1">
            <thead>
                <tr>
                    <th>No</th>
                    <th>NIK</th>
                    <th>Nama</th>
                    <th>Instansi</th>
                    <th>TTD</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $no = 1;
                @endphp
                @foreach ($absensi as $a)
                    <tr>
                        <td>{{ $no++ }}</td>
                        <td>{{ $a->nik }}</td>
                        <td>{{ $a->nama }}</td>
                        <td>{{ $a->instansi }}</td>
                        <td><img src="{{ $a->ttd }}" /></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>

</html>
