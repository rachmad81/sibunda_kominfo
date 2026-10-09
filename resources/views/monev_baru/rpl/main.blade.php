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
        <h2 class="content-heading" style="padding-top: 0px">Monev data Penerima Beasiswa RPL S1 se Surabaya</h2>
        <!-- Dynamic Table Full -->
        <div class="main-page block">
            <div class="block-header block-header-default row no-margin">
                <h3 class="block-title">Monev data Penerima Beasiswa RPL S1 se Surabaya</h3>
            </div>
            <div class="block-content block-content-full">
                @include('layout.alert')
                <div class="row">
                    <div class="col-lg-12" style="overflow: auto">
                        <div>
                            <center>
                                <h1 class="judul" style="display:none;">Monev data Penerima Beasiswa RPL S1 se Surabaya
                                </h1>
                            </center>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6" style="border-bottom: 1px solid #000;padding-bottom: 10px;">
                        <h4>Penyaringan data (filter)</h4>
                        <table style="width: 100%;">
                            <tr>
                                <td>Periode</td>
                                <td>
                                    @php
                                        $id_kecamatan = Auth::getUser()->id_kecamatan;
                                        $id_kelurahan = Auth::getUser()->id_kelurahan;
                                    @endphp
                                    <select name="periode" class="form-control periode_cari">
                                        @foreach ($periode as $p)
                                            <option value="{{ $p }}">{{ $p }}</option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2"><a href="javascript:void(0)" class="btn btn-success form-control"
                                        onclick="get_data()">Cari data</a></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-lg-12" style="overflow: auto;margin-top: 10px">
                        <table id="dataTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th style="width:1%">No</th>
                                    <th>Nama</th>
                                    <th>Nama Lembaga</th>
                                    <th>Periode</th>
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
        var dt_kecamatan = {!! $kecamatan !!};
        var periode = '';
        $(document).ready(function() {
            $('#sidebar-monitor').addClass('active');
            periode = $('.periode_cari').val();

            dt_tabel = $("#dataTable").DataTable({
                dom: "<'row'<'col-md-6'l><'col-md-6'B>>" +
                    "<'row'<'col-md-12'f>>" +
                    "<'row'<'col-md-12'tr>><'row'<'col-md-12'ip>>",
                buttons: [{
                        extend: 'excel',
                        title: 'judul',
                        filename: function() {
                            judul = $('.judul').html() + '_{{ date('YmdHi') }}';
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
                            judul = $('.judul').html() + '_{{ date('YmdHi') }}';
                            return judul;
                        }
                    },
                ],
                scrollX: true,
                processing: true,
                serverSide: true,
                autoWidth: true,
                ajax: {
                    url: '{{ route('monev_baru-rpl', ['halaman' => 'get_data']) }}',
                    type: 'post',
                    data: function(d) {
                        d.periode = periode;
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
                }, {
                    data: 'nama',
                    name: 'nama'
                }, {
                    data: 'unit_kerja',
                    name: 'unit_kerja'
                }, {
                    data: 'periode',
                    name: 'periode'
                }],
            });

            dt_tabel.on('processing.dt', function(e, settings, processing) {
                if (processing) {
                    $('#dataTable tbody').html(
                        '<tr><td colspan="18" class="text-center">Loading...</td></tr>'
                    );
                }
            });

            // get_kelurahan();
            get_data();
        });

        function get_data() {
            periode = $('.periode_cari').val();

            dt_tabel.ajax.reload();
        }

        function get_kelurahan() {
            var html = '<option value="all">..:: Semua Kelurahan ::..</option>';
            var kecamatan = $('.wadah_kecamatan').val();

            if (kecamatan != 'all') {
                if (dt_kecamatan[kecamatan].kelurahan.length > 0) {
                    var id_kelurahan = '{{ $id_kelurahan }}';
                    $.each(dt_kecamatan[kecamatan].kelurahan, function(k, v) {
                        if (id_kelurahan == v.id_kelurahan) {
                            html += '<option selected value="' + v.kelurahan_dispenduk + '">' + v
                                .kelurahan_dispenduk +
                                '</option>';
                        } else {
                            html += '<option value="' + v.kelurahan_dispenduk + '">' + v.kelurahan_dispenduk +
                                '</option>';
                        }
                    });
                }
            }

            $('.wadah_kelurahan').html(html);
        }
    </script>
@endsection
