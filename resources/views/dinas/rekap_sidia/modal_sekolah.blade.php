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
                                <th>Alamat Lembaga</th>
                                <th>Kelurahan Lembaga</th>
                                <th>Kecamatan Lembaga</th>
                                <th>Kuota Pagu</th>
                                <th>Nama Siswa</th>
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
        ajax: {
            url: '{{ route('dinas-rekap_sidia', ['halaman' => 'data_sekolah']) }}',
            type: 'post',
            data: function(d) {
                d.param = '{{ $param }}';
            }
        },
        columns: [{
            data: 'DT_RowIndex',
            name: 'DT_RowIndex',
            searchable: false,
            orderable: false
        }, {
            data: 'npsn',
            name: 'npsn'
        }, {
            data: 'nama_lembaga',
            name: 'nama_lembaga'
        }, {
            data: 'alamat_lembaga',
            name: 'alamat_lembaga'
        }, {
            data: 'kelurahan_lembaga',
            name: 'kelurahan_lembaga'
        }, {
            data: 'kecamatan_lembaga',
            name: 'kecamatan_lembaga'
        }, {
            data: 'kuota',
            name: 'kuota'
        }, {
            data: 'nama_siswa',
            name: 'nama_siswa',
            defaultContent: '-'
        }]
    });

    @if (in_array($kategori, ['jumlah_lembaga', 'jumlah_kuota']))
        tabel.column(6).visible(true);
        tabel.column(7).visible(false);
    @else
        tabel.column(6).visible(false);
        tabel.column(7).visible(true);
    @endif
</script>
