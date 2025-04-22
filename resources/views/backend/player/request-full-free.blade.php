@extends('layouts.master')
@section('title', 'Request For Full Membership')

@section('content')

    <div class="page-content">
        <div class="container-fluid">

            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">Request For Full Membership</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard </a></li>
                                <li class="breadcrumb-item active" style="color: #74788d;">Request For Full Membership</li>
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

                            @if($find)
                                @if($find->status == 'Pending')
                                    <p class="font-size-14 text-info text-center"> You have 1 pending request.</p> 

                                    <div class="text-muted mt-2" style="text-align: justify;">

                                        <div class="table-responsive">

                                            <table class="table align-middle table-nowrap">
                                                <tbody>
                                                    <tr>
                                                        <td>
                                                            <p class="mb-0"><i class="mdi mdi-chevron-right text-primary me-1"></i> Your request for <b>Full Membership</b> ({{ date('Y') }}) status is <b class="text-primary" style="font-weight:520;">Pending</b> now. </p>
                                                        </td>
                                                        
                                                    </tr>
                                                    <tr>
                                                        <td style="border-bottom: 0;">
                                                            <p class="mb-0"><i class="mdi mdi-chevron-right text-primary me-1"></i> Please wait for the administrator approval. </p>
                                                        </td>
                                                        
                                                    </tr>
                                                    
                                                </tbody>
                                            </table>

                                        </div>

                                    </div>

                                @elseif($find->status == 'Approved')

                                    <p class="font-size-14 text-success text-center"> You have 1 approved request.</p> 

                                    <div class="text-muted mt-2" style="text-align: justify;">

                                        <div class="table-responsive">

                                            <table class="table align-middle table-nowrap">
                                                <tbody>
                                                    <tr>
                                                        <td>
                                                            <p class="mb-0"><i class="mdi mdi-chevron-right text-primary me-1"></i> <b class="text-success" style="font-weight:520;">Congratulations!</b> Your request for <b>Full Membership</b> ({{ date('Y') }}) status is <b class="text-success" style="font-weight:520;">Approved</b> now. </p>
                                                        </td>
                                                        
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <p class="mb-0"><i class="mdi mdi-chevron-right text-primary me-1"></i> You are now <b>Full Member</b>. You don't need to participate manually. But you will have to give the preferences.</p>
                                                        </td>
                                                        
                                                    </tr>

                                                    <tr>
                                                        <td style="border-bottom: 0;">
                                                            <p class="mb-0"><i class="mdi mdi-chevron-right text-primary me-1"></i> You will be <b>automatically added</b> to the draw of a tournament or league.</p>
                                                        </td>
                                                        
                                                    </tr>
                                                    
                                                </tbody>
                                            </table>

                                        </div>

                                    </div>

                                @elseif($find->status == 'Declined')
                                    <p class="font-size-14 text-danger text-center"> You have 1 declined request.</p> 

                                    <div class="text-muted mt-2" style="text-align: justify;">

                                        <div class="table-responsive">

                                            <table class="table align-middle table-nowrap">
                                                <tbody>
                                                    <tr>
                                                        <td>
                                                            <p class="mb-0"><i class="mdi mdi-chevron-right text-primary me-1"></i> <b class="text-danger" style="font-weight:520;">Whoops!</b> Your request for <b>Full Membership</b> ({{ date('Y') }}) status is <b class="text-danger" style="font-weight:520;">Declined</b>. </p>
                                                        </td>
                                                        
                                                    </tr>
                                                    <tr>
                                                        <td style="border-bottom: 0;">
                                                            <h6 class="mb-0"><i class="mdi mdi-chevron-right text-primary me-1"></i> <span style="font-weight: 500;">Decline Reason:</span> <span style="font-weight: 400;">{{ $find->decline_reason }} </span> </h6>
                                                        </td>
                                                        
                                                    </tr>
                                                    
                                                </tbody>
                                            </table>

                                        </div>

                                    </div>
                                @endif
                            @else 
                                <p class="font-size-14"> Please check the following instructions before proceed:
                                </p> 

                                <div class="text-muted mt-2" style="text-align: justify;">

                                    <div class="table-responsive">

                                        <table class="table align-middle table-nowrap">
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <p class="mb-0"><i class="mdi mdi-chevron-right text-primary me-1"></i> You can request for a <b>Full Membership</b> status for this year <b style="font-weight:520;">{{ date('Y') }}</b> from the link of the below. </p>
                                                    </td>
                                                    
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <p class="mb-0"><i class="mdi mdi-chevron-right text-primary me-1"></i> Full membership includes 8 tournaments, 2 leagues, and TOP16 Finals. </p>
                                                    </td>
                                                    
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <p class="mb-0"><i class="mdi mdi-chevron-right text-primary me-1"></i> It will cost <b>120 €</b> for this year. </p>
                                                    </td>
                                                   
                                                </tr>
                                                <tr>
                                                    <td style="border-bottom: 0;">
                                                        <p class="mb-0"><i class="mdi mdi-chevron-right text-primary me-1"></i> Remember, you can only <b>request once a year</b> for this feature. So please request carefully so that your request can't be declined. </p>
                                                    </td> 
                                                    
                                                </tr>

                                                
                                            </tbody>
                                        </table>

                                    </div>

                                </div>

                                <form class="needs-validation" id="fullMem" action="{{ route('store.full.free') }}" method="post" novalidate="">
                                @csrf

                                    <div class="row">
                                        <input type="hidden" name="year" value="{{ date('Y') }}">

                                        <div class="col-12 col-md-6 col-sm-6 col-lg-6 col-xl-6">
                                            <button type="submit" name="online_payment" style="width: 100%; margin-top: 6px !important;" class="btn btn-success btn-label waves-effect waves-light">
                                                <i class="fab fa-stripe label-icon"></i> Pay Now 
                                                <i class="bx bx-right-arrow-circle bx-fade-right font-size-20 align-middle me-1"></i>
                                            </button>
                                        </div>

                                        <div class="col-12 col-md-6 col-sm-6 col-lg-6 col-xl-6 cusGap">
                                            <button type="submit" name="offline_payment" onclick="return confirm('Are you sure to request now?')" style="width: 100%;border: 1px solid #efe9e9 !important;color: #555 !important;" class="btn btn-light border btn-label waves-effect waves-light">                                                
                                                <i class="bx bx-money label-icon"></i> Pay with other methods 
                                                <i class="bx bx-right-arrow-circle bx-fade-right font-size-20 align-middle me-1"></i>
                                            </button>
                                        </div>
                                
                                    </div>

                                </form>

                            @endif
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


@section('scripts')
    <script type="text/javascript">
        $('#fullMem').one('submit', function() {
            $(this).find('button[name="offline_payment"]').prop('disabled', true);
        });
    </script>
@endsection

