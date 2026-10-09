@extends('layout.index')


@section('additional_styles')
    <style>
        tr td {
            text-align: center;
        }
    </style>
@endsection
@section('sidebar')
    @include('dinas/layout/sidebar')
@endsection

@section('content')
    <div class="content" id="app">
        <h2 class="content-heading" style="padding-top: 0px">Sewa Gedung Graha PAUD</h2>
        <!-- Dynamic Table Full -->
        <div class="block">
            <div class="block-header block-header-default row no-margin">
                <h3 class="block-title judul">Daftar Sewa Gedung</h3>
                <button class="btn btn-outline-success col-md-2 m-l-xs m-b-xs" data-toggle="modal" data-target="#modal_tambah"
                    onclick="form_mitra(0)">Tambah Mitra</button>
            </div>
            <div class="block-content block-content-full">
                <div class="row">
                    <div class="col-lg-4">
                        <input type="date" name="tanggal" class="form-control" onchange="filter()"
                            value="{{ $tanggal }}">
                    </div>
                    <div class="col-lg-4">
                        <select name="status" class="form-control" onchange="filter()">
                            <option value="">Semua Status</option>
                            <option>Pengajuan</option>
                            <option>Setujui</option>
                            <option>Block</option>
                        </select>
                    </div>
                </div>

                <div style="overflow: auto;margin-top: 15px">
                    <div class="text-center">
                        <h2 id="judul">{{ $judul }}</h2>
                    </div>
                    <table id="dataTable" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th style="width:1%">No</th>
                                <th width="27%">Penyewa</th>
                                <th>Kontak</th>
                                <th>Kegiatan</th>
                                <th>Tanggal</th>
                                <th>Jam</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-page"></div>
@endsection

@section('additional_scripts')
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript">
        $("#sidebar-sewa").addClass("active");

        var dt_table;
        var tanggal = $('input[name=tanggal]').val();
        var status = $('select[name=status]').val();

        $(document).ready(function() {
            dt_table = $('#dataTable').DataTable({
                dom: 'lBfrtip',
                buttons: [{
                        extend: 'excel',
                        title: 'judul',
                        filename: function() {
                            judul = $('.judul').html() + '_{{ date('YmdHi') }}';
                            return judul;
                        }
                    },
                    {
                        extend: 'print',
                        title: function() {
                            judul = $('.judul').html();
                            return judul;
                        },
                        filename: function() {
                            judul = $('.judul').html() + '_{{ date('YmdHi') }}';
                            return judul;
                        }
                    },
                ],
                lengthMenu: [
                    [10, 25, 50, -1],
                    [10, 25, 50, "All"]
                ],
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('dinas-sewa-halaman', ['get_data']) }}',
                    type: 'post',
                    data: function(d) {
                        d.tanggal = tanggal;
                        d.status = status;
                    },
                },
                columns: [{
                        data: 'DT_RowIndex',
                        sortable: false,
                        searchable: false,
                    },
                    {
                        data: 'penyewa',
                        name: 'penyewa',
                    },
                    {
                        data: 'kontak',
                        name: 'kontak'
                    },
                    {
                        data: 'kegiatan',
                        name: 'kegiatan'
                    },
                    {
                        data: 'tanggal',
                        name: 'tanggal'
                    },
                    {
                        data: 'jam',
                        name: 'jam'
                    },
                    {
                        data: 'status_baru',
                        name: 'status'
                    },
                    {
                        data: 'aksi',
                        searchbale: false,
                        sortable: false
                    },
                ],
            });
        });

        function filter() {
            tanggal = $('input[name=tanggal]').val();
            status = $('select[name=status]').val();

            dt_table.ajax.reload();
        }

        function ubah_status(id, status) {
            swal({
                title: "Pengajuan akan diubah menjadi " + status,
                text: "Apa anda yakin?",
                type: "warning",
                showCancelButton: true,
                cancelButtonText: 'Tidak',
                confirmButtonText: 'Ya, yakin',
            }).then(function(r) {
                if (r.value) {
                    var data = {
                        status: status,
                        id: id,
                    };
                    $.post('{{ route('dinas-sewa-halaman', ['simpan']) }}', data, function(data) {
                        if (data.code == 200) {
                            swal('Success', data.pesan, 'success');
                            dt_table.ajax.reload();
                        } else {
                            swal('Whooops', data.pesan, 'error');
                        }
                    }).fail(function() {
                        swal('Whooops', '500 - Internal server error', 'error');
                    });
                }
            })
        }
    </script>
@endsection
