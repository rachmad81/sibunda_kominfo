@extends('layout.index')


@section('additional_styles')
    <style>

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
        <h2 class="content-heading" style="padding-top: 0px">KKA (Kartu Kembang Anak) Dinas Kesehatan</h2>
        <!-- Dynamic Table Full -->
        <div class="block">
            <div class="block-header block-header-default row no-margin">
                <h3 class="block-title">KKA (Kartu Kembang Anak) Dinas Kesehatan</h3>
            </div>
            <div class="block-content block-content-full">
                <div class="row">
                    <div class="col-lg-6" style="border-bottom: 1px solid #000;padding-bottom: 10px;">
                        <h4>Penyaringan data (filter)</h4>
                        <table style="width: 100%;">
                            <tr>
                                <td>Tahun Pengisian</td>
                                <td>
                                    <select class="form-control tahun_monev">
                                        @foreach ($tahun as $t)
                                            <option value="{{ $t }}">{{ $t }}</option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>Bulan Pengisian</td>
                                <td>
                                    @php
                                        $bulan_angka = [
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
                                    <select class="form-control bulan_monev">
                                        @for ($i = 0; $i < count($bulan_angka); $i++)
                                            <option data-bulan="{{ $bulan_angka[$i] }}" value="{{ $i + 1 }}">
                                                {{ $bulan_angka[$i] }}</option>
                                        @endfor
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
                                <td>Status Pengisian</td>
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
                </div>
                <div style="overflow: auto;margin-top: 15px">
                    <div class="row">
                        <div class="col-lg-3 p-10 m-10"
                            style="background: linear-gradient(145deg,#c0fbff,#ffffff);color: #000000;">
                            <div style="width: 100%;text-align: center;">
                                <span style="font-weight: bold;font-size: 15pt">
                                    Jumlah Semua Siswa Aktif (Saat ini)
                                </span>
                            </div>
                            <div style="width: 100%;text-align: center;">
                                <span style="font-size: 30pt" class="wadah_siswa_aktif">50</span>
                            </div>
                        </div>
                        <div class="col-lg-3 p-10 m-10"
                            style="background: linear-gradient(145deg,#d5ffd8,#ffffff);color: #000000;">
                            <div style="width: 100%;text-align: center;">
                                <span style="font-weight: bold;font-size: 15pt">
                                    Jumlah sudah mengisi KKA (sesuai Periode Filter)
                                </span>
                            </div>
                            <div style="width: 100%;text-align: center;">
                                <span style="font-size: 30pt" class="wadah_siswa_aktif_sudah">50</span>
                            </div>
                        </div>
                    </div>
                    <div class="text-center">
                        <h2 id="judul">Judul</h2>
                    </div>
                    <table id="dataTable" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th style="width:1%">No</th>
                                <th width="27%">NIK</th>
                                <th width="27%">Nama Siswa</th>
                                <th width="27%">Tanggal Lahir</th>
                                <th width="27%">NPSN</th>
                                <th width="27%">Nama Sekolah</th>
                                <th width="27%">Kecamatan Sekolah</th>
                                <th width="27%">Kelurahan Sekolah</th>
                                <th width="27%">Status Siswa saat ini</th>
                                <th width="27%">Status Kelulusan</th>
                                <th width="27%">Tahun</th>
                                <th width="27%">Bulan</th>
                                <th width="27%">Tinggi Badan (Cm)</th>
                                <th width="27%">Berat Badan (Kg)</th>
                                <th width="27%">Status Gizi</th>
                                <th width="27%">Kesimpulan KKA</th>
                                <th width="27%">Rekomendasi</th>
                                <th width="27%">Rujukan</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-page"></div>
@endsection

@section('additional_scripts')
    <script type="text/javascript">
        var dt_tabel;
        var tahun_monev = $('.tahun_monev').val();
        var bulan_monev = $('.bulan_monev').val();
        var wadah_kecamatan = $('.wadah_kecamatan').val();
        var wadah_kelurahan = $('.wadah_kelurahan').val();
        var status_monev = $('.status_monev').val();
        var dt_kecamatan = {!! $kecamatan !!}

        $(document).ready(function() {
            $('#sidebar-kka').addClass('active');
            $('#sidebar-monitor').addClass('active');

            dt_tabel = $('#dataTable').DataTable({
                dom: "<'row'<'col-md-6'l><'col-md-6'B>>" +
                    "<'row'<'col-md-12'f>>" +
                    "<'row'<'col-md-12'tr>><'row'<'col-md-12'ip>>",
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('opd-kka', ['halaman' => 'get_data']) }}',
                    type: 'post',
                    data: function(d) {
                        d.tahun_monev = tahun_monev;
                        d.bulan_monev = bulan_monev;
                        d.wadah_kecamatan = wadah_kecamatan;
                        d.wadah_kelurahan = wadah_kelurahan;
                        d.status_monev = status_monev;
                    }
                },
                buttons: [{
                        extend: 'excel',
                        title: 'judul',
                        filename: function() {
                            judul = $('#judul').html() + '_{{ date('d-m-Y H.i') }}';
                            return judul;
                        }
                    },
                    {
                        extend: 'print',
                        title: function() {
                            judul = $('#judul').html();
                            return judul;
                        },
                        filename: function() {
                            judul = $('#judul').html() + '_{{ date('d-m-Y H.i') }}';
                            return judul;
                        }
                    },
                ],
                lengthMenu: [
                    [10, 25, 50, -1],
                    [10, 25, 50, "All"]
                ],
                pageLength: 25,
                columns: [{
                        data: 'DT_RowIndex',
                        searchable: false,
                        sortable: false,
                    },
                    {
                        data: 'nik',
                        name: 's.nik'
                    },
                    {
                        data: 'nama',
                        name: 's.nama'
                    },
                    {
                        data: 'tgl_lahir',
                        name: 's.tgl_lahir'
                    },
                    {
                        data: 'npsn',
                        name: 'se.npsn'
                    },
                    {
                        data: 'nama_sekolah',
                        name: 'se.nama'
                    },
                    {
                        data: 'kecamatan_sekolah',
                        name: 'kec.kecamatan_dispenduk'
                    },
                    {
                        data: 'kelurahan_sekolah',
                        name: 'kel.kelurahan_dispenduk'
                    },
                    {
                        data: 'status_siswa',
                        name: 's.status_siswa'
                    },
                    {
                        data: 'status_alumni',
                        name: 'status_alumni'
                    },
                    {
                        data: 'tahun',
                        name: 'kka.tahun'
                    },
                    {
                        data: 'bulan',
                        name: 'kka.bulan'
                    },
                    {
                        data: 'tb',
                        name: 'tbbb.tb'
                    },
                    {
                        data: 'bb',
                        name: 'tbbb.bb'
                    },
                    {
                        data: 'status_gizi',
                        name: 'tbbb.status_gizi'
                    },
                    {
                        data: 'kesimpulan_kka',
                        name: 'kka.kesimpulan_kka'
                    },
                    {
                        data: 'rekomendasi',
                        name: 'kka.rekomendasi'
                    },
                    {
                        data: 'rujukan',
                        name: 'r.id_rujukan'
                    },
                ]
            });

            dt_tabel.on('processing.dt', function(e, settings, processing) {
                if (processing) {
                    $('#dataTable tbody').html(
                        '<tr><td colspan="18" class="text-center">Loading...</td></tr>'
                    );
                }
            });

            get_data();
            get_kelurahan();
        });


        function get_data() {

            tahun_monev = $('.tahun_monev').val();
            bulan_monev = $('.bulan_monev').val();
            wadah_kecamatan = $('.wadah_kecamatan').val();
            wadah_kelurahan = $('.wadah_kelurahan').val();
            status_monev = $('.status_monev').val();

            var bulan1 = $('.bulan_monev option:selected');
            var judul = "Data KKA Tahun " + tahun_monev + " Bulan " + bulan1.data('bulan');
            var data = {
                tahun_monev: tahun_monev,
                bulan_monev: bulan_monev,
                wadah_kecamatan: wadah_kecamatan,
                wadah_kelurahan: wadah_kelurahan,
                status_monev: status_monev,
            };

            $('#judul').html(judul);
            dt_tabel.ajax.reload();

            $.post('{{ route('opd-kka', ['halaman' => 'hitung_jumlah']) }}', data, function(data) {
                $('.wadah_siswa_aktif').html(data.jumlah_siswa);
                $('.wadah_siswa_aktif_sudah').html(data.jumlah_siswa_sudah);
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
