@extends('layout.index')


@section('additional_styles')
    <style>
        tr td {
            text-align: center;
        }
    </style>
@endsection
@section('sidebar')
    @include('dinas/layout/sidebar')
@endsection

@section('content')
    <div class="content" id="app">
        <h2 class="content-heading" style="padding-top: 0px">Kelola Mitra</h2>
        <!-- Dynamic Table Full -->
        <div class="block">
            <div class="block-header block-header-default row no-margin">
                <h3 class="block-title judul">Daftar Mitra</h3>
                <button class="btn btn-outline-success col-md-2 m-l-xs m-b-xs" data-toggle="modal" data-target="#modal_tambah"
                    onclick="form_mitra(0)">Tambah Mitra</button>
            </div>
            <div class="block-content block-content-full">
                <div class="row">
                    <div class="col-lg-4">
                        <select name="status" class="form-control" onchange="filter()">
                            <option value="">Semua Status</option>
                            <option>Pengajuan</option>
                            <option>Setujui</option>
                            <option>Block</option>
                        </select>
                    </div>
                </div>

                <div style="overflow: auto;margin-top: 15px">
                    <div class="text-center">
                        <h2 id="judul">Judul</h2>
                    </div>
                    <table id="dataTable" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th style="width:1%">No</th>
                                <th width="27%">Nama Mitra</th>
                                <th>Nama Kepala/Pemilik</th>
                                <th>Kontak</th>
                                <th>Username</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-page"></div>
@endsection

@section('additional_scripts')
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript">
        var dt_table;
        var data_kecamatan = [];
        $(document).ready(function() {
            $("#sidebar-mitra").addClass("active");
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
                ajax: {
                    url: '{{ route('dinas-mitra-halaman', ['get_data']) }}?status=' + status,
                    type: 'post',
                },
                columns: [{
                        data: 'DT_RowIndex',
                        sortable: false,
                        searchable: false,
                    },
                    {
                        data: 'nama',
                        name: 'm.nama_mitra',
                    },
                    {
                        data: 'nama_kepala',
                        name: 'm.nama_kepala',
                    },
                    {
                        data: 'kontak',
                        name: 'm.kontak',
                    },
                    {
                        data: 'username',
                        name: 'u.username'
                    },
                    {
                        data: 'status',
                        name: 'm.status_aktif'
                    },
                    {
                        data: 'aksi',
                        searchbale: false,
                        sortable: false
                    },
                ],
            });

            dt_table.on('init', function() {
                $('div.dataTables_filter input')
                    .attr('autocomplete', 'off')
                    .attr('name', 'dt-search');
            });

            filter();
        });

        function filter() {
            var status = $('select[name=status]').val();

            var judul = 'Daftar Mitra ';
            if (status != '') {
                judul += 'Berstatus ' + status;
            } else {
                judul += 'Semua Status';
            }
            $('#judul').html(judul);

            dt_table.ajax.url('{{ route('dinas-mitra-halaman', ['get_data']) }}?status=' + status).load();
        }

        function form_mitra(id) {
            dt_table.on('init', function() {
                $('div.dataTables_filter input')
                    .attr('autocomplete', 'off')
                    .attr('name', 'dt-search');
            });

            $.post('{{ route('dinas-mitra-halaman', ['form_mitra']) }}', {
                id: id
            }, function(data) {
                $('.modal-page').html(data.content);
            }).fail(function() {
                swal('500 - Internal server error');
            })
        }

        function loginAs(id) {
            $.ajax({
                url: '{{ route('login-as') }}',
                type: 'post',
                data: {
                    id: id,
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(data) {
                    if (data.status == 'success') {
                        Swal.fire({
                            type: 'success',
                            title: 'berhasil login!',
                            showConfirmButton: true,
                            timer: 3000
                        }).then((result) => {
                            location.href = '{{ route('common-manajemen-data') }}'
                        })
                    } else {
                        Swal.fire({
                            title: 'Gagal login User!',
                            type: 'error',
                            text: data.message,
                            showConfirmButton: true,
                            timer: 3000
                        }).then((result) => {
                            Swal.close()
                        })
                    }
                }
            });
        }
    </script>
@endsection
