<!doctype html>
<html lang="en" class="no-focus">

<head>
    @include('layout/metadata')

    @include('layout/styles')

    @yield('additional_styles')

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
        integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <meta charset="UTF-8">
    <meta name="description"
        content="Sibunda surabaya, bunda bunda se kota surabaya, yg dipimpin istri walikota, dan istri-istri kepala kecamatan tiap kecamatan se kota surabaya">
    <meta name="keywords" content="sibunda, surabaya, sibunda surabaya, dinas pendidikan, dispendik">
    <meta name="author" content="Dinas Pendidikan Kota Surabaya">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<style>
    . website {
        display: block;
    }

    @media (max-width: 768px) {
        .website {
            display: none;
        }
    }
</style>

<body>

    <div id="page-container"
        class="sidebar-inverse side-scroll page-header-fixed page-header-glass page-header-inverse main-content-boxed">
        @include('layout.navbar-mobile')
        <!-- Header -->
        <header id="page-header">
            <!-- Header Content -->
            <div class="content-header">
                <!-- Left Section -->
                <div class="content-header-section">
                    <!-- Logo -->
                    <img src="{{ asset('logo/logo-sibunda.png') }}"
                        style="max-width: 80px; max-height: 65px; vertical-align: middle; padding-top: 3px;">
                    <!-- END Logo -->
                    <button type="button" class="btn btn-rounded btn-dual-secondary" data-toggle="layout"
                        data-action="header_search_on" style="line-height: 0">
                        <i class="fa fa-search mr-5"></i> Cari Satuan PAUD
                    </button>
                </div>
                <!-- END Left Section -->

                <!-- Right Section -->
                <div class="content-header-section">
                    <ul class="nav-main-header">
                        <li id="nav_dashboard">
                            <a href="{{ route('landing', ['dashboard']) }}"><i class="si si-home"></i>Dashboard</a>
                        </li>
                        <li id="nav_paud">
                            <a href="{{ route('landing', ['paud']) }}"><i class="si si-magnifier"></i>Satuan PAUD
                                Wil.</a>
                        </li>
                        <li id="nav_ijop">
                            <a href="{{ route('landing', ['ijop']) }}"><i class="si si-book-open"></i>Daftar Ijop</a>
                        </li>
                        <li id="nav_kegiatan">
                            <a href="{{ route('landing', ['kegiatan']) }}"><i class="si si-badge"></i>Kegiatan</a>
                        </li>
                        {{-- <li id="nav_edu">
								          <a href="{{ route('landing',['city_tour']) }}"><i class="si si-calendar"></i>City Tour</a>
							          </li> --}}
                        <li id="nav_buku">
                            <a href="{{ route('landing', ['buku']) }}"><i class="si si-book-open"></i>Buku</a>
                        </li>
                        <li id="nav_berkreasi">
                            <a href="{{ route('landing', ['berkreasi']) }}"><i class="si si-book-open"></i>BERKREASI</a>
                        </li>
                    </ul>
                    <div id="nav_dp3a" class="btn-group website" role="group">
                        <button type="button" class="btn btn-rounded btn-dual-secondary" id="page-header-user-dropdown"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="line-height: 0;">
                            DP3A
                            <i class="fa fa-angle-down"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right min-width-150"
                            aria-labelledby="page-header-user-dropdown">
                            <a class="dropdown-item" href="{{ route('dp3a', ['aksi' => 'permainan']) }}" target="_blank"
                                style="margin-bottom: 0px">
                                <i class="si si-arrow-right mr-2"></i> Permainan
                            </a>
                            <a class="dropdown-item" href="{{ route('dp3a', ['aksi' => 'telekonsultasi']) }}"
                                target="_blank" style="margin-bottom: 0px">
                                <i class="si si-arrow-right mr-2"></i> Telekonsultasi
                            </a>
                            <a class="dropdown-item" href="https://ppa-dp3appkb.surabaya.go.id/layanan/pengaduan"
                                target="_blank" style="margin-bottom: 0px">
                                <i class="si si-arrow-right mr-2"></i> Pengaduan
                            </a>
                            <a class="dropdown-item" href="https://ppa-dp3appkb.surabaya.go.id/kie" target="_blank"
                                style="margin-bottom: 0px">
                                <i class="si si-arrow-right mr-2"></i> KIE
                            </a>
                        </div>
                    </div>
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-rounded btn-dual-secondary" id="page-header-user-dropdown"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="line-height: 0;">
                            <i class="fa fa-user"></i> <span
                                class="d-none d-md-inline-block">{{ \Auth::check() ? \Auth::User()->nama : '' }}</span>
                            <i class="fa fa-angle-down"></i>
                        </button>
                        @if (\Auth::check())
                            <div class="dropdown-menu dropdown-menu-right min-width-200"
                                aria-labelledby="page-header-user-dropdown">
                                <h5 class="h6 text-center py-10 border-b text-uppercase">
                                    {{ \Auth::check() ? \Auth::User()->role : '' }}</h5>
                                <a class="dropdown-item" href="{{ route('common-manajemen-data') }}" target="_blank">
                                    <i class="si si-grid mr-2"></i> Manajemen Data
                                </a>

                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="{{ route('logout') }}">
                                    <i class="si si-logout mr-2"></i> Sign Out
                                </a>
                            </div>
                        @else
                            <div class="dropdown-menu dropdown-menu-right min-width-150"
                                aria-labelledby="page-header-user-dropdown">
                                <a class="dropdown-item" href="{{ route('login') }}" style="margin-bottom: 0px">
                                    <i class="si si-lock mr-2"></i> Login
                                </a>
                            </div>
                        @endif
                    </div>
                    <!-- END Toggle Sidebar -->
                </div>
                <!-- END Right Section -->
            </div>
            <!-- END Header Content -->

            <!-- Header Search -->
            <div id="page-header-search" class="overlay-header">
                <div class="content-header content-header-fullrow">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <!-- Close Search Section -->
                            <!-- Layout API, functionality initialized in Template._uiApiLayout() -->
                            <button type="button" class="btn btn-secondary px-15" data-toggle="layout"
                                data-action="header_search_off">
                                <i class="fa fa-times"></i>
                            </button>
                            <!-- END Close Search Section -->
                        </div>
                        <input type="text" class="form-control" placeholder="Nama Sekolah.."
                            id="page-header-search-input" name="search_paud">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-secondary px-15" onclick="cari_paud()">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- END Header Search -->

            <!-- Header Loader -->
            <div id="page-header-loader" class="overlay-header" style="background-color: #83bff0;">
                <div class="content-header content-header-fullrow text-center">
                    <div class="content-header-item">
                        <i class="fa fa-sun-o fa-spin text-white"></i>
                    </div>
                </div>
            </div>
            <!-- END Header Loader -->
        </header>
        <!-- END Header -->

        <!-- Main Container -->
        <main id="main-container" style="background-color: #f0f2f5; background: linear-gradient(#83bff0,white);">

            <!-- Header -->
            <div class="bg-primary" style="padding-top: 75px">
            </div>
            <!-- END Header -->

            @yield('content')

        </main>
        <!-- END Main Container -->

        <!-- Footer -->
        @include('layout.footer')
        <!-- END Footer -->
        @include('attention')
    </div>
    <!-- END Page Container -->
    <script src="{{ asset('assets/js/core/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/js/codebase.core.min.js') }}"></script>

    <script src="{{ asset('assets/js/codebase.app.min.js') }}"></script>

    <!-- Page JS Plugins -->
    <!-- <script src="{{ asset('assets/js/plugins/chartjs/Chart.bundle.min.js') }}"></script> -->

    <!-- Page JS Code -->
    <!-- <script src="{{ asset('assets/js/pages/db_corporate.min.js') }}"></script> -->

    <script type="text/javascript">
        function cari_paud() {
            var cari = $('input[name=search_paud]').val();

            if (cari.length > 3) {
                window.location = "{{ url('/') }}/landing/paud?page=0&cari=" + cari;
            } else {
                alert('Karakter harus lebih dari 3');
            }
        }
    </script>
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-DH4GE79502"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());

        gtag('config', 'G-DH4GE79502');
    </script>

    @yield('additional_js')

</body>

</html>
