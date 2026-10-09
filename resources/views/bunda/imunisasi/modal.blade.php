<!-- Modal -->
<div id="modal_presensi" class="modal fade" role="dialog">
	<div class="modal-dialog modal-lg">

		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Riwayat Imunisasi {{$s->nama}} | {{$s->sekolah}} | {{$s->kelas}} | {{$s->rombel}}</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<div class="col-lg-12" style="overflow: auto">
					Riwayat Imunisasi
					<table style="width: 100%;border-collapse: collapse;" border="1">
						<thead>
							@if($s->imunisasi!='')
							@php
							$data_imunisasi = $s->imunisasi;
							$temp = 0;
							foreach($data_imunisasi as $k=>$d){
								if($k>0){
									if(count($data_imunisasi[$k])>=count($data_imunisasi[($k-1)])){
										$temp = $k;
									}else{
										$temp;
									}
								}else{
									$temp = 0;
								}
							}
							$key = collect($data_imunisasi[$temp])->keys();
							@endphp
							<tr>
								@foreach($key as $k)
								<th>{{ucwords($k)}}</th>
								@endforeach
							</tr>
							@else
							<tr>
								<th>Tanggal</th>
								<th>Jenis imunisasi</th>
								<th>Imunisasi</th>
								<th>Imunisasi ke</th>
							</tr>
							@endif
						</thead>
						<tbody>
							@if($s->imunisasi!='')
							@foreach($data_imunisasi as $i)
							<tr>
								@foreach($key as $k)
								<td>{{(isset($i[$k])) ? $i[$k] : ''}}</td>
								@endforeach
							</tr>
							@endforeach
							@else
							<tr>
								<td colspan="4">Belum pernah imuniasasi</td>
							</tr>
							@endif
						</tbody>
					</table>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
			</div>
		</div>

	</div>
</div>
<style type="text/css">
	label{
		font-weight: 10;
	}
	table tr td,th{
		padding: 5px;
		text-align: left !important;
	}
</style>
<script type="text/javascript">
	$(document).ready(function(){
		$('#modal_presensi').modal({backdrop: 'static'});
	});
</script>