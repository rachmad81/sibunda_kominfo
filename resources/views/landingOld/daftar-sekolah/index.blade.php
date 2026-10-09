@extends('landing.layout')

@section('additional_styles')
@endsection

@section('content')
    @php
        $jenjang = ['TPA', 'KB', 'TK', 'SPS', 'RA'];
    @endphp
    <!-- Page Content -->

    <!-- Content -->
    <div class="content" style="padding-top: 0px; background-color: transparent;">
        <div class="my-20 text-center">
            <h2 style="margin-bottom: 0px; text-shadow: 3px 3px 6px white;">
                <b><i class="si si-graduation text-muted"></i> Daftar Sekolah PAUD {!! \Auth::check() ? \Auth::User()->getTingkat() : 'Kota Surabaya' !!}</b>
            </h2>
            @if (strlen($cari) > 1)
                <h3 class="h5 text-muted mb-0" style=" text-shadow: 3px 3px 6px white;">
                    Kata kunci pencarian "{{ $cari }}"
                </h3>
            @endif
        </div>
        <div class="row">
            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12" style="margin-bottom: 5px">
                <select class="form-control" name="cari_kecamatan" onchange="get_kelurahan()">
                    <option value="semua">Semua Kecamatan</option>
                    @foreach ($kecamatan as $v)
                        <option @if ($kode_kecamatan == $v->kecamatan_kode) selected @endif value="{{ $v->kecamatan_kode }}">
                            {{ $v->kecamatan_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12" style="margin-bottom: 5px">
                <select class="form-control" name="cari_kelurahan">
                    <option value="semua" selected>Semua Kelurahan</option>
                </select>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12" style="margin-bottom: 5px">
                <select class="form-control" name="cari_jenjang">
                    <option value="semua" selected>Semua Jenjang</option>
                    @for ($i = 0; $i < count($jenjang); $i++)
                        <option {{ $kode_jenjang == $jenjang[$i] ? 'selected' : '' }} value="{{ $jenjang[$i] }}">
                            {{ $jenjang[$i] }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <a href="javascript:void(0)" class="btn btn-secondary" style="line-height: 0" onclick="cari_sekolah()"><i
                        class="fa fa-search"></i> Cari</a>
            </div>
        </div>
        <div style="margin-bottom: 5px">&nbsp;</div>
        <div class="row">
            @if (count($data) > 0)
                @foreach ($data as $key => $value)
                    <div class="col-xs-12 col-md-6 col-xl-4" data-toggle="appear">
                        <!-- Property -->
                        <div class="block block-rounded">
                            <div class="block-content p-0 overflow-hidden">
                                <center>
                                    <a class="img-link" target="_blank"
                                        href="{{ ' https://profilsekolahdispendik.surabaya.go.id/umum/sekolah/detail_sekolah?npsn=' . $value->npsn }}">
                                        <img src="{{ $value->foto_sekolah }}" alt=""
                                            style="max-height: 218px; max-width: 100%; object-fit: contain;">
                                    </a>
                                    <!-- <a class="img-link" target="_blank" href="{{ 'http://profilsekolah.dispendik.surabaya.go.id/profilsekolahlama/umum/sekolah.php?j=' . $value->jenjang . '&npsn=' . $value->npsn }}">
                                        <img src="{{ $value->foto_sekolah }}" alt="" style="max-height: 218px; max-width: 100%; object-fit: contain;">
                                    </a> -->
                                </center>
                            </div>
                            <div class="block-content border-bottom">
                                <h5><small>({{ $value->npsn }})</small></h5>
                                <h4 class="font-size-h5 mb-10">{{ $value->nama }}</h4>
                                <p class="text-muted mb-0">
                                    <i class="fa fa-map-pin"></i> {{ $value->alamat }}<br>
                                    {{ $value->kecamatan->kecamatan_name . ', ' . $value->kelurahan->kelurahan_name }}<br>
                                    <i class="fa fa-phone-square"></i> {{ strlen($value->telp) > 9 ? $value->telp : '-' }}
                                <div class="row">
                                    <div class="col-12">
                                        <button class="btn btn-sm btn-rounded btn-outline-primary" style="line-height: 0;">
                                            <i class="fa fa-group"></i> {{ $value->getJumlahSiswa() }} Peserta Didik
                                        </button>
                                        <button class="btn btn-sm btn-rounded btn-outline-info" style="line-height: 0;">
                                            <i class="fa fa-user-circle-o"></i> {{ $value->getJumlahPegawai() }}
                                            Pegawai/Tendik
                                        </button>
                                    </div>
                                </div>
                                </p>
                            </div>
                            <div class="block-content border-bottom" style="padding:10px 15px">
                                <span class="d-inline d-md-none d-lg-none">
                                    <a class="btn btn-circle btn-primary" target="_blank"
                                        href="{{ 'https://www.google.com/maps/search/?api=1&query=' . $value->lat_long }}">
                                        <i class="fa fa-map-marker"></i>
                                    </a>
                                </span>
                                <!-- DESKTOP MAP -->
                                <span class="d-none d-md-inline">
                                    <a class="btn btn-circle btn-primary" href="javascript:void(0)"
                                        onclick="show_map('https://maps.google.com/maps?q={{ $value->lat_long }}&t=&z=15&ie=UTF8&iwloc=&output=embed','{{ $value->nama }}')">
                                        <i class="fa fa-map-marker"></i>
                                    </a>
                                </span>{{--
                            @if (\Auth::check())
                            <a class="btn btn-circle btn-primary" href="javascript:;" onclick="detail('pd','{{ $value->npsn }}')">
                                <i class="fa fa-user-circle-o"></i>
                            </a>
                            <a class="btn btn-circle btn-primary" href="javascript:;" onclick="detail('ptk','{{ $value->npsn }}')">
                                <i class="fa fa-users"></i>
                            </a>
                            @endif --}}
                                {{-- </p> --}}
                            </div>
                        </div>
                        <!-- END Property -->
                    </div>
                @endforeach
            @else
                <div class="col-lg-12 col-12">
                    <hr>
                    <center>
                        <p style="font-size: 20px;">Tidak ada data sekolah yang cocok</p>
                    </center>
                </div>
            @endif
        </div>
        <div class="row">
            <div class="col-12">
                {{ $data->onEachSide(1)->links() }}
            </div>
        </div>

        <div class="clearfix" style="margin-bottom: 50px"></div>
    </div>

    <div class="modal" id="modalMap" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Lokasi Sekolah (<span class="nama_sekolah"></span>)</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <iframe width="100%" height="300" id="map" src="" frameborder="0" scrolling="no"
                        marginheight="0" marginwidth="0"></iframe>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <div id="template_container">

    </div>
@endsection

@section('additional_js')
    <script type="text/javascript">
        @if (isset($menu_active))
            $('#nav_{{ $menu_active }}').children().addClass('active')
        @endif

        $(document).ready(function() {
            get_kelurahan();
        });

        function show_map(url, nama_sekolah) {
            $('#map').attr('src', url);
            $('.nama_sekolah').html(nama_sekolah);
            $('#modalMap').modal('show');
        }

        function detail(jenis, npsn) {
            var url = '{{ route('common-get-list') }}'
            var nama_modal = ''

            switch (jenis) {
                case 'pd':
                    nama_modal = 'modalPD'
                    break

                case 'ptk':
                    nama_modal = 'modalPTK'
                    break
            }

            $.ajax({
                url: url,
                type: 'get',
                data: {
                    jenis: jenis,
                    npsn: npsn,
                },
                success: function(data) {
                    $('#template_container').html(data.view)
                    $('#' + nama_modal).modal('show')
                },
                error: function(exception) {
                    alert('gagal! pesan : ' + exception.statusText)
                }
            });
        }

        function get_kelurahan() {
            var kec = $('select[name=cari_kecamatan]').val();

            $.get("{{ route('landing', ['halaman' => 'get_kelurahan']) }}", {
                kode_kecamatan: kec
            }, function(data) {
                var html = '<option value="semua">Semua Kelurahan</option>';
                if (data.length != 0) {
                    $.each(data, function(k, v) {
                        if (v.kelurahan_kode == '{{ $kode_kelurahan }}') {
                            html += '<option selected value="' + v.kelurahan_kode + '">' + v
                                .kelurahan_name + '</option>';
                        } else {
                            html += '<option value="' + v.kelurahan_kode + '">' + v.kelurahan_name +
                                '</option>';
                        }
                    });
                }

                $('select[name=cari_kelurahan]').html(html);
            });
        }

        function cari_sekolah() {
            var kec = $('select[name=cari_kecamatan]').val();
            var kel = $('select[name=cari_kelurahan]').val();
            var jenjang = $('select[name=cari_jenjang]').val();

            window.location = "{{ Request::url() }}?kecamatan=" + kec + "&kelurahan=" + kel + "&jenjang=" + jenjang +
                "&cari={{ $cari }}";
        }
    </script>
@endsection
