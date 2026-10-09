<!-- Side Navigation -->
<div class="content-side content-side-full">
    <ul class="nav-main">
        {{-- <li class="nav-main-heading" style="padding-top: 6px">
            <span class="sidebar-mini-visible">Manajemen Data</span>
            <span class="sidebar-mini-hidden">Manajemen Data</span>
        </li>
        <li>
            <a id="sidebar-user" href="{{ Route('bunda-user-index') }}">
                <i class="si si-users"></i>
                <span class="sidebar-mini-hide">User </span>
            </a>
        </li>
        <hr> --}}
        <!-- <li class="nav-main-heading" style="padding-top: 6px">
            <span class="sidebar-mini-visible">Informasi Biodata</span>
            <span class="sidebar-mini-hidden">Informasi Biodata</span>
        </li> -->
        <li>
            @php
                $role = Auth::getUSer()->role;
            @endphp
            <a href="{{ route($role . '-dashboard-index') }}" id="sidebar-beranda">
                <i class="si si-home"></i>
                <span class="sidebar-mini-hide">Beranda</span>
            </a>
        </li>
        {{-- <li>
            <a id="sidebar-profil" href="{{ Route('common-profil-index') }}">
                <i class="si si-user"></i>
                <span class="sidebar-mini-hide">Profil </span>
            </a>
        </li> --}}
        <li>
            <a id="sidebar-berkas" href="{{ Route('common-berkas-index') }}">
                <i class="si si-docs"></i>
                <span class="sidebar-mini-hide">Berkas </span>
            </a>
        </li>
        <li>
            <a id="sidebar-anggota" href="{{ Route('keanggotaan-halaman', ['halaman' => 'main']) }}">
                <i class="si si-users"></i>
                <span class="sidebar-mini-hide">Keanggotaan </span>
            </a>
        </li>
        <li>
            <a id="sidebar-program" href="{{ Route('common-program-index') }}">
                <i class="fa fa-window-restore"></i>
                <span class="sidebar-mini-hide">Program Kerja </span>
            </a>
        </li>
        <li>
            <a id="sidebar-kegiatan" href="{{ Route('common-kegiatan-index') }}">
                <i class="fa fa-align-left"></i>
                <span class="sidebar-mini-hide">Kegiatan </span>
            </a>
        </li>
        <li>
            <a class="btn btn-hero btn-sm btn-noborder btn-rounded btn-alt-primary"
                href="{{ route('landing', ['dashboard']) }}" style="margin: 10px">
                <i class="fa fa-arrow-left"></i> Kembali Ke Dashboard
            </a>
        </li>
    </ul>
</div>
<style type="text/css">
    .a_menu a {
        color: black !important;
    }

    .a_menu a:hover {
        color: black !important;
        background: #777 !important;
    }
</style>
<!-- END Side Navigation
