<!-- Modal -->
<style type="text/css">
	@media (min-width: 992px){
		.modal-lg {
			max-width: 80% !important;
		}
	}
</style>
<div id="myModalIdentitas" class="modal fade" role="dialog">
	<div class="modal-dialog modal-lg">

		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">
					Detail {{$judul}}, 
					@if(!empty($berkas))
					@if($berkas->status=='Selesai')
					<small style="color: #9ccc65">dengan Status ({{$berkas->status}})</small>
					@else
					<small>dengan Status (Draft)</small>
					@endif
					@else
					<small style="color: #ff6666">dengan Status (Belum diisi)</small>
					@endif
				</h4>
				{{-- <button type="button" class="close" data-dismiss="modal">&times;</button> --}}
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-lg-12 table-responsive">
						<table style="width: 100%;border-collapse: collapse;" border="1">
							<thead>
								<tr style="background: #ccc">
									<th>No</th>
									<th>Kriteria</th>
									<th></th>
									<th></th>
								</tr>
							</thead>
							<tbody>
								@foreach($kriteria as $k)
								<tr style="background: #fffc78">
									<td>{!! $k['number'] !!}</td>
									<td>{!! $k['title'] !!}</td>
									<td></td>
									<td></td>
								</tr>
								@foreach($k['subtitle'] as $s)
								<tr style="background: #fafae1">
									<td>{!! $k['number'] !!} {!! $s['number'] !!}</td>
									<td>{!! $s['title'] !!}</td>
									<td></td>
									<td></td>
								</tr>
								@php
								$no_tambahan = 1;
								@endphp
								
								@foreach($kegiatan_apresiasi->where('sub',$k['number']) as $in=>$ka)
								@php
								$nama = $k['name'].'___'.$s['number'].'___'.$ka->id_kegiatan.'___'.$k['number'];
								$kolom = $k['name'];

								if(!empty($berkas)){
									$baris = json_decode($berkas->$kolom);
									$arr_baris = collect($baris)->where('number',$s['number'])->where('id_kegiatan',$ka->id_kegiatan)->first();
									$isi_file = (!empty($arr_baris)) ? $arr_baris->file : '';
								}else{
									$isi_file = '';
								}


								@endphp
								<tr>
									<td>{{$k['number']}}.{{$s['number']}}.{{$no_tambahan}}</td>
									<td width="25%">
										{{$ka->nama_kegiatan}}
									</td>
									<td>										
										{{$isi_file}}
									</td>
									<td width="20%">
										<a href="{{$isi_file}}" target="_blank" class="btn btn-sm btn-warning">Lihat</a>
									</td>
								</tr>
								@php
								$no_tambahan++;
								@endphp
								@endforeach
								
								@endforeach
								@endforeach
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
		</div>

	</div>
</div>
<script type="text/javascript">
	$('#myModalIdentitas').modal({backdrop: 'show'});
</script>