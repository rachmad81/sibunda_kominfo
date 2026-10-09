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
@include('penilai/layout/sidebar')
@endsection

@section('content')
<div class="content" id="app">
	<h2 class="content-heading" style="padding-top: 0px">Penilaian Tahap 2 </h2>
	<!-- Dynamic Table Full -->
	<div class="block">
		<div class="block-header block-header-default row no-margin">
			<h3 class="block-title">Penilaian Tahap 2 </h3>
		</div>
		<div class="block-content block-content-full">
			@include('layout.alert')
			<div class="row">
				<div class="col-lg-12">
					<a href="https://drive.google.com/file/d/1jMwDIyEZcqraldKf7RmJYm6q3dNRwUI6/view?usp=sharing" target="_blank" class="btn btn-primary">Panduan Mengisi Penilaian</a>
				</div>
			</div>
			<div class="row">
				<div class="col-lg-2">
					<select class="form-control" name="tahun" onchange="ganti_tahun()">
						<option value="">Pilih Tahun</option>
						@for($i=2024;$i<=date('Y');$i++)
						<option {{($i==$tahun) ? 'selected' : ''}} value="{{$i}}">{{$i}}</option>
						@endfor
					</select>
				</div>
			</div>
			<div class="row">
				<div class="col-lg-12">
					<div class="table-responsive">
						<table id="dataTable" class="table table-bordered table-striped">
							<thead>
								<tr>
									<th rowspan="2">No</th>
									<th rowspan="2">Nama</th>
									<th rowspan="2">Tingkat</th>
									<th rowspan="2">Kecamatan</th>
									<th rowspan="2">Kelurahan</th>
									<th rowspan="2">Identitas Peserta</th>
									<th colspan="3">Tahap 1</th>
									<th rowspan="2">Nilai Tahap 2</th>
									<th rowspan="2">Status</th>
								</tr>
								<tr>
									<th>Kriteria Utama</th>
									<th>Kriteria Pendukung</th>
									<th>Nilai</th>
								</tr>
							</thead>
							<tbody class="tempat-data"></tbody>
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
	$(document).ready(function(){
		$('#sidebar-penilaian').addClass('active');
		$('#dataTable').DataTable();
		get_data()
	});

	function ganti_tahun(){
		var tahun = $('select[name=tahun]').val();
		window.location='{{url('/')}}/penilai/penilaian/tahap2/'+tahun;
	}

	function get_data(){
		var tahun = $('select[name=tahun]').val();
		var status_utama = $('select[name=status_utama]').val();
		var status_pendukung = $('select[name=status_pendukung]').val();


		$('tbody').html('<tr><td colspan="8">Sedang mencari</td></tr>');

		if(tahun==''){
			swal('Whooops','Tahun harus dipilih','error');
		}else{
			$('#dataTable').DataTable().destroy();
			$.get('{{route('penilai-penilaian-tahap2-get_data',['tahun'=>$tahun])}}',{tahun:tahun,utama:status_utama,pendukung:status_pendukung},function(data){
				$('.tempat-data').html(data.content);
				$('#dataTable').DataTable({
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
				})
			});
		}
	}

	function modal_identitas(user_id){
		var tahun = $('select[name=tahun]').val();
		$.post('{{route('penilai-penilaian-modal_identitas',['tahun'=>$tahun])}}',{user_id:user_id,tahun:tahun},function(data){
			$('.modal-page').html(data.content);
		});
	}

	function modal_penilaian(user_id){
		var tahun = $('select[name=tahun]').val();
		$.post('{{route('penilai-penilaian-tahap2-modal_penilaian',['tahun'=>$tahun])}}',{user_id:user_id,tahun:tahun},function(data){
			$('.modal-page').html(data.content);
		});
	}

	function simpan_nilai(namainput,table,bunda_id,sebagai=null){
		var data = new FormData();
		data.append('berkas',namainput);
		data.append('table',table);
		data.append('bunda_id',bunda_id);
		if(namainput=='status'){
			var file = $('select[name='+namainput+']').val();
		}else{
			var file = $('input[name='+namainput+']').val();
		}
		data.append('file',file);

		if(file){
			$.ajax({
				url: '{{route('penilai-penilaian-tahap2-simpan_nilai',['tahun'=>$tahun])}}',
				method: 'post',
				data: data,
				contentType: false,
				processData: false,
				success: function(data){
					if(data.code==200){
						swal('Success',data.message,'success');
						if(sebagai!=null){
							$('#myModalIdentitas').modal('hide');
							get_data();
						}
					}else{
						swal('Whooops',data.message,'error');
					}
				}
			})
		}else{
			swal('Whooops','File belum dipilih','error');
		}
	}

	{{-- TAHAP 1 --}}
	function modal_nilai(user_id){
		var tahun = $('select[name=tahun]').val();
		$.post('{{route('penilai-penilaian-tahap2-modal_nilai_tahap1',['tahun'=>$tahun])}}',{user_id:user_id,tahun:tahun},function(data){
			$('.modal-page').html(data.content);
		});
	}

	function detail(user_id,jenis){
		var tahun = $('select[name=tahun]').val();
		var data = {
			tahun:tahun,
			user_id:user_id,
			jenis:jenis,
		};

		$.post('{{route('penilai-penilaian-tahap2-detail',['tahun'=>$tahun])}}',data,function(data){
			if(data.code=='200'){
				$('.modal-page').html(data.content);
			}
		}).fail(function(e){
			swal('Whoops',e.responseJSON.message,'error');
		});
	}
	{{-- END TAHAP 1 --}}
</script>
@endsection
