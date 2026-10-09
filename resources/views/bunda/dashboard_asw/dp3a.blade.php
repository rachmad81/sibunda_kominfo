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
        </div>
    </div>
    <div class="col-lg-4">
        <h5>Tahun Pengaduan</h5>
        <select class="form-control tahun_pengaduan" onchange="cari_data_dp3a()">
            @foreach ($data as $k => $v)
                <option>{{ $k }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-lg-12" style="margin-top: 10px">
        <div style="width: 100%;margin: auto">
            <h3>Tahun Pengaduan <span class="wadah_tahun"></span></h3>
        </div>
        <div class="row">
            {{-- Jumlah Total --}}
            <div class="col-lg-4">
                <div class="block block-primary">
                    <div class="block-header block-header-default row no-margin flex">
                        <h3 class="block-title">Jumlah Total</h3>
                    </div>
                    <div class="block-content block-content-full flex">
                        @php
                            $param = [
                                'judul' => 'Data Pengaduan DP3A',
                                'kelamin' => '',
                                'kategori' => 'total',
                            ];
                            $param = base64_encode(json_encode($param));
                        @endphp
                        <h1 class="wadah_total" style="cursor: pointer"
                            onclick="modal('{{ $jenis }}','{{ $param }}')"></h1>
                    </div>
                </div>
            </div>
            {{-- Jumlah Puspaga --}}
            <div class="col-lg-4">
                <div class="block block-success">
                    <div class="block-header block-header-default row no-margin flex">
                        <h3 class="block-title">Jumlah PUSPAGA</h3>
                    </div>
                    <div class="block-content block-content-full flex">
                        @php
                            $param = [
                                'judul' => 'Data Pengaduan DP3A Penanganan PUSPAGA',
                                'kelamin' => '',
                                'kategori' => 'puspaga',
                            ];
                            $param = base64_encode(json_encode($param));
                        @endphp
                        <h1 class="wadah_puspaga" style="cursor: pointer"
                            onclick="modal('{{ $jenis }}','{{ $param }}')"></h1>
                    </div>
                </div>
            </div>
            {{-- Jumlah UPTD PPA --}}
            <div class="col-lg-4">
                <div class="block block-warning">
                    <div class="block-header block-header-default row no-margin flex">
                        <h3 class="block-title">Jumlah UPTD PPA</h3>
                    </div>
                    <div class="block-content block-content-full flex">
                        @php
                            $param = [
                                'judul' => 'Data Pengaduan DP3A Penanganan UPTD PPA',
                                'kelamin' => '',
                                'kategori' => 'ppa',
                            ];
                            $param = base64_encode(json_encode($param));
                        @endphp
                        <h1 class="wadah_ppa" style="cursor: pointer"
                            onclick="modal('{{ $jenis }}','{{ $param }}')"></h1>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    var data_dp3a = [];
    @foreach ($data as $k => $v)
        data_dp3a['{{ $k }}'] = {!! json_encode($v) !!};
    @endforeach

    cari_data_dp3a();

    function cari_data_dp3a() {
        var tahun = $('.tahun_pengaduan').val();
        var kec = $('.kecamatan option:selected');
        var kecamatan = kec.val();
        var kecamatan_kode = kec.data('kode');
        var kel = $('.kelurahan option:selected');
        var kelurahan = kel.val();
        var kelurahan_kode = kel.data('kode');

        $('.wadah_tahun').html(tahun);
        $('.wadah_tahun').html(tahun);
        $('.wadah_total').html(data_dp3a[tahun]['jumlah_total'] + ' Orang');
        $('.wadah_puspaga').html(data_dp3a[tahun]['jumlah_puspaga'] + ' Orang');
        $('.wadah_ppa').html(data_dp3a[tahun]['jumlah_ppa'] + ' Orang');
    }
</script>
