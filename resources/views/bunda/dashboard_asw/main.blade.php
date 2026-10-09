@extends('layout.index')


@section('additional_styles')
    <style>
        tr>th,
        td {
            text-align: center;
        }

        .block-primary {
            border: 1.5px solid #21a8d1;
        }

        .block-primary>.block-header {
            background: #21a8d1;
        }

        .block-success {
            border: 1.5px solid #36d66c;
        }

        .block-success>.block-header {
            background: #36d66c;
        }

        .block-warning {
            border: 1.5px solid #f1e14c;
        }

        .block-warning>.block-header {
            background: #f1e14c;
        }

        .fix_header {
            background: #fff;
            padding: 15px;
            margin-bottom: 10px;
        }

        .fix_header.sticky-fixed {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 9999;
            /* Agar selalu di atas konten lain */
            background-color: #ffffff;
            /* Beri background agar tidak transparan */
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            /* Opsional: bayangan tipis */
            transition: all 0.3s ease;
        }

        table th {
            border-top: 1px solid #000;
            /* Sesuaikan warna border tabelmu */
        }

        table td {
            border-top: 1px solid #000;
            /* Sesuaikan warna border tabelmu */
        }
    </style>
@endsection
@section('sidebar')
    @if (Auth::getUser()->role == 'bunda')
        @include('bunda/layout/sidebar')
    @elseif(Auth::getUser()->role == 'dinas')
        @include('dinas/layout/sidebar')
    @elseif(Auth::getUser()->role == 'opd')
        @include('opd/layout/sidebar')
    @else
        @include('opd/layout/sidebar')
    @endif
@endsection

@section('content')
    <div class="content" id="app">
        <div class="fix_header">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div style="font-weight: bold;font-size: 20pt">Dashboard ASW</div>
                </div>
                <div class="col-lg-6 text-right">
                    <div class="wadah_jam"></div>
                </div>
            </div>
        </div>
        <!-- Dynamic Table Full -->
        <div class="block">
            <div class="block-header block-header-default row no-margin">
                <h3 class="block-title">Dashboard ASW</h3>
            </div>
            <div class="block-content block-content-full">
                <div class="row">
                    <div class="col-lg-6">
                        @if (in_array(Auth::getUser()->role, ['dinas', 'opd']) ||
                                (Auth::getUser()->role == 'bunda' && in_array(Auth::getUser()->tingkat, ['kota'])))
                            <div>
                                <label>Kecamatan</label>
                                <select class="form-control kecamatan" onchange="get_kelurahan()">
                                    <option data-kode='All' data-dukcapil="semua" value="All">..:: Semua ::..</option>
                                    @foreach ($kecamatan as $kec)
                                        <option data-kode="{{ $kec->kecamatan_kode }}"
                                            data-dukcapil="{{ $kec->kecamatan_dispenduk }}">{{ $kec->kecamatan_dispenduk }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @elseif(Auth::getUser()->role == 'bunda' && in_array(Auth::getUser()->tingkat, ['kecamatan']))
                            <div>
                                <label>Kecamatan</label>
                                <select class="form-control kecamatan" disabled onchange="get_kelurahan()">
                                    <option data-kode='All' data-dukcapil="semua" value="All">..:: Semua ::..</option>
                                    @foreach ($kecamatan as $kec)
                                        <option @if ($kec->kecamatan_id == Auth::getUser()->id_kecamatan) selected @endif
                                            data-kode="{{ $kec->kecamatan_kode }}"
                                            data-dukcapil="{{ $kec->kecamatan_dispenduk }}">{{ $kec->kecamatan_dispenduk }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @elseif(Auth::getUser()->role == 'bunda' && in_array(Auth::getUser()->tingkat, ['kelurahan']))
                            <div>
                                <label>Kecamatan</label>
                                <select class="form-control kecamatan" disabled onchange="get_kelurahan()">
                                    <option data-kode='All' data-dukcapil="semua" value="All">..:: Semua ::..</option>
                                    @foreach ($kecamatan as $kec)
                                        <option @if ($kec->kecamatan_kode == Auth::getUser()->kelurahan->kecamatan_kode) selected @endif
                                            data-kode="{{ $kec->kecamatan_kode }}"
                                            data-dukcapil="{{ $kec->kecamatan_dispenduk }}">{{ $kec->kecamatan_dispenduk }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                        <div>
                            <label>Kelurahan</label>
                            <select class="form-control kelurahan">
                                <option value="All">..:: Semua ::..</option>
                            </select>
                        </div>
                        <br>
                        <a href="javascript:void(0)" onclick="ambil_data()" class="btn btn-primary">Cari</a>
                    </div>
                    <div class="col-lg-6">
                        <div>
                            <label for="">Pencarian data berdasarkan NIK</label>
                            <div>
                                <input type="text" name="nik" class="form-control" placeholder="35781xxxxxxxxxxx01">
                            </div>
                        </div>
                        <div style="margin-top: 10px">
                            <a href="javascript:void(0)" onclick="detail_informasi()" class="btn btn-primary">Cari</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @php
            $kategori = [
                'cekin_warga',
                'sibunda',
                'tanpa_intervensi',
                'dinsos',
                'rutilahu',
                'dp3a',
                'posyandu',
                'dispusip',
                'soth',
                'phbs',
            ];
            $judul_kategori = [
                'CEKIN WARGA',
                'SIBUNDA',
                'PENDUDUK TIDAK ADA INTERVENSI KESEHATAN',
                'DINAS SOSIAL',
                'RUTILAHU',
                'DP3A',
                'POSYANDU',
                'DISPUSIP',
                'SOTH (Sekolah Orang Tua Hebat)',
                'PHBS (Perilaku Hidup Bersih dan Sehat)',
            ];
        @endphp

        @for ($i = 0; $i < count($kategori); $i++)
            <div class="block">
                <div class="block-header block-header-default row no-margin flex">
                    <h3 class="block-title">{{ $judul_kategori[$i] }}</h3>
                    <a href="javascript:void(0)" class="btn btn-sm btn-success"
                        onclick="get_data('{{ $kategori[$i] }}')">Reload</a>
                </div>
                <div class="block-content block-content-full">
                    <div class="row">
                        <div class="col-lg-12 {{ $kategori[$i] }}" style="overflow: auto">

                        </div>
                    </div>
                </div>
            </div>
        @endfor
    </div>
    <div class="modal-page"></div>
@endsection

@section('additional_scripts')
    {{-- <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/data.js"></script>
    <script src="https://code.highcharts.com/modules/drilldown.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>
    <script src="https://code.highcharts.com/themes/adaptive.js"></script> --}}
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
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/data.js"></script>
    <script src="https://code.highcharts.com/modules/drilldown.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>
    <script src="https://code.highcharts.com/themes/adaptive.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#sidebar-dashboard_asw').addClass('active');

            var $header = $('.fix_header');

            // Ambil posisi awal .fix_header dari bagian atas dokumen
            var headerOffset = $header.offset().top;

            $(window).scroll(function() {
                // Cek apakah posisi scroll layar sudah melewatinya
                if ($(window).scrollTop() > headerOffset) {
                    $header.addClass('sticky-fixed');
                } else {
                    $header.removeClass('sticky-fixed');
                }
            });

            get_kelurahan();
        });

        function get_kelurahan() {
            var kec = $('.kecamatan option:selected');
            var kode = kec.data('kode');
            var dukcapil = kec.data('dukcapil');

            var data = {
                kode: kode,
                dukcapil: dukcapil,
            };

            $.post('{{ route('bunda-dashboard_asw-get_kelurahan') }}', data, function(data) {
                if (data.code == 200) {
                    var html = '';
                    @if (Auth::getUser()->tingkat == 'kelurahan')
                        if (data.kelurahan.length > 0) {
                            $.each(data.kelurahan, function(k, v) {
                                html += '<option data-kode="' + v.kelurahan_kode + '">' + v
                                    .kelurahan_dispenduk + '</option>';
                            });
                        }
                        $('.kelurahan').attr('disabled', 'disabled');
                    @else
                        html += '<option value="All">..:: Semua ::..</option>';
                        if (data.kelurahan.length > 0) {
                            $.each(data.kelurahan, function(k, v) {
                                html += '<option data-kode="' + v.kelurahan_kode + '">' + v
                                    .kelurahan_dispenduk + '</option>';
                            });
                        }
                        $('.kelurahan').removeAttr('disabled');
                    @endif
                    $('.kelurahan').html(html);
                } else if (data.code == '250') {
                    $('.kelurahan').html('<option value="All">..:: Semua ::..</option>');
                }
            });
        }

        function ambil_data() {
            @for ($i = 0; $i < count($kategori); $i++)
                get_data('{{ $kategori[$i] }}');
            @endfor
        }

        function get_data(jenis) {
            var kec = $('.kecamatan option:selected');
            var kecamatan = kec.val();
            var kecamatan_kode = kec.data('kode');
            var kel = $('.kelurahan option:selected');
            var kelurahan = kel.val();
            var kelurahan_kode = kel.data('kode');

            var data = {
                kecamatan: kecamatan,
                kelurahan: kelurahan,
                kecamatan_kode: kecamatan_kode,
                kelurahan_kode: kelurahan_kode,
                jenis: jenis,
            };

            $('.' + jenis).html(
                '<div style="width: 100%; margin: auto"><center><img src="{{ asset('load_putar.gif') }}" style="width: 20%"></center></div>'
            );
            $.post('{{ route('bunda-dashboard_asw-get_data') }}', data, function(data) {
                $('.' + jenis).html(data);
            }).fail(function() {
                $('.' + jenis).html('<center><h5>Error 500- Internal server error</h5></center>');
            });
        }

        function modal(menu, param) {
            var kec = $('.kecamatan option:selected');
            var kecamatan = kec.val();
            var kecamatan_kode = kec.data('kode');
            var kel = $('.kelurahan option:selected');
            var kelurahan = kel.val();
            var kelurahan_kode = kel.data('kode');


            var data = {
                kecamatan: kecamatan,
                kelurahan: kelurahan,
                kecamatan_kode: kecamatan_kode,
                kelurahan_kode: kelurahan_kode,
                menu: menu,
                param: param,
            };

            if (menu == 'dp3a') {
                var tahun = $('.tahun_pengaduan').val();
                data.tahun = tahun;
            } else if (menu == 'posyandu_timbang' || menu == 'posyandu_imunisasi') {
                var tahun = $('.tahun_posyandu').val();
                data.tahun = tahun;
            } else if (menu == 'dispusip') {
                var tahun = $('.tahun_kunjungan').val();
                var bulan = $('.bulan_kunjungan').val();
                data.tahun = tahun + '-' + bulan;
            }

            $.post('{{ route('bunda-dashboard_asw-modal') }}', data, function(data) {
                $('.modal-page').html(data);
            }).fail(function() {
                swal('Whooops', '500 - Internal server error', 'error');
            });
        }

        function detail_informasi() {
            nik = $('input[name=nik]').val();

            window.open('{{ url('dashboard_asw/detail_informasi/') }}/' + nik);
        }

        function updateWaktu() {
            const sekarang = new Date();

            // 1. Format Tanggal Indonesia (contoh: Selasa, 4 Agustus 2026)
            const opsiTanggal = {
                weekday: 'long',
                day: 'numeric',
                month: 'long',
                year: 'numeric'
            };
            const tanggalStr = sekarang.toLocaleDateString('id-ID', opsiTanggal);

            // 2. Format Jam, Menit, Detik (contoh: 10:45:09 WIB)
            const jam = String(sekarang.getHours()).padStart(2, '0');
            const menit = String(sekarang.getMinutes()).padStart(2, '0');
            const detik = String(sekarang.getSeconds()).padStart(2, '0');
            const jamStr = `${jam}:${menit}:${detik} WIB`;

            // 3. Tampilkan ke HTML
            $('.wadah_jam').html(tanggalStr + ' ' + jamStr);
        }

        // Jalankan fungsi pertama kali
        updateWaktu();

        // Perbarui setiap 1000 milidetik (1 detik)
        setInterval(updateWaktu, 1000);
    </script>
@endsection
