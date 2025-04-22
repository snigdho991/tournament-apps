@extends('layouts.master')
@section('title', 'Administrator - Declined Membership')

@section('content')

    
    <!-- ========================== Page Content ==================================== -->
    <div class="page-content">
        <div class="container-fluid">

            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">Declined Membership (<?php echo $declined->count(); ?>)</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item active">Declined Membership</li>
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
                                <span class="text-success"><b> Administrator - Declined Membership (<?php echo $declined->count(); ?>)</b></span>
                                
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
                                        <th>Membership Code</th>
                                        <th>Player</th>
                                        <th>Phone</th>
                                        <th>Fees</th>
                                        <th style="text-align: center;">Action</th>
                                        <th>Decline Reason</th>
                                        <th>Sent At</th>
                                    </tr>
                                </thead>


                                <tbody>
                                    @foreach($declined as $key => $data)
                                        <?php
                                            $user = \App\models\User::findOrFail($data->user_id);
                                        ?>
                                        <tr>
                                            <td><span style="margin-left: 3px;">{{ $key + 1 }}</span></td>
                                            
                                            <td>
                                                <span style="font-weight: 420;">{{ $data->membership_code }}</span>
                                                <span class="CellWithComment">
                                                    <i class="mdi mdi-arrow-down-bold-circle-outline ms-1 text-danger" style="position: relative; top: -4px; cursor: pointer; font-size: 18px; float: right;"></i>
                                                    <span class="CellComment" style="background-color:#f46a6a !important;">Declined </span>
                                                </span>
                                            </td>

                                            <td>{{ $user->name }}</td>
                                            <td>{{ $user->phone }}</td>
                                            <td><span style="font-weight: 500;">120 €</span></td>
                                            
                                            <td style="text-align: center;">
                                                <span style="display: inline-flex; flex-direction: row; gap: 15px;">
                                                    
                                                    <form action="{{ route('delete.membership', $data->id) }}" method="POST">
                                                        @csrf

                                                        <button class="btn btn-danger btn-sm waves-effect btn-label waves-light" onclick="return confirm('If you delete the membership, this player can be able to request again for this year. Are you sure to delete this membership request ?');"> 
                                                            <i class="bx bx-x-circle label-icon bx-tada"></i> Delete
                                                        </button>
                                                    </form>

                                                </span>                                                        
                                            </td>

                                            <td>{{ $data->decline_reason }}</td>

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
