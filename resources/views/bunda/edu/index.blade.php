@extends('layout.index')


@section('additional_styles')
    <style>
        tr>th,
        td {
            text-align: center;
        }
    </style>
@endsection
@section('sidebar')
    @if (Auth::getUser()->role == 'bunda')
        @include('bunda/layout/sidebar')
    @elseif(Auth::getUser()->role == 'dinas')
        @include('dinas/layout/sidebar')
    @else
        @include('opd/layout/sidebar')
    @endif
@endsection

@section('content')
    <div class="content" id="app">
        <h2 class="content-heading" style="padding-top: 0px">Edu City Tour</h2>
        <!-- Dynamic Table Full -->
        <div class="block">
            <div class="block-header block-header-default row no-margin">
                <h3 class="block-title">Edu City Tour</h3>
                <div>
                    <button type="button" onclick="export_peserta()">
                        <i class="fa fa-file-excel-o"></i> Download Rekap Peserta Didik (Excel)
                    </button>
                </div>
            </div>
            <div class="block-content block-content-full">
                @include('layout.alert')
                <div class="row">
                    <div class="col-lg-6">
                        <div class="row">
                            <div class="col-lg-4">
                                <label>Kecamatan</label>
                            </div>
                            <div class="col-lg-8">
                                <select style="width: 100%" name="kecamatan" onchange="get_data();get_kelurahan()"
                                    @if (in_array(Auth::getUser()->tingkat, ['kecamatan', 'kelurahan'])) disabled @endif>
                                    <option @if ($kode_kecamatan == '') selected @endif value="semua">Semua Kecamatan
                                    </option>
                                    @foreach ($kecamatan as $kec)
                                        <option @if ($kode_kecamatan == $kec->kecamatan_kode) selected @endif
                                            value="{{ $kec->kecamatan_kode }}">{{ $kec->kecamatan_dispenduk }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-4">
                                <label>Kelurahan</label>
                            </div>
                            <div class="col-lg-8">
                                <select style="width: 100%" name="kelurahan" onchange="get_data()"
                                    @if (in_array(Auth::getUser()->tingkat, ['kelurahan'])) disabled @endif>
                                    <option @if ($kode_kelurahan == '') selected @endif value="semua">Semua Kelurahan
                                    </option>
                                    @foreach ($kelurahan as $kec)
                                        <option @if ($kode_kelurahan == $kec->kelurahan_kode) selected @endif
                                            value="{{ $kec->kelurahan_kode }}">{{ $kec->kelurahan_dispenduk }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-4">
                                <label>Jenjang</label>
                            </div>
                            <div class="col-lg-8">
                                @php
                                    $get_jenjang = ['KB', 'TK', 'SPS', 'TPA'];
                                @endphp
                                <select style="width: 100%" name="jenjang" onchange="get_data()">
                                    <option @if ($jenjang == '') selected @endif value="semua">Semua Jenjang
                                    </option>
                                    @foreach ($get_jenjang as $j)
                                        <option @if ($jenjang == $j) selected @endif
                                            value="{{ $j }}">
                                            {{ $j }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-4">
                                <label>Wisata</label>
                            </div>
                            <div class="col-lg-8">
                                <select style="width: 100%" name="wisata" onchange="get_data();">
                                    <option selected value="semua">Semua Wisata</option>
                                    @foreach ($wisata as $w)
                                        <option value="{{ $w->id }}">{{ $w->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-4">
                                <label>Tahun</label>
                            </div>
                            <div class="col-lg-8">
                                <span style="width: 50%">
                                    <select style="width: 49%" name="tahun" onchange="get_data();">
                                        <option selected value="">.. Pilih Tahun ..</option>
                                        @for ($i = 2023; $i <= date('Y'); $i++)
                                            <option @if ($tahun == $i) selected @endif
                                                value="{{ $i }}">
                                                {{ $i }}</option>
                                        @endfor
                                        {{-- @foreach ($thn as $th)
                                    <option @if ($tahun == $th->tahun) selected @endif value="{{ $th->tahun }}">
                                        {{ $th->tahun }}</option>
                                @endforeach --}}
                                    </select>
                                </span>
                                <span style="width: 50%">
                                    @php
                                        $bulan = [
                                            '01',
                                            '02',
                                            '03',
                                            '04',
                                            '05',
                                            '06',
                                            '07',
                                            '08',
                                            '09',
                                            '10',
                                            '11',
                                            '12',
                                        ];
                                        $bulan1 = [
                                            'Januari',
                                            'Februari',
                                            'Maret',
                                            'April',
                                            'Mei',
                                            'Juni',
                                            'Juli',
                                            'Agustus',
                                            'September',
                                            'Oktober',
                                            'November',
                                            'Desember',
                                        ];
                                    @endphp
                                    <select style="width: 49%" name="bulan" onchange="get_data()">
                                        <option selected value="semua">Semua</option>
                                        @for ($i = 0; $i < count($bulan); $i++)
                                            <option value="{{ $bulan[$i] }}">{{ $bulan1[$i] }}</option>
                                        @endfor
                                    </select>
                                </span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-4">
                                <label>Status</label>
                            </div>
                            <div class="col-lg-8">
                                <select style="width: 100%" name="status" onchange="get_data()">
                                    <option>Semua</option>
                                    <option selected>Pengajuan</option>
                                    <option>Reschedule</option>
                                    <option>Setujui</option>
                                    <option>Tolak</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12" style="margin: 20px 0px">
                        <div id="chart-edu"></div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12" style="overflow: auto">
                        <div>
                            <center>
                                <h1 class="judul" style="display:none;">Educity Tour</h1>
                            </center>
                        </div>
                        <table id="dataTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th rowspan="2" style="width:1%">No</th>
                                    <th rowspan="2">NPSN</th>
                                    <th rowspan="2">Nama Sekolah</th>
                                    <th rowspan="2">Tanggal Pelaksanaan</th>
                                    <th rowspan="2">Jam Mulai</th>
                                    <th rowspan="2">Jam Selesai</th>
                                    <th rowspan="2">Kecamatan</th>
                                    <th rowspan="2">Kelurahan</th>
                                    <th rowspan="2">Tempat Wisata</th>
                                    <th rowspan="2">Alamat</th>
                                    <th colspan="2">Jumlah Peserta Didik</th>
                                    <th colspan="3">Penanggung Jawab</th>
                                    <th rowspan="2">Pendamping</th>
                                    <th rowspan="2">Status Jadwal</th>
                                    <th rowspan="2">Catatan</th>
                                    <th rowspan="2">Berkas Pendukung</th>
                                    <th rowspan="2">Aksi</th>
                                </tr>
                                <tr>
                                    <th>Riil</th>
                                    <th>Profil</th>
                                    <th>Nama</th>
                                    <th>Jabatan</th>
                                    <th>No Hp</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @include('attention')
    </div>
    <div class="modal-page"></div>
@endsection

@section('additional_scripts')
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/data.js"></script>
    <script src="https://code.highcharts.com/modules/drilldown.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>
    <script src="https://code.highcharts.com/themes/adaptive.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <style type="text/css">
        body {
            overflow: auto !important;
        }

        .modal {
            overflow: auto !important;
        }

        .dt-left {
            text-align: left;
        }
    </style>
    <script type="text/javascript">
        var dt_table;
        var judul = 'judul';

        var grafik = Highcharts.chart('chart-edu', {
            chart: {
                type: 'column'
            },
            title: {
                text: 'Grafik Data Penduduk',
                align: 'left'
            },
            subtitle: {
                text: '',
                align: 'left'
            },
            xAxis: {
                categories: ['-', '-', '-', '-', '-'],
                crosshair: true,
                accessibility: {
                    description: 'Status penduduk'
                }
            },
            yAxis: {
                min: 0,
                title: {
                    text: 'Frekuensi Kunjungan'
                }
            },
            legend: {
                symbolRadius: 3
            },
            tooltip: {
                shared: true,
                headerFormat: '<table><caption>{point.key}</caption>',
                pointFormat: `
            <tr>
              <th>
                <svg width="20" height="10">
                  <rect x="5" y="0" width="10" height="10" rx="3" ry="3"
                      fill="{series.color}" />
                </svg>
                {series.name}
              </th>
              <td>{point.y}</td>
            </tr>
            `,
                footerFormat: '</table>',
                valueSuffix: '   Kunjungan',
                useHTML: true
            },
            plotOptions: {
                column: {
                    pointPadding: 0.1,
                    borderWidth: 0
                }
            },
            series: [{
                name: "Semua",
                data: [0, 0, 0, 0, 0]
            }]
        });

        function grafiknya() {
            var tahun = $('select[name=tahun]').val();
            var kecamatan = $('select[name=kecamatan]').val();
            var kelurahan = $('select[name=kelurahan]').val();
            var jenjang = $('select[name=jenjang]').val();
            var wisata = $('select[name=wisata]').val();
            var bulan = $('select[name=bulan]').val();
            var status = $('select[name=status]').val();

            var data = {
                tahun: tahun,
                bulan: bulan,
                kec: kecamatan,
                kel: kelurahan,
                jenjang: jenjang,
                wisata: wisata,
                status: status,
            };

            $.post('{{ route('bunda-edu-get_grafik') }}', data, function(data) {
                grafik.setTitle({
                    text: data.judul
                });
                grafik.xAxis[0].setCategories(data.kategori, false); // false agar tidak diproses ulang dulu

                // Hapus series lama satu per satu dari belakang
                while (grafik.series.length > 0) {
                    grafik.series[0].remove(false);
                }

                // Tambahkan series baru dari data
                data.series.forEach(s => {
                    grafik.addSeries(s, false);
                });

                // Redraw grafik sekaligus di akhir
                grafik.redraw();
            }).fail(function() {
                // $('.' + jenis).html('<center><h5>Error 500- Internal server error</h5></center>');
            });
        }

        $(document).ready(function() {
            $('#sidebar-educity').addClass('active');
            $('#sidebar-kegiatan_pendukung').addClass('active');
            var tahun = $('select[name=tahun]').val();
            var kecamatan = $('select[name=kecamatan]').val();
            var kelurahan = $('select[name=kelurahan]').val();
            var jenjang = $('select[name=jenjang]').val();
            var wisata = $('select[name=wisata]').val();
            var bulan = $('select[name=bulan]').val();
            var status = $('select[name=status]').val();
            dt_table = $("#dataTable").DataTable({
                dom: "<'row'<'col-md-6'l><'col-md-6'B>>" +
                    "<'row'<'col-md-12'f>>" +
                    "<'row'<'col-md-12't>><'row'<'col-md-12'ip>>",
                buttons: [{
                        extend: 'excel',
                        title: 'judul',
                        filename: function() {
                            judul = $('.judul').html() + '_{{ date('d-m-Y H.i') }}';
                            return judul;
                        }
                    },
                    {
                        extend: 'print',
                        title: function() {
                            judul = $('.judul').html();
                            return judul;
                        },
                        filename: function() {
                            judul = $('.judul').html() + '_{{ date('d-m-Y H.i') }}';
                            return judul;
                        }
                    },
                ],
                lengthMenu: [
                    [10, 25, 50, -1],
                    [10, 25, 50, "All"]
                ],
                processing: true,
                serverSide: true,
                ajax: '{!! route('bunda-edu-get_data') !!}?tahun=' + tahun + '&bulan=' + bulan + '&kec=' + kecamatan +
                    '&kel=' + kelurahan + '&jenjang=' + jenjang + '&wisata=' + wisata + '&status=' + status,
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'npsn',
                        name: 'j.npsn'
                    },
                    {
                        data: 'nama_sekolah',
                        name: 's.nama'
                    },
                    {
                        data: 'tanggal_pelaksanaan',
                        name: 'j.tanggal'
                    },
                    {
                        data: 'jam_mulai',
                        name: 'j.jam_mulai'
                    },
                    {
                        data: 'jam_selesai',
                        name: 'j.jam_selesai'
                    },
                    {
                        data: 'nama_kecamatan',
                        name: 'kec.kecamatan_dispenduk'
                    },
                    {
                        data: 'nama_kelurahan',
                        name: 'kel.kelurahan_dispenduk'
                    },
                    {
                        data: 'nama_tempat',
                        name: 't.nama'
                    },
                    {
                        data: 'alamat',
                        name: 't.alamat'
                    },
                    {
                        data: 'jumlah_pd',
                        name: 'j.jumlah_pd'
                    },
                    {
                        data: 'jumlah_pd_profil',
                        name: 'j.jumlah_pd_profil'
                    },
                    {
                        data: 'nama_penanggung_jawab',
                        name: 'j.nama_penanggung_jawab'
                    },
                    {
                        data: 'jabatan_penanggung_jawab',
                        name: 'j.jabatan_penanggung_jawab'
                    },
                    {
                        data: 'no_hp_penanggung_jawab',
                        name: 'j.no_hp_penanggung_jawab'
                    },
                    {
                        data: 'pendampings',
                        name: 'pendampings',
                        searchable: false,
                        sortable: false,
                    },
                    {
                        data: 'status',
                        name: 'j.status_jadwal'
                    },
                    {
                        data: 'catatan_reschedule',
                        name: 'j.catatan_reschedule'
                    },
                    {
                        data: 'berkas',
                        name: 'berkas',
                        className: 'dt-left',
                        searchable: false,
                        sortable: false,
                    },
                    {
                        data: 'aksi',
                        name: 'aksi',
                        className: 'dt-left',
                        searchable: false,
                        sortable: false,
                    },
                ]
            });

            @if (Auth::getuser()->tingkat == 'kecamatan')
                get_kelurahan();
            @endif

            grafiknya();
        });

        function get_data() {
            $('tbody').html('<tr><td colspan="11"><h4>Harap tunggu.....</h4></td></tr>');
            var tahun = $('select[name=tahun]').val();
            var kecamatan = $('select[name=kecamatan]').val();
            var kelurahan = $('select[name=kelurahan]').val();
            var jenjang = $('select[name=jenjang]').val();
            var wisata = $('select[name=wisata]').val();
            var bulan = $('select[name=bulan]').val();
            var status = $('select[name=status]').val();

            var title = "Edu City Tour";

            $('.judul').html(title);
            judul = $('.judul').html();

            dt_table.ajax.url("{!! route('bunda-edu-get_data') !!}?tahun=" + tahun + "&bulan=" + bulan + "&kec=" + kecamatan + "&kel=" +
                kelurahan + "&jenjang=" + jenjang + "&wisata=" + wisata + "&status=" + status).load();

            grafiknya();
        }

        function export_peserta() {
            var tahun = $('select[name=tahun]').val();
            if (!tahun) {
                tahun = 2025;
            }
            window.location.href = "{{ route('bunda-edu-export_peserta') }}?tahun=" + tahun;
        }

        function get_kelurahan() {
            var kecamatan = $('select[name=kecamatan]').val();
            $.post("{{ url('bunda/umum/get_kelurahan') }}", {
                id: kecamatan
            }, function(data) {
                var html = '<option value="semua"> Semua Kelurahan</option>';
                $.each(data, function(k, v) {
                    html += '<option value="' + v.kelurahan_kode + '">' + v.kelurahan_dispenduk +
                        '</option>';
                });

                $('select[name=kelurahan]').html(html);
            });
        }

        function modal_konfirmasi(id) {
            $.post("{{ route('bunda-edu-modal_konfirmasi') }}", {
                id: id
            }, function(data) {
                $('.modal-page').html(data.content);
            });
        }

        function get_jadwal(tempat_id) {
            var tgl = $('input[name=tgl_pelaksanaan]').val();
            var data = {
                tgl: tgl,
                tempat_id: tempat_id,
            };
            $.post("{{ route('bunda-edu-get_jadwal') }}", data, function(data) {
                var html = '<tr><td colspan="4">Data kosong</td></tr>';
                if (data.data.length != 0) {
                    html = '';
                    html += '<tr>';
                    html += '<th> NPSN </th>';
                    html += '<th> Nama Sekolah </th>';
                    html += '<th> Jumlah PD </th>';
                    html += '<th> Jam Pelaksanaan </th>';
                    html += '<th> Status Jadwal </th>';
                    html += '</tr>';
                    $.each(data.data, function(k, v) {
                        html += '<tr>';
                        html += '<td>' + v.npsn + '</td>';
                        html += '<td>' + v.nama_sekolah + '</td>';
                        html += '<td>' + v.jumlah_pd + '</td>';
                        html += '<td>' + v.jam_pelaksanaan + '</td>';
                        html += '<td>' + v.status_jadwal + '</td>';
                        html += '</tr>';
                    })
                }
                $('#tempat_jadwal_lain').html(html);
                $('#tempat_tanggal').html(data.tgl);
            });
        }
    </script>
    <script></script>
@endsection
