@extends('layout.index')


@section('additional_styles')
    <style>
        tr>th>td {
            text-align: center;
        }

        .aktif-form {
            background: #ccc;
        }
    </style>
@endsection
@section('sidebar')
    @include('k3_paud_sd/layout/sidebar')
@endsection

@section('content')
    <div class="content" id="app">
        <h2 class="content-heading" style="padding-top: 0px">Dashboard </h2>
        <!-- Dynamic Table Full -->
        <div class="block">
            <div class="block-header block-header-default row no-margin">
                <h3 class="block-title">Dashboard </h3>
            </div>
            <div class="block-content block-content-full">
                @include('layout.alert')
                <div class="row">
                    <div class="col-lg-12">
                        <h1>Selamat Datang {{ Auth::getUser()->nama }}</h1>
                    </div>
                </div>
                <form class="form_dashboard">
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="basic-addon1">Tahun Program</span>
                                </div>
                                <select name="tahun_cari" class="form-control" required>
                                    @for ($i = date('Y'); $i >= 2022; $i--)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4">
                            <button type="submit" class="btn btn-sm btn-primary"><i class="si si-search"></i> Cari</button>
                        </div>
                    </div>
                </form>
                <div class="row">
                    <div class="col-lg-12 wadah_widget">
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
        $('#sidebar-beranda').addClass("active");
        $(document).ready(function() {
            $('#sidebar-beranda').addClass('active');

            cari_data();

            $('.form_dashboard').submit(function(e) {
                e.preventDefault();
                cari_data();
            });
        });

        function cari_data() {
            $('.wadah_widget').html('Sedang mencari ........');
            var data = $('form.form_dashboard').serialize();
            $.post('{{ route($jenis . '-dashboard-get_widget') }}', data, function(data) {
                $('.wadah_widget').html(data.content);
            }).fail(function() {
                swal('WHooops', '500 Internal server error', 'error');
            })
        }
    </script>
@endsection
