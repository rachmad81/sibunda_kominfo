<div class="block">
	<div class="block-header block-header-default row no-margin">
		<h3 class="block-title">Data anak belum masuk sekolah</h3>
	</div>
	<div class="block-content block-content-full">
		<div class="row">
			<div class="col-lg-12" style="overflow: auto">
				<div>
					<center>
						<h1 class="judul" style="display:none;">Detail anak</h1>
					</center>
				</div>
			</div>
		</div>
		<div class="row">
			@php
			$tidak_sekolah = [
				'Faktor Ekonomi'=>['Biaya pendaftaran/masuk terlalu mahal','Biaya SPP/iuran bulanan tidak terjangkau ','Biaya transportasi ke PAUD terlalu tinggi','Biaya seragam dan perlengkapan sekolah terlalu mahal','Orang tua bekerja dan tidak ada yang mengantar-jemput anak','Lainnya'],
				'Faktor Aksesibilitas / Jarak'=>['Lokasi PAUD terlalu jauh dari tempat tinggal (> 2 km)','Tidak tersedia transportasi yang aman ke PAUD','Kondisi jalan tidak aman/sulit dilalui','Tidak ada PAUD di sekitar tempat tinggal','Jumlah PAUD terbatas sehingga tidak dapat menampung semua anak','PAUD terdekat sudah penuh/tidak menerima siswa baru','Lainnya'],
				'Faktor Sosial Budaya'=>['Persepsi bahwa pendidikan di rumah sudah cukup untuk anak usia 5-7 tahun','Pandangan bahwa anak masih terlalu kecil untuk sekolah','Kekhawatiran terhadap keamanan anak di PAUD','Pendidikan PAUD dianggap kurang penting','Tradisi/adat yang tidak mendukung pendidikan formal pada usia dini','Ketakutan terhadap bullying atau perlakuan tidak menyenangkan','Lainnya'],
				'Faktor kondisi khusus anak (kesehatan/disabilitas/inklusi)'=>['Anak memiliki kebutuhan khusus namun tidak ada PAUD inklusi yang sesuai','Anak memiliki kondisi kesehatan tertentu yang menghambat untuk bersekolah','Anak memiliki trauma terhadap lingkungan sekolah','Anak mengalami kesulitan bersosialisasi','Lainnya'],
				'Alasan Administratif'=>['Tidak memiliki dokumen yang diperlukan (Akta Kelahiran, KK, dll)','Proses pendaftaran terlalu rumit','Keluarga baru pindah ke lokasi saat ini','Status kependudukan yang belum jelas','Lainnya'],
				'Lainnya'=>'',
			];
			@endphp
			<div class="col-lg-12" style="overflow: auto">
				<a href="javascript:void(0)" class="btn btn-sm btn-warning" onclick="kembali()">Kembali</a>
				<br>
				<br>
				Klik <a href="javascript:void(0)" class="btn btn-primary">Pakai Data Ini</a> untuk melengkapi form dengan data sesuai yang ditampilkan pada tabel diatasnya. 
				<form class="form_tambah">
					<table class="table table-bordered table-striped">
						<tr>
							<td style="width: 40%">NIK</td>
							<td>
								<input type="text" name="nik" class="form-control">
								<input type="hidden" name="json_param" class="form-control">
								<a href="javascript:void(0)" class="btn btn-sm btn-primary" onclick="cari_nik()">Cek</a>
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<div class="status_pencarian" style="overflow: auto"></div>
							</td>
						</tr>
						<tr>
							<td>Nama</td>
							<td>
								<input type="text" name="nama" class="form-control tempat_nama">
							</td>
						</tr>
						<tr>
							<td>Tempat lahir</td>
							<td>
								<input type="text" name="tempat_lahir" class="form-control tempat_tempat_lahir">
							</td>
						</tr>
						<tr>
							<td>Tanggal lahir</td>
							<td>
								<input type="date" name="tgl_lahir" class="form-control tempat_tgl_lahir">
							</td>
						</tr>
						<tr>
							<td>Kelamin</td>
							<td>
								<input type="text" name="kelamin" class="form-control tempat_kelamin">
							</td>
						</tr>
						<tr>
							<td>Alamat</td>
							<td>
								<input type="text" name="alamat" class="form-control tempat_alamat">
							</td>
						</tr>
						<tr>
							<td>RT/RW</td>
							<td>
								<div class="row">
									<div class="col-lg-5">
										<input type="text" name="rt" class="form-control tempat_rt">
									</div>
									/
									<div class="col-lg-5">
										<input type="text" name="rw" class="form-control tempat_rw">
									</div>
								</div>
							</td>
						</tr>
						<tr>
							<td>Kelurahan/Kecamatan</td>
							<td>
								<div class="row">
									<div class="col-lg-5">
										<input type="text" name="kelurahan" class="form-control tempat_kelurahan">
									</div>
									/
									<div class="col-lg-5">
										<input type="text" name="kecamatan" class="form-control tempat_kecamatan">
									</div>
								</div>
							</td>
						</tr>
						<tr>
							<td>Status alamat sesuai KK</td>
							<td>
								<label><input type="radio" name="status_alamat" value="Sesuai" onclick="ganti_status('alamat')"> Sesuai KK</label><br>
								<label><input type="radio" name="status_alamat" value="Tidak sesuai" onclick="ganti_status('alamat')">Tidak sesuai KK</label>
							</td>
						</tr>
						<tr class="ta_detail_alamat">
							<td>Catatan alamat</td>
							<td>
								<textarea name="detail_alamat" class="form-control" onkeyup="textAreaAdjust(this)"></textarea>
							</td>
						</tr>
						<tr>
							<td>Status Sekolah</td>
							<td>
								<label><input type="radio" name="status_sekolah" value="Sudah" onclick="ganti_status('sekolah')"> Sudah sekolah</label><br>
								<label><input type="radio" name="status_sekolah" value="Tidak sekolah" onclick="ganti_status('sekolah')">Tidak sekolah</label><br>
								<label><input type="radio" name="status_sekolah" value="Akan sekolah" onclick="ganti_status('sekolah')"> Akan sekolah</label>
							</td>
						</tr>
						<tr class="ta_detail_sekolah">
							<td>Catatan sekolah</td>
							<td>
								<textarea name="detail_sekolah" class="form-control" onkeyup="textAreaAdjust(this)"></textarea>
							</td>
						</tr>
						<tr class="ta_detail_tidak_sekolah">
							<td>Alasan tidak sekolah</td>
							<td>
								@php
								$arr_key = array_keys($tidak_sekolah);
								unset($arr_key[array_search('Lainnya', $arr_key)]);
								@endphp
								<select class="form-control" name="detail_tidak_sekolah" onchange="ganti_tidak_sekolah(this)">
									@foreach($tidak_sekolah as $k=>$v)
									<option>{{$k}}</option>
									@endforeach
								</select>
								<textarea name="detail_tidak_sekolah_lainnya" class="form-control" onkeyup="textAreaAdjust(this)"></textarea>
							</td>
						</tr>
						<tr class="ta_detail_tidak_sekolah1">
							<td>Detail alasan</td>
							<td>
								<select class="form-control" name="detail_alasan_tidak_sekolah" onchange="ganti_tidak_sekolah_lainnya(this)">
									
								</select>
								<textarea name="detail_alasan_tidak_sekolah_lainnya" class="form-control" onkeyup="textAreaAdjust(this)"></textarea>
							</td>
						</tr>
						<tr>
							<td>Bukti survei</td>
							<td>
								<input type="text" name="bukti_survei" class="form-control" value="{{$siswa->bukti_survei}}">
								<i>Upload link gdrive, pastikan settingan sudah di bagikan publik</i>
							</td>
						</tr>
						<tr>
							<td>Status Verifikasi</td>
							<td>
								<label><input type="radio" name="status_verifikasi" value="Draft"> Draft</label><br>
								<label><input type="radio" name="status_verifikasi" value="Selesai"> Selesai</label>
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<a href="javascript:void(0)" class="btn btn-primary form-control" onclick="simpan()">Simpan</a>
							</td>
						</tr>
					</table>
				</form>
				<a href="javascript:void(0)" class="btn btn-sm btn-warning" onclick="kembali()">Kembali</a>
			</div>
		</div>
	</div>
</div>
<script>
	function textAreaAdjust(element) {
		element.style.height = "1px";
		element.style.height = (25+element.scrollHeight)+"px";
	}

	var tidak_sekolah_karena = JSON.parse('{!! json_encode($tidak_sekolah) !!}');
	$(document).ready(function(){
		textAreaAdjust($('textarea[name=detail_alamat]')[0]);
		textAreaAdjust($('textarea[name=detail_sekolah]')[0]);
		ganti_status('alamat');
		ganti_status('sekolah');
		ganti_tidak_sekolah($('select[name=detail_tidak_sekolah]')[0]);
		ganti_tidak_sekolah_lainnya($('select[name=detail_alasan_tidak_sekolah]')[0]);
	});

	{{-- $.fn.json_beautify= function() {
		this.each(function(){
			var el = $(this),
			obj = JSON.parse(el.val()),
			pretty = JSON.stringify(obj, undefined, 4);
			el.val(pretty);
		});
	}; --}}

	{{-- $('textarea').json_beautify(); --}}

	function cari_nik(){
		var nik = $('input[name=nik]').val();
		$('input[name=json_param]').val('');
		$('.tempat_nama').val('');
		$('.tempat_tempat_lahir').val('');
		$('.tempat_tgl_lahir').val('');
		$('.tempat_kelamin').val('');
		$('.tempat_alamat').val('');
		$('.tempat_rt').val('');
		$('.tempat_rw').val('');
		$('.tempat_kelurahan').val('');
		$('.tempat_kecamatan').val('');
		if(nik.length<16){
			swal('Whooops','panjang karakter harus 16','error');
		}else{
			$.post('{{route('bunda-monitoring-blm_sekolah-cari_nik')}}',{nik:nik},function(data){
				$('.status_pencarian').html(data.content);
				if(data.anak_lengkap.length!=0){
					$('input[name=status_alamat][value='+data.anak_lengkap[0]['status_alamat']+']').prop('checked',true);
					$('textarea[name=detail_alamat]').val(data.anak_lengkap[0]['detail_alamat']);
					$('input[name=status_sekolah][value='+data.anak_lengkap[0]['status_sekolah']+']').prop('checked',true);
					console.log('sini'+data.anak_lengkap[0]['status_sekolah']);
					$('textarea[name=detail_sekolah]').val(data.anak_lengkap[0]['detail_sekolah']);
					$('select[name=detail_tidak_sekolah][value='+data.anak_lengkap[0]['detail_tidak_sekolah']+']').prop('selected',true);
					$('textarea[name=detail_tidak_sekolah_lainnya]').val(data.anak_lengkap[0]['detail_tidak_sekolah_lainnya']);
					$('select[name=detail_alasan_tidak_sekolah][value='+data.anak_lengkap[0]['detail_alasan_tidak_sekolah']+']').prop('selected',true);
					$('textarea[name=detail_alasan_tidak_sekolah_lainnya]').val(data.anak_lengkap[0]['detail_alasan_tidak_sekolah_lainnya']);
					$('input[name=status_verifikasi][value='+data.anak_lengkap[0]['status_verifikasi']+']').prop('checked',true);
					ganti_status('alamat');
					ganti_status('sekolah');
					textAreaAdjust($('textarea[name=detail_alamat]')[0]);
					textAreaAdjust($('textarea[name=detail_sekolah]')[0]);
					ganti_tidak_sekolah($('select[name=detail_tidak_sekolah]')[0]);
					textAreaAdjust($('textarea[name=detail_tidak_sekolah_lainnya]')[0]);
					ganti_tidak_sekolah_lainnya($('select[name=detail_alasan_tidak_sekolah]')[0]);
					textAreaAdjust($('textarea[name=detail_alasan_tidak_sekolah_lainnya]')[0]);
				}else{
					$('input[name=status_alamat]').prop('checked',false);
					$('textarea[name=detail_alamat]').val('');
					$('input[name=status_sekolah]').prop('checked',false);
					$('textarea[name=detail_sekolah]').val('');
					$('select[name=detail_tidak_sekolah]').prop('selected',false);
					$('textarea[name=detail_tidak_sekolah_lainnya]').val('');
					$('select[name=detail_alasan_tidak_sekolah]').prop('selected',false);
					$('textarea[name=detail_alasan_tidak_sekolah_lainnya]').val('');
					$('input[name=status_verifikasi]').prop('checked',false);
				}
			});
		}
	}

	function ganti_status(bagian){
		var status = $('input[name=status_'+bagian+']:checked').val();
		$('.ta_detail_tidak_sekolah').hide();
		$('.ta_detail_tidak_sekolah1').hide();
		if(status=='Tidak sesuai' || status=='Sudah' || status=='Akan sekolah'){
			$('.ta_detail_'+bagian).show();
		}else if(status=='Tidak sekolah'){
			$('.ta_detail_tidak_sekolah').show();
			$('.ta_detail_tidak_sekolah1').show();
			$('.ta_detail_'+bagian).hide();
		}else{
			$('.ta_detail_'+bagian).hide();
		}
	}

	function ganti_tidak_sekolah(ini){
		var status = ini.value;
		var status_sekolah = $('input[name=status_sekolah]:checked').val();
		var html = '';
		$('.ta_detail_tidak_sekolah1').hide();
		if(status=='Lainnya'){
			$('textarea[name=detail_tidak_sekolah_lainnya]').show();
		}else{
			$('textarea[name=detail_tidak_sekolah_lainnya]').hide();
			
			if(status_sekolah=='Tidak sekolah'){
				$('.ta_detail_tidak_sekolah1').show();
			}

			var nilai = '';

			$.each(tidak_sekolah_karena[status],function(k,v){
				if(tidak_sekolah_karena[status].indexOf(nilai)!=-1){
					if(v==nilai){
						html+='<option selected>'+v+'</option>';
					}else{
						html+='<option>'+v+'</option>';
					}
				}else{
					if(nilai!='' && v=='Lainnya'){
						html+='<option selected>'+v+'</option>';
					}else{
						html+='<option>'+v+'</option>';
					}
				}
			});

		}
		$('select[name=detail_alasan_tidak_sekolah]').html(html);
	}

	function ganti_tidak_sekolah_lainnya(ini){
		var status = ini.value;
		if(status=='Lainnya'){
			$('textarea[name=detail_alasan_tidak_sekolah_lainnya]').show();
		}else{
			$('textarea[name=detail_alasan_tidak_sekolah_lainnya]').hide();
		}
	}

	function pakai_data_ini(data_baris){
		var baris = JSON.parse(atob(data_baris));


		$('.tempat_nama').val(baris.nama);
		$('.tempat_tempat_lahir').val(baris.tempat_lahir);
		$('.tempat_tgl_lahir').val(baris.tgl_lahir);
		$('.tempat_kelamin').val(baris.kelamin);
		$('.tempat_alamat').val(baris.alamat);
		$('.tempat_rt').val(baris.rt);
		$('.tempat_rw').val(baris.rw);
		$('.tempat_kelurahan').val(baris.kelurahan);
		$('.tempat_kecamatan').val(baris.kecamatan);
		
		$('input[name=json_param]').val(data_baris);
	}

	function simpan(){
		var data = new FormData($('.form_tambah')[0]);
		if(data.get('nik')=='' || data.get('tempat_nama_siswa')=='' || data.get('tempat_ttl')=='' || data.get('tempat_alamat')==''){
			swal('Whooops','Mohon maaf lengkapi data dahulu.','warning');
		}else{
			$.ajax({
				url: '{{route('bunda-monitoring-blm_sekolah-simpan_tambah')}}',
				type: 'POST',
				data: data,
				contentType: false,
				processData: false,
				success: function(data){
					if(data.code==200){
						swal('Success','Berhasil disimpan','success');
						kembali();
						get_data();
					}else{
						swal('Whoooops','Gagal disimpan','error');
					}
				},
				fail: function(){
					swal('Whoooops','Gagal disimpan','error');
				}
			});
		}
	}
</script>