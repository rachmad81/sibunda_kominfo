<!-- Modal -->
<div id="modal_profil" class="modal fade" role="dialog">
	<div class="modal-dialog modal-md">

		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Detail Kegiatan</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="table-responsive">
						<table style="border-collapse: collapse;width: 100%;" border="1">
							<thead>
								<tr>
									<th colspan="2">Detail Kegiatan</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td>Nama Program</td>
									<td>{{$kegiatan->nama_program}}</td>
								</tr>
								<tr>
									<td>Nama Kegiatan</td>
									<td>{{$kegiatan->nama_kegiatan}}</td>
								</tr>
								<tr>
									<td>Tgl Kegiatan</td>
									<td>{{date('d-m-Y',strtotime($kegiatan->tgl_kegiatan))}}</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
				<br>
				<div class="row">
					<div class="table-responsive">
						<table style="border-collapse: collapse;width: 100%;" border="1">
							<thead>
								<tr>
									<th colspan="2">Laporan Kegiatan</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td>Foto cover</td>
									<td>
										@php
										$foto_exists = 0;
										$url = asset('logo/image-icon.png');
										if($kegiatan->foto!=''){
											if(Storage::exists($kegiatan->foto)){
												$url = route('storage',[base64_encode($kegiatan->foto),'1']);
												$foto_exists = 1;
											}
										}
										@endphp
										<a href="{{$url}}" target="_blank"><img src="{{$url}}" style="width:5cm;" alt="Cover foto {{$kegiatan->nama_kegiatan}}"></a>
									</td>
								</tr>
								<tr>
									<td>Url video</td>
									<td>
										@if($kegiatan->url_video!='')
										<a href="{{$url}}" target="_blank" class="btn btn-primary">Lihat</a>
										@else
										Belum/memang tidak ada video untuk laporan.
										@endif
									</td>
								</tr>
								@php
								$file_pendukung = json_decode($kegiatan->file_pendukung);
								$arr = ['kak','undangan','notulen','absensi','foto_geo'];
								$judul = ['KAK','Undangan','Notulen','Absensi','Foto Geotag'];
								@endphp
								@for($i=0;$i<count($arr);$i++)
								<tr>
									<td>{{$judul[$i]}}</td>
									<td>
										@php
										$kolom = $arr[$i];
										@endphp
										@if(!empty($file_pendukung) && $file_pendukung->$kolom!='')
										<a href="{{$file_pendukung->$kolom}}" target="_blank" class="btn btn-sm btn-primary">Lihat</a>
										@else
										Belum/memang tidak ada berkas untuk laporan.
										@endif
									</td>
								</tr>
								@endfor
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
			</div>
		</div>

	</div>
</div>
<style type="text/css">
	label {
		font-weight: 10;
	}

	table tr td,th{
		text-align: left;
		vertical-align: top;
		padding: 5px;
	}

	.modal-lg {
		max-width: 1200px !important;
	}
</style>
<script type="text/javascript">
	$(document).ready(function(){
		$('#modal_profil').modal();
	});
</script>