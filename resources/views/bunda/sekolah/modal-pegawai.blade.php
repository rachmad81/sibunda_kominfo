<!-- Modal -->
<div id="modal_pegawai" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content" style="background: #fff !important">
            <div class="modal-header">
                <h4 class="modal-title">Data Pegawai</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12">
                        <table id="dataTablePegawai" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th style="width:1%">No</th>
                                    <th>Jenjang</th>
                                    <th>NPSN</th>
                                    <th>Nama Sekolah</th>
                                    <th>NIK</th>
                                    <th>Nama</th>
                                    <th>Jabatan</th>
                                    <th>Tanggal Lahir</th>
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
</style>
<script type="text/javascript">
    $(document).ready(function() {
        $('#modal_pegawai').modal('show');
        dt_table = $("#dataTablePegawai").DataTable({
            lengthMenu: [
                [10, 25, 50, -1],
                [10, 25, 50, "All"]
            ],
            processing: true,
            serverside: true,
            ajax: `{!! route('bunda-sekolah-get_data_modal') !!}?jenis=pegawai&npsn={{ $npsn }}`,
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
                    data: 'no_ktp',
                    name: 'pegawai.no_ktp'
                },
                {
                    data: 'nama',
                    name: 'pegawai.nama'
                },
                {
                    data: 'jabatan',
                    name: 'jabatan_ptk.nama'
                },
                {
                    data: 'tgl_lahir',
                    name: 'pegawai.tgl_lahir'
                },
            ]
        });
    });
</script>
