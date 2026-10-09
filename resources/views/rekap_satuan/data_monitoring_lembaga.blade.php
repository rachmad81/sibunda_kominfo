<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header card-info text-white">
                <a href="javascript:void(0)" class="close btn btn-sm btn-success" style="font-size: 10pt;opacity: 1;"
                    onclick="get_data('monitoring_lembaga','load')">Load</a>
                <h4 class="modal-title">{{ $judul }}</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="myData">
                        <thead class="thead_siswa_non_paud">
                            <tr>
                                <th rowspan="3">No</th>
                                <th rowspan="3">Jenjang</th>
                                <th rowspan="3">Status</th>
                                <th rowspan="3">Jumlah Lembaga</th>
                                <th rowspan="2" colspan="3">PPDB</th>
                                <th rowspan="2" colspan="3">MPLS</th>
                                <th colspan="18">Pengembangan 6 kemampuan Fondasi</th>
                            </tr>
                            <tr>
                                <th colspan="3">Manasik Haji Cilik</th>
                                <th colspan="3">Educity Tour</th>
                                <th colspan="3">Talenta Seni</th>
                                <th colspan="3">Edugreen Living</th>
                                <th colspan="3">Gen Mitigasi Bencana</th>
                                <th colspan="3">Sekolahku Tempat Menyenangkan</th>
                            </tr>
                            <tr>
                                <th>Belum</th>
                                <th>Sudah</th>
                                <th>Sudah Monev</th>
                                <th>Belum</th>
                                <th>Sudah</th>
                                <th>Sudah Monev</th>
                                <th>Belum</th>
                                <th>Sudah</th>
                                <th>Sudah Monev</th>
                                <th>Belum</th>
                                <th>Sudah</th>
                                <th>Sudah Monev</th>
                                <th>Belum</th>
                                <th>Sudah</th>
                                <th>Sudah Monev</th>
                                <th>Belum</th>
                                <th>Sudah</th>
                                <th>Sudah Monev</th>
                                <th>Belum</th>
                                <th>Sudah</th>
                                <th>Sudah Monev</th>
                                <th>Belum</th>
                                <th>Sudah</th>
                                <th>Sudah Monev</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($lembaga as $k => $l)
                                <tr>
                                    <td>{{ $k + 1 }}</td>
                                    <td>{{ $l->jenjang == 'SPS' ? 'PPT' : $l->jenjang }}</td>
                                    <td>{{ $l->status }}</td>
                                    <td>
                                        <a data-toggle="modal" data-target="#modal_detail_siswa"
                                            href="javascript:void(0)"
                                            onclick="detail_data('lembaga',['{{ $l->jenjang }}','{{ $l->status }}','jumlah_lembaga','Detail Jumlah Lembaga'])">{{ $l->jumlah_lembaga }}</a>
                                    </td>

                                    @if (in_array($l->jenjang, ['KB', 'TPA']))
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    @else
                                        <td>
                                            <a data-toggle="modal" data-target="#modal_detail_siswa"
                                                href="javascript:void(0)"
                                                onclick="detail_data('lembaga',['{{ $l->jenjang }}','{{ $l->status }}','jumlah_lembaga','Detail Jumlah Lembaga Belum Mengikuti Educity Tour'])">{{ $l->ppdb_belum }}</a>
                                        </td>
                                        <td>{{ $l->ppdb_sudah }}</td>
                                        <td>{{ $l->ppdb_monev }}</td>
                                        <td>
                                            <a data-toggle="modal" data-target="#modal_detail_siswa"
                                                href="javascript:void(0)"
                                                onclick="detail_data('lembaga',['{{ $l->jenjang }}','{{ $l->status }}','jumlah_lembaga','Detail Jumlah Lembaga Belum Mengikuti Educity Tour'])">{{ $l->mpls_belum }}</a>
                                        </td>
                                        <td>{{ $l->mpls_sudah }}</td>
                                        <td>{{ $l->mpls_monev }}</td>
                                    @endif

                                    <td><a data-toggle="modal" data-target="#modal_detail_siswa"
                                            href="javascript:void(0)"
                                            onclick="detail_data('lembaga',['{{ $l->jenjang }}','{{ $l->status }}','jumlah_lembaga','Detail Jumlah Lembaga Belum Mengikuti Educity Tour'])">{{ $l->satu_belum }}</a>
                                    </td>
                                    <td>{{ $l->satu_sudah }}</td>
                                    <td>{{ $l->satu_monev }}</td>
                                    <td>
                                        <a data-toggle="modal" data-target="#modal_detail_siswa"
                                            href="javascript:void(0)"
                                            onclick="detail_data('lembaga',['{{ $l->jenjang }}','{{ $l->status }}','jumlah_lembaga_belum_educitytour','Detail Jumlah Lembaga Belum Mengikuti Educity Tour'])">{{ $l->dua_belum }}</a>
                                    </td>
                                    <td>
                                        <a data-toggle="modal" data-target="#modal_detail_siswa"
                                            href="javascript:void(0)"
                                            onclick="detail_data('lembaga',['{{ $l->jenjang }}','{{ $l->status }}','jumlah_lembaga_sudah_educitytour','Detail Jumlah Lembaga Sudah Mengikuti Educity Tour'])">{{ $l->dua_sudah }}</a>
                                    </td>
                                    <td>{{ $l->dua_monev }}</td>
                                    <td>
                                        <a data-toggle="modal" data-target="#modal_detail_siswa"
                                            href="javascript:void(0)"
                                            onclick="detail_data('lembaga',['{{ $l->jenjang }}','{{ $l->status }}','jumlah_lembaga_belum_talenta','Detail Jumlah Lembaga Belum Mengikuti Talenta Seni'])">{{ $l->tiga_belum }}</a>
                                    </td>
                                    <td>
                                        <a data-toggle="modal" data-target="#modal_detail_siswa"
                                            href="javascript:void(0)"
                                            onclick="detail_data('lembaga',['{{ $l->jenjang }}','{{ $l->status }}','jumlah_lembaga_sudah_talenta','Detail Jumlah Lembaga Sudah Mengikuti Talenta Seni'])">{{ $l->tiga_sudah }}</a>
                                    </td>
                                    <td>{{ $l->tiga_monev }}</td>
                                    <td>
                                        <a data-toggle="modal" data-target="#modal_detail_siswa"
                                            href="javascript:void(0)"
                                            onclick="detail_data('lembaga',['{{ $l->jenjang }}','{{ $l->status }}','jumlah_lembaga','Detail Jumlah Lembaga Belum Mengikuti Educity Tour'])">{{ $l->empat_belum }}</a>
                                    </td>
                                    <td>{{ $l->empat_sudah }}</td>
                                    <td>{{ $l->empat_monev }}</td>
                                    <td>
                                        <a data-toggle="modal" data-target="#modal_detail_siswa"
                                            href="javascript:void(0)"
                                            onclick="detail_data('lembaga',['{{ $l->jenjang }}','{{ $l->status }}','jumlah_lembaga','Detail Jumlah Lembaga Belum Mengikuti Educity Tour'])">{{ $l->lima_belum }}</a>
                                    </td>
                                    <td>{{ $l->lima_sudah }}</td>
                                    <td>{{ $l->lima_monev }}</td>
                                    <td>
                                        <a data-toggle="modal" data-target="#modal_detail_siswa"
                                            href="javascript:void(0)"
                                            onclick="detail_data('lembaga',['{{ $l->jenjang }}','{{ $l->status }}','jumlah_lembaga','Detail Jumlah Lembaga Belum Mengikuti Educity Tour'])">{{ $l->enam_belum }}</a>
                                    </td>
                                    <td>{{ $l->enam_sudah }}</td>
                                    <td>{{ $l->enam_monev }}</td>

                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3">Jumlah Total</td>
                                <td>{{ $lembaga->sum('jumlah_lembaga') }}</td>
                                <td>{{ $lembaga->sum('ppdb_belum') }}</td>
                                <td>{{ $lembaga->sum('ppdb_sudah') }}</td>
                                <td>{{ $lembaga->sum('ppdb_monev') }}</td>
                                <td>{{ $lembaga->sum('mpls_belum') }}</td>
                                <td>{{ $lembaga->sum('mpls_sudah') }}</td>
                                <td>{{ $lembaga->sum('mpls_monev') }}</td>

                                <td>{{ $lembaga->sum('satu_belum') }}</td>
                                <td>{{ $lembaga->sum('satu_sudah') }}</td>
                                <td>{{ $lembaga->sum('satu_monev') }}</td>
                                <td>{{ $lembaga->sum('dua_belum') }}</td>
                                <td>{{ $lembaga->sum('dua_sudah') }}</td>
                                <td>{{ $lembaga->sum('dua_monev') }}</td>
                                <td>{{ $lembaga->sum('tiga_belum') }}</td>
                                <td>{{ $lembaga->sum('tiga_sudah') }}</td>
                                <td>{{ $lembaga->sum('tiga_monev') }}</td>
                                <td>{{ $lembaga->sum('empat_belum') }}</td>
                                <td>{{ $lembaga->sum('empat_sudah') }}</td>
                                <td>{{ $lembaga->sum('empat_monev') }}</td>
                                <td>{{ $lembaga->sum('lima_belum') }}</td>
                                <td>{{ $lembaga->sum('lima_sudah') }}</td>
                                <td>{{ $lembaga->sum('lima_monev') }}</td>
                                <td>{{ $lembaga->sum('enam_belum') }}</td>
                                <td>{{ $lembaga->sum('enam_sudah') }}</td>
                                <td>{{ $lembaga->sum('enam_monev') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
