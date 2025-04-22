<!doctype html>
<html lang="en">

    
    <head>
        
        <meta charset="utf-8" />
        <title>403 Forbidden - {{ config('app.name') }}</title>
        
        @include('libs.meta-tags')
        
        @include('libs.styles')

    </head>

    <body>
        <span>
            <p style="margin-left: 20px; margin-top: 15px; font-weight:500">Hello, <span class="text-success"> {{ auth()->user()->name }} </span></p>
            <form method="POST" action="{{ route('logout') }}" style="float: right; margin-top: -40px;">
                @csrf

                <button type="submit" class="text-danger dropdown-item" style="font-weight: 500;">
                    <i class="bx bx-power-off font-size-16 align-middle me-1 text-danger"></i>

                    <span key="t-logout">
                        Log out
                    </span>                            
                </button>
            </form>
        </span>

        <div class="account-pages my-5 pt-5">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="text-center mb-5">
                            <h1 class="display-2 fw-medium">N<i class="bx bx-buoy bx-spin text-danger display-3"></i>T</h1>
                            <h4 style="margin-top: -15px;">APPROVED YET.</h4>
                            <h4 style="margin-top:15px;">Your account is waiting for the administrator approval.
                                <br>
                                Please check back later.</h4>
                            
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

        @include('libs.scripts')

    </body>

</html>