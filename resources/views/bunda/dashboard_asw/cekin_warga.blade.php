<style>
    table {
        border-collapse: collapse;
        width: 100%;
    }

    th,
    td {
        border: 1px solid #000;
    }
</style>
<div class="row">
    <div class="col-lg-12">
        <div style="width: 100%;margin: auto">
            <h3>{{ $judul }}</h3>
            <div class="row">
                <div class="col-lg-12" style="overflow: auto;padding-bottom: 10px">
                    <table class="table table-striped table-boredered">
                        <thead>
                            <tr style="border-top:1px solid #000 !important">
                                <th>Usia</th>
                                <th>Jumlah Total</th>
                                <th>Jumlah Bersekolah</th>
                                <th>Jumlah Akan Bersekolah</th>
                                <th>Jumlah Belum Bersekolah</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data as $d)
                                <tr>
                                    <td style="border-top:1px solid #000 !important">{{ $d->usia }}</td>
                                    @php
                                        $param = [
                                            'judul' => 'Data penduduk usia ' . $d->usia . ' tahun keseluruhan',
                                            'kategori' => 'total',
                                            'kelamin' => '',
                                            'usia' => $d->usia,
                                        ];
                                        $param = base64_encode(json_encode($param));
                                    @endphp
                                    <td class="text-right"
                                        style="text-decoration: underline;cursor: pointer;border-top:1px solid #000 !important"
                                        onclick="modal('{{ $jenis }}','{{ $param }}')">
                                        {{ number_format($d->jumlah_total, 0, '', '.') }}</td>
                                    @php
                                        $param = [
                                            'judul' => 'Data penduduk usia ' . $d->usia . ' tahun yang bersekolah',
                                            'kategori' => 'bersekolah',
                                            'kelamin' => '',
                                            'usia' => $d->usia,
                                        ];
                                        $param = base64_encode(json_encode($param));
                                    @endphp
                                    <td class="text-right"
                                        style="text-decoration: underline;cursor: pointer;border-top:1px solid #000 !important"
                                        onclick="modal('{{ $jenis }}','{{ $param }}')">
                                        {{ number_format($d->jumlah_bersekolah, 0, '', '.') }}</td>
                                    @php
                                        $param = [
                                            'judul' => 'Data penduduk usia ' . $d->usia . ' tahun yang akan bersekolah',
                                            'kategori' => 'akan_bersekolah',
                                            'kelamin' => '',
                                            'usia' => $d->usia,
                                        ];
                                        $param = base64_encode(json_encode($param));
                                    @endphp
                                    <td class="text-right"
                                        style="text-decoration: underline;cursor: pointer;border-top:1px solid #000 !important"
                                        onclick="modal('{{ $jenis }}','{{ $param }}')">
                                        {{ number_format($d->jumlah_akan_bersekolah, 0, '', '.') }}</td>
                                    @php
                                        $param = [
                                            'judul' =>
                                                'Data penduduk usia ' . $d->usia . ' tahun yang belum bersekolah',
                                            'kategori' => 'belum_bersekolah',
                                            'kelamin' => '',
                                            'usia' => $d->usia,
                                        ];
                                        $param = base64_encode(json_encode($param));
                                    @endphp
                                    <td class="text-right"
                                        style="text-decoration: underline;cursor: pointer;border-top:1px solid #000 !important"
                                        onclick="modal('{{ $jenis }}','{{ $param }}')">
                                        {{ number_format($d->jumlah_belum_bersekolah, 0, '', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr style="font-weight: bold">
                                <td>Total</td>
                                @php
                                    $param = [
                                        'judul' => 'Data penduduk usia 0 sd 6 tahun keseluruhan',
                                        'kategori' => 'total',
                                        'kelamin' => '',
                                        'usia' => 'semua',
                                    ];
                                    $param = base64_encode(json_encode($param));
                                @endphp
                                <td class="text-right" style="text-decoration: underline;cursor: pointer"
                                    onclick="modal('{{ $jenis }}','{{ $param }}')">
                                    {{ number_format($data->sum('jumlah_total'), 0, '', '.') }}</td>
                                @php
                                    $param = [
                                        'judul' => 'Data penduduk usia 0 sd 6 tahun yang bersekolah',
                                        'kategori' => 'bersekolah',
                                        'kelamin' => '',
                                        'usia' => 'semua',
                                    ];
                                    $param = base64_encode(json_encode($param));
                                @endphp
                                <td class="text-right" style="text-decoration: underline;cursor: pointer"
                                    onclick="modal('{{ $jenis }}','{{ $param }}')">
                                    {{ number_format($data->sum('jumlah_bersekolah'), 0, '', '.') }}
                                </td>
                                @php
                                    $param = [
                                        'judul' => 'Data penduduk usia 0 sd 6 tahun yang akan bersekolah',
                                        'kategori' => 'akan_bersekolah',
                                        'kelamin' => '',
                                        'usia' => 'semua',
                                    ];
                                    $param = base64_encode(json_encode($param));
                                @endphp
                                <td class="text-right" style="text-decoration: underline;cursor: pointer"
                                    onclick="modal('{{ $jenis }}','{{ $param }}')">
                                    {{ number_format($data->sum('jumlah_akan_bersekolah'), 0, '', '.') }}
                                </td>
                                @php
                                    $param = [
                                        'judul' => 'Data penduduk usia 0 sd 6 tahun yang belum bersekolah',
                                        'kategori' => 'belum_bersekolah',
                                        'kelamin' => '',
                                        'usia' => 'semua',
                                    ];
                                    $param = base64_encode(json_encode($param));
                                @endphp
                                <td class="text-right" style="text-decoration: underline;cursor: pointer"
                                    onclick="modal('{{ $jenis }}','{{ $param }}')">
                                    {{ number_format($data->sum('jumlah_belum_bersekolah'), 0, '', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
