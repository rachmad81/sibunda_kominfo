<form onsubmit="simpan('form_sinkron')" class="form_sinkron">
    <!-- KOMPONEN 8: JEJAK DIGITAL SISTEM -->
    <div class="section" id="sec8">
        <div class="section-header" onclick="toggleSection(this)">
            <div class="section-num" style="background:#6366f1">8</div>
            <h3>Jejak Digital Sistem <span class="tag-sistemik">🔗 TANGGAL TERAKHIR SISTEMIK</span></h3>
            <svg class="chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M6 9l6 6 6-6" />
            </svg>
        </div>
        <div class="section-body">
            <div class="komponen-wrap">
                @php
                    $tgl_sinkron = $dapodik->tgl_sinkron ?? '';
                    $tgl_sinkron = !in_array($tgl_sinkron, ['', 'null']) ? date('Y-m-d', strtotime($tgl_sinkron)) : '';

                    $tgl_profil = $update_profil->updated_at ?? '';
                    $tgl_profil = !in_array($tgl_profil, ['', 'null']) ? date('Y-m-d', strtotime($tgl_profil)) : '';
                    $tgl_profil = $tgl_profil == '1970-01-01' ? '' : $tgl_profil;

                    $data_value = [
                        'tanggal_sinkron_dapodik' => $tgl_sinkron,
                        'tanggal_sinkron_dapodik_riil' => '',
                        'catatan_tanggal_sinkron_dapodik' => '',
                        'tanggal_update_profil' => $tgl_profil,
                        'tanggal_update_profil_riil' => '',
                        'catatan_tanggal_update_profil' => '',
                    ];

                    if (!empty($monev)) {
                        $data_value = [
                            'tanggal_sinkron_dapodik' => !empty($monev->tanggal_sinkron_dapodik)
                                ? $monev->tanggal_sinkron_dapodik
                                : $data_value['tanggal_sinkron_dapodik'],
                            'tanggal_sinkron_dapodik_riil' => !empty($monev->tanggal_sinkron_dapodik_riil)
                                ? $monev->tanggal_sinkron_dapodik_riil
                                : $data_value['tanggal_sinkron_dapodik_riil'],
                            'catatan_tanggal_sinkron_dapodik' => !empty($monev->catatan_tanggal_sinkron_dapodik)
                                ? $monev->catatan_tanggal_sinkron_dapodik
                                : $data_value['catatan_tanggal_sinkron_dapodik'],
                            'tanggal_update_profil' => !empty($monev->tanggal_update_profil)
                                ? $monev->tanggal_update_profil
                                : $data_value['tanggal_update_profil'],
                            'tanggal_update_profil_riil' => !empty($monev->tanggal_update_profil_riil)
                                ? $monev->tanggal_update_profil_riil
                                : $data_value['tanggal_update_profil_riil'],
                            'catatan_tanggal_update_profil' => !empty($monev->catatan_tanggal_update_profil)
                                ? $monev->catatan_tanggal_update_profil
                                : $data_value['catatan_tanggal_update_profil'],
                        ];
                    }
                @endphp
                <table class="komponen-table">
                    <thead>
                        <tr>
                            <th style="width:28%">Uraian</th>
                            <th style="width:18%">Tanggal (Sistem) <span class="tag-sistemik"
                                    style="font-size:.6rem">SIS</span></th>
                            <th style="width:18%">Tanggal (Lapangan)</th>
                            <th style="width:36%">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>8a. Tanggal Terakhir Sinkronisasi Dapodik (SPTJM)</strong>
                                <span class="juknis-btn" tabindex="0">?
                                    <span class="juknis-tip"><strong>Juknis:</strong> SPTJM dicetak dari Dapodik,
                                        memuat nama operator & tanggal sinkronisasi terakhir. Sinkronisasi > 3 bulan
                                        lalu → data tidak mutakhir, potensi kelebihan BOSP.</span>
                                </span>
                            </td>
                            <td><input type="date" class="tbl-input" style="background:#f5f3ff"
                                    name="tanggal_sinkron_dapodik" value="{{ $data_value['tanggal_sinkron_dapodik'] }}">
                            </td>
                            <td><input type="date" class="tbl-input" name="tanggal_sinkron_dapodik_riil"></td>
                            <td>
                                <textarea class="tbl-textarea" placeholder="Nama operator, catatan jika SPTJM tidak tersedia"
                                    name="catatan_tanggal_sinkron_dapodik">{{ $data_value['catatan_tanggal_sinkron_dapodik'] }}</textarea>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>8b. Tanggal Terakhir Update Profil Sekolah (SiBunda)</strong></td>
                            <td><input type="date" class="tbl-input" style="background:#f5f3ff"
                                    name="tanggal_update_profil" value="{{ $data_value['tanggal_update_profil'] }}">
                            </td>
                            <td><input type="date" class="tbl-input" name="tanggal_update_profil_riil"
                                    value="{{ $data_value['tanggal_update_profil_riil'] }}"></td>
                            <td>
                                <textarea class="tbl-textarea" placeholder="Profil tidak diperbarui → data kelembagaan tidak aktual"
                                    name="catatan_tanggal_update_profil">{{ $data_value['catatan_tanggal_update_profil'] }}</textarea>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="risk-box">
                    <h4>⚠ Catatan Potensi Risiko</h4>
                    Data Dapodik tidak mutakhir → murid keluar masih tercatat aktif → kelebihan pencairan BOSP.
                    Profil tidak diperbarui → alokasi anggaran tidak tepat sasaran.
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
