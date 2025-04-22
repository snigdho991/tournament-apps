@extends('layouts.frontend-master')

@section('title', 'Our Rankings')

@section('content')
        
    <section class="section" id="faqs" style="margin-top: 50px !important;">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="text-center mb-5">
                        <div class="small-title">Our Rankings</div>
                        <h4>Updated: {{ date('d F, Y', strtotime($settings->rankings_last_updated)) }}</h4>
                    </div>
                </div>
            </div>
            <!-- end row -->

            <div class="row">
                <div class="col-lg-12">
                    <div class="vertical-nav">

                        <div class="card">
                            <div class="card-body">
                                <div class="table-rep-plugin">
                                    <div class="table-responsive mt-2">
                                        <table id="tech-companies-3" class="table align-middle table-nowrap w-100 text-center font-size-14">
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
    
                                                @foreach($user_rankings as $ranking)
                                                    
                                                    <tr>
                                                        <td>{{ $ranking->current_ranking }}</td>
                                                        
                                                        @if(is_numeric($ranking->move) && intval($ranking->move) == $ranking->move)
                                                            <?php $intMoveValue = intval($ranking->move); ?>

                                                            @if($intMoveValue > 0)
                                                                <td class="text-success" style="font-weight: 500;">↑ {{ $ranking->move }}</td>
                                                            @else
                                                                <td class="text-danger" style="font-weight: 500;">↓ {{ abs($ranking->move) }}</td>
                                                            @endif
                                                        @else
                                                            <td class="text-dark text-center">{{ $ranking->move }}</td>
                                                        @endif
    
                                                        <td>{{ $ranking->name }}</td>
                                                        <td>{{ $ranking->total_points }}</td>
                                                        <td>{{ $ranking->tour_played }}</td>
                                                    </tr>
                                                    
                                                @endforeach
                                                
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <!-- end vertical nav -->
                </div>
            </div>
            <!-- end row -->
        </div>
        <!-- end container -->
    </section>
        
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

        .sticky-table-header {
            margin-top: 11px !important;
            border-top: 1px solid #3f4e96 !important;
        }

        #tech-companies-3-col-0-clone {
            min-width: 98.36px !important;
        }
        #tech-companies-3-col-4-clone {
            min-width: 125.15px !important;
        }

        .dataTables_empty {
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

    @if(auth()->check())
        @if(auth()->user()->hasRole('Administrator'))
            <script>
                $(document).ready(function(){
                    $("#tech-companies-3").DataTable({
                            lengthMenu: [[-1, 10, 25, 50, 100, 250], ["All", 10, 25, 50, 100, 250]],
                            buttons:["copy","excel","colvis"],
                            columnDefs:[
                                { "orderable": false, "targets": [1, 2, 3] }
                            ]
                        })
                        .buttons()
                        .container()
                        .appendTo("#tech-companies-3_wrapper .col-md-6:eq(0)")
                });
            </script>
        @else
            <script>
                $(document).ready(function(){
                    $("#tech-companies-3").DataTable({
                            lengthMenu: [[-1, 10, 25, 50, 100, 250], ["All", 10, 25, 50, 100, 250]],
                            columnDefs:[
                                { "orderable": false, "targets": [1, 2, 3] }
                            ]
                        })
                        .buttons()
                        .container()
                        .appendTo("#tech-companies-3_wrapper .col-md-6:eq(0)")
                });
            </script>
        @endif
    @endif

@endsection
