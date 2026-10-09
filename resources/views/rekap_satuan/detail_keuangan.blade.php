<div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog" style="max-width:1000px;">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Detail {{ $judul }}</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="detailData">
                        <thead>
                            <tr>
                                <th>NO</th>
                                <th>JENJANG</th>
                                <th>NPSN</th>
                                <th>NAMA SEKOLAH</th>
                                @if (request('kolom') != 'jumlah_lembaga')
                                    <th>{{ $judul }}</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $kolom = request('kolom');
                            @endphp
                            @foreach ($lembagas as $k => $lembaga)
                                <tr>
                                    <td>{{ $k + 1 }}</td>
                                    <td>{{ $lembaga->jenjang }}</td>
                                    <td>{{ $lembaga->npsn }}</td>
                                    <td>{{ $lembaga->nama_sekolah }}</td>
                                    @if (request('kolom') != 'jumlah_lembaga')
                                        <td>{{ number_format($lembaga->$kolom, 0, '', '.') }}</td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            @if (request('kolom') != 'jumlah_lembaga')
                                <tr>
                                    <td colspan="4">Jumlah Total</td>
                                    <td>{{ number_format($lembagas->sum($kolom), 0, '', '.') }}</td>
                                </tr>
                            @endif
                        </tfoot>
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
        $('#table_siswa').DataTable();
    })
</script>
