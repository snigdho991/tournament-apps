@extends('layouts.master')
@section('title', 'Head to Head')

@section('content')

    <!-- ========================== Page Content ==================================== -->
    <div class="page-content">
        <div class="container-fluid">

            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">Head to Head</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item active">Head to Head</li>
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

                            <form action="{{ route('head.to.head.find') }}" method="get">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <h6 class="mb-0" style="display: flex; gap: 10px; align-items: center;">
                                            <select class="form-control select2" name="player_1" style="width: 100%;">
                                                    <option value="">Player 1</option>
                                                @foreach($players as $player)
                                                    <option value="{{ $player->id }}" 
                                                        
                                                            @if($player->id == old('player_1'))
                                                                selected
                                                            @endif
                                                        
                                                    >
                                                        {{ $player->name }}
                                                    </option>                                                
                                                @endforeach
                                            </select>
                                        </h6>
                                    </div>

                                    <div class="col-sm-1 text-center" style="margin-top: 10px !important;margin: 0 auto;"><span class="badge bg-secondary">VS</span> </div>

                                    <div class="col-sm-4 minStGap">
                                        <h6 class="mb-0" style="display: flex; gap: 10px; align-items: center;">
                                            <select class="form-control select2" name="player_2" style="width: 100%;">
                                                    <option value="">Player 2</option>
                                                @foreach($players as $player)
                                                    <option value="{{ $player->id }}" 
                                                        
                                                            @if($player->id == old('player_2'))
                                                                selected
                                                            @endif
                                                    >
                                                        {{ $player->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </h6>
                                    </div>

                                    <div class="col-sm-3 minStGap">
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

                <div class="col-12 text-center">
                    <div class="mt-auto">
                        <div class="alert alert-danger fade show px-3 mb-0" role="alert">
                            <div class="mb-3">
                                <i class="mdi mdi-qrcode-scan h1 text-danger"></i>
                            </div>

                            <div>
                                <h5 class="text-danger">Not Available!</h5>
                                <p><span style="font-weight: 500;">{{ request('draw') ? 'Previous winner for the '.request('draw') : 'Previous winner' }}</span> of tournament is not found for the year <span style="font-weight: 500;">{{ request('year') ? request('year') : date('Y') }}</span> till now !</p>                                        
                            </div>
                        </div>                                
                    </div>
                </div>
                        
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
