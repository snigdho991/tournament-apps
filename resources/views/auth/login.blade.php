<!doctype html>

<html lang="en">

    
    <head>
        
        <meta charset="utf-8" />
        <title>Login - {{ config('app.name') }}</title>
        
        @include('libs.meta-tags')
        
        @include('libs.styles')

        <style type="text/css">

            .auth-full-bg .bg-overlay {
                background: url({{ asset('assets/uploads/home/auth-pic.jpg') }});
                background-size: cover;
                background-repeat: no-repeat;
                background-position: center;
            }


        </style>

        <style type="text/css">
            @media screen and (min-width:220px) and (max-width:1199px) {
                #mobile {
                    display: none !important;
                }
            }
        </style>

    </head>

    <body class="auth-body-bg">
        
        <div>
            <div class="container-fluid p-0">
                <div class="row g-0">
                    
                    <div class="col-xl-8" id="mobile">
                        <div class="auth-full-bg pt-lg-5 p-4">
                            <div class="w-100">
                                <div class="bg-overlay"></div>
                                <div class="d-flex h-50 flex-column">
    
                                    <div class="p-4 mt-auto">
                                        <div class="row justify-content-center">
                                            <div class="col-lg-7">
                                                <div class="text-center">
                                                    
                                                    {{-- <h4 class="mb-3"><i class="bx bxs-quote-alt-left text-primary h1 align-middle me-3"></i><span class="text-primary">{{ \App\Models\User::where('role', 'Player')->count() }}</span> <span style="color: #000;">Registered Players</span></h4> --}}
                                                    
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- end col -->

                    <div class="col-xl-4">
                        <div class="auth-full-page-content p-md-5 p-4">
                            <div class="w-100">

                                <div class="d-flex flex-column h-100">
                                    <div style="margin-bottom: 2rem !important;">
                                        <a href="{{ url('/') }}" class="d-block auth-logo">
                                            <img src="{{ config('core.image.default.logo2d') }}" alt="" height="200" style="margin: 0 auto;" class="auth-logo-dark">
                                        </a>
                                    </div>
                                    <div class="my-auto">
                                        
                                        <div>
                                            <h5 class="text-primary">Welcome Back !</h5>
                                            <p class="text-muted">Sign in to continue to {{ config('app.name') }}</p>
                                        </div>
            
                                        <div class="mt-4">

                                            @if(count($errors) > 0)
                                                <div class="alert alert-dismissible fade show color-box bg-danger bg-gradient p-4" role="alert">
                                                    <x-jet-validation-errors class="mb-4 my-2 text-white" />
                                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                                </div>
                                            @endif

                                            @if (session('status'))
                                                <div class="alert alert-dismissible fade show color-box bg-success bg-gradient p-4" role="alert">
                                                    <div class="mb-4 my-2 text-white">
                                                        {{ session('status') }}
                                                    </div>
                                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                                </div>
                                            @endif

                                            @if (session('message'))
                                                <div class="alert alert-danger">{{ session('message') }}</div>
                                            @endif

                                            <form action="{{ route('login') }}" method="POST">
                                                @csrf
                
                                                <div class="mb-3">
                                                    <label for="username" class="form-label">{{ __('Email') }}</label>
                                                    <input type="email" class="form-control" id="username" name="email" value="{{ old('email') }}" required autofocus placeholder="Enter {{ __('email') }}">
                                                </div>
                        
                                                <div class="mb-3">
                                                
                                                @if (Route::has('password.request'))
                                                    <div class="float-end">
                                                        <a href="{{ route('password.request') }}" class="text-muted">Forgot password?</a>
                                                    </div>
                                                @endif

                                                    <label class="form-label">{{ __('Password') }}</label>
                                                    <div class="input-group auth-pass-inputgroup">
                                                        <input type="password" class="form-control" name="password" required autocomplete="current-password" placeholder="Enter {{ __('password') }}" aria-label="Password" aria-describedby="password-addon">
                                                        <button class="btn btn-light " type="button" id="password-addon"><i class="mdi mdi-eye-outline"></i></button>
                                                    </div>
                                                </div>
                        
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="remember-check" name="remember">
                                                    <label class="form-check-label" for="remember-check">
                                                        {{ __('Remember me') }}
                                                    </label>
                                                </div>
                                                
                                                <div class="mt-3 d-grid">
                                                    <button class="btn btn-primary waves-effect waves-light" type="submit">Log In</button>
                                                </div>
                    
                                            </form>
                                            
                                            <p class="text-muted text-center" style="margin-top: 30px !important;">Don't have a player account ? <a href="{{ route('register') }}" class="fw-medium text-primary"> Register With Us</a> </p>
                                        </div>
                                    </div>

                                    <div class="mt-md-3 text-center">
                                        <p class="mb-0">© <script>document.write(new Date().getFullYear())</script> <b>-</b> Developed with <i class="mdi mdi-heart text-danger"></i> by <span style="font-weight: 550;">{{ config('app.name') }} Team</span></p>
                                    </div>
                                </div>
                                                                
                            </div>
                        </div>
                    </div>
                    <!-- end col -->
                </div>
                <!-- end row -->
            </div>
            <!-- end container-fluid -->
        </div>

        <!-- JAVASCRIPT -->
        @include('libs.scripts')

    </body>

</html>