<aside class="w-full md:w-64 flex-shrink-0">
    <nav class="bg-white border border-gray-200 rounded-2xl p-4 space-y-2 shadow-sm sticky top-28"
        aria-label="Menu Utama">
        <p class="text-xs font-bold text-gray-400 px-3 uppercase tracking-wider mb-2">Menu Utama</p>

        <button
            onclick="switchTab('{{ route('paud_mutu-dashboard', ['halaman' => 'main', 'tahun' => $tahun, 'npsn' => $npsn]) }}')"
            class="@if (isset($active_menu) && $active_menu == 'dashboard') bg-blue-50 @else hover:bg-gray-50 @endif w-full flex items-center gap-4 px-4 py-3.5 rounded-xl text-left font-bold text-gray-700 text-blue-700 border-2 border-transparent transition-all">
            <i data-lucide="layout-dashboard" class="w-6 h-6"></i>
            <span>Dashboard</span>
        </button>

        <button
            onclick="switchTab('{{ route('paud_mutu-pendaftaran', ['halaman' => 'main', 'tahun' => $tahun, 'npsn' => $npsn]) }}')"
            class="@if (isset($active_menu) && $active_menu == 'pendaftaran') bg-blue-50 @else hover:bg-gray-50 @endif w-full flex items-center gap-4 px-4 py-3.5 rounded-xl text-left font-bold text-gray-700 text-blue-700 border-2 border-transparent transition-all">
            <span class="w-6 h-6">▣</span>
            <span>Pendaftaran</span>
        </button>

        <button
            onclick="switchTab('{{ route('paud_mutu-paudmutu', ['halaman' => 'halaman_verifikasi', 'tahun' => $tahun, 'npsn' => $npsn]) }}')"
            class="@if (isset($active_menu) && $active_menu == 'paudmutu') bg-blue-50 @else hover:bg-gray-50 @endif w-full flex items-center gap-4 px-4 py-3.5 rounded-xl text-left font-bold text-gray-700 text-blue-700 border-2 border-transparent transition-all">
            <span class="w-6 h-6">☷</span>
            <span>Administrasi Tahap I</span>
        </button>

        <button
            onclick="switchTab('{{ route('paud_mutu-tahap2', ['halaman' => 'main', 'tahun' => $tahun, 'npsn' => $npsn]) }}')"
            class="@if (isset($active_menu) && $active_menu == 'tahap2') bg-blue-50 @else hover:bg-gray-50 @endif w-full flex items-center gap-4 px-4 py-3.5 rounded-xl text-left font-bold text-gray-700 text-blue-700 border-2 border-transparent transition-all">
            <span class="w-6 h-6">★</span>
            <span>Mutu Layanan Tahap II</span>
        </button>

        {{-- <button onclick="switchTab('{{ route('paud_mutu-tahap3', ['halaman' => 'main', 'tahun' => $tahun, 'npsn' => $npsn]) }}')"  --}}
        <button onclick="Swal.fire('Whoooops','Maaf belum bisa digunakan','warning')"
            class="@if (isset($active_menu) && $active_menu == 'tahap3') bg-blue-50 @else hover:bg-gray-50 @endif w-full flex items-center gap-4 px-4 py-3.5 rounded-xl text-left font-bold text-gray-700 text-blue-700 border-2 border-transparent transition-all">
            <span class="w-6 h-6">⌖</span>
            <span>Tahap III</span>
        </button>

        {{-- <button onclick="switchTab('{{ route('paud_mutu-paudmutu', ['halaman' => 'main', 'tahun' => $tahun, 'npsn' => $npsn]) }}')" --}}
        <button onclick="Swal.fire('Whoooops','Maaf belum bisa digunakan','warning')"
            class="@if (isset($active_menu) && $active_menu == 'hasil') bg-blue-50 @else hover:bg-gray-50 @endif w-full flex items-center gap-4 px-4 py-3.5 rounded-xl text-left font-bold text-gray-700 text-blue-700 border-2 border-transparent transition-all">
            <span class="w-6 h-6">✓</span>
            <span>Hasil & Catatan</span>
        </button>

        <div class="pt-4 border-t border-gray-100 mt-4">
            <button onclick="showLogoutModal()"
                class="w-full flex items-center gap-4 px-4 py-3.5 rounded-xl text-left font-bold text-red-600 hover:bg-red-50 border-2 border-transparent transition-all">
                <i data-lucide="log-out" class="w-6 h-6"></i>
                <span>Keluar Akun</span>
            </button>
        </div>
    </nav>
</aside>
