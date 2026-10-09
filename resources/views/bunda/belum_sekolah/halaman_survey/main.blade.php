<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Instrumen Verifikasi Faktual ABS Usia 5–6 Tahun</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f8fafc
        }

        .field {
            width: 100%;
            padding: .75rem 1rem;
            border: 1px solid #cbd5e1;
            border-radius: .75rem;
            background: #fff;
            font-size: .875rem
        }

        .field:focus {
            outline: none;
            box-shadow: 0 0 0 2px #10b981;
            border-color: #10b981
        }

        .auto-field {
            background: #f1f5f9;
            color: #334155
        }

        .choice {
            display: flex;
            align-items: flex-start;
            gap: .75rem;
            padding: .8rem;
            border: 1px solid #e2e8f0;
            border-radius: .8rem;
            cursor: pointer
        }

        .choice:hover {
            background: #f8fafc
        }

        .section-card {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 1rem;
            padding: 1.5rem;
            box-shadow: 0 1px 2px rgba(15, 23, 42, .04)
        }

        .sub-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: .9rem;
            padding: 1rem
        }

        .section-title {
            display: flex;
            gap: .75rem;
            align-items: center;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 1rem;
            margin-bottom: 1.25rem
        }

        .section-number {
            width: 2rem;
            height: 2rem;
            border-radius: 9999px;
            background: #ecfdf5;
            color: #059669;
            font-weight: 800;
            font-size: .875rem;
            display: flex;
            align-items: center;
            justify-content: center;
            flex: none
        }

        .small-note {
            font-size: .75rem;
            color: #64748b;
            line-height: 1.55
        }

        .hidden-block {
            display: none
        }

        @media print {
            body {
                background: #fff;
                font-size: 11px
            }

            .no-print {
                display: none !important
            }

            .section-card {
                box-shadow: none;
                border: 1px solid #cbd5e1;
                break-inside: avoid;
                margin-bottom: 12px;
                padding: 14px
            }

            .sub-card {
                break-inside: avoid
            }

            .field {
                border: none !important;
                border-bottom: 1px dashed #64748b !important;
                border-radius: 0 !important;
                padding: 4px 2px !important;
                background: transparent !important
            }

            .auto-field {
                background: #f8fafc !important;
                border: 1px solid #cbd5e1 !important;
                border-radius: 4px !important;
                padding: 5px !important
            }

            .choice {
                padding: 5px;
                border: none
            }

            .print-header {
                display: block !important
            }

            .screen-header {
                display: none !important
            }

            textarea {
                min-height: 35px
            }

            input[type=checkbox],
            input[type=radio] {
                appearance: auto !important
            }
        }
    </style>
</head>

<body class="text-slate-800">
    <header class="screen-header bg-gradient-to-r from-emerald-600 to-teal-700 text-white px-4 py-7 shadow no-print">
        <div class="max-w-5xl mx-auto flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="p-3 rounded-xl bg-white/10"><i data-lucide="clipboard-check" class="w-8 h-8"></i></div>
                <div>
                    <p class="text-xs uppercase tracking-widest text-emerald-100 font-semibold">Instrumen Verifikasi
                        Faktual</p>
                    <h1 class="text-xl md:text-2xl font-extrabold">Anak Belum Sekolah Usia 5–6 Tahun</h1>
                    <p class="text-xs text-emerald-100 mt-1">Pendataan, verifikasi lapangan, fasilitasi, dan tindak
                        lanjut layanan PAUD.</p>
                </div>
            </div>
            <div class="flex gap-2">
                {{-- <button type="button" onclick="resetForm()"
                    class="px-4 py-2 rounded-lg bg-rose-500/30 border border-white/20 text-sm font-semibold">Reset</button>
                <button type="button" onclick="window.print()"
                    class="px-4 py-2 rounded-lg bg-white text-emerald-800 text-sm font-semibold">Cetak / PDF</button> --}}
            </div>
        </div>
    </header>

    <div class="print-header hidden max-w-5xl mx-auto px-8 py-5 border-b-2 border-slate-900 text-center">
        <h2 class="font-bold uppercase">Pemerintah Kota Surabaya</h2>
        <h1 class="text-lg font-extrabold uppercase mt-1">Instrumen Verifikasi Faktual Anak Belum Sekolah Usia 5–6 Tahun
        </h1>
    </div>

    <main class="max-w-5xl mx-auto px-4 py-6">
        <div class="section-card mb-6 bg-emerald-50 border-emerald-200">
            <p class="text-sm leading-6">Instrumen ini digunakan dalam pelaksanaan pendataan dan verifikasi lapangan
                Anak Belum Sekolah (ABS) usia 5–6 tahun. Pengisian dilakukan berdasarkan hasil kunjungan lapangan,
                wawancara dengan keluarga, serta informasi faktual.</p>
        </div>

        <form id="absForm" class="space-y-6" onsubmit="simpan()">
            <section class="section-card">
                <div class="section-title">
                    <div class="section-number">1</div>
                    <h2 class="font-bold text-lg">Identitas Petugas</h2>
                </div>
                <div class="grid md:grid-cols-2 gap-4">
                    <div><label class="text-xs font-semibold uppercase text-slate-500">Tanggal Kunjungan</label><input
                            type="date" name="tanggal_kunjungan" class="field mt-2"
                            value="{{ $survey->tgl_survey ?? date('Y-m-d') }}"></div>
                    <div><label class="text-xs font-semibold uppercase text-slate-500">Nama Bunda PAUD</label><input
                            type="text" name="nama_bunda_paud" class="field mt-2"
                            value="{{ $survey->nama_petugas ?? ($petugas->nama ?? '') }}"></div>
                    <div><label class="text-xs font-semibold uppercase text-slate-500">Unsur/Jabatan</label><input
                            type="text" name="unsur_jabatan" class="field mt-2"
                            value="{{ $survey->jabatan_petugs ?? ($petugas->nama_jabatan ?? '') }}"></div>
                    <div><label class="text-xs font-semibold uppercase text-slate-500">Nomor HP</label><input
                            type="tel" name="nomor_hp_petugas" class="field mt-2" placeholder="08xxxxxxxxxx"
                            value="{{ $survey->telp_petugas ?? ($petugas->no_telp ?? '') }}"></div>
                    <div><label class="text-xs font-semibold uppercase text-slate-500">Kelurahan</label><input
                            type="text" name="kelurahan_petugas" class="field mt-2"
                            value="{{ $survey->kelurahan ?? ($petugas->kelurahan_dispenduk ?? '') }}"></div>
                    <div><label class="text-xs font-semibold uppercase text-slate-500">Kecamatan</label><input
                            type="text" name="kecamatan_petugas" class="field mt-2"
                            value="{{ $survey->kecamatan ?? ($petugas->kecamatan_dispenduk ?? '') }}"></div>
                    <input type="hidden" name="nik" value="{{ $anak->nik }}">
                </div>
            </section>

            <section class="section-card">
                <div class="section-title">
                    <div class="section-number">2</div>
                    <div>
                        <h2 class="font-bold text-lg">Identitas Anak</h2>
                        <p class="small-note">Terisi otomatis oleh sistem.</p>
                    </div>
                </div>
                <div class="grid md:grid-cols-2 gap-4">
                    <div><label class="text-xs font-semibold uppercase text-slate-500">NIK</label><input readonly
                            class="field auto-field mt-2" placeholder="Otomatis dari sistem"
                            value="{{ $survey->nik ?? $anak->nik }}"></div>
                    <div><label class="text-xs font-semibold uppercase text-slate-500">Nama Anak</label><input readonly
                            class="field auto-field mt-2" placeholder="Otomatis dari sistem"
                            value="{{ $survey->nama_lengkap ?? $anak->nama_lengkap }}"></div>
                    <div><label class="text-xs
                            font-semibold uppercase text-slate-500">Jenis
                            Kelamin</label><input readonly class="field auto-field mt-2"
                            placeholder="Otomatis dari sistem"
                            value="{{ $survey->jenis_kelamin ?? $anak->jenis_kelamin }}"></div>
                    @php
                        $tgl_lahir = $survey->tgl_lahir ?? $anak->tgl_lahir;
                        $tgl_lahir = date('d-m-Y', strtotime($tgl_lahir));
                    @endphp
                    <div><label class="text-xs font-semibold uppercase text-slate-500">Tempat, Tanggal
                            Lahir</label><input readonly class="field auto-field mt-2"
                            placeholder="Otomatis dari sistem"
                            value="{{ $survey->tempat_lahir ?? $anak->tempat_lahir }}, {{ $tgl_lahir }}">
                    </div>
                    <div class="md:col-span-2"><label
                            class="text-xs font-semibold uppercase text-slate-500">Alamat</label>
                        <textarea readonly rows="2" class="field auto-field mt-2" placeholder="Otomatis dari sistem">{{ $survey->alamat_dom ?? $anak->alamat }}</textarea>
                    </div>
                    <div><label class="text-xs font-semibold uppercase text-slate-500">RT</label><input readonly
                            class="field auto-field mt-2" value="{{ $survey->rt_dom ?? $anak->rt }}">
                    </div>
                    <div><label class="text-xs font-semibold uppercase text-slate-500">RW</label><input readonly
                            class="field auto-field mt-2" value="{{ $survey->rw_dom ?? $anak->rw }}"></div>
                    <div><label class="text-xs font-semibold uppercase text-slate-500">Kelurahan</label><input readonly
                            class="field auto-field mt-2" value="{{ $survey->kelurahan_dom ?? $anak->kelurahan }}">
                    </div>
                    <div><label class="text-xs font-semibold uppercase text-slate-500">Kecamatan</label><input readonly
                            class="field auto-field mt-2" value="{{ $survey->kecamatan_dom ?? $anak->kecamatan }}">
                    </div>
                    <div><label class="text-xs font-semibold uppercase text-slate-500">Nama Ayah</label><input readonly
                            class="field auto-field mt-2" value="{{ $survey->nama_ayah ?? $anak->nama_ayah }}"></div>
                    <div><label class="text-xs font-semibold uppercase text-slate-500">Nama Ibu</label><input readonly
                            class="field auto-field mt-2" value="{{ $survey->nama_ibu ?? $anak->nama_ibu }}"></div>
                    <div><label class="text-xs font-semibold uppercase text-slate-500">Nomor HP Orang
                            Tua/Wali</label><input name="nomor_hp_orang_tua" type="tel" class="field mt-2"
                            value="{{ $survey->telp_anak ?? '' }}"></div>
                </div>
            </section>

            <section class="section-card">
                <div class="section-title">
                    <div class="section-number">3</div>
                    <h2 class="font-bold text-lg">Validasi Domisili</h2>
                </div>
                <p class="font-semibold text-sm mb-3">Apakah anak tinggal di alamat sesuai BNBA/KK?</p>
                <div class="space-y-2">
                    @php
                        $domisili = App\Tools\ParameterATS::data_domisili();

                        $val_domisili = collect($domisili)->pluck('value')->values()->toArray();
                        $status_domisili = $survey->status_alamat ?? '';
                        if ($status_domisili != '' && !in_array($status_domisili, $val_domisili)) {
                            $status_domisili = 'lainnya';
                        }
                    @endphp
                    @foreach ($domisili as $d)
                        <label class="choice">
                            <input @if ($status_domisili == $d['value']) checked @endif type="radio" name="domisili"
                                value="{{ $d['value'] }}" onchange="toggleDomisili()" required>
                            <span>{{ $d['text'] }}</span>
                        </label>
                    @endforeach

                </div>
                <div id="domisiliDetail" class="sub-card mt-4 hidden-block">
                    <div data-domisili="pindah_dalam" class="domisili-detail hidden-block"><label
                            class="text-xs font-semibold uppercase text-slate-500">Alamat Baru</label>
                        <textarea name="alamat_baru_dalam_kota" class="field mt-2" rows="2"></textarea>
                    </div>
                    <div data-domisili="pindah_luar" class="domisili-detail hidden-block grid md:grid-cols-2 gap-4">
                        <div><label class="text-xs font-semibold uppercase text-slate-500">Alamat Baru</label>
                            <textarea name="alamat_baru_luar_kota" class="field mt-2" rows="2"></textarea>
                        </div>
                        <div><label class="text-xs font-semibold uppercase text-slate-500">Nama Kabupaten/Kota
                                Baru</label><input name="kab_kota_baru" class="field mt-2"></div>
                        <div class="md:col-span-2"><label
                                class="text-xs font-semibold uppercase text-slate-500">Alasan
                                Pindah Luar Kota</label>
                            <textarea name="alasan_pindah_luar" class="field mt-2" rows="2"></textarea>
                        </div>
                    </div>
                    <div data-domisili="tidak_ditemukan" class="domisili-detail hidden-block"><label
                            class="text-xs font-semibold uppercase text-slate-500">Informasi
                            RT/RW/Tetangga/Keluarga</label>
                        <textarea name="informasi_tidak_ditemukan" class="field mt-2" rows="2"></textarea>
                    </div>
                    <div data-domisili="data_ganda" class="domisili-detail hidden-block"><label
                            class="text-xs font-semibold uppercase text-slate-500">Penjelasan Data Ganda/Salah
                            Sasaran</label>
                        <textarea name="penjelasan_data_ganda" class="field mt-2" rows="2"></textarea>
                    </div>
                    <div data-domisili="meninggal" class="domisili-detail hidden-block grid md:grid-cols-2 gap-4">
                        <div><label class="text-xs font-semibold uppercase text-slate-500">Alasan</label><input
                                name="alasan_meninggal" class="field mt-2" placeholder="Sakit/kecelakaan/dll"></div>
                        <div><label class="text-xs font-semibold uppercase text-slate-500">Tanggal
                                Meninggal</label><input type="date" name="tanggal_meninggal" class="field mt-2">
                        </div>
                    </div>
                    <div data-domisili="lainnya" class="domisili-detail hidden-block"><label
                            class="text-xs font-semibold uppercase text-slate-500">Keterangan Lainnya</label>
                        <textarea name="domisili_lainnya" class="field mt-2" rows="2"></textarea>
                    </div>
                </div>
            </section>

            <section class="section-card">
                <div class="section-title">
                    <div class="section-number">4</div>
                    <h2 class="font-bold text-lg">Status Keikutsertaan PAUD</h2>
                </div>
                <div class="grid md:grid-cols-2 gap-2">
                    @php
                        $sekolah = App\Tools\ParameterATS::data_sekolah();

                        $status_sekolah = $survey->status_sekolah ?? '';
                    @endphp

                    @foreach ($sekolah as $s)
                        <label class="choice">
                            <input type="radio" name="status_paud" value="{{ $s['value'] }}"
                                onchange="toggleStatusPaud()" @if ($s['value'] == $status_sekolah) checked @endif
                                required>
                            <span>{!! $s['text'] !!}</span>
                        </label>
                    @endforeach
                </div>
                <div class="mt-4 p-4 rounded-xl bg-amber-50 border border-amber-200 text-sm"><b>Catatan:</b> Untuk anak
                    yang sudah diketahui mengikuti PAUD atau sudah masuk SD/MI, statusnya perlu dimutakhirkan dalam
                    SIBUNDA berdasarkan hasil verifikasi oleh Bunda PAUD Kecamatan dan/atau Bunda PAUD Kelurahan.</div>
                <div id="poinSudahPaud" class="sub-card mt-4 hidden-block">
                    <h3 class="font-bold text-emerald-800 mb-4">Poin 1 — Jika Sudah Mengikuti PAUD</h3>
                    <div class="grid md:grid-cols-2 gap-4">
                        <div><label class="text-xs font-semibold uppercase text-slate-500">Status</label>
                            <div class="flex gap-5 mt-3"><label><input type="radio" name="wilayah_paud_saat_ini"
                                        value="dalam_kota" onchange="toggleNpsn('sudah')"> Dalam Kota
                                    Surabaya</label><label><input type="radio" name="wilayah_paud_saat_ini"
                                        value="luar_kota" onchange="toggleNpsn('sudah')"> Luar Kota Surabaya</label>
                            </div>
                        </div>
                        <div id="sudahNpsnWrap">
                            <label class="text-xs font-semibold uppercase text-slate-500">NPSN</label>
                            <input name="npsn_saat_ini" class="field mt-2" placeholder="Dipilih dari data SIBUNDA"
                                onkeyup="cari_sekolah(this,'saat_ini')">
                            <div id="wadah_saat_ini"></div>
                        </div>
                        <div><label class="text-xs font-semibold uppercase text-slate-500">Nama Satuan
                                PAUD</label><input name="nama_satuan_saat_ini" class="field mt-2"></div>
                        <div><label class="text-xs font-semibold uppercase text-slate-500">Jenis Layanan</label>
                            <div class="flex flex-wrap gap-4 mt-3">
                                <label><input type="radio" name="jenis_layanan" value="TK">
                                    TK</label>
                                <label><input type="radio" name="jenis_layanan" value="KB">
                                    KB</label>
                                <label><input type="radio" name="jenis_layanan" value="TPA">
                                    TPA</label>
                                <label><input type="radio" name="jenis_layanan" value="PPT">
                                    PPT</label>
                                <label><input type="radio" name="jenis_layanan" value="RA">
                                    RA</label>
                            </div>
                        </div>
                        <div class="md:col-span-2"><label
                                class="text-xs font-semibold uppercase text-slate-500">Alamat
                                Satuan PAUD</label>
                            <textarea name="alamat_satuan_saat_ini" class="field mt-2" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div id="poinAkanPaud" class="sub-card mt-4 hidden-block">
                    <h3 class="font-bold text-teal-800 mb-4">Poin 2 — Jika Akan Mengikuti PAUD</h3>
                    <div class="grid md:grid-cols-2 gap-4">
                        <div><label class="text-xs font-semibold uppercase text-slate-500">Orang tua sudah punya
                                gambaran sekolah di mana?</label>
                            <div class="flex gap-5 mt-3"><label><input type="radio" name="punya_gambaran_sekolah"
                                        value="ya"> Ya</label><label><input type="radio"
                                        name="punya_gambaran_sekolah" value="belum"> Belum</label></div>
                        </div>
                        <div><label class="text-xs font-semibold uppercase text-slate-500">Status</label>
                            <div class="flex gap-5 mt-3"><label><input type="radio" name="wilayah_paud_tujuan"
                                        value="dalam_kota" onchange="toggleNpsn('akan')"> Dalam
                                    Kota</label><label><input type="radio" name="wilayah_paud_tujuan"
                                        value="luar_kota" onchange="toggleNpsn('akan')"> Luar Kota</label></div>
                        </div>
                        <div id="akanNpsnWrap">
                            <label class="text-xs font-semibold uppercase text-slate-500">NPSN —
                                Khusus PAUD Dalam Kota</label>
                            <input name="npsn_tujuan" class="field mt-2" placeholder="Melihat data di SIBUNDA"
                                onkeyup="cari_sekolah(this,'tujuan')">
                            <div id="wadah_tujuan"></div>
                        </div>
                        <div><label class="text-xs font-semibold uppercase text-slate-500">Nama Satuan PAUD yang
                                Dituju</label><input name="nama_satuan_tujuan" class="field mt-2"></div>
                        <div class="md:col-span-2"><label
                                class="text-xs font-semibold uppercase text-slate-500">Alamat
                                Satuan PAUD yang
                                Dituju</label>
                            <textarea name="alamat_satuan_tujuan" class="field mt-2" rows="2"></textarea>
                        </div>
                        <div class="md:col-span-2"><label
                                class="text-xs font-semibold uppercase text-slate-500">Alasan</label>
                            <textarea name="alasan_pilihan_paud" class="field mt-2" rows="2"></textarea>
                        </div>
                    </div>
                </div>
            </section>

            <section class="section-card">
                <div class="section-title">
                    <div class="section-number">5</div>
                    <h2 class="font-bold text-lg">Alasan Belum Mengikuti PAUD</h2>
                </div>
                <!-- Step 1: Pilihan Faktor Terintegrasi Langsung dalam Form -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200">
                    <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-100">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600">
                            <i class="fa-solid fa-list-check mr-1.5 text-emerald-600"></i> Step 1: Centang Faktor
                            Terkait
                            (Klik untuk Buka Sub-Form)
                        </label>
                        <span id="selectedCountBadge"
                            class="text-xs font-semibold px-2.5 py-1 rounded-full bg-slate-100 text-slate-600">
                            0 dari 5 Terpilih
                        </span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3" id="factorSelectorGrid">

                        @php
                            $faktor = App\Tools\ParameterATS::data_faktor();
                            $arr_faktor = explode('|', $survey->detail_tidak_sekolah ?? '') ?? [];
                        @endphp
                        @foreach ($faktor as $f)
                            <!-- Checkbox Factor Pill Item 1 -->
                            <label id="pill-{{ $f['form'] }}"
                                class="factor-pill flex items-center justify-between p-3.5 rounded-xl border text-xs font-medium cursor-pointer transition-all bg-slate-50 border-slate-200 text-slate-700 hover:border-emerald-400">
                                <div class="flex items-center gap-2.5">
                                    <input @if (in_array($f['value'], $arr_faktor)) checked @endif type="checkbox"
                                        name="selected_factors[]" value="{{ $f['value'] }}"
                                        id="chk-input-{{ $f['form'] }}"
                                        onchange="toggleFactor('{{ $f['form'] }}')"
                                        class="w-4 h-4 text-emerald-600 rounded border-slate-300 focus:ring-emerald-500 cursor-pointer">
                                    <span>{{ $f['text'] }}</span>
                                </div>
                                <i
                                    class="fa-solid fa-chevron-down text-slate-400 text-xs transition-transform pill-icon"></i>
                            </label>
                        @endforeach
                    </div>

                    <!-- Visual Chip Ringkasan Faktor Aktif -->
                    <div id="selectedChipsContainer"
                        class="hidden mt-4 pt-3 border-t border-slate-100 flex flex-wrap items-center gap-2">
                        <span class="text-xs text-slate-500 font-medium mr-1">Faktor Terpilih:</span>
                        <div id="activeChipsList" class="flex flex-wrap gap-1.5"></div>
                    </div>
                </div>

                <!-- Empty State Warning jika tidak ada faktor yang dipilih -->
                <div id="emptyState"
                    class="bg-amber-50 border border-amber-200 rounded-2xl p-6 text-center text-amber-800">
                    <i class="fa-solid fa-circle-info text-2xl text-amber-500 mb-2"></i>
                    <p class="font-medium text-sm">Belum ada faktor yang dipilih.</p>
                    <p class="text-xs text-amber-600 mt-1">Silakan centang minimal satu faktor di atas untuk mengisi
                        rincian sub-form.</p>
                </div>

                <!-- FAKTOR 1 SUBFORM -->
                <div id="subform-f1"
                    class="hidden bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden subform-transition">
                    <div class="p-5 sm:p-6 bg-white border-b border-slate-100 flex items-center justify-between">
                        <h2 class="font-bold text-slate-800 text-base sm:text-lg">1. Faktor Ekonomi dan Kerentanan
                            Keluarga
                        </h2>
                        <span
                            class="text-xs text-emerald-700 bg-emerald-50 font-medium px-2.5 py-1 rounded-full border border-emerald-200 flex items-center gap-1">
                            <i class="fa-solid fa-circle-check text-emerald-600"></i> Active Form
                        </span>
                    </div>

                    <div class="p-5 sm:p-6 bg-slate-50/50 space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label
                                    class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">DATA
                                    DESIL DINSOS</label>
                                <input type="text" name="f1_desil" value="{{ $anak->desil_nasional ?? '' }}"
                                    class="w-full px-4 py-2.5 bg-slate-100 border border-slate-200 rounded-xl text-slate-700 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 font-medium"
                                    placeholder="Masukkan Desil">
                            </div>
                            <div>
                                <label
                                    class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">CATATAN
                                    VERIFIKASI</label>
                                <input type="text" name="f1_catatan"
                                    class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-slate-700 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500"
                                    placeholder="Tuliskan catatan verifikasi...">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- FAKTOR 2 SUBFORM -->
                <div id="subform-f2"
                    class="hidden bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden subform-transition">
                    <div class="p-5 sm:p-6 bg-white border-b border-slate-100 flex items-center justify-between">
                        <h2 class="font-bold text-slate-800 text-base sm:text-lg">2. Faktor Pengasuhan, Pemahaman, dan
                            Dukungan Keluarga</h2>
                        <span
                            class="text-xs text-emerald-700 bg-emerald-50 font-medium px-2.5 py-1 rounded-full border border-emerald-200 flex items-center gap-1">
                            <i class="fa-solid fa-circle-check text-emerald-600"></i> Active Form
                        </span>
                    </div>

                    <div class="p-5 sm:p-6 bg-slate-50/50 space-y-3">
                        <label
                            class="flex items-start gap-3 p-3.5 bg-white rounded-xl border border-slate-200 hover:border-slate-300 transition-all cursor-pointer">
                            <input type="checkbox" name="f2_options[]"
                                value="Orang tua/wali menganggap usia anak masih terlalu kecil atau pendidikan di rumah sudah cukup"
                                class="mt-0.5 w-4 h-4 text-emerald-600 rounded border-slate-300 focus:ring-emerald-500">
                            <span class="text-sm text-slate-700 leading-relaxed">Orang tua/wali menganggap usia anak
                                masih
                                terlalu kecil atau pendidikan di rumah sudah cukup</span>
                        </label>

                        <label
                            class="flex items-start gap-3 p-3.5 bg-white rounded-xl border border-slate-200 hover:border-slate-300 transition-all cursor-pointer">
                            <input type="checkbox" name="f2_options[]"
                                value="Orang tua/wali bekerja atau anak diasuh oleh kakek/nenek/keluarga"
                                class="mt-0.5 w-4 h-4 text-emerald-600 rounded border-slate-300 focus:ring-emerald-500">
                            <span class="text-sm text-slate-700 leading-relaxed">Orang tua/wali bekerja atau anak
                                diasuh
                                oleh kakek/nenek/keluarga</span>
                        </label>

                        <label
                            class="flex items-start gap-3 p-3.5 bg-white rounded-xl border border-slate-200 hover:border-slate-300 transition-all cursor-pointer">
                            <input type="checkbox" name="f2_options[]"
                                value="Orang tua/wali belum memahami pentingnya PAUD dan Wajib Belajar 1 Tahun Prasekolah"
                                class="mt-0.5 w-4 h-4 text-emerald-600 rounded border-slate-300 focus:ring-emerald-500">
                            <span class="text-sm text-slate-700 leading-relaxed">Orang tua/wali belum memahami
                                pentingnya
                                PAUD dan Wajib Belajar 1 Tahun Prasekolah</span>
                        </label>

                        <div class="pt-1">
                            <input type="text" name="f2_catatan"
                                class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-slate-700 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500"
                                placeholder="Catatan tambahan (misal: AAAA)">
                        </div>
                    </div>
                </div>

                <!-- FAKTOR 3 SUBFORM -->
                <div id="subform-f3"
                    class="hidden bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden subform-transition">
                    <div class="p-5 sm:p-6 bg-white border-b border-slate-100 flex items-center justify-between">
                        <div>
                            <h2 class="font-bold text-slate-800 text-base sm:text-lg">3. Faktor Kondisi Anak,
                                Kesehatan,
                                dan Kebutuhan Dukungan Khusus</h2>
                            <p class="text-xs text-slate-400 mt-0.5">Bridging data dengan Sayang Warga / Dinas
                                Kesehatan /
                                Dinas Sosial.</p>
                        </div>
                        <span
                            class="text-xs text-emerald-700 bg-emerald-50 font-medium px-2.5 py-1 rounded-full border border-emerald-200 flex items-center gap-1">
                            <i class="fa-solid fa-circle-check text-emerald-600"></i> Active Form
                        </span>
                    </div>

                    <div class="p-5 sm:p-6 bg-slate-50/50 space-y-3">
                        <label
                            class="flex items-start gap-3 p-3.5 bg-white rounded-xl border border-slate-200 hover:border-slate-300 transition-all cursor-pointer">
                            <input type="checkbox" name="f3_options[]"
                                value="Anak memiliki kondisi kesehatan tertentu atau sedang menjalani perawatan rutin, misalnya penyakit jantung bawaan, hydrocephalus, ketunaan, dan lain-lain"
                                class="mt-0.5 w-4 h-4 text-emerald-600 rounded border-slate-300 focus:ring-emerald-500">
                            <span class="text-sm text-slate-700 leading-relaxed">Anak memiliki kondisi kesehatan
                                tertentu
                                atau sedang menjalani perawatan rutin, misalnya penyakit jantung bawaan, hydrocephalus,
                                ketunaan, dan lain-lain</span>
                        </label>

                        <label
                            class="flex items-start gap-3 p-3.5 bg-white rounded-xl border border-slate-200 hover:border-slate-300 transition-all cursor-pointer">
                            <input type="checkbox" name="f3_options[]"
                                value="Anak memiliki hambatan tumbuh kembang, komunikasi, sosialisasi, atau adaptasi yang memerlukan layanan inklusi, pendampingan khusus, asesmen, atau rujukan lanjutan"
                                class="mt-0.5 w-4 h-4 text-emerald-600 rounded border-slate-300 focus:ring-emerald-500">
                            <span class="text-sm text-slate-700 leading-relaxed">Anak memiliki hambatan tumbuh kembang,
                                komunikasi, sosialisasi, atau adaptasi yang memerlukan layanan inklusi, pendampingan
                                khusus,
                                asesmen, atau rujukan lanjutan</span>
                        </label>

                        <div class="pt-1">
                            <input type="text" name="f3_catatan"
                                class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-slate-700 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500"
                                placeholder="Catatan tambahan (misal: BBB)">
                        </div>
                    </div>
                </div>

                <!-- FAKTOR 4 SUBFORM -->
                <div id="subform-f4"
                    class="hidden bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden subform-transition">
                    <div class="p-5 sm:p-6 bg-white border-b border-slate-100 flex items-center justify-between">
                        <div>
                            <h2 class="font-bold text-slate-800 text-base sm:text-lg">4. Faktor Administrasi</h2>
                            <p class="text-xs text-slate-400 mt-0.5">Bridging data Dispendukcapil dan
                                Diskominfo/Check-in
                                Warga.</p>
                        </div>
                        <span
                            class="text-xs text-emerald-700 bg-emerald-50 font-medium px-2.5 py-1 rounded-full border border-emerald-200 flex items-center gap-1">
                            <i class="fa-solid fa-circle-check text-emerald-600"></i> Active Form
                        </span>
                    </div>

                    <div class="p-5 sm:p-6 bg-slate-50/50 space-y-3">
                        <label
                            class="flex items-start gap-3 p-3.5 bg-white rounded-xl border border-slate-200 hover:border-slate-300 transition-all cursor-pointer">
                            <input type="checkbox" name="f4_options[]"
                                value="Anak belum memiliki akta kelahiran atau belum terdaftar dalam KK"
                                class="mt-0.5 w-4 h-4 text-emerald-600 rounded border-slate-300 focus:ring-emerald-500">
                            <span class="text-sm text-slate-700 leading-relaxed">Anak belum memiliki akta kelahiran
                                atau
                                belum terdaftar dalam KK</span>
                        </label>

                        <label
                            class="flex items-start gap-3 p-3.5 bg-white rounded-xl border border-slate-200 hover:border-slate-300 transition-all cursor-pointer">
                            <input type="checkbox" name="f4_options[]"
                                value="Dokumen anak/orang tua berbeda dengan domisili saat ini"
                                class="mt-0.5 w-4 h-4 text-emerald-600 rounded border-slate-300 focus:ring-emerald-500">
                            <span class="text-sm text-slate-700 leading-relaxed">Dokumen anak/orang tua berbeda dengan
                                domisili saat ini</span>
                        </label>

                        <div class="pt-1">
                            <input type="text" name="f4_catatan"
                                class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-slate-700 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500"
                                placeholder="Catatan tambahan (misal: CCC)">
                        </div>
                    </div>
                </div>

                <!-- FAKTOR 5 SUBFORM -->
                <div id="subform-f5"
                    class="hidden bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden subform-transition">
                    <div class="p-5 sm:p-6 bg-white border-b border-slate-100 flex items-center justify-between">
                        <h2 class="font-bold text-slate-800 text-base sm:text-lg">5. Lainnya — Jelaskan Secara Rinci
                        </h2>
                        <span
                            class="text-xs text-emerald-700 bg-emerald-50 font-medium px-2.5 py-1 rounded-full border border-emerald-200 flex items-center gap-1">
                            <i class="fa-solid fa-circle-check text-emerald-600"></i> Active Form
                        </span>
                    </div>

                    <div class="p-5 sm:p-6 bg-slate-50/50">
                        <textarea name="f5_detail" rows="4"
                            class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl text-slate-700 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 resize-y"
                            placeholder="Tuliskan penjelasan rinci lainnya di sini... (misal: DDD)"></textarea>
                    </div>
                </div>
            </section>

            @php
                $edukasi = App\Tools\ParameterATS::data_edukasi();

                $arr_edukasi = collect($edukasi)->pluck('value')->values()->toArray();
                $edukasi_ortu = explode('|', $survey->edukasi_ortu ?? '') ?? [];
                $edukasi_lainnya = array_diff($edukasi_ortu, $arr_edukasi);

                $edukasi_lainnya = implode('', $edukasi_lainnya);
            @endphp

            <section class="section-card">
                <div class="section-title">
                    <div class="section-number">6</div>
                    <h2 class="font-bold text-lg">Edukasi yang Diberikan kepada Orang Tua/Wali</h2>
                </div>
                <div class="grid md:grid-cols-2 gap-2">
                    @foreach ($edukasi as $e)
                        <label class="choice">
                            <input type="checkbox" @if (in_array($e['value'], $edukasi_ortu)) checked @endif name="edukasi[]"
                                value="{{ $e['value'] }}"><span>{{ $e['text'] }}</span>
                        </label>
                    @endforeach
                </div><input name="edukasi_lainnya" class="field mt-3" placeholder="Lainnya"
                    value="{{ $edukasi_lainnya }}">

                @php
                    $tanggapan = App\Tools\ParameterATS::data_tanggapan();

                    $arr_tanggapan = collect($tanggapan)->pluck('value')->values()->toArray();
                    $tanggapan_ortu = $survey->tanggapan_ortu ?? '';
                    $tanggapan_lainnya =
                        $tanggapan_ortu != '' && !in_array($tanggapan_ortu, $arr_tanggapan) ? $tanggapan_ortu : '';
                @endphp
                <div class="sub-card mt-4">
                    <h3 class="font-bold mb-3">Tanggapan Orang Tua/Wali Setelah Diberikan Edukasi</h3>
                    <div class="space-y-2">
                        @foreach ($tanggapan as $t)
                            <label class="choice">
                                <input @if ($t['value'] == $tanggapan_ortu) checked @endif type="radio"
                                    name="tanggapan_edukasi"
                                    value="{{ $t['value'] }}"><span>{{ $t['text'] }}</span>
                            </label>
                        @endforeach
                        <input name="tanggapan_lainnya" class="field" placeholder="Jelaskan tanggapan lainnya"
                            value="{{ $tanggapan_lainnya }}">
                    </div>
                </div>
            </section>

            <section class="section-card">
                <div class="section-title">
                    <div class="section-number">7</div>
                    <h2 class="font-bold text-lg">Rencana Tindak Lanjut</h2>
                </div>
                @php
                    $rtl = App\Tools\ParameterATS::data_rtl();
                    $tindak_lanjut = explode('|', $survey->tindak_lanjut ?? '') ?? [];
                @endphp
                <div class="grid md:grid-cols-2 gap-2">
                    @foreach ($rtl as $r)
                        <label class="choice">
                            <input @if (in_array($r['value'], $tindak_lanjut)) checked @endif type="checkbox" name="rtl[]"
                                value="{{ $r['value'] }}"><span>{{ $r['text'] }}</span>
                        </label>
                    @endforeach
                </div>
                <div class="sub-card mt-4"><label class="choice"><input type="checkbox" name="rtl_detail[]"
                            value="Penegakan diagnosis dan terapi ABK/Inklusi"><span class="font-semibold">a.
                            Penegakan diagnosis dan terapi
                            ABK/Inklusi</span></label>
                    <div class="ml-7 mt-2 space-y-2"><label class="choice"><input type="checkbox"
                                name="lokasi_rap[]"
                                value="Rumah ABK (Dinas Kesehatan) — hotline 089531365318"><span>Rumah ABK (Dinas
                                Kesehatan) — hotline
                                089531365318</span></label>
                        <p class="text-sm font-semibold mt-3">Rumah Anak Prestasi (Dinas Sosial):</p>
                        <div class="grid md:grid-cols-2 gap-2">
                            <label class="choice">
                                <input type="checkbox" name="lokasi_rap[]" value="RAP Sonokwijenan"><span>RAP
                                    Sonokwijenan</span>
                            </label>
                            <label class="choice">
                                <input type="checkbox" name="lokasi_rap[]" value="RAP Nginden Semolo"><span>RAP
                                    Nginden
                                    Semolo</span>
                            </label>
                            <label class="choice">
                                <input type="checkbox" name="lokasi_rap[]" value="RAP Kedung Cowek"><span>RAP Kedung
                                    Cowek</span>
                            </label>
                            <label class="choice">
                                <input type="checkbox" name="lokasi_rap[]" value="RAP Menanggal"><span>RAP
                                    Menanggal</span>
                            </label>
                        </div>
                    </div><label class="choice mt-3"><input type="checkbox" name="rtl_detail[]"
                            value="Pendampingan psikologis dan pola asuh orang tua ABK — PUSPAGA DP3APPKB, hotline 087722288959"><span><b>b.
                                Pendampingan psikologis dan pola asuh orang
                                tua
                                ABK</b> — PUSPAGA DP3APPKB, hotline 087722288959</span></label>
                </div>
                <input name="rtl_lainnya" class="field mt-4" placeholder="Lainnya">
            </section>

            <section class="section-card">
                <div class="section-title">
                    <div class="section-number">8</div>
                    <h2 class="font-bold text-lg">Dokumentasi</h2>
                </div>
                <div class="grid md:grid-cols-1">
                    <div><label class="text-xs font-semibold uppercase text-slate-500">Link Google Drive</label><input
                            type="url" name="link_google_drive" class="field mt-2"
                            placeholder="https://drive.google.com/..." value="{{ $survey->bukti_survei ?? '' }}">
                    </div>
                </div>
            </section>

            <section class="section-card">
                <div class="section-title">
                    <div class="section-number">9</div>
                    <h2 class="font-bold text-lg">Pernyataan Petugas</h2>
                </div>
                <p class="text-sm leading-6">Saya menyatakan bahwa data ini diisi berdasarkan hasil kunjungan
                    faktual/wawancara lapangan dan digunakan hanya untuk kepentingan pendataan, verifikasi, fasilitasi,
                    monitoring, evaluasi, serta tindak lanjut layanan PAUD.</p><label class="choice mt-4"><input
                        type="checkbox" name="pernyataan_benar" required><span>Saya menyetujui dan menyatakan data
                        yang diisi benar sesuai hasil verifikasi.</span></label>
                <div class="grid grid-cols-2 gap-8 text-center mt-12">
                    @php
                        $data_kecamatan = collect($ttd)->where('nama_unit', $user_bunda[0])->first();
                        $data_kelurahan = collect($ttd)->where('nama_unit', $user_bunda[1])->first();
                    @endphp
                    <div>
                        <p class="font-semibold">Bunda PAUD Kecamatan,</p>
                        {{-- <p>Pemilik tanda tangan = {{ $data_kecamatan->nama ?? '' }}</p> --}}
                        <div class="flex justify-center items-center my-2">
                            <img src="{{ $data_kecamatan->link_ttd ?? '' }}" alt="ttd_bunda_kecamatan"
                                style="height: 5cm"
                                onerror="this.src='https://upload.wikimedia.org/wikipedia/commons/1/14/No_Image_Available.jpg'">
                        </div>
                        <p>{{ $nama_bunda[0] }}</p>
                    </div>
                    <div>
                        <p class="font-semibold">Bunda PAUD Kelurahan,</p>
                        {{-- <p>Pemilik tanda tangan = {{ $data_kelurahan->nama ?? '' }}</p> --}}
                        <div class="flex justify-center items-center my-2">
                            <img src="{{ $data_kelurahan->link_ttd ?? '' }}" alt="ttd_bunda_kelurahan"
                                style="height: 5cm"
                                onerror="this.src='https://upload.wikimedia.org/wikipedia/commons/1/14/No_Image_Available.jpg'">
                        </div>
                        <p>{{ $nama_bunda[1] }}</p>
                    </div>
                </div>
            </section>

            <div class="no-print flex justify-end gap-3">
                <button type="button" onclick="window.close()"
                    class="px-5 py-3 rounded-xl border border-slate-300 font-semibold">Tutup</button>
                <button type="submit" name="btn_submit" value="Selesai"
                    class="px-5 py-3 rounded-xl text-white bg-blue-600 border border-slate-300 font-semibold">Simpan</button>
            </div>
        </form>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
    <script>
        lucide.createIcons();
        toggleDomisili();
        toggleStatusPaud();

        // Mapping label faktor
        const factorLabels = {
            f1: '1. Ekonomi & Kerentanan',
            f2: '2. Pengasuhan & Dukungan',
            f3: '3. Kondisi Anak & Kesehatan',
            f4: '4. Administrasi',
            f5: '5. Lainnya (Rincian)'
        };

        @foreach ($faktor as $f)
            @if (in_array($f['value'], $arr_faktor))
                toggleFactor('{{ $f['form'] }}')
            @endif
        @endforeach

        // Toggle Factor Active State
        function toggleFactor(factorId) {
            updateUI();
        }

        // Update view state dynamically
        function updateUI() {
            let activeCount = 0;
            const activeChipsList = document.getElementById('activeChipsList');
            activeChipsList.innerHTML = '';

            Object.keys(factorLabels).forEach(id => {
                const chkInput = document.getElementById(`chk-input-${id}`);
                const isActive = chkInput ? chkInput.checked : false;
                const pill = document.getElementById(`pill-${id}`);
                const subform = document.getElementById(`subform-${id}`);

                if (isActive) {
                    activeCount++;

                    // Style Pill Aktif
                    pill.classList.remove('bg-slate-50', 'border-slate-200', 'text-slate-700');
                    pill.classList.add('bg-emerald-50', 'border-emerald-500', 'text-emerald-800', 'shadow-sm');
                    pill.querySelector('.pill-icon').classList.add('rotate-180', 'text-emerald-600');

                    // Show Subform
                    subform.classList.remove('hidden');

                    // Add Chip Summary
                    const chip = document.createElement('span');
                    chip.className =
                        'text-[11px] bg-emerald-100 text-emerald-800 border border-emerald-300 px-2 py-0.5 rounded-md font-semibold flex items-center gap-1';
                    chip.innerHTML =
                        `${factorLabels[id]} <i class="fa-solid fa-xmark cursor-pointer hover:text-red-600 ml-0.5" onclick="deselectFactor('${id}')"></i>`;
                    activeChipsList.appendChild(chip);

                } else {
                    // Style Pill Inaktif
                    pill.classList.add('bg-slate-50', 'border-slate-200', 'text-slate-700');
                    pill.classList.remove('bg-emerald-50', 'border-emerald-500', 'text-emerald-800',
                        'shadow-sm');
                    pill.querySelector('.pill-icon').classList.remove('rotate-180', 'text-emerald-600');

                    // Hide Subform
                    subform.classList.add('hidden');
                }
            });

            // Update Counter Badge
            const badge = document.getElementById('selectedCountBadge');
            badge.innerText = `${activeCount} dari 5 Terpilih`;
            if (activeCount > 0) {
                badge.className =
                    'text-xs font-semibold px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-200';
            } else {
                badge.className = 'text-xs font-semibold px-2.5 py-1 rounded-full bg-slate-100 text-slate-600';
            }

            // Toggle Chips Container, Empty State & Action Buttons
            const chipsContainer = document.getElementById('selectedChipsContainer');
            const emptyState = document.getElementById('emptyState');
            const actionButtons = document.getElementById('actionButtons');

            if (activeCount > 0) {
                chipsContainer.classList.remove('hidden');
                emptyState.classList.add('hidden');
                // actionButtons.classList.remove('hidden');
            } else {
                chipsContainer.classList.add('hidden');
                emptyState.classList.remove('hidden');
                // actionButtons.classList.add('hidden');
            }
        }

        // Deselect Factor via Chip
        function deselectFactor(id) {
            const chkInput = document.getElementById(`chk-input-${id}`);
            if (chkInput) {
                chkInput.checked = false;
                updateUI();
            }
        }

        function toggleDomisili() {
            const selected = document.querySelector('input[name="domisili"]:checked')?.value;
            const wrap = document.getElementById('domisiliDetail');
            document.querySelectorAll('.domisili-detail').forEach(el => el.style.display = 'none');
            if (selected && selected !== 'sesuai') {
                wrap.style.display = 'block';
                const target = document.querySelector(`[data-domisili="${selected}"]`);
                if (target) target.style.display = target.classList.contains('grid') ? 'grid' : 'block';
            } else wrap.style.display = 'none';

            // 2. Sembunyikan container utama & reset atribut required di seluruh elemen detail
            wrap.classList.add('hidden-block');
            const allInputs = wrap.querySelectorAll('input, textarea');
            allInputs.forEach(input => {
                input.removeAttribute('required');
            });

            // 3. Jika ada pilihan yang dipilih
            if (selected) {
                const activeDetail = wrap.querySelector(`[data-domisili="${selected}"]`);

                // Sembunyikan semua sub-detail terlebih dahulu
                wrap.querySelectorAll('.domisili-detail').forEach(el => {
                    el.classList.add('hidden-block');
                });

                // Jika sub-detail untuk nilai yang dipilih ditemukan (selain pilihan 'sesuai'/yang tidak butuh detail)
                if (activeDetail) {
                    wrap.classList.remove('hidden-block');
                    activeDetail.classList.remove('hidden-block');

                    // Tambahkan atribut required HANYA ke field yang ada di dalam blok aktif
                    const activeInputs = activeDetail.querySelectorAll('input, textarea');
                    activeInputs.forEach(input => {
                        input.setAttribute('required', 'required');
                    });
                }
            }
        }

        // function toggleStatusPaud() {
        //     const selected = document.querySelector('input[name="status_paud"]:checked')?.value;
        //     document.getElementById('poinSudahPaud').style.display = selected === 'sudah_paud' ? 'block' : 'none';
        //     document.getElementById('poinAkanPaud').style.display = selected === 'akan_daftar' ? 'block' : 'none';
        // }

        // function toggleNpsn(type) {
        //     const group = type === 'sudah' ? 'wilayah_paud_saat_ini' : 'wilayah_paud_tujuan';
        //     const wrap = document.getElementById(type === 'sudah' ? 'sudahNpsnWrap' : 'akanNpsnWrap');
        //     const val = document.querySelector(`input[name="${group}"]:checked`)?.value;
        //     wrap.style.display = val === 'luar_kota' ? 'none' : 'block';
        // }

        // Helper function untuk memasang/mencabut required pada suatu kontainer
        function setRequiredState(container, isRequired) {
            if (!container) return;

            // Ambil elemen input, textarea, dan select di dalam kontainer yang tidak tersembunyi secara internal
            const inputs = container.querySelectorAll('input, textarea, select');

            inputs.forEach(input => {
                // Jika kontainer induk tersembunyi (hidden-block), cabut required
                if (!isRequired) {
                    input.removeAttribute('required');
                } else {
                    // Jika elemen berada di dalam sub-wrapper yang tersembunyi, jangan beri required
                    const isSubHidden = input.closest('.hidden-block') && input.closest('.hidden-block') !==
                        container;
                    if (!isSubHidden) {
                        input.setAttribute('required', 'required');
                    }
                }
            });
        }

        // 1. Fungsi Utama: Toggle Poin 1 (Sudah) vs Poin 2 (Akan)
        function toggleStatusPaud() {
            const status = document.querySelector('input[name="status_paud"]:checked')?.value;
            const poinSudah = document.getElementById('poinSudahPaud');
            const poinAkan = document.getElementById('poinAkanPaud');

            // Reset awal: Sembunyikan dan cabut required dari kedua section
            [poinSudah, poinAkan].forEach(el => {
                if (el) {
                    el.classList.add('hidden-block');
                    setRequiredState(el, false);
                }
            });

            // Sesuaikan nilai value ini dengan return dari ParameterATS::data_sekolah() Anda
            if (status === 'sudah' || status === 'sudah_paud') {
                poinSudah.classList.remove('hidden-block');
                setRequiredState(poinSudah, true);
                toggleNpsn('sudah'); // Jalankan pengecekan sub-kondisi NPSN
            } else if (status === 'akan' || status === 'akan_paud' || status === 'akan_daftar') {
                poinAkan.classList.remove('hidden-block');
                setRequiredState(poinAkan, true);
                toggleNpsn('akan'); // Jalankan pengecekan sub-kondisi NPSN
            }
        }

        // 2. Fungsi Sub-Kondisi: Toggle Input NPSN (Dalam Kota vs Luar Kota)
        function toggleNpsn(tipe) {
            let radioName, wrapId;

            if (tipe === 'sudah') {
                radioName = 'wilayah_paud_saat_ini';
                wrapId = 'sudahNpsnWrap';
            } else if (tipe === 'akan') {
                radioName = 'wilayah_paud_tujuan';
                wrapId = 'akanNpsnWrap';
            }

            const wilayah = document.querySelector(`input[name="${radioName}"]:checked`)?.value;
            const wrap = document.getElementById(wrapId);

            if (!wrap) return;

            if (wilayah === 'dalam_kota') {
                wrap.classList.remove('hidden-block');
                setRequiredState(wrap, true);
            } else {
                // Jika Luar Kota / belum dipilih, sembunyikan NPSN & cabut required
                wrap.classList.add('hidden-block');
                setRequiredState(wrap, false);
            }
        }

        function resetForm() {
            if (confirm('Kosongkan seluruh isian formulir?')) {
                document.getElementById('absForm').reset();
                document.getElementById('domisiliDetail').style.display = 'none';
                document.getElementById('poinSudahPaud').style.display = 'none';
                document.getElementById('poinAkanPaud').style.display = 'none';
            }
        }

        function simpan() {
            event.preventDefault();

            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            var data = new FormData($('#absForm')[0]);

            var url = '{{ route('bunda-monitoring-blm_sekolah-survey', ['halaman' => 'simpan']) }}';

            $.ajax({
                url: url,
                type: 'post',
                data: data,
                headers: {
                    'X-CSRF-TOKEN': token // Menyisipkan token CSRF Laravel
                },
                contentType: false,
                processData: false,
                success: function(data) {
                    Swal.fire({
                        title: 'Success',
                        text: 'Berhasil simpan',
                        icon: 'success',
                    })
                }
            });
        }

        function cari_sekolah(ini, wadah) {
            var cari = ini.value;

            if (cari.length > 4) {
                const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                $.ajax({
                    url: '{{ route('bunda-monitoring-blm_sekolah-survey', ['halaman' => 'cari_sekolah']) }}',
                    type: 'post',
                    data: {
                        cari: cari
                    },
                    headers: {
                        'X-CSRF-TOKEN': token // Menyisipkan token CSRF Laravel
                    },
                    success: function(data) {
                        var html = '';
                        if (data.length > 0) {
                            $.each(data, function(k, v) {
                                html +=
                                    '<a href="javascript:void(0)" onclick="set_sekolah(\'' + v
                                    .nama +
                                    '\',\'' + v.npsn + '\',\'' + btoa(v.alamat) + '\',\'' + v
                                    .jenjang +
                                    '\', \'' + wadah + '\')">' +
                                    v
                                    .nama + ' (' + v
                                    .npsn +
                                    ')</a><br>';
                            })
                        }
                        $('#wadah_' + wadah).html(html);
                    }
                })
            }
        }

        function set_sekolah(nama, npsn, alamat, jenjang, idnya) {
            $('input[name=npsn_' + idnya + ']').val(npsn);
            $('input[name=nama_satuan_' + idnya + ']').val(nama);
            $('textarea[name=alamat_satuan_' + idnya + ']').val(atob(alamat));
            $(`input[name=jenis_layanan][value="${jenjang}"]`).prop('checked', true);

            $('#wadah_' + idnya).html('');
        }
    </script>
</body>

</html>
