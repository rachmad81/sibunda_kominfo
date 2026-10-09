<div id="modal_ubah" class="modal fade" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Riwayat Pengguna</h3>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
            </div>
            <input type="hidden" name="id" value="{{$profil->id}}">
            <div class="modal-body">
                <table style="table-collapse:collapse;width: 100%" border="1">
                    <tr>
                        <th>Tingkat</th>
                        <th>Role</th>
                        <th>Kecamatan</th>
                        <th>Kelurahan</th>
                        <th>Aksi</th>
                        <th>Tgl perubahan</th>
                    </tr>
                    @if($riwayat->count()!=0)
                    @foreach($riwayat as $r)
                    <tr>
                        <td>{{$r->tingkat}}</td>
                        <td>{{$r->role}}</td>
                        <td>{{$r->kecamatan}}</td>
                        <td>{{$r->kelurahan}}</td>
                        <td>{{$r->aksi}}</td>
                        <td>{{date('d-m-Y H:i:s',strtotime($r->created_at))}}</td>
                    </tr>
                    @endforeach
                    @else
                    <tr>
                        <td colspan="6">Belum ada riwayat perubahan</td>
                    </tr>
                    @endif
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-white" data-dismiss="modal">Batalkan</button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function(){
        $('#modal_ubah').modal('show');
    })
</script>