<tr>
	<td>{{$peserta->no}}</td>
	<td>{{$peserta->nama}}</td>
	<td>{{($peserta->kec_instansi==null) ? 'Kelurahan' : 'Kecamatan'}}</td>
	<td>{{($peserta->kec_instansi==null) ? $peserta->kel_instansi : $peserta->kec_instansi}}</td>
	<td>{{$peserta->identitas}}</td>
	@php
	$total_nilai = 0;
	@endphp
	@foreach($penilai as $p)
	<td>{{$p->jumlah_utama_selesai}}</td>
	@php
	$total_nilai += $p->jumlah_utama_selesai;
	@endphp
	@endforeach
	<td>{{($total_nilai)}}</td>
</tr>