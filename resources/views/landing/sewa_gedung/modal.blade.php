{{-- MODAL --}}
<div class="modal fade" id="modal_tambah" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form onsubmit="simpan_sewa()" method="POST" class="form_sewa">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Tambah Kegiatan</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="dol-lg-12">
                            <div class="tempat_error alert alert-danger" style="display: none"></div>
                            @csrf
                            <div class="input-group mb-3">
                                <span class="input-group-text" id="basic-addon1">Nama Penyewa</span>
                                <input type="text" class="form-control" placeholder="Nama Penyewa"
                                    aria-label="Username" aria-describedby="basic-addon1" name="penyewa" value=""
                                    required>
                            </div>
                            <div class="input-group mb-3">
                                <span class="input-group-text" id="basic-addon1">Kontak</span>
                                <input type="text" class="form-control" placeholder="Kontak (No HP/email)"
                                    aria-label="Username" aria-describedby="basic-addon1" name="kontak" value=""
                                    required>
                            </div>
                            <div class="input-group mb-3">
                                <span class="input-group-text" id="basic-addon1">Tanggal</span>
                                <input type="date" class="form-control" placeholder="Tanggal sewa"
                                    aria-label="Username" aria-describedby="basic-addon1" name="tanggal" value=""
                                    required>
                            </div>
                            <div class="input-group mb-3">
                                <span class="input-group-text" id="basic-addon1">Waktu Sewa</span>
                                <input type="time" class="form-control" placeholder="Tanggal sewa"
                                    aria-label="Username" aria-describedby="basic-addon1" name="waktu" min="05:00"
                                    max="20:00" value="" required>
                            </div>
                            <div class="input-group mb-3">
                                <span class="input-group-text" id="basic-addon1">Kegiatan</span>
                                <input type="text" class="form-control" placeholder="Kegiatan" aria-label="Username"
                                    aria-describedby="basic-addon1" name="kegiatan" value="" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </form>
    </div>
</div>
