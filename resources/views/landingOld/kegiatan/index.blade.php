@extends('landing.layout')

@section('additional_styles')
@endsection

@section('content')
<!-- Page Content -->
	
	<!-- Content -->
	<div class="content" style="padding-top: 0px; background-color: transparent;">
		<div class="my-20 text-center">
            <h2 style="margin-bottom: 0px; text-shadow: 3px 3px 6px white;">
                <b><i class="si si-layers text-muted"></i> Daftar Kegiatan PAUD {!! \Auth::check() ? \Auth::User()->getTingkat() : 'Kota Surabaya' !!}</b>
            </h2>
        </div>
        <div style="margin-bottom: 5px">&nbsp;</div>
        <div class="row">
            @if( count($data) > 0 )
            	@foreach($data as $key => $value)
                <div class="col-md-6 col-xl-4 invisible" data-toggle="appear">
                    <!-- Property -->
                    <div class="block block-rounded">
                        <div class="block-content p-0 overflow-hidden">
                            <center>
                                <img src="{{ $value->url_foto }}" alt="" style="max-height: 218px; max-width: 100%; object-fit: contain;">
                            </center>
                        </div>
                        <div class="block-content border-bottom">
                            <h5><small><i class="fa fa-calendar"></i> {{ Waktu::parseFullDatetime($value->tgl_kegiatan.' '.$value->waktu_kegiatan) }}</small></h5>
                            <h4 class="font-size-h5 mb-10"><b>{{ $value->kegiatan }}</b></h4>
                            <p id="materi_{{$key}}" style="text-align: justify; line-height: 1.4;">
                                {{substr($value->materi,0,255)}} @if(strlen($value->materi)>255) <a href="javascript:void(0)" onclick="lengkap('materi_{{$key}}','{{base64_encode($value->materi)}}')">.. Selengkapnya</a> @endif
                            </p>
                            <p>
                                <small>
                                    <i class="fa fa-user"></i> <b>{{ $value->Pembuat->nama }}</b><br>
                                    ({{ $value->Pembuat->jabatanTingkatBunda() }})
                                </small>
                            </p>
                        </div>
                    </div>
                    <!-- END Property -->
                </div>
                @endforeach
            @else
            <div class="col-lg-12 col-12">
                <hr>
                <center>
                    <p style="font-size: 20px;">Tidak ada data sekolah yang cocok</p>
                </center>
            </div>
            @endif
    </div>
    <div class="row">
        <div class="col-12">
            {{ $data->onEachSide(1)->links() }}
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

    function show_map(url,nama_sekolah){
        $('#map').attr('src', url);
        $('.nama_sekolah').html(nama_sekolah);
        $('#modalMap').modal('show');
    }

    function detail(jenis, npsn){
        var url = '{{ route("common-get-list") }}'
        var nama_modal = ''

        switch(jenis){
            case 'pd':
                nama_modal = 'modalPD'
                break

            case 'ptk':
                nama_modal = 'modalPTK'
                break
        }
        
        $.ajax({
            url: url,
            type: 'get',
            data: {
                jenis : jenis,
                npsn : npsn,
            },
            success: function(data) {
                $('#template_container').html(data.view)
                $('#'+nama_modal).modal('show')
            }, 
            error:function (exception) {
                alert('gagal! pesan : '+exception.statusText)   
            }
        });
    }

    function lengkap(id,isi){
        isi = atob(isi);
        var mini = btoa(isi.substr(0,255));
        var materi = isi+' <a href="javascript:void(0)" onclick="sedikit(\''+id+'\',\''+mini+'\',\''+btoa(isi)+'\')"> Sedikit</a>';

        $('#'+id).html(materi);
    }

    function sedikit(id,mini,isi){
        isi = atob(isi);
        mini = atob(mini);
        var materi = mini+' <a href="javascript:void(0)" onclick="lengkap(\''+id+'\',\''+btoa(isi)+'\')"> Selengkapnya</a>';

        $('#'+id).html(materi);   
    }
</script>

@endsection