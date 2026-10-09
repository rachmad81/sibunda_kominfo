<!DOCTYPE HTML>
<html lang="en" class="no-focus">

<head>
    @include('layout/metadata')

    @include('layout/styles')

    @yield('additional_styles')
</head>

<body>
    <div id="page-container" class="sidebar-o enable-page-overlay side-scroll page-header-modern main-content-boxed">
        @include('layout/sideoverlay')
        @include('layout/topnavbar')

        <!-- Main Container -->
        <main id="main-container">

            <nav id="sidebar">
                <!-- Sidebar Content -->
                <div class="sidebar-content">
                    <!-- Side Header -->
                    <div class="content-header content-header-fullrow px-15"
                        style="padding-top: 2px; padding-bottom: 5px; height: 55px;">
                        <!-- Mini Mode -->
                        <div class="content-header-section sidebar-mini-visible-b">
                            <!-- Logo -->
                            <span class="content-header-item font-w700 font-size-xl float-left animated fadeIn">
                                <span class="text-dual-primary-dark">c</span><span class="text-primary">b</span>
                            </span>
                            <!-- END Logo -->
                        </div>
                        <!-- END Mini Mode -->

                        <!-- Normal Mode -->
                        <div class="content-header-section text-center align-parent sidebar-mini-hidden">
                            <!-- Close Sidebar, Visible only on mobile screens -->
                            <!-- Layout API, functionality initialized in Template._uiApiLayout() -->
                            <button type="button" class="btn btn-circle btn-dual-secondary d-lg-none align-v-r"
                                data-toggle="layout" data-action="sidebar_close">
                                <i class="fa fa-times text-danger"></i>
                            </button>
                            <!-- END Close Sidebar -->

                            <!-- Logo -->
                            <div class="content-header-item">
                                <a class="link-effect font-w700" href="#">
                                    <img src="{{ asset('logo/logo-sibunda.png') }}" width="50px" height="50px"
                                        style="margin-bottom: 5px">
                                    <span class="font-size-MD text-dual-primary-dark"> SI BUNDA</span><span
                                        class="font-size-xl text-primary"></span>
                                </a>
                            </div>
                            <!-- END Logo -->
                        </div>
                        <!-- END Normal Mode -->
                    </div>
                    <!-- END Side Header -->
                    <!-- Side User -->
                    <div class="content-side content-side-full content-side-user px-10 align-parent"
                        style="padding-top: 5px;">
                        <!-- Visible only in mini mode -->
                        <div class="sidebar-mini-visible-b align-v animated fadeIn">
                            <img class="img-avatar img-avatar32"
                                src="{{ \Auth::User()->getFotoProfil() ? \Auth::User()->getFotoProfil() : url('assets/media/avatars/avatar15.jpg') }}"
                                alt="">
                        </div>
                        <!-- END Visible only in mini mode -->

                        <!-- Visible only in normal mode -->
                        <div class="sidebar-mini-hidden-b text-center"
                            id="sidebar_style_inverse_toggle_action_container">
                            <a class="img-link" href="#">
                                <img class="img-avatar" src="{!! \Auth::User()->getFotoProfil() ? \Auth::User()->getFotoProfil() : url('assets/media/avatars/avatar15.jpg') !!}" alt="">
                            </a>
                            <ul class="list-inline mt-10">
                                <li class="list-inline-item">
                                    <a class="link-effect text-dual-primary-dark font-size-xs font-w600 text-uppercase"
                                        href="#"><small>
                                            @if (Auth::getUser()->role == 'mitra')
                                                {{ \Auth::User()->nama }}
                                            @else
                                                {{ \Auth::User()->getNamaProfil() }}
                                            @endif
                                        </small></a>
                                </li>
                                @if (Auth::getUser()->role != 'mitra')
                                    <li class="list" style="padding:0;margin-bottom:3px">
                                        <small>{!! \Auth::User()->getRole() !!}</small>
                                    </li>
                                @endif
                                <li class="list" style="padding:0;margin-bottom:3px">
                                    <label class="badge btn-alt-primary"
                                        style="padding: 5px 7px;">{!! \Auth::User()->getTingkat() !!}</label>
                                </li>
                            </ul>
                        </div>
                        <!-- END Visible only in normal mode -->
                    </div>
                    <!-- END Side User -->

                    @yield('sidebar')
                </div>
                <!-- Sidebar Content -->
            </nav>


            @yield('content')

        </main>
        <!-- END Main Container -->

        @include('layout/footer')
    </div>

    <div class="popup_rating">
        @include('rating.index')
    </div>


    @include('layout/script')

    @yield('additional_scripts')


</body>

</html>
