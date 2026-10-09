{{-- MODAL --}}
<div class="modal fade" id="modal_penghuni" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">{!! $judul !!}</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered table-striped" id="tabel_penghuni">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>NIK</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $k => $d)
                            <tr>
                                <td>{!! $k + 1 !!}</td>
                                <td>{!! $d->nama !!}</td>
                                <td>{!! $d->nik !!}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    $('#modal_penghuni').modal('show');
    $('#tabel_penghuni').DataTable();
</script>
