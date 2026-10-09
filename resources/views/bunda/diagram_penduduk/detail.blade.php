<div id="modal_tambah" class="modal fade" aria-hidden="true">
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
                <table id="dataTable" class="table table-bordered">
                    <thead>
                    </thead>
                    <tbody>
                        <div id="loading-overlay"
                            style="display:none; position:absolute; top:0; left:0; right:0; bottom:0;
						    background:rgba(255,255,255,0.7); z-index:9999; text-align:center; padding-top:100px;">
                            <div class="spinner-border text-primary"></div>
                            <div>Memuat data...</div>
                        </div>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" style="margin-bottom:0px" class="btn btn-white"
                    data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
<style>
    tbody tr td {
        text-align: left;
    }

    @media (min-width: 992px) {
        .modal-lg {
            max-width: 1800px;
        }
    }
</style>
<script>
    $('#modal_tambah').modal('show');
    $(document).ready(function() {
        var columns = JSON.parse('{!! $kolom_body !!}');
        $('#modal_tambah').css({
            zIndex: 9999,
        })
        var data = {
            kategori: '{{ $kategori }}',
            jenis: '{{ $jenis }}',
            kecamatan: '{{ $kecamatan }}',
            kelurahan: '{{ $kelurahan }}',
            kecamatan_kode: '{{ $kecamatan_kode }}',
            kelurahan_kode: '{{ $kelurahan_kode }}',
        };

        $("#dataTable").DataTable({
            dom: "<'row'<'col-md-6'l><'col-md-6'B>>" +
                "<'row'<'col-md-12'f>>" +
                "<'row'<'col-md-12't>><'row'<'col-md-12'ip>>",
            buttons: [{
                    extend: 'excel',
                    title: 'judul',
                    filename: function() {
                        judul = $('.modal-title').html() + '_{{ date('d-m-Y H.i') }}';
                        return judul;
                    }
                },
                {
                    extend: 'print',
                    title: function() {
                        judul = $('.modal-title').html();
                        return judul;
                    },
                    filename: function() {
                        judul = $('.modal-title').html() + '_{{ date('d-m-Y H.i') }}';
                        return judul;
                    }
                },
            ],
            scrollX: true,
            processing: true,
            language: {
                processing: "Sedang memproses data, mohon tunggu..."
            },
            lengthMenu: [
                [10, 25, 50, -1],
                [10, 25, 50, "All"]
            ],
            serverSide: true,
            autoWidth: false,
            ajax: {
                url: '{{ route('bunda-diagram_penduduk-get_detail') }}',
                type: 'post',
                data: data,
                beforeSend: function() {
                    $('#loading-overlay').show();
                },
                complete: function() {
                    $('#loading-overlay').hide();
                }
            },
            columns: columns
        });
    });
</script>
