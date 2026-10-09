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
                    <div style="font-weight: bold;font-size: 20pt">Dashboard Rekap Sidia</div>
                </div>
                <div class="col-lg-6 text-right">
                    <div class="wadah_jam"></div>
                </div>
            </div>
        </div>
        <!-- Dynamic Table Full -->
        <div class="block">
            <div class="block-header block-header-default row no-margin">
                <h3 class="block-title">Dashboard Rekap Sidia</h3>
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
                        <div>
                            <label>Range Usia</label>
                            <select class="form-control" name="usia">
                                <option value="1">2 - 3 th 11 bln</option>
                                <option value="2">4 - 6</option>
                            </select>
                        </div>
                        <br>
                        <a href="javascript:void(0)" onclick="ambil_data('kelurahan')" class="btn btn-primary btn_kel">Cari
                            Kelurahan</a>
                        <a href="javascript:void(0)" onclick="ambil_data('kecamatan')" class="btn btn-info btn_all">Cari
                            Kecamatan</a>
                    </div>
                    <div class="col-lg-6">
                        <div class="alert alert-info">
                            <b>Informasi</b>
                            <br>Usia 2-3 th 11 bln
                            <br>Usia 4-6 tahun
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
                                        <th rowspan="2">Usia</th>
                                        <th colspan="5">Belum Sekolah</th>
                                        <th colspan="2">Belum survey ada di-Sidia</th>
                                    </tr>
                                    <tr>
                                        <th>Jumlah</th>
                                        <th>Sudah Disurvey</th>
                                        <th>Persentase</th>
                                        <th>Belum Disurvey</th>
                                        <th>Persentase</th>
                                        <th>Jumlah</th>
                                        <th>Persentase</th>
                                    </tr>
                                </thead>
                            </table>
                            <div style="margin-top: 50px"></div>
                            <table id="tabel_rekap" class=""
                                style="width: 100%;border-collapse: collapse;border-color: #000" border="1">
                                <thead>
                                    <tr>
                                        <th rowspan="2">No</th>
                                        <th rowspan="2">Kecamatan</th>
                                        <th rowspan="2">Kelurahan</th>
                                        <th colspan="3">Belum Sekolah</th>
                                        <th colspan="4">Belum Survey Berada di-Sidia</th>
                                    </tr>
                                    <tr>
                                        <th>Jumlah</th>
                                        <th>Sudah Disurvey</th>
                                        <th>Belum Disurvey</th>
                                        <th>Jumlah Lembaga</th>
                                        <th>Jumlah Siswa Aktif</th>
                                        <th>Jumlah Ditemukan</th>
                                        <th>Persentases</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                            {{-- <div style="margin-top: 20px">
                                <b>Catatan :</b><br>
                                <a href="javascript:void(0)" class="badge badge-success p-5">Hijau</a> Ketika Jumlah selisih
                                <=0<br>
                                    <a href="javascript:void(0)" class="badge badge-warning p-5">Kuning</a> Ketika Jumlah
                                    selisih <=10<br>
                                        <a href="javascript:void(0)" class="badge badge-danger p-5">Merah</a> Ketika Jumlah
                                        selisih >10<br>
                            </div> --}}
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
        var usia = $('select[name=usia]').val();

        $(document).ready(function() {
            $('#sidebar-rekap_sidia').addClass('active');

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
            usia = $('select[name=usia]').val();

            tabel_rekap = $('#tabel_rekap').DataTable({
                ajax: {
                    url: '{{ route('dinas-rekap_sidia', ['halaman' => 'get_data']) }}',
                    type: 'post',
                    data: function(d) {
                        d.kecamatan = kecamatan;
                        d.kelurahan = kelurahan;
                        d.kecamatan_kode = kecamatan_kode;
                        d.kelurahan_kode = kelurahan_kode;
                        d.usia = usia;
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
                    name: 'kecamatan'
                }, {
                    data: 'kelurahan',
                    name: 'kelurahan',
                    defaultContent: '-' // <-- SOLUSI: Mencegah error tn/4 jika data kelurahan tidak ada
                }, {
                    data: 'jumlah_tidak',
                    name: 'jumlah_tidak'
                }, {
                    data: 'jumlah_sudah',
                    name: 'jumlah_sudah'
                }, {
                    data: 'jumlah_belum',
                    name: 'jumlah_belum'
                }, {
                    data: 'jumlah_lembaga_sidia',
                    name: 'jumlah_lembaga_sidia'
                }, {
                    data: 'jumlah_siswa_sidia',
                    name: 'jumlah_siswa_sidia'
                }, {
                    data: 'jumlah_belum_sidia',
                    name: 'jumlah_belum_sidia'
                }, {
                    data: 'persen',
                    name: 'persen'
                }, ]
            });

            rekap_besar = $('#rekap_besar').DataTable({
                ajax: {
                    url: '{{ route('dinas-rekap_sidia', ['halaman' => 'get_rekap_besar']) }}',
                    type: 'post',
                    data: function(d) {
                        d.kecamatan = kecamatan;
                        d.kelurahan = kelurahan;
                        d.kecamatan_kode = kecamatan_kode;
                        d.kelurahan_kode = kelurahan_kode;
                        d.usia = usia;
                    }
                },
                columns: [{
                    data: 'usia',
                    name: 'usia'
                }, {
                    data: 'jumlah_belum_bersekolah',
                    name: 'jumlah_belum_bersekolah'
                }, {
                    data: 'jumlah_survey',
                    name: 'jumlah_survey'
                }, {
                    data: 'persen_sudah',
                    name: 'persen_sudah'
                }, {
                    data: 'jumlah_belum_survey',
                    name: 'jumlah_belum_survey'
                }, {
                    data: 'persen_belum',
                    name: 'persen_belum'
                }, {
                    data: 'jumlah_belum_survey_sidia',
                    name: 'jumlah_belum_survey_sidia'
                }, {
                    data: 'persen_belum_sidia',
                    name: 'persen_belum_sidia'
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
            usia = $('select[name=usia]').val();
            pencarian = cari;

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
            $.post('{{ route('dinas-rekap_sidia', ['halaman' => 'modal_penduduk']) }}', data, function(data) {
                $('.modal-page').html(data.content);
            }).fail(function() {
                swal('Whooops', '500 - Internal server error', 'error');
            });
        }

        function modal_sidia(param) {
            var data = {
                param: param,
                jenis: 'sidia'
            };
            $.post('{{ route('dinas-rekap_sidia', ['halaman' => 'modal_penduduk']) }}', data, function(data) {
                $('.modal-page').html(data.content);
            }).fail(function() {
                swal('Whooops', '500 - Internal server error', 'error');
            });
        }
    </script>
@endsection
