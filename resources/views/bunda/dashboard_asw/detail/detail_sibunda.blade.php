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
                    'data': 'DT_RowIndex',
                    'name': 'DT_RowIndex',
                    'title': 'No',
                    'orderable': false,
                    'searchable': false,
                    'defaultContent': ''
                },
                {
                    'data': 'nama',
                    'name': 'a.nama_lengkap',
                    'title': 'Nama',
                    'defaultContent': ''
                },
                {
                    'data': 'data_nik',
                    'name': 'data_nik',
                    'title': 'NIK',
                    'defaultContent': ''
                },
                {
                    'data': 'tempat_lahir',
                    'name': 'a.tempat_lahir',
                    'title': 'Tempat Lahir',
                    'defaultContent': ''
                },
                {
                    'data': 'tgl_lahir',
                    'name': 'a.tgl_lahir',
                    'title': 'Tanggal Lahir',
                    'defaultContent': ''
                },
                {
                    'data': 'alamat',
                    'name': 'a.alamat',
                    'title': 'Alamat',
                    'defaultContent': ''
                },
                {
                    'data': 'rt',
                    'name': 'a.rt',
                    'title': 'RT',
                    'defaultContent': ''
                },
                {
                    'data': 'rw',
                    'name': 'a.rw',
                    'title': 'RW',
                    'defaultContent': ''
                },
                {
                    'data': 'kelurahan',
                    'name': 'a.kelurahan',
                    'title': 'Kelurahan',
                    'defaultContent': ''
                },
                {
                    'data': 'kecamatan',
                    'name': 'a.kecamatan',
                    'title': 'Kecamatan',
                    'defaultContent': ''
                },
                {
                    'data': 'alamat_dom',
                    'name': 'a.alamat_dom',
                    'title': 'Alamat Domisili',
                    'defaultContent': ''
                },
                {
                    'data': 'rt_dom',
                    'name': 'a.rt_dom',
                    'title': 'RT Domisili',
                    'defaultContent': ''
                },
                {
                    'data': 'rw_dom',
                    'name': 'a.rw_dom',
                    'title': 'RW Domisili',
                    'defaultContent': ''
                },
                {
                    'data': 'kelurahan_dom',
                    'name': 'a.kelurahan_dom',
                    'title': 'Kelurahan Domisili',
                    'defaultContent': ''
                },
                {
                    'data': 'kecamatan_dom',
                    'name': 'a.kecamatan_dom',
                    'title': 'Kecamatan Domisili',
                    'defaultContent': ''
                },
                {
                    'data': 'profil',
                    'name': 'a.nama_sekolah',
                    'title': 'Data Sekolah',
                    'defaultContent': ''
                },
                {
                    'data': 'status_aktif_pendidikan',
                    'name': 'status_aktif_pendidikan',
                    'title': 'Status Aktif Pendidikan',
                    'defaultContent': ''
                },
                {
                    'data': 'kelas_rombel',
                    'name': 'a.kelas',
                    'title': 'Kelas',
                    'defaultContent': ''
                },
                {
                    'data': 'status_survey',
                    'name': 'b.status_verifikasi',
                    'title': 'Status Survey',
                    'defaultContent': ''
                },
                {
                    'data': 'status_domisili',
                    'name': 'status_domisili',
                    'title': 'Status Alamat',
                    'defaultContent': ''
                },
                {
                    'data': 'detail_alamat',
                    'name': 'b.detail_alamat',
                    'title': 'Catatan Alamat',
                    'defaultContent': ''
                },
                {
                    'data': 'status_sekolah_n',
                    'name': 'status_sekolah_n',
                    'title': 'Status Sekolah',
                    'defaultContent': ''
                },
                {
                    'data': 'detail_sekolah',
                    'name': 'b.detail_sekolah',
                    'title': 'Catatan Sekolah',
                    'defaultContent': ''
                },
                {
                    'data': 'status_faktor',
                    'name': 'status_faktor',
                    'title': 'Faktor Tidak Bersekolah',
                    'defaultContent': ''
                },
                {
                    'data': 'pilihan_detail_tidak_sekolah',
                    'name': 'b.pilihan_detail_tidak_sekolah',
                    'title': 'Catatan Tidak Bersekolah',
                    'defaultContent': ''
                },
                {
                    'data': 'status_edukasi',
                    'name': 'status_edukasi',
                    'title': 'Catatan Tidak Bersekolah',
                    'defaultContent': ''
                },
                {
                    'data': 'status_tanggapan',
                    'name': 'status_tanggapan',
                    'title': 'Tanggapan Orang Tua',
                    'defaultContent': ''
                },
                {
                    'data': 'status_rtl',
                    'name': 'status_rtl',
                    'title': 'Rencana Tindak Lanjut',
                    'defaultContent': ''
                },
            ],
        });
    });
</script>
