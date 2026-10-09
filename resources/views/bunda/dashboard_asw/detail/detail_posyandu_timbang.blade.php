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
                                <th>NIK</th>
                                <th>Nama</th>
                                <th>Jenis Kelamin</th>
                                <th>Alamat</th>
                                <th>RT</th>
                                <th>RW</th>
                                <th>Kelurahan</th>
                                <th>Kecamatan</th>
                                <th>Tanggal Posyandu</th>
                                <th>Posyandu</th>
                                <th>Berat Badan</th>
                                <th>Tinggi Badan</th>
                                <th>Lila</th>
                                <th>Status TB Usia</th>
                                <th>Status BB Usia</th>
                                <th>Status BB TB</th>
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
                    data: 'nik_baru',
                    name: 'nik',
                },
                {
                    data: 'nama_lengkap'
                },
                {
                    data: 'jenis_kelamin'
                },
                {
                    data: 'alamat'
                },
                {
                    data: 'rt'
                },
                {
                    data: 'rw'
                },
                {
                    data: 'kelurahan'
                },
                {
                    data: 'kecamatan'
                },
                {
                    data: 'tanggal_posyandu'
                },
                {
                    data: 'posyandu'
                },
                {
                    data: 'beratbadan'
                },
                {
                    data: 'tinggibadan'
                },
                {
                    data: 'lila'
                },
                {
                    data: 'status_tb_usia'
                },
                {
                    data: 'status_bb_usia'
                },
                {
                    data: 'status_bb_tb'
                }
            ],
        });
    });
</script>
