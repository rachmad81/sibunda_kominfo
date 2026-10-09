<div class="block">
    <div class="block-header block-header-default row no-margin">
        <h3 class="block-title">{{ $title }}</h3>
    </div>
    <div class="block-content block-content-full">
        <form id="form_simpan">
            <div class="row">
                <input type="hidden" name="id_program"
                    value="@if (!empty($program)) {{ $program->id_program }} @endif">
                <div class="col-lg-4 col-md-4">Bidang</div>
                <div class="col-lg-8 col-md-8" style="margin-bottom: 10px">
                    @if (!in_array(Auth::getUser()->role, ['k3_paud', 'paud_sd', 'mitra']))
                        <select class="form-control bidang" required name="bidang">
                            @if (isset($program))
                                @if ($program->bidang == 'Bidang Pendidikan')
                                    <option
                                        {{ isset($program) && $program->bidang == 'Bidang Pendidikan' ? 'selected' : '' }}
                                        value="Bidang Pendidikan">Bidang Pendidikan</option>
                                @endif
                                {{-- <option {{ (isset($program) && $program->bidang == 'Program Pengelolaan Pendidikan') ? 'selected' : ''  }} value="Program Pengelolaan Pendidikan">Program Pengelolaan Pendidikan</option> --}}
                                @if ($program->bidang == 'Bidang Kesehatan')
                                    <option
                                        {{ isset($program) && $program->bidang == 'Bidang Kesehatan' ? 'selected' : '' }}
                                        value="Bidang Kesehatan">Bidang Kesehatan</option>
                                @endif
                                @if ($program->bidang == 'Bidang Pengasuhan dan Kesejahteraan Anak')
                                    <option
                                        {{ isset($program) && $program->bidang == 'Bidang Pengasuhan dan Kesejahteraan Anak' ? 'selected' : '' }}
                                        value="Bidang Pengasuhan dan Kesejahteraan Anak">Bidang Pengasuhan dan
                                        Kesejahteraan
                                        Anak</option>
                                @endif
                                @if ($program->bidang == 'Bidang Pemenuhan Hak dan Perlindungan Anak')
                                    <option
                                        {{ isset($program) && $program->bidang == 'Bidang Pemenuhan Hak dan Perlindungan Anak' ? 'selected' : '' }}
                                        value="Bidang Pemenuhan Hak dan Perlindungan Anak">Bidang Pemenuhan Hak dan
                                        Perlindungan Anak</option>
                                @endif
                            @endif
                            <option
                                {{ isset($program) && $program->bidang == 'Perencanaan dan Pengembangan Program' ? 'selected' : '' }}
                                value="Perencanaan dan Pengembangan Program">Perencanaan dan Pengembangan Program
                            </option>
                            <option
                                {{ isset($program) && $program->bidang == 'Sosialisasi dan Advokasi' ? 'selected' : '' }}
                                value="Sosialisasi dan Advokasi">Sosialisasi dan Advokasi</option>
                            <option
                                {{ isset($program) && $program->bidang == 'Koordinasi dan Kerjasama Kemitraan' ? 'selected' : '' }}
                                value="Koordinasi dan Kerjasama Kemitraan">Koordinasi dan Kerjasama Kemitraan</option>
                            <option
                                {{ isset($program) && $program->bidang == 'Pengawasan/Pemantauan dan Evaluasi' ? 'selected' : '' }}
                                value="Pengawasan/Pemantauan dan Evaluasi">Pengawasan/Pemantauan dan Evaluasi</option>
                        </select>
                    @else
                        <input type="text" class="form-control bidang" required name="bidang"
                            value="{{ !empty($program) ? $program->bidang : '' }}">
                    @endif
                </div>

                <div class="col-lg-4 col-md-4">Nama Program <i style="color:red">*</i></div>
                <div class="col-lg-8 col-md-8" style="margin-bottom: 10px"><input type="text" class="form-control"
                        name="nama_kegiatan"
                        value="@if (!empty($program)) {{ $program->nama_kegiatan }} @endif"></div>

                <div class="col-lg-4 col-md-4">Sasaran <i style="color:red">*</i></div>
                <div class="col-lg-8 col-md-8" style="margin-bottom: 10px"><input type="text" class="form-control"
                        name="sasaran" value="@if (!empty($program)) {{ $program->sasaran }} @endif"></div>

                <div class="col-lg-4 col-md-4">Jumlah Sasaran</div>
                <div class="col-lg-8 col-md-8" style="margin-bottom: 10px"><input type="number" class="form-control"
                        name="jumlah_sasaran"
                        value="@if (!empty($program)) {{ $program->jumlah_sasaran }} @endif"></div>

                <div class="col-lg-4 col-md-4">Tujuan <i style="color:red">*</i></div>
                <div class="col-lg-8 col-md-8" style="margin-bottom: 10px"><input type="text" class="form-control"
                        name="tujuan" value="@if (!empty($program)) {{ $program->tujuan }} @endif"></div>

                <div class="col-lg-4 col-md-4">Tahun <i style="color:red">*</i></div>
                <div class="col-lg-8 col-md-8" style="margin-bottom: 10px">
                    <select class="form-control" name="tahun">
                        <option value="">..:: Pilih Tahun ::..</option>
                        @for ($i = 2022; $i <= date('Y'); $i++)
                            <option @if (!empty($program) && $program->tahun == $i) selected @endif value="{{ $i }}">
                                {{ $i }}</option>
                        @endfor
                    </select>
                </div>

                <div class="col-lg-4 col-md-4">Waktu (Bulan)</div>
                <div class="col-lg-8 col-md-8" style="margin-bottom: 10px">
                    <?php
                    $bulan = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
                    $waktu = [];
                    if (!empty($program)) {
                        $waktu = explode(',', $program->waktu);
                    }
                    ?>
                    @for ($i = 1; $i <= 12; $i++)
                        <label style="cursor: pointer;"><input type="checkbox" name="waktu[]"
                                @if (in_array($i, $waktu)) checked @endif value="{{ $i }}"
                                style="margin-left: 20px"> {{ $bulan[$i] }}</label>
                    @endfor
                </div>
            </div>
            <div>
                <a href="javascript:void(0)" class="btn btn-outline-primary" onclick="simpan_form()"> Simpan</a>
                <a href="javascript:void(0)" class="btn btn-outline-warning" onclick="kembali_form()"> Kembali</a>
            </div>
        </form>
    </div>
</div>
