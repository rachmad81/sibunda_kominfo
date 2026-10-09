<div class="block-content block-content-full">
    @include('layout.alert')
    <div class="row">
        <div class="col-lg-1">
            <label>Kecamatan</label>
        </div>
        <div class="col-lg-4">
            <select style="width: 100%" name="kecamatan" onchange="get_data();get_kelurahan()"
                @if (in_array(Auth::getUser()->tingkat, ['kecamatan', 'kelurahan'])) disabled @endif>
                <option @if ($kode_kecamatan == '') selected @endif value="semua">Semua Kecamatan
                </option>
                @foreach ($kecamatan as $kec)
                    <option @if ($kode_kecamatan == $kec->kecamatan_kode) selected @endif value="{{ $kec->kecamatan_kode }}">
                        {{ $kec->kecamatan_dispenduk }}</option>
                @endforeach
            </select>
        </div>
    </div>
    {{-- <div class="row">
        <div class="col-lg-1">
            <label>Kelurahan</label>
        </div>
        <div class="col-lg-4">
            <select style="width: 100%" name="kelurahan" onchange="get_data()"
                @if (in_array(Auth::getUser()->tingkat, ['kelurahan'])) disabled @endif>
                <option @if ($kode_kelurahan == '') selected @endif value="semua">Semua Kelurahan
                </option>
                @foreach ($kelurahan as $kec)
                    <option @if ($kode_kelurahan == $kec->kelurahan_kode) selected @endif value="{{ $kec->kelurahan_kode }}">
                        {{ $kec->kelurahan_dispenduk }}</option>
                @endforeach
            </select>
        </div>
    </div> --}}
    <div class="row">
        <div class="col-lg-1">
            <label>Jenjang</label>
        </div>
        <div class="col-lg-4">
            @php
                $get_jenjang = ['KB', 'SPS', 'TPA', 'TK', 'RA', 'SD', 'MI'];
            @endphp
            <select style="width: 100%" name="jenjang" onchange="get_data()">
                <option @if ($jenjang == '') selected @endif value="semua">Semua Jenjang</option>
                @foreach ($get_jenjang as $j)
                    <option @if ($jenjang == $j) selected @endif value="{{ $j }}">
                        {{ $j }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-1">
            <label>Tempat</label>
        </div>
        <div class="col-lg-4">
            <select style="width: 100%" name="wisata" onchange="get_data()">
                <option selected value="semua">Semua Tempat</option>
                @foreach ($wisata as $w)
                    <option value="{{ $w->id }}">{{ $w->nama }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-1">
            <label>Tahun</label>
        </div>
        <div class="col-lg-4">
            <span style="width: 50%">
                <select style="width: 49%" name="tahun" onchange="get_data()">
                    <option selected value="">.. Pilih Tahun ..</option>
                    @for ($i = 2023; $i <= date('Y'); $i++)
                        <option @if ($tahun == $i) selected @endif value="{{ $i }}">
                            {{ $i }}</option>
                    @endfor
                    {{-- @foreach ($thn as $th)
                        <option @if ($tahun == $th->tahun) selected @endif value="{{ $th->tahun }}">
                            {{ $th->tahun }}</option>
                    @endforeach --}}
                </select>
            </span>
            <span style="width: 50%">
                @php
                    $bulan = ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'];
                    $bulan1 = [
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
                <select style="width: 49%" name="bulan" onchange="get_data()">
                    <option selected value="semua">Semua</option>
                    @for ($i = 0; $i < count($bulan); $i++)
                        <option value="{{ $bulan[$i] }}">{{ $bulan1[$i] }}</option>
                    @endfor
                </select>
            </span>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-1">
            <label>Status</label>
        </div>
        <div class="col-lg-4">
            <select style="width: 100%" name="status" onchange="get_data()">
                <option>Semua</option>
                <option selected>Pengajuan</option>
                <option>Reschedule</option>
                <option>Setujui</option>
                <option>Tolak</option>
            </select>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12" style="overflow: auto">
            <div>
                <center>
                    <h1 class="judul" style="display:none;">Talenta Seni</h1>
                </center>
            </div>
            <table id="ExTable1" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th rowspan="2" style="width:1%">No</th>
                        <th rowspan="2">NPSN</th>
                        <th rowspan="2">Nama Sekolah</th>
                        <th rowspan="2">Tanggal Pelaksanaan</th>
                        <th rowspan="2">Jam Mulai</th>
                        <th rowspan="2">Jam Selesai</th>
                        <th rowspan="2">Kecamatan</th>
                        <th rowspan="2">Kelurahan</th>
                        <th rowspan="2">Tempat Wisata</th>
                        <th rowspan="2">Alamat</th>
                        <th colspan="2">Jumlah Peserta Didik</th>
                        <th colspan="3">Penanggung Jawab</th>
                        <th rowspan="2">Pendamping</th>
                        <th rowspan="2">Status Jadwal</th>
                        <th rowspan="2">Catatan</th>
                        <th rowspan="2">Berkas Pendukung</th>
                        @if (Auth::getUser()->role == 'dinas')
                            <th rowspan="2">Aksi</th>
                        @endif
                    </tr>
                    <tr>
                        <th>Riil</th>
                        <th>Profil</th>
                        <th>Nama</th>
                        <th>Jabatan</th>
                        <th>No Hp</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
</div>
<script type="text/javascript">
    var dt_table;
    var judul = 'judul';
    var tahun = '';
    var kecamatan = '';
    var kelurahan = '';
    var jenjang = '';
    var wisata = '';
    var bulan = '';
    var status = '';
    $(document).ready(function() {
        dt_table = $("#ExTable1").DataTable({
            dom: "<'row'<'col-md-6'l><'col-md-6'B>>" +
                "<'row'<'col-md-12'f>>" +
                "<'row'<'col-md-12't>><'row'<'col-md-12'ip>>",
            buttons: [{
                    extend: 'excel',
                    title: 'judul',
                    filename: function() {
                        judul = $('.judul').html() + '_{{ date('d-m-Y H.i') }}';
                        return judul;
                    }
                },
                {
                    extend: 'print',
                    title: function() {
                        judul = $('.judul').html();
                        return judul;
                    },
                    filename: function() {
                        judul = $('.judul').html() + '_{{ date('d-m-Y H.i') }}';
                        return judul;
                    }
                },
            ],
            lengthMenu: [
                [10, 25, 50, -1],
                [10, 25, 50, "All"]
            ],
            processing: true,
            serverSide: true,
            ajax: {
                url: '{!! route('bunda-talenta-get_data') !!}',
                type: 'post',
                data: function(d) {
                    d.tahun = $('select[name=tahun]').val();
                    d.kec = $('select[name=kecamatan]').val();
                    d.kel = $('select[name=kelurahan]').val();
                    d.jenjang = $('select[name=jenjang]').val();
                    d.wisata = $('select[name=wisata]').val();
                    d.bulan = $('select[name=bulan]').val();
                    d.status = $('select[name=status]').val();
                }
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'npsn',
                    name: 'npsn'
                },
                {
                    data: 'nama_sekolah',
                    name: 'nama_sekolah'
                },
                {
                    data: 'tanggal_pelaksanaan',
                    name: 'tanggal_pelaksanaan'
                },
                {
                    data: 'jam_mulai',
                    name: 'jam_mulai'
                },
                {
                    data: 'jam_selesai',
                    name: 'jam_selesai'
                },
                {
                    data: 'nama_kecamatan',
                    name: 'nama_kecamatan'
                },
                {
                    data: 'nama_kelurahan',
                    name: 'nama_kelurahan'
                },
                {
                    data: 'nama_tempat',
                    name: 'nama_tempat'
                },
                {
                    data: 'alamat_tempat',
                    name: 'alamat_tempat'
                },
                {
                    data: 'jumlah_pd',
                    name: 'jadwal.jumlah_pd'
                },
                {
                    data: 'jumlah_pd_profil',
                    name: 'jadwal.jumlah_pd_profil'
                },
                {
                    data: 'nama_penanggung_jawab',
                    name: 'jadwal.nama_penanggung_jawab'
                },
                {
                    data: 'jabatan_penanggung_jawab',
                    name: 'jadwal.jabatan_penanggung_jawab'
                },
                {
                    data: 'no_hp_penanggung_jawab',
                    name: 'no_hp_penanggung_jawab'
                },
                {
                    data: 'pendampings',
                    name: 'pendampings'
                },
                {
                    data: 'status',
                    name: 'status'
                },
                {
                    data: 'catatan_reschedule',
                    name: 'jadwal.catatan_reschedule'
                },
                {
                    data: 'berkas',
                    name: 'berkas',
                    className: 'dt-left'
                },
                @if (Auth::getUser()->role == 'dinas')
                    {
                        data: 'aksi',
                        name: 'aksi',
                        className: 'dt-left'
                    },
                @endif
            ]
        });

        @if (Auth::getuser()->tingkat == 'kecamatan')
            get_kelurahan();
        @endif
    });

    function get_data() {
        $('tbody').html('<tr><td colspan="11"><h4>Harap tunggu.....</h4></td></tr>');

        var title = "Talenta Seni";

        $('.judul').html(title);
        judul = $('.judul').html();

        dt_table.ajax.reload();
    }

    function get_kelurahan() {
        var kecamatan = $('select[name=kecamatan]').val();
        $.post("{{ url('bunda/umum/get_kelurahan') }}", {
            id: kecamatan
        }, function(data) {
            var html = '<option value="semua"> Semua Kelurahan</option>';
            $.each(data, function(k, v) {
                html += '<option value="' + v.kelurahan_kode + '">' + v.kelurahan_dispenduk +
                    '</option>';
            });

            $('select[name=kelurahan]').html(html);
        });
    }

    function modal_konfirmasi(id) {
        $.post("{{ route('bunda-talenta-modal_konfirmasi') }}", {
            id: id
        }, function(data) {
            $('.modal-page').html(data.content);
        });
    }

    function get_jadwal(id) {
        var tempat_id = $('select[name=tempat_pelaksanaan]').val();
        var tanggal = $('select[name=tanggal]').val();
        var data = {
            tempat_id: tempat_id,
            jadwal_id: id,
            tanggal: tanggal,
        };
        $.post("{{ route('bunda-talenta-get_jadwal') }}", data, function(data) {
            var html = '<tr><td colspan="3">Data kosong</td></tr>';
            if (data.jadwal_sama.length != 0) {
                html = '';
                html += '<tr>';
                html += '<th> NPSN </th>';
                html += '<th> Nama Sekolah </th>';
                html += '<th> Jumlah PD </th>';
                html += '<th> Status Jadwal </th>';
                html += '</tr>';
                $.each(data.jadwal_sama, function(k, v) {
                    html += '<tr>';
                    html += '<td>' + v.npsn + '</td>';
                    html += '<td>' + v.nama_sekolah + '</td>';
                    html += '<td>' + v.jumlah_peserta + '</td>';
                    html += '<td>' + v.status_jadwal + '</td>';
                    html += '</tr>';
                })
            }

            $('#tempat_jadwal_lain').html(html);

            if (data.hari_buka == '') {
                $('select[name=tanggal]').html(null);
                $('input[name=jam_mulai]').val(null);
                $('input[name=jam_selesai]').val(null);
                $('.jumlah_peserta').html(null);
            } else {
                var html_tanggal = '';
                if (data.hari_buka.length > 0) {
                    $.each(data.hari_buka, function(k, v) {
                        if (v.tanggal == tanggal) {
                            html_tanggal += '<option selected>' + v.tanggal + '</option>';
                        } else {
                            html_tanggal += '<option>' + v.tanggal + '</option>';
                        }
                    });
                }
                $('select[name=tanggal]').html(html_tanggal);
                $('input[name=jam_mulai]').val(data.baris.mulai);
                $('input[name=jam_selesai]').val(data.baris.selesai);
                $('.jumlah_peserta').html('Lembaga = ' + data.jumlah_lembaga + '/' + data.baris
                    .jumlah_lembaga + '<br>Peserta = ' + data.jumlah_peserta + '/' + (data.baris
                        .jumlah_lembaga * 20));
            }
        });
    }
</script>
