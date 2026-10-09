<div class="block">
	<div class="block-header block-header-default row no-margin">
		<h3 class="block-title">{{$title}}</h3>
	</div>
	<div class="block-content block-content-full">
		<form id="form_simpan" enctype="multipart/form-data">
			<div class="row">
				<input type="hidden" name="id_kegiatan" value="@if(!empty($kegiatan)){{$kegiatan->id_kegiatan}}@endif">
				<div class="col-lg-4 col-md-4">Tahun Program</div>
				<div class="col-lg-8 col-md-8" style="margin-bottom: 10px">
					<select class="form-control" name="tahun_form" onchange="get_program()">
						<?php
						$tahun_cari = ['2022','2023'];
						?>
						<option value="">-- Tahun Program --</option>
						@for($i=2022;$i<=date('Y');$i++)
						<option value="{{$i}}" @if(!empty($kegiatan) && $kegiatan->tahun==$i) selected @endif>{{$i}}</option>
						@endfor
					</select>
				</div>
				<div class="col-lg-4 col-md-4">Nama Program <i style="color: red">*</i></div>
				<div class="col-lg-8 col-md-8" style="margin-bottom: 10px">
					<select class="form-control" name="program_id">
						<option value="">-- Pilih Program --</option>
						@if($program!='')
						@foreach($program as $k=>$v)
						<option value="{{$v->id_program}}" @if(!empty($kegiatan) && $kegiatan->program_id==$v->id_program) selected @endif>{{$v->nama_kegiatan}}</option>
						@endforeach
						@endif
					</select>
				</div>

				<div class="col-lg-4 col-md-4">Nama Kegiatan <i style="color: red">*</i></div>
				<div class="col-lg-8 col-md-8" style="margin-bottom: 10px"><input type="text" class="form-control" name="nama_kegiatan" value="@if(!empty($kegiatan)){{$kegiatan->kegiatan}}@endif"></div>

				<div class="col-lg-4 col-md-4">Tempat Kegiatan <i style="color: red">*</i></div>
				<div class="col-lg-8 col-md-8" style="margin-bottom: 10px"><input type="text" class="form-control" name="tempat_kegiatan" value="@if(!empty($kegiatan)){{$kegiatan->tempat_kegiatan}}@endif"></div>

				<div class="col-lg-4 col-md-4">Tanggal Kegiatan <i style="color: red">*</i></div>
				<div class="col-lg-8 col-md-8" style="margin-bottom: 10px"><input type="text" autocomplete="off" class="form-control" name="tgl_kegiatan" value="@if(!empty($kegiatan)){{$kegiatan->tgl_kegiatan}}@endif"></div>

				<div class="col-lg-4 col-md-4">Jam Kegiatan <i style="color: red">*</i></div>
				<div class="col-lg-8 col-md-8" style="margin-bottom: 10px">
					<input type="text" autocomplete="off" onclick="$timepicker.open()" class="form-control" name="waktu_kegiatan" value="@if(!empty($kegiatan)){{$kegiatan->waktu_kegiatan}}@endif">
				</div>

				<div class="col-lg-4 col-md-4">Bentuk Kegiatan</div>
				<div class="col-lg-8 col-md-8" style="margin-bottom: 10px">
					<?php
					$bentuk = ['Daring','Luring'];
					?>
					<select class="form-control" name="bentuk_keg">
						<option value="">-- Pilih Bentuk Kegiatan --</option>
						@for($i=0;$i<count($bentuk);$i++)
						<option value="{{$bentuk[$i]}}" @if(!empty($kegiatan) && $kegiatan->bentuk_keg==$bentuk[$i]) selected @endif>{{$bentuk[$i]}}</option>
						@endfor						
					</select>
				</div>

				<div class="col-lg-4 col-md-4">Materi</div>
				<div class="col-lg-8 col-md-8" style="margin-bottom: 10px">
					<textarea class="form-control" name="materi">@if(!empty($kegiatan)){!!$kegiatan->materi!!}@endif</textarea>
				</div>

				<div class="col-lg-4 col-md-4">Pemateri</div>
				<div class="col-lg-8 col-md-8" style="margin-bottom: 10px"><input type="text" class="form-control" name="narsum" value="@if(!empty($kegiatan)){{$kegiatan->narsum}}@endif"></div>

				<div class="col-lg-4 col-md-4">Sumber Dana</div>
				<div class="col-lg-8 col-md-8" style="margin-bottom: 10px"><input type="text" class="form-control" name="sumber_dana" value="@if(!empty($kegiatan)){{$kegiatan->sumber_dana}}@endif"></div>

				<div class="col-lg-4 col-md-4">Sasaran Kegiatan <i style="color: red">*</i></div>
				<div class="col-lg-8 col-md-8" style="margin-bottom: 10px"><textarea class="form-control" name="sasaran_kegiatan">@if(!empty($kegiatan)){{$kegiatan->sasaran_kegiatan}}@endif</textarea></div>

				<div class="col-lg-4 col-md-4">Tujuan Kegiatan <i style="color: red">*</i></div>
				<div class="col-lg-8 col-md-8" style="margin-bottom: 10px"><textarea class="form-control" name="tujuan_kegiatan">@if(!empty($kegiatan)){{$kegiatan->tujuan_kegiatan}}@endif</textarea></div>

				<div class="col-lg-4 col-md-4">Target Output Kegiatan</div>
				<div class="col-lg-8 col-md-8" style="margin-bottom: 10px"><textarea class="form-control" name="target_out_kegiatan">@if(!empty($kegiatan)){{$kegiatan->target_out_kegiatan}}@endif</textarea></div>

			</div>
			<div>
				<a href="javascript:void(0)" class="btn btn-outline-primary" onclick="simpan_form()"> Simpan</a>
				<a href="javascript:void(0)" class="btn btn-outline-warning" onclick="kembali_form()"> Kembali</a>
			</div>
		</form>
		<i style="color:red">* ) Wajib diisi</i>
	</div>
</div>
<script type="text/javascript">
	$('input[name=tgl_kegiatan]').datepicker({
		format: 'yyyy-mm-dd',
		uiLibrary: 'bootstrap',
	});
	var $timepicker = $('input[name=waktu_kegiatan]').timepicker({
		mode: '24hr',
		uiLibrary: 'bootstrap',
	});
</script>