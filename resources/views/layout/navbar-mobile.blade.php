<style type="text/css">
    .active_menu a,
    .active_menu span {
        color: #f27474 !important;
    }
</style>
<nav class="navbar navbar-dark bg-primary navbar-expand fixed-bottom d-md-none d-lg-none d-xl-none p-0">
    <ul class="navbar-nav nav-justified w-100 list-panel">
        <li class="nav-item @if (isset($menu_active) && $menu_active == 'dashboard') active_menu @endif">
            <a href="{{ url('/landing/dashboard') }}" class="nav-link text-center">
                <i class="si si-home fa-2x"></i>
                <span class="small d-block">Dashboard</span>
            </a>
        </li>
        <!-- <li class="nav-item @if (isset($menu_active) && $menu_active == 'pokja') active_menu @endif">
            <a href="{{ url('/landing/pokja') }}" class="nav-link text-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-bank2" viewBox="0 0 16 16">
                    <path d="M8.277.084a.5.5 0 0 0-.554 0l-7.5 5A.5.5 0 0 0 .5 6h1.875v7H1.5a.5.5 0 0 0 0 1h13a.5.5 0 1 0 0-1h-.875V6H15.5a.5.5 0 0 0 .277-.916l-7.5-5zM12.375 6v7h-1.25V6h1.25zm-2.5 0v7h-1.25V6h1.25zm-2.5 0v7h-1.25V6h1.25zm-2.5 0v7h-1.25V6h1.25zM8 4a1 1 0 1 1 0-2 1 1 0 0 1 0 2zM.5 15a.5.5 0 0 0 0 1h15a.5.5 0 1 0 0-1H.5z"/>
                </svg>
                <span class="small d-block">Pokja</span>
            </a>
        </li> -->
        <li class="nav-item @if (isset($menu_active) && $menu_active == 'paud') active_menu @endif">
            <a href="{{ url('/landing/paud') }}" class="nav-link text-center">
                <i class="si si-magnifier fa-2x"></i>
                <span class="small d-block">PAUD Wil.</span>
            </a>
        </li>
        <li class="nav-item @if (isset($menu_active) && $menu_active == 'ijop') active_menu @endif">
            <a href="{{ url('/landing/ijop') }}" class="nav-link text-center">
                <i class="si si-book-open fa-2x"></i>
                <span class="small d-block">Ijop</span>
            </a>
        </li>
        {{-- <li class="nav-item @if (isset($menu_active) && $menu_active == 'kegiatan') active_menu @endif">
            <a href="{{url('/landing/kegiatan')}}" class="nav-link text-center">
                <i class="si si-badge fa-2x"></i>
                <span class="small d-block">Kegiatan</span>
            </a>
        </li> --}}
        <li class="nav-item @if (isset($menu_active) && in_array($menu_active, ['kegiatan', 'city_tour', 'buku'])) active_menu @endif dropup">
            <a href="#" class="nav-link text-center" role="button" id="dropdownMenuProfile"
                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="si si-menu fa-2x"></i>
                <span class="small d-block">Lainnya</span>
            </a>
            <!-- Dropup menu for profile -->
            <div class="dropdown-menu dropdown-menu-left" aria-labelledby="dropdownMenuProfile">
                <a class="dropdown-item" href="{{ route('landing', ['kegiatan']) }}">Kagiatan</a>
                {{-- <a class="dropdown-item" href="{{ route('landing',['city_tour']) }}">City Tour</a> --}}
                <a class="dropdown-item" href="{{ route('landing', ['buku']) }}">Buku</a>
                <a class="dropdown-item" href="{{ route('dp3a', ['aksi' => 'permainan']) }}" target="_blank">DP3A -
                    Permainan</a>
                <a class="dropdown-item" href="{{ route('dp3a', ['aksi' => 'telekonsultasi']) }}" target="_blank">DP3A -
                    Telekonsultasi</a>
                <a class="dropdown-item" href="https://ppa-dp3appkb.surabaya.go.id/layanan/pengaduan"
                    target="_blank">DP3A -
                    Pengaduan</a>
                <a class="dropdown-item" href="https://ppa-dp3appkb.surabaya.go.id/kie" target="_blank">DP3A -
                    KIE</a>
            </div>
        </li>
        {{-- <li class="nav-item @if (isset($menu_active) && $menu_active == 'profile') active_menu @endif dropup">
            <a href="#" class="nav-link text-center" role="button" id="dropdownMenuProfile" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" >
                <svg width="1.5em" height="1.5em" viewBox="0 0 16 16" class="bi bi-person" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M10 5a2 2 0 1 1-4 0 2 2 0 0 1 4 0zM8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm6 5c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4zm-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664h10z" />
                </svg>
                <span class="small d-block">Profile</span>
            </a>
            <!-- Dropup menu for profile -->
            <div class="dropdown-menu dropdown-menu-left" aria-labelledby="dropdownMenuProfile">
                <a class="dropdown-item" href="#">Edit Profile</a>
                <a class="dropdown-item" href="#">Notification</a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="#">Logout</a>
            </div>
        </li> --}}
    </ul>
</nav>
