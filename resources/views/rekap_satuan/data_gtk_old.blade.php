<div class="row">
	<div class="col-lg-12">
		<div class="card">
			<div class="card-header card-info text-white">
				<a href="javascript:void(0)" class="close btn btn-sm btn-success" style="font-size: 10pt;opacity: 1;" onclick="get_data('gtk','load')">Load</a>
				<h4 class="modal-title">Data GTK</h4>
			</div>
			<div class="card-body">
				<div class="table-responsive">
					<table class="table table-bordered table-striped" id="myData">
						<thead class="thead_siswa_non_paud">
							<tr>
								<th>No</th>
								<th>Jenjang</th>
								<th>Status</th>
								<th>Jumlah Lembaga</th>
								<th>Jumlah Guru</th>
								<th>Jumlah S1</th>
								<th>Jumlah S2</th>
								<th>Jumlah S3</th>
								<th>Belum NUPTK</th>
								<th>Jumlah Guru Pelatihan</th>
								{{-- <th>JASPEL</th> --}}
								{{-- <th>BPJS</th> --}}
								<th>Sertifikasi</th>
								<th>TPG</th>
								<th>TAMSIL</th>
								<th>TUKIN</th>
							</tr>
						</thead>
						<tbody>
							@foreach($gtk as $k=>$l)
							<tr>
								<td>{{($k+1)}}</td>
								<td>{{($l->jenjang=='SPS') ? 'PPT' : $l->jenjang}}</td>
								<td>{{$l->status}}</td>
								<td>
									@php
									$data_lembaga = collect($lembaga)->where('status',$l->status)->where('jenjang',$l->jenjang)->first();
									@endphp
									@if(!empty($data_lembaga))
									<a href="javascript:void(0)" onclick="detail_data('lembaga',['{{$l->jenjang}}','{{$l->status}}','jumlah_lembaga','Detail Jumlah Lembaga '])">{{$data_lembaga->jumlah}}</a>
									@else
									@endif
								</td>
								<td><a href="javascript:void(0)" onclick="detail_data('gtk',['{{$l->jenjang}}','{{$l->status}}','jumlah_gtk','Detail Jumlah Guru '])">{{$l->jumlah_gtk}}</a></td>
								<td><a href="javascript:void(0)" onclick="detail_data('gtk',['{{$l->jenjang}}','{{$l->status}}','jumlah_s1','Detail Jumlah GTK Lulusan S1 '])">{{$l->jumlah_s1}}</a></td>
								<td><a href="javascript:void(0)" onclick="detail_data('gtk',['{{$l->jenjang}}','{{$l->status}}','jumlah_s2','Detail Jumlah GTK Lulusan S2 '])">{{$l->jumlah_s2}}</a></td>
								<td><a href="javascript:void(0)" onclick="detail_data('gtk',['{{$l->jenjang}}','{{$l->status}}','jumlah_s3','Detail Jumlah GTK Lulusan S3 '])">{{$l->jumlah_s3}}</a></td>
								<td><a href="javascript:void(0)" onclick="detail_data('gtk',['{{$l->jenjang}}','{{$l->status}}','jumlah_belum_nuptk','Detail Jumlah Belum NUPTK '])">{{$l->jumlah_belum_nuptk}}</a></td>
								<td><a href="javascript:void(0)" onclick="detail_data('gtk',['{{$l->jenjang}}','{{$l->status}}','jumlah_pelatihan','Detail Jumlah Jumlah Guru Pelatihan '])">{{$l->jumlah_pelatihan}}</a></td>
								{{-- <td><a href="javascript:void(0)" onclick="detail_data('gtk',['{{$l->jenjang}}','{{$l->status}}','jumlah_jaspel','Detail Jumlah JASPEL '])">{{$l->jumlah_jaspel}}</a></td> --}}
								{{-- <td>$l->jumlah_gtk</td> --}}
								<td><a href="javascript:void(0)" onclick="detail_data('gtk',['{{$l->jenjang}}','{{$l->status}}','jumlah_sertifikasi','Detail Jumlah Sertifikasi '])">{{$l->jumlah_sertifikasi}}</a></td>
								<td><a href="javascript:void(0)" onclick="detail_data('gtk',['{{$l->jenjang}}','{{$l->status}}','jumlah_tpg','Detail Jumlah TPG '])">{{$l->jumlah_tpg}}</a></td>
								<td><a href="javascript:void(0)" onclick="detail_data('gtk',['{{$l->jenjang}}','{{$l->status}}','jumlah_tamsil','Detail Jumlah TAMSIL '])">{{$l->jumlah_tamsil}}</a></td>
								<td><a href="javascript:void(0)" onclick="detail_data('gtk',['{{$l->jenjang}}','{{$l->status}}','jumlah_tukin','Detail Jumlah TUKIN '])">{{$l->jumlah_tukin}}</a></td>
							</tr>
							@endforeach
						</tbody>
						<tfoot>
							<tr>
								<td colspan="3">Jumlah Total</td>
								<td>{{$lembaga->sum('jumlah')}}</td>
								<td>{{$gtk->sum('jumlah_gtk')}}</td>
								<td>{{$gtk->sum('jumlah_s1')}}</td>
								<td>{{$gtk->sum('jumlah_s2')}}</td>
								<td>{{$gtk->sum('jumlah_s3')}}</td>
								<td>{{$gtk->sum('jumlah_belum_nuptk')}}</td>
								<td>{{$gtk->sum('jumlah_pelatihan')}}</td>
								{{-- <td>{{$gtk->sum('jumlah_jaspel')}}</td> --}}
								{{-- <td>$l->jumlah_gtk</td> --}}
								<td>{{$gtk->sum('jumlah_sertifikasi')}}</td>
								<td>{{$gtk->sum('jumlah_tpg')}}</td>
								<td>{{$gtk->sum('jumlah_tamsil')}}</td>
								<td>{{$gtk->sum('jumlah_tukin')}}</td>
							</tr>
						</tfoot>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>