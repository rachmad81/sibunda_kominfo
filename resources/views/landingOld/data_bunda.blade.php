@php
    $default_foto = 'https://i.pinimg.com/550x/42/85/cb/4285cb12b952b2c6f2909ce8558f48c9.jpg';
    $url_foto = $default_foto;
    $header = get_headers('https://dispendik.surabaya.go.id/sibunda_file/' . $bunda->path_foto, 1);
    $header1 = get_headers('http://103.76.17.11/sibunda_file/' . $bunda->path_foto, 1);
    if ($bunda->path_foto) {
        if ($header[0] == 'HTTP/1.1 200 OK') {
            $url_foto = 'https://dispendik.surabaya.go.id/sibunda_file/' . $bunda->path_foto;
        } elseif ($header1[0] == 'HTTP/1.1 200 OK') {
            $url_foto =
                'data:image/*;base64,' .
                base64_encode(file_get_contents('http://103.76.17.11/sibunda_file/' . $bunda->path_foto));
        } else {
            try {
                if (\Storage::exists($bunda->path_foto)) {
                    $foto = 'data:image/*;base64,' . base64_encode(Storage::get($bunda->path_foto));
                    $url_foto = $foto == '' ? $default_foto : $foto;
                }
            } catch (\Exception $e) {
                $url_foto = $default_foto;
            }
        }
    }
@endphp
<div class="carousel-item">
    <img class="d-block w-200" src="{{ asset('bg_asset/transparent.png') }}"
        style="background-image: url('{{ $url_foto }}'); margin-bottom: 25px;" alt="Second slide">
    <div class="carousel-caption">
        <h5 style="text-shadow: white;"><b>{{ $bunda->nama }}</b></h5>
        <p>{{ $bunda->nama_jabatan }} Tingkat {{ ucwords($bunda->tingkat) }} {{ $bunda->jabatan }} </p>
    </div>
</div>
