<!-- Modal -->
<div id="modal_profil" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Setting jadwal Talenta Seni</h4>
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
                                <td>{{ $jadwal->npsn }}</td>
                            </tr>
                            <tr>
                                <td>Nama Sekolah</td>
                                <td>{{ $jadwal->nama_sekolah }}</td>
                            </tr>
                            <tr>
                                <td>Alamat</td>
                                <td>{{ $jadwal->kelurahan }},
                                    {{ $jadwal->kecamatan }}, Kota Surabaya</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-8">
                            <br>
                            <table style="width: 100%;border-collapse: collapse;" border="1" class="kop_jadwal">
                                <tr style="background: #4da6ff;">
                                    <td style="font-size: 15pt" colspan="5">
                                        <b>Jadwal lokasi</b>
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
                                                                <b>* Penyesuain jadwal dapat mempertimbangkan penataan tempat pelaksanaan berikut</b> <a
                                                                href="{{ asset('mall.pdf') }}" target="_blank"
                                                                class="btn btn-sm btn-primary">Klik sini</a>
                                                            </td>
                                                        </tr>
                                                        <tr style="background: #4da6ff;">
                                                            <td style="font-size: 15pt" colspan="2"><b>Penetapan jadwal talenta seni</b>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>Tempat Pelaksanaan</td>
                                                            <td>
                                                                <select class="form-control" name="tempat_pelaksanaan" onchange="get_jadwal('{{$jadwal->id}}')">
                                                                    @foreach($mall as $m)
                                                                    <option value="{{$m->id}}">{{$m->nama}}</option>
                                                                    @endforeach
                                                                </select>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>Tanggal Pelaksanaan</td>
                                                            <td>
                                                                <select class="form-control" name="tanggal" onchange="get_jadwal('{{$jadwal->id}}')">
                                                                </select>
                                                                {{-- <input class="form-control" name="tanggal" type="text" readonly> --}}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>Waktu Pelaksanaan</td>
                                                            <td>
                                                                <input type="text" readonly name="jam_mulai"> s.d                     
                                                                <input type="text" readonly name="jam_selesai">                    
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>Batas Kuota (Disetujui/Batas)</td>
                                                            <td>
                                                                <span class="jumlah_peserta"></span>
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
                                        <th>Status Jadwal</th>
                                    </tr>
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
                                            <option value="Penyesuaian Dokumen">Penyesuaian Dokumen</option>
                                            @if ($jadwal->is_locked != true)
                                            <option value="Reschedule">Reschedule</option>
                                            <option value="Tolak">Tolak</option>
                                            @endif
                                        </select>
                                        *Jika memilih <strong>"Penyesuaian Dokumen"</strong>,<br>
                                        maka di <strong>sekolah</strong> akan tampil<br>
                                        Pengajuan <strong>Jadwal Sudah Disetujui</strong>,<br>
                                        Tetapi dokumen masih perlu diperbaiki
                                    </td>
                                </tr>
                                <tr class="tempat_alasan">
                                    <td>Catatan Bunda <i style="color: #f00;">*</i></td>
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
            get_jadwal('{{$jadwal->id}}');
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
            var tgl = $('select[name=tanggal]').val();
            var mulai = $('input[name=jam_mulai]').val();
            var selesai = $('input[name=jam_selesai]').val();
            var catatan = $('textarea[name=catatan]').val();
            var tempat = $('select[name=tempat_pelaksanaan]').val();

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
                            mulai:mulai,
                            selesai:selesai,
                            tempat_id:tempat,
                            id: '{{ $jadwal->id }}',
                        };
                        $.post("{{ route('bunda-talenta-konfirmasi') }}", data, function(data) {
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
