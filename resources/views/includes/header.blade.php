<header id="page-topbar">
    <div class="navbar-header">
        <div class="d-flex">
            <!-- LOGO -->
            <div class="navbar-brand-box">
                <a href="@if(Auth::user()->hasRole('Administrator')) {{ route('admin.dashboard') }} @elseif(Auth::user()->hasRole('User')) {{ route('user.dashboard') }} @endif" class="logo logo-dark">
                    <span class="logo-sm">
                        <img src="{{ config('core.image.default.logo2d') }}" alt="" height="30" width="30">
                    </span>
                    <span class="logo-lg">
                        <img src="{{ config('core.image.default.logo2d') }}" class="editPro" style="margin-top: 12px;" alt="" height="80">
                    </span>
                </a>

                <a href="@if(Auth::user()->hasRole('Administrator')) {{ route('admin.dashboard') }} @elseif(Auth::user()->hasRole('User')) {{ route('user.dashboard') }} @endif" class="logo logo-lights">
                    <span class="logo-sm">
                        <img src="{{ config('core.image.default.logo2d') }}" alt="" height="30" width="30">
                    </span>
                    <span class="logo-lg">
                        <img src="{{ config('core.image.default.logo2d') }}" class="editPro" style="margin-top: 12px;" alt="" height="80">
                    </span>
                </a>
            </div>

            <button type="button" class="btn btn-sm px-3 font-size-16 header-item waves-effect" id="vertical-menu-btn">
                <i class="fa fa-fw fa-bars"></i>
            </button>

            
        </div>

        <div class="d-flex">

            <div class="dropdown d-none d-lg-inline-block ms-1">
                <button type="button" class="btn header-item noti-icon waves-effect" data-toggle="fullscreen">
                    <i class="bx bx-fullscreen"></i>
                </button>
            </div>

            <div class="dropdown d-inline-block">

                <button type="button" class="btn header-item waves-effect" id="page-header-user-dropdown"
                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <img class="rounded-circle header-profile-user" src="@if(Auth::user()->profile_photo_path) {{ asset('assets/uploads/users/'.Auth::user()->profile_photo_path) }} @else {{ config('core.image.default.avatar') }} @endif"
                        alt="Header Avatar">
                    <span class="d-none d-xl-inline-block ms-1" key="t-henry">{{ Auth::user()->name }}</span>
                    <i class="mdi mdi-chevron-down d-none d-xl-inline-block"></i>
                </button>

                <div class="dropdown-menu dropdown-menu-end">
                    <!-- item-->
                    <a class="dropdown-item" href="{{ url('/user/profile') }}"><i class="bx bx-user font-size-16 align-middle me-1"></i> <span key="t-profile">Profile</span></a>
                    
                    <div class="dropdown-divider"></div>                    

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-danger dropdown-item">
                            <i class="bx bx-power-off font-size-16 align-middle me-1 text-danger"></i>

                            <span key="t-logout">
                                {{ __('Log out') }}
                            </span>                            
                        </button>
                    </form>
                </div>
            </div>

            <div class="dropdown d-inline-block">
                <button type="button" class="btn header-item noti-icon right-bar-toggle waves-effect">
                    <i class="bx bx-cog bx-spin"></i>
                </button>
            </div>

        </div>
    </div>
</header>