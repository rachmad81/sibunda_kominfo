<!-- Modal -->
<div id="myModalIdentitas" class="modal fade" role="dialog">
	<div class="modal-dialog modal-lg">

		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Identitas Peserta</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-lg-12">
						<h5>Identitas Peserta Lomba</h5>
						<div class="table-responsive">
							<form class="form-identitas">
								<table id="dataTable" class="table table-bordered table-striped">
									<tbody>
										<tr>
											<td style="width:60%"><b>Nama</b></td>
											<td>
												{{$identitas->nama}}
											</td>
										</tr>
										<tr>
											<td><b>No SK Penetapan/Pengukuhan Bunda PAUD Kecamatan/Kelurahan Tanggal Penetapan/Pengukuhan</b></td>
											<td>
												{{$identitas->no_sk}}
											</td>
										</tr>
										<tr>
											<td><b>No HP Bunda PAUD Kecamatan/Kelurahan</b></td>
											<td>
												{{$identitas->telp_peserta}}
											</td>
										</tr>
										<tr>
											<td><b>Instansi Pengusul Camat/Lurah</b></td>
											<td>
												{{$identitas->instansi}}
											</td>
										</tr>
										<tr>
											<td><b>Alamat Instansi</b></td>
											<td>
												{{$identitas->alamat_instansi}}
											</td>
										</tr>
										<tr>
											<td><b>Kecamatan Instansi</b></td>
											<td>
												{{$identitas->instansi}}
											</td>
										</tr>
										<tr>
											<td><b>No. HP Pengusul</b></td>
											<td>
												{{$identitas->telp_pengusul}}
											</td>
										</tr>
										<tr>
											<td><b>Alamat e-mail Pengusul</b></td>
											<td>
												{{$identitas->email_pengusul}}
											</td>
										</tr>
									</tbody>
								</table>
							</form>
						</div>
					</div>
					<div class="col-lg-12">
						<h5>Berkas Identitas</h5>
						<div class="table-responsive">
							<table id="dataTable" class="table table-bordered table-striped">
								<tbody>
									<tr>
										<td style="width:80%"><b>Surat pengusulan dari Camat/Lurah</b></td>
										<td>
											@if(!empty($berkas) && !is_null($berkas->surat_pengusulan))
											<a href="{{$berkas->surat_pengusulan}}" target="_blank" class="btn btn-sm btn-warning">View</a>
											@endif
										</td>
									</tr>
									<tr>
										<td><b>Surat Keputusan penetapan sebagai Bunda PAUD Kecamatan/Kelurahan</b></td>
										<td>
											@if(!empty($berkas) && !is_null($berkas->sk_penetapan))
											<a href="{{$berkas->sk_penetapan}}" target="_blank" class="btn btn-sm btn-warning">View</a>
											@endif
										</td>
									</tr>
									<tr>
										<td><b>Program Kerja Bunda PAUD Kecamatan/Kelurahan</b></td>
										<td>
											@if(!empty($berkas) && !is_null($berkas->program))
											<a href="{{$berkas->program}}" target="_blank" class="btn btn-sm btn-warning">View</a>
											@endif
										</td>
									</tr>
									<tr>
										<td><b>Surat Keputusan dan Struktur Organisasi Pokja Bunda PAUD Kecamatan/Kelurahan</b></td>
										<td>
											@if(!empty($berkas) && !is_null($berkas->struktur_organisasi))
											<a href="{{$berkas->struktur_organisasi}}" target="_blank" class="btn btn-sm btn-warning">View</a>
											@endif
										</td>
									</tr>
									<tr>
										<td><b>Profil Bunda PAUD Kecamatan/Kelurahan</b></td>
										<td>
											@if(!empty($berkas) && !is_null($berkas->profil_bunda))
											<a href="{{$berkas->profil_bunda}}" target="_blank" class="btn btn-sm btn-warning">View</a>
											@endif
										</td>
									</tr>
									<tr>
										<td><b>Profil perkembangan PAUD Kecamatan/Kelurahan</b></td>
										<td>
											@if(!empty($berkas) && !is_null($berkas->profil_perkembangan))
											<a href="{{$berkas->profil_perkembangan}}" target="_blank" class="btn btn-sm btn-warning">View</a>
											@endif
										</td>
									</tr>
								</tbody>
							</table>
						</div>
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
	$('#myModalIdentitas').modal('show');
</script>