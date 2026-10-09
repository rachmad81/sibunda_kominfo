<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header card-info text-white">
                <a href="javascript:void(0)" class="close btn btn-sm btn-success" style="font-size: 10pt;opacity: 1;"
                onclick="get_data('gtk','load')">Load</a>
                <h4 class="modal-title">Data GTK</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    @php
                    $variabel = [
                        ['key'=>'jumlah_gtk','total'=>0],
                        ['key'=>'jumlah_nonpns','total'=>0],
                        ['key'=>'jumlah_s1','total'=>0],
                        ['key'=>'jumlah_s2','total'=>0],
                        ['key'=>'jumlah_s3','total'=>0],
                        ['key'=>'jumlah_belum_nuptk','total'=>0],
                        ['key'=>'jumlah_pelatihan','total'=>0],
                        ['key'=>'jumlah_guru_penggerak','total'=>0],
                        ['key'=>'jumlah_tfg','total'=>0],
                        ['key'=>'jumlah_tpg','total'=>0],
                        ['key'=>'jumlah_tamsil','total'=>0],
                        ['key'=>'jumlah_jaspel','total'=>0],
                        ['key'=>'jumlah_sinaubareng','total'=>0],
                        ['key'=>'jumlah_pns','total'=>0],
                        ['key'=>'jumlah_pppk','total'=>0],
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
                                    $jumlah_gtk = $l->$kolom ?? 0;
                                    echo '<a href="javascript:void(0)" onclick="detail_data(\'gtk\',[\''.$l->npsn.'\',\''.$variabel[$i]['key'].'\'])">'.$jumlah_gtk.'</a>';
                                    $variabel[$i]['total'] += $jumlah_gtk;
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
