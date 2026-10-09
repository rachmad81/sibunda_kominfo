<!-- Modal -->
<div id="modal_profil" class="modal fade" role="dialog">
	<div class="modal-dialog modal-lg">

		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Indikator HI PAUD</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<table id="table_indikator" style="width: 100%;border-collapse: collapse;" border="1">
					<thead class="table_headers">
						<tr>
							<th style="width: 3%;">
								<center>NO</center>
							</th>
							<th>
								<center>INDIKATOR</center>
							</th>
							<th style="width: 60%">
								<center>AKTUALISASI</center>
							</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>
								<center>
									1
								</center>
							</td>
							<td>
								Jadwal Pemeriksaan DDTK
							</td>
							<td>
								@php
								$kurikulum = ['','','',''];
								$style = ['','','',''];
								switch ($pengajuan->jadwal_pemeriksaan_ddtk) {
									case '':
									$kurikulum[0] = 'selected';
									$style[0] = "font-weight: bold;";
									break;
									case '1':
									$kurikulum[1] = 'selected';
									$style[1] = "font-weight: bold;";
									break;
									case '2':
									$kurikulum[2] = 'selected';
									$style[2] = "font-weight: bold;";
									break;
									case '3':
									$kurikulum[3] = 'selected';
									$style[3] = "font-weight: bold;";
									break;
									default:
									break;
								}
								@endphp
								<select disabled style="width:100%">
									<option {{$kurikulum[0]}} value="" disabled>..Tidak memilih..</option>
									<option {{$kurikulum[1]}} value='1'>satu bulan sekali</option>
									<option {{$kurikulum[2]}} value='2'>dua bulan sekali</option>
									<option {{$kurikulum[3]}} value='3'>tiga bulan sekali</option>
								</select>
							</td>
						</tr>
						<tr>
							<td>
								<center>
									2
								</center>
							</td>
							<td>
								Jadwal Kesehatan
							</td>
							<td>
								@php
								$kurikulum = ['','','',''];
								$style = ['','','',''];
								switch ($pengajuan->jadwal_kesehatan) {
									case '':
									$kurikulum[0] = 'selected';
									$style[0] = "font-weight: bold;";
									break;
									case '1':
									$kurikulum[1] = 'selected';
									$style[1] = "font-weight: bold;";
									break;
									case '2':
									$kurikulum[2] = 'selected';
									$style[2] = "font-weight: bold;";
									break;
									case '3':
									$kurikulum[3] = 'selected';
									$style[3] = "font-weight: bold;";
									break;
									default:
									break;
								}
								@endphp
								<select disabled style="width:100%">
									<option {{$kurikulum[0]}} value="" disabled>..Tidak memilih..</option>
									<option {{$kurikulum[1]}} value='1'>satu bulan sekali</option>
									<option {{$kurikulum[2]}} value='2'>dua bulan sekali</option>
									<option {{$kurikulum[3]}} value='3'>tiga bulan sekali</option>
								</select>
							</td>
						</tr>
						<tr>
							<td>
								<center>
									3
								</center>
							</td>
							<td>
								Sistem rujukan DDT dengan puskesmas
							</td>
							<td>
								@php
								$kurikulum = ['','','',''];
								$style = ['','','',''];
								switch ($pengajuan->sistem_rujukan_ddt_dengan_puskesmas) {
									case '':
									$kurikulum[0] = 'selected';
									$style[0] = "font-weight: bold;";
									break;
									case '1':
									$kurikulum[1] = 'selected';
									$style[1] = "font-weight: bold;";
									break;
									case '2':
									$kurikulum[2] = 'selected';
									$style[2] = "font-weight: bold;";
									break;
									case '3':
									$kurikulum[3] = 'selected';
									$style[3] = "font-weight: bold;";
									break;
									default:
									break;
								}
								@endphp
								<select id="sistem_rujukan_ddt_dengan_puskesmas" disabled style="width:100%">
									<option {{$kurikulum[1]}} value='0'>Belum Ada</option>
									<option {{$kurikulum[2]}} value='1'>Ada</option>
								</select>
							</td>
						</tr>
						<tr>
							<td>
								<center>
									4
								</center>
							</td>
							<td>
								Jadwal PMT AS
							</td>
							<td>
								@php
								$kurikulum = ['','','',''];
								$style = ['','','',''];
								switch ($pengajuan->jadwal_pmt_as) {
									case '':
									$kurikulum[0] = 'selected';
									$style[0] = "font-weight: bold;";
									break;
									case '1':
									$kurikulum[1] = 'selected';
									$style[1] = "font-weight: bold;";
									break;
									case '2':
									$kurikulum[2] = 'selected';
									$style[2] = "font-weight: bold;";
									break;
									case '3':
									$kurikulum[3] = 'selected';
									$style[3] = "font-weight: bold;";
									break;
									default:
									break;
								}
								@endphp
								<select disabled style="width:100%">
									<option {{$kurikulum[0]}} value="" disabled>..Tidak memilih..</option>
									<option {{$kurikulum[1]}} value='1'>satu minggu sekali</option>
									<option {{$kurikulum[2]}} value='2'>dua minggu sekali</option>
									<option {{$kurikulum[3]}} value='3'>tiga minggu sekali</option>
								</select>
							</td>
						</tr>
						<tr>
							<td>
								<center>
									5
								</center>
							</td>
							<td>
								Jumlah dalam seminggu siswa mengikuti kegiatan cuci tangan berkelompok
							</td>
							<td>
								@php
								$kurikulum = ['','','','','','',''];
								$style = ['','','','','','',''];
								switch ($pengajuan->jadwal_cuci_tangan_berkelompok) {
									case '':
									$kurikulum[0] = 'selected';
									$style[0] = "font-weight: bold;";
									break;
									case '0':
									$kurikulum[1] = 'selected';
									$style[1] = "font-weight: bold;";
									break;
									case '1':
									$kurikulum[2] = 'selected';
									$style[2] = "font-weight: bold;";
									break;
									case '2':
									$kurikulum[3] = 'selected';
									$style[3] = "font-weight: bold;";
									break;
									case '3':
									$kurikulum[4] = 'selected';
									$style[4] = "font-weight: bold;";
									break;
									case '4':
									$kurikulum[5] = 'selected';
									$style[5] = "font-weight: bold;";
									break;
									case '5':
									$kurikulum[6] = 'selected';
									$style[6] = "font-weight: bold;";
									break;
									default:
									break;
								}
								@endphp
								<select disabled style="width:100%">
									<option {{$kurikulum[0]}} value="" disabled>..Tidak memilih..</option>
									<option {{$kurikulum[1]}} value='0'>Tidak Pernah</option>
									<option {{$kurikulum[2]}} value='1'>1 hari</option>
									<option {{$kurikulum[3]}} value='2'>2 hari</option>
									<option {{$kurikulum[4]}} value='3'>3 hari</option>
									<option {{$kurikulum[5]}} value='4'>4 hari</option>
									<option {{$kurikulum[6]}} value='5'>5 hari</option>
								</select>
							</td>
						</tr>
						<tr>
							<td>
								<center>
									6
								</center>
							</td>
							<td>
								Tipe Jamban
							</td>
							<td>
								@php
								$kurikulum = ['','','','','',''];
								$style = ['','','','','',''];
								switch ($pengajuan->tipe_jamban) {
									case '':
									$kurikulum[0] = 'selected';
									$style[0] = "font-weight: bold;";
									break;
									case 'leher angsa':
									$kurikulum[1] = 'selected';
									$style[1] = "font-weight: bold;";
									break;
									case 'cubluk dengan tutup':
									$kurikulum[2] = 'selected';
									$style[2] = "font-weight: bold;";
									break;
									case 'cubluk menggantung di atas sungai':
									$kurikulum[3] = 'selected';
									$style[3] = "font-weight: bold;";
									break;
									case 'cubluk tanpa tutup':
									$kurikulum[4] = 'selected';
									$style[4] = "font-weight: bold;";
									break;
									case 'tidak tersedia jamban':
									$kurikulum[5] = 'selected';
									$style[5] = "font-weight: bold;";
									break;
									default:
									break;
								}
								@endphp
								<select disabled style="width:100%">
									<option {{$kurikulum[0]}} value="" disabled>..Tidak memilih..</option>
									<option {{$kurikulum[1]}} value='leher angsa'>leher angsa</option>
									<option {{$kurikulum[2]}} value='cubluk dengan tutup'>cubluk dengan tutup</option>
									<option {{$kurikulum[3]}} value='cubluk menggantung di atas sungai'>cubluk menggantung di atas sungai</option>
									<option {{$kurikulum[4]}} value='cubluk tanpa tutup'>cubluk tanpa tutup</option>
									<option {{$kurikulum[5]}} value='tidak tersedia jamban'>tidak tersedia jamban</option>
								</select>
							</td>
						</tr>
						<tr>
							<td>
								<center>
									7
								</center>
							</td>
							<td>
								Jumlah keseluruhan tempat cuci tangan
							</td>
							<td style="text-align: left">
								&nbsp;{{$pengajuan->jumlah_tempat_cuci_tangan }}
							</td>
						</tr>
						<tr>
							<td>
								<center>
									8
								</center>
							</td>
							<td>
								Jumlah tempat cuci tangan yang rusak
							</td>
							<td style="text-align: left">
								&nbsp;{{ $pengajuan->jumlah_tempat_cuci_tangan_rusak }}
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