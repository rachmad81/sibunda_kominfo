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
                    <table class="table table-bordered table-striped" style="width: 100%" id="tabel_cekin">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Usia</th>
                                <th>Alamat</th>
                                <th>Kelurahan</th>
                                <th>Kecamatan</th>
                                <th>Kota/Kab</th>
                                <th>Prvinsi</th>
                                <th>Perpustakaan</th>
                                <th>Terakhir Berkunjung</th>
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
                    d.kelamin = '{{ $kelamin }}';
                    d.menu = '{{ $jenis }}';
                    d.kecamatan = '{{ $kecamatan }}';
                    d.kelurahan = '{{ $kelurahan }}';
                    d.tahun = '{{ $tahun }}';
                }
            },
            columns: [{
                    data: 'DT_RowIndex',
                    searchable: false,
                    sortable: false,
                },
                {
                    data: 'nama_lengkap',
                    name: 'a.nama_lengkap'
                },
                {
                    data: 'usia',
                    name: 'a.usia'
                },
                {
                    data: 'alamat',
                    name: 'a.alamat'
                },
                {
                    data: 'nama_kelurahanktp',
                    name: 'a.nama_kelurahanktp'
                },
                {
                    data: 'nama_kecamatanktp',
                    name: 'a.nama_kecamatanktp'
                },
                {
                    data: 'nama_kabkotktp',
                    name: 'a.nama_kabkotktp'
                },
                {
                    data: 'nama_provinsiktp',
                    name: 'a.nama_provinsiktp'
                },
                {
                    data: 'nama_lokasi',
                    name: 'a.nama_lokasi'
                },
                {
                    data: 'tanggal_entri',
                    name: 'a.tanggal_entri'
                },
            ],
        });
    });
</script>
