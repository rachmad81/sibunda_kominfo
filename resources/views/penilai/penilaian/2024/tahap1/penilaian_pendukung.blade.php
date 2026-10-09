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
				<h4 class="modal-title">Kriteria Pendukung</h4>
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
										<th style="width: 5%">No</th>
										<th>Uraian</th>
										<th style="width: 10%">Link file</th>
										<th style="'width: 50%;">Penilaian</th>
										<th style="'width: 1%;"></th>
									</tr>
								</thead>

								<tbody>
									@foreach($uraian_kriteria_pendukung as $k)
									<tr style="background: yellow;">
										<td>{!! $k['number'] !!}</td>
										<td colspan="4">{!! $k['title'] !!}</td>
									</tr>
									@foreach($k['subtitle'] as $s)
									@php
									$nama = $k['name'].'___'.$s['number'];
									$kolom = $k['name'];
									$kolom_nilai = 'nilai_'.$kolom;
									@endphp
									<tr>
										<td>{!! $s['number'] !!}</td>
										<td colspan="4">{!! $s['title'] !!}</td>
										
									</tr>

									@foreach($mitra_apresiasi->where('sub',$k['number']) as $in=>$ka)
									@php
									$nama = $k['name'].'___'.$s['number'].'___'.$ka->id_mitra.'___'.$k['number'];
									$kolom = $k['name'];
									$kolom_nilai = 'nilai_'.$kolom;

									if(!empty($berkas)){
										$baris = json_decode($berkas->$kolom);
										$arr_baris = collect($baris)->where('number',$s['number'])->where('id_kegiatan',$ka->id_mitra)->first();
										$isi_file = (!empty($arr_baris)) ? $arr_baris->file : '';
									}else{
										$isi_file = '';
									}


									@endphp
									<tr>
										<td>{{$k['number']}}.{{$s['number']}}.{{($in+1)}}</td>
										<td width="25%">@if($k['number']=='A') {{$ka->nama_mitra}} @else <a href="javascript:void(0)" onclick="modal_kegiatan('{{$ka->id_mitra}}')">{{$ka->nama_mitra}}</a> @endif</td>
										<td>
											{{$isi_file}}
										</td>
										<td>
											@php
											$penilaian = '0';
											if(!empty($nilai) && !is_null($nilai->$kolom_nilai)){
												$baris = collect(json_decode($nilai->$kolom_nilai))->where('number',$s['number'])->where('id_kegiatan',$ka->id_mitra)->first();
												if(!empty($baris)){
													$penilaian = $baris->file;
												}
											}					
											@endphp

											@if((empty($nilai) || (!empty($nilai) && $nilai->status!='Selesai')) && $tahun==date('Y'))
											<select class="form-control" name="nilai_{{$nama}}">
												@foreach($value_option as $v)
												<option @if(!empty($nilai) && $penilaian==$v['nilai']) selected @endif value="{{$v['nilai']}}">{{$v['text']}}</option>
												@endforeach
											</select>
											@else
											@php
											$text_nilai = collect($value_option)->pluck('text')->toArray();
											$val_nilai = collect($value_option)->pluck('nilai')->toArray();
											$key = array_search($penilaian, $val_nilai);
											echo $text_nilai[$key].' ('.$penilaian.')';
											@endphp
											@endif
										</td>
										<td width="20%">
											@if((empty($nilai) || (!empty($nilai) && $nilai->status!='Selesai')) && $tahun==date('Y'))
											<a href="javascript:void(0)" class="btn btn-sm btn-primary" onclick="simpan_nilai('nilai_{{$nama}}','nilai_kriteria_pendukung','{{$identitas->user_id}}')">Simpan</a>
											@if($isi_file!='')
											<a href="{{$isi_file}}" target="_blank" class="btn btn-sm btn-warning">Lihat</a>
											@endif

											@else
											@if($isi_file!='')
											<a href="{{$isi_file}}" target="_blank" class="btn btn-sm btn-warning">Lihat</a>
											@endif
											@endif
										</td>
									</tr>
									@endforeach

									@endforeach
									@endforeach
									@if((empty($nilai) || (!empty($nilai) && $nilai->status!='Selesai')) && $tahun==date('Y'))
									<tr>
										<td colspan="5">&nbsp;</td>
									</tr>
									<tr>
										<td colspan="3"><b>Simpan pengajuan sebagai</b></td>
										<td>
											<select class="form-control" name="status">
												<option value="Draft">Draft</option>
												<option value="Selesai">Kunci penilaian</option>
											</select>
										</td>
										<td>
											<a href="javascript:void(0)" class="btn btn-sm btn-success" onclick="simpan_nilai('status','nilai_kriteria_pendukung','{{$identitas->user_id}}','sebagai')">Simpan</a>
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
	// $('#myModalIdentitas').modal();
	$('#myModalIdentitas').modal({backdrop: 'static'});

	function modal_kegiatan(id_kegiatan){
		var data = {
			id_kegiatan:id_kegiatan,
		};

		$.post('{{route('penilai-penilaian-tahap1-modal_kegiatan',['tahun'=>$tahun])}}',data,function(data){
			$('.modal-page2').html(data.content);
			$('.modal-page2 .modal-content').css('background','#ccc');
		});
	}
</script>