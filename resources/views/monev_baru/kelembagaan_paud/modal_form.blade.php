<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instrumen Pembinaan Kelembagaan PAUD 2026</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box
        }

        :root {
            --ink: #1a1a2e;
            --ink2: #3a3a5c;
            --ink3: #6b6b8d;
            --bg: #f8f7f4;
            --card: #ffffff;
            --border: #e8e6e1;
            --accent: #1e40af;
            --accent2: #3b82f6;
            --accent-soft: #eff6ff;
            --green: #059669;
            --green-soft: #ecfdf5;
            --red: #dc2626;
            --red-soft: #fef2f2;
            --amber: #d97706;
            --amber-soft: #fffbeb;
            --purple: #7c3aed;
            --purple-soft: #f5f3ff;
            --teal: #0d9488;
            --teal-soft: #f0fdfa;
            --radius: 12px;
            --shadow: 0 1px 3px rgba(0, 0, 0, .06), 0 4px 12px rgba(0, 0, 0, .04);
            --shadow-lg: 0 4px 24px rgba(0, 0, 0, .08);
        }

        html {
            font-size: 14px;
            scroll-behavior: smooth
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--bg);
            color: var(--ink);
            line-height: 1.6;
            min-height: 100vh
        }

        .mono {
            font-family: 'JetBrains Mono', monospace;
            font-size: .85em
        }

        /* HEADER */
        .header {
            background: linear-gradient(135deg, #1e3a5f 0%, #1e40af 50%, #3b82f6 100%);
            color: #fff;
            padding: 2rem 1.5rem;
            position: relative;
            overflow: hidden;
        }

        .header::before {
            content: '';
            position: absolute;
            top: -60%;
            right: -10%;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(255, 255, 255, .08) 0%, transparent 70%);
            border-radius: 50%;
        }

        .header::after {
            content: '';
            position: absolute;
            bottom: -40%;
            left: -5%;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(255, 255, 255, .05) 0%, transparent 70%);
            border-radius: 50%;
        }

        .header-inner {
            max-width: 1100px;
            margin: 0 auto;
            position: relative;
            z-index: 1
        }

        .header h1 {
            font-size: 1.6rem;
            font-weight: 800;
            letter-spacing: -.02em;
            margin-bottom: .25rem
        }

        .header p {
            opacity: .85;
            font-size: .9rem;
            font-weight: 300
        }

        .badge-row {
            display: flex;
            gap: .5rem;
            margin-top: .75rem;
            flex-wrap: wrap
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            padding: .3rem .7rem;
            border-radius: 20px;
            font-size: .75rem;
            font-weight: 600;
            background: rgba(255, 255, 255, .15);
            backdrop-filter: blur(4px);
        }

        .badge svg {
            width: 14px;
            height: 14px
        }

        /* PERIODE BAR */
        .periode-bar {
            background: var(--amber-soft);
            border: 1px solid #fcd34d;
            padding: .75rem 1.5rem;
            display: flex;
            align-items: center;
            gap: .75rem;
            max-width: 1100px;
            margin: 1rem auto;
            border-radius: var(--radius);
            font-size: .85rem;
            font-weight: 600;
            color: #92400e;
        }

        .periode-bar svg {
            width: 20px;
            height: 20px;
            flex-shrink: 0;
            color: #d97706
        }

        /* NAV */
        .nav {
            position: sticky;
            top: 0;
            z-index: 100;
            background: rgba(248, 247, 244, .92);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border);
        }

        .nav-inner {
            max-width: 1100px;
            margin: 0 auto;
            display: flex;
            gap: .25rem;
            padding: .5rem 1rem;
            overflow-x: auto;
            scrollbar-width: none;
        }

        .nav-inner::-webkit-scrollbar {
            display: none
        }

        .nav-btn {
            padding: .45rem .85rem;
            border: none;
            background: transparent;
            font-family: inherit;
            font-size: .78rem;
            font-weight: 600;
            color: var(--ink3);
            border-radius: 8px;
            cursor: pointer;
            white-space: nowrap;
            transition: .2s;
        }

        .nav-btn:hover {
            background: var(--accent-soft);
            color: var(--accent)
        }

        .nav-btn.active {
            background: var(--accent);
            color: #fff
        }

        /* MAIN */
        .main {
            max-width: 1100px;
            margin: 0 auto;
            padding: 1rem 1rem 4rem
        }

        /* IDENTITY SECTION */
        .identity-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: var(--shadow);
        }

        .identity-card h2 {
            font-size: 1.1rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: var(--accent);
            display: flex;
            align-items: center;
            gap: .5rem
        }

        .id-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: .75rem
        }

        .id-field {
            display: flex;
            flex-direction: column;
            gap: .25rem
        }

        .id-field label {
            font-size: .75rem;
            font-weight: 600;
            color: var(--ink3);
            text-transform: uppercase;
            letter-spacing: .04em
        }

        .id-field input,
        .id-field select {
            padding: .55rem .75rem;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-family: inherit;
            font-size: .88rem;
            background: var(--bg);
            transition: .2s;
        }

        .id-field input:focus,
        .id-field select:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(30, 64, 175, .1)
        }

        /* SECTION */
        .section {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            margin-bottom: 1.25rem;
            box-shadow: var(--shadow);
            overflow: hidden;
        }

        .section-header {
            padding: 1rem 1.25rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: .75rem;
            user-select: none;
            transition: .15s;
        }

        .section-header:hover {
            background: var(--accent-soft)
        }

        .section-num {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: .85rem;
            color: #fff;
            flex-shrink: 0;
        }

        .section-header h3 {
            flex: 1;
            font-size: .95rem;
            font-weight: 700;
            color: var(--ink)
        }

        .section-header .chevron {
            width: 20px;
            height: 20px;
            color: var(--ink3);
            transition: .3s;
            flex-shrink: 0;
        }

        .section.open .chevron {
            transform: rotate(180deg)
        }

        .section-body {
            display: none;
            border-top: 1px solid var(--border)
        }

        .section.open .section-body {
            display: block
        }

        /* KOMPONEN TABLE */
        .komponen-wrap {
            padding: 1rem 1.25rem
        }

        .komponen-table {
            width: 100%;
            border-collapse: collapse
        }

        .komponen-table th {
            background: var(--bg);
            font-size: .72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .04em;
            color: var(--ink3);
            padding: .6rem .5rem;
            text-align: left;
            border-bottom: 2px solid var(--border);
            position: sticky;
            top: 0;
        }

        .komponen-table td {
            padding: .6rem .5rem;
            border-bottom: 1px solid var(--border);
            vertical-align: top;
            font-size: .85rem;
        }

        .komponen-table tr:last-child td {
            border-bottom: none
        }

        /* STATUS SELECT */
        .status-select {
            padding: .4rem .6rem;
            border: 1px solid var(--border);
            border-radius: 6px;
            font-family: inherit;
            font-size: .82rem;
            font-weight: 600;
            background: var(--bg);
            cursor: pointer;
            min-width: 130px;
        }

        .status-select.val-sesuai {
            border-color: var(--green);
            color: var(--green);
            background: var(--green-soft)
        }

        .status-select.val-tidak {
            border-color: var(--red);
            color: var(--red);
            background: var(--red-soft)
        }

        .status-select.val-ada {
            border-color: var(--green);
            color: var(--green);
            background: var(--green-soft)
        }

        .status-select.val-tidakada {
            border-color: var(--red);
            color: var(--red);
            background: var(--red-soft)
        }

        .status-select.val-berlaku {
            border-color: var(--green);
            color: var(--green);
            background: var(--green-soft)
        }

        .status-select.val-tidakberlaku {
            border-color: var(--red);
            color: var(--red);
            background: var(--red-soft)
        }

        .status-select.val-ya {
            border-color: var(--green);
            color: var(--green);
            background: var(--green-soft)
        }

        .status-select.val-sudah {
            border-color: var(--green);
            color: var(--green);
            background: var(--green-soft)
        }

        .status-select.val-belum {
            border-color: var(--amber);
            color: var(--amber);
            background: var(--amber-soft)
        }

        .status-select.val-pernah {
            border-color: var(--green);
            color: var(--green);
            background: var(--green-soft)
        }

        /* INPUT FIELDS IN TABLE */
        .tbl-input {
            width: 100%;
            padding: .35rem .5rem;
            border: 1px solid var(--border);
            border-radius: 6px;
            font-family: inherit;
            font-size: .82rem;
            background: #fff;
        }

        .tbl-input:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 2px rgba(30, 64, 175, .08)
        }

        .tbl-input.narrow {
            width: 80px;
            text-align: center
        }

        .tbl-textarea {
            width: 100%;
            min-height: 50px;
            padding: .4rem .5rem;
            border: 1px solid var(--border);
            border-radius: 6px;
            font-family: inherit;
            font-size: .82rem;
            resize: vertical;
            background: #fff;
        }

        .tbl-textarea:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 2px rgba(30, 64, 175, .08)
        }

        /* SISTEMIK TAG */
        .tag-sistemik {
            display: inline-flex;
            align-items: center;
            gap: .25rem;
            padding: .15rem .5rem;
            border-radius: 4px;
            font-size: .68rem;
            font-weight: 700;
            background: var(--purple-soft);
            color: var(--purple);
            border: 1px solid #c4b5fd;
            letter-spacing: .02em;
        }

        .tag-api {
            display: inline-flex;
            align-items: center;
            gap: .25rem;
            padding: .15rem .5rem;
            border-radius: 4px;
            font-size: .68rem;
            font-weight: 700;
            background: var(--teal-soft);
            color: var(--teal);
            border: 1px solid #99f6e4;
        }

        /* JUKNIS TOOLTIP */
        .juknis-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 22px;
            height: 22px;
            border-radius: 50%;
            border: 1.5px solid var(--accent2);
            background: var(--accent-soft);
            color: var(--accent2);
            font-size: .7rem;
            font-weight: 800;
            cursor: pointer;
            flex-shrink: 0;
            position: relative;
        }

        .juknis-btn:hover .juknis-tip {
            display: block
        }

        .juknis-tip {
            display: none;
            position: absolute;
            bottom: calc(100% + 8px);
            left: 50%;
            transform: translateX(-50%);
            width: 320px;
            padding: .75rem;
            background: var(--ink);
            color: #fff;
            border-radius: 8px;
            font-size: .78rem;
            font-weight: 400;
            line-height: 1.5;
            z-index: 50;
            box-shadow: 0 8px 32px rgba(0, 0, 0, .25);
        }

        .juknis-tip::after {
            content: '';
            position: absolute;
            top: 100%;
            left: 50%;
            transform: translateX(-50%);
            border: 6px solid transparent;
            border-top-color: var(--ink);
        }

        .juknis-tip strong {
            color: #93c5fd
        }

        /* RISK BOX */
        .risk-box {
            margin: .75rem 0;
            padding: .75rem 1rem;
            border-radius: 8px;
            background: var(--red-soft);
            border-left: 3px solid var(--red);
            font-size: .82rem;
        }

        .risk-box h4 {
            font-size: .78rem;
            font-weight: 700;
            color: var(--red);
            margin-bottom: .25rem
        }

        /* KESIMPULAN */
        .conclusion-card {
            background: var(--card);
            border: 2px solid var(--accent);
            border-radius: var(--radius);
            padding: 1.5rem;
            margin-top: 1.5rem;
            box-shadow: var(--shadow-lg);
        }

        .conclusion-card h2 {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--accent);
            margin-bottom: 1rem
        }

        .conclusion-options {
            display: flex;
            flex-direction: column;
            gap: .75rem;
            margin-bottom: 1.5rem
        }

        .conclusion-opt {
            display: flex;
            align-items: flex-start;
            gap: .75rem;
            padding: .75rem 1rem;
            border: 1px solid var(--border);
            border-radius: 8px;
            cursor: pointer;
            transition: .2s;
        }

        .conclusion-opt:hover {
            border-color: var(--accent);
            background: var(--accent-soft)
        }

        .conclusion-opt input[type=radio] {
            margin-top: .2rem;
            accent-color: var(--accent)
        }

        .conclusion-opt .opt-label {
            font-weight: 700;
            font-size: .88rem
        }

        .conclusion-opt .opt-desc {
            font-size: .8rem;
            color: var(--ink3);
            margin-top: .15rem
        }

        /* SIGNATURES */
        .sig-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 1rem;
            margin-top: 1.5rem
        }

        .sig-box {
            text-align: center;
            padding: 1rem;
            border: 1px dashed var(--border);
            border-radius: 8px
        }

        .sig-box .sig-title {
            font-size: .75rem;
            font-weight: 600;
            color: var(--ink3);
            margin-bottom: 2.5rem
        }

        .sig-box .sig-line {
            border-top: 1px solid var(--ink);
            padding-top: .25rem;
            font-size: .8rem;
            font-weight: 600
        }

        /* PRINT */
        .print-bar {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            padding: .75rem 1.5rem;
            background: rgba(255, 255, 255, .95);
            backdrop-filter: blur(8px);
            border-top: 1px solid var(--border);
            display: flex;
            justify-content: center;
            gap: .75rem;
            z-index: 100;
        }

        .btn {
            padding: .6rem 1.5rem;
            border: none;
            border-radius: 8px;
            font-family: inherit;
            font-size: .85rem;
            font-weight: 700;
            cursor: pointer;
            transition: .2s;
            display: inline-flex;
            align-items: center;
            gap: .4rem;
        }

        .btn-primary {
            background: var(--accent);
            color: #fff
        }

        .btn-primary:hover {
            background: #1e3a8a
        }

        .btn-secondary {
            background: var(--bg);
            color: var(--ink);
            border: 1px solid var(--border)
        }

        .btn-secondary:hover {
            background: var(--border)
        }

        /* PPT HIGHLIGHT */
        .ppt-highlight {
            background: linear-gradient(135deg, #fef3c7, #fde68a);
            border: 1px solid #f59e0b;
            border-radius: 8px;
            padding: .6rem 1rem;
            margin: .5rem 0;
            font-size: .82rem;
            display: flex;
            align-items: flex-start;
            gap: .5rem;
        }

        .ppt-highlight svg {
            width: 18px;
            height: 18px;
            flex-shrink: 0;
            color: #d97706;
            margin-top: .1rem
        }

        /* RESPONSIVE */
        @media(max-width:768px) {
            .header h1 {
                font-size: 1.2rem
            }

            .id-grid {
                grid-template-columns: 1fr
            }

            .komponen-wrap {
                padding: .75rem;
                overflow-x: auto
            }

            .komponen-table {
                min-width: 700px
            }

            .sig-grid {
                grid-template-columns: 1fr 1fr
            }

            .juknis-tip {
                width: 260px;
                left: auto;
                right: -10px;
                transform: none
            }

            .juknis-tip::after {
                left: auto;
                right: 16px
            }
        }

        @media print {

            .nav,
            .print-bar {
                display: none !important
            }

            .section-body {
                display: block !important
            }

            .header {
                padding: 1rem;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact
            }

            body {
                font-size: 11px
            }

            .section {
                break-inside: avoid
            }
        }
    </style>
</head>

<body>

    <!-- HEADER -->
    <div class="header">
        <div class="header-inner">
            <h1>INSTRUMEN PEMBINAAN KELEMBAGAAN SATUAN PAUD</h1>
            <p>Tahun 2026 — Kota Surabaya • Bunda PAUD Kecamatan &amp; Kelurahan</p>
            <div class="badge-row">
                <span class="badge">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="4" width="18" height="18" rx="2" />
                        <path d="M16 2v4M8 2v4M3 10h18" />
                    </svg>
                    Periode I: Apr–Mei 2026
                </span>
                <span class="badge">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="4" width="18" height="18" rx="2" />
                        <path d="M16 2v4M8 2v4M3 10h18" />
                    </svg>
                    Periode II: Okt–Nov 2026
                </span>
                <span class="badge">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                    </svg>
                    Kunjungan Tanpa Pemberitahuan
                </span>
            </div>
        </div>
    </div>

    <input type="hidden" class="tahun_monev" value="{{ $tahun }}">
    <input type="hidden" class="npsn_monev" value="{{ $npsn }}">
    <input type="hidden" class="periode_monev" value="{{ $periode }}">

    <!-- PERIODE BAR -->
    <div class="periode-bar">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="10" />
            <path d="M12 6v6l4 2" />
        </svg>
        <span>PEMBINAAN BERKALA: 2× setahun (Semester I &amp; II). Instrumen ini untuk Periode
            <select id="periodeSelect"
                style="font-weight:700;border:1px solid #d97706;border-radius:4px;padding:2px 6px;background:#fff;font-family:inherit;font-size:inherit;color:#92400e">
                <option @if ($periode == '1') selected @endif value="1">I (Apr–Mei 2026)</option>
                <option @if ($periode == '2') selected @endif value="2">II (Okt–Nov 2026)</option>
            </select>
        </span>
    </div>

    <!-- NAV -->
    <div class="nav">
        <div class="nav-inner">
            <button class="nav-btn active" onclick="scrollToSection('identitas')">Identitas</button>
            <button class="nav-btn" onclick="scrollToSection('sec1')">1. Izin Ops</button>
            <button class="nav-btn" onclick="scrollToSection('sec2')">2. Alamat</button>
            <button class="nav-btn" onclick="scrollToSection('sec3')">3. Akreditasi</button>
            <button class="nav-btn" onclick="scrollToSection('sec4')">4. Data Murid</button>
            <button class="nav-btn" onclick="scrollToSection('sec5')">5. Data PTK</button>
            <button class="nav-btn" onclick="scrollToSection('sec6')">6. Jaspel</button>
            <button class="nav-btn" onclick="scrollToSection('sec7')">7. BOSP</button>
            <button class="nav-btn" onclick="scrollToSection('sec8')">8. Jejak Digital</button>
            <button class="nav-btn" onclick="scrollToSection('sec9')">9. Khusus PPT</button>
            <button class="nav-btn" onclick="scrollToSection('kesimpulan')">Kesimpulan</button>
        </div>
    </div>

    <!-- MAIN -->
    <div class="main">

        {{-- IDENTITAS --}}
        @include('monev_baru.kelembagaan_paud.formnya.identitas')

        {{-- IJOP --}}
        @include('monev_baru.kelembagaan_paud.formnya.ijop')

        {{-- Alamat --}}
        @include('monev_baru.kelembagaan_paud.formnya.alamat')

        {{-- Akreditasi --}}
        @include('monev_baru.kelembagaan_paud.formnya.akreditasi')

        {{-- Data Murid --}}
        @include('monev_baru.kelembagaan_paud.formnya.data_murid')

        {{-- Data Ptk --}}
        @include('monev_baru.kelembagaan_paud.formnya.data_ptk')

        {{-- Jaspel --}}
        @include('monev_baru.kelembagaan_paud.formnya.jaspel')

        {{-- BOSP --}}
        @include('monev_baru.kelembagaan_paud.formnya.bosp')

        {{-- Jejak Digital --}}
        @include('monev_baru.kelembagaan_paud.formnya.digital')

        {{-- Beasiswa PAUD --}}
        @include('monev_baru.kelembagaan_paud.formnya.beasiswa_paud')

        {{-- PPT --}}
        @include('monev_baru.kelembagaan_paud.formnya.ppt')

        {{-- Kesimpulan --}}
        @include('monev_baru.kelembagaan_paud.formnya.kesimpulan')




    </div><!-- end .main -->
    <div class="modal-page"></div>

    <!-- PRINT BAR -->
    <div class="print-bar">
        <button class="btn btn-secondary" onclick="expandAll()">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                stroke-width="2">
                <path d="M7 10l5 5 5-5" />
            </svg>
            Buka Semua
        </button>
        <button class="btn btn-secondary" onclick="collapseAll()">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                stroke-width="2">
                <path d="M17 14l-5-5-5 5" />
            </svg>
            Tutup Semua
        </button>
        @if (!empty($monev))
            @php
                $url = route('monev_baru-kelembagaan_paud', [
                    'halaman' => 'cetak',
                    'npsn' => $monev->npsn,
                    'tahun' => $monev->tahun,
                    'periode' => $monev->periode,
                ]);
            @endphp
            <button class="btn btn-primary" onclick="window.open('{{ $url }}')">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2">
                    <path d="M6 9V2h12v7" />
                    <path d="M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2" />
                    <rect x="6" y="14" width="12" height="8" />
                </svg>
                Cetak Instrumen
            </button>
        @endif
    </div>

    <script>
        function toggleSection(header) {
            header.parentElement.classList.toggle('open');
        }

        function expandAll() {
            document.querySelectorAll('.section').forEach(s => s.classList.add('open'));
        }

        function collapseAll() {
            document.querySelectorAll('.section').forEach(s => s.classList.remove('open'));
        }

        function scrollToSection(id) {
            document.getElementById(id).scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
            document.querySelectorAll('.nav-btn').forEach(b => b.classList.remove('active'));
            event.target.classList.add('active');
            // open section
            const sec = document.getElementById(id);
            if (sec.classList.contains('section')) sec.classList.add('open');
        }

        function updateStatusClass(sel) {
            sel.className = 'status-select';
            const v = sel.value;
            if (v) sel.classList.add('val-' + v);
        }

        // Intersection observer for nav
        const sections = document.querySelectorAll('.section, .identity-card, .conclusion-card');
        const navBtns = document.querySelectorAll('.nav-btn');
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const id = entry.target.id;
                    navBtns.forEach(btn => {
                        btn.classList.remove('active');
                        if (btn.getAttribute('onclick')?.includes(id)) btn.classList.add('active');
                    });
                }
            });
        }, {
            threshold: 0.3
        });
        sections.forEach(s => {
            if (s.id) observer.observe(s);
        });

        // Auto-show/hide PPT section
        document.getElementById('jenisSatuan').addEventListener('change', function() {
            const pptSection = document.getElementById('sec9');
            if (this.value === 'PPT') {
                pptSection.style.display = '';
                pptSection.classList.add('open');
            } else if (this.value) {
                pptSection.style.display = 'none';
            } else {
                pptSection.style.display = '';
            }
        });
    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        function simpan(nama_kelas) {
            event.preventDefault();
            var data = new FormData($('.' + nama_kelas)[0]);
            var tahun_monev = $('.tahun_monev').val();
            var periode_monev = $('.periode_monev').val();
            var npsn_monev = $('.npsn_monev').val();
            data.append('tahun_monev', tahun_monev);
            data.append('npsn_monev', npsn_monev);
            data.append('periode_monev', periode_monev);

            $.ajax({
                url: '{{ route('monev_baru-kelembagaan_paud', ['halaman' => 'simpan']) }}',
                data: data,
                type: 'post',
                contentType: false,
                processData: false,
                success: function(data) {
                    alert('200 - Berhasil disimpan');
                },
                error: function() {
                    alert('500 - Internal server error');
                }
            });
        }

        function detail(kategori, npsn) {
            $.post('{{ route('monev_baru-kelembagaan_paud', ['halaman' => 'detail']) }}', {
                kategori: kategori,
                npsn: npsn
            }, function(data) {
                $('.modal-page').html(data.content);
            })
        }
    </script>
</body>

</html>
