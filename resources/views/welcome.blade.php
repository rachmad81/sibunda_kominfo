@extends('layout.index')

@section('additional_styles')
<style type="text/css">
    .list-panel{
        padding-inline-start:0px; 
        padding-top: 10px; 
        list-style-type: none; 
        display: flex;
    }

    .nav-item .nav-link:hover, .nav-item .small:hover{
        color: #f27474;
    }

    .nav-item .nav-link, .small{
        color: #f0f0f0;
    }
</style>
@endsection
@section('sidebar')
{{--
    @if(\Auth::User()->role == 'admin')
        @include('sekolah/layout/sidebar')
    @elseif(\Auth::User()->role == 'bunda')
        @include('sekolah/layout/sidebar')
    @else
        @include('kadis/layout/sidebar')
    @endif
    <div class="content-side content-side-full">
        <ul class="nav-main">
            <li class="nav-main-heading" style="padding-top: 6px">
                <span class="sidebar-mini-visible">Menu</span>
                <span class="sidebar-mini-hidden">Menu</span>
            </li>
        </ul>
    </div>
--}}
@endsection
@section('content')
<main id="app">
    <!-- Page Content -->
    <!-- Bottom Navbar -->
    <nav class="navbar navbar-dark bg-primary navbar-expand fixed-bottom d-md-none d-lg-none d-xl-none p-0">
        <ul class="navbar-nav nav-justified w-100 list-panel">
            <li class="nav-item">
                <a href="#" class="nav-link text-center">
                    <svg width="1.5em" height="1.5em" viewBox="0 0 16 16" class="bi bi-house" fill="currentColor"
                        xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd"
                            d="M2 13.5V7h1v6.5a.5.5 0 0 0 .5.5h9a.5.5 0 0 0 .5-.5V7h1v6.5a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 2 13.5zm11-11V6l-2-2V2.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5z" />
                        <path fill-rule="evenodd"
                            d="M7.293 1.5a1 1 0 0 1 1.414 0l6.647 6.646a.5.5 0 0 1-.708.708L8 2.207 1.354 8.854a.5.5 0 1 1-.708-.708L7.293 1.5z" />
                    </svg>
                    <span class="small d-block">Home</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link text-center">
                    <svg width="1.5em" height="1.5em" viewBox="0 0 16 16" class="bi bi-search" fill="currentColor"
                        xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd"
                            d="M10.442 10.442a1 1 0 0 1 1.415 0l3.85 3.85a1 1 0 0 1-1.414 1.415l-3.85-3.85a1 1 0 0 1 0-1.415z" />
                        <path fill-rule="evenodd"
                            d="M6.5 12a5.5 5.5 0 1 0 0-11 5.5 5.5 0 0 0 0 11zM13 6.5a6.5 6.5 0 1 1-13 0 6.5 6.5 0 0 1 13 0z" />
                    </svg>
                    <span class="small d-block">Search</span>
                </a>
            </li>
            <li class="nav-item dropup">
                <a href="#" class="nav-link text-center" role="button" id="dropdownMenuProfile" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" >
                    <svg width="1.5em" height="1.5em" viewBox="0 0 16 16" class="bi bi-person" fill="currentColor"
                        xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd"
                            d="M10 5a2 2 0 1 1-4 0 2 2 0 0 1 4 0zM8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm6 5c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4zm-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664h10z" />
                    </svg>
                    <span class="small d-block">Profile</span>
                </a>
                <!-- Dropup menu for profile -->
                <div class="dropdown-menu" aria-labelledby="dropdownMenuProfile">
                    <a class="dropdown-item" href="#">Edit Profile</a>
                    <a class="dropdown-item" href="#">Notification</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="#">Logout</a>
                </div>
            </li>
        </ul>
    </nav>
    <!-- User Info -->
    <div class="bg-image bg-image-bottom" style="background-image: url({{asset('assets/media/photos/photo13@2x.jpg')}});">
        <div class="bg-primary-dark-op" style="padding-top: 0px;padding-bottom:0px">
            <div class="content content-full text-center">
                <!-- Avatar -->
                <div class="mb-15">
                    <a class="img-link" href="#">
                        <img class="img-avatar img-avatar96 img-avatar-thumb" src="{{ url('assets/media/avatars/avatar15.jpg') }}" alt="">
                    </a>
                </div>
                <!-- END Avatar -->

                <!-- Personal -->
                <h1 class="h3 text-white font-w700 mb-10">-</h1>
                <h2 class="h5 text-white-op">
                    - - <a class="text-primary-light" href="javascript:void(0)">-</a>
                </h2>
                <!-- END Personal -->

                <!-- Actions -->
                <button type="button" class="btn btn-rounded btn-sm btn-alt-success mb-5" @click="updateFotoProfil">
                    <i class="fa fa-upload mr-5"></i> Ganti Foto
                </button>
                <!-- END Actions -->
            </div>
        </div>
    </div>
    <!-- END User Info -->

    <!-- Main Content -->
    <div class="content">
        @if(session()->has('alert-success'))
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
        <div class="alert alert-info" v-if="is_load_data">
            <i class="fa fa-spinner fa-spin"></i> Sedang meng-sinkronkan data..
        </div>
        <!-- Colleagues -->

        <div class="row">
            <div class="col-lg-12">
                <!-- Static Labels -->
                <div class="block">
                    <div class="block-header block-header-default">
                        <h3 class="block-title"><i class="si si-users mr-5"></i> Profil Pengguna</h3>
                    </div>
                    <div class="block-content">
                        <form action="#" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group row">
                                <div class="col-md-4">
                                    <div class="form-material">
                                        <input type="text" class="form-control" id="nama" name="nama" readonly>
                                        <label for="nama">Nama Perusahaan</label>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="form-material">
                                        <input type="text" class="form-control" id="alamat" name="alamat" required >
                                        <label for="alamat" class="required">Alamat</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-material">
                                        <input type="number" class="form-control" id="no_telp" name="no_telp" minlength="9" required>
                                        <label for="no_telp" class="required">No.Telp</label>
                                    </div>
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
        $(document).ready(function(){
            var table = $("#id_datatable").DataTable({
                dom:   "<'row'<'col-md-6'l><'col-md-6'B>>" +
                        "<'row'<'col-md-12'f>>" +
                        "<'row'<'col-md-12't>><'row'<'col-md-12'ip>>",
                buttons: [
                ],
                scrollX:true,
            });
        });
    </script>
    <script>
         let vm = new Vue({
            el:"#app",
            data:{
                is_load_data:false
            },
            created:function(){
                $("#sidebar-profil").addClass("active");

                // jQuery(function(){Codebase.helpers(['datepicker','select2']);});
            },
            methods:{
                updateFotoProfil:async function(){
                    var form_data = new FormData();

                    const {
                        value: file
                        } = await Swal({
                                        title: 'Pilih Foto',
                                        imageUrl: '{{asset("logo/image-icon.png")}}',
                                        imageHeight:100,
                                        imageWidth: 100,
                                        text:"Pilih Foto yang ingin dijadikan Foto Profil (Max 500 Kb)",
                                        input: 'file',
                                        inputAttributes: {
                                            'accept': 'image/*',
                                            'aria-label': 'Maksimal ukuran 500x500 atau 500kb'
                                            },
                                        animation: "slide-from-top",
                                        confirmButtonText:"Unggah",
                                        showCancelButton: true,
                                        cancelButtonText:"Batal"
                                    })

                                    if (file) {
                                        form_data.append('file', file)
                                        Swal.fire({
                                            imageUrl: 'https://miro.medium.com/max/1600/1*CsJ05WEGfunYMLGfsT2sXA.gif',
                                            imageHeight:90,
                                            imageWidth: 250,
                                            imageAlt: 'Mengunggah',
                                            title:'Mengunggah',
                                            showConfirmButton: false,
                                        })
                                        $.ajax({
                                            url:"#",
                                            method:"POST",
                                            data:form_data,
                                            dataType:'JSON',
                                            contentType: false,
                                            cache: false,
                                            processData: false,
                                            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                                            success: function(data) {
                                                if(data.status == 'success'){
                                                    Swal.fire({
                                                      title:'Berhasil!',
                                                      text:'Berhasil Mengunggah berkas!',
                                                      type:'success',
                                                      showConfirmButton:true
                                                    }).then((result) => {
                                                        if (result) {
                                                            window.location.reload()
                                                        }
                                                    })
                                                }
                                                else{
                                                    Swal.fire(
                                                      'Error!',
                                                      'Hubungi Developer dengan pesan : !'+data.message,
                                                      'error'
                                                    )
                                                }
                                            },
                                            error: function (reject) {
                                                if( reject.status === 422 ) {
                                                    var msg = ''
                                                    var errors = $.parseJSON(reject.responseText);
                                                    $.each(errors, function (key, val) {
                                                        if (key === 'errors') {
                                                            msg += val['file']+', '
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
                                    }
                                    else{
                                        Swal.fire(
                                          'Peringatan!',
                                          'Pilih foto terlebih dahulu!',
                                          'warning'
                                        )
                                    }
                }
            },
            computed:{

            },
            watch : {
            },
        });

    </script>
@endsection
