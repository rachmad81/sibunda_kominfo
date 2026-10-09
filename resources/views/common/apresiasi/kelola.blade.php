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
        <!-- Colleagues -->

        <div class="row">
            <div class="col-lg-12">
                <!-- Static Labels -->
                <div class="block">
                    <div class="block-header block-header-default">
                        <h3 class="block-title"><i class="si si-users mr-5"></i> {{ ucwords($aksi) }} Pengajuan Apresiasi</h3>
                    </div>
                    <div class="block-content">
                        @if($aksi == 'tambah')
                        <form action="{{ route('common-apresiasi-store') }}" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="id_bunda" :value="data.id_bunda">
                        @else
                        <form action="{{ route('common-apresiasi-update') }}" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="id" :value="data.id">
                        @endif
                            @csrf
                            <div class="form-group row">
                                <div class="col-md-1">
                                    <div class="form-material">
                                        <input type="number" class="form-control" id="tahun" required name="tahun" placeholder="{{ date('Y') }}" v-model="data.tahun" min="2022">
                                        <label for="tahun" class="required">Tahun</label>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-material">
                                        <input type="email" class="form-control" id="email" name="email" v-model="data.email" required>
                                        <label for="email" class="required">Email</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-material">
                                        <input type="text" class="form-control" id="nama" name="nama" v-model="data.nama" required>
                                        <label for="nama" class="required">Nama</label>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-material">
                                        <input type="number" class="form-control" id="no_telp" name="no_telp" minlength="9" v-model="data.no_telp" required>
                                        <label for="no_telp" class="required">No.Telp</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-material">
                                        <input type="text" class="form-control" id="alamat" name="alamat" v-model="data.alamat" required>
                                        <label for="alamat" class="required">Alamat</label>
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
                data:{},
                aksi: '{{ $aksi }}',
            },
            created:function(){
                $("#sidebar-apresiasi").addClass("active");

                var tmp = '{!! addslashes(json_encode( $data )) !!}'

                this.data = JSON.parse(tmp.replaceAll("&quot;",'"'))

                if (!this.data.tahun) {
                    this.data.tahun = '{{ date("Y") }}'
                }
            },
            methods:{
                
            },
            computed:{

            },
            watch : {
            },
        });

    </script>
@endsection
