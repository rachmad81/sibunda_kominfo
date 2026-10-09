<!DOCTYPE html>
<html lang="id">
@php
    $title = $jenjang == 'sd' ? 'Instrumen Monev MPLS SD ' . $tahun : 'Instrumen Monev MPLS PAUD ' . $tahun;
@endphp

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }}</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            background: #f0f0f0;
            color: #000;
            font-size: 12pt;
        }

        /* TOP BAR */
        .topbar {
            position: sticky;
            top: 0;
            z-index: 100;
            background: #222;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 20px;
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn-back {
            background: none;
            border: 1px solid #aaa;
            color: #fff;
            font-family: inherit;
            font-size: 11pt;
            padding: 5px 14px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .btn-back:hover {
            background: #444;
            border-color: #fff;
        }

        .topbar-title {
            font-size: 11pt;
            font-weight: 600;
            letter-spacing: 0.3px;
        }

        .btn-close {
            background: none;
            border: 1px solid #aaa;
            color: #fff;
            font-family: inherit;
            font-size: 13pt;
            width: 30px;
            height: 30px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            line-height: 1;
        }

        .btn-close:hover {
            background: #555;
            border-color: #fff;
        }

        /* TOAST NOTIF */
        .toast {
            position: fixed;
            bottom: 24px;
            right: 24px;
            background: #222;
            color: #fff;
            padding: 10px 20px;
            font-size: 10.5pt;
            border: 1px solid #555;
            display: none;
            z-index: 999;
        }

        .toast.show {
            display: block;
        }

        /* PAGE */
        .page {
            max-width: 800px;
            margin: 20px auto 40px;
            background: #fff;
            padding: 32px 40px;
            border: 1px solid #ccc;
        }

        /* HEADER */
        .doc-header {
            text-align: center;
            border: 2px solid #000;
            padding: 14px 20px;
            margin-bottom: 20px;
        }

        .doc-header h1 {
            font-size: 13pt;
            font-weight: 700;
            text-transform: uppercase;
            line-height: 1.5;
        }

        .doc-header h2 {
            font-size: 12pt;
            font-weight: 700;
            text-transform: uppercase;
            margin-top: 4px;
            line-height: 1.4;
        }

        /* IDENTITAS */
        .identitas-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
            font-size: 11pt;
        }

        .identitas-table td {
            padding: 5px 8px;
            vertical-align: top;
        }

        .identitas-table td:nth-child(1) {
            width: 38%;
            font-weight: 600;
        }

        .identitas-table td:nth-child(2) {
            width: 2%;
        }

        .identitas-table td:nth-child(3) {
            width: 60%;
        }

        .id-input {
            width: 100%;
            border: none;
            border-bottom: 1px solid #555;
            font-family: inherit;
            font-size: 11pt;
            outline: none;
            background: transparent;
            padding: 2px 0;
        }

        /* PETUNJUK */
        .petunjuk {
            border: 1px solid #888;
            border-left: 3px solid #555;
            padding: 8px 12px;
            margin-bottom: 14px;
            font-size: 10.5pt;
            background: #f5f5f5;
            line-height: 1.6;
            color: #000;
        }

        /* PROGRESS */
        .progress-row {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 12px;
            font-size: 10pt;
            color: #444;
        }

        .progress-bg {
            flex: 1;
            height: 5px;
            background: #ddd;
            border: 1px solid #bbb;
        }

        .progress-fill {
            height: 100%;
            background: #555;
            transition: width .3s;
        }

        /* INDICATOR TABLE */
        .ind-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10.5pt;
            margin-bottom: 20px;
        }

        .ind-table th,
        .ind-table td {
            border: 1px solid #555;
            padding: 7px 9px;
            vertical-align: top;
        }

        .ind-table thead th {
            background: #d8d8d8;
            text-align: center;
            font-weight: 700;
            font-size: 10.5pt;
            text-transform: uppercase;
            color: #000;
        }

        .ind-table th:nth-child(1) {
            width: 36px;
        }

        .ind-table th:nth-child(3) {
            width: 108px;
        }

        .ind-table th:nth-child(4) {
            width: 195px;
        }

        .ind-table tbody td:nth-child(1) {
            text-align: center;
            font-weight: 700;
            font-size: 11pt;
        }

        .ind-table tbody tr:nth-child(even) {
            background: #fafafa;
        }

        /* RADIO */
        .radio-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .radio-group label {
            display: flex;
            align-items: center;
            gap: 7px;
            cursor: pointer;
            font-size: 10.5pt;
            padding: 4px 8px;
            border: 1px solid #bbb;
            background: #fff;
            user-select: none;
        }

        .radio-group label:hover {
            background: #efefef;
        }

        .radio-group label.selected {
            background: #e0e0e0;
            border-color: #333;
            font-weight: 700;
        }

        .radio-group input[type="radio"] {
            accent-color: #000;
            margin: 0;
        }

        /* NOTE */
        .note-input {
            width: 100%;
            border: none;
            border-bottom: 1px solid #999;
            padding: 3px 0;
            font-family: inherit;
            font-size: 10.5pt;
            background: transparent;
            resize: none;
            min-height: 42px;
            line-height: 1.4;
            outline: none;
            display: block;
        }

        .note-input:focus {
            border-bottom-color: #000;
        }

        /* SUMMARY */
        .summary-row {
            display: flex;
            border: 1px solid #555;
            margin-bottom: 18px;
        }

        .summary-cell {
            flex: 1;
            padding: 9px 12px;
            text-align: center;
            border-right: 1px solid #555;
        }

        .summary-cell:last-child {
            border-right: none;
        }

        .summary-cell .sc-n {
            font-size: 20pt;
            font-weight: 700;
        }

        .summary-cell .sc-l {
            font-size: 9.5pt;
            color: #444;
        }

        hr {
            border: none;
            border-top: 1px solid #bbb;
            margin: 18px 0;
        }

        .section-title {
            font-size: 11pt;
            font-weight: 700;
            text-transform: uppercase;
            border-bottom: 2px solid #000;
            padding-bottom: 4px;
            margin-bottom: 12px;
        }

        /* KESIMPULAN */
        .kesp-row {
            display: flex;
            gap: 14px;
            margin-bottom: 12px;
        }

        .kesp-btn {
            flex: 1;
            padding: 9px 12px;
            border: 1px solid #666;
            background: #fff;
            font-family: inherit;
            font-size: 11pt;
            cursor: pointer;
            text-align: center;
            font-weight: 600;
        }

        .kesp-btn:hover {
            background: #efefef;
        }

        .kesp-btn.active {
            background: #e0e0e0;
            border: 2px solid #000;
        }

        .field-label {
            font-size: 10pt;
            font-weight: 700;
            text-transform: uppercase;
            margin-bottom: 4px;
            color: #333;
        }

        .textarea-full {
            width: 100%;
            border: 1px solid #888;
            padding: 7px 9px;
            font-family: inherit;
            font-size: 10.5pt;
            resize: vertical;
            min-height: 58px;
            margin-bottom: 12px;
            background: #fafafa;
            line-height: 1.5;
            outline: none;
        }

        .textarea-full:focus {
            border-color: #000;
            background: #fff;
        }

        /* GOOGLE DRIVE */
        .gdrive-wrap {
            border: 1px solid #888;
            padding: 10px 12px;
            background: #fafafa;
            margin-bottom: 16px;
        }

        .gdrive-label {
            font-size: 10pt;
            color: #444;
            margin-bottom: 6px;
        }

        .gdrive-input {
            width: 100%;
            border: none;
            border-bottom: 1px solid #777;
            padding: 5px 0;
            font-family: inherit;
            font-size: 10.5pt;
            background: transparent;
            outline: none;
        }

        .gdrive-input:focus {
            border-bottom-color: #000;
        }

        .gdrive-note {
            font-size: 9.5pt;
            color: #555;
            margin-top: 5px;
            font-style: italic;
        }

        /* TANDA TANGAN */
        .sign-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 28px;
            margin-top: 8px;
        }

        .sign-box {
            border: 1px solid #888;
            padding: 12px 14px;
        }

        .sign-box .sign-t {
            font-size: 10.5pt;
            font-weight: 700;
            text-align: center;
            margin-bottom: 6px;
            text-transform: uppercase;
        }

        .sign-area {
            height: 70px;
        }

        .sign-line {
            border-top: 1px solid #555;
            padding-top: 5px;
            font-size: 10pt;
            text-align: center;
            color: #444;
            font-style: italic;
        }

        /* SAVE INFO */
        .save-info {
            font-size: 9.5pt;
            color: #666;
            text-align: right;
            margin-top: 6px;
            font-style: italic;
        }

        /* BUTTONS */
        .btn-row {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 22px;
            padding-top: 14px;
            border-top: 2px solid #555;
        }

        .btn {
            padding: 9px 22px;
            border: 1px solid #555;
            background: #fff;
            font-family: inherit;
            font-size: 11pt;
            cursor: pointer;
            font-weight: 600;
        }

        .btn:hover {
            background: #e8e8e8;
        }

        .btn-dark {
            background: #333;
            color: #fff;
            border-color: #333;
        }

        .btn-dark:hover {
            background: #555;
        }

        .btn-save {
            background: #111;
            color: #fff;
            border-color: #111;
            font-size: 12pt;
            padding: 10px 28px;
        }

        .btn-save:hover {
            background: #333;
        }

        @media print {
            body {
                background: #fff;
            }

            .topbar,
            .btn-row,
            .progress-row,
            .toast {
                display: none !important;
            }

            .page {
                margin: 0;
                border: none;
                padding: 14px 18px;
                max-width: 100%;
            }

            .ind-table thead th {
                background: #ccc !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .ind-table tbody tr:nth-child(even) {
                background: #f5f5f5 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .radio-group label.selected {
                background: #ddd !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .petunjuk {
                background: #f0f0f0 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .kesp-btn.active {
                background: #ddd !important;
                border: 2px solid #000 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .gdrive-wrap {
                background: #f5f5f5 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>

<body>

    <!-- TOP BAR -->
    <div class="topbar">
        <div class="topbar-left">
            <button class="btn-back" onclick="goBack()">&#8592; Kembali</button>
            <span class="topbar-title">Monev MPLS 2026 &mdash; {{ strtoupper($jenjang) }}</span>
        </div>
        <button class="btn-close" onclick="goBack()" title="Tutup">&#10005;</button>
    </div>

    <!-- TOAST -->
    <div class="toast" id="toast"></div>

    <div class="page">
        @if ($jenjang == 'sd')
            <!-- HEADER -->
            <div class="doc-header">
                <h1>Instrumen Monitoring dan Evaluasi MPLS Ramah Tahun {{ $tahun }}</h1>
                <h2>Jenjang Sekolah Dasar (SD) &mdash; 20 Indikator</h2>
            </div>
        @else
            <!-- HEADER -->
            <div class="doc-header">
                <h1>Instrumen Monitoring dan Evaluasi MPLS Ramah Tahun {{ $tahun }}</h1>
                <h2>Jenjang Pendidikan Anak Usia Dini (PAUD) &mdash; 20 Indikator</h2>
            </div>
        @endif

        @php
            $nik_kelurahan = $bunda_kelurahan->nik ?? '';
            $nik_kecamatan = $bunda_kecamatan->nik ?? '';
            $ttd_kelurahan = '';
            $ttd_kecamatan = '';
            $stempel = $sekolah->stempel ?? '';
            $stempel = $stempel != '' ? 'https://dispendik.surabaya.go.id/profilsekolah_file/stempel/' . $stempel : '';

            $nama_bunda = '';
            $ttd_bunda_pakai = '';
            $ttd_bunda_kelurahan = collect($ttd_bunda)->where('nik', $nik_kelurahan)->first()->link_ttd ?? '';
            $ttd_bunda_kecamatan = collect($ttd_bunda)->where('nik', $nik_kecamatan)->first()->link_ttd ?? '';
            if (Auth::user()->tingkat == 'kecamatan') {
                $nama_bunda = $bunda_kecamatan->nama ?? '';
                if ($nik_kecamatan != '') {
                    $ttd_bunda_pakai = collect($ttd_bunda)->where('nik', $nik_kecamatan)->first()->link_ttd ?? '';
                }
            } elseif (Auth::user()->tingkat == 'kelurahan') {
                $nama_bunda = $bunda_kelurahan->nama ?? '';
                if ($nik_kelurahan != '') {
                    $ttd_bunda_pakai = collect($ttd_bunda)->where('nik', $nik_kelurahan)->first()->link_ttd ?? '';
                }
            }

            $nama_bunda_kelurahan = $bunda_kelurahan->nama ?? '';
            $nama_bunda_kecamatan = $bunda_kecamatan->nama ?? '';
        @endphp

        <!-- IDENTITAS -->
        <table class="identitas-table">
            <tr>
                <td>Nama
                    @if ($jenjang == 'sd')
                        Sekolah
                    @else
                        Satuan PAUD
                    @endif
                </td>
                <td>:</td>
                <td><input class="id-input" type="text" id="nama-satuan" value="{{ $sekolah->nama ?? '' }}"></td>
            </tr>
            <tr>
                <td>NPSN</td>
                <td>:</td>
                <td><input class="id-input" type="text" id="npsn" value="{{ $sekolah->npsn ?? '' }}"></td>
            </tr>
            <tr>
                <td>Alamat
                    @if ($jenjang == 'sd')
                        Sekolah
                    @else
                        Satuan PAUD
                    @endif
                </td>
                <td>:</td>
                <td><input class="id-input" type="text" id="alamat" value="{{ $sekolah->alamat ?? '' }}"></td>
            </tr>
            <tr>
                <td>Kelurahan / Kecamatan</td>
                <td>:</td>
                <td><input class="id-input" type="text" id="kelurahan"
                        value="{{ $sekolah->kelurahan_dispenduk ?? '' }} / {{ $sekolah->kecamatan_dispenduk }}"></td>
            </tr>
            <tr>
                <td>Nama Kepala Sekolah</td>
                <td>:</td>
                <td><input class="id-input" type="text" id="kepala" value="{{ $data_ks->nama ?? '' }}"></td>
            </tr>
            <tr>
                <td>Jumlah Murid Baru</td>
                <td>:</td>
                <td><input class="id-input" type="number" id="murid"></td>
            </tr>
            <tr>
                <td>Hari / Tanggal Monev</td>
                <td>:</td>
                <td><input class="id-input" type="date" id="tanggal" value="{{ date('Y-m-d') }}"></td>
            </tr>
            <tr>
                <td>Petugas Monitoring</td>
                <td>:</td>
                <td><input class="id-input" type="text" id="petugas" value="{{ $nama_bunda }}"></td>
            </tr>
        </table>
        @if ($jenjang == 'sd')
            <!-- PETUNJUK -->
            <div class="petunjuk">
                <strong>Petunjuk:</strong> Isi berdasarkan verifikasi dokumen, observasi, dan wawancara singkat.
                Beri tanda centang pada <strong>Ya</strong> atau <strong>Tidak</strong>.
                Jawaban <strong>Sebagian/Tidak</strong> wajib diberi catatan dan tindak lanjut.
            </div>
        @else
            <!-- PETUNJUK -->
            <div class="petunjuk">
                <strong>Petunjuk:</strong> Isi berdasarkan verifikasi dokumen, observasi, dan wawancara singkat.
                Beri tanda centang pada <strong>Ya</strong> atau <strong>Tidak</strong>.
                Jawaban <strong>Tidak</strong> wajib diberi catatan dan tindak lanjut.
            </div>
        @endif

        <!-- PROGRESS -->
        <div class="progress-row">
            <span id="progress-text">0 / 20 terisi</span>
            <div class="progress-bg">
                <div class="progress-fill" id="progress-fill" style="width:0%"></div>
            </div>
        </div>

        <!-- INDIKATOR TABLE -->
        <table class="ind-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Indikator</th>
                    <th>Hasil</th>
                    <th>Catatan / Bukti</th>
                </tr>
            </thead>
            <tbody id="tbody-ind"></tbody>
        </table>

        <!-- SUMMARY -->
        <div class="summary-row">
            <div class="summary-cell">
                <div class="sc-n" id="cnt-ya">0</div>
                <div class="sc-l">Ya</div>
            </div>
            <div class="summary-cell">
                <div class="sc-n" id="cnt-tidak">0</div>
                <div class="sc-l">Tidak</div>
            </div>
            <div class="summary-cell">
                <div class="sc-n" id="cnt-belum">20</div>
                <div class="sc-l">Belum Diisi</div>
            </div>
        </div>

        <hr>

        <!-- KESIMPULAN -->
        <div class="section-title">Kesimpulan dan Rekomendasi</div>

        <div class="field-label">Status Monev</div>
        <div class="kesp-row">
            <button class="kesp-btn" id="btn-sesuai" onclick="setKesp('sesuai')">Sesuai</button>
            <button class="kesp-btn" id="btn-perlu" onclick="setKesp('perlu')">Perlu Perbaikan</button>
        </div>

        <div class="field-label">Temuan Utama</div>
        <textarea class="textarea-full" id="temuan" placeholder="Tuliskan temuan utama hasil monev..."></textarea>

        <div class="field-label">Rekomendasi</div>
        <textarea class="textarea-full" id="rekomendasi" placeholder="Tuliskan rekomendasi tindak lanjut..."></textarea>

        <div class="field-label">Dokumentasi / Unggahan SIBUNDA</div>
        <div class="gdrive-wrap">
            <div class="gdrive-label">Link Google Drive dokumentasi (foto / video):</div>
            <input class="gdrive-input" type="url" id="gdrive-link" placeholder="https://drive.google.com/...">
            <div class="gdrive-note">Pastikan link telah diberikan akses untuk melihat (Anyone with the link - Viewer).
            </div>
        </div>

        <hr>
        <!-- TANDA TANGAN -->
        <div class="section-title">Tanda Tangan</div>
        <div class="sign-row">
            <div class="sign-box">
                <div class="sign-t">Bunda Kelurahan</div>
                <div class="sign-area" style="height: 3cm !important">
                    <center>
                        <img src="{{ $ttd_bunda_kelurahan }}" style="height: 3cm !important"
                            id="tempat_ttd_bunda_kelurahan"
                            onerror="this.onerror=null;this.src='https://png.pngtree.com/png-vector/20190917/ourmid/pngtree-not-found-circle-icon-vectors-png-image_1737851.jpg'"
                            alt="">
                    </center>
                    <input type="hidden" id="ttd_bunda" value="{{ $ttd_bunda_pakai }}">
                    <input type="hidden" id="ttd_ks" value="{{ $stempel }}">
                    <input type="hidden" id="tahun" value="{{ $tahun }}">
                    <input type="hidden" id="jenjang" value="{{ $jenjang }}">
                </div>
                <div class="sign-line" id="tempat_nama_bunda_kelurahan">
                    {{ $nama_bunda_kelurahan }}
                </div>
            </div>
            <div class="sign-box">
                <div class="sign-t">Kepala Satuan Pendidikan</div>
                <div class="sign-area" style="height: 3cm !important">
                    <center>
                        <img src="{{ $stempel }}" style="height: 3cm !important" id="tempat_ttd_ks"
                            onerror="this.onerror=null;this.src='https://png.pngtree.com/png-vector/20190917/ourmid/pngtree-not-found-circle-icon-vectors-png-image_1737851.jpg'"
                            alt="">
                    </center>
                </div>
                <div class="sign-line" id="tempat_nama_ks">
                    {{ $data_ks->nama ?? '' }}
                </div>
            </div>
        </div>

        <div style="display: grid;grid-template-columns:0.5fr 1fr 0.5fr;gap: 28px;margin-top: 10px">
            <div></div>
            <div class="sign-box">
                <div class="sign-t">Bunda Kecamatan</div>
                <div class="sign-area" style="height: 3cm !important">
                    <center>
                        <img src="{{ $ttd_bunda_kecamatan }}" style="height: 3cm !important"
                            id="tempat_ttd_bunda_kecamatan"
                            onerror="this.onerror=null;this.src='https://png.pngtree.com/png-vector/20190917/ourmid/pngtree-not-found-circle-icon-vectors-png-image_1737851.jpg'"
                            alt="">
                    </center>
                </div>
                <div class="sign-line" id="tempat_nama_bunda_kecamatan">
                    {{ $nama_bunda_kecamatan }}
                </div>
            </div>
            <div></div>
        </div>

        <!-- SAVE INFO -->
        <div class="save-info" id="save-info"></div>

        <!-- BUTTONS -->
        <div class="btn-row">
            {{-- <button class="btn" onclick="resetAll()">Reset</button> --}}
            <button class="btn" onclick="return window.close()">Tutup</button>
            <button class="btn btn-dark" onclick="window.print()">Cetak</button>
            <button class="btn btn-save" onclick="simpanData()">Simpan</button>
        </div>

    </div><!-- /page -->

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const STORAGE_KEY = 'monev_paud_2026';
        if ('{{ $cetak }}' !== '') {
            window.print();
        }

        @if ($jenjang == 'sd')
            const indikatorPAUD = [
                "Panitia dan program MPLS ditetapkan oleh kepala sekolah; program memuat jadwal lima hari, kegiatan, durasi, lokasi, metode, kebutuhan/anggaran, serta disampaikan kepada Dinas Pendidikan sesuai mekanisme.",
                "Sosialisasi kepada orang tua/wali dilaksanakan paling lambat lima hari sebelum pelaksanaan MPLS melalui media yang efektif dan memuat tujuan/asas, materi/jadwal/larangan, peran para pihak, kanal pengaduan, kesepakatan tata tertib, parenting, makan sehat, rangkaian identifikasi/asesmen, CKG, deteksi dini, dan evaluasi.",
                "MPLS dilaksanakan selama 5 hari pada minggu pertama awal tahun ajaran 2026/2027 (13–17 Juli 2026) di lingkungan sekolah; kegiatan di luar sekolah memiliki persetujuan, sedangkan seragam dan atribut tidak memberatkan.",
                "Murid disambut hangat dan pada hari pertama sangat disarankan diantar orang tua/wali sebagai dukungan transisi ke lingkungan sekolah.",
                "Rangkaian kegiatan mencakup pengenalan potensi diri, warga sekolah, lingkungan sekolah, kurikulum, serta unjuk minat dan bakat yang suportif, tidak memaksa, dan tidak kompetitif.",
                "Enam materi utama terlaksana: G7KAIH, Pagi Ceria, Sopan dan Santun Bermedia Sosial, budaya 5S, Gerakan Indonesia ASRI, dan Gerakan Rukun Sama Teman; materi pilihan sesuai ciri khas/kebutuhan sekolah.",
                "Kegiatan bersifat interaktif, berkesadaran, bermakna, menggembirakan, sesuai usia, memberi ruang refleksi, serta tidak didominasi ceramah atau pemaksaan.",
                "Pagi Ceria memuat Senam Anak Indonesia Hebat (SAIH), menyanyikan Indonesia Raya, dan berdoa bersama dengan menghormati kondisi serta keberagaman murid.",
                "Budaya Sekolah Aman dan Nyaman diterapkan melalui perlindungan fisik, psikologis, sosiokultural, dan digital serta penyediaan aksesibilitas/akomodasi bagi murid yang membutuhkan.",
                "Tidak terdapat perpeloncoan, kekerasan, perundungan, diskriminasi/intoleransi, pungutan, kegiatan atau atribut tidak relevan, pelibatan alumni, pelibatan murid SD sebagai panitia, maupun kegiatan tanpa pengawasan guru.",
                "Identifikasi massa tubuh dilakukan melalui pengukuran tinggi dan berat badan secara aman, privat, tanpa istilah yang menstigma, serta disertai saran konsultasi/rujukan bila diperlukan.",
                "Orang tua/wali mengisi Identifikasi Awal Kondisi Sosial-Emosional dan Konsentrasi Belajar; guru hanya membantu teknis, tidak mengarahkan jawaban, dan hasil tidak digunakan sebagai diagnosis/pelabelan.",
                "Orang tua/wali mengisi Identifikasi Bakat dan Minat sebagai gambaran awal potensi murid, bukan untuk membatasi pilihan atau menetapkan masa depan murid.",
                "Pada hari kedua, identifikasi massa tubuh dan tes fleksibilitas dilaksanakan dengan pemanasan, prosedur keselamatan, kriteria penghentian aktivitas, pencatatan/unggah hasil, perlindungan privasi, serta tanpa stigma.",
                "Deteksi dini dilaksanakan melalui biodata saat sosialisasi dan Survei Pengenalan Karakteristik dan Kebutuhan Perkembangan pasca-MPLS yang mencakup aspek personal, keluarga, belajar, kesehatan, dan kebutuhan dukungan.",
                "Sekolah menyosialisasikan pendaftaran CKG, berkoordinasi dengan puskesmas, mengonfirmasi pendaftaran, merekap jumlah murid sampai hari terakhir MPLS, dan melaporkannya dalam evaluasi kepala sekolah.",
                "Skrining murid berkebutuhan khusus hanya dilakukan bila terdapat indikasi dan akses ke ULD, fasilitas kesehatan, psikolog, Rumah ABK, Rumah Anak Prestasi atau SLB tidak tersedia/sulit; hasil bukan diagnosis, label, atau dasar penginputan Dapodik.",
                "Hasil identifikasi, asesmen kebugaran, deteksi dini, CKG, dan skrining digunakan untuk penyesuaian pembelajaran, pendampingan, komunikasi dengan orang tua, rujukan, dan pemantauan perkembangan murid.",
                "Kepala sekolah dan 30–45 orang tua/wali yang dipilih secara acak mengisi evaluasi daring Kemendikdasmen sejak hari kelima sampai 31 Juli 2026; sekolah juga mengevaluasi ketercapaian tujuan, keberhasilan, dan tantangan MPLS.",
                "Hasil evaluasi disampaikan kepada orang tua/wali dan laporan penyelenggaraan kepada Dinas Pendidikan paling lambat 30 hari kerja; laporan hasil asesmen dimanfaatkan untuk pembelajaran/program sekolah/pengasuhan dan seluruh data dijaga kerahasiaannya."
            ];
        @else
            const indikatorPAUD = [
                "Panitia dan program MPLS ditetapkan oleh kepala sekolah; program memuat jadwal lima hari, kegiatan, durasi, lokasi, metode, kebutuhan/anggaran, serta disampaikan kepada Dinas Pendidikan sesuai mekanisme.",
                "Sosialisasi kepada orang tua/wali dilaksanakan paling lambat lima hari sebelum pelaksanaan MPLS melalui media yang efektif dan memuat tujuan/asas, materi/jadwal/larangan, peran para pihak, kanal pengaduan, kesepakatan tata tertib, parenting, makan sehat, identifikasi awal, CKG, deteksi dini, dan evaluasi.",
                "MPLS dilaksanakan selama 5 hari pada minggu pertama awal tahun ajaran 2026/2027 (13–17 Juli 2026) di lingkungan satuan PAUD; kegiatan di luar satuan PAUD memiliki persetujuan, sedangkan seragam dan atribut tidak memberatkan.",
                "Murid disambut hangat; pada hari pertama orang tua/wali dapat mendampingi anak yang masih membutuhkan dukungan adaptasi, dengan tetap menumbuhkan kemandirian.",
                "Rangkaian kegiatan mencakup pengenalan potensi diri, warga satuan PAUD, lingkungan satuan PAUD, kurikulum, serta unjuk karya/ekspresi akhir yang sederhana dan tidak kompetitif.",
                "Enam materi utama terlaksana: G7KAIH, Pagi Ceria, Sopan dan Santun Bermedia Sosial, budaya 5S, Gerakan Indonesia ASRI, dan Gerakan Rukun Sama Teman; materi pilihan sesuai ciri khas/kebutuhan satuan PAUD.",
                "Kegiatan menggunakan pendekatan bermain, eksplorasi, gerak, lagu, interaksi, dan refleksi sederhana yang berkesadaran, bermakna, menggembirakan, sesuai usia, serta tanpa pemaksaan.",
                "Pagi Ceria memuat Senam Anak Indonesia Hebat (SAIH), menyanyikan Indonesia Raya, dan berdoa bersama dengan menghormati kondisi serta keberagaman murid.",
                "Budaya Satuan PAUD Aman dan Nyaman diterapkan melalui perlindungan fisik, psikologis, sosiokultural, dan digital serta penyediaan aksesibilitas/akomodasi bagi anak yang membutuhkan.",
                "Tidak terdapat perpeloncoan, kekerasan, perundungan, diskriminasi/intoleransi, pungutan, kegiatan atau atribut tidak relevan, pelibatan alumni, pelibatan murid sebagai panitia, maupun kegiatan tanpa pengawasan guru.",
                "Identifikasi massa tubuh dilakukan melalui pengukuran tinggi dan berat badan secara aman, privat, tanpa istilah yang menstigma, serta disertai saran konsultasi/rujukan bila diperlukan.",
                "Orang tua/wali mengisi Identifikasi Awal Kondisi Sosial-Emosional dan Konsentrasi Belajar; guru hanya membantu teknis, tidak mengarahkan jawaban, dan hasil tidak digunakan sebagai diagnosis/pelabelan.",
                "Deteksi dini dilaksanakan melalui biodata saat sosialisasi dan Survei Pengenalan Karakteristik dan Kebutuhan Perkembangan pasca-MPLS yang mencakup aspek personal, keluarga, belajar, kesehatan, dan kebutuhan dukungan.",
                "Satuan PAUD menyosialisasikan pendaftaran CKG, berkoordinasi dengan puskesmas, mengonfirmasi pendaftaran, merekap jumlah anak sampai hari terakhir MPLS, dan melaporkannya dalam evaluasi kepala sekolah.",
                "Skrining murid berkebutuhan khusus hanya dilakukan bila terdapat indikasi dan akses ke ULD, fasilitas kesehatan, psikolog, Rumah ABK, Rumah Anak Prestasi, PUSPAGA, atau SLB tidak tersedia/sulit; hasil bukan diagnosis, label, atau dasar penginputan Dapodik.",
                "Hasil identifikasi, deteksi dini, CKG, dan skrining digunakan untuk penyesuaian pembelajaran, pendampingan sosial-emosional/kesehatan, komunikasi dengan orang tua, rujukan, dan pemantauan perkembangan anak.",
                "Setelah hari kelima, satuan PAUD mengevaluasi ketercapaian tujuan, keberhasilan, dan tantangan pelaksanaan MPLS.",
                "Kepala sekolah dan 30–45 orang tua/wali yang dipilih secara acak mengisi evaluasi daring Kemendikdasmen sejak hari kelima sampai 31 Juli 2026.",
                "Hasil evaluasi disampaikan kepada orang tua/wali dan laporan penyelenggaraan disampaikan kepada Dinas Pendidikan paling lambat 30 hari kerja; laporan memuat rincian pelaksanaan dan hasil evaluasi.",
                "Laporan individual dan rekapitulasi hasil identifikasi dimanfaatkan oleh guru, kepala sekolah, dan orang tua untuk pembelajaran, program satuan PAUD, serta pengasuhan; seluruh data dan dokumentasi anak dijaga kerahasiaannya."
            ];
        @endif

        const state = {};
        let kesimpulan = '';

        /* BUILD TABLE */
        function buildTable() {
            const tbody = document.getElementById('tbody-ind');
            indikatorPAUD.forEach((text, i) => {
                const n = i + 1;
                const tr = document.createElement('tr');
                tr.innerHTML = `
      <td>${n}</td>
      <td style="line-height:1.55;">${text}</td>
      <td>
        <div class="radio-group">
          <label id="lbl-ya-${n}"><input type="radio" name="ind-${n}" value="ya" onchange="onPick(${n},'ya')"> Ya</label>
          <label id="lbl-tidak-${n}"><input type="radio" name="ind-${n}" value="tidak" onchange="onPick(${n},'tidak')"> Tidak</label>
        </div>
      </td>
      <td><textarea class="note-input" id="note-${n}" placeholder="..."></textarea></td>
    `;
                tbody.appendChild(tr);
            });
        }

        function onPick(n, val) {
            state[n] = val;
            ['ya', 'tidak'].forEach(v => document.getElementById('lbl-' + v + '-' + n).classList.remove('selected'));
            document.getElementById('lbl-' + val + '-' + n).classList.add('selected');
            updateSummary();
        }

        function updateSummary() {
            const vals = Object.values(state);
            document.getElementById('cnt-ya').textContent = vals.filter(v => v === 'ya').length;
            document.getElementById('cnt-tidak').textContent = vals.filter(v => v === 'tidak').length;
            document.getElementById('cnt-belum').textContent = 20 - Object.keys(state).length;
            const pct = Object.keys(state).length / 20 * 100;
            document.getElementById('progress-fill').style.width = pct + '%';
            document.getElementById('progress-text').textContent = Object.keys(state).length + ' / 20 terisi';
        }

        function setKesp(val) {
            kesimpulan = val;
            document.getElementById('btn-sesuai').classList.remove('active');
            document.getElementById('btn-perlu').classList.remove('active');
            document.getElementById('btn-' + val).classList.add('active');
        }

        /* ── SIMPAN ── */
        function simpanData() {
            const data = {
                identitas: {
                    namaSatuan: document.getElementById('nama-satuan').value,
                    npsn: document.getElementById('npsn').value,
                    alamat: document.getElementById('alamat').value,
                    kelurahan: document.getElementById('kelurahan').value,
                    kepala: document.getElementById('kepala').value,
                    murid: document.getElementById('murid').value,
                    tanggal: document.getElementById('tanggal').value,
                    petugas: document.getElementById('petugas').value
                },
                indikator: {},
                catatan: {},
                kesimpulan: kesimpulan,
                temuan: document.getElementById('temuan').value,
                rekomendasi: document.getElementById('rekomendasi').value,
                gdriveLink: document.getElementById('gdrive-link').value,
                disimpanPada: new Date().toLocaleString('id-ID'),
                tahun: document.getElementById('tahun').value,
                npsn: document.getElementById('npsn').value,
                jenjang: document.getElementById('jenjang').value,
                nama_bunda: document.getElementById('petugas').value,
                nama_ks: document.getElementById('kepala').value,
                ttd_ks: document.getElementById('ttd_ks').value,
                ttd_bunda: document.getElementById('ttd_bunda').value,
                tgl_monev: document.getElementById('tanggal').value,
                jumlah_murid_baru: document.getElementById('murid').value,
                monev_by: document.getElementById('petugas').value,
                nama_bunda_kelurahan: document.getElementById('tempat_nama_bunda_kelurahan').textContent,
                ttd_bunda_kelurahan: document.getElementById('tempat_ttd_bunda_kelurahan')?.src || '',
                nama_bunda_kecamatan: document.getElementById('tempat_nama_bunda_kecamatan').textContent,
                ttd_bunda_kecamatan: document.getElementById('tempat_ttd_bunda_kecamatan')?.src || ''
            };

            for (let n = 1; n <= 20; n++) {
                data.indikator[n] = state[n] || '';
                data.catatan[n] = document.getElementById('note-' + n).value;
            }

            // try {
            //     localStorage.setItem(STORAGE_KEY, JSON.stringify(data));
            //     showToast('Data berhasil disimpan — ' + data.disimpanPada);
            //     document.getElementById('save-info').textContent = 'Terakhir disimpan: ' + data.disimpanPada;
            // } catch (e) {
            //     showToast('Gagal menyimpan. Coba lagi.');
            // }

            try {
                fetch('{{ route('monev_baru-mpls', ['jenjang' => 'paud', 'halaman' => 'simpan_monev_26']) }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify(data)
                    })
                    .then(response => response.json())
                    .then(data => {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: 'Data berhasil disimpan'
                        });
                        console.log('Berhasil:', data);
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Mohon maaf',
                            text: 'Silahkan periksa kembali isian anda'
                        });
                        console.log('Error:', error);
                    });
            } catch (e) {
                console.log(e);
            }
        }

        /* ── MUAT DATA TERSIMPAN ── */
        function muatData() {
            // const raw = localStorage.getItem(STORAGE_KEY);
            const raw = @json($monev);
            if (!raw) return;
            try {
                const data = raw;
                const id = data.identitas || {};
                document.getElementById('nama-satuan').value = id.namaSatuan || '';
                document.getElementById('npsn').value = id.npsn || '';
                document.getElementById('alamat').value = id.alamat || '';
                document.getElementById('kelurahan').value = id.kelurahan || '';
                document.getElementById('kepala').value = id.kepala || '';
                document.getElementById('murid').value = id.murid || '';
                document.getElementById('tanggal').value = id.tanggal || '';
                document.getElementById('petugas').value = id.petugas || '';

                for (let n = 1; n <= 20; n++) {
                    const val = data.indikator[n];
                    if (val) {
                        state[n] = val;
                        document.querySelector('input[name="ind-' + n + '"][value="' + val + '"]').checked = true;
                        document.getElementById('lbl-' + val + '-' + n).classList.add('selected');
                    }
                    document.getElementById('note-' + n).value = data.catatan[n] || '';
                }

                if (data.kesimpulan) setKesp(data.kesimpulan);
                document.getElementById('temuan').value = data.temuan || '';
                document.getElementById('rekomendasi').value = data.rekomendasi || '';
                document.getElementById('gdrive-link').value = data.gdriveLink || '';
                document.getElementById('ttd_ks').value = data.ttd_ks || '';
                document.getElementById('ttd_bunda').value = data.ttd_bunda || '';
                document.getElementById('nama_bunda').value = data.nama_bunda || '';
                document.getElementById('nama_ks').value = data.nama_ks || '';
                document.getElementById('tahun').value = data.tahun || '';
                document.getElementById('jenjang').value = data.jenjang || '';
                document.getElementById('tempat_ttd_ks').src = data.ttd_ks || '';
                document.getElementById('tempat_ttd_bunda_kelurahan').src = data.ttd_bunda_kelurahan || '';
                document.getElementById('tempat_nama_bunda_kelurahan').textContent = data.nama_bunda_kelurahan || '';
                document.getElementById('tempat_ttd_bunda_kecamatan').src = data.ttd_bunda_kecamatan || '';
                document.getElementById('tempat_nama_bunda_kecamatan').textContent = data.nama_bunda_kecamatan || '';
                document.getElementById('tempat_nama_ks').text = data.nama_ks || '';

                console.log(data.nama_bunda || '');

                updateSummary();
                if (data.disimpanPada) {
                    document.getElementById('save-info').textContent = 'Terakhir disimpan: ' + data.disimpanPada;
                }
            } catch (e) {}
        }

        /* TOAST */
        function showToast(msg) {
            const el = document.getElementById('toast');
            el.textContent = msg;
            el.classList.add('show');
            setTimeout(() => el.classList.remove('show'), 3000);
        }

        /* RESET */
        function resetAll() {
            if (!confirm('Reset semua data? Data yang tersimpan di browser juga akan dihapus.')) return;
            for (let k in state) delete state[k];
            kesimpulan = '';
            for (let n = 1; n <= 20; n++) {
                document.querySelectorAll('input[name="ind-' + n + '"]').forEach(r => r.checked = false);
                ['ya', 'tidak'].forEach(v => document.getElementById('lbl-' + v + '-' + n).classList.remove('selected'));
                document.getElementById('note-' + n).value = '';
            }
            ['nama-satuan', 'npsn', 'alamat', 'kelurahan', 'kepala', 'murid', 'tanggal', 'petugas', 'temuan', 'rekomendasi',
                'gdrive-link'
            ]
            .forEach(id => document.getElementById(id).value = '');
            ['btn-sesuai', 'btn-perlu'].forEach(id => document.getElementById(id).classList.remove('active'));
            // localStorage.removeItem(STORAGE_KEY);
            document.getElementById('save-info').textContent = '';
            updateSummary();
        }

        /* BACK */
        function goBack() {
            if (window.history.length > 1) {
                window.history.back();
            } else {
                window.close();
            }
        }

        /* INIT */
        buildTable();
        muatData();
    </script>
</body>

</html>
