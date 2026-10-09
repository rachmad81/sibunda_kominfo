@extends('apresiasi.master')
@section('content')
    <!-- ROW 1: Rangkuman Statistik Cepat -->
    <div class="row mb-4">
        <div class="col-12 col-sm-6 col-xl-3 mb-3 mb-xl-0">
            <div class="card p-3 d-flex flex-row align-items-center justify-content-between h-100">
                <div>
                    <span class="text-muted fw-medium d-block mb-1" style="font-size: 0.85rem;">Total
                        Pengguna</span>
                    <h3 class="mb-0 fw-bold text-dark">12.450</h3>
                </div>
                <div class="bg-primary bg-opacity-10 p-3 rounded-3 text-primary">
                    <i class="bi bi-people-fill fs-3"></i>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3 mb-3 mb-xl-0">
            <div class="card p-3 d-flex flex-row align-items-center justify-content-between h-100">
                <div>
                    <span class="text-muted fw-medium d-block mb-1" style="font-size: 0.85rem;">Penjualan Hari
                        Ini</span>
                    <h3 class="mb-0 fw-bold text-dark">Rp 8,4M</h3>
                </div>
                <div class="bg-success bg-opacity-10 p-3 rounded-3 text-success">
                    <i class="bi bi-currency-dollar fs-3"></i>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3 mb-3 mb-xl-0">
            <div class="card p-3 d-flex flex-row align-items-center justify-content-between h-100">
                <div>
                    <span class="text-muted fw-medium d-block mb-1" style="font-size: 0.85rem;">Formulir
                        Terkirim</span>
                    <h3 class="mb-0 fw-bold text-dark">342</h3>
                </div>
                <div class="bg-warning bg-opacity-10 p-3 rounded-3 text-warning">
                    <i class="bi bi-file-earmark-text fs-3"></i>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3 mb-3 mb-xl-0">
            <div class="card p-3 d-flex flex-row align-items-center justify-content-between h-100">
                <div>
                    <span class="text-muted fw-medium d-block mb-1" style="font-size: 0.85rem;">Status
                        Server</span>
                    <h3 class="mb-0 fw-bold text-dark">99,9%</h3>
                </div>
                <div class="bg-info bg-opacity-10 p-3 rounded-3 text-info">
                    <i class="bi bi-cpu fs-3"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- ROW 2: FORM LENGKAP & PREVIEW TABEL -->
    <div class="row">

        <!-- KOLOM FORMULIR (Kiri) -->
        <div class="col-12 col-lg-7 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-file-earmark-ruled-fill me-2"></i>Form Entri Data &
                        Pendaftaran</span>
                    <span class="badge bg-primary">Baru</span>
                </div>
                <div class="card-body">
                    <form id="adminForm" onsubmit="handleFormSubmit(event)">

                        <!-- Baris Input Teks & Email -->
                        <div class="row g-3 mb-3">
                            <div class="col-12 col-sm-6">
                                <label for="inputNama" class="form-label">Nama Lengkap</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted"><i class="bi bi-person"></i></span>
                                    <input type="text" class="form-control" id="inputNama"
                                        placeholder="Contoh: Budi Santoso" required>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <label for="inputEmail" class="form-label">Alamat Email</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted"><i class="bi bi-envelope"></i></span>
                                    <input type="email" class="form-control" id="inputEmail" placeholder="budi@email.com"
                                        required>
                                </div>
                            </div>
                        </div>

                        <!-- Baris Input Tanggal & Jenis Layanan -->
                        <div class="row g-3 mb-3">
                            <div class="col-12 col-sm-6">
                                <label for="inputTanggal" class="form-label">Tanggal Bergabung</label>
                                <input type="date" class="form-control" id="inputTanggal" required>
                            </div>
                            <div class="col-12 col-sm-6">
                                <label for="selectKategori" class="form-label">Kategori Keanggotaan</label>
                                <select class="form-select" id="selectKategori" required>
                                    <option value="" selected disabled>Pilih Kategori...</option>
                                    <option value="Bronze">Bronze (Standard)</option>
                                    <option value="Silver">Silver (Medium)</option>
                                    <option value="Gold">Gold (Premium)</option>
                                    <option value="Platinum">Platinum (Exclusive)</option>
                                </select>
                            </div>
                        </div>

                        <!-- Unggah File Pendukung -->
                        <div class="mb-4">
                            <label for="inputFile" class="form-label">Dokumen Pendukung (PDF/Gambar)</label>
                            <div class="input-group">
                                <input type="file" class="form-control" id="inputFile" accept=".pdf,.png,.jpg,.jpeg">
                                <label class="input-group-text bg-light" for="inputFile"><i
                                        class="bi bi-upload"></i></label>
                            </div>
                            <div class="form-text text-muted" style="font-size: 0.75rem;">Maksimal ukuran
                                file: 2 MB. Format: JPG, PNG, PDF.</div>
                        </div>

                        <!-- Radio Button Group (Opsi Tunggal) -->
                        <div class="mb-4">
                            <label class="form-label d-block mb-2">Metode Pembayaran Utama</label>
                            <div class="bg-light p-3 rounded-3 border">
                                <div class="form-check form-check-inline me-4">
                                    <input class="form-check-input" type="radio" name="paymentMethod" id="radioTransfer"
                                        value="Transfer Bank" checked>
                                    <label class="form-check-label" for="radioTransfer">
                                        <i class="bi bi-bank text-primary me-1"></i> Transfer Bank
                                    </label>
                                </div>
                                <div class="form-check form-check-inline me-4">
                                    <input class="form-check-input" type="radio" name="paymentMethod"
                                        id="radioEwallet" value="E-Wallet">
                                    <label class="form-check-label" for="radioEwallet">
                                        <i class="bi bi-wallet2 text-success me-1"></i> Dompet Digital
                                        (E-Wallet)
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="paymentMethod" id="radioCc"
                                        value="Kartu Kredit">
                                    <label class="form-check-label" for="radioCc">
                                        <i class="bi bi-credit-card text-danger me-1"></i> Kartu Kredit
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Checkbox Group (Opsi Ganda) -->
                        <div class="mb-4">
                            <label class="form-label d-block mb-2">Pemberitahuan yang Diinginkan</label>
                            <div class="row g-2">
                                <div class="col-12 col-sm-6">
                                    <div class="form-check card p-2 mb-0 d-flex flex-row align-items-center gap-2 border">
                                        <input class="form-check-input ms-0" type="checkbox" id="checkPromo"
                                            value="Promo Khusus" checked>
                                        <label class="form-check-label" for="checkPromo">
                                            Email Penawaran & Promo
                                        </label>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="form-check card p-2 mb-0 d-flex flex-row align-items-center gap-2 border">
                                        <input class="form-check-input ms-0" type="checkbox" id="checkWeekly"
                                            value="Laporan Mingguan">
                                        <label class="form-check-label" for="checkWeekly">
                                            Laporan Analisis Mingguan
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Area Teks Masukan (Textarea) -->
                        <div class="mb-4">
                            <label for="textareaCatatan" class="form-label">Catatan Tambahan
                                (Opsional)</label>
                            <textarea class="form-control" id="textareaCatatan" rows="3"
                                placeholder="Tuliskan instruksi atau catatan tambahan khusus untuk pendaftar ini..."></textarea>
                        </div>

                        <!-- Switch Checkbox (Toggle Keaktifan Akun) -->
                        <div class="form-check form-switch mb-4">
                            <input class="form-check-input" type="checkbox" role="switch" id="switchStatus" checked>
                            <label class="form-check-label fw-medium" for="switchStatus">Aktifkan status akun
                                secara langsung</label>
                        </div>

                        <!-- Tombol Aksi -->
                        <div class="d-flex justify-content-end gap-2">
                            <button type="reset" class="btn btn-light px-4 border">Reset</button>
                            <button type="submit" class="btn btn-primary px-4"><i class="bi bi-save me-2"></i>Simpan
                                Data</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- KOLOM DAFTAR DATA / LOG AKTIVITAS (Kanan) -->
        <div class="col-12 col-lg-5">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-table me-2"></i>Pendaftar Terbaru</span>
                    <button class="btn btn-sm btn-outline-primary" onclick="showToast('Memperbarui data tabel...')">
                        <i class="bi bi-arrow-clockwise"></i>
                    </button>
                </div>
                <div class="card-body p-0">
                    <!-- Tabel Responsif -->
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="dataTabel">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Nama</th>
                                    <th>Kategori</th>
                                    <th>Status</th>
                                    <th class="text-end pe-3">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="ps-3">
                                        <div class="fw-semibold">Bambang Wijaya</div>
                                        <small class="text-muted">bambang@domain.com</small>
                                    </td>
                                    <td><span class="badge bg-warning text-dark">Gold</span></td>
                                    <td><span class="badge badge-active">Aktif</span></td>
                                    <td class="text-end pe-3">
                                        <button class="btn btn-sm btn-icon btn-light border"
                                            onclick="showToast('Edit Bambang')"><i class="bi bi-pencil"></i></button>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="ps-3">
                                        <div class="fw-semibold">Siti Rahma</div>
                                        <small class="text-muted">siti@example.com</small>
                                    </td>
                                    <td><span class="badge bg-secondary">Silver</span></td>
                                    <td><span class="badge badge-active">Aktif</span></td>
                                    <td class="text-end pe-3">
                                        <button class="btn btn-sm btn-icon btn-light border"
                                            onclick="showToast('Edit Siti')"><i class="bi bi-pencil"></i></button>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="ps-3">
                                        <div class="fw-semibold">Rian Ardiansyah</div>
                                        <small class="text-muted">rian.a@web.id</small>
                                    </td>
                                    <td><span class="badge bg-info text-dark">Platinum</span></td>
                                    <td><span class="badge bg-danger bg-opacity-10 text-danger">Nonaktif</span>
                                    </td>
                                    <td class="text-end pe-3">
                                        <button class="btn btn-sm btn-icon btn-light border"
                                            onclick="showToast('Edit Rian')"><i class="bi bi-pencil"></i></button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Panel Info Samping -->
                    <div class="p-3 bg-light border-top mt-auto">
                        <h6 class="fw-bold text-dark mb-2"><i class="bi bi-info-circle me-1 text-primary"></i>
                            Panduan Penggunaan</h6>
                        <p class="text-muted mb-0 small" style="line-height: 1.5;">
                            Gunakan formulir di sisi kiri untuk mendaftarkan mitra atau pelanggan baru secara
                            langsung ke dalam sistem database pusat. Pastikan dokumen yang diunggah valid.
                        </p>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
