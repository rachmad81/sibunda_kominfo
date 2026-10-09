<header id="page-header">
    <div class="content-header">
        <div class="content-header-section" style="display:flex;">

            <button type="button" class="btn btn-circle btn-dual-secondary d-md-block d-lg-block d-xl-block" data-toggle="layout" data-action="sidebar_toggle">
               <i class="fa fa-navicon"></i>
            </button>
            <!-- <button type="button" class="btn btn-rounded btn-dual-secondary d-md-none d-lg-none d-xl-none" data-toggle="layout" data-action="header_search_on">
                <i class="fa fa-search mr-5"></i> Cari Satuan PAUD
            </button> -->
            @if(session('back'))
            <a href="{{ route('login-as').'?back=1&id='.session('back') }}" class="btn btn-sm btn-warning">
                <i class="fa fa-refresh"></i> Akun Utama
            </a>
            @endif
        </div>

        <div class="content-header-section">
            
            <!-- Header Search -->
            <div id="page-header-search" class="overlay-header" style="color: #FFFFFF; z-index: 9;">
                <div class="content-header content-header-fullrow">
                    <form>
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Nama Sekolah .." id="page-header-search-input" name="page-header-search-input">
                            <div class="input-group-append">
                                <button type="button" class="btn btn-secondary px-15" data-toggle="layout" data-action="header_search_off">
                                    <i class="fa fa-times"></i>
                                </button>
                            </div>
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

            <!-- User Dropdown -->
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-rounded btn-sm btn-outline-secondary" id="page-header-user-dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fa fa-user"></i>
                    <span class="d-none d-md-inline-block">{{ \Auth::User()->getNamaProfil() }}</span>
                    <i class="fa fa-angle-down ml-5"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-right min-width-200" aria-labelledby="page-header-user-dropdown">
                    <h5 class="h6 text-center py-10 mb-5 border-b text-uppercase">{!! \Auth::User()->getTingkat() !!}</h5>
                    <!-- Layout API, functionality initialized in Template._uiApiLayout() -->
                    <a class="dropdown-item" href="javascript:void(0)" data-toggle="layout" data-action="side_overlay_toggle" id="overlay_trigger">
                        <i class="si si-wrench mr-5"></i> Setting
                    </a>
                    <!-- END Side Overlay -->

                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="{{ Route('logout') }}">
                        <i class="si si-logout mr-5"></i> Sign Out
                    </a>
                </div>
            </div>
            <!-- END User Dropdown -->


        </div>
    </div>

</header>
