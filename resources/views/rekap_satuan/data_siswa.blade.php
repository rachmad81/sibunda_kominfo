<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header card-info text-white">
                <a href="javascript:void(0)" class="close btn btn-sm btn-success" style="font-size: 10pt;opacity: 1;"
                onclick="get_data('{{$tab}}','load')">Load</a>
                <h4 class="modal-title">Data Siswa {{$judul}}</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    @php
                    $variabel = [
                        ['key'=>'jumlah_rombel','total'=>0],
                        ['key'=>'jumlah_siswa','total'=>0],
                        ['key'=>'jumlah_gakin_pramis','total'=>0],
                        ['key'=>'jumlah_sudah_polio_dosis_1','total'=>0],
                        ['key'=>'jumlah_sudah_polio_dosis_2','total'=>0],
                        ['key'=>'jumlah_pmt','total'=>0],
                        ['key'=>'jumlah_educitytour','total'=>0],
                        ['key'=>'jumlah_prestasi','total'=>0],
                        ['key'=>'jumlah_talenta','total'=>0],
                        ['key'=>'jumlah_manasik','total'=>0],
                        ['key'=>'jumlah_kitab_suci','total'=>0],
                        ['key'=>'jumlah_sudah_tb_bb','total'=>0],
                    ];
                    @endphp
                    <table class="table table-bordered" id="myData">
                        <thead>
                            <tr>
                                <th>NO</th>
                                <th>NPSN</th>
                                <th>NAMA SEKOLAH</th>
                                <th>JENJANG</th>
                                <th>STATUS</th>
                                @for($i=0;$i<count($variabel);$i++)
                                <th>{{ str_replace('_', ' ', $variabel[$i]['key']) }}</th>
                                @endfor
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($jumlah as $key=>$l)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $l->npsn }}</td>
                                <td>{{ $l->nama }}</td>
                                <td>{{($l->jenjang=='SPS') ? 'PPT' : $l->jenjang}}</td>
                                <td>{{ $l->status }}</td>
                                @for($i=0;$i<count($variabel);$i++)
                                <td>
                                    @php
                                    $kolom = $variabel[$i]['key'];
                                    $jumlah_siswa = $l->$kolom;
                                    echo '<a href="javascript:void(0)" onclick="detail_data(\''.$tab.'\',[\''.$l->npsn.'\',\''.$variabel[$i]['key'].'\'])">'.$jumlah_siswa.'</a>';
                                    $variabel[$i]['total'] += $jumlah_siswa;
                                    @endphp
                                </td>
                                @endfor
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="5">Jumlah Total</td>
                                @for($i=0;$i<count($variabel);$i++)
                                <td>{{ $variabel[$i]['total'] }}</td>
                                @endfor
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
