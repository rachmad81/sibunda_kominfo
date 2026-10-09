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
            url: '{{ route('dinas-rekap_ttd', ['halaman' => 'data_modal']) }}',
            type: 'post',
            data: function(d) {
                d.kategori = '{{ $kategori }}';
                d.jenjang = jenjang;
                d.kecamatan_kode = '{{ $kecamatan_kode }}';
                d.kelurahan_kode = '{{ $kelurahan_kode }}';
                d.tingkat = '{{ $tingkat }}';
            }
        },
        columns: [{
                data: 'DT_RowIndex',
                name: 'DT_RowIndex',
                sortable: false,
                orderable: false,
                defaultContent: '',
                title: 'No'
            },
            {
                data: 'npsn',
                name: 'se.npsn',
                className: 'kolom_lembaga text-left',
                defaultContent: '',
                title: 'NPSN'
            },
            {
                data: 'nama_sekolah',
                name: 'se.nama',
                className: 'kolom_lembaga text-left',
                defaultContent: '',
                title: 'Nama Lembaga'
            },
            {
                data: 'kelurahan_dispenduk',
                name: 'kel.kelurahan_dispenduk',
                className: 'kolom_lembaga text-left',
                defaultContent: '',
                title: 'Kelurahan'
            },
            {
                data: 'kecamatan_dispenduk',
                name: 'kec.kecamatan_dispenduk',
                className: 'kolom_lembaga text-left',
                defaultContent: '',
                title: 'Kecamatan '
            },
            {
                data: 'nama_siswa',
                name: 's.nama',
                className: 'kolom_lembaga text-left',
                defaultContent: '',
                title: 'Nama Siswa',
                visible: @if ($kategori == 'jumlah_lembaga')
                    false
                @else
                    true
                @endif
            },
            {
                data: 'kelas',
                name: 's.kelas',
                className: 'kolom_lembaga text-left',
                defaultContent: '',
                title: 'Kelas',
                visible: @if ($kategori == 'jumlah_lembaga')
                    false
                @else
                    true
                @endif
            },
            {
                data: 'rombel',
                name: 's.rombel',
                className: 'kolom_lembaga text-left',
                defaultContent: '',
                title: 'Rombel',
                visible: @if ($kategori == 'jumlah_lembaga')
                    false
                @else
                    true
                @endif
            },
        ]
    });
</script>
