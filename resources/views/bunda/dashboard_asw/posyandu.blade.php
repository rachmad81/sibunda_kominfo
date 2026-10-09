<style>
    table {
        border-collapse: collapse;
        width: 100%;
    }

    th,
    td {
        border: 1px solid #000;
    }
</style>
<div class="row">
    <div class="col-lg-12">
        <div style="width: 100%;margin: auto">
            <h3>{{ $judul }}</h3>
            <div class="row">
                @foreach ($data_posyandu as $pos)
                    <div class="col-lg-4">
                        <div class="block block-success">
                            <div class="block-header block-header-default row no-margin flex">
                                <h3 class="block-title">Jumlah {{ $pos->kategori }}</h3>
                            </div>
                            <div class="block-content block-content-full flex">
                                @php
                                    $param = [
                                        'judul' => 'Data Posyandu (' . $pos->kategori . ')',
                                        'kelamin' => '',
                                        'kategori' => $pos->kategori,
                                    ];
                                    $param = base64_encode(json_encode($param));
                                @endphp
                                <h1 style="cursor: pointer" onclick="modal('{{ $jenis }}','{{ $param }}')">
                                    {{ number_format($pos->jumlah, 0, '', '.') }} orang
                                </h1>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
