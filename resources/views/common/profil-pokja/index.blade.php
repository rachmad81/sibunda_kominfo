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
        <h2 class="content-heading" style="padding-top: 0px">Kelola Pokja</h2>
        <!-- Dynamic Table Full -->
        <div class="block">
            <div class="block-header block-header-default row no-margin">
                <h3 class="block-title">Daftar Pokja</h3>
                <a href="{{ route('common-pokja-kelola', ['tambah']) }}"
                    class="btn btn-outline-success col-md-2 m-l-xs m-b-xs">Tambah Pokja</a>
            </div>
            <div class="block-content block-content-full">
                @include('layout.alert')
                <table id="dataTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th style="width:1%">No</th>
                            <th width="27%">Nama</th>
                            <th>JK</th>
                            <th>Jabatan</th>
                            <th>TTL</th>
                            <th>No. Telp</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $key => $value)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $value->nama }}</td>
                                <td>{{ $value->kelamin }}</td>
                                <td>{{ $value->jabatanPokja->nama_jabatan }}</td>
                                <td>{{ $value->tempat_lahir . ', ' . $value->tgl_lahir }}</td>
                                <td>{{ $value->no_telp }}</td>
                                <td>
                                    <a class="btn btn-sm btn-outline-warning"
                                        href="{{ route('common-pokja-kelola', ['ubah']) . '?id=' . $value->id }}">
                                        Ubah
                                    </a>
                                    <button class="btn btn-sm btn-outline-danger"
                                        @click="confirmDeleteAlert('{{ $value->id }}')">Hapus</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
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
                $("#sidebar-pokja").addClass("active");
                jQuery(function() {
                    Codebase.helpers(['datepicker', 'select2']);
                });
            },
            methods: {
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
                                    url: "{{ route('common-pokja-delete') }}",
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
                                                title: 'Pokja berhasil dihapus',
                                                text: data.message,
                                                showConfirmButton: true,
                                                timer: 3000
                                            }).then((result) => {
                                                window.location.reload()
                                            })
                                        } else {
                                            Swal.fire({
                                                title: 'Pokja gagal dihapus',
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
