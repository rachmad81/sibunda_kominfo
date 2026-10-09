<!-- Modal -->
<div id="myModalIdentitas" class="modal fade" role="dialog">
	<div class="modal-dialog modal-lg">

		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">{{$judul}}</h4>
				{{-- <button type="button" class="close" data-dismiss="modal">&times;</button> --}}
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-lg-12">
						<div class="alert alert-info">
							- Nilai yang dihitung adalah nilai dengan status <b>Selesai</b>.<br>
							- <i class="fa fa-check" style="color: green"></i> menandakan bahwa nilai tsb adalah nilai yang dihitung, dan berfungsi untuk mengembalikan ke status <b>Draft</b> agar penilai dapat menilai kembali.
						</div>
						<div class="table-responsive">
							<table class="table table-bordered table-striped">
								<thead>
									<tr>
										<th>Penilai</th>
										<th>Status Kriteria Utama (Point)</th>
										<th>Status Kriteria Pendukung (Point)</th>
									</tr>
								</thead>
								<tbody>
									@foreach($penilai as $p)
									<tr>
										<td>{{$p->nama}}</td>
										<td>
											{!! $p->status_utama !!}
										</td>
										<td>
											{!! $p->status_pendukung !!}
										</td>
									</tr>
									@endforeach
									<tr>
										<td colspan="3">&nbsp</td>
									</tr>
									<tr>
										<td>Jumlah</td>
										<td>
											@php
											$jumlah_utama = collect($penilai)->sum('jumlah_utama');
											echo $jumlah_utama.' Point';
											@endphp
										</td>
										<td>
											@php
											$jumlah_pendukung = collect($penilai)->sum('jumlah_pendukung');
											echo $jumlah_pendukung.' Point';
											@endphp
										</td>
									</tr>
									<tr>
										<td>Jumlah Selesai</td>
										<td>
											@php
											$jumlah_selesai_utama = collect($penilai)->sum('jumlah_utama_selesai');
											echo $jumlah_utama.' Point';
											@endphp
										</td>
										<td>
											@php
											$jumlah_selesai_pendukung = collect($penilai)->sum('jumlah_pendukung_selesai');
											echo $jumlah_pendukung.' Point';
											@endphp
										</td>
									</tr>
									<tr>
										<td>
											Jumlah Point<br>
											<i>Rumus = (RU*4)+(RP*2)</i>
										</td>
										<td>
											@php
											$jumlah_selesai_utama*=4;
											echo $jumlah_selesai_utama.' Point';
											@endphp
										</td>
										<td>
											@php
											$jumlah_selesai_pendukung *=2;
											echo $jumlah_selesai_pendukung.' Point';
											@endphp
										</td>
									</tr>
									<tr style="background: #ffc6c6;font-weight: bold;">
										<td colspan="2">Jumlah Penilaian</td>
										<td>
											@php
											echo ($jumlah_selesai_utama+$jumlah_selesai_pendukung).' Point';
											@endphp
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
	$('#myModalIdentitas').modal({backdrop: 'show'});
</script>