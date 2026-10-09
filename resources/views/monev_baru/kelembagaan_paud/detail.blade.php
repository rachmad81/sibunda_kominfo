<!-- Modal -->
<div id="modal_detail_kelembagaan" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Detail Data</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12" style="margin-bottom:10px">
                        <div style="background:linear-gradient(145deg,#c1fdff,#c2c1ff);padding: 10px">
                            Data yang ditampilkan adalah data yang ada saat ini, jadi ada kemungkin jumlah tidak sesuai
                            dengan
                            di
                            bagian awal, dikarenakan kemungkinan sudah menympan datanya.
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12" style="overflow:auto">
                        <table id="tabel_detail_kelembagaan" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>NIK</th>
                                    <th>Alamat KK</th>
                                    <th>Kelurahan KK</th>
                                    <th>Kecamatan KK</th>
                                    <th>Alamat Dom</th>
                                    <th>Kelurahan Dom</th>
                                    <th>Kecamatan Dom</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-warning" data-dismiss="modal"
                    style="width: 100%;justify-content: center">
                    Tutup
                </button>
            </div>
        </div>

    </div>
</div>
<style type="text/css">
    label {
        font-weight: 10;
    }

    table.kop_jadwal tr td {
        text-align: left;
        vertical-align: top;
        padding: 5px;
    }

    .modal-lg {
        max-width: 1200px !important;
    }
</style>
<script type="text/javascript">
    var dt_tabel = '';
    var kategori = '{{ $kategori }}';
    var npsn = '{{ $npsn }}';
    $(document).ready(function() {
        $('#modal_detail_kelembagaan').modal({
            backdrop: 'static'
        });

        dt_tabel = $('#tabel_detail_kelembagaan').DataTable({
            dom: '<"row" <"col" l><"col" f>>' +
                '<"row" <"col" tr>>' +
                '<"row" <"col" i><"col" p>>',
            ajax: {
                url: '{{ route('monev_baru-kelembagaan_paud', ['halaman' => 'detail_baris']) }}',
                type: 'post',
                data: function(d) {
                    d.kategori = '{{ $kategori }}';
                    d.npsn = '{{ $npsn }}';
                }
            },
            processing: true,
            serverSide: true,
            lengthMenu: [
                [10, 25, 50, -1],
                [10, 25, 50, "All"]
            ],
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    searchable: false,
                    sortable: false,
                },
                {
                    data: 'nama',
                    name: 'nama'
                },
                {
                    data: 'nik',
                    name: 'nik'
                },
                {
                    data: 'alamat_kk',
                    name: 'alamat_kk'
                },
                {
                    data: 'kelurahan_kk',
                    name: 'kelurahan_kk'
                },
                {
                    data: 'kecamatan_kk',
                    name: 'kecamatan_kk'
                },
                {
                    data: 'alamat_dom',
                    name: 'alamat_dom'
                },
                {
                    data: 'kelurahan_dom',
                    name: 'kelurahan_dom'
                },
                {
                    data: 'kecamatan_dom',
                    name: 'kecamatan_dom'
                }
            ]
        })

        dt_tabel.on('processing.dt', function(e, settings, processing) {
            if (processing) {
                $('#tabel_detail_kelembagaan tbody').html(
                    '<tr><td colspan="18" class="text-center">Loading...</td></tr>'
                );
            }
        });
    });
</script>
<script type="text/javascript"></script>
