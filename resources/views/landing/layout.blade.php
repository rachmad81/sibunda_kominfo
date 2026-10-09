<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIBUNDA</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="shortcut icon" href="{{ asset('logo/logo-sibunda.png') }}" type="image/x-icon">
    <meta charset="UTF-8">
    <meta name="description"
        content="Sibunda surabaya, bunda bunda se kota surabaya, yg dipimpin istri walikota, dan istri-istri kepala kecamatan tiap kecamatan se kota surabaya">
    <meta name="keywords" content="sibunda, surabaya, sibunda surabaya, dinas pendidikan, dispendik">
    <meta name="author" content="Dinas Pendidikan Kota Surabaya">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        :root {
            --primary-color: #0d6efd;
        }

        body {
            background: #eee;
        }

        /* --- Custom Navbar Styles --- */
        .navbar-brand {
            font-weight: 700;
            letter-spacing: 1px;
        }

        /* Desktop Dropdown 3 Tingkat */
        @media (min-width: 992px) {
            .dropdown-menu .dropdown-menu {
                margin-left: 0;
                margin-right: 0;
            }

            .dropdown-item+.dropdown-menu {
                display: none;
                position: absolute;
                left: 100%;
                top: 0;
            }

            .dropdown-item:hover+.dropdown-menu,
            .dropdown-menu:hover>.dropdown-item+.dropdown-menu {
                display: block;
            }
        }

        /* --- Carousel Adjustments --- */
        .carousel-item {
            height: 50vh;
            /* min-height: 200px; */
            background: #333;
        }

        .carousel-item img {
            object-fit: contain;
            width: 100%;
        }

        .carousel-caption {
            bottom: 0%;
            z-index: 10;
        }

        /* --- Mobile Sidebar (Offcanvas) --- */
        @media (max-width: 991.98px) {
            .navbar-collapse {
                display: none !important;
            }

            .carousel-caption {
                bottom: 0%;
                z-index: 10;
            }
        }

        /* Custom spacing for sections */
        section {
            padding: 80px 0;
        }

        .navbar-brand {
            display: flex;
            align-items: center;
            text-decoration: none;
        }

        .brand-text {
            line-height: 0.5;
            /* Mengatur jarak antar baris teks */
        }

        .brand-text .title {
            color: #1a567e;
            /* Warna biru gelap sesuai gambar */
            font-weight: 800;
            /* Tebal */
            font-size: 1.5rem;
            /* Sesuaikan ukuran */
            display: block;
        }

        .brand-text .subtitle {
            color: #6c757d;
            /* Warna abu-abu */
            font-size: 1.1rem;
            font-weight: 400;
            display: block;
        }
    </style>
    @yield('css')
</head>

<body>

    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg sticky-top" style="background: #ffffff">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="{{ route('landing', ['home']) }}">
                <img src="{{ asset('logo/logo-sibunda.png') }}"
                    style="max-width: 80px; max-height: 65px; margin-right: 10px;">

                <div class="brand-text">
                    <span class="title">Bunda PAUD</span><br>
                    <span class="subtitle">Kota Surabaya</span>
                </div>
            </a>

            <!-- Mobile Toggle -->
            <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar"
                aria-controls="offcanvasNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Desktop Menu -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ isset($active_menu) && $active_menu == 'home' ? 'active' : '' }}"
                            href="{{ route('landing', ['home']) }}">
                            <i class="fa fa-home"></i>
                            Beranda
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ isset($active_menu) && $active_menu == 'menu_paud' ? 'active' : '' }}"
                            href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <i class="fa fa-building"></i>
                            PAUD
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <!-- Dropdown Tingkat 2 -->
                            <li>
                                <a class="dropdown-item {{ isset($active_submenu) && $active_submenu == 'paud' ? 'active' : '' }}"
                                    href="{{ route('landing', ['paud']) }}"><i class="fa fa-building"></i>
                                    Satuan PAUD
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item {{ isset($active_submenu) && $active_submenu == 'ijop' ? 'active' : '' }}"
                                    href="{{ route('landing', ['ijop']) }}">
                                    <i class="fa fa-check"></i>
                                    Daftar Ijop
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ isset($active_menu) && $active_menu == 'menu_ppa' ? 'active' : '' }}"
                            href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <i class="fa fa-female"></i>
                            SIAP PPAK
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <!-- Dropdown Tingkat 2 -->
                            <li>
                                <a class="dropdown-item" target="_blank"
                                    href="https://ppa-dp3appkb.surabaya.go.id/game">
                                    <i class="fa fa-gamepad"></i>
                                    Permainan Edukatif
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" target="_blank"
                                    href="https://ppa-dp3appkb.surabaya.go.id/konsultasi">
                                    <i class="fa fa-mobile"></i>
                                    Tele Konsultasi
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" target="_blank"
                                    href="https://ppa-dp3appkb.surabaya.go.id/layanan/pengaduan">
                                    <i class="fa fa-exclamation-circle"></i>
                                    Pengaduan Masyarakat
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" target="_blank" href="https://ppa-dp3appkb.surabaya.go.id/kie">
                                    <i class="fa fa-envelope"></i>
                                    KIE PAUD
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ isset($active_menu) && $active_menu == 'literasi' ? 'active' : '' }}"
                            href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <i class="fa fa-book-open"></i>
                            Literasi
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <!-- Dropdown Tingkat 2 -->
                            <li>
                                <a class="dropdown-item {{ isset($active_submenu) && $active_submenu == 'buku' ? 'active' : '' }}"
                                    href="{{ route('landing', ['buku']) }}">
                                    <i class="fa fa-book-open"></i>
                                    Buku
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item {{ isset($active_submenu) && $active_submenu == 'berkreasi' ? 'active' : '' }}"
                                    href="{{ route('landing', ['berkreasi']) }}">
                                    <i class="fa fa-book-open"></i>
                                    Berkreasi
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item {{ isset($active_menu) && $active_menu == 'berkreasi' ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('landing', ['kegiatan']) }}">
                            <i class="fa fa-clipboard"></i>
                            Kegiatan
                        </a>
                    </li>

                    <!-- Dropdown Tingkat 1 -->
                    {{-- <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            Layanan Kami
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <!-- Dropdown Tingkat 2 -->
                            <li class="position-relative">
                                <a class="dropdown-item d-flex justify-content-between align-items-center"
                                    href="#">
                                    Web Development <i class="bi bi-chevron-right ms-2 d-none d-lg-block"></i>
                                </a>
                                <ul class="dropdown-menu">
                                    <!-- Dropdown Tingkat 3 -->
                                    <li><a class="dropdown-item" href="#">Frontend (React)</a></li>
                                    <li><a class="dropdown-item" href="#">Backend (Node.js)</a></li>
                                    <li><a class="dropdown-item" href="#">Fullstack</a></li>
                                </ul>
                            </li>
                            <li class="position-relative">
                                <a class="dropdown-item d-flex justify-content-between align-items-center"
                                    href="#">
                                    Mobile Apps <i class="bi bi-chevron-right ms-2 d-none d-lg-block"></i>
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#">Android Native</a></li>
                                    <li><a class="dropdown-item" href="#">iOS Native</a></li>
                                    <li><a class="dropdown-item" href="#">Flutter</a></li>
                                </ul>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="#">Konsultasi Gratis</a></li>
                        </ul>
                    </li> --}}
                    @if (Auth::check())
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle {{ isset($active_menu) && $active_menu == 'literasi' ? 'active' : '' }}"
                                href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                <i class="fa fa-user"></i>
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <!-- Dropdown Tingkat 2 -->
                                <li>
                                    <a class="dropdown-item {{ isset($active_submenu) && $active_submenu == 'buku' ? 'active' : '' }}"
                                        href="{{ url('common/manajemen-data') }}">
                                        <i class="fa fa-cubes"></i>
                                        Manajemen Data
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('logout') }}">
                                        <i class="fa fa-sign-out"></i>
                                        Logout
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">
                                <i class="fa fa-sign-in"></i>
                                Login
                            </a>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </nav>

    <!-- MOBILE SIDEBAR (OFFCANVAS) -->
    <div class="offcanvas offcanvas-start" style="background: #10cfff" tabindex="-1" id="offcanvasNavbar"
        aria-labelledby="offcanvasNavbarLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasNavbarLabel">MENU</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"
                aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <ul class="navbar-nav justify-content-end flex-grow-1 pe-3">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('landing', ['home']) }}">
                        <i class="fa fa-home"></i>
                        Beranda
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="fa fa-user"></i>
                        Tentang
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <i class="fa fa-building"></i>
                        PAUD
                    </a>
                    <ul class="dropdown-menu border-0">
                        <li>
                            <a class="dropdown-item" href="{{ route('landing', ['paud']) }}">
                                <i class="fa fa-building"></i>
                                Satuan PAUD
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('landing', ['ijop']) }}">
                                <i class="fa fa-check"></i>
                                Daftar Ijop
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <i class="fa fa-female"></i>
                        PPA
                    </a>
                    <ul class="dropdown-menu border-0">
                        <!-- Dropdown Tingkat 2 -->
                        <li>
                            <a class="dropdown-item" target="_blank" href="https://ppa-dp3appkb.surabaya.go.id/game">
                                <i class="fa fa-gamepad"></i>
                                Permainan Edukatif
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" target="_blank"
                                href="https://ppa-dp3appkb.surabaya.go.id/konsultasi">
                                <i class="fa fa-mobile"></i>
                                Tele Konsultasi
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" target="_blank"
                                href="https://ppa-dp3appkb.surabaya.go.id/layanan/pengaduan">
                                <i class="fa fa-exclamation-circle"></i>
                                Pengaduan Masyarakat
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" target="_blank" href="https://ppa-dp3appkb.surabaya.go.id/kie">
                                <i class="fa fa-envelope"></i>
                                KIE PAUD
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <i class="fa fa-book-open"></i>
                        Literasi
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <!-- Dropdown Tingkat 2 -->
                        <li>
                            <a class="dropdown-item" href="{{ route('landing', ['buku']) }}">
                                <i class="fa fa-book-open"></i>
                                Buku
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('landing', ['berkreasi']) }}">
                                <i class="fa fa-book-open"></i>
                                Berkreasi
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('landing', ['kegiatan']) }}">
                        <i class="fa fa-clipboard"></i>
                        Kegiatan
                    </a>
                </li>
                @if (Auth::check())
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ isset($active_menu) && $active_menu == 'literasi' ? 'active' : '' }}"
                            href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa fa-user"></i>
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <!-- Dropdown Tingkat 2 -->
                            <li>
                                <a class="dropdown-item {{ isset($active_submenu) && $active_submenu == 'buku' ? 'active' : '' }}"
                                    href="{{ url('common/manajemen-data') }}">
                                    <i class="fa fa-cubes"></i>
                                    Manajemen Data
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('logout') }}">
                                    <i class="fa fa-sign-out"></i>
                                    Logout
                                </a>
                            </li>
                        </ul>
                    </li>
                @else
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">
                            <i class="fa fa-sign-in"></i>
                            Login
                        </a>
                    </li>
                @endif
                <!-- Mobile Accordion Menu (Alternative to nested dropdowns on mobile) -->
                {{-- <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        Layanan
                    </a>
                    <ul class="dropdown-menu border-0">
                        <li><a class="dropdown-item" href="#">Web Development</a></li>
                        <li><a class="dropdown-item" href="#">Mobile Apps</a></li>
                        <li><a class="dropdown-item" href="#">UI/UX Design</a></li>
                    </ul>
                </li> --}}
            </ul>
        </div>
    </div>

    @yield('content')

    <!-- FOOTER -->
    <footer class="bg-dark text-white py-4">
        <div class="container text-center">
            <p class="mb-0">&copy; 2026 Dinas Pendidikan Kota Surabaya.</p>
        </div>
    </footer>

    <!-- Bootstrap 5 JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <script>
        // Logika untuk menutup offcanvas saat link diklik (opsional)
        const navLinks = document.querySelectorAll('.offcanvas-body .nav-link');
        const offcanvasElement = document.getElementById('offcanvasNavbar');
        const bsOffcanvas = new bootstrap.Offcanvas(offcanvasElement);

        navLinks.forEach(link => {
            link.addEventListener('click', () => {
                if (!link.classList.contains('dropdown-toggle')) {
                    bsOffcanvas.hide();
                }
            });
        });

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>
    @yield('js')
</body>

</html>
