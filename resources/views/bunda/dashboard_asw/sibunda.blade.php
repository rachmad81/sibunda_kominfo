<style>
    table {
        border-collapse: collapse;
        width: 100%;
    }

    th,
    td {
        border: 1px solid #000;
    }

    thead tr th {
        vertical-align: top !important;
    }
</style>
<div class="row">
    <div class="col-lg-12">
        <div style="width: 100%;margin: auto">
            <h3>{{ $judul }}</h3>
            @include('bunda.dashboard_asw.sibunda.tabel_survey')
            @include('bunda.dashboard_asw.sibunda.tabel_survey_alamat')
            @include('bunda.dashboard_asw.sibunda.tabel_survey_faktor')
            <div class="row">
                <div class="col-lg-12">
                    <div id="grafik_sibunda"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    var grafik = Highcharts.chart('grafik_sibunda', {
        chart: {
            type: 'column'
        },
        title: {
            text: 'Grafik Data Penduduk',
            align: 'left'
        },
        subtitle: {
            text: '',
            align: 'left'
        },
        xAxis: {
            categories: ['-', '-', '-', '-', '-'],
            crosshair: true,
            accessibility: {
                description: 'Status penduduk'
            }
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Jumlah penduduk'
            },
            stackLabels: {
                enabled: true
            }
        },
        legend: {
            symbolRadius: 3
        },
        tooltip: {
            shared: true,
            headerFormat: '<table><caption>{point.key}</caption>',
            pointFormat: `
            <tr>
              <th>
                <svg width="20" height="10">
                  <rect x="5" y="0" width="10" height="10" rx="3" ry="3"
                      fill="{series.color}" />
                </svg>
                {series.name}
              </th>
              <td>{point.y}</td>
            </tr>
            `,
            footerFormat: '</table>',
            valueSuffix: '  Orang',
            useHTML: true
        },
        plotOptions: {
            column: {
                pointPadding: 0.1,
                borderWidth: 0
            }
        },
        series: [{
            name: "Semua",
            data: [0, 0, 0, 0, 0]
        }]
    });

    function grafiknya(jenis) {
        var kec = $('.kecamatan option:selected');
        var kecamatan = kec.val();
        var kecamatan_kode = kec.data('kode');
        var kel = $('.kelurahan option:selected');
        var kelurahan = kel.val();
        var kelurahan_kode = kel.data('kode');

        var data = {
            kecamatan: kecamatan,
            kelurahan: kelurahan,
            kecamatan_kode: kecamatan_kode,
            kelurahan_kode: kelurahan_kode,
            jenis: jenis,
        };

        $.post('{{ route('bunda-dashboard_asw-get_grafik') }}', data, function(data) {
            grafik.setTitle({
                text: data.judul
            });
            grafik.xAxis[0].setCategories(data.kategori);
            grafik.update({
                series: data.series,
            }, true, true);
        }).fail(function() {
            // $('.' + jenis).html('<center><h5>Error 500- Internal server error</h5></center>');
        });
    }

    grafiknya('sibunda');
</script>
