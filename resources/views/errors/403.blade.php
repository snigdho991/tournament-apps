<!doctype html>
<html lang="en">

    
    <head>
        
        <meta charset="utf-8" />
        <title>403 Forbidden - {{ config('app.name') }}</title>
        
        @include('libs.meta-tags')
        
        @include('libs.styles')

    </head>

    <body>

        <div class="account-pages my-5 pt-5">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="text-center mb-5">
                            <h1 class="display-2 fw-medium">4<i class="bx bx-buoy bx-spin text-primary display-3"></i>3</h1>
                            <h4 class="text-uppercase">Access Denied</h4>
                            <div class="mt-5 text-center">
                                @if(Auth::check())
                                    @if(Auth::user()->hasRole('Administrator'))
                                        <a class="btn btn-primary waves-effect waves-light" href="{{ route('admin.dashboard') }}">Back to Dashboard</a>
                                    @else
                                        <a class="btn btn-primary waves-effect waves-light" href="{{ route('player.dashboard') }}">Back to Dashboard</a>
                                    @endif
                                @else
                                    <a class="btn btn-primary waves-effect waves-light" href="{{ url('/login') }}">Back to Login</a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row justify-content-center">
                    <div class="col-md-8 col-xl-6">
                        <div>
                            <img src="{{ asset('assets/images/error-img.png') }}" alt="" class="img-fluid">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>

</html>