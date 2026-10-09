<div style="page-break-after: always">&nbsp;</div>
<div style="width: 100%">
    <table style="width: 100%">
        <tr>
            <td style="width:60%"></td>
            <td>
                Lampiran Keputusan<br>
                Nomor: {{ $monev->nomor }}<br>
                Tanggal: {{ date('d-m-Y', strtotime($monev->tanggal_verifikasi)) }}
            </td>
        </tr>
    </table>
</div>
<div style="width:100%;margin-top: 1cm;text-align: center;font-weight: bold">
    PENGANGKATAN PENDIDIK DAN TENAGA KEPENDIDIKAN<br>
    POS PAUD TERPADU (PPT) {{ $sekolah->nama_lembaga }}<br>
    KELURAHAN {{ $sekolah->kelurahan_dispenduk }} KECAMATAN {{ $sekolah->kecamatan_dispenduk }}<br>
    KOTA SURABAYA
</div>
<div style="width:100%;margin-top: 0.5cm;">
    <table style="width: 100%;border-collapse: collapse" border="1">
        <thead>
            <tr>
                <th>Nama</th>
                <th>NIK</th>
                <th>Tempat Tanggal Lahir</th>
                <th>Alamat KK</th>
                <th>Pend. Terakhir</th>
                <th>Jabatan</th>
            </tr>
        </thead>
        <tbody>
            @if ($gtk->count() > 0)
                @foreach ($gtk as $gt)
                    <tr>
                        <td>{{ $gt->nama }}</td>
                        <td>{{ $gt->nik }}</td>
                        <td>{{ $gt->tempat_lahir }}, {{ date('d-m-Y', strtotime($gt->tgl_lahir)) }}</td>
                        <td>{{ $gt->alamat }}</td>
                        <td>{{ $gt->pend_akhir }}</td>
                        <td>{{ $gt->nama_jabatan }}</td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>
    <table style="width: 100%;margin-top:0.5cm">
        <tr>
            <td style="width:60%"></td>
            <td>
                Bunda Pendidikan Anak Usia Dini<br>
                Kecamatan {{ ucwords(strtolower($sekolah->kecamatan_dispenduk)) }}<br>
                <img style="height: 3cm" src="{{ $ttd_bunda->link_ttd ?? '' }}" alt="TTD BUNDA KECAMATAN"
                    onerror="this.onerror=null;this.src='https://png.pngtree.com/png-vector/20190917/ourmid/pngtree-not-found-circle-icon-vectors-png-image_1737851.jpg'"><br>
                {{ $bunda_kecamatan->nama }}
            </td>
        </tr>
    </table>
</div>
