<!-- Modal -->
<div id="modal_profil" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Monitor Gemilang</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12" style="overflow:auto">
                        <form class="form-survey" onsubmit="kirim_survey()">
                            <input type="hidden" name="id_monev_gemilang"
                                value="{{ $monev->id_monev_gemilang ?? '' }}">
                            <table style="width: 100%;border-collapse: collapse" border="1">
                                @php
                                    $no = 1;
                                @endphp
                                <tr>
                                    <th colspan="3">Pertanaayn</th>
                                    <th>Ya</th>
                                    <th>Tidak</th>
                                    <th>#</th>
                                </tr>
                                @foreach ($pertanyaan as $p)
                                    <tr>
                                        <td style="font-weight: bold" colspan="2">{{ $no++ }}</td>
                                        <td style="font-weight: bold" colspan="4">{{ $p['kelompok'] }}</td>
                                    </tr>
                                    @foreach ($p['pertanyaan'] as $pt)
                                        <tr>
                                            <td>&nbsp;</td>
                                            <td>{{ $pt['kode'] }}</td>
                                            <td>{{ $pt['pertanyaan'] }}</td>
                                            <td>
                                                @php
                                                    $kolom = 'kelompok_' . $p['kode'];
                                                    $jawaban = json_decode($monev->$kolom);
                                                    $jawaban = collect($jawaban)->toArray();
                                                @endphp
                                                <label><input
                                                        name="isian_{{ $p['kode'] }}[{{ $p['kode'] }}_{{ $pt['kode'] }}]"
                                                        required type="radio" value="Ya"
                                                        @if (!empty($jawaban[$p['kode'] . '_' . $pt['kode']]) && $jawaban[$p['kode'] . '_' . $pt['kode']] == 'Ya') checked @endif> Ya</label>
                                            </td>
                                            <td>
                                                <label><input
                                                        name="isian_{{ $p['kode'] }}[{{ $p['kode'] }}_{{ $pt['kode'] }}]"
                                                        type="radio" value="Tidak"
                                                        @if (!empty($jawaban[$p['kode'] . '_' . $pt['kode']]) && $jawaban[$p['kode'] . '_' . $pt['kode']] == 'Tidak') checked @endif>
                                                    Tidak</label>
                                            </td>
                                            <td></td>
                                        </tr>
                                    @endforeach
                                @endforeach
                                <tr>
                                    <td colspan="3">Kesan</td>
                                    <td colspan="4">
                                        <textarea name="kesan" class="form-control">{{ $monev->kesan ?? '' }}</textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3">Saran</td>
                                    <td colspan="4">
                                        <textarea name="saran" class="form-control">{{ $monev->saran ?? '' }}</textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="7">
                                        <button type="submit" class="btn btn-primary">Simpan</button>
                                    </td>
                                </tr>
                            </table>
                        </form>
                    </div>
                </div>
            </div>
        </div>

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
        });
    });
</script>
<script type="text/javascript">
    function f_cari_sd() {
        var cari = $('input[name=cari_sd]').val();
        var data = {
            cari_sd: cari,
        };
        $.post('{{ route('monev_baru-gemilang', ['halaman' => 'cari_sd']) }}', data, function(data) {
            $('.wadah_sd').html(data.content);
        });
    }

    function set_sd(npsn, nama_lembaga, nama_ks, hp, alamat) {
        $('.wadah_sd').html('');
        $('input[name=cari_sd]').val('');

        $('input[name=npsn_sd]').val(npsn);
        $('input[name=nama_sd]').val(nama_lembaga);
        $('input[name=nama_ks_sd]').val(nama_ks);
        $('input[name=telp_sd]').val(hp);
        $('input[name=alamat_sd]').val(alamat);
    }
</script>
