@extends('layouts.master')
@section('title', 'Tournament Draw')

@section('content')

    <div class="page-content">
        <div class="container-fluid">

            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">Tournament Draw ({{ count($players) }} Players)</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard </a></li>
                                <li class="breadcrumb-item active" style="color: #74788d;">Tournament Draw</li>
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

                            <form class="needs-validation" action="{{ route('tree.tournament', $tournament->id) }}" method="post" novalidate="">
                            @csrf

                                <div class="row">

                                    <div class="col-xl-6">
                                        <div class="mb-3 position-relative">
                                            <label for="validationTooltip01" class="form-label">Number of Players</label>
                                                                                        
                                            <select class="form-control select2" id="validationTooltip01" name="tree_size" required="">
                                    
                                                <option value="">Select Number of Players</option>
                                                
                                                <option @if($tournament->tree_size == '8') selected @endif value="8">8 Players</option>
                                                <option @if($tournament->tree_size == '16') selected @endif value="16">16 Players</option>
                                                <option @if($tournament->tree_size == '32') selected @endif value="32">32 Players</option>

                                                <option @if($tournament->tree_size == '64') selected @endif value="64">64 Players</option>
                                            
                                            </select>

                                            <div class="valid-tooltip">
                                                Looks good!
                                            </div>

                                            <div class="invalid-tooltip">
                                                Please select number of players.
                                            </div>                                          

                                        </div>
                                    </div>

                                   
                                    <div class="col-xl-6">

                                        <div class="mb-3 position-relative">                                            
                                            <label for="" class="form-label"></label>
                                            
                                            <button class="btn btn-primary" style="margin-top: 6px !important; width: 100% !important" type="submit">Generate Draw Tree</button>

                                        </div>
                                    </div>

                                </div>

                            </form>

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
