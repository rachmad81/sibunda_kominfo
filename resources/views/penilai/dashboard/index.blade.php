@extends('layout.index')


@section('additional_styles')
<style>
	tr>th>td {
		text-align: center;
	}
	.aktif-form{
		background: #ccc;
	}
</style>
@endsection
@section('sidebar')
@include('penilai/layout/sidebar')
@endsection

@section('content')
<div class="content" id="app">
	<h2 class="content-heading" style="padding-top: 0px">Dashboard </h2>
	<!-- Dynamic Table Full -->
	<div class="block">
		<div class="block-header block-header-default row no-margin">
			<h3 class="block-title">Dashboard </h3>
		</div>
		<div class="block-content block-content-full">
			@include('layout.alert')
			<div class="row">
				<div class="col-lg-3">
					<div class="text-center" style="background: #94c1f6;padding: 20px;color: black;">
						<b class="fa fa-clipboard" style="font-weight: bold;font-size: 50pt;"></b>
						<br>
						<span style="font-size: 20pt;">Penilaian Tahap 1</span>
					</div>
					<a href="{{route('penilai-penilaian-tahap1-index',['tahun'=>date('Y')])}}" style="width: 100%;color: black !important" href="#">
						<div class="text-right" style="background: #7fa4d0;margin: 0;padding: 0;">
								Menuju halaman <i class="fa fa-arrow-right"></i>
						</div>
					</a>
				</div>
				<div class="col-lg-3">
					<div class="text-center" style="background: #94c1f6;padding: 20px;color: black;">
						<b class="fa fa-clipboard" style="font-weight: bold;font-size: 50pt;"></b>
						<br>
						<span style="font-size: 20pt;">Penilaian Tahap 2</span>
					</div>
					<a href="{{route('penilai-penilaian-tahap2-index',['tahun'=>date('Y')])}}" style="width: 100%;color: black !important" href="#">
						<div class="text-right" style="background: #7fa4d0;margin: 0;padding: 0;">
								Menuju halaman <i class="fa fa-arrow-right"></i>
						</div>
					</a>
				</div>
			</div>
		</div>
	</div>
	@include('attention')
</div>
<div class="modal-page"></div>
@endsection

@section('additional_scripts')
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
<style type="text/css">
	body {
		overflow: auto !important;
	}

	.modal {
		overflow: auto !important;
	}

	.dt-left {
		text-align: left;
	}
</style>
<script type="text/javascript">
	$(document).ready(function(){
		$('#sidebar-dashboard').addClass('active');
	});
</script>
@endsection
