<form onsubmit="simpan('form_bosp')" class="form_bosp">
    <!-- KOMPONEN 7: BOSP -->
    <div class="section" id="sec7">
        <div class="section-header" onclick="toggleSection(this)">
            <div class="section-num" style="background:#1e3a5f">7</div>
            <h3>Pengelolaan Dana BOSP (5 Sub-komponen) <span class="tag-sistemik">🔗 RKAS SISTEMIK</span></h3>
            <svg class="chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M6 9l6 6 6-6" />
            </svg>
        </div>
        <div class="section-body">
            <div class="komponen-wrap">
                <!-- 7a RKAS -->
                @php
                    $data_value = [
                        'rkas_semester' => $bosp->nominal ?? '',
                        'tahun_rkas' => $bosp->tahun ?? $tahun - 1,
                        'tahap_rkas' => $bosp->tahap ?? 'I',
                        'status_rkas' => '',
                        'catatan_rkas' => '',
                        'status_nota_rkas' => '',
                        'catatan_nota_rkas' => '',
                        'status_pembelian_rkas' => '',
                        'catatan_pembelian_rkas' => '',
                        'status_pelabelan' => '',
                        'catatan_pelabelan' => '',
                        'status_monitoring_dinas' => '',
                        'catatan_monitoring_dinas' => '',
                    ];

                    if (!empty($monev)) {
                        $data_value = [
                            'rkas_semester' => !empty($monev->rkas_semester)
                                ? $monev->rkas_semester
                                : $data_value['rkas_semester'],
                            'tahun_rkas' => !empty($monev->tahun_rkas) ? $monev->tahun_rkas : $data_value['tahun_rkas'],
                            'tahap_rkas' => !empty($monev->tahap_rkas) ? $monev->tahap_rkas : $data_value['tahap_rkas'],
                            'status_rkas' => !empty($monev->status_rkas)
                                ? $monev->status_rkas
                                : $data_value['status_rkas'],
                            'catatan_rkas' => !empty($monev->catatan_rkas)
                                ? $monev->catatan_rkas
                                : $data_value['catatan_rkas'],
                            'status_nota_rkas' => !empty($monev->status_nota_rkas)
                                ? $monev->status_nota_rkas
                                : $data_value['status_nota_rkas'],
                            'catatan_nota_rkas' => !empty($monev->catatan_nota_rkas)
                                ? $monev->catatan_nota_rkas
                                : $data_value['catatan_nota_rkas'],
                            'status_pembelian_rkas' => !empty($monev->status_pembelian_rkas)
                                ? $monev->status_pembelian_rkas
                                : $data_value['status_pembelian_rkas'],
                            'catatan_pembelian_rkas' => !empty($monev->catatan_pembelian_rkas)
                                ? $monev->catatan_pembelian_rkas
                                : $data_value['catatan_pembelian_rkas'],
                            'status_pelabelan' => !empty($monev->status_pelabelan)
                                ? $monev->status_pelabelan
                                : $data_value['status_pelabelan'],
                            'catatan_pelabelan' => !empty($monev->catatan_pelabelan)
                                ? $monev->catatan_pelabelan
                                : $data_value['catatan_pelabelan'],
                            'status_monitoring_dinas' => !empty($monev->status_monitoring_dinas)
                                ? $monev->status_monitoring_dinas
                                : $data_value['status_monitoring_dinas'],
                            'catatan_monitoring_dinas' => !empty($monev->catatan_monitoring_dinas)
                                ? $monev->catatan_monitoring_dinas
                                : $data_value['catatan_monitoring_dinas'],
                        ];
                    }
                @endphp
                <h4 style="margin-bottom:.5rem;font-size:.88rem;color:var(--accent)">7a. RKAS disusun sesuai Juknis
                    BOP dan disahkan <span class="tag-sistemik">🔗 RKAS SISTEMIK</span></h4>
                <table class="komponen-table">
                    <thead>
                        <tr>
                            <th>Uraian</th>
                            <th>Data Sistem</th>
                            <th>Status</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>RKAS Semester II 2025 <br>(Penerimaan BOP {{ $data_value['tahun_rkas'] }} -
                                    {{ $data_value['tahap_rkas'] }})</strong>
                                <span class="juknis-btn" tabindex="0">?
                                    <span class="juknis-tip"><strong>Juknis:</strong> Cek dokumen RKAS & halaman
                                        pengesahan. Jika tidak ada/belum disahkan → anggaran BOSP tidak dapat
                                        dipertanggungjawabkan (Pasal 42 Perwali 47/2013).</span>
                                </span>
                            </td>
                            <td>
                                <input type="hidden" name="tahun_rkas" value="{{ $data_value['tahun_rkas'] }}">
                                <input type="hidden" name="tahap_rkas" value="{{ $data_value['tahap_rkas'] }}">
                                <input type="text" class="tbl-input" placeholder="Status RKAS di sistem"
                                    style="background:#f5f3ff" name="rkas_semester"
                                    value="{{ $data_value['rkas_semester'] }}">
                            </td>
                            <td>
                                <select class="status-select" onchange="updateStatusClass(this)" name="status_rkas">
                                    <option value="">— Pilih —</option>
                                    <option @if ($data_value['status_rkas'] == 'true') selected @endif value="true">Ada
                                    </option>
                                    <option @if ($data_value['status_rkas'] == 'false') selected @endif value="false">Tidak Ada
                                    </option>
                                </select>
                            </td>
                            <td>
                                <textarea class="tbl-textarea" placeholder="Halaman pengesahan, tanda tangan pejabat berwenang" name="catatan_rkas">{{ $data_value['catatan_rkas'] }}</textarea>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <!-- 7b Belanja -->
                <h4 style="margin:1rem 0 .5rem;font-size:.88rem;color:var(--accent)">7b. Belanja BOSP sesuai item
                    di RKAS Sem II 2025</h4>
                <table class="komponen-table">
                    <thead>
                        <tr>
                            <th>Uraian</th>
                            <th>Status</th>
                            <th>Item Temuan di Luar RKAS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>Kesesuaian Nota/Invoice vs RKAS</strong>
                                <span class="juknis-btn" tabindex="0">?
                                    <span class="juknis-tip"><strong>Juknis:</strong> Cocokkan satu per satu:
                                        setiap nota dicocokkan dengan item RKAS. Jika ada belanja dari BOSP yang
                                        tidak ada di RKAS → Tidak Sesuai.</span>
                                </span>
                            </td>
                            <td>
                                <select class="status-select" onchange="updateStatusClass(this)"
                                    name="status_nota_rkas">
                                    <option value="">— Pilih —</option>
                                    <option @if ($data_value['status_nota_rkas'] == 'true') selected @endif value="true">Sesuai
                                    </option>
                                    <option @if ($data_value['status_nota_rkas'] == 'false') selected @endif value="false">Tidak
                                        Sesuai</option>
                                </select>
                            </td>
                            <td>
                                <textarea class="tbl-textarea" placeholder="Contoh: Sepeda anak Rp1.500.000 tidak ada di RKAS" name="catatan_nota_rkas">{{ $data_value['catatan_nota_rkas'] }}</textarea>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <!-- 7c Bukti Pembelian -->
                <h4 style="margin:1rem 0 .5rem;font-size:.88rem;color:var(--accent)">7c. Kelengkapan Bukti
                    Pembelian</h4>
                <table class="komponen-table">
                    <thead>
                        <tr>
                            <th>Dokumen</th>
                            <th>Status</th>
                            <th>Item Temuan Tanpa Bukti (Nama + Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>Invoice, Nota/Kuitansi, SP, BAST, Bukti Bayar Bank</strong>
                                <span class="juknis-btn" tabindex="0">?
                                    <span class="juknis-tip"><strong>Juknis:</strong> Cek: Invoice, Nota, SP, BAST
                                        (barang modal), Bukti Bayar Bank. Perhatikan: alamat penyedia = alamat
                                        Kepsek → konflik kepentingan.</span>
                                </span>
                            </td>
                            <td>
                                <select class="status-select" onchange="updateStatusClass(this)"
                                    name="status_pembelian_rkas">
                                    <option value="">— Pilih —</option>
                                    <option @if ($data_value['status_pembelian_rkas'] == 'true') selected @endif value="true">Lengkap
                                    </option>
                                    <option @if ($data_value['status_pembelian_rkas'] == 'false') selected @endif value="false">Tidak
                                        Lengkap</option>
                                </select>
                            </td>
                            <td>
                                <textarea class="tbl-textarea" placeholder="Contoh: Printer Rp2.951.490 tanpa bukti pembelian"
                                    name="catatan_pembelian_rkas">{{ $data_value['catatan_pembelian_rkas'] }}</textarea>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <!-- 7d Pelabelan -->
                <h4 style="margin:1rem 0 .5rem;font-size:.88rem;color:var(--accent)">7d. Pelabelan Permanen pada
                    Barang Modal BOSP</h4>
                <table class="komponen-table">
                    <thead>
                        <tr>
                            <th>Uraian</th>
                            <th>Status</th>
                            <th>Nama Barang Belum Berlabel</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>Barang modal diberi label permanen</strong> (stiker/plat/ukiran — nama PAUD
                                + "Dana BOSP")</td>
                            <td>
                                <select class="status-select" onchange="updateStatusClass(this)"
                                    name="status_pelabelan">
                                    <option value="">— Pilih —</option>
                                    <option @if ($data_value['status_pelabelan'] == 'true') selected @endif value="true">Sudah
                                    </option>
                                    <option @if ($data_value['status_pelabelan'] == 'false') selected @endif value="false">Belum
                                    </option>
                                </select>
                            </td>
                            <td>
                                <textarea class="tbl-textarea" placeholder="Daftar barang modal tanpa label" name="catatan_pelabelan">{{ $data_value['catatan_pelabelan'] }}</textarea>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <!-- 7e Pembinaan Dinas -->
                <h4 style="margin:1rem 0 .5rem;font-size:.88rem;color:var(--accent)">7e. Pembinaan/Monev dari Dinas
                    Pendidikan terkait BOSP</h4>
                <table class="komponen-table">
                    <thead>
                        <tr>
                            <th>Uraian</th>
                            <th>Status</th>
                            <th>Tahun/Bulan Terakhir</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>Pembinaan/monitoring dari Dinas</strong></td>
                            <td>
                                <select class="status-select" onchange="updateStatusClass(this)"
                                    name="status_monitoring_dinas">
                                    <option value="">— Pilih —</option>
                                    <option @if ($data_value['status_monitoring_dinas'] == 'true') selected @endif value="true">Pernah
                                    </option>
                                    <option @if ($data_value['status_monitoring_dinas'] == 'false') selected @endif value="false">Belum
                                        Pernah</option>
                                </select>
                            </td>
                            <td>
                                <textarea class="tbl-textarea" placeholder="Contoh: Monev terakhir Juni 2025, bentuk sosialisasi Zoom"
                                    name="catatan_monitoring_dinas">{{ $data_value['catatan_monitoring_dinas'] }}</textarea>
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
