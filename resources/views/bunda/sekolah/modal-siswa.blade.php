<!-- Modal -->
<div id="modal_siswa" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content" style="background: #fff !important">
            <div class="modal-header">
                <h4 class="modal-title">Data Siswa</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12" style="overflow: auto">
                        <table id="dataTablePegawai" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th rowspan="2" style="width:1%">No</th>
                                    <th rowspan="2">Jenjang</th>
                                    <th rowspan="2">NPSN</th>
                                    <th rowspan="2">Nama Sekolah</th>
                                    <th rowspan="2">NIK</th>
                                    <th rowspan="2">Nama</th>
                                    <th rowspan="2">Tanggal Lahir</th>
                                    <th colspan="5">Tahun {{ now()->year }} Bulan {{ now()->month }}</th>
                                </tr>
                                <tr>
                                    <th>Umur</th>
                                    <th>Tinggi Badan</th>
                                    <th>Berat Badan</th>
                                    <th>Status Gizi</th>
                                    <th>Kesimpulan KKA</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
            </div>
        </div>

    </div>
</div>
<style type="text/css">
    label {
        font-weight: 10;
    }

    .modal-lg {
        max-width: 1000px;
    }
</style>
<script type="text/javascript">
    $(document).ready(function() {
        $('#modal_siswa').modal('show');
        dt_table = $("#dataTablePegawai").DataTable({
            lengthMenu: [
                [10, 25, 50, -1],
                [10, 25, 50, "All"]
            ],
            processing: true,
            serverSide: true,
            ajax: `{!! route('bunda-sekolah-get_data_modal') !!}?jenis=siswa&npsn={{ $npsn }}`,
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'jenjang',
                    name: 'sekolah.jenjang'
                },
                {
                    data: 'npsn',
                    name: 'sekolah.npsn'
                },
                {
                    data: 'nama_sekolah',
                    name: 'sekolah.nama'
                },
                {
                    data: 'nik',
                    name: 'siswa.nik'
                },
                {
                    data: 'nama',
                    name: 'siswa.nama'
                },
                {
                    data: 'tgl_lahir',
                    name: 'siswa.tgl_lahir'
                },
                {
                    data: 'umur',
                    name: 'umur',
                    searchable: false
                },
                {
                    data: 'tb',
                    name: 'tb',
                    searchable: false
                },
                {
                    data: 'bb',
                    name: 'berat_badan',
                    searchable: false
                },
                {
                    data: 'status_gizi',
                    name: 'status_gizi',
                    searchable: false
                },
                {
                    data: 'kesimpulan_kka',
                    name: 'kesimpulan_kka',
                    searchable: false
                },
            ]
        });
    });
</script>
