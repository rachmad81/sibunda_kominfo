@extends('paudmutu.master.master')

@section('css_tambahan')
@endsection

@section('content')
    <section id="tab-dashboard" class="space-y-6">
        <div class="grid">
            <div class="bg-white rounded-2xl border p-6">
                <div class="text-2xl font-bold">Mutu Layanan Tahap II</div>
            </div>
        </div>
        @if (!empty($paud_mutu))
            <div class="grid sm:grid-cols-1 gap-6">

                <!-- TITLE GROUP SECTION -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80 mb-8">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div>
                            <div class="flex items-center space-x-2">
                                <h2 class="text-2xl font-bold text-slate-800">Unggah Proposal dan Dokumen</h2>
                                <button onclick="toggleGroupInfo()"
                                    class="text-slate-400 hover:text-brand-600 transition-colors"
                                    title="Informasi Kelompok">
                                    <i class="fa-regular fa-circle-question text-lg"></i>
                                </button>
                            </div>
                            <p class="text-sm text-slate-500 mt-1">
                                Lembaga: <strong class="text-slate-700">{{ $sekolah->nama ?? '' }}</strong> | Kepala
                                Sekolah: <span class="text-slate-700 font-medium">{{ $sekolah->nama_ks ?? '' }}</span>
                            </p>
                        </div>

                        <!-- Status Summary Counters -->
                        <div
                            class="flex items-center gap-3 bg-slate-50 p-3 rounded-xl border border-slate-100 self-start md:self-auto">
                            <div class="text-center px-3 border-r border-slate-200">
                                <span class="text-xs text-slate-400 font-medium block">Total Berkas</span>
                                <span id="statTotal" class="text-lg font-bold text-slate-700">0</span>
                            </div>
                            <div class="text-center px-3 border-r border-slate-200">
                                <span class="text-xs text-amber-600 font-medium block">Pending</span>
                                <span id="statPending" class="text-lg font-bold text-amber-600">0</span>
                            </div>
                            <div class="text-center px-3 border-r border-slate-200">
                                <span class="text-xs text-emerald-600 font-medium block">Disetujui</span>
                                <span id="statApproved" class="text-lg font-bold text-emerald-600">0</span>
                            </div>
                            <div class="text-center px-3">
                                <span class="text-xs text-rose-600 font-medium block">Ditolak</span>
                                <span id="statRejected" class="text-lg font-bold text-rose-600">0</span>
                            </div>
                            <div class="text-center px-3">
                                <span class="text-xs text-grey-600 font-medium block">Belum ada</span>
                                <span id="statBelum" class="text-lg font-bold text-grey-600">0</span>
                            </div>
                        </div>
                    </div>

                    <!-- Group Info Panel (Collapsible) -->
                    <div id="groupInfoPanel"
                        class="hidden mt-4 pt-4 border-t border-slate-100 text-xs text-slate-600 grid grid-cols-1 md:grid-cols-3 gap-4 bg-slate-50/50 p-4 rounded-xl">
                        <div>
                            <span class="font-semibold text-slate-700 block mb-1">NPSN / Izin Operasional:</span>
                            <p class="text-slate-500">{{ $sekolah->npsn ?? '' }} / {{ $sekolah->no_sk_operasional ?? '-' }}
                            </p>
                        </div>
                        <div>
                            <span class="font-semibold text-slate-700 block mb-1">Wilayah / Kabupaten:</span>
                            <p class="text-slate-500">Kota Surabaya</p>
                        </div>
                    </div>
                </div>

                <!-- FILTER & SEARCH CONTROLS -->
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4 mb-6">
                    <div class="flex items-center space-x-1 overflow-x-auto pb-1 sm:pb-0" id="filterContainer">
                        <button onclick="setFilter('Semua')" id="btn-Semua"
                            class="filter-btn px-4 py-2 rounded-xl text-xs font-medium transition-all flex items-center whitespace-nowrap bg-brand-600 text-white shadow-sm">
                            Semua Dokumen (<span id="cntSemua">0</span>)
                        </button>
                        <button onclick="setFilter('Pending')" id="btn-Pending"
                            class="filter-btn px-4 py-2 rounded-xl text-xs font-medium transition-all flex items-center whitespace-nowrap bg-white text-slate-600 hover:bg-amber-50">
                            <span class="w-2 h-2 rounded-full bg-amber-400 mr-2"></span>
                            Pending (<span id="cntPending">0</span>)
                        </button>
                        <button onclick="setFilter('Disetujui')" id="btn-Disetujui"
                            class="filter-btn px-4 py-2 rounded-xl text-xs font-medium transition-all flex items-center whitespace-nowrap bg-white text-slate-600 hover:bg-emerald-50">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 mr-2"></span>
                            Disetujui (<span id="cntDisetujui">0</span>)
                        </button>
                        <button onclick="setFilter('Ditolak')" id="btn-Ditolak"
                            class="filter-btn px-4 py-2 rounded-xl text-xs font-medium transition-all flex items-center whitespace-nowrap bg-white text-slate-600 hover:bg-rose-50">
                            <span class="w-2 h-2 rounded-full bg-rose-400 mr-2"></span>
                            Ditolak (<span id="cntDitolak">0</span>)
                        </button>
                        <button onclick="setFilter('Belum ada')" id="btn-Belum ada"
                            class="filter-btn px-4 py-2 rounded-xl text-xs font-medium transition-all flex items-center whitespace-nowrap bg-white text-slate-600 hover:bg-grey-50">
                            <span class="w-2 h-2 rounded-full bg-grey-400 mr-2"></span>
                            Belum (<span id="cntBelum">0</span>)
                        </button>
                    </div>

                    <div class="relative min-w-[240px]">
                        <i
                            class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                        <input type="text" id="searchInput" oninput="handleSearch()" placeholder="Cari nama berkas..."
                            class="w-full pl-9 pr-4 py-2 bg-white border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition-all outline-none">
                    </div>
                </div>

                <!-- DOCUMENT LIST CONTAINER -->
                <div id="documentList" class="space-y-4">
                    <!-- Dynamic JavaScript Rendered Document Cards -->
                </div>

                <!-- Empty State Container -->
                <div id="emptyNotice" class="hidden bg-white rounded-2xl p-12 text-center border border-slate-200">
                    <div
                        class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-3 text-slate-400">
                        <i class="fa-solid fa-folder-open text-2xl"></i>
                    </div>
                    <h4 class="text-base font-semibold text-slate-700">Dokumen Tidak Ditemukan</h4>
                    <p class="text-xs text-slate-400 mt-1">Tidak ada dokumen yang sesuai dengan kriteria pencarian atau
                        filter
                        status Anda.</p>
                </div>

            </div>
        @else
            <div class="max-w-5xl bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-6">

                <div class="flex items-center gap-1.5 text-gray-800">
                    <h3 class="text-lg font-bold">Pendaftaran PAUD Bermutu</h3>
                    <i data-lucide="info" class="w-4 h-4 text-gray-400 shrink-0"></i>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    <button type="button" onclick="pendaftaran()"
                        class="flex-1 px-4 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-xl text-sm transition-all shadow-lg shadow-indigo-100 text-center">
                        Mendaftar
                    </button>

                </div>

            </div>
        @endif

        @include('paudmutu.tahap2.modal_berkas')

    </section>
    <div id="modal-page"></div>
    @include('paudmutu.tahap2.modal_dokumen')
    <!-- TOAST CONTAINER -->
    <div id="toastContainer" class="fixed bottom-5 right-5 z-50 flex flex-col space-y-2 pointer-events-none"></div>
@endsection

@section('js_tambahan')
    <script>
        // Default initial data for the 8 document items grouped into sections
        // const initialDocuments = [{
        //         id: 1,
        //         group: 'GROUP I',
        //         groupName: 'Dokumen Legalitas & Administrasi Pembelajaran',
        //         groupIcon: 'fa-scale-balanced',
        //         code: 'DOC-LEG-01',
        //         title: 'Dokumen legalitas dan administrasi lembaga',
        //         url: 'https://drive.google.com/file/d/1A8x9Z_Legalitas_PAUD/view',
        //         score: 3,
        //         status: 'Pending',
        //         notes: 'Izin operasional terlampir, mohon perbarui SK pengurus terbaru.',
        //         lastUpdated: '21 Jul 2026, 10:15 WIB',
        //         history: [{
        //                 adminName: 'Drs. Ahmad Fauzi, M.Pd',
        //                 timestamp: '21 Jul 2026, 10:15 WIB',
        //                 status: 'Pending',
        //                 score: 3,
        //                 notes: 'Diunggah ulang oleh lembaga setelah revisi SK.',
        //                 url: 'https://drive.google.com/file/d/1A8x9Z_Legalitas_PAUD/view'
        //             },
        //             {
        //                 adminName: 'Siti Badriah, S.T.',
        //                 timestamp: '19 Jul 2026, 14:30 WIB',
        //                 status: 'Ditolak',
        //                 score: 2,
        //                 notes: 'Masa berlaku Akta Notaris lembaga telah kedaluwarsa.',
        //                 url: 'https://drive.google.com/file/d/1A8x9Z_Old/view'
        //             }
        //         ]
        //     },
        //     {
        //         id: 2,
        //         group: 'GROUP I',
        //         groupName: 'Dokumen Legalitas & Administrasi Pembelajaran',
        //         groupIcon: 'fa-scale-balanced',
        //         code: 'DOC-ADM-02',
        //         title: 'Dokumen administrasi pembelajaran',
        //         url: 'https://drive.google.com/file/d/2B7y8X_Kurikulum_PAUD/view',
        //         score: 4,
        //         status: 'Disetujui',
        //         notes: 'RPPM dan RPPH sudah sangat lengkap dan sesuai standar kurikulum merdeka.',
        //         lastUpdated: '20 Jul 2026, 16:45 WIB',
        //         history: [{
        //             adminName: 'Drs. Ahmad Fauzi, M.Pd',
        //             timestamp: '20 Jul 2026, 16:45 WIB',
        //             status: 'Disetujui',
        //             score: 4,
        //             notes: 'Kurikulum pembelajaran terstruktur rapi.',
        //             url: 'https://drive.google.com/file/d/2B7y8X_Kurikulum_PAUD/view'
        //         }]
        //     },
        //     {
        //         id: 3,
        //         group: 'GROUP II',
        //         groupName: 'Dokumen Mutu Layanan PAUD',
        //         groupIcon: 'fa-award',
        //         code: 'DOC-MUT-03',
        //         title: 'Dokumen mutu layanan PAUD',
        //         url: 'https://drive.google.com/file/d/3C6w7V_MutuLayanan/view',
        //         score: null,
        //         status: 'Pending',
        //         notes: '',
        //         lastUpdated: '18 Jul 2026, 09:00 WIB',
        //         history: [{
        //             adminName: 'System Auto-Import',
        //             timestamp: '18 Jul 2026, 09:00 WIB',
        //             status: 'Pending',
        //             score: null,
        //             notes: 'Dokumen baru diunggah oleh peserta.',
        //             url: 'https://drive.google.com/file/d/3C6w7V_MutuLayanan/view'
        //         }]
        //     },
        //     {
        //         id: 4,
        //         group: 'GROUP III',
        //         groupName: 'Dokumen Sarana, Prasarana & PTK',
        //         groupIcon: 'fa-building-columns',
        //         code: 'DOC-SAR-04',
        //         title: 'Dokumen sarana dan prasarana',
        //         url: 'https://drive.google.com/file/d/4D5v6U_Sarpras/view',
        //         score: 2,
        //         status: 'Ditolak',
        //         notes: 'Foto pendukung ketersediaan alat permainan edukatif (APE) belum jelas.',
        //         lastUpdated: '19 Jul 2026, 11:20 WIB',
        //         history: [{
        //             adminName: 'Siti Badriah, S.T.',
        //             timestamp: '19 Jul 2026, 11:20 WIB',
        //             status: 'Ditolak',
        //             score: 2,
        //             notes: 'Foto lokasi kurang memadai dan tidak mencantumkan denah bangunan.',
        //             url: 'https://drive.google.com/file/d/4D5v6U_Sarpras/view'
        //         }]
        //     },
        //     {
        //         id: 5,
        //         group: 'GROUP III',
        //         groupName: 'Dokumen Sarana, Prasarana & PTK',
        //         groupIcon: 'fa-building-columns',
        //         code: 'DOC-PTK-05',
        //         title: 'Dokumen pendidik dan tenaga kependidikan',
        //         url: 'https://drive.google.com/file/d/5E4u5T_Pendidik/view',
        //         score: 4,
        //         status: 'Disetujui',
        //         notes: 'Ijazah S1 PAUD seluruh guru terverifikasi valid.',
        //         lastUpdated: '21 Jul 2026, 08:30 WIB',
        //         history: [{
        //             adminName: 'Drs. Ahmad Fauzi, M.Pd',
        //             timestamp: '21 Jul 2026, 08:30 WIB',
        //             status: 'Disetujui',
        //             score: 4,
        //             notes: 'Kualifikasi pendidik sangat memuaskan.',
        //             url: 'https://drive.google.com/file/d/5E4u5T_Pendidik/view'
        //         }]
        //     },
        //     {
        //         id: 6,
        //         group: 'GROUP IV',
        //         groupName: 'Dokumen Keuangan & Inovasi',
        //         groupIcon: 'fa-wallet',
        //         code: 'DOC-KEU-06',
        //         title: 'Dokumen administrasi keuangan',
        //         url: 'https://drive.google.com/file/d/6F3t4S_Keuangan/view',
        //         score: null,
        //         status: 'Pending',
        //         notes: '',
        //         lastUpdated: '17 Jul 2026, 14:10 WIB',
        //         history: [{
        //             adminName: 'System Auto-Import',
        //             timestamp: '17 Jul 2026, 14:10 WIB',
        //             status: 'Pending',
        //             score: null,
        //             notes: 'Berkas diterima dari peserta.',
        //             url: 'https://drive.google.com/file/d/6F3t4S_Keuangan/view'
        //         }]
        //     },
        //     {
        //         id: 7,
        //         group: 'GROUP IV',
        //         groupName: 'Dokumen Keuangan & Inovasi',
        //         groupIcon: 'fa-wallet',
        //         code: 'DOC-INO-07',
        //         title: 'Dokumen inovasi, program unggulan, dan praktik baik',
        //         url: 'https://drive.google.com/file/d/7G2s3R_Inovasi/view',
        //         score: 3,
        //         status: 'Disetujui',
        //         notes: 'Program kebun sekolah anak terbukti meningkatkan motorik.',
        //         lastUpdated: '20 Jul 2026, 13:00 WIB',
        //         history: [{
        //             adminName: 'Drs. Ahmad Fauzi, M.Pd',
        //             timestamp: '20 Jul 2026, 13:00 WIB',
        //             status: 'Disetujui',
        //             score: 3,
        //             notes: 'Inovasi bagus dan berkelanjutan.',
        //             url: 'https://drive.google.com/file/d/7G2s3R_Inovasi/view'
        //         }]
        //     },
        //     {
        //         id: 8,
        //         group: 'GROUP V',
        //         groupName: 'Proposal Keikutsertaan Lembaga',
        //         groupIcon: 'fa-file-signature',
        //         code: 'PRP-KEI-08',
        //         title: 'Proposal Keikutsertaan Lembaga',
        //         url: 'https://drive.google.com/file/d/8H1r2Q_ProposalLembaga/view',
        //         score: 4,
        //         status: 'Disetujui',
        //         notes: 'Format susunan proposal memenuhi kriteria petunjuk teknis.',
        //         lastUpdated: '21 Jul 2026, 09:40 WIB',
        //         history: [{
        //             adminName: 'Drs. Ahmad Fauzi, M.Pd',
        //             timestamp: '21 Jul 2026, 09:40 WIB',
        //             status: 'Disetujui',
        //             score: 4,
        //             notes: 'Proposal lengkap dengan tanda tangan basah dan stempel.',
        //             url: 'https://drive.google.com/file/d/8H1r2Q_ProposalLembaga/view'
        //         }]
        //     }
        // ];

        const initialDocuments = {!! $jenis_berkas !!}

        // let storedData = JSON.parse(localStorage.getItem('veridoc_js_documents'));
        // Migrasi atau muat data jika belum terdapat properti group
        let documents = initialDocuments;
        let currentFilter = 'Semua';
        let currentSearch = '';

        setFilter('Semua');

        // function saveState() {
        //     localStorage.setItem('veridoc_js_documents', JSON.stringify(documents));
        // }

        function renderDocuments() {
            @if (empty($paud_mutu))
                return false;
            @else
                const listEl = document.getElementById('documentList');
                const emptyEl = document.getElementById('emptyNotice');
                listEl.innerHTML = '';

                let visibleCount = 0;

                // Definisi kelompok judul GROUP
                // const groupsDefinition = [{
                //         id: 'GROUP I',
                //         name: 'Dokumen Legalitas & Administrasi Pembelajaran',
                //         icon: 'fa-scale-balanced'
                //     },
                //     {
                //         id: 'GROUP II',
                //         name: 'Dokumen Mutu Layanan PAUD',
                //         icon: 'fa-award'
                //     },
                //     {
                //         id: 'GROUP III',
                //         name: 'Dokumen Sarana, Prasarana & PTK',
                //         icon: 'fa-building-columns'
                //     },
                //     {
                //         id: 'GROUP IV',
                //         name: 'Dokumen Keuangan & Inovasi',
                //         icon: 'fa-wallet'
                //     },
                //     {
                //         id: 'GROUP V',
                //         name: 'Proposal Keikutsertaan Lembaga',
                //         icon: 'fa-file-signature'
                //     }
                // ];

                const groupsDefinition = {!! $group_berkas !!};

                groupsDefinition.forEach(grp => {
                    // Filter dokumen dalam group ini
                    const groupDocs = documents.filter(doc => {
                        if (doc.group !== grp.group) return false;
                        const matchesSearch = doc.nama_berkas.toLowerCase().includes(currentSearch);
                        const matchesFilter = (currentFilter === 'Semua') || (doc.status_berkas ===
                            currentFilter);
                        return matchesSearch && matchesFilter;
                    });

                    if (groupDocs.length === 0) return;

                    visibleCount += groupDocs.length;

                    // Hitung statistik per group
                    const approvedCount = groupDocs.filter(d => d.status === 'Disetujui').length;
                    const pendingCount = groupDocs.filter(d => d.status === 'Pending').length;
                    const rejectedCount = groupDocs.filter(d => d.status === 'Ditolak').length;

                    // Elemen Header GROUP
                    const groupHeader = document.createElement('div');
                    groupHeader.className =
                        "pt-4 pb-2 mb-3 mt-6 first:mt-0 border-b-2 border-brand-500/20 flex flex-col sm:flex-row sm:items-center justify-between gap-2";
                    groupHeader.innerHTML = `
<div class="flex items-center space-x-3">
<h3 class="font-bold text-slate-800 text-base sm:text-lg flex items-center gap-2">
<i class="fa-solid fa-book-open text-brand-600 text-sm"></i>
${escapeHtml(grp.group)}
</h3>
</div>
<div class="flex items-center space-x-2 text-[11px] font-semibold text-slate-500 self-start sm:self-auto">
<span class="bg-slate-100 px-2.5 py-1 rounded-md border border-slate-200">${groupDocs.length} Berkas</span>
${approvedCount > 0 ? `<span class="bg-emerald-50 text-emerald-700 border border-emerald-200/80 px-2 py-0.5 rounded-md"><i class="fa-solid fa-check text-[9px] mr-1"></i>${approvedCount}</span>` : ''}
${pendingCount > 0 ? `<span class="bg-amber-50 text-amber-700 border border-amber-200/80 px-2 py-0.5 rounded-md"><i class="fa-solid fa-hourglass-half text-[9px] mr-1"></i>${pendingCount}</span>` : ''}
${rejectedCount > 0 ? `<span class="bg-rose-50 text-rose-700 border border-rose-200/80 px-2 py-0.5 rounded-md"><i class="fa-solid fa-xmark text-[9px] mr-1"></i>${rejectedCount}</span>` : ''}
</div>
`;
                    listEl.appendChild(groupHeader);

                    // Build Document Card untuk setiap item di dalam group
                    groupDocs.forEach(doc => {
                        const card = createDocumentCard(doc);
                        listEl.appendChild(card);
                    });
                });

                if (visibleCount === 0) {
                    emptyEl.classList.remove('hidden');
                } else {
                    emptyEl.classList.add('hidden');
                }

                updateSummaryCounters();
            @endif
        }

        function createDocumentCard(doc) {
            const card = document.createElement('div');
            card.className =
                "bg-white rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition-all duration-200 overflow-hidden mb-4";

            const badgeClass = getBadgeClass(doc.status_berkas);
            const dotClass = getDotClass(doc.status_berkas);

            card.innerHTML = `
<!-- Header Row -->
<div class="p-5 flex flex-col lg:flex-row lg:items-center justify-between gap-4 bg-white border-b border-slate-100">
  <div class="flex items-center space-x-3.5">
                        <div class="w-11 h-11 rounded-xl bg-blue-50 text-brand-600 flex items-center justify-center flex-shrink-0 border border-blue-100">
                            <i class="fa-regular fa-file-lines text-lg"></i>
                        </div>
                        <div>
                            <!-- Dibuat clickable dengan onclick openDocModal -->
                            <h3
                              onclick="openDocModal('${escapeHtml(doc.nama_berkas)}', '${btoa(doc.opsi1 || '')}', '${btoa(doc.opsi2 || '')}', '${btoa(doc.opsi3 || '')}', '${escapeHtml(doc.nilai1 || '')}', '${escapeHtml(doc.nilai2 || '')}', '${escapeHtml(doc.nilai3 || '')}', '${escapeHtml(doc.nilai4 || '')}', '${escapeHtml(doc.lastUpdated)}')"
                              class="font-semibold text-slate-800 text-base flex items-center gap-2 cursor-pointer hover:text-brand-600 transition-colors group"
                            >
                              <span>${escapeHtml(doc.nama_berkas)}</span>
                              <i class="fa-solid fa-circle-info text-xs text-slate-400 group-hover:text-brand-600 transition-colors"></i>
                            </h3>
                            <p class="text-xs text-slate-400 mt-0.5 flex items-center gap-2">
                              <span>Diubah: ${escapeHtml(doc.lastUpdated)}</span>
                            </p>
                        </div>
                    </div>

                    <!-- Status Badge & History Button -->
                    <div class="flex items-center space-x-3 self-end lg:self-auto">
                        <span class="${badgeClass} px-3 py-1 rounded-full text-xs font-semibold inline-flex items-center gap-1.5 border">
                            <span class="w-1.5 h-1.5 rounded-full ${dotClass}"></span>
                            <span>${escapeHtml(doc.status_berkas)}</span>
                        </span>

                        <button type="button" onclick="openHistoryModal(${doc.id})"
                                class="px-3 py-1.5 rounded-xl border border-slate-200 text-slate-600 hover:text-brand-600 hover:border-brand-200 hover:bg-brand-50 text-xs font-medium transition-all flex items-center gap-1.5">
                            <i class="fa-solid fa-clock-rotate-left text-slate-400"></i>
                            <span>Riwayat Verifikasi</span>
                            <span class="bg-slate-100 text-slate-600 px-1.5 py-0.2 text-[10px] rounded-full font-bold">
                                ${doc.riwayat.length}
                            </span>
                        </button>
                    </div>
                </div>


<!-- Evaluation Inputs Row -->
<div class="p-5 bg-slate-50/50 grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
  <!-- 1. URL File Input -->
  <div class="md:col-span-3">
    <label class="block text-xs font-medium text-slate-600 mb-1.5 flex items-center justify-between">
      <span><i class="fa-solid fa-link text-slate-400 mr-1"></i> URL Tautan Berkas</span>
      ${doc.url ? `
                              <a href="${escapeHtml(doc.url)}" target="_blank" class="text-brand-600 hover:underline text-[11px] font-medium flex items-center">
                                Buka File <i class="fa-solid fa-arrow-up-right-from-square text-[9px] ml-1"></i>
                              </a>
                            ` : ''}
    </label>
    <div class="relative flex items-center">
      <input type="url" id="url-${doc.id}" value="${escapeHtml(doc.url || '')}"
        placeholder="https://drive.google.com/..."
        class="w-full pr-10 pl-3 py-2 bg-white border border-slate-200 rounded-xl text-xs text-slate-700 focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition-all">
      <button type="button" onclick="copyToClipboard('${escapeHtml(doc.url || '')}')"
        class="absolute right-2 text-slate-400 hover:text-slate-600 p-1 rounded-md text-xs"
        title="Salin Tautan">
        <i class="fa-regular fa-copy"></i>
      </button>
    </div>
  </div>

    <!-- 2. Score Selector (1-4) -->
    <div class="md:col-span-3">
        <label class="block text-xs font-medium text-slate-600 mb-1.5">
            <i class="fa-solid fa-star text-amber-400 mr-1"></i> Penilaian (Skor 1-4)
        </label>
        <div class="grid grid-cols-4 gap-1 bg-white p-1 rounded-xl border border-slate-200">
            ${[1, 2, 3, 4].map(scoreNum => `
                                <button type="button" onclick="setDocScore(${doc.id}, ${scoreNum})"
                                        class="py-1.5 rounded-lg text-xs font-bold transition-all text-center flex items-center justify-center ${doc.score === scoreNum ? getScoreBtnClass(scoreNum) : 'text-slate-500 hover:bg-slate-100'}">
                                    ${scoreNum}
                                </button>
                            `).join('')}
        </div>
    </div>

    <!-- 3. Status Dropdown -->
    <div class="md:col-span-2">
        <label class="block text-xs font-medium text-slate-600 mb-1.5">
            <i class="fa-solid fa-list-check text-slate-400 mr-1"></i> Status Verifikasi
        </label>
        <select id="status-${doc.id}"
                class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs text-slate-700 font-medium focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition-all cursor-pointer">
            <option value="Pending" ${doc.status_berkas === 'Pending' ? 'selected' : ''}>⏳ Pending</option>
            <option value="Disetujui" ${doc.status_berkas === 'Disetujui' ? 'selected' : ''}>✅ Disetujui</option>
            <option value="Ditolak" ${doc.status_berkas === 'Ditolak' ? 'selected' : ''}>❌ Ditolak</option>
        </select>
    </div>

  <!-- Submit Save Action Button -->
  <div class="md:col-span-3 flex justify-end">
    <button type="button" onclick="saveDocument(${doc.id})"
      class="w-full bg-slate-800 hover:bg-slate-900 text-white font-medium py-2 px-4 rounded-xl text-xs shadow-sm hover:shadow transition-all flex items-center justify-center space-x-1.5">
      <i class="fa-solid fa-floppy-disk text-xs"></i>
      <span>Simpan Hasil Verifikasi</span>
    </button>
  </div>

  <!-- 4. Notes Form Field -->
  <div class="md:col-span-12 mt-1">
    <label class="block text-xs font-medium text-slate-600 mb-1">
      <i class="fa-regular fa-comment-dots text-slate-400 mr-1"></i> Catatan Peserta
    </label>
    <input type="text" id="notes-${doc.id}" value="${escapeHtml(doc.catatan || '')}"
      placeholder="Tambahkan catatan revisi, kelengkapan, atau alasan penolakan/persetujuan..."
      class="w-full px-3.5 py-2 bg-white border border-slate-200 rounded-xl text-xs text-slate-700 focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition-all">
  </div>


  <!-- 4. Notes Form Field -->
  <div class="md:col-span-12 mt-1">
      <label class="block text-xs font-medium text-slate-600 mb-1">
          <i class="fa-regular fa-comment-dots text-slate-400 mr-1"></i> Narasi implementasi/dampak
      </label>
      <textarea id="dampak-${doc.id}"
          class="w-full px-3.5 py-2 bg-white border border-slate-200 rounded-xl text-xs text-slate-700 focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition-all"
          placeholder="Narasi implementasi/dampak">${doc.narasi}</textarea>
  </div>
</div>
`;
            return card;
        }

        function updateSummaryCounters() {
            const total = documents.length;
            const pending = documents.filter(d => d.status_berkas === 'Pending').length;
            const approved = documents.filter(d => d.status_berkas === 'Disetujui').length;
            const rejected = documents.filter(d => d.status_berkas === 'Ditolak').length;
            const belum = documents.filter(d => d.status_berkas === 'Belum ada').length;

            document.getElementById('statTotal').innerText = total;
            document.getElementById('statPending').innerText = pending;
            document.getElementById('statApproved').innerText = approved;
            document.getElementById('statRejected').innerText = rejected;
            document.getElementById('statBelum').innerText = belum;

            document.getElementById('cntSemua').innerText = total;
            document.getElementById('cntPending').innerText = pending;
            document.getElementById('cntDisetujui').innerText = approved;
            document.getElementById('cntDitolak').innerText = rejected;
            document.getElementById('cntBelum').innerText = belum;
        }

        function setDocScore(id, score) {
            const doc = documents.find(d => d.id === id);
            if (doc) {
                doc.score = score;
                renderDocuments();
            }
        }

        function saveDocument(id) {
            const doc = documents.find(d => d.id === id);
            if (!doc) return;

            const urlInput = document.getElementById(`url-${id}`).value.trim();
            const statusInput = document.getElementById(`status-${id}`).value;
            const notesInput = document.getElementById(`notes-${id}`).value.trim();
            const dampakInput = document.getElementById(`dampak-${id}`).value.trim();

            const now = new Date();
            const timeStr = now.toLocaleDateString('id-ID', {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric'
                }) +
                ', ' + now.toLocaleTimeString('id-ID', {
                    hour: '2-digit',
                    minute: '2-digit'
                }) + ' WIB';

            // Update document values
            doc.url = urlInput;
            doc.status = statusInput;
            doc.notes = notesInput;
            doc.lastUpdated = timeStr;
            doc.dampak = dampakInput;

            // Push to audit history log
            doc.riwayat.unshift({
                adminName: 'Sekolah',
                timestamp: timeStr,
                status: statusInput,
                score: doc.score,
                notes: notesInput !== '' ? notesInput : '-',
                url: urlInput
            });

            var data_simpan = {
                status: statusInput,
                url: urlInput,
                notes: notesInput !== '' ? notesInput : '-',
                score: doc.score,
                berkas_id: doc.id,
                pendaftar_id: '{{ $paud_mutu->id ?? '' }}',
                dampak: dampakInput,
            };

            const url_simpan_berkas = '{{ route('paud_mutu-tahap2', ['halaman' => 'simpan_berkas']) }}';

            if (urlInput == '') {
                showToast(`Link berkas "<b>${doc.nama_berkas}</b>" Harus diisi!`, 'error');
            } else {
                postData(url_simpan_berkas, data_simpan)
                    .then(data => {
                        if (data.code == 200) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: 'Data berhasil disimpan',
                            }).then(result => {
                                location.reload();
                            })
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Mohon maaf',
                            text: '500 - Internal server error' + error
                        });
                    });

                // saveState();
                renderDocuments();
                showToast(`Hasil verifikasi "${doc.nama_berkas}" berhasil disimpan!`);
            }
        }

        function setFilter(filter) {
            currentFilter = filter;

            // Update Active Filter UI
            document.querySelectorAll('.filter-btn').forEach(btn => {
                btn.className =
                    'filter-btn px-4 py-2 rounded-xl text-xs font-medium transition-all flex items-center whitespace-nowrap bg-white text-slate-600 hover:bg-slate-50';
            });

            const activeBtn = document.getElementById(`btn-${filter}`);
            if (activeBtn) {
                activeBtn.className =
                    'filter-btn px-4 py-2 rounded-xl text-xs font-medium transition-all flex items-center whitespace-nowrap bg-blue-600 text-white shadow-sm';
            }

            renderDocuments();
        }

        function handleSearch() {
            currentSearch = document.getElementById('searchInput').value.toLowerCase().trim();
            renderDocuments();
        }

        function toggleGroupInfo() {
            const panel = document.getElementById('groupInfoPanel');
            panel.classList.toggle('hidden');
        }

        function openHistoryModal(id) {
            const doc = documents.find(d => d.id === id);
            // console.log(doc);
            if (!doc) return;

            document.getElementById('modalDocTitle').innerText = doc.nama_berkas;

            // Status badge modal
            const statusEl = document.getElementById('modalDocStatus');
            statusEl.innerText = doc.status_berkas;
            statusEl.className =
                `ml-2 px-2.5 py-0.5 rounded-full text-xs font-semibold ${getBadgeClass(doc.status_berkas)}`;

            // Score & Count
            document.getElementById('modalDocScore').innerText = doc.score ? `${doc.score} / 4` : 'Belum Dinilai';
            document.getElementById('modalAuditCount').innerText = `Total Audit: ${doc.riwayat.length} Rekam Jejak`;

            // Render Timeline Items
            const timelineEl = document.getElementById('modalTimeline');
            timelineEl.innerHTML = '';

            doc.riwayat.forEach(item => {
                const itemDiv = document.createElement('div');
                itemDiv.className = 'relative group';

                const dotBg = getHistoryDotBg(item.status);
                const iconClass = getHistoryIcon(item.status);

                itemDiv.innerHTML = `
<div class="absolute -left-6 top-1 w-5 h-5 rounded-full border-2 border-white flex items-center justify-center text-[10px] ${dotBg}">
<i class="${iconClass} text-white text-[8px]"></i>
</div>

<div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
<div class="flex flex-col sm:flex-row sm:items-center justify-between text-xs gap-1 mb-2">
<div class="font-semibold text-slate-800 flex items-center gap-1.5">
<i class="fa-solid fa-user-shield text-slate-400"></i>
${escapeHtml(item.created_by)}
</div>
<div class="text-slate-400 text-[11px] flex items-center gap-1">
<i class="fa-regular fa-clock"></i> ${escapeHtml(item.created_at)}
</div>
</div>

<div class="flex items-center space-x-3 mb-2 text-xs">
<span class="text-slate-500">Status: <strong class="${getHistoryTextClass(item.status_berkas)}">${escapeHtml(item.status_berkas)}</strong></span>
<span class="text-slate-300">&bull;</span>
</div>

${item.catatan ? `
                                                                                                                                                                <div class="bg-slate-50 p-2.5 rounded-lg border border-slate-100 text-xs text-slate-600">
                                                                                                                                                                <span class="font-semibold text-slate-500 block text-[10px] uppercase tracking-wider mb-0.5">${item.created_by=='Sekolah' ? 'Catatan Pengajuan' : 'Catatan Verifikator'}:</span>
                                                                                                                                                                "${escapeHtml(item.catatan)}"
                                                                                                                                                                </div>
                                                                                                                                                                ` : ''}

${item.url_berkas ? `
                                                                                                                                                                <div class="mt-2 text-[11px] text-slate-400 flex items-center gap-1">
                                                                                                                                                                <i class="fa-solid fa-paperclip"></i>
                                                                                                                                                                <span>Tautan saat ditinjau:</span>
                                                                                                                                                                <a href="${escapeHtml(item.url_berkas)}" target="_blank" class="text-brand-600 hover:underline truncate max-w-[200px] inline-block">
                                                                                                                                                                ${escapeHtml(item.url_berkas)}
                                                                                                                                                                </a>
                                                                                                                                                                </div>
                                                                                                                                                                ` : ''}
</div>
`;
                timelineEl.appendChild(itemDiv);
            });

            document.getElementById('historyModal').classList.remove('hidden');
        }

        function closeHistoryModal() {
            document.getElementById('historyModal').classList.add('hidden');
        }

        function getBadgeClass(status) {
            switch (status) {
                case 'Pending':
                    return 'bg-amber-50 text-amber-700 border-amber-200/80';
                case 'Disetujui':
                    return 'bg-emerald-50 text-emerald-700 border-emerald-200/80';
                case 'Ditolak':
                    return 'bg-rose-50 text-rose-700 border-rose-200/80';
                default:
                    return 'bg-slate-100 text-slate-600 border-slate-200';
            }
        }

        function getDotClass(status) {
            switch (status) {
                case 'Pending':
                    return 'bg-amber-500';
                case 'Disetujui':
                    return 'bg-emerald-500';
                case 'Ditolak':
                    return 'bg-rose-500';
                default:
                    return 'bg-slate-400';
            }
        }

        function getScoreBtnClass(score) {
            switch (score) {
                case 1:
                    return 'bg-rose-500 text-white shadow-xs';
                case 2:
                    return 'bg-amber-500 text-white shadow-xs';
                case 3:
                    return 'bg-blue-500 text-white shadow-xs';
                case 4:
                    return 'bg-emerald-600 text-white shadow-xs';
                default:
                    return 'bg-brand-600 text-white';
            }
        }

        function getHistoryDotBg(status) {
            switch (status) {
                case 'Pending':
                    return 'bg-amber-500';
                case 'Disetujui':
                    return 'bg-emerald-500';
                case 'Ditolak':
                    return 'bg-rose-500';
                default:
                    return 'bg-slate-400';
            }
        }

        function getHistoryIcon(status) {
            switch (status) {
                case 'Pending':
                    return 'fa-solid fa-hourglass-half';
                case 'Disetujui':
                    return 'fa-solid fa-check';
                case 'Ditolak':
                    return 'fa-solid fa-xmark';
                default:
                    return 'fa-solid fa-circle';
            }
        }

        function getHistoryTextClass(status) {
            switch (status) {
                case 'Pending':
                    return 'text-amber-600';
                case 'Disetujui':
                    return 'text-emerald-600';
                case 'Ditolak':
                    return 'text-rose-600';
                default:
                    return 'text-slate-600';
            }
        }

        function escapeHtml(str) {
            if (!str) return '';
            return str.replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }

        function copyToClipboard(text) {
            if (!text) return;
            const el = document.createElement('textarea');
            el.value = text;
            document.body.appendChild(el);
            el.select();
            document.execCommand('copy');
            document.body.removeChild(el);
            showToast('Tautan URL berhasil disalin ke clipboard!');
        }

        function showToast(message, type = 'success') {
            const container = document.getElementById('toastContainer');
            const toast = document.createElement('div');
            toast.className =
                'pointer-events-auto flex items-center p-4 text-slate-700 bg-white rounded-xl shadow-xl border border-slate-100 space-x-3 max-w-md transition-all duration-300 transform translate-y-2 opacity-0';
            if (type == 'success') {
                toast.innerHTML = `
<div class="bg-emerald-100 text-emerald-600 p-2 rounded-lg flex-shrink-0">
<i class="fa-solid fa-circle-check text-lg"></i>
</div>
<div class="text-sm font-medium">${message}</div>
`;
            } else {
                toast.innerHTML = `
<div class="bg-red-100 text-red-600 p-2 rounded-lg flex-shrink-0">
<i class="fa-solid fa-x-twitter text-lg">X</i>
</div>
<div class="text-sm font-medium">${message}</div>
`;
            }
            container.appendChild(toast);

            setTimeout(() => {
                toast.classList.remove('translate-y-2', 'opacity-0');
            }, 10);

            setTimeout(() => {
                toast.classList.add('opacity-0', 'translate-y-2');
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }

        function pendaftaran() {
            Swal.fire({
                icon: 'warning',
                title: 'Terima kasih sudah berpartisipasi',
                text: 'Selanjutnya melengkapi berkas yang dibutuhkan',
                showCancelButton: false,
                confirmButtonText: 'Ya',
                cancelButtonText: 'Tidak',
            }).then(result => {
                if (result.isConfirmed) {
                    const url_daftar = '{{ route('paud_mutu-tahap2', ['halaman' => 'pendaftaran']) }}';
                    const data = {};

                    postData(url_daftar, data)
                        .then(data => {
                            if (data.code == 200) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: 'Data berhasil disimpan',
                                }).then(result => {
                                    location.reload();
                                })
                            }
                        })
                        .catch(error => {
                            Swal.fire({
                                icon: 'error',
                                title: 'Mohon maaf',
                                text: '500 - Internal server error' + error
                            });
                        });
                }
            })
        }

        function openDocModal(nama, opsi1, opsi2, opsi3, nilai1, nilai2, nilai3, nilai4, lastUpdated) {
            // Set data ke dalam modal
            document.getElementById('modalNamaBerkas').innerText = nama || '-';
            document.getElementById('modalLastUpdated').innerText = lastUpdated ? `Diubah: ${lastUpdated}` : '';
            document.getElementById('opsi1Modal').innerHtml = btoa(opsi1) || '-';
            document.getElementById('opsi2Modal').innerHtml = btoa(opsi2) || '-';
            document.getElementById('opsi3Modal').innerHtml = btoa(opsi2) || '-';
            document.getElementById('nilai1Modal').innerText = nilai1 || '-';
            document.getElementById('nilai2Modal').innerText = nilai2 || '-';
            document.getElementById('nilai3Modal').innerText = nilai3 || '-';
            document.getElementById('nilai4Modal').innerText = nilai4 || '-';

            // Tampilkan Modal
            document.getElementById('docModal').classList.remove('hidden');

            updateModalContent('opsi1Modal', opsi1);
            updateModalContent('opsi2Modal', opsi2);
            updateModalContent('opsi3Modal', opsi3);
        }

        function closeDocModal() {
            document.getElementById('docModal').classList.add('hidden');
        }

        function updateModalContent(id, base64String) {
            const el = document.getElementById(id);
            if (!el) return; // Mencegah error jika elemen tidak ditemukan

            if (!base64String) {
                el.innerHTML = '-';
                return;
            }

            try {
                el.innerHTML = atob(base64String);
            } catch (err) {
                // Jika ternyata string biasa (bukan base64)
                el.innerHTML = base64String;
            }
        }

        // Initialize application render on DOM load
        document.addEventListener('DOMContentLoaded', () => {
            renderDocuments();
        });
    </script>
@endsection
