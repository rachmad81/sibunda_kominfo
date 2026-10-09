<!DOCTYPE html>
<html lang="id" class="text-[18px]">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Admin Aksesibel - Ramah Lansia</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Lucide Icons CDN -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts: Inter (Sangat terbaca) -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            transition: font-size 0.2s ease, background-color 0.2s ease;
        }

        /* Fokus yang sangat jelas untuk aksesibilitas keyboard/ketukan */
        *:focus-visible {
            outline: 4px solid #2563eb !important;
            outline-offset: 2px !important;
        }

        /* Mode Kontras Tinggi untuk Lansia dengan Low Vision */
        .high-contrast {
            background-color: #000000 !important;
            color: #ffffff !important;
        }

        .high-contrast .bg-card,
        .high-contrast .bg-white,
        .high-contrast header,
        .high-contrast aside {
            background-color: #121212 !important;
            border-color: #ffffff !important;
            color: #ffffff !important;
        }

        .high-contrast text,
        .high-contrast p,
        .high-contrast h1,
        .high-contrast h2,
        .high-contrast h3,
        .high-contrast span,
        .high-contrast td,
        .high-contrast th {
            color: #ffffff !important;
        }

        .high-contrast button,
        .high-contrast a,
        .high-contrast input,
        .high-contrast select,
        .high-contrast textarea {
            background-color: #000000 !important;
            color: #ffffff !important;
            border: 2px solid #ffffff !important;
        }

        .high-contrast .active-link {
            background-color: #ffffff !important;
            color: #000000 !important;
        }
    </style>
</head>

@yield('css_tambahan')

<body class="bg-[#fcfcfc] text-[#1a1a1a] min-h-screen flex flex-col antialiased">

    <!-- HEADER AKSESIBILITAS & PROFIL -->
    <header class="bg-white border-b border-gray-200 sticky top-0 z-40 transition-colors duration-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">

                <!-- Logo / Nama Dashboard -->
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-blue-50 text-blue-600 rounded-lg">
                        <i data-lucide="shield-check" class="w-8 h-8"></i>
                    </div>
                    <div>
                        @php
                            $conn = 'pgsql_paud';
                            $master_ks = DB::connection($conn)
                                ->table('public.sekolah as s')
                                ->leftJoin('public.pegawai as p', function ($j) use ($npsn) {
                                    return $j
                                        ->on('s.npsn', '=', 'p.npsn')
                                        ->where('p.npsn', $npsn)
                                        ->where('p.keterangan', 'Aktif')
                                        ->where('p.jabatan', 2);
                                })
                                ->where('s.npsn', $npsn)
                                ->selectRaw('p.nama,s.nama as nama_sekolah')
                                ->first();
                            $master_nama_ks = $master_ks->nama ?? '-';
                            $initial = '';
                            if ($master_nama_ks != '-') {
                                $arr_ks = explode(' ', $master_nama_ks);
                                $batas = count($arr_ks);
                                if (count($arr_ks) > 2) {
                                    $batas = 2;
                                }
                                for ($i = 0; $i < $batas; $i++) {
                                    $initial .= substr(strtoupper($arr_ks[$i]), 0, 1);
                                }
                            }
                        @endphp
                        <span class="text-xs font-semibold uppercase tracking-wider text-gray-500">Panel PAUD
                            BERMUTU</span>
                        <h1 class="hidden lg:block text-xl sm:text-2xl font-bold text-gray-900 leading-tight">
                            {{ $master_ks->nama_sekolah ?? '' }}</h1>
                    </div>
                </div>

                <!-- Alat Bantu Lansia (Aksesibilitas) -->
                <div class="flex items-center gap-2 sm:gap-4">

                    <!-- Pengatur Ukuran Teks -->
                    <div class="bg-gray-100 p-1.5 rounded-xl flex items-center gap-1" role="group"
                        aria-label="Pengatur ukuran tulisan">
                        <button onclick="changeFontSize('decrease')"
                            class="px-3 py-2 bg-white rounded-lg shadow-sm font-bold text-sm hover:bg-gray-50 transition-all border border-gray-200"
                            title="Kecilkan tulisan">
                            A-
                        </button>
                        <button onclick="changeFontSize('reset')"
                            class="px-3 py-2 bg-white rounded-lg shadow-sm font-bold text-base hover:bg-gray-50 transition-all border border-gray-200"
                            title="Ukuran normal">
                            A
                        </button>
                        <button onclick="changeFontSize('increase')"
                            class="px-3 py-2 bg-white rounded-lg shadow-sm font-bold text-lg hover:bg-gray-50 transition-all border border-gray-200"
                            title="Perbesar tulisan">
                            A+
                        </button>
                    </div>

                    <!-- Tombol Kontras Tinggi -->
                    <button onclick="toggleHighContrast()"
                        class="p-3 bg-gray-100 hover:bg-gray-200 rounded-xl transition-all border border-gray-200 flex items-center gap-2 font-medium"
                        title="Ubah Kontras Layar">
                        <i data-lucide="eye" class="w-6 h-6"></i>
                        <span class="hidden md:inline text-sm">Mode Kontras</span>
                    </button>

                    <!-- Profil Ringkas -->
                    <div class="hidden sm:flex items-center gap-3 pl-4 border-l border-gray-200">
                        <div
                            class="w-11 h-11 bg-blue-100 text-blue-800 rounded-full flex items-center justify-center font-bold text-lg">
                            {{ $initial }}
                        </div>
                        <div class="text-left">
                            <p class="font-bold text-sm text-gray-900">{{ $master_ks->nama ?? '-' }}</p>
                            <p class="text-xs text-gray-500">Kepala Satuan Pendidikan</p>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </header>

    <div class="max-w-7xl mx-auto w-full flex-1 flex flex-col md:flex-row px-4 sm:px-6 lg:px-8 py-6 gap-6">

        <!-- SIDEBAR / NAVIGASI UTAMA -->
        @include('paudmutu.master.sidebar')

        <!-- KONTEN UTAMA -->
        <main class="flex-1 min-w-0">

            @yield('content')

        </main>
    </div>

    <!-- FOOTER SEDERHANA -->
    <footer class="bg-white border-t border-gray-200 mt-auto py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-gray-500 text-sm">
            <p class="font-semibold">&copy; 2026 Admin PAUD Bermutu. Didesain Khusus untuk <b>Dinas Pendidikan & Bunda
                    PAUD
                    Kota Surabaya</b>.</p>
        </div>
    </footer>

    <!-- MODAL DETAIL LANSIA (MODAL BESAR RAMAH LANSIA) -->
    <div id="detailModal" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4 hidden"
        role="dialog" aria-modal="true" aria-labelledby="modalTitle">
        <div
            class="bg-white rounded-2xl border-2 border-gray-300 w-full max-w-xl p-6 sm:p-8 shadow-xl max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-start border-b border-gray-200 pb-4">
                <div>
                    <span class="text-xs font-bold text-blue-600 uppercase">Detail Pasien Lansia</span>
                    <h3 id="modalTitle" class="text-2xl font-bold text-gray-900 mt-1">Nama Pasien</h3>
                </div>
                <button onclick="closeModal('detailModal')"
                    class="p-2 text-gray-400 hover:text-gray-600 rounded-lg border-2 border-transparent hover:border-gray-300"
                    aria-label="Tutup jendela">
                    <i data-lucide="x" class="w-8 h-8"></i>
                </button>
            </div>
            <div class="mt-6 space-y-4 text-lg">
                <div class="grid grid-cols-2 gap-4 bg-gray-50 p-4 rounded-xl">
                    <div>
                        <p class="text-sm text-gray-500 font-semibold uppercase">Usia</p>
                        <p id="modalAge" class="font-bold text-gray-900 mt-1">-</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 font-semibold uppercase">Status Kehadiran</p>
                        <p id="modalStatus" class="font-bold text-gray-900 mt-1">-</p>
                    </div>
                </div>
                <div class="space-y-1">
                    <p class="text-sm text-gray-500 font-semibold uppercase">Catatan Pemeriksaan</p>
                    <p id="modalNote"
                        class="text-gray-800 leading-relaxed bg-blue-50/50 p-4 rounded-xl border border-blue-100 font-medium">
                        --</p>
                </div>
            </div>
            <div class="mt-8">
                <button onclick="closeModal('detailModal')"
                    class="w-full py-4 bg-blue-600 hover:bg-blue-700 text-white font-extrabold rounded-xl text-lg transition-all">
                    Tutup dan Kembali
                </button>
            </div>
        </div>
    </div>

    <!-- MODAL CONFIRMATION LOGOUT -->
    <div id="logoutModal" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4 hidden"
        role="dialog" aria-modal="true" aria-labelledby="logoutModalTitle">
        <div class="bg-white rounded-2xl border-2 border-gray-300 w-full max-w-md p-6 shadow-xl">
            <div class="text-center space-y-4">
                <div class="w-16 h-16 bg-red-100 text-red-600 rounded-full mx-auto flex items-center justify-center">
                    <i data-lucide="log-out" class="w-8 h-8"></i>
                </div>
                <h3 id="logoutModalTitle" class="text-2xl font-bold text-gray-900">Apakah Anda Ingin Keluar?</h3>
                <p class="text-gray-600 text-base leading-relaxed">Anda akan keluar dari sesi PAUD Bermutu saat ini.
                    Pastikan semua data pendaftaran baru telah disimpan.</p>
            </div>
            <div class="mt-6 flex gap-4">
                <button onclick="closeModal('logoutModal')"
                    class="flex-1 py-3.5 bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold rounded-xl border border-gray-300">
                    Batal
                </button>
                <button onclick="performLogout()"
                    class="flex-1 py-3.5 bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl">
                    Ya, Keluar
                </button>
            </div>
        </div>
    </div>

    <!-- BANNER PEMBERITAHUAN (Toast Alert Kustom, Menggantikan Alert Biasa) -->
    <div id="toastMessage"
        class="fixed bottom-6 right-6 z-50 bg-emerald-600 text-white px-6 py-4 rounded-xl shadow-xl flex items-center gap-3 transition-all transform translate-y-20 opacity-0 pointer-events-none">
        <i data-lucide="check-circle" class="w-6 h-6"></i>
        <span id="toastText" class="font-bold text-base">Berhasil melakukan aksi!</span>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Inisialisasi ikon Lucide
        lucide.createIcons();

        // Variabel untuk melacak pengaturan teks
        let currentSizeLevel = 1; // 1 = Normal, 0 = Kecil, 2 = Besar, 3 = Sangat Besar
        const sizeClasses = {
            0: "text-[16px]", // Kecil
            1: "text-[18px]", // Normal (Lebih besar dari browser standar)
            2: "text-[21px]", // Besar
            3: "text-[24px]" // Sangat Besar
        };

        // Fungsi mengubah ukuran tulisan
        function changeFontSize(action) {
            const htmlElement = document.documentElement;

            // Hapus kelas ukuran sebelumnya
            htmlElement.classList.remove(sizeClasses[currentSizeLevel]);

            if (action === 'increase') {
                if (currentSizeLevel < 3) currentSizeLevel++;
            } else if (action === 'decrease') {
                if (currentSizeLevel > 0) currentSizeLevel--;
            } else {
                currentSizeLevel = 1; // Reset
            }

            // Tambahkan kelas ukuran yang baru
            htmlElement.classList.add(sizeClasses[currentSizeLevel]);
        }

        // Fungsi mengganti Mode Kontras Tinggi
        function toggleHighContrast() {
            document.body.classList.toggle('high-contrast');
        }

        // Fungsi berpindah halaman/tab menu (tanpa memuat ulang halaman)
        function switchTab(url) {
            window.location = url;
        }

        // Fungsi Pencarian Data Lansia sederhana
        function filterLansia() {
            const input = document.getElementById('searchInput');
            const filter = input.value.toUpperCase();
            const table = document.getElementById('lansiaTable');
            const tr = table.getElementsByTagName('tr');

            for (let i = 1; i < tr.length; i++) {
                const tdName = tr[i].getElementsByClassName('font-name')[0];
                if (tdName) {
                    const txtValue = tdName.textContent || tdName.innerText;
                    if (txtValue.toUpperCase().indexOf(filter) > -1) {
                        tr[i].style.display = "";
                    } else {
                        tr[i].style.display = "none";
                    }
                }
            }
        }

        // Fungsi Menampilkan Detail Lansia di Modal
        function showDetail(name, age, status, note) {
            document.getElementById('modalTitle').textContent = name;
            document.getElementById('modalAge').textContent = age;
            document.getElementById('modalStatus').textContent = status;
            document.getElementById('modalNote').textContent = note;

            document.getElementById('detailModal').classList.remove('hidden');
        }

        // Fungsi Menutup Modal
        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
        }

        // Fungsi Menampilkan Modal Logout
        function showLogoutModal() {
            document.getElementById('logoutModal').classList.remove('hidden');
        }

        // Simulasi Logout Akun
        function performLogout() {
            closeModal('logoutModal');
            window.close();
            showToast('Anda berhasil keluar dari sistem.');
        }

        // Menghandle Pendaftaran Lansia Baru
        function handleFormRegister(event) {
            event.preventDefault();
            const name = document.getElementById('nama_lengkap').value;
            const birthYear = document.getElementById('tanggal_lahir').value;
            const gender = document.getElementById('jenis_kelamin').value;
            const history = document.getElementById('riwayat').value || '-';

            // Hitung estimasi usia
            const currentYear = new Date().getFullYear();
            const calculatedAge = currentYear - parseInt(birthYear);

            // Tambahkan baris baru ke tabel Lansia secara dinamis
            const tableBody = document.querySelector('#lansiaTable tbody');
            const newRow = document.createElement('tr');
            newRow.className = "hover:bg-gray-50";
            newRow.innerHTML = `
                <td class="p-5 font-bold text-gray-900 font-name">${name}</td>
                <td class="p-5 text-gray-600">${calculatedAge} Tahun</td>
                <td class="p-5 text-gray-600">Alamat Baru didaftarkan</td>
                <td class="p-5 text-center">
                    <span class="inline-flex px-3 py-1.5 rounded-full text-sm font-bold bg-green-100 text-green-800">Aktif</span>
                </td>
            `;
            tableBody.appendChild(newRow);

            // Reset formulir pendaftaran
            event.target.reset();

            // Beritahu pengguna dengan toast kustom
            showToast(`Sukses mendaftarkan ${name}!`);

            // Pindahkan kembali tampilan ke Tab Daftar Lansia agar pengguna bisa melihat hasilnya langsung
            setTimeout(() => {
                switchTab('pasien');
            }, 1000);
        }

        // Menampilkan pesan toast melayang kustom (ramah tanpa menginterupsi layar secara agresif)
        function showToast(message) {
            const toast = document.getElementById('toastMessage');
            const toastText = document.getElementById('toastText');
            toastText.textContent = message;

            // Efek transisi masuk
            toast.classList.remove('translate-y-20', 'opacity-0', 'pointer-events-none');

            // Sembunyikan setelah 3.5 detik
            setTimeout(() => {
                toast.classList.add('translate-y-20', 'opacity-0', 'pointer-events-none');
            }, 3500);
        }

        async function postData(url = '', data = {}) {
            try {
                const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const response = await fetch(url, {
                    method: 'POST', // Specifies the request type
                    headers: {
                        'Content-Type': 'application/json', // Tells the server we are sending JSON
                        'X-CSRF-TOKEN': token
                    },
                    body: JSON.stringify(data) // Converts JavaScript object to a JSON string
                });

                // Check if the response status is OK (200-299)
                // if (!response.ok) {
                //     throw new Error(`HTTP error! Status: ${response.status}`);
                // }

                return await response.json(); // Parses the JSON response from the server
            } catch (error) {
                // console.log('Error during POST request:', error);
                // throw error; // Re-throw the error for the caller to handle
            }
        }
    </script>

    @yield('js_tambahan')
</body>

</html>
