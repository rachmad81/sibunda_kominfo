<!-- Modal -->
<div id="modal_tolak" class="modal fade" role="dialog">
	<div class="modal-dialog modal-lg">

		<!-- Modal content-->
		<div class="modal-content" style="background: #fff !important">
			<div class="modal-header">
				<h4 class="modal-title">Tolak Pengajuan</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-lg-12">
						<label>Alasan Penolakan</label>
						<input type="hidden" name="id_pengajuan" value="{{$pengajuan->id}}">
						<textarea name="alasan" class="form-control" style="border: 1px solid black" placeholder="Tulisakan alasan penolakan"></textarea>
						<i style="font-size: 10pt">Minimal 15 karakter *</i>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<a href="javascript:void(0)" class="btn btn-sm btn-danger" onclick="tolak()">Tolak</a>
				<button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
			</div>
		</div>

	</div>
</div>
<style type="text/css">
	label{
		font-weight: 10;
	}
</style>
<script type="text/javascript">
	$(document).ready(function(){
		$('#modal_tolak').modal('show');
	});
</script>