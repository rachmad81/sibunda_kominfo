<form onsubmit="simpan('form_ptk')" class="form_ptk">
    <!-- KOMPONEN 5: DATA PTK -->
    <div class="section" id="sec5">
        <div class="section-header" onclick="toggleSection(this)">
            <div class="section-num" style="background:#dc2626">5</div>
            <h3>Jumlah Data PTK (sesuai SK Pengangkatan) <span class="tag-sistemik">🔗 DAPODIK & PROFIL
                    SISTEMIK</span></h3>
            <svg class="chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M6 9l6 6 6-6" />
            </svg>
        </div>
        <div class="section-body">
            @php
                $jumlah_guru = $dapodik->jumlah_guru ?? 0;
                $jumlah_tendik = $dapodik->jumlah_tendik ?? 0;
                $jumlah_guru = $jumlah_guru == 'null' ? 0 : (int) $jumlah_guru;
                $jumlah_tendik = $jumlah_tendik == 'null' ? 0 : (int) $jumlah_tendik;
                $jumlah_dapodik = $jumlah_guru + $jumlah_tendik;
                $data_value = [
                    'jumlah_ptk_dapodik' => $jumlah_dapodik,
                    'jumlah_ptk_profil' => $jumlah_ptk->jumlah ?? 0,
                    'jumlah_ptk_riil' => $jumlah_ptk->jumlah ?? 0,
                    'status_jumlah_ptk' => '',
                    'catatan_jumlah_ptk' => '',
                    'status_skpbm_ptk' => '',
                    'catatan_skpbm_ptk' => '',
                    'status_sk_pengangkatakan_ptk' => '',
                    'catatan_sk_pengangkatan_ptk' => '',
                    'catatan_hubungan_kekerabatan' => '',
                    'npsn' => $sekolah->npsn ?? '',
                ];

                if (!empty($monev)) {
                    $data_value = [
                        'jumlah_ptk_dapodik' => !empty($monev->jumlah_ptk_dapodik)
                            ? $monev->jumlah_ptk_dapodik
                            : $data_value['jumlah_ptk_dapodik'],
                        'jumlah_ptk_profil' => !empty($monev->jumlah_ptk_profil)
                            ? $monev->jumlah_ptk_profil
                            : $data_value['jumlah_ptk_profil'],
                        'jumlah_ptk_riil' => !empty($monev->jumlah_ptk_riil)
                            ? $monev->jumlah_ptk_riil
                            : $data_value['jumlah_ptk_riil'],
                        'status_jumlah_ptk' => !empty($monev->status_jumlah_ptk)
                            ? $monev->status_jumlah_ptk
                            : $data_value['status_jumlah_ptk'],
                        'catatan_jumlah_ptk' => !empty($monev->catatan_jumlah_ptk)
                            ? $monev->catatan_jumlah_ptk
                            : $data_value['catatan_jumlah_ptk'],
                        'status_skpbm_ptk' => !empty($monev->status_skpbm_ptk)
                            ? $monev->status_skpbm_ptk
                            : $data_value['status_skpbm_ptk'],
                        'catatan_skpbm_ptk' => !empty($monev->catatan_skpbm_ptk)
                            ? $monev->catatan_skpbm_ptk
                            : $data_value['catatan_skpbm_ptk'],
                        'status_sk_pengangkatakan_ptk' => !empty($monev->status_sk_pengangkatakan_ptk)
                            ? $monev->status_sk_pengangkatakan_ptk
                            : $data_value['status_sk_pengangkatakan_ptk'],
                        'catatan_sk_pengangkatan_ptk' => !empty($monev->catatan_sk_pengangkatan_ptk)
                            ? $monev->catatan_sk_pengangkatan_ptk
                            : $data_value['catatan_sk_pengangkatan_ptk'],
                        'catatan_hubungan_kekerabatan' => !empty($monev->catatan_hubungan_kekerabatan)
                            ? $monev->catatan_hubungan_kekerabatan
                            : $data_value['catatan_hubungan_kekerabatan'],
                        'npsn' => !empty($monev->npsn) ? $monev->npsn : $data_value['npsn'],
                    ];
                }
            @endphp
            <div class="komponen-wrap">
                <table class="komponen-table">
                    <thead>
                        <tr>
                            <th style="width:22%">Uraian</th>
                            <th style="width:10%">Dapodik <span class="tag-sistemik" style="font-size:.6rem">SIS</span>
                            </th>
                            <th style="width:10%">Profil <span class="tag-sistemik" style="font-size:.6rem">SIS</span>
                            </th>
                            <th style="width:10%">Riil (hadir)</th>
                            <th style="width:12%">Status</th>
                            <th style="width:36%">Keterangan / Alasan Tidak Sesuai</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>Jumlah PTK</strong>
                                <span class="juknis-btn" tabindex="0">?
                                    <span class="juknis-tip"><strong>Juknis:</strong> Hitung PTK hadir. Cek SK
                                        Pengangkatan (tanda tangan, stempel, tanggal). Tanyakan hubungan kekerabatan
                                        dengan Kepsek/Ketua RW/Ketua Yayasan.</span>
                                </span>
                            </td>
                            <td><input type="number" class="tbl-input narrow" placeholder="0"
                                    style="background:#f5f3ff" name="jumlah_ptk_dapodik"
                                    value="{{ $data_value['jumlah_ptk_dapodik'] }}" readonly>
                            </td>
                            <td><input type="number" class="tbl-input narrow" placeholder="0"
                                    style="background:#f5f3ff" name="jumlah_ptk_profil"
                                    value="{{ $data_value['jumlah_ptk_profil'] }}" readonly><br>
                                <a href="javascript:void(0)"
                                    onclick="detail('pegawai','{{ $data_value['npsn'] }}')">Lihat data</a>
                            </td>
                            <td><input type="number" class="tbl-input narrow" placeholder="Hitung"
                                    name="jumlah_ptk_riil" value="{{ $data_value['jumlah_ptk_riil'] }}"></td>
                            <td>
                                <select class="status-select" onchange="updateStatusClass(this)"
                                    name="status_jumlah_ptk">
                                    <option value="">— Pilih —</option>
                                    <option @if ($data_value['status_jumlah_ptk'] == 'true') selected @endif value="true">Sesuai
                                    </option>
                                    <option @if ($data_value['status_jumlah_ptk'] == 'false') selected @endif value="false">Tidak
                                        Sesuai</option>
                                </select>
                            </td>
                            <td>
                                <textarea class="tbl-textarea" placeholder="Catat nama PTK, hubungan kekerabatan, kualifikasi di kolom risiko jika ada."
                                    name="catatan_jumlah_ptk">{{ $data_value['catatan_jumlah_ptk'] }}</textarea>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Jadwal KBM / SKPBM</strong></td>
                            <td colspan="2" style="font-size:.78rem;color:var(--ink3)">Minta dokumen jadwal
                                tertulis</td>
                            <td>
                                <select class="status-select" onchange="updateStatusClass(this)" style="min-width:80px"
                                    name="status_skpbm_ptk">
                                    <option value="">—</option>
                                    <option @if ($data_value['status_skpbm_ptk'] == 'true') selected @endif value="true">Ada
                                    </option>
                                    <option @if ($data_value['status_skpbm_ptk'] == 'false') selected @endif value="false">Tidak Ada
                                    </option>
                                </select>
                            </td>
                            <td colspan="2">
                                <textarea class="tbl-textarea" placeholder="Hari, jam, sesi" name="catatan_skpbm_ptk">{{ $data_value['catatan_skpbm_ptk'] }}</textarea>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>SK Pengangkatan PTK</strong></td>
                            <td colspan="2" style="font-size:.78rem;color:var(--ink3)">Periksa masing-masing SK
                            </td>
                            <td>
                                <select class="status-select" onchange="updateStatusClass(this)" style="min-width:80px"
                                    name="status_sk_pengangkatakan_ptk">
                                    <option value="">—</option>
                                    <option @if ($data_value['status_sk_pengangkatakan_ptk'] == 'true') selected @endif value="true">Sah
                                    </option>
                                    <option @if ($data_value['status_sk_pengangkatakan_ptk'] == 'false') selected @endif value="false">Tidak
                                        Sah/Tidak Ada</option>
                                </select>
                            </td>
                            <td colspan="2">
                                <textarea class="tbl-textarea" placeholder="Detail SK yang bermasalah" name="catatan_sk_pengangkatan_ptk">{{ $data_value['catatan_sk_pengangkatan_ptk'] }}</textarea>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Hubungan Kekerabatan</strong></td>
                            <td colspan="3" style="font-size:.78rem;color:var(--ink3)">Tanyakan langsung</td>
                            <td colspan="2">
                                <textarea class="tbl-textarea"
                                    placeholder="Nama PTK, jenis hubungan (suami/istri/anak/dll), dengan siapa (Kepsek/RW/Yayasan)"
                                    name="catatan_hubungan_kekerabatan">{{ $data_value['catatan_hubungan_kekerabatan'] }}</textarea>
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
