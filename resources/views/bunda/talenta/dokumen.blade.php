<div class="block-content block-content-full">
    @include('layout.alert')
    <select name="tahun_dokumen" class="form-control" onchange="get_data_dokumen()">
        @for($i=2023;$i<=date('Y');$i++)
        <option @if($i==date('Y')) selected @endif value="{{$i}}">{{$i}}</option>
        @endfor
    </select>
    <table id="dtJadwal" class="table table-bordered">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Tempat</th>
                <th>Kecamatan</th>
                <th>Jumlah Peserta Disetujui</th>
                <th>Susunan Kepanitiaan</th>
                <th>Susunan Acara</th>
                <th>Laporan Kegiatan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>

        </tbody>
    </table>
</div>
<script type="text/javascript">
    var dok_table;
    $(document).ready(function(){
        var tahun = $('select[name=tahun_dokumen]').val();
        dok_table = $('#dtJadwal').DataTable(
        {
            dom: "<'row'<'col-md-6'l><'col-md-6'B>>" +
            "<'row'<'col-md-12'f>>" +
            "<'row'<'col-md-12't>><'row'<'col-md-12'ip>>",
            buttons: [{
                extend: 'excel',
                title: 'judul',
                filename: function() {
                    judul = $('.judul').html() + '_{{ date('d-m-Y H.i') }}';
                    return judul;
                }
            },
            {
                extend: 'print',
                title: function() {
                    judul = $('.judul').html();
                    return judul;
                },
                filename: function() {
                    judul = $('.judul').html() + '_{{ date('d-m-Y H.i') }}';
                    return judul;
                }
            },
            ],
            lengthMenu: [
                [10, 25, 50, -1],
                [10, 25, 50, "All"]
                ],
            processing: true,
            serverSide: true,
            ajax: '{!! route('bunda-talenta-get_data_dokumen') !!}?tahun=' + tahun,
            columns: [
            {
                data: 'DT_RowIndex',
                name: 'DT_RowIndex',
                orderable: false,
                searchable: false
            },
            {
                data:'nama',
                name:'nama'
            },
            {
                data:'kecamatan',
                name:'kecamatan'
            },
            {
                data:'jumlah_peserta',
                name:'jumlah_peserta'
            },
            {
                data:'berkas_panitia',
                name:'berkas_panitia'
            },
            {
                data:'berkas_susunan_acara',
                name:'berkas_susunan_acara'
            },
            {
                data:'berkas_laporan',
                name:'berkas_laporan'
            },
            {
                data:'cetak',
                name:'cetak'
            }
            ]
        });
    })

    function get_data_dokumen(){
        $('tbody').html('<tr><td colspan="7"><h4>Harap tunggu.....</h4></td></tr>');
        var tahun = $('select[name=tahun_dokumen]').val();
        dok_table.ajax.url('{!! route('bunda-talenta-get_data_dokumen') !!}?tahun=' + tahun).load();
    }

    function upload_dokumen_bunda(param){
        var data = new FormData();
        var isi_file = $('.'+param.kolom+'_'+param.tempat_id+'_'+param.kecamatan);
        console.log(isi_file[0].files[0]);
        data.append('berkas',isi_file[0].files[0]);
        data.append('param',JSON.stringify(param));

        Swal.fire({
            type: 'warning',
            title: 'Apakah kamu yakin?',
            showConfirmButton: true,
            showCancelButton: true,
        }).then((result) => {
            if (result.value) {
                $.ajax({
                    url: '{{route('bunda-talenta-upload_berkas')}}',
                    data: data,
                    processData: false,
                    contentType: false,
                    type: 'POST',
                    success: function(data){
                        if(data.code==200){
                            get_data_dokumen();
                            swal('Success',data.pesan,'success');
                        }else{
                            swal('Whooops',data.pesan,'error');
                        }
                    }
                });
            }else{
            }
            $('.'+param.kolom+'_'+param.tempat_id+'_'+param.kecamatan).val('');
        })
    }

    function lihat_file(path){
        var url = "{{ route('storage',[':path','1']) }}";
        url = url.replace(":path",btoa(path));

        window.open(url)
    }
</script>
