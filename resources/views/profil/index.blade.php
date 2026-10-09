@extends('layout.index')

@section('additional_styles')
<style>
    tr td {
        text-align:center;
    }
    </style>
@endsection

@section('sidebar')
    @if(\Auth::User()->role == 'dinas')
        @include('dinas/layout/sidebar')
    @elseif(\Auth::User()->role == 'bunda')
        @include('bunda/layout/sidebar')
    @else
        @include('pokja/layout/sidebar')
    @endif
@endsection

@section('content')
<main id="app">
    <!-- Page Content -->
    <!-- User Info -->
    <div class="bg-image bg-image-bottom" style="background-image: url({{asset('assets/media/photos/photo13@2x.jpg')}});">
        <div class="bg-primary-dark-op" style="padding-top: 0px;padding-bottom:0px">
            <div class="content content-full text-center">
                <!-- Avatar -->
                <div class="mb-15">
                    <a class="img-link" href="#">
                        <img class="img-avatar img-avatar96 img-avatar-thumb" src="{{ \Auth::User()->getFotoProfil() ? \Auth::User()->getFotoProfil() : url('assets/media/avatars/avatar15.jpg') }}" alt="">
                    </a>
                </div>
                <!-- END Avatar -->

                <!-- Personal -->
                <h1 class="h3 text-white font-w700 mb-10">{{\Auth::User()->nama}}</h1>
                <h2 class="h5 text-white-op">
                    {{\Auth::User()->getRole() }} - <a class="text-primary-light" href="javascript:void(0)">{{\Auth::User()->username}}</a>
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
        <div class="alert alert-info" v-if="loading">
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
                        <form action="{{route('common-profil-update-profil')}}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group row">
                                <div class="col-md-2">
                                    <div class="form-material">
                                        <input type="email" class="form-control" id="email" name="email" v-model="data.email">
                                        <label for="email">Email</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-material">
                                        <input type="text" class="form-control" id="nama" name="nama" v-model="data.nama">
                                        <label for="nama">Nama</label>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-material">
                                        <select class="form-control" id="kelamin" name="kelamin" required v-model="data.kelamin">
                                            <option value="L">Laki-laki</option>
                                            <option value="P">Perempuan</option>
                                        </select>
                                        <label for="kelamin" class="required">Kelamin</label>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="form-material">
                                        <input type="text" class="form-control" id="alamat" name="alamat" required v-model="data.alamat">
                                        <label for="alamat" class="required">Alamat</label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-2">
                                    <div class="form-material">
                                        <input type="text" class="form-control" id="tempat_lahir" name="tempat_lahir" v-model="data.tempat_lahir">
                                        <label for="tempat_lahir">Tempat Lahir</label>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-material">
                                        <input type="date" class="form-control" id="tgl_lahir" name="tgl_lahir" v-model="data.tgl_lahir">
                                        <label for="tgl_lahir">Tgl Lahir</label>
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <div class="form-material">
                                        <select class="form-control" id="agama" name="agama" required v-model="data.agama">
                                            <option value="Islam">Islam</option>
                                            <option value="Kristen">Kristen</option>
                                            <option value="Protestan">Protestan</option>
                                            <option value="Konghucu">Konghucu</option>
                                            <option value="Hindu">Hindu</option>
                                            <option value="Budha">Budha</option>
                                        </select>
                                        <label for="agama" class="required">Agama</label>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-material">
                                        <input type="number" class="form-control" id="no_telp" name="no_telp" minlength="9" required v-model="data.no_telp">
                                        <label for="no_telp" class="required">No.Telp</label>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-material">
                                        <select class="form-control" id="pendidikan_terakhir" name="pendidikan_terakhir" required v-model="data.pendidikan_terakhir">
                                            @foreach($list_pendidikan as $pendidikan)
                                            <option value="{{ $pendidikan->nama }}">{{ $pendidikan->nama }}</option>
                                            @endforeach
                                        </select>
                                        <label for="pendidikan_terakhir" class="required">Pendidikan Terakhir</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-material">
                                        <input type="text" class="form-control" id="jurusan_pendidikan" name="jurusan_pendidikan" required v-model="data.jurusan_pendidikan">
                                        <label for="jurusan_pendidikan" class="required">Jurusan Pendidikan</label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row" v-if="data.role != 'dinas'">
                                <div class="col-md-2">
                                    <div class="form-material">
                                        <input type="date" class="form-control" id="tgl_sk_penetapan" name="tgl_sk_penetapan" v-model="data.tgl_sk_penetapan">
                                        <label for="tgl_sk_penetapan" class="required">Tgl SK Penetapan</label>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-material">
                                        <input type="text" class="form-control" id="no_sk_penetapan" name="no_sk_penetapan" v-model="data.no_sk_penetapan">
                                        <label for="no_sk_penetapan" class="required">No SK Penetapan</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-material">
                                        <input type="text" class="form-control" id="pejabat_sk_penetapan" name="pejabat_sk_penetapan" v-model="data.pejabat_sk_penetapan">
                                        <label for="pejabat_sk_penetapan" class="required">Nama Pejabat SK Penetapan</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-material">
                                        <p v-if="data.path_sk_penetapan">
                                            <button type="button" class="btn btn-sm btn-outline-primary" @click="lihatFile">
                                                <i class="fa fa-image"></i> Lihat File
                                            </button>
                                            <small @click="data.path_sk_penetapan = null">
                                                <i class="fa fa-trash"></i> Hapus
                                            </small>
                                        </p>
                                        <input type="file" class="form-control" id="file_sk_penetapan" name="file_sk_penetapan" required accept="application/pdf,image/*" v-else>
                                        <label for="file_sk_penetapan" class="required">File SK Penetapan</label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row" v-if="data.role == 'bunda' || data.id_jabatan == 1">
                                <div class="col-md-12">
                                    <div class="form-material">
                                        <textarea id="kata_sambutan" class="form-control" required name="kata_sambutan" rows="5">@{{ data.kata_sambutan }}</textarea>
                                        <label for="kata_sambutan" class="required">Kata Sambutan</label>
                                    </div>
                                </div>
                            </div>

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
                loading:false,
                data:{}
            },
            created:function(){
                $("#sidebar-profil").addClass("active");
                var tmp = '{!! addslashes(json_encode(\Auth::User())) !!}'

                this.data = JSON.parse(tmp.replaceAll("&quot;",'"'))
                // jQuery(function(){Codebase.helpers(['datepicker','select2']);});
            },
            methods:{
                lihatFile:function(){
                    var url = "{{ route('storage',[':path','1']) }}"

                    window.open(url.replace(":path",btoa(this.data.path_sk_penetapan)))
                },
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
                                            url:"{{ route('common-profil-update-foto') }}",
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
