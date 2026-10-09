<style>
    .row {
        margin-top: 10px
    }

    .error {
        display: none;
    }
</style>
<!-- Modal -->
<div class="modal fade" id="modal_status" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form class="form-mitra" method="post" onsubmit="simpan()">
            <input type="hidden" value="{!! $id !!}" name="id">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Data Mitra</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <label class="col-lg-4">Username</label>
                        <div class="col-lg-8">
                            <input autocomplete="off" type="text" name="username" class="form-control"
                                value="{!! $mitra->username ?? '' !!}" required>
                            <div class="alert alert-danger error error_username"></div>
                        </div>
                    </div>
                    <div class="row">
                        <label class="col-lg-4">Password</label>
                        <div class="col-lg-8">
                            <input autocomplete="off" type="password" name="password" class="form-control"
                                @if ($id == 0) required @endif>
                            <div class="alert alert-danger error error_password"></div>
                        </div>
                    </div>
                    <div class="row">
                        <label class="col-lg-4">Nama Mitra</label>
                        <div class="col-lg-8">
                            <input autocomplete="off" type="text" name="nama_mitra" class="form-control"
                                value="{!! $mitra->nama_mitra ?? '' !!}" required>

                            <div class="alert alert-danger error error_nama_mitra"></div>
                        </div>
                    </div>
                    <div class="row">
                        @php
                            $kecamatan_mitra = $mitra->kecamatan ?? '';
                            $kelurahan_mitra = $mitra->kelurahan ?? '';
                        @endphp
                        <label class="col-lg-4">Kecamatan</label>
                        <div class="col-lg-8">
                            <select name="kecamatan" class="form-control" onchange="ganti_kecamatan()" required>
                                <option value="">Pilih Kecamatan</option>
                                @foreach ($kecamatan as $kec)
                                    <option @if ($kec->kecamatan_kode == $kecamatan_mitra) selected @endif
                                        value="{!! $kec->kecamatan_kode !!}">{!! $kec->kecamatan_dispenduk !!}</option>
                                @endforeach
                            </select>
                            <div class="alert alert-danger error error_kecamatan"></div>
                        </div>
                    </div>
                    <div class="row">
                        <label class="col-lg-4">Kelurahan</label>
                        <div class="col-lg-8">
                            <select name="kelurahan" class="form-control" required>
                                <option value="">Pilih Kelurahan</option>
                            </select>
                            <div class="alert alert-danger error error_kelurahan"></div>
                        </div>
                    </div>
                    <div class="row">
                        <label class="col-lg-4">Alamat</label>
                        <div class="col-lg-8">
                            <textarea name="alamat" class="form-control">{!! $mitra->alamat ?? '' !!}</textarea>
                            <div class="alert alert-danger error error_alamat"></div>
                        </div>
                    </div>

                    <div class="row">
                        <label class="col-lg-4">Nama Pimpinan/Kepala</label>
                        <div class="col-lg-8">
                            <input type="text" name="nama_kepala" class="form-control"
                                value="{!! $mitra->nama_kepala ?? '' !!}" autocomplete="off">
                            <div class="alert alert-danger error error_nama_kepala"></div>
                        </div>
                    </div>

                    <div class="row">
                        <label class="col-lg-4">Kontak</label>
                        <div class="col-lg-8">
                            <input type="text" name="kontak" class="form-control" value="{!! $mitra->kontak ?? '' !!}"
                                autocomplete="off">
                            <div class="alert alert-danger error error_kontak"></div>
                        </div>
                    </div>
                    <div class="row">
                        @php
                            $status = $mitra->status_aktif ?? '';
                            $checked = ['', '', ''];
                            switch ($status) {
                                case 'Pengajuan':
                                    $checked[0] = 'checked';
                                    break;
                                case 'Setujui':
                                    $checked[1] = 'checked';
                                    break;
                                case 'Block':
                                    $checked[2] = 'checked';
                                    break;

                                default:
                                    $checked[0] = 'checked';
                                    break;
                            }
                        @endphp
                        <label class="col-lg-4">Status Aktif</label>
                        <div class="col-lg-8">
                            <label><input type="radio" {!! $checked[0] !!} name="status" value="Pengajuan"
                                    required>
                                Pengajuan</label>
                            <label><input type="radio" {!! $checked[1] !!} name="status" value="Setujui">
                                Setujui</label>
                            <label><input type="radio" {!! $checked[2] !!} name="status" value="Block">
                                Block</label>

                            <div class="alert alert-danger error error_status"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </form>
    </div>
</div>
<script>
    $('#modal_status').modal('show');

    @foreach ($kecamatan as $kec)
        data_kecamatan['{!! $kec->kecamatan_kode !!}'] = {!! $kec->kelurahan !!};
    @endforeach

    ganti_kecamatan();

    function ganti_kecamatan() {
        var kode_kec = $('select[name=kecamatan]').val();
        var data_kelurahan = '<option value="">Pilih Kelurahan</option>';
        if (kode_kec != '') {
            $.each(data_kecamatan[kode_kec], function(k, v) {
                if (v.kelurahan_kode == '{!! $kelurahan_mitra !!}') {
                    data_kelurahan += '<option selected value="' + v.kelurahan_kode + '">' + v
                        .kelurahan_dispenduk +
                        '</option>';
                } else {
                    data_kelurahan += '<option value="' + v.kelurahan_kode + '">' + v.kelurahan_dispenduk +
                        '</option>';
                }
            });
        }

        $('select[name=kelurahan]').html(data_kelurahan);
    }

    function simpan() {
        event.preventDefault();

        var data = $('.form-mitra').serialize(0);
        $('.error').hide();

        $.post('{{ route('dinas-mitra-halaman', ['simpan_mitra']) }}', data, function(data) {
            if (data.code == 200) {
                swal('Success', data.pesan, 'success');
                $('#modal_status').modal('hide');
                filter();
            } else {
                if (data.pesan_errors) {
                    $.each(data.pesan_errors, function(k, v) {
                        var html = '';
                        if (v) {
                            html = '<ol>';
                            $.each(v, function(k1, v1) {
                                html += '<li>' + v1 + '</li>';
                            });
                            html += '</ol>';
                        }

                        $('.error_' + k).html(html);
                        $('.error_' + k).show();
                    });
                }
            }
        }).fail(function() {
            swal('500 - Internal server error');
        })
    }
</script>
