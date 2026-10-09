<!-- Side Navigation -->
<div class="content-side content-side-full">
    <ul class="nav-main">
        <li>
            <a id="sidebar-beranda" href="{{ route('bunda-beranda-index') }}">
                <i class="si si-home"></i>
                <span class="sidebar-mini-hide">Beranda</span>
            </a>
        </li>
        <li>
            <a id="sidebar-kegiatan_pelatihan" href="{{ route('bunda-pelatihan-index') }}">
                <i class="fa fa-window-restore"></i>
                <span class="sidebar-mini-hide">Pelatihan</span>
            </a>
        </li>
        <li>
            <a id="sidebar-educity" href="{{ route('bunda-edu-index') }}">
                <i class="fa fa-window-restore"></i>
                <span class="sidebar-mini-hide">Edu City Tour</span>
            </a>
        </li>
        <li>
            <a id="sidebar-" href="{{ route('bunda-fasilitasi-index') }}">
                <i class="fa fa-window-restore"></i>
                <span class="sidebar-mini-hide">Fasilitasi</span>
            </a>
        </li>
        <li>
            <a id="sidebar-" href="{{ route('bunda-vaksinasi-index') }}">
                <i class="fa fa-window-restore"></i>
                <span class="sidebar-mini-hide">Data Vaksinasi</span>
            </a>
        </li>
        <li>
            <a id="sidebar-program" href="{{ Route('common-program-index') }}">
                <i class="fa fa-align-left"></i>
                <span class="sidebar-mini-hide">Timeline Program </span>
            </a>
        </li>
        <li>
            <a id="sidebar-kegiatan" href="{{ Route('common-kegiatan-index') }}">
                <i class="fa fa-window-restore"></i>
                <span class="sidebar-mini-hide">Kegiatan </span>
            </a>
        </li>
        <li>
            <a id="sidebar-dashboard_asw" href="{{ route('bunda-dashboard_asw-main') }}">
                <i class="fa fa-align-left"></i>
                <span class="sidebar-mini-hide">Dashboard ASW</span>
            </a>
        </li>
        @if (Auth::getUser()->role == 'opd' && Auth::getUser()->id == '742')
            <li>
                <a id="sidebar-kka" href="{{ Route('opd-kka', ['halaman' => 'main']) }}">
                    <i class="fa fa-clipboard"></i>
                    <span class="sidebar-mini-hide">KKA </span>
                </a>
            </li>
        @endif
        <!--
        <li>
            <a id="sidebar-bkb" href="{{ Route('bunda-bkb-index') }}">
                <i class="fa fa-window-restore"></i>
                <span class="sidebar-mini-hide">Bina Keluarga Balita (BKB)</span>
            </a>
        </li>
        <li>
            <a id="sidebar-apresiasi" href="{{ Route('common-apresiasi-index') }}">
                <i class="fa fa-trophy"></i>
                <span class="sidebar-mini-hide">Pengajuan Apresiasi </span>
            </a>
        </li> -->
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
