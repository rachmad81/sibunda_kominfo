<div class="modal fade" id="modalPTK" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document" style="max-width: 90%;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Daftar Pendidik dan Tendik ({{ $sekolah->npsn.' - '.$sekolah->nama }})</h5>
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
                                    <th>NIK</th>
                                    <th>Nama</th>
                                    <th>Usia</th>
                                    <th>Status</th>
                                    <th>Induk</th>
                                    <th>Pend. Terakhir</th>
                                    <th>KK</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($data as $key => $value)
                                <tr>
                                    <td>{{ $key+1 }}</td>
                                    <td>{!! $value->nik !!}</td>
                                    <td>{{ $value->nama }}</td>
                                    <td>{{ Waktu::usia_angka($value->tgl_lahir) }} Th</td>
                                    <td>{{ $value->jabatan_ptk->nama }}</td>
                                    <td>{{ $value->induk ? 'Induk' : 'Non' }}</td>
                                    <td>{{ $value->jenjang_pendidikan->nama }}</td>
                                    <td>{{ $value->luar_kota ? 'Luar Kota' : 'Surabaya' }}</td>
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