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
        <h2 class="content-heading" style="padding-top: 0px">Daftar Bunda PAUD</h2>
        <!-- Dynamic Table Full -->
        <div class="block">
            <div class="block-header block-header-default row no-margin">
                <h3 class="block-title judul">Daftar Semua Bunda PAUD</h3>
            </div>
            <div class="block-content block-content-full">
                <table id="dataTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th style="width:1%">No</th>
                            <th>Tingkat</th>
                            <th>Jabatan</th>
                            <th>Kecamatan</th>
                            <th>Kelurahan</th>
                            <th width="27%">Nama Bunda</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
        <div class="page-modal"></div>
    </div>
@endsection

@section('additional_scripts')
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript">
        var dtTable;
        $(document).ready(function() {
            $('#sidebar-all-bunda').addClass('active');
            dtTable = $("#dataTable").DataTable({
                dom: 'lBfrtip',
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
                lengthMenu: [
                    [10, 25, 50, -1],
                    [10, 25, 50, "All"]
                ],
                processing: true,
                serverSide: true,
                ajax: '{{ route('dinas-all-bunda-get-list') }}',
                columns: [{
                        data: 'DT_RowIndex',
                        searchable: false,
                        sortable: false
                    },
                    {
                        data: 'tingkat'
                    },
                    {
                        data: 'nama_jabatan'
                    },
                    {
                        data: 'kecamatan'
                    },
                    {
                        data: 'kelurahan'
                    },
                    {
                        data: 'nama'
                    },
                    {
                        data: 'status'
                    },
                    {
                        data: 'aksi',
                        searchable: false,
                        sortable: false
                    },
                ],
            });
        });
    </script>
    <script>
        function get_data() {
            dtTable.ajax.url('{{ route('dinas-all-bunda-get-list') }}').load();
        }

        function ubah(id) {
            $.post('{{ route('dinas-all-bunda-modal') }}', {
                id: id
            }, function(data) {
                if (data.code == '200') {
                    $('.page-modal').html(data.content);
                } else {
                    swal('Whooops', 'Profil tidak ditemukan', 'error');
                }
            })
        }

        function riwayat(id) {
            $.post('{{ route('dinas-all-bunda-riwayat') }}', {
                id: id
            }, function(data) {
                if (data.code == '200') {
                    $('.page-modal').html(data.content);
                } else {
                    swal('Whooops', 'Profil tidak ditemukan', 'error');
                }
            })
        }

        function simpan() {
            var data = $('form#form_simpan').serialize(0);
            $.post("{{ route('dinas-all-bunda-simpan') }}", data, function(data) {
                if (data.code == '200') {
                    swal('Success', 'Berhasil disimpan', 'success');
                    $('tbody').html('');
                    $('.page-modal').html('');
                    $('.modal-backdrop').removeClass('modal-backdrop');
                    get_data();
                } else {
                    swal('Whooops', 'Gagal disimpan', 'error');
                }
            });
        }
    </script>
@endsection
