<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title>Sinkron</title>
</head>

<body>
    @php
        $daftar_kategori = [
            'Total anak',
            'Bersekolah',
            'Tidak Bersekolah',
            'TS sudah survey',
            'TS sudah survey Sesuai Alamat',
            'TS sudah survey Pindah dalam kota',
            'TS sudah survey Pindah luar kota',
            'TS sudah survey Tidak ditemukan',
            'TS sudah survey Ganda',
            'TS sudah survey Meninggal',
            'TS sudah survey Lain',
            'TS sudah survey Faktor 1',
            'TS sudah survey Faktor 2',
            'TS sudah survey Faktor 3',
            'TS sudah survey Faktor 4',
            'TS sudah survey Faktor 5',
            'TS sudah survey Faktor lain 5',
            'TS belum survey',
        ];
    @endphp
    <div class="menu space-y-4 p-6">
        <select name="usia" id="">
            <option value="All">..:: Semua Usia ::..</option>
            <option value="0">0</option>
            <option value="1">1</option>
            <option value="2">2</option>
            <option value="3">3</option>
            <option value="4">4</option>
            <option value="5">5</option>
            <option value="6">6</option>
        </select>
        <div>
            <a href="javascript:void(0)" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
                onclick="sinkron()">Sinkron</a>
        </div>
    </div>
    <div class="menu space-y-4 p-6">
        @foreach ($daftar_kategori as $k => $v)
            <div class="border-y-2 border-black py-2 my-2">
                <div>
                    <!-- Tombol link kategori -->
                    <a href="javascript:void(0)" id="link-{{ $k }}" onclick="get_nik({{ $k }})"
                        class="text-blue-600 font-medium hover:underline">
                        {{ $k }} => {{ $v }}
                    </a>
                </div>

                <!-- Area Proses & Lintasan Animasi GIF -->
                <div id="container-proses-{{ $k }}" class="hidden w-full overflow-hidden py-2">
                    <div id="lintasan-{{ $k }}" class="transition-all duration-500 ease-in-out"
                        style="margin-left: 90%;">
                        <img class="loading_gambar inline-block"
                            src="https://i.pinimg.com/originals/92/87/c5/9287c58a0dcdb3de8e3701da7583eb9f.gif"
                            alt="" style="width: 80px">
                        <div class="status text-sm font-semibold mt-1 text-gray-700"></div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</body>

<script src="{{ asset('assets/js/core/jquery.min.js') }}"></script>
<script>
    // Menggunakan Object State untuk menyimpan data per ID kategori secara independen
    let syncState = {};
    let usia;

    function sinkron() {
        usia = $('select[name=usia] option:selected').val();
        @foreach ($daftar_kategori as $k => $v)
            setTimeout(() => {
                $(`#container-proses-{{ $k }} .status`).html('');
                get_nik('{{ $k }}', usia);
            }, 1500);
        @endforeach
    }

    function get_nik(id, usia = '') {
        usia = (usia === '') ? $('select[name=usia] option:selected').val() : usia;
        // Nonaktifkan tombol kategori ini agar tidak diklik dua kali saat proses berjalan
        $(`#link-${id}`).css({
            'pointer-events': 'none',
            'opacity': '0.5'
        });
        $(`#container-proses-${id}`).removeClass('hidden');

        $.post('{{ url('api/cek_penduduk/get_nik') }}', {
            id: id,
            usia: usia
        }, function(data) {
            // Inisialisasi data khusus untuk kategori ID ini
            syncState[id] = {
                nik: data,
                error: 0
            };

            if (syncState[id].nik.length > 0) {
                baris(id, 0);
            } else {
                $(`#container-proses-${id} .loading_gambar`).attr('src',
                    'https://i.pinimg.com/originals/19/b1/8c/19b18cd43123b60955adfcfd9eb98f96.gif');
                $(`#container-proses-${id} .status`).html('Tidak ada data NIK.');
                $(`#link-${id}`).css({
                    'pointer-events': 'auto',
                    'opacity': '1'
                });
            }
        }).fail(function() {
            alert('500 - Internal server error');
            $(`#container-proses-${id}`).addClass('hidden');
            $(`#link-${id}`).css({
                'pointer-events': 'auto',
                'opacity': '1'
            });
        });
    }

    function baris(id, i) {
        let state = syncState[id];
        let nikList = state.nik;

        if (i < nikList.length) {
            const start = performance.now();

            // Hitung persentase pergeseran animasi GIF dari kanan (90%) ke kiri (0%)
            let progressPercent = nikList.length > 1 ? 90 - ((i / (nikList.length - 1)) * 90) : 0;
            $(`#lintasan-${id}`).css('margin-left', progressPercent + '%');

            // --- KODE BENCHMARK LOKAL ---
            let total = 0;
            for (let j = 0; j < 1_000_000; j++) {
                total += j;
            }
            // ----------------------------

            $.post('{{ route('bunda-dashboard_asw-proses_sinkron') }}', {
                    nik: nikList[i]
                },
                function(data) {
                    const end = performance.now();
                    const durasiDetik = ((end - start) / 1000).toFixed(4);

                    const waktu =
                        `<p>Data ${i}/${(nikList.length-1)}, NIK ${nikList[i]} - Waktu = ${durasiDetik} detik</p>`;
                    $(`#container-proses-${id} .status`).append(waktu);

                    if (data.code == 200) {
                        state.error = 0; // Reset error jika sukses
                        baris(id, i + 1); // Lanjut data berikutnya
                    } else {
                        state.error++;
                        if (state.error >= 3) {
                            state.error = 0;
                            baris(id, i + 1); // Skip jika sudah 3x gagal
                        } else {
                            baris(id, i); // Ulangi NIK yang sama
                        }
                    }
                }).fail(function(xhr) {
                const end = performance.now();
                const durasiDetik = ((end - start) / 1000).toFixed(4);

                const waktu =
                    `<p style="color:red;">Data ${i}, NIK Gagal ${nikList[i]} (Status: ${xhr.status}) - Waktu = ${durasiDetik} detik</p>`;
                $(`#container-proses-${id} .status`).append(waktu);

                state.error++;
                if (state.error >= 3) {
                    state.error = 0;
                    baris(id, i + 1);
                } else {
                    baris(id, i);
                }
            });

        } else {
            // Selesai: Geser ke 0% (kiri penuh) dan ubah gambar GIF
            $(`#lintasan-${id}`).css('margin-left', '0%');

            setTimeout(function() {
                $(`#container-proses-${id} .status`).append(
                    '<span class="text-green-600 font-bold">Terima kasih, data selesai disinkron!</span>'
                );
                $(`#container-proses-${id} .loading_gambar`).attr('src',
                    'https://i.pinimg.com/originals/19/b1/8c/19b18cd43123b60955adfcfd9eb98f96.gif');

                // Aktifkan kembali tombol link jika ingin diklik ulang
                $(`#link-${id}`).css({
                    'pointer-events': 'auto',
                    'opacity': '1'
                });
            }, 1000);
        }
    }

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
</script>

</html>
