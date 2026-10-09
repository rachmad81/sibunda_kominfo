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
	th,td{
		vertical-align: top;
	}
</style>
<body>
	Rekap Penilaian Tahap 2 Lomba Apresiasi Tahun {{$tahun}}
	<div class="loading">Sedang Menghitung ........... </div>

	<div>
		<table style="border-collapse: collapse;" border="1">
			<thead>
				<tr>
					<th rowspan="2">No</th>
					<th rowspan="2">Nama</th>
					<th rowspan="2">Tingkat</th>
					<th rowspan="2">Kecamatan/Kelurahan</th>
					<th rowspan="2">Identitas</th>
					<th colspan="{{collect($penilai)->count()}}">Tahap2</th>
					<th rowspan="2">Total</th>
				</tr>
				<tr>
					@foreach($penilai as $p)
					<th>{{$p->nama}}</th>
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
				$.post('{{url('api/rekap/nilai2/nilai-data')}}',{user_id:user_id[i],tahun:'{{$tahun}}',i:i},function(data){

					$('.tempat-data').append(data.content);

					get_data(i+1);
				}).fail(function(){
					console.log('User error = '+user_id[i]);
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