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
	<h2 class="content-heading" style="padding-top: 0px">Hitung Monitoring MPLS Tahun {{date('Y')}}</h2>
	<!-- Dynamic Table Full -->
	<div class="block">
		<div class="block-header block-header-default row no-margin">
			<h3 class="block-title">Hitung Monitoring MPLS Tahun {{date('Y')}}</h3>
		</div>
		<div class="block-content block-content-full">
			@include('layout.alert')
			<div class="row">
				<div class="col-lg-12" style="overflow: auto">
					<div>
						<center>
							<h1 class="judul" style="display:none;">Hitung Monitoring MPLS Tahun {{date('Y')}}</h1>
						</center>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-lg-12" style="overflow: auto">
					@php
					$form_tipe = ['A','B','C','C1','C2','C3','C4','C5','D','E','F','G','H'];
					@endphp
					<table class="table table-bordered">
						<tbody>
							<tr>
								<th colspan="3">A IDENTITAS</th>
							</tr>
							<tr>
								<td colspan="2">
									Cari Satuan Pendidikan (Jenjang {{strtoupper($jenjang)}})
								</td>
								<td>
									<input type="hidden" name="npsn">
									<input type="text" name="" class="nama form-control" placeholder="Cari satuan pendidikan" onkeyup="cari_sekolah('{{$jenjang}}',this)">
									<a href="javascript:void(0)" class="btn-open btn btn-sm btn-primary" onclick="set_sekolah('','','','','','')">Ganti Sekolah</a>
									<div class="tempat_sekolah"></div>
								</td>
							</tr>
							<tr>
								<td style="width: 5%">1</td>
								<td style="width: 45%">Nama Satuan (NPSN)</td>
								<td style="width: 50%;">
									<span class="npsn"></span>
								</td>
							</tr>
							<tr>
								<td>2</td>
								<td>Kelurahan</td>
								<td>
									<span class="kelurahan"></span>
								</td>
							</tr>
							<tr>
								<td>3</td>
								<td>Kecamatan</td>
								<td>
									<span class="kecamatan"></span>
								</td>
							</tr>
							<tr>
								<td>4</td>
								<td>Nama KS</td>
								<td>
									<span class="ks"></span>
								</td>
							</tr>
							<tr class="baris_cetak">
								<td colspan="3"><a href="javascript:void(0)" onclick="cetak()" class="btn btn-success">Cetak</a></td>
							</tr>
						</tbody>
					</table>

					<div class="wadah">
						@for($i=0;$i<count($form_tipe);$i++)
						<div class="panel_heading panel_heading_{{$form_tipe[$i]}}" style="padding: 10px;width: 100%;border: 1px solid #000;cursor: pointer;" onclick="tampilkan('{{$form_tipe[$i]}}')">Form {{$form_tipe[$i]}}</div>
						<div class="panel_body panel_body_{{$form_tipe[$i]}}">
							<form class="pengisian_{{$form_tipe[$i]}}">
								<table class="table table-bordered">
									<tbody class="form_{{$form_tipe[$i]}}">
										<tr>
											<td>Pilih Sekolah dulu</td>
										</tr>
									</tbody>
								</table>
							</form>
						</div>
						@endfor
					</div>
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
		$('#sidebar-monitoring-mpls').addClass('active');
		set_sekolah('','','','','','');
		tampilkan('panel_body_A');
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

	function cari_sekolah(jenjang,ini){
		var cari = ini.value;
		if(cari==''){
			$('.tempat_sekolah').html('');
		}else{
			$.post('{{route('bunda-monitoring-mpls-cari_sekolah')}}',{jenjang:jenjang,cari:cari},function(data){
				var html = '';
				if(data.length>0){
					$.each(data,function(k,v){
						html += '<a href="javascript:void(0)" onclick="set_sekolah(\''+v.npsn+'\',\''+(btoa(v.nama))+'\',\''+(btoa(v.ks))+'\',\''+v.kelurahan+'\',\''+v.kecamatan+'\',\'kunci\')">'+v.nama+' ('+v.npsn+')</a><br>';
					})
				}
				$('.tempat_sekolah').html(html);
			});
		}
	}

	function set_sekolah(npsn,nama,ks,kelurahan,kecamatan,kunci){
		$('input[name=npsn]').val(npsn);
		if(nama==''){
			$('.npsn').html(atob(nama));
			$('.baris_cetak').hide();
		}else{
			$('.npsn').html(atob(nama)+' ('+npsn+')');
			$('.baris_cetak').show();
		}
		$('.nama').val('');
		$('.ks').html(atob(ks));
		$('.kelurahan').html(kelurahan);
		$('.kecamatan').html(kecamatan);
		$('.tempat_sekolah').html('');
		set_kunci(kunci);
		if(kunci!=''){
			@for($i=0;$i<count($form_tipe);$i++)
			form_identitas(npsn,'{{$jenjang}}','{{$form_tipe[$i]}}');
			@endfor
		}
	}

	function form_identitas(npsn,jenjang,jenis_form){
		var data = {
			npsn:npsn,
			jenjang:jenjang,
			jenis_form:jenis_form,
		};
		$.post('{{route('bunda-monitoring-mpls-form_identitas')}}',data,function(data){
			$('.form_'+jenis_form).html(data.content);
		});
	}

	function simpan(form_pengisian){
		var npsn = $('input[name=npsn]').val();
		if(npsn!=''){
			var data = new FormData($('form.pengisian_'+form_pengisian)[0]);
			$.ajax({
				url: '{{route('bunda-monitoring-mpls-simpan')}}',
				data: data,
				type: 'POST',
				contentType: false,
				processData: false,
				success: function(data){
					if(data.code==200){
						swal('Success','Berhasil disimpan','success');
					}else{
						swal('Whooops','Gagal disimpan','error');
					}
				}
			});
		}else{
			swal('Whooops','Pilih sekolah dulu','warning');
		}
	}

	function tampilkan(panel_body){
		$('.panel_body').hide();
		$('.panel_heading').css('background','#ccc');
		$('.panel_body_'+panel_body).show();
		$('.panel_heading_'+panel_body).css('background','#ffc');
	}

	function cetak(){
		var npsn = $('input[name=npsn]').val();
		var param = {
			user_id: '{{Auth::getUser()->id}}',
			npsn: npsn,
			jenjang: '{{$jenjang}}',
		};

		param = btoa(JSON.stringify(param));
		var url = '{{url('cetak_monitoring_mpls')}}?param='+param;
		window.open(url,'new_tab');
	}
</script>
@endsection
