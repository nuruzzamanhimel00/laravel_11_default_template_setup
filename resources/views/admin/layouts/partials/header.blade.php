<header id="page-topbar">
    <div class="navbar-header">
        <div class="d-flex align-items-center">
            <!-- LOGO -->
            <div class="navbar-brand-box">
                <a href="{{ route('dashboard') }}" class="logo logo-dark">
                                <span class="logo-sm">
                                    <img src="{{ getStorageImage(config('settings.site_logo'),false,'logo') }}" alt="" height="22">
                                </span>
                    <span class="logo-lg">
                                    <img src="{{ getStorageImage(config('settings.wide_site_logo'),false,'wide_logo') }}" alt="">
                                </span>
                </a>

                <a href="{{ route('dashboard') }}" class="logo logo-light">
                                <span class="logo-sm">
                                    <img src="{{ getStorageImage(config('settings.site_logo'),false,'logo') }}" alt="" height="22">
                                </span>
                    <span class="logo-lg">
                                    <img src="{{ getStorageImage(config('settings.wide_site_logo'),false,'wide_logo') }}" class="w-100" alt="" height="60">
                                </span>
                </a>
            </div>

            <button type="button" class="btn btn-sm px-3 font-size-24 header-item waves-effect"
                    id="vertical-menu-btn">
               <img src="{{asset('/images/default/menu.png')}}" class="ic_bar_icon" alt="menu">
            </button>
        </div>

        <div class="d-flex align-items-center gap-3">
            <!-- App Search-->
            <!-- <div class="dropdown d-inline-block d-lg-none ms-2">
                <button type="button" class="btn header-item noti-icon waves-effect"
                        id="page-header-search-dropdown"
                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="mdi mdi-magnify"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0"
                     aria-labelledby="page-header-search-dropdown">

                    <form class="p-3">
                        <div class="form-group m-0">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Search ..."
                                       aria-label="Recipient's username">
                                <div class="input-group-append">
                                    <button class="btn btn-success" type="submit"><i class="mdi mdi-magnify"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div> -->

            <div class="dropdown d-none d-lg-inline-block">
                <button type="button" class="btn header-item noti-icon" data-bs-toggle="fullscreen">
                    <i class="mdi mdi-fullscreen"></i>
                </button>
            </div>

            {{-- <div class="dropdown d-inline-block">
                <button type="button" class="btn header-item noti-icon"
                        id="page-header-notifications-dropdown"
                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="mdi mdi-bell-outline"></i>
                    <span class="badge bg-danger py-1 px-2 rounded-pill">3</span>
                </button>

             </div> --}}
             {{-- @include('admin.layouts.partials.low_stock_alert_notifications')
             @include('admin.layouts.partials.notifications') --}}

            <div class="dropdown d-inline-block">
                <button type="button" class="btn header-item ic-user-profile d-flex align-items-center text-start gap-2" id="page-header-user-dropdown"
                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <img class="rounded header-profile-user" src="{{ auth()->user()->avatar_url }}"
                         alt="Header Avatar">
                         <div>
                            <p class="mb-0 fs-6"> {{auth()->user()->full_name}}</p>
                            <!-- <span class="fs-6 text-gray">user@gmail.com</span> -->
                         </div>
                        <i class="fas fa-angle-down"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-end">
                    <!-- item-->
                    <a class="dropdown-item" href="{{ route('users.edit', auth()->id()) }}"><i
                            class="mdi mdi-account-circle font-size-17 align-middle me-1"></i> Profile</a>
                    <div class="dropdown-divider"></div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <a onclick="event.preventDefault();
                                        this.closest('form').submit();"
                           class="dropdown-item text-danger"
                           href="javascript:void(0)">
                            <i class="bx bx-power-off font-size-17 align-middle me-1 text-danger"></i>
                            Logout
                        </a>
                    </form>
                </div>
            </div>

        </div>
    </div>
</header>
