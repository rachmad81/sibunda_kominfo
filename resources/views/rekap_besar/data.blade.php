<div class="row">
	<div class="col-lg-12">
		<div class="card">
			<div class="card-header card-info text-white">
				<a href="javascript:void(0)" class="close btn btn-sm btn-success" style="font-size: 10pt;opacity: 1;" onclick="get_data('load')">Load</a>
				<h4 class="modal-title">Data Rekap Besar</h4>
			</div>
			<div class="card-body" style="overflow: auto">
				<table class="table table-bordered">
					<thead>
						<tr style="background: #97c2dd;color: white">
							<th rowspan="2">No</th>
							<th rowspan="2">Jenjang</th>
							<th rowspan="2">Status</th>
							<th rowspan="2">Jumlah Lembaga</th>
							<th colspan="4">Akreditasi</th>
							<th rowspan="2">Jumlah Siswa</th>
							<th colspan="2">Pengisian TB BB</th>
							<th colspan="4">Vaksin Polio</th>
							<th rowspan="2">Peserta Educity Tour</th>
							<th rowspan="2">Peserta Talenta Seni</th>
							<th rowspan="2">Penerima PMT</th>
							<th rowspan="2">Prestasi</th>
							<th rowspan="2">Penghafal Kitab Suci</th>
							<th rowspan="2">Gakin Pramis</th>
							<th rowspan="2">Jumlah GTK</th>
							<th rowspan="2">Jumlah PNS</th>
							<th rowspan="2">Belum NUPTK</th>
							<th rowspan="2">Guru Pelatihan</th>
							{{-- <th rowspan="2">Jaspel</th> --}}
							<th rowspan="2">TPG</th>
							<th rowspan="2">TAMSIl</th>
							<th rowspan="2">TUKIN</th>
						</tr>
						<tr style="background: #97c2dd;color: white">
							<th>A</th>
							<th>B</th>
							<th>C</th>
							<th>Belum</th>
							<th>Sudah</th>
							<th>Belum</th>
							<th>Tahap 1 (Sudah)</th>
							<th>Tahap 1 (Belum)</th>
							<th>Tahap 2 (Sudah)</th>
							<th>Tahap 2 (Belum)</th>
						</tr>
					</thead>
					<tbody>
						@php
						$jenjang = $lembaga->pluck('jenjang')->unique()->toArray();
						$k=0;
						@endphp
						@foreach($jenjang as $j)
							@php
							$data = $lembaga->where('jenjang',$j);
							$data_siswa = $siswa->where('jenjang',$j);
							$data_gtk = $gtk->where('jenjang',$j);
							@endphp
							@foreach($data as $l)
								@php
								$data_siswa_status = $siswa->where('jenjang',$j)->where('status',$l->status);
								$data_gtk_status = $gtk->where('jenjang',$j)->where('status',$l->status);
								@endphp
								<tr>
									<td>{{($k+1)}}</td>
									<td>{{($l->jenjang=='SPS') ? 'PPT' : $l->jenjang}}</td>
									<td>{{$l->status}}</td>
									<td class="text-right">{{$l->jumlah_lembaga}}</td>
									<td class="text-right">{{$l->akreditasi_a}}</td>
									<td class="text-right">{{$l->akreditasi_b}}</td>
									<td class="text-right">{{$l->akreditasi_c}}</td>
									<td class="text-right">{{$l->akreditasi_belum}}</td>
									<td class="text-right">{{$data_siswa_status->sum('jumlah_siswa')}}</td>
									<td class="text-right">{{$data_siswa_status->sum('jumlah_sudah_tb_bb')}}</td>
									<td class="text-right">{{$data_siswa_status->sum('jumlah_siswa')-$data_siswa_status->sum('jumlah_sudah_tb_bb')}}</td>
									<td class="text-right">{{$data_siswa_status->sum('jumlah_sudah_polio_dosis_1')}}</td>
									<td class="text-right">{{($data_siswa_status->sum('jumlah_siswa')-$data_siswa_status->sum('jumlah_sudah_polio_dosis_1'))}}</td>
									<td class="text-right">{{$data_siswa_status->sum('jumlah_sudah_polio_dosis_2')}}</td>
									<td class="text-right">{{($data_siswa_status->sum('jumlah_siswa')-$data_siswa_status->sum('jumlah_sudah_polio_dosis_2'))}}</td>
									<td class="text-right">{{$data_siswa_status->sum('jumlah_educitytour')}}</td>
									<td class="text-right">{{$data_siswa_status->sum('jumlah_talenta')}}</td>
									<td class="text-right">{{$data_siswa_status->sum('jumlah_pmt')}}</td>
									<td class="text-right">{{$data_siswa_status->sum('jumlah_prestasi')}}</td>
									<td class="text-right">{{$data_siswa_status->sum('jumlah_kitab_suci')}}</td>
									<td class="text-right">{{$data_siswa_status->sum('jumlah_gakin_pramis')}}</td>
									<td class="text-right">{{$data_gtk_status->sum('jumlah_gtk')}}</td>
									<td class="text-right">{{$data_gtk_status->sum('jumlah_pns')}}</td>
									<td class="text-right">{{$data_gtk_status->sum('jumlah_belum_nuptk')}}</td>
									<td class="text-right">{{$data_gtk_status->sum('jumlah_pelatihan')}}</td>
									{{-- <td class="text-right">{{$data_gtk_status->sum('jumlah_jaspel')}}</td> --}}
									<td class="text-right">{{$data_gtk_status->sum('jumlah_tpg')}}</td>
									<td class="text-right">{{$data_gtk_status->sum('jumlah_tamsil')}}</td>
									<td class="text-right">{{$data_gtk_status->sum('jumlah_tukin')}}</td>
								</tr>
								@php
								$k++;
								@endphp
							@endforeach
							<tr style="font-weight: bold;background: #ffdae0;">
								<td colspan="3">Jumlah {{($j=='SPS') ? 'PPT' : $j}} </td>
								<td class="text-right">{{$data->sum('jumlah_lembaga')}}</td>
								<td class="text-right">{{$data->sum('akreditasi_a')}}</td>
								<td class="text-right">{{$data->sum('akreditasi_b')}}</td>
								<td class="text-right">{{$data->sum('akreditasi_c')}}</td>
								<td class="text-right">{{$data->sum('akreditasi_belum')}}</td>
								<td class="text-right">{{$data_siswa->sum('jumlah_siswa')}}</td>
								<td class="text-right">{{$data_siswa->sum('jumlah_sudah_tb_bb')}}</td>
								<td class="text-right">{{$data_siswa->sum('jumlah_siswa')-$data_siswa_status->sum('jumlah_sudah_tb_bb')}}</td>
								<td class="text-right">{{$data_siswa->sum('jumlah_sudah_polio_dosis_1')}}</td>
								<td class="text-right">{{($data_siswa->sum('jumlah_siswa')-$data_siswa->sum('jumlah_sudah_polio_dosis_1'))}}</td>
								<td class="text-right">{{$data_siswa->sum('jumlah_sudah_polio_dosis_2')}}</td>
								<td class="text-right">{{($data_siswa->sum('jumlah_siswa')-$data_siswa->sum('jumlah_sudah_polio_dosis_2'))}}</td>
								<td class="text-right">{{$data_siswa->sum('jumlah_educitytour')}}</td>
								<td class="text-right">{{$data_siswa->sum('jumlah_talenta')}}</td>
								<td class="text-right">{{$data_siswa->sum('jumlah_pmt')}}</td>
								<td class="text-right">{{$data_siswa->sum('jumlah_prestasi')}}</td>
								<td class="text-right">{{$data_siswa->sum('jumlah_kitab_suci')}}</td>
								<td class="text-right">{{$data_siswa->sum('jumlah_gakin_pramis')}}</td>
								<td class="text-right">{{$data_gtk->sum('jumlah_gtk')}}</td>
								<td class="text-right">{{$data_gtk->sum('jumlah_pns')}}</td>
								<td class="text-right">{{$data_gtk->sum('jumlah_belum_nuptk')}}</td>
								<td class="text-right">{{$data_gtk->sum('jumlah_pelatihan')}}</td>
								{{-- <td class="text-right">{{$data_gtk->sum('jumlah_jaspel')}}</td> --}}
								<td class="text-right">{{$data_gtk->sum('jumlah_tpg')}}</td>
								<td class="text-right">{{$data_gtk->sum('jumlah_tamsil')}}</td>
								<td class="text-right">{{$data_gtk->sum('jumlah_tukin')}}</td>
							</tr>
						@endforeach
						<tr style="font-weight: bold;background: #80c7f4">
							<td colspan="3">Jumlah Total </td>
							<td class="text-right">{{$lembaga->sum('jumlah_lembaga')}}</td>
							<td class="text-right">{{$lembaga->sum('akreditasi_a')}}</td>
							<td class="text-right">{{$lembaga->sum('akreditasi_b')}}</td>
							<td class="text-right">{{$lembaga->sum('akreditasi_c')}}</td>
							<td class="text-right">{{$lembaga->sum('akreditasi_belum')}}</td>
							<td class="text-right">{{$siswa->sum('jumlah_siswa')}}</td>
							<td class="text-right">{{$siswa->sum('jumlah_sudah_tb_bb')}}</td>
							<td class="text-right">{{$siswa->sum('jumlah_siswa')-$siswa->sum('jumlah_sudah_tb_bb')}}</td>
							<td class="text-right">{{$siswa->sum('jumlah_sudah_polio_dosis_1')}}</td>
							<td class="text-right">{{($siswa->sum('jumlah_siswa')-$siswa->sum('jumlah_sudah_polio_dosis_1'))}}</td>
							<td class="text-right">{{$siswa->sum('jumlah_sudah_polio_dosis_2')}}</td>
							<td class="text-right">{{($siswa->sum('jumlah_siswa')-$siswa->sum('jumlah_sudah_polio_dosis_2'))}}</td>
							<td class="text-right">{{$siswa->sum('jumlah_educitytour')}}</td>
							<td class="text-right">{{$siswa->sum('jumlah_talenta')}}</td>
							<td class="text-right">{{$siswa->sum('jumlah_pmt')}}</td>
							<td class="text-right">{{$siswa->sum('jumlah_prestasi')}}</td>
							<td class="text-right">{{$siswa->sum('jumlah_kitab_suci')}}</td>
							<td class="text-right">{{$siswa->sum('jumlah_gakin_pramis')}}</td>
							<td class="text-right">{{$gtk->sum('jumlah_gtk')}}</td>
							<td class="text-right">{{$gtk->sum('jumlah_pns')}}</td>
							<td class="text-right">{{$gtk->sum('jumlah_belum_nuptk')}}</td>
							<td class="text-right">{{$gtk->sum('jumlah_pelatihan')}}</td>
							{{-- <td class="text-right">{{$gtk->sum('jumlah_jaspel')}}</td> --}}
							<td class="text-right">{{$gtk->sum('jumlah_tpg')}}</td>
							<td class="text-right">{{$gtk->sum('jumlah_tamsil')}}</td>
							<td class="text-right">{{$gtk->sum('jumlah_tukin')}}</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>