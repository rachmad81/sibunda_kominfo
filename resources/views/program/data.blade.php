@if($data->count()!=0)
@foreach($data as $k=>$v)
<tr>
	<td style="text-align: left">{{($k+1)}}</td>
	<td style="text-align: left">{{$v->bidang}}</td>
	<td style="text-align: left">{{$v->nama_kegiatan}}</td>
	<td style="text-align: left">{{$v->sasaran}}</td>
	<td style="text-align: left">{{$v->jumlah_sasaran}}</td>
	<td style="text-align: left">{{$v->tujuan}}</td>
	@for($i=1;$i<=12;$i++)
	<?php
		$waktu = explode(',', $v->waktu);
		if(in_array($i,$waktu)){
			$bg = 'background: #04F5E1 !important';
		}else{
			$bg = '';
		}
	?>
	<td style="{{$bg}}"></td>
	@endfor
	<td style="white-space: nowrap;" class="aksi">
		<?php
			$button = '<button class="btn btn-sm btn-outline-warning" onclick="formula(\''.$v->id_program.'\')">Ubah</button>';
			$button .= '<button class="btn btn-sm btn-outline-danger" onclick="hapus(\''.$v->id_program.'\')">Hapus</button>';

			echo $button;
		?>
	</td>
</tr>
@endforeach
@else
<tr>
	<td colspan="18" style="text-align: center;">..:: Data Belum Ada ::..</td>
</tr>
@endif