@if($judul!='')
<tr>
	<th colspan="3">{{$judul}}</th>
</tr>
@endif

@php
$pertanyaan_identitas = collect($pertanyaan_identitas)->sortBy('nomor');
@endphp

@foreach($pertanyaan_identitas as $i)
<tr>
	<td style="width: 5%">{{$i->nomor}}</td>
	<td style="width: 45%">{!! $i->pertanyaan !!}</td>
	<td style="width: 50%">
		<input type="hidden" name="sekolah" value="{{$npsn}}">
		<input type="hidden" name="jenis_form" value="{{$jenis_form}}">
		@php
		$html = '';
		switch ($i->tipe_jawaban) {
			case 'text':
				$value = (isset($jawaban['isian_'.$i->id])) ? $jawaban['isian_'.$i->id] : '';
				$html = '<input name="isian_'.$i->id.'" type="text" class="form-control" placeholder="jawaban anda" value="'.$value.'">';
				break;
			case 'date':
				$value = (isset($jawaban['isian_'.$i->id])) ? $jawaban['isian_'.$i->id] : '';
				$html = '<input name="isian_'.$i->id.'" type="date" class="form-control" placeholder="jawaban anda" value="'.$value.'">';
				break;
			case 'dateinterval':
				$value = (isset($jawaban['mulai_'.$i->id])) ? $jawaban['mulai_'.$i->id] : '';
				$value1 = (isset($jawaban['akhir_'.$i->id])) ? $jawaban['akhir_'.$i->id] : '';
				$html = '<input name="mulai_'.$i->id.'" type="date" class="form-control" placeholder="jawaban anda" value="'.$value.'">';
				$html .= 'hingga';
				$html .= '<input name="akhir_'.$i->id.'" type="date" class="form-control" placeholder="jawaban anda" value="'.$value1.'">';
				break;
			case 'radio':
				$pilihan_jawaban = explode(';;;', $i->list_jawaban);
				for($j=0;$j<count($pilihan_jawaban);$j++){
					$selected = '';
					if(isset($jawaban['isian_'.$i->id]) && $jawaban['isian_'.$i->id]==$pilihan_jawaban[$j]){
						$selected = 'checked';
					}
					$html .= '<label style="margin-right: 15px"><input '.$selected.' name="isian_'.$i->id.'" type="radio" class=""value="'.$pilihan_jawaban[$j].'"> '.$pilihan_jawaban[$j].'</label>';
				}
				$value = (isset($jawaban['isian_catatan_'.$i->id])) ? $jawaban['isian_catatan_'.$i->id] : '';
				$html .= '<br><input name="isian_catatan_'.$i->id.'" type="text" placeholder="Catatan/Lainnya" class="form-control" value="'.$value.'">';
				break;
			case 'label':
				$html = '';
				break;
			case 'textarea':
				$value = (isset($jawaban['isian_'.$i->id])) ? $jawaban['isian_'.$i->id] : '';
				$html = '<textarea name="isian_'.$i->id.'" class="form-control" placeholder="jawaban anda">'.$value.'</textarea>';
				break;
			
			default:
				$html = '';
				break;
		}
		@endphp
		{!! $html !!}
	</td>
</tr>
@endforeach
<tr>
	<td colspan="3"><a href="javascript:void(0)" class="btn btn-primary" onclick="simpan('{{$jenis_form}}')">Simpan</a></td>
</tr>