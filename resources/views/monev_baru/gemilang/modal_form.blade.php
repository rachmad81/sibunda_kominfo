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
                        <form class="form-monev" onsubmit="kirim()">
                            @php
                            @endphp
                            <div>
                                Data Sekolah
                            </div>
                            @php
                                if (!empty($monev)) {
                                    $data_master = [
                                        'npsn_tk' => $monev->npsn_tk ?? '',
                                        'nama_tk' => $monev->nama_tk ?? '',
                                        'nama_ks_tk' => $monev->nama_ks_tk ?? '',
                                        'telp_ks_tk' => $monev->telp_ks_tk ?? '',
                                        'murid_tk' => $monev->murid_tk ?? '',
                                        'guru_tk' => $monev->guru_tk ?? '',
                                        'alamat_tk' => $monev->alamat_tk ?? '',
                                        'npsn_sd' => $monev->npsn_sd ?? '',
                                        'nama_sd' => $monev->nama_sd ?? '',
                                        'nama_ks_sd' => $monev->nama_ks_sd ?? '',
                                        'telp_ks_sd' => $monev->telp_ks_sd ?? '',
                                        'murid_sd' => $monev->murid_sd ?? '',
                                        'guru_sd' => $monev->guru_sd ?? '',
                                        'alamat_sd' => $monev->alamat_sd ?? '',
                                        'tahun' => $monev->tahun ?? '',
                                        'nama_bunda' => $monev->nama_bunda ?? '',
                                        'jabatan' => $monev->jabatan ?? '',
                                        'hp_bunda' => $monev->hp_bunda ?? '',
                                    ];
                                } else {
                                    $data_master = [
                                        'npsn_tk' => $sekolah->npsn ?? '',
                                        'nama_tk' => $sekolah->nama_lembaga ?? '',
                                        'nama_ks_tk' => $data_ks->nama ?? '',
                                        'telp_ks_tk' => $data_ks->hp ?? '',
                                        'murid_tk' => '',
                                        'guru_tk' => '',
                                        'alamat_tk' => $sekolah->alamat ?? '',
                                        'npsn_sd' => '',
                                        'nama_sd' => '',
                                        'nama_ks_sd' => '',
                                        'telp_ks_sd' => '',
                                        'murid_sd' => '',
                                        'guru_sd' => '',
                                        'alamat_sd' => '',
                                        'tahun' => $tahun,
                                        'nama_bunda' => '',
                                        'jabatan' => '',
                                        'hp_bunda' => '',
                                    ];
                                }
                            @endphp
                            <table style="width: 100%">
                                <tr>
                                    <td style="padding-left: 2%">Data Sekolah TK</td>
                                    <td></td>
                                    <td style="padding-left: 2%">Cari Sekolah SD</td>
                                    <td>
                                        <input type="hidden" name="tahun" class="form-control"
                                            value="{{ $tahun }}">
                                        <input type="text" name="cari_sd" class="form-control" onkeyup="f_cari_sd()">
                                        <div class="wadah_sd"></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding-left: 2%">NPSN TK</td>
                                    <td><input type="text" readonly required
                                            value="{{ $data_master['npsn_tk'] ?? '' }}" name="npsn_tk"
                                            class="form-control"></td>
                                    <td style="padding-left: 2%">NPSN SD</td>
                                    <td><input type="text" required value="{{ $data_master['npsn_sd'] ?? '' }}"
                                            name="npsn_sd" class="form-control">
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding-left: 2%">Sekolah TK</td>
                                    <td><input type="text" readonly required
                                            value="{{ $data_master['nama_tk'] ?? '' }}" name="nama_tk"
                                            class="form-control"></td>
                                    <td style="padding-left: 2%">Sekolah SD</td>
                                    <td><input type="text" readonly required
                                            value="{{ $data_master['nama_sd'] ?? '' }}" name="nama_sd"
                                            class="form-control"></td>
                                </tr>
                                <tr>
                                    <td style="padding-left: 2%">Kepala Sekolah TK</td>
                                    <td><input type="text" readonly required
                                            value="{{ $data_master['nama_ks_tk'] ?? '' }}" name="nama_ks_tk"
                                            class="form-control"></td>
                                    <td style="padding-left: 2%">Kepala Sekolah SD</td>
                                    <td><input type="text" readonly required
                                            value="{{ $data_master['nama_ks_sd'] ?? '' }}" name="nama_ks_sd"
                                            class="form-control"></td>
                                </tr>
                                <tr>
                                    <td style="padding-left: 2%">Telp KS TK</td>
                                    <td><input type="text" readonly required
                                            value="{{ $data_master['telp_ks_tk'] ?? '' }}" name="telp_tk"
                                            class="form-control"></td>
                                    <td style="padding-left: 2%">Telp KS SD</td>
                                    <td><input type="text" readonly required
                                            value="{{ $data_master['telp_ks_sd'] ?? '' }}" name="telp_sd"
                                            class="form-control"></td>
                                </tr>
                                <tr>
                                    <td style="padding-left: 2%">Alamat TK</td>
                                    <td><input type="text" readonly required
                                            value="{{ $data_master['alamat_tk'] ?? '' }}" name="alamat_tk"
                                            class="form-control"></td>
                                    <td style="padding-left: 2%">Alamat SD</td>
                                    <td><input type="text" readonly required
                                            value="{{ $data_master['alamat_sd'] ?? '' }}" name="alamat_sd"
                                            class="form-control"></td>
                                </tr>
                                <tr>
                                    <td style="padding-left: 2%">Jumlah Murid TK yang terlibat</td>
                                    <td><input type="text" required name="jumlah_murid_tk" class="form-control"
                                            value="{{ $data_master['murid_tk'] }}"></td>
                                    <td style="padding-left: 2%">Jumlah Murid SD yang terlibat</td>
                                    <td><input type="text" required name="jumlah_murid_sd" class="form-control"
                                            value="{{ $data_master['murid_sd'] }}"></td>
                                </tr>
                                <tr>
                                    <td style="padding-left: 2%">Jumlah Pegawai TK yang terlibat</td>
                                    <td><input type="text" required name="jumlah_pegawai_tk" class="form-control"
                                            value="{{ $data_master['guru_tk'] }}">
                                    </td>
                                    <td style="padding-left: 2%">Jumlah Pegawai SD yang terlibat</td>
                                    <td><input type="text" required name="jumlah_pegawai_sd" class="form-control"
                                            value="{{ $data_master['guru_sd'] }}">
                                    </td>
                                </tr>
                            </table>
                            <div>
                                Data Petugas Survey
                            </div>
                            <table style="width: 100%">
                                <tr>
                                    <td style="padding-left: 2%">Tanggal Monev</td>
                                    <td>
                                        <input type="date" name="tanggal_monev" required class="form-control"
                                            value="{{ $monev->tanggal_monev ?? '' }}">
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding-left: 2%">Tanggal Pelaksanaan</td>
                                    <td>
                                        <input type="date" name="tanggal_pelaksanaan" required
                                            class="form-control" value="{{ $monev->tanggal_pelaksanaan ?? '' }}">
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding-left: 2%">Mulai</td>
                                    <td>
                                        <input type="time" step="1" name="mulai" required
                                            class="form-control" value="{{ $monev->mulai ?? '' }}">
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding-left: 2%">Selesai</td>
                                    <td>
                                        <input type="time" step="1" name="selesai" required
                                            class="form-control" value="{{ $monev->selesai ?? '' }}">
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding-left: 2%">Nama Petugas</td>
                                    <td>
                                        <input type="text" name="nama_petugas" required class="form-control"
                                            value="{{ $data_master['nama_bunda'] }}">
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding-left: 2%">Jabatan Petugas</td>
                                    <td>
                                        <input type="text" required name="jabatan_petugas" class="form-control"
                                            value="{{ $data_master['jabatan'] }}">
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding-left: 2%">Telp Petugas</td>
                                    <td>
                                        <input type="text" required name="telp_petugas" class="form-control"
                                            value="{{ $data_master['hp_bunda'] }}">
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">
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
