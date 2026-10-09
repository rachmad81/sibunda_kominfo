<div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog" style="max-width:1000px;">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{ $judul }}</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="detailData">
                        <thead>
                            <tr>
                                <th>NO</th>
                                <th>NAMA</th>
                                <th>TANGGAL LAHIR</th>
                                <th>NIK</th>
                                <th>KELAS</th>
                                <th>ROMBEL</th>
                                <th>STATUS GAMIS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($lembagas as $k => $lembaga)
                                <tr>
                                    <td>{{ $k + 1 }}</td>
                                    <td>{{ $lembaga->nama }}</td>
                                    <td>{{ $lembaga->tgl_lahir }}</td>
                                    <td>{{ $lembaga->nik }}</td>
                                    <td>{{ $lembaga->kelas }}</td>
                                    <td>{{ $lembaga->rombel }}</td>
                                    <td>{{ $lembaga->status_gamis }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>

    </div>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        $('#myModal').modal();
        $('#detailData').DataTable();
    })
</script>
