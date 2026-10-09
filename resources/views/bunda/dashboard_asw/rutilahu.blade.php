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
        <h5>Tahun Pengerjaan</h5>
        <select class="form-control tahun_rutilahu" onchange="cari_data_rutilahu()">
            @foreach ($tahun as $k)
                <option>{{ $k }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-lg-12" style="margin-top: 10px">
        <div style="width: 100%;margin: auto">
            <h3>Data Penerima Rutilahu <span class="wadah_tahun_rutilahu"></span></h3>
        </div>
        <div class="row">
            {{-- Jumlah Imunisasi --}}
            <div class="col-lg-6">
                <div class="block block-primary">
                    <div class="block-header block-header-default row no-margin flex">
                        <h3 class="block-title">Data Rutilahu</h3>
                    </div>
                    <div class="block-content block-content-full flex">
                        <div style="overflow: auto">
                            <table style="width: 100%;border-collapse: collapse" border="1">
                                <thead>
                                    <tr>
                                        <th>Tahun Pengerjaan</th>
                                        <th>Sumber Dana</th>
                                        <th>Instansi</th>
                                        <th>Jumlah</th>
                                    </tr>
                                </thead>
                                <tbody class="wadah_data_rutilahu"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    var data_rutilahu = [];
    var data_gizi = [];
    @foreach ($tahun as $k)
        data_rutilahu['{{ $k }}'] = {!! json_encode($data[$k] ?? []) !!};
    @endforeach

    cari_data_rutilahu();

    function cari_data_rutilahu() {
        var tahun = $('.tahun_rutilahu').val();
        $('.wadah_tahun_rutilahu').html(tahun);
        $('.wadah_data_rutilahu').html('');

        var html_imunisasi = '';
        var html_gizi = '';
        $.each(data_rutilahu[tahun], function(k, v) {
            var param = {
                judul: 'Data Rutilahu Usia 0 - 6',
                kategori: '',
                sumber_dana: v.sumber_dana,
                instansi: v.instansi,
                tahun: tahun,
                kelamin: '',
            };
            param = btoa(JSON.stringify(param));
            html_imunisasi += '<tr>';
            html_imunisasi += '<td>' + v.tahun_pengerjaan + '</td>';
            html_imunisasi += '<td>' + v.sumber_dana + '</td>';
            html_imunisasi += '<td>' + v.instansi + '</td>';
            html_imunisasi += '<td style="cursor: pointer" onclick = "modal(\'rutilahu\',\'' + param +
                '\')">' +
                v
                .jumlah_total + ' Orang </td>';
            html_imunisasi += '</tr>';
        });
        $('.wadah_data_rutilahu').html(html_imunisasi);
    }
</script>
