<!-- Modal -->
<div id="modal_profil" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Monitor Perkembangan Inklusi ({{ $sekolah->nama }} - {{ $sekolah->npsn }})</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12" style="overflow:auto">
                        <table style="width: 100%;border-collapse: collapse" border="1">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tangal Pengisian</th>
                                    <th>Nama Siswa</th>
                                    <th>Status Siswa</th>
                                    <th>Status Alumni</th>
                                    <th>Kategori Inklusi</th>
                                    <th>Indikator</th>
                                    <th>Skor</th>
                                    <th>Catatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $no = 1;
                                @endphp
                                @foreach ($log as $l)
                                    <tr>
                                        <td>{{ $no++ }}</td>
                                        <td>{{ $l->tanggal }}</td>
                                        <td>{{ $l->nama }}</td>
                                        <td>{{ $l->status_siswa }}</td>
                                        <td>{{ $l->status_lulus }}</td>
                                        <td>{{ $l->text_tampil }}</td>
                                        <td>{{ $l->nama_indikator }}</td>
                                        <td>{{ $l->skor }}</td>
                                        <td>{{ $l->catatan_khusus }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
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
