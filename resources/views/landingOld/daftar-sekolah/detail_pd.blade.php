<div class="modal fade" id="modalPD" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document" style="max-width: 90%;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Daftar Peserta Didik ({{ $sekolah->npsn.' - '.$sekolah->nama }})</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12">
                        <table id="dataTable" class="table table-bordered table-striped">
                            <thead>
                                <tr >
                                    <th style="width:1%">No</th>
                                    <th>NIK / No.Akta</th>
                                    <th>Nama</th>
                                    <th>JK</th>
                                    <th>Usia</th>
                                    <th>Kelas</th>
                                    <th>Agama</th>
                                    <th>TB/BB</th>
                                    <th>KK</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($data as $key => $value)
                                <tr>
                                    <td>{{ $key+1 }}</td>
                                    <td>{!! ($value->nik ? $value->nik : '-').' /<br>'.$value->no_akta !!}</td>
                                    <td>{{ $value->nama }}</td>
                                    <td>{{ $value->kelamin }}</td>
                                    <td>{{ Waktu::usia_angka($value->tgl_lahir) }} Th</td>
                                    <td>{{ $value->kelas }}</td>
                                    <td>{{ $value->Agama->aga_nama }}</td>
                                    <td>{{ ($value->tinggi_badan ? $value->tinggi_badan : '-').' cm / '.($value->berat_badan ? $value->berat_badan : '-').' kg' }}</td>
                                    <td>{{ $value->kota }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>