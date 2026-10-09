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
        <h2 class="content-heading" style="padding-top: 0px">Data anak belum masuk sekolah</h2>
        <!-- Dynamic Table Full -->
        <div class="main-page block">
            <div class="block-header block-header-default row no-margin">
                <h3 class="block-title">Data anak belum masuk sekolah</h3>
            </div>
            <div class="block-content block-content-full">
                @include('layout.alert')
                <div class="row">
                    <div class="col-lg-12" style="overflow: auto">
                        <div>
                            <center>
                                <h1 class="judul" style="display:none;">Data anak belum masuk sekolah</h1>
                            </center>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6" style="border-bottom: 1px solid #000;padding-bottom: 10px;">
                        <h4>Penyaringan data (filter)</h4>
                        <table style="width: 100%;">
                            <tr>
                                <td>Berdasarkan API cek di profil sekolah</td>
                                <td>
                                    <select class="form-control status_api">
                                        <option>Tidak ada</option>
                                        <option>Ada</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>Status Alamat</td>
                                <td>
                                    <select class="form-control status_alamat">
                                        <option>Semua</option>
                                        <option value="Sesuai">Sesuai KK</option>
                                        <option value="Tidak sesuai">Tidak sesuai KK</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>Status Bersekolah</td>
                                <td>
                                    <select class="form-control status_sekolah">
                                        <option>Semua</option>
                                        <option value="Sudah">Sudah sekolah</option>
                                        <option value="Tidak sekolah">Tidak sekolah</option>
                                        <option value="Akan sekolah">Akan sekolah</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>Status Verifikasi</td>
                                <td>
                                    <select class="form-control status_verifikasi">
                                        <option>Semua</option>
                                        <option>Draft</option>
                                        <option>Selesai</option>
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
                        <a href="javascript:void(0)" class="btn btn-sm btn-primary" onclick="tambah()">Tambah data siswa</a>
                        <table id="dataTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th style="width:1%">No</th>
                                    <th width="27%">Nama</th>
                                    <th>Alamat</th>
                                    <th>RT</th>
                                    <th>RW</th>
                                    <th>Kelurahan</th>
                                    <th>Kecamatan</th>
                                    <th>Status Verifikasi</th>
                                    <th>Status Alamat</th>
                                    <th>Status Bersekolah</th>
                                    <th>Detail Alasan</th>
                                    <th>Bukti survei</th>
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
        $(document).ready(function() {
            $('#sidebar-monitoring-blm_sekolah').addClass('active');
            var status_api = $('.status_api').val();
            var status_alamat = $('.status_alamat').val();
            var status_sekolah = $('.status_sekolah').val();
            var status_verifikasi = $('.status_verifikasi').val();
            var param = 'status_api=' + status_api + '&status_alamat=' + status_alamat + '&status_sekolah=' +
                status_sekolah + '&status_verifikasi=' + status_verifikasi

            dt_tabel = $("#dataTable").DataTable({
                dom: "<'row'<'col-md-6'l><'col-md-6'B>>" +
                    "<'row'<'col-md-12'f>>" +
                    "<'row'<'col-md-12't>><'row'<'col-md-12'ip>>",
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
                    url: '{{ route('bunda-monitoring-blm_sekolah-data') }}?' + param,
                    type: 'get',
                },
                lengthMenu: [
                    [10, 25, 50, -1],
                    [10, 25, 50, "All"]
                ],
                columns: [{
                        data: 'DT_RowIndex'
                    },
                    {
                        data: 'nama'
                    },
                    {
                        data: 'alamat'
                    },
                    {
                        data: 'rt'
                    },
                    {
                        data: 'rw'
                    },
                    {
                        data: 'kelurahan'
                    },
                    {
                        data: 'kecamatan'
                    },
                    {
                        data: 'status_verif'
                    },
                    {
                        data: 'status_alamat'
                    },
                    {
                        data: 'status_sekolah'
                    },
                    {
                        data: 'detail_alasan'
                    },
                    {
                        data: 'tombol_bukti_survei'
                    },
                    {
                        data: 'aksi'
                    },
                ],
            });
        });

        function get_data() {
            var status_api = $('.status_api').val();
            var status_alamat = $('.status_alamat').val();
            var status_sekolah = $('.status_sekolah').val();
            var status_verifikasi = $('.status_verifikasi').val();
            var param = 'status_api=' + status_api + '&status_alamat=' + status_alamat + '&status_sekolah=' +
                status_sekolah + '&status_verifikasi=' + status_verifikasi
            dt_tabel.ajax.url('{{ route('bunda-monitoring-blm_sekolah-data') }}?' + param).load();
        }

        function ganti(nik) {
            $('.main-page').hide();
            $.post('{{ route('bunda-monitoring-blm_sekolah-ganti') }}', {
                nik: nik
            }, function(data) {
                if (data.code == 200) {
                    $('.modal-page').show();
                    $('.modal-page').html(data.content);
                }
            }).fail(function() {
                kembali();
            });
        }

        function kembali() {
            $('.main-page').show();
            $('.modal-page').hide();
        }

        function simpan() {
            var f_data = new FormData($('form.form_sekolah')[0]);

            $.ajax({
                url: '{{ route('bunda-monitoring-blm_sekolah-simpan') }}',
                data: f_data,
                type: 'POST',
                processData: false,
                contentType: false,
                success: function(data) {
                    if (data.code == 200) {
                        swal('Success', 'Berhasil disimpan', 'success');
                        kembali();
                        get_data();
                    } else if (data.code == 350) {
                        swal('Whooops', data.message, 'error');
                    } else {
                        swal('Whoooops', 'Gagal disimpan', 'error');
                    }
                },
                fail: function() {
                    swal('Whoooops', 'Gagal disimpan', 'error');
                }
            });
        }

        function tambah() {
            $('.main-page').hide();
            $.post('{{ route('bunda-monitoring-blm_sekolah-tambah') }}', {}, function(data) {
                if (data.code == 200) {
                    $('.modal-page').show();
                    $('.modal-page').html(data.content);
                }
            }).fail(function() {
                kembali();
            });
        }
    </script>
@endsection
