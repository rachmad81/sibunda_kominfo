<form onsubmit="simpan('form_ppt')" class="form_ppt">
    <!-- KOMPONEN 9: KHUSUS PPT -->
    <div class="section" id="sec9">
        <div class="section-header" onclick="toggleSection(this)">
            <div class="section-num" style="background:#e11d48">10</div>
            <h3>Khusus PPT (Pos PAUD Terpadu)</h3>
            <svg class="chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M6 9l6 6 6-6" />
            </svg>
        </div>
        @php
            $data_value = [
                'status_ppt_balai_rw' => '',
                'catatan_ppt_balai_rw' => '',
                'catatan_kondisi_balai_rw' => '',
                'jumlah_murid_pmt' => $pmt->jml_pd ?? '',
                'status_murid_pmt' => '',
                'catatan_murid_pmt' => '',
                'jumlah_murid_pmt_riil' => $jumlah_siswa->jumlah ?? '',
                'jadwal_pmt' => '2x/bulan',
                'menu_pmt' => !empty($pmt) ? $pmt->menu_keg_1 . ' & ' . $pmt->menu_keg_2 : '',
                'jumlah_penerima_beasiswa_s1' => $rpl ?? '',
                'jumlah_penerima_beasiswa_s1_riil' => $rpl ?? '',
                'status_penerima_beasiswa_s1' => '',
                'catatan_penerima_beasiswa_s1' => '',
                'npsn' => $sekolah->npsn ?? '',
            ];

            if (!empty($monev)) {
                $data_value = [
                    'status_ppt_balai_rw' => !empty($monev->status_ppt_balai_rw)
                        ? $monev->status_ppt_balai_rw
                        : $data_value['status_ppt_balai_rw'],
                    'catatan_ppt_balai_rw' => !empty($monev->catatan_ppt_balai_rw)
                        ? $monev->catatan_ppt_balai_rw
                        : $data_value['catatan_ppt_balai_rw'],
                    'catatan_kondisi_balai_rw' => !empty($monev->catatan_kondisi_balai_rw)
                        ? $monev->catatan_kondisi_balai_rw
                        : $data_value['catatan_kondisi_balai_rw'],
                    'jumlah_murid_pmt' => !empty($monev->jumlah_murid_pmt)
                        ? $monev->jumlah_murid_pmt
                        : $data_value['jumlah_murid_pmt'],
                    'status_murid_pmt' => !empty($monev->status_murid_pmt)
                        ? $monev->status_murid_pmt
                        : $data_value['status_murid_pmt'],
                    'catatan_murid_pmt' => !empty($monev->catatan_murid_pmt)
                        ? $monev->catatan_murid_pmt
                        : $data_value['catatan_murid_pmt'],
                    'jumlah_murid_pmt_riil' => !empty($monev->jumlah_murid_pmt_riil)
                        ? $monev->jumlah_murid_pmt_riil
                        : $data_value['jumlah_murid_pmt_riil'],
                    'jadwal_pmt' => !empty($monev->jadwal_pmt) ? $monev->jadwal_pmt : $data_value['jadwal_pmt'],
                    'menu_pmt' => !empty($monev->menu_pmt) ? $monev->menu_pmt : $data_value['menu_pmt'],
                    'jumlah_penerima_beasiswa_s1' => !empty($monev->jumlah_penerima_beasiswa_s1)
                        ? $monev->jumlah_penerima_beasiswa_s1
                        : $data_value['jumlah_penerima_beasiswa_s1'],
                    'jumlah_penerima_beasiswa_s1_riil' => !empty($monev->jumlah_penerima_beasiswa_s1_riil)
                        ? $monev->jumlah_penerima_beasiswa_s1_riil
                        : $data_value['jumlah_penerima_beasiswa_s1_riil'],
                    'status_penerima_beasiswa_s1' => !empty($monev->status_penerima_beasiswa_s1)
                        ? $monev->status_penerima_beasiswa_s1
                        : $data_value['status_penerima_beasiswa_s1'],
                    'catatan_penerima_beasiswa_s1' => !empty($monev->catatan_penerima_beasiswa_s1)
                        ? $monev->catatan_penerima_beasiswa_s1
                        : $data_value['catatan_penerima_beasiswa_s1'],
                    'npsn' => !empty($monev->npsn) ? $monev->npsn : $data_value['npsn'],
                ];
            }
        @endphp
        <div class="section-body">
            <div class="komponen-wrap">
                <div class="ppt-highlight">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                        <line x1="12" y1="9" x2="12" y2="13" />
                        <line x1="12" y1="17" x2="12.01" y2="17" />
                    </svg>
                    <div>
                        <strong>Komponen ini HANYA untuk satuan berjenis PPT.</strong> Jika satuan bukan PPT, lewati
                        bagian ini. Untuk TK/KB/TPA, langsung ke Kesimpulan.
                    </div>
                </div>

                <!-- 9a Balai RW -->
                <h4 style="margin:1rem 0 .5rem;font-size:.88rem;color:#e11d48">9a. Berlokasi di Balai RW</h4>
                <table class="komponen-table">
                    <thead>
                        <tr>
                            <th style="width:25%">Uraian</th>
                            <th style="width:15%">Status</th>
                            <th>Keterangan Lokasi Riil</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>KBM berlangsung di Balai RW</strong>
                                <span class="juknis-btn" tabindex="0">?
                                    <span class="juknis-tip"><strong>Juknis:</strong> Amati langsung apakah KBM
                                        berlangsung di Balai RW. Bukan berdasarkan keterangan pihak PAUD. Lokasi
                                        bukan Balai RW → PPT fiktif / pindah tanpa dasar hukum.</span>
                                </span>
                            </td>
                            <td>
                                <select class="status-select" onchange="updateStatusClass(this)"
                                    name="status_ppt_balai_rw">
                                    <option value="">— Pilih —</option>
                                    <option @if ($data_value['status_ppt_balai_rw'] == 'true') selected @endif value="true">Ya</option>
                                    <option @if ($data_value['status_ppt_balai_rw'] == 'false') selected @endif value="false">Tidak
                                    </option>
                                </select>
                            </td>
                            <td>
                                <textarea class="tbl-textarea"
                                    placeholder="Jika Tidak: catat lokasi sebenarnya. Contoh: di rumah pengelola yayasan Jl. Manukan 76, bukan Balai RW."
                                    name="catatan_ppt_balai_rw">{{ $data_value['catatan_ppt_balai_rw'] }}</textarea>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" style="background:#fffbeb;padding:.75rem">
                                <strong style="color:#92400e">📋 Keterangan Kondisi Riil di Balai RW:</strong><br>
                                <textarea class="tbl-textarea" style="margin-top:.5rem;min-height:80px"
                                    placeholder="Deskripsikan kondisi riil Balai RW secara detail:&#10;- Kondisi fisik bangunan (layak/tidak layak)&#10;- Ketersediaan sarana prasarana KBM&#10;- Apakah Balai RW digunakan bersama kegiatan lain?&#10;- Kondisi keamanan & kebersihan&#10;- Akses jalan menuju lokasi&#10;- Ketersediaan MCK/toilet"
                                    name="catatan_kondisi_balai_rw">{{ $data_value['catatan_kondisi_balai_rw'] }}</textarea>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <!-- 9b PMT -->
                <h4 style="margin:1rem 0 .5rem;font-size:.88rem;color:#e11d48">9b. Penerimaan PMT (Pemberian
                    Makanan Tambahan)</h4>
                <table class="komponen-table">
                    <thead>
                        <tr>
                            <th style="width:25%">Uraian</th>
                            <th style="width:12%">Data</th>
                            <th style="width:12%">Status</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>Jumlah Murid Penerima PMT</strong>
                                <span class="juknis-btn" tabindex="0">?
                                    <span class="juknis-tip"><strong>Juknis:</strong> Bandingkan jumlah penerima
                                        PMT dengan murid riil. Jadwal normal 2×/bulan. Menu harus bergizi: karbo +
                                        protein + sayur/buah. Ramadan tidak dihitung pelanggaran.</span>
                                </span>
                            </td>
                            <td>
                                <input type="number" class="tbl-input narrow" placeholder="Jml" name="jumlah_murid_pmt"
                                    value="{{ $data_value['jumlah_murid_pmt'] }}"><br>
                                <a href="javascript:void(0)" onclick="detail('pmt','{{ $data_value['npsn'] }}')">Lihat
                                    data</a>
                            </td>
                            <td rowspan="4">
                                <select class="status-select" onchange="updateStatusClass(this)"
                                    name="status_murid_pmt">
                                    <option value="">— Pilih —</option>
                                    <option @if ($data_value['status_murid_pmt'] == 'true') selected @endif value="true">Sesuai
                                    </option>
                                    <option @if ($data_value['status_murid_pmt'] == 'false') selected @endif value="false">Tidak
                                        Sesuai</option>
                                </select>
                            </td>
                            <td rowspan="4">
                                <textarea class="tbl-textarea" style="min-height:80px"
                                    placeholder="Bandingkan penerima vs murid riil. Catat jadwal, detail menu, bukti (foto, tanda terima). Contoh: 95 penerima (hanya Jan), menu risol mayo dan pukis."
                                    name="catatan_murid_pmt">{{ $data_value['catatan_murid_pmt'] }}</textarea>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Jumlah Murid Riil (pembanding)</strong></td>
                            <td>
                                <input type="number" class="tbl-input narrow" placeholder="Riil"
                                    name="jumlah_murid_pmt_riil" value="{{ $data_value['jumlah_murid_pmt_riil'] }}"><br>
                                <a href="javascript:void(0)" onclick="detail('siswa','{{ $data_value['npsn'] }}')">Lihat
                                    data</a>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Jadwal Pemberian PMT</strong></td>
                            <td><input type="text" class="tbl-input" placeholder="2×/bln" style="width:80px"
                                    name="jadwal_pmt" value="{{ $data_value['jadwal_pmt'] }}">
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Detail Menu PMT</strong></td>
                            <td><input type="text" class="tbl-input" placeholder="Jenis makanan" name="menu_pmt"
                                    value="{{ $data_value['menu_pmt'] }}">
                            </td>
                        </tr>
                    </tbody>
                </table>

                <!-- 9c Beasiswa RPL -->
                <h4 style="margin:1rem 0 .5rem;font-size:.88rem;color:#e11d48">9c. Penerima Beasiswa S1 RPL PAUD
                    <span class="tag-sistemik">🔗 PENERIMA BEASISWA SISTEMIK</span>
                </h4>
                <table class="komponen-table">
                    <thead>
                        <tr>
                            <th style="width:25%">Uraian</th>
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
                            <td><strong>Jumlah Penerima Beasiswa S1 RPL</strong>
                                <span class="juknis-btn" tabindex="0">?
                                    <span class="juknis-tip"><strong>Juknis:</strong> Tanyakan apakah ada PTK
                                        penerima Beasiswa S1 RPL PAUD. Telusuri apakah masih aktif mengajar di PPT
                                        tersebut atau sudah pindah instansi. Jika pindah → penerima fiktif.</span>
                                </span>
                            </td>
                            <td>
                                <input type="number" class="tbl-input narrow" placeholder="Sistem"
                                    style="background:#f5f3ff" name="jumlah_penerima_beasiswa_s1"
                                    value="{{ $data_value['jumlah_penerima_beasiswa_s1'] }}" readonly><br>
                                <a href="javascript:void(0)"
                                    onclick="detail('rpl','{{ $data_value['npsn'] }}')">Lihat
                                    data</a>
                            </td>
                            <td><input type="number" class="tbl-input narrow" placeholder="Riil"
                                    name="jumlah_penerima_beasiswa_s1_riil"
                                    value="{{ $data_value['jumlah_penerima_beasiswa_s1_riil'] }}"></td>
                            <td>
                                <select class="status-select" onchange="updateStatusClass(this)"
                                    name="status_penerima_beasiswa_s1">
                                    <option value="">— Pilih —</option>
                                    <option @if ($data_value['status_penerima_beasiswa_s1'] == 'true') selected @endif value="true">Ada
                                    </option>
                                    <option @if ($data_value['status_penerima_beasiswa_s1'] == 'false') selected @endif value="false">Tidak Ada
                                    </option>
                                </select>
                            </td>
                            <td>
                                <textarea class="tbl-textarea"
                                    placeholder="Nama penerima, status aktif/pindah, instansi tujuan jika pindah. Penerima yang sudah pindah = penerima fiktif."
                                    name="catatan_penerima_beasiswa_s1">{{ $data_value['catatan_penerima_beasiswa_s1'] }}</textarea>
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
