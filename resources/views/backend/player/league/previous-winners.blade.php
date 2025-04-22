@extends('layouts.master')
@section('title', 'Previous Winners - Leagues')

@section('content')

    <!-- ========================== Page Content ==================================== -->
    <div class="page-content">
        <div class="container-fluid">

            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">Previous Winners - Leagues</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item active">Previous Winners - Leagues</li>
                            </ol>
                        </div>

                    </div>
                </div>
            </div>
            <!-- end page title -->

            
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body text-center">

                            <form action="{{ route('previous.league.winners') }}" method="get">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <h6 class="mb-0" style="display: flex; gap: 10px; align-items: center;">
                                            <span style="min-width: 81px;">Select Year: </span>
                                            <select class="form-control select2" name="year" style="width: 100%;">
                                                <option value="2024" @if(request('year') == '2024') selected @endif>2024</option>
                                                <option value="2023" @if(request('year') == '2023') selected @endif>2023</option>
                                            </select>
                                        </h6>
                                    </div>

                                    <div class="col-sm-4 minStGap">
                                        <h6 class="mb-0" style="display: flex; gap: 10px; align-items: center;">
                                            <span style="min-width: 81px;">Select Draw: </span>
                                            <select class="form-control select2" name="draw" style="width: 100%;">
                                                <option value="All Draws" @if(request('draw') == 'All Draws') selected @endif>All Draws</option>
                                                <option value="1st Draw" @if(request('draw') == '1st Draw') selected @endif>1st Draw</option>
                                                <option value="2nd Draw" @if(request('draw') == '2nd Draw') selected @endif>2nd Draw</option>
                                                <option value="Top16 Finals" @if(request('draw') == 'Top16 Finals') selected @endif>Top16 Finals</option>
                                            </select>
                                        </h6>
                                    </div>

                                    <div class="col-sm-4 minStGap">
                                        <h6 class="mb-0" style="display: flex; gap: 10px; align-items: center;">
                                            <span class="minStWidth"></span>
                                            <button type="submit" class="btn btn-light waves-effect waves-light" style="width: 100%; border: 1px solid #e5e8ea;">
                                                <i class="bx bx-search-alt me-1" style="position: relative; top: 3px; font-size: 15px;"></i> Search
                                            </button>
                                        </h6>
                                    </div>

                                </div>
                            </form>

                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card">
                        <div class="card-body text-center">
                            <span style="text-align: center;font-weight: 500;font-size: 15px;"><mark><span style="font-weight: 350;">Showing result for:</span> @if(request('year') && request('draw')) {{ request('year') }} - {{ request('draw') }} @else {{ date('Y') }} - All Draws @endif</mark></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">

                @if($leagues->count() > 0) 
                    @foreach($leagues as $league)
                        <?php 
                            $statusclr = 'success';
                            $winnerArray = (array) json_decode($league->final_winners);
                            $winner_id = $winnerArray['match_1'];
                            $get_winner = App\Models\User::findOrFail($winner_id);
                        ?>
                        <div class="col-lg-3">
                            <div class="card border border-{{ $statusclr }}">
                                <div class="card-header bg-transparent border-{{ $statusclr }}">
                                    <h5 class="my-0 text-{{ $statusclr }} text-center"><i class="mdi mdi-tennis me-2"></i>{{ $league->name }}</h5>
                                    <div class="text-center text-{{ $statusclr }}"><small>{{ $league->draw_status }}</small></div>

                                    <div class="d-flex justify-content-center" style="margin-top: 10px; margin-bottom: -15px;">
                                        <i class="bx bx-trophy bx-fade-right text-{{ $statusclr }} display-4"></i>
                                    </div>
                                </div>

                                <div class="card-body" style="text-align: center;">
                                    <h6 class="mt-0" style="color: #5e7079;">{{ date('F y', strtotime($league->start)) }} - {{ date('F y', strtotime($league->end)) }}</h6>
                                    <h5 class="font-size-14 mb-1"><div class="round-details mt-2"><span class="date">Final Winner</span></div> <i class="bx bx-down-arrow-alt font-size-16 align-middle ms-1 me-1" style="color: #495057;position: relative;top: -3px;"></i> </h5>
                                    
                                    <span>                                                                       
                                        <a class="btn btn-outline-light btn-sm waves-effect waves-dark" style="color: #D07030 !important; font-weight:500;"><i class="bx bx-run" style="position: relative;top: 0.67px;"></i> {{ $get_winner->name }}</a>
                                    </span>

                                </div>
                            
                                <div class="card-footer" style="margin-top: -10px;">

                                    <a href="{{ route('view.league.tree', $league->id) }}" style="width: 100%;" class="btn btn-{{ $statusclr }} waves-effect waves-light"> League Draw <i class="bx bx-right-arrow-circle bx-tada font-size-20 align-middle ms-1"></i></a>
                                    
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="col-12 text-center">
                        <div class="mt-auto">
                            <div class="alert alert-danger fade show px-3 mb-0" role="alert">
                                <div class="mb-3">
                                    <i class="mdi mdi-qrcode-scan h1 text-danger"></i>
                                </div>

                                <div>
                                    <h5 class="text-danger">Not Available!</h5>
                                    <p><span style="font-weight: 500;">{{ request('draw') ? 'Previous winner for the '.request('draw') : 'Previous winner' }}</span> of league is not found for the year <span style="font-weight: 500;">{{ request('year') ? request('year') : date('Y') }}</span> till now !</p>                                        
                                </div>
                            </div>                                
                        </div>
                    </div>
                @endif   

            </div>
            <br>
            
            
        </div> <!-- container-fluid -->
    </div>
    <!-- End Page-content -->                
                
@endsection


@section('styles')
    <style type="text/css">
        @media screen and (max-width: 575px) {
            .minStGap {
                margin-top: 15px !important;
            }

            .minStWidth {
                min-width: 81px;
            }
        }

        .round-details {
            font-size: 13px;
            color: #2c7399;
            text-transform: uppercase;
            text-align: center;
            height: 20px;
        }

        .date {
            font-size: 10px;
            letter-spacing: 1px;
            color: #5e7079;
        }
    </style>
@endsection
