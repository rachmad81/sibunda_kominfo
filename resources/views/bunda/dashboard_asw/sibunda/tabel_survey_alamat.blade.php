<div class="row">
    <div class="col-lg-12">
        <h5 style="margin-bottom:0px;margin-top: 20px">Tabel Data Penduduk Belum Bersekolah dan Sudah Disurvey</h5>
    </div>
    <div class="col-lg-12" style="overflow: auto;">
        <table class="table table-striped table-striped">
            <thead>
                <tr style="border-top:1px solid #000 !important;">
                    <th rowspan="3">Usia</th>
                    <th>Disurvey</th>
                    <th colspan="7">Hasil Survey Berdasarkan Alamat</th>
                </tr>
                <tr style="border-top:1px solid #000 !important;">
                    <th rowspan="2">Jumlah</th>
                    <th rowspan="2">Sesuai</th>
                    <th colspan="2">Pindah</th>
                    <th colspan="4">Lainnya</th>
                </tr>
                <tr style="border-top:1px solid #000 !important;">
                    <th>Pindah Dalam Kota</th>
                    <th>Pindah Luar Kota</th>
                    <th>Tidak Ditemukan</th>
                    <th>Ganda/Salah Sasaran</th>
                    <th>Meninggal</th>
                    <th>Dengan Catatan</th>
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
                                    ' tahun yang belum bersekolah dan sudah disurvey status alamat pindah dalam Surabaya',
                                'kategori' => 'jumlah_survey_alamat_pindah_dalam',
                                'kelamin' => '',
                                'usia' => $d->usia,
                            ];
                            $param = base64_encode(json_encode($param));
                        @endphp
                        <td class="text-right"
                            style="text-decoration: underline;cursor: pointer;border-top:1px solid #000 !important"
                            onclick="modal('{{ $jenis }}','{{ $param }}')">
                            {{ number_format($d->jumlah_survey_alamat_pindah_dalam, 0, ',', '.') }}</td>
                        @php
                            $param = [
                                'judul' =>
                                    'Data penduduk usia ' .
                                    $d->usia .
                                    ' tahun yang belum bersekolah dan sudah disurvey status alamat pindah luar Surabaya',
                                'kategori' => 'jumlah_survey_alamat_pindah_luar',
                                'kelamin' => '',
                                'usia' => $d->usia,
                            ];
                            $param = base64_encode(json_encode($param));
                        @endphp
                        <td class="text-right"
                            style="text-decoration: underline;cursor: pointer;border-top:1px solid #000 !important"
                            onclick="modal('{{ $jenis }}','{{ $param }}')">
                            {{ number_format($d->jumlah_survey_alamat_pindah_luar, 0, ',', '.') }}</td>
                        @php
                            $param = [
                                'judul' =>
                                    'Data penduduk usia ' .
                                    $d->usia .
                                    ' tahun yang belum bersekolah dan sudah disurvey status tidak ditemukan',
                                'kategori' => 'jumlah_survey_alamat_tidak_ditemukan',
                                'kelamin' => '',
                                'usia' => $d->usia,
                            ];
                            $param = base64_encode(json_encode($param));
                        @endphp
                        <td class="text-right"
                            style="text-decoration: underline;cursor: pointer;border-top:1px solid #000 !important"
                            onclick="modal('{{ $jenis }}','{{ $param }}')">
                            {{ number_format($d->jumlah_survey_alamat_tidak_ditemukan, 0, ',', '.') }}
                        </td>
                        @php
                            $param = [
                                'judul' =>
                                    'Data penduduk usia ' .
                                    $d->usia .
                                    ' tahun yang belum bersekolah dan sudah disurvey status data ganda/salah sasaran',
                                'kategori' => 'jumlah_survey_alamat_ganda',
                                'kelamin' => '',
                                'usia' => $d->usia,
                            ];
                            $param = base64_encode(json_encode($param));
                        @endphp
                        <td class="text-right"
                            style="text-decoration: underline;cursor: pointer;border-top:1px solid #000 !important"
                            onclick="modal('{{ $jenis }}','{{ $param }}')">
                            {{ number_format($d->jumlah_survey_alamat_ganda, 0, ',', '.') }}</td>
                        @php
                            $param = [
                                'judul' =>
                                    'Data penduduk usia ' .
                                    $d->usia .
                                    ' tahun yang belum bersekolah dan sudah disurvey status meninggal',
                                'kategori' => 'jumlah_survey_alamat_meninggal',
                                'kelamin' => '',
                                'usia' => $d->usia,
                            ];
                            $param = base64_encode(json_encode($param));
                        @endphp
                        <td class="text-right"
                            style="text-decoration: underline;cursor: pointer;border-top:1px solid #000 !important"
                            onclick="modal('{{ $jenis }}','{{ $param }}')">
                            {{ number_format($d->jumlah_survey_alamat_meninggal, 0, ',', '.') }}</td>
                        @php
                            $param = [
                                'judul' =>
                                    'Data penduduk usia ' .
                                    $d->usia .
                                    ' tahun yang belum bersekolah dan sudah disurvey status alamat dengan catatan',
                                'kategori' => 'jumlah_survey_alamat_dg_catatan',
                                'kelamin' => '',
                                'usia' => $d->usia,
                            ];
                            $param = base64_encode(json_encode($param));
                        @endphp
                        <td class="text-right"
                            style="text-decoration: underline;cursor: pointer;border-top:1px solid #000 !important"
                            onclick="modal('{{ $jenis }}','{{ $param }}')">
                            {{ number_format($d->jumlah_survey_alamat_dg_catatan, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="font-weight: bold">
                    <td>Total</td>
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
                                'Data penduduk usia 0 sd 6 tahun yang belum bersekolah dan sudah disurvey status alamat pindah dalam Surabaya',
                            'kategori' => 'jumlah_survey_alamat_pindah_dalam',
                            'kelamin' => '',
                            'usia' => 'semua',
                        ];
                        $param = base64_encode(json_encode($param));
                    @endphp
                    <td class="text-right"
                        style="text-decoration: underline;cursor: pointer;border-top:1px solid #000 !important"
                        onclick="modal('{{ $jenis }}','{{ $param }}')">
                        {{ number_format($data->sum('jumlah_survey_alamat_pindah_dalam'), 0, ',', '.') }}
                    </td>
                    @php
                        $param = [
                            'judul' =>
                                'Data penduduk usia 0 sd 6 tahun yang belum bersekolah dan sudah disurvey status alamat pindah luar Surabaya',
                            'kategori' => 'jumlah_survey_alamat_pindah_luar',
                            'kelamin' => '',
                            'usia' => 'semua',
                        ];
                        $param = base64_encode(json_encode($param));
                    @endphp
                    <td class="text-right"
                        style="text-decoration: underline;cursor: pointer;border-top:1px solid #000 !important"
                        onclick="modal('{{ $jenis }}','{{ $param }}')">
                        {{ number_format($data->sum('jumlah_survey_alamat_pindah_luar'), 0, ',', '.') }}
                    </td>
                    @php
                        $param = [
                            'judul' =>
                                'Data penduduk usia 0 sd 6 tahun yang belum bersekolah dan sudah disurvey status tidak ditemukan',
                            'kategori' => 'jumlah_survey_alamat_tidak_ditemukan',
                            'kelamin' => '',
                            'usia' => 'semua',
                        ];
                        $param = base64_encode(json_encode($param));
                    @endphp
                    <td class="text-right"
                        style="text-decoration: underline;cursor: pointer;border-top:1px solid #000 !important"
                        onclick="modal('{{ $jenis }}','{{ $param }}')">
                        {{ number_format($data->sum('jumlah_survey_alamat_tidak_ditemukan'), 0, ',', '.') }}
                    </td>
                    @php
                        $param = [
                            'judul' =>
                                'Data penduduk usia 0 sd 6 tahun yang belum bersekolah dan sudah disurvey status data ganda/salah sasaran',
                            'kategori' => 'jumlah_survey_alamat_ganda',
                            'kelamin' => '',
                            'usia' => 'semua',
                        ];
                        $param = base64_encode(json_encode($param));
                    @endphp
                    <td class="text-right"
                        style="text-decoration: underline;cursor: pointer;border-top:1px solid #000 !important"
                        onclick="modal('{{ $jenis }}','{{ $param }}')">
                        {{ number_format($data->sum('jumlah_survey_alamat_ganda'), 0, ',', '.') }}</td>
                    @php
                        $param = [
                            'judul' =>
                                'Data penduduk usia 0 sd 6 tahun yang belum bersekolah dan sudah disurvey status meninggal',
                            'kategori' => 'jumlah_survey_alamat_meninggal',
                            'kelamin' => '',
                            'usia' => 'semua',
                        ];
                        $param = base64_encode(json_encode($param));
                    @endphp
                    <td class="text-right"
                        style="text-decoration: underline;cursor: pointer;border-top:1px solid #000 !important"
                        onclick="modal('{{ $jenis }}','{{ $param }}')">
                        {{ number_format($data->sum('jumlah_survey_alamat_meninggal'), 0, ',', '.') }}
                    </td>
                    @php
                        $param = [
                            'judul' =>
                                'Data penduduk usia 0 sd 6 tahun yang belum bersekolah dan sudah disurvey status alamat dengan catatan',
                            'kategori' => 'jumlah_survey_alamat_dg_catatan',
                            'kelamin' => '',
                            'usia' => 'semua',
                        ];
                        $param = base64_encode(json_encode($param));
                    @endphp
                    <td class="text-right"
                        style="text-decoration: underline;cursor: pointer;border-top:1px solid #000 !important"
                        onclick="modal('{{ $jenis }}','{{ $param }}')">
                        {{ number_format($data->sum('jumlah_survey_alamat_dg_catatan'), 0, ',', '.') }}
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
