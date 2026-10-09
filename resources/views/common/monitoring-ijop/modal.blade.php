<div id="modal_tambah" class="modal fade" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Tambah Hasil Monitoring</h3>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
            </div>
            <form method="POST" action="{{ route('common-monitoring-store') }}" class="form-horizontal" enctype="multipart/form-data">
            <div class="modal-body">
                @csrf
                <div class="row">
                    <div class="col-md-6 col-6">
                        <div class="form-material">
                            <select class="form-control select2" name="npsn" required>
                                @foreach($list_sekolah as $sekolah)
                                <option value="{{ $sekolah->npsn }}">{{ $sekolah->npsn.' - '.$sekolah->nama_sekolah }}</option>
                                @endforeach
                            </select>
                            <label for="sekolah" class="required">Sekolah</label>
                        </div>
                    </div>
                    <div class="col-md-6 col-12">
                        <div class="form-material">
                            <select class="form-control select2" name="petugas" required>
                                @foreach($list_petugas as $petugas)
                                <option value="{{ $petugas->id }}">{{ $petugas->jabatanPokja->nama_jabatan.' - '.$petugas->nama }}</option>
                                @endforeach
                            </select>
                            <label for="nama" class="required">Nama Petugas</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3 col-6">
                        <div class="form-material">
                            <input type="date" class="form-control" name="tanggal" required>
                            <label for="tanggal" class="required">Tgl. Monitoring</label>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="form-material">
                            <select class="form-control" name="hasil" required v-model="hasil">
                                <option value="Tutup">Tutup</option>
                                <option value="Tindak Lanjut">Tindak Lanjut</option>
                            </select>
                            <label for="hasil" class="required">Hasil</label>
                        </div>
                    </div>
                    <div class="col-md-6 col-6" v-if="hasil == 'Tindak Lanjut'">
                        <div class="form-material">
                            <input type="text" class="form-control" name="keterangan" required>
                            <label for="keterangan" class="required">Keterangan</label>
                        </div>
                    </div>
                    <div class="col-md-6 col-6" v-else-if="hasil == 'Tutup'">
                        <div class="form-material">
                            <input type="file" class="form-control" name="pernyataan_tutup" accept="application/pdf" required>
                            <label for="pernyataan_tutup" class="required">Srt. Pernyataan Tutup</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" style="margin-bottom:0px" class="btn btn-white" data-dismiss="modal">Batalkan</button>
                <button type="submit" class="btn btn-primary btn-submit">Tambahkan</button>
            </div>
            </form>
        </div>
    </div>
</div>

<div id="modal_ubah" class="modal fade" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Ubah Hasil Monitoring</h3>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
            </div>
            <form method="POST" action="{{route('common-monitoring-update')}}" class="form-horizontal" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="id" :value="detail.id">
            <div class="modal-body">

            </div>
            <div class="modal-footer">
                <button type="submit" style="margin-bottom:0px" class="btn btn-warning btn-submit">Ubah</button>
                <button type="button" class="btn btn-white" data-dismiss="modal">Batalkan</button>
            </div>
            </form>
        </div>
    </div>
</div>
