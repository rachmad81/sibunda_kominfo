<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Cetak </title>
	<meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<style>
	@media print {
		.page_break {page-break-after: always;}
	}
</style>
<body>
	<div style="width: 100%">
		<h1 style="text-align: center;width: 100%">Hasil Monitoring MPLS Jenjang {{$sekolah->jenjang}}</h1>
	</div>
	<div style="width: 100%">
		@php
		$form_tipe = ['B','C','C1','C2','C3','C4','C5','D','E','F','G','H'];
		if(!empty($mpls)){
			$baris_jawaban = (array)json_decode($mpls->jawaban);
		}else{
			$baris_jawaban = [];
		}
		$jawaban_a = (isset($baris_jawaban['A'])) ? (array)$baris_jawaban['A'] : [];
		@endphp
		<table style="width: 100%;border-collapse: collapse;" border="1">
			<tbody>
				<tr>
					<th colspan="3">A. IDENTITAS</th>
				</tr>
				<tr>
					<td style="width: 2%">1</td>
					<td style="width: 55%">Nama Satuan (NPSN)</td>
					<td style="width: 43%;">
						{{$sekolah->nama}} ({{$sekolah->npsn}})
					</td>
				</tr>
				<tr>
					<td>2</td>
					<td>Kelurahan</td>
					<td>
						{{$sekolah->kelurahan}}
					</td>
				</tr>
				<tr>
					<td>3</td>
					<td>Kecamatan</td>
					<td>
						{{$sekolah->kecamatan}}
					</td>
				</tr>
				<tr>
					<td>4</td>
					<td>Nama KS</td>
					<td>
						{{$sekolah->ks}}
					</td>
				</tr>
				<tr>
					<td>5</td>
					<td>Tanggal pelaksanaan MPLS</td>
					<td>
						@if(!empty($jawaban_a))
						{{$jawaban_a['mulai_24']}} s/d {{$jawaban_a['akhir_24']}}
						@endif
					</td>
				</tr>
				<tr>
					<td>6</td>
					<td>Hari / Tanggal Monev</td>
					<td>
						@if(!empty($jawaban_a))
						{{$jawaban_a['isian_27']}}
						@endif
					</td>
				</tr>
				<tr>
					<td>7</td>
					<td>Dokumen Foto Monev</td>
					<td>
						@if(!empty($jawaban_a))
						{{$jawaban_a['isian_28']}}
						@endif
					</td>
				</tr>
				<tr>
					<td>8</td>
					<td>Dokumen Video Monev</td>
					<td>
						@if(!empty($jawaban_a))
						{{$jawaban_a['isian_29']}}
						@endif
					</td>
				</tr>
			</tbody>
		</table>

		{{-- <div class="page_break"></div> --}}
		<div class="wadah">
			@for($i=0;$i<count($form_tipe);$i++)
			<br>
			<div class="panel_body panel_body_{{$form_tipe[$i]}}">
				<table style="width: 100%;border-collapse: collapse;" border="1">
					<tbody class="form_{{$form_tipe[$i]}}">
						<tr>
							<td>Pilih Sekolah dulu</td>
						</tr>
					</tbody>
				</table>
			</div>
			{{-- <div class="page_break"></div> --}}
			@endfor
		</div>
	</div>
</body>

<script src="{{ url('assets/js/codebase.core.min.js') }}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});	
	@for($i=0;$i<count($form_tipe);$i++)
	form_identitas('{{$sekolah->npsn}}','{{$jenjang}}','{{$user->id}}','{{$form_tipe[$i]}}')
	@endfor
	function form_identitas(npsn,jenjang,user_id,jenis_form){
		var data = {
			npsn:npsn,
			jenjang:jenjang,
			jenis_form:jenis_form,
			user_id: user_id,
		};
		$.post('{{url('form_cetak_monitoring_mpls')}}',data,function(data){
			$('.form_'+jenis_form).html(data.content);
		});
	}
</script>
</html>