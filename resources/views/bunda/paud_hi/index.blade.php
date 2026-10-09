@extends('layout.index')


@section('additional_styles')
<style>
    tr > th, td {
        text-align:center;
    }
</style>
@endsection
@section('sidebar')
@include('bunda/layout/sidebar')
@endsection

@section('content')
<div class="content" id="app">
    <h2 class="content-heading" style="padding-top: 0px">Kelola PAUD HI</h2>
    <!-- Dynamic Table Full -->
    <div class="block">
        <div class="block-header block-header-default row no-margin">
            <h3 class="block-title">Daftar Sekolah PAUD</h3>
        </div>
        <div class="block-content block-content-full">
            @include('layout.alert')
            <div class="row">
                <div class="col-lg-1">
                    <label>Kecamatan</label>
                </div>
                <div class="col-lg-4">
                    <select name="kecamatan" onchange="get_data();get_kelurahan()" @if(in_array(Auth::getUser()->tingkat,['kecamatan','kelurahan'])) disabled @endif>
                        <option @if($kode_kecamatan=='') selected @endif value="semua">Semua Kecamatan</option>
                        @foreach($kecamatan as $kec)
                        <option @if($kode_kecamatan==$kec->kecamatan_kode) selected @endif value="{{$kec->kecamatan_kode}}">{{$kec->kecamatan_dispenduk}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-1">
                    <label>Kelurahan</label>
                </div>
                <div class="col-lg-4">
                    <select name="kelurahan" onchange="get_data()" @if(in_array(Auth::getUser()->tingkat,['kelurahan'])) disabled @endif>
                        <option @if($kode_kelurahan=='') selected @endif value="semua">Semua Kelurahan</option>
                        @foreach($kelurahan as $kec)
                        <option @if($kode_kelurahan==$kec->kelurahan_kode) selected @endif value="{{$kec->kelurahan_kode}}">{{$kec->kelurahan_dispenduk}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-1">
                    <label>Jenjang</label>
                </div>
                <div class="col-lg-4">
                    @php
                    $get_jenjang = ['KB','TK','SPS','TPA'];
                    @endphp
                    <select name="jenjang" onchange="get_data()">
                        <option @if($jenjang=='') selected @endif value="semua">Semua Jenjang</option>
                        @foreach($get_jenjang as $j)
                        <option @if($jenjang==$j) selected @endif value="{{$j}}">{{$j}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-1">
                    <label>Tahun</label>
                </div>
                <div class="col-lg-4">
                    <select name="tahun" onchange="get_data()">
                        <option selected value="">.. Pilih Tahun ..</option>
                        @for($i=2023;$i<=date('Y');$i++)
                            <option @if ($tahun == $i) selected @endif value="{{ $i }}">{{ $i }}</option>
                        @endfor
                        {{-- @foreach($thn as $th)
                        <option @if($tahun==$th->th) selected @endif value="{{$th->th}}">{{$th->th}}</option>
                        @endforeach --}}
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12" style="overflow: auto;">
                    <table id="dataTable" class="table table-bordered table-striped">
                        <thead>
                            <tr >
                                <th style="width:1%">No</th>
                                <th>NPSN</th>
                                <th>Nama Sekolah</th>
                                <th>Alamat</th>
                                <th>Kecamatan</th>
                                <th>Kelurahan</th>
                                <th>Jumlah Pegawai</th>
                                <th>Jumlah Siswa</th>
                                <th>Status Pengajuan</th>
                                <th>Tgl pengisian instrumen</th>
                                <th>Jenjang</th>
                                <th width="4%">Nilai Instrumen</th>
                                <th width="5%">Cetak</th>
                                <th width="30%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @include('attention')
</div>
<div class="modal-page"></div>
@endsection

@section('additional_scripts')
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
<style type="text/css">
    .modal { overflow: auto !important; }
    .dt-left{
        text-align: left;
    }
</style>
<script type="text/javascript">
    var dt_table;
    $(document).ready(function(){
        $('#sidebar-paud').addClass('active');
        var tahun = $('select[name=tahun]').val();
        var kecamatan = $('select[name=kecamatan]').val();
        var kelurahan = $('select[name=kelurahan]').val();
        var jenjang = $('select[name=jenjang]').val();
        dt_table = $("#dataTable").DataTable({
            dom:   "<'row'<'col-md-6'l><'col-md-6'B>>" +
            "<'row'<'col-md-12'f>>" +
            "<'row'<'col-md-12't>><'row'<'col-md-12'ip>>",
            buttons: [
            {
                extend: 'excel',
                title: 'judul',
                filename: function(){
                    judul = $('.judul').html()+'_{{date('YmdHi')}}';
                    return judul;
                }
            },
            {
                extend: 'print',
                title: function(){
                    judul = $('.judul').html();
                    return judul;
                },
                filename: function(){
                    judul = $('.judul').html()+'_{{date('YmdHi')}}';
                    return judul;
                }
            },
            ],
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
            processing: true,
            ajax: '{!! route('bunda-paud_hi-get_data') !!}?tahun='+tahun+'&kec='+kecamatan+'&kel='+kelurahan+'&jenjang='+jenjang,
            columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'npsn', name: 's.npsn' },
            { data: 'nama', name: 's.nama' },
            { data: 'alamat', name: 's.alamat' },
            { data: 'kecamatan_dispenduk', name: 'kec.kecamatan_dispenduk' },
            { data: 'kelurahan_dispenduk', name: 'kel.kelurahan_dispenduk' },
            { data: 'jumlah_gtk', name: 'jumlah_gtk' },
            { data: 'jumlah_pd', name: 'jumlah_pd' },
            { data: 'status_pengajuan', name: 'status_pengajuan' },
            { data: 'created_at', name: 'created_at' },
            { data: 'jenjang', name: 's.jenjang' },
            { data: 'score', name: 'score'},
            { data: 'cetak', name: 'cetak'},
            { data: 'aksi', name: 'aksi', className: 'dt-left'},
            ]
        });

        @if(Auth::getuser()->tingkat=='kecamatan')
        get_kelurahan();
        @endif
    });

    function get_data(){
        $('tbody').html('<tr><td colspan="11"><h4>Harap tunggu.....</h4></td></tr>');
        var tahun = $('select[name=tahun]').val();
        var kecamatan = $('select[name=kecamatan]').val();
        var kelurahan = $('select[name=kelurahan]').val();
        var jenjang = $('select[name=jenjang]').val();
        dt_table.ajax.url("{!! route('bunda-paud_hi-get_data') !!}?tahun="+tahun+"&kec="+kecamatan+"&kel="+kelurahan+"&jenjang="+jenjang).load();
    }

    function modal_hi(id,tahun,kategori){
        var data = {
            id:id,
            tahun:tahun,
            kategori:kategori,
        };
        $.post("{{route('bunda-paud_hi-modal')}}",data,function(data){
            $('.modal-page').html(data.content);
        });
    }

    function tolak(){
        Swal.fire({
            type: 'warning',
            title: 'Apakah kamu yakin akan menolak?',
            showConfirmButton: true,
            showCancelButton: true,
        }).then((result) =>{
            if(result.value){
                var alasan = $('textarea[name=alasan]').val();
                var id = $('input[name=id_pengajuan]').val();
                var data = {
                    id:id,
                    alasan:alasan,
                    status:'Tolak',
                };

                if(alasan.length<15){
                    swal('Whooops','Minimal panjang 15 karakter','error');
                }else{
                    $.post("{{route('bunda-paud_hi-tolak')}}",data,function(data){
                        if(data.code=='200'){
                            swal('Success','Berhasil disimpan','success');
                            $('.modal-backdrop').removeClass('modal-backdrop');
                            $('.modal-page').html('');
                            get_data();
                        }else{
                            swal('Whoooops','Gagal menyimpan','error');
                        }
                    });
                }

            }
        })
    }

    function get_kelurahan(){
        var kecamatan = $('select[name=kecamatan]').val();
        $.post("{{url('bunda/umum/get_kelurahan')}}",{id:kecamatan},function(data){
            var html = '<option value="semua"> Semua Kelurahan</option>';
            $.each(data,function(k,v){
                html+= '<option value="'+v.kelurahan_kode+'">'+v.kelurahan_dispenduk+'</option>';
            });

            $('select[name=kelurahan]').html(html);
        });
    }

    function setujui(id){
        Swal.fire({
            type: 'warning',
            title: 'Apakah kamu yakin akan menyetujui pengajuan?',
            showConfirmButton: true,
            showCancelButton: true,
        }).then((result) =>{
            if(result.value){
                var data = {
                    id:id,
                };

                $.post("{{route('bunda-paud_hi-setujui')}}",data,function(data){
                    if(data.code=='200'){
                        swal('Success','Berhasil disimpan','success');
                        $('.modal-backdrop').removeClass('modal-backdrop');
                        $('.modal-page').html('');
                        get_data();
                    }else{
                        swal('Whoooops','Gagal menyimpan','error');
                    }
                });
            }
        })
    }

    function riwayat_catatan_pengajuan(id){
        $.post("{{route('bunda-paud_hi-riwayat_catatan_pengajuan')}}",{id:id},function(data){
            $('.modal-page').html(data.content);
        });
    }
</script>
<script>
</script>
@endsection
