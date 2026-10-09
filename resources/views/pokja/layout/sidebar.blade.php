<!-- Side Navigation -->
<div class="content-side content-side-full">
    <ul class="nav-main">
        <li class="nav-main-heading" style="padding-top: 6px">
            <span class="sidebar-mini-visible">Informasi Biodata</span>
            <span class="sidebar-mini-hidden">Informasi Biodata</span>
        </li>
        <li>
            <a id="sidebar-berkas" href="{{ Route('common-berkas-index') }}">
                <i class="si si-docs"></i>
                <span class="sidebar-mini-hide">Berkas </span>
            </a>
        </li>
        <li>
            <a id="sidebar-pokja" href="{{ Route('common-pokja-index') }}">
                <i class="si si-users"></i>
                <span class="sidebar-mini-hide">Kelompok Kerja </span>
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
                <i class="fa fa-align-left"></i>
                <span class="sidebar-mini-hide">Kegiatan </span>
            </a>
        </li>
        <li>
            <a class="btn btn-hero btn-sm btn-noborder btn-rounded btn-alt-primary" href="{{ route('landing',['dashboard']) }}" style="margin: 10px">
                <i class="fa fa-arrow-left"></i> Kembali Ke Dashboard
            </a>
        </li>
    </ul>
</div>
<!-- END Side Navigation
