<form onsubmit="simpan('form_beasiswa_paud')" class="form_beasiswa_paud">
    <!-- KOMPONEN 6: JASPEL -->
    <div class="section" id="sec6">
        <div class="section-header" onclick="toggleSection(this)">
            <div class="section-num" style="background:#d97706">9</div>
            <h3>Penerimaan Beasiswa PAUD <span class="tag-sistemik">🔗 JUMLAH MURID PENERIMA BEASISWA PAUD</span></h3>
            <svg class="chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M6 9l6 6 6-6" />
            </svg>
        </div>
        <div class="section-body">
            <div class="komponen-wrap">
                @php
                    $jumlah_basiswa = $beasiswa->count();

                    $data_value = [
                        'jumlah_beasiswa_paud' => $jumlah_basiswa,
                        'jumlah_beasiswa_paud_riil' => 0,
                        'status_beasiswa_paud' => '',
                        'catatan_beasiswa_paud' => '',
                        'npsn' => $sekolah->npsn ?? '',
                    ];

                    if (!empty($monev)) {
                        $data_value = [
                            'jumlah_beasiswa_paud' => !empty($monev->jumlah_beasiswa_paud)
                                ? $monev->jumlah_beasiswa_paud
                                : $data_value['jumlah_beasiswa_paud'],
                            'jumlah_beasiswa_paud_riil' => !empty($monev->jumlah_beasiswa_paud_riil)
                                ? $monev->jumlah_beasiswa_paud_riil
                                : $data_value['jumlah_beasiswa_paud_riil'],
                            'status_beasiswa_paud' => !empty($monev->status_beasiswa_paud)
                                ? $monev->status_beasiswa_paud
                                : $data_value['status_beasiswa_paud'],
                            'catatan_beasiswa_paud' => !empty($monev->catatan_beasiswa_paud)
                                ? $monev->catatan_beasiswa_paud
                                : $data_value['catatan_beasiswa_paud'],
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
                            <td><strong>Jumlah Murid Penerima Beasiswa PAUD</strong>
                                {{-- <span class="juknis-btn" tabindex="0">?
                                    <span class="juknis-tip"><strong>Juknis:</strong> Rasio: TK 1:15, KB 1:12, TPA
                                        1:10, PPT 1:12. Hitung: murid riil ÷ rasio = guru berhak (bulatkan ke
                                        bawah). Minta bukti penerimaan Jan–Mar 2026.</span>
                                </span> --}}
                            </td>
                            <td>
                                <input type="number" class="tbl-input narrow" placeholder="Sistem"
                                    style="background:#f5f3ff" name="jumlah_beasiswa_paud"
                                    value="{{ $data_value['jumlah_beasiswa_paud'] }}" readonly><br>
                                <a href="javascript:void(0)"
                                    onclick="detail('beasiswa_paud','{{ $data_value['npsn'] }}')">Lihat data</a>
                            </td>
                            <td><input type="number" class="tbl-input narrow" placeholder="Riil"
                                    name="jumlah_beasiswa_paud_riil"
                                    value="{{ $data_value['jumlah_beasiswa_paud_riil'] }}">
                            </td>
                            <td rowspan="4">
                                <select class="status-select" onchange="updateStatusClass(this)"
                                    name="status_beasiswa_paud">
                                    <option value="">— Pilih —</option>
                                    <option @if ($data_value['status_beasiswa_paud'] == 'true') selected @endif value="true">Sesuai
                                    </option>
                                    <option @if ($data_value['status_beasiswa_paud'] == 'false') selected @endif value="false">Tidak
                                        Sesuai</option>
                                </select>
                            </td>
                            <td rowspan="4">
                                <textarea class="tbl-textarea" style="min-height:80px" placeholder="Catatan anda" name="catatan_beasiswa_paud">{{ $data_value['catatan_beasiswa_paud'] }}</textarea>
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
