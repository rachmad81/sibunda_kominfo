@extends('layout.index')


@section('additional_styles')
<style>
	tr > th, td {
		text-align:center;
	}
</style>
@endsection
@section('sidebar')
@include('bunda/layout/sidebar')
@endsection

@section('content')
<div class="content" id="app">
	<h2 class="content-heading" style="padding-top: 0px">Bina Keluarga Balita</h2>
	<!-- Dynamic Table Full -->
	<div class="block">
		<div class="block-header block-header-default row no-margin">
			<h3 class="block-title">Daftar Sekolah PAUD</h3>
		</div>
		<div class="block-content block-content-full">
			@include('layout.alert')
			<div class="row">
				<div class="col-lg-1">
					<label>Kecamatan</label>
				</div>
				<div class="col-lg-4">
					<select name="kecamatan" onchange="get_data();get_kelurahan()" @if(in_array(Auth::getUser()->tingkat,['kecamatan','kelurahan'])) disabled @endif>
						<option @if($kode_kecamatan=='') selected @endif value="semua">Semua Kecamatan</option>
						@foreach($kecamatan as $kec)
						<option @if($kode_kecamatan==$kec->kecamatan_kode) selected @endif value="{{$kec->kecamatan_kode}}">{{$kec->kecamatan_dispenduk}}</option>
						@endforeach
					</select>
				</div>
			</div>
			<div class="row">
				<div class="col-lg-1">
					<label>Kelurahan</label>
				</div>
				<div class="col-lg-4">
					<select name="kelurahan" onchange="get_data()" @if(in_array(Auth::getUser()->tingkat,['kelurahan'])) disabled @endif>
						<option @if($kode_kelurahan=='') selected @endif value="semua">Semua Kelurahan</option>
						@foreach($kelurahan as $kec)
						<option @if($kode_kelurahan==$kec->kelurahan_kode) selected @endif value="{{$kec->kelurahan_kode}}">{{$kec->kelurahan_dispenduk}}</option>
						@endforeach
					</select>
				</div>
			</div>
			<div class="row">
				<div class="col-lg-1">
					<label>Jenjang</label>
				</div>
				<div class="col-lg-4">
					@php
					$get_jenjang = ['KB','TK','SPS','TPA'];
					@endphp
					<select name="jenjang" onchange="get_data()">
						<option @if($jenjang=='') selected @endif value="semua">Semua Jenjang</option>
						@foreach($get_jenjang as $j)
						<option @if($jenjang==$j) selected @endif value="{{$j}}">{{$j}}</option>
						@endforeach
					</select>
				</div>
			</div>
			<div class="row">
				<div class="col-lg-1">
					<label>Tahun</label>
				</div>
				<div class="col-lg-4">
					<select name="tahun" onchange="get_data()">
						<option selected value="">.. Pilih Tahun ..</option>
						@foreach($thn as $th)
						<option @if($tahun==$th->tahun) selected @endif value="{{$th->tahun}}">{{$th->tahun}}</option>
						@endforeach
					</select>
				</div>
			</div>
			<div class="row">
				<div class="col-lg-12" style="overflow: auto;">
					<div><center><h1 class="judul"></h1></center></div>
					<table id="dataTable" class="table table-bordered table-striped">
						<thead>
							<tr >
								<th style="width:1%">No</th>
								<th>NPSN</th>
								<th width="30%">Nama Sekolah</th>
								<th>Kecamatan</th>
								<th>Kelurahan</th>
								<th>Narasumber</th>
								<th>Topik</th>
								<th>Tempat</th>
								<th>Tgl Pelaksanaan</th>
								<th>Jumlah siswa</th>
								<th>Kehadiran</th>
								<th>Persentase Kehadiran (%)</th>
								<th width="7%">Aksi</th>
							</tr>
						</thead>
						<tbody>

						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
	@include('attention')
</div>
<div class="modal-page"></div>
@endsection

@section('additional_scripts')
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
<style type="text/css">
	.modal { overflow: auto !important; }
	.dt-left{
		text-align: left;
	}
</style>
<script type="text/javascript">
	var dt_table;
	$(document).ready(function(){
		$('#sidebar-kegiatan_pelatihan').addClass('active');
		var tahun = $('select[name=tahun]').val();
		var kecamatan = $('select[name=kecamatan]').val();
		var kelurahan = $('select[name=kelurahan]').val();
		var jenjang = $('select[name=jenjang]').val();
		dt_table = $("#dataTable").DataTable({
			dom:   "<'row'<'col-md-6'l><'col-md-6'B>>" +
			"<'row'<'col-md-12'f>>" +
			"<'row'<'col-md-12't>><'row'<'col-md-12'ip>>",
			buttons: [
			{
				extend: 'excel',
				title: 'judul',
				filename: function(){
					judul = $('.judul').html()+'_{{date('YmdHi')}}';
					return judul;
				}
			},
			{
				extend: 'print',
				title: function(){
					judul = $('.judul').html();
					return judul;
				},
				filename: function(){
					judul = $('.judul').html()+'_{{date('YmdHi')}}';
					return judul;
				}
			},
			],
			lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
			processing: true,
			serverSide: true,
			ajax: '{!! route('bunda-bkb-get_data') !!}?tahun='+tahun+'&kec='+kecamatan+'&kel='+kelurahan+'&jenjang='+jenjang,
			columns: [
				{ data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
				{ data: 'npsn', name: 'npsn' },
				{ data: 'nama_satuan', name: 'nama_satuan' },
				{ data: 'kecamatan', name: 'kecamatan' },
				{ data: 'kelurahan', name: 'kelurahan' },
				{ data: 'narasumber', name: 'narasumber' },
				{ data: 'topik', name: 'topik' },
				{ data: 'tempat', name: 'tempat' },
				{ data: 'tgl_pelaksanaan', name: 'tgl_pelaksanaan' },
				{ data: 'jml_siswa_satuan', name: 'jml_siswa_satuan' },
				{ data: 'kehadiran', name: 'kehadiran' },
				{ data: 'persentase', name: 'persentase' },
				{ data: 'aksi', name: 'aksi', className: 'dt-left'},
				]
		});
	});

	function get_data(){
		$('tbody').html('<tr><td colspan="11"><h4>Harap tunggu.....</h4></td></tr>');
		var tahun = $('select[name=tahun]').val();
		var kecamatan = $('select[name=kecamatan]').val();
		var kelurahan = $('select[name=kelurahan]').val();
		var jenjang = $('select[name=jenjang]').val();
		dt_table.ajax.url("{!! route('bunda-bkb-get_data') !!}?tahun="+tahun+"&kec="+kecamatan+"&kel="+kelurahan+"&jenjang="+jenjang).load();
	}

	function presensi(id){
		$.post("{{route('bunda-bkb-presensi')}}",{id:id},function(data){
			$('.modal-page').html(data.content);
		});
	}

	function get_kelurahan(){
		var kecamatan = $('select[name=kecamatan]').val();
		$.post("{{url('bunda/umum/get_kelurahan')}}",{id:kecamatan},function(data){
			var html = '<option value="semua"> Semua Kelurahan</option>';
			$.each(data,function(k,v){
				html+= '<option value="'+v.kelurahan_kode+'">'+v.kelurahan_dispenduk+'</option>';
			});

			$('select[name=kelurahan]').html(html);
		});
	}
</script>
<script>
</script>
@endsection
