@extends('landing.layout')

@section('additional_styles')
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/rowgroup/1.1.4/css/rowGroup.dataTables.min.css">
@endsection

@section('content')
@php
$jenjang = ['TPA','KB','TK','SPS'];
@endphp
<!-- Page Content -->

<!-- Content -->
<div class="content" style="padding-top: 0px; background-color: transparent;">
	<div class="my-20 text-center">
		<h2 style="margin-bottom: 0px; text-shadow: 3px 3px 6px white;">
			<b><i class="si si-calendar text-muted"></i> Edu City Tour {!! \Auth::check() ? \Auth::User()->getTingkat() : 'Kota Surabaya' !!}</b>
		</h2>
		@if( strlen($cari) > 1 )
		<h3 class="h5 text-muted mb-0" style=" text-shadow: 3px 3px 6px white;">
			Kata kunci pencarian "{{ $cari }}"
		</h3>
		@endif
	</div>
	<div class="row">
		<div class="col-lg-3 col-md-3 col-sm-12 col-xs-12" style="margin-bottom: 5px">
			<select class="form-control" name="cari_tahun">
				<option value="semua">Semua Tahun</option>
				@foreach($data_tahun as $v)
				<option @if($tahun==$v) selected @endif value="{{$v}}">{{$v}}</option>
				@endforeach
			</select>
		</div>
		<div class="col-lg-3 col-md-3 col-sm-12 col-xs-12" style="margin-bottom: 5px">
			<select class="form-control" name="cari_kecamatan" onchange="get_kelurahan()">
				<option value="semua">Semua Kecamatan</option>
				@foreach($kecamatan as $v)
				<option @if($kode_kecamatan==$v->kecamatan_kode) selected @endif value="{{$v->kecamatan_kode}}">{{$v->kecamatan_name}}</option>
				@endforeach
			</select>
		</div>
		<div class="col-lg-3 col-md-3 col-sm-12 col-xs-12" style="margin-bottom: 5px">
			<select class="form-control" name="cari_kelurahan">
				<option value="semua" selected>Semua Kelurahan</option>
			</select>
		</div>
		<div class="col-lg-3 col-md-3 col-sm-12 col-xs-12" style="margin-bottom: 5px">
			<select class="form-control" name="cari_jenjang">
				<option value="semua" selected>Semua Jenjang</option>
				@for($i = 0; $i < count($jenjang); $i++)
				<option {{ $kode_jenjang == $jenjang[$i] ? 'selected' : '' }} value="{{$jenjang[$i]}}">{{$jenjang[$i]}}</option>
				@endfor
			</select>
		</div>
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
			<a href="javascript:void(0)" class="btn btn-secondary" style="line-height: 0" onclick="cari_jadwal()"><i class="fa fa-search"></i> Cari</a>
		</div>
	</div>
	<div style="margin-bottom: 5px">&nbsp;</div>
</div>
<div class="row" style="margin: 10px">
	<div style="width: 100%">
		<table id="table_edu" style="width: 100%" border="1">
			<thead>
				<tr>
					<th rowspan="2" class="text-center">No</th>
					<th rowspan="2" class="text-center">NPSN</th>
					<th rowspan="2" class="text-center">Nama Sekolah</th>
					<th rowspan="2" class="text-center">Tanggal</th>
					<th rowspan="2" class="text-center">Kecamatan</th>
					<th rowspan="2" class="text-center">Kelurahan</th>
					<th rowspan="2" class="text-center">Tempat WIsata</th>
					<th rowspan="2" class="text-center">Alamat</th>
					<th rowspan="2" class="text-center">Harga Tiket</th>
					<th colspan="3" class="text-center">Jumlah Peserta</th>
					<th colspan="3" class="text-center">Penanggung Jawab</th>
				</tr>
				<tr>
					<th class="text-center">Total</th>
					<th class="text-center">L</th>
					<th class="text-center">P</th>
					<th class="text-center">Nama</th>
					<th class="text-center">Jabatan</th>
					<th class="text-center">No HP</th>
				</tr>
			</thead>
		</table>			
	</div>
</div>

<div class="modal" id="modalMap" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Lokasi Sekolah (<span class="nama_sekolah"></span>)</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<iframe width="100%" height="300" id="map" src="" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"></iframe>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>
<div id="template_container">

</div>
@endsection

@section('additional_js')
<script type="text/javascript" src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/rowgroup/1.1.4/js/dataTables.rowGroup.min.js"></script>

<script type="text/javascript">
	@if(isset($menu_active))
	$('#nav_{{ $menu_active }}').children().addClass('active')
	@endif

	var table_edu;

	$(document).ready(function(){
		get_kelurahan();

		table_edu = $('#table_edu').DataTable({
			"ordering": false,
			"responsive": true,
			"processing": true,
			"serverSide": true,
			"ajax": '{{url('/')}}/landing/get_city_tour?tahun={{$tahun}}&kec={{$kode_kecamatan}}&kel={{$kode_kelurahan}}&jenjang={{$kode_jenjang}}',
			"columns":[
				{data:'DT_RowIndex'},
				{data:'npsn'},
				{data:'nama_sekolah'},
				{data:'tanggal'},
				{data:'nama_kecamatan'},
				{data:'nama_kelurahan'},
				{data:'nama_tempat'},
				{data:'alamat_tempat'},
				{data:'htm_tempat'},
				{data:'jumlah_pd'},
				{data:'jumlah_pd_laki'},
				{data:'jumlah_pd_perempuan'},
				{data:'nama_penanggung_jawab'},
				{data:'jabatan_penanggung_jawab'},
				{data:'no_hp_penanggung_jawab'},
				// {data:'npsn', className:'text-center'},
				],
		});
	});

	function show_map(url,nama_sekolah){
		$('#map').attr('src', url);
		$('.nama_sekolah').html(nama_sekolah);
		$('#modalMap').modal('show');
	}

	function get_kelurahan(){
		var kec = $('select[name=cari_kecamatan]').val();

		$.get("{{route('landing',['halaman'=>'get_kelurahan'])}}",{kode_kecamatan:kec},function(data){
			var html = '<option value="semua">Semua Kelurahan</option>';
			if(data.length!=0){
				$.each(data,function(k,v){
					if(v.kelurahan_kode=='{{$kode_kelurahan}}'){
						html += '<option selected value="'+v.kelurahan_kode+'">'+v.kelurahan_name+'</option>';
					}else{
						html += '<option value="'+v.kelurahan_kode+'">'+v.kelurahan_name+'</option>';
					}
				});
			}

			$('select[name=cari_kelurahan]').html(html);
		});
	}

	function cari_jadwal(){
		$('table#table_edu tbody').html('');
		var kec = $('select[name=cari_kecamatan]').val();
		var kel = $('select[name=cari_kelurahan]').val();
		var jenjang = $('select[name=cari_jenjang]').val();
		var tahun = $('select[name=cari_tahun]').val();

		table_edu.ajax.url("{{url('/')}}/landing/get_city_tour?tahun="+tahun+"&kec="+kec+"&kel="+kel+"&jenjang="+jenjang).load();
	}
</script>

@endsection
