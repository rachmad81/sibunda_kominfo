@if($siswa->count()!=0)
@php
$no=1;
@endphp
@foreach($siswa as $s)
<tr>
	<td>{{$no++}}</td>
	<td>{{$s->nama}}</td>
	<td>{{$s->kelas}} - {{$s->rombel}}</td>
	<td>
		@php
		$data_imnunisasi = collect($imunisasi)->where('nik',$s->nik)->first();
		@endphp

		@if(!empty($data_imnunisasi))
		@php
		$temp = str_replace('""', '"', $data_imnunisasi->imunisasi);
		$temp = json_decode($temp,true);
		$temp = collect($temp)->sortBy('tanggal')->values();
		@endphp
		<ul>
			@foreach($temp as $t)
			@php
			$dosis = (empty($t['imunisasike'])) ? $t['dosis'] : $t['imunisasike'] ;
			@endphp
			<li>{{date('d-m-Y',strtotime($t['tanggal']))}} - {{$t['jenisimunisasi']}} ({{$t['imunisasi']}}) dosis {{$dosis}}</li>
			@endforeach
		</ul>
		{{-- <a href="javascript:void(0)" onclick="show('{{$s->nik}}')" class="btn btn-sm btn-primary">Lihat hasil</a> --}}
		@else
		Belum imunisasi
		@endif
	</td>
</tr>
@endforeach
@else
<tr>
	<td colspan="4">Data Tidak Ditemukan</td>
</tr>
@endif