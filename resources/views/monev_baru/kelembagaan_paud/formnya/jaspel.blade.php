<form onsubmit="simpan('form_jaspel')" class="form_jaspel">
    <!-- KOMPONEN 6: JASPEL -->
    <div class="section" id="sec6">
        <div class="section-header" onclick="toggleSection(this)">
            <div class="section-num" style="background:#d97706">6</div>
            <h3>Penerimaan Jaspel <span class="tag-sistemik">🔗 JUMLAH GURU PENERIMA SISTEMIK</span></h3>
            <svg class="chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M6 9l6 6 6-6" />
            </svg>
        </div>
        <div class="section-body">
            <div class="komponen-wrap">
                @php
                    $jumlah_jaspel = $jaspel->jumlah ?? 0;
                    $jumlah_ptk = $jumlah_ptk->jumlah ?? 0;

                    $data_value = [
                        'jumlah_ptk_jaspel' => $jumlah_jaspel,
                        'jumlah_ptk_jaspel_riil' => $jumlah_jaspel,
                        'status_penerima_jaspel' => '',
                        'catatan_jaspel' => '',
                        'jumlah_ptk_non_penerima' => $jumlah_ptk - $jumlah_jaspel,
                        'pemenuhan_rasio' => '',
                        'status_bukti_penerimaan_jaspel' => '',
                        'npsn' => $sekolah->npsn ?? '',
                    ];

                    if (!empty($monev)) {
                        $data_value = [
                            'jumlah_ptk_jaspel' => !empty($monev->jumlah_ptk_jaspel)
                                ? $monev->jumlah_ptk_jaspel
                                : $data_value['jumlah_ptk_jaspel'],
                            'jumlah_ptk_jaspel_riil' => !empty($monev->jumlah_ptk_jaspel_riil)
                                ? $monev->jumlah_ptk_jaspel_riil
                                : $data_value['jumlah_ptk_jaspel_riil'],
                            'status_penerima_jaspel' => !empty($monev->status_penerima_jaspel)
                                ? $monev->status_penerima_jaspel
                                : $data_value['status_penerima_jaspel'],
                            'catatan_jaspel' => !empty($monev->catatan_jaspel)
                                ? $monev->catatan_jaspel
                                : $data_value['catatan_jaspel'],
                            'jumlah_ptk_non_penerima' => !empty($monev->jumlah_ptk_non_penerima)
                                ? $monev->jumlah_ptk_non_penerima
                                : $data_value['jumlah_ptk_non_penerima'],
                            'pemenuhan_rasio' => !empty($monev->pemenuhan_rasio)
                                ? $monev->pemenuhan_rasio
                                : $data_value['pemenuhan_rasio'],
                            'status_bukti_penerimaan_jaspel' => !empty($monev->status_bukti_penerimaan_jaspel)
                                ? $monev->status_bukti_penerimaan_jaspel
                                : $data_value['status_bukti_penerimaan_jaspel'],
                            'npsn' => !empty($monev->npsn) ? $monev->npsn : $data_value['npsn'],
                        ];
                    }
                @endphp
                <table class="komponen-table">
                    <thead>
                        <tr>
                            <th style="width:28%">Uraian</th>
                            <th style="width:12%">Data Sistem <span class="tag-sistemik"
                                    style="font-size:.6rem">SIS</span>
                            </th>
                            <th style="width:12%">Riil</th>
                            <th style="width:12%">Status</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>Jumlah Guru Penerima Jaspel</strong>
                                <span class="juknis-btn" tabindex="0">?
                                    <span class="juknis-tip"><strong>Juknis:</strong> Rasio: TK 1:15, KB 1:12, TPA
                                        1:10, PPT 1:12. Hitung: murid riil ÷ rasio = guru berhak (bulatkan ke
                                        bawah). Minta bukti penerimaan Jan–Mar 2026.</span>
                                </span>
                            </td>
                            <td>
                                <input type="number" class="tbl-input narrow" placeholder="Sistem"
                                    style="background:#f5f3ff" name="jumlah_ptk_jaspel"
                                    value="{{ $data_value['jumlah_ptk_jaspel'] }}" readonly><br>
                                <a href="javascript:void(0)"
                                    onclick="detail('jaspel','{{ $data_value['npsn'] }}')">Lihat data</a>
                            </td>
                            <td><input type="number" class="tbl-input narrow" placeholder="Riil"
                                    name="jumlah_ptk_jaspel_riil" value="{{ $data_value['jumlah_ptk_jaspel_riil'] }}">
                            </td>
                            <td rowspan="4">
                                <select class="status-select" onchange="updateStatusClass(this)"
                                    name="status_penerima_jaspel">
                                    <option value="">— Pilih —</option>
                                    <option @if ($data_value['status_penerima_jaspel'] == 'true') selected @endif value="true">Sesuai
                                    </option>
                                    <option @if ($data_value['status_penerima_jaspel'] == 'false') selected @endif value="false">Tidak
                                        Sesuai</option>
                                </select>
                            </td>
                            <td rowspan="4">
                                <textarea class="tbl-textarea" style="min-height:80px"
                                    placeholder="Perhitungan rasio: ... murid riil ÷ ... (rasio) = ... guru berhak. Jika penerima > berhak → kelebihan pembayaran."
                                    name="catatan_jaspel">{{ $data_value['catatan_jaspel'] }}</textarea>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Jumlah Guru Non-Penerima</strong></td>
                            <td style="font-size:.78rem;color:var(--ink3)">—</td>
                            <td><input type="number" class="tbl-input narrow" placeholder="org"
                                    name="jumlah_ptk_non_penerima" value="{{ $data_value['jumlah_ptk_non_penerima'] }}">
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Pemenuhan Rasio Guru</strong></td>
                            <td colspan="2"><input type="text" class="tbl-input"
                                    placeholder="Contoh: 45 ÷ 12 = 3,75 → 4 guru berhak" name="pemenuhan_rasio"
                                    value="{{ $data_value['pemenuhan_rasio'] }}"></td>
                        </tr>
                        <tr>
                            <td><strong>Bukti Penerimaan Jan–Mar 2026</strong></td>
                            <td colspan="2">
                                <select class="status-select" onchange="updateStatusClass(this)" style="min-width:100px"
                                    name="status_bukti_penerimaan_jaspel">
                                    <option value="">—</option>
                                    <option @if ($data_value['status_bukti_penerimaan_jaspel'] == 'ada') selected @endif value="ada">Ada
                                        Lengkap
                                    </option>
                                    <option @if ($data_value['status_bukti_penerimaan_jaspel'] == 'belum') selected @endif value="belum">Sebagian
                                    </option>
                                    <option @if ($data_value['status_bukti_penerimaan_jaspel'] == 'tidakada') selected @endif value="tidakada">Tidak
                                        Ada
                                    </option>
                                </select>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div class="id-fielad" style="margin-top: 5px">
                    <button class="btn btn-primary" type="submit">
                        Simpan
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
