<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Data Penduduk Usia 0-6</title>
</head>

<body>
    <table style="width: 100%;border-collapse:collapse" border="3">
        <thead>
            <tr>
                <th>Usia</th>
                <th>Penduduk Total</th>
                <th>Penduduk Bersekolah</th>
                <th>Penduduk Belum Bersekolah</th>
                <th>Penduduk Sudah Disurvey</th>
                <th>Penduduk Balum Disurvey</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($penduduk as $p)
                <tr>
                    <td style="text-align: right;vertical-align: top">{{ $p->usia }}</td>
                    <td style="text-align: right;vertical-align: top">{{ number_format($p->jumlah_total, 0, '', '.') }}
                    </td>
                    <td style="text-align: right;vertical-align: top">
                        {{ number_format($p->jumlah_bersekolah, 0, '', '.') }}</td>
                    <td style="text-align: right;vertical-align: top">
                        {{ number_format($p->jumlah_belum_bersekolah, 0, '', '.') }}</td>
                    <td style="text-align: right;vertical-align: top">
                        {{ number_format($p->jumlah_belum_bersekolah_sudah_survey, 0, '', '.') }}
                    </td>
                    <td style="text-align: right;vertical-align: top">
                        {{ number_format($p->jumlah_belum_bersekolah_belum_survey, 0, '', '.') }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
