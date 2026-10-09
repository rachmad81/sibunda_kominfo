<div class="modal fade" id="myModal" role="dialog">
	<div class="modal-dialog modal-lg">

		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">{{$judul}}</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<div class="table-responsive">
					<table class="table table-bordered" id="detailData">
						<thead>
							<tr>
								<th>No</th>
								<th>Kecamatan</th>
								<th>Kelurahan</th>
								<th>NPSN</th>
								<th>Nama Satuan Pendidikan</th>
								<th>Jumlah</th>
							</tr>
						</thead>
						<tbody>
							@php
							$no=0;
							@endphp
							@foreach($lembaga as $k=>$l)
							<tr>
								<td>{{($no+1)}}</td>
								<td>{{$l->kecamatan_dispenduk}}</td>
								<td>{{$l->kelurahan_dispenduk}}</td>
								<td>{{$l->npsn}}</td>
								<td>{{$l->nama_sekolah}}</td>
								<td>{{$l->$kolom}}</td>
							</tr>
							@php
							$no++;
							@endphp
							@endforeach
						</tbody>
						<tfoot>
							<tr>
								<th colspan="5">Jumlah</th>
								<th>{{$lembaga->sum($kolom)}}</th>
							</tr>
						</tfoot>
					</table>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
		</div>

	</div>
</div>
<script type="text/javascript">
	$(document).ready(function(){
		$('#myModal').modal();
        $('#table_gtk').DataTable();
	})
</script>