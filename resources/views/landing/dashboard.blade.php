@extends('landing.layout')
@section('css')
    <style>
        .display-3 {
            font-size: 40pt
        }

        @media (max-width: 991.98px) {
            .display-3 {
                font-size: 20pt;
            }

            .lead {
                font-size: 10pt;
            }
        }

        .menu-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            padding: 40px;
            max-width: 1200px;
        }

        .menu-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 110px;
            text-align: center;
            cursor: pointer;
            transition: transform 0.2s ease;
        }

        .menu-item:hover {
            transform: translateY(-5px);
        }

        /* Lingkaran Luar (Ring tipis) */
        .icon-wrapper {
            position: relative;
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 12px;
            background: white;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }

        /* Lingkaran Berwarna (Inner) */
        .icon-circle {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
            font-size: 28px;
            position: relative;
        }

        /* Overlay untuk efek ring berwarna transparan di sekeliling */
        .icon-wrapper::before {
            content: '';
            position: absolute;
            top: -4px;
            left: -4px;
            right: -4px;
            bottom: -4px;
            border-radius: 50%;
            border: 4px solid currentColor;
            opacity: 0.15;
            pointer-events: none;
        }

        .label {
            font-size: 11px;
            font-weight: 700;
            color: #2c3e50;
            line-height: 1.3;
            text-transform: uppercase;
        }

        /* Variasi Warna Berdasarkan Gambar */
        .pink {
            color: #e91e63;
        }

        .pink .icon-circle {
            background: linear-gradient(135deg, #f06292, #e91e63);
        }

        .blue-purple {
            color: #5c6bc0;
        }

        .blue-purple .icon-circle {
            background: linear-gradient(135deg, #7986cb, #5c6bc0);
        }

        .teal {
            color: #009688;
        }

        .teal .icon-circle {
            background: linear-gradient(135deg, #4db6ac, #009688);
        }

        .green {
            color: #43a047;
        }

        .green .icon-circle {
            background: linear-gradient(135deg, #66bb6a, #43a047);
        }

        .orange {
            color: #ffb300;
        }

        .orange .icon-circle {
            background: linear-gradient(135deg, #ffca28, #ffb300);
        }

        .purple {
            color: #7e57c2;
        }

        .purple .icon-circle {
            background: linear-gradient(135deg, #9575cd, #7e57c2);
        }

        .light-blue {
            color: #03a9f4;
        }

        .light-blue .icon-circle {
            background: linear-gradient(135deg, #4fc3f7, #03a9f4);
        }

        .red {
            color: #d32f2f;
        }

        .red .icon-circle {
            background: linear-gradient(135deg, #e57373, #d32f2f);
        }
    </style>
    </style>
@endsection
@section('content')
    <div class="container">
        <div style="height: 10px"></div>
        <!-- CAROUSEL (Hero Section) -->
        <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-indicators">
                @php
                    $nomor = 0;
                @endphp
                @foreach ($user as $u1)
                    @foreach ($u1 as $u)
                        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="{{ $nomor }}"
                            class="{{ $nomor == 0 ? 'active' : '' }}"></button>
                        @php
                            $nomor++;
                        @endphp
                    @endforeach
                @endforeach
            </div>
            <div class="carousel-inner">
                @php
                    $nomor = 0;
                @endphp
                @endphp
                @foreach ($user as $u1)
                    @foreach ($u1 as $u)
                        <div class="carousel-item {{ $nomor == 0 ? 'active' : '' }}">
                            <img src="{{ asset('bg_carousel.png') }}" class="d-block" style="width: 100%" alt="Slide 1">
                            <div class="carousel-caption">
                                <div class="row align-items-center" style="padding: 50px;background: #00000099">
                                    <div class="col-lg-6">
                                        <img src="{{ asset('bg_asset/transparent.png') }}"
                                            style="width: 100%;height: 30vh;background-image: url('{!! $u['foto'] !!}');background-size: contain;background-position: center;background-repeat: no-repeat;"
                                            alt="user_{{ $nomor }}"
                                            onerror="this.style.backgroundImage = 'url(https://upload.wikimedia.org/wikipedia/commons/b/ba/City_of_Surabaya_Logo.svg)';">
                                    </div>
                                    <div class="col-lg-6">
                                        <h1 class="display-3 fw-bold">{{ $u['nama'] }}</h1>
                                        <p class="lead fw-bold">{{ $u['wilayah'] }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @php
                            $nomor++;
                        @endphp
                    @endforeach
                @endforeach
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
            </button>
        </div>

        <!-- MAIN CONTENT -->
        <main>
            <section id="tentang" class="bg-light">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="d-flex d-flex justify-content-center">
                                <h1 class="display-3 fw-bold">Layanan Kami</h1>
                            </div>
                            <div class="d-flex d-flex justify-content-center">
                                <p class="lead">
                                    Akses cepat ke berbagai layanan Bunda PAUD
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="menu-container">
                                <!-- Profil -->
                                <div class="menu-item pink">
                                    <div class="icon-wrapper">
                                        <div class="icon-circle">
                                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                stroke-linejoin="round">
                                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                                <circle cx="12" cy="7" r="4"></circle>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="label">Profil<br>Bunda PAUD</div>
                                </div>

                                <!-- Mitra -->
                                <div class="menu-item blue-purple"
                                    onclick="window.location='{{ route('landing', ['mitra']) }}'">
                                    <div class="icon-wrapper">
                                        <div class="icon-circle">
                                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                stroke-linejoin="round">
                                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                                <circle cx="9" cy="7" r="4"></circle>
                                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="label">Mitra<br>PAUD</div>
                                </div>

                                <!-- Konsultasi -->
                                <div class="menu-item teal">
                                    <div class="icon-wrapper">
                                        <div class="icon-circle">
                                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                stroke-linejoin="round">
                                                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z">
                                                </path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="label">Konsultasi<br>PAUD HI</div>
                                </div>

                                <!-- Tele Konsultasi -->
                                <div class="menu-item green"
                                    onclick="window.open('https://ppa-dp3appkb.surabaya.go.id/konsultasi')">
                                    <div class="icon-wrapper">
                                        <div class="icon-circle">
                                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                stroke-linejoin="round">
                                                <path
                                                    d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z">
                                                </path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="label">Tele<br>Konsultasi</div>
                                </div>

                                <!-- Peminjaman -->
                                <div class="menu-item orange"
                                    onclick="window.location='{{ route('landing', ['sewa_gedung']) }}'">
                                    <div class="icon-wrapper">
                                        <div class="icon-circle">
                                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                stroke-linejoin="round">
                                                <rect x="4" y="2" width="16" height="20" rx="2"
                                                    ry="2"></rect>
                                                <path d="M9 22v-4h6v4"></path>
                                                <path d="M8 6h.01"></path>
                                                <path d="M16 6h.01"></path>
                                                <path d="M8 10h.01"></path>
                                                <path d="M16 10h.01"></path>
                                                <path d="M8 14h.01"></path>
                                                <path d="M16 14h.01"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="label">Peminjaman<br>GRHA PAUD</div>
                                </div>

                                <!-- Permainan -->
                                <div class="menu-item purple"
                                    onclick="window.open('https://ppa-dp3appkb.surabaya.go.id/game')">
                                    <div class="icon-wrapper">
                                        <div class="icon-circle">
                                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                stroke-linejoin="round">
                                                <polyline points="21 8 21 21"></polyline>
                                                <polyline points="3 8 3 21"></polyline>
                                                <line x1="3" y1="8" x2="21" y2="8">
                                                </line>
                                                <line x1="12" y1="3" x2="12" y2="8">
                                                </line>
                                                <path d="M12 3L5 8"></path>
                                                <path d="M12 3L19 8"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="label">Permainan<br>Edukatif</div>
                                </div>

                                <!-- KIE -->
                                <div class="menu-item light-blue"
                                    onclick="window.open('https://ppa-dp3appkb.surabaya.go.id/kie')">
                                    <div class="icon-wrapper">
                                        <div class="icon-circle">
                                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                stroke-linejoin="round">
                                                <path d="M4 22V4c0-1.1.9-2 2-2h12c1.1 0 2 .9 2 2v18"></path>
                                                <path d="M8 18h8"></path>
                                                <path d="M8 14h8"></path>
                                                <path d="M8 10h8"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="label">KIE<br>PAUD</div>
                                </div>

                                <!-- Bunda TV -->
                                <div class="menu-item red">
                                    <div class="icon-wrapper">
                                        <div class="icon-circle">
                                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                stroke-linejoin="round">
                                                <circle cx="12" cy="12" r="10"></circle>
                                                <polygon points="10 8 16 12 10 16 10 8"></polygon>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="label">Bunda<br>TV</div>
                                </div>

                                <!-- Pengaduan -->
                                <div class="menu-item pink"
                                    onclick="window.open('https://ppa-dp3appkb.surabaya.go.id/layanan/pengaduan')">
                                    <div class="icon-wrapper">
                                        <div class="icon-circle">
                                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                stroke-linejoin="round">
                                                <path d="M11 5L6 9H2v6h4l5 4V5z"></path>
                                                <path d="M19.07 4.93a10 10 0 0 1 0 14.14"></path>
                                                <path d="M15.54 8.46a5 5 0 0 1 0 7.07"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="label">Pengaduan<br>Masyarakat</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            {{-- <section class="bg-light">
                <div class="container">
                    <div class="row align-items-center">
                        <div class="col-lg-6">
                            <h2 class="fw-bold mb-4">SIBUNDA?</h2>
                            <p class="text-muted">
                                SIBUNDA adalah sebuah aplikasi monitoring kegiatan BUNDA-BUNDA Kelurahan dan Kecamatan di
                                Kota
                                Surabaya.
                            </p>
                        </div>
                        <div class="col-lg-6 mt-4 mt-lg-0">
                            <img src="{{ asset('logo/logo-sibunda.png') }}" class="img-fluid rounded shadow"
                                alt="Tentang Kami">
                        </div>
                    </div>
                </div>
            </section> --}}

            <!-- CTA SECTION -->
            <section class="text-white text-center py-5">
                <div class="contain">
                    <img src="{{ asset('logo/pkk.png') }}" alt="Pkk" style="width:100%">
                </div>
            </section>
        </main>
    </div>
@endsection
