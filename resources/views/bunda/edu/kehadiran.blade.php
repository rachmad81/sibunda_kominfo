<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>
		Kehadiran Peserta Didik (Edu City Tour)
	</title>
</head>
<body>
	<div style="width:100%;text-align: center;">
		<h3>
			Surat Kehadiran Peserta Didik<br>
			Edu City Tour {!! $jadwal->nama_tempat !!} {!! date('d-m-Y',strtotime($jadwal->tanggal)) !!}<br>
			{!! $sekolah->npsn !!} - {!! $sekolah->nama !!}
		</h3>
	</div>
	<div style="width: 100%">
		<table style="width: 100%;border-collapse: collapse;" border="1" id="badan">
			<tr>
				<th>No</th>
				<th>Nama</th>
				<th>Kehadiran</th>
			</tr>
			@foreach($siswa as $k=>$s)
			<tr>
				<td style="text-align: center">{!! ($k+1) !!}</td>
				<td>{!! $s->nama !!}</td>
				<td style="text-align: center">{!! ($k+1) !!}. ..............</td>
			</tr>
			@endforeach
		</table>
	</div>
	<br>
	<div style="width: 100%;">
		<table style="width: 100%">
			<tr>
				<td style="width: 30%">&nbsp;</td>
				<td style="width: 30%">&nbsp;</td>
				<td style="text-align: center">
					<b>Surabaya, {!! date('d-m-Y',strtotime($jadwal->tanggal)) !!}</b>
					<br>
					Kepala Sekolah
					<br>
					<br>
					<br>
					<br>
					<br>
					<b><u>{!! $sekolah->kepala !!}</u></b>
				</td>
			</tr>
		</table>
	</div>
</body>
<style type="text/css">
	table#badan tr td{
		padding: 10px 0px 10px 0px;
	}
</style>
</html>