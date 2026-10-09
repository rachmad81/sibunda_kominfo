<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Cetak PAUD HI</title>
</head>
<body>
	<div>
		<table style="width: 100%;border-collapse: collapse;" border="1">
			<thead class="table_headers">
				<tr style="background: #01254d;color: white;">
					<th style="width: 3%;">
						<center>NO</center>
					</th>
					<th>
						<center>PROFIL SEKOLAH</center>
					</th>
					<th style="width: 70%">
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
					<td>
						{{$profil->npsn}}
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
					<td>
						{{$profil->nama_sekolah}}
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
					<td>
						{{$profil->alamat}}
					</td>
				</tr>
				<tr>
					<td>
						RT :
						{{$profil->rt}}
						RW :
						{{$profil->rw}}
					</td>
				</tr>
				<tr>
					<div class="row" style="margin-left: 1%">
						<td>
							Kelurahan :
							{{$profil->kelurahan}}
							Kecamatan :
							{{$profil->kecamatan}}
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
					<td>
						{{$profil->kepala_sekolah}}
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
					<td>
						{{$profil->tahun_berdiri}}
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
					<td>
						{{$profil->status_kepemilikan_lahan}}
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
					<td>
						Tahun {{$profil->akreditasi_tahun}}
						Nilai {{$profil->akreditasi_nilai}}
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
					<td>
						{{$profil->tgl_berakhir_izin}}
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
					$arr_kurikulum = json_decode($profil->kurikulum);
					@endphp
					<td>
						{!! join(',',$arr_kurikulum) !!}
					</td>
				</tr>
				<tr>
					<td>
						Kurikulum Khusus
					</td>
					<td>
						{{$profil->kurikulum_khusus}}
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
					<td>
					</td>
				</tr>
				<tr>
					<td>
						Kelompok A
					</td>
					<td>
						Laki-laki : {{$profil->jumlah_siswa_kel_a_laki}}, Perempuan : {{$profil->jumlah_siswa_kel_a_perempuan}}, Jumlah : {{$profil->jumlah_siswa_kel_a_laki+$profil->jumlah_siswa_kel_a_perempuan}}
					</td>
				</tr>
				<tr>
					<td>
						Kelompok B
					</td>
					<td colspan="1">
						Laki-laki : {{$profil->jumlah_siswa_kel_b_laki}}, Perempuan : {{$profil->jumlah_siswa_kel_b_perempuan}}, Jumlah : {{$profil->jumlah_siswa_kel_b_laki+$profil->jumlah_siswa_kel_b_perempuan}}
					</td>
				</tr>
				<tr>
					<td>
						Anak Berkebutuhan Khusus (ABK)
					</td>
					<td colspan="1">
						Laki-laki : {{$profil->jumlah_siswa_abk_laki}}, Perempuan : {{$profil->jumlah_siswa_abk_perempuan}}, Jumlah : {{$profil->jumlah_siswa_abk_laki+$profil->jumlah_siswa_abk_perempuan}}
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
					<td>
						Laki-laki : {{$profil->jumlah_gtk_laki}}, Perempuan : {{$profil->jumlah_gtk_perempuan}}, Jumlah : {{$profil->jumlah_gtk_laki+$profil->jumlah_gtk_perempuan}}
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
					<td>
						SMP : {{$profil->jumlah_pendidikan_gtk_smp}}, 
						SMA : {{$profil->jumlah_pendidikan_gtk_sma}}, 
						Sarjana : {{$profil->jumlah_pendidikan_gtk_sarjana}}
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
					<td>
						PNS : {{$profil->pegawai_pns}}, 
						Yayasan : {{$profil->pegawai_yayasan}}, 
						Lainnya : {{$profil->pegawai_lainnya}}
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
					<td>
						{{$profil->gedung_sekolah}}
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
					<td>
						{{$profil->kondisi_gedung}}
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	
	<br>
	<div>
		<table id="table_indikator" style="width: 100%;border-collapse: collapse;" border="1">
			<thead class="table_headers">
				<tr style="background: #01254d;color: white;">
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
						$kurikulum = ['..Tidak memilih..','satu bulan sekali','dua bulan sekali','tiga bulan sekali'];
						$val_kurikulum = ['',1,2,3];
						$key = array_search($profil->jadwal_pemeriksaan_ddtk,$val_kurikulum);
						echo $kurikulum[$key];
						@endphp
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
						$kurikulum = ['..Tidak memilih..','satu bulan sekali','dua bulan sekali','tiga bulan sekali'];
						$val_kurikulum = ['',1,2,3];
						$key = array_search($profil->jadwal_kesehatan,$val_kurikulum);
						echo $kurikulum[$key];
						@endphp
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
						$kurikulum = ['Belum Ada','Ada'];
						$val_kurikulum = [0,1];
						$key = array_search($profil->sistem_rujukan_ddt_dengan_puskesmas,$val_kurikulum);
						echo $kurikulum[$key];
						@endphp
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
						$kurikulum = ['..Tidak memilih..','satu minggu sekali','dua minggu sekali','tiga minggu sekali'];
						$val_kurikulum = ['',1,2,3];
						$key = array_search($profil->jadwal_pmt_as,$val_kurikulum);
						echo $kurikulum[$key];
						@endphp
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
						$kurikulum = ['..Tidak memilih..','Tidak Pernah','1 hari','2 hari','3 hari','4 hari','5 hari'];
						$val_kurikulum = ['','0','1','2','3','4','5'];
						
						$key = array_search($profil->jadwal_cuci_tangan_berkelompok,$val_kurikulum);
						echo $kurikulum[$key];
						@endphp
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
						{{$profil->tipe_jamban}}
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
						{{$profil->jumlah_tempat_cuci_tangan }}
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
						{{ $profil->jumlah_tempat_cuci_tangan_rusak }}
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	
	<br>
	<div>
		@php
    			// Untuk menampilkan Prefix Kategori
		$alphabets = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];
    			// Untuk melakukan tracking Prefix Kategori yang perlu digunakan
		$categoryNumber = 0;
    			// Untuk melakukan tracking Prefix Instrumen yang perlu digunakan
		$instrumenNumber = 1;
    			// Untuk menampilkan Prefix Sub Instrumen
		$subalphabets = ['a.', 'b.', 'c.', 'd.', 'e.', 'f.', 'g.', 'h.', 'i.', 'j.', 'k.', 'l.', 'm.', 'n.', 'o.', 'p.', 'q.', 'r.', 's.', 't.', 'u.', 'v.', 'w.', 'x.', 'y.', 'z.'];
    			// Untuk menyetor jumlah row per halaman
		$rowsPerPage = [];
		$page = -1;
		@endphp
		<table id="table_instrumen_hi" style="width: 100%;border-collapse: collapse;" border="1">
			<thead class="table_headers">
				<tr style="background: #01254d;color: white">
					<th rowspan="2" style="width: 3%;">
						<center>NO</center>
					</th>
					<th rowspan="2">
						<center>PERTANYAAN / PERNYATAAN</center>
					</th>
					<th colspan="2">
						<center>HASIL PEMANTAUAN</center>
					</th>
				</tr>
				<tr style="background: #01254d;color: white">
					<th style="width:3%">
						<center>Iya</center>
					</th style="width:3%">
					<th>
						<center>Tidak</center>
					</th>
				</tr>
			</thead>
			<tbody id="table_instrumen_hi_body">
				@foreach ($categories as $categoryKey => $category)
				@php
				$instrumenNumber = 1;
				$listOfCategories = [];
				@endphp

				@if (!$category->Children->isEmpty())
				@foreach ($category->Children as $categoryChildKey => $categoryChild)
				@php
						//Data Category dari parent, $categoryChild, dimasukkan ke $listOfCategories
				array_push($listOfCategories, $categoryChild); 
				$rowsPerPage[] = 0;
				@endphp
				@endforeach
				@php
				$page += 1;
				$rowsPerPage[$page] += 1;
				$page -= 1;
				@endphp
				<tr class="table_parent_categories" style="background: #004391;color: white">
					<td>
						<center>
							<strong>
								{{ $alphabets[$categoryNumber] }}
							</strong>
						</center>
					</td>
					<td colspan="5">
						<strong>{{ $category->nama }}</strong>
					</td>
				</tr>
				@else
				@php
						//Data Category, $category, dimasukkan ke $listOfCategories
				array_push($listOfCategories, $category); 
				$rowsPerPage[] = 0;
				@endphp
				@endif
				@php
				$childPrefixKey = 0;
				@endphp
				{{-- Loop ini dibuat agar deretan fungsi berikut bisa digunakan baik oleh Category dengan parent maupun Category tanpa parent --}}
				@foreach ($listOfCategories as $key => $category)
				@php
				$page += 1;
				$rowsPerPage[$page] += 1;
				@endphp
				<tr class="table_categories" style="background: #0077ff;color: white">
					<td>
						@if (count($listOfCategories) > 1)
						@php
						$childPrefixKey++;
						@endphp
						<center>
							<strong>{{ $alphabets[$categoryNumber] }}.{{ $childPrefixKey }}</strong>
						</center>
						@else
						@php
								//Urutan Kategori diperbarui
						$categoryNumber++; 
						@endphp
						<center><strong>{{ $alphabets[$categoryNumber] }}</strong></center>
						@endif
					</td>
					<td colspan="5">
						<strong>{{ $category->nama }}</strong>
					</td>
					@php
					$listOfInstruments = [];
					@endphp
				</tr>
				@foreach ($category->Instrumen as $instrumenKey => $instrumen)
				@php
				$rowsPerPage[$page] += 1;
				@endphp
				<tr id="instrumen-{{ $instrumen->id }}" class="table_instruments">
					<td>
						<center>{{ $instrumenNumber }}</center>
					</td>
					<td>
						@if (!$instrumen->Children->isEmpty())
						<strong>{{ $instrumen->nama }}</strong>
						@else
						{{ $instrumen->nama }}
						@endif
						@if ($instrumen->has_dynamic_children == true)

						@endif
					</td>
					@php
					$instrumenNumber++;
					@endphp
					@if ($instrumen->is_fillable === true)
					@php
					$style = ['',''];
					if($instrumen->hasil_pemantauan===true){
						$style[0] = '✓';
					}else if($instrumen->hasil_pemantauan===false){
						$style[1] = '✓';
					}
					@endphp
					<td>
						<center>
							{!! $style[0] !!}
						</center>
					</td>
					<td>
						<center>
							{!! $style[1] !!}
						</center>
					</td>
					@else
					<td>
					</td>
					@endif
				</tr>
				@if (!$instrumen->Children->isEmpty())
				@foreach ($instrumen->Children as $instrumenChildKey => $instrumenChild)
				@php
				$rowsPerPage[$page] += 1;
				@endphp
				<tr id="instrumen-{{ $instrumen->id }}-{{ $instrumenChild->id }}"
					class="instrumen-{{ $instrumen->id }} table_instruments">
					<td></td>
					<td>{{ $subalphabets[$instrumenChildKey] }}
						{{ $instrumenChild->nama }}
						@if ($instrumenChild->has_dynamic_children == true)

						@endif
					</td>
					@if ($instrumenChild->is_fillable === true)
					@php
					$style = ['',''];
					if($instrumenChild->hasil_pemantauan===true){
						$style[0] = '✓';
					}else if($instrumenChild->hasil_pemantauan===false){
						$style[1] = '✓';
					}
					@endphp
					<td>
						<center>
							{!! $style[0] !!}
						</center>
					</td>
					<td>
						<center>
							{!! $style[1] !!}
						</center>
					</td>
					@else
					<td>
					</td>
					@endif
				</tr>
				@if (!$instrumenChild->Children->isEmpty())
				@foreach ($instrumenChild->Children as $instrumenGrandChildKey => $instrumenGrandChild)
				@php
				$rowsPerPage[$page] += 1;
				@endphp
				<tr id="instrumen-{{ $instrumen->id }}-{{ $instrumenChild->id }}-{{ $instrumenGrandChild->id }}"
					class="instrumen-{{ $instrumen->id }}-{{ $instrumenChild->id }} table_instruments">
					<td></td>
					<td>({{ $instrumenGrandChildKey + 1 }})
						{{ $instrumenGrandChild->nama }}
						@if ($instrumenGrandChild->has_dynamic_children == true)

						@endif
					</td>
					@if ($instrumenGrandChild->is_fillable === true)
					@php
					$style = ['',''];
					if($instrumenGrandChild->hasil_pemantauan===true){
						$style[0] = '✓';
					}else if($instrumenGrandChild->hasil_pemantauan===false){
						$style[1] = '✓';
					}
					@endphp
					<td>
						<center>
							{!! $style[0] !!}
						</center>
					</td>
					<td>
						<center>
							{!! $style[1] !!}
						</center>
					</td>
					@else
					<td>
					</td>
					@endif
				</tr>
				@if ($instrumenGrandChild->has_dynamic_children == true && !$instrumenGrandChild->dynamic_children->isEmpty())
				@foreach ($instrumenGrandChild->dynamic_children as $dcKey => $dynamic_child)
				@php
				$rowsPerPage[$page] += 1;
				@endphp
				<tr class="table_instruments">
					<td></td>
					<td>
						{{$dynamic_child->nama}}
					</td>
					@php
					$style = ['',''];
					if($dynamic_child->hasil_pemantauan===true){
						$style[0] = '✓';
					}else if($dynamic_child->hasil_pemantauan===false){
						$style[1] = '✓';
					}
					@endphp
					<td>
						<center>
							{!! $style[0] !!}
						</center>
					</td>
					<td>
						<center>
							{!! $style[1] !!}
						</center>
					</td>
				</tr>
				@endforeach
				@endif
				@endforeach
				@endif
				@if ($instrumenChild->has_dynamic_children == true && !$instrumenChild->dynamic_children->isEmpty())
				@foreach ($instrumenChild->dynamic_children as $dcKey => $dynamic_child)
				@php
				$rowsPerPage[$page] += 1;
				@endphp
				<tr class="table_instruments">
					<td></td>
					<td>
						{{ $dynamic_child->nama }}
					</td>
					@php
					$style = ['',''];
					if($dynamic_child->hasil_pemantauan===true){
						$style[0] = '✓';
					}else if($dynamic_child->hasil_pemantauan===false){
						$style[1] = '✓';
					}
					@endphp
					<td>
						<center>
							{!! $style[0] !!}
						</center>
					</td>
					<td>
						<center>
							{!! $style[1] !!}
						</center>
					</td>
				</tr>
				@endforeach
				@endif
				@endforeach
				@endif
				@if ($instrumen->has_dynamic_children == true && !$instrumen->dynamic_children->isEmpty())
				@foreach ($instrumen->dynamic_children as $dcKey => $dynamic_child)
				@php
				$rowsPerPage[$page] += 1;
				@endphp
				<tr class="table_instruments">
					<td></td>
					<td>
						{{ $dynamic_child->nama }}
					</td>
					@php
					$style = ['',''];
					if($dynamic_child->hasil_pemantauan===true){
						$style[0] = '✓';
					}else if($dynamic_child->hasil_pemantauan===false){
						$style[1] = '✓';
					}
					@endphp
					<td>
						<center>
							{!! $style[0] !!}
						</center>
					</td>
					<td>
						<center>
							{!! $style[1] !!}
						</center>
					</td>
				</tr>
				@endforeach
				@endif
				@endforeach
				@endforeach
				@endforeach
			</tbody>
		</table>
	</div>
	<script type="text/javascript">
		(function() {
			window.print();
		})();
	</script>
</body>
</html>