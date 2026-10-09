<!-- Modal -->
<div class="modal fade" id="modal_mitra" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('landing', ['daftar_mitra']) }}" method="post">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">{{ $judul }}</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="dol-lg-12">
                            @csrf
                            <div class="input-group mb-3">
                                <span class="input-group-text" id="basic-addon1">Username</span>
                                <input type="text" class="form-control" placeholder="Username" aria-label="Username"
                                    aria-describedby="basic-addon1" name="username" value="">
                            </div>
                            <div class="input-group mb-3">
                                <span class="input-group-text" id="basic-addon1">Password</span>
                                <input type="password" class="form-control" placeholder="Password" aria-label="Username"
                                    aria-describedby="basic-addon1" name="password" value="">
                            </div>
                            <div class="input-group mb-3">
                                <span class="input-group-text" id="basic-addon1">Nama</span>
                                <input type="text" class="form-control" placeholder="Nama Mitra"
                                    aria-label="Username" aria-describedby="basic-addon1" name="nama" value=""
                                    autocomplete="off">
                            </div>
                            <div class="input-group mb-3">
                                <span class="input-group-text" id="kecamatan">Kecamatan</span>
                                <select class="form-control" name="kecamatan" onchange="ganti_kecamatan()">
                                    <option value="">Kecamatan</option>
                                    @foreach ($kecamatan as $kec)
                                        <option value="{!! $kec['kecamatan_kode'] !!}">
                                            {!! $kec['kecamatan_dispenduk'] !!}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="input-group mb-3">
                                <span class="input-group-text" id="kelurahan">Kelurahan</span>
                                <select class="form-control" name="kelurahan">
                                    <option value="">Kelurahan</option>
                                </select>
                            </div>
                            <div class="input-group mb-3">
                                <span class="input-group-text">Alamat</span>
                                <textarea class="form-control" aria-label="With textarea" name="alamat"></textarea>
                            </div>
                            <div class="input-group mb-3">
                                <span class="input-group-text" id="kelurahan">Nama Pimpinan</span>
                                <input type="text" class="form-control" name="nama_kepala" autocomplete="off"
                                    placeholder="Contoh Alexander Purwoto">
                            </div>
                            <div class="input-group mb-3">
                                <span class="input-group-text" id="kelurahan">Kontak (No Hp)</span>
                                <input type="text" class="form-control" name="kontak" autocomplete="off"
                                    placeholder="Contoh 085785785785">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>
<script>
    $('#modal_mitra').modal('show');

    var data_kecamatan = [];
    var kelurahan = [];

    $(document).ready(function() {
        @foreach ($kecamatan as $kec)
            data_kecamatan['{!! $kec['kecamatan_kode'] !!}'] = '{!! $kec['kelurahan'] !!}';
        @endforeach
        ganti_kecamatan();
    });

    function ganti_kecamatan() {
        var html_kelurahan = '<option value="">Kelurahan</option>';
        var kecamatan = $('select[name=kecamatan]').val();
        console.log(kecamatan);
        if (kecamatan != '') {
            var data_kelurahan = data_kecamatan[kecamatan];
            kelurahan = JSON.parse(atob(data_kelurahan));
        } else {
            kelurahan = [];
        }

        $.each(kelurahan, function(k, v) {
            html_kelurahan += '<option value="' + v.kelurahan_kode + '">' + v.kelurahan_dispenduk +
                '</option>';
        });

        $('select[name=kelurahan]').html(html_kelurahan);
    }
</script>
