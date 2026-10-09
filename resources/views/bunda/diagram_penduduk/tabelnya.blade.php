@php
    $komponen = [
        [
            'index' => 0,
            'judul' => 'Penduduk 5-6 Tahun',
            'kolom' => 'penduduk',
            'reload' => false,
            'color' => '#544fc5',
        ],
    ];
@endphp
<center>
    <h3>{{ $judul }}</h3>
</center>
<div class="row">
    <div class="col-lg-12">
        <div id="wadah_chart">
            <div id="container_chart" style="width:100%; height:400px;"></div>
        </div>
    </div>
</div>
<style>
    #wadah_chart.fixed {
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        /* biar ada efek melayang */
    }
</style>
<script>
    var chart;

    function get_jumlah(index, i, keterangan, reload = null) {
        var data = {
            i: i,
            keterangan: keterangan,
            kecamatan: '{{ $kecamatan }}',
            kelurahan: '{{ $kelurahan }}',
            kecamatan_kode: '{{ $kecamatan_kode }}',
            kelurahan_kode: '{{ $kelurahan_kode }}',
            reload: reload,
        };

        keterangan = keterangan.replace(/&quot;/g, '"');
        var str_keterangan = keterangan.replace(/"/g, '&quot;');;
        var bagan = JSON.parse(keterangan);

        let n_tbody = index;
        let n_tr = 1;
        let n_td = 1;
        var cari_tbody = $('.waduh').find('tbody').eq(n_tbody);

        var html_awal = '<tbody><tr style="background: #ccc">';
        html_awal += '<td style="text-align: left">';
        html_awal += '<b>' + bagan['judul'] + '</b> ';
        if (bagan['reload']) {
            ;
            html_awal += '<span style="float:right">'
            html_awal += '<a href="javascript:void(0)" class="btn btn-sm btn-success" onclick="get_jumlah(\'' + index +
                '\',\'' + i + '\',\'' + str_keterangan + '\',\'reload\')">Reload</a>'
            html_awal += '</span>'
        }
        html_awal += '</td>'
        html_awal += '<td style="text-align: right"></td>'
        html_awal += '</tr>'
        html_awal += '<tr>'
        html_awal += '<td colspan="2"><img src="{{ asset('loading.gif') }}" style="width: 2cm"></td>'
        html_awal += '</tr></tbody>';

        cari_tbody.replaceWith(html_awal);

        $.post('{{ route('bunda-diagram_penduduk-get_jumlah') }}', data, function(data) {
            var html = '';

            $.each(data.dril, function(k, v) {
                html += '<tr>';
                html +=
                    '<td style="text-align: left;padding-left: 1cm"><a href="javascript:void(0)" onclick="detail_data(\'' +
                    v.kategori + '\',\'' + v.name + '\')">' + v.name + '</a></td>';
                html +=
                    '<td style="text-align: right"><a href="javascript:void(0)" onclick="detail_data(\'' +
                    v.kategori + '\',\'' + v.name + '\')">' + v.y + '</a></td>';
                html += '</tr>';
            });

            var cari_tbody = $('.waduh').find('tbody').eq(n_tbody);
            var cari_tr = cari_tbody.find('tr').eq(n_tr);
            var cari_td = cari_tbody.find('tr').eq(0).find('td').eq(n_td);
            cari_td.html(data.jumlah_display);

            if (cari_tr.length > 0) {
                cari_tr.replaceWith(html);
            }

            perbarui_chart(index, data);
        });
    }

    function perbarui_chart(i, data) {
        chart.series[0].setData(data.dril);
    }

    $(document).ready(function() {
        get_chart();
        @foreach ($komponen as $k => $v)
            get_jumlah('{{ $k }}', '{{ $v['index'] }}', '{{ json_encode($v) }}');
        @endforeach

    });

    $(window).on("scroll", function() {
        let $chart = $("#wadah_chart");
        let $parentCol = $chart.parent(); // <div class="col-lg-6"> pembungkus
        let offsetTop = $chart.data("offsetTop");

        // simpan posisi awal sekali saja
        if (!offsetTop) {
            offsetTop = $chart.offset().top;
            $chart.data("offsetTop", offsetTop);
        }

        let scrollTop = $(window).scrollTop();

        if (scrollTop >= offsetTop) {
            if (!$chart.hasClass("fixed")) {
                $chart
                    .addClass("fixed")
                    .css({
                        position: "fixed",
                        top: 0,
                        width: $parentCol.width(), // jaga lebar sesuai col
                        zIndex: 1030,
                        background: "#fff"
                    });

                // biar chart Highcharts ikut menyesuaikan
                if ($chart.highcharts()) {
                    $chart.highcharts().reflow();
                }
            }
        } else {
            if ($chart.hasClass("fixed")) {
                $chart
                    .removeClass("fixed")
                    .css({
                        position: "static",
                        width: "auto",
                        top: "auto",
                        zIndex: "auto",
                        background: "transparent"
                    });
            }
        }
    });

    function detail_data(kategori, jenis) {
        var data = {
            kategori: kategori,
            jenis: jenis,
            kecamatan: '{{ $kecamatan }}',
            kelurahan: '{{ $kelurahan }}',
            kecamatan_kode: '{{ $kecamatan_kode }}',
            kelurahan_kode: '{{ $kelurahan_kode }}',
        };

        $.post("{{ route('bunda-diagram_penduduk-form_detail') }}", data, function(data) {
            $('.modal-page').html(data.content);
        });
    }


    function get_chart() {
        // Data retrieved from https://gs.statcounter.com/browser-market-share#monthly-202201-202201-bar

        // Create the chart
        chart = Highcharts.chart('container_chart', {
            chart: {
                type: 'column'
            },
            style: {
                width: "100%",
                height: "100%"
            },
            title: {
                text: 'Data Pendataan'
            },
            subtitle: {
                text: 'Click the columns to view versions. Source: <a href="http://statcounter.com" target="_blank">statcounter.com</a>'
            },
            accessibility: {
                announceNewData: {
                    enabled: true
                }
            },
            xAxis: {
                type: 'category'
            },
            yAxis: {
                title: {
                    text: 'Data jumlah'
                }

            },
            legend: {
                enabled: false
            },
            plotOptions: {
                series: {
                    borderWidth: 0,
                    dataLabels: {
                        enabled: true,
                        format: '{point.y}'
                    },
                    cursor: 'pointer',
                    point: {
                        events: {
                            click: function() {
                                if (this.options) {
                                    detail_data(this.options.kategori, this.options.name);
                                }
                            }
                        }
                    },
                }
            },

            tooltip: {
                headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
                pointFormat: '<span style="color:{point.color}">{point.name}</span>: ' +
                    '<b>{point.y}</b> of total<br/>'
            },

            series: [{
                name: 'Data Jumlah',
                colorByPoint: true,
                data: [{
                    name: '',
                    y: 0,
                    color: '{{ $v['color'] }}'
                }]
            }],
        });
    }
</script>
