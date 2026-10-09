<style type="text/css">
	#identitas td{
		vertical-align: top;
		border-top: 1px solid black;
		border-bottom: 1px solid black;
	}
</style>
<table style="width: 100%" id="identitas">
	<tbody>
		<tr>
			<td><b>Nama</b></td>
			<td>{{$identitas->nama}}</td>
			<td><b>Telp Peserta</b></td>
			<td>{{$identitas->telp_peserta}}</td>
		</tr>
		<tr>
			<td><b>Instansi</b></td>
			<td>{{$identitas->instansi}}</td>
			<td><b>Telp Pengusul</b></td>
			<td>{{$identitas->telp_pengusul}}</td>
		</tr>
		<tr>
			<td><b>Alamat Instansi</b></td>
			<td>{{$identitas->alamat_instansi}}</td>
			<td></td>
			<td></td>
		</tr>
		<tr>
			<td colspan="4">
				<div class="alert alert-warning">
					<b>Informasi</b><br>
					- Tombol <a href="javascript:void(0)" class="btn btn-sm btn-primary">Simpan</a> digunakan untuk menyimpan penilaian setiap baris.<br>
					- Tombol <a href="javascript:void(0)" class="btn btn-sm btn-success">Simpan dan Tutup</a> digunakan untuk menyimpan penilaian.<br>
					&nbsp;&nbsp;&nbsp;&nbsp;- <b>Draft</b> menyimpan sementara dan masih bisa diedit dikemudian waktu.<br>
					&nbsp;&nbsp;&nbsp;&nbsp;- <b>Kunci penilaian</b> menyimpan dan tidak bisa diedit kecuali menghubungi admin aplikasi.
				</div>
			</td>
		</tr>
	</tbody>
</table>