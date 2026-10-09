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
        <h2 class="content-heading" style="padding-top: 0px">Konsultasi PAUD HI</h2>
        <!-- Dynamic Table Full -->
        <div class="block">
            <div class="block-header block-header-default row no-margin">
                <h3 class="block-title">Konsultasi PAUD HI</h3>
            </div>
            <div class="block-content block-content-full">
                @include('layout.alert')
                <div class="row">
                    <div class="col-lg-1">
                        <label>Kecamatan</label>
                    </div>
                    <div class="col-lg-4">
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
                    <div class="col-lg-1">
                        <label>Kelurahan</label>
                    </div>
                    <div class="col-lg-4">
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
                    <div class="col-lg-1">
                        <label>Jenjang</label>
                    </div>
                    <div class="col-lg-4">
                        @php
                            $get_jenjang = ['KB', 'TK', 'SPS', 'TPA'];
                        @endphp
                        <select style="width: 100%" name="jenjang" onchange="get_data()">
                            <option @if ($jenjang == '') selected @endif value="semua">Semua Jenjang</option>
                            @foreach ($get_jenjang as $j)
                                <option @if ($jenjang == $j) selected @endif value="{{ $j }}">
                                    {{ $j }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-1">
                        <label>Materi</label>
                    </div>
                    <div class="col-lg-4">
                        <span style="width: 50%">
                            <select style="width: 49%" name="materi" onchange="get_data()">
                                <option selected value="semua">Semua Materi</option>
                                @foreach ($materi as $w)
                                    <option value="{{ $w->id }}">{{ $w->nama }}</option>
                                @endforeach
                            </select>
                        </span>
                        <span style="width: 50%">
                            <select style="width: 49%" name="jenis" onchange="get_data()">
                                <option selected value="semua">Semua Jenis</option>
                                <option value="Online">Online</option>
                                <option value="Offline">Offline</option>
                            </select>
                        </span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-1">
                        <label>Tahun</label>
                    </div>
                    <div class="col-lg-4">
                        <span style="width: 50%">
                            <select style="width: 49%" name="tahun" onchange="get_data()">
                                <option selected value="">.. Pilih Tahun ..</option>
                                @for($i=2023;$i<=date('Y');$i++)
                                    <option @if ($tahun == $i) selected @endif value="{{ $i }}">{{ $i }}</option>
                                @endfor
                                {{-- @foreach ($thn as $th)
                                    <option @if ($tahun == $th->tahun) selected @endif value="{{ $th->tahun }}">
                                        {{ $th->tahun }}</option>
                                @endforeach --}}
                            </select>
                        </span>
                        <span style="width: 50%">
                            @php
                                $bulan = ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'];
                                $bulan1 = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
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
                    <div class="col-lg-1">
                        <label>Status</label>
                    </div>
                    <div class="col-lg-4">
                        <select style="width: 100%" name="status" onchange="get_data()">
                            <option>Semua</option>
                            <option selected>Pengajuan</option>
                            <option>Reschedule</option>
                            <option>Setujui</option>
                            <option>Tolak</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12" style="overflow: auto">
                        <div>
                            <center>
                                <h1 class="judul" style="display:none;">Konsultasi PAUD HI</h1>
                            </center>
                        </div>
                        <table id="dataTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th style="width:1%">No</th>
                                    <th>NPSN</th>
                                    <th>Nama Sekolah</th>
                                    <th>Kecamatan</th>
                                    <th>Kelurahan</th>
                                    <th>Tanggal Pelaksanaan</th>
                                    <th>Jam Mulai</th>
                                    <th>Jam Selesai</th>
                                    <th>Materi</th>
                                    <th>Jenis</th>
                                    <th>Klien</th>
                                    <th>Pendamping</th>
                                    <th>Lokasi</th>
                                    <th>Status</th>
                                    <th>Catatan</th>
                                    <th>Surat Permohonan</th>
                                    @if (Auth::getUser()->role == 'dinas')
                                        <th>Aksi</th>
                                    @endif
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
        $(document).ready(function() {
            $('#sidebar-kegiatan_pelatihan').addClass('active');
            var tahun = $('select[name=tahun]').val();
            var kecamatan = $('select[name=kecamatan]').val();
            var kelurahan = $('select[name=kelurahan]').val();
            var jenjang = $('select[name=jenjang]').val();
            var materi = $('select[name=materi]').val();
            var bulan = $('select[name=bulan]').val();
            var jenis = $('select[name=jenis]').val();
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
                ajax: '{!! route('bunda-konsultasi-get_data') !!}?tahun=' + tahun + '&bulan=' + bulan + '&kec=' + kecamatan +
                    '&kel=' + kelurahan + '&jenjang=' + jenjang + '&materi=' + materi + '&jenis=' + jenis +
                    '&status=' + status,
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'npsn',
                        name: 'npsn'
                    },
                    {
                        data: 'nama_sekolah',
                        name: 'nama_sekolah'
                    },
                    {
                        data: 'nama_kecamatan',
                        name: 'nama_kecamatan'
                    },
                    {
                        data: 'nama_kelurahan',
                        name: 'nama_kelurahan'
                    },
                    {
                        data: 'tanggal_pelaksanaan',
                        name: 'tanggal_pelaksanaan'
                    },
                    {
                        data: 'jam_mulai',
                        name: 'jam_mulai'
                    },
                    {
                        data: 'jam_selesai',
                        name: 'jam_selesai'
                    },
                    {
                        data: 'nama_materi',
                        name: 'nama_materi'
                    },
                    {
                        data: 'jenis',
                        name: 'jenis'
                    },
                    {
                        data: 'data_klien',
                        name: 'data_klien'
                    },
                    {
                        data: 'pendampings',
                        name: 'pendampings'
                    },
                    {
                        data: 'lokasi',
                        name: 'lokasi'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'catatan_reschedule',
                        name: 'catatan_reschedule'
                    },
                    {
                        data: 'berkas',
                        name: 'berkas'
                    },
                    @if (Auth::getUser()->role == 'dinas')
                        {
                            data: 'aksi',
                            name: 'aksi',
                            className: 'dt-left'
                        },
                    @endif
                ]
            });

            @if (Auth::getuser()->tingkat == 'kecamatan')
                get_kelurahan();
            @endif
        });

        function get_data() {
            $('tbody').html('<tr><td colspan="11"><h4>Harap tunggu.....</h4></td></tr>');
            var tahun = $('select[name=tahun]').val();
            var kecamatan = $('select[name=kecamatan]').val();
            var kelurahan = $('select[name=kelurahan]').val();
            var jenjang = $('select[name=jenjang]').val();
            var materi = $('select[name=materi]').val();
            var bulan = $('select[name=bulan]').val();
            var jenis = $('select[name=jenis]').val();
            var status = $('select[name=status]').val();

            var title = "Konsultasi PAUD HI";

            $('.judul').html(title);
            judul = $('.judul').html();

            dt_table.ajax.url("{!! route('bunda-konsultasi-get_data') !!}?tahun=" + tahun + "&bulan=" + bulan + "&kec=" + kecamatan + "&kel=" +
                    kelurahan + "&jenjang=" + jenjang + "&materi=" + materi + "&jenis=" + jenis + "&status=" + status)
                .load();
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
            $.post("{{ route('bunda-konsultasi-modal_konfirmasi') }}", {
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
            $.post("{{ route('bunda-konsultasi-get_jadwal') }}", data, function(data) {
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
