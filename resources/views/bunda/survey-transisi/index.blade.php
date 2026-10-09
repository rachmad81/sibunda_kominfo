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
        <h2 class="content-heading" style="padding-top: 0px">Survey Transisi PAUD Ke SD</h2>
        <!-- Dynamic Table Full -->
        <div class="block">
            <div class="block-header block-header-default row no-margin">
                <h3 class="block-title">Survey Transisi PAUD Ke SD</h3>
            </div>

            <div class="block-content block-content-full">
                <div class="card-body">
                    <form action={{ route('bunda-survey_transisi-store') }} method="POST">
                        @csrf
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>
                                            No
                                        </th>
                                        <th style="width:600px;">
                                            Pertanyaan
                                        </th>
                                        <th>
                                            Jawaban
                                        </th>
                                        <th>
                                            Keterangan
                                        </th>
                                        <th>
                                            Bukti Dokumentasi (Link Google Drive)
                                        </th>
                                    </tr>
                                </thead>
                                @php
                                @endphp
                                <tbody style="color:black;text-align:justify;text-justify:inter-word;">
                                    @foreach ($pertanyaans as $pertanyaan)
                                        {{-- @if (in_array(session('jenjang'), $pertanyaan['jenjang'])) --}}
                                        <tr>
                                            <td>
                                                {{ $pertanyaan['nomor'] }}
                                            </td>
                                            <td>
                                                {{ $pertanyaan['pertanyaan'] }}
                                            </td>
                                            <td>
                                                @if ($pertanyaan['jawaban'] === true)
                                                    <select id="jawaban[{{ $pertanyaan['id'] }}]"
                                                        name="jawaban[{{ $pertanyaan['id'] }}]" class="form-control">
                                                        <option value="" selected>Pilih...</option>
                                                        <option value="true"
                                                            {{ $pertanyaan->jawabanSurvey !== null ? ($pertanyaan->jawabanSurvey->jawaban === true ? 'selected' : '') : '' }}>
                                                            Ada/Sudah</option>
                                                        <option value="false"
                                                            {{ $pertanyaan->jawabanSurvey !== null ? ($pertanyaan->jawabanSurvey->jawaban === false ? 'selected' : '') : '' }}>
                                                            Tidak Ada/Belum</option>
                                                    </select>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($pertanyaan['jawaban'] === true)
                                                    <textarea id="keterangan[{{ $pertanyaan['id'] }}]" name="keterangan[{{ $pertanyaan['id'] }}]" class="form-control">{{ $pertanyaan->jawabanSurvey ? $pertanyaan->jawabanSurvey->keterangan : '' }}</textarea>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($pertanyaan['jawaban'] === true)
                                                    <input id="bukti_dokumentasi[{{ $pertanyaan['id'] }}]"
                                                        name="bukti_dokumentasi[{{ $pertanyaan['id'] }}]" type="text"
                                                        class="form-control"
                                                        {{ $pertanyaan->jawabanSurvey ? "value={$pertanyaan->jawabanSurvey->bukti_dokumentasi}" : '' }}>
                                                @endif
                                            </td>
                                        </tr>
                                        {{-- @endif --}}
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <input type="hidden" name="tipe" value="bunda2024">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </form>
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
        $(document).ready(function() {
            $('#sidebar-monitor').addClass('active');
        });
    </script>
    <script></script>
@endsection
