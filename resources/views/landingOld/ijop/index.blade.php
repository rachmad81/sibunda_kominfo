@extends('landing.layout')
@section('additional_styles')
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/rowgroup/1.5.0/css/rowGroup.dataTables.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/3.1.2/css/buttons.dataTables.css">
<style type="text/css">
	#table_ijop{
		background: white;
	}
</style>
@endsection
@section('content')
<!-- Page Content -->
<!-- Content -->
<div class="content">
	<div class="my-20 text-center">
		<h2 style="margin-bottom: 0px; text-shadow: 2px 2px 8px white">
			<b><i class="si si-graduation text-muted"></i> Daftar Status Ijin Operasional Sekolah {!! \Auth::check() ? \Auth::User()->getTingkat() : 'Kota Surabaya' !!}</b>
		</h2>
	</div>
	<div class="row">
		<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12" style="margin-bottom: 5px">
			<select class="form-control" name="cari_kecamatan" onchange="get_kelurahan()">
				<option value="semua">Semua Kecamatan</option>
				@foreach($kecamatan as $v)
				<option @if($kode_kecamatan==$v->kecamatan_kode) selected @endif value="{{$v->kecamatan_kode}}">{{$v->kecamatan_name}}</option>
				@endforeach
			</select>
		</div>
		<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12" style="margin-bottom: 5px">
			<select class="form-control" name="cari_kelurahan">
				<option value="semua" selected>Semua Kelurahan</option>
			</select>
		</div>
		<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12" style="margin-bottom: 5px">
			<select class="form-control" name="cari_jenjang">
				<option value="semua" selected>Semua Jenjang</option>
				@for($i = 0; $i < count($jenjang); $i++)
				<option {{ $kode_jenjang == $jenjang[$i] ? 'selected' : '' }} value="{{$jenjang[$i]}}">{{$jenjang[$i]}}</option>
				@endfor
			</select>
		</div>
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
			<a href="javascript:void(0)" class="btn btn-secondary" style="line-height: 0" onclick="cari_sekolah()"><i class="fa fa-search"></i> Cari</a>
		</div>
	</div>
	<div style="margin-bottom: 5px">&nbsp;</div>
	<div class="row p-1">
		<div style="width: 100%;overflow: auto;">
			<table id="table_ijop" class="display nowrap" style="width: 100%">
				<thead>
					<tr>
						<th class="text-center">NPSN</th>
						<th class="text-center">Nama Sekolah</th>
						<th class="text-center">Kecamatan</th>
						<th class="text-center">Kelurahan</th>
						<th class="text-center">Selesai Berlaku</th>
						<th class="text-center">Status</th>
						<th class="text-center">Hasil Monitoring</th>
						<th class="text-center">Keterangan</th>
					</tr>
				</thead>
			</table>			
		</div>
	</div>
	<div class="clearfix" style="margin-bottom: 50px"></div>
</div>
@endsection
@section('additional_js')
<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
<script src="https://cdn.datatables.net/2.1.8/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/rowgroup/1.5.0/js/dataTables.rowGroup.js"></script>
<script src="https://cdn.datatables.net/rowgroup/1.5.0/js/rowGroup.dataTables.js"></script>

<script src="https://cdn.datatables.net/buttons/3.1.2/js/dataTables.buttons.js"></script>
<script src="https://cdn.datatables.net/buttons/3.1.2/js/buttons.dataTables.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/3.1.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/3.1.2/js/buttons.print.min.js"></script>

<script type="text/javascript">
	$(document).ready(function() {
		get_kelurahan();
		
		var table = $('#table_ijop').DataTable({
			dom: '<Blf><t>ip>',
			buttons: ['excel', 'pdf', 'print'],
			ordering: false,
			responsive: true,
			processing: true,
			serverSide: true,
			ajax: '{{url('/')}}/landing/get_ijop?kec={{$kode_kecamatan}}&kel={{$kode_kelurahan}}&jenjang={{$kode_jenjang}}',
			columns:[
				{data:'npsn'},
				{data:'nama_sekolah'},
				{data:'kecamatan_name'},
				{data:'kelurahan_name'},
				{data:'waktu'},
				{data:'status'},
				{data:'hasil_monitoring', className:'text-center'},
				{data:'keterangan_monitoring'},
				],
			lengthMenu: [
                [10, 25, 50, -1],
                ['10', '25', '50', 'Semua']
            ],
			rowGroup: {
				dataSrc: ['kecamatan_name','kelurahan_name']
			},
		});

		// table.columns( [2] ).visible( false );
	} );
	@if(isset($data["menu_active"]))
	$('#nav_{{ $data["menu_active"] }}').children().addClass('active')
	@endif

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

	function cari_sekolah(){
		var kec = $('select[name=cari_kecamatan]').val();
		var kel = $('select[name=cari_kelurahan]').val();
		var jenjang = $('select[name=cari_jenjang]').val();

		window.location = "{{Request::url()}}?kecamatan="+kec+"&kelurahan="+kel+"&jenjang="+jenjang+"&cari={{$cari}}";
	}
</script>
@endsection