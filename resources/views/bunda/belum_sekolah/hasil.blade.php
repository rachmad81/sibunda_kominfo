<table style="width: 100%">
	<tr>
		<td>Hasil pencarian pada data daftar anak belum sekolah</td>
		<td>Hasil pencarian pada data PAUD</td>
		<td>Hasil pencarian pada data SD</td>
	</tr>
	<tr>
		<td>
			<table style="width: 100%;">
				@foreach($anak_belum_sekolah as $k=>$v)
				<tr>
					<td colspan="2" style="background: #ccc">Data {{($k+1)}}</td>
				</tr>
				@foreach($v as $k1=>$v1)
				<tr>
					<td>{{$k1}}</td>
					<td>: {{$v1}}</td>
				</tr>
				@endforeach
				<tr>
					<td colspan="2"><a href="javascript:void(0)" class="btn btn-primary form-control" onclick="pakai_data_ini('{!! base64_encode(json_encode($v)) !!}')">Pakai Data Ini</a></td>
				</tr>
				@endforeach
			</table>
		</td>
		<td>
			<table style="width: 100%;">
				@foreach($data_paud as $k=>$v)
				<tr>
					<td colspan="2" style="background: #ccc">Data {{($k+1)}}</td>
				</tr>
				@foreach($v as $k1=>$v1)
				<tr>
					<td>{{$k1}}</td>
					<td>: {{$v1}}</td>
				</tr>
				@endforeach
				<tr>
					<td colspan="2"><a href="javascript:void(0)" class="btn btn-primary form-control" onclick="pakai_data_ini('{!! base64_encode(json_encode($v)) !!}')">Pakai Data Ini</a></td>
				</tr>
				@endforeach
			</table>
		</td>
		<td>
			<table style="width: 100%;">
				@foreach($data_sd as $k=>$v)
				<tr>
					<td colspan="2" style="background: #ccc">Data {{($k+1)}}</td>
				</tr>
				@foreach($v as $k1=>$v1)
				<tr>
					<td>{{$k1}}</td>
					<td>: {{$v1}}</td>
				</tr>
				@endforeach
				<tr>
					<td colspan="2"><a href="javascript:void(0)" class="btn btn-primary form-control" onclick="pakai_data_ini('{!! base64_encode(json_encode($v)) !!}')">Pakai Data Ini</a></td>
				</tr>
				@endforeach
			</table>
		</td>
	</tr>
</table>