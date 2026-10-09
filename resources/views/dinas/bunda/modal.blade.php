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
            <form method="POST" id="form_simpan" class="form-horizontal" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="id" value="{{$profil->id}}">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-material">
                                <input type="text" class="form-control" name="nama" required value="{{$profil->nama}}">
                                <label for="nama" class="required">Nama Lengkap</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-material">
                                <select class="form-control" name="jabatan">

                                </select>
                                <label for="nama" class="required">Jabatan</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 col-6">
                            <div class="form-material">
                                @php
                                $selected = ['','',''];
                                if($profil->tingkat=='kota'){
                                    $selected[0] = 'selected';
                                }else if($profil->tingkat=='kecamatan'){
                                    $selected[1] = 'selected';
                                }else{
                                    $selected[2] = 'selected';
                                }
                                @endphp
                                <select class="form-control" name="tingkat" required onchange="ubah_wilayah()">
                                    <option value="kota" {{$selected[0]}}>Kota</option>
                                    <option value="kecamatan" {{$selected[1]}}>Kecamatan</option>
                                    <option value="kelurahan" {{$selected[2]}}>Kelurahan</option>
                                </select>
                                <label for="tingkat" class="required">Tingkat</label>
                            </div>
                        </div>
                        <div class="col-md-6 col-6 role_bunda_kota">
                            <div class="form-material">
                                <select class="form-control" name="wilayah" required>
                                </select>
                                <label for="wilayah" class="required">Wilayah</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-material">
                                <select class="form-control" name="aksi" required>
                                    <option value="mutasi">Mutasi</option>
                                    <option value="non-aktif">Non Aktifkan</option>
                                    <option value="aktif">Aktifkan</option>
                                    <option value="pensiun">Pensiun</option>
                                </select>
                                <label for="nama" class="required">Aksi</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="javascript:void(0)" style="margin-bottom:0px" class="btn btn-warning btn-submit" onclick="simpan()">Ubah</a>
                    <button type="button" class="btn btn-white" data-dismiss="modal">Batalkan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function(){
        $('#modal_ubah').modal('show');
        ubah_wilayah();
    })

    function ubah_wilayah(){
        var tingkat = $('select[name=tingkat]').val();
        if(tingkat!='kota'){
            $('.role_bunda_kota').show();
            $.get("{{route('dinas-user-get-wilayah')}}?jenis="+tingkat,function(data){
                var html = '';
                $.each(data.obj,function(k,v){
                    if(tingkat=='kecamatan'){
                        var id_pilih = '{{$profil->id_kecamatan}}'
                    }else{
                        var id_pilih = '{{$profil->id_kelurahan}}'
                    }
                    var select = '';
                    if(v.id==id_pilih){
                        select = 'selected';
                    }

                    html += '<option value="'+v.id+'" '+select+'>'+v.nama+'</option>';
                });

                $('select[name=wilayah]').html(html);
            });
        }else{
            $('.role_bunda_kota').hide();
        }
        $.post("{{route('dinas-all-bunda-jabatan')}}",{tingkat:tingkat},function(data){
            var html = '';
            $.each(data,function(k,v){
                var id_pilih = '{{$profil->id_jabatan}}'
                var select = '';
                if(v.id==id_pilih){
                    select = 'selected';
                }

                html+= '<option value="'+v.id+'" '+select+'>'+v.nama_jabatan+'</option>';
            })

            $('select[name=jabatan]').html(html);
        });
    }
</script>