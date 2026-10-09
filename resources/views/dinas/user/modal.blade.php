<div id="modal_tambah" class="modal fade" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Tambah Akun Pengguna</h3>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
            </div>
            <form method="POST" action="{{ route('dinas-user-store') }}" class="form-horizontal"
                enctype="multipart/form-data">
                <div class="modal-body">
                    @csrf
                    <div class="row">
                        <div class="col-md-4 col-6">
                            <div class="form-material">
                                <select class="form-control" name="role" required v-model="selected_role">
                                    <option value="dinas">Dinas</option>
                                    <option value="opd">Opd (Dinas)</option>
                                    <option value="bunda">Bunda</option>
                                    <option value="penilai">Penilai</option>
                                    <option value="k3_paud">K3 PAUD</option>
                                    {{-- <option value="gugus_tugas">Gugus Tugas</option> --}}
                                    <option value="paud_sd">Forum Komunikasi PAUD SD</option>
                                    {{-- <option value="gugus">Gugus Tugas</option> --}}
                                </select>
                                <label for="role" class="required">Role</label>
                            </div>
                        </div>
                        <div class="col-md-4 col-6" v-if="selected_role != 'dinas'">
                            <div class="form-material">
                                <select class="form-control" name="tingkat" required v-model="selected_tingkat">
                                    <option value="kota">Kota</option>
                                    <option value="kecamatan">Kecamatan</option>
                                    <option value="kelurahan">Kelurahan</option>
                                </select>
                                <label for="tingkat" class="required">Tingkat</label>
                            </div>
                        </div>
                        <div class="col-md-4 col-6" v-if="selected_tingkat != 'kota' && selected_role != 'dinas'">
                            <div class="form-material">
                                <select class="form-control" name="wilayah" required>
                                    <option v-for="wilayah in list_wilayah" :value="wilayah.id">
                                        @{{ wilayah.nama }}</option>
                                </select>
                                <label for="wilayah" class="required">Wilayah</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-material">
                                <input type="text" class="form-control" name="nama" required>
                                <label for="nama" class="required">Nama Lengkap</label>
                            </div>
                        </div>
                        <div class="col-md-4 col-6">
                            <div class="form-material">
                                <input type="text" class="form-control" name="username" required>
                                <label for="username" class="required">Username</label>
                            </div>
                        </div>
                        <div class="col-md-4 col-6">
                            <div class="form-material">
                                <input type="password" class="form-control" name="password" required>
                                <label for="password" class="required">Kata Sandi</label>
                            </div>
                        </div>
                        <div class="col-md-4 col-6">
                            <div class="form-material">
                                <input type="password" class="form-control" name="re-password" required>
                                <label for="re-password" class="required">Ulang Kata Sandi</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" style="margin-bottom:0px" class="btn btn-white"
                        data-dismiss="modal">Batalkan</button>
                    <button type="submit" class="btn btn-primary btn-submit">Tambahkan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="modal_ubah" class="modal fade" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Ubah Akun Pengguna</h3>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
            </div>
            <form method="POST" action="{{ route('dinas-user-update') }}" class="form-horizontal"
                enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="id" :value="detail.id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4 col-6">
                            <div class="form-material">
                                <select class="form-control" name="role" required v-model="selected_role">
                                    <option value="dinas">Dinas</option>
                                    <option value="opd">Opd (Dinas)</option>
                                    <option value="bunda">Bunda</option>
                                    <option value="penilai">Penilai</option>
                                    <option value="k3_paud">K3 PAUD</option>
                                    <option value="paud_sd">Forum Komunikasi PAUD SD</option>
                                </select>
                                <label for="role" class="required">Role</label>
                            </div>
                        </div>
                        <div class="col-md-4 col-6" v-if="selected_role != 'dinas'">
                            <div class="form-material">
                                <select class="form-control" name="tingkat" required v-model="selected_tingkat">
                                    <option value="kota">Kota</option>
                                    <option value="kecamatan">Kecamatan</option>
                                    <option value="kelurahan">Kelurahan</option>
                                </select>
                                <label for="tingkat" class="required">Tingkat</label>
                            </div>
                        </div>
                        <div class="col-md-4 col-6" v-if="selected_tingkat != 'kota' && selected_role != 'dinas'">
                            <div class="form-material">
                                <select class="form-control" name="wilayah" required v-model="selected_wilayah">
                                    <option v-for="wilayah in list_wilayah" :value="wilayah.id">
                                        @{{ wilayah.nama }}</option>
                                </select>
                                <label for="wilayah" class="required">Wilayah</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-material">
                                <input type="text" class="form-control" name="nama" required
                                    v-model="detail.nama">
                                <label for="nama" class="required">Nama Lengkap</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <br>
                            <label>Ganti Kata Sandi? </label>
                            <input type="checkbox" name="ganti_pass" v-model="ganti_pass">
                        </div>
                        <div class="col-md-4 col-6">
                            <div class="form-material">
                                <input type="text" class="form-control" name="username" readonly
                                    v-model="detail.username">
                                <label for="username" class="required">Username</label>
                            </div>
                        </div>
                        <div class="col-md-4 col-6">
                            <div class="form-material">
                                <input type="password" class="form-control" name="password" required
                                    v-bind:disabled="!ganti_pass">
                                <label for="password" v-bind:class="[ganti_pass ? 'required' : '']">Kata Sandi</label>
                            </div>
                        </div>
                        <div class="col-md-4 col-6">
                            <div class="form-material">
                                <input type="password" class="form-control" name="re-password" required
                                    v-bind:disabled="!ganti_pass">
                                <label for="re-password" v-bind:class="[ganti_pass ? 'required' : '']">Re-Kata
                                    Sandi</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" style="margin-bottom:0px" class="btn btn-warning btn-submit">Ubah</button>
                    <button type="button" class="btn btn-white" data-dismiss="modal">Batalkan</button>
                </div>
            </form>
        </div>
    </div>
</div>
