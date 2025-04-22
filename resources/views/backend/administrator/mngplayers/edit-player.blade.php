@extends('layouts.master')
@section('title', 'Update Player Profile')

@section('content')
    <!-- ========================== Page Content ==================================== -->
    <div class="page-content">
        <div class="container-fluid">

            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">Update Player Profile</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"> Dashboard </a></li>
                                <li class="breadcrumb-item active" style="color: #74788d;">Update Player Profile</li>
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

                            <h4 class="card-title text-center mb-4">Update Profile Information</h4>
                            <br>

                            <form class="needs-validation" action="{{ route('update.player', $player->id) }}" enctype="multipart/form-data" method="post" novalidate="">
                            @csrf

                                <div class="row">
                                    <div class="col-lg-5"></div>

                                    <div class="col-lg-2">
                                        <p>@if($player->profile_photo_path) Current @else Default @endif Profile Photo</p>
                                        <div class="zoom-gallery d-flex flex-wrap">
                                            @if($player->profile_photo_path)
                                                <a href="{{ asset('assets/uploads/users/'.$player->profile_photo_path) }}" title="{{ $player->profile_photo_path }}">
                                                    <img src="{{ asset('assets/uploads/users/'.$player->profile_photo_path) }}" alt="" style="height: 125px !important; width: 125px !important;" class="img-thumbnail rounded-circle">
                                                </a>
                                            @else
                                                <a href="{{ config('core.image.default.avatar') }}" title="No Profile Photo">
                                                    <img src="{{ config('core.image.default.avatar') }}" alt="" style="height: 125px !important; width: 125px !important;" class="img-thumbnail rounded-circle">
                                                </a>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-lg-5"></div>
                                </div>

                                <br><br><br>

                                <div class="row mb-4">
                                    <label for="validationTooltip100" class="col-lg-2 col-form-label">Change Profile Photo</label>
                                    
                                    <div class="col-lg-10" style="margin-bottom: 0.7rem!important;">
                                        <input type="file" class="form-control" id="validationTooltip100" name="profile_photo_path">                                        
                                    </div>
                                </div>

                                <div class="row mb-4">
                                    <label for="validationTooltip01" class="col-lg-2 col-form-label">Full Name</label>
                                    
                                    <div class="col-lg-10" style="margin-bottom: 0.7rem!important;">
                                        <input type="text" class="form-control" id="validationTooltip01" placeholder="Enter name" name="name" value="{{ old('name', $player->name) }}" required="">
                                        <div class="valid-tooltip">
                                            Looks good!
                                        </div>

                                        <div class="invalid-tooltip">
                                            Please enter full name.
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-4">
                                    <label for="validationTooltip02" class="col-lg-2 col-form-label">E-mail Address</label>
                                    
                                    <div class="col-lg-10" style="margin-bottom: 0.7rem!important;">
                                        <input type="email" class="form-control" id="validationTooltip02" placeholder="Enter email address" name="email" value="{{ old('email', $player->email) }}" required="">
                                        <div class="valid-tooltip">
                                            Looks good!
                                        </div>

                                        <div class="invalid-tooltip">
                                            Please enter valid email address.
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-4">
                                    <label for="validationTooltip12" class="col-lg-2 col-form-label">Gender</label>
                                    
                                    <div class="col-lg-10" style="margin-bottom: 0.7rem!important;">
                                        <select class="form-control select2" id="validationTooltip12" name="gender" required="" style="width: 100%;">
                                                        
                                            <option value="">Select Your Gender</option>

                                            <optgroup label="Genders List">
                                                <option value="Male" @if($player->gender == 'Male') selected @endif>Male</option>
                                                <option value="Female" @if($player->gender == 'Female') selected @endif>Female</option>
                                                <option value="Others" @if($player->gender == 'Others') selected @endif>Others</option>
                                            </optgroup>

                                        </select>

                                        <div class="valid-tooltip">
                                            Looks good!
                                        </div>

                                        <div class="invalid-tooltip">
                                            Please select gender.
                                        </div>
                                    </div>

                                </div>


                                <div class="row mb-4">
                                    <label for="datepicker2" class="col-lg-2 col-form-label">Date Of Birth</label>
                                    
                                    <div class="col-lg-10" style="margin-bottom: 0.7rem!important;">
                                        <div class="input-group" id="datepicker2">
                                            <input type="text" class="form-control" placeholder="Select Date Of Birth" data-date-format="dd M, yyyy" data-date-container='#datepicker2' data-provide="datepicker" data-date-autoclose="true" name="age" value="{{ old('age', $player->age) }}">
                                            <span class="input-group-text"><i class="mdi mdi-calendar"></i></span>
                                        </div>

                                        <div class="valid-tooltip">
                                            Looks good!
                                        </div>

                                        <div class="invalid-tooltip">
                                            Please select date of birth.
                                        </div>
                                    </div>

                                </div>


                                 <div class="row mb-4">
                                    <label for="validationTooltip10" class="col-lg-2 col-form-label">Phone (8 Digits)</label>
                                    
                                    <div class="col-lg-10" style="margin-bottom: 0.7rem!important;">
                                        <div class="row">
                                            <div class="col-2">
                                                <input type="tel" class="form-control" id="validationTooltip10" placeholder="+357" value="+357" disabled>
                                            </div>

                                            <div class="col-10">
                                                <input type="tel" class="form-control" id="validationTooltip10" placeholder="Enter phone number" pattern="[0-9]{8}" name="phone" value="{{ old('phone', str_replace('+357',"",$player->phone)) }}" required="">
                                            </div>
                                        </div>
                                            
                                        <div class="valid-tooltip">
                                            Looks good!
                                        </div>

                                        <div class="invalid-tooltip">
                                            Please enter valid phone number.
                                        </div>
                                    </div>
                                </div>
                                            
                                <div class="row justify-content-end">
                                    
                                    <div class="col-lg-10">
                                        
                                        <button class="btn btn-primary" style="margin-top: 8px!important; width: 100% !important" type="submit">Update Player Information</button>
                                        
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

        </div> <!-- container-fluid -->
    </div>
    <!-- End Page-content -->                
                
@endsection
