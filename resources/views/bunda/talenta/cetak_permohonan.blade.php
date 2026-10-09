<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Cetak Permohonan Talenta Seni</title>
</head>
<body>
	<div style="width: 100%">
		<div style="width:100%;text-align: center;">
			<p style="font-size: 80px">KOP SURAT</p>	
		</div>

		<table style="width: 100%">
			<tr style="vertcal-align: top">
				<td>
					<table style="width: 100%">
						<tr>
							<td>Nomor</td>
							<td>:</td>
							<td></td>
						</tr>
						<tr>
							<td>Sifat</td>
							<td>:</td>
							<td>Penting</td>
						</tr>
						<tr>
							<td>Lampiran</td>
							<td>:</td>
							<td></td>
						</tr>
						<tr>
							<td>Perihal</td>
							<td>:</td>
							<td>Permohonan Talenta Seni Siswa</td>
						</tr>
					</table>
				</td>
				<td>
					Surabaya,<br>
					&nbsp;&nbsp;&nbsp;Kepada<br>
					Yth. Bunda Paud Kota Suarabaya<br>
					di-<br>
					&nbsp;&nbsp;&nbsp;<b>SURABAYA</b>
				</td>
			</tr>
		</table>

		<table style="width: 100%">
			<tr style="vertcal-align: top">
				<td>
					&nbsp;&nbsp;&nbsp;Dengan hormat, bersama ini kami mohon bantuan untuk memfasilitasi kegiatan
					Talenta Seni Siswa. Adapun kegiatan tersebut akan dilaksanakan
					pada:
				</td>
			</tr>
		</table>

		<table style="width: 80%;margin-left: 5cm">
			<tr>
				<td>Lokasi</td>
				<td>:</td>
				<td>{{$tempat->nama}}</td>
			</tr>
			<tr>
				<td>Alamat</td>
				<td>:</td>
				<td>{{$tempat->alamat}}</td>
			</tr>
			<tr>
				<td>Tanggal</td>
				<td>:</td>
				<td>{{(!empty($keterangan)) ? $keterangan->tanggal : '-'}}</td>
			</tr>
			<tr>
				<td>Waktu</td>
				<td>:</td>
				<td>{{(!empty($keterangan)) ? $keterangan->mulai.' s/d '.$keterangan->selesai : '-'}}</td>
			</tr>
		</table>

		<div style="width: 80%;text-align: center;margin: auto;">
			<div style="font-weight: bold;">Data Peserta</div>
			<table style="width: 100%;border-collapse: collapse;" border="1">
				<thead>
					<tr>
						<th>Nama Sekolah</th>
						<th>Jenjang</th>
						<th>Judul Tampilan</th>
						<th>Jumlah Peserta</th>
						<th>Jumlah Pendamping</th>
					</tr>
				</thead>
				<tbody>
					@if($jadwal->count()!=0)
					@foreach($jadwal as $j)
					@php
					$baris = $peserta->where('jadwal_id',$j->id)->count();
					$baris_pendamping = $pendamping->where('jadwal_id',$j->id)->count();
					@endphp
					<tr>
						<td>{{$j->nama_sekolah}}</td>
						<td>{{$j->jenjang}}</td>
						<td>{{$j->judul}}</td>
						<td>{{$baris}}</td>
						<td>{{$baris_pendamping}}</td>
					</tr>
					@endforeach
					@endif
				</tbody>
			</table>		
		</div>
		
		<table style="width: 100%">
			<tr style="vertcal-align: top">
				<td>
					Demikian atas perhatian dan kesediaan Bapak disampaikan terima kasih.
				</td>
			</tr>
		</table>

		<br>
		<br>
		<br>
		<br>
		<div style="page-break-before: always;"></div>
		<div>
			<b style="font-size: 25pt">Daftar siswa setiap Sekolah</b><br>
			@if($jadwal->count()!=0)
			@foreach($jadwal as $j)
			@php
			$nomor=0;
			$baris = $peserta->where('jadwal_id',$j->id);
			$baris_pendamping = $pendamping->where('jadwal_id',$j->id);
			@endphp

			<table style="width: 40%;border-collapse: collapse;">
				<tr>
					<td><b>Nama Sekolah</b></td>
					<td>: {{$j->nama_sekolah}}</td>
				</tr>
				<tr>
					<td><b>NPSN</b></td>
					<td>: {{$j->npsn}}</td>
				</tr>
				<tr>
					<td><b>Judul</b></td>
					<td>: {{$j->judul}}</td>
				</tr>
				<tr>
					<td><b>Jumlah Peserta</b></td>
					<td>: {{$baris->count()}}</td>
				</tr>
				<tr>
					<td><b>Jumlah Pendamping</b></td>
					<td>: {{$baris_pendamping->count()}}</td>
				</tr>
			</table>
			
			<b style="font-size: 18pt">Daftar Peserta Talenta Seni</b>
			<table style="width: 100%;border-collapse: collapse;" border="1">
				<thead>
					<tr>
						<th>No</th>
						<th>Nama Siswa</th>
						<th>NPSN</th>
						<th>Nama Sekolah</th>
					</tr>
				</thead>
				<tbody>
					@foreach($baris as $b)
					@php
					$nomor++;
					@endphp
					<tr>
						<td>{{$nomor}}</td>
						<td>{{$b->nama}}</td>
						<td>{{$j->npsn}}</td>
						<td>{{$j->nama_sekolah}}</td>
					</tr>
					@endforeach
				</tbody>
			</table>

			<b style="font-size: 18pt">Daftar Pendamping Talenta Seni</b>
			<table style="width: 100%;border-collapse: collapse;" border="1">
				<thead>
					<tr>
						<th>No</th>
						<th>Nama Pendamping</th>
						<th>Jabatan</th>
						<th>HP</th>
						<th>NPSN</th>
						<th>Nama Sekolah</th>
					</tr>
				</thead>
				<tbody>
					@foreach($baris_pendamping as $b)
					@php
					$nomor++;
					@endphp
					<tr>
						<td>{{$nomor}}</td>
						<td>{{$b->nama}}</td>
						<td>{{$b->jabatan}}</td>
						<td>{{$b->hp}} / {{$b->hp2}}</td>
						<td>{{$j->npsn}}</td>
						<td>{{$j->nama_sekolah}}</td>
					</tr>
					@endforeach
				</tbody>
			</table>
			<div style="page-break-before: always;"></div>
			
			@endforeach
			@endif
		</div>
	</div>
</body>
</html>