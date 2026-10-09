<!-- Modal -->
<div id="modal_tembusan" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Wisata {{ $wisata->nama }}</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="form_opd">
                    <input type="hidden" name="id" value="{{ $wisata->id }}">
                    <table style="width: 100%;border-collapse: collapse;" border="1">
                        <tr>
                            <td>Nama Wisata</td>
                            <td>
                                <input required type="text" class="form-control" name="nama"
                                    value="{{ $wisata->nama }}">
                            </td>
                        </tr>
                        <tr>
                            <td>Kapasitas Wisata</td>
                            <td>
                                <input required type="number" min="1" class="form-control" name="kapasitas"
                                    value="{{ $wisata->kapasitas_pd }}">
                            </td>
                        </tr>
                        <tr>
                            <td>Hari Buka</td>
                            <td>
                                @php
                                    $hari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                                    $hari_pakai = $wisata->hari_buka;
                                    if (!empty($hari_pakai)) {
                                        $arr_hari = str_replace(['[', ']'], ['', ''], $hari_pakai);
                                        $arr_hari = explode(',', $arr_hari);
                                    } else {
                                        $arr_hari = [];
                                    }
                                @endphp
                                @for ($i = 0; $i < count($hari); $i++)
                                    <label>
                                        <input @if (in_array($i, $arr_hari)) checked @endif type="checkbox"
                                            name="hari_buka[]" value="{{ $i }}">
                                        {{ $hari[$i] }}
                                    </label>
                                @endfor
                            </td>
                        </tr>
                        <tr>
                            <td>Alamat</td>
                            <td>
                                <input required type="text" name="alamat" class="form-control"
                                    value="{{ $wisata->alamat }}">
                            </td>
                        </tr>
                        <tr>
                            <td>Kecamatan</td>
                            <td>
                                <select required name="kecamatan" class="form-control" onchange="ganti_kecamatan()">
                                    <option value="">.. Pilih Kecamatan ..</option>
                                    @foreach ($kecamatan as $kec)
                                        <option @if ($kec['kecamatan'] == strtoupper($wisata->kecamatan)) selected @endif>
                                            {{ $kec['kecamatan'] }}</option>
                                    @endforeach
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>Kelurahan</td>
                            <td>
                                <select required name="kelurahan" class="form-control">
                                    <option value="">.. Pilih Kelurahan ..</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>Status Ditampilkan</td>
                            <td>
                                <select required name="status" class="form-control">
                                    <option @if ($wisata->status_tampil == true) selected @endif value="true">Tampilkan
                                    </option>
                                    <option @if ($wisata->status_tampil == false) selected @endif value="false">Sembunyikan
                                    </option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>OPD</td>
                            <td>
                                <table style="width: 100%">
                                    @foreach ($opd as $o)
                                        <tr>
                                            <td><input type="checkbox" @if (in_array($o->id, $id_opd)) checked @endif
                                                    id="{{ $o->id }}" name="opd[]" value="{{ $o->id }}">
                                            </td>
                                            <td>
                                                <label for="{{ $o->id }}"
                                                    style="margin-left: 5px">{{ $o->nama }}</label>
                                            </td>
                                        </tr>
                                    @endforeach
                                </table>
                            </td>
                        </tr>
                    </table>
                </form>
            </div>
            <div class="modal-footer">
                <a href="javascript:void(0)" onclick="simpan()" class="btn btn-sm btn-primary">Simpan</a>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>

    </div>
</div>
<style type="text/css">
    table tr td {
        vertical-align: top;
    }
</style>
<script type="text/javascript">
    var kecamatan = [];
    @foreach ($kecamatan as $kec)
        kecamatan['{{ $kec['kecamatan'] }}'] = {!! $kec['kelurahan'] !!};
    @endforeach
    $(document).ready(function() {
        $('#modal_tembusan').modal('show');
        ganti_kecamatan();
    });

    function ganti_kecamatan() {
        var kecamatan_nama = $('select[name=kecamatan]').val();
        var kelurahan = kecamatan[kecamatan_nama];
        var html = '<option value="">.. Pilih Kelurahan ..</option>';

        $.each(kelurahan, function(k, v) {
            if (v.kelurahan_dispenduk == '{{ strtoupper($wisata->kelurahan) }}') {
                html += '<option selected>' + v.kelurahan_dispenduk + '</option>';
            } else {
                html += '<option>' + v.kelurahan_dispenduk + '</option>';
            }
        });

        $('select[name=kelurahan]').html(html);
    }

    function simpan() {
        var data = $('form#form_opd').serialize(0);
        $.post("{{ route('dinas-data_paud-wisata-simpan') }}", data, function(data) {
            if (data.code == '200') {
                swal('Success', 'Berhasil disimpan', 'success');
                get_data();
                $('.modal-backdrop').removeClass('modal-backdrop');
                $('.page-modal').html('');
            } else {
                swal('Success', 'Gagal disimpan', 'error');
            }
        })
    }
</script>
