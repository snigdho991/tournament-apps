@extends('layouts.master')
@section('title', 'All Tournaments')

@section('content')
    
    <div class="modal fade" id="staticBackdropD" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title text-center" id="staticBackdropLabel">Participating Players (Tournament)</h4>
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
                        <h4 class="mb-sm-0 font-size-18">All Tournaments</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item active">All Tournaments</li>
                            </ol>
                        </div>

                    </div>
                </div>
            </div>
            <!-- end page title -->

            @foreach($tournaments as $key => $all_tournaments)
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body text-center">
                                <span style="text-align: center;font-weight: 500;font-size: 15px;"><mark>Year: {{ $key }}</mark></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    
                    @foreach($all_tournaments as $tournament)
                        <?php $participants = []; ?>
                        <?php 
                            if($tournament->status == 'On') {
                                $statusclr = 'success';
                                $btnsts = 'right';
                            } else if($tournament->status == 'Off') {
                                $statusclr = 'danger';
                                $btnsts = 'left';
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
                                    
                                        <button class="btn btn-light btn-sm waves-effect waves-light" style="margin-top: 6px;" id="getTourPlayers" data-bs-toggle="modal" data-bs-target="#staticBackdropD" data-url="{{ route('get.tournament.players', $tournament->id) }}"><i class="bx bxs-group" style="position: relative;top: 0.67px;"></i> {{ count($participants) }} Participants</button>
                                    
                                        <button class="btn btn-outline-light btn-sm waves-effect waves-dark" style="margin-top: 6px;" id="getSupervisor" data-bs-toggle="modal" data-bs-target="#staticBackdropSu" data-url="{{ route('get.tournament.supervisor', $tournament->id) }}"><i class="bx bx-run" style="position: relative;top: 0.67px;"></i> Supervisor</button>
                                    </span>

                                    <ul class="list-inline mb-0 font-size-16" style="margin-top: 25px;">
                                        <li class="list-inline-item">
                                            <a href="{{ route('edit.tournament', $tournament->id) }}" class="text-success p-1"><i class="bx bxs-edit-alt"></i></a>
                                        </li>
                                        <li class="list-inline-item">
                                            <a href="{{ route('delete.tournament', $tournament->id) }}" onclick="return confirm('Are you sure to delete permanently?')" class="text-danger p-1"><i class="bx bxs-trash"></i></a>
                                        </li>
                                    </ul>

                                </div>
                            
                                <div class="card-footer" style="margin-top: -10px;">

                                    <a href="
                                        @if ((strpos($tournament->name, "ELITE1500") !== false) && (date('Y', strtotime($tournament->start)) == '2023'))
                                            @if($tournament->tree_size == '8')  
                                                {{ route('draw.tournament.eight.players', $tournament->id) }} 
                                            @elseif($tournament->tree_size == '16') 
                                                {{ route('draw.tournament.sixteen.players', $tournament->id) }} 
                                            @elseif($tournament->tree_size == '32') 
                                                {{ route('draw.tournament.thirtytwo.players', $tournament->id) }} 
                                            @else 
                                                {{ route('draw.tournament.sixtyfour.players', $tournament->id) }} 
                                            @endif 
                                        @elseif (strpos($tournament->name, "ELITE1500") !== false && (date('Y', strtotime($tournament->start)) !== '2023'))
                                            {{ route('group.tournament', $tournament->id) }}
                                        @else
                                            @if($tournament->tree_size) 
                                                @if($tournament->tree_size == '8')  {{ route('draw.tournament.eight.players', $tournament->id) }} 
                                                    @elseif($tournament->tree_size == '16') {{ route('draw.tournament.sixteen.players', $tournament->id) }} 
                                                    @elseif($tournament->tree_size == '32') {{ route('draw.tournament.thirtytwo.players', $tournament->id) }} 
                                                @else {{ route('draw.tournament.sixtyfour.players', $tournament->id) }} 
                                                @endif 
                                            @else 
                                                {{ route('draw.tournament', $tournament->id) }} 
                                            @endif
                                            
                                        @endif" 
                                        
                                        style="width: 100%;" class="btn btn-{{ $statusclr }} waves-effect waves-light">@if($tournament->tree_size) Tournament Draw @else Draw Tournament @endif<i class="bx bx-right-arrow-circle bx-tada font-size-20 align-middle ms-1"></i></a>
                                    
                                </div>
                            </div>
                        </div>
                    @endforeach
                            
                </div>
                <br>
            @endforeach

            
        </div> <!-- container-fluid -->
    </div>
    <!-- End Page-content -->                
                
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
