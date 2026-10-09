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
        <h2 class="content-heading" style="padding-top: 0px">Verifikasi PAUD Bermutu</h2>
        <!-- Dynamic Table Full -->
        <div class="main-page block">
            <div class="block-header block-header-default row no-margin">
                <h3 class="block-title">Verifikasi PAUD Bermutu</h3>
            </div>
            <div class="block-content block-content-full">
                @include('layout.alert')
                <div class="row">
                    <div class="col-lg-12" style="overflow: auto">
                        <div>
                            <center>
                                <h1 class="judul" style="display:none;">Verifikasi PAUD Bermutu</h1>
                            </center>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6" style="border-bottom: 1px solid #000;padding-bottom: 10px;">
                        <h4>Penyaringan data (filter)</h4>
                        <table style="width: 100%;">
                            <tr>
                                <td>Tahun Monev</td>
                                <td>
                                    <select class="form-control tahun_monev">
                                        @foreach ($tahun as $t)
                                            <option value="{{ $t }}">{{ $t }}</option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>Kecamatan</td>
                                <td>
                                    @php
                                        $id_kecamatan = Auth::getUser()->id_kecamatan;
                                        $id_kelurahan = Auth::getUser()->id_kelurahan;
                                        $tingkat = Auth::getUser()->tingkat;
                                        $status_kecamatan = '';
                                        $status_kelurahan = '';
                                        if ($tingkat == 'kecamatan') {
                                            $status_kecamatan = 'disabled';
                                        } elseif ($tingkat == 'kelurahan') {
                                            $cari_kecamatan = collect($data_kecamatan)
                                                ->where('id_kelurahan', $id_kelurahan)
                                                ->first();

                                            $id_kecamatan = $cari_kecamatan->id_kecamatan ?? '';

                                            $status_kecamatan = 'disabled';
                                            $status_kelurahan = 'disabled';
                                        }
                                    @endphp

                                    <select name="kecamatan" class="form-control wadah_kecamatan" {{ $status_kecamatan }}
                                        onchange="get_kelurahan()">
                                        <option value="all">..:: Semua Kecamatan ::..</option>
                                        @foreach ($kecamatan as $kec)
                                            <option @if ($id_kecamatan == $kec['id_kecamatan']) selected @endif
                                                value="{{ $kec['kecamatan'] }}">{{ $kec['kecamatan'] }}</option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>Kelurahan</td>
                                <td>
                                    <select name="kelurahan" {{ $status_kelurahan }} class="form-control wadah_kelurahan">
                                        <option value="all">..:: Semua Kelurahan ::..</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>Status Verifikasi</td>
                                <td>
                                    <select class="form-control status_monev">
                                        <option value="all">Semua</option>
                                        <option>Sudah</option>
                                        <option>Belum</option>
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
                        <table id="dataTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th style="width:1%">No</th>
                                    <th>NPSN</th>
                                    <th width="27%">Nama Lembaga</th>
                                    <th>Jenjang Lembaga</th>
                                    <th>Status Lembaga</th>
                                    <th width="27%">Alamat</th>
                                    <th>Kelurahan</th>
                                    <th>Kecamatan</th>
                                    <th>Jumlah Berkas</th>
                                    <th>Status Pending</th>
                                    <th>Status Ditolak</th>
                                    <th>Status Disetujui</th>
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
        var tahun_monev = '';
        var status_monev = '';
        var dt_kecamatan = {!! $kecamatan !!};
        var kecamatan_cari = '';
        var kelurahan_cari = '';
        $(document).ready(function() {
            $('#sidebar-paud_mutu').addClass('active');
            tahun_monev = $('.tahun_monev').val();
            status_monev = $('.status_monev').val();
            kecamatan_cari = $('.wadah_kecamatan').val();
            kelurahan_cari = $('.wadah_kelurahan').val();

            dt_tabel = $("#dataTable").DataTable({
                dom: "<'row'<'col-md-6'l><'col-md-6'B>>" +
                    "<'row'<'col-md-12'f>>" +
                    "<'row'<'col-md-12'tr>><'row'<'col-md-12'ip>>",
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
                    url: '{{ route('paud_mutu-paudmutu', ['halaman' => 'get_data']) }}',
                    type: 'post',
                    data: function(d) {
                        d.tahun_monev = tahun_monev;
                        d.status_monev = status_monev;
                        d.kecamatan = kecamatan_cari;
                        d.kelurahan = kelurahan_cari;
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
                        data: 'npsn',
                        name: 'sek.npsn'
                    },
                    {
                        data: 'nama',
                        name: 'sek.nama'
                    },
                    {
                        data: 'jenjang',
                        name: 'sek.jenjang'
                    },
                    {
                        data: 'status',
                        name: 'sek.status'
                    },
                    {
                        data: 'alamat',
                        name: 'sek.alamat'
                    },
                    {
                        data: 'kelurahan_dispenduk',
                        name: 'kel.kelurahan_dispenduk'
                    },
                    {
                        data: 'kecamatan_dispenduk',
                        name: 'kec.kecamatan_dispenduk'
                    },
                    {
                        data: 'jumlah_berkas',
                        name: 'jumlah_berkas',
                        searchable: false
                    },
                    {
                        data: 'pending',
                        name: 'pending',
                        searchable: false
                    },
                    {
                        data: 'ditolak',
                        name: 'ditolak',
                        searchable: false
                    },
                    {
                        data: 'disetujui',
                        name: 'disetujui',
                        searchable: false
                    },
                    {
                        data: 'aksi',
                        name: 'aksi',
                        searchable: false,
                        orderable: false
                    },
                ],
            });

            dt_tabel.on('processing.dt', function(e, settings, processing) {
                if (processing) {
                    $('#dataTable tbody').html(
                        '<tr><td colspan="10" class="text-center">Loading...</td></tr>'
                    );
                }
            });

            get_kelurahan();
            get_data();
        });

        function get_data() {
            tahun_monev = $('.tahun_monev').val();
            status_monev = $('.status_monev').val();
            kecamatan_cari = $('.wadah_kecamatan').val();
            kelurahan_cari = $('.wadah_kelurahan').val();

            dt_tabel.ajax.reload();
        }

        function kirim() {
            event.preventDefault();
            var data = $('form.form-monev').serialize();

            $.post('{{ route('paud_mutu-paudmutu', ['halaman' => 'simpan']) }}', data, function(data) {
                if (data.code == 200) {
                    swal('Success', 'Berhasil disimpan', 'success');
                    $('#modal_profil').modal('hide');
                    get_data();
                } else {
                    swal('Whoooops', 'Gagal disimpan', 'error');
                }
            });
        }

        function get_kelurahan() {
            var html = '<option value="all">..:: Semua Kelurahan ::..</option>';
            var kecamatan = $('.wadah_kecamatan').val();

            if (kecamatan != 'all') {
                if (dt_kecamatan[kecamatan].kelurahan.length > 0) {
                    var id_kelurahan = '{{ $id_kelurahan }}';
                    $.each(dt_kecamatan[kecamatan].kelurahan, function(k, v) {
                        if (id_kelurahan == v.id_kelurahan) {
                            html += '<option selected value="' + v.kelurahan_dispenduk + '">' + v
                                .kelurahan_dispenduk +
                                '</option>';
                        } else {
                            html += '<option value="' + v.kelurahan_dispenduk + '">' + v.kelurahan_dispenduk +
                                '</option>';
                        }
                    });
                }
            }

            $('.wadah_kelurahan').html(html);
        }
    </script>
@endsection
