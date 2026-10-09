@extends('layout.index')

@section('additional_styles')
    <style>
        th,
        td {
            text-align: center;
        }

        @media print {
            @page {
                size: landscape
            }
        }
    </style>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {{-- <link href="{{asset('datetimepicker/datepicker.css')}}" rel="stylesheet" type="text/css" /> --}}
    <link href="https://unpkg.com/gijgo@1.9.13/css/gijgo.min.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">
@endsection
@section('sidebar')
    @if (\Auth::User()->role == 'dinas')
        @include('dinas/layout/sidebar')
    @elseif(\Auth::User()->role == 'bunda')
        @include('bunda/layout/sidebar')
    @elseif(\Auth::User()->role == 'opd')
        @include('opd/layout/sidebar')
    @elseif(in_array(\Auth::User()->role, ['k3_paud', 'paud_sd', 'gugus', 'mitra']))
        @include('k3_paud_sd/layout/sidebar')
    @else
        @include('pokja/layout/sidebar')
    @endif
@endsection

@section('content')
    <div class="content" id="app">
        <h2 class="content-heading" style="padding-top: 0px">Kelola Kegiatan</h2>
        <!-- Dynamic Table Full -->
        <div class="kontent-utama">
            <div class="block">
                <div class="block-header block-header-default row no-margin">
                    <h3 class="block-title judul">Daftar Kegiatan</h3>
                    <button class="btn btn-outline-success col-md-2 m-l-xs m-b-xs" onclick="formula()">Tambah
                        Kegiatan</button>
                </div>
                <div class="block-content block-content-full">
                    <div class="row">
                        <div class="col-md-4 col-sm-12 col-xs-12">
                            <div class="form-group row">
                                <label class="col-md-4 label-control">Tahun Program</label>
                                <div class="col-md-8">
                                    <select class="form-control" name="tahun_cari" onchange="ganti_tahun()">
                                        <option value="">-- Tahun Program --</option>
                                        @for ($i = 2022; $i <= date('Y'); $i++)
                                            <option value="{{ $i }}">{{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 col-sm-12 col-xs-12">
                            <div class="form-group row">
                                <label class="col-md-4 label-control">Status Kegiatan</label>
                                <div class="col-md-8">
                                    <select class="form-control" name="status_kegiatan" onchange="ganti_tahun()">
                                        <option value="all">-- Semua --</option>
                                        <option value="sudah">Sudah Laporan</option>
                                        <option value="belum">Belum Laporan</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 col-sm-12 col-xs-12">
                            <button class="btn btn-outline-info col-md-2 m-l-xs m-b-xs"
                                onclick="printDiv('printarea')">Cetak</button>
                        </div>
                    </div>
                    @include('layout.alert')
                    <div>
                        <center>
                            <h1 class="judul">{{ Auth::getUser()->nama }}</h1>
                        </center>
                    </div>
                    <div id="printarea" style="overflow: auto;">
                        <div style="width: 100%;text-align: center;">
                            <h1 class="title_print">
                                Kegiatan BUNDA PAUD<br>
                                {!! Auth::User()->getTingkat() !!}
                            </h1>
                        </div>
                        <table style="width: 100%;" class="table table-bordered table-striped" id="isi-table">
                            <thead>
                                <tr>
                                    <th width="1%">No</th>
                                    <th style="white-space: nowrap;" class="aksi">Nama Program</th>
                                    <th>Nama Kegiatan</th>
                                    <th style="min-width: 250px;">Detail Kegiatan</th>
                                    <th class="aksi" style="min-width: 250px;">Materi</th>
                                    <th class="title_print" style="min-width: 250px;">Materi</th>
                                    <th>Dana</th>
                                    <th style="min-width: 250px;">Sasaran</th>
                                    <th>Hasil</th>
                                    <th>Output</th>
                                    <th class="aksi">Dok.</th>
                                    <th class="aksi">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="jadwal"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="side-kontent"></div>
    </div>
@endsection

@section('additional_scripts')
    <script src="{{ asset('datetimepicker/datepicker.js') }}" type="text/javascript"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript">
        $('#isi-table').DataTable();
        $("#sidebar-kegiatan").addClass("active");
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $(document).ready(function() {
            $('input[name=tgl_kegiatan]').datepicker();
            $('.title_print').hide();
        });

        var th = '';

        function ganti_tahun() {
            $('#isi-table').dataTable().fnDestroy();;
            $('#jadwal').empty();

            th = $('select[name=tahun_cari]').val();
            status = $('select[name=status_kegiatan]').val();

            $('.isi-table tbody').html('');
            $.post("{{ route('common-kegiatan-data') }}", {
                th: th,
                status: status
            }, function(data) {

                $('#jadwal').html(data.content);
                $('.title_print').hide();

                new $('#isi-table').DataTable({
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
                    serverSide: false,
                });
            });
        }

        function get_program() {
            th = $('select[name=tahun_form]').val();
            $.post("{{ route('common-program-data') }}", {
                th: th,
                get_program: 'data'
            }, function(data) {
                var html = '<option value="">-- Pilih Program --</option>';
                if (data.length != 0) {
                    $.each(data, function(k, v) {
                        html += '<option value="' + v.id_program + '">' + v.nama_kegiatan + ' (' + v.tahun +
                            ')</option>';
                    });
                }
                $('select[name=program_id]').html(html);
            });
        }

        function formula(id_kegiatan = null) {
            var data = {
                id_kegiatan: id_kegiatan,
            };

            $.post("{{ route('common-kegiatan-form') }}", data, function(data) {
                $('.kontent-utama').hide();
                $('.side-kontent').html(data.content);
                $('.side-kontent').show();
            });
        }

        function form_laporan(id_kegiatan = null) {
            var data = {
                id_kegiatan: id_kegiatan,
            };

            $.post("{{ route('common-kegiatan-form_laporan') }}", data, function(data) {
                $('.kontent-utama').hide();
                $('.side-kontent').html(data.content);
                $('.side-kontent').show();
            });
        }

        function kembali_form() {
            $('.kontent-utama').show();
            $('.side-kontent').hide();
            ganti_tahun();
        }

        function simpan_form() {
            var data = new FormData($('form#form_simpan')[0]);

            var izinkan = 1;
            var pesan = '';
            if (data.get('program_id') == '') {
                izinkan = 0;
                pesan = 'Program harus dipilih';
            } else if (data.get('nama_kegiatan') == '') {
                izinkan = 0;
                pesan = 'Nama kegiatan harus diisi';
            } else if (data.get('tgl_kegiatan') == '') {
                izinkan = 0;
                pesan = 'Tanggal kegiatan harus diisi';
            } else if (data.get('sasaran_kegiatan') == '') {
                izinkan = 0;
                pesan = 'Sasaran kegiatan harus diisi';
            } else if (data.get('tujuan_kegiatan') == '') {
                izinkan = 0;
                pesan = 'Tujuan kegiatan harus diisi';
            }

            // if(data.get('id_kegiatan')==''){
            // 	if(data.get('foto').name==''){
            // 		izinkan = 0;
            // 		pesan = 'Foto kegiatan harus diisi';
            // 	}
            // }

            if (izinkan == 1) {
                $.ajax({
                    type: "POST",
                    enctype: 'multipart/form-data',
                    url: "{{ route('common-kegiatan-simpan') }}",
                    data: data,
                    processData: false,
                    contentType: false,
                    cache: false,
                    timeout: 800000,
                    success: function(data) {
                        if (data.code == '200') {
                            kembali_form();
                        }
                        swal(data.title, data.message, data.type);
                    },
                    error: function(e) {
                        console.log("ERROR : ", e);
                    }
                });
            } else {
                swal('Whooops', pesan, 'warning');
            }
        }

        function simpan_laporan() {
            var data = new FormData($('form#form_simpan')[0]);
            var foto_exists = $('#foto_exists').val();

            var izinkan = 1;
            var pesan = '';

            if (foto_exists == '0') {
                if (data.get('foto').name == '') {
                    izinkan = 0;
                    pesan = 'Foto kegiatan harus diisi';
                }
            }

            if (izinkan == 1) {
                $.ajax({
                    type: "POST",
                    enctype: 'multipart/form-data',
                    url: "{{ route('common-kegiatan-simpan_laporan') }}",
                    data: data,
                    processData: false,
                    contentType: false,
                    cache: false,
                    timeout: 800000,
                    success: function(data) {
                        if (data.code == '200') {
                            kembali_form();
                        }
                        swal(data.title, data.message, data.type);
                    },
                    error: function(e) {
                        console.log("ERROR : ", e);
                    }
                });
            } else {
                swal('Whooops', pesan, 'warning');
            }
        }

        function hapus(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.value) {
                    $.post("{{ route('common-kegiatan-hapus') }}", {
                        id_kegiatan: id
                    }, function(data) {
                        if (data.code == '200') {
                            kembali_form();
                        }
                        swal(data.title, data.message, data.type);
                    });
                } else {}
            })
        }

        function printDiv(divName) {
            $('.aksi').hide();
            $('.title_print').show();
            $('#isi-table').DataTable().destroy();

            var printContents = document.getElementById(divName).innerHTML;
            var originalContents = document.body.innerHTML;

            document.body.innerHTML = printContents;

            window.print();

            document.body.innerHTML = originalContents;
            $('.aksi').show();
            $('.title_print').hide();
            $('select[name=tahun_cari]').val(th);
            ganti_tahun();
        }

        function lengkap(id, isi) {
            isi = atob(isi);
            var mini = btoa(isi.substr(0, 255));
            var materi = isi + ' <a href="javascript:void(0)" onclick="sedikit(\'' + id + '\',\'' + mini + '\',\'' + btoa(
                isi) + '\')"> Sedikit</a>';

            $('#' + id).html(materi);
        }

        function sedikit(id, mini, isi) {
            isi = atob(isi);
            mini = atob(mini);
            var materi = mini + ' <a href="javascript:void(0)" onclick="lengkap(\'' + id + '\',\'' + btoa(isi) +
                '\')"> Selengkapnya</a>';

            $('#' + id).html(materi);
        }
    </script>
@endsection
