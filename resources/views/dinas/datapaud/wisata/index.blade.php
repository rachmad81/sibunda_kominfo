@extends('layout.index')


@section('additional_styles')
    <style>
        tr td {
            text-align: left;
        }

        table#dataTable li {
            list-style-type: auto;
        }
    </style>
@endsection
@section('sidebar')
    @include('dinas/layout/sidebar')
@endsection

@section('content')
    <div class="content" id="app">
        <h2 class="content-heading" style="padding-top: 0px">Daftar Wisata</h2>
        <!-- Dynamic Table Full -->
        <div class="block">
            <div class="block-header block-header-default row no-margin">
                <h3 class="block-title judul">Daftar Wisata</h3>
            </div>
            <div class="block-content block-content-full">
                <div style="overflow: auto">
                    <table id="dataTable" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th style="width:1%">No</th>
                                <th>Nama</th>
                                <th>Alamat</th>
                                <th>Dinas Terkait</th>
                                <th>Status Tampil</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="page-modal"></div>
    </div>
@endsection

@section('additional_scripts')
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript">
        var dtTable;
        $(document).ready(function() {
            $('#sidebar-datapaud').addClass('active');
            dtTable = $("#dataTable").DataTable({
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
                ajax: '{{ route('dinas-data_paud-wisata-data') }}',
                columns: [{
                        data: 'DT_RowIndex',
                        searchable: false,
                        sortable: false
                    },
                    {
                        data: 'nama'
                    },
                    {
                        data: 'alamat'
                    },
                    {
                        data: 'tembusan'
                    },
                    {
                        data: 'status'
                    },
                    {
                        data: 'aksi',
                        searchable: false,
                        sortable: false
                    },
                ],
            });
        });
    </script>
    <script>
        function get_data() {
            dtTable.ajax.url('{{ route('dinas-data_paud-wisata-data') }}').load();
        }

        function form(id) {
            $.post("{{ route('dinas-data_paud-wisata-form') }}", {
                id: id
            }, function(data) {
                $('.page-modal').html(data.content);
            })
        }
    </script>
@endsection
