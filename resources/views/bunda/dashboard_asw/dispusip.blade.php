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
    <div class="col-lg-3">
        <h5>Tahun Kunjungan</h5>
        <select class="form-control tahun_kunjungan" onchange="cari_data_dp3a()">
            @foreach ($tahun as $v)
                <option>{{ $v }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-lg-3">
        <h5>Bulan</h5>
        @php
            $bulan = [
                'Januari',
                'Februari',
                'Maret',
                'April',
                'Mei',
                'Juni',
                'Juli',
                'Agustus',
                'September',
                'Oktober',
                'November',
                'Desember',
            ];
        @endphp
        <select class="form-control bulan_kunjungan" onchange="cari_data_dp3a()">
            @foreach ($bulan as $k => $v)
                <option value="{{ $k + 1 }}">{{ $v }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-lg-12" style="margin-top: 10px">
        <div style="width: 100%;margin: auto">
            <h3>Tahun Pengaduan <span class="wadah_tahun"></span></h3>
        </div>
        <div class="row">
            {{-- PERPUSTAKAAN RUNGKUT --}}
            <div class="col-lg-4">
                <div class="block block-primary">
                    <div class="block-header block-header-default row no-margin flex">
                        <h3 class="block-title">Jumlah Pengunjung PERPUSTAKAAN RUNGKUT</h3>
                    </div>
                    <div class="block-content block-content-full flex">
                        @php
                            $param = [
                                'judul' => 'Data Pengunjung di PERPUSTAKAAN RUNGKUT',
                                'kelamin' => '',
                                'kategori' => 'rungkut',
                            ];
                            $param = base64_encode(json_encode($param));
                        @endphp
                        <h1 class="wadah_rungkut" style="cursor: pointer"
                            onclick="modal('{{ $jenis }}','{{ $param }}')"></h1>
                    </div>
                </div>
            </div>
            {{-- PERPUSTAKAAN BALAI PEMUDA --}}
            <div class="col-lg-4">
                <div class="block block-primary">
                    <div class="block-header block-header-default row no-margin flex">
                        <h3 class="block-title">Jumlah Pengunjung PERPUSTAKAAN BALAI PEMUDA</h3>
                    </div>
                    <div class="block-content block-content-full flex">
                        @php
                            $param = [
                                'judul' => 'Data Pengunjung di PERPUSTAKAAN BALAI PEMUDA',
                                'kelamin' => '',
                                'kategori' => 'pemuda',
                            ];
                            $param = base64_encode(json_encode($param));
                        @endphp
                        <h1 class="wadah_pemuda" style="cursor: pointer"
                            onclick="modal('{{ $jenis }}','{{ $param }}')"></h1>
                    </div>
                </div>
            </div>
            {{-- PERPUSTAKAAN LAINNYA --}}
            <div class="col-lg-4">
                <div class="block block-primary">
                    <div class="block-header block-header-default row no-margin flex">
                        <h3 class="block-title">Jumlah Pengunjung Perpustakaan Lainnya</h3>
                    </div>
                    <div class="block-content block-content-full flex">
                        @php
                            $param = [
                                'judul' => 'Data Pengunjung di Perpustakaan Lainnya',
                                'kelamin' => '',
                                'kategori' => 'lainnya',
                            ];
                            $param = base64_encode(json_encode($param));
                        @endphp
                        <h1 class="wadah_lainnya" style="cursor: pointer"
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
        var tahun = $('.tahun_kunjungan').val();
        var bulan = $('.bulan_kunjungan').val();
        var indexnya = tahun + '-' + bulan;


        var kec = $('.kecamatan option:selected');
        var kecamatan = kec.val();
        var kecamatan_kode = kec.data('kode');
        var kel = $('.kelurahan option:selected');
        var kelurahan = kel.val();
        var kelurahan_kode = kel.data('kode');

        if (typeof data_dp3a[indexnya] === 'undefined') {
            $('.wadah_rungkut').html('');
            $('.wadah_pemuda').html('');
            $('.wadah_lainnya').html('');
        } else {
            if (data_dp3a[indexnya].length > 0) {
                $.each(data_dp3a[indexnya], function(k, v) {
                    $('.wadah_' + v.tempat).html(v.jumlah_total + ' Orang');
                })
            } else {
                $('.wadah_rungkut').html('');
                $('.wadah_pemuda').html('');
                $('.wadah_lainnya').html('');
            }
        }
    }
</script>
