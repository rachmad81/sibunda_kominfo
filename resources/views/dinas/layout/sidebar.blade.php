 <!-- Side Navigation -->
 <div class="content-side content-side-full">
     <ul class="nav-main">
         <li class="nav-main-heading" style="padding-top: 6px">
             <span class="sidebar-mini-visible">Manajemen Data</span>
             <span class="sidebar-mini-hidden">Manajemen Data</span>
         </li>
         <li>
             <a id="sidebar-user" href="{{ Route('dinas-user-index') }}">
                 <i class="si si-users"></i>
                 <span class="sidebar-mini-hide">User </span>
             </a>
         </li>
         {{-- <li>
             <a id="sidebar-user" href="{{ Route('dinas-user_baru', ['halaman' => 'main']) }}">
                 <i class="si si-users"></i>
                 <span class="sidebar-mini-hide">User </span>
             </a>
         </li> --}}
         <li>
             <a id="sidebar-mitra" data-toggle="dropdown" href="javascript:void(0)">
                 <i class="fa fa-group"></i>
                 <span class="sidebar-mini-hide">Mitra <i class="fa fa-caret-down"></i></span>
             </a>
             <ul class="dropdown-menu" role="menu" aria-labelledby="sidebar-rekap"
                 style="height: auto;background: #ccc;">
                 <li class="a_menu" style="opacity:1 !important;transform: translateY(0px) !important;"><a
                         role="menuitem" href="{{ route('dinas-mitra-halaman', ['index']) }}">Data</a>
                 </li>
                 <li class="a_menu" style="opacity:1 !important;transform: translateY(0px) !important;"><a
                         role="menuitem" href="{{ route('bunda-rekap-kerjasama-mitra', ['halaman' => 'main']) }}">Rekap
                         Kerjasama</a></li>
             </ul>
         </li>
         <li>
             <a id="sidebar-sewa" href="{{ Route('dinas-sewa-halaman', ['index']) }}">
                 <i class="si si-book-open"></i>
                 <span class="sidebar-mini-hide">Sewa Graha PAUD </span>
             </a>
         </li>
         {{-- <hr>
        <li class="nav-main-heading" style="padding-top: 6px">
            <span class="sidebar-mini-visible">Informasi Biodata</span>
            <span class="sidebar-mini-hidden">Informasi Biodata</span>
        </li>
        <li >
            <a id="sidebar-profil" href="{{ Route('common-profil-index') }}">
                <i class="si si-user"></i>
                <span class="sidebar-mini-hide">Profil </span>
            </a>
        </li> --}}
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
             <a id="sidebar-bunda" href="{{ Route('dinas-bunda-index') }}">
                 <i class="fa fa-users"></i>
                 <span class="sidebar-mini-hide">Daftar Penanggung Jawab Bunda PAUD </span>
             </a>
         </li>
         <li>
             <a id="sidebar-all-bunda" href="{{ Route('dinas-all-bunda-index') }}">
                 <i class="fa fa-users"></i>
                 <span class="sidebar-mini-hide">Daftar Semua Bunda PAUD </span>
             </a>
         </li>
         <li>
             <a id="sidebar-survey" href="{{ Route('dinas-survey-index') }}">
                 <i class="fa fa-users"></i>
                 <span class="sidebar-mini-hide">Hasil Penilaian Aplikasi</span>
             </a>
         </li>
         <li>
             <a id="sidebar-rekap" data-toggle="dropdown" href="javascript:void(0)">
                 <i class="fa fa-clipboard"></i>
                 <span class="sidebar-mini-hide">Rekap <i class="fa fa-caret-down"></i></span>
             </a>
             <ul class="dropdown-menu" role="menu" aria-labelledby="sidebar-rekap"
                 style="height: auto;background: #ccc;">
                 {{-- <li class="a_menu" style="opacity:1 !important;transform: translateY(0px) !important;"><a role="menuitem" href="{{route('rekap-satuan_paud-main')}}">Satuan PAUD</a></li> --}}
                 <li class="a_menu" style="opacity:1 !important;transform: translateY(0px) !important;"><a
                         role="menuitem" href="{{ route('rekap-user-main') }}">User</a></li>
                 <li class="a_menu" style="opacity:1 !important;transform: translateY(0px) !important;"><a
                         role="menuitem" href="{{ route('rekap-profil-main') }}">Profil</a></li>
                 <li class="a_menu" style="opacity:1 !important;transform: translateY(0px) !important;"><a
                         role="menuitem" href="{{ route('rekap-program-main') }}">Program Kerja</a></li>
                 <li class="a_menu" style="opacity:1 !important;transform: translateY(0px) !important;"><a
                         role="menuitem" href="{{ route('rekap-kegiatan-main') }}">Kegiatan</a></li>
             </ul>
         </li>
         <li>
             <a id="sidebar-rekap_satuan" data-toggle="dropdown" href="javascript:void(0)">
                 <i class="fa fa-align-left"></i>
                 <span class="sidebar-mini-hide">Rekap Satuan PAUD<i class="fa fa-caret-down"></i></span>
             </a>
             <ul class="dropdown-menu" role="menu" aria-labelledby="sidebar-rekap"
                 style="height: auto;background: #ccc;">
                 {{-- <li class="a_menu" style="opacity:1 !important;transform: translateY(0px) !important;"><a
                         role="menuitem" href="{{ route('bunda-rekap_profil-index') }}">Rekap Satuan</a></li>
                 <li class="a_menu" style="opacity:1 !important;transform: translateY(0px) !important;"><a
                         role="menuitem" href="{{ route('bunda-rekap_besar-index') }}">Rekap Besar</a></li> --}}

                 <li class="a_menu" style="opacity:1 !important;transform: translateY(0px) !important;"><a
                         role="menuitem"
                         href="{{ route('dinas-rekap-satuan_pendidikan', ['halaman' => 'main']) }}">Rekap
                         Satuan Pendidikan</a></li>

                 <li class="a_menu" style="opacity:1 !important;transform: translateY(0px) !important;"><a
                         role="menuitem" href="{{ route('dinas-rekap-siswa', ['halaman' => 'main']) }}">Rekap
                         Siswa</a></li>

                 <li class="a_menu" style="opacity:1 !important;transform: translateY(0px) !important;"><a
                         role="menuitem" href="{{ route('dinas-rekap-pegawai', ['halaman' => 'main']) }}">Rekap
                         Pegawai</a></li>
             </ul>
         </li>
         <li>
             <a id="sidebar-kegiatan_pendukung" data-toggle="dropdown" href="javascript:void(0)">
                 <i class="fa fa-align-left"></i>
                 <span class="sidebar-mini-hide">Kegiatan Pendukung <i class="fa fa-caret-down"></i></span>
             </a>
             <ul class="dropdown-menu" role="menu" aria-labelledby="sidebar-rekap"
                 style="height: auto;background: #ccc;">
                 <li class="a_menu" style="opacity:1 !important;transform: translateY(0px) !important;"><a
                         role="menuitem" href="{{ route('bunda-pelatihan-index') }}">Pelatihan</a></li>
                 <li class="a_menu" style="opacity:1 !important;transform: translateY(0px) !important;"><a
                         role="menuitem" href="{{ route('bunda-konsultasi-index') }}">Konsultasi PAUD HI</a></li>
                 <li class="a_menu" style="opacity:1 !important;transform: translateY(0px) !important;"><a
                         role="menuitem" href="{{ route('bunda-fasilitasi-index') }}">Permohonan Fasilitasi</a></li>
                 <li class="a_menu" style="opacity:1 !important;transform: translateY(0px) !important;"><a
                         role="menuitem" href="{{ route('bunda-edu-index') }}">Edu City Tour</a></li>
                 <li class="a_menu" style="opacity:1 !important;transform: translateY(0px) !important;"><a
                         role="menuitem" href="{{ route('bunda-rekap_bus-index') }}">Rekap Permohonan BUS</a></li>
             </ul>
         </li>
         <li>
             <a id="sidebar-rekap_puskesmas" data-toggle="dropdown" href="javascript:void(0)">
                 <i class="fa fa-align-left"></i>
                 <span class="sidebar-mini-hide">Rekap Kunjungan Puskesmas <i class="fa fa-caret-down"></i></span>
             </a>
             <ul class="dropdown-menu" role="menu" aria-labelledby="sidebar-rekap"
                 style="height: auto;background: #ccc;">
                 <li class="a_menu" style="opacity:1 !important;transform: translateY(0px) !important;"><a
                         role="menuitem" href="{{ route('dinas-rekap_puskesmas', ['halaman' => 'wilayah']) }}">Per
                         Wilayah</a></li>
                 <li class="a_menu" style="opacity:1 !important;transform: translateY(0px) !important;"><a
                         role="menuitem" href="{{ route('dinas-rekap_puskesmas', ['halaman' => 'tanggal']) }}">Per
                         Tanggal</a></li>
                 <li class="a_menu" style="opacity:1 !important;transform: translateY(0px) !important;"><a
                         role="menuitem" href="{{ route('dinas-rekap_puskesmas', ['halaman' => 'bulan']) }}">Per
                         Bulan</a></li>
                 <li class="a_menu" style="opacity:1 !important;transform: translateY(0px) !important;"><a
                         role="menuitem" href="{{ route('dinas-rekap_puskesmas', ['halaman' => 'kunjungan']) }}">Per
                         Kunjungan</a></li>
             </ul>
         </li>
         <li>
             <a id="sidebar-datapaud" data-toggle="dropdown" href="javascript:void(0)">
                 <i class="fa fa-clipboard"></i>
                 <span class="sidebar-mini-hide">Data PAUD <i class="fa fa-caret-down"></i></span>
             </a>
             <ul class="dropdown-menu" role="menu" aria-labelledby="sidebar-rekap"
                 style="height: auto;background: #ccc;">
                 <li class="a_menu" style="opacity:1 !important;transform: translateY(0px) !important;"><a
                         role="menuitem" href="{{ route('dinas-data_paud-wisata-index') }}">Tempat Wisata</a></li>
                 <li class="a_menu" style="opacity:1 !important;transform: translateY(0px) !important;"><a
                         role="menuitem" href="{{ route('dinas-data_paud-pelatihan-index') }}">Pelatihan</a></li>
             </ul>
         </li>
         {{-- @if (Session::has('tes')) --}}
         <li>
             <a id="sidebar-lomba" href="{{ Route('dinas-lomba-index') }}">
                 <i class="fa fa-align-left"></i>
                 <span class="sidebar-mini-hide">Lomba Appresiasi Bunda Kelurahan/Kecamatan</span>
             </a>
         </li>
         <li>
             <a id="sidebar-paud_mutu" href="{{ Route('paud_mutu-paudmutu', ['halaman' => 'main']) }}">
                 <i class="fa fa-align-left"></i>
                 <span class="sidebar-mini-hide">PAUD Bermutu</span>
             </a>
         </li>
         <li>
             <a id="sidebar-rekap_mbg" href="{{ Route('dinas-rekap_mbg', ['halaman' => 'main']) }}">
                 <i class="fa fa-align-left"></i>
                 <span class="sidebar-mini-hide">MBG</span>
             </a>
         </li>
         <li>
             <a id="sidebar-rekap_ttd" href="{{ Route('dinas-rekap_ttd', ['halaman' => 'main']) }}">
                 <i class="fa fa-align-left"></i>
                 <span class="sidebar-mini-hide">Tablet Tambah Darah</span>
             </a>
         </li>
         {{-- <li>
             <a id="sidebar-rekap_ttd" href="javascript:void(0)" onclick="onprogres_bos()">
                 <i class="fa fa-align-left"></i>
                 <span class="sidebar-mini-hide">Anak Tidak Terintervensi Kesehatan</span>
             </a>
         </li> --}}
         <li>
             <a id="sidebar-talenta" data-toggle="dropdown" href="javascript:void(0)">
                 <i class="fa fa-image"></i>
                 <span class="sidebar-mini-hide">Talenta Seni <i class="fa fa-caret-down"></i></span>
             </a>
             <ul class="dropdown-menu" role="menu" aria-labelledby="sidebar-rekap"
                 style="height: auto;background: #ccc;">
                 <li class="a_menu" style="opacity:1 !important;transform: translateY(0px) !important;"><a
                         role="menuitem" href="{{ route('bunda-talenta-index') }}">Talenta Seni</a></li>
                 <li class="a_menu" style="opacity:1 !important;transform: translateY(0px) !important;"><a
                         role="menuitem"
                         href="{{ route('telenta_tempat-halaman', ['halaman' => 'main']) }}">Tempat</a></li>
                 <li class="a_menu" style="opacity:1 !important;transform: translateY(0px) !important;"><a
                         role="menuitem" href="{{ route('bunda-jadwal_talenta-index') }}">Jadwal</a></li>
             </ul>
         </li>
         <li>
             <a id="sidebar-diagram" data-toggle="dropdown" href="javascript:void(0)">
                 <i class="fa fa-bar-chart"></i>
                 <span class="sidebar-mini-hide">Diagram <i class="fa fa-caret-down"></i></span>
             </a>
             <ul class="dropdown-menu" role="menu" aria-labelledby="sidebar-rekap"
                 style="height: auto;background: #ccc;">
                 {{-- <li class="a_menu" style="opacity:1 !important;transform: translateY(0px) !important;"><a role="menuitem" href="{{route('rekap-satuan_paud-main')}}">Satuan PAUD</a></li> --}}
                 <li class="a_menu" style="opacity:1 !important;transform: translateY(0px) !important;"><a
                         role="menuitem" href="{{ route('bunda-diagram_penduduk-main') }}">Diagram Penduduk</a></li>
                 {{-- <li class="a_menu" style="opacity:1 !important;transform: translateY(0px) !important;"><a
                         role="menuitem" href="{{ route('bunda-diagram_anak-main') }}">Diagram anak 5-6 tahun</a>
                 </li> --}}
                 <li class="a_menu" style="opacity:1 !important;transform: translateY(0px) !important;"><a
                         role="menuitem" href="{{ route('bunda-diagram_guru-main') }}">Diagram Guru</a></li>
                 <li class="a_menu" style="opacity:1 !important;transform: translateY(0px) !important;"><a
                         role="menuitem" href="{{ route('bunda-diagram_lembaga-main') }}">Diagram Lembaga</a></li>
             </ul>
         </li>
         <li>
             <a id="sidebar-data_apsmk" href="{{ route('dinas-data_aps_apm_apk', ['halaman' => 'main']) }}">
                 <i class="fa fa-desktop"></i>
                 <span class="sidebar-mini-hide">Data APM, APS, APK</span>
             </a>
         </li>
         <li>
             <a id="sidebar-dashboard_asw" href="{{ route('bunda-dashboard_asw-main') }}">
                 <i class="fa fa-desktop"></i>
                 <span class="sidebar-mini-hide">Dashboard ASW</span>
             </a>
         </li>
         <li>
             <a id="sidebar-rekap_sekolah" href="{{ route('dinas-rekap_sekolah', ['halaman' => 'main']) }}">
                 <i class="fa fa-desktop"></i>
                 <span class="sidebar-mini-hide">Dashboard Rekap Sekolah</span>
             </a>
         </li>
         <li>
             <a id="sidebar-rekap_sidia" href="{{ route('dinas-rekap_sidia', ['halaman' => 'main']) }}">
                 <i class="fa fa-desktop"></i>
                 <span class="sidebar-mini-hide">Dashboard Rekap Sidia</span>
             </a>
         </li>
         <li>
             <a id="sidebar-dashboard_posyandu" href="{{ route('bunda-iframe', ['halaman' => 'posyandu']) }}">
                 <i class="fa fa-desktop"></i>
                 <span class="sidebar-iframe">Dashboard Aplikasi ASW</span>
             </a>
         </li>
         <li>
             <a id="sidebar-dashboard_game" href="{{ route('bunda-iframe', ['halaman' => 'game_siappak']) }}">
                 <i class="fa fa-desktop"></i>
                 <span class="sidebar-iframe">Dashboard Aplikasi SIAP PPAK</span>
             </a>
         </li>
         {{-- <li>
             <a id="sidebar-dashboard_pendampingan"
                 href="{{ route('bunda-iframe', ['halaman' => 'pendampingan']) }}">
                 <i class="fa fa-desktop"></i>
                 <span class="sidebar-iframe">Dashboard Pendampingan</span>
             </a>
         </li>
         <li>
             <a id="sidebar-dashboard_akses" href="{{ route('bunda-iframe', ['halaman' => 'akses_asw']) }}">
                 <i class="fa fa-desktop"></i>
                 <span class="sidebar-iframe">Dashboard Akses ASW</span>
             </a>
         </li> --}}
         {{-- <li>
             <a id="sidebar-dashboard_iframe" href="{{ route('bunda-dashboard_iframe-main') }}">
                 <i class="fa fa-desktop"></i>
                 <span class="sidebar-iframe">Dashboard Iframe</span>
             </a>
         </li> --}}
         {{-- <li>
             <a id="sidebar-kka" href="{{ Route('opd-kka', ['halaman' => 'main']) }}">
                 <i class="fa fa-clipboard"></i>
                 <span class="sidebar-mini-hide">KKA </span>
             </a>
         </li>
         <li>
             <a id="sidebar-beasiswa_paud" href="{{ Route('beasiswa_paud-halaman', ['halaman' => 'main']) }}">
                 <i class="fa fa-money"></i>
                 <span class="sidebar-mini-hide">Beasiswa PAUD </span>
             </a>
         </li> --}}

         <li>
             <a id="sidebar-monitor" data-toggle="dropdown" href="javascript:void(0)">
                 <i class="fa fa-clipboard"></i>
                 <span class="sidebar-mini-hide">Monev <i class="fa fa-caret-down"></i></span>
             </a>
             <ul class="dropdown-menu" role="menu" aria-labelledby="sidebar-rekap"
                 style="height: auto;background: #ccc;">
                 <li class="a_menu" style="opacity:1 !important;transform: translateY(0px) !important;"><a
                         role="menuitem" href="{{ route('monev_baru-bop', ['halaman' => 'main']) }}">Monitor
                         BOP</a>
                 </li>
                 <li class="a_menu" style="opacity:1 !important;transform: translateY(0px) !important;"><a
                         role="menuitem" href="{{ route('monev_baru-jaspel', ['halaman' => 'main']) }}">Monitor
                         Jaspel</a>
                 </li>
                 <li class="a_menu" style="opacity:1 !important;transform: translateY(0px) !important;"><a
                         role="menuitem" href="{{ route('monev_baru-gemilang', ['halaman' => 'main']) }}">Monitor
                         Gemilang</a>
                 </li>
                 <li class="a_menu" style="opacity:1 !important;transform: translateY(0px) !important;"><a
                         role="menuitem"
                         href="{{ route('monev_baru-kembang_inklusi', ['halaman' => 'main']) }}">Monitor
                         Kembang Inklusi</a>
                 </li>
                 <li class="a_menu" style="opacity:1 !important;transform: translateY(0px) !important;"><a
                         role="menuitem" href="{{ route('opd-kka', ['halaman' => 'main']) }}">Monitor
                         KKA</a>
                 </li>
                 <li class="a_menu" style="opacity:1 !important;transform: translateY(0px) !important;"><a
                         role="menuitem"
                         href="{{ route('monev_baru-mpls', ['halaman' => 'main', 'jenjang' => 'paud']) }}">Monitor
                         MPLS PAUD</a>
                 </li>
                 <li class="a_menu" style="opacity:1 !important;transform: translateY(0px) !important;"><a
                         role="menuitem"
                         href="{{ route('monev_baru-mpls', ['halaman' => 'main', 'jenjang' => 'sd']) }}">Monitor
                         MPLS SD</a>
                 </li>
                 <li class="a_menu" style="opacity:1 !important;transform: translateY(0px) !important;"><a
                         role="menuitem" href="{{ route('monev_baru-ijop', ['halaman' => 'main']) }}">Monitor
                         Ijop</a>
                 </li>
                 <li class="a_menu" style="opacity:1 !important;transform: translateY(0px) !important;"><a
                         role="menuitem" href="{{ route('bunda-survey_transisi-monitor') }}">Monitor
                         Transisi PAUD ke SD</a>
                 </li>
                 <li class="a_menu" style="opacity:1 !important;transform: translateY(0px) !important;"><a
                         role="menuitem" href="{{ route('bunda-monitoring-blm_sekolah-main') }}">Monitor anak
                         belum sekolah</a>
                 </li>
                 <li class="a_menu" style="opacity:1 !important;transform: translateY(0px) !important;"><a
                         role="menuitem" href="{{ route('monev_baru-soth', ['halaman' => 'main']) }}">Monitor anak
                         SOTH</a>
                 </li>

                 <li class="a_menu" style="opacity:1 !important;transform: translateY(0px) !important;"><a
                         role="menuitem" href="{{ route('bunda-g7kaih-main') }}">Monitor G7KAIH</a>
                 </li>

                 <li class="a_menu" style="opacity:1 !important;transform: translateY(0px) !important;"><a
                         role="menuitem" href="{{ Route('beasiswa_paud-halaman', ['halaman' => 'main']) }}">Monitor
                         Beasiswa
                         PAUD</a>
                 </li>

                 <li class="a_menu" style="opacity:1 !important;transform: translateY(0px) !important;"><a
                         role="menuitem" href="{{ Route('monev_baru-rpl', ['halaman' => 'main']) }}">Monitor
                         Beasiswa
                         RPL S1</a>
                 </li>

                 <li class="a_menu" style="opacity:1 !important;transform: translateY(0px) !important;"><a
                         role="menuitem" href="{{ Route('monev_baru-bosp', ['halaman' => 'main']) }}">Monitor
                         Penerimaan
                         BOP</a>
                 </li>
                 <li class="a_menu" style="opacity:1 !important;transform: translateY(0px) !important;"><a
                         role="menuitem"
                         href="{{ route('monev_baru-kelembagaan_paud', ['halaman' => 'main']) }}">Monitor
                         kelembagaan PAUD</a>
                 </li>
             </ul>
         </li>
         {{-- @endif --}}
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
