@extends('layout.index')


@section('additional_styles')
<style>
	tr>th>td {
		text-align: center;
	}
	.aktif-form{
		background: #ccc;
	}
</style>
@endsection
@section('sidebar')
@include('dinas/layout/sidebar')
@endsection

@section('content')
<div class="content" id="app">
	<h2 class="content-heading" style="padding-top: 0px">Penilaian Tahap 1 </h2>
	<!-- Dynamic Table Full -->
	<div class="block">
		<div class="block-header block-header-default row no-margin">
			<h3 class="block-title">Penilaian Tahap 1 </h3>
		</div>
		<div class="block-content block-content-full">
			@include('layout.alert')
			<div class="row">
				<div class="col-lg-2">
					<select class="form-control" name="tahun" onchange="get_data()">
						<option value="">Pilih Tahun</option>
						@for($i=2024;$i<=date('Y');$i++)
						<option {{($i==date('Y')) ? 'selected' : ''}} value="{{$i}}">{{$i}}</option>
						@endfor
					</select>
				</div>
			</div>
			<div class="row">
				<div class="col-lg-6">
					<a href="javascript:void(0)" class="btn btn-success" onclick="rekap_lomba()">Rekap</a>
					<a href="javascript:void(0)" class="btn btn-success" onclick="rekap_nilai()">Rekap Penilaian Tahap 1</a>
					<a href="javascript:void(0)" class="btn btn-success" onclick="rekap_nilai_2()">Rekap Penilaian Tahap 2</a>
					<a href="javascript:void(0)" class="btn btn-success" onclick="rekap_nilai_akhir()">Rekap Penilaian Akhir</a>
				</div>
			</div>
			<div class="row">
				<div class="col-lg-12">
					<div class="table-responsive">
						<table id="dataTable" class="table table-bordered table-striped">
							<thead>
								<tr>
									{{-- <th>No</th> --}}
									<th>Nama</th>
									<th>Tingkat</th>
									<th>Kecamatan</th>
									<th>Kelurahan</th>
									<th>Kriteria Utama</th>
									<th>Kriteria Pendukung</th>
									<th>Identitas Peserta</th>
									<th>Nilai Tahap 1</th>
									<th>Nilai Tahap 2</th>
									<th>Nilai Akhir (40% Tahap1+60% Tahap2)</th>
								</tr>
							</thead>
							<tbody></tbody>
						</table>
					</div>
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
	var dt_table;
	$(document).ready(function(){
		var tahun = $('select[name=tahun]').val();
		var status_utama = $('select[name=status_utama]').val();
		var status_pendukung = $('select[name=status_pendukung]').val();
		$('#sidebar-lomba').addClass('active');

		dt_table = $("#dataTable").DataTable({
			dom: "<'row'<'col-md-6'l><'col-md-6'B>>" +
			"<'row'<'col-md-12'f>>" +
			"<'row'<'col-md-12't>><'row'<'col-md-12'ip>>",
			buttons: [{
				extend: 'excel',
				title: 'judul',
				filename: function() {
					judul = $('.judul').html() + '_{{ date('d-m-Y H.i') }}';
					return judul;
				}
			},
			{
				extend: 'print',
				title: function() {
					judul = $('.judul').html();
					return judul;
				},
				filename: function() {
					judul = $('.judul').html() + '_{{ date('d-m-Y H.i') }}';
					return judul;
				}
			},
			],
			lengthMenu: [
				[10, 25, 50, -1],
				[10, 25, 50, "All"]
				],
			processing: true,
			// serverSide: true,
			order: false,
			ajax: '{!! route('dinas-lomba-get_data') !!}?tahun='+tahun+'&utama='+status_utama+'&pendukung='+status_pendukung,
			columns: [
			// {data: row(),},
			{data: 'nama',},
			{data: 'tingkat',},
			{data: 'kec_instansi',},
			{data: 'kel_instansi',},
			{data: 'st_kriteria_utama',},
			{data: 'st_kriteria_pendukung',},
			{data: 'identitas',},
			{data: 'nilai',},
			{data: 'nilai_tahap2',},
			{data: 'nilai_akhir',},
			]
		});
	});

	function get_data(){
		var tahun = $('select[name=tahun]').val();
		var status_utama = $('select[name=status_utama]').val();
		var status_pendukung = $('select[name=status_pendukung]').val();

		$('tbody').html('<tr><td colspan="6">Sedang mencari</td></tr>');

		if(tahun==''){
			swal('Whooops','Tahun harus dipilih','error');
		}else{
			dt_table.ajax.url('{{route('dinas-lomba-get_data')}}?tahun='+tahun+'&utama='+status_utama+'&pendukung='+status_pendukung).load();
		}
	}

	function modal_identitas(user_id){
		var tahun = $('select[name=tahun]').val();
		$.post('{{route('dinas-lomba-modal_identitas')}}',{user_id:user_id,tahun:tahun},function(data){
			$('.modal-page').html(data.content);
		});
	}

	function modal_nilai(user_id){
		var tahun = $('select[name=tahun]').val();
		$.post('{{route('dinas-lomba-modal_nilai')}}',{user_id:user_id,tahun:tahun},function(data){
			$('.modal-page').html(data.content);
		});
	}

	function modal_nilai2(user_id){
		var tahun = $('select[name=tahun]').val();
		$.post('{{route('dinas-lomba-modal_nilai2')}}',{user_id:user_id,tahun:tahun},function(data){
			$('.modal-page').html(data.content);
		});
	}

	function reset(id,tabel){
		var tahun = $('select[name=tahun]').val();
		Swal.fire({
			title: 'Apa kamu yakin?',
			text: "Status akan dikembalikan menjadi Draft",
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			confirmButtonText: 'Ya, Aku yakin!'
		}).then((result) => {
			if (result.value) {
				$.post('{{route('dinas-lomba-reset')}}',{id:id,tabel:tabel},function(data){
					$('.modal').modal('hide');
					if(data.code==200){
						$('#myModalIdentitas').modal('hide');
						if(tabel=='kriteria_utama' || tabel=='kriteria_pendukung'){
							get_data();
						}else if(tabel=='nilai_kriteria_tahap2'){
							modal_nilai2(data.user_id);
						}else{
							modal_nilai(data.user_id);
						}
						swal('Success',data.message,'success');
					}else{
						swal('Whooops',data.message,'error');
					}
				});
			}else{
			}
		})
	}

	function rekap_lomba(){
		var tahun = $('select[name=tahun]').val();
		window.open('{{url('api/rekap/lomba/lomba')}}?tahun='+tahun,'');
	}

	function detail(user_id,jenis){
		var tahun = $('select[name=tahun]').val();
		var data = {
			tahun:tahun,
			user_id:user_id,
			jenis:jenis,
		};

		$.post('{{route('dinas-lomba-detail')}}',data,function(data){
			if(data.code=='200'){
				$('.modal-page').html(data.content);
			}
		}).fail(function(e){
			swal('Whoops',e.responseJSON.message,'error');
		});
	}

	function rekap_nilai(){
		var tahun = $('select[name=tahun]').val();
		window.open('{{url('api/rekap/nilai/nilai')}}?tahun='+tahun,'');
	}

	function rekap_nilai_2(){
		var tahun = $('select[name=tahun]').val();
		window.open('{{url('api/rekap/nilai2/nilai')}}?tahun='+tahun,'');
	}

	function rekap_nilai_akhir(){
		var tahun = $('select[name=tahun]').val();
		window.open('{{url('api/rekap/nilai_akhir/nilai')}}?tahun='+tahun,'');
	}
</script>
@endsection
