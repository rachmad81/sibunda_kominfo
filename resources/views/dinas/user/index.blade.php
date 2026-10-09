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
                <h3 class="block-title judul">Daftar Pengguna</h3>
                <button class="btn btn-outline-success col-md-2 m-l-xs m-b-xs" data-toggle="modal"
                    data-target="#modal_tambah">Tambah Pengguna</button>
            </div>
            <div class="block-content block-content-full">
                <div class="col-md-4">
                    <div class="form-group row">
                        <label class="label-control">Jenis Akun</label>
                        <select class="form-control" name="filter_role" v-model="filter_role">
                            <option value="">-- Pilih Role --</option>
                            <option value="all">Semua</option>
                            <option value="opd">Opd (Dinas)</option>
                            <option value="bunda">Bunda</option>
                            <option value="penilai">Penilai</option>
                            <option value="k3_paud">K3 PAUD</option>
                            <option value="paud_sd">Forum Komunikasi PAUD SD</option>
                            {{-- <option value="gugus">Gugus Tugas</option> --}}
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
                    url: '{{ route('dinas-user-index') }}?jenis=' + vm.filter_role,
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
                        data: 'role'
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
    </script>
    <script>
        let vm = new Vue({
            el: "#app",
            data: {
                selected_role: 'dinas',
                selected_tingkat: '',
                selected_wilayah: '',
                list_wilayah: [],
                ganti_pass: false,
                filter_role: 'all',
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
                    this.selected_role = this.detail.role
                    this.selected_tingkat = this.detail.tingkat

                    if (this.detail.tingkat == 'kecamatan') {
                        this.selected_wilayah = this.detail.id_kecamatan
                    } else if (this.detail.tingkat == 'kelurahan') {
                        this.selected_wilayah = this.detail.id_kelurahan
                    }
                },
                loginAs: function(id) {
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
                                    url: "{{ route('dinas-user-delete') }}",
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
                filter_role: function() {
                    if (this.filter_role == 'all')
                        var url = '{{ route('dinas-user-index') }}'
                    else
                        var url = '{{ route('dinas-user-index') }}?jenis=' + this.filter_role

                    if ($.fn.DataTable.isDataTable('#dataTable')) {
                        $("#dataTable").DataTable().clear().destroy()
                    }

                    $("#dataTable").DataTable({
                        dom: "<'row'<'col-md-6'l><'col-md-6'B>>" +
                            "<'row'<'col-md-12'f>>" +
                            "<'row'<'col-md-12't>><'row'<'col-md-12'ip>>",
                        buttons: [],
                        scrollX: true,
                        autoWidth: false,
                        processing: true,
                        serverSide: true,
                        ajax: {
                            url: url,
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
                                data: 'role'
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
                        ]
                    });
                },
                selected_tingkat: function() {
                    if (this.selected_tingkat != 'kota') {
                        $.ajax({
                            url: '{{ route('dinas-user-get-wilayah') }}',
                            type: 'get',
                            data: {
                                jenis: vm.selected_tingkat,
                            },
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(data) {
                                if (data.status == 'success') {
                                    vm.list_wilayah = data.obj
                                } else {
                                    Swal.fire({
                                        title: 'Gagal!',
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

                }
            },
        });
    </script>
@endsection
