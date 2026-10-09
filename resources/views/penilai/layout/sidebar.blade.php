<!-- Side Navigation -->
<div class="content-side content-side-full">
    <ul class="nav-main">
        <li class="nav-main-heading" style="padding-top: 6px">
            <span class="sidebar-mini-visible">Manajemen Data</span>
            <span class="sidebar-mini-hidden">Manajemen Data</span>
        </li>
        <li>
            <a id="sidebar-dashboard" href="{{ Route('penilai-dashboard-index') }}">
                <i class="si si-home"></i>
                <span class="sidebar-mini-hide">Dasboard </span>
            </a>
        </li>
        <li>
            <a id="sidebar-penilaian" data-toggle="dropdown" href="javascript:void(0)">
                <i class="fa fa-clipboard"></i>
                <span class="sidebar-mini-hide">Penilaian <i class="fa fa-caret-down"></i></span>
            </a>
            <ul class="dropdown-menu" role="menu" aria-labelledby="sidebar-rekap" style="height: auto;background: #ccc;">
                <li class="a_menu" style="opacity:1 !important;transform: translateY(0px) !important;"><a role="menuitem" href="{{route('penilai-penilaian-tahap1-index',['tahun'=>date('Y')])}}">Tahap 1</a></li>
                <li class="a_menu" style="opacity:1 !important;transform: translateY(0px) !important;"><a role="menuitem" href="{{route('penilai-penilaian-tahap2-index',['tahun'=>date('Y')])}}">Tahap 2</a></li>
            </ul>
        </li>
        <li>
            <a class="btn btn-hero btn-sm btn-noborder btn-rounded btn-alt-primary" href="{{ route('landing', ['dashboard']) }}" style="margin: 10px">
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
