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
        <h2 class="content-heading" style="padding-top: 0px">Talenta Seni</h2>
        <!-- Dynamic Table Full -->
        <div class="block">
            <div class="block-content block-content-full">
                @include('layout.alert')
                <div class="row">
                    <div class="col-lg-12" style="overflow: auto">
                        <div>
                            <center>
                                <h1 class="judul" style="display:none;">Jadwal Talenta Seni</h1>
                            </center>
                        </div>
                        <a href="javascript:void(0)" class="btn btn-sm btn-info" onclick="form('0')">Tambah</a>
                        <table id="ExTable1" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tempat</th>
                                    <th>Tanggal</th>
                                    <th>Jam</th>
                                    <th>Kuota Lembaga</th>
                                    <th>Kuota Siswa</th>
                                    <th>Kecamatan</th>
                                    <th>Aksi</th>
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
            $('#sidebar-talenta').addClass('active');
            dt_table = $("#ExTable1").DataTable({
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
                ajax: '{!! route('bunda-jadwal_talenta-get_data') !!}',
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'nama',
                        name: 'nama'
                    },
                    {
                        data: 'tanggal',
                        name: 'tanggal'
                    },
                    {
                        data: 'jam',
                        name: 'jam'
                    },
                    {
                        data: 'jumlah_lembaga',
                        name: 'jumlah_lembaga'
                    },
                    {
                        data: 'jumlah_siswa',
                        name: 'jumlah_siswa'
                    },
                    {
                        data: 'kecamatan',
                        name: 'kecamatan'
                    },
                    {
                        data: 'aksi',
                        name: 'aksi',
                        className: 'dt-left'
                    },
                ]
            });

            @if (Auth::getuser()->tingkat == 'kecamatan')
                get_kelurahan();
            @endif
        });

        function get_data() {
            dt_table.ajax.url("{!! route('bunda-jadwal_talenta-get_data') !!}").load();
        }

        function form(id) {
            $.post('{{ route('bunda-jadwal_talenta-modal_form') }}', {
                id: id
            }, function(data) {
                $('.modal-page').html(data.content);
            });
        }

        function hapus(id) {
            Swal.fire({
                type: 'warning',
                title: 'Apakah kamu yakin?',
                showConfirmButton: true,
                showCancelButton: true,
            }).then((result) => {
                if (result.value) {
                    var data = {
                        id: id
                    }
                    $.post("{{ route('bunda-jadwal_talenta-hapus') }}", data, function(data) {
                        if (data.code == '200') {
                            get_data();
                            swal('Success', data.message, 'success');
                        } else {
                            swal('Whooop', data.message, 'error');
                        }
                    });
                }
            })
        }
    </script>
@endsection
