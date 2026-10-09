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
    @include('k3_paud_sd/layout/sidebar')
@endsection

@section('content')
    <div class="content" id="app">
        <h2 class="content-heading" style="padding-top: 0px">Keanggotaan </h2>
        <!-- Dynamic Table Full -->
        <div class="block">
            <div class="block-header block-header-default row no-margin">
                <div style="display: flex;justify-content: center">
                    <h3 class="block-title">Data keanggotaan </h3>
                </div>
                <div style="display: flex;justify-content: center">
                    <a href="javascript:void(0)" onclick="f_form_anggota('0')" class="btn btn-outline-primary"><i
                            class="fa fa-plus"></i> Tambah</a>
                </div>
            </div>
            <div class="block-content block-content-full">
                @include('layout.alert')
                <div class="row">
                    <div class="col-lg-12">
                        <div style="overflow: auto">
                            <table class="table table-bordered" id="tabel_anggota">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama</th>
                                        <th>Jabatan</th>
                                        <th>No Telp</th>
                                        <th>Berkas</th>
                                        <th>#</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12 wadah_widget">
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
        $('#sidebar-anggota').addClass("active");
        var dt_tabel;
        $(document).ready(function() {
            dt_tabel = $('#tabel_anggota').DataTable({
                ajax: {
                    url: '{{ route('keanggotaan-halaman', ['halaman' => 'get_data']) }}',
                    type: 'post',
                    data: function(d) {},
                },
                lengthMenu: [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, 'semua']
                ],
                pageLength: 50,
                columns: [{
                        data: 'DT_RowIndex',
                        searchable: false,
                        sortable: false
                    },
                    {
                        data: 'nama',
                        name: 'nama'
                    },
                    {
                        data: 'jabatan',
                        name: 'jabatan'
                    },
                    {
                        data: 'no_telp',
                        name: 'no_telp'
                    },
                    {
                        data: 'berkas',
                        searchable: false,
                        sortable: false,
                    },
                    {
                        data: 'aksi',
                        searchable: false,
                        sortable: false,
                    },
                ]
            });
        });

        function get_data() {
            dt_tabel.ajax.reload();
        }

        function f_form_anggota(id) {
            $.post('{{ route('keanggotaan-halaman', ['halaman' => 'form_anggota']) }}', {
                id: id
            }, function(data) {
                $('.modal-page').html(data.content);
            });
        }

        function f_hapus(id) {
            swal({
                title: 'Data akan dihapus',
                text: 'Apakah kamu yakin?',
                type: 'warning',
                showCancelButton: true,
            }).then((result) => {
                if (result.value) {
                    $.post('{{ route('keanggotaan-halaman', ['halaman' => 'hapus']) }}', {
                        id: id
                    }, function(data) {
                        if (data.code == 200) {
                            get_data();
                            swal('Success', '200 Berhasil terhapus', 'success');
                        } else {
                            swal('WHooops', '250 Gagal terhapus', 'error');
                        }
                    }).fail(function() {
                        swal('WHooops', '500 Internal Server Error', 'error');
                    })
                }
            });
        }

        function simpan() {
            event.preventDefault();

            var data = $('.isian_anggota').serialize();
            console.log(data);
            $.post('{{ route('keanggotaan-halaman', ['halaman' => 'simpan']) }}', data, function(data) {
                if (data.code == '200') {
                    $('#modal_keanggotaan').modal('hide');
                    get_data();
                    swal('Success', '200 - Berhasil disimpan', 'success');
                } else if (data.code == 400) {
                    swal('Whooops', data.pesan, 'warning');
                } else {
                    swal('Whooops', '250 Gagal tersimpan', 'error');
                }
            }).fail(function() {
                swal('Whooops', '500 Internal server error', 'error');
            })
        }
    </script>
@endsection
