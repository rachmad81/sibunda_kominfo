@extends('layout.index')


@section('additional_styles')
<style>
	tr>th>td {
		text-align: center;
	}
	.aktif-form{
		background: #ccc;
	}
	.disabled{
		background: #fff !important;
		color: black;
	}
	ul li{
		list-style-type: circle !important;
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
	<h2 class="content-heading" style="padding-top: 0px">Imunisasi ({{$judul}})</h2>
	<!-- Dynamic Table Full -->
	<div class="block">
		<div class="block-header block-header-default row no-margin">
			<h3 class="block-title">Imunisasi ({{$judul}})</h3>
		</div>
		<div class="block-content block-content-full">
			@include('layout.alert')
			<div class="row">
				<div class="col-lg-12" style="overflow: auto">
					<div>
						<center>
							<h1 class="judul" style="display:none;">Imunisasi ({{$judul}})</h1>
						</center>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-lg-12" style="overflow: auto">
					<form class="form_pencarian">
						<table class="table table-bordered">
							<tbody>
								<tr>
									<td style="width: 45%">Jenjang</td>
									<td style="width: 50%;">
										<label style="margin-right: 1cm;"><input type="radio" name="jenjang" value="PAUD" checked> PAUD</label>
										<label><input type="radio" name="jenjang" value="SD"> SD/ sederajat</label>
									</td>
								</tr>
								<tr>
									<td>
										Cari Satuan Pendidikan
									</td>
									<td>
										<input type="hidden" name="npsn">
										<input type="text" name="" class="nama form-control" placeholder="Cari satuan pendidikan" onkeyup="cari_sekolah(this)">
										<a href="javascript:void(0)" class="btn-open btn btn-sm btn-primary" onclick="set_sekolah('','','','')">Ganti Sekolah</a>
										<div class="tempat_sekolah"></div>
									</td>
								</tr>
								<tr>
									<td style="width: 45%">Nama Satuan (NPSN)</td>
									<td style="width: 50%;">
										<span class="npsn"></span>
									</td>
								</tr>
								<tr>
									<td>Kelas</td>
									<td>
										<select class="form-control" name="kelas">
											
										</select>
									</td>
								</tr>
								<tr>
									<td colspan="2">
										<a href="javascript:void(0)" class="btn btn-primary form-control" onclick="tampilkan()">Tampilkan</a>
									</td>
								</tr>
							</tbody>
						</table>
					</form>

					<table class="table table-bordered table-striped">
						<thead>
							<tr>
								<th>No</th>
								<th>Nama</th>
								<th>Kelas</th>
								<th>Riwayat</th>
							</tr>
						</thead>
						<tbody class="wadah_siswa"></tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
	@include('attention')
</div>
<div class="modal-page"></div>
<div>
	@include('bunda.lomba.panduan')
</div>
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
		$('#sidebar-imunisasi').addClass('active');
		set_sekolah('','','','');
	});

	function set_kunci(kunci){
		if(kunci==''){
			$('input[name=npsn]').val('');
			$('.nama').show();
			$('.btn-open').hide();
		}else{
			$('.nama').hide();
			$('.btn-open').show();
		}
	}

	function set_kunci1(kunci){
		if(kunci==''){
			$('input[name=siswa_id]').val('');
			$('.nama_siswa').show();
			$('.btn-open1').hide();
		}else{
			$('.nama_siswa').hide();
			$('.btn-open1').show();
		}
	}

	function cari_sekolah(ini){
		var jenjang = $('input[name=jenjang]:checked').val();
		var cari = ini.value;
		if(cari==''){
			$('.tempat_sekolah').html('');
		}else{
			$.post('{{route('bunda-imunisasi-cari_sekolah')}}',{jenjang:jenjang,cari:cari},function(data){
				var html = '';
				if(data.length>0){
					$.each(data,function(k,v){
						html += '<a href="javascript:void(0)" onclick="set_sekolah(\''+v.npsn+'\',\''+(btoa(v.nama))+'\',\''+v.kelas+'\',\'kunci\')">'+v.nama+' ('+v.npsn+')</a><br>';
					})
				}
				$('.tempat_sekolah').html(html);
			});
		}
	}

	function set_sekolah(npsn,nama,kelas,kunci){
		$('input[name=npsn]').val(npsn);
		if(nama==''){
			$('.npsn').html(atob(nama));
			$('select[name=kelas]').html('');
		}else{
			$('.npsn').html(atob(nama)+' ('+npsn+')');
			var new_kelas = JSON.parse(atob(kelas));
			var html = '<option value="">Pilih kelas</option>';
			$.each(new_kelas,function(k,v){
				html += '<option data-kelas="'+v.kelas+'" data-rombel="'+v.rombel+'">'+v.kelas+' - '+v.rombel+'</option>';
			});
			$('select[name=kelas]').html(html);
		}
		$('.nama').val('');
		$('.tempat_sekolah').html('');
		set_kunci(kunci);
	}

	function tampilkan(){
		var jenjang = $('input[name=jenjang]:checked').val();
		var npsn = $('input[name=npsn]').val();
		var kelas = $('select[name=kelas] option:selected').data('kelas');
		var rombel = $('select[name=kelas] option:selected').data('rombel');
		
		if(npsn==''){
			swal('Whooops','Cari sekolahnya dulu','warning');
		}else{
			$.post('{{route('bunda-imunisasi-get_siswa')}}',{jenjang:jenjang,npsn:npsn,kelas:kelas,rombel:rombel},function(data){
				$('.wadah_siswa').html(data.content);
			});
		}
	}

	function show(nik){
		var jenjang = $('input[name=jenjang]:checked').val();
		var npsn = $('input[name=npsn]').val();
		var kelas = $('select[name=kelas] option:selected').data('kelas');
		var rombel = $('select[name=kelas] option:selected').data('rombel');

		$.post('{{route('bunda-imunisasi-tampilkan')}}',{jenjang:jenjang,npsn:npsn,kelas:kelas,rombel:rombel,nik:nik},function(data){
			$('.modal-page').html(data.content);
		});
	}
</script>
@endsection
