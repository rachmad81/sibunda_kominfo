<!-- MODAL RIWAYAT VERIFIKASI -->
<div id="historyModal"
    class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">
    <div
        class="bg-white rounded-2xl max-w-2xl w-full shadow-2xl border border-slate-100 overflow-hidden transform transition-all">

        <!-- Modal Header -->
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <div class="flex items-center space-x-3">
                <div class="w-9 h-9 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                </div>
                <div>
                    <h3 class="font-bold text-slate-800 text-base">Riwayat Verifikasi</h3>
                    <p id="modalDocTitle" class="text-xs text-slate-500 truncate max-w-xs sm:max-w-md">-</p>
                </div>
            </div>
            <button type="button" onclick="closeHistoryModal()"
                class="text-slate-400 hover:text-slate-600 p-2 rounded-xl hover:bg-slate-100 transition-colors">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <!-- Modal Body Timeline -->
        <div class="p-6 max-h-[60vh] overflow-y-auto custom-scrollbar">

            <div class="flex items-center justify-between mb-4 bg-slate-50 p-3 rounded-xl border border-slate-100">
                <div>
                    <span class="text-xs text-slate-500">Status Saat Ini:</span>
                    <span id="modalDocStatus" class="ml-2 px-2.5 py-0.5 rounded-full text-xs font-semibold">
                        -
                    </span>
                </div>
                <div>
                    <span class="text-xs text-slate-500">Nilai Akhir:</span>
                    <span id="modalDocScore"
                        class="ml-2 font-bold text-slate-800 bg-white px-2 py-0.5 rounded-md border text-xs">
                        -
                    </span>
                </div>
            </div>

            <!-- Timeline List Container -->
            <div id="modalTimeline"
                class="relative pl-6 space-y-6 before:absolute before:left-2.5 before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-200">
                <!-- Populated via JavaScript -->
            </div>

        </div>

        <!-- Modal Footer -->
        <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between">
            <span id="modalAuditCount" class="text-xs text-slate-400">Total Audit: 0 Rekam Jejak</span>
            <button type="button" onclick="closeHistoryModal()"
                class="px-4 py-2 bg-white border border-slate-200 text-slate-700 hover:bg-slate-100 font-medium text-xs rounded-xl transition-all">
                Tutup Modal
            </button>
        </div>

    </div>
</div>
