<!-- Modal -->
<div id="modal_profil" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Monitor Jaspel</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12" style="overflow:auto">
                        <form class="form-monev" onsubmit="kirim()">
                            @php
                                $status_ijop = ['checked', ''];
                                $status_kbm = ['checked', ''];
                                if (!empty($monev)) {
                                    $data_lembaga = [
                                        'npsn' => $monev->npsn ?? '',
                                        'nama_lembaga' => $monev->nama_lembaga ?? '',
                                        'akreditasi' => $monev->akreditasi ?? '',
                                        'alamat' => $monev->alamat_lembaga ?? '',
                                        'kecamatan' => $monev->kecamatan ?? '',
                                        'nama_ks' => $monev->nama_ks ?? '',
                                        'tgl_ijop' => $monev->tgl_berakhir_ijop ?? '',
                                        'ijop' => $monev->ijop ?? '',
                                        'kbm' => $monev->kbm ?? '',
                                        'petugas_survey' => $monev->petugas_survey ?? '',
                                    ];
                                    $status_ijop =
                                        $data_lembaga['ijop'] == 'Tidak ada' ? ['', 'checked'] : $status_ijop;
                                    $status_kbm = $data_lembaga['kbm'] == 'Tidak ada' ? ['', 'checked'] : $status_kbm;
                                } else {
                                    $alamat = $sekolah->alamat ?? '';
                                    $alamat .= ', RT ' . $sekolah->rt ?? '';
                                    $alamat .= ', RW ' . $sekolah->rw ?? '';
                                    $alamat .= ', Kelurahan ' . $sekolah->kelurahan_dispenduk ?? '';
                                    $alamat .= ', Kecmaatan ' . $sekolah->kecamatan_dispenduk ?? '';
                                    $data_lembaga = [
                                        'npsn' => $sekolah->npsn ?? '',
                                        'nama_lembaga' => $sekolah->nama_lembaga ?? '',
                                        'akreditasi' => $sekolah->akreditasi ?? '',
                                        'alamat' => $alamat ?? '',
                                        'kecamatan' => $sekolah->kecamatan_dispenduk ?? '',
                                        'nama_ks' => $data_ks->nama ?? '',
                                        'tgl_ijop' => $data_ijop->tgl_izin_operasional ?? '',
                                        'petugas_survey' => '',
                                    ];

                                    $value_status_ijop = $data_ijop->tgl_izin_operasional ?? 'kosong';
                                    $status_ijop = $value_status_ijop != 'kosong' ? ['checked', ''] : ['', 'checked'];
                                }
                            @endphp

                            <table class="table table-bordered">
                                <tr>
                                    <td>Tanggal Monev</td>
                                    <td>
                                        <input required type="hidden" class="form-control" name="tahun"
                                            value="{{ $tahun }}">
                                        <input required type="date" class="form-control" name="tgl_monev"
                                            value="{{ $monev->tgl_monev ?? '' }}">
                                    </td>
                                </tr>
                                <tr>
                                    <td>NPSN</td>
                                    <td>
                                        <input required type="text" class="form-control" name="npsn"
                                            value="{{ $data_lembaga['npsn'] }}">
                                    </td>
                                </tr>
                                <tr>
                                    <td>Nama Lembaga</td>
                                    <td>
                                        <input required type="text" class="form-control" name="nama_lembaga"
                                            value="{{ $data_lembaga['nama_lembaga'] }}">
                                    </td>
                                </tr>
                                <tr>
                                    <td>Akreditasi</td>
                                    <td>
                                        <input required type="text" class="form-control" name="akreditasi"
                                            value="{{ $data_lembaga['akreditasi'] }}">
                                    </td>
                                </tr>
                                <tr>
                                    <td>Alamat</td>
                                    <td>
                                        <textarea required class="form-control" name="alamat">{{ $data_lembaga['alamat'] }}</textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Kecamatan</td>
                                    <td>
                                        <input required type="text" class="form-control" name="kecamatan"
                                            value="{{ $data_lembaga['kecamatan'] }}">
                                    </td>
                                </tr>
                                <tr>
                                    <td>Nama Kepala Sekolah</td>
                                    <td>
                                        <input required type="text" class="form-control" name="nama_ks"
                                            value="{{ $data_lembaga['nama_ks'] }}">
                                    </td>
                                </tr>
                                <tr>
                                    <td>Ijin Operasional</td>
                                    <td>
                                        <label><input required type="radio" name="status_ijop" value="Ada"
                                                {{ $status_ijop[0] }}>
                                            Ada</label>
                                        <br>
                                        <label><input required type="radio" name="status_ijop" value="Tidak ada"
                                                {{ $status_ijop[1] }}> Tidak
                                            ada</label>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Tanggal Berakhir Ijop</td>
                                    <td>
                                        <input required type="date" class="form-control" name="tgl_ijop"
                                            value="{{ $data_lembaga['tgl_ijop'] }}">
                                    </td>
                                </tr>
                                <tr>
                                    <td>Kegiatan Belajar Mengajar</td>
                                    <td>
                                        <label><input required type="radio" name="status_kbm" value="Ada"
                                                {{ $status_kbm[0] }}>
                                            Ada</label>
                                        <br>
                                        <label><input required type="radio" name="status_kbm" value="Tidak ada"
                                                {{ $status_kbm[1] }}> Tidak
                                            ada</label>
                                    </td>
                                </tr>
                            </table>

                            {{-- PERTANYAAN --}}
                            <table class="table table-bordered">
                                <tr>
                                    <td>1. Berapa jumlah murid di Profil Sekolah?</td>
                                </tr>
                                <tr>
                                    <td>
                                        <input required name="jumlah_murid_profil" type="text" class="form-control"
                                            value="{{ $monev->jumlah_murid_profil ?? $jumlah_siswa }}">
                                    </td>
                                </tr>

                                <tr>
                                    <td>2. Berapa jumlah murid di Dapodik?</td>
                                </tr>
                                <tr>
                                    <td>
                                        <input required name="jumlah_murid_dapodik" type="text" class="form-control"
                                            value="{{ $monev->jumlah_murid_dapodik ?? '' }}">
                                    </td>
                                </tr>

                                <tr>
                                    <td>3. Apakah ada daftar hadir murid?</td>
                                </tr>
                                <tr>
                                    <td>
                                        <input required name="daftar_hadir_siswa" type="text" class="form-control"
                                            value="{{ $monev->daftar_hadir_siswa ?? '' }}">
                                    </td>
                                </tr>

                                <tr>
                                    <td>4. Berapa jumlah murid di daftar kehadiran lembaga?</td>
                                </tr>
                                <tr>
                                    <td>
                                        <input required name="jumlah_murid_daftar" type="text" class="form-control"
                                            value="{{ $monev->jumlah_murid_daftar ?? '' }}">
                                    </td>
                                </tr>

                                <tr>
                                    <td>5. Berapa jumlah murid riil saat pelaksanaan KBM?</td>
                                </tr>
                                <tr>
                                    <td>
                                        <input required name="jumlah_murid_riil" type="text" class="form-control"
                                            value="{{ $monev->jumlah_murid_riil ?? '' }}">
                                    </td>
                                </tr>

                                <tr>
                                    <td>6. Apakah jumlah murid Dapodik dan Profil Sekolah sesuai?</td>
                                </tr>
                                <tr>
                                    <td>
                                        <input required name="status_jumlah_siswa" type="text" class="form-control"
                                            value="{{ $monev->status_jumlah_siswa ?? '' }}">
                                    </td>
                                </tr>

                                <tr>
                                    <td>7. Jika tidak sesuai, sebutkan alasannya!</td>
                                </tr>
                                <tr>
                                    <td>
                                        <input required name="alasan_status" type="text" class="form-control"
                                            value="{{ $monev->alasan_status ?? '' }}">
                                    </td>
                                </tr>

                                <tr>
                                    <td>8. Berapa jumlah (Rp) penerimaan Jaspel bulan Lalu?</td>
                                </tr>
                                <tr>
                                    <td>
                                        <input required name="penerimaan_jaspel" type="text" class="form-control"
                                            value="{{ $monev->penerimaan_jaspel ?? '' }}">
                                    </td>
                                </tr>

                                <tr>
                                    <td>9. Berapa penerimaan Jaspel tiap orang?</td>
                                </tr>
                                <tr>
                                    <td>
                                        <input required name="penerimaan_jaspel_orang" type="text"
                                            class="form-control" value="{{ $monev->penerimaan_jaspel_orang ?? '' }}">
                                    </td>
                                </tr>

                                <tr>
                                    <td>10. Apakah ada pemotongan jaspel langsung dari Kepala Sekolah atau pihak lain?
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <input required name="pemotongan_jaspel" type="text" class="form-control"
                                            value="{{ $monev->pemotongan_jaspel ?? '' }}">
                                    </td>
                                </tr>

                                <tr>
                                    <td>11. Apakah tiap penerima Jaspel sudah tanda tangan konfirmasi penerimaan Jaspel
                                        bulan lalu?</td>
                                </tr>
                                <tr>
                                    <td>
                                        <input required name="ttd_penerimaan" type="text" class="form-control"
                                            value="{{ $monev->ttd_penerimaan ?? '' }}">
                                    </td>
                                </tr>

                                <tr>
                                    <td>Catatan</td>
                                </tr>
                                <tr>
                                    <td>
                                        <textarea required name="catatan" class="form-control">{{ $monev->catatan ?? '' }}</textarea>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Petugas Survey</td>
                                </tr>
                                <tr>
                                    <td>
                                        <input required name="petugas_survey" type="text" class="form-control"
                                            value="{{ $monev->petugas_survey ?? '' }}">
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <button type="submit" class="btn btn-primary">Simpan</button>
                                        <button type="button" class="btn btn-warning"
                                            data-dismiss="modal">Tutup</button>
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
<script type="text/javascript"></script>
