<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Pengisian Monev MPLS</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<style>
    table tr td {
        vertical-align: top
    }

    .warna {
        background: linear-gradient(145deg, #ffdede, #fff) !important;
    }

    .btn {
        padding: 0.2cm;
        cursor: pointer;
    }

    .btn-primary {
        background: linear-gradient(145deg, #9285ff, #fff);
    }

    .btn-primary:hover {
        background: linear-gradient(-145deg, #9285ff, #fff);
        padding: 0.3cm;
    }
</style>s

<body>

    <div class="col-lg-12" style="width: 21cm;margin: auto">
        @php
            $form_tipe = ['A', 'B', 'C', 'C1', 'C2', 'C3', 'C4', 'C5', 'D', 'E', 'F', 'G', 'H'];
        @endphp
        <table style="width: 100%">
            <tbody>
                <tr>
                    <th colspan="3">A IDENTITAS</th>
                </tr>
                <tr>
                    <td style="width: 5%">1</td>
                    <td style="width: 45%">Nama Satuan (NPSN)</td>
                    <td style="width: 50%;">
                        <span class="npsn">{{ $sekolah->nama }} ({{ $sekolah->npsn }})</span>
                    </td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>Kelurahan</td>
                    <td>
                        <span class="kelurahan">{{ $sekolah->kelurahan_dispenduk }}</span>
                    </td>
                </tr>
                <tr>
                    <td>3</td>
                    <td>Kecamatan</td>
                    <td>
                        <span class="kecamatan">{{ $sekolah->kecamatan_dispenduk }}</span>
                    </td>
                </tr>
                <tr>
                    <td>4</td>
                    <td>Nama KS</td>
                    <td>
                        <span class="ks">{{ $data_ks->nama }}</span>
                    </td>
                </tr>
            </tbody>
        </table>
        <div class="wadah">
            @for ($i = 0; $i < count($form_tipe); $i++)
                <div class="panel_heading panel_heading_{{ $form_tipe[$i] }}"
                    style="width: 100%;border: 1px solid #000;cursor: pointer;margin-top: 0.5cm"
                    onclick="pengisian('{{ $form_tipe[$i] }}')">
                    <div style="width:100%;padding: 0.2cm">
                        Form {{ $form_tipe[$i] }}
                    </div>
                </div>
                <div class="wadah_pertanyaan wadah_pertanyaan_{{ $form_tipe[$i] }}">Wadah Pertanyaan</div>
            @endfor
        </div>
    </div>
</body>
<script src="{{ asset('assets/js/jquery.min.js') }}"></script>

<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $(document).ready(function() {
        $('.wadah_pertanyaan').hide();
    });

    function pengisian(jenis_form) {
        $('.wadah_pertanyaan').hide();
        $('.panel_heading').css('background', 'transparent');

        var data = {
            jenis_form: jenis_form,
            npsn: '{{ $sekolah->npsn }}',
            tahun: '{{ $tahun }}',
            jenjang_sekolah: '{{ $sekolah->jenjang }}',
        };

        $.post('{{ route('monev_baru-mpls', ['jenjang' => $jenjang, 'halaman' => 'halaman_pertanyaan']) }}', data,
            function(data) {
                $('.wadah_pertanyaan').hide();
                $('.wadah_pertanyaan_' + jenis_form).html(data.content);
                $('.wadah_pertanyaan_' + jenis_form).show();
                $('.panel_heading_' + jenis_form).css('background', 'linear-gradient(145deg,#ffe0cc,#fff)');
            });
    }

    function simpan(jenis_form) {
        event.preventDefault();
        var data = new FormData($('form.form_pertanyaan_' + jenis_form)[0]);
        $.ajax({
            url: '{{ route('monev_baru-mpls', ['halaman' => 'simpan', 'jenjang' => $jenjang]) }}',
            data: data,
            type: 'POST',
            contentType: false,
            processData: false,
            success: function(data) {
                if (data.code == 200) {
                    alert('200 - Berhasil disimpan');
                } else {
                    alert('250 - Gagal disimpan');
                }
            }
        });
    }
</script>

</html>
