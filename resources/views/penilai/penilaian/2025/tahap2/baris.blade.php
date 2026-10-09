@foreach($data as $n=>$d)
<tr>
	<td>{{($n+1)}}</td>
	<td>{{$d->nama}}</td>
	<td>{{$d->tingkat}}</td>
	<td>{{$d->kec_instansi}}</td>
	<td>{{$d->kel_instansi}}</td>
	<td>{!! $d->identitas !!}</td>
	<td>{!! $d->status_tahap1_utama !!}</td>
	<td>{!! $d->status_tahap1_pendukung !!}</td>
	<td>{!! $d->nilai_tahgap1 !!}</td>
	<td>{!! $d->nilai !!} Point</td>
	<td>{!! $d->status_tahap2 !!}</td>
</tr>
@endforeach