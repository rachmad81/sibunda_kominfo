<!-- Modal -->
<div id="modal_profil" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Konfirmasi Jadwal Edu City Tour</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12">
                        <table style="width: 100%;border-collapse: collapse;" border="1" class="kop_jadwal">
                            <tr style="background: #4da6ff;">
                                <td style="font-size: 15pt" colspan="2"><b>Data Sekolah</b></td>
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
                            <tr>
                                <td>Tanggal Pengajuan</td>
                                <td>{{ $jadwal->tanggal_pengajuan ? now()->createFromFormat('Y-m-d H:i:s', $jadwal->tanggal_pengajuan)->format('d-m-y H:i:s') : '' }}
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
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-8">
                        <br>
                        <table style="width: 100%;border-collapse: collapse;" border="1" class="kop_jadwal">
                            <tr style="background: #4da6ff;">
                                <td style="font-size: 15pt" colspan="4">
                                    <b>Jadwal wisata pada tanggal <span
                                            id="tempat_tanggal">{{ date('d-m-Y', strtotime($jadwal->tanggal)) }}</span></b>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="5">
                                    <table style="width: 100%">
                                        <tr>
                                            <td style="padding: 0px">
                                                <table style="width: 100%;border-collapse: collapse;" border="1">
                                                    <tr>
                                                        <td colspan="2" style="color: #ff4d4d">
                                                            <b>Baris dengan warna <i
                                                                    style="background: #bfee9a;color: black">Hijau</i>
                                                                adalah sekolah yg sedang dibuka pengajuannya</b>
                                                            <br>
                                                            <b>* Penyesuain jadwal dapat mempertimbangkan dengan
                                                                kalender akademik berikut</b> <a
                                                                href="{{ asset('ka.pdf') }}" target="_blank"
                                                                class="btn btn-sm btn-primary">Klik sini</a>
                                                        </td>
                                                    </tr>
                                                    <tr style="background: #4da6ff;">
                                                        <td style="font-size: 15pt" colspan="2"><b>Tujuan Wisata</b>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>Tempat Wisata</td>
                                                        <td>{{ $jadwal->nama_tempat }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Alamat</td>
                                                        <td>{{ $jadwal->alamat_tempat }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Tanggal Pelaksanaan</td>
                                                        <td>{{ date('d-m-Y', strtotime($jadwal->tanggal)) }}
                                                            ({{ date('H:i', strtotime($jadwal->jam_mulai)) }} s/d
                                                            {{ date('H:i', strtotime($jadwal->jam_selesai)) }})</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Kuota</td>
                                                        <td>
                                                            <b>{{ $jadwal->kapasitas_pd }}</b>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>Penanggung Jawab</td>
                                                        <td>
                                                            {{ $jadwal->nama_penanggung_jawab }}
                                                            ({{ $jadwal->jabatan_penanggung_jawab }})<br>{{ $jadwal->no_hp_penanggung_jawab }}
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tbody id="tempat_jadwal_lain">
                                <tr>
                                    <th>NPSN</th>
                                    <th>Nama Sekolah</th>
                                    <th>Jumlah PD</th>
                                    <th>Jam Pelaksanaan</th>
                                    <th>Status Jadwal</th>
                                </tr>
                                @if ($sama->count() != 0)
                                    @foreach ($sama as $s)
                                        <tr style="background: @if ($s->npsn == $sekolah->npsn) #bfee9a @endif">
                                            <td>{{ $s->npsn }}</td>
                                            <td>
                                                @php
                                                    $nama_sekolah = $sekolah_lain->where('npsn', $s->npsn)->first();
                                                    echo $nama_sekolah->nama;
                                                @endphp
                                            </td>
                                            <td>{{ $s->jumlah_pd }}</td>
                                            <td>{{ date('H:i', strtotime($s->jam_mulai)) }} s/d
                                                {{ date('H:i', strtotime($s->jam_selesai)) }}</td>
                                            <td>{{ $s->status_jadwal }}</td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="4">Data kosong</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <div class="col-lg-4">
                        <br>
                        <table style="width: 100%;border-collapse: collapse;" border="1" class="kop_jadwal">
                            <tr style="background: #4da6ff;">
                                <td style="font-size: 15pt" colspan="2"><b>Form Konfirmasi</b></td>
                            </tr>
                            <tr>
                                <td>Konfirmasi <i style="color: #f00;">*</i></td>
                                <td>
                                    <select name="persetujuan" class="form-control" onchange="persetujuan()">
                                        <option value="">..:: Pilih Persetujuan ::..</option>
                                        <option value="Setujui">Setujui</option>
                                        <option value="Reschedule">Reschedule</option>
                                        <option value="Tolak">Tolak</option>
                                        <option value="Draft">Draft</option>
                                    </select>
                                </td>
                            </tr>
                            <tr class="tempat_alasan">
                                <td>Tanggal Pelaksanaan <i style="color: #f00;">*</i></td>
                                <td>
                                    <input id="reschedule_date" class="form-control" type="date"
                                        name="tgl_pelaksanaan" value="{{ $jadwal->tanggal }}"
                                        onchange="get_jadwal('{{ $jadwal->tempat_id }}')"
                                        min="{{ now()->format('Y-m-d') }}">
                                    <p id="reschedule_date_alert" style="color:red;" />
                                </td>
                            </tr>
                            <tr class="tempat_alasan">
                                <td>Catatan Bunda <i style="color: #f00;">*</i></td>
                                <td>
                                    <textarea name="catatan" class="form-control"></textarea>
                                </td>
                            </tr>
                        </table>
                        <br>
                        @if ($jadwal_bis)
                            <div class="alert alert-warning">
                                <div class="row">
                                    <div style="margin-left:3%;">
                                        <i class="si si-info"></i>
                                    </div>
                                    <div style="margin-left:3%;">
                                        <strong>
                                            Sekolah ini juga mengajukan permohonan bis<br>pada tanggal pelaksanaan
                                            Educity Tour ini!
                                        </strong>
                                    </div>
                                </div>
                            </div>
                        @endif
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
        max-width: 1200px !important;
    }
</style>
<script type="text/javascript">
    $('#reschedule_date').change(function() {
        let reschedule_date = new Date(this.value).getDate();
        let reschedule_month = new Date(this.value).getMonth() + 1;
        if (reschedule_date < 10 || reschedule_date > 26) {
            $('#reschedule_date').val('');
            $('#reschedule_date_alert').html(
                "Tanggal Reschedule tidak dapat berada di tanggal yang kurang dari tanggal 10 dan lebih dari 26"
            );
            return;
        }
        if (reschedule_month == 6 || reschedule_month == 7) {
            $('#reschedule_date').val('');
            $('#reschedule_date_alert').html(
                "Tanggal Reschedule tidak dapat berada di bulan Juni atau Juli"
            );
            return;
        }
        $('#reschedule_date_alert').html('');

    })

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
            if (tgl == '' || catatan == '') {
                ijinkan = 0;
            }
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
                    $.post("{{ route('bunda-edu-konfirmasi') }}", data, function(data) {
                        if (data.code == '200') {
                            $('.modal-page').html('');
                            $('.modal-backdrop').removeClass('modal-backdrop');
                            get_data();
                            swal('Success', data.message, 'success');
                        } else {
                            swal('Whooop', data.message, 'error');
                        }
                    });
                }
            })
        } else {
            swal('Whooops', 'Lengkapi dahulu form persetujuannya', 'error');
        }
    }
</script>
