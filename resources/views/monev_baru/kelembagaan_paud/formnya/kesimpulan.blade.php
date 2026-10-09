<form onsubmit="simpan('form_kesimpulan')" class="form_kesimpulan">
    <!-- KESIMPULAN -->
    <div class="conclusion-card" id="kesimpulan">
        <h2>
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M9 11l3 3L22 4" />
                <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11" />
            </svg>
            Kesimpulan &amp; Rekomendasi Tindak Lanjut
        </h2>

        @php
            $nik_kelurahan = $bunda_kelurahan->nik ?? '';
            $nik_kecamatan = $bunda_kecamatan->nik ?? '';
            $ttd_kelurahan = '';
            $ttd_kecamatan = '';
            $stempel = $sekolah->stempel ?? '';
            $stempel = $stempel != '' ? 'https://dispendik.surabaya.go.id/profilsekolah_file/stempel/' . $stempel : '';

            if ($nik_kelurahan != '') {
                $ttd_kelurahan = collect($ttd_bunda)->where('nik', $nik_kelurahan)->first()->link_ttd ?? '';
            }
            if ($nik_kecamatan != '') {
                $ttd_kecamatan = collect($ttd_bunda)->where('nik', $nik_kecamatan)->first()->link_ttd ?? '';
            }
            $data_value = [
                'kesimpulan_rekomendasi' => '',
                'rekomendasi_tindak_lanjut' => '',
                'nama_satuan' => $sekolah->nama_lembaga ?? '',
                'nama_ks' => $data_ks->nama ?? '',
                'kelurahan' => $bunda_kelurahan->kelurahan_dispenduk ?? '',
                'nama_bunda_kelurahan' => $bunda_kelurahan->nama ?? '',
                'kecamatan' => $bunda_kecamatan->kecamatan_dispenduk ?? '',
                'nama_bunda_kecamatan' => $bunda_kecamatan->nama ?? '',
                'nik_bunda_kelurahan' => $nik_kelurahan,
                'ttd_bunda_kelurahan' => $ttd_kelurahan,
                'nik_bunda_kecamatan' => $nik_kecamatan,
                'ttd_bunda_kecamatan' => $ttd_kecamatan,
                'ttd_ks' => $stempel,
            ];

            if (!empty($monev)) {
                $data_value = [
                    'kesimpulan_rekomendasi' => !empty($monev->kesimpulan_rekomendasi)
                        ? $monev->kesimpulan_rekomendasi
                        : $data_value['kesimpulan_rekomendasi'],
                    'rekomendasi_tindak_lanjut' => !empty($monev->rekomendasi_tindak_lanjut)
                        ? $monev->rekomendasi_tindak_lanjut
                        : $data_value['rekomendasi_tindak_lanjut'],
                    'nama_satuan' => !empty($monev->nama_satuan) ? $monev->nama_satuan : $data_value['nama_satuan'],
                    'nama_ks' => !empty($monev->nama_ks) ? $monev->nama_ks : $data_value['nama_ks'],
                    'kelurahan' => !empty($monev->kelurahan) ? $monev->kelurahan : $data_value['kelurahan'],
                    'nama_bunda_kelurahan' => !empty($monev->nama_bunda_kelurahan)
                        ? $monev->nama_bunda_kelurahan
                        : $data_value['nama_bunda_kelurahan'],
                    'kecamatan' => !empty($monev->kecamatan) ? $monev->kecamatan : $data_value['kecamatan'],
                    'nama_bunda_kecamatan' => !empty($monev->nama_bunda_kecamatan)
                        ? $monev->nama_bunda_kecamatan
                        : $data_value['nama_bunda_kecamatan'],
                    'nik_bunda_kelurahan' => !empty($monev->nik_bunda_kelurahan)
                        ? $monev->nik_bunda_kelurahan
                        : $data_value['nik_bunda_kelurahan'],
                    'ttd_bunda_kelurahan' => !empty($monev->ttd_bunda_kelurahan)
                        ? $monev->ttd_bunda_kelurahan
                        : $data_value['ttd_bunda_kelurahan'],
                    'nik_bunda_kecamatan' => !empty($monev->nik_bunda_kecamatan)
                        ? $monev->nik_bunda_kecamatan
                        : $data_value['nik_bunda_kecamatan'],
                    'ttd_bunda_kecamatan' => !empty($monev->ttd_bunda_kecamatan)
                        ? $monev->ttd_bunda_kecamatan
                        : $data_value['ttd_bunda_kecamatan'],
                    'ttd_ks' => !empty($monev->ttd_ks) ? $monev->ttd_ks : $data_value['ttd_ks'],
                ];
            }
        @endphp

        <div class="conclusion-options">
            <label class="conclusion-opt">
                <input type="radio" name="kesimpulan_rekomendasi" @if ($data_value['kesimpulan_rekomendasi'] == 'sesuai') checked @endif
                    value="sesuai">
                <div>
                    <div class="opt-label" style="color:var(--green)">☐ Data dan dokumen sesuai kondisi riil</div>
                    <div class="opt-desc">Seluruh komponen tidak menunjukkan ketidaksesuaian. Tidak diperlukan
                        tindakan khusus. Arsipkan instrumen.</div>
                </div>
            </label>
            <label class="conclusion-opt">
                <input type="radio" name="kesimpulan_rekomendasi" @if ($data_value['kesimpulan_rekomendasi'] == 'admin') checked @endif
                    value="admin">
                <div>
                    <div class="opt-label" style="color:var(--amber)">☐ Terdapat ketidaksesuaian administratif
                        (perlu pembinaan)</div>
                    <div class="opt-desc">Temuan administratif (barang belum berlabel, daftar hadir tidak rutin,
                        Dapodik terlambat update), tanpa indikasi penyimpangan dana. Monitoring ulang 1 bulan.</div>
                </div>
            </label>
            <label class="conclusion-opt">
                <input type="radio" name="kesimpulan_rekomendasi" @if ($data_value['kesimpulan_rekomendasi'] == 'signifikan') checked @endif
                    value="signifikan">
                <div>
                    <div class="opt-label" style="color:var(--red)">☐ Terdapat ketidaksesuaian signifikan (perlu
                        klarifikasi lanjutan)</div>
                    <div class="opt-desc">Selisih data besar, belanja di luar RKAS, bukti mencurigakan, indikasi
                        penyimpangan. Laporkan ke Dinas Pendidikan & Inspektorat hari yang sama. BOSP/Jaspel
                        ditangguhkan.</div>
                </div>
            </label>
        </div>

        <h4 style="font-size:.88rem;margin-bottom:.5rem">Rekomendasi Tindak Lanjut (spesifik & terukur)</h4>
        <textarea class="tbl-textarea" style="min-height:100px"
            placeholder="Contoh yang BENAR:&#10;- PPT A diminta melengkapi bukti pembelian printer Rp2.951.490 dalam 7 hari kerja.&#10;- Dinas perlu hitung ulang pencairan BOSP Sem I 2026 untuk TK B berdasarkan 30 murid riil (bukan 49 Dapodik).&#10;&#10;JANGAN tulis rekomendasi umum seperti 'Segera perbaiki administrasi' atau 'Data murid agar disesuaikan'."
            name="rekomendasi_tindak_lanjut">{{ $data_value['rekomendasi_tindak_lanjut'] }}</textarea>

        <div
            style="margin-top:1.5rem;padding:1rem;background:var(--bg);border-radius:8px;font-size:.82rem;line-height:1.7;color:var(--ink2)">
            <strong>Pernyataan Validasi:</strong><br>
            <em>Kepala PAUD yang menandatangani instrumen ini menyatakan bertanggung jawab penuh atas kebenaran
                seluruh data yang tercantum. Apabila di kemudian hari ditemukan ketidaksesuaian data, kekeliruan
                pencatatan, atau penyimpangan dalam pengelolaan anggaran, maka tanggung jawab sepenuhnya berada pada
                pihak satuan PAUD yang bersangkutan, sesuai dengan ketentuan peraturan perundang-undangan yang
                berlaku.</em>
        </div>

        <div class="sig-grid" style="grid-template-columns: repeat(3, 1fr)">

            <input type="hidden" name="ttd_bunda_kelurahan" value="{{ $data_value['ttd_bunda_kelurahan'] }}">
            <input type="hidden" name="ttd_bunda_kecamatan" value="{{ $data_value['ttd_bunda_kecamatan'] }}">
            <input type="hidden" name="nik_bunda_kelurahan" value="{{ $data_value['nik_bunda_kelurahan'] }}">
            <input type="hidden" name="nik_bunda_kecamatan" value="{{ $data_value['nik_bunda_kecamatan'] }}">
            <input type="hidden" name="ttd_ks" value="{{ $data_value['ttd_ks'] }}">

            <div class="sig-box">
                <div class="sig-title">Bunda PAUD Kecamatan {{ $data_value['kecamatan'] }}</div>
                <img style="height: 3cm" src="{{ $data_value['ttd_bunda_kecamatan'] }}" alt="TTD BUNDA KECAMATAN"
                    onerror="this.onerror=null;this.src='https://png.pngtree.com/png-vector/20190917/ourmid/pngtree-not-found-circle-icon-vectors-png-image_1737851.jpg'">
                <div class="sig-line">( {{ $data_value['nama_bunda_kecamatan'] }} )</div>
            </div>
            <div class="sig-box">
                <div class="sig-title">Bunda PAUD Kelurahan {{ $data_value['kelurahan'] }}</div>
                <img style="height: 3cm" src="{{ $data_value['ttd_bunda_kelurahan'] }}" alt="TTD BUNDA KELURAHAN"
                    onerror="this.onerror=null;this.src='https://png.pngtree.com/png-vector/20190917/ourmid/pngtree-not-found-circle-icon-vectors-png-image_1737851.jpg'">
                <div class="sig-line">( {{ $data_value['nama_bunda_kelurahan'] }} )</div>
            </div>
            <div class="sig-box">
                <div class="sig-title">Kepala PAUD {{ $data_value['nama_satuan'] }}</div>
                <img style="height: 3cm" src="{{ $data_value['ttd_ks'] }}" alt="TTD KS PAUD"
                    onerror="this.onerror=null;this.src='https://png.pngtree.com/png-vector/20190917/ourmid/pngtree-not-found-circle-icon-vectors-png-image_1737851.jpg'">
                <div class="sig-line">( {{ $data_value['nama_ks'] }} )</div>
            </div>
        </div>

        <div class="id-fielad" style="margin-top: 5px">
            <button class="btn btn-primary" type="submit">
                Simpan
            </button>
        </div>
    </div>
</form>
