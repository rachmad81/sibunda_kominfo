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
    @include('bunda/layout/sidebar')
@endsection

@section('content')
    <div class="content" id="app">
        <h2 class="content-heading" style="padding-top: 0px">Kelola Pengguna</h2>
        <!-- Dynamic Table Full -->
        <div class="block">
            <div class="block-header block-header-default row no-margin">
                <h3 class="block-title judul">Daftar Pengguna</h3>
                <button class="btn btn-outline-success col-md-2 m-l-xs m-b-xs" data-toggle="modal"
                    data-target="#modal_tambah">Tambah Pengguna</button>
            </div>
            <div class="block-content block-content-full">
                @include('layout.alert')
                <table id="dataTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th style="width:1%">No</th>
                            <th width="27%">Nama</th>
                            <th>Nama Pengguna</th>
                            <th>Jabatan</th>
                            <th>Status</th>
                            <th>Masuk Terakhir</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
        @include('bunda.user.modal')
        @include('attention')
    </div>
@endsection

@section('additional_scripts')
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $("#dataTable").DataTable({
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
                    url: '{{ route('bunda-user-index') }}',
                    type: 'get',
                },
                columns: [{
                        data: 'DT_RowIndex'
                    },
                    {
                        data: 'nama'
                    },
                    {
                        data: 'username'
                    },
                    {
                        data: 'jabatan'
                    },
                    {
                        data: 'status'
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
    </script>
    <script>
        let vm = new Vue({
            el: "#app",
            data: {
                ganti_pass: false,
                detail: []
            },
            created: function() {
                $("#sidebar-user").addClass("active");
                jQuery(function() {
                    Codebase.helpers(['datepicker', 'select2']);
                });
            },
            methods: {
                updateData: function(data) {
                    this.detail = JSON.parse(data)
                },
                confirmDeleteAlert: function(id) {

                    Swal.fire({
                        title: "Apakah Anda yakin?",
                        text: "Data akan dihapus! Anda perlu menghubungi developer untuk mengembalikannya lagi!",
                        type: 'warning',
                        showCancelButton: true,
                        cancelButtonText: 'Batal',
                        confirmButtonColor: '#d33',
                        confirmButtonText: 'Hapus',
                        showLoaderOnConfirm: true,
                        preConfirm: () => {
                            return new Promise(function(resolve) {
                                $.ajax({
                                    url: "{{ route('bunda-user-delete') }}",
                                    type: 'POST',
                                    data: {
                                        id: id,
                                    },
                                    headers: {
                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]')
                                            .attr('content')
                                    },
                                    success: function(data) {
                                        if (data.status == 'success') {
                                            Swal.fire({
                                                type: 'success',
                                                title: 'User berhasil dihapus',
                                                text: data.message,
                                                showConfirmButton: true,
                                                timer: 3000
                                            }).then((result) => {
                                                window.location.reload()
                                            })
                                        } else {
                                            Swal.fire({
                                                title: 'User gagal dihapus',
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
                            })
                        },
                        allowOutsideClick: () => !Swal.isLoading()
                    })
                }
            },
            computed: {

            },
            watch: {

            },
        });
    </script>
@endsection
