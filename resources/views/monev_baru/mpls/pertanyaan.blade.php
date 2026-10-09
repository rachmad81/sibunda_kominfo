<form class="form_pertanyaan_{{ $jenis_form }}" onsubmit="simpan('{{ $jenis_form }}')">

    <table style="width: 100%;">

        @if ($judul != '')
            <tr>
                <th colspan="3">
                    {{ $judul }}
                </th>
            </tr>
            <tr>
                <th style="height: 0.3cm">&nbsp;</th>
            </tr>
        @endif
        <input type="hidden" name="sekolah" value="{{ $npsn }}">
        <input type="hidden" name="jenis_form" value="{{ $jenis_form }}">
        <input type="hidden" name="tahun" value="{{ $tahun }}">

        @php
            $pertanyaan_identitas = collect($pertanyaan_identitas)->sortBy('nomor');
        @endphp

        @foreach ($pertanyaan_identitas as $i)
            <tr class="warna">
                <td style="width: 5%;text-align: right">{{ $i->nomor }}</td>
                <td style="width: 45%">{!! $i->pertanyaan !!}</td>
                <td style="width: 50%">
                    @php
                        $html = '';
                        switch ($i->tipe_jawaban) {
                            case 'text':
                                $value = isset($jawaban['isian_' . $i->id]) ? $jawaban['isian_' . $i->id] : '';
                                $html =
                                    '<input name="isian_' .
                                    $i->id .
                                    '" type="text" class="form-control" placeholder="jawaban anda" value="' .
                                    $value .
                                    '">';
                                break;
                            case 'date':
                                $value = isset($jawaban['isian_' . $i->id]) ? $jawaban['isian_' . $i->id] : '';
                                $html =
                                    '<input name="isian_' .
                                    $i->id .
                                    '" type="date" class="form-control" placeholder="jawaban anda" value="' .
                                    $value .
                                    '">';
                                break;
                            case 'dateinterval':
                                $value = isset($jawaban['mulai_' . $i->id]) ? $jawaban['mulai_' . $i->id] : '';
                                $value1 = isset($jawaban['akhir_' . $i->id]) ? $jawaban['akhir_' . $i->id] : '';
                                $html =
                                    '<input name="mulai_' .
                                    $i->id .
                                    '" type="date" class="form-control" placeholder="jawaban anda" value="' .
                                    $value .
                                    '">';
                                $html .= 'hingga';
                                $html .=
                                    '<input name="akhir_' .
                                    $i->id .
                                    '" type="date" class="form-control" placeholder="jawaban anda" value="' .
                                    $value1 .
                                    '">';
                                break;
                            case 'radio':
                                $pilihan_jawaban = explode(';;;', $i->list_jawaban);
                                for ($j = 0; $j < count($pilihan_jawaban); $j++) {
                                    $selected = '';
                                    if (
                                        isset($jawaban['isian_' . $i->id]) &&
                                        $jawaban['isian_' . $i->id] == $pilihan_jawaban[$j]
                                    ) {
                                        $selected = 'checked';
                                    }
                                    $html .=
                                        '<label style="margin-right: 15px"><input ' .
                                        $selected .
                                        ' name="isian_' .
                                        $i->id .
                                        '" type="radio" class=""value="' .
                                        $pilihan_jawaban[$j] .
                                        '"> ' .
                                        $pilihan_jawaban[$j] .
                                        '</label><br>';
                                }
                                $value = isset($jawaban['isian_catatan_' . $i->id])
                                    ? $jawaban['isian_catatan_' . $i->id]
                                    : '';
                                $html .=
                                    '<input name="isian_catatan_' .
                                    $i->id .
                                    '" type="text" placeholder="Catatan/Lainnya" class="form-control" value="' .
                                    $value .
                                    '">';
                                break;
                            case 'label':
                                $html = '';
                                break;
                            case 'textarea':
                                $value = isset($jawaban['isian_' . $i->id]) ? $jawaban['isian_' . $i->id] : '';
                                $html =
                                    '<textarea name="isian_' .
                                    $i->id .
                                    '" class="form-control" placeholder="jawaban anda">' .
                                    $value .
                                    '</textarea>';
                                break;

                            default:
                                $html = '';
                                break;
                        }
                    @endphp
                    {!! $html !!}
                </td>
            </tr>
            <tr>
                <td colspan="3" style="border-top:1px solid #000;height:0.2cm">&nbsp;</td>
            </tr>
        @endforeach
        <tr>
            <td colspan="3">
                <button type="submit" class="btn btn-primary">Simpan</button>
            </td>
        </tr>
    </table>
</form>
