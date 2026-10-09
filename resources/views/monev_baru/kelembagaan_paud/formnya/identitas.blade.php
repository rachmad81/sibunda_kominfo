<form onsubmit="simpan('form_identitas')" class="form_identitas">
    <!-- IDENTITAS -->
    <div class="identity-card" id="identitas">
        <h2>
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                <circle cx="12" cy="7" r="4" />
            </svg>
            Data Identitas Satuan PAUD
        </h2>
        @php
            $data_value = [
                'nama_satuan' => $sekolah->nama_lembaga ?? '',
                'npsn' => $sekolah->npsn ?? '',
                'jenis_satuan' => $sekolah->jenjang ?? '',
                'kecamatan' => $sekolah->kecamatan_dispenduk ?? '',
                'kelurahan' => $sekolah->kelurahan_dispenduk ?? '',
                'alamat_satuan' => $sekolah->alamat ?? '',
                'nama_ks' => $data_ks->nama ?? '',
                'no_ks' => $data_ks->hp ?? '',
                'tanggal_kunjungan' => '',
                'jam_kunjungan' => '',
                'nama_bunda_kecamatan' => $bunda_kecamatan->nama ?? '',
                'nama_bunda_kelurahan' => $bunda_kelurahan->nama ?? '',
                'lat_long' => $sekolah->lat_long ?? '',
            ];
            if (!empty($monev)) {
                $data_value = [
                    'nama_satuan' => !empty($monev->nama_satuan) ? $monev->nama_satuan : $data_value['nama_satuan'],
                    'npsn' => !empty($monev->npsn) ? $monev->npsn : $data_value['npsn'],
                    'jenis_satuan' => !empty($monev->jenis_satuan) ? $monev->jenis_satuan : $data_value['jenis_satuan'],
                    'kecamatan' => !empty($monev->kecamatan) ? $monev->kecamatan : $data_value['kecamatan'],
                    'kelurahan' => !empty($monev->kelurahan) ? $monev->kelurahan : $data_value['kelurahan'],
                    'alamat_satuan' => !empty($monev->alamat_satuan)
                        ? $monev->alamat_satuan
                        : $data_value['alamat_satuan'],
                    'nama_ks' => !empty($monev->nama_ks) ? $monev->nama_ks : $data_value['nama_ks'],
                    'no_ks' => !empty($monev->no_ks) ? $monev->no_ks : $data_value['no_ks'],
                    'tanggal_kunjungan' => !empty($monev->tanggal_kunjungan)
                        ? $monev->tanggal_kunjungan
                        : $data_value['tanggal_kunjungan'],
                    'jam_kunjungan' => !empty($monev->jam_kunjungan)
                        ? $monev->jam_kunjungan
                        : $data_value['jam_kunjungan'],
                    'nama_bunda_kecamatan' => !empty($monev->nama_bunda_kecamatan)
                        ? $monev->nama_bunda_kecamatan
                        : $data_value['nama_bunda_kecamatan'],
                    'nama_bunda_kelurahan' => !empty($monev->nama_bunda_kelurahan)
                        ? $monev->nama_bunda_kelurahan
                        : $data_value['nama_bunda_kelurahan'],
                    'lat_long' => !empty($monev->lat_long) ? $monev->lat_long : $data_value['lat_long'],
                ];
            }
        @endphp
        <div class="id-grid">
            <div class="id-field">
                <label>Nama Satuan PAUD</label>
                <input type="text" placeholder="Masukkan nama satuan PAUD" readonly name="nama_satuan"
                    value="{{ $data_value['nama_satuan'] }}">
            </div>
            <div class="id-field">
                <label>NPSN</label>
                <input type="text" placeholder="8 digit NPSN" class="mono" readonly name="npsn"
                    value="{{ $data_value['npsn'] }}">
            </div>
            <div class="id-field">
                <label>Jenis Satuan</label>
                <select id="jenisSatuan" disabled>
                    <option @if ($data_value['jenis_satuan'] == '') selected @endif value="">— Pilih —</option>
                    <option @if ($data_value['jenis_satuan'] == 'TK') selected @endif value="TK">TK (Taman
                        Kanak-Kanak)</option>
                    <option @if ($data_value['jenis_satuan'] == 'RA') selected @endif value="RA">RA (Raudhatul
                        Athfal)</option>
                    <option @if ($data_value['jenis_satuan'] == 'KB') selected @endif value="KB">KB (Kelompok
                        Bermain)</option>
                    <option @if ($data_value['jenis_satuan'] == 'TPA') selected @endif value="TPA">TPA (Taman
                        Penitipan Anak)</option>
                    <option @if ($data_value['jenis_satuan'] == 'SPS') selected @endif value="SPS">PPT (Pos PAUD
                        Terpadu)</option>
                </select>
                <input type="hidden" name="jenis_satuan" value="{{ $data_value['jenis_satuan'] }}">
            </div>
            <div class="id-field">
                <label>Kecamatan</label>
                <input type="text" placeholder="Nama kecamatan" readonly name="kecamatan"
                    value="{{ $data_value['kecamatan'] }}">
            </div>
            <div class="id-field">
                <label>Kelurahan</label>
                <input type="text" placeholder="Nama kelurahan" readonly name="kelurahan"
                    value="{{ $data_value['kelurahan'] }}">
            </div>
            <div class="id-field">
                <label>Alamat Lengkap</label>
                <input type="text" placeholder="Jl. / RT/RW" readonly name="alamat_satuan"
                    value="{{ $data_value['alamat_satuan'] }}">
            </div>
            <div class="id-field">
                <label>Nama Kepala PAUD</label>
                <input type="text" placeholder="Nama lengkap" readonly name="nama_ks"
                    value="{{ $data_value['nama_ks'] }}">
            </div>
            <div class="id-field">
                <label>No. HP Kepala PAUD</label>
                <input type="tel" placeholder="08xxxxxxxxxx" readonly name="no_ks"
                    value="{{ $data_value['no_ks'] }}">
            </div>
            <div class="id-field">
                <label>Tanggal Kunjungan</label>
                <input type="date" name="tanggal_kunjungan" required value="{{ $data_value['tanggal_kunjungan'] }}"
                    style="background: #fff">
            </div>
            <div class="id-field">
                <label>Jam Kunjungan</label>
                <input type="time" name="jam_kunjungan" step="1" required
                    value="{{ $data_value['jam_kunjungan'] }}" style="background: #fff">
            </div>
            <div class="id-field">
                <label>Nama Bunda PAUD Kecamatan</label>
                <input type="text" readonly name="nama_bunda_kecamatan"
                    value="{{ $data_value['nama_bunda_kecamatan'] }}">
            </div>
            <div class="id-field">
                <label>Nama Bunda PAUD Kelurahan</label>
                <input type="text" readonly name="nama_bunda_kelurahan"
                    value="{{ $data_value['nama_bunda_kelurahan'] }}">
            </div>
            <div class="id-field">
                <label>Koordinat Sekolah</label>
                <input type="text" readonly name="lat_long" value="{{ $data_value['lat_long'] }}">
                @if ($data_value['lat_long'] != '')
                    <a href="https://www.google.com/maps/search/?api=1&query={{ $data_value['lat_long'] }}"
                        target="blank">Lihat
                        Map</a>
                @endif
            </div>

        </div>
        <div class="id-fielad" style="margin-top: 5px">
            <button class="btn btn-primary" type="submit">
                Simpan
            </button>
        </div>
    </div>
</form>
