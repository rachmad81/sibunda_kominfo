<!-- Modal -->
<div id="modal_riwayat" class="modal fade" role="dialog" style="background: rgba(0,0,0,0.7)">
	<div class="modal-dialog modal-lg">

		<!-- Modal content-->
		<div class="modal-content" style="background: #fff !important">
			<div class="modal-header" style="background: #71a3ba">
				<h4 class="modal-title">Riwayat Catatan</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<table>
					<tr>
						<td style="text-align: left;vertical-align: top;padding: 5px;width: 40%;">
							<b style="font-size: 25pt">Instrumen</b>
						</td>
						<td style="text-align: left;vertical-align: top;padding: 5px;width: 60%;">
							<b>{!! $instrumen->nama_kategori !!}</b>
							<br>
							{!! $instrumen->nama !!}
						</td>
					</tr>
				</table>
				<br>
				<table style="width: 100%;border-collapse: collapse;" border="1">
					<tr>
						<th style="text-align: left;vertical-align: top">Catatan</th>
						<th style="text-align: left;vertical-align: top">Pemberi Catatan</th>
						<th style="text-align: left;vertical-align: top">Tanggal Catatan</th>
					</tr>
					@foreach($catatan as $c)
					<tr>
						<td style="text-align: left;vertical-align: top">{{$c->komentar}}</td>
						<td style="text-align: left;vertical-align: top">{{$c->komentator}}</td>
						<td style="text-align: left;vertical-align: top">{{date('d-m-Y H:i:s',strtotime($c->created_at))}}</td>
					</tr>
					@endforeach
				</table>
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
</style>
<script type="text/javascript">
	$(document).ready(function(){
		$('#modal_riwayat').modal('show');
	});
</script>