<!-- Modal -->
<div id="modal_profil" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <form method="post" onsubmit="simpan()" class="form_tempat">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Setting Tempat Talenta Seni</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12" style="overflow: auto">
                            <table class="table table-striped">
                                <tr>
                                    <td>Nama</td>
                                    <td>
                                        <input type="hidden" name="id" value="{{ $id }}">
                                        <input type="text" class="form-control" name="nama" required
                                            value="{{ $tempat->nama ?? '' }}">
                                    </td>
                                </tr>
                                <tr>
                                    <td>Alamat</td>
                                    <td>
                                        <input type="text" class="form-control" name="alamat" required
                                            value="{{ $tempat->alamat ?? '' }}">
                                    </td>
                                </tr>
                                <tr>
                                    <td>Kontak</td>
                                    <td>
                                        <input type="text" class="form-control" name="kontak" required
                                            value="{{ $tempat->kontak ?? '' }}">
                                    </td>
                                </tr>
                                <tr>
                                    <td>Area</td>
                                    <td>
                                        <input type="text" class="form-control" name="area" required
                                            value="{{ $tempat->area ?? '' }}">
                                    </td>
                                </tr>
                                <tr>
                                    <td>Kecamatan</td>
                                    <td style="text-align: left">
                                        @php
                                            $data_kecamatan = $tempat->kecamatan ?? '';
                                            if ($data_kecamatan != '') {
                                                $data_kecamatan = explode(',', $data_kecamatan);
                                            } else {
                                                $data_kecamatan = [];
                                            }
                                        @endphp
                                        @foreach ($kecamatan as $k)
                                            <label for="{{ $k->kecamatan_kode }}">
                                                <input id="{{ $k->kecamatan_kode }}" type="checkbox"
                                                    @if (in_array($k->kecamatan_dispenduk, $data_kecamatan)) checked @endif name="kecamatan[]"
                                                    value="{{ $k->kecamatan_dispenduk }}">
                                                {{ $k->kecamatan_dispenduk }}
                                            </label><br>
                                        @endforeach
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </form>
    </div>
</div>
<style type="text/css">
    label {
        font-weight: 10;
    }

    table.kop_jadwal tr td {
        text-align: left;
        vertical-align: top;
        padding: 5px;
    }

    .modal-lg {
        max-width: 1200px !important;
    }
</style>
<script type="text/javascript">
    $(document).ready(function() {
        $('#modal_profil').modal({
            backdrop: 'static'
        })
    })

    function simpan() {
        event.preventDefault();
        f_data = new FormData($('.form_tempat')[0]);
        if ($('input[name="kecamatan[]"]:checked').length === 0) {
            swal('Mohon memilih paling sedikit 1 kecamatan');
        } else {
            $.ajax({
                url: '{{ route('telenta_tempat-halaman', ['halaman' => 'simpan']) }}',
                type: 'post',
                data: f_data,
                contentType: false,
                processData: false,
                success: function(data) {
                    if (data.code == 200) {
                        swal('Success', 'Berhasil disimpan', 'success');
                        $('#modal_profil').modal('hide');
                    } else {
                        swal('Whoooops', 'Gagal disimpan', 'error');
                    }
                    get_data();
                }
            })
        }
    }
</script>
