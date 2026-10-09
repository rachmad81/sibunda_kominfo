<!-- Modal -->
<style type="text/css">
	@media (min-width: 576px) {
		.modal-dialog.modal-custom{
			max-width: 1500px !important;
			margin: 1.75rem auto;
		}
	}
</style>
<div id="myModalIdentitas" class="modal fade" role="dialog">
	<div class="modal-dialog modal-custom">

		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Kriteria Utama</h4>
				{{-- <button type="button" class="close" data-dismiss="modal">&times;</button> --}}
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-lg-12">
						@include('penilai.penilaian.'.$tahun.'.tahap1.identitas')
					</div>
					<div class="col-lg-12">
						@php
						$value_option = [
							[
								'nilai'=>0,
								'text'=>'Tidak ada',
							],
							[
								'nilai'=>1,
								'text'=>'Ada, namun belum sesuai',
							],
							[
								'nilai'=>2,
								'text'=>'Ada, dan sesuai namun tidak lengkap',
							],
							[
								'nilai'=>3,
								'text'=>'Ada, sesuai dan lengkap',
							],
						];
						$sebagai_option = [
							[
								'nilai'=>'Draft',
								'text'=>'Draft',
							],
							[
								'nilai'=>'Selesai',
								'text'=>'Kunci penilaian',
							],
						];
						@endphp
						<div class="table-responsive">
							<table id="dataTable" class="table table-bordered table-striped">
								<thead>
									<tr>
										<th>No</th>
										<th>Uraian</th>
										<th>Penilaian (0-100)</th>
										<th>#</th>
									</tr>
								</thead>
								<tbody>
									@foreach($tahap2 as $n=>$t)
									<tr style="background: #d0dff4">
										<td>{{($n+1)}}</td>
										<td colspan="3">{{$t['kompetensi']}}</td>
									</tr>
									@foreach($t['indikator'] as $indi)
									@php
									$nama = $t['id'].'___'.$indi['id'];
									$checked = ['','','','','',''];
									if(!empty($nilai)){
										$data_penilaian = json_decode($nilai->hasil);
										$dt = collect($data_penilaian)->where('id_kompetensi',$t['id'])->where('id_indikator',$indi['id'])->first();
										if(!empty($dt)){
											// $checked[$dt->nilai] = 'selected';
											// $arr_hasil_penilaian = ['','Sangat Tidak Baik','Tidak Baik','Cukup','Baik','Sangat Baik'];
											// $hasil_penilaian = $arr_hasil_penilaian[$dt->nilai];
											$hasil_penilaian = $dt->nilai;
										}else{
											// $checked[0] = 'selected';
											$hasil_penilaian = 0;
										}
									}else{
										// $checked[0] = 'selected';
										$hasil_penilaian = 0;
									}
									@endphp
									<tr>
										<td></td>
										<td>{{$indi['text']}}</td>
										<td>
											@if(!empty($nilai) && $nilai->status=='Selesai')
											{{$hasil_penilaian}}
											@else
											{{-- <select class="form-control" name="{{$nama}}">
												<option {{$checked[0]}} value="0">..:: Pilih Penilaian ::..</option>
												<option {{$checked[1]}} value="1">Sangat Tidak Baik</option>
												<option {{$checked[2]}} value="2">Tidak Baik</option>
												<option {{$checked[3]}} value="3">Cukup</option>
												<option {{$checked[4]}} value="4">Baik</option>
												<option {{$checked[5]}} value="5">Sangat Baik</option>
											</select> --}}
											<input type="number" class="form-control" min="0" max="100" name="{{$nama}}" value="{{$hasil_penilaian}}">
											@endif
										</td>
										<td>
											@if((empty($nilai) || (!empty($nilai) && $nilai->status!='Selesai')) && $tahun==date('Y'))
											<a href="javascript:void(0)" class="btn btn-sm btn-primary" onclick="simpan_nilai('{{$nama}}','nilai_kriteria_tahap2','{{$identitas->user_id}}')">Simpan</a>
											@endif
										</td>
									</tr>
									@endforeach
									@endforeach
									@if((empty($nilai) || (!empty($nilai) && $nilai->status!='Selesai')) && $tahun==date('Y'))
									<tr>
										<td colspan="4">&nbsp;</td>
									</tr>
									<tr>
										<td colspan="2"><b>Simpan pengajuan sebagai</b></td>
										<td>
											<select class="form-control" name="status">
												<option value="Draft">Draft</option>
												<option value="Selesai">Kunci penilaian</option>
											</select>
										</td>
										<td>
											<a href="javascript:void(0)" class="btn btn-sm btn-success" onclick="simpan_nilai('status','nilai_kriteria_tahap2','{{$identitas->user_id}}','sebagai')">Simpan</a>
										</td>
									</tr>
									@endif
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
<div class="modal-page2"></div>
<script type="text/javascript">
	$('#myModalIdentitas').modal({backdrop: 'static'});
	// $('#myModalIdentitas').modal();
</script>