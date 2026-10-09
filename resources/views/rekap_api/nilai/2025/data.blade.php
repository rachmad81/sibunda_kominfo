@php
$colspan = 5;
$colspan += count($penilai);
$colspan += count($penilai);
$colspan += count($penilai);
$nomor=1;
@endphp
<tr>
	<td colspan="{{$colspan}}"><h3>{{$kelompok}}</h3></td>
</tr>
@foreach($pesertas as $peserta)
<tr>
	<td>{{$nomor}}</td>
	<td>{{$peserta->nama}}</td>
	<td>{{($peserta->kec_instansi==null) ? 'Kelurahan' : 'Kecamatan'}}</td>
	<td>{{($peserta->kec_instansi==null) ? $peserta->kel_instansi : $peserta->kec_instansi}}</td>
	<td>{{$peserta->identitas}}</td>
	
	@php
	$nilai_utama_selesai = [];
	$nilai_pendukung_selesai = [];
	@endphp

	@foreach($penilai as $in=>$p)
	<td>
		@php
		$nilai_selesai = collect($peserta->penilai)->where('penilai_id',$p->id)->first();
		@endphp
		
		@php
		if(!empty($nilai_selesai)){
			$nilai_utama_selesai[$in] = $nilai_selesai['jumlah_utama_selesai'];
			$nilai_pendukung_selesai[$in] = $nilai_selesai['jumlah_pendukung_selesai'];
			// $nilai_utama_selesai[$in] = 0;
			// $nilai_pendukung_selesai[$in] = 0;
		}else{
			$nilai_utama_selesai[$in] = 0;
			$nilai_pendukung_selesai[$in] = 0;
		}
		@endphp
		{{$nilai_utama_selesai[$in]}}
	</td>
	@endforeach
	
	@foreach($penilai as $in=>$p)
	<td>
		{{$nilai_pendukung_selesai[$in]}}
	</td>
	@endforeach
	
	@php
	$total_nilai = 0;
	@endphp
	
	@foreach($penilai as $in=>$p)
	<td>{{($nilai_utama_selesai[$in]*4)+($nilai_pendukung_selesai[$in]*2)}}</td>
	@php
	$total_nilai += (($nilai_utama_selesai[$in]*4)+($nilai_pendukung_selesai[$in]*2));
	@endphp
	@endforeach
	
	<td>{{$total_nilai}}</td>
</tr>
@php
$nomor++;
@endphp
@endforeach