@extends('layouts.master')
@section('title', 'Settings')

@section('content')

    <div class="page-content">
        <div class="container-fluid">

            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">Settings</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard </a></li>
                                <li class="breadcrumb-item active" style="color: #74788d;">Settings</li>
                            </ol>
                        </div>

                    </div>
                </div>
            </div>
            <!-- end page title -->

            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">

                            <form class="needs-validation" action="{{ route('update.settings') }}" method="post" novalidate="">
                            @csrf

                                <div class="row">

                                    <div class="col-6">
                                        <div class="mb-3 position-relative">
                                            <label for="two_tournament_fees" class="form-label">Two Tournament Fees (€)</label>

                                            <input type="number" name="two_tournament_fees" value="{{ old('two_tournament_fees', $settings->two_tournament_fees) }}" step="0.01" class="form-control" id="two_tournament_fees" placeholder="Enter two tournament fees" required="">

                                            <div class="valid-tooltip">
                                                Looks good!
                                            </div>

                                            <div class="invalid-tooltip">
                                                Please enter two tournament fees.
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-6">
                                        <div class="mb-3 position-relative">
                                            <label for="validationTooltip12" class="form-label">Tournaments Open For</label>

                                            <select class="form-control select2" id="validationTooltip12" name="tournaments_open_for" required="">
                                                    
                                                <option value="">Select Draw Type</option>

                                                <option value="1st Draw" @if($settings->tournaments_open_for == '1st Draw') selected @endif>1st Draw</option>
                                                <option value="2nd Draw" @if($settings->tournaments_open_for == '2nd Draw') selected @endif>2nd Draw</option>
                                                <option value="3rd Draw" @if($settings->tournaments_open_for == '3rd Draw') selected @endif>3rd Draw</option>
                                                <option value="4th Draw" @if($settings->tournaments_open_for == '4th Draw') selected @endif>4th Draw</option>
                                                
                                            </select>

                                            <div class="valid-tooltip">
                                                Looks good!
                                            </div>

                                            <div class="invalid-tooltip">
                                                Please select tournaments draw type.
                                            </div>

                                        </div>
                                    </div>

                                </div>

                                <br>

                                <div class="row">
                                    <div class="col-6">
                                        <div class="mb-3 position-relative">
                                            <label for="validationTooltip14" class="form-label">Leagues Open For</label>

                                            <select class="form-control select2" id="validationTooltip14" name="leagues_open_for" required="">
                                                    
                                                <option value="">Select Draw Type</option>

                                                <option value="1st Draw" @if($settings->leagues_open_for == '1st Draw') selected @endif>1st Draw</option>
                                                <option value="2nd Draw" @if($settings->leagues_open_for == '2nd Draw') selected @endif>2nd Draw</option>
                                                <option value="Top16 Finals" @if($settings->leagues_open_for == 'Top16 Finals') selected @endif>Top16 Finals</option>
                                                
                                            </select>

                                            <div class="valid-tooltip">
                                                Looks good!
                                            </div>

                                            <div class="invalid-tooltip">
                                                Please select leagues draw type.
                                            </div>

                                        </div>
                                    </div>

                                    <div class="col-6">
                                        <div class="mb-3 position-relative">
                                            <label for="validationTooltip25" class="form-label">Ranking Publish Button</label>

                                            <select class="form-control select2" id="validationTooltip25" name="publish_button_status" required="">
                                                    
                                                <option value="">Select Button Status</option>

                                                <option value="Locked" @if($settings->publish_button_status == 'Locked') selected @endif>Locked</option>
                                                <option value="Unlocked" @if($settings->publish_button_status == 'Unlocked') selected @endif>Unlocked</option>
                                                
                                            </select>

                                            <div class="valid-tooltip">
                                                Looks good!
                                            </div>

                                            <div class="invalid-tooltip">
                                                Please select publish button status.
                                            </div>

                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12">

                                        <div class="mb-3 position-relative">                                            
                                            <label for="" class="form-label"></label>
                                            
                                            <button class="btn btn-primary" style="width: 100% !important" type="submit">Save Settings</button>

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
