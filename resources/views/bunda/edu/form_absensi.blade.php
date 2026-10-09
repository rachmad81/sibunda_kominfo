<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulir Absensi Digital</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .signature-pad {
            border: 2px dashed #cbd5e1;
            border-radius: 0.5rem;
            cursor: crosshair;
            touch-action: none;
        }

        body {
            background-color: #f8fafc;
        }
    </style>
</head>

<body class="min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-lg bg-white rounded-xl shadow-lg p-8">
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-gray-800">Daftar Hadir</h1>
            <p class="text-gray-500">Silakan isi data diri Anda di bawah ini</p>
        </div>

        <form id="attendanceForm" class="space-y-5">
            <!-- Nama Kegiatan -->
            <div>
                <label for="nik" class="block text-sm font-medium text-gray-700 mb-1">Nama Tempat</label>
                <h2 style="font-weight: bold">{{ $educity->nama_tempat }}</h2>
                <h2 style="font-weight: bold">{{ $educity->npsn }} - {{ $educity->nama_sekolah }}</h2>
                <h2 style="font-weight: bold">{{ App\Tools\Waktu::parseFullDate($educity->tanggal) }}</h2>
            </div>
            <!-- Input NIK -->
            <div>
                <label for="nik" class="block text-sm font-medium text-gray-700 mb-1">NIK (Nomor Induk
                    Kependudukan)</label>
                <input type="hidden" name="id_kegiatan" value="{{ $educity->id }}">
                <input type="text" id="nik" name="nik" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all"
                    placeholder="Contoh: 3201xxxxxxxxxxxx">
            </div>

            <!-- Input Nama -->
            <div>
                <label for="nama" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                <input type="text" id="nama" name="nama" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all"
                    placeholder="Masukkan nama lengkap">
            </div>

            <!-- Input Instansi -->
            <div>
                <label for="instansi" class="block text-sm font-medium text-gray-700 mb-1">Instansi / Lembaga</label>
                <input type="text" id="instansi" name="instansi" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all"
                    placeholder="Contoh: PT. Maju Jaya / Universitas Indonesia">
            </div>

            <!-- Papan Tanda Tangan -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanda Tangan</label>
                <div class="relative">
                    <canvas id="signaturePad" class="signature-pad w-full h-40 bg-gray-50"></canvas>
                    <button type="button" id="clearBtn"
                        class="absolute bottom-2 right-2 text-xs bg-red-100 text-red-600 px-2 py-1 rounded hover:bg-red-200 transition-colors">
                        Hapus
                    </button>
                </div>
                <p class="text-xs text-gray-400 mt-1">*Gunakan mouse atau jari untuk tanda tangan</p>
            </div>

            <!-- Tombol Submit -->
            <button type="submit"
                class="w-full bg-blue-600 text-white font-semibold py-3 rounded-lg hover:bg-blue-700 transition-colors shadow-md">
                Kirim Absensi
            </button>
        </form>

        <!-- Pesan Sukses (Tersembunyi) -->
        <div id="successMessage" class="hidden text-center py-10">
            <div class="mb-4 text-green-500">
                <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h2 class="text-xl font-bold text-gray-800">Terima Kasih!</h2>
            <p class="text-gray-600 mt-2">Absensi Anda telah berhasil direkam.</p>
            <button onclick="window.location.reload()" class="mt-6 text-blue-600 font-medium underline">Isi form
                kembali</button>
        </div>

        <!-- Pesan Error (Tersembunyi) -->
        <div id="errorMessage" class="hidden text-center py-10">
            <div class="mb-4 text-green-500">
                <svg class="w-16 h-16 mx-auto text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z">
                    </path>
                </svg>
            </div>
            <h2 class="text-xl font-bold text-gray-800">Mohon maaf!</h2>
            <p class="text-gray-600 mt-2 wadah_pesan_error">Terjadi kendala server.</p>
            <button onclick="window.location.reload()" class="mt-6 text-blue-600 font-medium underline">Isi form
                kembali</button>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        const canvas = document.getElementById('signaturePad');
        const ctx = canvas.getContext('2d');
        const clearBtn = document.getElementById('clearBtn');
        const form = document.getElementById('attendanceForm');
        const successMessage = document.getElementById('successMessage');
        const errorMessage = document.getElementById('errorMessage');

        // Menyesuaikan ukuran canvas
        function resizeCanvas() {
            const ratio = Math.max(window.devicePixelRatio || 1, 1);
            canvas.width = canvas.offsetWidth * ratio;
            canvas.height = canvas.offsetHeight * ratio;
            ctx.scale(ratio, ratio);
            ctx.strokeStyle = "#1e293b";
            ctx.lineWidth = 2;
            ctx.lineCap = "round";
        }

        window.addEventListener('resize', resizeCanvas);
        resizeCanvas();

        let drawing = false;

        function getPos(e) {
            const rect = canvas.getBoundingClientRect();
            const clientX = e.touches ? e.touches[0].clientX : e.clientX;
            const clientY = e.touches ? e.touches[0].clientY : e.clientY;
            return {
                x: clientX - rect.left,
                y: clientY - rect.top
            };
        }

        function startDrawing(e) {
            drawing = true;
            const pos = getPos(e);
            ctx.beginPath();
            ctx.moveTo(pos.x, pos.y);
            e.preventDefault();
        }

        function draw(e) {
            if (!drawing) return;
            const pos = getPos(e);
            ctx.lineTo(pos.x, pos.y);
            ctx.stroke();
            e.preventDefault();
        }

        function stopDrawing() {
            drawing = false;
        }

        // Event Listeners Mouse
        canvas.addEventListener('mousedown', startDrawing);
        canvas.addEventListener('mousemove', draw);
        window.addEventListener('mouseup', stopDrawing);

        // Event Listeners Touch (Mobile)
        canvas.addEventListener('touchstart', startDrawing);
        canvas.addEventListener('touchmove', draw);
        canvas.addEventListener('touchend', stopDrawing);

        // Hapus Tanda Tangan
        clearBtn.addEventListener('click', () => {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
        });

        // Submit Form
        form.addEventListener('submit', (e) => {
            e.preventDefault();

            // Validasi sederhana: Cek apakah canvas kosong (bisa ditingkatkan)
            // Di sini kita hanya mensimulasikan proses pengiriman

            const formData = {
                nik: document.getElementById('nik').value,
                nama: document.getElementById('nama').value,
                instansi: document.getElementById('instansi').value,
                id_kegiatan: $('input[name=id_kegiatan]').val(),
                signature: canvas.toDataURL() // Data gambar base64
            };

            console.log("Data Absensi Dikirim:", formData);

            $.post('{{ route('bunda-edu-halaman_absensi', ['halaman' => 'simpan']) }}', formData, function(
                data) {
                form.classList.add('hidden');
                if (data.code == 200) {
                    // Sembunyikan form dan tampilkan pesan sukses
                    form.classList.add('hidden');
                    successMessage.classList.remove('hidden');
                } else {
                    form.classList.add('hidden');
                    $('.wadah_pesan_error').html(data.pesan);
                    errorMessage.classList.remove('hidden');
                }
            }).fail(function() {
                form.classList.add('hidden');
                $('.wadah_pesan_error').html('500 - Internal server error');
                errorMessage.classList.remove('hidden');
            });
        });
    </script>
</body>

</html>
