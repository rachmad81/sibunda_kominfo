<!doctype html>
    <html lang="id" class="no-focus">
    <head>
        @include('layout.metadata')
        @include('layout.styles')

    </head>
    <body>
        <div id="page-container" class="main-content-boxed">
            @include('layout.navbar-mobile')
            <!-- Main Container -->
            <main id="main-container">

                <!-- Page Content -->
                <div class="bg-body-dark bg-pattern" style="background-image: url('assets/media/various/bg-pattern-inverse.png');">
                    <div class="row mx-0 justify-content-center">
                        <div class="hero-static col-lg-6 col-xl-4">
                            <div class="content content-full overflow-hidden">
                                <!-- Header -->
                                <div class="mt-30 py-30 text-center">
                                    <a class="link-effect font-w700" href="index.html">
                                        <img src="{{ asset('logo/logo-sibunda.png') }}" style="max-width: 120px; max-height: 120px;">
                                    </a>
                                    <h1 class="h4 font-w700 mt-10 mb-10">Selamat Datang Bunda</h1>
                                    <h2 class="h5 font-w400 text-muted mb-0">Sistem Informasi Bunda PAUD Kota Surabaya</h2>
                                </div>
                                <!-- END Header -->

                                <form action="{{ route('try-login') }}" method="post" style="margin-bottom: 20vh">
                                    @csrf
                                    <div class="block block-themed block-rounded block-shadow">
                                        <div class="block-header bg-gd-primary">
                                            <h3 class="block-title">Informasi Akun</h3>
                                            <div class="block-options">
                                                <button type="button" class="btn-block-option">
                                                    <i class="si si-user"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="block-content">
                                            @if(session()->has('alert'))
                                            <div class="alert alert-danger"><i class="si si-info"></i> {{ session()->get('alert') }}</div>
                                            @endif
                                            <div class="row">
                                                <div class="col-lg-12 col-md-12">
                                                @include('attention_login')
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-12">
                                                    <label for="username">Nama Pengguna</label>
                                                    <input type="text" class="form-control" name="username" required value="{{ old('username') }}">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-12">
                                                    <label for="password">Kata Sandi</label>
                                                    <input type="password" class="form-control" name="password" required minlength="4">
                                                </div>
                                                <div class="col-lg-12">
                                                    <label><input type="checkbox" class="tampil_sandi" onchange="tampilkan_sandi()"> Tampilkan Kata Sandi</label>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-12">
                                                    <label for="password">Captcha</label>
                                                    <div class="captcha">
                                                        <span>{!! captcha_img() !!}</span>
                                                        <button type="button" class="btn btn-danger" class="reload" id="reload">
                                                            &#x21bb;
                                                        </button>
                                                    </div>
                                                    <input type="text" placeholder="captcha" class="form-control" name="captcha" required minlength="4">
                                                </div>
                                            </div>
                                            <div class="form-group row mb-0">
                                                <div class="col-sm-6 d-sm-flex align-items-center push">
                                                    <div class="custom-control custom-checkbox mr-auto ml-0 mb-0">
                                                        <input type="checkbox" class="custom-control-input" id="remember_me" name="remember_me">
                                                        <label class="custom-control-label" for="remember_me">Ingat Saya</label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6 text-sm-right push">
                                                    <button type="submit" class="btn btn-alt-primary">
                                                        <i class="si si-login mr-10"></i> Masuk
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-lg-12">
                                                    <a class="col-lg-12 btn btn-outline-info" href="{{ url('/') }}">
                                                        <i class="si si-action-undo"></i> Kembali ke Dashboard
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                                <!-- END Sign In Form -->
                            </div>
                        </div>
                    </div>
                </div>
                <!-- END Page Content -->

            </main>
            <!-- END Main Container -->
        </div>
        <!-- END Page Container -->
    </div>

    <script src="assets/js/codebase.core.min.js"></script>

    <script src="assets/js/codebase.app.min.js"></script>

    <!-- Page JS Plugins -->
    <script src="assets/js/plugins/jquery-validation/jquery.validate.min.js"></script>

    <!-- Page JS Code -->
    <script src="assets/js/pages/op_auth_signin.min.js"></script>
    <script type="text/javascript">
        $('#reload').click(function () {
            $.ajax({
                type: 'GET',
                url: 'reload-captcha',
                success: function (data) {
                    $(".captcha span").html(data.captcha);
                }
            });
        });

        function tampilkan_sandi(){
            var cek = $('.tampil_sandi').prop('checked');
            if(cek){
                $('input[name=password]').attr('type','text');
            }else{
                $('input[name=password]').attr('type','password');
            }
        }
    </script>
</body>
</html>