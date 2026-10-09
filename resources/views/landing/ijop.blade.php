@extends('landing.layout')
@section('css')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/rowgroup/1.5.0/css/rowGroup.dataTables.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/3.1.2/css/buttons.dataTables.css">
    <style>
    </style>
@endsection
@section('content')
    <div class="container">
        <div class="row bg-light">
            <div class="col-lg-12">
                <div class="row">
                    <div class="col-lg-12 d-flex justify-content-center">
                        <h1>Daftar Status Ijin Operasional Sekolah</h1>
                    </div>
                </div>
                <div class="row" style="margin: 20px 0px">
                    <div class="col-lg-4">
                        <select name="kecamatan" class="form-control" onchange="ganti_kecamatan()">
                            <option value="">Pilih Kecamatan</option>
                            @foreach ($kecamatan as $kec)
                                <option value="{!! $kec['kecamatan_kode'] !!}" @if ($kec['kecamatan_kode'] == $cari_kecamatan) selected @endif>
                                    {!! $kec['kecamatan_dispenduk'] !!}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-4">
                        <select name="kelurahan" class="form-control">
                            <option value="">Pilih Kelurahan</option>
                        </select>
                    </div>
                    <div class="col-lg-4">
                        @php
                            $jenjang = ['TPA', 'SPS', 'KB', 'TK', 'RA'];
                        @endphp
                        <select name="jenjang" class="form-control">
                            <option value="">Pilih Jenjang</option>
                            @for ($i = 0; $i < count($jenjang); $i++)
                                <option @if ($jenjang[$i] == $cari_jenjang) selected @endif>{{ $jenjang[$i] }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-lg-12">
                        <a href="javascript:void(0)" class="btn btn-primary" onclick="cari()"><i class="fa fa-search"></i>
                            Cari</a>
                    </div>
                </div>
                <div class="row">
                    <div style="width: 100%;overflow: auto;">
                        <table id="table_ijop" class="display nowrap" style="width: 100%">
                            <thead>
                                <tr>
                                    <th class="text-center">NPSN</th>
                                    <th class="text-center">Nama Sekolah</th>
                                    <th class="text-center">Kecamatan</th>
                                    <th class="text-center">Kelurahan</th>
                                    <th class="text-center">Selesai Berlaku</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Hasil Monitoring</th>
                                    <th class="text-center">Keterangan</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
                <div class="row">&nbsp;</div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/rowgroup/1.5.0/js/dataTables.rowGroup.js"></script>
    <script src="https://cdn.datatables.net/rowgroup/1.5.0/js/rowGroup.dataTables.js"></script>

    <script src="https://cdn.datatables.net/buttons/3.1.2/js/dataTables.buttons.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.1.2/js/buttons.dataTables.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.1.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.1.2/js/buttons.print.min.js"></script>
    <script>
        var data_kecamatan = [];
        var kelurahan = [];

        $(document).ready(function() {
            @foreach ($kecamatan as $kec)
                data_kecamatan['{!! $kec['kecamatan_kode'] !!}'] = '{!! $kec['kelurahan'] !!}';
            @endforeach
            ganti_kecamatan();
        });

        function ganti_kecamatan() {
            var html_kelurahan = '<option value="">Pilih Kelurahan</option>';
            var kecamatan = $('select[name=kecamatan]').val();
            if (kecamatan != '') {
                var data_kelurahan = data_kecamatan[kecamatan];
                kelurahan = JSON.parse(atob(data_kelurahan));
            } else {
                kelurahan = [];
            }

            $.each(kelurahan, function(k, v) {
                if (v.kelurahan_kode == '{{ $cari_kelurahan }}') {
                    html_kelurahan += '<option selected value="' + v.kelurahan_kode + '">' + v.kelurahan_dispenduk +
                        '</option>';
                } else {
                    html_kelurahan += '<option value="' + v.kelurahan_kode + '">' + v.kelurahan_dispenduk +
                        '</option>';
                }
            });

            $('select[name=kelurahan]').html(html_kelurahan);
        }

        function cari() {
            var kecamatan = $('select[name=kecamatan]').val();
            var kelurahan = $('select[name=kelurahan]').val();
            var jenjang = $('select[name=jenjang]').val();

            location = '{{ Request::url() }}?kecamatan=' + kecamatan + '&kelurahan=' + kelurahan + '&jenjang=' +
                jenjang;
        }

        var table = $('#table_ijop').DataTable({
            dom: "<'row'<'col-md-10'B><'col-md-2'f>>" +
                "<'row'<'col-md-12'l>>" +
                "<'row'<'col-md-12'tr>>" +
                "<'row'<'col-md-4'i><'col-md-8'p>>",
            buttons: ['excel', 'pdf', 'print'],
            ordering: false,
            responsive: true,
            processing: true,
            serverSide: true,
            ajax: '{{ url('/') }}/landing/get_ijop?kecamatan={{ $cari_kecamatan }}&kelurahan={{ $cari_kelurahan }}&jenjang={{ $cari_jenjang }}',
            columns: [{
                    data: 'npsn',
                    name: 'm.npsn'
                },
                {
                    data: 'nama_sekolah',
                    name: 'i.nama_sekolah'
                },
                {
                    data: 'kecamatan_name',
                    name: 'i.kecamatan_name'
                },
                {
                    data: 'kelurahan_name',
                    name: 'i.kelurahan_name'
                },
                {
                    data: 'waktu',
                    searchable: false,
                    sortable: false,
                },
                {
                    data: 'status',
                    searchable: false,
                    sortable: false,
                },
                {
                    data: 'hasil_monitoring',
                    className: 'text-center',
                    name: 'm.hasil'
                },
                {
                    data: 'keterangan_monitoring',
                    name: 'm.keterangan'
                },
            ],
            lengthMenu: [
                [10, 25, 50, -1],
                ['10', '25', '50', 'Semua']
            ],
            rowGroup: {
                dataSrc: ['kecamatan_name', 'kelurahan_name']
            },
        });
    </script>
@endsection
