<div id="modal_keanggotaan" class="modal fade" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form class="isian_anggota" onsubmit="simpan()">
                <div class="modal-header">
                    <h3 class="modal-title">{{ $judul }}</h3>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                        <span class="sr-only">Close</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" value="{{ $id }}" name="id">
                    <div class="row">
                        <div class="col-lg-12">
                            <label for="">Nama *</label>
                            <input type="text" name="nama" class="form-control" value="{{ $anggota->nama ?? '' }}"
                                autocomplete="off" placeholder="Nama anggota" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <label for="">Jabatan *</label>
                            @if (Auth::getUser()->role == 'mitra')
                                <input type="text" name="jabatan" class="form-control"
                                    value="{{ $anggota->jabatan ?? '' }}" autocomplete="off"
                                    placeholder="Kepala Perusahaan/Kepala Bagian" required>
                            @else
                                @php
                                    $jabatan = App\Http\Libraries\GetData::jabatan(Auth::getUser()->role);
                                    $jabatan_terpilih = $anggota->jabatan ?? '';
                                @endphp
                                <select name="jabatan" class="form-control" required>
                                    <option value="">.:: Pilih Jabatan ::..</option>
                                    @for ($i = 0; $i < count($jabatan); $i++)
                                        <option @if ($jabatan[$i] == $jabatan_terpilih) selected @endif>{{ $jabatan[$i] }}
                                        </option>
                                    @endfor
                                </select>
                            @endif
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <label for="">No Telp *</label>
                            <input type="text" name="no_telp" class="form-control"
                                value="{{ $anggota->no_telp ?? '' }}" autocomplete="off" placeholder="08575454xxxxx"
                                required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <label for="">Berkas Tambahan</label> <span style="font-size:9pt">(Upload url file,
                                bisa diupload dahulu di google drive)</span>
                            <input type="text" name="berkas_tambahan" class="form-control"
                                value="{{ $anggota->berkas_tambahan ?? '' }}" autocomplete="off"
                                placeholder="https://drive.google.com/.........">
                        </div>
                    </div>
                </div>
                <div class="row" style="padding: 0px 10px">
                    <div class="col-lg-12" style="font-size: 9pt;">
                        <b>*</b>) Wajib diisi
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" style="margin-bottom:0px" class="btn btn-white"
                        data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<style>
    @media (min-width: 992px) {
        .modal-lg {
            max-width: 1800px;
        }
    }
</style>
<script>
    $('#modal_keanggotaan').modal({
        backdrop: 'static'
    });
</script>
