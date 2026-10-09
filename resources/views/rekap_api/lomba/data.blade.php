<tr>
	<td>{{$d->nama_peserta}}</td>
	<td>{{$d->kec_instansi}}</td>
	<td>{{$d->kel_instansi}}</td>
	<td>{{$d->no_sk}}</td>
	<td>'{{$d->telp_peserta}}</td>
	<td>{{$d->instansi}}</td>
	<td>{{$d->alamat_instansi}}</td>
	<td>'{{$d->telp_pengusul}}</td>
	<td>{{$d->email_pengusul}}</td>

	<td><a href="{{$d->surat_pengusulan}}">{{$d->surat_pengusulan}}</a></td>
	<td><a href="{{$d->sk_penetapan}}">{{$d->sk_penetapan}}</a></td>
	<td><a href="{{$d->program}}">{{$d->program}}</a></td>
	<td><a href="{{$d->struktur_organisasi}}">{{$d->struktur_organisasi}}</a></td>
	<td><a href="{{$d->profil_bunda}}">{{$d->profil_bunda}}</a></td>
	<td><a href="{{$d->profil_perkembangan}}">{{$d->profil_perkembangan}}</a></td>

	@foreach($utama as $u)
	<td>
		@php
		$kolom = $u['name'];
		$arr = json_decode($d->$kolom);
		$arr = collect($arr)->sortBy('number');
		@endphp
		@foreach($arr as $a)
		Penilaian {{$u['number']}} {{$a->number}}<br>
		@php
		$baris_keg = collect($kegiatan)->where('id_kegiatan',$a->id_kegiatan)->first();
		@endphp
		@if(!empty($baris_keg))
		Nama Kegiatan = <b>{{$baris_keg->nama_kegiatan}}</b><br>
		<a href="{{$a->file}}">{{$a->file}}</a><br><br>
		@endif
		@endforeach
	</td>
	@endforeach

	@foreach($pendukung as $p)
	<td>
		@php
		$kolom = $p['name'];
		$arr = json_decode($d->$kolom);
		$arr = collect($arr)->sortBy('number');
		@endphp
		@foreach($arr as $a)
		Penilaian {{$p['number']}} {{$a->number}}<br>
		@if($p['number']=='A')
		@php
		$baris_keg = collect($mitra)->where('id_mitra',$a->id_kegiatan)->first();
		@endphp
		@if(!empty($baris_keg))
		Nama Mitra = <b>{{$baris_keg->nama_mitra}}</b><br>
		<a href="{{$a->file}}">{{$a->file}}</a><br><br>
		@endif
		@else
		@php
		$baris_keg = collect($kegiatan)->where('id_kegiatan',$a->id_kegiatan)->first();
		@endphp
		@if(!empty($baris_keg))
		Nama Kegiatan = <b>{{$baris_keg->nama_kegiatan}}</b><br>
		<a href="{{$a->file}}">{{$a->file}}</a><br><br>
		@endif
		@endif
		@endforeach
	</td>
	@endforeach
</tr>