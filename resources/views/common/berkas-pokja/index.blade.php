@extends('layout.index')

@section('additional_styles')
    <style>
        tr td {
            text-align: center;
        }
    </style>
@endsection

@section('sidebar')
    @if (\Auth::User()->role == 'dinas')
        @include('dinas/layout/sidebar')
    @elseif(\Auth::User()->role == 'bunda')
        @include('bunda/layout/sidebar')
    @elseif(in_array(\Auth::User()->role, ['k3_paud', 'paud_sd', 'gugus', 'mitra']))
        @include('k3_paud_sd/layout/sidebar')
    @else
        @include('pokja/layout/sidebar')
    @endif
@endsection

@section('content')
    <main id="app">
        <!-- Page Content -->
        <!-- User Info -->
        <div class="bg-image bg-image-bottom"
            style="background-image: url({{ asset('assets/media/photos/photo13@2x.jpg') }});">
            <div class="bg-primary-dark-op" style="padding-top: 0px;padding-bottom:0px">
                <div class="content content-full text-center">
                    <!-- Avatar -->
                    <div class="mb-15">
                        <a class="img-link" href="#">
                            <img class="img-avatar img-avatar96 img-avatar-thumb" src="{{ asset('logo/logo-sibunda.png') }}"
                                alt="">
                        </a>
                    </div>
                    <!-- END Avatar -->

                    <!-- Personal -->
                    <h1 class="h3 text-white font-w700 mb-10 text-uppercase">{{ \Auth::User()->getNamaProfil() }}</h1>
                    <h2 class="h5 text-white-op">
                        {{ \Auth::User()->getRole() }} - <a class="text-primary-light"
                            href="javascript:void(0)">{!! str_replace('<br>', ' ', \Auth::User()->getTingkat()) !!}</a>
                    </h2>
                    <!-- END Personal -->

                    <!-- Actions -->
                    <!-- <button type="button" class="btn btn-rounded btn-sm btn-alt-success mb-5" @click="updateFotoProfil">
                                                                                        <i class="fa fa-upload mr-5"></i> Ganti Foto
                                                                                    </button> -->
                    <!-- END Actions -->
                </div>
            </div>
        </div>
        <!-- END User Info -->

        <!-- Main Content -->
        <div class="content">
            @if (session()->has('alert-success'))
                <div class="alert alert-success">
                    {{ session()->get('alert-success') }}
                </div>
            @elseif(session()->has('alert-fail'))
                <div class="alert alert-danger">
                    {{ session()->get('alert-fail') }}
                </div>
            @elseif(session()->has('alert-warning'))
                <div class="alert alert-warning">
                    {{ session()->get('alert-warning') }}
                </div>
            @endif
            <div class="alert alert-info" v-if="loading">
                <i class="fa fa-spinner fa-spin"></i> Sedang meng-sinkronkan data..
            </div>
            <!-- Colleagues -->

            <div class="row">
                <div class="col-lg-12">
                    <!-- Static Labels -->
                    <div class="block">
                        <div class="block-header block-header-default">
                            @php
                                switch (Auth::getUser()->role) {
                                    case 'k3_paud':
                                        $judul_besar = 'Berkas SK K3 PAUD';
                                        $judul_kecil = 'Berkas SK K3 PAUD';
                                        break;
                                    case 'paud_sd':
                                        $judul_besar = 'Berkas SK Forum PAUD SD';
                                        $judul_kecil = 'Berkas SK Forum PAUD SD';
                                        break;
                                    case 'mitra':
                                        $judul_besar = 'Berkas SK Mitra';
                                        $judul_kecil = 'Berkas SK Mitra';
                                        break;

                                    default:
                                        $judul_besar = 'Berkas SK Bunda & Pokja';
                                        $judul_kecil = 'SK Bunda';
                                        break;
                                }
                            @endphp
                            <h3 class="block-title"><i class="si si-users mr-5"></i> {{ $judul_besar }}</h3>
                        </div>
                        <div class="block-content">
                            <form action="{{ route('common-berkas-store') }}" method="post" enctype="multipart/form-data">
                                @csrf
                                <p class="row p-10 bg-primary-light" style="margin-bottom:0px"><b>{{ $judul_kecil }}</b>
                                </p>
                                <div class="form-group row bg-primary-lighter" style="padding-bottom: 10px;">
                                    <div class="col-md-2">
                                        <div class="form-material">
                                            <input type="date" class="form-control" id="tgl_sk_penetapan_bunda"
                                                name="tgl_sk_penetapan_bunda"
                                                value="{{ $data ? $data->tgl_sk_penetapan_bunda : '' }}">
                                            <label for="tgl_sk_penetapan_bunda" class="required">Tgl SK Penetapan</label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-material">
                                            <input type="text" class="form-control" id="no_sk_penetapan_bunda"
                                                name="no_sk_penetapan_bunda"
                                                value="{{ $data ? $data->no_sk_penetapan_bunda : '' }}">
                                            <label for="no_sk_penetapan_bunda" class="required">No SK Penetapan</label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-material">
                                            <input type="text" class="form-control" id="pejabat_sk_penetapan_bunda"
                                                name="pejabat_sk_penetapan_bunda"
                                                value="{{ $data ? $data->pejabat_sk_penetapan_bunda : '' }}">
                                            <label for="pejabat_sk_penetapan_bunda" class="required">Nama Pejabat SK
                                                Penetapan</label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-material">
                                            <p v-if="path_sk_penetapan_bunda != null">
                                                <button type="button" class="btn btn-sm btn-outline-primary"
                                                    @click="lihatFile('path_sk_penetapan_bunda')">
                                                    <i class="fa fa-image"></i> Lihat File
                                                </button>
                                                <small @click="path_sk_penetapan_bunda = null">
                                                    <i class="fa fa-trash"></i> Hapus
                                                </small>
                                            </p>
                                            <input v-else type="file" class="form-control" id="file_sk_penetapan_bunda"
                                                name="file_sk_penetapan_bunda" required accept="application/pdf,image/*">
                                            <label for="file_sk_penetapan_bunda" class="required">File SK Penetapan</label>
                                        </div>
                                    </div>
                                </div>
                                <hr style="margin: 0px">
                                <hr style="margin: 0px">
                                <hr style="margin-top: 0px">

                                @if (!in_array(Auth::getUser()->role, ['k3_paud', 'paud_sd', 'mitra']))
                                    <p class="row p-10 bg-primary-light" style="margin-bottom:0px"><b>SK POKJA</b></p>
                                    <div class="form-group row bg-primary-lighter" style="padding-bottom: 10px;">
                                        <div class="col-md-2">
                                            <div class="form-material">
                                                <input type="date" class="form-control" id="tgl_sk_penetapan_pokja"
                                                    name="tgl_sk_penetapan_pokja"
                                                    value="{{ $data ? $data->tgl_sk_penetapan_pokja : '' }}">
                                                <label for="tgl_sk_penetapan_pokja" class="required">Tgl SK
                                                    Penetapan</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-material">
                                                <input type="text" class="form-control" id="no_sk_penetapan_pokja"
                                                    name="no_sk_penetapan_pokja"
                                                    value="{{ $data ? $data->no_sk_penetapan_pokja : '' }}">
                                                <label for="no_sk_penetapan_pokja" class="required">No SK
                                                    Penetapan</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-material">
                                                <input type="text" class="form-control"
                                                    id="pejabat_sk_penetapan_pokja" name="pejabat_sk_penetapan_pokja"
                                                    value="{{ $data ? $data->pejabat_sk_penetapan_pokja : '' }}">
                                                <label for="pejabat_sk_penetapan_pokja" class="required">Nama Pejabat SK
                                                    Penetapan</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-material">
                                                <p v-if="path_sk_penetapan_pokja != null">
                                                    <button type="button" class="btn btn-sm btn-outline-primary"
                                                        @click="lihatFile('path_sk_penetapan_pokja')">
                                                        <i class="fa fa-image"></i> Lihat File
                                                    </button>
                                                    <small @click="path_sk_penetapan_pokja = null">
                                                        <i class="fa fa-trash"></i> Hapus
                                                    </small>
                                                </p>
                                                <input v-else type="file" class="form-control"
                                                    id="file_sk_penetapan_pokja" name="file_sk_penetapan_pokja" required
                                                    accept="application/pdf,image/*">
                                                <label for="file_sk_penetapan_pokja" class="required">File SK
                                                    Penetapan</label>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <small>
                                    <label style="color: red">*</label>) Wajib diisi!
                                </small>
                                <div class="form-group row">
                                    <div class="col-md-9">
                                        <button type="submit" class="btn btn-alt-primary">Submit</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- END Colleagues -->

        </div>
        <!-- END Main Content -->
        <!-- END Page Content -->

    </main>
    <!-- END Main Container -->
@endsection

@section('additional_scripts')
    <script type="text/javascript">
        $(document).ready(function() {
            var table = $("#id_datatable").DataTable({
                dom: "<'row'<'col-md-6'l><'col-md-6'B>>" +
                    "<'row'<'col-md-12'f>>" +
                    "<'row'<'col-md-12't>><'row'<'col-md-12'ip>>",
                buttons: [],
                scrollX: true,
            });
        });
    </script>
    <script>
        let vm = new Vue({
            el: "#app",
            data: {
                loading: false,
                data: {},
                path_sk_penetapan_bunda: null,
                path_sk_penetapan_pokja: null
            },
            created: function() {
                $("#sidebar-berkas").addClass("active");
                // jQuery(function(){Codebase.helpers(['datepicker','select2']);});
                @if ($data && $data->path_sk_penetapan_bunda)
                    this.path_sk_penetapan_bunda = '{{ $data->path_sk_penetapan_bunda }}'
                @endif

                @if ($data && $data->path_sk_penetapan_pokja)
                    this.path_sk_penetapan_pokja = '{{ $data->path_sk_penetapan_pokja }}'
                @endif
            },
            methods: {
                lihatFile: function(type) {
                    var url = "{{ route('storage', [':path', '1']) }}"
                    switch (type) {
                        case 'path_sk_penetapan_bunda':
                            url = url.replace(":path", btoa(this.path_sk_penetapan_bunda));
                            break
                        case 'path_sk_penetapan_pokja':
                            url = url.replace(":path", btoa(this.path_sk_penetapan_pokja));
                            break
                    }
                    console.log(url);

                    window.open(url)
                },
                updateFotoProfil: async function() {
                    var form_data = new FormData();

                    const {
                        value: file
                    } = await Swal({
                        title: 'Pilih Foto',
                        imageUrl: '{{ asset('logo/image-icon.png') }}',
                        imageHeight: 100,
                        imageWidth: 100,
                        text: "Pilih Foto yang ingin dijadikan Foto Profil (Max 500 Kb)",
                        input: 'file',
                        inputAttributes: {
                            'accept': 'image/*',
                            'aria-label': 'Maksimal ukuran 500x500 atau 500kb'
                        },
                        animation: "slide-from-top",
                        confirmButtonText: "Unggah",
                        showCancelButton: true,
                        cancelButtonText: "Batal"
                    })

                    if (file) {
                        form_data.append('file', file)
                        Swal.fire({
                            imageUrl: 'https://miro.medium.com/max/1600/1*CsJ05WEGfunYMLGfsT2sXA.gif',
                            imageHeight: 90,
                            imageWidth: 250,
                            imageAlt: 'Mengunggah',
                            title: 'Mengunggah',
                            showConfirmButton: false,
                        })
                        $.ajax({
                            url: "{{ route('common-profil-update-foto') }}",
                            method: "POST",
                            data: form_data,
                            dataType: 'JSON',
                            contentType: false,
                            cache: false,
                            processData: false,
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(data) {
                                if (data.status == 'success') {
                                    Swal.fire({
                                        title: 'Berhasil!',
                                        text: 'Berhasil Mengunggah berkas!',
                                        type: 'success',
                                        showConfirmButton: true
                                    }).then((result) => {
                                        if (result) {
                                            window.location.reload()
                                        }
                                    })
                                } else {
                                    Swal.fire(
                                        'Error!',
                                        'Hubungi Developer dengan pesan : !' + data.message,
                                        'error'
                                    )
                                }
                            },
                            error: function(reject) {
                                if (reject.status === 422) {
                                    var msg = ''
                                    var errors = $.parseJSON(reject.responseText);
                                    $.each(errors, function(key, val) {
                                        if (key === 'errors') {
                                            msg += val['file'] + ', '
                                        }
                                    });
                                    Swal.fire(
                                        'Error!',
                                        msg,
                                        'error'
                                    )
                                }
                            }
                        })
                    } else {
                        Swal.fire(
                            'Peringatan!',
                            'Pilih foto terlebih dahulu!',
                            'warning'
                        )
                    }
                }
            },
            computed: {

            },
            watch: {},
        });
    </script>
@endsection
