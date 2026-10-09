<div class="block">
    <div class="block-header block-header-default row no-margin">
        <h3 class="block-title">Data anak belum masuk sekolah</h3>
    </div>
    <div class="block-content block-content-full">
        <div class="row">
            <div class="col-lg-12" style="overflow: auto">
                <div>
                    <center>
                        <h1 class="judul" style="display:none;">Detail anak</h1>
                    </center>
                </div>
            </div>
        </div>
        <div class="row">
            @php
                $tidak_sekolah = [
                    'Faktor Ekonomi' => [
                        'Biaya pendaftaran/masuk terlalu mahal',
                        'Biaya SPP/iuran bulanan tidak terjangkau ',
                        'Biaya transportasi ke PAUD terlalu tinggi',
                        'Biaya seragam dan perlengkapan sekolah terlalu mahal',
                        'Orang tua bekerja dan tidak ada yang mengantar-jemput anak',
                        'Lainnya',
                    ],
                    'Faktor Aksesibilitas / Jarak' => [
                        'Lokasi PAUD terlalu jauh dari tempat tinggal (> 2 km)',
                        'Tidak tersedia transportasi yang aman ke PAUD',
                        'Kondisi jalan tidak aman/sulit dilalui',
                        'Tidak ada PAUD di sekitar tempat tinggal',
                        'Jumlah PAUD terbatas sehingga tidak dapat menampung semua anak',
                        'PAUD terdekat sudah penuh/tidak menerima siswa baru',
                        'Lainnya',
                    ],
                    'Faktor Sosial Budaya' => [
                        'Persepsi bahwa pendidikan di rumah sudah cukup untuk anak usia 5-7 tahun',
                        'Pandangan bahwa anak masih terlalu kecil untuk sekolah',
                        'Kekhawatiran terhadap keamanan anak di PAUD',
                        'Pendidikan PAUD dianggap kurang penting',
                        'Tradisi/adat yang tidak mendukung pendidikan formal pada usia dini',
                        'Ketakutan terhadap bullying atau perlakuan tidak menyenangkan',
                        'Lainnya',
                    ],
                    'Faktor kondisi khusus anak (kesehatan/disabilitas/inklusi)' => [
                        'Anak memiliki kebutuhan khusus namun tidak ada PAUD inklusi yang sesuai',
                        'Anak memiliki kondisi kesehatan tertentu yang menghambat untuk bersekolah',
                        'Anak memiliki trauma terhadap lingkungan sekolah',
                        'Anak mengalami kesulitan bersosialisasi',
                        'Lainnya',
                    ],
                    'Alasan Administratif' => [
                        'Tidak memiliki dokumen yang diperlukan (Akta Kelahiran, KK, dll)',
                        'Proses pendaftaran terlalu rumit',
                        'Keluarga baru pindah ke lokasi saat ini',
                        'Status kependudukan yang belum jelas',
                        'Lainnya',
                    ],
                    'Lainnya' => '',
                ];
            @endphp
            <div class="col-lg-12" style="overflow: auto">
                <a href="javascript:void(0)" class="btn btn-sm btn-warning" onclick="kembali()">Kembali</a>
                <form class="form_sekolah">
                    <table class="table table-bordered table-striped">
                        <tr>
                            <td style="width: 40%">Nama</td>
                            <td>{{ $siswa->nama_lengkap }}</td>
                        </tr>
                        <tr>
                            <td>Tempat, tanggal lahir (jenis kelamin)</td>
                            <td>{{ $siswa->tempat_lahir }}, {{ date('d-m-Y', strtotime($siswa->tgl_lahir)) }}
                                ({{ $siswa->jenis_kelamin }})</td>
                        </tr>
                        <tr>
                            <td>Alamat</td>
                            <td>{{ $siswa->alamat }}, RT {{ $siswa->rt }}, RW {{ $siswa->rw }},
                                {{ $siswa->kelurahan }}, {{ $siswa->kecamatan }}</td>
                        </tr>
                        <tr>
                            <td>Status alamat sesuai KK</td>
                            <td>
                                <input type="hidden" name="nik" value="{{ $siswa->nik }}">
                                <label><input type="radio" @if ($siswa->status_alamat == 'Sesuai') checked @endif
                                        name="status_alamat" value="Sesuai" onclick="ganti_status('alamat')"> Sesuai
                                    KK</label><br>
                                <label><input type="radio" @if ($siswa->status_alamat == 'Tidak sesuai') checked @endif
                                        name="status_alamat" value="Tidak sesuai" onclick="ganti_status('alamat')">Tidak
                                    sesuai KK</label>
                            </td>
                        </tr>
                        <tr class="ta_detail_alamat">
                            <td>Catatan alamat</td>
                            <td>
                                <textarea name="detail_alamat" class="form-control" onkeyup="textAreaAdjust(this)">{!! $siswa->detail_alamat !!}</textarea>
                            </td>
                        </tr>
                        <tr>
                            <td>Status Sekolah</td>
                            <td>
                                <label><input type="radio" @if ($siswa->status_sekolah == 'Sudah') checked @endif
                                        name="status_sekolah" value="Sudah" onclick="ganti_status('sekolah')"> Sudah
                                    sekolah</label><br>
                                <label><input type="radio" @if ($siswa->status_sekolah == 'Tidak sekolah') checked @endif
                                        name="status_sekolah" value="Tidak sekolah"
                                        onclick="ganti_status('sekolah')">Tidak sekolah</label><br>
                                <label><input type="radio" @if ($siswa->status_sekolah == 'Akan sekolah') checked @endif
                                        name="status_sekolah" value="Akan sekolah" onclick="ganti_status('sekolah')">
                                    Akan sekolah</label>
                            </td>
                        </tr>
                        <tr class="ta_detail_sekolah">
                            <td>Catatan sekolah</td>
                            <td>
                                <textarea name="detail_sekolah" class="form-control" onkeyup="textAreaAdjust(this)">{!! $siswa->detail_sekolah !!}</textarea>
                            </td>
                        </tr>
                        <tr class="ta_detail_tidak_sekolah">
                            <td>Alasan tidak sekolah</td>
                            <td>
                                @php
                                    $arr_key = array_keys($tidak_sekolah);
                                    unset($arr_key[array_search('Lainnya', $arr_key)]);
                                @endphp
                                <select class="form-control" name="detail_tidak_sekolah"
                                    onchange="ganti_tidak_sekolah(this)">
                                    @foreach ($tidak_sekolah as $k => $v)
                                        @if (in_array($siswa->detail_tidak_sekolah, $arr_key))
                                            @if ($siswa->detail_tidak_sekolah == $k)
                                                <option selected>{{ $k }}</option>
                                            @else
                                                <option>{{ $k }}</option>
                                            @endif
                                        @else
                                            @if ($siswa->detail_tidak_sekolah != '' && $k == 'Lainnya')
                                                <option selected>{{ $k }}</option>
                                            @else
                                                <option>{{ $k }}</option>
                                            @endif
                                        @endif
                                    @endforeach
                                </select>
                                <textarea name="detail_tidak_sekolah_lainnya" class="form-control" onkeyup="textAreaAdjust(this)">{!! $siswa->detail_tidak_sekolah !!}</textarea>
                            </td>
                        </tr>
                        <tr class="ta_detail_tidak_sekolah1">
                            <td>Detail alasan</td>
                            <td>
                                <select class="form-control" name="detail_alasan_tidak_sekolah"
                                    onchange="ganti_tidak_sekolah_lainnya(this)">

                                </select>
                                <textarea name="detail_alasan_tidak_sekolah_lainnya" class="form-control" onkeyup="textAreaAdjust(this)">{!! $siswa->pilihan_detail_tidak_sekolah !!}</textarea>
                            </td>
                        </tr>
                        <tr>
                            <td>Bukti survei</td>
                            <td>
                                <input type="text" name="bukti_survei" class="form-control"
                                    value="{{ $siswa->bukti_survei }}">
                                <i>Upload link gdrive, pastikan settingan sudah di bagikan publik</i>
                            </td>
                        </tr>
                        <tr>
                            <td>Status Verifikasi</td>
                            <td>
                                <label><input type="radio" @if ($siswa->status_verifikasi == 'Draft') checked @endif
                                        name="status_verifikasi" value="Draft"> Draft</label><br>
                                <label><input type="radio" @if ($siswa->status_verifikasi == 'Selesai') checked @endif
                                        name="status_verifikasi" value="Selesai"> Selesai</label>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <a href="javascript:void(0)" class="btn btn-primary form-control"
                                    onclick="simpan()">Simpan</a>
                            </td>
                        </tr>
                    </table>
                </form>
                <a href="javascript:void(0)" class="btn btn-sm btn-warning" onclick="kembali()">Kembali</a>
            </div>
        </div>
    </div>
</div>
<script>
    function textAreaAdjust(element) {
        element.style.height = "1px";
        element.style.height = (25 + element.scrollHeight) + "px";
    }

    var tidak_sekolah_karena = JSON.parse('{!! json_encode($tidak_sekolah) !!}');
    $(document).ready(function() {
        textAreaAdjust($('textarea[name=detail_alamat]')[0]);
        textAreaAdjust($('textarea[name=detail_sekolah]')[0]);
        ganti_status('alamat');
        ganti_status('sekolah');
        ganti_tidak_sekolah($('select[name=detail_tidak_sekolah]')[0]);
        ganti_tidak_sekolah_lainnya($('select[name=detail_alasan_tidak_sekolah]')[0]);
    });

    {{-- $.fn.json_beautify= function() {
		this.each(function(){
			var el = $(this),
			obj = JSON.parse(el.val()),
			pretty = JSON.stringify(obj, undefined, 4);
			el.val(pretty);
		});
	}; --}}

    {{-- $('textarea').json_beautify(); --}}

    function ganti_status(bagian) {
        var status = $('input[name=status_' + bagian + ']:checked').val();
        $('.ta_detail_tidak_sekolah').hide();
        $('.ta_detail_tidak_sekolah1').hide();
        if (status == 'Tidak sesuai' || status == 'Sudah' || status == 'Akan sekolah') {
            $('.ta_detail_' + bagian).show();
        } else if (status == 'Tidak sekolah') {
            $('.ta_detail_tidak_sekolah').show();
            $('.ta_detail_tidak_sekolah1').show();
            $('.ta_detail_' + bagian).hide();
        } else {
            $('.ta_detail_' + bagian).hide();
        }
    }

    function ganti_tidak_sekolah(ini) {
        var status = ini.value;
        var status_sekolah = $('input[name=status_sekolah]:checked').val();
        var html = '';
        $('.ta_detail_tidak_sekolah1').hide();
        if (status == 'Lainnya') {
            $('textarea[name=detail_tidak_sekolah_lainnya]').show();
        } else {
            $('textarea[name=detail_tidak_sekolah_lainnya]').hide();

            if (status_sekolah == 'Tidak sekolah') {
                $('.ta_detail_tidak_sekolah1').show();
            }

            var nilai = '{{ $siswa->pilihan_detail_tidak_sekolah }}';

            $.each(tidak_sekolah_karena[status], function(k, v) {
                if (tidak_sekolah_karena[status].indexOf(nilai) != -1) {
                    if (v == nilai) {
                        html += '<option selected>' + v + '</option>';
                    } else {
                        html += '<option>' + v + '</option>';
                    }
                } else {
                    if (nilai != '' && v == 'Lainnya') {
                        html += '<option selected>' + v + '</option>';
                    } else {
                        html += '<option>' + v + '</option>';
                    }
                }
            });

        }
        $('select[name=detail_alasan_tidak_sekolah]').html(html);
    }

    function ganti_tidak_sekolah_lainnya(ini) {
        var status = ini.value;
        if (status == 'Lainnya') {
            $('textarea[name=detail_alasan_tidak_sekolah_lainnya]').show();
        } else {
            $('textarea[name=detail_alasan_tidak_sekolah_lainnya]').hide();
        }
    }
</script>
