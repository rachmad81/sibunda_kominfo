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
                    <div class="block block-success">
                        <div class="block-header block-header-default row no-margin flex">
                            <h3 class="block-title">Jumlah Penerima PKH</h3>
                        </div>
                        <div class="block-content block-content-full flex">
                            @php
                                $param = [
                                    'judul' => 'Data Penerima Bantuan Dinas Sosial Usia 0 - 6',
                                    'kelamin' => '',
                                    'kategori' => 'pkh',
                                ];
                                $param = base64_encode(json_encode($param));
                            @endphp
                            <h1 style="cursor: pointer" onclick="modal('{{ $jenis }}','{{ $param }}')">
                                {{ number_format($data->jumlah_penerima_pkh, 0, '', '.') }} orang</h1>
                            <table>
                                <tr>
                                    <th>Laki laki</th>
                                    <th>Perempuan</th>
                                </tr>
                                <tr>
                                    @php
                                        $param = [
                                            'judul' => 'Data Penerima Bantuan Dinas Sosial Usia 0 - 6',
                                            'kelamin' => 'L',
                                            'kategori' => 'pkh',
                                        ];
                                        $param = base64_encode(json_encode($param));
                                    @endphp
                                    <td style="cursor: pointer"
                                        onclick="modal('{{ $jenis }}','{{ $param }}')">
                                        {{ number_format($data->jumlah_penerima_pkh_laki, 0, '', '.') }}</td>
                                    @php
                                        $param = [
                                            'judul' => 'Data Penerima Bantuan Dinas Sosial Usia 0 - 6',
                                            'kelamin' => 'P',
                                            'kategori' => 'pkh',
                                        ];
                                        $param = base64_encode(json_encode($param));
                                    @endphp
                                    <td style="cursor: pointer"
                                        onclick="modal('{{ $jenis }}','{{ $param }}')">
                                        {{ number_format($data->jumlah_penerima_pkh_perempuan, 0, '', '.') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="block block-success">
                        <div class="block-header block-header-default row no-margin flex">
                            <h3 class="block-title">Jumlah Penerima Alat Bantu DIsabilitas</h3>
                        </div>
                        <div class="block-content block-content-full flex">
                            @php
                                $param = [
                                    'judul' => 'Data Penerima Bantuan Dinas Sosial Usia 0 - 6',
                                    'kelamin' => '',
                                    'kategori' => 'alat_bantu',
                                ];
                                $param = base64_encode(json_encode($param));
                            @endphp
                            <h1 style="cursor: pointer" onclick="modal('{{ $jenis }}','{{ $param }}')">
                                {{ number_format($data->jumlah_penerima_alat_bantu, 0, '', '.') }} orang</h1>
                            <table>
                                <tr>
                                    <th>Laki laki</th>
                                    <th>Perempuan</th>
                                </tr>
                                <tr>
                                    @php
                                        $param = [
                                            'judul' => 'Data Penerima Bantuan Dinas Sosial Usia 0 - 6',
                                            'kelamin' => 'L',
                                            'kategori' => 'alat_bantu',
                                        ];
                                        $param = base64_encode(json_encode($param));
                                    @endphp
                                    <td style="cursor: pointer"
                                        onclick="modal('{{ $jenis }}','{{ $param }}')">
                                        {{ number_format($data->jumlah_penerima_alat_bantu_laki, 0, '', '.') }}</td>
                                    @php
                                        $param = [
                                            'judul' => 'Data Penerima Bantuan Dinas Sosial Usia 0 - 6',
                                            'kelamin' => 'P',
                                            'kategori' => 'alat_bantu',
                                        ];
                                        $param = base64_encode(json_encode($param));
                                    @endphp
                                    <td style="cursor: pointer"
                                        onclick="modal('{{ $jenis }}','{{ $param }}')">
                                        {{ number_format($data->jumlah_penerima_alat_bantu_perempuan, 0, '', '.') }}
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="block block-primary">
                        <div class="block-header block-header-default row no-margin flex">
                            <h3 class="block-title">Jumlah Disabilitas</h3>
                        </div>
                        <div class="block-content block-content-full flex">
                            @php
                                $param = [
                                    'judul' => 'Data Disabilitas Dinas Sosial Usia 0 - 6',
                                    'kelamin' => '',
                                    'kategori' => 'disabilitas',
                                ];
                                $param = base64_encode(json_encode($param));
                            @endphp
                            <h1 style="cursor: pointer" onclick="modal('{{ $jenis }}','{{ $param }}')">
                                {{ number_format($data_inklusi->jumlah_inklusi, 0, '', '.') }} orang</h1>
                            <table>
                                <tr>
                                    <th>Laki laki</th>
                                    <th>Perempuan</th>
                                </tr>
                                <tr>
                                    @php
                                        $param = [
                                            'judul' => 'Data Disabilitas Dinas Sosial Usia 0 - 6',
                                            'kelamin' => 'L',
                                            'kategori' => 'disabilitas',
                                        ];
                                        $param = base64_encode(json_encode($param));
                                    @endphp
                                    <td style="cursor: pointer"
                                        onclick="modal('{{ $jenis }}','{{ $param }}')">
                                        {{ number_format($data_inklusi->jumlah_inklusi_laki, 0, '', '.') }}</td>
                                    @php
                                        $param = [
                                            'judul' => 'Data Disabilitas Dinas Sosial Usia 0 - 6',
                                            'kelamin' => 'P',
                                            'kategori' => 'disabilitas',
                                        ];
                                        $param = base64_encode(json_encode($param));
                                    @endphp
                                    <td style="cursor: pointer"
                                        onclick="modal('{{ $jenis }}','{{ $param }}')">
                                        {{ number_format($data_inklusi->jumlah_inklusi_perempuan, 0, '', '.') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
