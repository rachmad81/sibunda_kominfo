@extends('layout.index')

@section('additional_styles')
<title class="judul"></title>
@endsection

@section('additional_styles')
<style>
	tr td {
		text-align:center;
	}
	.modal{overflow: auto;}
</style>
@endsection
@section('sidebar')
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">
@include('dinas/layout/sidebar')
@endsection

@section('content')
<div class="content" id="app">
	<h2 class="content-heading" style="padding-top: 0px">Rekap Besar PAUD</h2>
	<!-- Dynamic Table Full -->
	<div class="block">
		<div class="block-header block-header-default row no-margin">
			<h3 class="block-title">Rekap Besar PAUD</h3>
		</div>
		<div class="block-content block-content-full">
			<div class="col-lg-12">
				<div class="row">
	                <div class="col-md-2">
                        <label>Kecamatan</label>
                        <select id="kecamatan" name="kecamatan" onchange="get_kelurahan();get_data();"
                            @if (in_array(Auth::getUser()->tingkat, ['kecamatan', 'kelurahan'])) disabled @endif class="form-control">
                        </select>
	                </div>
	                <div class="col-md-2">
                        <label>Kelurahan</label>
                        <select id="kelurahan" name="kelurahan" onchange="get_data()" 
                            @if (in_array(Auth::getUser()->tingkat, ['kelurahan'])) disabled @endif class="form-control">
                        </select>
	                </div>
				</div>
			</div>
			@include('layout.alert')
			<div><center><h1 class="judul"></h1></center></div>
			<div class="tempat_rekap">
					
			</div>
			<div class="loading">
				Sedang menghitung data.......
			</div>
			<div class="modal-page"></div>
		</div>
	</div>
	@include('dinas.user.modal')
</div>
@endsection

@section('additional_scripts')
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/rowgroup/1.2.0/js/dataTables.rowGroup.min.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		$('.loading').hide();
		$('.tempat_rekap').show();
        get_kecamatan();
        get_kelurahan();
        get_data();
	});

	let kecamatanSelect = document.getElementById("kecamatan");
    let kelurahanSelect = document.getElementById("kelurahan");
    let kecamatans = {!! $kecamatans !!};

    function get_kecamatan() {
        kecamatanSelect.innerHTML = "";
        @if (in_array(request()->user()->tingkat, ['kota']))
            kecamatanSelect.innerHTML += `<option value="all" selected>Semua Kecamatan</option>`;
        @endif
        kecamatans.forEach(kecamatan => {
            kecamatanSelect.innerHTML +=
                `<option value="${kecamatan.kecamatan_kode}">${kecamatan.kecamatan_dispenduk}</option>`;
        });
    }

    function get_kelurahan() {
        kelurahanSelect.innerHTML = "";
        @if (in_array(request()->user()->tingkat, ['kota', 'kecamatan']))
            kelurahanSelect.innerHTML += `<option value="all" selected>Semua Kelurahan</option>`;
        @endif
        if (kecamatanSelect.value != "") {
            kecamatans.forEach(kecamatan => {
                var kelurahans = JSON.parse(kecamatan.kelurahans);
                kelurahans.forEach(kelurahan => {
                    if (kecamatan.kecamatan_kode == kecamatanSelect.value) {
                        kelurahanSelect.innerHTML +=
                            `<option value="${kelurahan.kelurahan_kode}">${kelurahan.kelurahan_dispenduk}</option>`;
                    }
                })
            });
        }
    }

	function get_data(load=''){
		$('.loading').show();
		$('.tempat_rekap').hide();

		var jenjang = $('#jenjang').val();
		var kecamatan = $('#kecamatan').val();
		var kelurahan = $('#kelurahan').val();
		var data = {
			kecamatan:kecamatan,
			kelurahan:kelurahan,
			load:load,
		};
		$.post('{{route('bunda-rekap_besar-get_data')}}',data,function(data){
			$('.loading').hide();
			$('.tempat_rekap').show();
			$('.tempat_rekap').html(data.content);
			$('#myData').DataTable();
		}).fail(function(){
			$('.loading').hide();
			$('.tempat_rekap').show();
		});
	}

	function detail_data(jenis,param){
		$('.loading').show();
		$('.tempat_rekap').hide();

		var jenjang = $('#jenjang').val();
		var data = {
			jenis:jenis,
			param:param,
			jenjang:jenjang,
		};
		$.post('{{route('bunda-rekap_profil-detail_data')}}',data,function(data){
			$('.loading').hide();
			$('.tempat_rekap').show();
			$('.modal-page').html(data.content);
		}).fail(function(){
			$('.loading').hide();
			$('.modal-page').show();
		});
	}
</script>
@endsection
