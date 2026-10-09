@foreach($data as $n=>$d)
<tr>
	<td>{{$d->no}}</td>
	<td>{{$d->nama}}</td>
	<td>{{$d->tingkat}}</td>
	<td>{{$d->kec_instansi}}</td>
	<td>{{$d->kel_instansi}}</td>
	<td>{!! $d->identitas !!}</td>
	<td>{!! $d->status_utama !!}</td>
	<td>{!! $d->status_pendukung !!}</td>
</tr>
@endforeach