<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>No</th>
            <th>Waktu</th>
            <th>Nama Penyewa</th>
            <th>Nama Kegiatan</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @php
            $nomor = 1;
        @endphp
        @if ($hari_ini->count() > 0)
            @foreach ($hari_ini as $k => $s)
                <tr>
                    <td>{{ $nomor++ }}</td>
                    <td>{{ $s->jam }}</td>
                    <td>{{ $s->penyewa }}</td>
                    <td>{{ $s->kegiatan }}</td>
                    <td>{{ $s->status }}</td>
                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="5" class="text-center">Belum ada jadwal untuk tanggal ini</td>
            </tr>
        @endif
    </tbody>
</table>
