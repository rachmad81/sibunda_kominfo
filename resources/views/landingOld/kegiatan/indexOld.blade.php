@extends('landing.layout')
@section('additional_styles')
@endsection
@section('content')
<div>
	<div class="content" style="padding-top: 0px; background: transparent;">
		<div id="page-container" class="main-content-boxed">

            <!-- Main Container -->
            <main id="main-container">

                <!-- Page Content -->
                <div class="hero" style="background-image: url('assets/media/various/bg-pattern-inverse.png');">
                    <div class="hero-inner">
                        <div class="content content-full">
                            <div class="text-center">
                                <i class="si si-chemistry text-primary display-3"></i>
                                <h1 class="h2 font-w700 mt-30 mb-10">We’ll be back soon!</h1>
                                <h2 class="h3 font-w400 text-muted mb-50">Fitur dalam proses..</h2>
                                <a class="btn btn-hero btn-noborder btn-rounded btn-alt-primary" href="{{ route('landing',['dashboard']) }}">
                                    <i class="fa fa-arrow-left mr-10"></i> Kembali Ke Halaman Utama
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- END Page Content -->

            </main>
            <!-- END Main Container -->
        </div>
	</div>
</div>
<script type="text/javascript">
    @if(isset($data["menu_active"]))
    $('#nav_{{ $data["menu_active"] }}').children().addClass('active')
    @endif
</script>
@endsection
