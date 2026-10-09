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
		$disabled1 = '';
		$disabled2 = '';
		if(is_null($s->ks_id)){
			$disabled1 = 'disabled';
		}
		if(is_null($s->kr_id)){
			$disabled2 = 'disabled';
		}
		@endphp
		<a href="javascript:void(0)" class="btn btn-sm btn-primary {{$disabled2}}" onclick="show('{{$s->siswa_id}}','rumah')">Kebiasaan Rumah</a>
		<a href="javascript:void(0)" class="btn btn-sm btn-info {{$disabled1}}" onclick="show('{{$s->siswa_id}}','sekolah')">Kebiasaan Sekolah</a>
	</td>
</tr>
@endforeach
@else
<tr>
	<td colspan="4">Data Tidak Ditemukan</td>
</tr>
@endif