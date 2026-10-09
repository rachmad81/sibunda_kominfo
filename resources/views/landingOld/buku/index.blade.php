@extends('landing.layout')

@section('additional_styles')
@endsection

@section('content')
<!-- Page Content -->
	
	<!-- Content -->
	<div class="content" style="padding-top: 0px; background-color: transparent;">
		<div class="my-20 text-center">
            <h2 style="margin-bottom: 0px; text-shadow: 3px 3px 6px white;">
                <b><i class="si si-book-open text-muted"></i> Buku-buku Sibunda</b>
            </h2>
        </div>
        <div style="margin-bottom: 5px">&nbsp;</div>
        <div class="row">
            @if( count($buku) > 0 )
            	@foreach($buku as $key => $value)
                <div class="col-md-6 col-xl-4 invisible" data-toggle="appear">
                    <!-- Property -->
                    <div class="block block-rounded">
                        <div class="block-content p-0 overflow-hidden">
                            <center>
                                <img src="{{ $value->cover }}" alt="" style="max-height: 218px; max-width: 100%; object-fit: contain;">
                                {{-- <iframe src="https://drive.google.com/file/d/1KVg8uAYGJsp98-jaVYObXFMIgyonY6kV/preview" width="640" height="480" allow="autoplay"></iframe> --}}
                            </center>
                        </div>
                        <div class="block-content border-bottom">
                            <h5><small><i class="fa fa-calendar"></i> {{ Waktu::parseFullDatetime($value->created_at) }}</small></h5>
                            <h4 class="font-size-h5 mb-10"><b>{{ $value->judul_buku }}</b></h4>
                            <a target="_blank" href="{{$value->file_buku}}" class="btn btn-sm btn-primary">Lihat buku</a>
                        </div>
                    </div>
                    <!-- END Property -->
                </div>
                @endforeach
            @else
            <div class="col-lg-12 col-12">
                <hr>
                <center>
                    <p style="font-size: 20px;">Belum ada buku yang diunggah.</p>
                </center>
            </div>
            @endif
    </div>
    <div class="row">
        <div class="col-12">
        	@if( count($buku) > 0 )
            {{ $buku->onEachSide(9)->links() }}
            @endif
        </div>
    </div>

    <div class="clearfix" style="margin-bottom: 50px"></div>
</div>

<div class="modal" id="modalMap" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Lokasi Sekolah (<span class="nama_sekolah"></span>)</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <iframe width="100%" height="300" id="map" src="" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"></iframe>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<div id="template_container">
    
</div>
@endsection

@section('additional_js')

<script type="text/javascript">
	@if(isset($menu_active))
    $('#nav_{{ $menu_active }}').children().addClass('active')
    @endif
</script>

@endsection