<h4>IDENTITAS CALON PENERIMA APRESIASI BUNDA PAUD KECAMATAN DAN KELURAHAN KOTA SURABAYA TAHUN {{$tahun}}</h4>
<div class="table-responsive">
	<style type="text/css">
		input[type=text]:focus {
			border: 1px solid #000 !important;
		}
	</style>
	<form class="form-identitas">
		<table id="dataTable" class="table table-bordered table-striped">
			<thead>
				<tr>
					<th>Uraian</th>
					<th>Pengisian</th>
					<th>Keterangan</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td style="width:40%">
						<b>Nama</b>
						@if($tahun==date('Y') && !empty($identitas) && $identitas->nama!=$pokja->nama)
						<br><i style="font-size: 11pt;color: red">* Ada perbedaan antara data yang sudah disimpan dan data di aplikasi.</i>
						<br><a href="javascript:void(0)" onclick="sesuaikan('nama')">Sesuaikan Aplikasi</a>
						@endif
					</td>
					<td>
						@if(!empty($identitas))
						<span class="nama">{{$identitas->nama}}</span>
						<input type="hidden" name="nama" class="form-control" readonly value="{{$identitas->nama}}">
						@else
						{{$pokja->nama}}
						<input type="hidden" name="nama" class="form-control" readonly value="{{$pokja->nama}}">
						@endif
					</td>
					<td rowspan="9" style="vertical-align: top;">
						<b>Informasi Penting</b><br>
						Jika ada ketidaksesuaian data, bisa edit di sini: <br>
						<a href="{{url('common/pokja/kelola/ubah')}}?id={{$pokja->id}}" class="btn btn-sm btn-info">Edit Profil</a>
						<a href="{{url('common/berkas')}}" class="btn btn-sm btn-info">Edit SK</a>
					</td>
				</tr>
				<tr>
					<td>
						<b>No SK Penetapan/Pengukuhan Bunda PAUD Kecamatan/Kelurahan / Tanggal SK</b>
						@if($tahun==date('Y') && !empty($identitas) && $identitas->no_sk!=$pokja->no_sk_penetapan_bunda)
						<br><i style="font-size: 11pt;color: red">* Ada perbedaan antara data yang sudah disimpan dan data di aplikasi.</i>
						<br><a href="javascript:void(0)" onclick="sesuaikan('no_sk')">Sesuaikan Aplikasi</a>
						@endif
					</td>
					<td>
						@if(!empty($identitas))
						<span class="no_sk">{{$identitas->no_sk}}</span>
						<input type="hidden" name="no_sk" class="form-control" readonly value="{{$identitas->no_sk}}">
						@else
						{{$pokja->no_sk_penetapan_bunda}}
						<input type="hidden" name="no_sk" class="form-control" readonly value="{{$pokja->no_sk_penetapan_bunda}}">
						@endif
					</td>
				</tr>
				<tr>
					<td>
						<b>No HP Bunda PAUD Kecamatan/Kelurahan</b>
						@if($tahun==date('Y') && !empty($identitas) && $identitas->telp_peserta!=$pokja->no_telp)
						<br><i style="font-size: 11pt;color: red">* Ada perbedaan antara data yang sudah disimpan dan data di aplikasi.</i>
						<br><a href="javascript:void(0)" onclick="sesuaikan('telp_bunda')">Sesuaikan Aplikasi</a>
						@endif
					</td>
					<td>
						@if(!empty($identitas))
						<span class="telp_bunda">{{$identitas->telp_peserta}}</span>
						<input type="hidden" name="telp_bunda" class="form-control" readonly value="{{$identitas->telp_peserta}}">
						@else
						{{$pokja->no_telp}}
						<input type="hidden" name="telp_bunda" class="form-control" readonly value="{{$pokja->no_telp}}">
						@endif
					</td>
				</tr>
				<tr>
					<td>
						<b>Instansi Pengusul Camat/Lurah</b>
						{{-- @if($tahun==date('Y') && !empty($identitas) && $identitas->instansi!=$pokja->unit_kerja)
						<br><i style="font-size: 11pt;color: red">* Ada perbedaan antara data yang sudah disimpan dan data di aplikasi.</i>
						<br><a href="javascript:void(0)" onclick="sesuaikan('instansi')">Sesuaikan Aplikasi</a>
						@endif --}}
					</td>
					<td>
						@if(!empty($identitas))
						<input type="text" name="instansi" class="form-control" value="{{$identitas->instansi}}">
						@else
						<input type="text" name="instansi" class="form-control" value="">
						@endif
					</td>
				</tr>
				<tr>
					<td>
						<b>Alamat Instansi</b>
						@if($tahun==date('Y') && !empty($identitas) && $identitas->alamat_instansi!=$pokja->alamat_kerja)
						<br><i style="font-size: 11pt;color: red">* Ada perbedaan antara data yang sudah disimpan dan data di aplikasi.</i>
						<br><a href="javascript:void(0)" onclick="sesuaikan('alamat_instansi')">Sesuaikan Aplikasi</a>
						@endif
					</td>
					<td>
						@if(!empty($identitas))
						<span class="alamat_instansi">{{$identitas->alamat_instansi}}</span>
						<input type="hidden" name="alamat_instansi" class="form-control" readonly value="{{$identitas->alamat_instansi}}">
						@else
						{{$pokja->alamat_kerja}}
						<input type="hidden" name="alamat_instansi" class="form-control" readonly value="{{$pokja->alamat_kerja}}">
						@endif
					</td>
				</tr>
				@if(Auth::User()->tingkat=='kecamatan')
				<tr>
					<td><b>Kecamatan Instansi</b></td>
					<td>
						<input type="text" name="kecamatan_instansi" class="form-control" readonly value="{{Auth::User()->kecamatan->kecamatan_dispenduk}}">
					</td>
				</tr>
				@elseif(Auth::User()->tingkat=='kelurahan')
				<tr>
					<td><b>Kelurahan Instansi</b></td>
					<td>
						<input type="text" name="kelurahan_instansi" class="form-control" readonly value="{{Auth::User()->kelurahan->kelurahan_dispenduk}}">
					</td>
				</tr>
				@else
				<tr>
					<td><b>Instansi</b></td>
					<td>
						<input type="text" class="form-control" readonly value="Kota Surabaya">
					</td>
				</tr>
				@endif
				<tr>
					<td><b>No. HP Pengusul</b></td>
					<td>
						<input type="text" name="telp_pengusul" class="form-control" value="{{(!empty($identitas)) ? $identitas->telp_pengusul : ''}}">
					</td>
				</tr>
				<tr>
					<td><b>Alamat e-mail Pengusul</b></td>
					<td>
						<input type="text" name="email_pengusul" class="form-control" value="{{(!empty($identitas)) ? $identitas->email_pengusul : ''}}">
					</td>
				</tr>
				<tr>
					<td colspan="2">
						@if($tahun==date('Y'))
						<a href="javascript:void(0)" class="btn btn-sm btn-primary" onclick="simpan_identitas()">Simpan</a>
						@endif
					</td>
				</tr>
			</tbody>
		</table>
	</form>
</div>

<script type="text/javascript">
	function simpan_identitas(){
		var data = $('.form-identitas').serialize(0);
		$.post("{{route('bunda-lomba-simpan_identitas',['tahun'=>$tahun])}}",data,function(data){
			if(data.code=='200'){
				location.reload();
				swal('Success',data.message,'success');
				formnya('form1');
			}else{
				swal('Whoops',data.message,'error');
			}
		});
	}

	var data_identitas;
	identitas = {
		nama:'{{$pokja->nama}}',
		no_sk:'{{$pokja->no_sk_penetapan_bunda}}',
		tgl_sk:'{{$pokja->tgl_sk_penetapan_bunda}}',
		telp_bunda:'{{$pokja->no_telp}}',
		instansi:'{{$pokja->unit_kerja}}',
		alamat_instansi:'{{$pokja->alamat_kerja}}',
	}

	function sesuaikan(nama){
		var isinya = '';
		if(nama=='nama'){
			isinya = identitas.nama;
		}else if(nama=='no_sk'){
			isinya = identitas.no_sk;
		}else if(nama=='telp_bunda'){
			isinya = identitas.telp_bunda;
		}else if(nama=='instansi'){
			isinya = identitas.instansi;
		}else if(nama=='alamat_instansi'){
			isinya = identitas.alamat_instansi;
		}else if(nama=='tgl_sk'){
			isinya = identitas.tgl_sk;
		}
		$('input[name='+nama+']').val(isinya);
		$('.'+nama).html(isinya);
	}

</script>