<tr>
	<td>{{$peserta->no}}</td>
	<td>{{$peserta->nama}}</td>
	<td>{{($peserta->kec_instansi==null) ? 'Kelurahan' : 'Kecamatan'}}</td>
	<td>{{($peserta->kec_instansi==null) ? $peserta->kel_instansi : $peserta->kec_instansi}}</td>
	<td>{{$peserta->identitas}}</td>
	@foreach($penilai as $p)
	<td>{{$p->jumlah_utama_selesai}}</td>
	@endforeach
	@foreach($penilai as $p)
	<td>{{$p->jumlah_pendukung_selesai}}</td>
	@endforeach
	@php
	$total_nilai = 0;
	@endphp
	@foreach($penilai as $p)
	<td>{{($p->jumlah_utama_selesai*4)+($p->jumlah_pendukung_selesai*3)}}</td>
	@php
	$total_nilai += (($p->jumlah_utama_selesai*4)+($p->jumlah_pendukung_selesai*3));
	@endphp
	@endforeach
	<td>{{$total_nilai}}</td>
</tr>