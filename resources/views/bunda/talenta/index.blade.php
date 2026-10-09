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
        <h2 class="content-heading" style="padding-top: 0px">Talenta Seni</h2>
        <!-- Dynamic Table Full -->
        <div class="block">
            <div class="block-header block-header-default row no-margin">
                <h3 class="block-title">Talenta Seni</h3>
            </div>

            <div class="block-content block-content-full">
                <div class="row" style="margin-bottom: 15px;">
                    <div class="col-md-12">
                        <a href="javascript:void(0)" class="btn btn-primary"
                            onclick="load_page('penjadwalan')">Penjadwalan</a>
                        <a href="javascript:void(0)" class="btn btn-primary" onclick="load_page('dokumen')">Dokumen</a>
                    </div>
                </div>

                <div style="overflow: auto" class="tempat_rekap">

                </div>
                <div class="loading">
                    Sedang menghitung data.......
                </div>
                <div class="modal-page"></div>
            </div>
        </div>
        @include('attention')
    </div>
    <div class="modal-page"></div>
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
            $('#sidebar-talenta').addClass('active');
            load_page('penjadwalan');
        });

        function load_page(jenis) {
            $('.loading').show();
            $('.tempat_rekap').hide();
            
            var data = {
                jenis: jenis,
            };
            $.post('{{ route('bunda-talenta-load_page') }}', data, function(data) {
                $('.loading').hide();
                $('.tempat_rekap').show();
                $('.tempat_rekap').html(data.content);
                $('#myData').DataTable();
            }).fail(function() {
                $('.loading').hide();
                $('.tempat_rekap').show();
            });
        }
    </script>
    <script></script>
@endsection
