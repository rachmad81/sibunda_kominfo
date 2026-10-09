<style>
    .heading-widget {
        padding: 10px;
        font-size: 13pt;
        border-bottom: 2px solid #fff;
    }

    .body-widget {
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 80pt;
        min-height: 200px;
        width: 100%;
    }
</style>
<div class="row" style="margin-top: 20px">
    <div class="col-lg-12">
        <h2>{{ $judul }}</h2>
    </div>
</div>
<div class="row">
    <div class="col-lg-3">
        <div style="background: #3f7294;color: white">
            <div class="heading-widget">Jumlah Program</div>
            <div class="body-widget">
                <div>{{ $program }}</div>
            </div>
        </div>
    </div>
    <div class="col-lg-3">
        <div style="background: #056dad;color: white">
            <div class="heading-widget">Jumlah Kegiatan</div>
            <div class="body-widget">
                <div>
                    @php
                        echo $kegiatan->sum('jumlah');
                    @endphp
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3">
        <div style="background: #3f9446;color: white">
            <div class="heading-widget">Jumlah Sudah Laporan</div>
            <div class="body-widget">
                <div>
                    @php
                        echo $kegiatan->where('status_laporan', 'Sudah')->sum('jumlah');
                    @endphp
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row mt-2">
    <div class="col-lg-12">
        <a href="{{ Route('common-kegiatan-index') }}" class="btn btn-info form-control">Menuju Halaman Kegiatan <i
                class="fa fa-arrow-right"></i></a>
    </div>
</div>
