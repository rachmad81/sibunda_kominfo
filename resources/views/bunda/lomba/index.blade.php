@extends('layout.index')


@section('additional_styles')
    <style>
        tr>th>td {
            text-align: center;
        }

        .aktif-form {
            background: #ccc;
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
        <h2 class="content-heading" style="padding-top: 0px">Lomba Apresiasi Bunda Kelurahan/Kecamatan</h2>
        <!-- Dynamic Table Full -->
        <div class="block">
            <div class="block-header block-header-default row no-margin">
                <h3 class="block-title">Lomba Apresiasi Bunda Kelurahan/Kecamatan</h3>
            </div>
            <div class="block-content block-content-full">
                @include('layout.alert')
                <div class="row">
                    <div class="col-lg-12" style="overflow: auto">
                        <div>
                            <center>
                                <h1 class="judul" style="display:none;">Lomba Apresiasi Bunda Kelurahan/Kecamatan</h1>
                            </center>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-2">Tahun Lomba</div>
                    <div class="col-lg-4">
                        <div class="input-group mb-3">
                            <select class="form-control tahun_lomba">
                                @for ($i = date('Y'); $i >= 2024; $i--)
                                    <option @if ($i == $tahun) selected @endif)>{{ $i }}</option>
                                @endfor
                            </select>
                            <div class="input-group-append">
                                <a href="javascript:void(0)" class="btn btn-primary" onclick="ganti_tahun()">Cari</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-2" style="overflow: auto">
                        <div class="form-menu">
                            <a href="javascript:void(0)" onclick="formnya('form1')" class="form-menu form1"
                                style="font-weight: bold;font-size: 15pt;">Identitas Calon</a>
                            <hr>
                            <a href="javascript:void(0)"
                                onclick="@if ($status_iden == 'ada') formnya('form2') @else return swal('Whooop','Mohon lengkapi dulu form Identitas Calon','warning') @endif"
                                class="form-menu form2" style="font-weight: bold;font-size: 15pt;">Dokumen Administrasi</a>
                            <hr>
                            <a href="javascript:void(0)"
                                onclick="@if ($status_iden == 'ada') formnya('form3') @else return swal('Whooop','Mohon lengkapi dulu form Identitas Calon','warning') @endif"
                                class="form-menu form3" style="font-weight: bold;font-size: 15pt;">Penilaian Kriteria
                                Utama</a>
                            <hr>
                            <a href="javascript:void(0)"
                                onclick="@if ($status_iden == 'ada') formnya('form4') @else return swal('Whooop','Mohon lengkapi dulu form Identitas Calon','warning') @endif"
                                class="form-menu form4" style="font-weight: bold;font-size: 15pt;">Penilaian Kriteria
                                Pendukung</a>
                            <hr>
                            {{-- <a href="javascript:void(0)" onclick="@if ($status_iden == 'ada') formnya('panduan') @else return swal('Whooop','Mohon lengkapi dulu form Identitas Calon','warning') @endif" class="form-menu form4" style="font-weight: bold;font-size: 15pt;">Panduan</a><hr> --}}
                            @php
                                switch ($tahun) {
                                    case '2024':
                                        $url =
                                            'https://drive.google.com/file/d/1YVHY3YUAZ0H9il9QbzkqUlgNVo9OlFYI/view?usp=drive_link';
                                        break;
                                    case '2025':
                                        $url =
                                            'https://drive.google.com/file/d/1cAFsD9FmIk1lgP5os7jboPX-tTutlq2y/view?usp=drive_link';
                                        break;

                                    default:
                                        $url =
                                            'https://i.pinimg.com/1200x/cd/69/2a/cd692a5b77f66b4f5a54919849345f49.jpg';
                                        break;
                                }
                            @endphp
                            <a href="{{ $url }}" target="_blank" class="form-menu panduan"
                                style="font-weight: bold;font-size: 15pt;">Panduan</a>
                            <hr>
                        </div>
                    </div>
                    <div class="col-lg-10">
                        <div class="tempat-form"></div>
                    </div>
                </div>
            </div>
        </div>
        @include('attention')
    </div>
    <div class="modal-page"></div>
    <div>
        @include('bunda.lomba.panduan')
    </div>
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
        $(document).ready(function() {
            $('#sidebar-lomba').addClass('active');
        });

        function formnya(namakelas) {
            $.post("{{ route('bunda-lomba-get_form', ['tahun' => $tahun]) }}", {
                form: namakelas
            }, function(data) {
                if (typeof(data.code) != "undefined" && data.code !== null) {
                    swal('Whooops', data.message, 'error');
                } else {
                    $('.form-menu').removeClass('aktif-form');
                    $('.' + data.kelas).addClass('aktif-form');
                    $('.tempat-form').html(data.content);
                }
            });
        }

        function ganti_tahun() {
            var tahun = $('.tahun_lomba').val();
            window.location = '{{ url('/') }}/bunda/lomba/' + tahun;
        }

        function simpan_berkas(namainput, table, form, typeinput = null) {
            var data = new FormData();
            data.append('berkas', namainput);
            data.append('table', table);
            data.append('form', form);
            // var file = $('input[name='+namainput+']')[0].files[0];
            if (typeinput == null) {
                var file = $('input[name=' + namainput + ']').val();
            } else {
                var file = $('select[name=' + namainput + ']').val();
            }
            data.append('file', file);

            // if(file){
            $.ajax({
                url: '{{ route('bunda-lomba-simpan_berkas', ['tahun' => $tahun]) }}',
                method: 'post',
                data: data,
                contentType: false,
                processData: false,
                success: function(data) {
                    if (data.code == 200) {
                        swal('Success', data.message, 'success');
                        formnya(data.form);
                    } else {
                        swal('Whooops', data.message, 'error');
                    }
                }
            })
            // }else{
            // 	swal('Whooops','File belum dipilih','error');
            // }
        }
    </script>
@endsection
