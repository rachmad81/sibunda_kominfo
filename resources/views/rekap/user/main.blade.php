@extends('layout.index')

@section('additional_styles')
    <title class="judul"></title>
@endsection

@section('additional_styles')
    <style>
        tr td {
            text-align: center;
        }
    </style>
@endsection
@section('sidebar')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">
    @include('dinas/layout/sidebar')
@endsection

@section('content')
    <div class="content" id="app">
        <h2 class="content-heading" style="padding-top: 0px">Rekap Pengguna</h2>
        <!-- Dynamic Table Full -->
        <div class="block">
            <div class="block-header block-header-default row no-margin">
                <h3 class="block-title judul">Rekap Pengguna</h3>
            </div>
            <div class="block-content block-content-full">
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group row">
                                <label class="label-control">Jenis Akun</label>
                                <select class="form-control" id="role">
                                    <option value="">-- Pilih Role --</option>
                                    <option value="all">Semua</option>
                                    <option value="opd">Dinas</option>
                                    <option value="bunda">Bunda</option>
                                    <option value="pokja">Pokja</option>
                                    <option value="k3_paud">K3s PAUD</option>
                                    <option value="mitra">Mitra</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group row">
                                <label class="label-control">Tingkat</label>
                                <select class="form-control" id="tingkat" onchange="get_tingkat(this.value)">
                                    <option value="all">Semua</option>
                                    <option value="kota">Kota</option>
                                    <option value="kecamatan">Kecamatan</option>
                                    <option value="kelurahan">Kelurahan</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2" id="kecamatannya">
                            <div class="form-group row">
                                <label class="label-control">Kecamatan</label>
                                <select class="form-control" id="kecamatan" onchange="get_kelurahan(this.value)">
                                    <option value="all">Semua Kecamatan</option>
                                    @if ($kecamatan->count() != 0)
                                        @foreach ($kecamatan as $kec)
                                            <option value="{{ $kec->kecamatan_id }}">{{ $kec->kecamatan_dispenduk }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2" id="kelurahannya">
                            <div class="form-group row">
                                <label class="label-control">Kelurahan</label>
                                <select class="form-control" id="tempat_kelurahan">
                                    <option value="all">Semua Kelurahan</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row" style="margin-bottom: 15px;">
                    <div class="col-md-3">
                        <a href="javascript:void(0)" class="btn btn-primary" onclick="get_data()"><i
                                class="fa fa-search"></i> Cari</a>
                    </div>
                </div>
                @include('layout.alert')
                <div>
                    <center>
                        <h1 class="judul"></h1>
                    </center>
                </div>
                <table id="dataTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th style="width:1%">No</th>
                            <th width="27%">Nama</th>
                            <th>Tingkat</th>
                            <th>Jabatan</th>
                            <th>SK Bunda</th>
                            <th>SK Pokja</th>
                            <th>Kecamatan</th>
                            <th>Kelurahan</th>
                            <th>Masuk Terakhir</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
        @include('dinas.user.modal')
    </div>
@endsection

@section('additional_scripts')
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript">
        var dt_table = '';
        var judul = 'judul';
        $(document).ready(function() {
            $("#sidebar-rekap").addClass("active");
            $('#kecamatannya').hide();
            $('#kelurahannya').hide();

            dt_table = $("#dataTable").DataTable({
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
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('rekap-user-data') }}',
                    type: 'get',
                },
                lengthMenu: [
                    [10, 25, 50, -1],
                    [10, 25, 50, "All"]
                ],
                iDisplayLength: -1,
                columns: [{
                        data: 'DT_RowIndex'
                    },
                    {
                        data: 'nama'
                    },
                    {
                        data: 'tingkat'
                    },
                    {
                        data: 'nama_jabatan'
                    },
                    {
                        data: 'detail_sk_bunda'
                    },
                    {
                        data: 'detail_sk_pokja'
                    },
                    {
                        data: 'nama_kecamatan'
                    },
                    {
                        data: 'nama_kelurahan'
                    },
                    {
                        data: 'last_login'
                    },
                ],
            });
        });

        function get_data() {
            $('#dataTable tbody').html('');
            var role = $('#role').val();
            var tingkat = $('#tingkat').val();
            var kecamatan = $('#kecamatan').val();
            var tempat_kelurahan = $('#tempat_kelurahan').val();

            var uri = '?role=' + role + '&tingkat=' + tingkat + '&kecamatan=' + kecamatan + '&kelurahan=' +
                tempat_kelurahan;

            judul = 'Judul';
            var izinkan = 1;

            if (role == '') {
                izinkan = 0;
                swal('WHooops', 'Role harus dipilih', 'error');
            }

            if (izinkan == 1) {
                var nama_role = $('#role option:selected').text();
                var nama_tingkat = $('#tingkat option:selected').text();
                judul = 'Rekap User ' + nama_role;
                judul += ' Tingkat ' + nama_tingkat;
                if (tingkat == 'kota') {
                    judul += ' Wilayah Kota Surabaya'
                } else if (tingkat == 'kecamatan') {
                    var nama_kec = $('#kecamatan option:selected').text();
                    judul += ' Wilayah ' + nama_kec;
                } else if (tingkat == 'kelurahan') {
                    var nama_kec = $('#tempat_kelurahan option:selected').text();
                    judul += ' Wilayah ' + nama_kec;
                }
                dt_table.ajax.url("{{ route('rekap-user-data') }}" + uri).load();
            }

            $('.judul').html(judul);
            judul = $('.judul').html();
        }

        function get_kelurahan(kec_kode) {
            var html = '<option value="all">Semua Kelurahan</option>';
            if (kec_kode == 'all') {
                $('#tempat_kelurahan').html(html);
            } else {
                $.post("{{ route('rekap-umum-get_kelurahan') }}", {
                    kec_kode: kec_kode
                }, function(data) {
                    if (data.length != 0) {
                        $.each(data, function(k, v) {
                            html += '<option value="' + v.kelurahan_id + '">' + v.kelurahan_dispenduk +
                                '</option>';
                        });
                    }
                    $('#tempat_kelurahan').html(html);
                });
            }
        }

        function get_tingkat(tingkat) {
            if (tingkat == 'kota') {
                $('#kecamatannya').hide();
                $('#kelurahannya').hide();
            } else if (tingkat == 'kecamatan') {
                $('#kecamatannya').show();
                $('#kelurahannya').hide();
            } else if (tingkat == 'kelurahan') {
                $('#kecamatannya').show();
                $('#kelurahannya').show();
            }
        }
    </script>
@endsection
