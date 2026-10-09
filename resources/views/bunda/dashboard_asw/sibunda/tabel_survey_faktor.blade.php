<div class="row">
    <div class="col-lg-12">
        <h5 style="margin-bottom:0px;margin-top: 20px">Tabel Hasil Survey, Alamat Sesuai dan Belum Bersekolah
        </h5>
    </div>
    <div class="col-lg-12" style="overflow: auto;">
        <table class="table table-striped table-striped">
            <thead>
                <tr style="border-top:1px solid #000 !important;">
                    <th rowspan="2">Usia</th>
                    <th rowspan="2">Jumlah Belum Bersekolah & Alamat Sesuai</th>
                    <th colspan="6">Hasil Survey Berdasarkan Faktor</th>
                </tr>
                <tr style="border-top:1px solid #000 !important;">
                    <th rowspan="2">Faktor 1 Ekonomi & Kerentanan</th>
                    <th rowspan="2">Faktor 2 Pengasuhan & Dukungan</th>
                    <th rowspan="2">Faktor 3 Kondisi Anak & Kesehatan</th>
                    <th rowspan="2">Faktor 4 Administrasi</th>
                    <th rowspan="2">Faktor 5 Lainnya (Rincian)</th>
                    <th rowspan="2">Selain 5 Faktor</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $d)
                    <tr>
                        <td style="border-top:1px solid #000 !important">{{ $d->usia }}</td>
                        @php
                            $param = [
                                'judul' =>
                                    'Data penduduk usia ' .
                                    $d->usia .
                                    ' tahun yang belum bersekolah dan sudah disurvey status alamat sesuai KK',
                                'kategori' => 'jumlah_survey_alamat_sesuai',
                                'kelamin' => '',
                                'usia' => $d->usia,
                            ];
                            $param = base64_encode(json_encode($param));
                        @endphp
                        <td class="text-right"
                            style="text-decoration: underline;cursor: pointer;border-top:1px solid #000 !important"
                            onclick="modal('{{ $jenis }}','{{ $param }}')">
                            {{ number_format($d->jumlah_survey_alamat_sesuai, 0, ',', '.') }}</td>

                        @php
                            $param = [
                                'judul' =>
                                    'Data penduduk usia ' .
                                    $d->usia .
                                    ' tahun yang belum bersekolah dan sudah disurvey status Faktor 1',
                                'kategori' => 'jumlah_sudah_survey_faktor1',
                                'kelamin' => '',
                                'usia' => $d->usia,
                            ];
                            $param = base64_encode(json_encode($param));
                        @endphp
                        <td class="text-right"
                            style="text-decoration: underline;cursor: pointer;border-top:1px solid #000 !important"
                            onclick="modal('{{ $jenis }}','{{ $param }}')">
                            {{ number_format($d->jumlah_sudah_survey_faktor1, 0, ',', '.') }}</td>
                        @php
                            $param = [
                                'judul' =>
                                    'Data penduduk usia ' .
                                    $d->usia .
                                    ' tahun yang belum bersekolah dan sudah disurvey status Faktor 2',
                                'kategori' => 'jumlah_sudah_survey_faktor2',
                                'kelamin' => '',
                                'usia' => $d->usia,
                            ];
                            $param = base64_encode(json_encode($param));
                        @endphp
                        <td class="text-right"
                            style="text-decoration: underline;cursor: pointer;border-top:1px solid #000 !important"
                            onclick="modal('{{ $jenis }}','{{ $param }}')">
                            {{ number_format($d->jumlah_sudah_survey_faktor2, 0, ',', '.') }}</td>
                        @php
                            $param = [
                                'judul' =>
                                    'Data penduduk usia ' .
                                    $d->usia .
                                    ' tahun yang belum bersekolah dan sudah disurvey status Faktor 3',
                                'kategori' => 'jumlah_sudah_survey_faktor3',
                                'kelamin' => '',
                                'usia' => $d->usia,
                            ];
                            $param = base64_encode(json_encode($param));
                        @endphp
                        <td class="text-right"
                            style="text-decoration: underline;cursor: pointer;border-top:1px solid #000 !important"
                            onclick="modal('{{ $jenis }}','{{ $param }}')">
                            {{ number_format($d->jumlah_sudah_survey_faktor3, 0, ',', '.') }}</td>
                        @php
                            $param = [
                                'judul' =>
                                    'Data penduduk usia ' .
                                    $d->usia .
                                    ' tahun yang belum bersekolah dan sudah disurvey status Faktor 4',
                                'kategori' => 'jumlah_sudah_survey_faktor4',
                                'kelamin' => '',
                                'usia' => $d->usia,
                            ];
                            $param = base64_encode(json_encode($param));
                        @endphp
                        <td class="text-right"
                            style="text-decoration: underline;cursor: pointer;border-top:1px solid #000 !important"
                            onclick="modal('{{ $jenis }}','{{ $param }}')">
                            {{ number_format($d->jumlah_sudah_survey_faktor4, 0, ',', '.') }}</td>
                        @php
                            $param = [
                                'judul' =>
                                    'Data penduduk usia ' .
                                    $d->usia .
                                    ' tahun yang belum bersekolah dan sudah disurvey status Faktor 5',
                                'kategori' => 'jumlah_sudah_survey_faktor5',
                                'kelamin' => '',
                                'usia' => $d->usia,
                            ];
                            $param = base64_encode(json_encode($param));
                        @endphp
                        <td class="text-right"
                            style="text-decoration: underline;cursor: pointer;border-top:1px solid #000 !important"
                            onclick="modal('{{ $jenis }}','{{ $param }}')">
                            {{ number_format($d->jumlah_sudah_survey_faktor5, 0, ',', '.') }}</td>
                        @php
                            $param = [
                                'judul' =>
                                    'Data penduduk usia ' .
                                    $d->usia .
                                    ' tahun yang belum bersekolah dan sudah disurvey status selain 5 Faktor',
                                'kategori' => 'jumlah_sudah_survey_lain_faktor',
                                'kelamin' => '',
                                'usia' => $d->usia,
                            ];
                            $param = base64_encode(json_encode($param));
                        @endphp
                        <td class="text-right"
                            style="text-decoration: underline;cursor: pointer;border-top:1px solid #000 !important"
                            onclick="modal('{{ $jenis }}','{{ $param }}')">
                            {{ number_format($d->jumlah_sudah_survey_lain_faktor, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="font-weight: bold">
                    <td>Total</td>
                    @php
                        $param = [
                            'judul' =>
                                'Data penduduk usia 0 sd 6 tahun yang belum bersekolah dan sudah disurvey status alamat sesuai KK',
                            'kategori' => 'jumlah_survey_alamat_sesuai',
                            'kelamin' => '',
                            'usia' => 'semua',
                        ];
                        $param = base64_encode(json_encode($param));
                    @endphp
                    <td class="text-right"
                        style="text-decoration: underline;cursor: pointer;border-top:1px solid #000 !important"
                        onclick="modal('{{ $jenis }}','{{ $param }}')">
                        {{ number_format($data->sum('jumlah_survey_alamat_sesuai'), 0, ',', '.') }}</td>

                    @php
                        $param = [
                            'judul' =>
                                'Data penduduk usia 0 sd 6 tahun yang belum bersekolah dan disurvey status Faktor 1',
                            'kategori' => 'jumlah_sudah_survey_faktor1',
                            'kelamin' => '',
                            'usia' => 'semua',
                        ];
                        $param = base64_encode(json_encode($param));
                    @endphp
                    <td class="text-right" style="text-decoration: underline;cursor: pointer"
                        onclick="modal('{{ $jenis }}','{{ $param }}')">
                        {{ number_format($data->sum('jumlah_sudah_survey_faktor1'), 0, ',', '.') }}</td>
                    @php
                        $param = [
                            'judul' =>
                                'Data penduduk usia 0 sd 6 tahun yang belum bersekolah dan disurvey status Faktor 2',
                            'kategori' => 'jumlah_sudah_survey_faktor2',
                            'kelamin' => '',
                            'usia' => 'semua',
                        ];
                        $param = base64_encode(json_encode($param));
                    @endphp
                    <td class="text-right" style="text-decoration: underline;cursor: pointer"
                        onclick="modal('{{ $jenis }}','{{ $param }}')">
                        {{ number_format($data->sum('jumlah_sudah_survey_faktor2'), 0, ',', '.') }}</td>
                    @php
                        $param = [
                            'judul' =>
                                'Data penduduk usia 0 sd 6 tahun yang belum bersekolah dan disurvey status Faktor 3',
                            'kategori' => 'jumlah_sudah_survey_faktor3',
                            'kelamin' => '',
                            'usia' => 'semua',
                        ];
                        $param = base64_encode(json_encode($param));
                    @endphp
                    <td class="text-right" style="text-decoration: underline;cursor: pointer"
                        onclick="modal('{{ $jenis }}','{{ $param }}')">
                        {{ number_format($data->sum('jumlah_sudah_survey_faktor3'), 0, ',', '.') }}</td>
                    @php
                        $param = [
                            'judul' =>
                                'Data penduduk usia 0 sd 6 tahun yang belum bersekolah dan disurvey status Faktor 4',
                            'kategori' => 'jumlah_sudah_survey_faktor4',
                            'kelamin' => '',
                            'usia' => 'semua',
                        ];
                        $param = base64_encode(json_encode($param));
                    @endphp
                    <td class="text-right" style="text-decoration: underline;cursor: pointer"
                        onclick="modal('{{ $jenis }}','{{ $param }}')">
                        {{ number_format($data->sum('jumlah_sudah_survey_faktor4'), 0, ',', '.') }}</td>
                    @php
                        $param = [
                            'judul' =>
                                'Data penduduk usia 0 sd 6 tahun yang belum bersekolah dan disurvey status Faktor 5',
                            'kategori' => 'jumlah_sudah_survey_faktor5',
                            'kelamin' => '',
                            'usia' => 'semua',
                        ];
                        $param = base64_encode(json_encode($param));
                    @endphp
                    <td class="text-right" style="text-decoration: underline;cursor: pointer"
                        onclick="modal('{{ $jenis }}','{{ $param }}')">
                        {{ number_format($data->sum('jumlah_sudah_survey_faktor5'), 0, ',', '.') }}</td>
                    @php
                        $param = [
                            'judul' =>
                                'Data penduduk usia 0 sd 6 tahun yang belum bersekolah dan disurvey status diliaur 5 Faktor',
                            'kategori' => 'jumlah_sudah_survey_lain_faktor',
                            'kelamin' => '',
                            'usia' => 'semua',
                        ];
                        $param = base64_encode(json_encode($param));
                    @endphp
                    <td class="text-right" style="text-decoration: underline;cursor: pointer"
                        onclick="modal('{{ $jenis }}','{{ $param }}')">
                        {{ number_format($data->sum('jumlah_sudah_survey_lain_faktor'), 0, ',', '.') }}
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
