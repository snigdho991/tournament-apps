@extends('layouts.master')
@section('title', 'Send E-mails')

@section('content')

    <div class="page-content">
        <div class="container-fluid">

            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">Send E-mails</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard </a></li>
                                <li class="breadcrumb-item active" style="color: #74788d;">Send E-mails</li>
                            </ol>
                        </div>

                    </div>
                </div>
            </div>
            <!-- end page title -->

            <div class="row">
                <div class="col-lg-12">
                    
                    @if(count($errors) > 0)
                        <div class="alert alert-dismissible fade show color-box bg-danger bg-gradient p-4" role="alert">
                            <x-jet-validation-errors class="mb-4 my-2 text-white" />
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                        
                </div>
            </div>


            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">


                                <div class="row text-center">

                                    <h5 class="text-warning text-center mb-3">Current E-mail Stats</h5>
                                    <h5>E-mails have been sent to <span class="text-success"><b>{{ $normal_sents->count() }}</b></span> normal players of total <span class="text-danger"><b>{{ $normal->count() }}</b></span> normal players.</h5>
                                    <h5 class="mt-1">E-mails have been sent to <span class="text-success"><b>{{ $full_sents->count() }}</b></span> full member players of total <span class="text-danger"><b>{{ $full->count() }}</b></span> full member players.</h5>
                                    

                                </div>

                        </div>
                        <!-- end card body -->
                    </div>
                    <!-- end card -->
                </div>
                <!-- end col -->
            </div>

            <br>
            
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">


                                <div class="row">

                                    <h6 class="text-danger text-center mb-0">You can send e-mails from here.</h6>

                                    <div class="col-xl-6">
                                        <form class="needs-validation" action="{{ route('send.mail.to.all.players') }}" method="post" novalidate="">
                                            @csrf
                                                
                                                <br>
                                                <div class="mb-3 position-relative">                                            
                                                    <label for="" class="form-label">Write email for all non full players</label>
                                                    <textarea class="form-control" name="mail_msg" rows="10" required>Hello Tennis4all fans,
<br><br>
Registrations for the 2nd leagues of 2023 are now open!!
<br>
<a href="{{ url('/login') }}">Login</a> to your profile and choose your category.
<br><br>
Registrations open until: 24th of May.
<br><br>
Have a nice weekend!
                                                    </textarea>
                                                </div>

                                                <div class="mb-3 position-relative">                                            
                                                    <label for="" class="form-label"></label>
                                                    
                                                    <button class="btn btn-success waves-effect waves-light w-sm" style="width: 100% !important; height: 75px;" onclick="return confirm('Are you sure to send the e-mails ?')" type="submit"><i class="bx bx-envelope d-block font-size-16"></i> Send E-mails To All Non full Players</button>
                                                    
                                                </div>
                                            
                                        </form>
                                    </div>

                                    <div class="col-xl-6">
                                        <form class="needs-validation" action="{{ route('send.mail.to.all.fullmembers') }}" method="post" novalidate="">
                                            @csrf

                                                <br>
                                                <div class="mb-3 position-relative">                                            
                                                    <label for="" class="form-label">Write email for all full players</label>
                                                    <textarea class="form-control" name="mail_msg" rows="10" required>Hello Tennis4all fans,
<br><br>
Registrations for the 2nd leagues of 2023 are now open!!
<br>
<a href="{{ url('/login') }}">Login</a> to your profile and choose your category.
<br><br>
Registrations open until: 24th of May.
<br><br>
Have a nice weekend!
                                                    </textarea>
                                                </div>
                                            
                                                <div class="mb-3 position-relative">                                            
                                                    <label for="" class="form-label"></label>
                                                    
                                                    <button class="btn btn-primary waves-effect waves-light w-sm" style="width: 100% !important; height: 75px;" onclick="return confirm('Are you sure to send the e-mails ?')" type="submit"><i class="bx bx-envelope d-block font-size-16"></i> Send E-mails To All full Players/Members</button>

                                                </div>
                                            
                                        </form>
                                    </div>

                                </div>

                                <br>

                                <div class="row">

                                    <h6 class="text-danger text-center mb-0">You can clear/reset e-mails from here.</h6>

                                    <div class="col-xl-6">
                                        <form class="needs-validation" action="{{ route('clear.mail.to.all.players') }}" method="post" novalidate="">
                                            @csrf
                                            

                                                <div class="mb-3 position-relative">                                            
                                                    <label for="" class="form-label"></label>
                                                    
                                                    <div class="button-items">
                                                        <button class="btn btn-danger waves-effect waves-light w-sm" style="width: 100% !important; height: 75px;" onclick="return confirm('Are you sure to clear emails of all the Non full Players so that you can send new emails to them?')" type="submit"><i class="bx bx-trash-alt d-block font-size-16"></i> Clear emails of all Non full Players</button>
                                                    </div>

                                                </div>
                                            
                                        </form>
                                    </div>

                                    <div class="col-xl-6">
                                        <form class="needs-validation" action="{{ route('clear.mail.to.all.fullmembers') }}" method="post" novalidate="">
                                            @csrf
                                            
                                                <div class="mb-3 position-relative">                                            
                                                    <label for="" class="form-label"></label>
                                                    
                                                    <button class="btn btn-danger waves-effect waves-light w-sm" style="width: 100% !important; height: 75px;" onclick="return confirm('Are you sure to clear emails of all the Full Players/Members so that you can send new emails to them?')" type="submit"><i class="bx bx-trash-alt d-block font-size-16"></i> Clear emails of all Full Players/Members</button>

                                                </div>
                                            
                                        </form>
                                    </div>

                                </div>

                        </div>
                        <!-- end card body -->
                    </div>
                    <!-- end card -->
                </div>
                <!-- end col -->
            </div>
            <!-- end row -->

        </div>
    </div>

@endsection
