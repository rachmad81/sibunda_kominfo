<div class="row" style="margin-top: 20px">
    <div class="col-lg-12">
        <h2>{{ $judul }}</h2>
    </div>
    <div class="col-lg-12">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Usia</th>
                    <th>Jumlah Penduduk</th>
                    <th>Jumlah Sudah sekolah</th>
                    <th>Jumlah Belum sekolah</th>
                    <th>Jumlah Sudah disurvey</th>
                    <th>Jumlah belum disurvey</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($penduduk as $p)
                    <tr>
                        <td>{{ $p->usia }}</td>
                        <td class="text-right">{{ number_format($p->jumlah_penduduk, 0, '', '.') }}</td>
                        <td class="text-right">{{ number_format($p->jumlah_bersekolah, 0, '', '.') }}</td>
                        <td class="text-right">{{ number_format($p->jumlah_belum_sekolah, 0, '', '.') }}</td>
                        <td class="text-right">{{ number_format($p->jumlah_sudah_survey, 0, '', '.') }}</td>
                        <td class="text-right">{{ number_format($p->jumlah_belum_survey, 0, '', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th>Total</th>
                    <th class="text-right">{{ number_format($penduduk->sum('jumlah_penduduk'), 0, '', '.') }}</th>
                    <th class="text-right">{{ number_format($penduduk->sum('jumlah_bersekolah'), 0, '', '.') }}</th>
                    <th class="text-right">{{ number_format($penduduk->sum('jumlah_belum_sekolah'), 0, '', '.') }}</th>
                    <th class="text-right">{{ number_format($penduduk->sum('jumlah_sudah_survey'), 0, '', '.') }}</th>
                    <th class="text-right">{{ number_format($penduduk->sum('jumlah_belum_survey'), 0, '', '.') }}</th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <a href="{{ route('bunda-monitoring-blm_sekolah-main') }}" class="btn btn-success form-control">Menuju Halaman
            Survey <i class="fa fa-arrow-right"></i></a>
    </div>
</div>
