@extends('landing.layout')
@section('additional_styles')
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
        integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <style type="text/css">
        .carousel * {
            color: #000 !important;
        }

        .carousel-item img {
            width: 100%;
            height: 50vh;
            background-size: contain;
            background-position: center center;
            background-repeat: no-repeat;
        }

        .carousel-caption {
            background: #AFEEEE;
            opacity: 0.7;
            border-radius: 15px;
            padding: 15px !important;
            position: relative !important;
            width: 70%;
        }

        .carousel-indicators li {
            background-color: #ccc !important;
        }

        .carousel-indicators .active {
            background-color: #000 !important;
        }

        .wadah_carousel {
            margin-top: 15vh;
        }

        @media (max-width: 576px) {
            .carousel-item img {
                width: 100%;
                margin-top: 50px;
                max-height: 60vh;
            }

            .carousel {
                max-height: 70vh !important;
            }

            .carousel-caption {
                left: 2% !important;
                right: 2% !important;
                width: 96%;
            }

            .wadah_carousel {
                margin-top: 0vh;
            }
        }
    </style>
@endsection
@section('content')

    <!-- Page Content -->
    <div>

        <!-- Content -->
        <div class="content">
            <div class="wadah_carousel">
                <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
                    <ol class="carousel-indicators">
                        <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
                        @if (count($bunda) != 0)
                            @foreach ($bunda as $k => $v)
                                <li data-target="#carouselExampleIndicators" data-slide-to="{{ $k + 1 }}"></li>
                            @endforeach
                        @endif
                    </ol>
                    <div class="carousel-inner">
                        @php
                            $url_foto_sambutan = $sambutan['foto'];
                        @endphp
                        <div class="carousel-item active">
                            <img class="d-block w-200" src="{{ asset('bg_asset/transparent.png') }}"
                                style="background-image: url('{{ $url_foto_sambutan }}'); margin-bottom: 25px"
                                alt="First slide">
                            <div class="sambutan carousel-caption">
                                <h5 style="margin: 0px;">
                                    <b>Selamat Datang Di SI Bunda Kota Surabaya</b>
                                </h5>
                                {{-- <h5 style="margin: 0px;">
								<b>{{ $sambutan['judul'] }}</b>
							</h5>
							<p style="margin: 1px">{!! nl2br($sambutan['kata']) !!}</p>
							<p style="margin: 1px">{!! '- '.$sambutan['nama'] !!}</p> --}}
                            </div>
                        </div>
                    </div>
                    <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev"
                        style="text-shadow: 4px 4px 6px grey;">
                        <i class="si si-arrow-left fa-2x"></i>
                    </a>
                    <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next"
                        style="text-shadow: 4px 4px 6px grey;">
                        <i class="si si-arrow-right fa-2x"></i>
                    </a>
                </div>
            </div>
        </div>
        <!-- END Content -->
        @include('attention');

        <!-- END Page Content -->
    @endsection
    @section('additional_js')
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"
            integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous">
        </script>
        <script type="text/javascript">
            @if (isset($menu_active))
                $('#nav_{{ $menu_active }}').children().addClass('active')
            @endif

            var id_bunda = [];

            $(document).ready(function() {
                @if (count($bunda) != 0)
                    @foreach ($bunda as $k => $v)
                        id_bunda.push('{{ $v->id }}');
                    @endforeach
                @endif
                if (id_bunda.length != 0) {
                    baris(0);
                }
            });

            function baris(i) {
                if ((i + 1) <= id_bunda.length) {
                    $.get("{{ route('landing', ['halaman' => 'data_bunda']) }}", {
                        id: id_bunda[i],
                        i: i
                    }, function(data) {
                        $('.carousel-inner').append(data.content);
                        baris((i + 1));
                    }).fail(function() {
                        baris(i);
                    });
                }
            }
        </script>
    @endsection
