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
                    <table class="" style="width: 100%;border-collapse: collapse" border="1" id="tabel_cekin">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>NPSN</th>
                                <th>Nama Lembaga</th>
                                <th>Status</th>
                                <th>Jenjang</th>
                                <th>Alamat</th>
                                <th>Kelurahan</th>
                                <th>Kecamatan</th>
                                <th>Nama Siswa</th>
                                <th>NIK Siswa</th>
                                <th>Usia</th>
                                <th>Alamat KK Siswa</th>
                                <th>Kelurahan KK Siswa</th>
                                <th>Kecamatan KK Siswa</th>
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
    var jenjang = [];
    @foreach ($jenjang as $j)
        jenjang.push('{{ $j }}');
    @endforeach
    var tabel = $('#tabel_cekin').DataTable({
        ajax: {
            url: '{{ route('dinas-rekap-siswa', ['halaman' => 'data_modal']) }}',
            type: 'post',
            data: function(d) {
                d.kategori = '{{ $kategori }}';
                d.jenjang = jenjang;
                d.kecamatan_kode = '{{ $kecamatan_kode }}';
                d.kelurahan_kode = '{{ $kelurahan_kode }}';
            }
        },
        columns: [{
                data: 'DT_RowIndex',
                name: 'DT_RowIndex',
                sortable: false,
                orderable: false
            },
            {
                data: 'npsn',
                name: 'npsn',
                className: 'kolom_lembaga text-left'
            },
            {
                data: 'nama_lembaga',
                name: 'nama_lembaga',
                className: 'kolom_lembaga text-left'
            },
            {
                data: 'status',
                name: 'status',
                className: 'kolom_lembaga text-left'
            },
            {
                data: 'jenjang',
                name: 'jenjang',
                className: 'kolom_lembaga text-left'
            },
            {
                data: 'alamat',
                name: 'alamat',
                className: 'kolom_lembaga text-left'
            },
            {
                data: 'kelurahan_dispenduk',
                name: 'kelurahan_dispenduk',
                className: 'kolom_lembaga text-left'
            },
            {
                data: 'kecamatan_dispenduk',
                name: 'kecamatan_dispenduk',
                className: 'kolom_lembaga text-left'
            },
            {
                data: 'nama',
                name: 'nama',
                className: 'kolom_lembaga text-left'
            },
            {
                data: 'nik',
                name: 'nik',
                className: 'kolom_lembaga text-left'
            },
            {
                data: 'usia',
                name: 'usia',
                className: 'kolom_lembaga text-left'
            },
            {
                data: 'alamat_rumah',
                name: 'alamat_rumah',
                className: 'kolom_lembaga text-left'
            },
            {
                data: 'kelurahan_siswa',
                name: 'kelurahan_siswa',
                className: 'kolom_lembaga text-left'
            },
            {
                data: 'kecamatan_siswa',
                name: 'kecamatan_siswa',
                className: 'kolom_lembaga text-left'
            },
        ]
    });
</script>
