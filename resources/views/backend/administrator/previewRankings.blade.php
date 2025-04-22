@extends('layouts.master')
@section('title', 'Preview & Publish')

@section('content')

    <!-- ========================== Page Content ==================================== -->
    <div class="page-content">
        <div class="container-fluid">

            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">Preview & Publish - <a href="{{ route('frontend.bg.rankings') }}" target="_blank"><small style="text-decoration: underline; text-transform: none;">(View Current Rankings)</small></a></h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item active">Preview & Publish</li>
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
                                    <table id="tech-companies-1" class="table align-middle table-nowrap w-100 text-center">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Ranking</th>
                                                <th>Move</th>
                                                <th>Player</th>
                                                <th>Total Points</th>
                                                <th>Tour. Played</th>
                                                

                                                <th>1st Elite1500</th>
                                                <th>1st Pro1000</th>
                                                <th>1st Adv500</th>
                                                <th>1st Int250</th>
                                                <th>1st Rook100</th>

                                                <th>2nd Elite1500</th>
                                                <th>2nd Pro1000</th>
                                                <th>2nd Adv500</th>
                                                <th>2nd Int250</th>
                                                <th>2nd Rook100</th>

                                                <th>1st Leagues</th>


                                                <th>3rd Elite1500</th>
                                                <th>3rd Pro1000</th>
                                                <th>3rd Adv500</th>
                                                <th>3rd Int250</th>
                                                <th>3rd Rook100</th>

                                                <th>4th Elite1500</th>
                                                <th>4th Pro1000</th>
                                                <th>4th Adv500</th>
                                                <th>4th Int250</th>
                                                <th>4th Rook100</th>

                                                <th>2nd Leagues</th>
                                                <th>Top16 Finals</th>

                                            </tr>
                                        </thead>


                                        <tbody>

                                            @foreach($quarter_rankings as $ranking)
                                                <tr>
                                                    <td>{{ $ranking['ranking'] }}</td>
                                                    
                                                    {{-- @if($ranking['isNew'])
                                                        <td class="text-dark" style="text-align: right;">NEW</td>
                                                    @else
                                                        @if($ranking['ranking'] < $ranking['previous_ranking'])
                                                            <td class="text-success">
                                                                <i class="bx bx-up-arrow-alt ms-1" 
                                                                    style="position: relative; font-size: 19px; float: left;">
                                                                </i>
                                                                {{ $ranking['previous_ranking'] - $ranking['ranking'] }}
                                                            </td>
                                                        @elseif($ranking['ranking'] > $ranking['previous_ranking'])
                                                            <td class="text-danger">
                                                                <i class="bx bx-down-arrow-alt ms-1" 
                                                                    style="position: relative; font-size: 19px; float: left;">
                                                                </i>
                                                                {{ $ranking['ranking'] - $ranking['previous_ranking'] }}
                                                            </td>
                                                        @else
                                                            <td class="text-dark">-</td>
                                                        @endif
                                                    @endif --}}

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

                                                    <td>{{ \Str::limit($ranking['player'], 21, '..') }}</td>
                                                    <td>{{ $ranking['total'] }}</td>
                                                    <td>{{ $ranking['played'] }}</td>

                                                    <td>{!! $ranking['1st_elite'] == "n/a" ? '0' : '<span style="color:#f93649">'.$ranking['1st_elite'].'</span>' !!}</td>
                                                    <td>{!! $ranking['1st_pro'] == "n/a" ? '0' : '<span style="color:#f93649">'.$ranking['1st_pro'].'</span>' !!}</td>
                                                    <td>{!! $ranking['1st_adv'] == "n/a" ? '0' : '<span style="color:#f93649">'.$ranking['1st_adv'].'</span>' !!}</td>
                                                    <td>{!! $ranking['1st_int'] == "n/a" ? '0' : '<span style="color:#f93649">'.$ranking['1st_int'].'</span>' !!}</td>
                                                    <td>{!! $ranking['1st_rookie'] == "n/a" ? '0' : '<span style="color:#f93649">'.$ranking['1st_rookie'].'</span>' !!}</td>

                                                    <td>{!! $ranking['2nd_elite'] == "n/a" ? '0' : '<span style="color:#f93649">'.$ranking['2nd_elite'].'</span>' !!}</td>
                                                    <td>{!! $ranking['2nd_pro'] == "n/a" ? '0' : '<span style="color:#f93649">'.$ranking['2nd_pro'].'</span>' !!}</td>
                                                    <td>{!! $ranking['2nd_adv'] == "n/a" ? '0' : '<span style="color:#f93649">'.$ranking['2nd_adv'].'</span>' !!}</td>
                                                    <td>{!! $ranking['2nd_int'] == "n/a" ? '0' : '<span style="color:#f93649">'.$ranking['2nd_int'].'</span>' !!}</td>
                                                    <td>{!! $ranking['2nd_rookie'] == "n/a" ? '0' : '<span style="color:#f93649">'.$ranking['2nd_rookie'].'</span>' !!}</td>

                                                    <td>{!! $ranking['1st_league'] == "n/a" ? '0' : '<span style="color:#f93649">'.$ranking['1st_league'].'</span>' !!}</td>

                                                    <td>{!! $ranking['3rd_elite'] == "n/a" ? '0' : '<span style="color:#f93649">'.$ranking['3rd_elite'].'</span>' !!}</td>
                                                    <td>{!! $ranking['3rd_pro'] == "n/a" ? '0' : '<span style="color:#f93649">'.$ranking['3rd_pro'].'</span>' !!}</td>
                                                    <td>{!! $ranking['3rd_adv'] == "n/a" ? '0' : '<span style="color:#f93649">'.$ranking['3rd_adv'].'</span>' !!}</td>
                                                    <td>{!! $ranking['3rd_int'] == "n/a" ? '0' : '<span style="color:#f93649">'.$ranking['3rd_int'].'</span>' !!}</td>
                                                    <td>{!! $ranking['3rd_rookie'] == "n/a" ? '0' : '<span style="color:#f93649">'.$ranking['3rd_rookie'].'</span>' !!}</td>

                                                    <td>{!! $ranking['4th_elite'] == "n/a" ? '0' : '<span style="color:#f93649">'.$ranking['4th_elite'].'</span>' !!}</td>
                                                    <td>{!! $ranking['4th_pro'] == "n/a" ? '0' : '<span style="color:#f93649">'.$ranking['4th_pro'].'</span>' !!}</td>
                                                    <td>{!! $ranking['4th_adv'] == "n/a" ? '0' : '<span style="color:#f93649">'.$ranking['4th_adv'].'</span>' !!}</td>
                                                    <td>{!! $ranking['4th_int'] == "n/a" ? '0' : '<span style="color:#f93649">'.$ranking['4th_int'].'</span>' !!}</td>
                                                    <td>{!! $ranking['4th_rookie'] == "n/a" ? '0' : '<span style="color:#f93649">'.$ranking['4th_rookie'].'</span>' !!}</td>

                                                    <td>{!! $ranking['2nd_league'] == "n/a" ? '0' : '<span style="color:#f93649">'.$ranking['2nd_league'].'</span>' !!}</td>
                                                    <td>{!! $ranking['top16_finals'] == "n/a" ? '0' : '<span style="color:#f93649">'.$ranking['top16_finals'].'</span>' !!}</td>
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
                            <div class="card-body">

                                <form class="needs-validation" action="{{ route('publishRankings') }}" method="post" novalidate="">
                                @csrf

                                    <div class="row">

                                        <div class="col-12">
                                            <div class="mb-3 position-relative">
                                                <label for="validationTooltip01" class="form-label">Generate Rankings For</label>
                                                                                            
                                                <select class="form-control select2" id="validationTooltip01" name="rankings_type" required="">
                                        
                                                    <option value="">--- Select any ---</option> 
                                                    
                                                    @if($settings->rankings_type == null || $settings->rankings_type == '4th Quarter')
                                                        <option value="1st Quarter">1st Quarter</option>
                                                    @elseif ($settings->rankings_type == '1st Quarter')
                                                        <option value="2nd Quarter">2nd Quarter</option>
                                                    @elseif ($settings->rankings_type == '2nd Quarter')
                                                        <option value="3rd Quarter">3rd Quarter</option>
                                                    @elseif ($settings->rankings_type == '3rd Quarter')
                                                        <option value="4th Quarter">4th Quarter</option>
                                                    @endif
                                                    

                                                </select>

                                                <div class="valid-feedback">
                                                    Looks good!
                                                </div>

                                                <div class="invalid-feedback">
                                                    Please select rankings type.
                                                </div>                                          

                                            </div>
                                        </div>


                                        <div class="col-xl-12">

                                            <div class="mb-3 position-relative text-center">                                                
                                                <button class="btn btn-primary" onclick="return confirm('Are you sure to preview?');" style="" type="submit">Preview Rankings</button>
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
            /* border: 1px solid #e1e7f0;
            border-right: 2px solid #e1e7f0 !important;
            border-bottom-color: #e1e7f0 !important; */
            background: #556ee6;
            border-color: #556ee6;
            color: #fff;
            border-right: 1px solid #a3b1f4 !important;
        }

        #tech-companies-1-col-0-clone {
            min-width: 98.36px !important;
        }
        #tech-companies-1-col-4-clone {
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
            $("#tech-companies-1").DataTable({
                    lengthMenu: [[-1, 10, 25, 50, 100, 250], ["All", 10, 25, 50, 100, 250]],
                    buttons:["copy","excel","colvis"],
                    columnDefs:[
                        { "orderable": false, "targets": [1, 2, 3, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27] }  // Disable sorting on the first and second columns
                    ]
                })
                .buttons()
                .container()
                .appendTo("#tech-companies-1_wrapper .col-md-6:eq(0)")
        });
    </script>
@endsection
