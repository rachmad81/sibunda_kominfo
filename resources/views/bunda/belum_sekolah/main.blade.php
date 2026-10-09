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
                                <td>Status Alamat</td>
                                <td>
                                    <select class="form-control status_alamat">
                                        <option>Semua</option>
                                        <option value="Sesuai">Sesuai KK</option>
                                        <option value="Tidak sesuai">Tidak sesuai KK</option>
                                    </select>
                                </td>
                            </tr>
                            {{-- <tr>
                                <td>Status Bersekolah</td>
                                <td>
                                    <select class="form-control status_sekolah">
                                        <option value="Tidak sekolah">Tidak sekolah</option>
                                        <option value="Sudah">Sudah sekolah</option>
                                        <option value="Akan sekolah">Akan sekolah</option>
                                    </select>
                                </td>
                            </tr> --}}
                            <tr>
                                <td>Status Verifikasi</td>
                                <td>
                                    <select class="form-control status_verifikasi">
                                        <option>Semua</option>
                                        <option>Belum</option>
                                        <option>Draft</option>
                                        <option>Selesai</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>Usia</td>
                                <td>
                                    <select class="form-control status_usia">
                                        <option>Semua</option>
                                        @for ($i = 0; $i <= 6; $i++)
                                            <option>{{ $i }}</option>
                                        @endfor
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
                                    <th>NIK</th>
                                    <th>Usia</th>
                                    <th>Kelamin</th>
                                    <th>Alamat</th>
                                    <th>RT</th>
                                    <th>RW</th>
                                    <th>Kelurahan</th>
                                    <th>Kecamatan</th>
                                    <th>Alamat Domisili</th>
                                    <th>RT Domisili</th>
                                    <th>RW Domisili</th>
                                    <th>Kelurahan Domisili</th>
                                    <th>Kecamatan Domisili</th>
                                    <th>Status Survey</th>
                                    <th>Status Alamat</th>
                                    <th>Catatan Alamat</th>
                                    <th>Status Bersekolah</th>
                                    <th>Catatan Bersekolah</th>
                                    <th>Faktor Tidak Bersekolah</th>
                                    <th>Catatan Tidak Bersekolah</th>
                                    <th>Edukasi Orang Tua</th>
                                    <th>Tanggapan Orang Tua</th>
                                    <th>Tindak Lanjut</th>
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
        @include('bunda.belum_sekolah.modal_info')
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
        var status_alamat = '';
        var status_sekolah = '';
        var status_verifikasi = '';
        var status_usia = '';
        $(document).ready(function() {
            // $('#modal_tambah').modal({
            //     backdrop: 'static'
            // });

            $('#sidebar-monitor').addClass('active');
            status_alamat = $('.status_alamat').val();
            status_sekolah = $('.status_sekolah').val();
            status_verifikasi = $('.status_verifikasi').val();
            status_usia = $('.status_usia').val();

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
                    url: '{{ route('bunda-monitoring-blm_sekolah-data') }}',
                    type: 'post',
                    data: function(d) {
                        d.status_alamat = status_alamat;
                        d.status_sekolah = status_sekolah;
                        d.status_verifikasi = status_verifikasi;
                        d.status_usia = status_usia;
                    }
                },
                lengthMenu: [
                    [10, 25, 50, -1],
                    [10, 25, 50, "All"]
                ],
                columns: [{
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: 'nama_lengkap',
                        name: 'a.nama_lengkap'
                    },
                    {
                        data: 'nik',
                        name: 'a.nik'
                    },
                    {
                        data: 'usia',
                        name: 'a.tgl_lahir',
                        searchable: false
                    },
                    {
                        data: 'jenis_kelamin',
                        name: 'a.jenis_kelamin'
                    },
                    {
                        data: 'alamat',
                        name: 'a.alamat'
                    },
                    {
                        data: 'rt',
                        name: 'a.rt'
                    },
                    {
                        data: 'rw',
                        name: 'a.rw'
                    },
                    {
                        data: 'kelurahan',
                        name: 'a.kelurahan'
                    },
                    {
                        data: 'kecamatan',
                        name: 'a.kecamatan'
                    },
                    {
                        data: 'alamat_dom',
                        name: 'a.alamat_dom'
                    },
                    {
                        data: 'rt_dom',
                        name: 'a.rt_dom'
                    },
                    {
                        data: 'rw_dom',
                        name: 'a.rw_dom'
                    },
                    {
                        data: 'kelurahan_dom',
                        name: 'a.kelurahan_dom'
                    },
                    {
                        data: 'kecamatan_dom',
                        name: 'a.kecamatan_dom'
                    },
                    {
                        data: 'status_verif',
                        name: 'b.status_verifikasi'
                    },
                    {
                        data: 'status_domisili',
                        name: 'status_domisili'
                    },
                    {
                        data: 'detail_alamat',
                        name: 'b.detail_alamat'
                    },
                    {
                        data: 'status_sekolah_n',
                        name: 'status_sekolah_n'
                    },
                    {
                        data: 'detail_sekolah',
                        name: 'b.detail_sekolah'
                    },
                    {
                        data: 'status_faktor',
                        name: 'status_faktor'
                    },
                    {
                        data: 'pilihan_detail_tidak_sekolah',
                        name: 'b.pilihan_detail_tidak_sekolah'
                    },
                    {
                        data: 'status_edukasi',
                        name: 'status_edukasi'
                    },
                    {
                        data: 'status_tanggapan',
                        name: 'status_tanggapan'
                    },
                    {
                        data: 'status_rtl',
                        name: 'status_rtl'
                    },
                    {
                        data: 'tombol_bukti_survei',
                        name: 'b.bukti_survey',
                        searchable: false,
                    },
                    {
                        data: 'aksi',
                        sortable: false,
                        searchable: false
                    },
                ],
            });
        });

        function get_data() {
            status_alamat = $('.status_alamat').val();
            status_sekolah = $('.status_sekolah').val();
            status_verifikasi = $('.status_verifikasi').val();
            status_usia = $('.status_usia').val();

            // dt_tabel.clear().draw();
            dt_tabel.on('preXhr.dt', function() {
                // Menghapus elemen TR di dalam tbody secara langsung
                $('#dataTable tbody').empty();
            });
            dt_tabel.ajax.reload();
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
