<div>
    @foreach ($sd as $s)
        <a href="javascript:void(0)"
            onclick="set_sd('{{ $s['npsn'] ?? '' }}','{{ $s['nama'] ?? '' }}','{{ $s['nama_ks'] ?? '' }}','{{ $s['hp'] ?? '' }}','{{ $s['alamat'] ?? '' }}')">{{ $s['nama'] }}
            ({{ $s['npsn'] }})
        </a><br>
    @endforeach
</div>
