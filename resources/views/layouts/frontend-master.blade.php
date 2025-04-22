
<!doctype html>
<html lang="en">

    <head>
        
        <meta charset="utf-8" />
        <title>@yield('title') - {{ config('app.name') }}</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta content="Hello there" name="description" />
        <meta content="Snigdho" name="author" />
        <!-- App favicon -->
        <link rel="shortcut icon" href="{{ asset('/assets/images/favicon.ico') }}">

        <link href="{{ asset('/assets/libs/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />

        <!-- owl.carousel css -->
        <link rel="stylesheet" href="{{ asset('/assets/libs/owl.carousel/assets/owl.carousel.min.css') }}">

        <link rel="stylesheet" href="{{ asset('/assets/libs/owl.carousel/assets/owl.theme.default.min.css') }}">

        <!-- Icons Css -->
        <link href="{{ asset('/assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
        
        <!-- DataTables -->
        <link href="{{ asset('/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />

        <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('/assets/css/app.min.css') }}" rel="stylesheet" type="text/css" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/noty/3.1.4/noty.css" />

        <style type="text/css">
            .notification-msg {
                color: rgb(195, 203, 228); 
                font-weight: 450;
            }
            .notification-time {
                color: #fff;
            }

            .mfp-title {
                display: none !important;
            }
        </style>
            
        <style type="text/css">
            .navigation.nav-sticky {
                background-color: #fff !important;
            }

            .mfp-img {
                max-height: 451px !important;
            }

            .mfp-title a {
                display: none !important;
            }
        </style>

        <!-- Lightbox css -->
        <link href="{{ asset('/assets/libs/magnific-popup/magnific-popup.css') }}" rel="stylesheet" type="text/css" />

        @if(request()->routeIs('frontend.bg*'))
            <style type="text/css">
                .nav-link {
                    color: #222 !important;
                }
            </style>
        @endif

        @yield('styles')

    </head>

    <body data-bs-spy="scroll" data-bs-target="#topnav-menu" data-bs-offset="60">

        <nav class="navbar navbar-expand-lg navigation fixed-top sticky" @if(request()->routeIs('frontend.bg*')) style="background: #fff;" @endif>
            <div class="container">
                <a class="navbar-logo" href="{{ url('/') }}">
                    <img src="{{ config('core.image.default.logo2d') }}" style="margin-top: 5px; margin-bottom: 5px;" class="editPro" alt="" height="70">
                </a>

                <button type="button" class="btn btn-sm px-3 font-size-16 d-lg-none header-item waves-effect waves-light" data-bs-toggle="collapse" data-bs-target="#topnav-menu-content">
                    <i class="fa fa-fw fa-bars"></i>
                </button>
              
                <div class="collapse navbar-collapse" id="topnav-menu-content">
                    <ul class="navbar-nav ms-auto" id="topnav-menu">

                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('/') }}">Home</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link @if(url()->full() == route('frontend.bg.rankings')) active @endif" @if(url()->full() == route('frontend.bg.rankings')) style="color: #556ee6 !important;" @endif href="{{ route('frontend.bg.rankings') }}">Rankings</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" @if(request()->routeIs('frontend.bg*')) href="{{ url('/#tournamentsf') }}" @else href="#tournamentsf" @endif>Tournaments</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" @if(request()->routeIs('frontend.bg*')) href="{{ url('/#leaguesf') }}" @else href="#leaguesf" @endif>Leagues</a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link @if(url()->full() == route('frontend.bg.faq')) active @endif" @if(url()->full() == route('frontend.bg.faq')) style="color: #556ee6 !important;" @endif href="{{ route('frontend.bg.faq') }}">FAQs</a>
                        </li>

                    </ul>

                    <div class="my-2 ms-lg-2">
                        {{-- @if(Auth::user())
                            <a href="{{ route('dashboard') }}" class="btn btn-dark w-xs">Go To Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-success w-xs">Sign In</a>
                        @endif --}}
                    </div>
                </div>
            </div>
        </nav>

        @yield('content')

        <!-- Footer start -->
        <footer class="landing-footer">
            <div class="container">

                <div class="row">
                    
                    <div class="col-lg-4 col-sm-6">
                        <div class="mb-4 mb-lg-0">
                            <h5 class="mb-3 footer-list-title"><a href="{{ route('frontend.bg.rankings') }}" style="color: #fff !important;">Our Rankings</a></h5>
                        </div>
                    </div>

                    <div class="col-lg-4 col-sm-6">
                        <div class="mb-4 mb-lg-0">
                            <h5 class="mb-3 footer-list-title">Contact: <a href="mailto:{{ \App\Models\User::where('role', 'Administrator')->first()->email }}"><span class="font-size-14">{{ \App\Models\User::where('role', 'Administrator')->first()->email }}</span></a></h5>

                        </div>
                    </div>

                    <div class="col-lg-4 col-sm-6">
                        
                        <p class="mb-2"><script>document.write(new Date().getFullYear())</script> © {{ config('app.name') }}. Developed by Team Tennis4all</p>

                    </div>

                    
                </div>
                
            </div>
            <!-- end container -->
        </footer>
        <!-- Footer end -->

        <!-- JAVASCRIPT -->
        <script src="{{ asset('/assets/libs/jquery/jquery.min.js') }}"></script>
        <script src="{{ asset('/assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
        <script src="{{ asset('/assets/libs/metismenu/metisMenu.min.js') }}"></script>
        <script src="{{ asset('/assets/libs/simplebar/simplebar.min.js') }}"></script>
        <script src="{{ asset('/assets/libs/node-waves/waves.min.js') }}"></script>

        <script src="{{ asset('/assets/libs/jquery.easing/jquery.easing.min.js') }}"></script>

        <!-- Plugins js-->
        <script src="{{ asset('/assets/libs/jquery-countdown/jquery.countdown.min.js') }}"></script>

        <!-- owl.carousel js -->
        <script src="{{ asset('/assets/libs/owl.carousel/owl.carousel.min.js') }}"></script>

        <!-- ICO landing init -->
        <script src="{{ asset('/assets/js/pages/ico-landing.init.js') }}"></script>

        <script src="{{ asset('/assets/js/app.js') }}"></script>
        <script src="{{ asset('/assets/js/pages/form-validation.init.js') }}"></script>

        <!-- Magnific Popup-->
        <script src="{{ asset('/assets/libs/magnific-popup/jquery.magnific-popup.min.js') }}"></script>
        
        <!-- lightbox init js-->
        <script src="{{ asset('/assets/js/pages/lightbox.init.js') }}"></script>

        <script src="{{ asset('/assets/libs/select2/js/select2.min.js') }}"></script>

        <!-- Required datatable js -->
        <script src="{{ asset('/assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('/assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>

        <!-- Responsive examples -->
        <script src="{{ asset('/assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
        <script src="{{ asset('/assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js') }}"></script>

        <!-- Datatable init js -->
        <script src="{{ asset('/assets/js/pages/datatables.init.js') }}"></script>

        <!-- Buttons examples -->
        <script src="{{ asset('/assets/libs/datatables.net-buttons/js/dataTables.buttons.min.js') }}"></script>
        <script src="{{ asset('/assets/libs/datatables.net-buttons-bs4/js/buttons.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('/assets/libs/jszip/jszip.min.js') }}"></script>
        <script src="{{ asset('/assets/libs/pdfmake/build/pdfmake.min.js') }}"></script>
        <script src="{{ asset('/assets/libs/pdfmake/build/vfs_fonts.js') }}"></script>
        <script src="{{ asset('/assets/libs/datatables.net-buttons/js/buttons.html5.min.js') }}"></script>
        <script src="{{ asset('/assets/libs/datatables.net-buttons/js/buttons.print.min.js') }}"></script>
        <script src="{{ asset('/assets/libs/datatables.net-buttons/js/buttons.colVis.min.js') }}"></script>

        <!-- TOASTER -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/noty/3.1.4/noty.js"></script>

        <script>       
            @if(Session::has('success')) new Noty({ 
                    type:'success', 
                    layout:'topRight', 
                    text: '{{ Session::get('success') }}', 
                    timeout: 5000
                }).show(); 
            @endif

            @if(Session::has('info')) new Noty({ 
                    type:'info', 
                    layout:'topRight', 
                    text: '{{ Session::get('info') }}', 
                    timeout: 5000
                }).show(); 
            @endif

            @if(Session::has('error')) new Noty({ 
                    type:'error', 
                    layout:'topRight', 
                    text: '{{ Session::get('error') }}', 
                    timeout: 5000
                }).show(); 
            @endif

            @if(Session::has('im-val-error')) new Noty({ 
                    type:'error', 
                    layout:'topRight', 
                    text: '{{ Session::get('im-val-error') }}', 
                    timeout: 5000
                }).show(); 
            @endif

            @if(Session::has('warning')) new Noty({ 
                    type:'warning', 
                    layout:'topRight', 
                    text: '{{ Session::get('warning') }}', 
                    timeout: 5000
                }).show(); 
            @endif
        </script>

        @yield('scripts')

    </body>

</html>
