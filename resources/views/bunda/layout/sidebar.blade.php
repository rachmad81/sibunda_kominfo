<!-- Side Navigation -->
<div class="content-side content-side-full">
    <ul class="nav-main">
        <li class="nav-main-heading" style="padding-top: 6px">
            <span class="sidebar-mini-visible">Manajemen Data</span>
            <span class="sidebar-mini-hidden">Manajemen Data</span>
        </li>

        <li>
            <a id="sidebar-beranda" href="{{ Route('bunda-beranda-index') }}">
                <i class="si si-home"></i>
                <span class="sidebar-mini-hide">Beranda </span>
            </a>
        </li>

        {{-- <li>
            <a id="sidebar-user" href="{{ Route('bunda-user-index') }}">
                <i class="si si-users"></i>
                <span class="sidebar-mini-hide">User </span>
            </a>
        </li> --}}
        <hr>
        <!-- <li class="nav-main-heading" style="padding-top: 6px">
            <span class="sidebar-mini-visible">Informasi Biodata</span>
            <span class="sidebar-mini-hidden">Informasi Biodata</span>
        </li> -->
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
            <a id="sidebar-pokja" href="{{ Route('common-pokja-index') }}">
                <i class="si si-users"></i>
                <span class="sidebar-mini-hide">Kelompok Kerja </span>
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
            <a id="sidebar-mitra" data-toggle="dropdown" href="javascript:void(0)">
                <i class="fa fa-group"></i>
                <span class="sidebar-mini-hide">Mitra <i class="fa fa-caret-down"></i></span>
            </a>
            <ul class="dropdown-menu" role="menu" aria-labelledby="sidebar-rekap"
                style="height: auto;background: #ccc;">
                <li class="a_menu" style="opacity:1 !important;transform: translateY(0px) !important;"><a
                        role="menuitem" href="{{ route('bunda-kerjasama-mitra', ['halaman' => 'main']) }}">Kerjasama</a>
                </li>
                <li class="a_menu" style="opacity:1 !important;transform: translateY(0px) !important;"><a
                        role="menuitem"
                        href="{{ route('bunda-rekap-kerjasama-mitra', ['halaman' => 'main']) }}">Rekap</a></li>
            </ul>
        </li>
        <li>
            <a id="sidebar-kegiatan_pelatihan" data-toggle="dropdown" href="javascript:void(0)">
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
        {{-- <li>
            <a id="sidebar-kegiatan_pelatihan" href="{{ route('bunda-monitoring-index') }}">
                <i class="fa fa-align-left"></i>Monitoring
            </a>
        </li> --}}
        <li>
            <a id="sidebar-paud" href="{{ Route('bunda-paud_hi-index') }}">
                <i class="fa fa-clipboard"></i>
                <span class="sidebar-mini-hide">PAUD HI </span>
            </a>
        </li>
        <li>
            <a id="sidebar-sekolah" href="{{ Route('bunda-sekolah-index') }}">
                <i class="fa fa-align-left"></i>
                <span class="sidebar-mini-hide">Sekolah </span>
            </a>
        </li>
        <li>
            <a id="sidebar-vaksinasi" href="{{ Route('bunda-vaksinasi-index') }}">
                <i class="fa fa-align-left"></i>
                <span class="sidebar-mini-hide">Vaksinasi </span>
            </a>
        </li>
        {{-- <li>
            <a id="sidebar-sekolah" href="{{ Route('bunda-konfirmasi-pegawai-index') }}">
                <i class="si si-users"></i>
                <span class="sidebar-mini-hide">Konfirmasi Pegawai </span>
            </a>
        </li> --}}
        <!--
        <li>
            <a id="sidebar-bkb" href="{{ Route('bunda-bkb-index') }}">
                <i class="fa fa-window-restore"></i>
                <span class="sidebar-mini-hide">Bina Keluarga Balita (BKB)</span>
            </a>
        </li>
        <li>
            <a id="sidebar-edu" href="{{ Route('bunda-edu-index') }}">
                <i class="fa fa-window-restore"></i>
                <span class="sidebar-mini-hide">Edu City Tour</span>
            </a>
        </li>
        <li>
            <a id="sidebar-apresiasi" href="{{ Route('common-apresiasi-index') }}">
                <i class="fa fa-trophy"></i>
                <span class="sidebar-mini-hide">Pengajuan Apresiasi </span>
            </a>
        </li> -->
        <li>
            <a id="sidebar-materi" href="{{ Route('common-materi-index') }}">
                <i class="fa fa-align-left"></i>
                <span class="sidebar-mini-hide">Materi</span>
            </a>
        </li>
        {{-- @if (in_array(Auth::user()->tingkat, ['kecamatan', 'kelurahan']) && Session::has('tes')) --}}
        @if (in_array(Auth::user()->tingkat, ['kecamatan', 'kelurahan', 'kota']))
            @php
                $pengecualian = DB::table('cutoff_rekap.batas_lomba_pengecualian')
                    ->where('user_id', Auth::getUser()->id)
                    ->where('tahun', date('Y'))
                    ->where('status', 'true')
                    ->first();
            @endphp
            @if (date('Y-m-d H:i:s') <= '2026-09-20 14:00:00' ||
                    (!empty($pengecualian) && date('Y-m-d H:I:s') <= $pengecualian->batas_waktu))
                @php
                    $href_lomba = Route('bunda-lomba-index', ['tahun' => date('Y')]);
                    $onclcik_lomba = '';
                @endphp
            @else
                @php
                    $href_lomba = 'javascript:void(0)';
                    $onclcik_lomba =
                        "return swal('Whoops','Pendaftaran sudah ditutup, data akan dikirim untuk dinilai','error')";
                @endphp
            @endif
            <li>
                <a id="sidebar-lomba" href="{{ $href_lomba }}" onclick="{{ $onclcik_lomba }}">
                    <i class="fa fa-align-left"></i>
                    <span class="sidebar-mini-hide">Lomba Appresiasi Bunda Kelurahan/Kecamatan</span>
                </a>
            </li>
            {{-- <li>
                <a id="sidebar-lomba" href="{{ route('apresiasi-dashboard', ['halaman' => 'main']) }}"
                    target="_blank">
                    <i class="fa fa-align-left"></i>
                    <span class="sidebar-mini-hide">Appresiasi Bunda Kelurahan/Kecamatan (2026)</span>
                </a>
            </li> --}}
            <li>
                <a id="sidebar-paud_mutu" href="{{ route('paud_mutu-paudmutu', ['halaman' => 'main']) }}">
                    <i class="fa fa-align-left"></i>
                    <span class="sidebar-mini-hide">PAUD Bermutu</span>
                </a>
            </li>
        @endif
        @if (in_array(Auth::user()->tingkat, ['kecamatan', 'kota']))
            <li>
                <a id="sidebar-talenta" href="{{ route('bunda-talenta-index') }}">
                    <i class="fa fa-align-left"></i>
                    <span class="sidebar-mini-hide">Talenta Seni</span>
                </a>
            </li>
        @endif
        {{-- @if (in_array(Auth::user()->tingkat, ['kecamatan', 'kota', 'kelurahan']))
        <li>
            <a id="sidebar-pelaporan-gemilang" href="{{route('bunda-pelaporan-gemilang-index')}}">
                <i class="fa fa-align-left"></i>
                <span class="sidebar-mini-hide">Pelaporan GEMILANG</span>
            </a>
        </li>
        @endif --}}
        @if (in_array(Auth::user()->tingkat, ['kecamatan', 'kota', 'kelurahan']))
            {{-- <li>
                <a id="sidebar-monitor" data-toggle="dropdown" href="javascript:void(0)">
                    <i class="fa fa-clipboard"></i>
                    <span class="sidebar-mini-hide">Monev <i class="fa fa-caret-down"></i></span>
                </a>
                <ul class="dropdown-menu" role="menu" aria-labelledby="sidebar-rekap"
                    style="height: auto;background: #ccc;">
                    <li class="a_menu" style="opacity:1 !important;transform: translateY(0px) !important;"><a
                            role="menuitem"
                            href="{{ route('bunda-monitoring-mpls-main', ['jenjang' => 'paud']) }}">Monitor MPLS
                            PAUD</a></li>
                    <li class="a_menu" style="opacity:1 !important;transform: translateY(0px) !important;"><a
                            role="menuitem"
                            href="{{ route('bunda-monitoring-mpls-main', ['jenjang' => 'sd']) }}">Monitor MPLS SD</a>
                    </li>
                    <li class="a_menu" style="opacity:1 !important;transform: translateY(0px) !important;"><a
                            role="menuitem" href="{{ Route('common-monitoring-index') }}">Monitoring Ijop</a>
                    </li>
                    <li class="a_menu" style="opacity:1 !important;transform: translateY(0px) !important;"><a
                            role="menuitem" href="{{ route('bunda-survey_transisi-index') }}">Survey Transisi PAUD Ke
                            SD</a>
                    </li>
                    <li class="a_menu" style="opacity:1 !important;transform: translateY(0px) !important;"><a
                            role="menuitem" href="{{ route('bunda-survey_transisi-monitor') }}">Monitor Transisi PAUD
                            ke SD</a>
                    </li>
                    <li class="a_menu" style="opacity:1 !important;transform: translateY(0px) !important;"><a
                            role="menuitem" href="{{ route('bunda-monitoring-blm_sekolah-main') }}">Monitoring anak
                            belum sekolah</a>
                    </li>
                    <li class="a_menu" style="opacity:1 !important;transform: translateY(0px) !important;"><a
                            role="menuitem" href="{{ route('monev_baru-jaspel', ['halaman' => 'main']) }}">Monitoring
                            Jaspel
                            PAUD</a>
                    </li>
                    <li class="a_menu" style="opacity:1 !important;transform: translateY(0px) !important;"><a
                            role="menuitem" href="{{ route('monev_baru-bop', ['halaman' => 'main']) }}">Monitoring
                            BOP PAUD</a>
                    </li>
                    <li class="a_menu" style="opacity:1 !important;transform: translateY(0px) !important;"><a
                            role="menuitem" href="{{ Route('opd-kka', ['halaman' => 'main']) }}">Monitoring
                            KKA</a>
                    </li>
                    <li class="a_menu" style="opacity:1 !important;transform: translateY(0px) !important;"><a
                            role="menuitem"
                            href="{{ Route('monev_baru-kembang_inklusi', ['halaman' => 'main']) }}">Monitoring
                            Perkembangan Inklusi</a>
                    </li>
                    <li class="a_menu" style="opacity:1 !important;transform: translateY(0px) !important;"><a
                            role="menuitem"
                            href="{{ Route('monev_baru-gemilang', ['halaman' => 'main']) }}">Monitoring
                            Gemilang</a>
                    </li>
                </ul>
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
                            role="menuitem"
                            href="{{ Route('beasiswa_paud-halaman', ['halaman' => 'main']) }}">Monitor Beasiswa
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
                    <li class="a_menu" style="opacity:1 !important;transform: translateY(0px) !important;"><a
                            role="menuitem"
                            href="{{ route('monev_baru-sk_pengangkatan', ['halaman' => 'main']) }}">Monitor
                            SK Pengangkatan</a>
                    </li>
                </ul>
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
            {{-- <li>
                <a id="sidebar-diagram" data-toggle="dropdown" href="javascript:void(0)">
                    <i class="fa fa-bar-chart"></i>
                    <span class="sidebar-mini-hide">Diagram <i class="fa fa-caret-down"></i></span>
                </a>
                <ul class="dropdown-menu" role="menu" aria-labelledby="sidebar-rekap"
                    style="height: auto;background: #ccc;">
                    <li class="a_menu" style="opacity:1 !important;transform: translateY(0px) !important;"><a
                            role="menuitem" href="{{ route('rekap-satuan_paud-main') }}">Satuan PAUD</a></li>
                    <li class="a_menu" style="opacity:1 !important;transform: translateY(0px) !important;"><a
                            role="menuitem" href="{{ route('bunda-diagram_penduduk-main') }}">Diagram Penduduk</a>
                    </li>
                    <li class="a_menu" style="opacity:1 !important;transform: translateY(0px) !important;"><a
                            role="menuitem" href="{{ route('bunda-diagram_anak-main') }}">Diagram anak 5-6 tahun</a>
                    </li>
                    <li class="a_menu" style="opacity:1 !important;transform: translateY(0px) !important;"><a
                            role="menuitem" href="{{ route('bunda-diagram_guru-main') }}">Diagram Guru</a></li>
                    <li class="a_menu" style="opacity:1 !important;transform: translateY(0px) !important;"><a
                            role="menuitem" href="{{ route('bunda-diagram_lembaga-main') }}">Diagram Lembaga</a></li>
                </ul>
            </li> --}}
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
                <a id="sidebar-kka" href="{{ Route('opd-kka', ['halaman' => 'main']) }}">
                    <i class="fa fa-clipboard"></i>
                    <span class="sidebar-mini-hide">KKA </span>
                </a>
            </li> --}}
            <li>
                <a id="sidebar-imunisasi" href="{{ route('bunda-imunisasi-main') }}">
                    <i class="fa fa-align-left"></i>
                    <span class="sidebar-mini-hide">Imunisasi</span>
                </a>
            </li>
        @endif
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
