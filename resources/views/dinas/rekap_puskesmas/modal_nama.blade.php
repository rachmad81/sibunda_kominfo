<div id="modal_nama" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg" style="max-width: 1100px;">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{ $judul }}</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body" style="max-height: 70vh; overflow: auto">
                <p>
                    <a href="javascript:void(0)" data-toggle="collapse" data-target="#collapse_sekolah">
                        <i class="fa fa-caret-right"></i> Lihat Daftar Sekolah ({{ $sekolah->count() }})
                    </a>
                </p>
                <div class="collapse" id="collapse_sekolah">
                    <div style="overflow-x: auto">
                        <table id="tabel_modal_sekolah" class="table table-bordered table-striped" style="width: 100%">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Sekolah</th>
                                    <th>NPSN</th>
                                    <th>Jenjang</th>
                                    <th>Kelurahan</th>
                                    <th>Puskesmas</th>
                                    <th>Posyandu</th>
                                    <th>Jumlah Murid</th>
                                    <th>Akan Diperiksa</th>
                                    <th>Sudah Diperiksa</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($sekolah as $i => $s)
                                    <tr>
                                        <td>{{ $i + 1 }}</td>
                                        <td>{{ $s->nama_sekolah }}</td>
                                        <td>{{ $s->npsn }}</td>
                                        <td>{{ $s->jenjang_label }}</td>
                                        <td>{{ $s->kelurahan_dispenduk }}</td>
                                        <td>{{ $s->nama_puskesmas }}</td>
                                        <td>{{ $s->nama_posyandu }}</td>
                                        <td>{{ $s->jumlah_murid }}</td>
                                        <td>{{ $s->akan_diperiksa }}</td>
                                        <td>{{ $s->sudah_diperiksa }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10">Data kosong</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <hr>
                </div>
                <div style="overflow-x: auto">
                    <table id="tabel_modal_murid" class="table table-bordered table-striped" style="width: 100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>L/P</th>
                                <th>Usia</th>
                                <th>Nama Sekolah</th>
                                <th>Kelurahan</th>
                                <th>Tanggal Kunjungan</th>
                                <th>Status Diperiksa</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($data as $i => $row)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td>{{ $row->nama }}</td>
                                    <td>{{ $row->jenis_kelamin }}</td>
                                    <td>{{ $row->usia }}</td>
                                    <td>{{ $row->nama_sekolah }}</td>
                                    <td>{{ $row->kelurahan_dispenduk }}</td>
                                    <td>{{ $row->tanggal ? \Carbon\Carbon::parse($row->tanggal)->format('d-m-Y') : '-' }}
                                    </td>
                                    <td>{{ $row->sudah_diperiksa ? 'Sudah Diperiksa' : 'Akan Diperiksa' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8">Data kosong</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <span style="margin-right: auto">Total: {{ count($data) }} siswa dari {{ $sekolah->count() }}
                    sekolah</span>
                <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        $('#modal_nama').modal({
            backdrop: 'static'
        });
        $('#modal_nama').on('hidden.bs.modal', function() {
            $(this).remove();
        });

        function renumberDraw() {
            var api = this.api();
            var start = api.page.info().start;
            api.column(0, {
                page: 'current'
            }).nodes().each(function(cell, i) {
                cell.innerHTML = start + i + 1;
            });
        }

        var tabelSekolah = $('#tabel_modal_sekolah').DataTable({
            dom: "<'row'<'col-md-6'l><'col-md-6'B>>" +
                "<'row'<'col-md-12'f>>" +
                "<'row'<'col-md-12't>><'row'<'col-md-12'ip>>",
            pageLength: 10,
            columnDefs: [{
                orderable: false,
                targets: 0
            }],
            drawCallback: renumberDraw,
            buttons: [{
                extend: 'excelHtml5',
                text: '<i class="fa fa-file-excel-o"></i> Export Excel',
                title: 'Daftar Sekolah - {{ addslashes($judul) }}'
            }]
        });

        $('#tabel_modal_murid').DataTable({
            dom: "<'row'<'col-md-6'l><'col-md-6'B>>" +
                "<'row'<'col-md-12'f>>" +
                "<'row'<'col-md-12't>><'row'<'col-md-12'ip>>",
            pageLength: 5,
            columnDefs: [{
                orderable: false,
                targets: 0
            }],
            drawCallback: renumberDraw,
            buttons: [{
                extend: 'excelHtml5',
                text: '<i class="fa fa-file-excel-o"></i> Export Excel',
                title: 'Daftar Murid - {{ addslashes($judul) }}'
            }]
        });

        // Tabel sekolah diinisialisasi saat collapse-nya masih tersembunyi, jadi lebar
        // kolomnya perlu dihitung ulang begitu ditampilkan.
        $('#collapse_sekolah').on('shown.bs.collapse', function() {
            tabelSekolah.columns.adjust();
        });
    });
</script>
