<h4>Kriteria Utama</h4>
@include('bunda.lomba.alert')

<div class="table-responsive">
	<table id="dataTable" class="table table-bordered table-striped">
		<thead>
			<tr>
				<th style="width: 5%">No</th>
				{{-- @if(!empty($berkas) && $berkas->status=='Selesai')
				<th style="width: 70%">Uraian</th>
				<th></th>
				@else
				<th style="width: 30%">Uraian</th>
				<th>Link File</th>
				<th></th>
				@endif --}}
				<th colspan="3">Uraian</th>
			</tr>
		</thead>
		<tbody>
			@foreach($uraian_kriteria_utama as $k)
			<tr style="background: yellow;">
				<td>{!! $k['number'] !!}</td>
				<td colspan="3">{!! $k['title'] !!}</td>
				{{-- @if(!empty($berkas) && $berkas->status=='Selesai')
				<td></td>
				@else
				<td></td>
				<td></td>
				@endif --}}
			</tr>
			@foreach($k['subtitle'] as $s)
			<tr>
				<td>{!! $s['number'] !!}</td>
				<td colspan="3">{!! $s['title'] !!}</td>
			</tr>
			@php
			$no_tambahan = 1;
			@endphp
			@foreach($kegiatan_apresiasi->where('sub',$k['number']) as $in=>$ka)
			@php
			$nama = $k['name'].'___'.$s['number'].'___'.$ka->id_kegiatan.'___'.$k['number'];
			$kolom = $k['name'];

			if(!empty($berkas)){
				$baris = json_decode($berkas->$kolom);
				$arr_baris = collect($baris)->where('number',$s['number'])->where('id_kegiatan',$ka->id_kegiatan)->first();
				$isi_file = (!empty($arr_baris)) ? $arr_baris->file : '';
			}else{
				$isi_file = '';
			}


			@endphp
			<tr>
				<td>{{$k['number']}}.{{$s['number']}}.{{$no_tambahan}}</td>
				<td width="25%"><a href="javascript:void(0)" onclick="modal_kegiatan('{{$ka->id_kegiatan}}')">{{$ka->nama_kegiatan}}</a></td>
				<td>
					@if((empty($berkas) || (!empty($berkas) && $berkas->status!='Selesai')) && $tahun==date('Y'))
					<input type="url" name="{{$nama}}" class="form-control" style="border:1px solid #ccc" placeholder="URL Google Drive disini..." value="{{$isi_file}}">
					@else
					{{$isi_file}}
					@endif
				</td>
				<td width="20%">
					@if((empty($berkas) || (!empty($berkas) && $berkas->status!='Selesai')) && $tahun==date('Y'))
					<a href="javascript:void(0)" class="btn btn-sm btn-primary" onclick="simpan_berkas('{{$nama}}','kriteria_utama','form3')">Simpan</a>
					@if($isi_file!='')
					<a href="{{$isi_file}}" target="_blank" class="btn btn-sm btn-warning">Lihat</a>
					@endif

					@if(in_array($k['number'],['A','B','C','D','E','F','G']) AND $s['number']=='1')
					<a href="javascript:void(0)" class="btn btn-sm btn-danger" onclick="hapus_kegiatan('{{$ka->id}}')">Hapus</a>
					@endif

					@else
					@if($isi_file!='')
					<a href="{{$isi_file}}" target="_blank" class="btn btn-sm btn-warning">Lihat</a>
					@endif
					@endif
				</td>
			</tr>
			@php
			$no_tambahan++;
			@endphp
			@endforeach
			@if(in_array($k['number'],['A','B','C','D','E','F','G']) AND $s['number']=='1')

			@php
			$program = $kegiatan->pluck('nama_program')->sort();
			@endphp
			
			@if((empty($berkas) || (!empty($berkas) && $berkas->status!='Selesai')) && $tahun==date('Y'))
			<tr style="background: #fcc">
				<td></td>
				<td colspan="3">
					<select class="form-control kegiatan-chosen kegiatan_id{{$k['number']}}">
						<option value="">..:: Pilih Kegiatan ::..</option>
						@foreach($program as $prog)
						<optgroup label="{{$prog}}">
							@php
							$keg = $kegiatan->where('nama_program',$prog)->sortBy('nama_kegiatan');
							@endphp
							@foreach($keg as $kg)
							<option value="{{$k['number']}}--{{$kg->id_kegiatan}}">{{$kg->nama_kegiatan}}</option>
							@endforeach
						</optgroup>
						@endforeach
					</select>
					<a href="javascript:void(0)" class="btn btn-sm btn-primary" onclick="tambah_kegiatan('{{$k['number']}}')"><i class="fa fa-plus"></i> Tambahkan</a>
				</td>
			</tr>
			@endif

			@endif
			@endforeach
			@endforeach
			@if((empty($berkas) || (!empty($berkas) && $berkas->status!='Selesai')) && $tahun==date('Y'))
			<tr>
				<td colspan="4">&nbsp;</td>
			</tr>
			<tr>
				<td colspan="2"><b>Simpan pengajuan sebagai</b></td>
				<td>
					<select class="form-control" name="status">
						<option value="Draft">Draft</option>
						<option value="Selesai">Ajukan untuk dinilai</option>
					</select>
				</td>
				<td>
					@if($tahun==date('Y'))
					<a href="javascript:void(0)" class="btn btn-sm btn-success" onclick="simpan_berkas('status','kriteria_utama','form3','select')">Simpan</a>
					@endif
				</td>
			</tr>
			@endif
		</tbody>
	</table>
</div>
<div class="modal-page"></div>
<script type="text/javascript">
	$(document).ready(function(){
		$(".kegiatan-chosen").chosen();
	})

	function tambah_kegiatan(id){
		var kegiatan_id = $('.kegiatan_id'+id).val();
		var data = {
			kegiatan_id:kegiatan_id,
		};

		if(kegiatan_id==''){
			swal('Whooops','Pilih kegiatan lebih dhulu','error');
		}else{
			$.post('{{route('bunda-lomba-simpan_kegiatan',['tahun'=>$tahun])}}',data,function(data){
				if(data.code=='200'){
					formnya('form3');
				}
			});
		}
	}

	function hapus_kegiatan(id){
		var data = {
			id:id,
		};

		Swal.fire({
			title: 'Apa kamu yakin?',
			text: "Data tidak bisa dikembalikan kembali!",
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			confirmButtonText: 'Iya, aku yakin!',
			cancelButtonText: 'Tidak!'
		}).then((result) => {
			if (result.value) {
				$.post('{{route('bunda-lomba-hapus_kegiatan',['tahun'=>$tahun])}}',data,function(data){
					if(data.code=='200'){
						formnya('form3');
					}
				});
			}else{
			}
		})
	}

	function modal_kegiatan(id_kegiatan){
		var data = {
			id_kegiatan:id_kegiatan,
		};

		$.post('{{route('bunda-lomba-modal_kegiatan',['tahun'=>$tahun])}}',data,function(data){
			$('.modal-page').html(data.content);
		});
	}
</script>