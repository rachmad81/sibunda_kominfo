<tr>
	<td>{{$peserta->no}}</td>
	<td>{{$peserta->nama}}</td>
	<td>{{($peserta->kec_instansi==null) ? 'Kelurahan' : 'Kecamatan'}}</td>
	<td>{{($peserta->kec_instansi==null) ? $peserta->kel_instansi : $peserta->kec_instansi}}</td>
	<td>{{$peserta->identitas}}</td>
	@php
	$total_nilai = 0;
	$tahap1 = 0;
	$tahap2 = 0;
	@endphp
	@foreach($penilai as $p)
	<td>{{($p->jumlah_utama_selesai*4)+($p->jumlah_pendukung_selesai*3)}}</td>
	<td>{{($p->jumlah_tahap2_selesai*10)}}</td>
	@php
	$tahap1 += ($p->jumlah_utama_selesai*4)+($p->jumlah_pendukung_selesai*3);
	$tahap2 += ($p->jumlah_tahap2_selesai*10);
	@endphp
	@endforeach
	<td>{{$tahap1}}</td>
	<td>{{$tahap2}}</td>
	@php
	$total_nilai = number_format(((40/100)*$tahap1)+((60/100)*$tahap2),2,'.','');
	@endphp
	<td>{{$total_nilai}}</td>
</tr>