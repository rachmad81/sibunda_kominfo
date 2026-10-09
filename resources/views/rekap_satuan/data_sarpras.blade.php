<div class="row">
	<div class="col-lg-12">
		<div class="card">
			<div class="card-header card-info text-white">
				<a href="javascript:void(0)" class="close btn btn-sm btn-success" style="font-size: 10pt;opacity: 1;" onclick="get_data('sarpras','load')">Load</a>
				<h4 class="modal-title">Data Sarana Prasarana</h4>
			</div>
			<div class="card-body">
				<div class="table-responsive">
					<table class="table table-bordered table-striped">
						<thead class="thead_siswa_non_paud">
							<tr>
								<th>No</th>
								<th>Jenis Ruangan</th>
								<th>Jumlah Ruang</th>
								<th>Baik</th>
								<th>Sedang</th>
								<th>Rusak Berat</th>
							</tr>
						</thead>
						<tbody>
							@php
							$ruang = [
								'Ruang Kelas',
								'Ruang Kepala Sekolah / Lembaga',
								'Ruang Guru',
								'Ruang BK',
								'Ruang TU',
								'Perpustakaan',
								'Laboratorium',
								'Kamar Mandi',
								'Ruang UKS',
								'Ruang Aula',
								'Tempat Ibadah',
								'Sarana Olahraga',
								'Sanitasi',
								'Kantin',
								'Lapangan Upacara / Olahraga'
							];
							@endphp

							@for($i=0;$i<count($ruang);$i++)
							@php
							$jumlah_ruang = $lembaga->where('jenis_ruangan',$ruang[$i])->sum('jumlah_ruang');
							$jumlah_baik = $lembaga->where('jenis_ruangan',$ruang[$i])->sum('jumlah_baik');
							$jumlah_sedang = $lembaga->where('jenis_ruangan',$ruang[$i])->sum('jumlah_sedang');
							$jumlah_buruk = $lembaga->where('jenis_ruangan',$ruang[$i])->sum('jumlah_buruk');
							@endphp
							<tr>
								<td>{{($i+1)}}</td>
								<td>{{$ruang[$i]}}</td>
								<td><a href="javascript:void(0)" onclick="detail_data('sarpras',['{{$ruang[$i]}}','jumlah_ruang','Jumlah Semua Ruangan'])">{{$jumlah_ruang}}</a></td>
								<td><a href="javascript:void(0)" onclick="detail_data('sarpras',['{{$ruang[$i]}}','jumlah_baik','Jumlah Ruangan Kondisi Baik'])">{{$jumlah_baik}}</a></td>
								<td><a href="javascript:void(0)" onclick="detail_data('sarpras',['{{$ruang[$i]}}','jumlah_sedang','Jumlah Ruangan Kondisi Sedang'])">{{$jumlah_sedang}}</a></td>
								<td><a href="javascript:void(0)" onclick="detail_data('sarpras',['{{$ruang[$i]}}','jumlah_buruk','Jumlah Ruangan Kondisi Buruk'])">{{$jumlah_buruk}}</a></td>
							</tr>
							@if($i==0)
							@php
							$kelas = $rombel->pluck('kelas')->unique()->toArray();
							$kelas = array_values($kelas);
							@endphp
							@for($j=0;$j<count($kelas);$j++)
							@php
							$jumlah_rombel = $rombel->where('kelas',$kelas[$j])->sum('jumlah');
							@endphp
							<tr>
								@if($j==0)
								<td rowspan="{{count($kelas)}}">Rombel</td>
								@endif
								<td>{{$kelas[$j]}}</td>
								<td colspan="5">{{$jumlah_rombel}}</td>
							</tr>
							@endfor
							@endif
							@endfor
						</tbody>
						<tfoot>
							<tr>
								<th colspan="2">Total</th>
								<th>{{$lembaga->sum('jumlah_ruang')}}</th>
								<th>{{$lembaga->sum('jumlah_baik')}}</th>
								<th>{{$lembaga->sum('jumlah_sedang')}}</th>
								<th>{{$lembaga->sum('jumlah_buruk')}}</th>
							</tr>
						</tfoot>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>