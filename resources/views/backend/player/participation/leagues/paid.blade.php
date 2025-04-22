@extends('layouts.master')
@section('title', 'My Paid Participations - Leagues')

@section('content')

    
    <div class="modal fade" id="staticBackdropLeague" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabelLeag" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title text-center" id="staticBackdropLabelLeag">Participating Players (League)</h4>
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


    <!-- ========================== Page Content ==================================== -->
    <div class="page-content">
        <div class="container-fluid">

            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">My Paid Participations - Leagues ({{ date('Y') }})</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item active">My Paid Participations - Leagues</li>
                            </ol>
                        </div>

                    </div>
                </div>
            </div>
            <!-- end page title -->

                @foreach($paids as $paid)
                    @if($paid->leagues)

                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body" style="text-align: center;">
                                        <h5>
                                            <span class="text-success"><b>{{ $paid->league_draw_status }} of Leagues</b><br>

                                                <div class="row">
                                                    <div class="col-3"></div>
                                                    <div class="col-6">
                                                        <div class="row">

                                                            <div class="col-lg-6"><p style="margin-top: 15px; font-size: 80%; margin-bottom: 0px;">Participation Code: <span style="font-weight:510;">{{ $paid->participation_code }}</span></p></div>
                                                            <div class="col-lg-6"><p style="margin-top: 15px; font-size: 80%; margin-bottom: 0px;">Paid League Fees: <span style="font-weight:510;">{{ $paid->league_fees }} €</span></p></div>
                                                            
                                                        </div>
                                                    </div>
                                                    <div class="col-3"></div>
                                                </div>
                                                
                                            </span>
                                            
                                        </h5> 
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-xl-12 text-center">
                                <h4 class="mb-sm-0 font-size-18">Leagues</h4>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-4"></div>
                            @foreach(json_decode($paid->leagues) as $single_league)
                                <?php $participants = []; ?>
                                <?php 
                                    $league = \App\Models\League::findOrFail($single_league);
                                    
                                    $statusclr = 'success';
                                    $btnsts = 'right';
                                    
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

                                <div class="col-lg-4">
                                    <div class="card border border-{{ $statusclr }}">
                                        <div class="card-header bg-transparent border-{{ $statusclr }}">
                                            <h5 class="my-0 text-{{ $statusclr }} text-center"><i class="mdi mdi-tennis me-2"></i>{{ $league->name }}</h5>

                                            <div class="d-flex justify-content-center" style="margin-top: 10px; margin-bottom: -15px;">
                                                <i class="bx bx-receipt bx-fade-{{ $btnsts }} text-{{ $statusclr }} display-4"></i>
                                            </div>
                                        </div>
                                        <div class="card-body" style="text-align: center;">
                                            <h5 class="card-title mt-0">{{ date('F y', strtotime($league->start)) }} - {{ date('F y', strtotime($league->end)) }}</h5>
                                            <h5 class="font-size-14"><i class="bx bx-money font-size-18 me-1" style="position: relative;top: 3px;"></i> Fees <i class="bx bx-right-arrow-alt font-size-16 text-primary align-middle ms-1 me-1"></i> {{ $league->fees }} €</h5>
                                            
                                            <span style="display: inline-flex; flex-direction: row; gap: 10px;">

                                                <button class="btn btn-light btn-sm waves-effect waves-light" style="margin-top: 6px;" id="getLeagPlayers" data-bs-toggle="modal" data-bs-target="#staticBackdropLeague" data-url="{{ route('get.league.players', $league->id) }}"><i class="bx bxs-group" style="position: relative;top: 0.67px;"></i> {{ count($participants) }} Participants</button>

                                                <button class="btn btn-outline-light btn-sm waves-effect waves-dark" style="margin-top: 6px;" id="getSupervisor" data-bs-toggle="modal" data-bs-target="#staticBackdropSu" data-url="{{ route('get.league.supervisor', $league->id) }}"><i class="bx bx-run" style="position: relative;top: 0.67px;"></i> Supervisor</button>
                                            </span>

                                        </div>
                                    
                                        <div class="card-footer" style="margin-top: -10px;">

                                            <a href="#" onclick='return confirm("Draw has not been performed yet. You will be notified by SMS and E-mail after the draw." )' style="width: 100%;" class="swalalert btn btn-success waves-effect waves-light">View Draw <i class="bx bx-right-arrow-circle bx-tada font-size-20 align-middle ms-1"></i></a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            <div class="col-lg-4"></div>
                                    
                        </div>

                        <br>

                    @else
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body" style="text-align: center;">
                                        <h5>

                                            <span class="text-danger"><b>No participation found for {{ $paid->league_draw_status }} of Leagues</b></span>
                                            
                                        </h5> 
                                    </div>
                                </div>
                            </div>
                        </div>

                    @endif
                @endforeach

            
        </div> <!-- container-fluid -->
    </div>
    <!-- End Page-content -->                
                
@endsection


@section('scripts')
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
