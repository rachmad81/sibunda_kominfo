<div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{ $judul }}</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="{{ route('bunda-rekap_profil-foto_store') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="detailData">
                            <thead>
                                <tr>
                                    <th>Instrumen monitoring</th>
                                    <th>Foto pelaksanaan monotoring</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <select class="form-control" name="status" required>
                                            <option>Belum</option>
                                            <option>Sudah</option>
                                            <option>Sudah Monev</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="file" name="file">
                                    </td>
                                    <input type="hidden" name="jenis" required value="{{ $jenis }}">
                                    <input type="hidden" name="npsn" required value="{{ $npsn }}">
                                    <input type="hidden" name="tahun" required value="{{ $tahun }}">
                                    <input type="hidden" name="bulan" required value="{{ $bulan }}">
                                    <input type="hidden" name="jenjang" required value="{{ $jenjang }}">
                                    <input type="hidden" name="status_sekolah" required value="{{ $status }}">
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <button type="submit" class="btn btn-sm btn-primary">Simpan</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </form>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>

    </div>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        $('#myModal').modal();
        // $('#table_gtk').DataTable();
    })
</script>
