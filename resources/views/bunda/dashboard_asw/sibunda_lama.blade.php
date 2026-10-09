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
                <div class="col-lg-4">
                    <div class="block block-primary">
                        <div class="block-header block-header-default row no-margin flex">
                            <h3 class="block-title">Jumlah Belum Bersekolah</h3>
                        </div>
                        <div class="block-content block-content-full flex">
                            @php
                                $param = [
                                    'judul' => 'Data Belum Bersekolah Usia 5 - 6',
                                    'kelamin' => '',
                                    'kategori' => 'belum',
                                ];
                                $param = base64_encode(json_encode($param));
                            @endphp
                            <h1 style="cursor: pointer" onclick="modal('{{ $jenis }}','{{ $param }}')">
                                {{ number_format($data->jumlah_total, 0, '', '.') }} orang</h1>
                            <table>
                                <tr>
                                    <th>Laki laki</th>
                                    <th>Perempuan</th>
                                </tr>
                                <tr>
                                    @php
                                        $param = [
                                            'judul' => 'Data Belum Bersekolah Usia 5 - 6',
                                            'kelamin' => 'L',
                                            'kategori' => 'belum',
                                        ];
                                        $param = base64_encode(json_encode($param));
                                    @endphp
                                    <td style="cursor: pointer"
                                        onclick="modal('{{ $jenis }}','{{ $param }}')">
                                        {{ number_format($data->jumlah_total_laki, 0, '', '.') }}</td>
                                    @php
                                        $param = [
                                            'judul' => 'Data Belum Bersekolah Usia 5 - 6',
                                            'kelamin' => 'P',
                                            'kategori' => 'belum',
                                        ];
                                        $param = base64_encode(json_encode($param));
                                    @endphp
                                    <td style="cursor: pointer"
                                        onclick="modal('{{ $jenis }}','{{ $param }}')">
                                        {{ number_format($data->jumlah_total_perempuan, 0, '', '.') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="block block-success">
                        <div class="block-header block-header-default row no-margin flex">
                            <h3 class="block-title">Jumlah Sudah Survey</h3>
                        </div>
                        <div class="block-content block-content-full flex">
                            @php
                                $param = [
                                    'judul' => 'Data Sudah Disurvey Usia 5 - 6',
                                    'kelamin' => '',
                                    'kategori' => 'survey',
                                ];
                                $param = base64_encode(json_encode($param));
                            @endphp
                            <h1 style="cursor: pointer" onclick="modal('{{ $jenis }}','{{ $param }}')">
                                {{ number_format($data->jumlah_sudah_survey, 0, '', '.') }} orang</h1>
                            <table>
                                <tr>
                                    <th>Laki laki</th>
                                    <th>Perempuan</th>
                                </tr>
                                <tr>
                                    @php
                                        $param = [
                                            'judul' => 'Data Sudah Disurvey Usia 5 - 6',
                                            'kelamin' => 'L',
                                            'kategori' => 'survey',
                                        ];
                                        $param = base64_encode(json_encode($param));
                                    @endphp
                                    <td style="cursor: pointer"
                                        onclick="modal('{{ $jenis }}','{{ $param }}')">
                                        {{ number_format($data->jumlah_sudah_survey_laki, 0, '', '.') }}</td>
                                    @php
                                        $param = [
                                            'judul' => 'Data Sudah Disurvey Usia 5 - 6',
                                            'kelamin' => 'P',
                                            'kategori' => 'survey',
                                        ];
                                        $param = base64_encode(json_encode($param));
                                    @endphp
                                    <td style="cursor: pointer"
                                        onclick="modal('{{ $jenis }}','{{ $param }}')">
                                        {{ number_format($data->jumlah_sudah_survey_perempuan, 0, '', '.') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="block block-warning">
                        <div class="block-header block-header-default row no-margin flex">
                            <h3 class="block-title">Jumlah Belum Survey <span style="color: red">*</span></h3>
                        </div>
                        <div class="block-content block-content-full flex">
                            @php
                                $param = [
                                    'judul' => 'Data Belum Survey Usia 5 - 6',
                                    'kelamin' => '',
                                    'kategori' => 'belum_survey',
                                ];
                                $param = base64_encode(json_encode($param));
                            @endphp
                            <h1 style="cursor: pointer" onclick="modal('{{ $jenis }}','{{ $param }}')">
                                {{ number_format($data->jumlah_belum_survey, 0, '', '.') }} orang</h1>
                            <table>
                                <tr>
                                    <th>Laki laki</th>
                                    <th>Perempuan</th>
                                </tr>
                                <tr>
                                    @php
                                        $param = [
                                            'judul' => 'Data Belum Survey Usia 5 - 6',
                                            'kelamin' => 'L',
                                            'kategori' => 'belum_survey',
                                        ];
                                        $param = base64_encode(json_encode($param));
                                    @endphp
                                    <td style="cursor: pointer"
                                        onclick="modal('{{ $jenis }}','{{ $param }}')">
                                        {{ number_format($data->jumlah_belum_survey_laki, 0, '', '.') }}</td>
                                    @php
                                        $param = [
                                            'judul' => 'Data Belum Survey Usia 5 - 6',
                                            'kelamin' => 'P',
                                            'kategori' => 'belum_survey',
                                        ];
                                        $param = base64_encode(json_encode($param));
                                    @endphp
                                    <td style="cursor: pointer"
                                        onclick="modal('{{ $jenis }}','{{ $param }}')">
                                        {{ number_format($data->jumlah_belum_survey_perempuan, 0, '', '.') }}</td>
                                </tr>
                            </table>
                            <br>
                            <div style="font-size: 10pt">
                                <span style="color: red">*</span> Data tidak ditemukan di data ATS.<br>
                                <span style="color: red">*</span> Data di cekin warga jenjang sekolahnya kosong.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
