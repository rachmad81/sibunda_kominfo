@extends('layout.index')


@section('additional_styles')
    <style>
        tr>th,
        td {
            text-align: center;
        }
    </style>
@endsection
@section('sidebar')
    @include('bunda/layout/sidebar')
@endsection

@section('content')
    <div class="content" id="app">
        <h2 class="content-heading" style="padding-top: 0px">Konfirmasi Pegawai</h2>
        <!-- Dynamic Table Full -->
        <div class="block">
            <div class="block-header block-header-default row no-margin">
                <h3 class="block-title">Daftar Data Konfirmasi Pegawai</h3>
            </div>
            <div class="block-content block-content-full">
                @include('layout.alert')
                <div class="row">
                    <div class="col-lg-1">
                        <label>Kecamatan</label>
                    </div>
                    <div class="col-lg-4">
                        <select id="kecamatan" name="kecamatan" onchange="get_kelurahan();get_data();"
                            @if (in_array(Auth::getUser()->tingkat, ['kecamatan', 'kelurahan'])) disabled @endif>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-1">
                        <label>Kelurahan</label>
                    </div>
                    <div class="col-lg-4">
                        <select id="kelurahan" name="kelurahan" onchange="get_data()"
                            @if (in_array(Auth::getUser()->tingkat, ['kelurahan'])) disabled @endif>
                        </select>
                    </div>
                </div>
                {{-- <div class="row">
                    <div class="col-lg-1">
                        <label>Jenjang</label>
                    </div>
                    <div class="col-lg-4">
                        @php
                            $get_jenjang = ['KB', 'TK', 'SPS', 'TPA'];
                        @endphp
                        <select name="jenjang" onchange="get_data()">
                            <option value="" selected>Semua Jenjang</option>
                            @foreach ($get_jenjang as $j)
                                <option value="{{ $j }}">
                                    {{ $j }}</option>
                            @endforeach
                        </select>
                    </div>
                </div> --}}
                <div class="row">
                    <div class="col-lg-12" style="overflow: auto;">
                        <table id="dataTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th style="width:1%">No</th>
                                    <th>Jenjang</th>
                                    <th>NPSN</th>
                                    <th>Nama Sekolah</th>
                                    {{-- <th>NIK</th> --}}
                                    <th>Nama Pegawai</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @include('attention')
    </div>
    <div class="modal-page"></div>
@endsection

@section('additional_scripts')
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <style type="text/css">
        .modal {
            overflow: auto !important;
        }

        .dt-left {
            text-align: left;
        }
    </style>
    <script type="text/javascript">
        var dt_table;
        let kecamatanSelect = document.getElementById("kecamatan");
        let kelurahanSelect = document.getElementById("kelurahan");
        let kecamatans = {!! $kecamatans !!};

        function get_kecamatan() {
            kecamatanSelect.innerHTML = "";
            @if (in_array(request()->user()->tingkat, ['kota']))
                kecamatanSelect.innerHTML += `<option value="" selected>Semua Kecamatan</option>`;
            @endif
            kecamatans.forEach(kecamatan => {
                kecamatanSelect.innerHTML +=
                    `<option value="${kecamatan.kecamatan_kode}">${kecamatan.kecamatan_dispenduk}</option>`;
            });
        }

        function get_kelurahan() {
            kelurahanSelect.innerHTML = "";
            @if (in_array(request()->user()->tingkat, ['kota', 'kecamatan']))
                kelurahanSelect.innerHTML += `<option value="" selected>Semua Kelurahan</option>`;
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

        $(document).ready(function() {
            get_kecamatan();
            get_kelurahan();
            $('#sidebar-sekolah').addClass('active');
            var kecamatan = $('select[name=kecamatan]').val();
            var kelurahan = $('select[name=kelurahan]').val();
            var jenjang = $('select[name=jenjang]').val();
            dt_table = $("#dataTable").DataTable({
                lengthMenu: [
                    [10, 25, 50, -1],
                    [10, 25, 50, "All"]
                ],
                processing: true,
                serverside: true,
                ajax: `{!! route('bunda-konfirmasi-pegawai-get_data') !!}?kec=${kecamatan}&kel=${kelurahan}`,
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'jenjang_tujuan',
                        name: 'pg_transaksi_gtk.jenjang_tujuan'
                    },
                    {
                        data: 'npsn_tujuan',
                        name: 'pg_transaksi_gtk.npsn_tujuan'
                    },
                    {
                        data: 'lembaga_tujuan',
                        name: 'pg_transaksi_gtk.lembaga_tujuan'
                    },
                    // {
                    //     data: 'nik',
                    //     name: 'pg_transaksi_gtk.nik'
                    // },
                    {
                        data: 'nama_pegawai',
                        name: 'pg_transaksi_gtk.nama_pegawai'
                    },
                    {
                        data: 'aksi',
                        name: 'aksi',
                        orderable: false,
                        searchable: false
                    },
                ]
            });
        });

        function get_data() {
            $('tbody').html('<tr><td colspan="11"><h4>Harap tunggu.....</h4></td></tr>');
            var kecamatan = $('select[name=kecamatan]').val();
            var kelurahan = $('select[name=kelurahan]').val();
            var jenjang = $('select[name=jenjang]').val();
            dt_table.ajax.url(`{!! route('bunda-konfirmasi-pegawai-get_data') !!}?kec=${kecamatan}&kel=${kelurahan}`).load();
        }

        function modal(id) {
            var data = {
                id: id,
            };
            $.post("{{ route('bunda-konfirmasi-pegawai-modal') }}", data, function(data) {
                $('.modal-page').html(data.content);
            });
        }
    </script>
@endsection
