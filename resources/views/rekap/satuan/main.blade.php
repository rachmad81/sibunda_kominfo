@extends('layout.index')

@section('additional_styles')
<title class="judul"></title>
@endsection

@section('additional_styles')
<style>
	tr td {
		text-align:center;
	}
</style>
@endsection
@section('sidebar')
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">
@include('dinas/layout/sidebar')
@endsection

@section('content')
<div class="content" id="app">
	<h2 class="content-heading" style="padding-top: 0px">Rekap Satuan PAUD</h2>
	<!-- Dynamic Table Full -->
	<div class="block">
		<div class="block-header block-header-default row no-margin">
			<h3 class="block-title">Rekap Satuan PAUD</h3>
		</div>
		<div class="block-content block-content-full">
			<div class="col-lg-12">
				<div class="row">
					<div class="col-md-2">
						<div class="form-group row">
							<label class="label-control">Jenjang</label>
							<select class="form-control" id="jenjang">
								<option value="">-- Pilih Jenjang --</option>
								<option value="all">Semua Jenjang</option>
								<option value="TPA">TPA</option>
								<option value="KB">KB</option>
								<option value="TK">TK</option>
								<option value="SPS">SPS</option>
							</select>
						</div>
					</div>
					<div class="col-md-2" id="kecamatannya">
						<div class="form-group row">
							<label class="label-control">Kecamatan</label>
							<select class="form-control" id="kecamatan" onchange="get_kelurahan(this.value)">
								<option value="all">Semua Kecamatan</option>
								@if($kecamatan->count()!=0)
								@foreach($kecamatan as $kec)
								<option value="{{$kec->kecamatan_id}}">{{$kec->kecamatan_dispenduk}}</option>
								@endforeach
								@endif
							</select>
						</div>
					</div>
					<div class="col-md-2" id="kelurahannya">
						<div class="form-group row">
							<label class="label-control">Kelurahan</label>
							<select class="form-control" id="tempat_kelurahan">
								<option value="all">Semua Kelurahan</option>
							</select>
						</div>
					</div>
				</div>
			</div>
			<div class="row" style="margin-bottom: 15px;">
				<div class="col-md-3">
					<a href="javascript:void(0)" class="btn btn-primary" onclick="get_data()"><i class="fa fa-search"></i> Cari</a>
				</div>
			</div>
			@include('layout.alert')
			<div><center><h1 class="judul"></h1></center></div>
			<div style="overflow: auto">
				<table id="dataTable" class="table table-bordered table-striped">
					<thead>
						<tr >
							<th style="width:1%">No</th>
							<th>Jenjang</th>
							<th>Kecamatan</th>
							<th>Kelurahan</th>
							<th>Jumlah RW</th>
							<th>Jumlah RT</th>
							<th>RW Tempat lembaga</th>
							<th>RT Tempat lembaga</th>
							<th>NPSN</th>
							<th width="27%">Nama</th>
							<th>Telepon</th>
							<th>Alamat</th>
							<th>PTK</th>
							<th>Siswa</th>
							<th>Rombel</th>
							<th>Pagu</th>
							<th>Nama KS</th>
							<th>HP KS</th>
						</tr>
					</thead>
					<tbody>

					</tbody>
				</table>
			</div>
		</div>
	</div>
	@include('dinas.user.modal')
</div>
@endsection

@section('additional_scripts')
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/rowgroup/1.2.0/js/dataTables.rowGroup.min.js"></script>
<script type="text/javascript">
	var dt_table = '';
	var judul = 'judul';
	$(document).ready(function(){
		$("#sidebar-rekap").addClass("active");
		dt_table = $("#dataTable").DataTable({
			dom: 'lBfrtip',
			buttons: [
			{
				extend: 'excel',
				title: 'judul',
				filename: function(){
					judul = $('.judul').html()+'_{{date('YmdHi')}}';
					return judul;
				}
			},
			],
			processing: true,
			serverSide: true,
			ajax: {
				url: '{{ route("rekap-satuan_paud-data") }}',
				type: 'get',
			},
			lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
			iDisplayLength: -1,
			columns: [
			{data: 'DT_RowIndex'},
			{data: 'jenjang'},
			{data: 'kecamatan_dispenduk'},
			{data: 'kelurahan_dispenduk'},
			{data: 'jumlah_rw'},
			{data: 'jumlah_rt'},
			{data: 'rw'},
			{data: 'rt'},
			{data: 'npsn'},
			{data: 'nama'},
			{data: 'telp'},
			{data: 'alamat'},
			{data: 'jumlah_pegawai'},
			{data: 'jumlah_siswa'},
			{data: 'jumlah_rombel'},
			{data: 'jumlah_pagu'},
			{data: 'nama_ks'},
			{data: 'hp_ks'},
			],
			order:[[ 2, 'asc' ],[1,'asc']],
			rowGroup: {
				startRender: null,
				endRender: function ( rows, group ) {
					var juml_siswa = rows
					.data()
					.pluck('jumlah_siswa')
					.reduce( function (a, b) {
						return a + b*1;
					}, 0);

					var juml_pegawai = rows
					.data()
					.pluck('jumlah_pegawai')
					.reduce( function (a, b) {
						return a + b*1;
					}, 0);

					var juml_rombel = rows
					.data()
					.pluck('jumlah_rombel')
					.reduce( function (a, b) {
						return a + b*1;
					}, 0);

					return $('<tr/>')
					.append( '<td colspan="12"><b>'+group+' ('+rows.count()+')</b></td>' )
					.append( '<td><b>'+juml_pegawai+'</b></td>' )
					.append( '<td><b>'+juml_siswa+'</b></td>' )
					.append( '<td><b>'+juml_rombel+'</b></td>' )
					.append( '<td><b></b></td>' );
				},
				dataSrc: ['kecamatan_dispenduk']
			}
		});
	});

	function get_data(){
		$('#dataTable tbody').html('');
		var jenjang = $('#jenjang').val();
		var kecamatan = $('#kecamatan').val();
		var tempat_kelurahan = $('#tempat_kelurahan').val();

		var uri = '?jenjang='+jenjang+'&kecamatan='+kecamatan+'&kelurahan='+tempat_kelurahan;

		judul = 'Judul';
		var izinkan = 1;

		if(jenjang==''){
			izinkan = 0;
			swal('WHooops','Jenjang harus dipilih','error');
		}else{

		}

		if(izinkan==1){	
			var nama_tingkat = $('#jenjang option:selected').text();
			if(nama_tingkat!='Semua Jenjang'){
				judul = 'Rekap Satuan PAUD Jenjang '+nama_tingkat;
			}else{
				judul = 'Rekap Satuan PAUD '+nama_tingkat;
			}
			
			var nama_kec = $('#kecamatan option:selected').text();
			if(nama_kec!='Semua Kecamatan'){
				judul += ' Kecamatan '+nama_kec;
			}else{
				judul += ' '+nama_kec;
			}

			var nama_kec = $('#tempat_kelurahan option:selected').text();
			if(nama_kec!='Semua Kelurahan'){
				judul += ' Kelurahan '+nama_kec;
			}else{
				judul += ' '+nama_kec;
			}

			dt_table.ajax.url("{{route('rekap-satuan_paud-data')}}"+uri).load();
		}

		$('.judul').html(judul);
		judul = $('.judul').html();
	}

	function get_kelurahan(kec_kode){
		var html = '<option value="all">Semua Kelurahan</option>';
		if(kec_kode=='all'){
			$('#tempat_kelurahan').html(html);
		}else{
			$.post("{{route('rekap-umum-get_kelurahan')}}",{kec_kode:kec_kode},function(data){
				if(data.length!=0){
					$.each(data,function(k,v){
						html += '<option value="'+v.kelurahan_id+'">'+v.kelurahan_dispenduk+'</option>';
					});
				}
				$('#tempat_kelurahan').html(html);
			});
		}
	}
</script>
@endsection
