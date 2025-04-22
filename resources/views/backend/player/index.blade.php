@extends('layouts.master')
@section('title', 'Player Dashboard')

@section('content')


    <div class="modal fade" id="staticBackdropTournament" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabelTour" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title text-center" id="staticBackdropLabelTour">Participating Players (Tournament)</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <h5 class="deal-title" style="font-weight: 400;"></h5>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" style="margin: auto !important;" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="staticBackdropLeague" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabelLeague" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title text-center" id="staticBackdropLabelLeague">Participating Players (League)</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <h5 class="deal-title" style="font-weight: 400;"></h5>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" style="margin: auto !important;" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="staticBackdropSu" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabelSu" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title text-center" id="staticBackdropLabelSu">Supervisor Details (<span class="deal-part"></span>)</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <h5 class="deal-title" style="font-weight: 400;"></h5>
                    <h5 class="deal-contact" style="font-weight: 400;"></h5>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" style="margin: auto !important;" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>


    <div class="page-content">
        <div class="container-fluid">

            @if($tournaments->count() > 0)
                <div class="row">
                    <div class="col-12">
                        <div class="alert alert-dismissible alert-success mt-3" style="text-align: center; margin-bottom: 40px;padding: 4px;" role="alert">
                    
                            <marquee direction="left" style="font-weight: 410;margin-top: 5px;font-size: 14.5px;">
                                <span style="color: #343a40!important"><i class="bx bx-bell bx-tada" style="position: relative; top: 1.5px;"></i> Notification:</span> <span class="badge bg-success rounded-pill" style="position: relative; top: -1.5px;">1</span> Registrations for the <strong>{{ $settings->tournaments_open_for }} of the tournaments of 2023</strong> are now open. Please <a href="@if(Auth::user()->status == 'Full Member') {{ route('full.member.preferences') }} @else {{ route('player.participate') }} @endif" style="color: #222;">click here</a> to choose your preferences or participate in the tournaments.
                            </marquee>

                            <button type="button" class="btn-close text-white" style="padding: 15px;" data-bs-dismiss="alert" aria-label="Close"></button>
                        
                        </div>
                    </div>
                </div>
            @endif

            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">{{ Auth::user()->role }} Dashboard</h4>                      
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-6">

                    <div class="card text-center">
                        <div class="card-body">
                            <span class="badge rounded-pill badge-soft-primary font-size-11">Player Info</span>
                            <div class="avatar-sm mx-auto mb-4">
                                <br><span class="avatar-title rounded-circle bg-primary bg-soft text-primary font-size-16">
                                    <a class="image-popup-vertical-fit" href="@if(Auth::user()->profile_photo_path) {{ asset('assets/uploads/users/'.Auth::user()->profile_photo_path) }} @else {{ config('core.image.default.avatar') }} @endif">
                                        <img src="@if(Auth::user()->profile_photo_path) {{ asset('assets/uploads/users/'.Auth::user()->profile_photo_path) }} @else {{ config('core.image.default.avatar') }} @endif" alt="player-image" height="40" width="40" style="border-radius: 50%;">
                                    </a>
                                </span>
                            </div>

                            {{-- <span class="badge rounded-pill bg-success mb-2 mt-3">Residential</span> --}}

                            <br><h4 class="font-size-18 mb-2">{{ Auth::user()->name }} @if(Auth::user()->status == 'Full Member') <span class="badge rounded-pill bg-success font-size-11" style="vertical-align: middle !important;">FULL MEMBER</span> @endif</h4>

                            <p class="text-muted font-size-14">{{ Auth::user()->gender }}</p>
                            <p class="text-muted font-size-14" style="margin-top: -15px;">D.O.B - {{ Auth::user()->age }} </p>
                            <p class="text-muted font-size-14" style="margin-top: -15px;">{{ Auth::user()->phone }}</p>
                            <p class="text-muted font-size-14" style="margin-top: -15px;">{{ Auth::user()->email }}</p>

                            <div class="row" style="margin-top: -10px;">
                                <div class="mt-4 col-lg-4 col-xl-4 col-md-4 col-sm-4 col-12">
                                    <h5 class="font-size-15">{{ $paid_part }}</h5>
                                    <p class="text-muted mb-0">@if($paid_part > 1) Participations @else Participation @endif</p>
                                </div>
                                <div class="mt-4 col-lg-4 col-xl-4 col-md-4 col-sm-4 col-12">
                                    <a href="{{ url('/user/profile') }}" class="btn btn-outline-success waves-effect waves-light editprofile" style="width: 100%;">
                                        <i class="bx bxs-edit-alt d-block font-size-16"></i> Edit Profile
                                    </a>
                                </div>
                                <div class="mt-4 col-lg-4 col-xl-4 col-md-4 col-sm-4 col-12">
                                    <h5 class="font-size-15">€ {{ $paid_fees }}</h5>
                                    <p class="text-muted mb-0">Fees Paid</p>
                                </div>
                            </div>

                        </div>
                    </div>

                </div> <!-- end col -->
                <div class="col-lg-6">
                    <div class="card text-center">
                        <div class="card-body">
                            <span class="badge rounded-pill badge-soft-info font-size-11">All Fees</span>
                            
                            <div class="text-muted mt-4" style="text-align: justify;">

                                <div class="table-responsive mt-4">

                                    <table class="table align-middle table-nowrap" style="margin-bottom: -3px;">
                                        <tbody>
                                            <tr>
                                                <td >
                                                    <p class="mb-0"><i class="mdi mdi-chevron-right text-primary me-1"></i> 1 Tournament / 1 Category </p>
                                                </td>
                                                <td>
                                                    <h5 class="mb-0"> <span class="text-primary" style="text-transform: uppercase; font-weight: 550;">€ 20</span></h5>
                                                </td>
                                               
                                            </tr>
                                            <tr>
                                                <td >
                                                    <p class="mb-0"><i class="mdi mdi-chevron-right text-primary me-1"></i> 1 tournament / 2 Categories </p>
                                                </td>
                                                <td>
                                                    <h5 class="mb-0"> <span class="text-primary" style="text-transform: uppercase; font-weight: 550;">€ 35</span></h5>
                                                </td>
                                                
                                            </tr>
                                            <tr>
                                                <td >
                                                    <p class="mb-0"><i class="mdi mdi-chevron-right text-primary me-1"></i> Leagues </p>
                                                </td>
                                                <td>
                                                    <h5 class="mb-0"> <span class="text-primary" style="text-transform: uppercase; font-weight: 550;">€ 30</span></h5>
                                                </td>
                                                
                                            </tr>
                                            <tr>
                                                <td >
                                                    <p class="mb-0"><i class="mdi mdi-chevron-right text-primary me-1"></i> Top16 Finals </p>
                                                </td>
                                                <td>
                                                    <h5 class="mb-0"> <span class="text-primary" style="text-transform: uppercase; font-weight: 550;">€ 20</span></h5>
                                                </td>
                                                
                                            </tr>

                                            <tr>
                                                <td >
                                                    <p class="mb-0"><i class="mdi mdi-chevron-right text-primary me-1"></i> 1 Day Tournament </p>
                                                </td>
                                                <td>
                                                    <h5 class="mb-0"> <span class="text-primary" style="text-transform: uppercase; font-weight: 550;">€ 20</span></h5>
                                                </td>
                                                
                                            </tr>

                                            <tr style="border-bottom: 0px solid #fff;">
                                                <td >
                                                    <p class="mb-0"><i class="mdi mdi-chevron-right text-primary me-1"></i> FULL MEMBERSHIP </p>
                                                </td>
                                                <td>
                                                    <h5 class="mb-0"> <span class="text-primary" style="text-transform: uppercase; font-weight: 550;">€ 120</span></h5>
                                                </td>
                                                
                                            </tr>
                                        </tbody>
                                    </table>

                                </div>

                            </div>

                        </div>
                    </div>
                </div>
            </div>
            
            <br>

            @foreach($tournaments as $key => $all_tournaments)

                <div class="row">
                    <div class="col-xl-12 text-center">
                        <h4 class="mb-sm-0 font-size-18">Tournaments Ongoing ({{ $key }})</h4>
                    </div>
                </div>

                <div class="row" style="margin-top: 15px !important;">

                    @foreach($all_tournaments as $tournament)
                        <?php $participants = []; ?>
                        <?php 
                            if($tournament->status == 'On') {
                                $statusclr = 'info';
                                $btnsts = 'right';
                            } else if($tournament->status == 'Off') {
                                $statusclr = 'success';
                                $btnsts = 'right';
                            }

                            $payments = \App\Models\Payment::all();
                            foreach ($payments as $key => $payment) {
                                
                                if($payment->status == 'Paid') {
                                    if($payment->tournaments) {
                                        $arrays = json_decode($payment->tournaments);
                                    } else {
                                        $arrays = [];
                                    }
                                } else {
                                    $arrays = [];
                                }
                                

                                foreach($arrays as $k => $value) {
                                    if($tournament->id == $value) {
                                        array_push($participants, $tournament->id);
                                    }
                                }

                            }
                        ?>
                        <div class="col-lg-3">
                            <div class="card border border-{{ $statusclr }}">
                                <div class="card-header bg-transparent border-{{ $statusclr }}">
                                    <h5 class="my-0 text-{{ $statusclr }} text-center"><i class="mdi mdi-tennis me-2"></i>{{ $tournament->name }}</h5>
                                    <div class="text-center text-{{ $statusclr }}"><small>{{ $tournament->draw_status }}</small></div>

                                    <div class="d-flex justify-content-center" style="margin-top: 10px; margin-bottom: -15px;">
                                        <i class="bx bx-toggle-{{ $btnsts }} bx-fade-{{ $btnsts }} text-{{ $statusclr }} display-4"></i>
                                    </div>
                                </div>
                                <div class="card-body" style="text-align: center;">
                                    <h5 class="card-title mt-0">{{ date('F y', strtotime($tournament->start)) }} - {{ date('F y', strtotime($tournament->end)) }}</h5>
                                    <h5 class="font-size-14"><i class="bx bx-money font-size-18 me-1" style="position: relative;top: 3px;"></i> Fees <i class="bx bx-right-arrow-alt font-size-16 text-primary align-middle ms-1 me-1"></i> {{ $tournament->fees }} €</h5>
                                        
                                    <span style="display: inline-flex; flex-direction: row; gap: 10px;">

                                        <button class="btn btn-light btn-sm waves-effect waves-light" style="margin-top: 6px;" id="getTourPlayers" data-bs-toggle="modal" data-bs-target="#staticBackdropTournament" data-url="{{ route('get.tournament.players', $tournament->id) }}"><i class="bx bxs-group" style="position: relative;top: 0.67px;"></i> {{ count($participants) }} Participants</button>

                                        <button class="btn btn-outline-light btn-sm waves-effect waves-dark" style="margin-top: 6px;" id="getSupervisor" data-bs-toggle="modal" data-bs-target="#staticBackdropSu" data-url="{{ route('get.tournament.supervisor', $tournament->id) }}"><i class="bx bx-run" style="position: relative;top: 0.67px;"></i> Supervisor</button>
                                    </span>

                                </div>
                            
                                <div class="card-footer" style="margin-top: -10px;">

                                    <a href="@if(Auth::user()->status == 'Full Member') {{ route('full.member.preferences') }} @else {{ route('player.participate') }} @endif" style="width: 100%;" class="btn btn-{{ $statusclr }} waves-effect waves-light">Participate <i class="bx bx-right-arrow-circle bx-tada font-size-20 align-middle ms-1"></i></a>
                                    
                                </div>
                            </div>
                        </div>
                    
                    @endforeach
                </div>

            @endforeach


            @foreach($leagues as $key => $all_leagues)

                <div class="row" style="margin-top: 5px !important;">
                    <div class="col-xl-12 text-center">
                        <h4 class="mb-sm-0 font-size-18">Leagues Ongoing ({{ $key }})</h4>
                    </div>
                </div>

                <div class="row" style="margin-top: 15px !important;">

                    <div class="col-lg-3"></div>

                    @foreach($all_leagues as $league)
                        <?php $league_participants = []; ?>
                        <?php 
                            if($league->status == 'On') {
                                $statusclr = 'success';
                                $btnsts = 'right';
                            } else if($league->status == 'Off') {
                                $statusclr = 'info';
                                $btnsts = 'right';
                            }

                            $payments = \App\Models\Payment::all();
                            foreach ($payments as $key => $payment) {
                                
                                if($payment->league_status == 'Paid') {
                                    if($payment->leagues) {
                                        $arrays = json_decode($payment->leagues);
                                    } else {
                                        $arrays = [];
                                    }
                                } else {
                                    $arrays = [];
                                }
                                

                                foreach($arrays as $k => $value) {
                                    if($league->id == $value) {
                                        array_push($league_participants, $league->id);
                                    }
                                }

                            }
                        ?>
                        <div class="col-lg-3">
                            <div class="card border border-{{ $statusclr }}">
                                <div class="card-header bg-transparent border-{{ $statusclr }}">
                                    <h5 class="my-0 text-{{ $statusclr }} text-center"><i class="mdi mdi-tennis me-2"></i>{{ $league->name }}</h5>
                                    <div class="text-center text-{{ $statusclr }}"><small>{{ $league->draw_status }}</small></div>

                                    <div class="d-flex justify-content-center" style="margin-top: 10px; margin-bottom: -15px;">
                                        
                                        <i class="bx bx-toggle-{{ $btnsts }} bx-fade-{{ $btnsts }} text-{{ $statusclr }} display-4"></i>

                                    </div>
                                </div>
                                <div class="card-body" style="text-align: center;">
                                    <h5 class="card-title mt-0">{{ date('F y', strtotime($league->start)) }} - {{ date('F y', strtotime($league->end)) }}</h5>
                                    <h5 class="font-size-14"><i class="bx bx-money font-size-18 me-1" style="position: relative;top: 3px;"></i> Fees <i class="bx bx-right-arrow-alt font-size-16 text-primary align-middle ms-1 me-1"></i> {{ $league->fees }} €</h5>

                                    <span style="display: inline-flex; flex-direction: row; gap: 10px;">

                                        <button class="btn btn-light btn-sm waves-effect waves-light" style="margin-top: 6px;" id="getLeagPlayers" data-bs-toggle="modal" data-bs-target="#staticBackdropLeague" data-url="{{ route('get.league.players', $league->id) }}"><i class="bx bxs-group" style="position: relative;top: 0.67px;"></i> {{ count($league_participants) }} Participants</button>

                                        <button class="btn btn-outline-light btn-sm waves-effect waves-dark" style="margin-top: 6px;" id="getSupervisor" data-bs-toggle="modal" data-bs-target="#staticBackdropSu" data-url="{{ route('get.league.supervisor', $league->id) }}"><i class="bx bx-run" style="position: relative;top: 0.67px;"></i> Supervisor</button>

                                    </span>

                                </div>
                            
                                <div class="card-footer" style="margin-top: -10px;">

                                    <a href="@if(Auth::user()->status == 'Full Member') {{ route('full.member.preferences') }} @else {{ route('player.participate') }} @endif" style="width: 100%;" class="btn btn-{{ $statusclr }} waves-effect waves-light">Participate <i class="bx bx-right-arrow-circle bx-tada font-size-20 align-middle ms-1"></i></a>
                                    
                                </div>
                            </div>
                        </div>
                    
                    @endforeach

                    
                </div>

            @endforeach

            </div>
            <!-- end row -->

        </div>
    </div>

@endsection

@section('styles')
    <style type="text/css">
        @media only screen and (min-width: 271px) and (max-width: 575px)  {
            .editprofile {
                width: 70% !important;
            }
        }
    </style>
@endsection

@section('scripts')
    <script>
        $(document).ready(function(){

            $(document).on('click', '#getTourPlayers', function(e){

                e.preventDefault();

                var url = $(this).data('url');

                $.ajax({
                    url: url,
                    type: 'GET',
                })
                .done(function(data){
                    if(data.length > 0) {
                        $('.deal-title').html(data);
                    } else {
                        $('.deal-title').html('No Player Found!');
                    }

                })
                .fail(function(){
                    alert('Something went wrong!')
                });

            });

        });
    </script>


    <script>
        $(document).ready(function(){

            $(document).on('click', '#getLeagPlayers', function(e){

                e.preventDefault();

                var url = $(this).data('url');

                $.ajax({
                    url: url,
                    type: 'GET',
                })
                .done(function(data){
                    if(data.length > 0) {
                        $('.deal-title').html(data);
                    } else {
                        $('.deal-title').html('No Player Found!');
                    }

                })
                .fail(function(){
                    alert('Something went wrong!')
                });

            });

        });
    </script>

    <script>
        $(document).ready(function(){

            $(document).on('click', '#getSupervisor', function(e){

                e.preventDefault();

                var url = $(this).data('url');

                $.ajax({
                    url: url,
                    type: 'GET',
                })
                .done(function(data){

                    $('.deal-part').html(data.name);
                    $('.deal-title').html('Name : ' + data.supervisor_name);
                    $('.deal-contact').html('Contact : ' + data.supervisor_phone);
                    
                })
                .fail(function(){
                    alert('Something went wrong!')
                });

            });

        });
    </script>
    
@endsection
