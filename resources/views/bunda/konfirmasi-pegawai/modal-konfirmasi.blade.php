<!-- Modal -->
<div id="modal_pegawai" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <form action="{{ route('bunda-konfirmasi-pegawai-konfirmasi') }}" method="POST">
            <div class="modal-content" style="background: #fff !important">
                <div class="modal-header">
                    <h4 class="modal-title">Data Pegawai</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <table id="dataTablePegawai" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th width="30%"><strong>Keterangan</strong></th>
                                        <th><strong>Isi</strong></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><strong>Jenis Pengajuan</strong></td>
                                        <td>{{ $data->nama_pg_jenis }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Keperluan</strong></td>
                                        <td>
                                            @switch($data->keperluan)
                                                @case('1')
                                                    Entry data Profil Sekolah, SKPBM dan Dapodik
                                                @break

                                                @case('2')
                                                    Entry data Dapodik
                                                @break

                                                @default
                                                    -
                                                @break
                                            @endswitch
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan=2><strong>Biodata</strong></td>
                                    </tr>
                                    <tr>
                                        <td><strong>KK Asal</strong></td>
                                        <td>{{ $data->kk_asal }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>NIK</strong></td>
                                        <td>{{ $data->nik }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Nama Pegawai</strong></td>
                                        <td>{{ $data->nama_pegawai }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>TTL</strong></td>
                                        <td>{{ $data->tempat_lahir }}, {{ $data->tgl_lahir }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Jenis Kelamin</strong></td>
                                        <td>{{ $data->jenis_kelamin }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Agama</strong></td>
                                        <td>{{ $data->agama }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Nama Ibu Kandung</strong></td>
                                        <td>{{ $data->nama_ibu_kandung }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Status Perkawinan</strong></td>
                                        <td>{{ $data->status_perkawinan }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Kualifikasi Pendidikan</strong></td>
                                        <td>{{ $data->kualifikasi_pendidikan }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Email</strong></td>
                                        <td>{{ $data->email }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan=2><strong>Alamat Domisili</strong></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Provinsi Domisili</strong></td>
                                        <td>{{ $data->provinsi_domisili }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Kab/Kota Domisili</strong></td>
                                        <td>{{ $data->kab_kota_domisili }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Kelurahan Domisili</strong></td>
                                        <td>{{ $data->kelurahan_domisili }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Kecamatan Domisili</strong></td>
                                        <td>{{ $data->kecamatan_domisili }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>RT/RW Domisili</strong></td>
                                        <td>{{ $data->rt_domisili }}/{{ $data->rw_domisili }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Alamat Domisili</strong></td>
                                        <td>{{ $data->alamat_domisili }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Kode POS</strong></td>
                                        <td>{{ $data->kode_pos }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan=2><strong>Kepegawaian</strong></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Status Kepegawaian</strong></td>
                                        <td>{{ $data->status_kepegawaian }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Jenis PTK</strong></td>
                                        <td>{{ $data->jenis_ptk }}</td>
                                    </tr>
                                    @if ($data->jenis_ptk == 'Guru' or $data->jenis_ptk == 'Tendik' or $data->jenis_ptk == 'Pelatih')
                                        <tr>
                                            <td><strong>Detail PTK</strong></td>
                                            <td>{{ $data->keterangan_ptk }}</td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <td><strong>NIP</strong></td>
                                        <td>{{ $data->nip }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>NUPTK</strong></td>
                                        <td>{{ $data->nuptk }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Lembaga Pengangkatan</strong></td>
                                        <td>{{ $data->lembaga_pengangkatan }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>SK Pengangkatan</strong></td>
                                        <td>{{ $data->no_sk_pengangkatan }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>TMT SK Pengangkatan</strong></td>
                                        <td>{{ $data->tmt_pengangkatan }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Sumber Gaji</strong></td>
                                        <td>{{ $data->sumber_gaji }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Sudah Lisensi Kepala Sekolah</strong></td>
                                        <td>{{ $data->sudah_lisensi_ks }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan=2><strong>Penugasan</strong></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Wilayah</strong></td>
                                        <td>Kec. {{ $data->kecamatan_lembaga_tujuan }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Sekolah</strong></td>
                                        <td>{{ '( ' . $data->npsn_tujuan . ' )' }} {{ $data->lembaga_tujuan }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Status Penugasan</strong></td>
                                        <td>{{ $data->is_induk ? 'Induk' : 'Non Induk' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>No Surat Tugas</strong></td>
                                        <td>{{ $data->no_surat_tugas }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Tanggal Surat Tugas</strong></td>
                                        <td>{{ $data->tgl_surat_tugas }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>TMT Surat Tugas</strong></td>
                                        <td>{{ $data->tmt_tugas }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Keterangan Tambahan</strong></td>
                                        <td>{{ $data->keterangan_pengajuan }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-lg-12">
                            <table class="table table-bordered table-striped table-hover ">
                                <thead>
                                    <tr>
                                        <th width="5%">No</th>
                                        <th>Lampiran</th>
                                        <th>File</th>
                                        <th>Kesesuaian</th>
                                        <th>Catatan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $i = 1; ?>
                                    @foreach ($lampiran as $r)
                                        <tr>
                                            <td>{{ $i++ }}</td>
                                            <td width="50%">
                                                {{ $r->nama_jenis }}
                                            </td>
                                            <td>
                                                <div id="hasil_{{ $r->id_pg_transaksi_berkas }}">
                                                    @if ($r->file == '')
                                                        -
                                                    @else
                                                        <?php $link_file = $r->link; ?>
                                                        <a target="_blank"
                                                            href="{{ str_replace('https://filesdispendik.surabaya.go.id', 'https://dispendik.surabaya.go.id', $link_file) }}"
                                                            title="View Link" <font color="blue"><i
                                                                class="fa fa-eye"></i></font>
                                                        </a>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <select name="kesesuaian_{{ $r->id_pg_transaksi_berkas }}"
                                                    id="kesesuaian_{{ $r->id_pg_transaksi_berkas }}" required
                                                    class="form-control kesesuaian"
                                                    data_id="{{ $r->id_pg_transaksi_berkas }}">
                                                    <option value="" selected disabled>-- Pilih Kesesuaian--
                                                    </option>
                                                    <option value="true">Sesuai</option>
                                                    <option value="false">Tidak Sesuai</option>
                                                </select>
                                            </td>
                                            <td>
                                                <input type="text" name="catatan_{{ $r->id_pg_transaksi_berkas }}"
                                                    id="catatan_{{ $r->id_pg_transaksi_berkas }}" class="form-control"
                                                    style="display:none;"
                                                    placeholder="Catatan jika pengajuan ditolak...">
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="col-lg-12">
                            <select name="kesesuaian" id="kesesuaian" required class="form-control kesesuaian">
                                <option value="" selected disabled>-- Pilih Kesesuaian--
                                </option>
                                <option value="true">Sesuai</option>
                                <option value="false">Tidak Sesuai</option>
                            </select>
                            <input type="text" name="catatan" id="catatan" class="form-control"
                                style="display:none;" placeholder="Catatan jika pengajuan ditolak...">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    @csrf
                    <input type="text" name="id" value={{ $data->id_pg_transaksi_gtk }} style="display:none;">
                    <button type="submit" class="btn btn-primary">Konfirmasi</button>
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
</style>
<script type="text/javascript">
    $(document).ready(function() {
        $('#modal_pegawai').modal('show');
    });
    var kesesuaianInputs = document.querySelectorAll('.kesesuaian');
    kesesuaianInputs.forEach((kesesuaianInput) => {
        kesesuaianInput.addEventListener("change", (event) => {
            if (event.target.getAttribute('id') == 'kesesuaian') {
                var catatanInput = document.querySelector('#catatan');
            } else {
                var catatanInput = document.querySelector('#catatan_' + event.target.getAttribute(
                    'data_id'));
            }
            if (event.target.value == "true") {
                catatanInput.style.display = "none";
                catatanInput.removeAttribute("required");
            } else {
                catatanInput.style.display = "block";
                catatanInput.setAttribute("required", "true");
            }
        });
    })
</script>
