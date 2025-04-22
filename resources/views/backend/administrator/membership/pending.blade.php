@extends('layouts.master')
@section('title', 'Administrator - Pending Membership')

@section('content')

    <div class="modal fade approveModal" tabindex="-1" role="dialog" aria-labelledby="transaction-detailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                <h5 class="modal-title">Approve Membership</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <form class="needs-validation" action="" method="post" id="approve" novalidate="">
                        @csrf

                        <div class="mb-3">
                            
                            <label for="validationTooltip02" class="form-label">Payment Info</label>
                            <textarea class="form-control" id="validationTooltip02" placeholder="Enter Payment Information" name="payment_info" rows="2" required="">{{ old('payment_info') }}</textarea>
                    
                        </div>

                        <button class="btn btn-success waves-effect btn-label waves-light" onclick="return confirm('Are you sure to  approve the membership ?');" style="margin-top: 6px !important; width: 100% !important" type="submit"> Approve Membership</button>

                    </form>

                </div>

                <div class="modal-footer">
                    <button type="button" style="margin: auto !important;" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade declineModal" tabindex="-1" role="dialog" aria-labelledby="transaction-detailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                <h5 class="modal-title">Decline Membership</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <form class="needs-validation" action="" method="post" id="decline" novalidate="">
                        @csrf

                        <div class="mb-3">
                            
                            <label for="validationTooltip04" class="form-label">Decline Reason</label>
                            <textarea class="form-control" id="validationTooltip04" placeholder="Enter Decline Reason" name="decline_reason" rows="2" required="">{{ old('decline_reason') }}</textarea>
                    
                        </div>

                        <button class="btn btn-danger waves-effect btn-label waves-light" onclick="return confirm('Are you sure to  decline the membership ?');" style="margin-top: 6px !important; width: 100% !important" type="submit"> Decline Membership</button>

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
                        <h4 class="mb-sm-0 font-size-18">Pending Membership (<?php echo $pending->count(); ?>)</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item active">Pending Membership</li>
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
                                <span class="text-success"><b> Administrator - Pending Membership (<?php echo $pending->count(); ?>)</b></span>
                                
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
                                        <th>Sent At</th>
                                        <th style="text-align: center;">Action</th>

                                    </tr>
                                </thead>


                                <tbody>
                                    @foreach($pending as $key => $data)
                                        <?php
                                            $user = \App\models\User::findOrFail($data->user_id);
                                        ?>
                                        <tr>
                                            <td><span style="margin-left: 3px;">{{ $key + 1 }}</span></td>
                                            
                                            <td>
                                                <span style="font-weight: 420;">{{ $data->membership_code }}</span>
                                                <span class="CellWithComment">
                                                    <i class="mdi mdi-arrow-left-bold-circle-outline ms-1 text-primary" style="position: relative; top: -4px; cursor: pointer; font-size: 18px; float: right;"></i>
                                                    <span class="CellComment" style="background-color:#0275d8 !important;">Pending</span>
                                                </span>
                                            </td>

                                            <td>{{ $user->name }}</td>
                                            <td>{{ $user->phone }}</td>
                                            <td><span style="font-weight: 500;">120 €</span></td>

                                            <td>{{ $data->created_at->diffForHumans() }}</td>

                                            <td style="text-align: center;">
                                                <span style="display: inline-flex; flex-direction: row; gap: 15px;">
                                                    
                                                    <button type="button" class="btn btn-success btn-sm waves-effect btn-label waves-light" data-bs-toggle="modal" data-memid="{{ $data->id }}" data-bs-target=".approveModal">
                                                        <i class="bx bx-check-double label-icon bx-tada"></i> Approve
                                                    </button>
                                                
                                                    <button type="button" class="btn btn-danger btn-sm waves-effect btn-label waves-light" data-bs-toggle="modal" data-memid="{{ $data->id }}" data-bs-target=".declineModal"> 
                                                        <i class="bx bx-x-circle label-icon bx-tada"></i> Decline
                                                    </button>

                                                </span>                                                        
                                            </td>

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

    <script type="text/javascript">
        
        $('.approveModal').on('show.bs.modal', function(e) {
            var link           = $(e.relatedTarget),
                memid         = link.data('memid'),
                modal          = $(this);
        
                document.getElementById("approve").action = "/administrator/approve/membership/" + memid;
            
        });
    </script>

    <script type="text/javascript">
        
        $('.declineModal').on('show.bs.modal', function(e) {
            var link           = $(e.relatedTarget),
                memid         = link.data('memid'),
                modal          = $(this);
        
                document.getElementById("decline").action = "/administrator/decline/membership/" + memid;
            
        });
    </script>

@endsection
