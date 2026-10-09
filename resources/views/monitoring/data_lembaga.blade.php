<div class="row">
	<div class="col-lg-12">
		<div class="card">
			<div class="card-header card-info text-white">
				<a href="javascript:void(0)" class="close btn btn-sm btn-success" style="font-size: 10pt;opacity: 1;" onclick="get_data('lembaga','load')">Load</a>
				<h4 class="modal-title">{{$judul}}</h4>
			</div>
			<div class="card-body">
				<table class="table table-bordered table-striped" id="myData">
					<thead class="thead_siswa_non_paud">
						<tr>
							<th>No</th>
							<th>Jenjang</th>
							<th>Status</th>
							<th>NPSN</th>
							<th>Nama Lembaga</th>
							<th>Alamat</th>
							<th>Kecamatan</th>
							<th>Kelurahan</th>
							<th>Akreditasi</th>
						</tr>
					</thead>
					<tbody>
						@foreach($lembaga as $k=>$l)
						<tr>
							<td>{{$k+1}}</td>
							<td>{{($l->jenjang=='SPS') ? 'PPT' : $l->jenjang}}</td>
							<td>{{$l->status}}</td>
							<td>{{$l->npsn}}</td>
							<td>{{$l->nama_lembaga}}</td>
							<td>{{$l->alamat}}</td>
							<td>{{$l->kecamatan_dispenduk}}</td>
							<td>{{$l->kelurahan_dispenduk}}</td>
							<td>{{$l->akreditasi}}</td>
						</tr>
						@endforeach
					</tbody>
				</table>			
			</div>
		</div>
	</div>
</div>