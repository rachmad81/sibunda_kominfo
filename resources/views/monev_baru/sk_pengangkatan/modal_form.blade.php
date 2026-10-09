<!-- Modal -->
<style>
    table tr td {
        vertical-align: top;
    }
</style>
<div id="modal_profil" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Monitor SK Pengangkatan</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12" style="overflow:auto">
                        <form method="post" class="form-monev" onsubmit="kirim()">
                            <table style="width: 100%;border-collapse: collapse">
                                <tr>
                                    <td>Nama Lembaga</td>
                                    <td>{{ $sekolah->nama_lembaga }} ({{ $sekolah->npsn }})</td>
                                </tr>
                                <tr>
                                    <td>Jumlah GTK</td>
                                    <td>{{ $gtk->count() }}</td>
                                </tr>
                                <tr>
                                    <td>Jumlah KS</td>
                                    <td>{{ $gtk->where('jenis_pegawai', 'KS')->count() }}</td>
                                </tr>
                                <tr>
                                    <td>Jumlah Guru</td>
                                    <td>{{ $gtk->where('jenis_pegawai', 'Guru')->count() }}</td>
                                </tr>
                                <tr>
                                    <td>Jumlah Tendik</td>
                                    <td>{{ $gtk->where('jenis_pegawai', 'Tendik')->count() }}</td>
                                </tr>
                            </table>
                            <br>
                            <h4>Data Pegawai</h4>
                            <table style="width: 100%;border-collapse: collapse" border="1">
                                <thead>
                                    <tr>
                                        <th>Nama</th>
                                        <th>Jenis Pegawai</th>
                                        <th>Status</th>
                                        <th>Berkas</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($gtk as $gt)
                                        @php
                                            $data_siagus = collect($siagus)->where(
                                                'id_pg_trasnsaksi_gtk',
                                                $gt->pengajuan_gtk_id,
                                            );
                                            $status_data_siagus = collect($data_siagus)->first();
                                        @endphp
                                        <tr>
                                            <td>{{ $gt->nama }}</td>
                                            <td>{{ $gt->jenis_pegawai }}</td>
                                            <td>{{ $status_data_siagus->status_pengajuan ?? 'Tidak ada pengajuan' }}
                                            </td>
                                            <td>
                                                <table style="width: 100%;border-collapse: collapse" border="1">
                                                    <thead>
                                                        <tr>
                                                            <th>Jenis Berkas</th>
                                                            <th>Kesesuaian</th>
                                                            <th>File</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @if ($data_siagus->count() > 0)
                                                            @foreach ($data_siagus as $ds)
                                                                <tr>
                                                                    <td>{{ $ds->jenis_berkas }}</td>
                                                                    <td>{{ $ds->kesesuaian }}</td>
                                                                    <td>
                                                                        @php
                                                                            $url_file = str_replace(
                                                                                'https://filesdispendik.surabaya.go.id/siagus/',
                                                                                'https://dispendik.surabaya.go.id/siagus_file/',
                                                                                $ds->link,
                                                                            );
                                                                        @endphp
                                                                        <a href="{{ $url_file }}"
                                                                            target="_blank">Lihat File</a>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        @else
                                                            <tr>
                                                                <td colspan="3">Tidak ada pengajuan disiagus</td>
                                                            </tr>
                                                        @endif
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <h4>Data Pegawai Menerima Jaspel</h4>
                            <table style="width: 100%;border-collapse: collapse" border="1">
                                <thead>
                                    <tr>
                                        <th>Nama</th>
                                        <th>Jenis Pegawai</th>
                                        <th>Status</th>
                                        <th>Berkas</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($jaspel as $gt)
                                        @php
                                            $data_siagus = collect($siagus)->where(
                                                'id_pg_trasnsaksi_gtk',
                                                $gt->pengajuan_gtk_id,
                                            );
                                            $status_data_siagus = collect($data_siagus)->first();
                                        @endphp
                                        <tr>
                                            <td>{{ $gt->nama }}</td>
                                            <td>{{ $gt->jenis_pegawai }}</td>
                                            <td>{{ $status_data_siagus->status_pengajuan ?? 'Tidak ada pengajuan' }}
                                            </td>
                                            <td>
                                                <table style="width: 100%;border-collapse: collapse" border="1">
                                                    <thead>
                                                        <tr>
                                                            <th>Jenis Berkas</th>
                                                            <th>Kesesuaian</th>
                                                            <th>File</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @if ($data_siagus->count() > 0)
                                                            @foreach ($data_siagus as $ds)
                                                                <tr>
                                                                    <td>{{ $ds->jenis_berkas }}</td>
                                                                    <td>{{ $ds->kesesuaian }}</td>
                                                                    <td><a href="{{ $ds->link }}"
                                                                            target="_blank">Lihat File</a></td>
                                                                </tr>
                                                            @endforeach
                                                        @else
                                                            <tr>
                                                                <td colspan="3">Tidak ada pengajuan disiagus</td>
                                                            </tr>
                                                        @endif
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div style="width: 100%">
                                <label for="">Nomor SK</label>
                                <input type="text" class="form-control" name="nomor"
                                    value="{{ $monev->nomor ?? '' }}">
                                <label for="">Tanggal Verifikasi</label>
                                <input type="date" class="form-control" name="tanggal_verifikasi"
                                    value="{{ $monev->tanggal_verifikasi ?? date('Y-m-d') }}">
                                <label for="">SK Pengangkatan</label>
                                <input type="hidden" name="npsn" value="{{ $sekolah->npsn }}">
                                <input type="hidden" name="status_sk_pengangkatan" value="Disetujui">
                                <button type="submit" class="btn btn-primary">Terbitkan SK Pengangkatan</button>
                                <button type="button" data-dismiss="modal" class="btn btn-warning">Tutup</button>
                            </div>
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
