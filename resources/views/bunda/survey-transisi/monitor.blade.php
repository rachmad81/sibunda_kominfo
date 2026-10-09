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
        <h2 class="content-heading" style="padding-top: 0px">Monitor Survey Transisi PAUD Ke SD</h2>
        <!-- Dynamic Table Full -->
        <div class="block">
            <div class="block-header block-header-default row no-margin">
                <h3 class="block-title">Monitor Survey Transisi PAUD Ke SD</h3>
            </div>

            <div class="block-content block-content-full">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="datatable">
                            <thead>
                                <tr>
                                    <th>
                                        No
                                    </th>
                                    <th>
                                        NPSN
                                    </th>
                                    <th>
                                        Nama Satuan Pendidikan
                                    </th>
                                    <th>
                                        Kecamatan
                                    </th>
                                    <th>
                                        Kelurahan
                                    </th>
                                    <th>
                                        Tanggal Pengisian
                                    </th>
                                    <th style="max-width:100px;">
                                        Persentase Jawaban
                                    </th>
                                    <th style="max-width:100px;">
                                        Persentase Keterangan
                                    </th>
                                    <th style="max-width:100px;">
                                        Persentase Bukti Dokumentasi
                                    </th>
                                    <th style="max-width:100px;">
                                        Persentase Total
                                    </th>
                                    <th style="max-width:100px;">
                                        Aksi
                                    </th>
                                </tr>
                            </thead>
                            @php
                            @endphp
                            <tbody style="color:black;text-align:justify;text-justify:inter-word;">
                                @foreach ($lembagas as $key => $lembaga)
                                    <tr>
                                        <td>
                                            {{ ++$key }}
                                        </td>
                                        <td>
                                            {{ $lembaga->npsn }}
                                        </td>
                                        <td>
                                            {{ $lembaga->nama }}
                                        </td>
                                        <td>
                                            {{ $lembaga->kecamatan }}
                                        </td>
                                        <td>
                                            {{ $lembaga->kelurahan }}
                                        </td>
                                        <td>
                                            {{ now()->parse($lembaga->updated_at)->format('d-m-Y H:i') }}
                                        </td>
                                        <td>
                                            {{ $lembaga->persentase_jawaban }}
                                        </td>
                                        <td>
                                            {{ $lembaga->persentase_keterangan }}
                                        </td>
                                        <td>
                                            {{ $lembaga->persentase_bukti_dokumentasi }}
                                        </td>
                                        <td>
                                            {{ $lembaga->persentase_total }}
                                        </td>
                                        <td>
                                            <button onclick="load_page(`satdik2024`,`{{ $lembaga->npsn }}`)"
                                                class="btn btn-primary">
                                                Lihat
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @include('attention')
    </div>
    <div class="modal-page">
        <div id="modal">
        </div>
    </div>
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
            $('#sidebar-monitor').addClass('active');
            $('#datatable').DataTable();
        });

        function load_page(tipe, npsn) {
            $("#modal").html('');

            var data = {
                tipe: tipe,
                npsn: npsn,
            };
            $.post('{{ route('bunda-survey_transisi-monitor_detail') }}', data, function(data) {
                $('#modal').html(data.content);
            }).fail(function() {});
        }
    </script>
    <script></script>
@endsection
