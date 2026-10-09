@extends('layout.index')

@section('additional_styles')
    <title class="judul"></title>
@endsection

@section('additional_styles')
    <style>
        tr td {
            text-align: center;
        }

        .modal {
            overflow: auto;
        }
    </style>
@endsection
@section('sidebar')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">
    @if (request()->user()->role == 'dinas')
        @include('dinas/layout/sidebar')
    @elseif (request()->user()->role == 'bunda')
        @include('bunda/layout/sidebar')
    @endif
@endsection

@section('content')
    <div class="content" id="app">
        <h2 class="content-heading" style="padding-top: 0px">Rekap Satuan PAUD</h2>
        <!-- Dynamic Table Full -->
        <div class="block">
            <div class="block-header block-header-default row no-margin">
                <h3 class="block-title">Rekap Satuan PAUD</h3>
            </div>
            <div class="block-content block-content-full">
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group row">
                                <label class="label-control">Jenjang</label>
                                <select class="form-control" id="jenjang">
                                    <option value="all">Semua Jenjang</option>
                                    <option value="TPA">TPA</option>
                                    <option value="KB">KB</option>
                                    <option value="TK">TK</option>
                                    <option value="RA">RA</option>
                                    <option value="SPS">PPT</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label>Kecamatan</label>
                            <select id="kecamatan" name="kecamatan" onchange="get_kelurahan();"
                                @if (in_array(Auth::getUser()->tingkat, ['kecamatan', 'kelurahan'])) disabled @endif class="form-control">
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label>Kelurahan</label>
                            <select id="kelurahan" name="kelurahan" @if (in_array(Auth::getUser()->tingkat, ['kelurahan'])) disabled @endif
                                class="form-control">
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label>Bulan</label>
                            <select id="bulan" name="bulan" class="form-control">
                                @php
                                    $bulans = [
                                        'Januari',
                                        'Februari',
                                        'Maret',
                                        'April',
                                        'Mei',
                                        'Juni',
                                        'Juli',
                                        'Agustus',
                                        'September',
                                        'Oktober',
                                        'November',
                                        'Desember',
                                    ];
                                @endphp
                                @for ($bulan = 1; $bulan <= now()->month; $bulan++)
                                    <option value="{{ $bulan }}" selected>{{ $bulans[$bulan - 1] }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row" style="margin-bottom: 15px;">
                    <div class="col-md-12">
                        <a href="javascript:void(0)" class="btn btn-primary" onclick="get_data('lembaga')">Lembaga PAUD</a>
                        <a href="javascript:void(0)" class="btn btn-primary" onclick="get_data('monitoring_lembaga')">Rekap
                            Monitoring PAUD</a>
                        <a href="javascript:void(0)" class="btn btn-primary" onclick="get_data('siswa')">Siswa PAUD</a>
                        <a href="javascript:void(0)" class="btn btn-primary" onclick="get_data('gtk')">Guru Pendidik</a>
                        <a href="javascript:void(0)" class="btn btn-primary" onclick="get_data('sarpras')">Sarana
                            Prasarana</a>
                        <a href="javascript:void(0)" class="btn btn-primary" onclick="get_data('keuangan')">Keuangan</a>
                    </div>
                    <div class="col-md-12">
                        <a href="javascript:void(0)" class="btn btn-danger" onclick="get_data('lembaga_sd')">Lembaga
                            SD/MI</a>
                        <a href="javascript:void(0)" class="btn btn-danger"
                            onclick="get_data('monitoring_lembaga_sd')">Rekap Monitoring SD/MI</a>
                        <a href="javascript:void(0)" class="btn btn-danger" onclick="get_data('siswa_sd')">Siswa (Kelas 1
                            dan 2) SD/MI</a>
                    </div>
                </div>
                @include('layout.alert')
                <div>
                    <center>
                        <h1 class="judul"></h1>
                    </center>
                </div>
                <div style="overflow: auto" class="tempat_rekap">

                </div>
                <div class="loading">
                    Sedang menghitung data.......
                </div>
                <div class="modal-page"></div>
            </div>
        </div>
        @include('dinas.user.modal')
    </div>
@endsection

@section('additional_scripts')
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/rowgroup/1.2.0/js/dataTables.rowGroup.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('.loading').hide();
            $('.tempat_rekap').show();
            get_kecamatan();
            get_kelurahan();
        });

        let kecamatanSelect = document.getElementById("kecamatan");
        let kelurahanSelect = document.getElementById("kelurahan");
        let kecamatans = {!! $kecamatans !!};

        function get_kecamatan() {
            kecamatanSelect.innerHTML = "";
            @if (in_array(request()->user()->tingkat, ['kota']))
                kecamatanSelect.innerHTML += `<option value="all" selected>Semua Kecamatan</option>`;
            @endif
            kecamatans.forEach(kecamatan => {
                kecamatanSelect.innerHTML +=
                    `<option value="${kecamatan.kecamatan_kode}">${kecamatan.kecamatan_dispenduk}</option>`;
            });
        }

        function get_kelurahan() {
            kelurahanSelect.innerHTML = "";
            @if (in_array(request()->user()->tingkat, ['kota', 'kecamatan']))
                kelurahanSelect.innerHTML += `<option value="all" selected>Semua Kelurahan</option>`;
            @endif
            if (kecamatanSelect.value != "") {
                kecamatans.forEach(kecamatan => {
                    var kelurahans = JSON.parse(kecamatan.kelurahans);
                    kelurahans.forEach(kelurahan => {
                        if (kecamatan.kecamatan_kode == kecamatanSelect.value) {
                            kelurahanSelect.innerHTML +=
                                `<option value="${kelurahan.kelurahan_kode}">${kelurahan.kelurahan_dispenduk}</option>`;
                        }
                    })
                });
            }
        }

        function get_data(jenis, load = '') {
            $('.loading').show();
            $('.tempat_rekap').hide();

            var jenjang = $('#jenjang').val();
            var kecamatan = $('#kecamatan').val();
            var kelurahan = $('#kelurahan').val();
            var bulan = $('#bulan').val();
            var data = {
                jenis: jenis,
                jenjang: jenjang,
                kecamatan: kecamatan,
                kelurahan: kelurahan,
                bulan: bulan,
                load: load,
            };
            $.post('{{ route('bunda-rekap_profil-get_data') }}', data, function(data) {
                $('.loading').hide();
                $('.tempat_rekap').show();
                $('.tempat_rekap').html(data.content);
                $('#myData').DataTable();
            }).fail(function() {
                $('.loading').hide();
                $('.tempat_rekap').show();
            });
        }

        function detail_data(jenis, param) {
            $('.loading').show();
            $('.tempat_rekap').hide();

            var jenjang = $('#jenjang').val();
            var kecamatan = $('#kecamatan').val();
            var kelurahan = $('#kelurahan').val();
            var bulan = $('#bulan').val();

            var data = {
                jenis: jenis,
                param: param,
                jenjang: jenjang,
                kecamatan: kecamatan,
                kelurahan: kelurahan,
                bulan: bulan,
            };
            if (jenis == 'lembaga' || jenis == 'lembaga_sd') {
                data.jenjang = param[0];
                data.status = param[1];
                data.kolom = param[2];
                data.judul = param[3];
            } else if (jenis == 'keuangan') {
                data.kolom = param[0];
                data.jenis_anggaran = param[1];
            } else if(jenis == 'siswa_sd' || jenis == 'siswa' || jenis == 'gtk'){
                
            }
            $.post('{{ route('bunda-rekap_profil-detail_data') }}', data, function(data) {
                $('.loading').hide();
                $('.tempat_rekap').show();
                $('.modal-page').html(data.content);
                $('#detailData').DataTable({
                    pageLength: 25,
                    lengthMenu: [
                        [10, 25, 50, -1],
                        [10, 25, 50, 'All']
                    ],
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
                });
            }).fail(function() {
                $('.loading').hide();
                $('.tempat_rekap').show();
            });
        }
    </script>
@endsection
