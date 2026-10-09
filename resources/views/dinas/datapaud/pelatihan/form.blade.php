<!-- Modal -->
<div id="modal_tembusan" class="modal fade" role="dialog">
	<div class="modal-dialog">

		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Pelatihan {{$wisata->nama}}</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<form id="form_opd">
					<input type="hidden" name="id" value="{{$wisata->id}}">
					<table style="width: 100%;border-collapse: collapse;" border="1">
						<tr>
							<td>Nama</td>
							<td>
								<textarea name="nama" style="width: 100%">{{$wisata->nama}}</textarea>
							</td>
						</tr>
						<tr>
							<td>Deskripsi</td>
							<td>
								<textarea name="deskripsi" style="width: 100%">{{$wisata->deskripsi}}</textarea>
							</td>
						</tr>
						<tr>
							<td>OPD</td>
							<td>
								<table style="width: 100%">
									@foreach($opd as $o)
									<tr>
										<td><input type="checkbox" @if(in_array($o->id,$id_opd)) checked @endif id="{{$o->id}}" name="opd[]" value="{{$o->id}}"></td>
										<td>
											<label for="{{$o->id}}" style="margin-left: 5px">{{$o->nama}}</label>
										</td>
									</tr>
									@endforeach
								</table>
							</td>
						</tr>
					</table>
				</form>
			</div>
			<div class="modal-footer">
				<a href="javascript:void(0)" onclick="simpan()" class="btn btn-sm btn-primary">Simpan</a>
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
		</div>

	</div>
</div>
<style type="text/css">
	table tr td{
		vertical-align: top;
	}
</style>
<script type="text/javascript">
	$(document).ready(function(){
		$('#modal_tembusan').modal('show');
	});

	function simpan(){
		var data = $('form#form_opd').serialize(0);
		$.post("{{route('dinas-data_paud-pelatihan-simpan')}}",data,function(data){
			if(data.code=='200'){
				swal('Success','Berhasil disimpan','success');
				get_data();
				$('.modal-backdrop').removeClass('modal-backdrop');
				$('.page-modal').html('');
			}else{
				swal('Success','Gagal disimpan','error');
			}
		})
	}
</script>