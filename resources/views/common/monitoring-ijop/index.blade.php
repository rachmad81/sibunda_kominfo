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
    @if (\Auth::User()->role == 'dinas')
        @include('dinas/layout/sidebar')
    @elseif(\Auth::User()->role == 'bunda')
        @include('bunda/layout/sidebar')
    @else
        @include('pokja/layout/sidebar')
    @endif
@endsection

@section('content')
    <div class="content" id="app">
        <h2 class="content-heading" style="padding-top: 0px">Monitoring Ijin Operasional</h2>
        <!-- Dynamic Table Full -->
        <div class="block">
            <div class="block-header block-header-default row no-margin">
                <h3 class="block-title">Daftar Monitoring</h3>
                <button class="btn btn-outline-success col-md-2 m-l-xs m-b-xs" data-toggle="modal"
                    data-target="#modal_tambah">Tambah Monitoring</button>
            </div>
            <div class="block-content block-content-full">
                @include('layout.alert')
                <table id="dataTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th style="width:1%">No</th>
                            <th>Tanggal</th>
                            <th>Petugas</th>
                            <th>Sekolah</th>
                            <th>Hasil</th>
                            <th>Keterangan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
        @include('common.monitoring-ijop.modal')
    </div>
@endsection

@section('additional_scripts')
    <script type="text/javascript">
        $(document).ready(function() {
            $("#dataTable").DataTable({
                dom: "<'row'<'col-md-6'l><'col-md-6'B>>" +
                    "<'row'<'col-md-12'f>>" +
                    "<'row'<'col-md-12't>><'row'<'col-md-12'ip>>",
                buttons: [],
                scrollX: true,
                processing: true,
                serverSide: true,
                autoWidth: false,
                ajax: {
                    url: '{{ route('common-monitoring-index') }}',
                    type: 'get',
                },
                columns: [{
                        data: 'DT_RowIndex'
                    },
                    {
                        data: 'tanggal'
                    },
                    {
                        data: 'petugas'
                    },
                    {
                        data: 'sekolah'
                    },
                    {
                        data: 'hasil'
                    },
                    {
                        data: 'keterangan'
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
                hasil: '',
                detail: []
            },
            created: function() {
                $("#sidebar-monitor").addClass("active");
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
                        text: "Data akan dihapus! data yang sudah dihapus tidak dapat dikembalikan kembali!",
                        type: 'warning',
                        showCancelButton: true,
                        cancelButtonText: 'Batal',
                        confirmButtonColor: '#d33',
                        confirmButtonText: 'Hapus',
                        showLoaderOnConfirm: true,
                        preConfirm: () => {
                            return new Promise(function(resolve) {
                                $.ajax({
                                    url: "{{ route('common-monitoring-delete') }}",
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
                                                title: 'Data berhasil dihapus',
                                                text: data.message,
                                                showConfirmButton: true,
                                                timer: 3000
                                            }).then((result) => {
                                                window.location.reload()
                                            })
                                        } else {
                                            Swal.fire({
                                                title: 'Data gagal dihapus',
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
