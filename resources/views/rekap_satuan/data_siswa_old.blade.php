<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header card-info text-white">
                <a href="javascript:void(0)" class="close btn btn-sm btn-success" style="font-size: 10pt;opacity: 1;"
                    onclick="get_data('siswa','load')">Load</a>
                <h4 class="modal-title">Data Siswa</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                <table class="table table-bordered" id="myData">
                    <thead>
                        <tr>
                            <th rowspan="3">NO</th>
                            <th rowspan="3">JENJANG</th>
                            <th rowspan="3">STATUS</th>
                            {{-- @if (request()->user()->tingkat == 'kecamatan')
                <th rowspan="3">NPSN</th>
                <th rowspan="3">NAMA SEKOLAH</th>
                <th rowspan="3">ALAMAT</th>
                <th rowspan="3">KECAMATAN</th>
                <th rowspan="3">KELURAHAN</th>
            @endif --}}
                            <th rowspan="3">JUMLAH LEMBAGA</th>
                            <th rowspan="3">JUMLAH SISWA</th>
                            <th rowspan="2" colspan="2">PENGISIAN TB BB</th>
                            <th rowspan="1" colspan="4">VAKSINASI POLIO</th>
                            <th rowspan="3">GAKIN/PRAMIS</th>
                            <th rowspan="3">PESERTA EDUCITY TOUR</th>
                            <th rowspan="3">PESERTA TALENTA SENI</th>
                            <th rowspan="3">PENERIMA PMT</th>
                            <th rowspan="3">PRESTASI</th>
                            <th rowspan="3">PENGHAFAL KITAB SUCI</th>
                            {{-- <th rowspan="3">TALENTA</th>
            <th rowspan="3">MANASIK</th> --}}
                        </tr>
                        <tr>
                            <th colspan="2">Tahap 1</th>
                            <th colspan="2">Tahap 2</th>
                        </tr>
                        <tr>
                            <th>Sudah</th>
                            <th>Belum</th>
                            <th>Sudah</th>
                            <th>Belum</th>
                            <th>Sudah</th>
                            <th>Belum</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($siswa as $key => $s)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{($s->jenjang=='SPS') ? 'PPT' : $s->jenjang}}</td>
                                <td>{{ $s->status }}</td>
                                @if($s->jenjang=='TK A')
                                <td rowspan="2">
                                    @php
                                    $jenjang = ($s->jenjang=='TK A') ? 'TK' : $s->jenjang;
                                    $data_lembaga = collect($lembaga)->where('status',$s->status)->where('jenjang',$jenjang)->first();
                                    @endphp
                                    @if(!empty($data_lembaga))
                                    <a data-toggle="modal" data-target="#modal_detail_siswa" href="javascript:void(0)"
                                        onclick="detail_data('lembaga',['{{ $s->jenjang }}','{{ $s->status }}','jumlah_lembaga','Detail Jumlah Lembaga'])">{{ $data_lembaga->jumlah }}</a>
                                    @else
                                    @endif
                                </td>
                                @elseif($s->jenjang=='TK B')
                                @else
                                <td>
                                    @php
                                    $jenjang = ($s->jenjang=='TK A') ? 'TK' : $s->jenjang;
                                    $data_lembaga = collect($lembaga)->where('status',$s->status)->where('jenjang',$jenjang)->first();
                                    @endphp
                                    @if(!empty($data_lembaga))
                                    <a data-toggle="modal" data-target="#modal_detail_siswa" href="javascript:void(0)"
                                        onclick="detail_data('lembaga',['{{ $s->jenjang }}','{{ $s->status }}','jumlah_lembaga','Detail Jumlah Lembaga'])">{{ $data_lembaga->jumlah }}</a>
                                    @else
                                    @endif
                                    {{-- <a data-toggle="modal" data-target="#modal_detail_siswa" href="javascript:void(0)"
                                        onclick="detail_data('lembaga',['{{ $s->jenjang }}','{{ $s->status }}','jumlah_lembaga','Detail Jumlah Lembaga'])">{{ $s->jumlah_lembaga }}</a> --}}
                                </td>
                                @endif
                                <td>
                                    <a data-toggle="modal" data-target="#modal_detail_siswa" href="javascript:void(0)"
                                        onclick="detail_data('siswa',['{{ $s->jenjang }}','{{ $s->status }}','jumlah_siswa','Detail Jumlah Siswa'])">{{ $s->jumlah_siswa }}</a>
                                </td>
                                <td>
                                    <a data-toggle="modal" data-target="#modal_detail_siswa" href="javascript:void(0)"
                                        onclick="detail_data('siswa',['{{ $s->jenjang }}','{{ $s->status }}','jumlah_sudah_tb_bb','Detail Jumlah Sudah Pengisian TB BB'])">{{ $s->jumlah_sudah_tb_bb }}</a>
                                </td>
                                <td>
                                    <a data-toggle="modal" data-target="#modal_detail_siswa" href="javascript:void(0)"
                                        onclick="detail_data('siswa',['{{ $s->jenjang }}','{{ $s->status }}','jumlah_belum_tb_bb','Detail Jumlah Belum Pengisian TB BB'])">{{ $s->jumlah_siswa - $s->jumlah_sudah_tb_bb }}</a>
                                </td>
                                <td>
                                    <a data-toggle="modal" data-target="#modal_detail_siswa" href="javascript:void(0)"
                                        onclick="detail_data('siswa',['{{ $s->jenjang }}','{{ $s->status }}','jumlah_sudah_polio_dosis_1','Detail Jumlah Sudah Polio Tahap 1'])">{{ $s->jumlah_sudah_polio_dosis_1 }}</a>
                                </td>
                                <td>
                                    <a data-toggle="modal" data-target="#modal_detail_siswa" href="javascript:void(0)"
                                        onclick="detail_data('siswa',['{{ $s->jenjang }}','{{ $s->status }}','jumlah_belum_polio_dosis_1','Detail Jumlah Belum Polio Tahap 1'])">{{ $s->jumlah_siswa - $s->jumlah_sudah_polio_dosis_1 }}</a>
                                </td>
                                <td>
                                    <a data-toggle="modal" data-target="#modal_detail_siswa" href="javascript:void(0)"
                                        onclick="detail_data('siswa',['{{ $s->jenjang }}','{{ $s->status }}','jumlah_sudah_polio_dosis_2','Detail Jumlah Sudah Polio Tahap 2'])">{{ $s->jumlah_sudah_polio_dosis_2 }}</a>
                                </td>
                                <td>
                                    <a data-toggle="modal" data-target="#modal_detail_siswa" href="javascript:void(0)"
                                        onclick="detail_data('siswa',['{{ $s->jenjang }}','{{ $s->status }}','jumlah_belum_polio_dosis_2','Detail Jumlah Belum Polio Tahap 2'])">{{ $s->jumlah_siswa - $s->jumlah_sudah_polio_dosis_2 }}</a>
                                </td>
                                <td>
                                    <a data-toggle="modal" data-target="#modal_detail_siswa" href="javascript:void(0)"
                                        onclick="detail_data('siswa',['{{ $s->jenjang }}','{{ $s->status }}','jumlah_gakin_pramis','Detail Jumlah Siswa Gakin Pramis'])">{{ $s->jumlah_gakin_pramis }}</a>
                                </td>
                                <td>
                                    <a data-toggle="modal" data-target="#modal_detail_siswa" href="javascript:void(0)"
                                        onclick="detail_data('siswa',['{{ $s->jenjang }}','{{ $s->status }}','jumlah_educitytour','Detail Jumlah Siswa Mengikuti Educity Tour'])">{{ $s->jumlah_educitytour }}</a>
                                </td>
                                <td>
                                    <a data-toggle="modal" data-target="#modal_detail_siswa" href="javascript:void(0)"
                                        onclick="detail_data('siswa',['{{ $s->jenjang }}','{{ $s->status }}','jumlah_talenta','Detail Jumlah Siswa Mengikuti Talenta Seni'])">{{ $s->jumlah_talenta }}</a>
                                </td>
                                <td>
                                    <a data-toggle="modal" data-target="#modal_detail_siswa" href="javascript:void(0)"
                                        onclick="detail_data('siswa',['{{ $s->jenjang }}','{{ $s->status }}','jumlah_pmt','Detail Jumlah Siswa Penerima PMT'])">{{ $s->jumlah_pmt }}</a>
                                </td>
                                <td>
                                    <a data-toggle="modal" data-target="#modal_detail_siswa" href="javascript:void(0)"
                                        onclick="detail_data('siswa',['{{ $s->jenjang }}','{{ $s->status }}','jumlah_prestasi','Detail Jumlah Siswa Prestasi'])">{{ $s->jumlah_prestasi }}</a>
                                </td>
                                <td>
                                    <a data-toggle="modal" data-target="#modal_detail_siswa" href="javascript:void(0)"
                                        onclick="detail_data('siswa',['{{ $s->jenjang }}','{{ $s->status }}','jumlah_kitab_suci','Detail Jumlah Siswa Penghafal Kitab Suci'])">{{ $s->jumlah_kitab_suci }}</a>
                                </td>
                                {{-- @if (request()->user()->tingkat == 'kecamatan')
                    <td>{{ $s->npsn }}</td>
                    <td>{{ $s->nama }}</td>
                    <td>{{ $s->alamat }}</td>
                    <td>{{ $s->kecamatan_dispenduk }}</td>
                    <td>{{ $s->kelurahan_dispenduk }}</td>
                <td>
                    <a data-toggle="modal" data-target="#modal_detail_siswa" href="javascript:void(0)"
                        onclick="detail_data('siswa',['{{ $s->npsn }}','jumlah_siswa','{{ $s->nama }}','Detail Jumlah Siswa'])">{{ $s->jumlah_siswa }}</a>
                </td>
                <td>
                    <a data-toggle="modal" data-target="#modal_detail_siswa" href="javascript:void(0)"
                        onclick="detail_data('siswa',['{{ $s->npsn }}','jumlah_sudah_polio_dosis_1','{{ $s->nama }}','Detail Jumlah Sudah Polio Tahap 1'])">{{ $s->jumlah_sudah_polio_dosis_1 }}</a>
                </td>
                <td>
                    <a data-toggle="modal" data-target="#modal_detail_siswa" href="javascript:void(0)"
                        onclick="detail_data('siswa',['{{ $s->npsn }}','jumlah_belum_polio_dosis_1','{{ $s->nama }}','Detail Jumlah Belum Polio Tahap 1'])">{{ $s->jumlah_siswa - $s->jumlah_sudah_polio_dosis_1 }}</a>
                </td>
                <td>
                    <a data-toggle="modal" data-target="#modal_detail_siswa" href="javascript:void(0)"
                        onclick="detail_data('siswa',['{{ $s->npsn }}','jumlah_sudah_polio_dosis_2','{{ $s->nama }}','Detail Jumlah Sudah Polio Tahap 2'])">{{ $s->jumlah_sudah_polio_dosis_2 }}</a>
                </td>
                <td>
                    <a data-toggle="modal" data-target="#modal_detail_siswa" href="javascript:void(0)"
                        onclick="detail_data('siswa',['{{ $s->npsn }}','jumlah_belum_polio_dosis_2','{{ $s->nama }}','Detail Jumlah Belum Polio Tahap 2'])">{{ $s->jumlah_siswa - $s->jumlah_sudah_polio_dosis_2 }}</a>
                </td>
                <td>
                    <a data-toggle="modal" data-target="#modal_detail_siswa" href="javascript:void(0)"
                        onclick="detail_data('siswa',['{{ $s->npsn }}','jumlah_educitytour','{{ $s->nama }}','Detail Jumlah Siswa Mengikuti Educity Tour'])">{{ $s->jumlah_educitytour }}</a>
                </td>
                <td>
                    <a data-toggle="modal" data-target="#modal_detail_siswa" href="javascript:void(0)"
                        onclick="detail_data('siswa',['{{ $s->npsn }}','jumlah_pmt','{{ $s->nama }}','Detail Jumlah Siswa Penerima PMT'])">{{ $s->jumlah_pmt }}</a>
                </td>
                <td>
                    <a data-toggle="modal" data-target="#modal_detail_siswa" href="javascript:void(0)"
                        onclick="detail_data('siswa',['{{ $s->npsn }}','jumlah_prestasi','{{ $s->nama }}','Detail Jumlah Siswa Prestasi'])">{{ $s->jumlah_prestasi }}</a>
                </td>
                <td>
                    <a data-toggle="modal" data-target="#modal_detail_siswa" href="javascript:void(0)"
                        onclick="detail_data('siswa',['{{ $s->npsn }}','jumlah_kitab_suci','{{ $s->nama }}','Detail Jumlah Siswa Penghafal Kitab Suci'])">{{ $s->jumlah_kitab_suci }}</a>
                </td>
                <td>
                    <a data-toggle="modal" data-target="#modal_detail_siswa" href="javascript:void(0)"
                        onclick="detail_data('siswa',['{{ $s->npsn }}','jumlah_gakin_pramis','{{ $s->nama }}','Detail Jumlah Siswa Gakin Pramis'])">{{ $s->jumlah_gakin_pramis }}</a>
                </td>
                @endif --}}
                                {{-- <td>
                    <a data-toggle="modal" data-target="#modal_detail_siswa" href="javascript:void(0)"
                        onclick="detail_data('siswa',['{{$s->npsn}}','jumlah_talenta','{{$s->nama}}','Detail Siswa Talenta'])">{{ $s->jumlah_talenta }}</a>
                </td>
                <td>
                    <a data-toggle="modal" data-target="#modal_detail_siswa" href="javascript:void(0)"
                        onclick="detail_data('siswa',['{{$s->npsn}}','jumlah_manasik','{{$lembaga->nama}}','Detail Siswa Manasik'])">{{ $lembaga->jumlah_manasik }}</a>
                </td> --}}
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        @php
                        $tkb = collect($siswa)->where('jenjang','TK B')->sum('jumlah_lembaga');
                        @endphp
                        <tr>
                            <td colspan="3">Jumlah Total</td>
                            <td>{{ ($lembaga->sum('jumlah')) }}</td>
                            <td>{{ $siswa->sum('jumlah_siswa') }}</td>
                            <td>{{ $siswa->sum('jumlah_sudah_tb_bb') }}</td>
                            <td>{{ $siswa->sum('jumlah_siswa') - $siswa->sum('jumlah_sudah_tb_bb') }}</td>
                            <td>{{ $siswa->sum('jumlah_sudah_polio_dosis_1') }}</td>
                            <td>{{ $siswa->sum('jumlah_siswa') - $siswa->sum('jumlah_sudah_polio_dosis_1') }}
                            </td>
                            <td>{{ $siswa->sum('jumlah_sudah_polio_dosis_2') }}</td>
                            <td>{{ $siswa->sum('jumlah_siswa') - $siswa->sum('jumlah_sudah_polio_dosis_2') }}
                            </td>
                            <td>{{ $siswa->sum('jumlah_gakin_pramis') }}</td>
                            <td>{{ $siswa->sum('jumlah_educitytour') }}</td>
                            <td>{{ $siswa->sum('jumlah_talenta') }}</td>
                            <td>{{ $siswa->sum('jumlah_pmt') }}</td>
                            <td>{{ $siswa->sum('jumlah_prestasi') }}</td>
                            <td>{{ $siswa->sum('jumlah_kitab_suci') }}</td>
                            {{-- <td>{{ $siswa->sum('jumlah_talenta') }}</td>
            <td>{{ $siswa->sum('jumlah_manasik') }}</td> --}}
                        </tr>
                    </tfoot>
                </table>
                </div>
            </div>
        </div>
    </div>
</div>
