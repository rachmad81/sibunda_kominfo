<form onsubmit="simpan('form_jumlah_murid')" class="form_jumlah_murid">
    <!-- KOMPONEN 4: JUMLAH DATA MURID -->
    <div class="section" id="sec4">
        <div class="section-header" onclick="toggleSection(this)">
            <div class="section-num" style="background:#059669">4</div>
            <h3>Jumlah Data Murid <span class="tag-sistemik">🔗 DAPODIK & PROFIL SISTEMIK</span></h3>
            <svg class="chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M6 9l6 6 6-6" />
            </svg>
        </div>
        @php
            $data_value = [
                'jumlah_murid_dapodik' => $dapodik->jumlah_pd ?? '',
                'jumlah_murid_profil' => $jumlah_siswa->jumlah ?? '',
                'jumlah_murid_riil' => $jumlah_siswa->jumlah ?? '',
                'jumlah_rombel' => '',
                'status_jumlah_murid' => '',
                'catatan_jumlah_murid' => '',
                'daftar_hadir_murid' => '',
                'status_daftar_hadir' => '',
                'catatan_daftar_hadir_murid' => '',
                'buku_induk_murid' => '',
                'foto_murid' => $jumlah_siswa->jumlah_foto ?? '',
                'jumlah_kk_akte_murid' => $jumlah_siswa->jumlah_akta ?? '',
                'kk_akte_murid' => '',
                'npsn' => $sekolah->npsn ?? '',
            ];

            if (!empty($monev)) {
                $data_value = [
                    'jumlah_murid_dapodik' => !empty($monev->jumlah_murid_dapodik)
                        ? $monev->jumlah_murid_dapodik
                        : $data_value['jumlah_murid_dapodik'],
                    'jumlah_murid_profil' => !empty($monev->jumlah_murid_profil)
                        ? $monev->jumlah_murid_profil
                        : $data_value['jumlah_murid_profil'],
                    'jumlah_murid_riil' => !empty($monev->jumlah_murid_riil)
                        ? $monev->jumlah_murid_riil
                        : $data_value['jumlah_murid_riil'],
                    'jumlah_rombel' => !empty($monev->jumlah_rombel)
                        ? $monev->jumlah_rombel
                        : $data_value['jumlah_rombel'],
                    'status_jumlah_murid' => !empty($monev->status_jumlah_murid)
                        ? $monev->status_jumlah_murid
                        : $data_value['status_jumlah_murid'],
                    'catatan_jumlah_murid' => !empty($monev->catatan_jumlah_murid)
                        ? $monev->catatan_jumlah_murid
                        : $data_value['catatan_jumlah_murid'],
                    'daftar_hadir_murid' => !empty($monev->daftar_hadir_murid)
                        ? $monev->daftar_hadir_murid
                        : $data_value['daftar_hadir_murid'],
                    'status_daftar_hadir' => !empty($monev->status_daftar_hadir)
                        ? $monev->status_daftar_hadir
                        : $data_value['status_daftar_hadir'],
                    'catatan_daftar_hadir_murid' => !empty($monev->catatan_daftar_hadir_murid)
                        ? $monev->catatan_daftar_hadir_murid
                        : $data_value['catatan_daftar_hadir_murid'],
                    'buku_induk_murid' => !empty($monev->buku_induk_murid)
                        ? $monev->buku_induk_murid
                        : $data_value['buku_induk_murid'],
                    'foto_murid' => !empty($monev->foto_murid) ? $monev->foto_murid : $data_value['foto_murid'],
                    'jumlah_kk_akte_murid' => !empty($monev->jumlah_kk_akte_murid)
                        ? $monev->jumlah_kk_akte_murid
                        : $data_value['jumlah_kk_akte_murid'],
                    'kk_akte_murid' => !empty($monev->kk_akte_murid)
                        ? $monev->kk_akte_murid
                        : $data_value['kk_akte_murid'],
                    'npsn' => !empty($monev->npsn) ? $monev->npsn : $data_value['npsn'],
                ];
            }
        @endphp
        <div class="section-body">
            <div class="komponen-wrap">
                <table class="komponen-table">
                    <thead>
                        <tr>
                            <th style="width:22%">Uraian</th>
                            <th style="width:10%">Dapodik <span class="tag-sistemik" style="font-size:.6rem">SIS</span>
                            </th>
                            <th style="width:10%">Profil <span class="tag-sistemik" style="font-size:.6rem">SIS</span>
                            </th>
                            <th style="width:10%">Riil (hitung)</th>
                            <th style="width:10%">Rombel</th>
                            <th style="width:10%">Status</th>
                            <th style="width:28%">Alasan Tidak Sesuai / Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>Jumlah Peserta Didik</strong>
                                <span class="juknis-btn" tabindex="0">?
                                    <span class="juknis-tip"><strong>Juknis:</strong> (a) Dapodik: dari data yg
                                        dibawa. (b) Profil: dari data yg dibawa. (c) Riil: hitung langsung di
                                        lapangan. Sesuai jika angka sama / selisih 1-2 anak dgn alasan jelas di
                                        daftar hadir.</span>
                                </span>
                            </td>
                            <td><input type="number" class="tbl-input narrow" placeholder="0"
                                    style="background:#f5f3ff" name="jumlah_murid_dapodik"
                                    value="{{ $data_value['jumlah_murid_dapodik'] }}" readonly></td>
                            <td>
                                <input type="number" class="tbl-input narrow" placeholder="0"
                                    style="background:#f5f3ff" name="jumlah_murid_profil"
                                    value="{{ $data_value['jumlah_murid_profil'] }}" readonly><br>
                                <a href="javascript:void(0)" onclick="detail('siswa','{{ $data_value['npsn'] }}')">Lihat
                                    data</a>
                            </td>
                            <td><input type="number" class="tbl-input narrow" placeholder="Hitung"
                                    name="jumlah_murid_riil" value="{{ $data_value['jumlah_murid_riil'] }}"></td>
                            <td><input type="number" class="tbl-input narrow" placeholder="Jml" name="jumlah_rombel"
                                    value="{{ $data_value['jumlah_rombel'] }}"></td>
                            <td>
                                <select class="status-select" onchange="updateStatusClass(this)"
                                    name="status_jumlah_murid">
                                    <option value="">— Pilih —</option>
                                    <option @if ($data_value['status_jumlah_murid'] == 'true') selected @endif value="true">Sesuai
                                    </option>
                                    <option @if ($data_value['status_jumlah_murid'] == 'false') selected @endif value="false">Tidak
                                        Sesuai</option>
                                </select>
                            </td>
                            <td>
                                <textarea class="tbl-textarea"
                                    placeholder="Wajib diisi jika Tidak Sesuai. Contoh: Dapodik 49, Profil 50, riil 30 — selisih besar, potensi kelebihan BOSP/Jaspel."
                                    name="catatan_jumlah_murid">{{ $data_value['catatan_jumlah_murid'] }}</textarea>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <h4 style="margin:1rem 0 .5rem;font-size:.88rem;color:var(--ink2)">4e. Kelengkapan Dokumen
                    Murid
                    <span class="tag-sistemik">🔗 AKTA & KK SISTEMIK</span>
                </h4>
                <table class="komponen-table">
                    <thead>
                        <tr>
                            <th>Dokumen</th>
                            <th>Data Sistem <span class="tag-sistemik">SIS</span></th>
                            <th>Kondisi Lapangan</th>
                            <th>Status</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Daftar hadir harian (bulan berjalan)</td>
                            <td style="color:var(--ink3);font-size:.78rem">—</td>
                            <td>
                                <select class="status-select" onchange="updateStatusClass(this)"
                                    name="daftar_hadir_murid" style="min-width:100px">
                                    <option value="">— Pilih —</option>
                                    <option @if ($data_value['daftar_hadir_murid'] == 'ada') selected @endif value="ada">Ada & Rutin
                                    </option>
                                    <option @if ($data_value['daftar_hadir_murid'] == 'belum') selected @endif value="belum">Ada tapi
                                        Tidak Rutin</option>
                                    <option @if ($data_value['daftar_hadir_murid'] == 'tidakada') selected @endif value="tidakada">Tidak Ada
                                    </option>
                                </select>
                            </td>
                            <td rowspan="4">
                                <select class="status-select" onchange="updateStatusClass(this)"
                                    name="status_daftar_hadir">
                                    <option value="">— Pilih —</option>
                                    <option @if ($data_value['status_daftar_hadir'] == 'true') selected @endif value="true">Ada
                                    </option>
                                    <option @if ($data_value['status_daftar_hadir'] == 'false') selected @endif value="false">Tidak Ada
                                    </option>
                                </select>
                            </td>
                            <td rowspan="4">
                                <textarea class="tbl-textarea" placeholder="Rincian mana yang ada dan mana yang tidak ada"
                                    name="catatan_daftar_hadir_murid">{{ $data_value['catatan_daftar_hadir_murid'] }}</textarea>
                            </td>
                        </tr>
                        <tr>
                            <td>Buku induk siswa</td>
                            <td style="color:var(--ink3);font-size:.78rem">—</td>
                            <td>
                                <select class="status-select" onchange="updateStatusClass(this)" name="buku_induk_murid"
                                    style="min-width:100px">
                                    <option value="">—</option>
                                    <option @if ($data_value['buku_induk_murid'] == 'true') selected @endif value="true">Ada
                                    </option>
                                    <option @if ($data_value['buku_induk_murid'] == 'false') selected @endif value="false">Tidak Ada
                                    </option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>Foto siswa terpasang</td>
                            <td style="color:var(--ink3);font-size:.78rem">-</td>
                            <td>
                                <select class="status-select" onchange="updateStatusClass(this)" name="foto_murid"
                                    style="min-width:100px">
                                    <option value="">—</option>
                                    <option @if ($data_value['foto_murid'] == 'true') selected @endif value="true">Ada
                                    </option>
                                    <option @if ($data_value['foto_murid'] == 'false') selected @endif value="false">Tidak
                                        Ada</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>KK dan Akta Kelahiran</strong>
                                <span class="juknis-btn" tabindex="0">?
                                    <span class="juknis-tip"><strong>Juknis:</strong> Cek apakah KK dan akta
                                        kelahiran tersimpan di sekolah. Data sistemik dicocokkan dengan dokumen
                                        fisik yang ada.</span>
                                </span>
                            </td>
                            <td>
                                <input type="text" class="tbl-input mono" placeholder="Jml record sistem"
                                    style="background:#f5f3ff;width:80px" name="jumlah_kk_akte_murid"
                                    value="{{ $data_value['jumlah_kk_akte_murid'] }}" readonly><br>
                                <a href="javascript:void(0)"
                                    onclick="detail('siswa_kk','{{ $data_value['npsn'] }}')">Lihat data</a>
                            </td>
                            <td>
                                <select class="status-select" onchange="updateStatusClass(this)" name="kk_akte_murid"
                                    style="min-width:100px">
                                    <option value="">—</option>
                                    <option @if ($data_value['kk_akte_murid'] == 'ada') selected @endif value="ada">Ada
                                        Lengkap</option>
                                    <option @if ($data_value['kk_akte_murid'] == 'belum') selected @endif value="belum">Sebagian
                                    </option>
                                    <option @if ($data_value['kk_akte_murid'] == 'tidakada') selected @endif value="tidakada">Tidak
                                        Ada</option>
                                </select>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div class="risk-box">
                    <h4>⚠ Catatan Potensi Risiko</h4>
                    Dapodik/Profil > riil → potensi kelebihan BOSP & Jaspel. Tidak ada daftar hadir atau kosong
                    banyak hari → pelanggaran Standar Pengelolaan (Permendikdasmen 26/2025 Pasal 2).
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
