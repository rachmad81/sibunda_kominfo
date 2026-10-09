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
        <h2 class="content-heading" style="padding-top: 0px">Rekap Program</h2>
        <!-- Dynamic Table Full -->
        <div class="block">
            <div class="block-header block-header-default row no-margin">
                <h3 class="block-title">Rekap Program</h3>
            </div>
            <div class="block-content block-content-full">
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group row">
                                <label class="label-control">Tahun Program</label>
                                <select class="form-control" id="tahun">
                                    <option value="">-- Tahun Program --</option>
                                    @if ($tahun->count() != 0)
                                        @foreach ($tahun as $t)
                                            <option value="{{ $t->tahun }}">{{ $t->tahun }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group row">
                                <label class="label-control">Bidang</label>
                                <select class="form-control" id="bidang">
                                    <option value="all">Semua Bidang</option>
                                    @if ($bidang->count() != 0)
                                        @foreach ($bidang as $t)
                                            <option value="{{ $t->bidang }}">{{ $t->bidang }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group row">
                                <label class="label-control">Tingkat</label>
                                <select class="form-control" id="tingkat" onchange="get_tingkat(this.value)">
                                    <option value="all">Semua Tingkat</option>
                                    @if ($tingkat->count() != 0)
                                        @foreach ($tingkat as $t)
                                            <option value="{{ $t->tingkat }}">Tingkat {{ $t->tingkat }}</option>
                                        @endforeach
                                    @endif
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
                <div style="overflow: auto">
                    <table id="dataTable" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th style="width:1%">No</th>
                                <th width="27%">Bidang</th>
                                <th>Program</th>
                                <th>Sasaran</th>
                                <th>Tujuan</th>
                                <th>Pembuat</th>
                                <th>Role</th>
                                <th>Tingkat</th>
                                <th>Kecamatan</th>
                                <th>Kelurahan</th>
                                <th>1</th>
                                <th>2</th>
                                <th>3</th>
                                <th>4</th>
                                <th>5</th>
                                <th>6</th>
                                <th>7</th>
                                <th>8</th>
                                <th>9</th>
                                <th>10</th>
                                <th>11</th>
                                <th>12</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
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
                    url: '{{ route('rekap-program-data') }}',
                    type: 'get',
                },
                lengthMenu: [
                    [10, 25, 50, -1],
                    [10, 25, 50, "All"]
                ],
                iDisplayLength: 25,
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        sortable: false,
                        searchable: false,
                    },
                    {
                        data: 'bidang',
                        name: 'p.bidang'
                    },
                    {
                        data: 'nama_kegiatan',
                        name: 'p.nama_kegiatan'
                    },
                    {
                        data: 'sasaran',
                        name: 'p.sasaran'
                    },
                    {
                        data: 'tujuan',
                        name: 'p.tujuan'
                    },
                    {
                        data: 'pembuat',
                        name: 'u.nama'
                    },
                    {
                        data: 'role',
                        name: 'u.role'
                    },
                    {
                        data: 'tingkat',
                        name: 'p.tingkat'
                    },
                    {
                        data: 'nama_kecamatan',
                        searchable: false,
                        sortable: false,
                    },
                    {
                        data: 'nama_kelurahan',
                        searchable: false,
                        sortable: false,
                    },
                    {
                        data: 'kol_1',
                        searchable: false,
                        sortable: false
                    },
                    {
                        data: 'kol_2',
                        searchable: false,
                        sortable: false
                    },
                    {
                        data: 'kol_3',
                        searchable: false,
                        sortable: false
                    },
                    {
                        data: 'kol_4',
                        searchable: false,
                        sortable: false
                    },
                    {
                        data: 'kol_5',
                        searchable: false,
                        sortable: false
                    },
                    {
                        data: 'kol_6',
                        searchable: false,
                        sortable: false
                    },
                    {
                        data: 'kol_7',
                        searchable: false,
                        sortable: false
                    },
                    {
                        data: 'kol_8',
                        searchable: false,
                        sortable: false
                    },
                    {
                        data: 'kol_9',
                        searchable: false,
                        sortable: false
                    },
                    {
                        data: 'kol_10',
                        searchable: false,
                        sortable: false
                    },
                    {
                        data: 'kol_11',
                        searchable: false,
                        sortable: false
                    },
                    {
                        data: 'kol_12',
                        searchable: false,
                        sortable: false
                    },
                ],
            });

            $('#dataTable').on('length.dt', function() {
                // kosongkan isi tabel (tbody saja)
                $('#dataTable tbody').empty();
            });
        });

        function get_data() {
            $('#dataTable tbody').html('');

            var tahun = $('#tahun').val();
            var tingkat = $('#tingkat').val();
            var bidang = $('#bidang').val();
            var kecamatan = $('#kecamatan').val();
            var kelurahan = $('#tempat_kelurahan').val();

            var uri = '?tingkat=' + tingkat + '&tahun=' + tahun + '&bidang=' + bidang + '&kecamatan=' + kecamatan +
                '&kelurahan=' + kelurahan;

            judul = 'Judul';
            var izinkan = 1;

            if (tahun == '') {
                izinkan = 0;
                swal('WHooops', 'Tahun harus dipilih', 'error');
            } else {}

            if (izinkan == 1) {
                var nama_tingkat = $('#tingkat option:selected').text();
                var nama_bidang = $('#bidang option:selected').text();
                judul = 'Rekap Timeline Program Tahun ' + tahun;
                judul += ' ' + nama_tingkat;
                judul += ' ' + nama_bidang;
                if (tingkat == 'kota') {
                    judul += ' Wilayah Kota Surabaya';
                } else if (tingkat == 'kecamatan') {
                    var nama_kec = $('#kecamatan option:selected').text();
                    judul += ' Wilayah ' + nama_kec;
                } else if (tingkat == 'kelurahan') {
                    var nama_kec = $('#kecamatan option:selected').text();
                    judul += ' Wilayah ' + nama_kec;
                    var nama_kec = $('#tempat_kelurahan option:selected').text();
                    judul += ' Wilayah ' + nama_kec;
                }
                dt_table.clear().draw();
                dt_table.ajax.url("{{ route('rekap-program-data') }}" + uri).load();
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
            if (tingkat == 'kecamatan') {
                $('#kecamatannya').show();
                $('#kelurahannya').hide();
            } else if (tingkat == 'kelurahan') {
                $('#kecamatannya').show();
                $('#kelurahannya').show();
            } else {
                $('#kecamatannya').hide();
                $('#kelurahannya').hide();
            }
        }
    </script>
@endsection
