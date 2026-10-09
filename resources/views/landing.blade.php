<!doctype html>
<html lang="en" class="no-focus">
    <head>
        @include("layout/metadata")

        @include("layout/styles")

        @yield('additional_styles')
    </head>
    <body>

        <div id="page-container" class="sidebar-inverse side-scroll page-header-fixed page-header-glass page-header-inverse main-content-boxed">
            @include("layout.navbar-mobile")
            <!-- Header -->
            <header id="page-header">
                <!-- Header Content -->
                <div class="content-header">
                    <!-- Left Section -->
                    <div class="content-header-section">
                        <!-- Logo -->
                        <div class="content-header-item mr-5">
                            <a class="link-effect font-w600" href="index.html">
                                <img src="{{ asset('logo/logo-sibunda.png') }}" style="max-width: 50px; max-height: 30px;">
                            </a>
                        </div>
                        <!-- END Logo -->
                        <button type="button" class="btn btn-rounded btn-dual-secondary" data-toggle="layout" data-action="header_search_on">
                            <i class="fa fa-search mr-5"></i> Search
                        </button>
                    </div>
                    <!-- END Left Section -->

                    <!-- Right Section -->
                    <div class="content-header-section">
                        
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-rounded btn-dual-secondary" id="page-header-user-dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa fa-user"></i>
                                <i class="fa fa-angle-down ml-5"></i>
                            </button>
                            @if(\Auth::check())
                            <div class="dropdown-menu dropdown-menu-right min-width-200" aria-labelledby="page-header-user-dropdown">
                                <h5 class="h6 text-center py-10 mb-5 border-b text-uppercase">User</h5>
                                <a class="dropdown-item" href="be_pages_generic_profile.html">
                                    <i class="si si-user mr-5"></i> Profile
                                </a>

                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="op_auth_signin.html">
                                    <i class="si si-logout mr-5"></i> Sign Out
                                </a>
                            </div>
                            @else
                            <div class="dropdown-menu dropdown-menu-right min-width-150" aria-labelledby="page-header-user-dropdown">
                                <a class="dropdown-item" href="{{ route('login') }}" style="margin-bottom: 0px">
                                    <i class="si si-lock mr-5"></i> Login
                                </a>
                            </div>
                            @endif
                        </div>
                        <!-- END Toggle Sidebar -->
                    </div>
                    <!-- END Right Section -->
                </div>
                <!-- END Header Content -->

                <!-- Header Search -->
                <div id="page-header-search" class="overlay-header">
                    <div class="content-header content-header-fullrow">
                        <form>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <!-- Close Search Section -->
                                    <!-- Layout API, functionality initialized in Template._uiApiLayout() -->
                                    <button type="button" class="btn btn-secondary px-15" data-toggle="layout" data-action="header_search_off">
                                        <i class="fa fa-times"></i>
                                    </button>
                                    <!-- END Close Search Section -->
                                </div>
                                <input type="text" class="form-control" placeholder="Search or hit ESC.." id="page-header-search-input" name="page-header-search-input">
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-secondary px-15">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- END Header Search -->

                <!-- Header Loader -->
                <div id="page-header-loader" class="overlay-header bg-primary">
                    <div class="content-header content-header-fullrow text-center">
                        <div class="content-header-item">
                            <i class="fa fa-sun-o fa-spin text-white"></i>
                        </div>
                    </div>
                </div>
                <!-- END Header Loader -->
            </header>
            <!-- END Header -->

            <!-- Main Container -->
            <main id="main-container">

                <!-- Header -->
                <div class="bg-primary">
                    <div class="content content-top">
                        <div class="row push">
                            <div class="col-md py-10 d-md-flex align-items-md-center text-center">
                                <h1 class="text-white mb-0">
                                    <span class="font-w300">Dashboard</span>
                                    <span class="font-w400 font-size-lg text-white-op d-none d-md-inline-block">Welcome Admin</span>
                                </h1>
                            </div>
                            <div class="col-md py-10 d-md-flex align-items-md-center justify-content-md-end text-center">
                                <button type="button" class="btn btn-alt-primary">
                                    <i class="fa fa-user-plus mr-5"></i> New Account
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- END Header -->

                <!-- Page Content -->
                <div class="bg-white">
                    <!-- Breadcrumb -->
                    <div class="content">
                        <nav class="breadcrumb mb-0">
                            <a class="breadcrumb-item" href="javascript:void(0)">Home</a>
                            <span class="breadcrumb-item active">Dashboard</span>
                        </nav>
                    </div>
                    <!-- END Breadcrumb -->

                    <!-- Content -->
                    <div class="content">
                        <!-- Icon Navigation -->
                        <div class="row gutters-tiny push">
                            <div class="col-6 col-md-4 col-xl-2">
                                <a class="block block-rounded block-bordered block-link-shadow text-center" href="javascript:void(0)">
                                    <div class="block-content">
                                        <p class="mt-5">
                                            <i class="si si-home fa-3x text-muted"></i>
                                        </p>
                                        <p class="font-w600">Home</p>
                                    </div>
                                </a>
                            </div>
                            <div class="col-6 col-md-4 col-xl-2">
                                <a class="block block-rounded block-bordered block-link-shadow ribbon ribbon-primary text-center" href="javascript:void(0)">
                                    <div class="ribbon-box">5</div>
                                    <div class="block-content">
                                        <p class="mt-5">
                                            <i class="si si-envelope fa-3x text-muted"></i>
                                        </p>
                                        <p class="font-w600">Inbox</p>
                                    </div>
                                </a>
                            </div>
                            <div class="col-6 col-md-4 col-xl-2">
                                <a class="block block-rounded block-bordered block-link-shadow text-center" href="javascript:void(0)">
                                    <div class="block-content">
                                        <p class="mt-5">
                                            <i class="si si-bag fa-3x text-muted"></i>
                                        </p>
                                        <p class="font-w600">Cart</p>
                                    </div>
                                </a>
                            </div>
                            <div class="col-6 col-md-4 col-xl-2">
                                <a class="block block-rounded block-bordered block-link-shadow text-center" href="javascript:void(0)">
                                    <div class="block-content">
                                        <p class="mt-5">
                                            <i class="si si-bar-chart fa-3x text-muted"></i>
                                        </p>
                                        <p class="font-w600">Statistics</p>
                                    </div>
                                </a>
                            </div>
                            <div class="col-6 col-md-4 col-xl-2">
                                <a class="block block-rounded block-bordered block-link-shadow ribbon ribbon-primary text-center" href="javascript:void(0)">
                                    <div class="ribbon-box">3</div>
                                    <div class="block-content">
                                        <p class="mt-5">
                                            <i class="si si-cloud-download fa-3x text-muted"></i>
                                        </p>
                                        <p class="font-w600">Downloads</p>
                                    </div>
                                </a>
                            </div>
                            <div class="col-6 col-md-4 col-xl-2">
                                <a class="block block-rounded block-bordered block-link-shadow text-center" href="javascript:void(0)">
                                    <div class="block-content">
                                        <p class="mt-5">
                                            <i class="si si-docs fa-3x text-muted"></i>
                                        </p>
                                        <p class="font-w600">Documents</p>
                                    </div>
                                </a>
                            </div>
                        </div>
                        <!-- END Icon Navigation -->

                        <!-- Mini Stats -->
                        <div class="row">
                            <div class="col-md-6 col-xl-3">
                                <a class="block block-rounded block-bordered" href="javascript:void(0)">
                                    <div class="block-content p-5">
                                        <div class="py-30 text-center bg-body-light rounded">
                                            <div class="font-size-h2 font-w700 mb-0 text-muted">78</div>
                                            <div class="font-size-sm font-w600 text-uppercase">Sales</div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-6 col-xl-3">
                                <a class="block block-rounded block-bordered" href="javascript:void(0)">
                                    <div class="block-content p-5">
                                        <div class="py-30 text-center bg-body-light rounded">
                                            <div class="font-size-h2 font-w700 mb-0 text-muted">$880</div>
                                            <div class="font-size-sm font-w600 text-uppercase">Earnings</div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-6 col-xl-3">
                                <a class="block block-rounded block-bordered" href="javascript:void(0)">
                                    <div class="block-content p-5">
                                        <div class="py-30 text-center bg-body-light rounded">
                                            <div class="font-size-h2 font-w700 mb-0 text-muted">$4,500</div>
                                            <div class="font-size-sm font-w600 text-uppercase">Total</div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-6 col-xl-3">
                                <a class="block block-rounded block-bordered" href="javascript:void(0)">
                                    <div class="block-content p-5">
                                        <div class="py-30 text-center bg-body-light rounded">
                                            <div class="font-size-h2 font-w700 mb-0 text-muted">$19,700</div>
                                            <div class="font-size-sm font-w600 text-uppercase">Estimated</div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                        <!-- END Mini Stats -->

                        <!-- Charts -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="block block-rounded block-bordered">
                                    <div class="block-header">
                                        <h3 class="block-title text-uppercase">Sales</h3>
                                        <div class="block-options">
                                            <button type="button" class="btn-block-option" data-toggle="block-option" data-action="state_toggle" data-action-mode="demo">
                                                <i class="si si-refresh"></i>
                                            </button>
                                            <button type="button" class="btn-block-option">
                                                <i class="si si-wrench"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="block-content p-5">
                                        <!-- Lines Chart Container functionality is initialized in js/pages/db_corporate.min.js which was auto compiled from _es6/pages/db_corporate.js -->
                                        <!-- For more info and examples you can check out http://www.chartjs.org/docs/ -->
                                        <canvas class="js-chartjs-corporate-lines"></canvas>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="block block-rounded block-bordered">
                                    <div class="block-header">
                                        <h3 class="block-title text-uppercase">Earnings</h3>
                                        <div class="block-options">
                                            <button type="button" class="btn-block-option" data-toggle="block-option" data-action="state_toggle" data-action-mode="demo">
                                                <i class="si si-refresh"></i>
                                            </button>
                                            <button type="button" class="btn-block-option">
                                                <i class="si si-wrench"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="block-content p-5">
                                        <!-- Lines Chart Container functionality is initialized in js/pages/db_corporate.min.js which was auto compiled from _es6/pages/db_corporate.js -->
                                        <!-- For more info and examples you can check out http://www.chartjs.org/docs/ -->
                                        <canvas class="js-chartjs-corporate-lines2"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- END Charts -->

                        <!-- More Data -->
                        <div class="row">
                            <!-- Latest Orders -->
                            <div class="col-xl-6">
                                <div class="block block-rounded block-bordered">
                                    <div class="block-header">
                                        <h3 class="block-title text-uppercase">Latest Orders</h3>
                                        <div class="block-options">
                                            <button type="button" class="btn-block-option" data-toggle="block-option" data-action="state_toggle" data-action-mode="demo">
                                                <i class="si si-refresh"></i>
                                            </button>
                                            <button type="button" class="btn-block-option">
                                                <i class="si si-wrench"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="block-content p-5">
                                        <table class="table table-borderless table-striped mb-0">
                                            <thead>
                                                <tr>
                                                    <th style="width: 100px;">ID</th>
                                                    <th>Status</th>
                                                    <th class="d-none d-sm-table-cell">Customer</th>
                                                    <th class="text-right">Value</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <a class="font-w600" href="javascript:void(0)">ORD.1851</a>
                                                    </td>
                                                    <td>
                                                        <span class="badge badge-danger">Canceled</span>
                                                    </td>
                                                    <td class="d-none d-sm-table-cell">
                                                        <a href="javascript:void(0)">Jose Parker</a>
                                                    </td>
                                                    <td class="text-right">
                                                        <span class="text-black">$203</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <a class="font-w600" href="javascript:void(0)">ORD.1850</a>
                                                    </td>
                                                    <td>
                                                        <span class="badge badge-warning">Pending</span>
                                                    </td>
                                                    <td class="d-none d-sm-table-cell">
                                                        <a href="javascript:void(0)">Jose Parker</a>
                                                    </td>
                                                    <td class="text-right">
                                                        <span class="text-black">$842</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <a class="font-w600" href="javascript:void(0)">ORD.1849</a>
                                                    </td>
                                                    <td>
                                                        <span class="badge badge-danger">Canceled</span>
                                                    </td>
                                                    <td class="d-none d-sm-table-cell">
                                                        <a href="javascript:void(0)">Betty Kelley</a>
                                                    </td>
                                                    <td class="text-right">
                                                        <span class="text-black">$151</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <a class="font-w600" href="javascript:void(0)">ORD.1848</a>
                                                    </td>
                                                    <td>
                                                        <span class="badge badge-success">Completed</span>
                                                    </td>
                                                    <td class="d-none d-sm-table-cell">
                                                        <a href="javascript:void(0)">Barbara Scott</a>
                                                    </td>
                                                    <td class="text-right">
                                                        <span class="text-black">$688</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <a class="font-w600" href="javascript:void(0)">ORD.1847</a>
                                                    </td>
                                                    <td>
                                                        <span class="badge badge-danger">Canceled</span>
                                                    </td>
                                                    <td class="d-none d-sm-table-cell">
                                                        <a href="javascript:void(0)">Susan Day</a>
                                                    </td>
                                                    <td class="text-right">
                                                        <span class="text-black">$963</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <a class="font-w600" href="javascript:void(0)">ORD.1846</a>
                                                    </td>
                                                    <td>
                                                        <span class="badge badge-success">Completed</span>
                                                    </td>
                                                    <td class="d-none d-sm-table-cell">
                                                        <a href="javascript:void(0)">Susan Day</a>
                                                    </td>
                                                    <td class="text-right">
                                                        <span class="text-black">$836</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <a class="font-w600" href="javascript:void(0)">ORD.1845</a>
                                                    </td>
                                                    <td>
                                                        <span class="badge badge-info">Processing</span>
                                                    </td>
                                                    <td class="d-none d-sm-table-cell">
                                                        <a href="javascript:void(0)">Alice Moore</a>
                                                    </td>
                                                    <td class="text-right">
                                                        <span class="text-black">$616</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <a class="font-w600" href="javascript:void(0)">ORD.1844</a>
                                                    </td>
                                                    <td>
                                                        <span class="badge badge-warning">Pending</span>
                                                    </td>
                                                    <td class="d-none d-sm-table-cell">
                                                        <a href="javascript:void(0)">Sara Fields</a>
                                                    </td>
                                                    <td class="text-right">
                                                        <span class="text-black">$162</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <a class="font-w600" href="javascript:void(0)">ORD.1843</a>
                                                    </td>
                                                    <td>
                                                        <span class="badge badge-info">Processing</span>
                                                    </td>
                                                    <td class="d-none d-sm-table-cell">
                                                        <a href="javascript:void(0)">Betty Kelley</a>
                                                    </td>
                                                    <td class="text-right">
                                                        <span class="text-black">$683</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <a class="font-w600" href="javascript:void(0)">ORD.1842</a>
                                                    </td>
                                                    <td>
                                                        <span class="badge badge-info">Processing</span>
                                                    </td>
                                                    <td class="d-none d-sm-table-cell">
                                                        <a href="javascript:void(0)">Megan Fuller</a>
                                                    </td>
                                                    <td class="text-right">
                                                        <span class="text-black">$240</span>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <!-- END Latest Orders -->

                            <!-- Top Products -->
                            <div class="col-xl-6">
                                <div class="block block-rounded block-bordered">
                                    <div class="block-header">
                                        <h3 class="block-title text-uppercase">Top Products</h3>
                                        <div class="block-options">
                                            <button type="button" class="btn-block-option" data-toggle="block-option" data-action="state_toggle" data-action-mode="demo">
                                                <i class="si si-refresh"></i>
                                            </button>
                                            <button type="button" class="btn-block-option">
                                                <i class="si si-wrench"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="block-content p-5">
                                        <table class="table table-borderless table-striped mb-0">
                                            <thead>
                                                <tr>
                                                    <th class="d-none d-sm-table-cell" style="width: 100px;">ID</th>
                                                    <th>Product</th>
                                                    <th class="text-center">Orders</th>
                                                    <th class="d-none d-sm-table-cell text-center">Rating</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td class="d-none d-sm-table-cell">
                                                        <a class="font-w600" href="javascript:void(0)">PID.258</a>
                                                    </td>
                                                    <td>
                                                        <a href="javascript:void(0)">Dark Souls III</a>
                                                    </td>
                                                    <td class="text-center">
                                                        <a class="text-gray-dark" href="javascript:void(0)">912</a>
                                                    </td>
                                                    <td class="d-none d-sm-table-cell text-center">
                                                        <div class="text-warning">
                                                            <i class="fa fa-star"></i>
                                                            <i class="fa fa-star"></i>
                                                            <i class="fa fa-star"></i>
                                                            <i class="fa fa-star"></i>
                                                            <i class="fa fa-star"></i>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="d-none d-sm-table-cell">
                                                        <a class="font-w600" href="javascript:void(0)">PID.198</a>
                                                    </td>
                                                    <td>
                                                        <a href="javascript:void(0)">Bioshock Collection</a>
                                                    </td>
                                                    <td class="text-center">
                                                        <a class="text-gray-dark" href="javascript:void(0)">895</a>
                                                    </td>
                                                    <td class="d-none d-sm-table-cell text-center">
                                                        <div class="text-warning">
                                                            <i class="fa fa-star"></i>
                                                            <i class="fa fa-star"></i>
                                                            <i class="fa fa-star"></i>
                                                            <i class="fa fa-star"></i>
                                                            <i class="fa fa-star"></i>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="d-none d-sm-table-cell">
                                                        <a class="font-w600" href="javascript:void(0)">PID.852</a>
                                                    </td>
                                                    <td>
                                                        <a href="javascript:void(0)">Alien Isolation</a>
                                                    </td>
                                                    <td class="text-center">
                                                        <a class="text-gray-dark" href="javascript:void(0)">820</a>
                                                    </td>
                                                    <td class="d-none d-sm-table-cell text-center">
                                                        <div class="text-warning">
                                                            <i class="fa fa-star"></i>
                                                            <i class="fa fa-star"></i>
                                                            <i class="fa fa-star"></i>
                                                            <i class="fa fa-star"></i>
                                                            <i class="fa fa-star"></i>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="d-none d-sm-table-cell">
                                                        <a class="font-w600" href="javascript:void(0)">PID.741</a>
                                                    </td>
                                                    <td>
                                                        <a href="javascript:void(0)">Bloodborne</a>
                                                    </td>
                                                    <td class="text-center">
                                                        <a class="text-gray-dark" href="javascript:void(0)">793</a>
                                                    </td>
                                                    <td class="d-none d-sm-table-cell text-center">
                                                        <div class="text-warning">
                                                            <i class="fa fa-star"></i>
                                                            <i class="fa fa-star"></i>
                                                            <i class="fa fa-star"></i>
                                                            <i class="fa fa-star"></i>
                                                            <i class="fa fa-star"></i>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="d-none d-sm-table-cell">
                                                        <a class="font-w600" href="javascript:void(0)">PID.985</a>
                                                    </td>
                                                    <td>
                                                        <a href="javascript:void(0)">Forza Motorsport 7</a>
                                                    </td>
                                                    <td class="text-center">
                                                        <a class="text-gray-dark" href="javascript:void(0)">782</a>
                                                    </td>
                                                    <td class="d-none d-sm-table-cell text-center">
                                                        <div class="text-warning">
                                                            <i class="fa fa-star"></i>
                                                            <i class="fa fa-star"></i>
                                                            <i class="fa fa-star"></i>
                                                            <i class="fa fa-star"></i>
                                                            <i class="fa fa-star"></i>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="d-none d-sm-table-cell">
                                                        <a class="font-w600" href="javascript:void(0)">PID.056</a>
                                                    </td>
                                                    <td>
                                                        <a href="javascript:void(0)">Fifa 18</a>
                                                    </td>
                                                    <td class="text-center">
                                                        <a class="text-gray-dark" href="javascript:void(0)">776</a>
                                                    </td>
                                                    <td class="d-none d-sm-table-cell text-center">
                                                        <div class="text-warning">
                                                            <i class="fa fa-star"></i>
                                                            <i class="fa fa-star"></i>
                                                            <i class="fa fa-star"></i>
                                                            <i class="fa fa-star"></i>
                                                            <i class="fa fa-star"></i>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="d-none d-sm-table-cell">
                                                        <a class="font-w600" href="javascript:void(0)">PID.036</a>
                                                    </td>
                                                    <td>
                                                        <a href="javascript:void(0)">Gears of War 4</a>
                                                    </td>
                                                    <td class="text-center">
                                                        <a class="text-gray-dark" href="javascript:void(0)">680</a>
                                                    </td>
                                                    <td class="d-none d-sm-table-cell text-center">
                                                        <div class="text-warning">
                                                            <i class="fa fa-star"></i>
                                                            <i class="fa fa-star"></i>
                                                            <i class="fa fa-star"></i>
                                                            <i class="fa fa-star"></i>
                                                            <i class="fa fa-star"></i>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="d-none d-sm-table-cell">
                                                        <a class="font-w600" href="javascript:void(0)">PID.682</a>
                                                    </td>
                                                    <td>
                                                        <a href="javascript:void(0)">Minecraft</a>
                                                    </td>
                                                    <td class="text-center">
                                                        <a class="text-gray-dark" href="javascript:void(0)">670</a>
                                                    </td>
                                                    <td class="d-none d-sm-table-cell text-center">
                                                        <div class="text-warning">
                                                            <i class="fa fa-star"></i>
                                                            <i class="fa fa-star"></i>
                                                            <i class="fa fa-star"></i>
                                                            <i class="fa fa-star"></i>
                                                            <i class="fa fa-star"></i>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="d-none d-sm-table-cell">
                                                        <a class="font-w600" href="javascript:void(0)">PID.478</a>
                                                    </td>
                                                    <td>
                                                        <a href="javascript:void(0)">Dishonored 2</a>
                                                    </td>
                                                    <td class="text-center">
                                                        <a class="text-gray-dark" href="javascript:void(0)">640</a>
                                                    </td>
                                                    <td class="d-none d-sm-table-cell text-center">
                                                        <div class="text-warning">
                                                            <i class="fa fa-star"></i>
                                                            <i class="fa fa-star"></i>
                                                            <i class="fa fa-star"></i>
                                                            <i class="fa fa-star"></i>
                                                            <i class="fa fa-star"></i>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="d-none d-sm-table-cell">
                                                        <a class="font-w600" href="javascript:void(0)">PID.952</a>
                                                    </td>
                                                    <td>
                                                        <a href="javascript:void(0)">Gran Turismo Sport</a>
                                                    </td>
                                                    <td class="text-center">
                                                        <a class="text-gray-dark" href="javascript:void(0)">630</a>
                                                    </td>
                                                    <td class="d-none d-sm-table-cell text-center">
                                                        <div class="text-warning">
                                                            <i class="fa fa-star"></i>
                                                            <i class="fa fa-star"></i>
                                                            <i class="fa fa-star"></i>
                                                            <i class="fa fa-star"></i>
                                                            <i class="fa fa-star"></i>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <!-- END Top Products -->
                        </div>
                        <!-- END More Data -->
                    </div>
                    <!-- END Content -->
                </div>
                <!-- END Page Content -->

            </main>
            <!-- END Main Container -->

            <!-- Footer -->
            <footer id="page-footer" class="bg-white opacity-0">
                <div class="content py-20 font-size-xs clearfix">
                    <div class="float-right">
                        Crafted with <i class="fa fa-heart text-pulse"></i> by <a class="font-w600" href="https://1.envato.market/ydb" target="_blank">pixelcave</a>
                    </div>
                    <div class="float-left">
                        <a class="font-w600" href="https://1.envato.market/95j" target="_blank">Codebase 3.0</a> &copy; <span class="js-year-copy">2017</span>
                    </div>
                </div>
            </footer>
            <!-- END Footer -->
        </div>
        <!-- END Page Container -->

        <!--
            Codebase JS Core

            Vital libraries and plugins used in all pages. You can choose to not include this file if you would like
            to handle those dependencies through webpack. Please check out assets/_es6/main/bootstrap.js for more info.

            If you like, you could also include them separately directly from the assets/js/core folder in the following
            order. That can come in handy if you would like to include a few of them (eg jQuery) from a CDN.

            assets/js/core/jquery.min.js
            assets/js/core/bootstrap.bundle.min.js
            assets/js/core/simplebar.min.js
            assets/js/core/jquery-scrollLock.min.js
            assets/js/core/jquery.appear.min.js
            assets/js/core/jquery.countTo.min.js
            assets/js/core/js.cookie.min.js
        -->
        <script src="assets/js/codebase.core.min.js"></script>

        <!--
            Codebase JS

            Custom functionality including Blocks/Layout API as well as other vital and optional helpers
            webpack is putting everything together at assets/_es6/main/app.js
        -->
        <script src="assets/js/codebase.app.min.js"></script>

        <!-- Page JS Plugins -->
        <script src="assets/js/plugins/chartjs/Chart.bundle.min.js"></script>

        <!-- Page JS Code -->
        <script src="assets/js/pages/db_corporate.min.js"></script>

    </body>
</html>