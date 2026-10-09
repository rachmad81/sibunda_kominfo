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
@if (Auth::getUser()->role == 'bunda')
@include('bunda/layout/sidebar')
@elseif(Auth::getUser()->role == 'dinas')
@include('dinas/layout/sidebar')
@else
@include('penilai/layout/sidebar')
@endif
@endsection

@section('content')
<div class="content" id="app">
	<h2 class="content-heading" style="padding-top: 0px">Lomba Apresiasi Bunda Kelurahan/Kecamatan</h2>
	<!-- Dynamic Table Full -->
	<div class="block">
		<div class="block-header block-header-default row no-margin">
			<h3 class="block-title">Mohon maaf....</h3>
		</div>
		<div class="block-content block-content-full">
			@include('layout.alert')
			<div class="row">
				<div class="col-lg-12" style="overflow: auto;">
					<center>
						<h2>
							Mohon maaf 
						</h2>
						<div style="width: 75%">
							<img src="https://i.pinimg.com/originals/5f/01/65/5f01651f63c0b159563e1578d68eea50.png" style="'width: 100%">
						</div>
						<h2>
							Jadwal penilaian sudah ditutup.
						</h2>
					</center>
				</div>
			</div>
		</div>
	</div>
	@include('attention')
</div>
<div class="modal-page"></div>
<div>
	@include('bunda.lomba.panduan')
</div>
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
	
</script>
@endsection
