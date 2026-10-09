@php
    $monitorings = collect([
        (object) [
            'name' => 'PPDB',
            'column' => 'ppdb',
            'is_fondasi' => false,
        ],
        (object) [
            'name' => 'MPLS',
            'column' => 'mpls',
            'is_fondasi' => false,
        ],
        (object) [
            'name' => 'Manasik Haji Cilik',
            'column' => 'manasik_haji_cilik',
            'is_fondasi' => true,
        ],
        (object) [
            'name' => 'Educity Tour',
            'column' => 'educity_tour',
            'is_fondasi' => true,
        ],
        (object) [
            'name' => 'Talenta Seni',
            'column' => 'talenta_seni',
            'is_fondasi' => true,
        ],
        (object) [
            'name' => 'Edugreen Living',
            'column' => 'edugreen_living',
            'is_fondasi' => true,
        ],
        (object) [
            'name' => 'Gen Mitigasi Bencana',
            'column' => 'gen_mitigasi_bencana',
            'is_fondasi' => true,
        ],
        (object) [
            'name' => 'Sekolahku Tempat Menyenangkan',
            'column' => 'sekolahku_tempat_menyenangkan',
            'is_fondasi' => true,
        ],
    ]);
@endphp
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header card-info text-white">
                <a href="javascript:void(0)" class="close btn btn-sm btn-success" style="font-size: 10pt;opacity: 1;"
                    onclick="get_data('{{ $jenis }}','load')">Load</a>
                <h4 class="modal-title">{{ $judul }}</h4>
            </div>
            <div class="card-body">
                @if (in_array(Auth::user()->tingkat, ['kecamatan', 'kelurahan']))
                    <div class="alert alert-info">
                        Klik <a href="javascript:void(0)" class="btn btn-sm btn-success"
                            title="Lakukan monitoring">Monitoring</a> pada baris sekolah untuk mengisi data monitoring.
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="myData">
                        <thead class="thead_siswa_non_paud">
                            <tr>
                                <th rowspan="3">No</th>
                                <th rowspan="3">Jenjang</th>
                                <th rowspan="3">Status</th>
                                <th rowspan="3">NPSN</th>
                                <th rowspan="3">Nama Lembaga</th>
                                <th rowspan="3">Alamat</th>
                                <th rowspan="3">Kecamatan</th>
                                <th rowspan="3">Kelurahan</th>
                                <th rowspan="3">Akreditasi</th>
                                @if ($jenis == 'lembaga_sd')
                                    <th rowspan="3">Jumlah Siswa</th>
                                    <th rowspan="1" colspan="4">Vaksinasi Polio</th>
                                @endif
                                @foreach ($monitorings->where('is_fondasi', false) as $monitoring)
                                    <th rowspan="2" colspan="2">{{ $monitoring->name }}</th>
                                @endforeach
                                <th colspan="12">Pengembangan 6 kemampuan Fondasi</th>
                            </tr>
                            <tr>
                                @if ($jenis == 'lembaga_sd')
                                    <th colspan="2">Tahap 1</th>
                                    <th colspan="2">Tahap 2</th>
                                @endif
                                @foreach ($monitorings->where('is_fondasi', true) as $monitoring)
                                    <th colspan="2">{{ $monitoring->name }}</th>
                                @endforeach
                            </tr>
                            <tr>
                                @if ($jenis == 'lembaga_sd')
                                    <th>Sudah</th>
                                    <th>Belum</th>
                                    <th>Sudah</th>
                                    <th>Belum</th>
                                @endif
                                @foreach ($monitorings as $monitoring)
                                    <th>Status</th>
                                    <th>File</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($lembaga as $k => $l)
                                <tr>
                                    <td>{{ $k + 1 }}</td>
                                    <td>{{ $l->jenjang == 'SPS' ? 'PPT' : $l->jenjang }}</td>
                                    <td>{{ $l->status }}</td>
                                    <td>{{ $l->npsn }}</td>
                                    <td>{{ $l->nama_lembaga }}</td>
                                    <td>{{ $l->alamat }}</td>
                                    <td>{{ $l->kecamatan_dispenduk }}</td>
                                    <td>{{ $l->kelurahan_dispenduk }}</td>
                                    <td>{{ $l->akreditasi }}</td>
                                    @if ($jenis == 'lembaga_sd')
                                        <td>{{ $l->jumlah_siswa }}</td>
                                        <td>{{ $l->jumlah_sudah_polio_dosis_1 }}</td>
                                        <td>{{ $l->jumlah_siswa - $l->jumlah_sudah_polio_dosis_1 }}</td>
                                        <td>{{ $l->jumlah_sudah_polio_dosis_2 }}</td>
                                        <td>{{ $l->jumlah_siswa - $l->jumlah_sudah_polio_dosis_2 }}</td>
                                    @endif

                                    @if (in_array(Auth::user()->tingkat, ['kecamatan', 'kelurahan']))
                                        @foreach ($monitorings as $monitoring)
                                            @if ($monitoring->is_fondasi == false && in_array($l->jenjang, ['KB', 'TPA']))
                                                <td></td>
                                                <td></td>
                                            @else
                                                @php
                                                    $columnStatus = "status_{$monitoring->column}";
                                                    $columnFile = "file_{$monitoring->column}";
                                                @endphp
                                                <td>
                                                    @if ($l->$columnStatus)
                                                        Status Monitoring:<br>
                                                        {{ $l->$columnStatus }}<br>
                                                    @endif
                                                    <a href="javascript:void(0)" class="btn btn-sm btn-success"
                                                        onclick="detail_data('monitoring',['{{ $l->npsn }}','{{ $l->nama_lembaga }}','{{ $monitoring->column }}','{{ $l->jenjang }}','{{ $l->tahun }}','{{ $l->bulan }}','{{ $l->status }}'])"
                                                        title="Lakukan monitoring">Monitoring</a>
                                                </td>
                                                <td>
                                                    @if ($l->$columnFile)
                                                        <a href="https://dispendik.surabaya.go.id/sibunda_file/{{ $l->$columnFile }}"
                                                            target="_blank" class="btn btn-sm btn-primary"
                                                            title="File">File</a>
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                            @endif
                                        @endforeach
                                    @else
                                        @foreach ($monitorings as $monitoring)
                                            @if ($monitoring->is_fondasi == false && in_array($l->jenjang, ['KB', 'TPA']))
                                                <td></td>
                                                <td></td>
                                            @else
                                                @php
                                                    $columnStatus = "status_{$monitoring->column}";
                                                    $columnFile = "file_{$monitoring->column}";
                                                @endphp
                                                <td>
                                                    @if ($l->$columnStatus)
                                                        {{ $l->$columnStatus }}<br>
                                                    @else
                                                        Belum
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($l->$columnFile)
                                                        <a href="https://dispendik.surabaya.go.id/sibunda_file/{{ $l->$columnFile }}"
                                                            target="_blank" class="btn btn-sm btn-primary"
                                                            title="File">File</a>
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                            @endif
                                        @endforeach
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
