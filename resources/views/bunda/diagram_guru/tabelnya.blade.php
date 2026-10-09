@php
// $komponen = [
// 	0=>'Jumlah Anak usia 5-7 tahun',
// 	1=>'Jumlah Siswa Aktif (Profil Sekolah) usia 5-7 tahun (Berdasarkan alamat siswa (KK Surabaya))',
// 	7=>'Jumlah Siswa Aktif (Profil Sekolah) (Berdasarkan alamat siswa (KK Surabaya))',
// 	8=>'Jumlah Siswa Aktif (Profil Sekolah)',
// 	2=>'Jumlah Lembaga TPA',
// 	3=>'Jumlah Lembaga SPS',
// 	4=>'Jumlah Lembaga KB',
// 	5=>'Jumlah Lembaga TK',
// 	6=>'Jumlah Lembaga RA',
// ];

$komponen = [
	[
		'index'=>0,
		'judul'=>'Semua Jenjang',
		'jenjang'=>'Semua',
	],
	[
		'index'=>1,
		'judul'=>'Jenjang TPA',
		'jenjang'=>'TPA',
	],
	[
		'index'=>2,
		'judul'=>'Jenjang SPS/PPT',
		'jenjang'=>'SPS/PPT',
	],
	[
		'index'=>3,
		'judul'=>'Jenjang KB',
		'jenjang'=>'KB',
	],
	[
		'index'=>4,
		'judul'=>'Jenjang TK',
		'jenjang'=>'TK',
	],
	[
		'index'=>5,
		'judul'=>'Jenjang RA',
		'jenjang'=>'RA',
	],
];
@endphp
<center>
	<h3>{{$judul}}</h3>
</center>
<div class="row">
	<div class="col-lg-4">
		<table class="table table-bordered waduh">
			<thead>
				<tr>
					<th>Komponen</th>
					<th>Jumlah</th>
				</tr>
			</thead>
			@foreach($komponen as $k=>$v)
			<tbody>
				<tr style="background: #ccc">
					<td style="text-align: left"><b>{{$v['judul']}}</b></td>
					<td style="text-align: right"></td>
				</tr>
				<tr>
					<td colspan="2"><img src="{{asset('loading.gif')}}" style="width: 2cm"></td>
				</tr>
			</tbody>
			@endforeach
		</table>
	</div>
	<div class="col-lg-8">
		<div id="wadah_chart">
			<div id="container_chart" style="width:100%; height:400px;"></div>
		</div>
	</div>
</div>
<style>
	#wadah_chart.fixed {
		box-shadow: 0 2px 5px rgba(0,0,0,0.1); /* biar ada efek melayang */
	}
</style>
<script>
	var chart;
	var categories = [];
	var series = [];
	var data_series = [];

	function get_jumlah(index,jenjang,keterangan){
		var data = {
			jenjang:jenjang,
			keterangan:keterangan,
			kecamatan:'{{$kecamatan}}',
			kelurahan:'{{$kelurahan}}',
			kecamatan_kode:'{{$kecamatan_kode}}',
			kelurahan_kode:'{{$kelurahan_kode}}',
		};
		var bagan = JSON.parse(keterangan.replace(/&quot;/g, '"'));

		$.post('{{route('bunda-diagram_guru-get_jumlah')}}',data,function(data){
			var html = '';

			$.each(data.dril,function(k,v){
				html+='<tr>';
				html+='<td style="text-align: left;padding-left: 1cm"><a href="javascript:void(0)" onclick="detail_data(\''+v.kategori+'\',\''+v.name+'\')">'+v.name+'</a></td>';
				html+='<td style="text-align: right"><a href="javascript:void(0)" onclick="detail_data(\''+v.kategori+'\',\''+v.name+'\')">'+v.y+'</a></td>';
				html += '</tr>';
			});

			let n_tbody = index;
			let n_tr = 1;
			let n_td = 1;
			let cari_tbody = $('.waduh').find('tbody').eq(n_tbody);
			let cari_tr = cari_tbody.find('tr').eq(n_tr);
			let cari_td = cari_tbody.find('tr').eq(0).find('td').eq(1);

			console.log(cari_td.length);

			cari_td.html(data.jumlah_display);

			if (cari_tr.length > 0) {
				cari_tr.replaceWith(html);
			}

			perbarui_chart(index,data);
		});
	}

	function perbarui_chart(i,data){
		$.each(data.dril,function(k,v){
			chart.series[k].data[i].update(v.y);
		});
	}

	$(document).ready(function(){
		@foreach($komponen as $k)
		categories.push('{{$k['jenjang']}}');
		data_series.push(0);
		@endforeach

		series = [
			{
				name: 'Semua Lulusan',
				data: data_series,
			},
			{
				name: 'Lulusan SMA',
				data: data_series,
			},
			{
				name: 'Lulusan D1 - D3',
				data: data_series,
			},
			{
				name: 'Lulusan S1 - S3',
				data: data_series,
			},
			{
				name: 'Lulusan Lainnya',
				data: data_series,
			},
		];

		get_chart();
		@foreach($komponen as $k=>$v)
		get_jumlah('{{$v['index']}}','{{$v['jenjang']}}','{!! json_encode($v) !!}');
		@endforeach
	});

	$(window).on("scroll", function () {
	    let $chart = $("#wadah_chart");
	    let $parentCol = $chart.parent();   // <div class="col-lg-6"> pembungkus
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

	function detail_data(kategori,jenis){
		var data = {
			kategori:kategori,
			jenis:jenis,
			kecamatan:'{{$kecamatan}}',
			kelurahan:'{{$kelurahan}}',
			kecamatan_kode:'{{$kecamatan_kode}}',
			kelurahan_kode:'{{$kelurahan_kode}}',
		};

		$.post("{{route('bunda-diagram_guru-form_detail')}}",data,function(data){
			$('.modal-page').html(data.content);
		});
	}

	function get_chart(){
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
				text: 'Data Kualifikasi Guru'
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
				type: 'category',
				categories: categories,
				crosshair: true,
		        accessibility: {
		            description: 'Jenjang'
		        }
			},
			yAxis: {
				title: {
					text: 'Data jumlah'
				}

			},
			legend: {
				enabled: true
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
							click: function () {
								if(this.options){
									{{-- console.log(this); --}}
									detail_data(this.category,this.series.name);
								}
							}
						}
					},
				}
			},

			tooltip: {
				headerFormat: '<span style="font-size:11px">{point.category}</span><br>',
				pointFormat: '<span style="color:{point.color}">{series.name}</span>: ' +
				'<b>{point.y}</b> of total<br/>'
			},

			series: series,
		});
	}

</script>