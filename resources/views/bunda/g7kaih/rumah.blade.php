<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Kebiasaan Di Rumah</title>
</head>
<body>
	<div style="width: 100%;margin: auto;text-align: center;">
		<h1>JURNAL REFLEKSI 7 + 2 KEBIASAAN ANAK INDONESIA HEBAT AKTIVITAS DI RUMAH</h1>
	</div>
	<div style="width: 100%;margin: auto;">
		<h3>Form {{$param['bulan']}} - {{$param['tahun']}}</h3>
	</div>
	<div style="width: 100%">
		<table style="width: 50%">
			<tr>
				<td>Nama</td>
				<td>: {{$siswa->nama}}</td>
			</tr>
			<tr>
				<td>Sekolah</td>
				<td>: {{$siswa->nama_sekolah}} ({{$siswa->npsn}})</td>
			</tr>
			<tr>
				<td>Kelas</td>
				<td>: {{$siswa->kelas}}</td>
			</tr>
			<tr>
				<td>Rombel</td>
				<td>: {{$siswa->rombel}}</td>
			</tr>
			<tr>
				<td>Tanggal Pengisian</td>
				<td>: {{date('d-m-Y',strtotime($data_kebiasaan['created_at']))}}</td>
			</tr>
		</table>
	</div>
	<div style="width: 100%">
		@php
		$nilai = ['1-7 kali','8-14 kali','15-21 kali','22-30 kali'];
		@endphp
		<table style="width: 100%;border-collapse: collapse;" border="1">
			<thead>
				<tr style="background: #ccc">
					<th>Kebiasaan</th>
					@for($i=0;$i<count($nilai);$i++)
					<th>{{$nilai[$i]}}</th>
					@endfor
					<th>
						Keterangan
					</th>
				</tr>
			</thead>
			<tbody>
				@foreach($macam_kebiasaan as $k=>$v)
				<tr style="background: #eee">
					<th>{!! $k !!}</th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
				</tr>
				@foreach($v as $key=>$val)
				<tr>
					<td>{!! $val !!}</td>
					@for($i=0;$i<count($nilai);$i++)
					@if($data_kebiasaan[$key]==$nilai[$i])
					<td style="text-align: center;">
						( V )
					</td>
					@else
					<td></td>
					@endif
					@endfor
					<td></td>
				</tr>
				@endforeach
				@endforeach
			</tbody>
		</table>
	</div>
</body>
</html>