@extends('layout.index')


@section('additional_styles')
    <style>
        tr>th,
        td {
            text-align: center;
        }
    </style>
@endsection

@section('sidebar')
    @if (\Auth::User()->role == 'dinas')
        @include('dinas/layout/sidebar')
    @elseif(\Auth::User()->role == 'bunda')
        @include('bunda/layout/sidebar')
    @else
        @include('pokja/layout/sidebar')
    @endif
@endsection

@section('content')
    <div class="content" id="app">
        <h2 class="content-heading" style="padding-top: 0px">Materi</h2>
        <!-- Dynamic Table Full -->
        <div class="block">
            <div class="block-header block-header-default row no-margin">
                <h3 class="block-title">Daftar Materi</h3>
            </div>
            <div class="block-content block-content-full">
                @include('layout.alert')
                <table id="dataTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th style="width:1%">No</th>
                            <th>Judul Materi</th>
                            <th>Link</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($materis as $key => $materi)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $materi->nama }}</td>
                                <td>
                                    <a href="{{ $materi->link }}" class="btn btn-sm" target="_blank">
                                        Download
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('additional_scripts')
    <script type="text/javascript">
        $(document).ready(function() {
            $("#dataTable").DataTable({
                dom: "<'row'<'col-md-6'l><'col-md-6'B>>" +
                    "<'row'<'col-md-12'f>>" +
                    "<'row'<'col-md-12't>><'row'<'col-md-12'ip>>",
                buttons: [],
                scrollX: true,
                processing: true,
                // serverSide: true,
                autoWidth: false,
            });
        });
    </script>
@endsection
