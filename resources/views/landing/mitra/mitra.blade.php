@extends('landing.layout')
@section('css')
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --accent-gradient: linear-gradient(135deg, #ff9a9e 0%, #fecfef 99%, #fecfef 100%);
            --glass-bg: rgba(255, 255, 255, 0.15);
            --glass-border: rgba(255, 255, 255, 0.3);
        }

        .custom-card {
            background: var(--glass-bg);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            overflow: hidden;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            transition: transform 0.3s ease;
        }

        .custom-card:hover {
            transform: translateY(-10px);
        }

        .card-header-img {
            height: 180px;
            background: var(--primary-gradient);
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .card-header-img::after {
            content: "";
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 50%;
            background: linear-gradient(to top, rgba(15, 23, 42, 0.1), transparent);
        }

        .profile-img {
            width: 110px;
            height: 110px;
            border-radius: 50%;
            border: 4px solid #fff;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
            object-fit: cover;
            position: absolute;
            bottom: -55px;
            z-index: 2;
        }

        .card-body {
            padding: 70px 30px 30px 30px;
            text-align: center;
            color: white;
        }

        .card-title {
            font-weight: 700;
            font-size: 1.5rem;
            margin-bottom: 5px;
            color: #f3687a;
        }

        .card-subtitle {
            font-weight: 300;
            color: #94a3b8;
            margin-bottom: 20px;
            letter-spacing: 1px;
            text-transform: uppercase;
            font-size: 0.85rem;
        }

        .card-text {
            font-size: 0.95rem;
            line-height: 1.6;
            color: #cbd5e1;
            margin-bottom: 25px;
        }

        .btn-maps {
            background: var(--primary-gradient);
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 50px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 10px 15px -3px rgba(118, 75, 162, 0.4);
        }

        .btn-maps:hover {
            box-shadow: 0 20px 25px -5px rgba(118, 75, 162, 0.5);
            transform: scale(1.05);
            color: white;
            filter: brightness(1.1);
        }

        .social-links {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 25px;
            border-top: 1px solid var(--glass-border);
            padding-top: 20px;
        }

        .social-icon {
            color: #94a3b8;
            transition: color 0.3s;
            cursor: pointer;
        }

        .social-icon:hover {
            color: #fff;
        }
    </style>
@endsection
@section('content')
    <div class="container">
        <div class="row bg-light">
            <div class="col-lg-12">
                <div class="row">
                    <div class="col-lg-12 d-flex justify-content-center">
                        <h1>Daftar Mitra</h1>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="d-flex justify-content-between">
                            <div>
                                <a href="#" class="btn btn-primary" onclick="tambah(0)">
                                    <i class="fa fa-plus"></i> Daftar Mitra
                                </a>
                            </div>
                            <div>
                                <div class="input-group mb-3">
                                    <input type="text" class="form-control" placeholder="Cari Mitra" name="cari_mitra"
                                        value="{!! $cari !!}">
                                    <span class="input-group-text" id="basic-addon1" onclick="cari()"><i
                                            class="fa fa-search"></i></span>
                                </div>
                            </div>
                        </div>
                        @if (Session::has('message'))
                            <div class="alert alert-success">
                                {!! Session::get('message') !!}
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $err)
                                        <li>{{ $err }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="row" style="margin-top: 20px">
                    @foreach ($mitra as $s)
                        <div class="col-lg-4 d-flex justify-content-center">
                            <div class="custom-card">
                                <!-- Header Image Area -->
                                <div class="card-header-img">
                                    <img src="#" alt="Foto Profil" class="profile-img"
                                        onerror="this.onerror=null;this.src='{{ asset('logo/image-icon.png') }}';">
                                    <div style="position: absolute; top: 15px; right: 20px; color: rgba(255,255,255,0.5)">
                                        <i data-lucide="more-horizontal"></i>
                                    </div>
                                </div>

                                <div class="card-body">
                                    <h2 class="card-title">{{ $s->nama_mitra }}</h2>
                                    <div class="card-subtitle" style="color: #000">({!! $s->status_aktif !!})</div>

                                    <p class="card-text" style="text-align: left;color: #000">
                                        <i class="fa fa-map-marker"></i> {{ $s->alamat }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="row" style="margin: 20px 0px;overflow:auto">
                    <div class="col-lg-12 d-lg-flex justify-content-center">
                        {!! $mitra->appends(request()->query())->onEachSide(1)->links() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-page"></div>
@endsection
@section('js')
    <script>
        function tambah(id) {
            var data = {
                id: id,
            };
            $.post('{{ route('landing', ['form_mitra']) }}', data, function(data) {
                $('.modal-page').html(data.content);
            }).fail(function() {
                alert('500 - Internal Server Error');
            });
        }

        function cari() {
            var cari = $('input[name=cari_mitra]').val();
            if (cari.length >= 1 && cari.length <= 5) {
                alert('Minimal 5 karakter');
            } else {
                window.location = '{{ route('landing', ['mitra']) }}?nama_mitra=' + cari;
            }
        }
    </script>
@endsection
