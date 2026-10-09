<div class="row">
    <div class="col-lg-12">
        <h5 style="margin-bottom:0px;margin-top: 20px">Tabel Data Penduduk Belum bersekolah</h5>
    </div>
    <div class="col-lg-12" style="overflow: auto;">
        <table class="table table-striped table-striped">
            <thead>
                <tr style="border-top:1px solid #000 !important;">
                    <th rowspan="2">Usia</th>
                    <th rowspan="2">Jumlah Belum Bersekolah</th>
                    <th colspan="2">Disurvey</th>
                    <th colspan="2">Belum Disurvey</th>
                </tr>
                <tr style="border-top:1px solid #000 !important;">
                    <th>Jumlah</th>
                    <th>Persentase</th>
                    <th>Jumlah</th>
                    <th>Persentase</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $d)
                    <tr>
                        <td style="border-top:1px solid #000 !important">{{ $d->usia }}</td>
                        @php
                            $param = [
                                'judul' => 'Data penduduk usia ' . $d->usia . ' tahun yang belum bersekolah',
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
                                'judul' =>
                                    'Data penduduk usia ' .
                                    $d->usia .
                                    ' tahun yang belum bersekolah dan sudah disurvey',
                                'kategori' => 'sudah',
                                'kelamin' => '',
                                'usia' => $d->usia,
                            ];
                            $param = base64_encode(json_encode($param));
                        @endphp
                        <td class="text-right"
                            style="text-decoration: underline;cursor: pointer;border-top:1px solid #000 !important"
                            onclick="modal('{{ $jenis }}','{{ $param }}')">
                            {{ number_format($d->jumlah_survey, 0, '', '.') }}</td>
                        <td class="text-right"
                            style="text-decoration: underline;cursor: pointer;border-top:1px solid #000 !important">
                            {{ number_format($d->persentase_survey, 2, ',', '.') }}%</td>
                        @php
                            $param = [
                                'judul' =>
                                    'Data penduduk usia ' .
                                    $d->usia .
                                    ' tahun  yang belum bersekolah dan belum disurvey',
                                'kategori' => 'belum',
                                'kelamin' => '',
                                'usia' => $d->usia,
                            ];
                            $param = base64_encode(json_encode($param));
                        @endphp
                        <td class="text-right"
                            style="text-decoration: underline;cursor: pointer;border-top:1px solid #000 !important"
                            onclick="modal('{{ $jenis }}','{{ $param }}')">
                            {{ number_format($d->jumlah_belum_survey, 0, '', '.') }}</td>
                        <td class="text-right"
                            style="text-decoration: underline;cursor: pointer;border-top:1px solid #000 !important">
                            {{ number_format($d->persentase_belum_survey, 2, ',', '.') }}%</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="font-weight: bold">
                    <td>Total</td>
                    @php
                        $param = [
                            'judul' => 'Data penduduk usia 0 sd 6 tahun yang belum bersekolah',
                            'kategori' => 'total',
                            'kelamin' => '',
                            'usia' => 'semua',
                        ];
                        $param = base64_encode(json_encode($param));
                    @endphp
                    <td class="text-right" style="text-decoration: underline;cursor: pointer"
                        onclick="modal('{{ $jenis }}','{{ $param }}')">
                        {{ number_format($data->sum('jumlah_total'), 0, '', '.') }}
                    </td>
                    @php
                        $param = [
                            'judul' => 'Data penduduk usia 0 sd 6 tahun yang belum bersekolah dan sudah disurvey',
                            'kategori' => 'sudah',
                            'kelamin' => '',
                            'usia' => 'semua',
                        ];
                        $param = base64_encode(json_encode($param));
                    @endphp
                    <td class="text-right" style="text-decoration: underline;cursor: pointer"
                        onclick="modal('{{ $jenis }}','{{ $param }}')">
                        {{ number_format($data->sum('jumlah_survey'), 0, '', '.') }}</td>
                    @php
                        $totalTotal = $data->sum('jumlah_total');
                        $totalSurvey = $data->sum('jumlah_survey');
                        $totalBelumSurvey = $data->sum('jumlah_belum_survey');
                        $persentaseSurveyTotal = $totalTotal > 0 ? ($totalSurvey / $totalTotal) * 100 : 0;
                        $persentaseBelumSurveyTotal = $totalTotal > 0 ? ($totalBelumSurvey / $totalTotal) * 100 : 0;
                    @endphp
                    <td class="text-right">
                        {{ number_format($persentaseSurveyTotal, 2, ',', '.') }}%</td>
                    @php
                        $param = [
                            'judul' => 'Data penduduk usia 0 sd 6 tahun yang belum bersekolah dan belum disurvey',
                            'kategori' => 'belum',
                            'kelamin' => '',
                            'usia' => 'semua',
                        ];
                        $param = base64_encode(json_encode($param));
                    @endphp
                    <td class="text-right" style="text-decoration: underline;cursor: pointer"
                        onclick="modal('{{ $jenis }}','{{ $param }}')">
                        {{ number_format($data->sum('jumlah_belum_survey'), 0, '', '.') }}</td>
                    <td class="text-right">
                        {{ number_format($persentaseBelumSurveyTotal, 2, ',', '.') }}%</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
