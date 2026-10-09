<!-- Modal -->
<div id="modal_profil" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <style>
            tr td {
                vertical-align: top;
                padding: 0.2cm 0cm;
            }
        </style>
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Monitor Ijop</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12" style="overflow:auto">
                        <form class="form-monev" onsubmit="kirim()" enctype="multipart/form-data">
                            <input type="hidden" name="npsn" value="{{ $sekolah->npsn }}">
                            <input type="hidden" name="tahun" value="{{ $tahun }}">
                            <table style="width: 100%">
                                <tr>
                                    <td>Sekolah</td>
                                    <td>{{ $sekolah->nama_lembaga }} ({{ $sekolah->npsn }})</td>
                                </tr>
                                <tr>
                                    <td>Nama Petugas</td>
                                    <td>
                                        {{ $monev->petugas }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>Tanggal Monitoring</td>
                                    <td>
                                        {{ $monev->tanggal ?? '' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>Hasil</td>
                                    <td>
                                        {{ $monev->hasil }}
                                    </td>
                                </tr>
                                @if ($monev->hasil == 'Tindak Lanjut')
                                    <tr class="tr_tindak_lanjut">
                                        <td>Keterangan</td>
                                        <td>
                                            {{ $monev->keterangan ?? '' }}
                                        </td>
                                    </tr>
                                @else
                                    <tr class="tr_tutup">
                                        <td>Surat Pernyataan Tutup</td>
                                        <td>
                                            @php
                                                $file_pernyataan = $monev->path_pernyataan_tutup ?? '';
                                            @endphp
                                            @if (!empty($file_pernyataan))
                                                <a href="https://dispendik.surabaya.go.id/sibunda_file/{{ $file_pernyataan }}"
                                                    target="_blank" class="btn btn-sm btn-success">Lihat</a>
                                            @endif
                                        </td>
                                    </tr>
                                @endif


                                <tr>
                                    <td colspan="2">
                                        <button type="button" class="btn btn-warning"
                                            data-dismiss="modal">Tutup</button>
                                    </td>
                                </tr>
                            </table>
                        </form>
                    </div>
                </div>
            </div>
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
<script type="text/javascript"></script>
