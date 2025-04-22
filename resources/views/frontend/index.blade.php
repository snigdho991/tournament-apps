@extends('layouts.frontend-master')

@section('title', 'Home Page')

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

                    <div style="width: 100%; text-align: center;">
                        <a href="{{ route('login') }}" class="btn btn-primary waves-effect waves-light mt-3 mb-2">Login Now <i class="bx bx-right-arrow-circle bx-tada font-size-20 align-middle ms-1"></i></a>
                    </div>
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

                    <div style="width: 100%; text-align: center;">
                        <a href="{{ route('login') }}" class="btn btn-primary waves-effect waves-light mt-3 mb-2">Login Now <i class="bx bx-right-arrow-circle bx-tada font-size-20 align-middle ms-1"></i></a>
                    </div>
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

        
    <section class="section hero-section bg-ico-hero" id="home" style="padding-top: 150px; padding-bottom: 100px;">
        <div class="nav-back-custom"></div>
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-5">
                    <div class="text-white-50">
                        <h1 class="text-white font-weight-semibold mb-3 hero-title"> {{ config('app.name') }} - <span>Follow Your Passion Fight till the end.</span></h1>
                        <p class="font-size-14" style="color: #ddd !important;">Tennis4all Cyprus is a tennis community of players who like to play tennis for fun and exercise. Our tournaments base is Limassol. Players in tennis4all tournaments are amateurs and separated in 3 categories: beginners, intermediate and experts (not professionals). If you are interested to participate you just have to create a profile in our web page and register to our tournaments.</p>
                        
                        <div class="button-items mt-4">
                            @if(Auth::user())
                                <a href="@if(Auth::user()->hasRole('Administrator')) {{ route('admin.dashboard') }} @elseif(Auth::user()->hasRole('Player')) {{ route('player.dashboard') }} @endif" target="_blank" class="btn btn-success w-xs">Your Dashboard</a>
                                <a href="{{ route('frontend.bg.faq') }}" class="btn btn-light">View FAQ</a>
                            @else
                                <a href="{{ route('login') }}" class="btn btn-success w-xs">Log In</a>
                                <a href="{{ route('register') }}" class="btn btn-primary w-xs">Register</a>
                            @endif

                        </div>
                    </div>
                </div>

                <div class="col-lg-7 col-md-10 col-sm-12 ms-lg-auto">
                    <div class="card overflow-hidden mb-0 mt-5 mt-lg-0">

                        <div class="card-body">
                            <div class="zoom-gallery d-flex flex-wrap">
                                <a href="{{ asset('/assets/uploads/home/slider.webp') }}">
                                    <img src="{{ asset('/assets/uploads/home/slider.webp') }}" alt="" class="rounded img-fluid mx-auto d-block">
                                </a>
                            </div>
                        </div>
                            
                    </div>
                </div>
            </div>
            <!-- end row -->
        </div>
        <!-- end container -->
    </section>

    <section class="section pt-4 bg-white" id="about" style="padding-top:30px !important;">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="text-center">
                        <h4 style="margin-bottom: 15px;">Who We Are</h4>
                    </div>
                </div>
            </div>

            <div class="row" style="margin-top: 5px;">
                
                <div class="col-lg-12 col-12">

                    <h4 class="card-title-desc font-size-16 mb-3" style="font-weight: 400 !important; line-height: 24px; text-align: justify;">Tennis4all Cyprus is a tennis community of players who like to play tennis for fun and exercise. Players in tennis4all tournaments are amateurs and separated in 3 categories: beginners, intermediate and experts (not professionals). If you are interested to participate you just have to create a profile in our web page and register to our tournaments.</h4>

                </div>

            </div>


            <div class="row" style="margin-top: 5px;">
                
                <div class="col-lg-6 col-6">
                    <div class="zoom-gallery d-flex flex-wrap mt-4">
                        <a href="{{ asset('/assets/uploads/home/one.jfif') }}">
                            <img src="{{ asset('/assets/uploads/home/one.jfif') }}" alt="" class="rounded img-fluid mx-auto d-block">
                        </a>
                    </div>
                </div>
                <div class="col-lg-6 col-6">
                    <div class="zoom-gallery d-flex flex-wrap mt-4">
                        <a href="{{ asset('/assets/uploads/home/two.jpg') }}">
                            <img src="{{ asset('/assets/uploads/home/two.jpg') }}" style="height: 112%;" alt="" class="rounded img-fluid mx-auto d-block">
                        </a>
                    </div>
                </div>
            </div>

            <!-- end row -->
        </div>
        <!-- end container -->
    </section>

    <section class="section" id="tournamentsf" style="padding-top:60px !important;">
        
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="text-center">
                        <h4>Our Tournaments</h4>
                        
                    </div>
                </div>
            </div>
            <!-- end row -->

            <div class="row" style="padding-top: 5px !important;">

                <div class="col-lg-12 col-12">
                    <p class="font-size-14 mb-3" style="font-weight: 400 !important; line-height: 27px; text-align: justify;">Players are separated in 3 categories: beginners, intermediate and experts (not professionals). Based on those categories the relevant tournaments are taking place: Rookie100, INT250, ADV500 and PRO1000. Tournaments are knock-out rounds and points allocation is based on ATP points system.</p>
                </div>

            </div>

            @foreach($tournaments as $key => $all_tournaments)
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
                        <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 col-12">
                            <div class="card border border-{{ $statusclr }}">
                                <div class="card-header bg-transparent border-{{ $statusclr }}">
                                    <h5 class="my-0 text-{{ $statusclr }} text-center"><i class="mdi mdi-tennis me-2"></i>{{ $tournament->name }}</h5>
                                    <div class="text-center text-{{ $statusclr }}"><small>{{ $tournament->draw_status }}</small></div>

                                    <div class="d-flex justify-content-center" style="margin-top: 10px; margin-bottom: -15px;">
                                        <i class="bx bx-toggle-{{ $btnsts }} bx-fade-{{ $btnsts }} text-{{ $statusclr }} display-4"></i>
                                    </div>
                                </div>
                                <div class="card-body" style="text-align: center;">
                                    <h5 class="card-title mt-0">{{ date('M. y', strtotime($tournament->start)) }} - {{ date('M. y', strtotime($tournament->end)) }}</h5>
                                    <h5 class="font-size-14"><i class="bx bx-money font-size-18 me-1" style="position: relative;top: 3px;"></i> Fees <i class="bx bx-right-arrow-alt font-size-16 text-primary align-middle ms-1 me-1"></i> {{ $tournament->fees }} €</h5>
                                        
                                    <span style="display: inline-flex; flex-direction: row; gap: 10px;">

                                        <button class="btn btn-light btn-sm waves-effect waves-light" style="margin-top: 8px;" id="getTourPlayers" data-bs-toggle="modal" data-bs-target="#staticBackdropTournament" data-url="{{ route('get.tournament.players', $tournament->id) }}"><i class="bx bxs-group" style="position: relative;top: 0.67px;"></i> {{ count($participants) }} Participants</button>

                                        <button class="btn btn-outline-light btn-sm waves-effect waves-dark" style="margin-top: 8px;" id="getSupervisor" data-bs-toggle="modal" data-bs-target="#staticBackdropSu" data-url="{{ route('get.tournament.supervisor', $tournament->id) }}"><i class="bx bx-run" style="position: relative;top: 0.67px;"></i> Supervisor</button>
                                    </span>

                                </div>
                            
                                <div class="card-footer" style="margin-top: -10px;">

                                    <a href="@if(Auth::check()) @if(Auth::user()->status == 'Full Member') {{ route('full.member.preferences') }} @else {{ route('player.participate') }} @endif @else # @endif" style="width: 100%;" class="btn btn-{{ $statusclr }} waves-effect waves-light">Participate <i class="bx bx-right-arrow-circle bx-tada font-size-20 align-middle ms-1"></i></a>
                                    
                                </div>
                            </div>
                        </div>
                    
                    @endforeach
                </div>

            @endforeach

        </div>
        <!-- end container -->
    </section>


    <section class="section bg-white" id="leaguesf" style="margin-top:-20px !important;">
        
        <div class="container">
            
            <div class="row">
                <div class="col-lg-12">
                    <div class="text-center" style="margin-top:-20px !important;">
                        <h4>Our Leagues</h4>
                        
                    </div>
                </div>
            </div>
            <!-- end row -->

            <div class="row" style="padding-top: 5px !important;">

                <div class="col-lg-12 col-12">
                    <p class="font-size-14 mb-3" style="font-weight: 400 !important; line-height: 27px; text-align: justify;">“Leagues” is a group stage organisation. Players who are interested to play in Leagues are separated in Categories based on their level of game(Cat “A”, Cat “B” and Cat “C”). Two (2) to four (4) first players of each group (depends on the participation) are qualified in the knock-out stage playing with the other qualifying players. At the end of the Leagues the Final of each category is set with the winner and finalist winning a trophy.</p>
                </div>

            </div>

            @foreach($leagues as $key => $all_leagues)
                <div class="row" style="margin-top: 15px !important;">

                    @foreach($all_leagues as $league)
                        <?php $participants = []; ?>
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
                                        array_push($participants, $league->id);
                                    }
                                }

                            }
                        ?>

                            <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 col-12" @if($league->name == 'Category C') style="margin: 0 auto;" @endif>
                                <div class="card border border-{{ $statusclr }}">
                                    <div class="card-header bg-transparent border-{{ $statusclr }}">
                                        <h5 class="my-0 text-{{ $statusclr }} text-center"><i class="mdi mdi-tennis me-2"></i>{{ $league->name }}</h5>
                                        <div class="text-center text-{{ $statusclr }}"><small>{{ $league->draw_status }}</small></div>

                                        <div class="d-flex justify-content-center" style="margin-top: 10px; margin-bottom: -15px;">
                                            <i class="bx bx-toggle-{{ $btnsts }} bx-fade-{{ $btnsts }} text-{{ $statusclr }} display-4"></i>
                                        </div>
                                    </div>
                                    <div class="card-body" style="text-align: center;">
                                        <h5 class="card-title mt-0">{{ date('F y', strtotime($league->start)) }}-{{ date('F y', strtotime($league->end)) }}</h5>
                                        <h5 class="font-size-14"><i class="bx bx-money font-size-18 me-1" style="position: relative;top: 3px;"></i> Fees <i class="bx bx-right-arrow-alt font-size-16 text-primary align-middle ms-1 me-1"></i> {{ $league->fees }} €</h5>
                                            
                                        <span style="display: inline-flex; flex-direction: row; gap: 10px;">

                                            <button class="btn btn-light btn-sm waves-effect waves-light" style="margin-top: 8px;" id="getLeagPlayers" data-bs-toggle="modal" data-bs-target="#staticBackdropLeague" data-url="{{ route('get.league.players', $league->id) }}"><i class="bx bxs-group" style="position: relative;top: 0.67px;"></i> {{ count($participants) }} Participants</button>

                                            <button class="btn btn-outline-light btn-sm waves-effect waves-dark" style="margin-top: 8px;" id="getSupervisor" data-bs-toggle="modal" data-bs-target="#staticBackdropSu" data-url="{{ route('get.league.supervisor', $league->id) }}"><i class="bx bx-run" style="position: relative;top: 0.67px;"></i> Supervisor</button>
                                        </span>

                                    </div>
                                
                                    <div class="card-footer" style="margin-top: -10px;">

                                        <a href="@if(Auth::check()) @if(Auth::user()->status == 'Full Member') {{ route('full.member.preferences') }} @else {{ route('player.participate') }} @endif @else # @endif" style="width: 100%;" class="btn btn-{{ $statusclr }} waves-effect waves-light">Participate <i class="bx bx-right-arrow-circle bx-tada font-size-20 align-middle ms-1"></i></a>
                                        
                                    </div>
                                </div>
                            </div>
                    
                    @endforeach
                </div>

            @endforeach

        </div>
        <!-- end container -->
    </section>


@endsection

@section('scripts')
    
    <script>
        $(document).ready(function() {
            $(".select2").select2({
                allowClear: true,
            });
        });
    </script>

    <script type="text/javascript">
        $('.owl-carousel').owlCarousel({
            margin:10,
            loop:true,
            autoWidth:true,
            items:4,
            rtl:false,
        });
    </script>

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
                        $('.deal-title').html('Please login to the app to see the tournament players.');
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
                    console.log(data)
                    if(data.length > 0) {
                        $('.deal-title').html(data);
                    } else {
                        $('.deal-title').html('Please login to the app to see the league players.');
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