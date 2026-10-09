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
								<th>NIK</th>
								<th>Nama</th>
								<th>Status</th>
								<th>Pendidikan</th>
							</tr>
						</thead>
						<tbody>
							@foreach($lembaga as $k=>$l)
							<tr>
								<td>{{($k+1)}}</td>
								<td>{{$l->no_ktp}}</td>
								<td>{{$l->nama}}</td>
								<td>{{$l->status_guru}}</td>
								<td>{{$l->pendidikan}}</td>
							</tr>
							@endforeach
						</tbody>
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
	})
</script>