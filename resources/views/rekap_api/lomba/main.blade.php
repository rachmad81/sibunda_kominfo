<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>
		Data Peserta Lomba Apresiasi
	</title>
</head>
<style type="text/css">
	td{
		vertical-align: top;
	}
</style>
<body>
	Rekap Peserta Lomba Apresiasi Tahun {{$tahun}}
	<div class="loading">Sedang Menghitung ........... </div>

	<div>
		<table style="border-collapse: collapse;" border="1">
			<thead>
				<tr>
					<th colspan="9">Identitas</th>
					<th colspan="6">Berkas</th>
					<th colspan="{{count($utama)}}">Kriteria Utama</th>
					<th colspan="{{count($pendukung)}}">Kriteria Pendukung</th>
				</tr>
				<tr>
					<th>Nama</th>
					<th>Kecamatan</th>
					<th>Kelurahan</th>
					<th>NO SK</th>
					<th>Telp Peserta</th>
					<th>Instansi</th>
					<th>Alamat instansi</th>
					<th>Telp pengusul</th>
					<th>Email pengusul</th>

					<th>Surat Pengusulan</th>
					<th>sk penetapan</th>
					<th>program</th>
					<th>struktur organisasi</th>
					<th>profil bunda</th>
					<th>profil perkembangan</th>

					@foreach($utama as $u)
					<th>{{$u['number']}} {{-- Inovasi --}}</th>
					@endforeach

					@foreach($pendukung as $p)
					<th>{{$p['number']}} {{-- Inovasi --}}</th>
					@endforeach
				</tr>
			</thead>
			<tbody class="tempat-data">
			</tbody>
		</table>		
	</div>

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
	<script type="text/javascript">
		var user_id = [];
		var error = 0;
		var persen = 0;

		$(document).ready(function(){
			@foreach($user as $u)
			user_id.push('{{$u->user_id}}')
			@endforeach

			get_data(0);
		});

		function get_data(i){
			persen = (((i+1)/user_id.length)*100).toFixed(2);
			$('.loading').html(persen);
			if((i+1)<=user_id.length){
				$.post('{{url('api/rekap/lomba/lomba-data')}}',{user_id:user_id[i],tahun:'{{$tahun}}'},function(data){

					$('.tempat-data').append(data.content);

					get_data(i+1);
				}).fail(function(){
					if(error>=5){
						get_data(i+1);
						error=0;
					}else{
						get_data(i);
						error++;
					}
				});
			}else{
				persen = ((i/user_id.length)*100).toFixed(2);
				$('.loading').html(persen+' Selesai');
			}
		}
	</script>
</body>
</html>