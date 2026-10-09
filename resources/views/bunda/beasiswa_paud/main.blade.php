@extends('layout.index')


@section('additional_styles')
    <style>
        tr>th>td {
            text-align: center;
        }

        .aktif-form {
            background: #ccc;
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
        <h2 class="content-heading" style="padding-top: 0px">Penerima Beasiswa PAUD</h2>
        <!-- Dynamic Table Full -->
        <div class="main-page block">
            <div class="block-header block-header-default row no-margin">
                <h3 class="block-title">Penerima Beasiswa PAUD</h3>
            </div>
            <div class="block-content block-content-full">
                @include('layout.alert')
                <div class="row">
                    <div class="col-lg-12" style="overflow: auto">
                        <div>
                            <center>
                                <h1 class="judul" style="display:none;">Penerima Beasiswa PAUD</h1>
                            </center>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <table style="width: 100%">
                            @if (Auth::getUser()->tingkat != 'kelurahan')
                                <tr>
                                    <td>Kecamatan Siswa</td>
                                    <td>
                                        <select name="kecamatan" class="form-control" onchange="ganti_kecamatan()"
                                            @if (Auth::getUser()->tingkat == 'kecamatan') disabled @endif>
                                            <option value="semua">..:: Semua Kecamatan ::..</option>
                                            @foreach ($data_kecamatan as $kec)
                                                <option data-kec_id="{{ $kec['kecamatan_id'] }}"
                                                    value="{{ $kec['kecamatan_dispenduk'] }}"
                                                    @if (Auth::getUser()->tingkat == 'kecamatan' && Auth::getUser()->id_kecamatan == $kec['kecamatan_id']) selected @endif>
                                                    {{ $kec['kecamatan_dispenduk'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Kelurahan Siswa</td>
                                    <td>
                                        <select name="kelurahan" class="form-control"></select>
                                    </td>
                                </tr>
                            @else
                                <tr>
                                    <td>Kecamatan KK Siswa</td>
                                    <td>
                                        <select name="kecamatan" class="form-control" onchange="ganti_kecamatan()" disabled>
                                            <option value="{{ $data_kelurahan->kecamatan_dispenduk ?? '' }}">
                                                {{ $data_kelurahan->kecamatan_dispenduk ?? '' }}</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Kelurahan KK Siswa</td>
                                    <td>
                                        <select name="kelurahan" class="form-control" disabled>
                                            <option value="{{ $data_kelurahan->kelurahan_dispenduk ?? '' }}" selected>
                                                {{ $data_kelurahan->kelurahan_dispenduk ?? '' }}
                                            </option>
                                        </select>
                                    </td>
                                </tr>
                            @endif

                            <tr>
                                <td>Tahun</td>
                                <td>
                                    <select name="tahun" class="form-control">
                                        @foreach ($tahun_beasiswa as $t)
                                            <option value="{{ $t }}">{{ $t }}</option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>Periode</td>
                                <td>
                                    <select name="periode" class="form-control">
                                        <option value="1">1 (Januari - Juni)</option>
                                        <option value="2">2 (Juli - Desember)</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2"><a href="javascript:void(0)" class="btn btn-sm btn-primary"
                                        onclick="get_data()"><i class="fa fa-search"></i> Cari</a></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-lg-12" style="overflow: auto;margin-top: 10px">
                        <h3 style="text-align: center" class="judul"></h3>
                        <table id="dataTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th style="width:1%">No</th>
                                    <th width="27%">Nama</th>
                                    <th>NIK</th>
                                    <th>NPSN</th>
                                    <th>Sekolah</th>
                                    <th>Alamat KK Siswa</th>
                                    <th>Kecamatan KK Siswa</th>
                                    <th>Kelurahan KK Siswa</th>
                                    <th>File Pengajuan</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-page"></div>
        @include('attention')
    </div>
    <div>
        @include('bunda.lomba.panduan')
    </div>
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
        var dt_tabel = '';
        var data_kecamatan = {!! $data_kecamatan !!};
        var isi_kecamatan = $('select[name=kecamatan]').val();
        var isi_kelurahan = $('select[name=kelurahan]').val();
        var isi_tahun = $('select[name=tahun]').val();
        var isi_periode = $('select[name=periode]').val();

        $(document).ready(function() {
            $('#sidebar-beasiswa_paud').addClass('active');
            $('#sidebar-monitor').addClass('active');
            dt_tabel = $("#dataTable").DataTable({
                dom: "<'row'<'col-md-6'l><'col-md-6'B>>" +
                    "<'row'<'col-md-12'f>>" +
                    "<'row'<'col-md-12't>><'row'<'col-md-12'ip>>",
                buttons: [{
                        extend: 'excel',
                        title: 'judul',
                        filename: function() {
                            judul = $('.judul').html() + '{{ date('YmdHi') }}';
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
                            judul = $('.judul').html() + '{{ date('YmdHi') }}';
                            return judul;
                        }
                    },
                ],
                scrollX: true,
                processing: true,
                serverSide: true,
                autoWidth: true,
                ajax: {
                    url: '{{ route('beasiswa_paud-halaman', ['halaman' => 'get_data']) }}',
                    type: 'post',
                    data: function(d) {
                        d.tahun = isi_tahun;
                        d.periode = isi_periode;
                        d.kecamatan = isi_kecamatan;
                        d.kelurahan = isi_kelurahan;
                    }
                },
                lengthMenu: [
                    [10, 25, 50, -1],
                    [10, 25, 50, "All"]
                ],
                columns: [{
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: 'nama_siswa',
                        name: 'd.nama_siswa',
                    },
                    {
                        data: 'nik_siswa',
                        name: 'd.nik_siswa',
                    },
                    {
                        data: 'npsn',
                        name: 'b.npsn',
                    },
                    {
                        data: 'nama_sekolah',
                        name: 's.nama',
                    },
                    {
                        data: 'alamat',
                        name: 'd.alamat',
                    },
                    {
                        data: 'kecamatan',
                        name: 'd.kecamatan',
                    },
                    {
                        data: 'kelurahan',
                        name: 'd.kelurahan',
                    },
                    {
                        data: 'surat_pengajuan',
                        name: 'b.file_surat_pengajuan',
                    },
                ],
            });

            @if (Auth::getUser()->tingkat != 'kelurahan')
                ganti_kecamatan();
            @endif
            get_data();
        });

        function ganti_kecamatan() {
            var kecamatan = $('select[name=kecamatan]').val();
            var html_kelurahan = '<option value="semua">..:: Semua Kelurahan ::..</option>';
            if (kecamatan == 'semua') {} else {
                var kelurahan = data_kecamatan.filter(item => item.kecamatan_dispenduk == kecamatan)
                    .flatMap(item => item.kelurahan);
                if (kelurahan.length > 0) {
                    $.each(kelurahan, function(k, v) {
                        html_kelurahan += '<option value="' + v.kelurahan_dispenduk + '">' + v.kelurahan_dispenduk +
                            '</option>';
                    });
                }
            }
            console.log(kecamatan);
            $('select[name=kelurahan]').html(html_kelurahan);
        }

        function get_data() {
            isi_kecamatan = $('select[name=kecamatan]').val();
            isi_kelurahan = $('select[name=kelurahan]').val();
            isi_tahun = $('select[name=tahun]').val();
            isi_periode = $('select[name=periode]').val();

            judul = 'Data Penerima Beasiswa PAUD ';
            if (isi_kecamatan == 'semua') {
                judul += 'se Kota Surabaya';
            } else {
                judul += 'Kecamatan ' + isi_kecamatan;
                if (isi_kelurahan == 'semua') {} else {
                    judul += ', Kelurahan ' + isi_kelurahan;

                }
            }

            judul += ' Periode ' + isi_periode;
            judul += ' Tahun ' + isi_tahun;

            $('.judul').html(judul);

            dt_tabel.ajax.reload();
        }
    </script>
@endsection
