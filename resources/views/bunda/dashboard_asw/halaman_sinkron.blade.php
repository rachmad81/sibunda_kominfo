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
            Sinkron Data dengan NIK {{ $nik }}
        </div>
        <div>
            <div>
                <img src="https://i.pinimg.com/originals/92/87/c5/9287c58a0dcdb3de8e3701da7583eb9f.gif" alt="">
            </div>
            <div>
                Mohon tunggu...
            </div>
        </div>

        <div>
            <a href="{{ route('bunda-dashboard_asw-detail_informasi', ['nik' => $nik]) }}"
                class="bg-amber-100 hover:bg-amber-200 text-amber-800 font-medium py-2 px-4 rounded-lg transition duration-150 ease-in-out">Kembali</a>
        </div>
    </div>
</body>
<script src="{{ asset('assets/js/core/jquery.min.js') }}"></script>
<script>
    $(document).ready(function() {
        sinkron();
    });

    function sinkron() {
        $.post('{{ route('bunda-dashboard_asw-proses_sinkron') }}', {
            nik: '{{ $nik }}'
        }, function(data) {
            if (data.code == 200) {
                window.location = '{{ route('bunda-dashboard_asw-detail_informasi', ['nik' => $nik]) }}';
            } else {
                alert('Tidak bisa sinkron');
            }
        }).fail(function() {
            alert('Tidak bisa sinkron');
        });
    }

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
</script>

</html>
