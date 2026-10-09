@extends('layout.index')


@section('additional_styles')
<style>
    tr td {
        text-align:center;
    }
    </style>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">
@endsection
@section('sidebar')
    @include('dinas/layout/sidebar')
@endsection
<title>SiBunda - Laporan Hasil Penilaian Aplikasi</title>
@section('content')
    <div class="content" id="app">
        <h2 class="content-heading" style="padding-top: 0px">Kelola Hasil Penilaian Aplikasi</h2>
        <!-- Dynamic Table Full -->
    <div class="block">
        <div class="block-header block-header-default row no-margin">
            <h3 class="block-title">Hasil Penilaian Aplikasi</h3>
        </div>
        <div class="block-content block-content-full">
            <div class="col-md-4">
                <div class="form-group row">
                    <label class="label-control">Tahun Penilaian</label>
                    <select class="form-control" name="tahun" onchange="data_baru()">
                        <option value="" disabled>-- Tahun Penilaian --</option>
                        @if($survey->count()!=0)
                        @foreach($survey as $s)
                        <option value="{{$s->tahun}}|{{$s->semester}}" @if($s->tahun==$tahun) selected @endif>{{$s->tahun}} semester {{$s->semester}}</option>
                        @endforeach
                        @endif
                    </select>
                </div>
            </div>
            @include('layout.alert')
            <table id="dataTable" class="table table-bordered table-striped">
                <thead>
                    <tr >
                        <th style="width:1%">No</th>
                        <th width="27%">Nama</th>
                        <th>Tingkat</th>
                        <th>Bintang</th>
                        <th>Saran</th>
                        <th>Tgl Pengisian</th>
                    </tr>
                </thead>
                <tbody>
                    
                </tbody>
            </table>
        </div>
    </div>
    @include('dinas.user.modal')
</div>
@endsection

@section('additional_scripts')
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript">
        var table;
        $(document).ready(function(){
            table = $("#dataTable").DataTable({
                dom: 'Bfrtip',
                buttons: [
                    'excel'
                ],
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route("dinas-survey-data") }}?tahun={{$tahun}}',
                    type: 'get',
                },
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
                iDisplayLength: -1,
                columns: [
                    {data: 'DT_RowIndex'},
                    {data: 'nama'},
                    {data: 'tingkat'},
                    {data: 'bintang'},
                    {data: 'saran'},
                    {data: 'tgl_pengisian'},
                ],
            });
        });

        function data_baru(){
            th = $('select[name=tahun]').val();
            table.ajax.url("{{ route("dinas-survey-data") }}?tahun="+th).load();
        }
    </script>
@endsection
