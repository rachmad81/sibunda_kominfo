@extends('landing.layout')

@section('additional_styles')
@endsection

@section('content')
    <!-- Page Content -->

    <!-- Content -->
    <div class="content" style="padding-top: 0px; background-color: transparent;">
        <div class="my-20 text-center">
            <h2 style="margin-bottom: 0px; text-shadow: 3px 3px 6px white;">
                <b>{{ $judul }}</b>
            </h2>
        </div>
        <div style="margin-bottom: 5px">&nbsp;</div>
        <div class="row">
            <div class="col-lg-12">
                {!! $content !!}
            </div>
        </div>
    </div>
@endsection

@section('additional_js')
    <script type="text/javascript">
        @if (isset($menu_active))
            $('#nav_{{ $menu_active }}').children().addClass('active')
        @endif
    </script>
@endsection
