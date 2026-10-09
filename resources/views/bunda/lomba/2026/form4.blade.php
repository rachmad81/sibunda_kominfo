<h4>Kriteria Pendukung</h4>
@include('bunda.lomba.alert')

<div class="table-responsive">
	<table id="dataTable" class="table table-bordered table-striped">
		<thead>
			<tr>
				<th style="width: 5%">No</th>
				@if(!empty($berkas) && $berkas->status=='Selesai')
				<th style="width: 70%">Uraian</th>
				<th></th>
				@else
				<th style="width: 30%">Uraian</th>
				<th>Link File</th>
				<th></th>
				@endif
			</tr>
		</thead>
		<tbody>
			@foreach($uraian_kriteria_pendukung as $k)
			<tr style="background: yellow;">
				<td>{!! $k['number'] !!}</td>
				<td>{!! $k['title'] !!}</td>
				<td></td>
				<td></td>
			</tr>
			@foreach($k['subtitle'] as $s)
			<tr>
				<td>{!! $s['number'] !!}</td>
				<td>{!! $s['title'] !!}</td>
				<td></td>
				<td></td>
			</tr>
			@php
			$tahun_keg = $mitra_apresiasi->where('sub',$k['number'].'_'.$s['number'])->pluck('tahun')->unique()->sort();
			$no_tambahan = 1;
			@endphp
			@if($k['number']=='A')
			<tr>
				<td colspan="4">Mitra</td>
			</tr>
			@endif

			{{-- MITRA --}}
			@foreach($mitra_apresiasi->where('sub',$k['number'].'_'.$s['number'])->where('jenis','mitra') as $in=>$ma)
			@php
			$nama = $k['name'].'___'.$s['number'].'___'.$ma->id_mitra.'___'.$k['number'];
			$kolom = $k['name'];

			if(!empty($berkas)){
				$baris = json_decode($berkas->$kolom);
				$arr_baris = collect($baris)->where('number',$s['number'])->where('id_kegiatan',$ma->id_mitra)->first();
				$isi_file = (!empty($arr_baris)) ? $arr_baris->file : '';
			}else{
				$isi_file = '';
			}


			@endphp
			<tr>
				<td>{{$k['number']}}.{{$s['number']}}.{{$no_tambahan}}</td>
				<td width="25%"><a href="javascript:void(0)" onclick="modal_kegiatan('{{$ma->id_mitra}}')">{{$ma->nama_mitra}}</a></td>
				<td>
					@if((empty($berkas) || (!empty($berkas) && $berkas->status!='Selesai')) && $tahun==date('Y'))
					<input type="url" name="{{$nama}}" class="form-control" style="border:1px solid #ccc" placeholder="URL Google Drive disini..." value="{{$isi_file}}">
					@else
					{{$isi_file}}
					@endif
				</td>
				<td width="20%">
					@if((empty($berkas) || (!empty($berkas) && $berkas->status!='Selesai')) && $tahun==date('Y'))
					<a href="javascript:void(0)" class="btn btn-sm btn-primary" onclick="simpan_berkas('{{$nama}}','kriteria_pendukung','form4')">Simpan</a>
					@if($isi_file!='')
					<a href="{{$isi_file}}" target="_blank" class="btn btn-sm btn-warning">Lihat</a>
					@endif

					@if(in_array($k['number'],['A','B','C','D','E','F','G','H','I']))
					<a href="javascript:void(0)" class="btn btn-sm btn-danger" onclick="hapus_mitra('{{$ma->id}}')">Hapus</a>
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
			{{-- END MITRA --}}

			@foreach($tahun_keg as $th)			
			<tr>
				<td colspan="5"><b>Tahun Kegiatan {{$th}}</b></td>
			</tr>

			{{-- KEGIATAN --}}
			@foreach($mitra_apresiasi->where('sub',$k['number'].'_'.$s['number'])->where('jenis','kegiatan')->where('tahun',$th) as $in=>$ma)
			@php
			$nama = $k['name'].'___'.$s['number'].'___'.$ma->id_mitra.'___'.$k['number'];
			$kolom = $k['name'];

			if(!empty($berkas)){
				$baris = json_decode($berkas->$kolom);
				$arr_baris = collect($baris)->where('number',$s['number'])->where('id_kegiatan',$ma->id_mitra)->first();
				$isi_file = (!empty($arr_baris)) ? $arr_baris->file : '';
			}else{
				$isi_file = '';
			}


			@endphp
			<tr>
				<td>{{$k['number']}}.{{$s['number']}}.{{$no_tambahan}}</td>
				<td width="25%"><a href="javascript:void(0)" onclick="modal_kegiatan('{{$ma->id_mitra}}')">{{$ma->nama_mitra}}</a></td>
				<td>
					@if((empty($berkas) || (!empty($berkas) && $berkas->status!='Selesai')) && $tahun==date('Y'))
					<input type="url" name="{{$nama}}" class="form-control" style="border:1px solid #ccc" placeholder="URL Google Drive disini..." value="{{$isi_file}}">
					@else
					{{$isi_file}}
					@endif
				</td>
				<td width="20%">
					@if((empty($berkas) || (!empty($berkas) && $berkas->status!='Selesai')) && $tahun==date('Y'))
					<a href="javascript:void(0)" class="btn btn-sm btn-primary" onclick="simpan_berkas('{{$nama}}','kriteria_pendukung','form4')">Simpan</a>
					@if($isi_file!='')
					<a href="{{$isi_file}}" target="_blank" class="btn btn-sm btn-warning">Lihat</a>
					@endif

					@if(in_array($k['number'],['A','B','C','D','E','F','G','H','I']))
					<a href="javascript:void(0)" class="btn btn-sm btn-danger" onclick="hapus_mitra('{{$ma->id}}')">Hapus</a>
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
			{{-- END KEGIATAN --}}

			@endforeach


			@if(in_array($k['number'],['A']))
			
			@if((empty($berkas) || (!empty($berkas) && $berkas->status!='Selesai')) && $tahun==date('Y'))
			<tr style="background: #fcc">
				<td></td>
				<td colspan="3">
					Jika tidak ada dalam list bisa pilih lainnya dan ketikkan nama mitra.
					<select class="form-control mitra-chosen mitra_apresiasi{{$k['number']}}_{{$s['number']}}" onchange="mitra_lain(this,'{{$k['number']}}--{{$s['number']}}')">
						<option value="">..:: Pilih Mitra ::..</option>
						@foreach($mitra_daftar as $ma)
						<option value="{{$k['number']}}--{{$s['number']}}--{{$ma->id_mitra}}">{{$ma->nama_mitra}}</option>
						@endforeach
						<option value="{{$k['number']}}--{{$s['number']}}--lainnya">Lainnya</option>
					</select>
					<div class="mitra-hide mitra_lain{{$k['number']}}--{{$s['number']}}">
						<input type="text" name="mitra_lain{{$k['number']}}_{{$s['number']}}" class="form-control" placeholder="Tuliskan Mitra yang bekerja sama di sini">
					</div>
					<a href="javascript:void(0)" class="btn btn-sm btn-primary" onclick="tambah_mitra('{{$k['number']}}','{{$s['number']}}')"><i class="fa fa-plus"></i> Tambahkan</a>
				</td>
			</tr>
			@endif
			@endif
			@if(in_array($k['number'],['A','B','C','D','E','F','G','H','I']))

			@php
			$program = $kegiatan->pluck('tahun','nama_program')->sort();
			@endphp
			
			@if((empty($berkas) || (!empty($berkas) && $berkas->status!='Selesai')) && $tahun==date('Y'))
			<tr style="background: #fcc">
				<td></td>
				<td colspan="3">
					<select class="form-control mitra-chosen kegiatan_apresiasi{{$k['number']}}_{{$s['number']}}">
						<option value="">..:: Pilih Kegiatan ::..</option>
						@foreach($program as $prog=>$thp)
						<optgroup label="{{$prog}} (Tahun {{$thp}})">
							@php
							$keg = $kegiatan->where('nama_program',$prog)->sortBy('nama_kegiatan');
							@endphp
							@foreach($keg as $kg)
							<option value="{{$k['number']}}--{{$s['number']}}--{{$kg->id_kegiatan}}">{{$kg->nama_kegiatan}}</option>
							@endforeach
						</optgroup>
						@endforeach
					</select>
					<a href="javascript:void(0)" class="btn btn-sm btn-primary" onclick="tambah_kegiatan('{{$k['number']}}','{{$s['number']}}')"><i class="fa fa-plus"></i> Tambahkan</a>
				</td>
			</tr>
			@endif
			@endif

			@endforeach
			@endforeach
			@if((empty($berkas) || (!empty($berkas) && $berkas->status!='Selesai')) && $tahun==date('Y'))
			<tr>
				<td colspan="5">&nbsp;</td>
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
					<a href="javascript:void(0)" class="btn btn-sm btn-success" onclick="simpan_berkas('status','kriteria_pendukung','form4','select')">Simpan</a>
				</td>
			</tr>
			@endif
		</tbody>
	</table>
</div>
<script type="text/javascript">
	$(document).ready(function(){
		$('.mitra-chosen').chosen();
		$('.mitra-hide').hide();
	});

	function mitra_lain(ini,id){
		console.log(ini.value+' '+id+'');
		if(ini.value==id+'--lainnya'){
			$('.mitra_lain'+id).show();
		}else{
			$('.mitra_lain'+id).hide();
		}
	}

	function tambah_mitra(id,sub_id){
		var mitra_id = $('.mitra_apresiasi'+id+'_'+sub_id).val();
		var mitra_lain = $('input[name=mitra_lain'+id+'_'+sub_id+']').val();
		var data = {
			mitra_id:mitra_id,
			mitra_lain:mitra_lain,
			jenis:'mitra',
		};

		if(mitra_id==''){
			swal('Whooops','Pilih kegiatan lebih dhulu','error');
		}else{
			$.post('{{route('bunda-lomba-simpan_mitra',['tahun'=>$tahun])}}',data,function(data){
				if(data.code=='200'){
					formnya('form4');
				}
			});
		}
	}

	function tambah_kegiatan(id,sub_id){
		var mitra_id = $('.kegiatan_apresiasi'+id+'_'+sub_id).val();
		var data = {
			mitra_id:mitra_id,
			jenis:'kegiatan',
		};

		if(mitra_id==''){
			swal('Whooops','Pilih kegiatan lebih dhulu','error');
		}else{
			$.post('{{route('bunda-lomba-simpan_mitra',['tahun'=>$tahun])}}',data,function(data){
				if(data.code=='200'){
					formnya('form4');
				}
			});
		}
	}

	function hapus_mitra(id){
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
				$.post('{{route('bunda-lomba-hapus_mitra',['tahun'=>$tahun])}}',data,function(data){
					if(data.code=='200'){
						formnya('form4');
					}
				});
			}else{
			}
		})
	}
</script>