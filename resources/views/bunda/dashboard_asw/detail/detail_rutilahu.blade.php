<div id="modal_cekin" class="modal fade" aria-hidden="true" style="margin-top: 50px">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">{{ $judul }}</h3>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
            </div>
            <div class="modal-body">
                <div style="width:100%;overflow: auto">
                    <table class="table table-bordered table-striped tabel-responsive" style="width: 100%"
                        id="tabel_cekin">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>NIK</th>
                                <th>Nama</th>
                                <th>Usia</th>
                                <th>Jenis Kelamin</th>
                                <th>Alamat</th>
                                <th>RT</th>
                                <th>RW</th>
                                <th>Kelurahan</th>
                                <th>Kecamatan</th>
                                <th>Alamat Domisili</th>
                                <th>RT Domisili</th>
                                <th>RW Domisili</th>
                                <th>Kelurahan Domisili</th>
                                <th>Kecamatan Domisili</th>
                                <th>Tahun Pengerjaan</th>
                                <th>Sumber Dana</th>
                                <th>Instansi</th>
                                <th>Status Pengerjaan</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" style="margin-bottom:0px" class="btn btn-white"
                    data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
<style>
    @media (min-width: 992px) {
        .modal-lg {
            max-width: 1800px;
        }
    }
</style>
<script>
    $('#modal_cekin').modal('show');
    $(document).ready(function() {
        $('#tabel_cekin').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route('bunda-dashboard_asw-detail_data') }}',
                type: 'post',
                data: function(d) {
                    d.kategori = '{{ $kategori }}';
                    d.tahun = '{{ $tahun }}';
                    d.sumber_dana = '{{ $sumber_dana }}';
                    d.instansi = '{{ $instansi }}';
                    d.kelamin = '{{ $kelamin }}';
                    d.menu = '{{ $jenis }}';
                    d.kecamatan = '{{ $kecamatan }}';
                    d.kelurahan = '{{ $kelurahan }}';
                }
            },
            columns: [{
                    data: 'DT_RowIndex',
                    searchable: false,
                    sortable: false,
                },
                {
                    data: 'nik_baru',
                    name: 'a.nik'
                },
                {
                    data: 'nama_lengkap',
                    name: 'a.nama_lengkap'
                },
                {
                    data: 'usia',
                    name: 'a.tgl_lahir',
                    searchable: false,
                },
                {
                    data: 'jenis_kelamin',
                    name: 'a.jenis_kelamin'
                },
                {
                    data: 'alamat',
                    name: 'a.alamat'
                },
                {
                    data: 'rt',
                    name: 'a.rt'
                },
                {
                    data: 'rw',
                    name: 'a.rw'
                },
                {
                    data: 'kelurahan',
                    name: 'a.kelurahan'
                },
                {
                    data: 'kecamatan',
                    name: 'a.kecamatan'
                },
                {
                    data: 'alamat_dom',
                    name: 'a.alamat_dom'
                },
                {
                    data: 'rt_dom',
                    name: 'a.rt_dom'
                },
                {
                    data: 'rw_dom',
                    name: 'a.rw_dom'
                },
                {
                    data: 'kelurahan_dom',
                    name: 'a.kelurahan_dom'
                },
                {
                    data: 'kecamatan_dom',
                    name: 'a.kecamatan_dom'
                },
                {
                    data: 'tahun_pengerjaan',
                    name: 'b.tahun_pengerjaan'
                },
                {
                    data: 'sumber_dana',
                    name: 'b.sumber_dana'
                },
                {
                    data: 'instansi',
                    name: 'b.instansi'
                },
                {
                    data: 'status_pekerjaan',
                    name: 'b.status_pekerjaan'
                },
            ],
        });
    });
</script>
