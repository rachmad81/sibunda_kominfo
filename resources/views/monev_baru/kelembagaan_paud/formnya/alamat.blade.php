<!-- KOMPONEN 2: KESESUAIAN ALAMAT -->
<form onsubmit="simpan('form_alamat')" class="form_alamat">
    <div class="section" id="sec2">
        <div class="section-header" onclick="toggleSection(this)">
            <div class="section-num" style="background:#0d9488">2</div>
            <h3>Kesesuaian Alamat dalam Izin Operasional</h3>
            <svg class="chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M6 9l6 6 6-6" />
            </svg>
        </div>
        @php
            $data_value = [
                'alamat_ijop' => $ijop->alamat_sekolah ?? '',
                'status_alamat_ijop' => '',
                'catatan_alamat_ijop' => '',
                'alamat_riil_sp' => $sekolah->alamat ?? '',
            ];

            if (!empty($monev)) {
                $data_value = [
                    'alamat_ijop' => !empty($monev->alamat_ijop) ? $monev->alamat_ijop : $data_value['alamat_ijop'],
                    'status_alamat_ijop' => !empty($monev->status_alamat_ijop)
                        ? $monev->status_alamat_ijop
                        : $data_value['status_alamat_ijop'],
                    'catatan_alamat_ijop' => !empty($monev->catatan_alamat_ijop)
                        ? $monev->catatan_alamat_ijop
                        : $data_value['catatan_alamat_ijop'],
                    'alamat_riil_sp' => !empty($monev->alamat_riil_sp)
                        ? $monev->alamat_riil_sp
                        : $data_value['alamat_riil_sp'],
                ];
            }
        @endphp
        <div class="section-body">
            <div class="komponen-wrap">
                <table class="komponen-table">
                    <thead>
                        <tr>
                            <th>Uraian</th>
                            <th>Isi</th>
                            <th>Status</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <strong>Alamat di Izin Operasional</strong>
                            </td>
                            <td>
                                <input type="text" class="tbl-input" placeholder="Alamat sesuai izin"
                                    name="alamat_ijop" value="{{ $data_value['alamat_ijop'] }}" readonly>
                            </td>
                            <td rowspan="2">
                                <select class="status-select" onchange="updateStatusClass(this)"
                                    name="status_alamat_ijop">
                                    <option value="">— Pilih —</option>
                                    <option @if ($data_value['status_alamat_ijop'] == 'true') selected @endif value="true">Sesuai
                                    </option>
                                    <option @if ($data_value['status_alamat_ijop'] == 'false') selected @endif value="false">Tidak
                                        Sesuai</option>
                                </select>
                            </td>
                            <td rowspan="2">
                                <textarea class="tbl-textarea"
                                    placeholder="Jika tidak sesuai, jelaskan perbedaan. Jika ada lembaga lain di lokasi sama, catat nama & status izin."
                                    name="catatan_alamat_ijop">{{ $data_value['catatan_alamat_ijop'] }}</textarea>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Alamat Riil di Lapangan</strong>
                                <span class="juknis-btn" tabindex="0">?
                                    <span class="juknis-tip"><strong>Juknis:</strong> Bandingkan alamat di Izin
                                        Ops
                                        dengan lokasi fisik KBM saat kunjungan. Pengamatan langsung, bukan
                                        berdasarkan keterangan pihak PAUD.</span>
                                </span>
                            </td>
                            <td><input type="text" class="tbl-input" placeholder="Alamat riil saat kunjungan"
                                    name="alamat_riil_sp" value="{{ $data_value['alamat_riil_sp'] }}">
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
