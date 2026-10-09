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
                    d.usia = '{{ $usia }}';
                }
            },
            columns: [{
                    data: 'DT_RowIndex',
                    searchable: false,
                    sortable: false,
                    defaultContent: '',
                    title: 'No'
                },
                {
                    data: 'nik_baru',
                    name: 'a.nik',
                    defaultContent: '',
                    title: 'NIK'
                },
                {
                    data: 'nama_lengkap',
                    name: 'a.nama_lengkap',
                    defaultContent: '',
                    title: 'Nama Lengkap'
                },
                {
                    data: 'usia',
                    name: 'usia',
                    searchable: false,
                    defaultContent: '',
                    title: 'Usia'
                },
                {
                    data: 'jenis_kelamin',
                    name: 'a.jenis_kelamin',
                    defaultContent: '',
                    title: 'Jenis Kelamin'
                },
                {
                    data: 'alamat',
                    name: 'a.alamat',
                    defaultContent: '',
                    title: 'Alamat KK'
                },
                {
                    data: 'rt',
                    name: 'a.rt',
                    defaultContent: '',
                    title: 'RT KK'
                },
                {
                    data: 'rw',
                    name: 'a.rw',
                    defaultContent: '',
                    title: 'RW KK'
                },
                {
                    data: 'kelurahan',
                    name: 'a.kelurahan',
                    defaultContent: '',
                    title: 'Kelurahan KK'
                },
                {
                    data: 'kecamatan',
                    name: 'a.kecamatan',
                    defaultContent: '',
                    title: 'Kecamatan KK'
                },
                {
                    data: 'alamat_dom',
                    name: 'a.alamat_dom',
                    defaultContent: '',
                    title: 'Alamat DOmisili'
                },
                {
                    data: 'rt_dom',
                    name: 'a.rt_dom',
                    defaultContent: '',
                    title: 'RT Domisili'
                },
                {
                    data: 'rw_dom',
                    name: 'a.rw_dom',
                    defaultContent: '',
                    title: 'RW Domisili'
                },
                {
                    data: 'kelurahan_dom',
                    name: 'a.kelurahan_dom',
                    defaultContent: '',
                    title: 'Kelurahan Domisili'
                },
                {
                    data: 'kecamatan_dom',
                    name: 'a.kecamatan_dom',
                    defaultContent: '',
                    title: 'Kecamatan Domisili'
                },
                {
                    data: 'status_phbs',
                    name: 'a.status_phbs',
                    defaultContent: '',
                    title: 'Status PHBS'
                },
            ],
        });
    });
</script>
