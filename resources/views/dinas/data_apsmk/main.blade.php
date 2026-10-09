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
                    <div style="font-weight: bold;font-size: 20pt">Dashboard APK, APS, APM</div>
                </div>
                <div class="col-lg-6 text-right">
                    <div class="wadah_jam"></div>
                </div>
            </div>
        </div>
        <!-- Dynamic Table Full -->
        <div class="block">
            <div class="block-header block-header-default row no-margin">
                <h3 class="block-title">Dashboard APK, APS, APM</h3>
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
                        {{-- <div>
                            <label>Range Usia</label>
                            <select class="form-control" name="usia">
                                <option value="1">2 - 3 th 11 bln</option>
                                <option value="2">4 - 6</option>
                            </select>
                        </div> --}}
                        <br>
                        <a href="javascript:void(0)" onclick="ambil_data('kelurahan')" class="btn btn-primary btn_kel">Cari
                            Kelurahan</a>
                        <a href="javascript:void(0)" onclick="ambil_data('kecamatan')" class="btn btn-info btn_all">Cari
                            Kecamatan</a>
                    </div>
                    <div class="col-lg-6">
                        <div class="alert alert-info">
                            <b>Informasi</b><br>
                            Rumus APK, APM, dan APS Berdasarkan Tabel Data<br>
                            <!-- 1. APS -->
                            <p>1. APS (Angka Partisipasi Sekolah)</p>
                            <div class="formula-box">
                                \[
                                APS = \left( \frac{\text{JUMLAH BERSEKOLAH}}{\text{JUMLAH TOTAL}} \right) \times 100\%
                                \]
                            </div>

                            <!-- 2. APM -->
                            <p>2. APM (Angka Partisipasi Murni)</p>
                            <div class="formula-box">
                                \[
                                APM = \left( \frac{\sum \text{JUMLAH BERSEKOLAH (Usia 3–6 Tahun)}}{\sum \text{JUMLAH TOTAL
                                (Usia 3–6 Tahun)}} \right) \times 100\%
                                \]
                            </div>

                            <!-- 3. APK -->
                            <p>3. APK (Angka Partisipasi Kasar)</p>
                            <div class="formula-box">
                                \[
                                APK = \left( \frac{\sum \text{JUMLAH BERSEKOLAH (Keseluruhan / Usia 0–6 Tahun)}}{\sum
                                \text{JUMLAH TOTAL (Kelompok Usia Resmi 3–6 Tahun)}} \right) \times 100\%
                                \]
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div style="overflow: auto">
                            <table class="" style="width: 100%;border-collapse: collapse;border-color: #000"
                                border="1" id="rekap_besar">
                                <thead>
                                    <tr>
                                        <th>Jumlah Bersekolah (0-6 tahun)</th>
                                        <th>Jumlah Total(0-6 tahun)</th>
                                        <th>Jumlah Bersekolah (3-6 tahun)</th>
                                        <th>Jumlah Total (3-6 tahun)</th>
                                        <th>APS</th>
                                        <th>APM</th>
                                        <th>APK</th>
                                    </tr>
                                </thead>
                            </table>
                            <div style="margin-top: 50px"></div>
                            <table id="tabel_rekap" class=""
                                style="width: 100%;border-collapse: collapse;border-color: #000" border="1">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th style="width: 15%">Kecamatan</th>
                                        <th style="width: 15%">Kelurahan</th>
                                        <th>Jumlah Bersekolah (0-6 tahun)</th>
                                        <th>Jumlah Total (0-6 tahun)</th>
                                        <th>Jumlah Bersekolah (3-6 tahun)</th>
                                        <th>Jumlah Total (3-6 tahun)</th>
                                        <th style="width: 10%">APS</th>
                                        <th style="width: 10%">APM</th>
                                        <th style="width: 10%">APK</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
        var tabel_rekap = '';
        var rekap_besar = '';
        var pencarian = 'kelurahan';

        var kec = $('.kecamatan option:selected');
        var kecamatan = kec.val();
        var kecamatan_kode = kec.data('kode');
        var kel = $('.kelurahan option:selected');
        var kelurahan = kel.val();
        var kelurahan_kode = kel.data('kode');
        // var usia = $('select[name=usia]').val();

        $(document).ready(function() {
            $('#sidebar-data_apsmk').addClass('active');

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

            kec = $('.kecamatan option:selected');
            kecamatan = kec.val();
            kecamatan_kode = kec.data('kode');
            kel = $('.kelurahan option:selected');
            kelurahan = kel.val();
            kelurahan_kode = kel.data('kode');
            // usia = $('select[name=usia]').val();

            tabel_rekap = $('#tabel_rekap').DataTable({
                ajax: {
                    url: '{{ route('dinas-data_aps_apm_apk', ['halaman' => 'get_data']) }}',
                    type: 'post',
                    data: function(d) {
                        d.kecamatan = kecamatan;
                        d.kelurahan = kelurahan;
                        d.kecamatan_kode = kecamatan_kode;
                        d.kelurahan_kode = kelurahan_kode;
                        // d.usia = usia;
                        d.pencarian = pencarian;
                    }
                },
                columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    searchable: false,
                    orderable: false
                }, {
                    data: 'kecamatan',
                    name: 'kecamatan',
                    className: 'text-left'
                }, {
                    data: 'kelurahan',
                    name: 'kelurahan',
                    className: 'text-left',
                    defaultContent: '-' // <-- SOLUSI: Mencegah error tn/4 jika data kelurahan tidak ada
                }, {
                    data: 'jumlah_bersekolah',
                    name: 'jumlah_bersekolah',
                    className: 'text-right'
                }, {
                    data: 'total',
                    name: 'total',
                    className: 'text-right'
                }, {
                    data: 'jumlah_bersekolah_paud',
                    name: 'jumlah_bersekolah_paud',
                    className: 'text-right'
                }, {
                    data: 'usia_paud',
                    name: 'usia_paud',
                    className: 'text-right'
                }, {
                    data: 'aps',
                    name: 'aps',
                    className: 'text-right'
                }, {
                    data: 'apm',
                    name: 'apm',
                    className: 'text-right'
                }, {
                    data: 'apk',
                    name: 'apk',
                    className: 'text-right'
                }, ]
            });

            rekap_besar = $('#rekap_besar').DataTable({
                ajax: {
                    url: '{{ route('dinas-data_aps_apm_apk', ['halaman' => 'get_rekap_besar']) }}',
                    type: 'post',
                    data: function(d) {
                        d.kecamatan = kecamatan;
                        d.kelurahan = kelurahan;
                        d.kecamatan_kode = kecamatan_kode;
                        d.kelurahan_kode = kelurahan_kode;
                        // d.usia = usia;
                    }
                },
                columns: [{
                    data: 'jumlah_bersekolah',
                    name: 'jumlah_bersekolah'
                }, {
                    data: 'total',
                    name: 'total'
                }, {
                    data: 'jumlah_bersekolah_paud',
                    name: 'jumlah_bersekolah_paud'
                }, {
                    data: 'usia_paud',
                    name: 'usia_paud'
                }, {
                    data: 'aps',
                    name: 'aps'
                }, {
                    data: 'apm',
                    name: 'apm'
                }, {
                    data: 'apk',
                    name: 'apk'
                }, ]
            });

            get_kelurahan().done(function() {
                ambil_data('kelurahan');
            });
        });

        function get_kelurahan() {
            var kec = $('.kecamatan option:selected');
            var kode = kec.data('kode');
            var dukcapil = kec.data('dukcapil');

            if (kode == 'All') {
                $('.btn_kel').html('Cari Kelurahan');
                $('.btn_all').show();
            } else {
                $('.btn_kel').html('Cari');
                $('.btn_all').hide();
            }

            var data = {
                kode: kode,
                dukcapil: dukcapil,
            };

            return $.post('{{ route('bunda-dashboard_asw-get_kelurahan') }}', data, function(data) {
                if (data.code == 200) {
                    var html = '';
                    @if (Auth::getUser()->tingkat == 'kelurahan') if (data.kelurahan.length > 0) {
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
                        $('.kelurahan').removeAttr('disabled'); @endif
                    $('.kelurahan').html(html);
                } else if (data.code == '250') {
                    $('.kelurahan').html('<option value="All">..:: Semua ::..</option>');
                }
            });
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

        function ambil_data(cari) {
            kec = $('.kecamatan option:selected');
            kecamatan = kec.val();
            kecamatan_kode = kec.data('kode');
            kel = $('.kelurahan option:selected');
            kelurahan = kel.val();
            kelurahan_kode = kel.data('kode');
            // usia = $('select[name=usia]').val();
            pencarian = cari;
            // var batas = 12;
            // if (usia == '2') {
            //     batas = 15;
            // }

            // $('.wadah_batas').html(batas);

            // dt_tabel.clear().draw();
            tabel_rekap.on('preXhr.dt', function() {
                // Menghapus elemen TR di dalam tbody secara langsung
                $('#tabel_rekap tbody').empty();
            });

            rekap_besar.on('preXhr.dt', function() {
                // Menghapus elemen TR di dalam tbody secara langsung
                $('#rekap_besar tbody').empty();
            });

            if (cari == 'kecamatan') {
                tabel_rekap.column(2).visible(false);
            } else {
                tabel_rekap.column(2).visible(true);
            }
            tabel_rekap.ajax.reload();
            rekap_besar.ajax.reload();
        }

        // Jalankan fungsi pertama kali
        updateWaktu();

        // Perbarui setiap 1000 milidetik (1 detik)
        setInterval(updateWaktu, 1000);



        function modal_penduduk(param) {
            var data = {
                param: param,
                jenis: 'penduduk'
            };
            $.post('{{ route('dinas-data_aps_apm_apk', ['halaman' => 'modal_penduduk']) }}', data, function(data) {
                $('.modal-page').html(data.content);
            }).fail(function() {
                swal('Whooops', '500 - Internal server error', 'error');
            });
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
@endsection
