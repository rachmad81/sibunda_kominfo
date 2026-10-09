<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header card-info text-white">
                {{-- <a href="javascript:void(0)" class="close btn btn-sm btn-success" style="font-size: 10pt;opacity: 1;"
                    onclick="get_data('keuangan','load')">Load</a> --}}
                <h4 class="modal-title">Data Keuangan</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th rowspan="2">No</th>
                                <th rowspan="2" align="center">Jenis Pendapatan</th>
                                <th rowspan="2" align="center">Jumlah Lembaga</th>
                                <th rowspan="2" align="center">Jumlah Total Pendapatan</th>
                                <th rowspan="2" align="center">Jumlah Pendapatan Operasional</th>
                                <th colspan="4">Jumlah Realisasi Operasional</th>
                                <th rowspan="2" align="center">Jumlah Total Realisasi</th>
                            </tr>
                            <tr>
                                <td>TW1</td>
                                <td>TW2</td>
                                <td>TW3</td>
                                <td>TW4</td>
                            </tr>
                        </thead>
                        <tbody class="tempat_keuangan">
                            @if ($keuangans->count() > 0)
                                @php
                                    $total = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                                @endphp
                                @foreach ($keuangans as $key => $keuangan)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $keuangan->jenis_anggaran }}</td>
                                        <td style="text-align: right">
                                            <a href="javascript:void(0)"
                                                onclick="detail_data('keuangan',['jumlah_lembaga','{{ $keuangan->jenis_anggaran }}'])">{{ number_format($keuangan->jumlah_lembaga, 0, '', '.') }}
                                            </a>
                                            {{-- {{ number_format($keuangan->jumlah_lembaga, 0, '', '.') }} --}}
                                        </td>
                                        <td style="text-align: right">
                                            @if (request()->user()->tingkat == 'kota')
                                                <a href="javascript:void(0)"
                                                    onclick="detail_data('keuangan',['jumlah_total_anggaran','{{ $keuangan->jenis_anggaran }}'])">{{ number_format($keuangan->jumlah_total_anggaran, 0, '', '.') }}
                                                </a>
                                            @else
                                                {{ number_format($keuangan->jumlah_total_anggaran, 0, '', '.') }}
                                            @endif
                                        </td>
                                        <td style="text-align: right">
                                            @if (request()->user()->tingkat == 'kota')
                                                <a href="javascript:void(0)"
                                                    onclick="detail_data('keuangan',['jumlah_anggaran_operasional','{{ $keuangan->jenis_anggaran }}'])">{{ number_format($keuangan->jumlah_anggaran_operasional, 0, '', '.') }}
                                                </a>
                                            @else
                                                {{ number_format($keuangan->jumlah_anggaran_operasional, 0, '', '.') }}
                                            @endif
                                        </td>
                                        <td style="text-align: right">
                                            @if (request()->user()->tingkat == 'kota')
                                                <a href="javascript:void(0)"
                                                    onclick="detail_data('keuangan',['realisasi_operasional_tw1','{{ $keuangan->jenis_anggaran }}'])">{{ number_format($keuangan->realisasi_operasional_tw1, 0, '', '.') }}
                                                </a>
                                            @else
                                                {{ number_format($keuangan->realisasi_operasional_tw1, 0, '', '.') }}
                                            @endif
                                        </td>
                                        <td style="text-align: right">
                                            @if (request()->user()->tingkat == 'kota')
                                                <a href="javascript:void(0)"
                                                    onclick="detail_data('keuangan',['realisasi_operasional_tw2','{{ $keuangan->jenis_anggaran }}'])">{{ number_format($keuangan->realisasi_operasional_tw2, 0, '', '.') }}
                                                </a>
                                            @else
                                                {{ number_format($keuangan->realisasi_operasional_tw2, 0, '', '.') }}
                                            @endif
                                        </td>
                                        <td style="text-align: right">
                                            @if (request()->user()->tingkat == 'kota')
                                                <a href="javascript:void(0)"
                                                    onclick="detail_data('keuangan',['realisasi_operasional_tw3','{{ $keuangan->jenis_anggaran }}'])">{{ number_format($keuangan->realisasi_operasional_tw3, 0, '', '.') }}
                                                </a>
                                            @else
                                                {{ number_format($keuangan->realisasi_operasional_tw3, 0, '', '.') }}
                                            @endif
                                        </td>
                                        <td style="text-align: right">
                                            @if (request()->user()->tingkat == 'kota')
                                                <a href="javascript:void(0)"
                                                    onclick="detail_data('keuangan',['realisasi_operasional_tw4','{{ $keuangan->jenis_anggaran }}'])">{{ number_format($keuangan->realisasi_operasional_tw4, 0, '', '.') }}
                                                </a>
                                            @else
                                                {{ number_format($keuangan->realisasi_operasional_tw4, 0, '', '.') }}
                                            @endif
                                        </td>
                                        <td style="text-align: right">
                                            @if (request()->user()->tingkat == 'kota')
                                                <a href="javascript:void(0)"
                                                    onclick="detail_data('keuangan',['jumlah_total_realisasi','{{ $keuangan->jenis_anggaran }}'])">{{ number_format($keuangan->jumlah_total_realisasi, 0, '', '.') }}
                                                </a>
                                            @else
                                                {{ number_format($keuangan->jumlah_total_realisasi, 0, '', '.') }}
                                            @endif
                                        </td>
                                    </tr>
                                    @php
                                        $total[10] += $keuangan->jumlah_total_anggaran;
                                        $total[0] += $keuangan->jumlah_anggaran_operasional;
                                        $total[1] += $keuangan->realisasi_operasional_tw1;
                                        $total[2] += $keuangan->realisasi_operasional_tw2;
                                        $total[3] += $keuangan->realisasi_operasional_tw3;
                                        $total[4] += $keuangan->realisasi_operasional_tw4;
                                        $total[11] += $keuangan->jumlah_total_realisasi;
                                        $total[12] += $keuangan->jumlah_lembaga;
                                    @endphp
                                @endforeach
                            @endif
                            <tr class="baris-total">
                                <td colspan="2"><b>Total</b></td>
                                <td style="text-align: right">{{ number_format($total[12], 0, '', '.') }}</td>
                                <td style="text-align: right">{{ number_format($total[10], 0, '', '.') }}</td>
                                <td style="text-align: right">{{ number_format($total[0], 0, '', '.') }}</td>
                                <td style="text-align: right">{{ number_format($total[1], 0, '', '.') }}</td>
                                <td style="text-align: right">{{ number_format($total[2], 0, '', '.') }}</td>
                                <td style="text-align: right">{{ number_format($total[3], 0, '', '.') }}</td>
                                <td style="text-align: right">{{ number_format($total[4], 0, '', '.') }}</td>
                                <td style="text-align: right">{{ number_format($total[11], 0, '', '.') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
