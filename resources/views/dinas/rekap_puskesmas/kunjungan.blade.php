@extends('layout.index')

@section('additional_styles')
    <style>
        tr>th,
        td {
            text-align: center;
        }

        .fix_header {
            background: #fff;
            padding: 15px;
            margin-bottom: 10px;
        }

        tfoot th {
            background: #f5f5f5;
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
        <div class="fix_header">
            <div style="font-weight: bold;font-size: 20pt">Rekap Kunjungan Puskesmas - Per Kunjungan</div>
        </div>
        <div class="block">
            <div class="block-header block-header-default row no-margin">
                <h3 class="block-title">Rincian Per Kunjungan</h3>
            </div>
            <div class="block-content block-content-full">
                {{-- <small>* Satu sekolah bisa punya lebih dari satu kunjungan, jadi satu baris di sini = satu jadwal
                    kunjungan.</small> --}}
                <div class="row" style="margin-top: 10px">
                    <div class="col-lg-2">
                        <label>Tahun</label>
                        <select class="form-control" name="tahun" onchange="update_tanggal_default(); ambil_data()">
                            <option value="semua">Semua Tahun</option>
                            @foreach ($tahun_list as $th)
                                <option value="{{ $th }}" @if ($th == date('Y')) selected @endif>{{ $th }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-2">
                        <label>Bulan</label>
                        @php
                            $bulan_list = [
                                '01' => 'Januari',
                                '02' => 'Februari',
                                '03' => 'Maret',
                                '04' => 'April',
                                '05' => 'Mei',
                                '06' => 'Juni',
                                '07' => 'Juli',
                                '08' => 'Agustus',
                                '09' => 'September',
                                '10' => 'Oktober',
                                '11' => 'November',
                                '12' => 'Desember',
                            ];
                        @endphp
                        <select class="form-control" name="bulan" onchange="update_tanggal_default(); ambil_data()">
                            <option value="semua">Semua Bulan</option>
                            @foreach ($bulan_list as $kode => $nama)
                                <option value="{{ $kode }}" @if ($kode == date('m')) selected @endif>{{ $nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-2">
                        <label>Sampai Tanggal</label>
                        <input type="date" class="form-control" name="tanggal"
                            value="{{ $tanggal_default }}" onchange="ambil_data()">
                    </div>
                    <div class="col-lg-2">
                        <label>Kecamatan</label>
                        <select class="form-control kecamatan" onchange="get_kelurahan()">
                            <option data-kode="All" value="All">..:: Semua ::..</option>
                            @foreach ($kecamatan as $kec)
                                <option data-kode="{{ $kec->kecamatan_kode }}"
                                    value="{{ $kec->kecamatan_kode }}">{{ $kec->kecamatan_dispenduk }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-2">
                        <label>Kelurahan</label>
                        <select class="form-control kelurahan" onchange="ambil_data()">
                            <option value="All">..:: Semua ::..</option>
                        </select>
                    </div>
                    <div class="col-lg-2">
                        <label>Jenjang</label>
                        <select class="form-control" name="jenjang" onchange="ambil_data()">
                            <option value="semua">Semua Jenjang</option>
                            @foreach ($label_jenjang as $kode => $label)
                                <option value="{{ $kode }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row" style="margin-top: 15px">
                    <div class="col-lg-12" style="overflow: auto">
                        <table id="tabel_rekap" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Sekolah</th>
                                    <th>NPSN</th>
                                    <th>Jenjang</th>
                                    <th>Kecamatan</th>
                                    <th>Kelurahan</th>
                                    <th>Puskesmas</th>
                                    <th>Posyandu</th>
                                    <th>CP Puskesmas</th>
                                    <th>CP Posyandu</th>
                                    <th>Tanggal Kunjungan</th>
                                    <th>Jumlah Murid</th>
                                    <th>Akan Diperiksa</th>
                                    <th>Sudah Diperiksa</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="11">Total Jumlah Siswa</th>
                                    <th class="ft-jumlah_murid">-</th>
                                    <th class="ft-akan_diperiksa">-</th>
                                    <th class="ft-sudah_diperiksa">-</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-page"></div>
@endsection

@section('additional_scripts')
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script type="text/javascript">
        var tabel_rekap;

        $(document).ready(function() {
            $('#sidebar-rekap_puskesmas').addClass('active');
            update_tanggal_default();

            tabel_rekap = $('#tabel_rekap').DataTable({
                ajax: {
                    url: '{{ route('dinas-rekap_puskesmas', ['halaman' => 'get_data_kunjungan']) }}',
                    type: 'post',
                    data: function(d) {
                        d.tahun = $('select[name=tahun]').val();
                        d.bulan = $('select[name=bulan]').val();
                        d.tanggal = $('input[name=tanggal]').val();
                        d.jenjang = $('select[name=jenjang]').val();
                        d.kecamatan_kode = $('.kecamatan option:selected').data('kode');
                        d.kelurahan_kode = $('.kelurahan option:selected').data('kode');
                    }
                },
                dom: "<'row'<'col-md-6'l><'col-md-6'B>>" +
                    "<'row'<'col-md-12'f>>" +
                    "<'row'<'col-md-12't>><'row'<'col-md-12'ip>>",
                buttons: [{
                    extend: 'excelHtml5',
                    text: '<i class="fa fa-file-excel-o"></i> Export Excel',
                    title: 'Rekap Kunjungan Puskesmas - Per Kunjungan',
                    filename: 'rekap_puskesmas_per_kunjungan_{{ date('Y-m-d') }}'
                }],
                drawCallback: function() {
                    var api = this.api();
                    var start = api.page.info().start;
                    api.column(0, {
                        page: 'current'
                    }).nodes().each(function(cell, i) {
                        cell.innerHTML = start + i + 1;
                    });
                },
                columns: [{
                        data: null,
                        name: 'DT_RowIndex',
                        searchable: false,
                        orderable: false,
                        defaultContent: ''
                    },
                    {
                        data: 'nama_sekolah',
                        name: 'se.nama'
                    },
                    {
                        data: 'npsn',
                        name: 'k.npsn'
                    },
                    {
                        data: 'jenjang_label',
                        name: 'se.jenjang'
                    },
                    {
                        data: 'kecamatan_dispenduk',
                        name: 'kec.kecamatan_dispenduk'
                    },
                    {
                        data: 'kelurahan_dispenduk',
                        name: 'kel.kelurahan_dispenduk'
                    },
                    {
                        data: 'nama_puskesmas',
                        name: 'k.nama_puskesmas'
                    },
                    {
                        data: 'nama_posyandu',
                        name: 'k.nama_posyandu'
                    },
                    {
                        data: 'cp_puskesmas',
                        name: 'cp_puskesmas',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'cp_posyandu',
                        name: 'cp_posyandu',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'tanggal_fmt',
                        name: 'k.tanggal'
                    },
                    {
                        data: 'jumlah_murid',
                        name: 'jumlah_murid',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'akan_diperiksa',
                        name: 'akan_diperiksa',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'sudah_diperiksa',
                        name: 'sudah_diperiksa',
                        searchable: false,
                        orderable: false
                    },
                ]
            });

            get_kelurahan();
        });

        function get_kelurahan() {
            var kec = $('.kecamatan option:selected');
            var kode = kec.data('kode');

            $.post('{{ route('bunda-dashboard_asw-get_kelurahan') }}', {
                kode: kode,
                dukcapil: kec.text()
            }, function(data) {
                var html = '<option value="All">..:: Semua ::..</option>';
                if (data.code == 200 && data.kelurahan.length > 0) {
                    $.each(data.kelurahan, function(k, v) {
                        html += '<option data-kode="' + v.kelurahan_kode + '">' + v.kelurahan_dispenduk +
                            '</option>';
                    });
                }
                $('.kelurahan').html(html);
                ambil_data();
            });
        }

        function fmt_tanggal(d) {
            return d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' +
                String(d.getDate()).padStart(2, '0');
        }

        function update_tanggal_default() {
            var bulan = $('select[name=bulan]').val();
            var tahun = $('select[name=tahun]').val();
            var input = $('input[name=tanggal]');
            if (bulan && bulan != 'semua' && tahun && tahun != 'semua') {
                var awalBulan = new Date(parseInt(tahun), parseInt(bulan) - 1, 1);
                var akhirBulan = new Date(parseInt(tahun), parseInt(bulan), 0);
                input.attr('min', fmt_tanggal(awalBulan));
                input.attr('max', fmt_tanggal(akhirBulan));
                input.val(fmt_tanggal(akhirBulan));
            } else {
                input.removeAttr('min');
                input.removeAttr('max');
            }
        }

        function ambil_data() {
            tabel_rekap.ajax.reload();
            load_footer();
        }

        function load_footer() {
            $.post('{{ route('dinas-rekap_puskesmas', ['halaman' => 'get_footer_kunjungan']) }}', {
                tahun: $('select[name=tahun]').val(),
                bulan: $('select[name=bulan]').val(),
                tanggal: $('input[name=tanggal]').val(),
                jenjang: $('select[name=jenjang]').val(),
                kecamatan_kode: $('.kecamatan option:selected').data('kode'),
                kelurahan_kode: $('.kelurahan option:selected').data('kode'),
            }, function(data) {
                $.each(data, function(col, html) {
                    $('.ft-' + col).html(html);
                });
            }).fail(function() {
                swal('Whooops', 'Gagal memuat total keseluruhan', 'error');
            });
        }

        function modal_nama(param) {
            $.post('{{ route('dinas-rekap_puskesmas', ['halaman' => 'modal_nama']) }}', {
                param: param
            }, function(data) {
                $('.modal-page').html(data.content);
            }).fail(function() {
                swal('Whooops', '500 - Internal server error', 'error');
            });
        }
    </script>
@endsection
