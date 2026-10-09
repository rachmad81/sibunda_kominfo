<!-- Modal -->
<div id="modal_profil" class="modal fade" role="dialog">
    <div class="modal-dialog modal-md">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Setting jadwal Talenta Seni</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="row">
                	<form id="form-jadwal">
	                	<table style="width: 100%">
	                		<tr>
	                			<td><b>Tempat</b></td>
	                			<td>
	                				<input type="hidden" name="id" value="{{$id}}">
	                				<select name="tempat_id" class="form-control">
		                				@foreach($tempat as $t)
		                				<option @if($t->id==$jadwal->tempat_id) selected @endif value="{{$t->id}}">{{$t->nama}}</option>
		                				@endforeach
	                				</select>
	                			</td>
	                		</tr>
	                		<tr>
	                			<td><b>Tanggal</b></td>
	                			<td><input class="form-control" type="date" name="tanggal" value="{{$jadwal->tanggal}}"></td>
	                		</tr>
	                		<tr>
	                			<td><b>Jam</b></td>
	                			<td>
	                				<input class="form-control" type="time" name="mulai" value="{{$jadwal->mulai}}">
	                				<input class="form-control" type="time" name="selesai" value="{{$jadwal->selesai}}">
	                			</td>
	                		</tr>
	                		<tr>
	                			<td><b>Jumlah Lembaga</b></td>
	                			<td>
	                				<input class="form-control" type="number" name="jumlah_lembaga" value="{{$jadwal->jumlah_lembaga}}">
	                			</td>
	                		</tr>
	                		<tr>
	                			<td><b>Jumlah Siswa</b></td>
	                			<td>
	                				<input class="form-control" type="number" name="jumlah_siswa" value="{{$jadwal->jumlah_siswa}}">
	                			</td>
	                		</tr>
	                		<tr>
	                			<td><b>Kecamatan</b></td>
	                			<td>
	                				<select name="kecamatan" class="form-control">
		                				@foreach($kecamatan as $k)
		                				<option @if($k->kecamatan_dispenduk==$jadwal->kecamatan) selected @endif value="{{$k->kecamatan_dispenduk}}">{{$k->kecamatan_dispenduk}}</option>
		                				@endforeach
	                				</select>
	                			</td>
	                		</tr>
	                	</table>
                	</form>
                </div>
            </div>
            <div class="modal-footer">
                <a href="javascript:void(0)" class="btn btn-primary" onclick="simpan()">Simpan</a>
                <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
            </div>

        </div>
    </div>
    <style type="text/css">
        label {
            font-weight: 10;
        }

        table.kop_jadwal tr td {
            text-align: left;
            vertical-align: top;
            padding: 5px;
        }

        .modal-lg {
            max-width: 1200px !important;
        }
    </style>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#modal_profil').modal({
                backdrop: 'static'
            });
        });

        function simpan() {
            Swal.fire({
                type: 'warning',
                title: 'Apakah kamu yakin?',
                showConfirmButton: true,
                showCancelButton: true,
            }).then((result) => {
                if (result.value) {
                    var data = $('#form-jadwal').serialize(0);
                    $.post("{{ route('bunda-jadwal_talenta-konfirmasi') }}", data, function(data) {
                        if (data.code == '200') {
                            $('.modal-page').html('');
                            $('.modal-backdrop').removeClass('modal-backdrop');
                            get_data();
                            swal('Success', data.message, 'success');
                        } else {
                            swal('Whooop', data.message, 'error');
                        }
                    });
                }
            })
        }
    </script>
