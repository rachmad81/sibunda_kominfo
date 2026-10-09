<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Data Penduduk Usia 0-6</title>
    <!-- Datatables -->
    <link rel="stylesheet" href="{{ url('assets/js/plugins/datatables/dataTables.bootstrap4.css') }}">
    <link rel="stylesheet" href="{{ url('assets/js/plugins/datatables/buttons-bs4/buttons.bootstrap4.min.css') }}">
    <link rel="stylesheet" id="css-main" href="{{ url('assets/css/codebase.css') }}">
</head>
<style>
    body {
        background-color: #fff;
    }

    .table-responsive-custom {
        overflow-x: auto;
        width: 100%;
    }

    table tr td {
        vertical-align: top
    }
</style>

<body>
    <div style="width: 100%;padding: 15px">
        <div style="width: 100%;">
            <table style="width: 100%;border-collapse:collapse" border="3" id="tabel_penduduk">
                <thead>
                    <tr>
                        <th>NIK</th>
                        <th>Nama</th>
                        <th>Usia</th>
                        <th>Alamat</th>
                        <th>Kecamatan</th>
                        <th>Kelurahan</th>
                        <th>Alamat Domisli</th>
                        <th>Kecamatan Domisli</th>
                        <th>Kelurahan Domisli</th>
                        <th>Status Bersekolah</th>
                        <th>Jenjang Sekolah</th>
                        <th>Nama Sekolah</th>
                        <th>Status Survey</th>
                        <th>Status Sekolah</th>
                        <th>Status Alamat</th>
                        <th>Catatan</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</body>

<script src="{{ url('assets/js/codebase.core.min.js') }}"></script>
<script src="{{ url('assets/js/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ url('assets/js/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ url('assets/js/plugins/datatables/buttons-bs4/buttons.bootstrap4.min.js') }}"></script>
<script>
    var dt_tabel;
    $(document).ready(function() {
        dt_tabel = $('#tabel_penduduk').DataTable({
            ajax: {
                url: '{{ route('api-iframe-get_detail_penduduk') }}',
                type: 'post',
                data: function(d) {
                    d.kecamatan = '{{ $kecamatan }}';
                    d.kelurahan = '{{ $kelurahan }}';
                    d.usia = '{{ $usia }}';
                    d.kategori = '{{ $kategori }}';
                }
            },
            columns: [{
                    data: 'nik',
                    name: 'nik',
                },
                {
                    data: 'nama_lengkap',
                    name: 'nama_lengkap',
                },
                {
                    data: 'usia',
                    name: 'usia',
                },
                {
                    data: 'alamat',
                    name: 'alamat',
                },
                {
                    data: 'kecamatan',
                    name: 'kecamatan',
                },
                {
                    data: 'kelurahan',
                    name: 'kelurahan',
                },
                {
                    data: 'alamat_dom',
                    name: 'alamat_dom',
                },
                {
                    data: 'kecamatan_dom',
                    name: 'kecamatan_dom',
                },
                {
                    data: 'kelurahan_dom',
                    name: 'kelurahan_dom',
                },
                {
                    data: 'status_bersekolah',
                    name: 'status_bersekolah',
                },
                {
                    data: 'jenjang_sekolah',
                    name: 'jenjang_sekolah',
                },
                {
                    data: 'nama_sekolah',
                    name: 'nama_sekolah',
                },
                {
                    data: 'status_verifikasi',
                    name: 'status_verifikasi',
                },
                {
                    data: 'status_sekolah',
                    name: 'status_sekolah',
                },
                {
                    data: 'status_alamat',
                    name: 'status_alamat',
                },
                {
                    data: 'catatan',
                    name: 'catatan',
                }
            ]
        });
    })
</script>

</html>
