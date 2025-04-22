@extends('layouts.master')
@section('title', 'Publish Rankings')

@section('content')

    <!-- ========================== Page Content ==================================== -->
    <div class="page-content">
        <div class="container-fluid">

            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">Publish Rankings - <a href="{{ route('frontend.bg.rankings') }}" target="_blank"><small style="text-decoration: underline; text-transform: none;">(View Current Rankings)</small></a></h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item active">Publish Rankings</li>
                            </ol>
                        </div>

                    </div>
                </div>
            </div>
            <!-- end page title -->

            
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-rep-plugin">
                                <div class="table-responsive mt-2">
                                    <table id="tech-companies-2" class="table align-middle table-nowrap w-100 text-center">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Ranking</th>
                                                <th>Move</th>
                                                <th>Player</th>
                                                <th>Total Points</th>
                                                <th>Tour. Played</th>
                                            </tr>
                                        </thead>


                                        <tbody>

                                            @foreach($quarter_rankings as $ranking)
                                                
                                                <tr>
                                                    <td>{{ $ranking['ranking'] }}</td>
                                                    
                                                    @if($ranking['isNew'])
                                                        <td class="text-dark text-center">NEW</td>
                                                    @else
                                                        @if($ranking['ranking'] < $ranking['current_ranking'])
                                                            
                                                            <td class="text-success" style="font-weight: 500;">↑ {{ $ranking['current_ranking'] - $ranking['ranking'] }}</td>

                                                        @elseif($ranking['ranking'] > $ranking['current_ranking'])
                                                            
                                                            <td class="text-danger" style="font-weight: 500;">↓ {{ $ranking['ranking'] - $ranking['current_ranking'] }}</td>

                                                        @else
                                                            <td class="text-dark text-center">-</td>
                                                        @endif
                                                    @endif

                                                    <td>{{ $ranking['player'] }}</td>
                                                    <td>{{ $ranking['total'] }}</td>
                                                    <td>{{ $ranking['played'] }}</td>
                                                </tr>
                                                
                                            @endforeach
                                            
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>



            @if($settings)
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body text-center">

                                <form class="needs-validation" action="{{ route('publishRankingsSubmit') }}" method="post" novalidate="">
                                @csrf

                                    <input type="hidden" name="quarter_rankings" value="{{ json_encode($quarter_rankings) }}">
                                    <input type="hidden" name="rankings_type" value="{{ $rankings_type }}">
                                    
                                    <div class="row">

                                        <div class="col-12">
                                            <div class="mb-3 position-relative">
                                                <label for="" class="form-label">Publish Rankings For: <h5 class="text-success mt-1">{{ $rankings_type }}</h5></label>
                                                                                            
                                                 <hr>                               

                                            </div>
                                        </div>


                                        <div class="col-12 mt-2">

                                            <div class="mb-3 position-relative text-center">                                            
                                                <label for="" class="form-label text-danger">Are you sure to publish the rankings now? REMEMBER, This operation can't be undone.</label><br>
                                                
                                                <span>
                                                    <button 
                                                        @if($settings->publish_button_status == 'Locked') 
                                                            class="btn btn-danger" disabled="" style="width: 80%; margin-top:10px;" 
                                                        @else 
                                                            class="btn btn-success" style="width: 100%; margin-top:10px;" 
                                                        @endif 
                                                        onclick="return confirm('Are you sure to publish now? This operation can\'t be undone.');" type="submit">
                                                            PUBLISH RANKINGS NOW
                                                    </button>
                                                    @if($settings->publish_button_status == 'Locked') 
                                                        <a href="{{ route('get.settings') }}" 
                                                            style="
                                                                text-decoration: underline !important;
                                                                top: 5px;position: relative;
                                                                left: 10px;"
                                                        >
                                                            Unlock Now
                                                            <i class="mdi mdi-arrow-right"></i>
                                                        </a>
                                                    @endif
                                                </span>

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
            @endif
            
            
        </div> <!-- container-fluid -->
    </div>
    <!-- End Page-content -->                
                
@endsection


@section('styles')
    <link href="{{ asset('/assets/libs/admin-resources/rwd-table/rwd-table.min.css') }}" rel="stylesheet" type="text/css" />   
    <style type="text/css">
        .table-light th {
            background: #556ee6;
            border-color: #556ee6;
            color: #fff;
            border-right: 1px solid #a3b1f4 !important;
        }

        #tech-companies-2-col-0-clone {
            min-width: 98.36px !important;
        }
        #tech-companies-2-col-4-clone {
            min-width: 125.15px !important;
        }

        .dataTables_empty {
            text-align: left !important;
            color: #dc3545;
        }
        
        @media screen and (max-width: 575px) {
            .minStGap {
                margin-top: 15px !important;
            }

            .minStWidth {
                min-width: 81px;
            }
        }

        table.dataTable {
            margin-top: 15px !important;
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

        .sorting {
            position: relative;
        }

        table.dataTable {
            border-collapse: collapse !important;
        }

        .btn-toolbar {
            display: none !important;
        }

    </style>
@endsection


@section('scripts')
    <script src="{{ asset('/assets/libs/admin-resources/rwd-table/rwd-table.min.js') }}"></script>
    <script src="{{ asset('/assets/js/pages/table-responsive.init.js') }}"></script>

    <script>
        $(document).ready(function(){
            $("#tech-companies-2").DataTable({
                    lengthMenu: [[-1, 10, 25, 50, 100, 250], ["All", 10, 25, 50, 100, 250]],
                    buttons:["copy","excel","colvis"],
                    columnDefs:[
                        { "orderable": false, "targets": [1, 2, 3] }  // Disable sorting on the first and second columns
                    ]
                })
                .buttons()
                .container()
                .appendTo("#tech-companies-2_wrapper .col-md-6:eq(0)")
        });
    </script>
@endsection
