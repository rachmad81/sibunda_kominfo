<!-- Modal -->
<div id="modal_profil" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Monitor Survey Transisi PAUD Ke SD
                    @php
                        $pertanyaan = $pertanyaans->first();
                        $jawabanSurvey = $pertanyaan ? $pertanyaan->jawabanSurvey : null;
                        $sekolahDetail = $jawabanSurvey ? "{$jawabanSurvey->npsn} - {$jawabanSurvey->nama}" : '';
                    @endphp
                    {{ $sekolahDetail }}
                </h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="row">
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
                                                    name="jawaban[{{ $pertanyaan['id'] }}]" class="form-control"
                                                    readonly>
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
                                            @if ($pertanyaan['keterangan'] === true)
                                                <textarea id="keterangan[{{ $pertanyaan['id'] }}]" name="keterangan[{{ $pertanyaan['id'] }}]" class="form-control"
                                                    readonly>{{ $pertanyaan->jawabanSurvey ? $pertanyaan->jawabanSurvey->keterangan : '' }}</textarea>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($pertanyaan['bukti_dokumentasi'] === true)
                                                @if ($pertanyaan->jawabanSurvey !== null && $pertanyaan->jawabanSurvey->bukti_dokumentasi !== null)
                                                    <a href="{{ $pertanyaan->jawabanSurvey->bukti_dokumentasi }}"
                                                        target="_blank" class="btn btn-primary">Link Google Drive</a>
                                                @endif
                                            @endif
                                        </td>
                                    </tr>
                                    {{-- @endif --}}
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            {{-- <div class="modal-footer">
                <a href="javascript:void(0)" class="btn btn-primary" onclick="simpan()">Simpan</a>
                <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
            </div> --}}
        </div>

    </div>
</div>
<style type="text/css">
    label {
        font-weight: 10;
    }

    table.kop_jadwal tr td {
        text-align: left;
        vertical-align: top;
        padding: 5px;
    }

    .modal-lg {
        max-width: 1200px !important;
    }
</style>
<script type="text/javascript">
    $(document).ready(function() {
        $('#modal_profil').modal({
            backdrop: 'static'
        });
    });
</script>
