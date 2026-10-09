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
                                        <input type="text" name="petugas" required
                                            value="{{ $monev->petugas ?? '' }}" class="form-control">
                                    </td>
                                </tr>
                                <tr>
                                    <td>Tanggal Monitoring</td>
                                    <td>
                                        <input type="date" name="tanggal" required
                                            value="{{ $monev->tanggal ?? '' }}" class="form-control">
                                    </td>
                                </tr>
                                <tr>
                                    <td>Hasil</td>
                                    <td>
                                        @php
                                            $hasil = ['', 'selected'];
                                            $hasil_monev = $monev->hasil ?? '';
                                            if ($hasil_monev == 'Tutup') {
                                                $hasil = ['selected', ''];
                                            }
                                        @endphp
                                        <select name="hasil" class="form-control" required onchange="ganti_hasil()">
                                            <option {{ $hasil[0] }}>Tutup</option>
                                            <option {{ $hasil[1] }}>Tindak Lanjut</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr class="tr_tindak_lanjut">
                                    <td>Keterangan</td>
                                    <td>
                                        <textarea class="form-control" name="keterangan" required>{{ $monev->keterangan ?? '' }}</textarea>
                                    </td>
                                </tr>
                                <tr class="tr_tutup">
                                    <td>Surat Pernyataan Tutup</td>
                                    <td>
                                        <input type="file" name="pernyataan_tutup" class="form-control"
                                            accept=".pdf">
                                        @php
                                            $file_pernyataan = $monev->path_pernyataan_tutup ?? '';
                                        @endphp
                                        @if (!empty($file_pernyataan))
                                            <a href="https://dispendik.surabaya.go.id/sibunda_file/{{ $file_pernyataan }}"
                                                target="_blank" class="btn btn-sm btn-success">Lihat</a>
                                        @endif
                                    </td>
                                </tr>


                                <tr>
                                    <td colspan="2">
                                        <button type="submit" class="btn btn-primary">Simpan</button>
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
    var file_pernyataan = '{{ $file_pernyataan }}';

    $(document).ready(function() {
        $('#modal_profil').modal({
            backdrop: 'static'
        });
        ganti_hasil();
    });

    function ganti_hasil() {
        $('.tr_tindak_lanjut').hide();
        $('.tr_tutup').hide();
        var hasil = $('select[name=hasil]').val();
        if (hasil == 'Tindak Lanjut') {
            $('.tr_tindak_lanjut').show();

            $('input[name=pernyataan_tutup]').removeAttr('required');
            $('textarea[name=keterangan]').attr('required', 'required');
        } else {
            $('.tr_tutup').show();
            $('textarea[name=keterangan]').removeAttr('required');
            if (file_pernyataan == '') {
                $('input[name=pernyataan_tutup]').attr('required', 'required');
            }
        }
    }
</script>
<script type="text/javascript"></script>
