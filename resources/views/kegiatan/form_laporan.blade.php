<div class="block">
	<div class="block-header block-header-default row no-margin">
		<h3 class="block-title">{{$title}}</h3>
	</div>
	<div class="alert alert-warning">
		<div class="row">
			<div class="col-lg-12">
				Berkas pendukung diupload di Google drive dan di<i>Share</i> (bagikan) ke publik, copy dan paste link google drive ke form pengisian.
			</div>
		</div>
		<div class="row">
			<div class="col-lg-3">
				<a href="javascript:void(0)" data-toggle="modal" data-target="#myModalPanduan" class="btn btn-sm btn-danger">Panduan Isi Link</a>
				<a href="https://drive.google.com/" target="_blank" class="btn btn-sm btn-primary">Google Drive</a>
			</div>
		</div>
	</div>
	<div>
		@include('bunda.lomba.panduan')
	</div>
	<div class="block-content block-content-full">
		<form id="form_simpan" enctype="multipart/form-data">
			<div class="row">
				<input type="hidden" name="id_kegiatan" value="@if(!empty($kegiatan)){{$kegiatan->id_kegiatan}}@endif">

				<div class="col-lg-4 col-md-4">Hasil Kegiatan</div>
				<div class="col-lg-8 col-md-8" style="margin-bottom: 10px"><textarea class="form-control" name="hasil_kegiatan">@if(!empty($kegiatan)){{$kegiatan->hasil_kegiatan}}@endif</textarea></div>

				<div class="col-lg-4 col-md-4">
					Foto <i style="color: red">*</i>
					<br><i style="font-size: 10pt;color: red">* Upload link Google Drive</i>
				</div>
				<div class="col-lg-8 col-md-8" style="margin-bottom: 10px">
					{{-- <input type="file" class="form-control" name="foto" accept="image/*"> --}}
					{{-- <i style="font-size: 10pt;font-weight: bold;">* Ukuran file 300KB</i> --}}
					<input type="text" class="form-control" name="foto" value="{{$kegiatan->foto}}">
					@php
					$foto_exists = 0;
					$url = asset('logo/image-icon.png');
					if($kegiatan->foto!=''){
						try {
							if(Storage::exists($kegiatan->foto)){
								$url = route('storage',[base64_encode($kegiatan->foto),'1']);
								$foto_exists = 1;
							}else{
								$url = $kegiatan->foto;
							}
						} catch (\Exception $e) {
							$url = '';
						}
					}
					@endphp
					<input type="hidden" id="foto_exists" value="{{$foto_exists}}">
					<div class="row">
						<div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
							{{-- <a href="{{$url}}" target="_blank"><img src="{{$url}}" style="width: 100%"></a> --}}
							<a href="{{$url}}" target="_blank" class="btn btn-sm btn-primary">View</a>
						</div>
					</div>
				</div>

				<div class="col-lg-4 col-md-4">Link Sosial Media</div>
				<div class="col-lg-8 col-md-8" style="margin-bottom: 10px">
					<input type="url" class="form-control" name="url_video" value="@if(!empty($kegiatan)){{$kegiatan->url_video}}@endif">
					@if(!empty($kegiatan) && $kegiatan->url_video!='')
					<a href="{{$kegiatan->url_video}}" target="_blank" class="btn btn-sm btn-primary">View</a>
					@endif
				</div>

				@php
				if(!empty($kegiatan)){
					$file_pendukung = json_decode($kegiatan->file_pendukung);
				}else{
					$file_pendukung = '';
				}
				@endphp

				<div class="col-lg-4 col-md-4"><span>KAK</span> <span style="font-size: 12px;">(Link URL GDrive dengan Format PDF)</span></div>
				<div class="col-lg-8 col-md-8" style="margin-bottom: 10px">
					<input placeholder="Link URL dari Google Drive" type="url" class="form-control" name="kak" value="@if($file_pendukung!='') {{$file_pendukung->kak}} @endif">
					@if($file_pendukung!='' && $file_pendukung->kak!='')
					<a href="{{$file_pendukung->kak}}" target="_blank" class="btn btn-primary btn-sm">View</a>
					@endif
				</div>

				<div class="col-lg-4 col-md-4"><span>Undangan</span> <span style="font-size: 12px;">(Link URL GDrive dengan Format PDF)</span></div>
				<div class="col-lg-8 col-md-8" style="margin-bottom: 10px">
					<input placeholder="Link URL dari Google Drive" type="url" class="form-control" name="undangan" value="@if($file_pendukung!='') {{$file_pendukung->undangan}} @endif">
					@if($file_pendukung!='' && $file_pendukung->undangan!='')
					<a href="{{$file_pendukung->undangan}}" target="_blank" class="btn btn-primary btn-sm">View</a>
					@endif
				</div>

				<div class="col-lg-4 col-md-4"><span>Notulen</span> <span style="font-size: 12px;">(Link URL GDrive dengan Format PDF)</span></div>
				<div class="col-lg-8 col-md-8" style="margin-bottom: 10px">
					<input placeholder="Link URL dari Google Drive" type="url" class="form-control" name="notulen" value="@if($file_pendukung!='') {{$file_pendukung->notulen}} @endif">
					@if($file_pendukung!='' && $file_pendukung->notulen!='')
					<a href="{{$file_pendukung->notulen}}" target="_blank" class="btn btn-primary btn-sm">View</a>
					@endif
				</div>

				<div class="col-lg-4 col-md-4"><span>Absensi</span> <span style="font-size: 12px;">(Link URL GDrive dengan Format PDF)</span></div>
				<div class="col-lg-8 col-md-8" style="margin-bottom: 10px">
					<input placeholder="Link URL dari Google Drive" type="url" class="form-control" name="absensi" value="@if($file_pendukung!='') {{$file_pendukung->absensi}} @endif">
					@if($file_pendukung!='' && $file_pendukung->absensi!='')
					<a href="{{$file_pendukung->absensi}}" target="_blank" class="btn btn-primary btn-sm">View</a>
					@endif
				</div>

				<div class="col-lg-4 col-md-4"><span>Foto geotag</span> <span style="font-size: 12px;">(Link URL GDrive dengan Format Gambar)</span></div>
				<div class="col-lg-8 col-md-8" style="margin-bottom: 10px">
					<input placeholder="Link URL dari Google Drive" type="url" class="form-control" name="foto_geo" value="@if($file_pendukung!='') {{$file_pendukung->foto_geo}} @endif">
					@if($file_pendukung!='' && $file_pendukung->foto_geo!='')
					<a href="{{$file_pendukung->foto_geo}}" target="_blank" class="btn btn-primary btn-sm">View</a>
					@endif
				</div>

			</div>
			<div>
				<a href="javascript:void(0)" class="btn btn-outline-primary" onclick="simpan_laporan()"> Simpan</a>
				<a href="javascript:void(0)" class="btn btn-outline-warning" onclick="kembali_form()"> Kembali</a>
			</div>
		</form>
		<i style="color:red">* ) Wajib diisi</i>
	</div>
</div>