<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title>Halaman Sinkron</title>
</head>

<body>
    <div class="flex min-h-screen flex-col items-center justify-center gap-4">
        <div class="text-center text-3xl">
            Sinkron Data Penduduk (<span class="wadah_nik"></span>)
        </div>
        <div>
            <div>
                <img class="loading_gambar"
                    src="https://i.pinimg.com/originals/92/87/c5/9287c58a0dcdb3de8e3701da7583eb9f.gif" alt=""
                    style="width: 200px">
            </div>
            <div class="status_loading">
                Mohon tunggu...
            </div>
        </div>
    </div>
</body>
<script src="{{ asset('assets/js/core/jquery.min.js') }}"></script>
<script>
    var nik = [];
    var error = 0;
    $(document).ready(function() {
        @foreach ($penduduk as $p)
            nik.push('{{ $p->nik }}');
        @endforeach

        baris(0);
    });

    function baris(i) {
        var title = '';
        $('.wadah_nik').html(nik[i]);
        if ((i + {{ $ofset }}) <= ((nik.length - 1) + {{ $ofset }})) {
            $.post('{{ route('bunda-dashboard_asw-proses_sinkron') }}', {
                    nik: nik[i]
                },
                function(data) {
                    if (data.code == 200) {
                        baris(i + 1);
                    } else {
                        error++;
                        baris(i);
                        if (error >= 3) {
                            error = 0;
                            baris(i + 1);
                        }
                    }
                    title = (i + {{ $ofset }}) + '/' + ((nik.length - 1) + {{ $ofset }}) + ' Data';
                    $('.status_loading').html(title);
                }).fail(function() {
                error++;
                baris(i);
                if (error >= 3) {
                    error = 0;
                    baris(i + 1);
                }
            });
        } else {
            title = 'Terimas kasih data selesai disinkron';
            setTimeout(function() {
                $('.status_loading').html(title);
                $('.loading_gambar').attr('src',
                    'https://i.pinimg.com/originals/19/b1/8c/19b18cd43123b60955adfcfd9eb98f96.gif')
            }, 2000);
        }
    }

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
</script>

</html>
