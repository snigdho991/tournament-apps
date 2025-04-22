@extends('layouts.master')
@section('title', 'Full Members Preferences')

@section('content')
    <!-- ========================== Page Content ==================================== -->
    <div class="page-content">
        <div class="container-fluid">

            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">Full Members Preferences</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="{{ route('player.dashboard') }}">Dashboard </a></li>
                                <li class="breadcrumb-item active" style="color: #74788d;">Full Members Preferences</li>
                            </ol>
                        </div>

                    </div>
                </div>
            </div>
            <!-- end page title -->

            <div class="row">
                <div class="col-xl-12">
                    
                    <div class="row" id="deviceStandard" style="margin-top:-40px;">
                        <div class="col-md-4"></div>
                        <div class="col-md-4" style="text-align: center !important;">
                            <div class="alert alert-primary fade show mb-0" style="font-weight:600;" role="alert">
                                <i class="bx bx-timer bx-tada me-2 font-size-15" style="position: relative; top: 2px !important;"></i>
                                <span class="timer"></span>
                            </div>
                        </div>
                        <div class="col-md-4"></div>
                    </div>

                    <br><br>
                </div>
            </div>

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
                    
                    <div class="alert alert-dismissible fade show color-box bg-info bg-gradient p-1" role="alert">
                        <marquee class="mb-1 my-2 text-white" direction="left" style="font-weight: 410;">
                            As a Full Member, you don't have to pay any fees to participate in the tournaments or leagues. Just give the preferences in which you want to participate.
                        </marquee>
                    </div>
                      
                </div>
            </div>


            <div class="row">
                <div class="col-lg-7">
                    @if($tournaments->count() > 0)
                        @if($auth_part)

                            @if($auth_part->tournaments)
                                <?php $tours = json_decode($auth_part->tournaments); ?>
                                <div class="alert alert-success" style="text-align: center; margin-bottom: 40px;padding: 4px;" role="alert">
                
                                    <marquee direction="left" style="font-weight: 410;margin-top: 5px;font-size: 14.5px;">
                                        <span style="color: #343a40!important"><i class="bx bx-bell bx-tada" style="position: relative; top: 1.5px;"></i> Notification:</span> <span class="badge bg-success rounded-pill" style="position: relative; top: -1.5px;">1</span> You have already prefered the <strong>{{ $settings->tournaments_open_for }}</strong> of the tournaments! Please <a href="{{ route('player.paid.tournaments.participations') }}" style="color: #222;">click here</a> to check the participation.
                                    </marquee>
                                   
                                </div>
                            @else
                                <form class="needs-validation" action="{{ route('store.tournaments.preferences') }}" method="post" novalidate="">
                                @csrf
                                    <div class="card border border-success">
                                        <div class="card-body">

                                            <div class="row">
                                                <div class="col-md-3"></div>
                                                <div class="col-md-4 text-center">
                                                    <h5 class="text-dark">{{ $settings->tournaments_open_for }}</h5>
                                                </div>
                                                <div class="col-md-5"></div>
                                            </div>
                                            <br>
                                            <div class="row">
                                                <div class="col-md-5">
                                                    <div class="mb-3 position-relative">
                                                        <label for="validationTooltip01" class="form-label" style="font-size: 16px;">
                                                            Choose Tournaments <br>
                                                            <small class="text-primary">For {{ $settings->tournaments_open_for }}</small>
                                                        </label>
                                                    </div>
                                                </div>

                                                
                                                <div class="col-md-7">
                                                    <div class="mb-3 position-relative">

                                                        @foreach($tournaments as $key => $tournament)

                                                            <div class="form-check form-radio-success mb-3" style="font-size: 17px;">
                                                                <input class="form-check-input" name="tournaments[]" type="checkbox" onchange="tournamentCheck()" data-price="{{ $tournament->fees }}" data-twotour="{{ $settings->two_tournament_fees }}" id="formCheckcolor{{ $key + 1 }}" value="{{ $tournament->id }}">
                                                                <label class="form-check-label font-size-16" for="formCheckcolor{{ $key + 1 }}">
                                                                    {{ $tournament->name }} - {{ $tournament->fees }}€ <span style="font-size: .8125rem; position: relative; top: -1.4px;">({{ date('M Y', strtotime($tournament->start)) }} - {{ date('M Y', strtotime($tournament->end)) }})</span>
                                                                </label>
                                                            </div>

                                                        @endforeach
                                                        
                                                    </div>
                                                </div>
                                            

                                                <div class="col-md-5 mt-3">
                                                    <div class="mb-3 position-relative">
                                                        <label for="validationTooltip09" class="form-label" style="font-size: 16px;">
                                                            Tournament Fees
                                                        </label>
                                                    </div>
                                                </div>

                                                
                                                <div class="col-md-7 mt-3">
                                                    <div class="mb-3 position-relative">

                                                        <div class="font-size-16" id="feesValueTour">0 €</div>
                                                        <input type="hidden" name="tournament_fees" id="feesInputValueTour" value="">

                                                    </div>
                                                </div>
                                                
                                                <input type="hidden" name="league_fees" value="0">
                                                                               
                                            </div>

                                        </div>
                                        <!-- end card body -->

                                        <div class="card-footer">
                                            <button type="submit" style="width: 100%;" class="btn btn-success btn-label waves-effect waves-light"><i class="bx bx-money label-icon"></i> Tournament Preferences <i class="bx bx-right-arrow-circle bx-fade-right font-size-20 align-middle me-1"></i></button>
                                        </div>
                                    </div>
                                </form>
                            @endif
                        @else
                            <form class="needs-validation" action="{{ route('store.tournaments.preferences') }}" method="post" novalidate="">
                                @csrf
                                <div class="card border border-success">
                                    <div class="card-body">

                                        <div class="row">
                                            <div class="col-md-3"></div>
                                            <div class="col-md-4 text-center">
                                                <h5 class="text-dark">{{ $settings->tournaments_open_for }}</h5>
                                            </div>
                                            <div class="col-md-5"></div>
                                        </div>
                                        <br>
                                        <div class="row">
                                            <div class="col-md-5">
                                                <div class="mb-3 position-relative">
                                                    <label for="validationTooltip01" class="form-label" style="font-size: 16px;">
                                                        Choose Tournaments <br>
                                                        <small class="text-primary">For {{ $settings->tournaments_open_for }}</small>
                                                    </label>
                                                </div>
                                            </div>

                                            
                                            <div class="col-md-7">
                                                <div class="mb-3 position-relative">

                                                    @foreach($tournaments as $key => $tournament)

                                                        <div class="form-check form-radio-success mb-3" style="font-size: 17px;">
                                                            <input class="form-check-input" name="tournaments[]" type="checkbox" onchange="tournamentCheck()" data-price="{{ $tournament->fees }}" data-twotour="{{ $settings->two_tournament_fees }}" id="formCheckcolor{{ $key + 1 }}" value="{{ $tournament->id }}">
                                                            <label class="form-check-label font-size-16" for="formCheckcolor{{ $key + 1 }}">
                                                                {{ $tournament->name }} - {{ $tournament->fees }}€ <span style="font-size: .8125rem; position: relative; top: -1.4px;">({{ date('M Y', strtotime($tournament->start)) }} - {{ date('M Y', strtotime($tournament->end)) }})</span>
                                                            </label>
                                                        </div>

                                                    @endforeach
                                                    
                                                </div>
                                            </div>


                                            <div class="col-md-5 mt-3">
                                                <div class="mb-3 position-relative">
                                                    <label for="validationTooltip09" class="form-label" style="font-size: 16px;">
                                                        Tournament Fees
                                                    </label>
                                                </div>
                                            </div>

                                            
                                            <div class="col-md-7 mt-3">
                                                <div class="mb-3 position-relative">

                                                    <div class="font-size-16" id="feesValueTour">0 €</div>
                                                    <input type="hidden" name="tournament_fees" id="feesInputValueTour" value="">

                                                </div>
                                            </div>

                                            <input type="hidden" name="league_fees" value="0">
                                                                              
                                        </div>

                                    </div>
                                    <!-- end card body -->

                                    <div class="card-footer">
                                        <button type="submit" style="width: 100%;" class="btn btn-success btn-label waves-effect waves-light"><i class="bx bx-money label-icon"></i> Tournament Preferences <i class="bx bx-right-arrow-circle bx-fade-right font-size-20 align-middle me-1"></i></button>
                                    </div>
                                </div>
                            </form>
                        @endif
                    @else
                        <div class="alert alert-dismissible alert-warning" style="text-align: center; margin-bottom: 40px;padding: 4px;" role="alert">
                
                            <marquee direction="left" style="font-weight: 410;margin-top: 5px;font-size: 14.5px;">
                                <span style="color: #343a40!important"><i class="bx bx-bell bx-tada" style="position: relative; top: 1.5px;"></i> Notification:</span> <span class="badge bg-danger rounded-pill" style="position: relative; top: -1.5px;">1</span> No open tournament found ! Please <span style="color: #222;">try again </span> later.
                            </marquee>
                            <button type="button" class="btn-close text-white" data-bs-dismiss="alert" aria-label="Close"></button>

                        </div>
                    @endif



                    <!-- LEAGUES -->
                    @if($leagues->count() > 0)
                        @if($league_auth_part)

                            @if($league_auth_part->leagues)
                                <?php $tours = json_decode($league_auth_part->leagues); ?>
                                <div class="alert alert-info" style="text-align: center; margin-bottom: 40px;padding: 4px;" role="alert">
                
                                    <marquee direction="left" style="font-weight: 410;margin-top: 5px;font-size: 14.5px;">
                                        <span style="color: #343a40!important"><i class="bx bx-bell bx-tada" style="position: relative; top: 1.5px;"></i> Notification:</span> <span class="badge bg-info rounded-pill" style="position: relative; top: -1.5px;">1</span> You have already prefered the <strong>{{ $settings->leagues_open_for }}</strong> of the leagues! Please <a href="{{ route('player.paid.leagues.participations') }}" style="color: #222;">click here</a> to check the participation.
                                    </marquee>
                                    
                                </div>
                            @else
                                <form class="needs-validation" action="{{ route('store.leagues.preferences') }}" method="post" novalidate="">
                                @csrf
                                    <div class="card border border-success">
                                        <div class="card-body">

                                            <div class="row">
                                                <div class="col-md-3"></div>
                                                <div class="col-md-4 text-center">
                                                    <h5 class="text-dark"> {{ $settings->leagues_open_for }}</h5>
                                                </div>
                                                <div class="col-md-5"></div>
                                            </div>
                                            <br>
                                            <div class="row">


                                                <div class="col-md-5 mt-3">
                                                    <div class="mb-3 position-relative">
                                                        <label for="validationTooltip01" class="form-label" style="font-size: 16px;">
                                                            Choose Leagues <br>
                                                            <small class="text-primary">For {{ $settings->leagues_open_for }}</small>
                                                        </label>
                                                    </div>
                                                </div>

                                                
                                                <div class="col-md-7 mt-3">
                                                    <div class="mb-3 position-relative">

                                                        @foreach($leagues as $key => $league)

                                                            <div class="form-check form-radio-success mb-3" style="font-size: 17px;">
                                                                <input class="form-check-input" name="leagues[]" type="checkbox" onchange="leagueCheck()" data-price="{{ $league->fees }}" id="formCheckcolor{{ $key + 1000 }}" value="{{ $league->id }}">
                                                                <label class="form-check-label font-size-16" for="formCheckcolor{{ $key + 1000 }}">
                                                                    {{ $league->name }} - {{ $league->fees }}€ <span style="font-size: .8125rem; position: relative; top: -1.4px;">({{ date('M Y', strtotime($league->start)) }} - {{ date('M Y', strtotime($league->end)) }})</span>
                                                                </label>
                                                            </div>

                                                        @endforeach
                                                        
                                                    </div>
                                                </div>

                                                <div class="col-md-5 mt-3">
                                                    <div class="mb-3 position-relative">
                                                        <label for="validationTooltip09" class="form-label" style="font-size: 16px;">
                                                            League Fees
                                                        </label>
                                                    </div>
                                                </div>

                                                
                                                <div class="col-md-7 mt-3">
                                                    <div class="mb-3 position-relative">

                                                        <div class="font-size-16" id="feesValueLeag">0 €</div>
                                                        <input type="hidden" name="league_fees" id="feesInputValueLeag" value="">

                                                    </div>
                                                </div>

                                                <input type="hidden" name="tournament_fees" value="0">
                                                                                  
                                            </div>

                                        </div>
                                        <!-- end card body -->

                                        <div class="card-footer">
                                            <button type="submit" style="width: 100%;" class="btn btn-success btn-label waves-effect waves-light"><i class="bx bx-money label-icon"></i> League Preferences <i class="bx bx-right-arrow-circle bx-fade-right font-size-20 align-middle me-1"></i></button>
                                        </div>
                                    </div>
                                </form>
                            @endif
                        @else
                            <form class="needs-validation" action="{{ route('store.leagues.preferences') }}" method="post" novalidate="">
                                @csrf
                                <div class="card border border-success">
                                    <div class="card-body">

                                        <div class="row">
                                            <div class="col-md-3"></div>
                                            <div class="col-md-4 text-center">
                                                <h5 class="text-dark"> {{ $settings->leagues_open_for }}</h5>
                                            </div>
                                            <div class="col-md-5"></div>
                                        </div>

                                        
                                        <div class="row">
                                            <div class="col-md-5 mt-3">
                                                <div class="mb-3 position-relative">
                                                    <label for="validationTooltip01" class="form-label" style="font-size: 16px;">
                                                        Choose Leagues <br>
                                                        <small class="text-primary">For {{ $settings->leagues_open_for }}</small>
                                                    </label>
                                                </div>
                                            </div>

                                            
                                            <div class="col-md-7 mt-3">
                                                <div class="mb-3 position-relative">

                                                    @foreach($leagues as $key => $league)

                                                        <div class="form-check form-radio-success mb-3" style="font-size: 17px;">
                                                            <input class="form-check-input" name="leagues[]" type="checkbox" onchange="leagueCheck()" data-price="{{ $league->fees }}" id="formCheckcolor{{ $key + 1000 }}" value="{{ $league->id }}">
                                                            <label class="form-check-label font-size-16" for="formCheckcolor{{ $key + 1000 }}">
                                                                {{ $league->name }} - {{ $league->fees }}€ <span style="font-size: .8125rem; position: relative; top: -1.4px;">({{ date('M Y', strtotime($league->start)) }} - {{ date('M Y', strtotime($league->end)) }})</span>
                                                            </label>
                                                        </div>

                                                    @endforeach
                                                    
                                                </div>
                                            </div>


                                            <div class="col-md-5 mt-3">
                                                <div class="mb-3 position-relative">
                                                    <label for="validationTooltip09" class="form-label" style="font-size: 16px;">
                                                        League Fees
                                                    </label>
                                                </div>
                                            </div>

                                            
                                            <div class="col-md-7 mt-3">
                                                <div class="mb-3 position-relative">

                                                    <div class="font-size-16" id="feesValueLeag">0 €</div>
                                                    <input type="hidden" name="league_fees" id="feesInputValueLeag" value="">

                                                </div>
                                            </div>
                                                     

                                            <input type="hidden" name="tournament_fees" value="0">
                         
                                        </div>

                                    </div>
                                    <!-- end card body -->

                                    <div class="card-footer">
                                        <button type="submit" style="width: 100%;" class="btn btn-success btn-label waves-effect waves-light"><i class="bx bx-money label-icon"></i> League Preferences <i class="bx bx-right-arrow-circle bx-fade-right font-size-20 align-middle me-1"></i></button>
                                    </div>
                                </div>
                            </form>
                        @endif
                    @else
                        <div class="alert alert-dismissible alert-warning" style="text-align: center; margin-bottom: 40px;padding: 4px;" role="alert">
                
                            <marquee direction="left" style="font-weight: 410;margin-top: 5px;font-size: 14.5px;">
                                <span style="color: #343a40!important"><i class="bx bx-bell bx-tada" style="position: relative; top: 1.5px;"></i> Notification:</span> <span class="badge bg-danger rounded-pill" style="position: relative; top: -1.5px;">1</span> No open league found ! Please <span style="color: #222;">try again </span> later.
                            </marquee>
                            <button type="button" class="btn-close text-white" data-bs-dismiss="alert" aria-label="Close"></button>

                        </div>
                    @endif
                        
                </div>

                <div class="col-lg-5">
                    
                    <div class="card border border-success">
                        <div class="card-body">
                            <h4 class="mb-sm-0 font-size-16 text-center">Payment Information</h4>
                            <p class="font-size-12 mt-1" style="color: #777 !important; text-align: right;">To make it easier for you we offer a variety of ways to pay your fees.</p>
                            <div class="table-responsive">

                                <table class="table align-middle table-nowrap" style="margin-bottom: -5px; margin-top: -10px;">
                                    <tbody>
                                        <tr>
                                            <td >
                                                <p class="mb-0"><i class="mdi mdi-chevron-right text-primary me-1"></i> Bank of Cyprus deposit Acc No. 357021543018 <br><span style="margin-left: 19px;">(referencing your name)</span> </p>
                                            </td>
                                           
                                        </tr>
                                        <tr>
                                            <td >
                                                <p class="mb-0"><i class="mdi mdi-chevron-right text-primary me-1"></i> Revolut </p>
                                            </td>
                                            
                                        </tr>
                                        <tr>
                                            <td >
                                                <p class="mb-0"><i class="mdi mdi-chevron-right text-primary me-1"></i> QuickPay </p>
                                            </td>
                                            
                                        </tr>
                                        <tr style="border-bottom: 0px solid #fff;">
                                            <td >
                                                <p class="mb-0"><i class="mdi mdi-chevron-right text-primary me-1"></i> Cash </p>
                                            </td>
                                            
                                        </tr>
                                    </tbody>
                                </table>

                            </div>

                        </div>
                    </div>

                </div>
                <!-- end col -->
            </div>
            
        </div> <!-- container-fluid -->
    </div>
    <!-- End Page-content -->                
                
@endsection


@section('styles')
    <style type="text/css">
        .spinner-grow {
            animation: 0.9s linear infinite spinner-grow !important;
        }

        @media screen and (max-width: 1199px) and (min-width: 300px) {
            #deviceStandard{
                margin-top: 5px !important;
            }

            #marBot{
                margin-top: 12px !important;
            }
        }
    </style>
@endsection

@section('scripts')
        <script type="text/javascript">
            window.onload = displayClock();
            
            function displayClock(){
                var display = new Date().toLocaleTimeString();
                $('.timer').text(display);
                setTimeout(displayClock, 1000); 
            }
        </script>

        <script type="text/javascript">
            var tour;
            var leag;

            function tournamentCheck() {

                var get_length = $('input[name="tournaments[]"]:checked').length;
                
                var price_array = [];
                var twotour;
                $('input[name="tournaments[]"]:checked').each(function() {
                    price_array.push($(this).data('price'));
                    twotour = $(this).data('twotour');
                });

                if (get_length == 2) {
                    tour = 0;
                } else if (get_length == 0) {
                    tour = 0;
                } else {
                    tour = 0;
                }

                if (get_length > 1) {
                    $("input[name='tournaments[]']:checkbox:not(:checked)").prop("disabled", true);                    
                } else {
                    $("input[name='tournaments[]']").prop("disabled", false); 
                }

                document.getElementById("feesInputValueTour").value = tour;
                document.getElementById("feesValueTour").innerHTML = tour + ' €';

            }

            function leagueCheck() {

                var get_length = $('input[name="leagues[]"]:checked').length;

                var price_array = [];
                
                $('input[name="leagues[]"]:checked').each(function() {
                    price_array.push($(this).data('price'));
                });

                leag = 0;

                if (get_length > 0) {
                    $("input[name='leagues[]']:checkbox:not(:checked)").prop("disabled", true);                    
                } else {
                    $("input[name='leagues[]']").prop("disabled", false); 
                }

                document.getElementById("feesInputValueLeag").value = leag;
                document.getElementById("feesValueLeag").innerHTML = leag + ' €';

            }

            

        </script>
@endsection