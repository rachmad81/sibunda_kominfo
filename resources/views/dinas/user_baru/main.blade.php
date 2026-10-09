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
        <h2 class="content-heading" style="padding-top: 0px">Kelola Pengguna</h2>
        <!-- Dynamic Table Full -->
        <div class="block">
            <div class="block-header block-header-default row no-margin">
                <h3 class="block-title">Daftar Pengguna</h3>
                <button class="btn btn-outline-success col-md-2 m-l-xs m-b-xs" data-toggle="modal"
                    data-target="#modal_tambah">Tambah Pengguna</button>
            </div>
            <div class="block-content block-content-full">
                <div class="col-md-4">
                    <div class="form-group row">
                        <label class="label-control">Jenis Akun</label>
                        <select class="form-control" name="filter_role" onchange="get_data()">
                            <option value="">-- Pilih Role --</option>
                            <option value="all">Semua</option>
                            <option value="opd">Opd (Dinas)</option>
                            <option value="bunda">Bunda</option>
                            <option value="penilai">Penilai</option>
                            <option value="k3_paud">K3 PAUD</option>
                            <option value="paud_sd">Forum Komunikasi PAUD SD</option>
                        </select>
                    </div>
                </div>
                @include('layout.alert')
                <table id="dataTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th style="width:1%">No</th>
                            <th width="27%">Nama</th>
                            <th>Nama Pengguna</th>
                            <th>Role</th>
                            <th>Tingkat</th>
                            <th>Wilayah</th>
                            <th>Masuk Terakhir</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
        @include('dinas.user.modal')
        @include('attention')
    </div>
@endsection

@section('additional_scripts')
    <script type="text/javascript">
        var dt_tabel;
        $("#sidebar-user").addClass("active");
        $(document).ready(function() {
            dt_tabel = $("#dataTable").DataTable({
                dom: "<'row'<'col-md-6'l><'col-md-6'B>>" +
                    "<'row'<'col-md-12'f>>" +
                    "<'row'<'col-md-12't>><'row'<'col-md-12'ip>>",
                buttons: [],
                scrollX: true,
                processing: true,
                serverSide: true,
                autoWidth: false,
                ajax: {
                    url: '{{ route('dinas-user_baru', ['halaman' => 'get_data']) }}',
                    type: 'post',
                    data: function(d) {
                        var role = $('select[name=filter_role]').val();
                        d.role = role;
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        sortable: false,
                        searchable: false,
                    },
                    {
                        data: 'nama'
                    },
                    {
                        data: 'username'
                    },
                    {
                        data: 'role',
                        name: 'role'
                    },
                    {
                        data: 'tingkat'
                    },
                    {
                        data: 'wilayah'
                    },
                    {
                        data: 'last_login'
                    },
                    {
                        data: 'aksi'
                    },
                ],
            });
        });

        function get_data() {
            dt_tabel.ajax.reload();
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
