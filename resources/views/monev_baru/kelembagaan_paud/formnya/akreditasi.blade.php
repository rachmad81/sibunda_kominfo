<form onsubmit="simpan('form_akreditasi')" class="form_akreditasi">
    <!-- KOMPONEN 3: AKREDITASI -->
    <div class="section" id="sec3">
        <div class="section-header" onclick="toggleSection(this)">
            <div class="section-num" style="background:#7c3aed">3</div>
            <h3>Akreditasi <span class="tag-sistemik">🔗 BY SISTEM</span></h3>
            <svg class="chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M6 9l6 6 6-6" />
            </svg>
        </div>
        <div class="section-body">
            @php
                $data_value = [
                    'peringkat_akreditasi' => '',
                    'akreditasi' => $ijop->akreditasi ?? '',
                    'catatan_akreditasi' => '',
                    'no_akreditasi' => '',
                    'berlaku_akreditasi' => '',
                    'status_akreditasi' => '',
                    'catatan_status_akreditasi' => '',
                ];

                if (!empty($monev)) {
                    $data_value = [
                        'peringkat_akreditasi' => !empty($monev->peringkat_akreditasi)
                            ? $monev->peringkat_akreditasi
                            : $data_value['peringkat_akreditasi'],
                        'akreditasi' => !empty($monev->akreditasi) ? $monev->akreditasi : $data_value['akreditasi'],
                        'catatan_akreditasi' => !empty($monev->catatan_akreditasi)
                            ? $monev->catatan_akreditasi
                            : $data_value['catatan_akreditasi'],
                        'no_akreditasi' => !empty($monev->no_akreditasi)
                            ? $monev->no_akreditasi
                            : $data_value['no_akreditasi'],
                        'berlaku_akreditasi' => !empty($monev->berlaku_akreditasi)
                            ? $monev->berlaku_akreditasi
                            : $data_value['berlaku_akreditasi'],
                        'status_akreditasi' => !empty($monev->status_akreditasi)
                            ? $monev->status_akreditasi
                            : $data_value['status_akreditasi'],
                        'catatan_status_akreditasi' => !empty($monev->catatan_status_akreditasi)
                            ? $monev->catatan_status_akreditasi
                            : $data_value['catatan_status_akreditasi'],
                    ];
                }
            @endphp
            <div class="komponen-wrap">
                <table class="komponen-table">
                    <thead>
                        <tr>
                            <th style="width:25%">Uraian</th>
                            <th style="width:18%">Data Sistem <span class="tag-sistemik">SISTEMIK</span></th>
                            <th style="width:15%">Status</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>Peringkat Akreditasi</strong>
                                <span class="juknis-btn" tabindex="0">?
                                    <span class="juknis-tip"><strong>Juknis:</strong> Minta Sertifikat
                                        Akreditasi
                                        BAN PDM. Cek nomor SK & masa berlaku. Jika habis/tidak ada → pilih
                                        "Tidak
                                        Terakreditasi".</span>
                                </span>
                            </td>
                            <td>
                                <input type="text" class="tbl-input mono" placeholder="Data BAN PDM via sistem"
                                    style="background:#f5f3ff" name="peringkat_akreditasi"
                                    value="{{ $data_value['peringkat_akreditasi'] }}" readonly>
                            </td>
                            <td>
                                <select class="status-select" name="akreditasi" onchange="updateStatusClass(this)">
                                    <option value="">— Pilih —</option>
                                    <option @if ($data_value['akreditasi'] == 'A') selected @endif value="A">
                                        A
                                    </option>
                                    <option @if ($data_value['akreditasi'] == 'B') selected @endif value="B">
                                        B
                                    </option>
                                    <option @if ($data_value['akreditasi'] == 'C') selected @endif value="C">
                                        C</option>
                                    <option @if ($data_value['akreditasi'] == '-') selected @endif value="-">
                                        Tidak Terakreditasi</option>
                                </select>
                            </td>
                            <td rowspan="3">
                                <textarea class="tbl-textarea" placeholder="Nomor SK Akreditasi, masa berlaku. Jika tidak terakreditasi, catat alasan."
                                    name="catatan_akreditasi">{{ $data_value['catatan_akreditasi'] }}</textarea>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Nomor SK Akreditasi</strong></td>
                            <td><input type="text" class="tbl-input mono" placeholder="Auto dari sistem"
                                    style="background:#f5f3ff" name="no_akreditasi"
                                    value="{{ $data_value['no_akreditasi'] }}" readonly>
                            </td>
                            <td>—</td>
                        </tr>
                        <tr>
                            <td><strong>Masa Berlaku Akreditasi</strong></td>
                            <td>
                                <input type="date" class="tbl-input mono" placeholder="s/d dd/mm/yyyy"
                                    style="background:#f5f3ff" name="berlaku_akreditasi"
                                    value="{{ $data_value['berlaku_akreditasi'] }}" readonly>
                            </td>
                            <td>—</td>
                        </tr>
                        <tr>
                            <td><strong>Validasi Lapangan</strong></td>
                            <td style="font-size:.78rem;color:var(--ink3)">Cocokkan sertifikat fisik vs data
                                sistem
                            </td>
                            <td>
                                <select class="status-select" onchange="updateStatusClass(this)"
                                    name="status_akreditasi">
                                    <option value="">— Pilih —</option>
                                    <option @if ($data_value['status_akreditasi'] == 'true') selected @endif value="true">Sesuai
                                    </option>
                                    <option @if ($data_value['status_akreditasi'] == 'false') selected @endif value="false">Tidak
                                        Sesuai</option>
                                </select>
                            </td>
                            <td>
                                <textarea class="tbl-textarea" placeholder="Catatan perbedaan data sistem vs dokumen fisik"
                                    name="catatan_status_akreditasi">{{ $data_value['catatan_status_akreditasi'] }}</textarea>
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
