<!-- Modal -->
<div id="modal_vaksinasi" class="modal fade" role="dialog">
    <div class="modal-dialog" style="max-width:1000px;">

        <!-- Modal content-->
        <div class="modal-content" style="background: #fff !important">
            <div class="modal-header">
                <h4 class="modal-title">Data
                    {{ $jenis ? 'Vaksinasi ' . $jenis : 'Siswa' }}
                    {{ $status ? 'Status ' . $status : '' }}
                    {{ $dosis ? 'Tahap ' . $dosis : '' }}
                </h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12">
                        <table id="dataTableVaksinasi" class="table table-bordered table-striped" width="100%">
                            <thead>
                                <tr>
                                    <th style="width:1%" rowspan="2">No</th>
                                    <th rowspan="2">Jenjang</th>
                                    <th rowspan="2">NPSN</th>
                                    <th rowspan="2">Nama Sekolah</th>
                                    {{-- <th rowspan="2">NIK</th> --}}
                                    <th rowspan="2">Nama</th>
                                    <th rowspan="2">Tanggal Lahir</th>
                                    <th rowspan="2">Umur</th>
                                    <th rowspan="1" colspan="2">Vaksinasi Polio</th>
                                </tr>
                                <tr>
                                    <th rowspan="1" style="min-width:100px;">Tahap 1</th>
                                    <th rowspan="1" style="min-width:100px;">Tahap 2</th>
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
        $('#modal_vaksinasi').modal('show');
        dataTableVaksinasi = $("#dataTableVaksinasi").DataTable({
            lengthMenu: [
                [10, 25, 50, -1],
                [10, 25, 50, "All"]
            ],
            processing: true,
            serverSide: true,
            ajax: `{!! route('bunda-vaksinasi-get_data_modal') !!}?modal={{ $modal }}&jenjang={{ $jenjang }}&npsn={{ $npsn }}&modal={{ $modal }}&jenis={{ $jenis }}&status={{ $status }}&dosis={{ $dosis }}`,
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
                // {
                //     data: 'nik',
                //     name: 'siswa.nik'
                // },
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
                    data: 'polio_dosis_1',
                    name: 'polio_dosis_1'
                },
                {
                    data: 'polio_dosis_2',
                    name: 'polio_dosis_2'
                }
            ]
        });
    });
</script>
