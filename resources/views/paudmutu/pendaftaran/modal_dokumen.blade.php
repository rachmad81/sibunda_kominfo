<!-- Modal Detail Berkas -->
<div id="docModal"
    class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/40 backdrop-blur-sm flex items-center justify-center p-4">
    <div
        class="bg-white rounded-2xl max-w-4xl w-full shadow-xl border border-slate-100 overflow-hidden transform transition-all">
        <!-- Header Modal -->
        <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <div class="flex items-center space-x-3">
                <div class="w-9 h-9 rounded-lg bg-blue-50 text-brand-600 flex items-center justify-center flex-shrink-0">
                    <i class="fa-regular fa-file-lines text-base"></i>
                </div>
                <div>
                    <h4 class="font-semibold text-slate-800 text-sm" id="modalNamaBerkas"></h4>
                    <p class="text-[11px] text-slate-400" id="modalLastUpdated"></p>
                </div>
            </div>
            <button onclick="closeDocModal()"
                class="text-slate-400 hover:text-slate-600 w-8 h-8 rounded-lg flex items-center justify-center hover:bg-slate-100 transition-all">
                <i class="fa-solid fa-xmark text-base"></i>
            </button>
        </div>

        <!-- Body Modal -->
        <div class="p-5 space-y-4">
            <div>
                <span class="text-xs font-semibold text-slate-800 block mb-1">Keterangan</span>
                <p id="opsi1Modal" class="text-sm text-slate-400"></p>
            </div>

            <div>
                <span class="text-xs font-semibold text-slate-800 block mb-1">Bukti Wajib</span>
                <p id="opsi2Modal" class="text-sm text-slate-400"></p>
            </div>
        </div>

        <!-- Footer Modal -->
        <div class="p-4 border-t border-slate-100 bg-slate-50/50 flex justify-end">
            <button onclick="closeDocModal()"
                class="px-4 py-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-medium text-xs rounded-xl transition-all">
                Tutup
            </button>
        </div>
    </div>
</div>
