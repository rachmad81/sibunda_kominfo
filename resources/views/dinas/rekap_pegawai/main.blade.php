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

    {{-- STYLE KOLOM --}}
    <style>
        .kolom_lembaga {
            padding: 5px;
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
                    <div style="font-weight: bold;font-size: 20pt">Dashboard Rekap Pegawai</div>
                </div>
                <div class="col-lg-6 text-right">
                    <div class="wadah_jam"></div>
                </div>
            </div>
        </div>
        <!-- Dynamic Table Full -->
        <div class="block">
            <div class="block-header block-header-default row no-margin">
                <h3 class="block-title judul">Dashboard Rekap Pegawai</h3>
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
                            <label>Jenjang</label>
                            <div>
                                <label><input type="checkbox" name="jenjang[]" value="All" onclick="pilih_jenjang('All')"
                                        checked>Semua</label>
                                @php
                                    $jenjangnya = ['TPA', 'SPS', 'KB', 'TK', 'RA'];
                                @endphp
                                @foreach ($jenjangnya as $j)
                                    <label><input type="checkbox" name="jenjang[]" value="{{ $j }}"
                                            onclick="pilih_jenjang()" checked>{{ $j }}</label>
                                @endforeach
                            </div>
                        </div>
                        <br>
                        <a href="javascript:void(0)" onclick="cari_data()" class="btn btn-primary btn_kel">Cari</a>
                    </div>
                    <div class="col-lg-6">
                        <div class="alert alert-info">
                            <b>Informasi</b>
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
                                        <th>No</th>
                                        <th>Kecamatan</th>
                                        <th>Kelurahan</th>
                                        <th>Jumlah</th>
                                        <th>Jumlah PNS</th>
                                        <th>Jumlah GTY</th>
                                        <th>Jumlah Status Lain</th>
                                        <th>Jumlah Lulusan SMA</th>
                                        <th>Jumlah Lulusan Diploma</th>
                                        <th>Jumlah Lulusan S1</th>
                                        <th>Jumlah Lulusan S2</th>
                                        <th>Jumlah Lulusan Lain</th>
                                        <th>Jumlah Laki</th>
                                        <th>Jumlah Perempuan</th>
                                        <th>Jumlah Usia < 20</th>
                                        <th>Jumlah Usia 20 - 30</th>
                                        <th>Jumlah Usia 31 - 40</th>
                                        <th>Jumlah Usia 41 - 50</th>
                                        <th>Jumlah Usia > 50</th>
                                        <th>Jumlah RPL S1</th>
                                        <th>Jumlah Koding</th>
                                        <th>Jumlah Deep Learning</th>
                                        <th>Jumlah Sekolah Ramah Anak</th>
                                        <th>Jumlah Sertifikasi</th>
                                    </tr>
                                </thead>
                                <tfoot></tfoot>
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
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript">
        var rekap_besar = '';
        var pencarian = 'baris';

        var kec = $('.kecamatan option:selected');
        var kecamatan = kec.val();
        var kecamatan_kode = kec.data('kode');
        var kel = $('.kelurahan option:selected');
        var kelurahan = kel.val();
        var kelurahan_kode = kel.data('kode');
        var jenjang = $('input[name="jenjang[]"]:checked').map(function() {
            return $(this).val();
        }).get();

        $(document).ready(function() {
            $('#sidebar-rekap_satuan').addClass('active');

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
            jenjang = $('input[name="jenjang[]"]:checked').map(function() {
                return $(this).val();
            }).get();

            rekap_besar = $('#rekap_besar').DataTable({
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
                lengthMenu: [
                    [10, 25, 50, -1],
                    [10, 25, 50, "All"]
                ],
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('dinas-rekap-pegawai', ['halaman' => 'get_data']) }}',
                    type: 'post',
                    data: function(d) {
                        d.kecamatan = kecamatan;
                        d.kelurahan = kelurahan;
                        d.kecamatan_kode = kecamatan_kode;
                        d.kelurahan_kode = kelurahan_kode;
                        d.pencarian = pencarian;
                        d.jenjang = jenjang;
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        sortable: false,
                        orderable: false
                    },
                    {
                        data: 'kecamatan',
                        name: 'kecamatan',
                        className: 'text-left kolom_lembaga'
                    },
                    {
                        data: 'kelurahan',
                        name: 'kelurahan',
                        className: 'text-left kolom_lembaga'
                    },
                    {
                        data: 'jumlah_pegawai',
                        name: 'jumlah_pegawai',
                        className: 'text-right kolom_lembaga'
                    },
                    {
                        data: 'jumlah_pns',
                        name: 'jumlah_pns',
                        className: 'text-right kolom_lembaga'
                    },
                    {
                        data: 'jumlah_gty',
                        name: 'jumlah_gty',
                        className: 'text-right kolom_lembaga'
                    },
                    {
                        data: 'jumlah_lain',
                        name: 'jumlah_lain',
                        className: 'text-right kolom_lembaga'
                    },
                    {
                        data: 'jumlah_lulusan_sma',
                        name: 'jumlah_lulusan_sma',
                        className: 'text-right kolom_lembaga'
                    },
                    {
                        data: 'jumlah_lulusan_diploma',
                        name: 'jumlah_lulusan_diploma',
                        className: 'text-right kolom_lembaga'
                    },
                    {
                        data: 'jumlah_lulusan_s1',
                        name: 'jumlah_lulusan_s1',
                        className: 'text-right kolom_lembaga'
                    },
                    {
                        data: 'jumlah_lulusan_s2',
                        name: 'jumlah_lulusan_s2',
                        className: 'text-right kolom_lembaga'
                    },
                    {
                        data: 'jumlah_lulusan_lain',
                        name: 'jumlah_lulusan_lain',
                        className: 'text-right kolom_lembaga'
                    },
                    {
                        data: 'jumlah_laki',
                        name: 'jumlah_laki',
                        className: 'text-right kolom_lembaga'
                    },
                    {
                        data: 'jumlah_perempuan',
                        name: 'jumlah_perempuan',
                        className: 'text-right kolom_lembaga'
                    },
                    {
                        data: 'jumlah_usia_20_kurang',
                        name: 'jumlah_usia_20_kurang',
                        className: 'text-right kolom_lembaga'
                    },
                    {
                        data: 'jumlah_usia_20',
                        name: 'jumlah_usia_20',
                        className: 'text-right kolom_lembaga'
                    },
                    {
                        data: 'jumlah_usia_30',
                        name: 'jumlah_usia_30',
                        className: 'text-right kolom_lembaga'
                    },
                    {
                        data: 'jumlah_usia_40',
                        name: 'jumlah_usia_40',
                        className: 'text-right kolom_lembaga'
                    },
                    {
                        data: 'jumlah_usia_50_lebih',
                        name: 'jumlah_usia_50_lebih',
                        className: 'text-right kolom_lembaga'
                    },
                    {
                        data: 'jumlah_rpl',
                        name: 'jumlah_rpl',
                        className: 'text-right kolom_lembaga'
                    },
                    {
                        data: 'jumlah_koding',
                        name: 'jumlah_koding',
                        className: 'text-right kolom_lembaga'
                    },
                    {
                        data: 'jumlah_deep',
                        name: 'jumlah_deep',
                        className: 'text-right kolom_lembaga'
                    },
                    {
                        data: 'jumlah_sra',
                        name: 'jumlah_sra',
                        className: 'text-right kolom_lembaga'
                    },
                    {
                        data: 'jumlah_sertifikasi',
                        name: 'jumlah_sertifikasi',
                        className: 'text-right kolom_lembaga'
                    },
                ]
            });

            get_kelurahan().done(function() {
                ambil_data('baris');
                ambil_data('total');
            });
        });

        function get_kelurahan() {
            var kec = $('.kecamatan option:selected');
            var kode = kec.data('kode');
            var dukcapil = kec.data('dukcapil');

            var data = {
                kode: kode,
                dukcapil: dukcapil,
            };

            return $.post('{{ route('bunda-dashboard_asw-get_kelurahan') }}', data, function(data) {
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
            jenjang = $('input[name="jenjang[]"]:checked').map(function() {
                return $(this).val();
            }).get();
            pencarian = cari;

            if (pencarian == 'baris') {
                rekap_besar.on('preXhr.dt', function() {
                    // Menghapus elemen TR di dalam tbody secara langsung
                    $('#rekap_besar tbody').empty();
                });
                rekap_besar.ajax.reload();
            } else {
                var data = {
                    kecamatan: kecamatan,
                    kecamatan_kode: kecamatan_kode,
                    kelurahan: kelurahan,
                    kelurahan_kode: kelurahan_kode,
                    pencarian: pencarian,
                    jenjang: jenjang,
                };
                $.post('{{ route('dinas-rekap-pegawai', ['halaman' => 'get_data']) }}', data, function(data) {
                    $('#rekap_besar tfoot').html(data);
                });
            }
        }

        function cari_data() {
            ambil_data('baris');
            ambil_data('total');
        }

        // Jalankan fungsi pertama kali
        updateWaktu();

        // Perbarui setiap 1000 milidetik (1 detik)
        setInterval(updateWaktu, 1000);

        function pilih_jenjang(jenis = '0') {
            if (jenis == 'All') {
                $('input[name="jenjang[]"]').prop('checked', true);
            } else {
                $('input[name="jenjang[]"][value=All]').prop('checked', false);
            }
        }

        function modal(kec = 'All', kel = 'All', kategori) {
            jenjang = $('input[name="jenjang[]"]:checked').map(function() {
                return $(this).val();
            }).get();
            if (kec == 'All') {
                kec = $('.kecamatan option:selected');
                kecamatan = kec.val();
                kec = kec.data('kode');
            }
            if (kel == 'All') {
                kel = $('.kelurahan option:selected');
                kelurahan = kel.val();
                kel = kel.data('kode');
            }

            var data = {
                kecamatan_kode: kec,
                kelurahan_kode: kel,
                jenjang: jenjang,
                kategori: kategori,
            }

            $.ajax({
                url: '{{ route('dinas-rekap-pegawai', ['halaman' => 'modal']) }}',
                type: 'post',
                data: data,
                success: function(data) {
                    $('.modal-page').html(data);
                }
            })
        }
    </script>
@endsection
