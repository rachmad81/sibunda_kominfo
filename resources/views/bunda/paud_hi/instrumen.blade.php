<!-- Modal -->
<div id="modal_profil" class="modal fade" role="dialog">
	<div class="modal-dialog modal-lg" style="max-width: 80% !important">

		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Instrumen HI PAUD</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<div class="blob" style="background: #eee;border: 1px solid black;">
					<table style="width: 100%;">
						<tr>
							<td colspan="3" style="text-align: left;vertical-align: top;">
								<b style="font-size: 15pt;color: red">Untuk diperhatikan</b>
							</td>
						</tr>
						<tr>
							<td style="width: 35%;text-align: left;vertical-align: top;">
								<b>Untuk menyimpan catatan:</b>
								<ul>
									<li>1. Ketikkan catatan pada kolom catatan</li>
									<li>2. Klik <b>simpan</b> sesuai baris pencatatan</li>
								</ul>
							</td>
							<td style="width: 35%;text-align: left;vertical-align: top;">
								<b>Untuk melihat riwayat catatan:</b>
								<ul>
									<li>1. Klik <b>Riwayat Catatan</b> sesuai baris pencatatan</li>
									<li>2. Akan muncul pop up riwayat catatan terhadap baris yg dipilih, di urutkan berdasarkan tanggal terbaru</li>
								</ul>
							</td>
							<td>
								<h3>Score yang diperoleh</h3>
								<h2>{{$hasil_iya}}</h2>
								@php
								if($hasil_iya>=76){
									$status_score = '<span style="background: #00cdcd;padding: 5px;border-radius: 10px"><b>Sangat Tinggi</b></span>';
								}else if($hasil_iya<=75 && $hasil_iya>=56){
									$status_score = '<span style="background: #33ff33;padding: 5px;border-radius: 10px"><b>Tinggi</b></span>';
								}else if($hasil_iya<=55 && $hasil_iya>=36){
									$status_score = '<span style="background: #ffff00;padding: 5px;border-radius: 10px"><b>Sedang</b></span>';
								}else if($hasil_iya<=35 && $hasil_iya>=0){
									$status_score = '<span style="background: #ffa500;padding: 5px;border-radius: 10px"><b>Rendah</b></span>';
								}else{
									$status_score = '';
								}
								echo $status_score;
								@endphp
							</td>
						</tr>
					</table>
				</div>
				<br>
				<br>
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
						<tr>
							<th rowspan="2" style="width: 3%;">
								<center>NO</center>
							</th>
							<th rowspan="2">
								<center>PERTANYAAN / PERNYATAAN</center>
							</th>
							<th colspan="3">
								<center>HASIL PEMANTAUAN</center>
							</th>
							<th rowspan="2">
								<center>Catatan</center>
							</th>
							<th rowspan="2">
								<center>Pemberi Catatan</center>
							</th>
							<th rowspan="2" style="width: 15%">
								<center>Aksi</center>
							</th>
						</tr>
						<tr>
							<th style="width:3%">
								<center>Iya</center>
							</th style="width:3%">
							<th>
								<center>Tidak</center>
							</th>
							<th style="width:20%">
								<center>Dokumentasi <br> <small>(Link Google Drive)</small></center>
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
						<tr class="table_parent_categories">
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
						<tr class="table_categories">
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
								$style[0] = "background: #6bcf8e;";
							}else if($instrumen->hasil_pemantauan===false){
								$style[1] = "background: #6bcf8e;";
							}
							@endphp
							<td style="{{$style[0]}}">
								<center>
									Iya
								</center>
							</td>
							<td style="{{$style[1]}}">
								<center>
									Tidak
								</center>
							</td>
							<td>
								<center>
									<a href="{{ $instrumen->file }}" target="_blank">{{ $instrumen->file }}</a>
								</center>
							</td>
							<td>
								<textarea id="komentar_parent_{{$instrumen->id}}">{{$instrumen->komentar}}</textarea>
							</td>
							<td>
								<span id="komentator_parent_{{$instrumen->id}}">{{$instrumen->komentator}}</span>
							</td>
							<td>
								<a href="javascript:void(0)" onclick="simpan_komentar('{{$instrumen->id}}','komentar_parent_{{$instrumen->id}}','parent')" class="btn btn-sm btn-primary">Simpan</a>
								<a href="javascript:void(0)" onclick="riwayat('{{$instrumen->id}}','komentar_parent_{{$instrumen->id}}','parent')" class="btn btn-sm btn-warning">Riwayat Catatan</a>
							</td>
							@else
							<td colspan="5">
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
								$style[0] = "background: #6bcf8e;";
							}else if($instrumenChild->hasil_pemantauan===false){
								$style[1] = "background: #6bcf8e;";
							}
							@endphp
							<td style="{{$style[0]}}">
								<center>
									Iya
								</center>
							</td>
							<td style="{{$style[1]}}">
								<center>
									Tidak
								</center>
							</td>
							<td>
								<center>
									<a href="{{ $instrumenChild->file }}" target="_blank">{{ $instrumenChild->file }}</a>
								</center>
							</td>
							<td>
								<textarea id="komentar_parent_{{$instrumenChild->id}}">{{$instrumenChild->komentar}}</textarea>
							</td>
							<td>
								<span id="komentator_parent_{{$instrumenChild->id}}">{{$instrumenChild->komentator}}</span>
							</td>
							<td>
								<a href="javascript:void(0)" onclick="simpan_komentar('{{$instrumenChild->id}}','komentar_parent_{{$instrumenChild->id}}','parent')" class="btn btn-sm btn-primary">Simpan</a>
								<a href="javascript:void(0)" onclick="riwayat('{{$instrumenChild->id}}','komentar_parent_{{$instrumenChild->id}}','parent')" class="btn btn-sm btn-warning">Riwayat Catatan</a>
							</td>
							@else
							<td colspan="5">
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
								$style[0] = "background: #6bcf8e;";
							}else if($instrumenGrandChild->hasil_pemantauan===false){
								$style[1] = "background: #6bcf8e;";
							}
							@endphp
							<td style="{{$style[0]}}">
								<center>
									Iya
								</center>
							</td>
							<td style="{{$style[1]}}">
								<center>
									Tidak
								</center>
							</td>
							<td>
								<center>
									<a href="{{ $instrumen->file }}" target="_blank">{{ $instrumen->file }}</a>
								</center>
							</td>
							<td>
								<textarea id="komentar_parent_{{$instrumenGrandChild->id}}">{{$instrumenGrandChild->komentar}}</textarea>
							</td>
							<td>
								<span id="komentator_parent_{{$instrumenGrandChild->id}}">{{$instrumenGrandChild->komentator}}</span>
							</td>
							<td>
								<a href="javascript:void(0)" onclick="simpan_komentar('{{$instrumenGrandChild->id}}','komentar_parent_{{$instrumenGrandChild->id}}','parent')" class="btn btn-sm btn-primary">Simpan</a>
								<a href="javascript:void(0)" onclick="riwayat('{{$instrumenGrandChild->id}}','komentar_parent_{{$instrumenGrandChild->id}}','parent')" class="btn btn-sm btn-warning">Riwayat Catatan</a>
							</td>
							@else
							<td colspan="5">
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
								$style[0] = "background: #6bcf8e;";
							}else if($dynamic_child->hasil_pemantauan===false){
								$style[1] = "background: #6bcf8e;";
							}
							@endphp
							<td style="{{$style[0]}}">
								<center>
									Iya
								</center>
							</td>
							<td style="{{$style[1]}}">
								<center>
									Tidak
								</center>
							</td>
							<td>
								<center>
									<a href="{{ $dynamic_child->file }}" target="_blank">{{ $dynamic_child->file }}</a>
								</center>
							</td>
							<td>
								<textarea id="komentar_child_{{$dynamic_child->id}}">{{$dynamic_child->komentar}}</textarea>
							</td>
							<td>
								<span id="komentator_child_{{$dynamic_child->id}}">{{$dynamic_child->komentator}}</span>
							</td>
							<td>
								<a href="javascript:void(0)" onclick="simpan_komentar('{{$dynamic_child->id}}','komentar_child_{{$dynamic_child->id}}','child')" class="btn btn-sm btn-primary">Simpan</a>
								<a href="javascript:void(0)" onclick="riwayat('{{$dynamic_child->id}}','komentar_child_{{$dynamic_child->id}}','child')" class="btn btn-sm btn-warning">Riwayat Catatan</a>
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
								$style[0] = "background: #6bcf8e;";
							}else if($dynamic_child->hasil_pemantauan===false){
								$style[1] = "background: #6bcf8e;";
							}
							@endphp
							<td style="{{$style[0]}}">
								<center>
									Iya
								</center>
							</td>
							<td style="{{$style[1]}}">
								<center>
									Tidak
								</center>
							</td>
							<td>
								<center>
									<a href="{{ $dynamic_child->file }}" target="_blank">{{ $dynamic_child->file }}</a>
								</center>
							</td>
							<td>
								<textarea id="komentar_child_{{$dynamic_child->id}}">{{$dynamic_child->komentar}}</textarea>
							</td>
							<td>
								<span id="komentator_child_{{$dynamic_child->id}}">{{$dynamic_child->komentator}}</span>
							</td>
							<td>
								<a href="javascript:void(0)" onclick="simpan_komentar('{{$dynamic_child->id}}','komentar_child_{{$dynamic_child->id}}','child')" class="btn btn-sm btn-primary">Simpan</a>
								<a href="javascript:void(0)" onclick="riwayat('{{$dynamic_child->id}}','komentar_child_{{$dynamic_child->id}}','child')" class="btn btn-sm btn-warning">Riwayat Catatan</a>
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
								$style[0] = "background: #6bcf8e;";
							}else if($dynamic_child->hasil_pemantauan===false){
								$style[1] = "background: #6bcf8e;";
							}
							@endphp
							<td style="{{$style[0]}}">
								<center>
									Iya
								</center>
							</td>
							<td style="{{$style[1]}}">
								<center>
									Tidak
								</center>
							</td>
							<td>
								<center>
									<a href="{{ $dynamic_child->file }}" target="_blank">{{ $dynamic_child->file }}</a>
								</center>
							</td>
							<td>
								<textarea id="komentar_child_{{$dynamic_child->id}}">{{$dynamic_child->komentar}}</textarea>
							</td>
							<td>
								<span id="komentator_child_{{$dynamic_child->id}}">{{$dynamic_child->komentator}}</span>
							</td>
							<td>
								<a href="javascript:void(0)" onclick="simpan_komentar('{{$dynamic_child->id}}','komentar_child_{{$dynamic_child->id}}','child')" class="btn btn-sm btn-primary">Simpan</a>
								<a href="javascript:void(0)" onclick="riwayat('{{$dynamic_child->id}}','komentar_child_{{$dynamic_child->id}}','child')" class="btn btn-sm btn-warning">Riwayat Catatan</a>
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
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
			</div>
		</div>

	</div>
</div>
<div class="modalin"></div>
<style type="text/css">
	label{
		font-weight: 10;
	}


	.paginationmodal {
		margin-top: 2%;
		margin-bottom: 2%;
		display: inline-block;
		color: black;
		float: left;
		padding: 8px 16px;
		text-decoration: none;
	}

	.paginationmodal.active {
		background-color: #0077ff;
		color: white;
	}

	.blob {
		background: black;
		padding: 10px;
		width: 100%;
	}

	.blob {
		background: black;
		width: 100%;

		box-shadow: 0 0 0 0 rgba(0, 0, 0, 1);
		transform: scale(1);
		animation: pulse 2s infinite;
	}

	@keyframes pulse {
		0% {
			/*transform: scale(0.95);*/
			box-shadow: 0 0 0 0 rgba(0, 0, 0, 0.7);
		}

		70% {
			/*transform: scale(1);*/
			box-shadow: 0 0 0 20px rgba(0, 0, 0, 0);
		}

		100% {
			/*transform: scale(0.95);*/
			box-shadow: 0 0 0 0 rgba(0, 0, 0, 0);
		}
	}
</style>
<script type="text/javascript">
	$(document).ready(function() {
		$('#modal_profil').modal({backdrop: 'static'});
		rowsShown = @json($rowsPerPage);
		uniqueKey = 0;
		$('#table_instrumen_hi').after('<div id="nav"></div>');
		let numPages = rowsShown.length;
		for (i = 1; i <= numPages; i++) {
			let pageNum = i;
			$('#nav').append('<a class="paginationmodal" href="#" rel="' + i + '">' + pageNum + '</a> ');
		}
		$('#table_instrumen_hi tbody tr').hide();
		$('#table_instrumen_hi tbody tr').slice(0, rowsShown[0]).show();
		$('#nav a:first').addClass('active');
		$('#nav a').bind('click', function() {

			$('#nav a').removeClass('active');
			$(this).addClass('active');
			let currPage = $(this).attr('rel');
			let startItem = 0;
			for (let i = 0; i < currPage - 1; i++) {
				startItem += rowsShown[i];
			}
			let endItem = startItem + rowsShown[currPage - 1];
			$('#table_instrumen_hi tbody tr').css('opacity', '0.0').hide().slice(startItem, endItem).
			css('display', 'table-row').animate({
				opacity: 1
			}, 300);
		});
	});

	function riwayat(id,komentar,kategori){
		var komentator = komentar.replace('komentar','komentator');
		var data = {
			id:id,
			komentar:$('#'+komentar).val(),
			kategori:kategori,
			id_pengajuan:'{{$pengajuan->id}}',
		};

		$.post("{{route('bunda-paud_hi-riwayat')}}",data,function(data){
			$('.modalin').html('');
			if(data.code=='200'){
				$('.modalin').html(data.content);
			}else{
				swal('Whooops',data.msg,'error');
			}
		});
	}

	function simpan_komentar(id,komentar,kategori){
		var komentator = komentar.replace('komentar','komentator');
		var data = {
			id:id,
			komentar:$('#'+komentar).val(),
			kategori:kategori,
			id_pengajuan:'{{$pengajuan->id}}',
		};

		$.post("{{route('bunda-paud_hi-simpan')}}",data,function(data){
			if(data.code=='200'){
				if(data.komentar==null){
					$('#'+komentar).val('');
					$('#'+komentator).html(data.komentator);
				}else{
					$('#'+komentar).val(data.komentar);
					$('#'+komentator).html(data.komentator);
				}
				swal('Success',data.msg,'success');
			}else{
				swal('Whooops',data.msg,'error');
			}
		});
	}
</script>