<!-- Modal -->
<div id="modal_profil" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Konfirmasi Permohonan Fasilitasi</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12" style="overflow: auto">
                        <table style="width: 100%;border-collapse: collapse;" border="1" class="kop_jadwal">
                            <tr style="background: #4da6ff;">
                                <td style="font-size: 15pt" colspan="4"><b>Data Sekolah</b></td>
                            </tr>
                            <tr>
                                <td>NPSN</td>
                                <td>{{ $sekolah->npsn }}</td>
                            </tr>
                            <tr>
                                <td>Nama Sekolah</td>
                                <td>{{ $sekolah->nama }}</td>
                            </tr>
                            <tr>
                                <td>Alamat</td>
                                <td>{{ $sekolah->alamat }}, {{ $sekolah->kelurahan_dispenduk }},
                                    {{ $sekolah->kecamatan_dispenduk }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-lg-12" style="overflow: auto">
                        <br>
                        <table style="width: 100%;border-collapse: collapse;" border="1" class="kop_jadwal">
                            <tr style="background: #4da6ff;">
                                <td style="font-size: 15pt" colspan="4"><b>Data Permohonan Fasilitasi</b></td>
                            </tr>
                            <tr>
                                <td>Tangal</td>
                                <td>{{ date('d-m-Y', strtotime($jadwal->tanggal)) }}
                                    ({{ date('H:i', strtotime($jadwal->jam_mulai)) }} s/d
                                    {{ date('H:i', strtotime($jadwal->jam_selesai)) }})</td>
                            </tr>
                            <tr>
                                <td>Materi</td>
                                <td>{{ $jadwal->nama_materi }}</td>
                            </tr>
                            <tr>
                                <td>Jenis</td>
                                <td>{{ $jadwal->jenis }}</td>
                            </tr>
                            <tr>
                                <td>Pendamping</td>
                                <td>
                                    <table>
                                        <tr>
                                            <td>Nama</td>
                                            <td>Jabatan</td>
                                            <td>No Hp</td>
                                        </tr>
                                        @foreach ($pendampings as $p)
                                            <tr>
                                                <td>{{ $p->nama_pegawai }}</td>
                                                <td>{{ $p->jabatan }}</td>
                                                <td>{{ $p->hp }} / {{ $p->hp2 }}</td>
                                            </tr>
                                        @endforeach
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td>Keterangan</td>
                                <td>
                                    @php
                                        if ($jadwal->id_materi == '1') {
                                            $ket = '<b>Kegiatan</b><br>' . $jadwal->kegiatan . '<br><br>';
                                            $ket .=
                                                '<b>Penanggungjawab</b><br>' .
                                                $jadwal->nama_penanggung_jawab .
                                                '<br><br>';
                                            $ket .= '<b>Jumlah Bis</b><br>' . $jadwal->jumlah_bis . '<br><br>';
                                            $ket .= '<b>Lokasi Jemput</b><br>' . $jadwal->lokasi_jemput . '<br>';
                                            $ket .= '<b>Lokasi Antar</b><br>' . $jadwal->lokasi_antar . '<br><br>';
                                            $ket .= '<b>Jam Mulai</b><br>' . $jadwal->jam_mulai . '<br>';
                                            $ket .= '<b>Jam Selesai</b><br>' . $jadwal->jam_selesai;
                                        } elseif ($jadwal->id_materi == '3') {
                                            $ket = '<b>Kegiatan</b><br>' . $jadwal->kegiatan;
                                        } elseif ($jadwal->id_materi == '2') {
                                            $ket = '<b>Kerusakan</b><br>' . $jadwal->kerusakan . '<br><br>';
                                            $ket .=
                                                '<a href="https://dispendik.surabaya.go.id/profilsekolah_file/' .
                                                $jadwal->file_bukti_kerusakan .
                                                '" target="_blank" class="btn btn-sm btn-success">Bukti Kerusakan</a>';
                                        } else {
                                            $ket = 'Keterangan';
                                        }

                                        echo $ket;
                                    @endphp
                                </td>
                            </tr>
                            <tr>
                                <td>Surat Permohonan</td>
                                <td>
                                    @if ($jadwal->file_surat_permohonan)
                                        <a style="margin-left: 5px"
                                            href="https://dispendik.surabaya.go.id/profilsekolah_file/{{ $jadwal->file_surat_permohonan }}"
                                            target="_blank" class="btn btn-sm btn-primary">Lihat</a>
                                    @else
                                        Belum upload surat
                                    @endif
                                </td>
                            </tr>
                        </table>
                        <br>
                        <table style="width: 100%;border-collapse: collapse;" border="1" class="kop_jadwal">
                            <tr style="background: #4da6ff;">
                                <td style="font-size: 15pt" colspan="2"><b>Form Konfirmasi</b></td>
                            </tr>
                            <tr>
                                <td>Konfirmasi</td>
                                <td>
                                    <select name="persetujuan" class="form-control" onchange="persetujuan()">
                                        <option value="">..:: Pilih Persetujuan ::..</option>
                                        <option value="Setujui">Setujui</option>
                                        <option value="Reschedule">Reschedule</option>
                                        <option value="Tolak">Tolak</option>
                                    </select>
                                </td>
                            </tr>
                            @if ($jadwal->id_materi != '2')
                                <tr class="tempat_alasan">
                                    <td>Tanggal Pelaksanaan</td>
                                    <td>
                                        <input class="form-control" type="date" name="tgl_pelaksanaan"
                                            value="{{ $jadwal->tanggal }}"
                                            onchange="get_jadwal('{{ $jadwal->tempat_id }}')">
                                    </td>
                                </tr>
                            @endif
                            <tr class="tempat_alasan">
                                <td>Catatan Bunda</td>
                                <td>
                                    <textarea name="catatan" class="form-control"></textarea>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a href="javascript:void(0)" class="btn btn-primary" onclick="simpan()">Simpan</a>
                <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
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
        max-width: 500px !important;
    }
</style>
<script type="text/javascript">
    $(document).ready(function() {
        $('#modal_profil').modal({
            backdrop: 'static'
        });
        persetujuan();
    });

    function persetujuan() {
        var persetujuan = $('select[name=persetujuan]').val();
        if (persetujuan == 'Setujui' || persetujuan == '') {
            $('.tempat_alasan').hide();
        } else {
            $('.tempat_alasan').show();
        }
    }

    function simpan() {
        var persetujuan = $('select[name=persetujuan]').val();
        var tgl = $('input[name=tgl_pelaksanaan]').val();
        var catatan = $('textarea[name=catatan]').val();

        var ijinkan = 1;

        if (persetujuan == '') {
            ijinkan = 0;
        }

        if (persetujuan != 'Setujui') {
            @if ($jadwal->id_materi != '2')
                if (tgl == '' || catatan == '') {
                    ijinkan = 0;
                }
            @else
                if (catatan == '') {
                    ijinkan = 0;
                }
            @endif
        }

        if (ijinkan == 1) {
            Swal.fire({
                type: 'warning',
                title: 'Apakah kamu yakin?',
                showConfirmButton: true,
                showCancelButton: true,
            }).then((result) => {
                if (result.value) {
                    var data = {
                        persetujuan: persetujuan,
                        tgl: tgl,
                        catatan: catatan,
                        id: '{{ $jadwal->id }}',
                    };
                    $.post("{{ route('bunda-fasilitasi-konfirmasi') }}", data, function(data) {
                        if (data.code == '200') {
                            $('.modal-page').html('');
                            $('.modal-backdrop').removeClass('modal-backdrop');
                            get_data();
                            swal('Success', 'Berhasil menyimpan', 'success');
                        } else {
                            swal('Whooop', 'Gagal menyimpan', 'error');
                        }
                    });
                }
            })
        } else {
            swal('Whooops', 'Lengkapi dahulu form persetujuannya', 'error');
        }
    }
</script>
