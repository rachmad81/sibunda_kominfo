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
                        <h3 class="block-title"><i class="si si-users mr-5"></i> Ploting Kegiatan Komponen Teknis</h3>
                    </div>
                    <div class="block-content">
                        <form action="{{ route('common-apresiasi-store') }}" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="id" :value="data.id">
                            @csrf
                            <div class="form-group row">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Komponen</th>
                                            <th>Kegiatan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($data as $key => $value)
                                        <tr>
                                            <td>{{ $key+1 }}</td>
                                            <td>{{ $value->komponen }}</td>
                                            <td>
                                                -
                                                <br>
                                                <a class="btn btn-sm btn-outline-primary"
                                                    href="#">
                                                    Tambah Kegiatan
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
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

                // var tmp = '{!! addslashes(json_encode( $data )) !!}'

                // this.data = JSON.parse(tmp.replaceAll("&quot;",'"'))
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
