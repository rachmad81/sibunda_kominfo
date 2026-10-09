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
                <div class="col-lg-12" style="overflow: auto">
                    <table class="table table-striped table-boredered">
                        <thead>
                            <tr>
                                <th>Usia</th>
                                <th>Jumlah Anak SOTH</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data as $d)
                                <tr>
                                    <td>{{ $d->usia }}</td>
                                    @php
                                        $param = [
                                            'judul' => 'Data penduduk usia ' . $d->usia . ' tahun yang mengikuti SOTH',
                                            'kategori' => 'total',
                                            'kelamin' => '',
                                            'usia' => $d->usia,
                                        ];
                                        $param = base64_encode(json_encode($param));
                                    @endphp
                                    <td class="text-right" style="text-decoration: underline;cursor: pointer"
                                        onclick="modal('{{ $jenis }}','{{ $param }}')">
                                        {{ number_format($d->jumlah, 0, '', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr style="font-weight: bold">
                                <td>Total</td>
                                @php
                                    $param = [
                                        'judul' => 'Data penduduk usia 0 sd 6 tahun yang mengikuti SOTH',
                                        'kategori' => 'total',
                                        'kelamin' => '',
                                        'usia' => 'semua',
                                    ];
                                    $param = base64_encode(json_encode($param));
                                @endphp
                                <td class="text-right" style="text-decoration: underline;cursor: pointer"
                                    onclick="modal('{{ $jenis }}','{{ $param }}')">
                                    {{ number_format($data->sum('jumlah'), 0, '', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
