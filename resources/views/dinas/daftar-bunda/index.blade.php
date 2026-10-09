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
        <h2 class="content-heading" style="padding-top: 0px">Daftar Bunda PAUD</h2>
        <!-- Dynamic Table Full -->
        <div class="block">
            <div class="block-header block-header-default row no-margin">
                <h3 class="block-title judul">Daftar Bunda PAUD</h3>
            </div>
            <div class="block-content block-content-full">
                <table id="dataTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th style="width:1%">No</th>
                            <th>Tingkat</th>
                            <th>Kecamatan</th>
                            <th>Kelurahan</th>
                            <th width="27%">Nama Bunda</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(value, idx) in data_wilayah">
                            <td>@{{ idx + 1 }}</td>
                            <td>@{{ value.tingkat }}</td>
                            <td>@{{ value.kecamatan ? value.kecamatan : '-' }}</td>
                            <td>@{{ value.kelurahan ? value.kelurahan : '-' }}</td>
                            <td>@{{ value.nama_bunda.length < 1 ? 'Sedang memuat' : value.nama_bunda.nama }}</td>
                            <td style="width: 5%">
                                -
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="page-modal"></div>
    </div>
@endsection

@section('additional_scripts')
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $("#dataTable").DataTable({
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
            });
        });
    </script>
    <script>
        let vm = new Vue({
            el: "#app",
            data: {
                loading: false,
                data_wilayah: []
            },
            created: function() {
                $("#sidebar-bunda").addClass("active");
                // jQuery(function(){Codebase.helpers(['datepicker','select2']);});
                @foreach ($data as $item)
                    this.data_wilayah.push({
                        tingkat: '{{ $item['tingkat'] }}',
                        id: '{{ $item['id'] }}',
                        kecamatan: '{{ $item['kecamatan'] }}',
                        kelurahan: '{{ $item['kelurahan'] }}',
                        nama_bunda: ''
                    })
                @endforeach

                var _this = this

                setTimeout(function() {
                    $.each(_this.data_wilayah, function(idx, value) {
                        _this.getNama('bunda', idx);
                    })
                }, 2000);
            },
            methods: {
                getNama: function(jenis, idx) {
                    if (jenis == 'bunda') {
                        var url = '{{ route('dinas-bunda-get-list', ['bunda']) }}'
                        var obj = this.data_wilayah
                    }

                    $.ajax({
                        url: url,
                        type: 'post',
                        data: {
                            id: obj[idx].id,
                            tingkat: obj[idx].tingkat
                        },
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(data) {
                            if (data.status == 'success') {
                                obj[idx].nama_bunda = data.obj
                            } else {
                                obj[idx].nama_bunda = 'Gagal memuat'
                            }
                        },
                        error: function(exception) {
                            obj[idx].nama_bunda = 'Gagal memuat!'
                        }
                    });
                }
            },
            computed: {

            },
            watch: {

            },
        });
    </script>
@endsection
