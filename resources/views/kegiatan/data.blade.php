@if ($data->count() != 0)
    @foreach ($data as $k => $v)
        <tr>
            <td width="1%">{{ $k + 1 }}</td>
            <td style="white-space: nowrap;" class="aksi">{{ $v->nama_program }}</td>
            <td>{{ $v->kegiatan }}</td>
            <td>
                {{ $v->tempat_kegiatan }}<br>
                {{ date('d-m-Y', strtotime($v->tgl_kegiatan)) }} {{ $v->waktu_kegiatan }}<br>
                {{ $v->bentuk_keg }}
            </td>
            <td class="aksi">
                <p id="materi_{{ $k }}">
                    {{ substr($v->materi, 0, 255) }} @if (strlen($v->materi) > 255)
                        <a href="javascript:void(0)"
                            onclick="lengkap('materi_{{ $k }}','{{ base64_encode($v->materi) }}')">Selengkapnya</a>
                    @endif
                </p>
                ({{ $v->narsum }})
            </td>
            <td class="title_print">{{ $v->materi }}<br>({{ $v->narsum }})</td>
            <td>{{ $v->sumber_dana }}</td>
            <td>
                <b>Sasaran :</b><br>
                {{ $v->sasaran_kegiatan }}<br>
                <b>Tujuan :</b><br>
                {{ $v->tujuan_kegiatan }}
            </td>
            <td>{{ $v->hasil_kegiatan }}</td>
            <td>{{ $v->target_out_kegiatan }}</td>
            <td class="aksi">
                @if ($v->foto)
                    @php
                        $url = asset('logo/image-icon.png');
                        if ($v->foto != '') {
                            try {
                                if (Storage::exists($v->foto)) {
                                    $url = route('storage', [base64_encode($v->foto), '1']);
                                    $foto_exists = 1;
                                } else {
                                    $url = $v->foto;
                                }
                            } catch (\Exception $e) {
                                $url = '';
                            }
                        }
                    @endphp
                    <a href="{{ $url }}" target="_blank" class="btn btn-sm btn-info">Foto</a>
                @endif
                @if ($v->url_video != '')
                    <a href="{!! $v->url_video !!}" target="_blank" class="btn btn-sm btn-danger">Video</a>
                @endif

                @if ($v->jumlah != null)
                    <a href="{{ route('kegiatan-absensi', ['halaman' => 'rekap_absensi', 'code' => base64_encode($v->id_kegiatan)]) }}"
                        class="btn btn-sm btn-primary" target="_blank">Rekap Absensi</a>
                @endif
            </td>
            <td style="white-space: nowrap;" class="aksi">
                <?php
                $button = '<button class="btn btn-sm btn-outline-warning" onclick="formula(\'' . $v->id_kegiatan . '\')">Ubah</button>';
                $button .= ' <button class="btn btn-sm btn-outline-info" onclick="form_laporan(\'' . $v->id_kegiatan . '\')">Laporan Kegiatan</button>';
                $button .= ' <a href="' . route('kegiatan-absensi', ['halaman' => 'absensi', 'code' => base64_encode($v->id_kegiatan)]) . '" target="_blank"><button class="btn btn-sm btn-outline-success">Absensi Kegiatan</button></a>';
                $button .= ' <button class="btn btn-sm btn-outline-danger" onclick="hapus(\'' . $v->id_kegiatan . '\')">Hapus</button>';
                
                echo $button;
                ?>
            </td>
        </tr>
    @endforeach
@else
@endif
