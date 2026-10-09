<style>
    table {
        border-collapse: collapse;
        width: 100%;
    }

    th,
    td {
        border: 1px solid #000;
    }

    td {
        text-align: left;
        padding: 5px
    }
</style>
<div class="row">
    <div class="col-lg-12">
        <div style="width: 100%;margin: auto">
            <h3>{{ $judul }}</h3>
        </div>
    </div>
    <div class="col-lg-4">
        <h5>Tahun Imunisasi dan Posyandu</h5>
        <select class="form-control tahun_posyandu" onchange="cari_data_posyandu()">
            @foreach ($tahun_cari as $k)
                <option>{{ $k }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-lg-12" style="margin-top: 10px">
        <div style="width: 100%;margin: auto">
            <h3>Data Imunisasi dan Posyandu <span class="wadah_tahun_posyandu"></span></h3>
        </div>
        <div class="row">
            {{-- Jumlah Imunisasi --}}
            <div class="col-lg-6">
                <div class="block block-primary">
                    <div class="block-header block-header-default row no-margin flex">
                        <h3 class="block-title">Data Imunisasi</h3>
                    </div>
                    <div class="block-content block-content-full flex">
                        <div style="overflow: auto">
                            <table style="width: 100%;border-collapse: collapse" border="1">
                                <thead>
                                    <tr>
                                        <th>Imunisasi</th>
                                        <th>Jumlah</th>
                                    </tr>
                                </thead>
                                <tbody class="wadah_imunisasi"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Jumlah Posyandu --}}
            <div class="col-lg-6">
                <div class="block block-success">
                    <div class="block-header block-header-default row no-margin flex">
                        <h3 class="block-title">Data Posyandu</h3>
                    </div>
                    <div class="block-content block-content-full flex">
                        <div style="overflow: auto">
                            <table style="width: 100%;border-collapse: collapse" border="1">
                                <thead>
                                    <tr>
                                        <th>Status Gizi</th>
                                        <th>Jumlah</th>
                                    </tr>
                                </thead>
                                <tbody class="wadah_gizi"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    var data_imunisasi = [];
    var data_gizi = [];
    @foreach ($tahun_cari as $k)
        data_imunisasi['{{ $k }}'] = {!! json_encode($data_imunisasi[$k] ?? []) !!};
        data_gizi['{{ $k }}'] = {!! json_encode($data_gizi[$k] ?? []) !!};
    @endforeach

    cari_data_posyandu();

    function cari_data_posyandu() {
        var tahun = $('.tahun_posyandu').val();
        $('.wadah_tahun_posyandu').html(tahun);
        $('.wadah_imunisasi').html('');
        $('.wadah_gizi').html('');

        var html_imunisasi = '';
        var html_gizi = '';
        $.each(data_imunisasi[tahun], function(k, v) {
            var param = {
                judul: 'Data Posyandu Usia 0 - 6',
                kategori: v.imunisasi,
                kelamin: '',
            };
            param = btoa(JSON.stringify(param));
            html_imunisasi += '<tr>';
            html_imunisasi += '<td>' + v.imunisasi + '</td>';
            html_imunisasi += '<td style="cursor: pointer" onclick = "modal(\'posyandu_imunisasi\',\'' + param +
                '\')">' +
                v
                .jumlah_total + ' Orang </td>';
            html_imunisasi += '</tr>';
        });
        $('.wadah_imunisasi').html(html_imunisasi);
        $.each(data_gizi[tahun], function(k, v) {
            var param = {
                judul: 'Data Posyandu Usia 0 - 6',
                kategori: v.status_bb_tb,
                kelamin: '',
            };
            param = btoa(JSON.stringify(param));
            html_gizi += '<tr>';
            html_gizi += '<td>' + v.status_bb_tb + '</td>';
            html_gizi += '<td style="cursor: pointer" onclick = "modal(\'posyandu_timbang\',\'' + param +
                '\')">' + v
                .jumlah_total + ' Orang </td>';
            html_gizi += '</tr>';
        });
        $('.wadah_gizi').html(html_gizi);
    }
</script>
