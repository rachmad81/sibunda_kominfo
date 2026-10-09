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
                                <th rowspan="2">No</th>
                                <th rowspan="2">NIK</th>
                                <th rowspan="2">Nama</th>
                                <th rowspan="2">Usia</th>
                                <th rowspan="2">Jenis Kelamin</th>
                                <th rowspan="2">Alamat</th>
                                <th rowspan="2">RT</th>
                                <th rowspan="2">RW</th>
                                <th rowspan="2">Kelurahan</th>
                                <th rowspan="2">Kecamatan</th>
                                <th rowspan="2">Alamat Domisili</th>
                                <th rowspan="2">Kelurahan Domisili</th>
                                <th rowspan="2">Kecamatan Domisili</th>
                                <th colspan="5">Hasil Survey</th>
                                <th colspan="6">Sidia</th>
                            </tr>
                            <tr>
                                <th>Status Sekolah</th>
                                <th>Catatan Sekolah</th>
                                <th>Status Alamat</th>
                                <th>Catatan Alamat</th>
                                <th>Alasan TIdak Sekolah</th>
                                <th>NPSN</th>
                                <th>Nama Lembaga</th>
                                <th>Kelurahan Lembaga</th>
                                <th>Kecamatan Lembaga</th>
                                <th>Nama Siswa</th>
                                <th>Status Siswa</th>
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
    @php
        $arr_param = json_decode(base64_decode($param));
        $kategori = $arr_param->kategori;
    @endphp
    $('#modal_cekin').modal('show');
    var tabel = $('#tabel_cekin').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route('dinas-rekap_sidia', ['halaman' => 'data_penduduk']) }}',
            type: 'post',
            data: function(d) {
                d.param = '{{ $param }}';
            }
        },
        columns: [{
                data: 'DT_RowIndex',
                name: 'DT_RowIndex',
                sortable: false,
                searchable: false,
            },
            {
                data: 'nik',
                name: 'nik',
                defaultContent: '-',
            },
            {
                data: 'nama_lengkap',
                name: 'nama_lengkap',
                defaultContent: '-',
            },
            {
                data: 'usia',
                name: 'usia',
                defaultContent: '-',
            },
            {
                data: 'jenis_kelamin',
                name: 'jenis_kelamin',
                defaultContent: '-',
            },
            {
                data: 'alamat',
                name: 'alamat',
                defaultContent: '-',
            },
            {
                data: 'rt',
                name: 'rt',
                defaultContent: '-',
            },
            {
                data: 'rw',
                name: 'rw',
                defaultContent: '-',
            },
            {
                data: 'kelurahan',
                name: 'kelurahan',
                defaultContent: '-',
            },
            {
                data: 'kecamatan',
                name: 'kecamatan',
                defaultContent: '-',
            },
            {
                data: 'alamat_dom',
                name: 'alamat_dom',
                defaultContent: '-',
            },
            {
                data: 'kelurahan_dom',
                name: 'kelurahan_dom',
                defaultContent: '-',
            },
            {
                data: 'kecamatan_dom',
                name: 'kecamatan_dom',
                defaultContent: '-',
            },
            {
                data: 'status_Sekolah',
                name: 'status_Sekolah',
                defaultContent: '-'
            },
            {
                data: 'detail_sekolah',
                name: 'detail_sekolah',
                defaultContent: '-'
            },
            {
                data: 'status_alamat',
                name: 'status_alamat',
                defaultContent: '-'
            },
            {
                data: 'detail_alamat',
                name: 'detail_alamat',
                defaultContent: '-'
            },
            {
                data: 'detail_pilihan_tidak_sekolah',
                name: 'detail_pilihan_tidak_sekolah',
                defaultContent: '-'
            },
            {
                data: 'npsn',
                name: 'npsn',
                defaultContent: '-'
            },
            {
                data: 'nama_lembaga',
                name: 'nama_lembaga',
                defaultContent: '-'
            },
            {
                data: 'kelurahan_lembaga',
                name: 'kelurahan_lembaga',
                defaultContent: '-'
            },
            {
                data: 'kecamatan_lembaga',
                name: 'kecamatan_lembaga',
                defaultContent: '-'
            },
            {
                data: 'nama_siswa',
                name: 'nama_siswa',
                defaultContent: '-'
            },
            {
                data: 'keterangan',
                name: 'keterangan',
                defaultContent: '-'
            },
        ]
    });

    @if ($kategori == 'jumlah_belum_sidia')
        tabel.column(18).visible(true);
        tabel.column(19).visible(true);
        tabel.column(20).visible(true);
        tabel.column(21).visible(true);
        tabel.column(22).visible(true);
        tabel.column(23).visible(true);
    @else
        tabel.column(18).visible(false);
        tabel.column(19).visible(false);
        tabel.column(20).visible(false);
        tabel.column(21).visible(false);
        tabel.column(22).visible(false);
        tabel.column(23).visible(false);
    @endif
</script>
