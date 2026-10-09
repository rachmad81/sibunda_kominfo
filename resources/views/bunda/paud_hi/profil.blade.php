<!-- Modal -->
<div id="modal_profil" class="modal fade" role="dialog">
	<div class="modal-dialog modal-lg">

		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Profil HI PAUD</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<table id="table_profil" style="width: 100%;border-collapse: collapse;" border="1">
					<thead class="table_headers">
						<tr>
							<th style="width: 3%;">
								<center>NO</center>
							</th>
							<th>
								<center>PROFIL SEKOLAH</center>
							</th>
							<th colspan="4" style="width: 70%">
								<center>KETERANGAN</center>
							</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>
								<center>1</center>
							</td>
							<td> NPSN </td>
							<td colspan="4">
								{{$pengajuan->npsn}}
							</td>
						</tr>
						<tr>
							<td>
								<center>
									2
								</center>
							</td>
							<td>
								Nama Sekolah
							</td>
							<td colspan="4">
								{{$pengajuan->nama_sekolah}}
							</td>
						</tr>
						<tr>
							<td rowspan="3">
								<center>
									3
								</center>
							</td>
							<td rowspan="3">
								Alamat
							</td>
							<td colspan="4">
								{{$pengajuan->alamat}}
							</td>
						</tr>
						<tr>
							<td colspan="1">
								RT :
								{{$pengajuan->rt}}
							</td>
							<td colspan="3">
								RW :
								{{$pengajuan->rw}}
							</td>
						</tr>
						<tr>
							<div class="row" style="margin-left: 1%">
								<td colspan="1">
									Kelurahan :
									{{$pengajuan->kelurahan}}
								</td>
								<td colspan="3">
									Kecamatan :
									{{$pengajuan->kecamatan}}
								</td>
							</div>
						</tr>
						<tr>
							<td>
								<center>
									4
								</center>
							</td>
							<td>
								Nama Kepala Sekolah / Pengelola
							</td>
							<td colspan="4">
								{{$pengajuan->kepala_sekolah}}
							</td>
						</tr>
						<tr>
							<td>
								<center>
									5
								</center>
							</td>
							<td>
								Tahun Berdiri
							</td>
							<td colspan="4">
								{{$pengajuan->tahun_berdiri}}
							</td>
						</tr>
						<tr>
							<td>
								<center>
									6
								</center>
							</td>
							<td>
								Kepemilikan Lahan
							</td>
							<td colspan="4">
								{{$pengajuan->status_kepemilikan_lahan}}
							</td>
						</tr>
						<tr>
							<td>
								<center>
									7
								</center>
							</td>
							<td>
								Akreditasi (Tahun dan Nilai)
							</td>
							<td colspan="1">
								Tahun {{$pengajuan->akreditasi_tahun}}
							</td>
							<td colspan="3">
								Nilai {{$pengajuan->akreditasi_nilai}}
							</td>
						</tr>
						<tr>
							<td>
								<center>
									8
								</center>
							</td>
							<td>
								Masa Izin Operasional Berakhir
							</td>
							<td colspan="4">
								{{$pengajuan->tgl_berakhir_izin}}
							</td>
						</tr>
						<tr>
							<td rowspan="2">
								<center>
									9
								</center>
							</td>
							<td>
								Kurikulum
							</td>
							@php
							$kurikulum = ['','','','',''];
							$style = ['','','','',''];
							$arr_kurikulum = json_decode($pengajuan->kurikulum);
							
							if(in_array('Kurikulum 2013',$arr_kurikulum)){
								$kurikulum[0] = 'checked';
								$style[0] = "font-weight: bold;";
							}

							if(in_array('Kurikulum Darurat',$arr_kurikulum)){
								$kurikulum[1] = 'checked';
								$style[1] = "font-weight: bold;";
							}

							if(in_array('Kurikulum Merdeka - Mandiri Belajar',$arr_kurikulum)){
								$kurikulum[2] = 'checked';
								$style[2] = "font-weight: bold;";
							}

							if(in_array('Kurikulum Merdeka - Mandiri Berubah',$arr_kurikulum)){
								$kurikulum[3] = 'checked';
								$style[3] = "font-weight: bold;";
							}

							if(in_array('Kurikulum Merdeka - Mandiri Berbagi',$arr_kurikulum)){
								$kurikulum[4] = 'checked';
								$style[4] = "font-weight: bold;";
							}							
							
							@endphp
							<td colspan="4">
								<label style="{{$style[0]}}">
									<input type="checkbox" value="Kurikulum 2013" disabled {{$kurikulum[0]}}> Kurikulum 2013
								</label>
								<label style="{{$style[1]}}">
									<input type="checkbox" value="Kurikulum Darurat" disabled {{$kurikulum[1]}}> Kurikulum Darurat
								</label>
								<label style="{{$style[2]}}">
									<input type="checkbox" value="Kurikulum Merdeka - Mandiri Belajar" disabled {{$kurikulum[2]}}> Kurikulum Merdeka - Mandiri Belajar
								</label>
								<label style="{{$style[3]}}">
									<input type="checkbox" value="Kurikulum Merdeka - Mandiri Berubah" disabled {{$kurikulum[3]}}> Kurikulum Merdeka - Mandiri Berubah
								</label>
								<label style="{{$style[4]}}">
									<input type="checkbox" value="Kurikulum Merdeka - Mandiri Berbagi" disabled {{$kurikulum[4]}}> Kurikulum Merdeka - Mandiri Berbagi
								</label>
							</td>
						</tr>
						{{-- <tr>
							<td>
								Bagi kurikulum merdeka
							</td>
							@php
							$kurikulum = ['','',''];
							$style = ['','',''];
							switch ($pengajuan->kurikulum_merdeka) {
								case 'Mandiri Belajar':
								$kurikulum[0] = 'checked';
								$style[0] = "font-weight: bold;";
								break;
								case 'Mandiri Berubah':
								$kurikulum[1] = 'checked';
								$style[1] = "font-weight: bold;";
								break;
								case 'Mandiri Berbagi':
								$kurikulum[2] = 'checked';
								$style[2] = "font-weight: bold;";
								break;
								default:
								break;
							}
							@endphp
							<td colspan="1">
								<label style="{{$style[0]}}">									
									<input type="radio" value="Mandiri Belajar" disabled {{$kurikulum[0]}}> Mandiri Belajar
								</label>
							</td>
							<td colspan="1">
								<label style="{{$style[1]}}">									
									<input type="radio" value="Mandiri Berubah" disabled {{$kurikulum[1]}}> Mandiri Berubah
								</label>
							</td>
							<td colspan="2">
								<label style="{{$style[2]}}">									
									<input type="radio" value="Mandiri Berbagi" disabled {{$kurikulum[2]}}> Mandiri Berbagi
								</label>
							</td>
						</tr> --}}
						<tr>
							<td>
								Kurikulum Khusus
							</td>
							<td colspan="4">
								{{$pengajuan->kurikulum_khusus}}
							</td>
						</tr>
						<tr>
							<td rowspan="4">
								<center>
									10
								</center>
							</td>
							<td>
								Jumlah Siswa :
							</td>
							<td colspan="4">
							</td>
						</tr>
						<tr>
							<td>
								Kelompok A
							</td>
							<td colspan="1">
								L : {{$pengajuan->jumlah_siswa_kel_a_laki}}
							</td>
							<td colspan="1">
								P : {{$pengajuan->jumlah_siswa_kel_a_perempuan}}
							</td>
							<td colspan="2">
								Jumlah : {{$pengajuan->jumlah_siswa_kel_a_laki+$pengajuan->jumlah_siswa_kel_a_perempuan}}
							</td>
						</tr>
						<tr>
							<td>
								Kelompok B
							</td>
							<td colspan="1">
								L : {{$pengajuan->jumlah_siswa_kel_b_laki}}
							</td>
							<td colspan="1">
								P : {{$pengajuan->jumlah_siswa_kel_b_perempuan}}
							</td>
							<td colspan="2">
								Jumlah : {{$pengajuan->jumlah_siswa_kel_b_laki+$pengajuan->jumlah_siswa_kel_b_perempuan}}
							</td>
						</tr>
						<tr>
							<td>
								Anak Berkebutuhan Khusus (ABK)
							</td>
							<td colspan="1">
								L : {{$pengajuan->jumlah_siswa_abk_laki}}
							</td>
							<td colspan="1">
								P : {{$pengajuan->jumlah_siswa_abk_perempuan}}
							</td>
							<td colspan="2">
								Jumlah : {{$pengajuan->jumlah_siswa_abk_laki+$pengajuan->jumlah_siswa_abk_perempuan}}
							</td>
						</tr>
						<tr>
							<td>
								<center>
									11
								</center>
							</td>
							<td>
								Jumlah Guru / Tenaga Kependidikan
							</td>
							<td colspan="1">
								L : {{$pengajuan->jumlah_gtk_laki}}
							</td>
							<td colspan="1">
								P : {{$pengajuan->jumlah_gtk_perempuan}}
							</td>
							<td colspan="2">
								Jumlah : {{$pengajuan->jumlah_gtk_laki+$pengajuan->jumlah_gtk_perempuan}}
							</td>
						</tr>
						<tr>
							<td>
								<center>
									12
								</center>
							</td>
							<td>
								Pendidikan Terakhir Guru / Tenaga Kependidikan
							</td>
							<td colspan="1">
								SMP : {{$pengajuan->jumlah_pendidikan_gtk_smp}}
							</td>
							<td colspan="1">
								SMA : {{$pengajuan->jumlah_pendidikan_gtk_sma}}
							</td>
							<td colspan="2">
								Sarjana : {{$pengajuan->jumlah_pendidikan_gtk_sarjana}}
							</td>
						</tr>
						<tr>
							<td>
								<center>
									13
								</center>
							</td>
							<td>
								Status Kepegawaian
							</td>
							<td colspan="1">
								PNS : {{$pengajuan->pegawai_pns}}
							</td>
							<td colspan="1">
								Yayasan : {{$pengajuan->pegawai_yayasan}}
							</td>
							<td colspan="2">
								Lainnya : {{$pengajuan->pegawai_lainnya}}
							</td>
						</tr>
						<tr>
							<td>
								<center>
									14
								</center>
							</td>
							<td>
								Gedung Sekolah
							</td>
							@php
							$kurikulum = ['','',''];
							$style = ['','',''];
							switch ($pengajuan->gedung_sekolah) {
								case 'Milik Sendiri':
								$kurikulum[0] = 'checked';
								$style[0] = "font-weight: bold;";
								break;
								case 'Balai Rw':
								$kurikulum[1] = 'checked';
								$style[1] = "font-weight: bold;";
								break;
								case 'Lainnya':
								$kurikulum[2] = 'checked';
								$style[2] = "font-weight: bold;";
								break;
								default:
								break;
							}
							@endphp
							<td colspan="1">
								<label style="{{$style[0]}}">
								<input type="radio" value="Milik Sendiri" disabled {{$kurikulum[0]}}> Milik Sendiri
								</label>
							</td>
							<td colspan="1">
								<label style="{{$style[1]}}">
								<input type="radio" value="Balai Rw" disabled {{$kurikulum[1]}}> Balai Rw
								</label>
							</td>
							<td colspan="2">
								<label style="{{$style[2]}}">
								<input type="radio" value="Lainnya" disabled {{$kurikulum[2]}}> Lainnya
								</label>
							</td>
						</tr>
						<tr>
							<td>
								<center>
									15
								</center>
							</td>
							<td>
								Kondisi Gedung
							</td>
							@php
							$kurikulum = ['','','',''];
							$style = ['','','',''];
							switch ($pengajuan->kondisi_gedung) {
								case 'Baik':
								$kurikulum[0] = 'checked';
								$style[0] = "font-weight: bold;";
								break;
								case 'Rusak Ringan':
								$kurikulum[1] = 'checked';
								$style[1] = "font-weight: bold;";
								break;
								case 'Rusak Sedang':
								$kurikulum[2] = 'checked';
								$style[2] = "font-weight: bold;";
								break;
								case 'Rusak Berat':
								$kurikulum[3] = 'checked';
								$style[3] = "font-weight: bold;";
								break;
								default:
								break;
							}
							@endphp

							<td colspan="1">
								<label style="{{$style[0]}}">
								<input type="radio" value="Baik" disabled {{$kurikulum[0]}}> Baik
								</label>
							</td>
							<td colspan="1">
								<label style="{{$style[1]}}">
								<input type="radio" value="Rusak Ringan" disabled {{$kurikulum[1]}}> Rusak Ringan
								</label>
							</td>
							<td colspan="1">
								<label style="{{$style[2]}}">
								<input type="radio" value="Rusak Sedang" disabled {{$kurikulum[2]}}> Rusak Sedang
								</label>
							</td>
							<td colspan="1">
								<label style="{{$style[3]}}">
								<input type="radio" value="Rusak Berat" disabled {{$kurikulum[3]}}> Rusak Berat
								</label>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
			</div>
		</div>

	</div>
</div>
<style type="text/css">
	label{
		font-weight: 10;
	}
</style>
<script type="text/javascript">
	$(document).ready(function(){
		$('#modal_profil').modal({backdrop: 'static'});
	});
</script>