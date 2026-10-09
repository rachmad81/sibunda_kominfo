<!-- ============================================ -->
<!-- KOMPONEN 1: IZIN OPERASIONAL -->
<!-- ============================================ -->
<form onsubmit="simpan('form_ijop')" class="form_ijop">
    <div class="section open" id="sec1">
        <div class="section-header" onclick="toggleSection(this)">
            <div class="section-num" style="background:var(--accent)">1</div>
            <h3>Izin Operasional <span class="tag-api">⚡ API SISTEM</span></h3>
            <svg class="chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M6 9l6 6 6-6" />
            </svg>
        </div>
        <div class="section-body">
            @php
                $data_value = [
                    'no_izin_operasional' => $ijop->no_sk_baru ?? '',
                    'status_izin_operasional' => '',
                    'keterangan_izin_operasional' => '',
                    'tanggal_terbit' => $ijop->tgl_disetujui ?? '',
                    'tanggal_berakhir' => $ijop->tgl_berakhir_baru ?? '',
                    'validasi_izin_operasional' => '',
                    'catatan_validasi_izin_operasional' => '',
                ];

                if (!empty($monev)) {
                    $data_value = [
                        'no_izin_operasional' => !empty($monev->no_izin_operasional)
                            ? $monev->no_izin_operasional
                            : $data_value['no_izin_operasional'],
                        'status_izin_operasional' => !empty($monev->status_izin_operasional)
                            ? $monev->status_izin_operasional
                            : $data_value['status_izin_operasional'],
                        'keterangan_izin_operasional' => !empty($monev->keterangan_izin_operasional)
                            ? $monev->keterangan_izin_operasional
                            : $data_value['keterangan_izin_operasional'],
                        'tanggal_terbit' => !empty($monev->tanggal_terbit)
                            ? $monev->tanggal_terbit
                            : $data_value['tanggal_terbit'],
                        'tanggal_berakhir' => !empty($monev->tanggal_berakhir)
                            ? $monev->tanggal_berakhir
                            : $data_value['tanggal_berakhir'],
                        'validasi_izin_operasional' => !empty($monev->validasi_izin_operasional)
                            ? $monev->validasi_izin_operasional
                            : $data_value['validasi_izin_operasional'],
                        'catatan_validasi_izin_operasional' => !empty($monev->catatan_validasi_izin_operasional)
                            ? $monev->catatan_validasi_izin_operasional
                            : $data_value['catatan_validasi_izin_operasional'],
                    ];
                }
            @endphp
            <div class="komponen-wrap">
                <table class="komponen-table">
                    <thead>
                        <tr>
                            <th style="width:25%">Uraian</th>
                            <th style="width:18%">Data Sistem <span class="tag-api">API</span></th>
                            <th style="width:15%">Status</th>
                            <th style="width:42%">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <strong>Nomor Izin Operasional</strong>
                                <span class="juknis-btn" tabindex="0">?
                                    <span class="juknis-tip">
                                        <strong>Juknis:</strong>
                                        Tim monitoring meminta
                                        dokumen Izin Operasional asli atau salinan resmi. Periksa nomor izin,
                                        tanggal terbit, dan tanggal berakhir masa berlaku.
                                    </span>
                                </span>
                            </td>
                            <td>
                                <input type="text" class="tbl-input mono" placeholder="Auto dari API sistem"
                                    style="background:#f0fdfa" name="no_izin_operasional"
                                    value="{{ $data_value['no_izin_operasional'] }}" readonly>
                            </td>
                            <td rowspan="3">
                                <select class="status-select" onchange="updateStatusClass(this)"
                                    name="status_izin_operasional">
                                    <option value="">— Pilih —</option>
                                    <option @if ($data_value['status_izin_operasional'] == 'true') selected @endif value="true">
                                        Berlaku</option>
                                    <option @if ($data_value['status_izin_operasional'] == 'false') selected @endif value="false">
                                        Tidak Berlaku</option>
                                </select>
                            </td>
                            <td rowspan="3">
                                <textarea class="tbl-textarea"
                                    placeholder="Nomor izin + tanggal s/d tanggal. Contoh: No. 500.16.7.2/2020, berlaku s/d 3 Nov 2026"
                                    name="keterangan_izin_operasional">{{ $data_value['keterangan_izin_operasional'] }}</textarea>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Tanggal Terbit</strong></td>
                            <td>
                                <input type="date" class="tbl-input mono" placeholder="" style="background:#f0fdfa"
                                    name="tanggal_terbit" value="{{ $data_value['tanggal_terbit'] }}" readonly>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Tanggal Berakhir</strong></td>
                            <td>
                                <input type="date" class="tbl-input mono" placeholder="" style="background:#f0fdfa"
                                    name="tanggal_berakhir" value="{{ $data_value['tanggal_berakhir'] }}" readonly>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Validasi Lapangan</strong></td>
                            <td colspan="1" style="font-size:.78rem;color:var(--ink3)">Cocokkan dokumen
                                fisik
                                dengan data API</td>
                            <td>
                                <select class="status-select" onchange="updateStatusClass(this)"
                                    name="validasi_izin_operasional">
                                    <option value="">— Pilih —</option>
                                    <option @if ($data_value['validasi_izin_operasional'] == 'true') selected @endif value="true">Sesuai
                                    </option>
                                    <option @if ($data_value['validasi_izin_operasional'] == 'false') selected @endif value="false">Tidak
                                        Sesuai</option>
                                </select>
                            </td>
                            <td>
                                <textarea class="tbl-textarea" placeholder="Catatan jika ada perbedaan antara data API dan dokumen fisik"
                                    name="catatan_validasi_izin_operasional">{{ $data_value['catatan_validasi_izin_operasional'] }}</textarea>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="risk-box">
                    <h4>⚠ Catatan Potensi Risiko</h4>
                    Izin habis tapi masih beroperasi → tidak memenuhi syarat BOSP. Sekolah tanpa izin sama
                    sekali.
                    Satu lokasi digunakan dua lembaga berbeda — catat nama & status izin lembaga lain.
                </div>
                <div class="id-fielad" style="margin-top: 5px">
                    <button class="btn btn-primary" type="submit">
                        Simpan
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
