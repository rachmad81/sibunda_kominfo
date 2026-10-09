@extends('layout.index')

@section('additional_styles')
    <style>
        th,
        td {
            text-align: center;
        }
    </style>
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection
@section('sidebar')
    @if (\Auth::User()->role == 'dinas')
        @include('dinas/layout/sidebar')
    @elseif(\Auth::User()->role == 'bunda')
        @include('bunda/layout/sidebar')
    @elseif(Auth::getUser()->role == 'opd')
        @include('opd/layout/sidebar')
    @elseif(in_array(\Auth::User()->role, ['k3_paud', 'paud_sd', 'gugus', 'mitra']))
        @include('k3_paud_sd/layout/sidebar')
    @else
        @include('pokja/layout/sidebar')
    @endif
@endsection

@section('content')
    <div class="content" id="app">
        <h2 class="content-heading" style="padding-top: 0px">Kelola Program</h2>
        <!-- Dynamic Table Full -->
        <div class="kontent-utama">
            <div class="block">
                <div class="block-header block-header-default row no-margin">
                    <h3 class="block-title">Daftar Program</h3>
                    <button class="btn btn-outline-success col-md-2 m-l-xs m-b-xs" onclick="formula()">Tambah
                        Program</button>
                </div>
                <div class="block-content block-content-full">
                    <div class="row">
                        <div class="col-12">
                            <div class="alert alert-info">
                                <i class="fa fa-exclamation"></i> <b>Perhatian</b>
                                <p style="margin: 0px">
                                    Sebelum cetak pastikan men-ceklist cetak background/latar belakang!
                                </p>
                            </div>
                        </div>
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
                        <div class="col-md-4 col-sm-12 col-xs-12">
                            <button class="btn btn-outline-info col-md-2 m-l-xs m-b-xs"
                                onclick="printDiv('printarea')">Cetak</button>
                        </div>
                    </div>
                    @include('layout.alert')
                    <div id="printarea" style="overflow: auto">
                        <div style="width: 100%;text-align: center;">
                            <h1 id="title_print" style="display: none">
                                Program BUNDA PAUD<br>
                                {!! Auth::User()->getTingkat() !!}
                            </h1>
                        </div>
                        <table style="width: 100%;" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th width="1%">No</th>
                                    <th>Bidang</th>
                                    <th style="white-space: nowrap;">Nama Program</th>
                                    <th>Sasaran</th>
                                    <th>Jumlah Sasaran</th>
                                    <th>Tujuan</th>
                                    @for ($i = 1; $i <= 12; $i++)
                                        <th>{{ $i }}</th>
                                    @endfor
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
    <script type="text/javascript">
        $("#sidebar-program").addClass("active");
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var th = '';

        function ganti_tahun() {
            th = $('select[name=tahun_cari]').val();
            $.post("{{ route('common-program-data') }}", {
                th: th
            }, function(data) {
                $('#jadwal').html(data.content);
            });
        }

        function formula(id_program = null) {
            var data = {
                id_program: id_program,
            };

            $.post("{{ route('common-program-form') }}", data, function(data) {
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
            var message = '';

            if (data.get('nama_kegiatan') == '') {
                izinkan = 0;
                message = 'Nama program harus diisi';
            } else if (data.get('sasaran') == '') {
                izinkan = 0;
                message = 'Sasaran program harus diisi';
            } else if (data.get('tujuan') == '') {
                izinkan = 0;
                message = 'Tujuan program harus diisi';
            } else if (data.get('tahun') == '') {
                izinkan = 0;
                message = 'Tahun program harus dipilih';
            }

            if (izinkan == 1) {
                $.ajax({
                    type: "POST",
                    enctype: 'multipart/form-data',
                    url: "{{ route('common-program-simpan') }}",
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
                swal('Whooops', message, 'error');
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
                    $.post("{{ route('common-program-hapus') }}", {
                        id_program: id
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
            $('#title_print').show();
            alert("Mohon untuk memilih (header dan footer),(background graphics) pada saat mencetak halaman");

            var printContents = document.getElementById(divName).innerHTML;
            var originalContents = document.body.innerHTML;

            document.body.innerHTML = printContents;

            window.print();

            document.body.innerHTML = originalContents;
            $('.aksi').show();
            $('#title_print').hide();
            $('select[name=tahun_cari]').val(th);
        }
    </script>
@endsection
