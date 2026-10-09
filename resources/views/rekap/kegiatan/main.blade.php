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
        <h2 class="content-heading" style="padding-top: 0px">Rekap Kegiatan</h2>
        <!-- Dynamic Table Full -->
        <div class="block">
            <div class="block-header block-header-default row no-margin">
                <h3 class="block-title">Rekap Kegiatan</h3>
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
                                    <option value="opd">OPD</option>
                                    <option value="k3_paud">K3 PAUD</option>
                                    <option value="paud_sd">Forum Komunikasi PAUD SD</option>
                                    <option value="mitra">Mitra</option>
                                    @if ($tingkat->count() != 0)
                                        @foreach ($tingkat as $t)
                                            <option value="{{ $t->tingkat }}">Tingkat {{ $t->tingkat }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>

                        <div class="col-md-2" id="opdnya">
                            <div class="form-group row">
                                <label class="label-control">OPD</label>
                                <select class="form-control" id="opd">
                                    <option value="all">Semua OPD</option>
                                    @if ($opd->count() != 0)
                                        @foreach ($opd as $op)
                                            <option value="{{ $op->username }}">{{ $op->nama }}</option>
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
                        {{-- <a href="javascript:void(0)" class="btn btn-success" onclick="get_export()"><i
                                class="fa fa-search"></i> Export</a> --}}
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
                                <th>{{ str_replace('_', ' ', 'nama_program') }},</th>
                                <th>{{ str_replace('_', ' ', 'nama_kegiatan') }},</th>
                                <th>{{ str_replace('_', ' ', 'bidang') }},</th>
                                <th>{{ str_replace('_', ' ', 'tingkat') }},</th>
                                <th>{{ str_replace('_', ' ', 'tempat_kegiatan') }},</th>
                                <th>{{ str_replace('_', ' ', 'tgl_kegiatan') }},</th>
                                <th>{{ str_replace('_', ' ', 'waktu_kegiatan') }},</th>
                                <th>{{ str_replace('_', ' ', 'bentuk_keg') }},</th>
                                <th>{{ str_replace('_', ' ', 'materi') }},</th>
                                <th>{{ str_replace('_', ' ', 'narsum') }},</th>
                                <th>{{ str_replace('_', ' ', 'sumber_dana') }},</th>
                                <th>{{ str_replace('_', ' ', 'foto') }},</th>
                                <th>{{ str_replace('_', ' ', 'url_video') }},</th>
                                <th>{{ str_replace('_', ' ', 'sasaran_kegiatan') }},</th>
                                <th>{{ str_replace('_', ' ', 'tujuan_kegiatan') }},</th>
                                <th>{{ str_replace('_', ' ', 'hasil_kegiatan') }},</th>
                                <th>{{ str_replace('_', ' ', 'target_out_kegiatan') }},</th>
                                <th>{{ str_replace('_', ' ', 'kak') }}</th>
                                <th>{{ str_replace('_', ' ', 'notulen') }}</th>
                                <th>{{ str_replace('_', ' ', 'absensi') }}</th>
                                <th>{{ str_replace('_', ' ', 'foto_geo') }}</th>
                                <th>{{ str_replace('_', ' ', 'undangan') }}</th>

                                <th>{{ str_replace('_', ' ', 'waktu_program') }},</th>
                                <th>{{ str_replace('_', ' ', 'sasaran_program') }},</th>
                                <th>{{ str_replace('_', ' ', 'tujuan_program') }},</th>
                                <th>{{ str_replace('_', ' ', 'tahun_program') }},</th>
                                <th>{{ str_replace('_', ' ', 'jumlah_sasaran_program') }},</th>

                                <th>{{ str_replace('_', ' ', 'nama') }}</th>
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

        var tahun = $('#tahun').val();
        var tingkat = $('#tingkat').val();
        var bidang = $('#bidang').val();
        var opd = $('#opd').val();
        var kecamatan = $('#kecamatan').val();
        var kelurahan = $('#tempat_kelurahan').val();

        $(document).ready(function() {
            $("#sidebar-rekap").addClass("active");
            $('#kecamatannya').hide();
            $('#kelurahannya').hide();
            $('#opdnya').hide();

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
                    url: '{{ route('rekap-kegiatan-data') }}',
                    type: 'post',
                    data: function(d) {
                        d.tingkat = tingkat;
                        d.tahun = tahun;
                        d.bidang = bidang;
                        d.kecamatan = kecamatan;
                        d.kelurahan = kelurahan;
                        d.opd = opd;
                    },
                },
                lengthMenu: [
                    [10, 25, 50, -1],
                    [10, 25, 50, "All"]
                ],
                iDisplayLength: 25,
                columns: [{
                        data: 'DT_RowIndex',
                        searchable: false,
                        sortable: false
                    },
                    {
                        data: 'nama_program',
                        name: 'p.nama_kegiatan'
                    },
                    {
                        data: 'nama_kegiatan',
                        name: 'k.nama_kegiatan'
                    },
                    {
                        data: 'bidang',
                        name: 'p.bidang'
                    },
                    {
                        data: 'tingkat',
                        name: 'p.tingkat'
                    },
                    {
                        data: 'tempat_kegiatan',
                        name: 'k.tempat_kegiatan'
                    },
                    {
                        data: 'tgl_kegiatan',
                        name: 'k.tgl_kegiatan'
                    },
                    {
                        data: 'waktu_kegiatan',
                        name: 'k.waktu_kegiatan'
                    },
                    {
                        data: 'bentuk_keg',
                        name: 'k.bentuk_keg'
                    },
                    {
                        data: 'materi',
                        name: 'k.materi'
                    },
                    {
                        data: 'narsum',
                        name: 'k.narsum'
                    },
                    {
                        data: 'sumber_dana',
                        name: 'k.sumber_dana'
                    },
                    {
                        data: 'foto',
                        name: 'k.foto'
                    },
                    {
                        data: 'url_video',
                        name: 'k.url_video'
                    },
                    {
                        data: 'sasaran_kegiatan',
                        name: 'k.sasaran_kegiatan'
                    },
                    {
                        data: 'tujuan_kegiatan',
                        name: 'k.tujuan_kegiatan'
                    },
                    {
                        data: 'hasil_kegiatan',
                        name: 'k.hasil_kegiatan'
                    },
                    {
                        data: 'target_out_kegiatan',
                        name: 'k.target_out_kegiatan'
                    },
                    {
                        data: 'kak',
                        name: 'k.file_pendukung',
                    },
                    {
                        data: 'notulen',
                        name: 'k.file_pendukung',
                    },
                    {
                        data: 'absensi',
                        name: 'k.file_pendukung',
                    },
                    {
                        data: 'foto_geo',
                        name: 'k.file_pendukung',
                    },
                    {
                        data: 'undangan',
                        name: 'k.file_pendukung',
                    },


                    {
                        data: 'waktu_program',
                        name: 'p.waktu'
                    },
                    {
                        data: 'sasaran_program',
                        name: 'p.sasaran'
                    },
                    {
                        data: 'tujuan_program',
                        name: 'p.tujuan'
                    },
                    {
                        data: 'tahun_program',
                        name: 'p.tahun'
                    },
                    {
                        data: 'jumlah_sasaran_program',
                        name: 'p.jumlah_sasaran'
                    },


                    {
                        data: 'nama',
                        name: 'u.nama'
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

            tahun = $('#tahun').val();
            tingkat = $('#tingkat').val();
            bidang = $('#bidang').val();
            opd = $('#opd').val();
            kecamatan = $('#kecamatan').val();
            kelurahan = $('#tempat_kelurahan').val();

            judul = 'Judul';
            var izinkan = 1;

            if (tahun == '') {
                izinkan = 0;
                swal('WHooops', 'Tahun harus dipilih', 'error');
            } else {}

            // dt_table.row($(this).parents('tr')).remove();

            if (izinkan == 1) {
                var uri = '?tingkat=' + tingkat + '&tahun=' + tahun + '&bidang=' + bidang + '&kecamatan=' + kecamatan +
                    '&kelurahan=' + kelurahan + '&opd=' + opd;
                var nama_tingkat = $('#tingkat option:selected').text();
                var nama_bidang = $('#bidang option:selected').text();
                var nama_opd = $('#opd option:selected').text();
                judul = 'Rekap Kegiatan Program Tahun ' + tahun;
                judul += ' ' + nama_tingkat;
                judul += ' ' + nama_bidang;
                if (tingkat == 'opd') {
                    judul += ' Wilayah ' + nama_opd;
                } else if (tingkat == 'kota') {
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

                dt_table.ajax.reload();
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
            if (tingkat == 'opd') {
                $('#opdnya').show();
                $('#kecamatannya').hide();
                $('#kelurahannya').hide();
            } else if (tingkat == 'kecamatan') {
                $('#opdnya').hide();
                $('#kecamatannya').show();
                $('#kelurahannya').hide();
            } else if (tingkat == 'kelurahan') {
                $('#opdnya').hide();
                $('#kecamatannya').show();
                $('#kelurahannya').show();
            } else {
                $('#opdnya').hide();
                $('#kecamatannya').hide();
                $('#kelurahannya').hide();
            }
        }

        function get_export() {
            var tahun = $('#tahun').val();
            var tingkat = $('#tingkat').val();
            var bidang = $('#bidang').val();
            var opd = $('#opd').val();
            var kecamatan = $('#kecamatan').val();
            var kelurahan = $('#tempat_kelurahan').val();

            if (tahun == '') {
                swal('Whooops', 'Tahun harus dipilih', 'error')
            } else {
                var data = {
                    'tingkat': tingkat,
                    'tahun': tahun,
                    'bidang': bidang,
                    'kecamatan': kecamatan,
                    'kelurahan': kelurahan,
                    'opd': opd,
                };
                $.post('{{ route('rekap-kegiatan-export') }}', data, function(data) {

                })
            }
        }
    </script>
@endsection
