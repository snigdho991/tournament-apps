@extends('layouts.master')
@section('title', 'Update League')

@section('content')

    <div class="page-content">
        <div class="container-fluid">

            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">Update League</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard </a></li>
                                <li class="breadcrumb-item active" style="color: #74788d;">Update League</li>
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

                            <form class="needs-validation" action="{{ route('update.league', $league->id) }}" method="post" novalidate="">
                            @csrf

                                <div class="row">

                                    <div class="col-xl-6">
                                        <div class="mb-3 position-relative">
                                            <label for="" class="form-label">League Start Month</label>
                                                                                        
                                            <div class="input-group" id="datepicker4">
                                                <input type="text" name="start" value="{{ date('F Y', strtotime($league->start)) }}" placeholder="Select league start month" class="form-control" data-date-container='#datepicker4' data-provide="datepicker" data-date-format="MM yyyy" data-date-min-view-mode="1" required>
                                                <span class="input-group-text"><i class="mdi mdi-calendar"></i></span>

                                                <div class="valid-tooltip">
                                                    Looks good!
                                                </div>

                                                <div class="invalid-tooltip">
                                                    Please select league start month.
                                                </div>
                                            </div>                                            

                                        </div>
                                    </div>

                                    <br>

                                    <div class="col-xl-6">

                                        <div class="mb-3 position-relative">                                            
                                            <label for="" class="form-label">League End Month</label>
                                            
                                            <div class="input-group" id="datepicker4">
                                                <input type="text" name="end" value="{{ date('F Y', strtotime($league->end)) }}" placeholder="Select league end month" class="form-control" data-date-container='#datepicker4' data-provide="datepicker" data-date-format="MM yyyy" data-date-min-view-mode="1" required>
                                                <span class="input-group-text"><i class="mdi mdi-calendar"></i></span>

                                                <div class="valid-tooltip">
                                                    Looks good!
                                                </div>

                                                <div class="invalid-tooltip">
                                                    Please select league end month.
                                                </div>
                                            </div>  

                                        </div>
                                    </div>

                                </div>

                                <br>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3 position-relative">
                                            <label for="name" class="form-label">League Name</label>

                                            <input type="text" name="name" value="{{ $league->name }}" class="form-control" id="name" placeholder="Enter league name" required="">

                                            <div class="valid-tooltip">
                                                Looks good!
                                            </div>

                                            <div class="invalid-tooltip">
                                                Please enter league name.
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3 position-relative">
                                            <label for="fees" class="form-label">League Fees (€)</label>

                                            <input type="number" name="fees" value="{{ $league->fees }}" step="0.01" class="form-control" id="fees" placeholder="Enter league fees" required="">

                                            <div class="valid-tooltip">
                                                Looks good!
                                            </div>

                                            <div class="invalid-tooltip">
                                                Please enter league fees.
                                            </div>
                                        </div>
                                    </div> 

                                </div>

                                <br>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3 position-relative">
                                            <label for="supervisor_name" class="form-label">Supervisor Name</label>

                                            <input type="text" name="supervisor_name" value="{{ old('supervisor_name', $league->supervisor_name) }}" class="form-control" id="supervisor_name" placeholder="Enter supervisor name" required="">

                                            <div class="valid-tooltip">
                                                Looks good!
                                            </div>

                                            <div class="invalid-tooltip">
                                                Please enter supervisor name.
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3 position-relative">
                                            <label for="supervisor_phone" class="form-label">Supervisor Phone</label>

                                            <input type="tel" class="form-control" id="supervisor_phone" placeholder="Enter supervisor phone number" pattern="[0-9+]{5,20}" name="supervisor_phone" value="{{ old('supervisor_phone', $league->supervisor_phone) }}" required="">

                                            <div class="valid-tooltip">
                                                Looks good!
                                            </div>

                                            <div class="invalid-tooltip">
                                                Please enter supervisor valid phone number.
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <br>

                                <div class="row">
                                    <div class="col-md-5"></div>
                                    <div class="col-md-2">
                                        <div class="mb-3 position-relative">
                                            <label for="status" class="form-label">League Registration</label>

                                            <div class="switch">
                                                <input type="checkbox" name="status" id="switch3" switch="bool" @if($league->status == 'On') checked="" @endif>
                                                <label for="switch3" data-on-label="Yes" data-off-label="No" style="margin-left: 55px;"></label>
                                            </div>

                                            <div class="valid-tooltip">
                                                Looks good!
                                            </div>

                                            <div class="invalid-tooltip">
                                                Please select league status.
                                            </div>
                                        </div>
                                    </div> 
                                    <div class="col-md-5"></div>
                                </div>

                                
                                <div class="row">
                                    
                                    <div class="col-md-12">
                                        
                                        <button class="btn btn-primary" style="margin-top: 6px !important; width: 100% !important" type="submit">Update League</button>
                                        
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