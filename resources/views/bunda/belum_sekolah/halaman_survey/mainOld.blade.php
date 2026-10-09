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
            <div class="flex gap-2"><button type="button" onclick="resetForm()"
                    class="px-4 py-2 rounded-lg bg-rose-500/30 border border-white/20 text-sm font-semibold">Reset</button><button
                    type="button" onclick="window.print()"
                    class="px-4 py-2 rounded-lg bg-white text-emerald-800 text-sm font-semibold">Cetak / PDF</button>
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

        {{-- <form id="absForm" class="space-y-6" onsubmit="simpan()"> --}}
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
                        value="{{ $survey->nama_petugas ?? $petugas->nama }}"></div>
                <div><label class="text-xs font-semibold uppercase text-slate-500">Unsur/Jabatan</label><input
                        type="text" name="unsur_jabatan" class="field mt-2"
                        value="{{ $survey->jabatan_petugs ?? $petugas->nama_jabatan }}"></div>
                <div><label class="text-xs font-semibold uppercase text-slate-500"
                        value="{{ $survey->telp_petugas ?? $petugas->no_telp }}">Nomor HP</label><input type="tel"
                        name="nomor_hp_petugas" class="field mt-2" placeholder="08xxxxxxxxxx"></div>
                <div><label class="text-xs font-semibold uppercase text-slate-500">Kelurahan</label><input
                        type="text" name="kelurahan_petugas" class="field mt-2"
                        value="{{ $survey->kelurahan ?? $petugas->kelurahan_dispenduk }}"></div>
                <div><label class="text-xs font-semibold uppercase text-slate-500">Kecamatan</label><input
                        type="text" name="kecamatan_petugas" class="field mt-2"
                        value="{{ $survey->kecamatan ?? $petugas->kecamatan_dispenduk }}"></div>
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
                        Kelamin</label><input readonly class="field auto-field mt-2" placeholder="Otomatis dari sistem"
                        value="{{ $survey->jenis_kelamin ?? $anak->jenis_kelamin }}"></div>
                @php
                    $tgl_lahir = $survey->tgl_lahir ?? $anak->tgl_lahir;
                    $tgl_lahir = date('d-m-Y', strtotime($tgl_lahir));
                @endphp
                <div><label class="text-xs font-semibold uppercase text-slate-500">Tempat, Tanggal
                        Lahir</label><input readonly class="field auto-field mt-2" placeholder="Otomatis dari sistem"
                        value="{{ $survey->tempat_lahir ?? $anak->tempat_lahir }}, {{ $tgl_lahir }}">
                </div>
                <div class="md:col-span-2"><label class="text-xs font-semibold uppercase text-slate-500">Alamat</label>
                    <textarea readonly rows="2" class="field auto-field mt-2" placeholder="Otomatis dari sistem">{{ $survey->alamat_dom ?? $anak->alamat_dom }}</textarea>
                </div>
                <div><label class="text-xs font-semibold uppercase text-slate-500">RT</label><input readonly
                        class="field auto-field mt-2" value="{{ $survey->rt_dom ?? $anak->rt_dom }}">
                </div>
                <div><label class="text-xs font-semibold uppercase text-slate-500">RW</label><input readonly
                        class="field auto-field mt-2" value="{{ $survey->rw_dom ?? $anak->rw_dom }}"></div>
                <div><label class="text-xs font-semibold uppercase text-slate-500">Kelurahan</label><input readonly
                        class="field auto-field mt-2" value="{{ $survey->kelurahan_dom ?? $anak->kelurahan_dom }}">
                </div>
                <div><label class="text-xs font-semibold uppercase text-slate-500">Kecamatan</label><input readonly
                        class="field auto-field mt-2" value="{{ $survey->kecamatan_dom ?? $anak->kecamatan_dom }}">
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
                <label class="choice"><input type="radio" name="domisili" value="sesuai"
                        onchange="toggleDomisili()"><span>Ya, anak tinggal sesuai alamat</span></label>
                <label class="choice"><input type="radio" name="domisili" value="pindah_dalam"
                        onchange="toggleDomisili()"><span>Tidak, anak pindah dalam Kota Surabaya</span></label>
                <label class="choice"><input type="radio" name="domisili" value="pindah_luar"
                        onchange="toggleDomisili()"><span>Tidak, anak pindah luar Kota Surabaya</span></label>
                <label class="choice"><input type="radio" name="domisili" value="tidak_ditemukan"
                        onchange="toggleDomisili()"><span>Anak tidak ditemukan di alamat</span></label>
                <label class="choice"><input type="radio" name="domisili" value="data_ganda"
                        onchange="toggleDomisili()"><span>Data ganda / salah sasaran</span></label>
                <label class="choice"><input type="radio" name="domisili" value="meninggal"
                        onchange="toggleDomisili()"><span>Meninggal dunia</span></label>
                <label class="choice"><input type="radio" name="domisili" value="lainnya"
                        onchange="toggleDomisili()"><span>Lainnya</span></label>
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
                    <div class="md:col-span-2"><label class="text-xs font-semibold uppercase text-slate-500">Alasan
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
                <label class="choice"><input type="radio" name="status_paud" value="sudah_paud"
                        onchange="toggleStatusPaud()"><span>Sudah mengikuti PAUD <b>(lanjut Poin
                            1)</b></span></label>
                <label class="choice"><input type="radio" name="status_paud" value="akan_daftar"
                        onchange="toggleStatusPaud()"><span>Akan mendaftar tahun ajaran 2026/2027 <b>(lanjut Poin
                            2)</b></span></label>
                <label class="choice"><input type="radio" name="status_paud" value="sudah_sd"
                        onchange="toggleStatusPaud()"><span>Sudah masuk SD/MI atau sederajat</span></label>
                <label class="choice"><input type="radio" name="status_paud" value="belum_paud"
                        onchange="toggleStatusPaud()"><span>Belum mengikuti PAUD</span></label>
                <label class="choice"><input type="radio" name="status_paud" value="pernah_berhenti"
                        onchange="toggleStatusPaud()"><span>Pernah mengikuti PAUD tetapi berhenti</span></label>
                <label class="choice"><input type="radio" name="status_paud" value="verifikasi_ulang"
                        onchange="toggleStatusPaud()"><span>Tidak diketahui/perlu verifikasi ulang</span></label>
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
                    <div class="md:col-span-2"><label class="text-xs font-semibold uppercase text-slate-500">Alamat
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
                                Kota</label><label><input type="radio" name="wilayah_paud_tujuan" value="luar_kota"
                                    onchange="toggleNpsn('akan')"> Luar Kota</label></div>
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
                    <div class="md:col-span-2"><label class="text-xs font-semibold uppercase text-slate-500">Alamat
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

        <form id="absForm" class="space-y-6" onsubmit="simpan()">
            <section class="section-card">
                <div class="section-title">
                    <div class="section-number">5</div>
                    <div>
                        <h2 class="font-bold text-lg">Alasan Belum Mengikuti PAUD</h2>
                        <p class="small-note">Dapat dipilih lebih dari satu sesuai kondisi faktual.</p>
                    </div>
                </div>
                <div class="space-y-5">
                    <div class="sub-card">
                        <h3 class="font-bold mb-2">1. Faktor Ekonomi dan Kerentanan Keluarga</h3>
                        <div class="grid md:grid-cols-2 gap-4">
                            <div><label class="text-xs font-semibold uppercase text-slate-500">Data Desil
                                    Dinsos</label><input readonly name="data_desil" class="field auto-field mt-2"
                                    placeholder="Otomatis menarik data Desil Dinsos"
                                    value="{{ $anak->desil_nasional ?? '' }}"></div>
                            <div><label class="text-xs font-semibold uppercase text-slate-500">Catatan
                                    Verifikasi</label><input name="catatan_desil" class="field mt-2"></div>
                        </div>
                    </div>
                    <div class="sub-card">
                        <h3 class="font-bold mb-3">2. Faktor Pengasuhan, Pemahaman, dan Dukungan Keluarga</h3>
                        <div class="space-y-2"><label class="choice"><input type="checkbox"
                                    name="alasan_pengasuhan[]" value="usia_masih_kecil"><span>Orang tua/wali
                                    menganggap usia anak masih terlalu kecil atau pendidikan di rumah sudah
                                    cukup</span></label><label class="choice"><input type="checkbox"
                                    name="alasan_pengasuhan[]" value="orang_tua_bekerja"><span>Orang tua/wali bekerja
                                    atau anak diasuh oleh kakek/nenek/keluarga</span></label><label
                                class="choice"><input type="checkbox" name="alasan_pengasuhan[]"
                                    value="belum_memahami"><span>Orang
                                    tua/wali belum memahami pentingnya PAUD dan
                                    Wajib Belajar 1 Tahun Prasekolah</span></label><input
                                name="alasan_pengasuhan_lainnya" class="field" placeholder="Lainnya"></div>
                    </div>
                    <div class="sub-card">
                        <h3 class="font-bold mb-1">3. Faktor Kondisi Anak, Kesehatan, dan Kebutuhan Dukungan Khusus
                        </h3>
                        <p class="small-note mb-3">Bridging data dengan Sayang Warga / Dinas Kesehatan / Dinas Sosial.
                        </p>
                        <div class="space-y-2"><label class="choice"><input type="checkbox"
                                    name="alasan_kondisi_anak[]" value="kesehatan"><span>Anak memiliki kondisi
                                    kesehatan tertentu atau sedang menjalani perawatan rutin, misalnya penyakit jantung
                                    bawaan, hydrocephalus, ketunaan, dan lain-lain</span></label><label
                                class="choice"><input type="checkbox" name="alasan_kondisi_anak[]"
                                    value="hambatan_tumbuh_kembang"><span>Anak memiliki hambatan tumbuh kembang,
                                    komunikasi, sosialisasi, atau adaptasi yang memerlukan layanan inklusi, pendampingan
                                    khusus, asesmen, atau rujukan lanjutan</span></label><input
                                name="alasan_kondisi_lainnya" class="field" placeholder="Lainnya"></div>
                    </div>
                    <div class="sub-card">
                        <h3 class="font-bold mb-1">4. Faktor Administrasi</h3>
                        <p class="small-note mb-3">Bridging data Dispendukcapil dan Diskominfo/Check-in Warga.</p>
                        <div class="space-y-2"><label class="choice"><input type="checkbox"
                                    name="alasan_administrasi[]" value="belum_akta_kk"><span>Anak belum memiliki akta
                                    kelahiran atau belum terdaftar dalam KK</span></label><label class="choice"><input
                                    type="checkbox" name="alasan_administrasi[]"
                                    value="dokumen_beda_domisili"><span>Dokumen anak/orang tua berbeda dengan domisili
                                    saat ini</span></label><input name="alasan_administrasi_lainnya" class="field"
                                placeholder="Lainnya"></div>
                    </div>
                    <div class="sub-card">
                        <h3 class="font-bold mb-3">5. Lainnya — Jelaskan Secara Rinci</h3>
                        <textarea name="alasan_lainnya_rinci" rows="3" class="field"></textarea>
                    </div>
                </div>
            </section>
            <button type="submit">Simpan</button>
        </form>

        <section class="section-card">
            <div class="section-title">
                <div class="section-number">6</div>
                <h2 class="font-bold text-lg">Edukasi yang Diberikan kepada Orang Tua/Wali</h2>
            </div>
            <div class="grid md:grid-cols-2 gap-2">
                <label class="choice"><input type="checkbox" name="edukasi[]"
                        value="pentingnya_paud"><span>Pentingnya PAUD bagi tumbuh kembang anak</span></label><label
                    class="choice"><input type="checkbox" name="edukasi[]" value="wajib_belajar"><span>Kebijakan
                        Wajib Belajar 1 Tahun Prasekolah</span></label><label class="choice"><input type="checkbox"
                        name="edukasi[]" value="paud_terdekat"><span>Informasi satuan PAUD
                        terdekat yang tersedia</span></label><label class="choice"><input type="checkbox"
                        name="edukasi[]" value="bantuan_beasiswa"><span>Informasi bantuan kesehatan/beasiswa
                        PAUD</span></label><label class="choice"><input type="checkbox" name="edukasi[]"
                        value="layanan_inklusi"><span>Informasi layanan PAUD inklusi atau dukungan bagi
                        ABK</span></label><label class="choice"><input type="checkbox" name="edukasi[]"
                        value="adminduk"><span>Informasi pengurusan dokumen administrasi
                        kependudukan</span></label>
            </div><input name="edukasi_lainnya" class="field mt-3" placeholder="Lainnya">
            <div class="sub-card mt-4">
                <h3 class="font-bold mb-3">Tanggapan Orang Tua/Wali Setelah Diberikan Edukasi</h3>
                <div class="space-y-2"><label class="choice"><input type="radio" name="tanggapan_edukasi"
                            value="bersedia"><span>Bersedia mendaftarkan anak ke PAUD</span></label><label
                        class="choice"><input type="radio" name="tanggapan_edukasi"
                            value="mempertimbangkan"><span>Masih mempertimbangkan</span></label><label
                        class="choice"><input type="radio" name="tanggapan_edukasi"
                            value="lainnya"><span>Lainnya</span></label><input name="tanggapan_lainnya"
                        class="field" placeholder="Jelaskan tanggapan lainnya"></div>
            </div>
        </section>

        <section class="section-card">
            <div class="section-title">
                <div class="section-number">7</div>
                <h2 class="font-bold text-lg">Rencana Tindak Lanjut</h2>
            </div>
            <div class="grid md:grid-cols-2 gap-2">
                <label class="choice"><input type="checkbox" name="rtl[]" value="edukasi_lanjutan"><span>Dilakukan
                        edukasi atau kunjungan lanjutan kepada orang
                        tua/wali</span></label><label class="choice"><input type="checkbox" name="rtl[]"
                        value="fasilitasi_daftar"><span>Difasilitasi mendaftar ke satuan PAUD
                        terdekat</span></label><label class="choice"><input type="checkbox" name="rtl[]"
                        value="koordinasi_opd"><span>Dikoordinasikan dengan Dinas Pendidikan/OPD
                        terkait</span></label><label class="choice"><input type="checkbox" name="rtl[]"
                        value="bantuan"><span>Diusulkan bantuan/beasiswa/dukungan sosial sesuai
                        ketentuan</span></label><label class="choice"><input type="checkbox" name="rtl[]"
                        value="disdukcapil"><span>Dirujuk ke layanan administrasi kependudukan
                        (Disdukcapil)</span></label><label class="choice"><input type="checkbox" name="rtl[]"
                        value="kesehatan_abk"><span>Dirujuk ke layanan kesehatan/ABK/inklusi (Rumah
                        ABK/PUSPAGA/RAP)</span></label>
            </div>
            <div class="sub-card mt-4"><label class="choice"><input type="checkbox" name="rtl_detail[]"
                        value="diagnosa_terapi"><span class="font-semibold">a. Penegakan diagnosis dan terapi
                        ABK/Inklusi</span></label>
                <div class="ml-7 mt-2 space-y-2"><label class="choice"><input type="checkbox" name="rujukan_abk[]"
                            value="rumah_abk"><span>Rumah ABK (Dinas Kesehatan) — hotline
                            089531365318</span></label>
                    <p class="text-sm font-semibold mt-3">Rumah Anak Prestasi (Dinas Sosial):</p>
                    <div class="grid md:grid-cols-2 gap-2"><label class="choice"><input type="checkbox"
                                name="lokasi_rap[]" value="sonokwijenan"><span>RAP
                                Sonokwijenan</span></label><label class="choice"><input type="checkbox"
                                name="lokasi_rap[]" value="nginden_semolo"><span>RAP Nginden
                                Semolo</span></label><label class="choice"><input type="checkbox" name="lokasi_rap[]"
                                value="kedung_cowek"><span>RAP Kedung
                                Cowek</span></label><label class="choice"><input type="checkbox" name="lokasi_rap[]"
                                value="menanggal"><span>RAP Menanggal</span></label></div>
                </div><label class="choice mt-3"><input type="checkbox" name="rtl_detail[]"
                        value="pendampingan_psikologis"><span><b>b. Pendampingan psikologis dan pola asuh orang tua
                            ABK</b> — PUSPAGA DP3APPKB, hotline 087722288959</span></label>
            </div>
            <input name="rtl_lainnya" class="field mt-4" placeholder="Lainnya">
        </section>

        <section class="section-card">
            <div class="section-title">
                <div class="section-number">8</div>
                <h2 class="font-bold text-lg">Dokumentasi</h2>
            </div>
            <div class="grid md:grid-cols-2 gap-4">
                <div><label class="text-xs font-semibold uppercase text-slate-500">Foto Tersedia?</label>
                    <div class="flex gap-6 mt-3"><label><input type="radio" name="foto_tersedia" value="ya">
                            Ya</label><label><input type="radio" name="foto_tersedia" value="tidak"> Tidak</label>
                    </div>
                </div>
                <div><label class="text-xs font-semibold uppercase text-slate-500">Link Google Drive</label><input
                        type="url" name="link_google_drive" class="field mt-2"
                        placeholder="https://drive.google.com/..."></div>
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
                <div>
                    <p class="font-semibold">Bunda PAUD Kecamatan,</p>
                    <div class="h-20"></div><input name="ttd_bunda_kecamatan" class="field text-center"
                        placeholder="Nama lengkap">
                </div>
                <div>
                    <p class="font-semibold">Bunda PAUD Kelurahan,</p>
                    <div class="h-20"></div><input name="ttd_bunda_kelurahan" class="field text-center"
                        placeholder="Nama lengkap">
                </div>
            </div>
        </section>

        <div class="no-print flex justify-end gap-3">
            <button type="button" onclick="window.close()"
                class="px-5 py-3 rounded-xl border border-slate-300 font-semibold">Tutup</button>
            <button type="button" onclick="window.print()"
                class="px-5 py-3 rounded-xl bg-emerald-600 text-white font-semibold">Cetak PDF</button>
            <button type="submit"
                class="px-5 py-3 rounded-xl text-white bg-blue-600 border border-slate-300 font-semibold">Simpan</button>
        </div>
        {{-- </form> --}}
    </main>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
    <script>
        lucide.createIcons();

        function toggleDomisili() {
            const selected = document.querySelector('input[name="domisili"]:checked')?.value;
            const wrap = document.getElementById('domisiliDetail');
            document.querySelectorAll('.domisili-detail').forEach(el => el.style.display = 'none');
            if (selected && selected !== 'sesuai') {
                wrap.style.display = 'block';
                const target = document.querySelector(`[data-domisili="${selected}"]`);
                if (target) target.style.display = target.classList.contains('grid') ? 'grid' : 'block';
            } else wrap.style.display = 'none';
        }

        function toggleStatusPaud() {
            const selected = document.querySelector('input[name="status_paud"]:checked')?.value;
            document.getElementById('poinSudahPaud').style.display = selected === 'sudah_paud' ? 'block' : 'none';
            document.getElementById('poinAkanPaud').style.display = selected === 'akan_daftar' ? 'block' : 'none';
        }

        function toggleNpsn(type) {
            const group = type === 'sudah' ? 'wilayah_paud_saat_ini' : 'wilayah_paud_tujuan';
            const wrap = document.getElementById(type === 'sudah' ? 'sudahNpsnWrap' : 'akanNpsnWrap');
            const val = document.querySelector(`input[name="${group}"]:checked`)?.value;
            wrap.style.display = val === 'luar_kota' ? 'none' : 'block';
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
                                    '<a href="javascript:void(0)" onclick="set_sekolah(\'' + v.nama +
                                    '\',\'' + v.npsn + '\',\'' + btoa(v.alamat) + '\',\'' + v.jenjang +
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
