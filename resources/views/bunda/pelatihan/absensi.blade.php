<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Presensi Kegiatan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
        }
    </style>
</head>

<body class="min-h-screen flex items-center justify-center p-4">

    <div class="max-w-4xl w-full bg-white rounded-2xl shadow-xl overflow-hidden flex flex-col md:flex-row">

        <!-- Kolom Kiri: QR Code & URL -->
        <div
            class="md:w-5/12 bg-slate-50 p-8 flex flex-col items-center justify-center border-b md:border-b-0 md:border-r border-slate-200">
            <div class="text-center mb-6">
                <h3 class="text-lg font-bold text-slate-800">Scan untuk Absensi</h3>
                <p class="text-sm text-slate-500">Arahkan kamera ponsel Anda</p>
            </div>

            <div id="qrcode" class="bg-white p-4 rounded-xl shadow-sm border border-slate-100 mb-6"></div>

            <div class="w-full">
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2 text-center">Atau akses
                    melalui URL:</p>
                <div class="bg-white border border-slate-200 rounded-lg p-3 flex items-center justify-between">
                    <span id="url-text"
                        class="text-sm text-blue-600 truncate mr-2">https://absensi.link/kegiatan-01</span>
                    <button onclick="copyUrl()" class="text-slate-400 hover:text-blue-500 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="Wait for copy icon" />
                            <path
                                d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                        </svg>
                    </button>
                </div>
                <p id="copy-msg" class="text-[10px] text-green-500 mt-1 text-center hidden font-medium">URL berhasil
                    disalin!</p>
            </div>
        </div>

        <!-- Kolom Kanan: Detail Kegiatan -->
        <div class="md:w-7/12 p-8 lg:p-12">
            <div
                class="inline-block px-3 py-1 rounded-full bg-blue-100 text-blue-700 text-xs font-bold uppercase tracking-widest mb-4">
                Konfirmasi Kehadiran
            </div>
            <h1 class="text-3xl font-bold text-slate-900 mb-6 leading-tight">{{ $pelatihan->nama_materi }}</h1>

            <div class="space-y-6">
                <!-- Info Item -->
                <div class="flex items-start">
                    <div class="bg-slate-100 p-2 rounded-lg mr-4 text-slate-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-semibold text-slate-500 uppercase">Waktu & Tanggal</h4>
                        <p class="text-slate-800 font-medium text-lg">
                            {{ App\Tools\Waktu::parseFullDate($pelatihan->tanggal) }}</p>
                        <p class="text-slate-600">{{ $pelatihan->jam_mulai }} s.d {{ $pelatihan->jam_selesai }} WIB</p>
                    </div>
                </div>

                <!-- Info Item -->
                <div class="flex items-start">
                    <div class="bg-slate-100 p-2 rounded-lg mr-4 text-slate-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-semibold text-slate-500 uppercase">Lokasi</h4>
                        <p class="text-slate-800 font-medium text-lg">
                            {{ $pelatihan->alamat_lokasi != '' ? $pelatihan->alamat_lokasi : $pelatihan->lokasi }}</p>
                    </div>
                </div>

                <!-- Info Item -->
                <div class="flex items-start">
                    <div class="bg-slate-100 p-2 rounded-lg mr-4 text-slate-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-semibold text-slate-500 uppercase">Peserta</h4>
                        <p class="text-slate-800 font-medium text-lg">{{ $pelatihan->npsn }} -
                            {{ $pelatihan->nama_sekolah }}</p>
                    </div>
                </div>
            </div>

            <div class="mt-10 pt-6 border-t border-slate-100">
                <p class="text-sm text-slate-500 italic"></p>
            </div>
        </div>
    </div>

    <script>
        // Konfigurasi URL Absensi
        const attendanceUrl = "{{ $url }}";
        document.getElementById('url-text').innerText = attendanceUrl;

        // Inisialisasi QR Code
        new QRCode(document.getElementById("qrcode"), {
            text: attendanceUrl,
            width: 180,
            height: 180,
            colorDark: "#1e293b",
            colorLight: "#ffffff",
            correctLevel: QRCode.CorrectLevel.H
        });

        // Fungsi Salin URL
        function copyUrl() {
            const text = document.getElementById('url-text').innerText;
            const textArea = document.createElement("textarea");
            textArea.value = text;
            document.body.appendChild(textArea);
            textArea.select();
            document.execCommand("copy");
            document.body.removeChild(textArea);

            const msg = document.getElementById('copy-msg');
            msg.classList.remove('hidden');
            setTimeout(() => {
                msg.classList.add('hidden');
            }, 2000);
        }
    </script>
</body>

</html>
