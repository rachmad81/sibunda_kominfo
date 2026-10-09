@extends('layout.index')
@php
    header("Content-Security-Policy: frame-ancestors 'self' https://kantorku.surabaya.go.id;");
@endphp

@section('additional_styles')
    <style>
        tr>th,
        td {
            text-align: center;
        }

        .block-primary {
            border: 1.5px solid #21a8d1;
        }

        .block-primary>.block-header {
            background: #21a8d1;
        }

        .block-success {
            border: 1.5px solid #36d66c;
        }

        .block-success>.block-header {
            background: #36d66c;
        }

        .block-warning {
            border: 1.5px solid #f1e14c;
        }

        .block-warning>.block-header {
            background: #f1e14c;
        }

        .fix_header {
            background: #fff;
            padding: 15px;
            margin-bottom: 10px;
        }

        .fix_header.sticky-fixed {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 9999;
            /* Agar selalu di atas konten lain */
            background-color: #ffffff;
            /* Beri background agar tidak transparan */
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            /* Opsional: bayangan tipis */
            transition: all 0.3s ease;
        }
    </style>
@endsection
@section('sidebar')
    @if (Auth::getUser()->role == 'bunda')
        @include('bunda/layout/sidebar')
    @elseif(Auth::getUser()->role == 'dinas')
        @include('dinas/layout/sidebar')
    @elseif(Auth::getUser()->role == 'opd')
        @include('opd/layout/sidebar')
    @else
        @include('opd/layout/sidebar')
    @endif
@endsection

@section('content')
    <div class="content" id="app">
        <div class="fix_header">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div style="font-weight: bold;font-size: 20pt">{{ $judul ?? '' }}</div>
                </div>
                <div class="col-lg-6 text-right">
                    <div class="wadah_jam"></div>
                </div>
            </div>
        </div>
        <!-- Dynamic Table Full -->
        <div class="block">
            <div class="block-content block-content-full">
                <div class="row">
                    <div class="col-lg-4">
                        <b>Langkah Pengisian</b><br>
                        1. Cari Nama Mitra, jika tidak ada di pilihan bisa ketik secara lengkap nama mitranya.<br>
                        2. Mitra berperan sebagai apa.<br>
                        3. Isikan MoU dengan mitra berupa link gdrive, pastikan statusnya public.
                    </div>
                    <div class="col-lg-4">
                        <b>Langkah Pembaruan</b><br>
                        1. Cari Data Kerjasama, Klik Edit.<br>
                        2. Isikan perubahan.<br>
                        3. Klik Perbarui untuk simpan.
                    </div>
                    <div class="col-lg-4">
                        <b>Catatan Tambahan</b><br>
                        1. Untuk menambah data pastikan tombolnya adalah Tambahkan.<br>
                        2. Jika tombolnya Perbarui, anda bisa klik reset untuk menjadikannya Tambahkan.
                    </div>
                </div>
            </div>
        </div>
        <div class="block">
            <div class="block-content block-content-full">
                <div class="row">
                    <div class="col-lg-12">
                        <h5>Form Kerjasama</h5>
                        <form class="form_kerjasama" onsubmit="simpan()">
                            <table style="width: 100%">
                                <thead>
                                    <tr>
                                        <th>Nama Mitra</th>
                                        <th>Sebagai</th>
                                        <th>FIle MOU</th>
                                        <th>#</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <input type="hidden" name="id_kerjasama" value="0">
                                            <input type="text" class="form-control" required
                                                placeholder="Cari nama mitra atau ketik nama mitra" name="nama_mitra"
                                                onkeyup="cari_mitra()" autocomplete="off">
                                            <div class="wadah_mitra" style="text-align: left"></div>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control" required
                                                placeholder="Contoh: Rekanan, Penyedia Dana, CSR, dll" name="sebagai"
                                                autocomplete="off">
                                        </td>
                                        <td>
                                            <input type="text" class="form-control"
                                                placeholder="https://drive.google.com/......." name="mou"
                                                autocomplete="off">
                                        </td>
                                        <td>
                                            <button type="submit" class="btn btn-sm btn-primary btn-aksi"><i
                                                    class="fa fa-plus"></i>
                                                Tambahkan</button>
                                            <button type="button" class="btn btn-sm btn-info" onclick="ulang()"><i
                                                    class="fa fa-gear"></i>
                                                Reset</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </form>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12" style="overflow: auto">
                        <table id="tabel_kerjasama" style="width: 100%;border-collapse: collapse" border="1">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Mitra</th>
                                    <th>Sebagai</th>
                                    <th>Bunda PAUD</th>
                                    <th>MOU</th>
                                    <th>#</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-page"></div>
@endsection

@section('additional_scripts')
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <style type="text/css">
        body {
            overflow: auto !important;
        }

        .modal {
            overflow: auto !important;
        }

        .dt-left {
            text-align: left;
        }
    </style>
    <script type="text/javascript">
        $('#sidebar-mitra').addClass('active');
        var tabel = $('#tabel_kerjasama').DataTable({
            ajax: {
                url: '{{ route('bunda-kerjasama-mitra', ['halaman' => 'get_data']) }}',
                type: 'post',
                data: {}
            },
            columns: [{
                data: 'DT_RowIndex',
                name: 'DT_RowIndex',
                sortable: false,
                orderable: false
            }, {
                data: 'nama_mitra',
                name: 'nama_mitra'
            }, {
                data: 'sebagai',
                name: 'sebagai'
            }, {
                data: 'nama_bunda',
                name: 'nama_bunda'
            }, {
                data: 'file_mou',
                name: 'mou'
            }, {
                data: 'aksi',
                name: 'aksi',
                sortable: false,
                orderable: false
            }]
        });

        function simpan() {
            event.preventDefault();

            var data = new FormData($('.form_kerjasama')[0]);

            $.ajax({
                url: '{{ route('bunda-kerjasama-mitra', ['halaman' => 'simpan']) }}',
                type: 'post',
                data: data,
                contentType: false,
                processData: false,
                success: function(data) {
                    if (data.code == '200') {
                        tabel.ajax.reload();
                        ulang();
                        swal('Success', 'Berhasil disimpan', 'success');
                    } else {
                        swal('Whooops', data.message, 'error');
                    }
                }
            })
        }

        function cari_mitra() {
            var cari = $('input[name=nama_mitra]').val();

            $.ajax({
                url: '{{ route('bunda-kerjasama-mitra', ['halaman' => 'cari_mitra']) }}',
                type: 'post',
                data: {
                    cari: cari
                },
                success: function(data) {
                    var html = '';
                    if (data.length > 0) {
                        $.each(data, function(k, v) {
                            html += '<a href="javascript:void(0)" onclick="set_mitra(\'' + v
                                .nama_mitra + '\')">' + v.nama_mitra + '</a><br>';
                        });
                    }

                    $('.wadah_mitra').html(html);
                }
            })
        }

        function set_mitra(nama) {
            $('input[name=nama_mitra]').val(nama);
            $('.wadah_mitra').html('');
        }

        function edit(id, nama, sebagai, mou) {
            $('input[name=id_kerjasama]').val(id);
            $('input[name=nama_mitra]').val(nama);
            $('input[name=sebagai]').val(sebagai);
            $('input[name=mou]').val(mou);

            $('.btn-aksi').html('<i class="fa fa-edit"></i> Perbarui').removeClass('btn-primary').addClass('btn-warning');
        }

        function ulang() {
            $('.form_kerjasama')[0].reset();
            $('input[name=id_kerjasama]').val(0);

            $('.btn-aksi').html('<i class="fa fa-plus"></i> Tambahkan').removeClass('btn-warning').addClass('btn-primary');
        }

        function hapus(id) {
            Swal.fire({
                title: 'Apa kamu yakin?',
                text: "Data tidak bisa dikembalikan kembali!",
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Iya, aku yakin!',
                cancelButtonText: 'Tidak!'
            }).then((result) => {
                if (result.value) {
                    $.post('{{ route('bunda-kerjasama-mitra', ['halaman' => 'hapus']) }}', {
                        id: id
                    }, function(data) {
                        if (data.code == '200') {
                            tabel.ajax.reload();
                        }
                    });
                } else {}
            })
        }
    </script>
@endsection
