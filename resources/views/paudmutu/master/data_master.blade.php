<!-- TABS 1: RINGKASAN (DASHBOARD) -->
<section id="tab-dashboard" class="space-y-6">
    <!-- Sambutan -->
    <div class="bg-white border border-gray-200 p-6 rounded-2xl shadow-sm">
        <h2 class="text-2xl sm:text-3xl font-bold text-gray-900">Selamat Pagi, Bapak Utomo!</h2>
        <p class="text-gray-600 mt-2 leading-relaxed">Berikut adalah rangkuman aktivitas kesehatan warga
            binaan lansia hari ini. Semua data tersaji dengan huruf berukuran besar agar nyaman dibaca.</p>
    </div>

    <!-- Kartu Statistik Utama -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">

        <!-- Kartu 1 -->
        <div class="bg-white border-l-8 border-l-blue-600 border border-gray-200 p-6 rounded-2xl shadow-sm">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-semibold text-gray-500 uppercase">Total Warga Lansia</p>
                    <p class="text-4xl font-extrabold text-gray-900 mt-1">142</p>
                    <p class="text-sm text-gray-600 mt-2">Orang terdaftar aktif</p>
                </div>
                <div class="p-3 bg-blue-50 text-blue-600 rounded-xl">
                    <i data-lucide="users" class="w-8 h-8"></i>
                </div>
            </div>
        </div>

        <!-- Kartu 2 -->
        <div class="bg-white border-l-8 border-l-emerald-600 border border-gray-200 p-6 rounded-2xl shadow-sm">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-semibold text-gray-500 uppercase">Hadir Cek Fisik</p>
                    <p class="text-4xl font-extrabold text-gray-900 mt-1">86</p>
                    <p class="text-sm text-emerald-600 font-semibold mt-2 flex items-center gap-1">
                        <i data-lucide="trending-up" class="w-4 h-4"></i> Hari ini sudah hadir
                    </p>
                </div>
                <div class="p-3 bg-emerald-50 text-emerald-600 rounded-xl">
                    <i data-lucide="activity" class="w-8 h-8"></i>
                </div>
            </div>
        </div>

        <!-- Kartu 3 -->
        <div class="bg-white border-l-8 border-l-amber-500 border border-gray-200 p-6 rounded-2xl shadow-sm">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-semibold text-gray-500 uppercase">Perlu Perhatian</p>
                    <p class="text-4xl font-extrabold text-gray-900 mt-1">12</p>
                    <p class="text-sm text-amber-600 font-semibold mt-2">Butuh tindak lanjut</p>
                </div>
                <div class="p-3 bg-amber-50 text-amber-500 rounded-xl">
                    <i data-lucide="alert-triangle" class="w-8 h-8"></i>
                </div>
            </div>
        </div>

    </div>

    <!-- Tabel Data Sederhana & Nyaman Dibaca -->
    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h3 class="text-xl font-bold text-gray-900">Jadwal Pemeriksaan Hari Ini</h3>
                <p class="text-sm text-gray-500 mt-1">Daftar lansia yang dijadwalkan hadir untuk cek tensi
                    dan gula darah.</p>
            </div>
            <button onclick="switchTab('pasien')"
                class="px-5 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-sm transition-all text-base flex items-center justify-center gap-2">
                Lihat Semua Lansia
                <i data-lucide="arrow-right" class="w-5 h-5"></i>
            </button>
        </div>

        <!-- Container Tabel yang Responsif -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-gray-600 border-b border-gray-200">
                        <th class="p-5 font-bold text-sm sm:text-base">Nama Lengkap</th>
                        <th class="p-5 font-bold text-sm sm:text-base">Usia</th>
                        <th class="p-5 font-bold text-sm sm:text-base text-center">Status Kehadiran</th>
                        <th class="p-5 font-bold text-sm sm:text-base">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <tr class="hover:bg-gray-50">
                        <td class="p-5 font-bold text-gray-900">Ibu Siti Aminah</td>
                        <td class="p-5 text-gray-600">68 Tahun</td>
                        <td class="p-5 text-center">
                            <span
                                class="inline-flex items-center px-4 py-1.5 rounded-full text-sm font-bold bg-emerald-100 text-emerald-800">
                                Selesai Cek
                            </span>
                        </td>
                        <td class="p-5">
                            <button
                                onclick="showDetail('Siti Aminah', '68 Tahun', 'Selesai Cek', 'Tekanan darah normal 120/80, gula darah aman.')"
                                class="px-4 py-2 bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded-lg font-bold text-sm">
                                Lihat Detail
                            </button>
                        </td>
                    </tr>
                    <tr class="hover:bg-gray-50">
                        <td class="p-5 font-bold text-gray-900">Bapak Ahmad Hidayat</td>
                        <td class="p-5 text-gray-600">72 Tahun</td>
                        <td class="p-5 text-center">
                            <span
                                class="inline-flex items-center px-4 py-1.5 rounded-full text-sm font-bold bg-amber-100 text-amber-800">
                                Menunggu
                            </span>
                        </td>
                        <td class="p-5">
                            <button
                                onclick="showDetail('Ahmad Hidayat', '72 Tahun', 'Menunggu', 'Menunggu giliran cek tensi berkala harian.')"
                                class="px-4 py-2 bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded-lg font-bold text-sm">
                                Lihat Detail
                            </button>
                        </td>
                    </tr>
                    <tr class="hover:bg-gray-50">
                        <td class="p-5 font-bold text-gray-900">Bapak Kartono Sastro</td>
                        <td class="p-5 text-gray-600">79 Tahun</td>
                        <td class="p-5 text-center">
                            <span
                                class="inline-flex items-center px-4 py-1.5 rounded-full text-sm font-bold bg-red-100 text-red-800">
                                Perlu Perhatian
                            </span>
                        </td>
                        <td class="p-5">
                            <button
                                onclick="showDetail('Kartono Sastro', '79 Tahun', 'Perlu Perhatian', 'Tensi darah tinggi 150/95. Disarankan istirahat dahulu sebelum cek ulang.')"
                                class="px-4 py-2 bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded-lg font-bold text-sm">
                                Lihat Detail
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</section>

<!-- TABS 2: DAFTAR LANSIA -->
<section id="tab-pasien" class="hidden space-y-6">
    <div class="bg-white border border-gray-200 p-6 rounded-2xl shadow-sm">
        <h2 class="text-2xl font-bold text-gray-900">Daftar Warga Lansia Terdaftar</h2>
        <p class="text-gray-600 mt-1 leading-relaxed">Kelola dan lihat informasi lengkap para warga lansia
            di wilayah ini.</p>

        <!-- Form Cari Sederhana (Input Besar) -->
        <div class="mt-6 flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <span class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-gray-400">
                    <i data-lucide="search" class="w-6 h-6"></i>
                </span>
                <input type="text" id="searchInput" onkeyup="filterLansia()"
                    placeholder="Ketik nama lansia untuk mencari..."
                    class="w-full pl-12 pr-4 py-4 border-2 border-gray-300 rounded-xl focus:border-blue-600 text-lg shadow-sm"
                    aria-label="Cari nama lansia">
            </div>
        </div>
    </div>

    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse" id="lansiaTable">
                <thead>
                    <tr class="bg-gray-50 text-gray-600 border-b border-gray-200">
                        <th class="p-5 font-bold">Nama Lengkap</th>
                        <th class="p-5 font-bold">Usia</th>
                        <th class="p-5 font-bold">Alamat Rumah</th>
                        <th class="p-5 font-bold text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <tr class="hover:bg-gray-50">
                        <td class="p-5 font-bold text-gray-900 font-name">Ibu Siti Aminah</td>
                        <td class="p-5 text-gray-600">68 Tahun</td>
                        <td class="p-5 text-gray-600">RT 04 / RW 02, No. 12</td>
                        <td class="p-5 text-center">
                            <span
                                class="inline-flex px-3 py-1.5 rounded-full text-sm font-bold bg-green-100 text-green-800">Aktif</span>
                        </td>
                    </tr>
                    <tr class="hover:bg-gray-50">
                        <td class="p-5 font-bold text-gray-900 font-name">Bapak Ahmad Hidayat</td>
                        <td class="p-5 text-gray-600">72 Tahun</td>
                        <td class="p-5 text-gray-600">RT 02 / RW 01, No. 45</td>
                        <td class="p-5 text-center">
                            <span
                                class="inline-flex px-3 py-1.5 rounded-full text-sm font-bold bg-green-100 text-green-800">Aktif</span>
                        </td>
                    </tr>
                    <tr class="hover:bg-gray-50">
                        <td class="p-5 font-bold text-gray-900 font-name">Bapak Kartono Sastro</td>
                        <td class="p-5 text-gray-600">79 Tahun</td>
                        <td class="p-5 text-gray-600">RT 01 / RW 03, No. 08</td>
                        <td class="p-5 text-center">
                            <span
                                class="inline-flex px-3 py-1.5 rounded-full text-sm font-bold bg-green-100 text-green-800">Aktif</span>
                        </td>
                    </tr>
                    <tr class="hover:bg-gray-50">
                        <td class="p-5 font-bold text-gray-900 font-name">Ibu Wahyuni Lestari</td>
                        <td class="p-5 text-gray-600">81 Tahun</td>
                        <td class="p-5 text-gray-600">RT 05 / RW 02, No. 22</td>
                        <td class="p-5 text-center">
                            <span
                                class="inline-flex px-3 py-1.5 rounded-full text-sm font-bold bg-red-100 text-red-800">Sakit</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</section>

<!-- TABS 3: PENDAFTARAN (FORMULIR BESAR) -->
<section id="tab-pendaftaran" class="hidden space-y-6">
    <div class="bg-white border border-gray-200 p-6 sm:p-8 rounded-2xl shadow-sm">
        <h2 class="text-2xl font-bold text-gray-900">Formulir Pendaftaran Warga Lansia Baru</h2>
        <p class="text-gray-600 mt-1 leading-relaxed">Isi formulir pendaftaran di bawah ini. Formulir
            dirancang dengan kolom besar agar mudah diketik dan dibaca.</p>

        <form onsubmit="handleFormRegister(event)" class="mt-8 space-y-6">

            <!-- Nama Lengkap -->
            <div class="space-y-2">
                <label for="nama_lengkap" class="block font-bold text-gray-800 text-lg">Nama Lengkap
                    Lansia</label>
                <input type="text" id="nama_lengkap" required placeholder="Contoh: Bapak Joko Widodo"
                    class="w-full px-4 py-4 border-2 border-gray-300 rounded-xl focus:border-blue-600 text-lg shadow-sm">
            </div>

            <!-- Grid untuk Usia dan Jenis Kelamin -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">

                <!-- Tanggal Lahir -->
                <div class="space-y-2">
                    <label for="tanggal_lahir" class="block font-bold text-gray-800 text-lg">Tahun
                        Lahir</label>
                    <input type="number" min="1920" max="1975" id="tanggal_lahir" required
                        placeholder="Contoh: 1955"
                        class="w-full px-4 py-4 border-2 border-gray-300 rounded-xl focus:border-blue-600 text-lg shadow-sm">
                </div>

                <!-- Jenis Kelamin -->
                <div class="space-y-2">
                    <label for="jenis_kelamin" class="block font-bold text-gray-800 text-lg">Jenis
                        Kelamin</label>
                    <select id="jenis_kelamin" required
                        class="w-full px-4 py-4 border-2 border-gray-300 bg-white rounded-xl focus:border-blue-600 text-lg shadow-sm">
                        <option value="" disabled selected>Pilih salah satu...</option>
                        <option value="Laki-laki">Laki-laki</option>
                        <option value="Perempuan">Perempuan</option>
                    </select>
                </div>

            </div>

            <!-- Riwayat Penyakit Ringkas -->
            <div class="space-y-2">
                <label for="riwayat" class="block font-bold text-gray-800 text-lg">Riwayat Penyakit (Bila
                    Ada)</label>
                <textarea id="riwayat" rows="3" placeholder="Contoh: Darah tinggi, diabetes ringan, atau kolesterol..."
                    class="w-full px-4 py-4 border-2 border-gray-300 rounded-xl focus:border-blue-600 text-lg shadow-sm"></textarea>
            </div>

            <!-- Tombol Submit Besar -->
            <div class="pt-4">
                <button type="submit"
                    class="w-full py-4.5 bg-blue-600 hover:bg-blue-700 text-white font-extrabold rounded-xl text-lg shadow-md transition-all flex items-center justify-center gap-2">
                    <i data-lucide="save" class="w-6 h-6"></i>
                    Daftarkan Sekarang
                </button>
            </div>

        </form>
    </div>
</section>

<!-- TABS 4: BANTUAN / PANDUAN PENGGUNAAN -->
<section id="tab-bantuan" class="hidden space-y-6">
    <div class="bg-white border border-gray-200 p-6 sm:p-8 rounded-2xl shadow-sm">
        <h2 class="text-2xl font-bold text-gray-900 mb-2">Panduan Cara Menggunakan Website</h2>
        <p class="text-gray-600 leading-relaxed mb-6">Sistem ini sengaja dibuat sangat sederhana agar
            memudahkan operasional harian. Berikut adalah cara penggunaannya:</p>

        <div class="space-y-6">
            <div class="flex gap-4">
                <div
                    class="w-12 h-12 bg-blue-100 text-blue-700 rounded-full flex-shrink-0 flex items-center justify-center font-bold text-xl">
                    1
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Membaca Grafik dan Angka</h3>
                    <p class="text-gray-600 mt-1 leading-relaxed">Angka besar di halaman utama menunjukkan
                        total data hari ini. Jika ada yang berwarna merah dengan tanda seru, berarti ada
                        lansia yang butuh istirahat atau perawatan tambahan.</p>
                </div>
            </div>

            <div class="flex gap-4">
                <div
                    class="w-12 h-12 bg-blue-100 text-blue-700 rounded-full flex-shrink-0 flex items-center justify-center font-bold text-xl">
                    2
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Cara Mengubah Ukuran Tulisan</h3>
                    <p class="text-gray-600 mt-1 leading-relaxed">Apabila tulisan terasa kekecilan, tekan
                        tombol <strong>A+</strong> di pojok kanan atas untuk memperbesar. Tekan
                        <strong>A-</strong> untuk mengecilkan, dan tombol <strong>A</strong> untuk
                        mengembalikan ke ukuran normal.
                    </p>
                </div>
            </div>

            <div class="flex gap-4">
                <div
                    class="w-12 h-12 bg-blue-100 text-blue-700 rounded-full flex-shrink-0 flex items-center justify-center font-bold text-xl">
                    3
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Mode Kontras Tinggi untuk Mata Lelah</h3>
                    <p class="text-gray-600 mt-1 leading-relaxed">Jika layar putih terasa menyilaukan mata
                        setelah membaca terlalu lama, tekan tombol <strong>Mode Kontras</strong> di bagian
                        atas. Layar akan beralih ke warna hitam yang tajam namun sangat teduh untuk mata.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>
