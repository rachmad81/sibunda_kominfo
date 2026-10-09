@extends('layout.index')


@section('additional_styles')
<style>
	tr>th,
	td {
		text-align: center;
	}
</style>
@endsection
@section('sidebar')
@if (Auth::getUser()->role == 'bunda')
@include('bunda/layout/sidebar')
@elseif(Auth::getUser()->role == 'dinas')
@include('dinas/layout/sidebar')
@else
@include('opd/layout/sidebar')
@endif
@endsection

@section('content')
<div class="content" id="app">
	<h2 class="content-heading" style="padding-top: 0px">Diagram Lembaga</h2>
	<!-- Dynamic Table Full -->
	<div class="block">
		<div class="block-header block-header-default row no-margin">
			<h3 class="block-title">Diagram Lembaga</h3>
		</div>
		<div class="block-content block-content-full">
			<div class="row">
				<div class="col-lg-6">
					@if(Auth::getUser()->role=='dinas' || (Auth::getUser()->role=='bunda' && in_array(Auth::getUser()->tingkat,['kota'])))
					<div>
						<label>Kecamatan</label>
						<select class="form-control kecamatan" onchange="get_kelurahan()">
							<option data-kode='All' data-dukcapil="semua" value="All">..:: Semua ::..</option>
							@foreach($kecamatan as $kec)
							<option data-kode="{{$kec->kecamatan_kode}}" data-dukcapil="{{$kec->kecamatan_dispenduk}}">{{$kec->kecamatan_dispenduk}}</option>
							@endforeach
						</select>
					</div>
					@elseif(Auth::getUser()->role=='bunda' && in_array(Auth::getUser()->tingkat,['kecamatan']))
					<div>
						<label>Kecamatan</label>
						<select class="form-control kecamatan" disabled onchange="get_kelurahan()">
							<option data-kode='All' data-dukcapil="semua" value="All">..:: Semua ::..</option>
							@foreach($kecamatan as $kec)
							<option @if($kec->kecamatan_id==Auth::getUser()->id_kecamatan) selected @endif data-kode="{{$kec->kecamatan_kode}}" data-dukcapil="{{$kec->kecamatan_dispenduk}}">{{$kec->kecamatan_dispenduk}}</option>
							@endforeach
						</select>
					</div>
					@endif
					<div>
						<label>Kelurahan</label>
						<select class="form-control kelurahan">
							<option value="All">..:: Semua ::..</option>
						</select>
					</div>
					<br>
					<a href="javascript:void(0)" onclick="get_data()" class="btn btn-primary">Cari</a>
				</div>
			</div>
			<div class="row">
				<div class="col-lg-12 wadah_tabel" style="overflow: auto">

				</div>
			</div>
		</div>
	</div>
</div>
<div class="modal-page"></div>
@endsection

@section('additional_scripts')
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/data.js"></script>
<script src="https://code.highcharts.com/modules/drilldown.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>
<script src="https://code.highcharts.com/themes/adaptive.js"></script>
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
	$(document).ready(function() {
		$('#sidebar-diagram').addClass('active');
		get_kelurahan();
		{{-- $('#datatable').DataTable(); --}}
	});

	function get_kelurahan(){
		var kec = $('.kecamatan option:selected');
		var kode = kec.data('kode');
		var dukcapil = kec.data('dukcapil');

		var data = {
			kode:kode,
			dukcapil:dukcapil,
		};

		$.post('{{route('bunda-diagram_lembaga-get_kelurahan')}}',data,function(data){
			if(data.code==200){
				var html = '';
				@if(Auth::getUser()->tingkat=='kelurahan')
				if(data.kelurahan.length>0){
					$.each(data.kelurahan,function(k,v){
						html+='<option data-kode="'+v.kelurahan_kode+'">'+v.kelurahan_dispenduk+'</option>';
					});
				}
				$('.kelurahan').attr('disabled','disabled');
				@else
				html += '<option value="All">..:: Semua ::..</option>';
				if(data.kelurahan.length>0){
					$.each(data.kelurahan,function(k,v){
						html+='<option data-kode="'+v.kelurahan_kode+'">'+v.kelurahan_dispenduk+'</option>';
					});
				}
				$('.kelurahan').removeAttr('disabled');
				@endif
				$('.kelurahan').html(html);
			}else if(data.code=='250'){
				$('.kelurahan').html('<option value="All">..:: Semua ::..</option>');
			}
		});
	}

	function get_data(){
		var kec = $('.kecamatan option:selected');
		var kecamatan = kec.val();
		var kecamatan_kode = kec.data('kode');
		var kel = $('.kelurahan option:selected');
		var kelurahan = kel.val();
		var kelurahan_kode = kel.data('kode');

		var data = {
			kecamatan:kecamatan,
			kelurahan:kelurahan,
			kecamatan_kode:kecamatan_kode,
			kelurahan_kode:kelurahan_kode,
		};

		$.post('{{route('bunda-diagram_lembaga-get_data')}}',data,function(data){
			$('.wadah_tabel').html(data.content);
		});
	}
</script>
@endsection
