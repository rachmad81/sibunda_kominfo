<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instrumen Verifikasi Faktual Anak Belum Sekolah</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Font: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f1f5f9;
            color: #1e293b;
        }

        /* Print styles optimization */
        @media print {
            .no-print {
                display: none !important;
            }

            body {
                background-color: #ffffff !important;
                color: #000000 !important;
            }

            .page-container {
                box-shadow: none !important;
                padding: 0 !important;
                margin: 0 !important;
                width: 100% !important;
                max-width: 100% !important;
            }

            input,
            textarea,
            select {
                border-color: #cbd5e1 !important;
                background-color: transparent !important;
            }
        }

        /* Custom dotted border utility */
        .border-dotted-bottom {
            border-bottom: 1.5px dotted #94a3b8;
        }
    </style>
</head>

<body class="py-8 px-4 sm:px-6 md:px-8 min-h-screen">

    <!-- Action Bar / Toolbar (Hidden during print) -->
    <div
        class="max-w-4xl mx-auto mb-6 no-print flex flex-wrap items-center justify-between gap-4 bg-white p-4 rounded-xl shadow-sm border border-slate-200">
        <div class="flex items-center gap-2">
            <span class="inline-block w-3 h-3 bg-emerald-500 rounded-full"></span>
            <span class="text-sm font-semibold text-slate-700">Template Form Verifikasi ABS</span>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="resetForm()"
                class="px-4 py-2 text-xs md:text-sm font-medium text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors flex items-center gap-2">
                <i class="fa-solid fa-rotate-left"></i> Reset Form
            </button>
            <button onclick="window.print()"
                class="px-4 py-2 text-xs md:text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg shadow-sm transition-colors flex items-center gap-2">
                <i class="fa-solid fa-print"></i> Cetak / Simpan PDF
            </button>
        </div>
    </div>

    <!-- Main Container Document -->
    <main class="page-container max-w-4xl mx-auto bg-white p-6 md:p-10 rounded-2xl shadow-md border border-slate-200">

        <!-- Header Document -->
        <header class="text-center mb-6">
            <p class="text-xs sm:text-sm font-bold tracking-widest text-slate-700 uppercase mb-1">
                Pemerintah Kota Surabaya
            </p>
            <h1 class="text-base sm:text-lg md:text-xl font-bold text-slate-900 uppercase tracking-wide">
                Instrumen Verifikasi Faktual Anak Belum Sekolah Usia 5–6 Tahun
            </h1>
            <div class="mt-4 border-b-2 border-slate-900 w-full"></div>
        </header>

        <!-- Alert / Information Box -->
        <section class="mb-8 bg-[#e8f7f0] border border-[#a8ebd0] rounded-xl p-4 sm:p-5">
            <p class="text-xs sm:text-sm text-[#2d5a44] leading-relaxed text-justify sm:text-left">
                Instrumen ini digunakan dalam pelaksanaan pendataan dan verifikasi lapangan Anak Belum Sekolah (ABS)
                usia 5–6 tahun.
                Pengisian dilakukan berdasarkan hasil kunjungan lapangan, wawancara dengan keluarga, serta informasi
                faktual.
            </p>
        </section>

        <!-- Form Container -->
        <form id="verifikasiForm" onsubmit="event.preventDefault();">

            <!-- SECTION 1: IDENTITAS PETUGAS -->
            <section class="mb-8 border border-slate-300 rounded-2xl p-5 sm:p-6 bg-white relative">
                <!-- Section Header -->
                <div class="flex items-center gap-3 mb-4 pb-3 border-b border-slate-200">
                    <span
                        class="w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-[#e6f7f0] text-emerald-600 font-bold text-sm sm:text-base flex items-center justify-center">
                        1
                    </span>
                    <h2 class="text-base sm:text-lg font-bold text-slate-800">
                        Identitas Petugas
                    </h2>
                </div>

                <!-- Grid Inputs Section 1 -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-y-5 gap-x-8">

                    <!-- Tanggal Kunjungan -->
                    <div class="flex flex-col">
                        <label
                            class="text-[11px] sm:text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">
                            Tanggal Kunjungan
                        </label>
                        <div class="relative border-dotted-bottom flex items-center py-1">
                            <input type="text" value="03/08/2026"
                                class="w-full text-sm font-semibold text-slate-800 focus:outline-none bg-transparent uppercase"
                                placeholder="DD/MM/YYYY">
                            <i
                                class="fa-regular fa-calendar text-slate-700 text-sm absolute right-1 pointer-events-none"></i>
                        </div>
                    </div>

                    <!-- Nama Bunda PAUD -->
                    <div class="flex flex-col">
                        <label
                            class="text-[11px] sm:text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">
                            Nama Bunda PAUD
                        </label>
                        <div class="border-dotted-bottom py-1">
                            <input type="text" value="FARITA MAYKURNIAWATI, S.P"
                                class="w-full text-sm font-semibold text-slate-800 focus:outline-none bg-transparent uppercase">
                        </div>
                    </div>

                    <!-- Unsur / Jabatan -->
                    <div class="flex flex-col">
                        <label
                            class="text-[11px] sm:text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">
                            Unsur/Jabatan
                        </label>
                        <div class="border-dotted-bottom py-1">
                            <input type="text" value="Bunda PAUD - Penanggungjawab"
                                class="w-full text-sm font-semibold text-slate-800 focus:outline-none bg-transparent">
                        </div>
                    </div>

                    <!-- Nomor HP -->
                    <div class="flex flex-col">
                        <label
                            class="text-[11px] sm:text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">
                            Nomor HP
                        </label>
                        <div class="border-dotted-bottom py-1">
                            <input type="text" value="082234594919"
                                class="w-full text-sm font-semibold text-slate-800 focus:outline-none bg-transparent">
                        </div>
                    </div>

                    <!-- Kelurahan -->
                    <div class="flex flex-col">
                        <label
                            class="text-[11px] sm:text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">
                            Kelurahan
                        </label>
                        <div class="border-dotted-bottom py-1">
                            <input type="text" value="WONOREJO"
                                class="w-full text-sm font-semibold text-slate-800 focus:outline-none bg-transparent uppercase">
                        </div>
                    </div>

                    <!-- Kecamatan -->
                    <div class="flex flex-col">
                        <label
                            class="text-[11px] sm:text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">
                            Kecamatan
                        </label>
                        <div class="border-dotted-bottom py-1">
                            <input type="text" value="RUNGKUT"
                                class="w-full text-sm font-semibold text-slate-800 focus:outline-none bg-transparent uppercase">
                        </div>
                    </div>

                </div>
            </section>

            <!-- SECTION 2: IDENTITAS ANAK -->
            <section class="border border-slate-300 rounded-2xl p-5 sm:p-6 bg-white relative">
                <!-- Section Header -->
                <div class="flex flex-col mb-4 pb-3 border-b border-slate-200">
                    <div class="flex items-center gap-3">
                        <span
                            class="w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-[#e6f7f0] text-emerald-600 font-bold text-sm sm:text-base flex items-center justify-center">
                            2
                        </span>
                        <h2 class="text-base sm:text-lg font-bold text-slate-800">
                            Identitas Anak
                        </h2>
                    </div>
                    <p class="text-xs text-slate-400 mt-1 ml-10">
                        Terisi otomatis oleh sistem.
                    </p>
                </div>

                <!-- Grid Inputs Section 2 -->
                <div class="space-y-4">

                    <!-- Row 1: NIK & Nama Anak -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex flex-col">
                            <label
                                class="text-[11px] sm:text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">
                                NIK
                            </label>
                            <input type="text" value="3578036411240002"
                                class="w-full px-3 py-2 text-sm font-medium text-slate-800 bg-[#f8fafc] border border-slate-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-emerald-500">
                        </div>

                        <div class="flex flex-col">
                            <label
                                class="text-[11px] sm:text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">
                                Nama Anak
                            </label>
                            <input type="text" value="EMBUN AMEERA SHAFIYYAH"
                                class="w-full px-3 py-2 text-sm font-medium text-slate-800 bg-[#f8fafc] border border-slate-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-emerald-500 uppercase">
                        </div>
                    </div>

                    <!-- Row 2: Jenis Kelamin & Tempat Tanggal Lahir -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex flex-col">
                            <label
                                class="text-[11px] sm:text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">
                                Jenis Kelamin
                            </label>
                            <input type="text" value="PEREMPUAN"
                                class="w-full px-3 py-2 text-sm font-medium text-slate-800 bg-[#f8fafc] border border-slate-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-emerald-500 uppercase">
                        </div>

                        <div class="flex flex-col">
                            <label
                                class="text-[11px] sm:text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">
                                Tempat, Tanggal Lahir
                            </label>
                            <input type="text" value="KABUPATEN PACITAN, 24-11-2024"
                                class="w-full px-3 py-2 text-sm font-medium text-slate-800 bg-[#f8fafc] border border-slate-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-emerald-500 uppercase">
                        </div>
                    </div>

                    <!-- Row 3: Alamat -->
                    <div class="flex flex-col">
                        <label
                            class="text-[11px] sm:text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">
                            Alamat
                        </label>
                        <textarea rows="2"
                            class="w-full px-3 py-2 text-sm font-medium text-slate-800 bg-[#f8fafc] border border-slate-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-emerald-500 uppercase resize-y">WONOREJO TIMUR</textarea>
                    </div>

                    <!-- Row 4: RT & RW -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex flex-col">
                            <label
                                class="text-[11px] sm:text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">
                                RT
                            </label>
                            <input type="text" value="1"
                                class="w-full px-3 py-2 text-sm font-medium text-slate-800 bg-[#f8fafc] border border-slate-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-emerald-500">
                        </div>

                        <div class="flex flex-col">
                            <label
                                class="text-[11px] sm:text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">
                                RW
                            </label>
                            <input type="text" value="7"
                                class="w-full px-3 py-2 text-sm font-medium text-slate-800 bg-[#f8fafc] border border-slate-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-emerald-500">
                        </div>
                    </div>

                    <!-- Row 5: Kelurahan & Kecamatan -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex flex-col">
                            <label
                                class="text-[11px] sm:text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">
                                Kelurahan
                            </label>
                            <input type="text" value="WONOREJO"
                                class="w-full px-3 py-2 text-sm font-medium text-slate-800 bg-[#f8fafc] border border-slate-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-emerald-500 uppercase">
                        </div>

                        <div class="flex flex-col">
                            <label
                                class="text-[11px] sm:text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">
                                Kecamatan
                            </label>
                            <input type="text" value="RUNGKUT"
                                class="w-full px-3 py-2 text-sm font-medium text-slate-800 bg-[#f8fafc] border border-slate-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-emerald-500 uppercase">
                        </div>
                    </div>

                    <!-- Row 6: Nama Ayah & Nama Ibu -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex flex-col">
                            <label
                                class="text-[11px] sm:text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">
                                Nama Ayah
                            </label>
                            <input type="text" value="" placeholder="Masukkan nama ayah..."
                                class="w-full px-3 py-2 text-sm font-medium text-slate-800 bg-[#f8fafc] border border-slate-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-emerald-500 uppercase">
                        </div>

                        <div class="flex flex-col">
                            <label
                                class="text-[11px] sm:text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">
                                Nama Ibu
                            </label>
                            <input type="text" value="JULIA WAHYUNI AMBARWATI"
                                class="w-full px-3 py-2 text-sm font-medium text-slate-800 bg-[#f8fafc] border border-slate-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-emerald-500 uppercase">
                        </div>
                    </div>

                    <!-- Row 7: Nomor HP Orang Tua/Wali (Dotted Field at bottom left) -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                        <div class="flex flex-col">
                            <label
                                class="text-[11px] sm:text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">
                                Nomor HP Orang Tua/Wali
                            </label>
                            <div class="border-dotted-bottom py-1">
                                <input type="text" value="" placeholder="Nomor HP aktif..."
                                    class="w-full text-sm font-semibold text-slate-800 focus:outline-none bg-transparent">
                            </div>
                        </div>
                    </div>

                </div>
            </section>

        </form>

    </main>

    <!-- Helper JavaScript Functions -->
    <script>
        function resetForm() {
            if (confirm("Apakah Anda yakin ingin mengosongkan form ini?")) {
                document.getElementById("verifikasiForm").reset();
            }
        }
    </script>
</body>

</html>
