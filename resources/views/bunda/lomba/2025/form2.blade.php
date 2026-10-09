<h4>DOKUMEN ADMINISTRASI APRESIASI BUNDA PAUD KECAMATAN DAN KELURAHAN KOTA SURABAYA TAHUN {{$tahun}}</h4>
@include('bunda.lomba.alert')
<div class="table-responsive">
	<table id="dataTable" class="table table-bordered table-striped">
		<thead>
			<tr>
				<th>Uraian</th>
				<th>Link File</th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td style="width:40%"><b>Surat pengusulan dari Camat/Lurah</b></td>
				<td>
					{{-- <input type="file" accept=".pdf" class="form-control" name="surat_pengusulan"> --}}
					<input type="text" class="form-control" name="surat_pengusulan" value="{{(!empty($berkas)) ? $berkas->surat_pengusulan : ''}}">
				</td>
				<td>
					{{-- <a href="{{asset('dokumen_identitas/A.docx')}}" target="_blank" class="btn btn-sm btn-info">Contoh</a> --}}
					@if($tahun==date('Y'))
					<a href="javascript:void(0)" class="btn btn-sm btn-primary" onclick="simpan_berkas('surat_pengusulan','berkas_identitas','form2')">Simpan</a>
					@endif
					@if(!empty($berkas) && !is_null($berkas->surat_pengusulan))
					<a href="{{$berkas->surat_pengusulan}}" target="_blank" class="btn btn-sm btn-warning">View</a>
					@endif
				</td>
			</tr>
			<tr>
				<td><b>Surat Keputusan penetapan sebagai Bunda PAUD Kecamatan/Kelurahan</b></td>
				<td>
					{{-- <input type="file" accept=".pdf" class="form-control" name="sk_penetapan"> --}}
					<input type="text" class="form-control" name="sk_penetapan" value="{{(!empty($berkas)) ? $berkas->sk_penetapan : ''}}">
				</td>
				<td>
					{{-- <a href="{{asset('dokumen_identitas/B.docx')}}" target="_blank" class="btn btn-sm btn-info">Contoh</a> --}}
					@if($tahun==date('Y'))
					<a href="javascript:void(0)" class="btn btn-sm btn-primary" onclick="simpan_berkas('sk_penetapan','berkas_identitas','form2')">Simpan</a>
					@endif
					@if(!empty($berkas) && !is_null($berkas->sk_penetapan))
					<a href="{{$berkas->sk_penetapan}}" target="_blank" class="btn btn-sm btn-warning">View</a>
					@endif
				</td>
			</tr>
			<tr>
				<td><b>Program Kerja Bunda PAUD Kecamatan/Kelurahan</b></td>
				<td>
					{{-- <input type="file" accept=".pdf" class="form-control" name="program"> --}}
					<input type="text" class="form-control" name="program" value="{{(!empty($berkas)) ? $berkas->program : ''}}">
				</td>
				<td>
					{{-- <a href="{{asset('dokumen_identitas/C.docx')}}" target="_blank" class="btn btn-sm btn-info">Contoh</a> --}}
					@if($tahun==date('Y'))
					<a href="javascript:void(0)" class="btn btn-sm btn-primary" onclick="simpan_berkas('program','berkas_identitas','form2')">Simpan</a>
					@endif
					@if(!empty($berkas) && !is_null($berkas->program))
					<a href="{{$berkas->program}}" target="_blank" class="btn btn-sm btn-warning">View</a>
					@endif
				</td>
			</tr>
			<tr>
				<td><b>Surat Keputusan dan Struktur Organisasi Pokja Bunda PAUD Kecamatan/Kelurahan</b></td>
				<td>
					{{-- <input type="file" accept=".pdf" class="form-control" name="struktur_organisasi"> --}}
					<input type="text" class="form-control" name="struktur_organisasi" value="{{(!empty($berkas)) ? $berkas->struktur_organisasi : ''}}">
				</td>
				<td>
					{{-- <a href="{{asset('dokumen_identitas/D.docx')}}" target="_blank" class="btn btn-sm btn-info">Contoh</a> --}}
					@if($tahun==date('Y'))
					<a href="javascript:void(0)" class="btn btn-sm btn-primary" onclick="simpan_berkas('struktur_organisasi','berkas_identitas','form2')">Simpan</a>
					@endif
					@if(!empty($berkas) && !is_null($berkas->struktur_organisasi))
					<a href="{{$berkas->struktur_organisasi}}" target="_blank" class="btn btn-sm btn-warning">View</a>
					@endif
				</td>
			</tr>
			<tr>
				<td><b>Profil Bunda PAUD Kecamatan/Kelurahan</b></td>
				<td>
					{{-- <input type="file" accept=".pdf" class="form-control" name="profil_bunda"> --}}
					<input type="text" class="form-control" name="profil_bunda" value="{{(!empty($berkas)) ? $berkas->profil_bunda : ''}}">
				</td>
				<td>
					{{-- <a href="{{asset('dokumen_identitas/E.docx')}}" target="_blank" class="btn btn-sm btn-info">Contoh</a> --}}
					@if($tahun==date('Y'))
					<a href="javascript:void(0)" class="btn btn-sm btn-primary" onclick="simpan_berkas('profil_bunda','berkas_identitas','form2')">Simpan</a>
					@endif
					@if(!empty($berkas) && !is_null($berkas->profil_bunda))
					<a href="{{$berkas->profil_bunda}}" target="_blank" class="btn btn-sm btn-warning">View</a>
					@endif
				</td>
			</tr>
			<tr>
				<td><b>Profil perkembangan PAUD Kecamatan/Kelurahan</b></td>
				<td>
					{{-- <input type="file" accept=".pdf" class="form-control" name="profil_perkembangan"> --}}
					<input type="text" class="form-control" name="profil_perkembangan" value="{{(!empty($berkas)) ? $berkas->profil_perkembangan : ''}}">
				</td>
				<td>
					{{-- <a href="{{asset('dokumen_identitas/F.docx')}}" target="_blank" class="btn btn-sm btn-info">Contoh</a> --}}
					@if($tahun==date('Y'))
					<a href="javascript:void(0)" class="btn btn-sm btn-primary" onclick="simpan_berkas('profil_perkembangan','berkas_identitas','form2')">Simpan</a>
					@endif
					@if(!empty($berkas) && !is_null($berkas->profil_perkembangan))
					<a href="{{$berkas->profil_perkembangan}}" target="_blank" class="btn btn-sm btn-warning">View</a>
					@endif
				</td>
			</tr>
			<tr>
				<td><b>Kartu Tanda Penduduk (KTP)</b></td>
				<td>
					{{-- <input type="file" accept=".pdf" class="form-control" name="profil_perkembangan"> --}}
					<input type="text" class="form-control" name="ktp" value="{{(!empty($berkas)) ? $berkas->ktp : ''}}">
				</td>
				<td>
					{{-- <a href="{{asset('dokumen_identitas/F.docx')}}" target="_blank" class="btn btn-sm btn-info">Contoh</a> --}}
					@if($tahun==date('Y'))
					<a href="javascript:void(0)" class="btn btn-sm btn-primary" onclick="simpan_berkas('ktp','berkas_identitas','form2')">Simpan</a>
					@endif
					@if(!empty($berkas) && !is_null($berkas->ktp))
					<a href="{{$berkas->ktp}}" target="_blank" class="btn btn-sm btn-warning">View</a>
					@endif
				</td>
			</tr>
			<tr>
				<td><b>SK Pengukuhan Bunda PAUD</b></td>
				<td>
					{{-- <input type="file" accept=".pdf" class="form-control" name="profil_perkembangan"> --}}
					<input type="text" class="form-control" name="sk_pengukuhan" value="{{(!empty($berkas)) ? $berkas->sk_pengukuhan : ''}}">
				</td>
				<td>
					{{-- <a href="{{asset('dokumen_identitas/F.docx')}}" target="_blank" class="btn btn-sm btn-info">Contoh</a> --}}
					@if($tahun==date('Y'))
					<a href="javascript:void(0)" class="btn btn-sm btn-primary" onclick="simpan_berkas('sk_pengukuhan','berkas_identitas','form2')">Simpan</a>
					@endif
					@if(!empty($berkas) && !is_null($berkas->sk_pengukuhan))
					<a href="{{$berkas->sk_pengukuhan}}" target="_blank" class="btn btn-sm btn-warning">View</a>
					@endif
				</td>
			</tr>
		</tbody>
	</table>
</div>
<script type="text/javascript">
</script>