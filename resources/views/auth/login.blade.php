<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Aplikasi Kami</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
        }

        .input-focus:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .captcha-box {
            background: repeating-linear-gradient(45deg,
                    #f3f4f6,
                    #f3f4f6 10px,
                    #e5e7eb 10px,
                    #e5e7eb 20px);
            letter-spacing: 0.5em;
            user-select: none;
        }

        .alert-danger {
            background: #cc5555;
            color: #fff;
            padding: 10px;
        }
    </style>
</head>

<body>

    <div class="w-full max-w-md p-6">
        <div class="login-card rounded-3xl p-8 md:p-10">
            <!-- Logo Aplikasi -->
            <div class="flex flex-col items-center mb-8">
                <div
                    class="w-20 h-20 bg-blue-500 rounded-2xl flex items-center justify-center mb-4 shadow-lg shadow-blue-200">
                    <img src="{{ asset('logo/logo-sibunda.png') }}" class="h-18 w-18 text-white"
                        alt="Logo aplikasi SIBUNDA">
                    {{-- <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-white" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4" />
                    </svg> --}}
                </div>
                <h1 class="text-2xl font-bold text-gray-800">Selamat Datang</h1>
                <p class="text-gray-500 text-sm mt-1">Silakan masuk ke akun Anda</p>
            </div>

            <!-- Form Login -->
            <form id="loginForm" class="space-y-5" action="{{ route('try-login') }}" method="post">
                @csrf
                <!-- Jenis User -->
                @if (session()->has('alert'))
                    <div class="alert alert-danger"><i class="si si-info"></i> {{ session()->get('alert') }}</div>
                @endif
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5 ml-1">Jenis User</label>
                    <div class="relative">
                        <select name="userType" id="userType" required
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 text-gray-700 transition-all input-focus appearance-none">
                            <option value="" disabled selected>Pilih akses...</option>
                            <option value="dinas">Admin</option>
                            <option value="bunda">Bunda</option>
                            <option value="penilai">Penilai</option>
                            <option value="opd">OPD</option>
                            <option value="k3_paud">K3 PAUD</option>
                            <option value="paud_sd">Forum KOmunikasi PAUD SD</option>
                            {{-- <option value="gugus">Gugus Tugas</option> --}}
                            <option value="mitra">Mitra</option>
                        </select>
                        <div
                            class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-400">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M19 9l-7 7-7-7" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Username -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5 ml-1">Username</label>
                    <input type="text" name="username" required placeholder="Masukkan username"
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 text-gray-700 transition-all input-focus"
                        value="{{ old('username') }}">
                </div>

                <!-- Password -->
                <div>
                    <div class="flex justify-between items-center mb-1.5 ml-1">
                        <label class="block text-sm font-semibold text-gray-700">Password</label>
                        {{-- <a href="#" class="text-xs text-blue-500 hover:underline">Lupa password?</a> --}}
                    </div>
                    <input type="password" name="password" required placeholder="••••••••"
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 text-gray-700 transition-all input-focus">
                    <input type="checkbox" onclick="tampilkan_sandi()" class="tampil_sandi"
                        value="{{ old('password') }}"> Tampilkan sandi
                </div>

                <!-- Captcha Section -->
                <div class="space-y-3 p-4 bg-gray-50 rounded-2xl border border-gray-100">
                    <label class="block text-sm font-semibold text-gray-700 ml-1">Verifikasi Keamanan</label>
                    <div class="flex items-center gap-3">
                        <div id="captchaDisplay"
                            class="captcha-box flex-1 h-12 flex items-center justify-center font-bold text-xl italic text-gray-600 rounded-xl border border-gray-200 shadow-inner">
                            {!! captcha_img() !!}
                            <!-- Captcha text generated by JS -->
                        </div>
                        <button type="button" onclick="reload_captcha()"
                            class="p-3 text-blue-500 hover:bg-blue-50 rounded-xl transition-colors"
                            title="Refresh Captcha">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                        </button>
                    </div>
                    <input type="text" id="captchaInput" name="captcha" required placeholder="Masukkan kode di atas"
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-white text-gray-700 transition-all input-focus">
                </div>

                <!-- Tombol Login -->
                <button type="submit"
                    class="w-full py-3.5 bg-blue-500 hover:bg-blue-600 text-white font-bold rounded-xl shadow-lg shadow-blue-200 transition-all active:scale-95 mt-4">
                    Masuk Sekarang
                </button>
                <div style="text-align: center">
                    <a href="{{ url('/') }}">
                        Kembali ke beranda
                    </a>
                </div>
            </form>

            <!-- Footer -->
            {{-- <div class="mt-8 text-center">
                <p class="text-gray-500 text-sm">Belum punya akun? <a href="#"
                        class="text-blue-500 font-semibold hover:underline">Daftar</a></p>
            </div> --}}
        </div>

        <!-- Pesan Alert Custom -->
        <div id="alertBox" class="hidden mt-4 p-4 rounded-xl text-center text-sm font-medium"></div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script>
        // Jalankan saat pertama kali load
        // window.onload = generateCaptcha;

        function reload_captcha() {
            $.ajax({
                type: 'GET',
                url: 'reload-captcha',
                success: function(data) {
                    $("#captchaDisplay").html(data.captcha);
                }
            });
        };

        function login() {

        }

        function tampilkan_sandi() {
            var cek = $('.tampil_sandi').prop('checked');
            if (cek) {
                $('input[name=password]').attr('type', 'text');
            } else {
                $('input[name=password]').attr('type', 'password');
            }
        }

        // document.getElementById('loginForm').addEventListener('submit', function(e) {
        //     e.preventDefault();
        //     const alertBox = document.getElementById('alertBox');
        //     const captchaInput = document.getElementById('captchaInput').value.toUpperCase();
        //     const formData = new FormData(this);

        //     // Simulasi loading
        //     const btn = this.querySelector('button');
        //     const originalText = btn.innerText;
        //     btn.innerText = 'Memproses...';
        //     btn.disabled = true;

        //     setTimeout(() => {
        //         alertBox.innerText = `Berhasil login sebagai ${formData.get('userType')}!`;
        //         alertBox.className =
        //             'mt-4 p-4 rounded-xl text-center text-sm font-medium bg-green-100 text-green-700';
        //         alertBox.classList.remove('hidden');

        //         btn.innerText = originalText;
        //         btn.disabled = false;
        //     }, 1000);
        // });
    </script>
</body>

</html>
