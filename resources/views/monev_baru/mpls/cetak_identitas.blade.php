@if ($judul != '')
    <tr>
        <th colspan="3">{{ $judul }}</th>
    </tr>
@endif

@php
    $pertanyaan_identitas = collect($pertanyaan_identitas)->sortBy('nomor');
@endphp

@foreach ($pertanyaan_identitas as $i)
    <tr>
        <td style="width: 2%">{{ $i->nomor }}</td>
        <td style="width: 55%">{!! $i->pertanyaan !!}</td>
        <td style="width: 43%">
            @php
                $html = '';
                switch ($i->tipe_jawaban) {
                    case 'text':
                        $value = isset($jawaban['isian_' . $i->id]) ? $jawaban['isian_' . $i->id] : '';
                        $html = $value;
                        break;
                    case 'date':
                        $value = isset($jawaban['isian_' . $i->id]) ? $jawaban['isian_' . $i->id] : '';
                        $html = $value;
                        break;
                    case 'dateinterval':
                        $value = isset($jawaban['mulai_' . $i->id]) ? $jawaban['mulai_' . $i->id] : '';
                        $value1 = isset($jawaban['akhir_' . $i->id]) ? $jawaban['akhir_' . $i->id] : '';
                        $html = $value;
                        $html .= ' hingga ';
                        $html .= $value1;
                        break;
                    case 'radio':
                        $value = isset($jawaban['isian_' . $i->id]) ? $jawaban['isian_' . $i->id] : '';
                        $value1 = isset($jawaban['isian_catatan_' . $i->id])
                            ? ' (' . $jawaban['isian_catatan_' . $i->id] . ')'
                            : '';
                        $html = $value;
                        $html .= $value1;
                        break;
                    case 'label':
                        $html = '';
                        break;
                    case 'textarea':
                        $value = isset($jawaban['isian_' . $i->id]) ? $jawaban['isian_' . $i->id] : '';
                        $html = $value;
                        break;

                    default:
                        $html = '';
                        break;
                }
            @endphp
            {!! $html !!}
        </td>
    </tr>
@endforeach
