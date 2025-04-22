@extends('layouts.master')
@section('title', 'Administrator - Paid Tournament 2nd Draw Participations')

@section('content')

    <!-- Static Backdrop Modal -->
    <div class="modal fade" id="staticBackdropD" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title text-center" id="staticBackdropLabel">Tournament Details</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <h5 class="deal-title"></h5>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" style="margin: auto !important;" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade declineModal" tabindex="-1" role="dialog" aria-labelledby="transaction-detailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                <h5 class="modal-title">Decline Participation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <form class="needs-validation" action="" method="post" id="decline" novalidate="">
                        @csrf

                        <div class="mb-3">
                            
                            <label for="validationTooltip04" class="form-label">Decline Reason</label>
                            <textarea class="form-control" id="validationTooltip04" placeholder="Enter Decline Reason" name="decline_reason" rows="2" required="">{{ old('decline_reason') }}</textarea>
                    
                        </div>

                        <button class="btn btn-danger waves-effect btn-label waves-light" onclick="return confirm('Are you sure to decline this already approved tournament participation ?');" style="margin-top: 6px !important; width: 100% !important" type="submit"> Decline Participation</button>

                    </form>

                </div>

                <div class="modal-footer">
                    <button type="button" style="margin: auto !important;" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade changeModal" tabindex="-1" role="dialog" aria-labelledby="transaction-changeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                <h5 class="modal-title">Change Tournaments</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <form class="needs-validation" action="" method="post" id="changemodal" novalidate="">
                        @csrf

                            <div class="row">

                                <div class="col-md-5 mt-3">
                                    <div class="mb-3 position-relative">
                                        <label for="validationTooltip14" class="form-label" style="font-size: 16px;">
                                            Choose Tournaments
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="col-md-7 mt-3">
                                    <div class="mb-3 position-relative">    
                                   
                                        @foreach(\App\Models\Tournament::where('status', 'On')->whereYear('start', date('Y'))->get() as $key => $tournament)

                                            <div class="form-check form-radio-success mb-3" style="font-size: 17px;">
                                                <input class="form-check-input" name="tournaments[]" type="checkbox" onchange="tournamentCheck()" data-price="{{ $tournament->fees }}" data-twotour="{{ \App\Models\Settings::findOrFail(1)->two_tournament_fees }}" id="formCheckcolor{{ $key + 1 }}" value="{{ $tournament->id }}">
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

                            </div>

                        <button class="btn btn-primary waves-effect btn-label waves-light" onclick="return confirm('Are you sure to change the tournaments participation ?');" style="margin-top: 6px !important; width: 100% !important" type="submit">Update Tournaments</button>

                    </form>

                </div>

                <div class="modal-footer">
                    <button type="button" style="margin: auto !important;" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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
                        <h4 class="mb-sm-0 font-size-18">Paid Tournament 2nd Draw Participations (<?php echo $paid->count(); ?>)</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item active">Paid Tournament 2nd Draw Participations(Administrator)</li>
                            </ol>
                        </div>

                    </div>
                </div>
            </div>
            <!-- end page title -->

            @if(count($errors) > 0)
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="alert alert-dismissible fade show color-box bg-danger bg-gradient p-4" role="alert">
                                    <x-jet-validation-errors class="mb-4 my-2 text-white" />
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body" style="text-align: center;">
                            <h5>
                                <span class="text-success"><b> Administrator - Paid Tournament 2nd Draw Participations (<?php echo $paid->count(); ?>)</b><br><p style="margin-top: 7px; font-size: 80%; margin-bottom: 0px;">Total Paid: {{ $amount }} €</p></span>    

                                <h6 class="mb-0"> <a href="{{ route('admin.paid.tournaments.participations') }}">All Draws</a> || <a href="{{ route('admin.paid.tournaments.participations.first') }}">1st Draw</a> || <a href="{{ route('admin.paid.tournaments.participations.second') }}">2nd Draw</a> || <a href="{{ route('admin.paid.tournaments.participations.third') }}">3rd Draw</a> || <a href="{{ route('admin.paid.tournaments.participations.fourth') }}">4th Draw</a> </h6>
                            </h5> 
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">

                            <table id="datatable" class="table table-bordered dt-responsive nowrap w-100">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Participation Code</th>
                                        <th>Player</th>
                                        <th>Phone</th>
                                        <th style="text-align: center;">Tournaments</th>
                                        <th style="text-align: center;">Change Tour.</th>
                                        <th>Tour. Fees</th>
                                        <th>Type</th>
                                        <th style="text-align: center;">Action</th>
                                        <th>Payment Info</th>
                                        <th>Sent At</th>

                                    </tr>
                                </thead>


                                <tbody>
                                    @foreach($paid as $key => $data)
                                        <?php
                                            $tournaments = json_decode($data->tournaments);
                                            $player = \App\models\User::findOrFail($data->user_id);
                                        ?>
                                        <tr>
                                            <td><span style="margin-left: 3px;">{{ $key + 1 }}</span></td>
                                            
                                            <td>
                                                <span style="font-weight: 420;">{{ $data->participation_code }}</span>
                                                <span class="CellWithComment">
                                                    <i class="mdi mdi-arrow-up-bold-circle-outline ms-1 text-success" style="position: relative; top: -4px; cursor: pointer; font-size: 18px; float: right;"></i>
                                                    <span class="CellComment" style="background-color:#34c38f !important;">Paid</span>
                                                </span>
                                            </td>

                                            <td>{{ $player->name }}</td>
                                            <td>{{ $player->phone }}</td>

                                            <td class="text-center">
                                                <button class="btn btn-outline-dark btn-rounded btn-sm waves-effect waves-light" id="getTournament" data-bs-toggle="modal" data-bs-target="#staticBackdropD" data-url="{{ route('get.single.tournament', $data->id) }}">
                                                     View 
                                                    <i class="bx bxs-right-arrow" style="position: relative; top: 1px;"></i>
                                                </button>
                                            </td>

                                            <td class="text-center">
                                                <button class="btn btn-light btn-sm waves-effect waves-light" data-bs-toggle="modal" data-bs-target=".changeModal" data-changeid="{{ $data->id }}"> Change <i class="bx bx-right-arrow-circle font-size-18 align-middle ms-1"></i></button>
                                            </td>

                                            <td><span style="font-weight: 500;">{{ $data->tournament_fees }} €</span></td>
                                            <td>{{ $data->draw_status }}</td>
                                            <td style="text-align: center;">
                                                <span style="display: inline-flex; flex-direction: row; gap: 15px;">
                                                    
                                                    <button type="button" class="btn btn-danger btn-sm waves-effect btn-label waves-light" data-bs-toggle="modal" data-partid="{{ $data->id }}" data-bs-target=".declineModal"> 
                                                        <i class="bx bx-x-circle label-icon bx-tada"></i> Decline
                                                    </button>

                                                </span>                                                        
                                            </td>
                                            
                                            <td>{{ $data->payment_info }}</td>
                                            <td>{{ $data->created_at->diffForHumans() }}</td>

                                        </tr>
                                    @endforeach
                                </tbody>

                            </table>

                        </div>
                    </div>
                </div> <!-- end col -->
            </div> <!-- end row -->

            <p style="text-align: center; margin-top: 10px;"><a class="btn btn-primary waves-effect waves-light" href="{{ route('admin.dashboard') }}"><i class="far fa-arrow-alt-circle-left"></i> Go To Dashboard </a></p>
            <br>

        </div> <!-- container-fluid -->
    </div>
    <!-- End Page-content -->                
                
@endsection


@section('styles')
    <style type="text/css">

        .table.dataTable.dtr-inline.collapsed>tbody>tr>td.dtr-control:before, table.dataTable.dtr-inline.collapsed>tbody>tr>td.dtr-control:before {
            margin-top: -14px !important;
        }

        .CellWithComment{
            position:relative;
        }

        .CellComment{
            display: none;
            position: absolute; 
            z-index: 100;
            padding: .25em .4em;
            font-size: 75%;
            font-weight: 500;
            line-height: 1;
            color: #fff;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: .25rem;
        }

        .CellWithComment:hover span.CellComment{
            display:block;
        }

        .form-control:disabled, .form-control[readonly] {
            color: #000 !important;
            background: rgb(142 147 168 / 25%)!important;
        }
    </style>
@endsection


@section('scripts')
    <script>
        $(document).ready(function(){

            $(document).on('click', '#getTournament', function(e){

                e.preventDefault();

                var url = $(this).data('url');

                $.ajax({
                    url: url,
                    type: 'GET',
                })
                .done(function(data){

                    $('.deal-title').html(data);

                })
                .fail(function(){
                    alert('Something went wrong!')
                });

            });

        });

    </script>

    <script type="text/javascript">
        
        $('.declineModal').on('show.bs.modal', function(e) {
            var link           = $(e.relatedTarget),
                partid         = link.data('partid'),
                modal          = $(this);
        
                document.getElementById("decline").action = "/administrator/decline/tournament/participation/" + partid;
            
        });
    </script>

    <script type="text/javascript">
        
        $('.changeModal').on('show.bs.modal', function(e) {
            var link           = $(e.relatedTarget),
                changeid       = link.data('changeid'),
                modal          = $(this);
        
                document.getElementById("changemodal").action = "/administrator/change/tournament/" + changeid;
            
        });
    </script>

    <script type="text/javascript">
        var tour;
            
        function tournamentCheck() {

            var get_length = $('input[name="tournaments[]"]:checked').length;
            
            var price_array = [];
            var twotour;
            $('input[name="tournaments[]"]:checked').each(function() {
                price_array.push($(this).data('price'));
                twotour = $(this).data('twotour');
            });

            const sum = price_array.reduce((partialSum, a) => partialSum + a, 0);

            if (get_length == 2) {
                tour = twotour;
            } else if (get_length == 0) {
                tour = 0;
            } else {
                tour = sum;
            }

            if (get_length > 1) {
                $("input[name='tournaments[]']:checkbox:not(:checked)").prop("disabled", true);                    
            } else {
                $("input[name='tournaments[]']").prop("disabled", false); 
            }

            document.getElementById("feesInputValueTour").value = tour;
            document.getElementById("feesValueTour").innerHTML = tour + ' €';

        }

    </script>
    
@endsection
