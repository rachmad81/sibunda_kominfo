@extends('layout.index')


@section('additional_styles')
    <style>
        tr>th>td {
            text-align: center;
        }

        .aktif-form {
            background: #ccc;
        }

        .disabled {
            background: #fff !important;
            color: black;
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
        <h2 class="content-heading" style="padding-top: 0px">G7KIAH ({{ $judul }})</h2>
        <!-- Dynamic Table Full -->
        <div class="block">
            <div class="block-header block-header-default row no-margin">
                <h3 class="block-title">G7KIAH ({{ $judul }})</h3>
            </div>
            <div class="block-content block-content-full">
                @include('layout.alert')
                <div class="row">
                    <div class="col-lg-12" style="overflow: auto">
                        <div>
                            <center>
                                <h1 class="judul" style="display:none;">G7KIAH ({{ $judul }})</h1>
                            </center>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12" style="overflow: auto">
                        <form class="form_pencarian">
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <td>
                                            Cari Satuan Pendidikan
                                        </td>
                                        <td>
                                            <input type="hidden" name="npsn">
                                            <input type="text" name="" class="nama form-control"
                                                placeholder="Cari satuan pendidikan" onkeyup="cari_sekolah(this)">
                                            <a href="javascript:void(0)" class="btn-open btn btn-sm btn-primary"
                                                onclick="set_sekolah('','','','','','')">Ganti Sekolah</a>
                                            <div class="tempat_sekolah"></div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="width: 45%">Nama Satuan (NPSN)</td>
                                        <td style="width: 50%;">
                                            <span class="npsn"></span>
                                        </td>
                                    </tr>
                                    {{-- <tr>
									<td>Nama siswa</td>
									<td>
										<input type="hidden" name="siswa_id">
										<input type="text" name="nama_siswa" class="nama_siswa form-control" placeholder="Cari siswa" onkeyup="cari_siswa(this)">
										<div class="btn-open1 nama_siswa"></div>
										<a href="javascript:void(0)" class="btn-open1 btn btn-sm btn-primary" onclick="set_siswa('','','','','','')">Ganti Siswa</a>
										<div class="tempat_siswa"></div>
									</td>
								</tr> --}}
                                    <tr>
                                        <td>Tahun</td>
                                        <td>
                                            <select class="form-control" name="tahun">
                                                @foreach ($tahun as $t)
                                                    <option>{{ $t }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Bulan</td>
                                        <td>
                                            <select class="form-control" name="bulan">
                                                @for ($i = 0; $i < 12; $i++)
                                                    <option>{{ $i + 1 }}</option>
                                                @endfor
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2">
                                            <a href="javascript:void(0)" class="btn btn-primary form-control"
                                                onclick="tampilkan()">Tampilkan</a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </form>
                        <a href="javascript:void(0)" class="btn btn-primary disabled">Kebiasaan Sekolah</a> Menandakan siswa
                        belum mengisi<br>
                        <a href="javascript:void(0)" class="btn btn-primary">Kebiasaan Rumah</a> Siswa sudah mengisi<br>
                        <a href="javascript:void(0)" class="btn btn-info">Kebiasaan Sekolah</a> Siswa sudah mengisi
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>Kelas</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="wadah_siswa"></tbody>
                        </table>
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
            $('#sidebar-g7kaih').addClass('active');
            $('#sidebar-monitor').addClass('active');
            set_sekolah('', '', '');
            set_siswa('', '', '')
        });

        function set_kunci(kunci) {
            if (kunci == '') {
                $('input[name=npsn]').val('');
                $('.nama').show();
                $('.btn-open').hide();
            } else {
                $('.nama').hide();
                $('.btn-open').show();
            }
        }

        function set_kunci1(kunci) {
            if (kunci == '') {
                $('input[name=siswa_id]').val('');
                $('.nama_siswa').show();
                $('.btn-open1').hide();
            } else {
                $('.nama_siswa').hide();
                $('.btn-open1').show();
            }
        }

        function cari_sekolah(ini) {
            var cari = ini.value;
            if (cari == '') {
                $('.tempat_sekolah').html('');
            } else {
                $.post('{{ route('bunda-g7kaih-cari_sekolah') }}', {
                    cari: cari
                }, function(data) {
                    var html = '';
                    if (data.length > 0) {
                        $.each(data, function(k, v) {
                            html += '<a href="javascript:void(0)" onclick="set_sekolah(\'' + v.npsn +
                                '\',\'' + (btoa(v.nama)) + '\',\'kunci\')">' + v.nama + ' (' + v.npsn +
                                ')</a><br>';
                        })
                    }
                    $('.tempat_sekolah').html(html);
                });
            }
        }

        function set_sekolah(npsn, nama, kunci) {
            $('input[name=npsn]').val(npsn);
            if (nama == '') {
                $('.npsn').html(atob(nama));
            } else {
                $('.npsn').html(atob(nama) + ' (' + npsn + ')');
            }
            $('.nama').val('');
            $('.tempat_sekolah').html('');
            set_kunci(kunci);
        }

        function tampilkan() {
            var npsn = $('input[name=npsn]').val();
            var tahun = $('select[name=tahun]').val();
            var bulan = $('select[name=bulan]').val();

            if (npsn == '') {
                swal('Whooops', 'Cari sekolahnya dulu', 'warning');
            } else {
                $.post('{{ route('bunda-g7kaih-get_siswa') }}', {
                    bulan: bulan,
                    tahun: tahun,
                    npsn: npsn
                }, function(data) {
                    $('.wadah_siswa').html(data.content);
                });
            }
        }

        function set_siswa(siswa_id, nama, kunci) {
            $('input[name=siswa_id]').val(siswa_id);
            $('.nama_siswa').html(nama);
            $('.tempat_siswa').html('');
            set_kunci1(kunci);
        }

        function show(siswa_id, lokasi) {
            var tahun = $('select[name=tahun]').val();
            var bulan = $('select[name=bulan]').val();
            window.open('{{ route('bunda-g7kaih-tampilkan') }}?lokasi=' + lokasi + '&siswa_id=' + siswa_id + '&tahun=' +
                tahun + '&bulan=' + bulan);
        }
    </script>
@endsection
