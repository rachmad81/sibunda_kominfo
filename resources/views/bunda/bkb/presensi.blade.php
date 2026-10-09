<!-- Modal -->
<div id="modal_presensi" class="modal fade" role="dialog">
	<div class="modal-dialog modal-lg">

		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Detail dan Presensi Bina Keluarga Balita</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<table class="table_presensi" style="width: 100%;text-align: left !important" border="1">
					<tr>
						<td colspan="4" style="background: #ccc">
							<h3>Parenting</h3>
						</td>
					</tr>
					<tr>
						<td style="width: 15%"><b>Topik</b></td>
						<td style="width: 35%">{{$parenting->topik}}</td>
						<td style="width: 15%"><b>Foto Kegiatan</b></td>
						<td style="width: 35%">
							@if($parenting->path_kegiatan!='')
							<a class="btn btn-sm btn-info" href="https://simpauddispendik.surabaya.go.id/storage/umum/{{base64_encode($parenting->path_kegiatan)}}/1" target="_blank">Lihat berkas</a>
							@else
							Belum upload berkas
							@endif
						</td>
					</tr>
					<tr>
						<td><b>Narasumber</b></td>
						<td>{{$parenting->narasumber}}</td>
						<td><b>Permohonan Narasumber</b></td>
						<td>
							@if($parenting->path_permohonan_narsum!='')
							<a class="btn btn-sm btn-info" href="https://simpauddispendik.surabaya.go.id/storage/umum/{{base64_encode($parenting->path_permohonan_narsum)}}/1" target="_blank">Lihat berkas</a>
							@else
							Belum upload berkas
							@endif
						</td>
					</tr>
					<tr>
						<td><b>Tempat</b></td>
						<td>{{$parenting->tempat}}</td>
						<td><b>Berkas Materi</b></td>
						<td>
							@if($parenting->path_materi!='')
							<a class="btn btn-sm btn-info" href="https://simpauddispendik.surabaya.go.id/storage/umum/{{base64_encode($parenting->path_materi)}}/1" target="_blank">Lihat berkas</a>
							@else
							Belum upload berkas
							@endif
						</td>
					</tr>
					<tr>
						<td><b>Tgl Pelaksanaan</b></td>
						<td>{{$parenting->hari}}, {{date('d-m-Y',strtotime($parenting->tgl_pelaksanaan))}} {{$parenting->jam_pelaksanaan}}</td>
						<td><b>Berkas Presensi</b></td>
						<td>
							@if($parenting->path_presensi!='')
							<a class="btn btn-sm btn-info" href="https://simpauddispendik.surabaya.go.id/storage/umum/{{base64_encode($parenting->path_presensi)}}/1" target="_blank">Lihat berkas</a>
							@else
							Belum upload berkas
							@endif
						</td>
					</tr>
					<tr>
						<td><b>Jenis Kegiatan</b></td>
						<td>{{$parenting->jenis_kegiatan}}</td>
					</tr>
					<tr>
						<td><b>Url</b></td>
						<td>{{$parenting->url}}</td>
					</tr>
				</table>
				<br>
				<table class="table_presensi" style="width: 100%;text-align: left !important" border="1">
					<tr>
						<td colspan="4" style="background: #ccc">
							<h3>Absensi Parenting</h3>
						</td>
					</tr>
					<tr>
						<th>Kode</th>
						<th>Nama Siswa</th>
						<th>Nama Peserta</th>
						<th>TTD</th>
					</tr>
					@foreach($presensi as $p)
					<tr>
						<td>{{$p->kode}}</td>
						<td>{{$p->nama_siswa}}</td>
						<td>{{$p->nama_peserta}}</td>
						<td><img src="https://simpauddispendik.surabaya.go.id/storage/umum/{{base64_encode($p->path_ttd)}}/1" style="width: 1.5cm"></td>
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
	table.table_presensi tr td{
		text-align: left !important;
	}
</style>
<script type="text/javascript">
	$(document).ready(function(){
		$('#modal_presensi').modal({backdrop: 'static'});
	});
</script>