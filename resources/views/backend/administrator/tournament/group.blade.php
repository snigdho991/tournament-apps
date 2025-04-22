@extends('layouts.master')
@section('title', 'Tournament Group')

@section('content')

    <div class="page-content">
        <div class="container-fluid">

            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">Tournament Group ({{ count($players) }} Players)</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard </a></li>
                                <li class="breadcrumb-item active" style="color: #74788d;">Tournament Group </li>
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

                            <form class="needs-validation" action="{{ route('store.league.group', $league->id) }}" method="post" novalidate="">
                            @csrf

                                <input type="hidden" name="chk_type" value="Tournament">
                                <div class="row">

                                    <div class="col-xl-6">
                                        <div class="mb-3 position-relative">
                                            <label for="player_per_group" class="form-label">Number of Players (Per Group)</label>
                                                                                        
                                            <select class="form-control select2" id="player_per_group" name="player_per_group" required="">
                                    
                                                <option value="">Select Number of Players</option>
                                                
                                                <option value="4" {{ $league->player_per_group == 4 ? "selected" : "" }}>4</option>
                                                <option value="5" {{ $league->player_per_group == 5 ? "selected" : "" }}>5</option>
                                                <option value="6" {{ $league->player_per_group == 6 ? "selected" : "" }}>6</option>
                                                <option value="7" {{ $league->player_per_group == 7 ? "selected" : "" }}>7</option>
                                                <option value="8" {{ $league->player_per_group == 8 ? "selected" : "" }}>8</option>
                                            
                                            </select>

                                            <div class="valid-tooltip">
                                                Looks good!
                                            </div>

                                            <div class="invalid-tooltip">
                                                Please select number of players in each group.
                                            </div>                                          

                                        </div>
                                    </div>

                                    <div class="col-xl-6">
                                        <div class="mb-3 position-relative">
                                            <label for="group_size" class="form-label">Number of Groups</label>
                                                                                        
                                            <input type="number" name="group_size" value="{{ old('group_size', $league->group_size) }}" class="form-control" id="group_size" placeholder="Enter group size" required="">
                                        
                                       
                                            <div class="valid-tooltip">
                                                Looks good!
                                            </div>

                                            <div class="invalid-tooltip">
                                                Please select number of groups.
                                            </div>                                          

                                        </div>
                                    </div>

                                   
                                    <div class="col-xl-12">

                                        <div class="mb-3 position-relative">                                            
                                            <label for="" class="form-label"></label>
                                            
                                            <button class="btn btn-primary" onclick="return confirm('Are you sure?');" style="margin-top: 6px !important; width: 100% !important" type="submit">Generate Tournament Groups</button>

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
            
            
            @if($league->group_size && $league->player_per_group)
                <div class="row mt-4">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body" style="text-align: center;">

                                <span class="badge bg-info mb-4 text-center">Group Deadlines</span>
                                
                                <form class="needs-validation" action="{{ route('submit.league.deadlines', $league->id) }}" method="post" novalidate="">
                                @csrf
                                    <input type="hidden" name="chk_type" value="Tournament">
                                
                                    <div class="row mb-4">

                                        @for($i = 1; $i < $league->group_size + 1; $i++)
                                            <?php 
                                                $grp_word = strtolower(ucwords((new NumberFormatter('en_IN', NumberFormatter::SPELLOUT))->format($i)));
                                                ${"t_d_group" . $i} = json_decode($league->{"group_" . $grp_word . "_deadline"});
                                            ?>

                                            <div class="mb-4">
                                                <h5 class="text-center text-info" style="font-weight:510;">Group {{ $grp_word }} </h5>
                                                <div class="input-daterange input-group" id="project-date-inputgroup" data-provide="datepicker" data-date-format="dd M, yyyy"  data-date-container='#project-date-inputgroup' data-date-autoclose="true">

                                                    <input type="text" class="start form-control" @if($league->{"group_" . $grp_word . "_deadline"}) value="{{ ${"t_d_group" . $i}->start }}" @endif placeholder="Start Date" name="group_{{ $i }}_start" required />
                                                    <input type="text" class="end form-control" @if($league->{"group_" . $grp_word . "_deadline"}) value="{{ ${"t_d_group" . $i}->end }}" @endif placeholder="End Date" name="group_{{ $i }}_end" required />

                                                </div>

                                            </div>

                                        @endfor
                                    </div>


                                    <button type="submit" class="btn btn-success btn-label waves-effect waves-light mb-1" onclick="return confirm('Are you sure to set the schedule of the league?');"><i class="bx bx-add-to-queue label-icon"></i> Submit Deadlines <i class="bx bx-right-arrow-circle bx-fade-right font-size-20 align-middle me-1"></i></button>

                                </form>

                            </div>
                        </div>
                    </div>
                </div>
            
            @endif
            

            @if($league->group_size && $league->player_per_group)
                <div class="row mt-4">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">

                                <div class="row">
                                    <div>
                                        <h5 class="text-center text-info" style="font-weight:510;">Set Up Groups</h5>
                                    </div>

                                    <i class="bx bx-chevrons-down bx-fade-down text-info display-6 text-center mt-1"></i>
                                    <span style="margin-top:20px;"></span>
                                        
                                    <div class="col-xl-4"></div>
                                    <div class="col-xl-4 text-center">
                                        <span class="badge bg-info mb-4">Group Players</span>
                                    </div>
                                    <div class="col-xl-4"></div>                                        
                                        
                                </div>

                                <br>

                                @for($i = 1; $i < $league->group_size + 1; $i++)
                                    <?php 
                                        $grp_word = strtolower(ucwords((new NumberFormatter('en_IN', NumberFormatter::SPELLOUT))->format($i)));
                                    ?>

                                    <form class="needs-validation" action="{{ route('submit.group.'.$i.'.league', $league->id) }}" enctype="multipart/form-data" method="post" novalidate="">
                                        @csrf
                                            <input type="hidden" name="chk_type" value="Tournament">

                                        <div class="row">
                                            <div class="col-6">
                                                <div class="mb-3 position-relative">
                                                    <label for="player_per_group" class="form-label">Group - {{ $i }}</label>
                                                                                                
                                                    <select class="form-control select2 select2-multiple" multiple="multiple" id="player_per_group{{ $i }}" name="group_{{ $i }}[]" data-placeholder="Choose Players..." required="">
                                            
                                                        @foreach($players as $player)
                                                            <?php 
                                                                $user = \App\Models\User::findOrFail($player);

                                                                if($league->{"group_" . $grp_word . "_players"}) {
                                                                    $group_players = json_decode($league->{"group_" . $grp_word . "_players"}, true);
                                                                }
                                                            ?>


                                                            <option value="{{ $user->id }}" @if($league->{"group_" . $grp_word . "_players"}) {{ in_array($user->id, $group_players) ? "selected" : "" }} @endif>{{ $user->name }}</option>

                                                        @endforeach
                                                    
                                                    </select>

                                                    <div class="valid-tooltip">
                                                        Looks good!
                                                    </div>

                                                    <div class="invalid-tooltip">
                                                        Please select number of players in each group.
                                                    </div>                                          

                                                </div>
                                            </div>
                                                           
                                            <div class="col-6">
                                                <div class="mb-3 position-relative">
                                                    <label class="form-label"></label>
                                                    <button class="btn btn-success" style="margin-top: 8px!important; width: 100% !important" type="submit">Save Group</button>  
                                                </div>  
                                            </div>
                                        </div>
                                        
                                    </form>
                                @endfor
                                
                            </div>
                        </div>
                    </div>
                </div>
                
                <br>
            @endif
            

            @if($league->group_size && $league->player_per_group)
                @if($league->group_one_players)

                    <!--- GROUP TABLE -->
                    <div class="row mt-4">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">

                                        @for($i = 1; $i < $league->group_size + 1; $i++)
                                            <?php 
                                                $grp_word = strtolower(ucwords((new NumberFormatter('en_IN', NumberFormatter::SPELLOUT))->format($i)));
                                            ?>
                                            
                                            @if($league->{"group_" . $grp_word . "_players"})

                                                @if($league->{"group_" . $grp_word . "_stats"})

                                                    <?php 
                                                        $stats_array = json_decode($league->{"group_" . $grp_word . "_stats"}, true);
                                                        uasort($stats_array, function($a, $b) {
                                                            return $b['pts'] < $a['pts'] ? -1 : 1;
                                                        });
                                                    ?>

                                                    <div class="col-xl-4 col-lg-6 col-md-6 col-12">
                                                        <div class="ptable">
                                                            <h5 class="text-grey-darker mb-3" style="font-family: Fjalla One; text-decoration: underline;">Group - {{ $i }} :</h5>

                                                                <table>
                                                                    <tr class="col">
                                                                        <th>#</th>
                                                                        <th style="text-align: center;">Player</th>
                                                                        <th>GP</th>
                                                                        <th>W</th>
                                                                        <th>L</th>
                                                                        <th>PTS</th>
                                                                    </tr>

                                                                    <?php $incr = 1; ?>
                                                                    @foreach($stats_array as $key => $stat)
                                                                        <?php 
                                                                            $gr_player = \App\Models\User::findOrFail($key);
                                                                            
                                                                        ?>

                                                                        
                                                                        <tr class="@if($incr < 5) wpos @else pos @endif">
                                                                            <td>{{ $incr }}</td>
                                                                            <td>{{ $gr_player->name }}</td>
                                                                            <td>{{ $stat['gp'] }}</td>
                                                                            <td>{{ $stat['w'] }}</td>
                                                                            <td>{{ $stat['l'] }}</td>
                                                                            <td>{{ $stat['pts'] }}</td>
                                                                        </tr>
                                                                        
                                                                        <?php $incr++; ?>

                                                                    @endforeach

                                                                </table>

                                                        </div>
                                                    </div>

                                                @else

                                                    <?php 
                                                        $group_players = json_decode($league->{"group_" . $grp_word . "_players"}, true);
                                                    ?>

                                                    <div class="col-xl-4 col-lg-6 col-md-6 col-12">
                                                        <div class="ptable">
                                                            <h5 class="text-grey-darker mb-3" style="font-family: Fjalla One; text-decoration: underline;">Group - {{ $i }} :</h5>

                                                                <table>
                                                                    <tr class="col">
                                                                        <th>#</th>
                                                                        <th style="text-align: center;">Player</th>
                                                                        <th>GP</th>
                                                                        <th>W</th>
                                                                        <th>L</th>
                                                                        <th>PTS</th>
                                                                    </tr>

                                                                    @foreach($group_players as $ke => $user_id)
                                                                        <?php 
                                                                            $gr_player = \App\Models\User::findOrFail($user_id);
                                                                        ?>
                                                                        <tr class="wpos">
                                                                            <td>{{ $ke + 1 }}</td>
                                                                            <td>{{ $gr_player->name }}</td>
                                                                            <td>0</td>
                                                                            <td>0</td>
                                                                            <td>0</td>
                                                                            <td>0</td>
                                                                        </tr>
                                                                    @endforeach

                                                                </table>

                                                        </div>
                                                    </div>

                                                @endif

                                            @else
                                                <div class="col-xl-4 col-lg-6 col-md-6 col-12">
                                                    <div class="ptable">
                                                        <h5 class="text-grey-darker mb-3" style="font-family: Fjalla One; text-decoration: underline;">Group - {{ $i }} :</h5>

                                                            <table>
                                                                <tr class="col">
                                                                    <th>#</th>
                                                                    <th style="text-align: center;">Player</th>
                                                                    <th>GP</th>
                                                                    <th>W</th>
                                                                    <th>L</th>
                                                                    <th>PTS</th>
                                                                </tr>

                                                                @for($inc = 1; $inc < $league->player_per_group + 1; $inc++)
                                                                    
                                                                    <tr class="ypos">
                                                                        <td>{{ $inc }}</td>
                                                                        <td>N/A</td>
                                                                        <td>0</td>
                                                                        <td>0</td>
                                                                        <td>0</td>
                                                                        <td>0</td>
                                                                    </tr>
                                                                @endfor

                                                            </table>

                                                    </div>
                                                </div>
                                            @endif
                                        @endfor

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                @endif
            @endif


            <!--- GROUP MATCHES -->
            @if($league->group_size && $league->player_per_group)
                
                @if($league->group_one_players)
                    <div class="row mt-4">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-body">
                                    @for($i = 1; $i < $league->group_size + 1; $i++)
                                        <?php 
                                            $grp_word = strtolower(ucwords((new NumberFormatter('en_IN', NumberFormatter::SPELLOUT))->format($i)));
                                        ?>

                                        @if($league->{"group_" . $grp_word . "_players"})
                                            <?php 
                                                $group_players = json_decode($league->{"group_" . $grp_word . "_players"}, true);
                                                $count_players = count($group_players);
                                                $gr_matches = ($count_players * ($count_players - 1)) / 2;
                                            ?>
                                            

                                            <div class="row">
                                                <div>
                                                    <h5 class="text-center text-info" style="font-weight:510;">Group - {{ $i }}</h5>
                                                </div>

                                                <i class="bx bx-chevrons-down bx-fade-down text-info display-6 text-center mt-1"></i>
                                                <span style="margin-top:20px;"></span>
                                                    
                                                <div class="col-xl-4"></div>
                                                <div class="col-xl-4 text-center">
                                                    <span class="badge bg-info mb-4">Matches</span>
                                                </div>
                                                <div class="col-xl-4"></div>                                    
                                            </div>

                                            <br>

                                            <div class="row">

                                                @for($j = 1; $j < $gr_matches + 1; $j++)
                                                    <?php 
                                                        $match_word = strtolower(ucwords((new NumberFormatter('en_IN', NumberFormatter::SPELLOUT))->format($j)));
                                                    ?>
                                                    <div class="col-xl-6">
                                                        <div class="mb-3 position-relative">

                                                            <form class="needs-validation" action="{{ route('submit.group.'.$grp_word.'.matches', [$match_word, $league->id]) }}" method="post" novalidate="">
                                                                @csrf
                                                                    <input type="hidden" name="chk_type" value="Tournament">
                                        
                                                                <label class="form-label text-dark"> Match - {{ $j }}</label>
                                                                <div class="row">

                                                                    <div class="col-5">
                                                                        <select class="select2 form-control" id="onevalidationTooltip{{ ($j + 1000) * $i }}" name="group_{{ $i }}_mat_{{ $j }}_player_1" required>
                                                        
                                                                            <option value="">Select Player</option>
                                                                            
                                                                            <?php 
                                                                                
                                                                                ${"group_". $i ."_mat_". $j} = [];

                                                                                if($league->{"group_" . $grp_word . "_matches"}) {
                                                                                    $find_matches = json_decode($league->{"group_" . $grp_word . "_matches"}, true);

                                                                                    if(array_key_exists('match_'.$j, $find_matches)) {
                                                                                        $matches = $find_matches['match_'.$j];
                                                                                        $match = explode(" VS ", $matches);
                                                                                        array_push(${"group_". $i ."_mat_". $j}, \App\Models\User::findOrFail($match[0])->name);
                                                                                        array_push(${"group_". $i ."_mat_". $j}, \App\Models\User::findOrFail($match[1])->name);
                                                                                    }
                                                                                }


                                                                                if($league->{"group_" . $grp_word . "_results"}) {
                                                                                    $find_results = json_decode($league->{"group_" . $grp_word . "_results"}, true);
                                                                                }
                                                                            ?>

                                                                            @foreach($group_players as $player)
                                                                                <?php 
                                                                                    $user = \App\Models\User::findOrFail($player);

                                                                                    if($league->{"group_" . $grp_word . "_matches"}) {
                                                                                        $find_matches = json_decode($league->{"group_" . $grp_word . "_matches"}, true);

                                                                                        if(array_key_exists('match_'.$j, $find_matches)) {
                                                                                            $matches = $find_matches['match_'.$j];
                                                                                            $match = explode(" VS ", $matches);
                                                                                        }
                                                                                    }
                                                                                ?>


                                                                                <option value="{{ $user->id }}" @if($league->{"group_" . $grp_word . "_matches"}) @if(array_key_exists('match_'.$j, $find_matches)) {{ $match[0] == $user->id ? "selected" : "" }} @endif @else {{ old('group_'.$i.'_mat_'.$j.'_player_1') == $user->id ? "selected" : "" }} @endif>{{ $user->name }}</option>

                                                                            @endforeach

                                                                        </select>

                                                                        <div class="invalid-feedback">
                                                                            Please select a player.
                                                                        </div>
                                                                    </div>
                                                                    
                                                                    <div class="col-1 text-center" style="margin-top: 10px !important;margin: 0 auto;"><span class="badge bg-secondary">VS</span> </div>

                                                                    <div class="col-5">
                                                                        <select class="select2 form-control" id="twovalidationTooltip{{ ($j + 20000) * $i }}" name="group_{{ $i }}_mat_{{ $j }}_player_2" required>
                                                        
                                                                            <option value="">Select Player</option>
                                                                            
                                                                            @foreach($group_players as $player)
                                                                                <?php 
                                                                                    $user = \App\Models\User::findOrFail($player);

                                                                                    if($league->{"group_" . $grp_word . "_matches"}) {
                                                                                        $find_matches = json_decode($league->{"group_" . $grp_word . "_matches"}, true);

                                                                                        if(array_key_exists('match_'.$j, $find_matches)) {
                                                                                            $matches = $find_matches['match_'.$j];
                                                                                            $match = explode(" VS ", $matches);
                                                                                        }
                                                                                    }
                                                                                ?>


                                                                                <option value="{{ $user->id }}" @if($league->{"group_" . $grp_word . "_matches"}) @if(array_key_exists('match_'.$j, $find_matches)) {{ $match[1] == $user->id ? "selected" : "" }} @endif @else {{ old('group_'.$i.'_mat_'.$j.'_player_2') == $user->id ? "selected" : "" }} @endif>{{ $user->name }}</option>

                                                                            @endforeach


                                                                        </select>

                                                                        <div class="invalid-feedback">
                                                                            Please select a player.
                                                                        </div>
                                                                    </div>


                                                                    <div class="col-5">

                                                                        <button type="submit" class="btn btn-success btn-label waves-effect waves-light mt-3" @if($league->{"group_" . $grp_word . "_results"}) @if(array_key_exists('match_'.$j, $find_results)) disabled @endif @endif style="margin: 0 auto; width: 100%;" @if($league->{"group_" . $grp_word . "_matches"}) @if($find_matches) @if(array_key_exists('match_'.$j, $find_matches)) onclick="return confirm('Match draw has been performed already. Are you sure to change the draw and send SMS to the players again?');" @else onclick="return confirm('Are you sure to make the match draw and send SMS to the players?');" @endif @else onclick="return confirm('Are you sure to make the match draw and send SMS to the players?');" @endif @endif ><i class="bx bx-add-to-queue label-icon"></i> @if($league->{"group_" . $grp_word . "_matches"}) @if($find_matches) @if(array_key_exists('match_'.$j, $find_matches)) Change @else Submit @endif @else Submit @endif @else Submit @endif Match <i class="bx bx-right-arrow-circle bx-fade-right font-size-20 align-middle me-1"></i></button>

                                                                    </div>

                                                                </div>
                                                            </form>

                                                        </div>
                                                    </div>
                                                @endfor

                                            </div>
                                                       
                                            <br><br>

                                        @endif

                                    @endfor
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endif


            <!--- GROUP RESULTS -->
            @if($league->group_size && $league->player_per_group)
                
                @if($league->group_one_players)
                    <div class="row mt-4">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-body">
                                    @for($i = 1; $i < $league->group_size + 1; $i++)
                                        <?php 
                                            $grp_word = strtolower(ucwords((new NumberFormatter('en_IN', NumberFormatter::SPELLOUT))->format($i)));
                                        ?>

                                        @if($league->{"group_" . $grp_word . "_players"})
                                            <?php 
                                                $group_players = json_decode($league->{"group_" . $grp_word . "_players"}, true);
                                                $count_players = count($group_players);
                                                $gr_matches = ($count_players * ($count_players - 1)) / 2;
                                            ?>
                                            
                                            <div class="row">
                                                <div>
                                                    <h5 class="text-center text-info" style="font-weight:510;">Group - {{ $i }}</h5>
                                                </div>

                                                <i class="bx bx-chevrons-down bx-fade-down text-info display-6 text-center mt-1"></i>
                                                <span style="margin-top:20px;"></span>
                                                    
                                                <div class="col-xl-4"></div>
                                                <div class="col-xl-4 text-center">
                                                    <span class="badge bg-info mb-4">Results</span>
                                                </div>
                                                <div class="col-xl-4"></div>                                    
                                            </div>

                                            <br>

                                            <div class="row">

                                                @for($j = 1; $j < $gr_matches + 1; $j++)
                                                    <?php 
                                                        $match_word = strtolower(ucwords((new NumberFormatter('en_IN', NumberFormatter::SPELLOUT))->format($j)));
                                                    ?>

                                                    <div class="col-xl-6 mt-4">
                                                    
                                                        <form class="needs-validation" action="{{ route('submit.group.'.$grp_word.'.results', [$match_word, $league->id]) }}" method="post" novalidate="">
                                                        @csrf
                                                            <input type="hidden" name="chk_type" value="Tournament">
                                        
                                                            <label class="form-label text-dark" style="border:1px dashed #ddd; padding: 7px; border-radius: 2px; margin-bottom: 15px;"> Match - {{ $j }}</label>

                                                            <?php 
                                                                if($league->{"group_" . $grp_word . "_matches"}) {
                                                                    $find_matches = json_decode($league->{"group_" . $grp_word . "_matches"}, true);

                                                                    if(array_key_exists('match_'.$j, $find_matches)) {
                                                                        $matches = $find_matches['match_'.$j];
                                                                        $match = explode(" VS ", $matches);

                                                                        ${"p1_m".$j} = \App\Models\User::findOrFail($match[0]);
                                                                        ${"p2_m".$j} = \App\Models\User::findOrFail($match[1]);
                                                                    }

                                                                }

                                                                if($league->{"group_" . $grp_word . "_results"}) {
                                                                    
                                                                    for ($k = 1; $k < 4; $k++) {
                                                                        ${"group".$i."_m".$j."_s".$k} = [];
                                                                    }

                                                                    
                                                                    $find_results = json_decode($league->{"group_" . $grp_word . "_results"}, true);

                                                                    if(array_key_exists('match_'.$j, $find_results)) {
                                                                        $results = $find_results['match_'.$j];
                                                                        foreach($results as $result_array) {
                                                                            if(array_key_exists('set_1', $result_array)) {
                                                                                $rs = $result_array['set_1'];
                                                                                array_push(${"group".$i."_m".$j."_s1"}, $rs);
                                                                            }

                                                                            if(array_key_exists('set_2', $result_array)) {
                                                                                $rs2 = $result_array['set_2'];
                                                                                array_push(${"group".$i."_m".$j."_s2"}, $rs2);
                                                                            }

                                                                            if(array_key_exists('set_3', $result_array)) {
                                                                                $rs3 = $result_array['set_3'];
                                                                                array_push(${"group".$i."_m".$j."_s3"}, $rs3);
                                                                            }
                                                                        }
                                                                    }

                                                                }
                                                            ?>


                                                            <input type="hidden" name="p1_m{{ $j }}" value="@if($league->{"group_" . $grp_word . "_matches"}) @if(array_key_exists('match_'.$j, $find_matches)) {{ ${"p1_m".$j}->id }} @endif @endif">
                                                            <input type="hidden" name="p2_m{{ $j }}" value="@if($league->{"group_" . $grp_word . "_matches"}) @if(array_key_exists('match_'.$j, $find_matches)) {{ ${"p2_m".$j}->id }} @endif @endif">

                                                            <div class="row mb-2">
                                                                
                                                                <label for="validationTooltip100" class="col-xl-4 col-form-label">@if($league->{"group_" . $grp_word . "_matches"}) @if(array_key_exists('match_'.$j, $find_matches)) {{ ${"p1_m" . $j}->name }} @else N/A @endif @else N/A @endif</label>
                                                                
                                                                <div class="col-xl-8" style="margin-bottom: 0.7rem!important;">
                                                                    <div class="row">
                                                                        <div class="col-4">
                                                                            <input type="number" min="0" max="7" class="form-control" id="validationTooltip01" placeholder="1st Set" @if($league->{"group_" . $grp_word . "_results"}) value="{{ ${"group".$i."_m".$j."_s1"} ? ${"group".$i."_m".$j."_s1"}[0] : '' }}" @endif name="p1_m{{ $j }}_s1" required="">
                                                                        </div>

                                                                        <div class="col-4">
                                                                            <input type="number" min="0" max="7" class="form-control" id="validationTooltip01" placeholder="2nd Set" @if($league->{"group_" . $grp_word . "_results"}) value="{{ ${"group".$i."_m".$j."_s2"} ? ${"group".$i."_m".$j."_s2"}[0] : '' }}" @endif name="p1_m{{ $j }}_s2" required="">
                                                                        </div>

                                                                        <div class="col-4">
                                                                            <input type="number" min="0" max="7" class="form-control" id="validationTooltip01" placeholder="3rd Set" @if($league->{"group_" . $grp_word . "_results"}) value="{{ ${"group".$i."_m".$j."_s3"} ? ${"group".$i."_m".$j."_s3"}[0] : '' }}" @endif name="p1_m{{ $j }}_s3">
                                                                        </div>
                                                                    </div>
                                                                    
                                                                </div>

                                                            </div>

                                                            <div class="row mb-2">
                                                                <label for="validationTooltip100" class="col-xl-4 col-form-label">@if($league->{"group_" . $grp_word . "_matches"}) @if(array_key_exists('match_'.$j, $find_matches)) {{ ${"p2_m" . $j}->name }} @else N/A @endif @else N/A @endif </label>
                                                                
                                                                <div class="col-xl-8">
                                                                    <div class="row">
                                                                        <div class="col-4">
                                                                            <input type="number" min="0" max="7" class="form-control" id="validationTooltip01" placeholder="1st Set" @if($league->{"group_" . $grp_word . "_results"}) value="{{ ${"group".$i."_m".$j."_s1"} ? ${"group".$i."_m".$j."_s1"}[1] : '' }}" @endif name="p2_m{{ $j }}_s1" required="">
                                                                        </div>

                                                                        <div class="col-4">
                                                                            <input type="number" min="0" max="7" class="form-control" id="validationTooltip01" placeholder="2nd Set" @if($league->{"group_" . $grp_word . "_results"}) value="{{ ${"group".$i."_m".$j."_s2"} ? ${"group".$i."_m".$j."_s2"}[1] : '' }}" @endif name="p2_m{{ $j }}_s2" required="">
                                                                        </div>

                                                                        <div class="col-4">
                                                                            <input type="number" min="0" max="7" class="form-control" id="validationTooltip01" placeholder="3rd Set" @if($league->{"group_" . $grp_word . "_results"}) value="{{ ${"group".$i."_m".$j."_s3"} ? ${"group".$i."_m".$j."_s3"}[1] : '' }}" @endif name="p2_m{{ $j }}_s3">
                                                                        </div>
                                                                    </div>
                                                                    
                                                                </div>
                                                            </div>

                                                            <?php 
                                                                $find_status = json_decode($league->{"group_" . $grp_word . "_status"}, true);
                                                            ?>

                                                            <div class="row mb-2" style="margin-top: 20px;">
                                                                <label for="group_{{ $i }}_mat_{{ $j }}_status" class="col-xl-4 col-form-label text-info">Status</label>
                                                                
                                                                <div class="col-xl-8">
                                                                    <div class="mb-1 position-relative">
                                                                        <select class="form-control select2" id="group_{{ $i }}_mat_{{ $j }}_status" name="group_{{ $i }}_mat_{{ $j }}_status">
                                                    
                                                                            <option value="">Select Status</option>

                                                                            <option value="Withdraw" @if($find_status) @if(array_key_exists('match_'.$j, $find_status)) {{ $find_status['match_'.$j] == 'Withdraw' ? "selected" : "" }} @endif @endif>Withdraw</option>

                                                                            <option value="Retired" @if($find_status) @if(array_key_exists('match_'.$j, $find_status)) {{ $find_status['match_'.$j] == 'Retired' ? "selected" : "" }} @endif @endif>Retired</option>

                                                                            <option value="Decided by Organisers" @if($find_status) @if(array_key_exists('match_'.$j, $find_status)) {{ $find_status['match_'.$j] == 'Decided by Organisers' ? "selected" : "" }} @endif @endif>Decided by Organisers</option>
                                                                            
                                                                        </select>

                                                                        <div class="valid-feedback">
                                                                            Looks good!
                                                                        </div>
                                                                    </div>
                                                                    
                                                                </div>
                                                            </div>

                                                            <div class="row">
                                                                <div class="col-4"></div>
                                                                <div class="col-8 mb-4">
                                                                    <button type="submit" class="btn btn-success btn-label waves-effect waves-light mt-3" style="width: 100%; margin: 0 auto;" onclick="return confirm('Are you sure to submit the match result? Please be confirm as you can\'t be able to change the result later.');"

                                                                        @if($league->{"group_" . $grp_word . "_results"}) 
                                                                            @if(array_key_exists('match_'.$j, $find_results)) disabled 
                                                                            @endif 
                                                                        @endif>

                                                                         <i class="bx bx-add-to-queue label-icon"></i> 
                                                                         @if($league->{"group_" . $grp_word . "_results"}) 
                                                                            @if(array_key_exists('match_'.$j, $find_results)) 
                                                                                Already Submitted!
                                                                            @else 
                                                                                Submit Result
                                                                            @endif
                                                                        @else
                                                                            Submit Result
                                                                        @endif <i class="bx bx-right-arrow-circle bx-fade-right font-size-20 align-middle me-1"></i>
                                                                    </button>  

                                                                </div>
                                                            </div>
                                                        </form>

                                                    </div>
                                                @endfor

                                            </div>
                                                       
                                            <br><br>

                                        @endif

                                    @endfor
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

            @endif

            
            <!--- GROUP RETIRES -->
            @if($league->group_size && $league->player_per_group)
                @if($league->group_one_players)
                    <div class="row mt-4">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-body">
                                    @for($i = 1; $i < $league->group_size + 1; $i++)
                                        <?php 
                                            $grp_word = strtolower(ucwords((new NumberFormatter('en_IN', NumberFormatter::SPELLOUT))->format($i)));
                                        ?>
    
                                        @if($league->{"group_" . $grp_word . "_status"})
                                            <?php 
                                                $find_statuses = json_decode($league->{"group_" . $grp_word . "_status"}, true);
                                                $group_players = json_decode($league->{"group_" . $grp_word . "_players"}, true);
                                                $count_players = count($group_players);
                                                $gr_matches = ($count_players * ($count_players - 1)) / 2;
    
                                                $group_ad_retires = json_decode($league->{"group_" . $grp_word . "_retires"}, true);
                                            ?>
                                            
                                            @if(in_array('Retired', $find_statuses))
                                                <?php 
                                                    ${"group_".$i."_mat_retires"} = [];
                                                ?>
                                                <div class="row">
                                                    <div>
                                                        <h5 class="text-center text-info" style="font-weight:510;">Group - {{ $i }}</h5>
                                                    </div>
    
                                                    <i class="bx bx-chevrons-down bx-fade-down text-info display-6 text-center mt-1"></i>
                                                    <span style="margin-top:20px;"></span>
                                                        
                                                    <div class="col-xl-4"></div>
                                                    <div class="col-xl-4 text-center">
                                                        <span class="badge bg-info mb-4">Who're Retiring</span>
                                                    </div>
                                                    <div class="col-xl-4"></div>                                    
                                                </div>
    
                                                <br>
    
                                                <div class="row">
    
                                                    @for($j = 1; $j < $gr_matches + 1; $j++)
                                                        <?php 
                                                            $match_word = strtolower(ucwords((new NumberFormatter('en_IN', NumberFormatter::SPELLOUT))->format($j)));
                                                        ?>
                                                        <div class="col-xl-6">
                                                            <div class="mb-3 position-relative">
                                                                
                                                                <form class="needs-validation" action="{{ route('submit.group.retires', [$grp_word, $league->id]) }}" method="post" novalidate="">
                                                                    @csrf
                                                                        <input type="hidden" name="chk_type" value="Tournament">
    
                                                
                                                                    <div class="row">
    
                                                                        <div class="col-6">
                                                                            <label class="form-label text-dark"> Select Player </label>
    
                                                                            <select class="form-control select2" id="group_{{ $grp_word }}_mat_{{ $j }}_retire_player" name="group_{{ $grp_word }}_mat_retire_player" required>
                                                            
                                                                                <option value="">Select Player</option>
                                                                                
                                                                                <?php 
                                                                                    
                                                                                    if($group_ad_retires) {               
                                                                                        if(array_key_exists('match_'.$j, $group_ad_retires)) {
                                                                                            $retire_player = $group_ad_retires['match_'.$j];
                                                                                            ${"group_".$i."_mat_retires"}['match_'.$j] = \App\Models\User::findOrFail($retire_player)->name;
                                                                                        }
                                                                                    }
                                                                        
                                                                                ?>
    
                                                                                @foreach($players as $player)
                                                                                    <?php 
                                                                                        $user = \App\Models\User::findOrFail($player);
                                                                                    ?>
    
    
                                                                                    <option value="{{ $user->id }}" @if($group_ad_retires) @if(array_key_exists('match_'.$j, $group_ad_retires)) {{ $retire_player == $user->id ? "selected" : "" }} @else {{ old('group_'.$grp_word.'_mat_retire_player') == $user->id ? "selected" : "" }} @endif @else {{ old('group_'.$grp_word.'_mat_retire_player') == $user->id ? "selected" : "" }} @endif>{{ $user->name }}</option>
    
                                                                                @endforeach
    
                                                                            </select>
    
                                                                            <div class="invalid-feedback">
                                                                                Please select a player.
                                                                            </div>
                                                                        </div>
                                                                        
                                                                        
                                                                        <div class="col-6">
                                                                            <label class="form-label text-dark"> Define Match </label>
    
                                                                            <select class="form-control select2" id="group_{{ $grp_word }}_mat_{{ $j }}_retire" name="group_{{ $grp_word }}_mat_retire" required>
                                                            
                                                                                
                                                                                <?php 
                                                                                    $match_word = strtolower(ucwords((new NumberFormatter('en_IN', NumberFormatter::SPELLOUT))->format($j)));
                                                                                ?>
                                                                                <option value="match_{{ $j }}" @if($group_ad_retires) @if(array_key_exists('match_'.$j, $group_ad_retires)) selected @endif @endif>Match - {{ $j }}</option>
                                                                                
    
                                                                            </select>
    
                                                                            <div class="invalid-feedback">
                                                                                Please select/define a match.
                                                                            </div>
                                                                        </div>
    
    
                                                                        <div class="col-12">
                                                                            <button type="submit" class="btn btn-warning btn-label waves-effect waves-light mt-3" style="margin: 0 auto; width: 100%;" @if($league->{"group_" . $grp_word . "_retires"}) @if(array_key_exists('match_'.$j, $group_ad_retires)) onclick="return confirm('Match retirement has been performed already. Are you sure to change?');" @else onclick="return confirm('Are you sure to make the match retirement?');" @endif @else onclick="return confirm('Are you sure to make the match retirement?');" @endif><i class="bx bx-add-to-queue label-icon"></i> @if($league->{"group_" . $grp_word . "_retires"}) @if(array_key_exists('match_'.$j, $group_ad_retires))  Change @else Submit @endif @else Submit @endif Retirement <i class="bx bx-right-arrow-circle bx-fade-right font-size-20 align-middle me-1"></i></button>
                                                                        </div>
    
                                                                    </div>  
    
                                                                </form>
    
                                                            </div>
                                                        </div>
                                                    @endfor
    
                                                </div>
                                                           
                                                <br><br>
    
                                            @endif
    
                                        @endif
    
                                    @endfor
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endif


            <!--- GROUP POINTS -->
            @if($league->group_size && $league->player_per_group)
                
                @if($league->group_one_players)
                    <div class="row mt-4">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-body">
                                    @for($i = 1; $i < $league->group_size + 1; $i++)
                                        <?php 
                                            $grp_word = strtolower(ucwords((new NumberFormatter('en_IN', NumberFormatter::SPELLOUT))->format($i)));
                                        ?>

                                        @if($league->{"group_" . $grp_word . "_players"})
                                            <?php 
                                                $group_players = json_decode($league->{"group_" . $grp_word . "_players"}, true);
                                                $count_players = count($group_players);
                                                $gr_matches = ($count_players * ($count_players - 1)) / 2;
                                            ?>
                                            
                                            <div class="row">
                                                <div>
                                                    <h5 class="text-center text-info" style="font-weight:510;">Group - {{ $i }}</h5>
                                                </div>

                                                <i class="bx bx-chevrons-down bx-fade-down text-info display-6 text-center mt-1"></i>
                                                <span style="margin-top:20px;"></span>
                                                    
                                                <div class="col-xl-4"></div>
                                                <div class="col-xl-4 text-center">
                                                    <span class="badge bg-info mb-4">Points</span>
                                                </div>
                                                <div class="col-xl-4"></div>                                    
                                            </div>

                                            <br>

                                            <div class="row">
                                                <form class="needs-validation" action="{{ route('submit.group.points', [$grp_word, $league->id]) }}" method="post" novalidate="">
                                                    @csrf

                                                    <input type="hidden" name="chk_type" value="Tournament">
                                                    
                                                    @for($j = 1; $j < $gr_matches + 1; $j++)
                                                        <?php 
                                                            $match_word = strtolower(ucwords((new NumberFormatter('en_IN', NumberFormatter::SPELLOUT))->format($j)));
                                                        ?>

                                                        <div class="col-xl-12 mt-4">                                                   
                                            
                                                            <label class="form-label text-dark" style="border:1px dashed #ddd; padding: 7px; border-radius: 2px; margin-bottom: 15px;"> Match - {{ $j }}</label>

                                                            <?php 
                                                                if($league->{"group_" . $grp_word . "_matches"}) {
                                                                    $find_matches = json_decode($league->{"group_" . $grp_word . "_matches"}, true);
                                                                    
                                                                    if(array_key_exists('match_'.$j, $find_matches)) {
                                                                        $matches = $find_matches['match_'.$j];
                                                                        $match = explode(" VS ", $matches);

                                                                        ${"p1_m".$j} = \App\Models\User::findOrFail($match[0]);
                                                                        ${"p2_m".$j} = \App\Models\User::findOrFail($match[1]);
                                                                    }

                                                                    if($league->{"group_" . $grp_word . "_winners"}) {
                                                                        $find_winners = json_decode($league->{"group_" . $grp_word . "_winners"}, true);
                                                                        
                                                                        if(array_key_exists('match_'.$j, $find_winners)){
                                                                            $winner = $find_winners['match_'.$j];
                                                                        }
                                                                    }

                                                                }


                                                                if($league->{"group_" . $grp_word . "_points"}) {
                                                                    ${"group".$i."_p1_m".$j} = '';
                                                                    ${"group".$i."_p2_m".$j} = '';

                                                                    $find_points = (array)json_decode($league->{"group_" . $grp_word . "_points"}, true);

                                                                    if(array_key_exists('match_'.$j, $find_points)) {
                                                                        $results = $find_points['match_'.$j];
                                                                        
                                                                        ${"group".$i."_p1_m".$j} = $results[${"p1_m".$j}->id];
                                                                        ${"group".$i."_p2_m".$j} = $results[${"p2_m".$j}->id];
                                                                    }
                                                                }

                                                                
                                                            ?>
                                                            


                                                            <input type="hidden" name="p1_m{{ $j }}_id" value="
                                                                @if($league->{"group_" . $grp_word . "_matches"}) 
                                                                    @if(array_key_exists('match_'.$j, $find_matches)) 
                                                                        {{ ${"p1_m".$j}->id ?? '' }} 
                                                                    @endif 
                                                                @endif"
                                                            >

                                                            <input type="hidden" name="p2_m{{ $j }}_id" value="
                                                                @if($league->{"group_" . $grp_word . "_matches"}) 
                                                                    @if(array_key_exists('match_'.$j, $find_matches)) 
                                                                        {{ ${"p2_m".$j}->id ?? '' }} 
                                                                    @endif 
                                                                @endif"
                                                            >


                                                            <div class="row mb-2">
                                                                
                                                                <label for="validationTooltip101" class="col-xl-4 col-form-label">
                                                                    @if($league->{"group_" . $grp_word . "_matches"}) 
                                                                        @if(array_key_exists('match_'.$j, $find_matches)) 
                                                                            {{ ${"p1_m" . $j}->name ?? "N/A" }} 
                                                                            @if($league->{"group_" . $grp_word . "_winners"})
                                                                                @if(array_key_exists('match_'.$j, $find_winners)) 
                                                                                    <span class="text-danger text-center" style="font-weight: 500; font-size: 10px;">
                                                                                        {{ $winner == ${"p1_m" . $j}->id ? "(W)" : "" }}
                                                                                    </span>
                                                                                @endif 
                                                                            @endif
                                                                        @else 
                                                                            N/A 
                                                                        @endif 
                                                                    @else 
                                                                        N/A 
                                                                    @endif
                                                                </label>
                                                                
                                                                <div class="col-xl-8" style="margin-bottom: 0.7rem!important;">
                                                                    <div class="row">
                                                                        <div class="col-12">
                                                                            <input type="number" min="0" class="form-control" placeholder="Enter point" 
                                                                                @if($league->{"group_" . $grp_word . "_points"}) 
                                                                                    value="{{ isset(${"group".$i."_p1_m".$j}) ? ${"group".$i."_p1_m".$j} : '' }}"
                                                                                @endif 
                                                                                name="p1_m{{ $j }}"
                                                                            >
                                                                        </div>
                                                                    </div>
                                                                    
                                                                </div>

                                                            </div>

                                                            <div class="row mb-2">

                                                                <label for="validationTooltip102" class="col-xl-4 col-form-label">
                                                                    @if($league->{"group_" . $grp_word . "_matches"}) 
                                                                        @if(array_key_exists('match_'.$j, $find_matches)) 
                                                                            {{ ${"p2_m" . $j}->name ?? "N/A" }} 
                                                                            @if($league->{"group_" . $grp_word . "_winners"})
                                                                                @if(array_key_exists('match_'.$j, $find_winners)) 
                                                                                    <span class="text-danger text-center" style="font-weight: 500; font-size: 10px;">
                                                                                        {{ $winner == ${"p2_m" . $j}->id ? "(W)" : "" }}
                                                                                    </span>
                                                                                @endif 
                                                                            @endif
                                                                        @else 
                                                                            N/A 
                                                                        @endif 
                                                                    @else 
                                                                        N/A 
                                                                    @endif 
                                                                </label>
                                                                
                                                                <div class="col-xl-8">
                                                                    <div class="row">
                                                                        <div class="col-12">
                                                                            <input type="number" min="0" class="form-control" placeholder="Enter point" 
                                                                                @if($league->{"group_" . $grp_word . "_points"}) 
                                                                                    value="{{ isset(${"group".$i."_p2_m".$j}) ? ${"group".$i."_p2_m".$j} : '' }}"
                                                                                @endif 
                                                                                name="p2_m{{ $j }}"
                                                                            >
                                                                        </div>
                                                                    </div>
                                                                    
                                                                </div>
                                                            </div>

                                                        </div>
                                                    @endfor

                                                    <div class="row">
                                                        <div class="col-xl-4"></div>
                                                        <div class="col-xl-5">
                                                            <button type="submit" class="btn btn-info btn-label waves-effect waves-light mt-4" style="width: 100%; margin: 0 auto;"  
                                                                onclick="return confirm('Are you sure to submit the points?');" 
                                                            >
                                                                <i class="bx bx-add-to-queue label-icon"></i> Save Points 
                                                                <i class="bx bx-right-arrow-circle bx-fade-right font-size-20 align-middle me-1"></i>
                                                            </button>
                                                        </div>
                                                        <div class="col-xl-3"></div>
                                                    </div>
                                                </form>

                                            </div>
                                                       
                                            <br><br>

                                        @endif

                                    @endfor

                                </div>
                            </div>
                        </div>
                    </div>
                @endif

            @endif

            <br>

            @if($league->group_size && $league->player_per_group)
                @if($league->group_one_players)
                
                    <header class="hero">
                        <div class="hero-wrap">
                         <p class="intro" id="intro">Tennis4All</p>
                             <h1 id="headline">Tournament</h1>
                             <p class="year"><i class="fa fa-star"></i> {{ $league->name }} <i class="fa fa-star"></i></p>
                         <p>Group Fixtures ({{ count($players) }} Players)</p>
                       </div>
                    </header>

                    <section id="bracket">
                        <div class="container" style="overflow: scroll;">
                            <div class="split split-one">

                                    @for($i = 1; $i < $league->group_size + 1; $i++)
                                        <?php 
                                            $grp_word = strtolower(ucwords((new NumberFormatter('en_IN', NumberFormatter::SPELLOUT))->format($i)));
                                        ?>

                                        <?php
                                            if($league->{"group_" . $grp_word . "_deadline"}) {
                                                ${"t_d_group" . $i} = json_decode($league->{"group_" . $grp_word . "_deadline"});
                                                ${"strt_g". $i} = explode(', ', ${"t_d_group" . $i}->start);
                                                ${"endd_g". $i} = explode(', ', ${"t_d_group" . $i}->end);
                                            }
                                        ?>

                                        <?php
                                            if($league->{"group_" . $grp_word . "_players"}) {
                                                $group_players = json_decode($league->{"group_" . $grp_word . "_players"}, true);
                                                $count_players = count($group_players);
                                                $gr_matches = ($count_players * ($count_players - 1)) / 2;
                                            } else {
                                                $gr_matches = ($league->player_per_group * ($league->player_per_group - 1)) / 2;
                                            }
                                        ?>

                                        @if($league->{"group_" . $grp_word . "_matches"})

                                            <?php 
                                                $group_matches = json_decode($league->{"group_" . $grp_word . "_matches"}, true);
                                            ?>
                                            
                                            
                                            <div class="col-6">
                                                <div class="round round-one current">
                                                    <div class="round-details">Group - {{ $i }}<br/><span class="date">@if($league->{"group_" . $grp_word . "_deadline"}) {{ ${"strt_g". $i}[0] }} - {{ ${"endd_g". $i}[0] }} @else N/A @endif</span>
                                                    </div>
                                                    
                                                    @for($j = 1; $j < $gr_matches + 1; $j++)
                                                        @if (array_key_exists('match_'.$j, $group_matches))

                                                            <?php 
                                                                $get_matches = $group_matches['match_'.$j];
                                                                $vs_match = explode(" VS ", $get_matches);
                                                            ?>

                                                            @if ($league->{"group_" . $grp_word . "_results"})

                                                                <?php 
                                                                    $group_results = json_decode($league->{"group_" . $grp_word . "_results"}, true);
                                                                ?>
                                                                
                                                                @if($league->{"group_" . $grp_word . "_status"})

                                                                    <?php 
                                                                        $group_status = json_decode($league->{"group_" . $grp_word . "_status"}, true);
                                                                    ?>

                                                                    @if (array_key_exists('match_'.$j, $group_status))
                                                                        @if($group_status['match_'.$j])
                                                                            
                                                                            <?php
                                                                                if (${"group". $i ."_m" . $j . "_s1"}[0] > ${"group". $i ."_m" . $j . "_s1"}[1] && ${"group". $i ."_m" . $j . "_s2"}[0] > ${"group". $i ."_m" . $j . "_s2"}[1]) {

                                                                                    ${"group". $i ."_m" . $j . "_p1_total"} = 2;
                                                                                    ${"group". $i ."_m" . $j . "_p2_total"} = 0;

                                                                                } elseif (${"group". $i ."_m" . $j . "_s1"}[1] > ${"group". $i ."_m" . $j . "_s1"}[0] && ${"group". $i ."_m" . $j . "_s2"}[1] > ${"group". $i ."_m" . $j . "_s2"}[0]) {

                                                                                    ${"group". $i ."_m" . $j . "_p1_total"} = 0;
                                                                                    ${"group". $i ."_m" . $j . "_p2_total"} = 2;

                                                                                } elseif (${"group". $i ."_m" . $j . "_s1"}[0] > ${"group". $i ."_m" . $j . "_s1"}[1] && ${"group". $i ."_m" . $j . "_s2"}[0] < ${"group". $i ."_m" . $j . "_s2"}[1] && ${"group". $i ."_m" . $j . "_s3"}[0] > ${"group". $i ."_m" . $j . "_s3"}[1]) {

                                                                                    ${"group". $i ."_m" . $j . "_p1_total"} = 2;
                                                                                    ${"group". $i ."_m" . $j . "_p2_total"} = 1;

                                                                                } elseif (${"group". $i ."_m" . $j . "_s1"}[0] < ${"group". $i ."_m" . $j . "_s1"}[1] && ${"group". $i ."_m" . $j . "_s2"}[0] > ${"group". $i ."_m" . $j . "_s2"}[1] && ${"group". $i ."_m" . $j . "_s3"}[0] < ${"group". $i ."_m" . $j . "_s3"}[1]) {

                                                                                    ${"group". $i ."_m" . $j . "_p1_total"} = 1;
                                                                                    ${"group". $i ."_m" . $j . "_p2_total"} = 2;

                                                                                } elseif (${"group". $i ."_m" . $j . "_s1"}[0] < ${"group". $i ."_m" . $j . "_s1"}[1] && ${"group". $i ."_m" . $j . "_s2"}[0] > ${"group". $i ."_m" . $j . "_s2"}[1] && ${"group". $i ."_m" . $j . "_s3"}[0] > ${"group". $i ."_m" . $j . "_s3"}[1]) {

                                                                                    ${"group". $i ."_m" . $j . "_p1_total"} = 2;
                                                                                    ${"group". $i ."_m" . $j . "_p2_total"} = 1;

                                                                                } elseif (${"group". $i ."_m" . $j . "_s1"}[0] > ${"group". $i ."_m" . $j . "_s1"}[1] && ${"group". $i ."_m" . $j . "_s2"}[0] < ${"group". $i ."_m" . $j . "_s2"}[1] && ${"group". $i ."_m" . $j . "_s3"}[0] < ${"group". $i ."_m" . $j . "_s3"}[1]) {

                                                                                    ${"group". $i ."_m" . $j . "_p1_total"} = 1;
                                                                                    ${"group". $i ."_m" . $j . "_p2_total"} = 2;

                                                                                }
                                                                            ?>                                                                             

                                                                            <ul class="matchup">

                                                                                <span class="custooltip">
                                                                                    <li
                                                                                        class="team team-top @if (${"group". $i ."_m" . $j . "_p1_total"} > ${"group". $i ."_m" . $j . "_p2_total"}) winnerclractive @endif">
                                                                                        {{ \Illuminate\Support\Str::limit(${"group_". $i ."_mat_" . $j}[0], 100) }}

                                                                                        @if (${"group". $i ."_m". $j ."_p1_total"} < ${"group". $i ."_m". $j ."_p2_total"})
                                                                                            @if($group_status['match_'.$j] == 'Retired')
                                                                                                @if($league->{"group_". $grp_word ."_retires"})
                                                                                                <?php 
                                                                                                    $group_retires = json_decode($league->{"group_" . $grp_word . "_retires"}, true);
                                                                                                ?>
                                                                                                    @if (array_key_exists('match_'.$j, $group_retires))
                                                                                                        @if($group_retires['match_'.$j] == $vs_match[0])
                                                                                                            
                                                                                                            <span class="text-danger text-center" style="font-size: 8px;font-weight: 500;">(R)</span>

                                                                                                        @endif
                                                                                                    @endif
                                                                                                @endif
                                                                                                <span
                                                                                                    class="score">{{ ${"group". $i ."_m". $j ."_s1"}[0] }}
                                                                                                    {{ ${"group". $i ."_m". $j ."_s2"}[0] }} {{ ${"group". $i ."_m". $j ."_s3"}[0] }}</span>
                                                                                            @elseif($group_status['match_'.$j] == 'Withdraw')
                                                                                                <span class="text-danger text-center" style="font-size: 8px;font-weight: 500;">(W)</span>
                                                                                                <span
                                                                                                    class="score">&#8212;</span>
                                                                                            @elseif($group_status['match_'.$j] == 'Decided by Organisers')
                                                                                                <span class="text-danger text-center" style="font-size: 8px;font-weight: 500;">(D)</span>
                                                                                                <span
                                                                                                    class="score">&#8212;</span>
                                                                                            @endif

                                                                                        @else
                                                                                            @if($group_status['match_'.$j] == 'Retired')
                                                                                                @if($league->{"group_". $grp_word ."_retires"})
                                                                                                <?php 
                                                                                                    $group_retires = json_decode($league->{"group_" . $grp_word . "_retires"}, true);
                                                                                                ?>
                                                                                                    @if (array_key_exists('match_'.$j, $group_retires))
                                                                                                        @if($group_retires['match_'.$j] == $vs_match[0])
                                                                                                            
                                                                                                            <span class="text-danger text-center" style="font-size: 8px;font-weight: 500;">(R)</span>

                                                                                                        @endif
                                                                                                    @endif
                                                                                                @endif
                                                                                                <span
                                                                                                class="score winnerclractive">{{ ${"group". $i ."_m". $j ."_s1"}[0] }}
                                                                                                {{ ${"group". $i ."_m". $j ."_s2"}[0] }} {{ ${"group". $i ."_m". $j ."_s3"}[0] }}</span>
                                                                                            @elseif($group_status['match_'.$j] == 'Withdraw')
                                                                                                <span
                                                                                                class="score winnerclractive">&#8212;</span>
                                                                                            @elseif($group_status['match_'.$j] == 'Decided by Organisers')
                                                                                                <span
                                                                                                class="score winnerclractive">&#8212;</span>
                                                                                            @endif
                                                                                                
                                                                                        @endif

                                                                                    </li>
                                                                                    <span class="custooltiptext">{{ ${"group_". $i ."_mat_" . $j}[0] }}

                                                                                        @if($group_status)
                                                                                            @if($group_status['match_'.$j] == 'Retired')
                                                                                                @if($league->{"group_". $grp_word ."_retires"})
                                                                                                <?php 
                                                                                                    $group_retires = json_decode($league->{"group_" . $grp_word . "_retires"}, true);
                                                                                                ?>
                                                                                                    @if (array_key_exists('match_'.$j, $group_retires))
                                                                                                        @if($group_retires['match_'.$j] == $vs_match[0])
                                                                                                            
                                                                                                            ({{ $group_status['match_'.$j] }})

                                                                                                        @endif
                                                                                                    @endif
                                                                                                @endif
                                                                                            @else
                                                                                                @if (${"group". $i ."_m". $j ."_p1_total"} < ${"group". $i ."_m". $j ."_p2_total"}) 
                                                                                                    ({{ $group_status['match_'.$j] }}) 
                                                                                                @endif
                                                                                            @endif
                                                                                        @endif

                                                                                    </span>

                                                                                </span>

                                                                                <span class="custooltip">
                                                                                    
                                                                                    <li
                                                                                        class="team team-top @if (${"group". $i ."_m" . $j . "_p1_total"} < ${"group". $i ."_m" . $j . "_p2_total"}) winnerclractive @endif">
                                                                                        {{ \Illuminate\Support\Str::limit(${"group_". $i ."_mat_" . $j}[1], 100) }}

                                                                                        
                                                                                        @if (${"group". $i ."_m". $j ."_p1_total"} > ${"group". $i ."_m". $j ."_p2_total"})
                                                                                            @if($group_status['match_'.$j] == 'Retired')
                                                                                                @if($league->{"group_". $grp_word ."_retires"})
                                                                                                <?php 
                                                                                                    $group_retires = json_decode($league->{"group_" . $grp_word . "_retires"}, true);
                                                                                                ?>
                                                                                                    @if (array_key_exists('match_'.$j, $group_retires))
                                                                                                        @if($group_retires['match_'.$j] == $vs_match[1])
                                                                                                            
                                                                                                            <span class="text-danger text-center" style="font-size: 8px;font-weight: 500;">(R)</span>

                                                                                                        @endif
                                                                                                    @endif
                                                                                                @endif
                                                                                                <span
                                                                                                    class="score">{{ ${"group". $i ."_m". $j ."_s1"}[1] }}
                                                                                                    {{ ${"group". $i ."_m". $j ."_s2"}[1] }} {{ ${"group". $i ."_m". $j ."_s3"}[1] }}</span>
                                                                                            @elseif($group_status['match_'.$j] == 'Withdraw')
                                                                                                <span class="text-danger text-center" style="font-size: 8px;font-weight: 500;">(W)</span>
                                                                                                <span
                                                                                                    class="score">&#8212;</span>
                                                                                            @elseif($group_status['match_'.$j] == 'Decided by Organisers')
                                                                                                <span class="text-danger text-center" style="font-size: 8px;font-weight: 500;">(D)</span>
                                                                                                <span
                                                                                                    class="score">&#8212;</span>
                                                                                            @endif

                                                                                        @else
                                                                                            @if($group_status['match_'.$j] == 'Retired')
                                                                                                @if($league->{"group_". $grp_word ."_retires"})
                                                                                                <?php 
                                                                                                    $group_retires = json_decode($league->{"group_" . $grp_word . "_retires"}, true);
                                                                                                ?>
                                                                                                    @if (array_key_exists('match_'.$j, $group_retires))
                                                                                                        @if($group_retires['match_'.$j] == $vs_match[1])
                                                                                                            
                                                                                                            <span class="text-danger text-center" style="font-size: 8px;font-weight: 500;">(R)</span>

                                                                                                        @endif
                                                                                                    @endif
                                                                                                @endif
                                                                                                <span
                                                                                                class="score winnerclractive">{{ ${"group". $i ."_m". $j ."_s1"}[1] }}
                                                                                                {{ ${"group". $i ."_m". $j ."_s2"}[1] }} {{ ${"group". $i ."_m". $j ."_s3"}[1] }}</span>
                                                                                            @elseif($group_status['match_'.$j] == 'Withdraw')
                                                                                                <span
                                                                                                class="score winnerclractive">&#8212;</span>
                                                                                            @elseif($group_status['match_'.$j] == 'Decided by Organisers')
                                                                                                <span
                                                                                                class="score winnerclractive">&#8212;</span>
                                                                                            @endif
                                                                                                
                                                                                        @endif

                                                                                    </li>
                                                                                    <span class="custooltiptext">{{ ${"group_". $i ."_mat_" . $j}[1] }}

                                                                                        @if($group_status)
                                                                                            @if($group_status['match_'.$j] == 'Retired')
                                                                                                @if($league->{"group_". $grp_word ."_retires"})
                                                                                                <?php 
                                                                                                    $group_retires = json_decode($league->{"group_" . $grp_word . "_retires"}, true);
                                                                                                ?>
                                                                                                    @if (array_key_exists('match_'.$j, $group_retires))
                                                                                                        @if($group_retires['match_'.$j] == $vs_match[1])
                                                                                                            
                                                                                                            ({{ $group_status['match_'.$j] }})

                                                                                                        @endif
                                                                                                    @endif
                                                                                                @endif
                                                                                            @else
                                                                                                @if (${"group". $i ."_m". $j ."_p1_total"} > ${"group". $i ."_m". $j ."_p2_total"}) 
                                                                                                    ({{ $group_status['match_'.$j] }}) 
                                                                                                @endif
                                                                                            @endif
                                                                                        @endif

                                                                                    </span>

                                                                                </span>

                                                                            </ul>

                                                                        @else
                                                                            @if (array_key_exists('match_'.$j, $group_results))
                                                                                
                                                                                <?php
                                                                                    if (${"group". $i ."_m" . $j . "_s1"}[0] > ${"group". $i ."_m" . $j . "_s1"}[1] && ${"group". $i ."_m" . $j . "_s2"}[0] > ${"group". $i ."_m" . $j . "_s2"}[1]) {

                                                                                        ${"group". $i ."_m" . $j . "_p1_total"} = 2;
                                                                                        ${"group". $i ."_m" . $j . "_p2_total"} = 0;

                                                                                    } elseif (${"group". $i ."_m" . $j . "_s1"}[1] > ${"group". $i ."_m" . $j . "_s1"}[0] && ${"group". $i ."_m" . $j . "_s2"}[1] > ${"group". $i ."_m" . $j . "_s2"}[0]) {

                                                                                        ${"group". $i ."_m" . $j . "_p1_total"} = 0;
                                                                                        ${"group". $i ."_m" . $j . "_p2_total"} = 2;

                                                                                    } elseif (${"group". $i ."_m" . $j . "_s1"}[0] > ${"group". $i ."_m" . $j . "_s1"}[1] && ${"group". $i ."_m" . $j . "_s2"}[0] < ${"group". $i ."_m" . $j . "_s2"}[1] && ${"group". $i ."_m" . $j . "_s3"}[0] > ${"group". $i ."_m" . $j . "_s3"}[1]) {

                                                                                        ${"group". $i ."_m" . $j . "_p1_total"} = 2;
                                                                                        ${"group". $i ."_m" . $j . "_p2_total"} = 1;

                                                                                    } elseif (${"group". $i ."_m" . $j . "_s1"}[0] < ${"group". $i ."_m" . $j . "_s1"}[1] && ${"group". $i ."_m" . $j . "_s2"}[0] > ${"group". $i ."_m" . $j . "_s2"}[1] && ${"group". $i ."_m" . $j . "_s3"}[0] < ${"group". $i ."_m" . $j . "_s3"}[1]) {

                                                                                        ${"group". $i ."_m" . $j . "_p1_total"} = 1;
                                                                                        ${"group". $i ."_m" . $j . "_p2_total"} = 2;

                                                                                    } elseif (${"group". $i ."_m" . $j . "_s1"}[0] < ${"group". $i ."_m" . $j . "_s1"}[1] && ${"group". $i ."_m" . $j . "_s2"}[0] > ${"group". $i ."_m" . $j . "_s2"}[1] && ${"group". $i ."_m" . $j . "_s3"}[0] > ${"group". $i ."_m" . $j . "_s3"}[1]) {

                                                                                        ${"group". $i ."_m" . $j . "_p1_total"} = 2;
                                                                                        ${"group". $i ."_m" . $j . "_p2_total"} = 1;

                                                                                    } elseif (${"group". $i ."_m" . $j . "_s1"}[0] > ${"group". $i ."_m" . $j . "_s1"}[1] && ${"group". $i ."_m" . $j . "_s2"}[0] < ${"group". $i ."_m" . $j . "_s2"}[1] && ${"group". $i ."_m" . $j . "_s3"}[0] < ${"group". $i ."_m" . $j . "_s3"}[1]) {

                                                                                        ${"group". $i ."_m" . $j . "_p1_total"} = 1;
                                                                                        ${"group". $i ."_m" . $j . "_p2_total"} = 2;

                                                                                    }
                                                                                ?>                                                                             

                                                                                <ul class="matchup">

                                                                                    <span class="custooltip">
                                                                                        <li
                                                                                            class="team team-top @if (${"group". $i ."_m" . $j . "_p1_total"} > ${"group". $i ."_m" . $j . "_p2_total"}) winnerclractive @endif">
                                                                                            {{ \Illuminate\Support\Str::limit(${"group_". $i ."_mat_" . $j}[0], 100) }}

                                                                                            <span class="score @if (${"group" .$i ."_m". $j . "_p1_total"} > ${"group" .$i ."_m". $j . "_p2_total"}) winnerclractive @endif">{{ ${"group" .$i ."_m". $j . "_s1"}[0] }} {{ ${"group" .$i ."_m". $j . "_s2"}[0] }} {{ ${"group" .$i ."_m". $j . "_s3"}[0] }}
                                                                                            </span>

                                                                                            {{-- @if (${"group". $i ."_m". $j ."_p1_total"} < ${"group". $i ."_m". $j ."_p2_total"})
                                                                                                @if($group_status['match_'.$j] == 'Retired')
                                                                                                    @if($league->{"group_". $grp_word ."_retires"})
                                                                                                    <?php 
                                                                                                        $group_retires = json_decode($league->{"group_" . $grp_word . "_retires"}, true);
                                                                                                    ?>
                                                                                                        @if (array_key_exists('match_'.$j, $group_retires))
                                                                                                            @if($group_retires['match_'.$j] == $vs_match[0])
                                                                                                                
                                                                                                                <span class="text-danger text-center" style="font-size: 8px;font-weight: 500;">(R)</span>

                                                                                                            @endif
                                                                                                        @endif
                                                                                                    @endif
                                                                                                    <span
                                                                                                        class="score">{{ ${"group". $i ."_m". $j ."_s1"}[0] }}
                                                                                                        {{ ${"group". $i ."_m". $j ."_s2"}[0] }} {{ ${"group". $i ."_m". $j ."_s3"}[0] }}</span>
                                                                                                @elseif($group_status['match_'.$j] == 'Withdraw')
                                                                                                    <span class="text-danger text-center" style="font-size: 8px;font-weight: 500;">(W)</span>
                                                                                                    <span
                                                                                                        class="score">&#8212;</span>
                                                                                                @elseif($group_status['match_'.$j] == 'Decided by Organisers')
                                                                                                    <span class="text-danger text-center" style="font-size: 8px;font-weight: 500;">(D)</span>
                                                                                                    <span
                                                                                                        class="score">&#8212;</span>
                                                                                                @endif

                                                                                            @else
                                                                                                @if($group_status['match_'.$j] == 'Retired')
                                                                                                    @if($league->{"group_". $grp_word ."_retires"})
                                                                                                    <?php 
                                                                                                        $group_retires = json_decode($league->{"group_" . $grp_word . "_retires"}, true);
                                                                                                    ?>
                                                                                                        @if (array_key_exists('match_'.$j, $group_retires))
                                                                                                            @if($group_retires['match_'.$j] == $vs_match[0])
                                                                                                                
                                                                                                                <span class="text-danger text-center" style="font-size: 8px;font-weight: 500;">(R)</span>

                                                                                                            @endif
                                                                                                        @endif
                                                                                                    @endif
                                                                                                    <span
                                                                                                    class="score winnerclractive">{{ ${"group". $i ."_m". $j ."_s1"}[0] }}
                                                                                                    {{ ${"group". $i ."_m". $j ."_s2"}[0] }} {{ ${"group". $i ."_m". $j ."_s3"}[0] }}</span>
                                                                                                @elseif($group_status['match_'.$j] == 'Withdraw')
                                                                                                    <span
                                                                                                    class="score winnerclractive">&#8212;</span>
                                                                                                @elseif($group_status['match_'.$j] == 'Decided by Organisers')
                                                                                                    <span
                                                                                                    class="score winnerclractive">&#8212;</span>
                                                                                                @endif
                                                                                                    
                                                                                            @endif --}}

                                                                                        </li>
                                                                                        <span class="custooltiptext">{{ ${"group_". $i ."_mat_" . $j}[0] }}

                                                                                            {{-- @if($group_status)
                                                                                                @if($group_status['match_'.$j] == 'Retired')
                                                                                                    @if($league->{"group_". $grp_word ."_retires"})
                                                                                                    <?php 
                                                                                                        $group_retires = json_decode($league->{"group_" . $grp_word . "_retires"}, true);
                                                                                                    ?>
                                                                                                        @if (array_key_exists('match_'.$j, $group_retires))
                                                                                                            @if($group_retires['match_'.$j] == $vs_match[0])
                                                                                                                
                                                                                                                ({{ $group_status['match_'.$j] }})

                                                                                                            @endif
                                                                                                        @endif
                                                                                                    @endif
                                                                                                @else
                                                                                                    @if (${"group". $i ."_m". $j ."_p1_total"} < ${"group". $i ."_m". $j ."_p2_total"}) 
                                                                                                        ({{ $group_status['match_'.$j] }}) 
                                                                                                    @endif
                                                                                                @endif
                                                                                            @endif --}}

                                                                                        </span>

                                                                                    </span>

                                                                                    <span class="custooltip">
                                                                                        
                                                                                        <li
                                                                                            class="team team-top @if (${"group". $i ."_m" . $j . "_p1_total"} < ${"group". $i ."_m" . $j . "_p2_total"}) winnerclractive @endif">
                                                                                            {{ \Illuminate\Support\Str::limit(${"group_". $i ."_mat_" . $j}[1], 100) }}

                                                                                            <span class="score @if (${"group". $i ."_m". $j . "_p2_total"} > ${"group". $i ."_m". $j . "_p1_total"}) winnerclractive @endif">{{ ${"group". $i ."_m". $j . "_s1"}[1] }} {{ ${"group". $i ."_m". $j . "_s2"}[1] }} {{ ${"group". $i ."_m". $j . "_s3"}[1] }}
                                                                                            </span>

                                                                                            {{-- @if (${"group". $i ."_m". $j ."_p1_total"} > ${"group". $i ."_m". $j ."_p2_total"})
                                                                                                @if($group_status['match_'.$j] == 'Retired')
                                                                                                    @if($league->{"group_". $grp_word ."_retires"})
                                                                                                    <?php 
                                                                                                        $group_retires = json_decode($league->{"group_" . $grp_word . "_retires"}, true);
                                                                                                    ?>
                                                                                                        @if (array_key_exists('match_'.$j, $group_retires))
                                                                                                            @if($group_retires['match_'.$j] == $vs_match[1])
                                                                                                                
                                                                                                                <span class="text-danger text-center" style="font-size: 8px;font-weight: 500;">(R)</span>

                                                                                                            @endif
                                                                                                        @endif
                                                                                                    @endif
                                                                                                    <span
                                                                                                        class="score">{{ ${"group". $i ."_m". $j ."_s1"}[1] }}
                                                                                                        {{ ${"group". $i ."_m". $j ."_s2"}[1] }} {{ ${"group". $i ."_m". $j ."_s3"}[1] }}</span>
                                                                                                @elseif($group_status['match_'.$j] == 'Withdraw')
                                                                                                    <span class="text-danger text-center" style="font-size: 8px;font-weight: 500;">(W)</span>
                                                                                                    <span
                                                                                                        class="score">&#8212;</span>
                                                                                                @elseif($group_status['match_'.$j] == 'Decided by Organisers')
                                                                                                    <span class="text-danger text-center" style="font-size: 8px;font-weight: 500;">(D)</span>
                                                                                                    <span
                                                                                                        class="score">&#8212;</span>
                                                                                                @endif

                                                                                            @else
                                                                                                @if($group_status['match_'.$j] == 'Retired')
                                                                                                    @if($league->{"group_". $grp_word ."_retires"})
                                                                                                    <?php 
                                                                                                        $group_retires = json_decode($league->{"group_" . $grp_word . "_retires"}, true);
                                                                                                    ?>
                                                                                                        @if (array_key_exists('match_'.$j, $group_retires))
                                                                                                            @if($group_retires['match_'.$j] == $vs_match[1])
                                                                                                                
                                                                                                                <span class="text-danger text-center" style="font-size: 8px;font-weight: 500;">(R)</span>

                                                                                                            @endif
                                                                                                        @endif
                                                                                                    @endif
                                                                                                    <span
                                                                                                    class="score winnerclractive">{{ ${"group". $i ."_m". $j ."_s1"}[1] }}
                                                                                                    {{ ${"group". $i ."_m". $j ."_s2"}[1] }} {{ ${"group". $i ."_m". $j ."_s3"}[1] }}</span>
                                                                                                @elseif($group_status['match_'.$j] == 'Withdraw')
                                                                                                    <span
                                                                                                    class="score winnerclractive">&#8212;</span>
                                                                                                @elseif($group_status['match_'.$j] == 'Decided by Organisers')
                                                                                                    <span
                                                                                                    class="score winnerclractive">&#8212;</span>
                                                                                                @endif
                                                                                                    
                                                                                            @endif --}}

                                                                                        </li>
                                                                                        <span class="custooltiptext">{{ ${"group_". $i ."_mat_" . $j}[1] }}

                                                                                            {{-- @if($group_status)
                                                                                                @if($group_status['match_'.$j] == 'Retired')
                                                                                                    @if($league->{"group_". $grp_word ."_retires"})
                                                                                                    <?php 
                                                                                                        $group_retires = json_decode($league->{"group_" . $grp_word . "_retires"}, true);
                                                                                                    ?>
                                                                                                        @if (array_key_exists('match_'.$j, $group_retires))
                                                                                                            @if($group_retires['match_'.$j] == $vs_match[1])
                                                                                                                
                                                                                                                ({{ $group_status['match_'.$j] }})

                                                                                                            @endif
                                                                                                        @endif
                                                                                                    @endif
                                                                                                @else
                                                                                                    @if (${"group". $i ."_m". $j ."_p1_total"} > ${"group". $i ."_m". $j ."_p2_total"}) 
                                                                                                        ({{ $group_status['match_'.$j] }}) 
                                                                                                    @endif
                                                                                                @endif
                                                                                            @endif --}}

                                                                                        </span>

                                                                                    </span>

                                                                                </ul>

                                                                            @else
                                                                                <ul class="matchup">

                                                                                    <span class="custooltipleft">
                                                                                        <li class="team team-top">
                                                                                            {{ \Illuminate\Support\Str::limit(${"group_". $i ."_mat_" . $j}[0], 100) }}

                                                                                            <span class="score">N/A</span>

                                                                                        </li>
                                                                                        <span class="custooltiplefttext">{{ ${"group_". $i ."_mat_" . $j}[0] }}</span>
                                                                                    </span>

                                                                                    <span class="custooltipleft">
                                                                                        <li class="team team-bottom">
                                                                                            {{ \Illuminate\Support\Str::limit(${"group_". $i ."_mat_" . $j}[1], 100) }}

                                                                                            <span class="score">N/A</span>

                                                                                        </li>
                                                                                        <span class="custooltiplefttext">{{ ${"group_". $i ."_mat_" . $j}[1] }}</span>
                                                                                    </span>

                                                                                </ul>
                                                                            @endif
                                                                        @endif

                                                                    @else
                                                                        @if (array_key_exists('match_'.$j, $group_results))
                                                                           
                                                                            <?php
                                                                                if (${"group". $i ."_m" . $j . "_s1"}[0] > ${"group". $i ."_m" . $j . "_s1"}[1] && ${"group". $i ."_m" . $j . "_s2"}[0] > ${"group". $i ."_m" . $j . "_s2"}[1]) {

                                                                                    ${"group". $i ."_m" . $j . "_p1_total"} = 2;
                                                                                    ${"group". $i ."_m" . $j . "_p2_total"} = 0;

                                                                                } elseif (${"group". $i ."_m" . $j . "_s1"}[1] > ${"group". $i ."_m" . $j . "_s1"}[0] && ${"group". $i ."_m" . $j . "_s2"}[1] > ${"group". $i ."_m" . $j . "_s2"}[0]) {

                                                                                    ${"group". $i ."_m" . $j . "_p1_total"} = 0;
                                                                                    ${"group". $i ."_m" . $j . "_p2_total"} = 2;

                                                                                } elseif (${"group". $i ."_m" . $j . "_s1"}[0] > ${"group". $i ."_m" . $j . "_s1"}[1] && ${"group". $i ."_m" . $j . "_s2"}[0] < ${"group". $i ."_m" . $j . "_s2"}[1] && ${"group". $i ."_m" . $j . "_s3"}[0] > ${"group". $i ."_m" . $j . "_s3"}[1]) {

                                                                                    ${"group". $i ."_m" . $j . "_p1_total"} = 2;
                                                                                    ${"group". $i ."_m" . $j . "_p2_total"} = 1;

                                                                                } elseif (${"group". $i ."_m" . $j . "_s1"}[0] < ${"group". $i ."_m" . $j . "_s1"}[1] && ${"group". $i ."_m" . $j . "_s2"}[0] > ${"group". $i ."_m" . $j . "_s2"}[1] && ${"group". $i ."_m" . $j . "_s3"}[0] < ${"group". $i ."_m" . $j . "_s3"}[1]) {

                                                                                    ${"group". $i ."_m" . $j . "_p1_total"} = 1;
                                                                                    ${"group". $i ."_m" . $j . "_p2_total"} = 2;

                                                                                } elseif (${"group". $i ."_m" . $j . "_s1"}[0] < ${"group". $i ."_m" . $j . "_s1"}[1] && ${"group". $i ."_m" . $j . "_s2"}[0] > ${"group". $i ."_m" . $j . "_s2"}[1] && ${"group". $i ."_m" . $j . "_s3"}[0] > ${"group". $i ."_m" . $j . "_s3"}[1]) {

                                                                                    ${"group". $i ."_m" . $j . "_p1_total"} = 2;
                                                                                    ${"group". $i ."_m" . $j . "_p2_total"} = 1;

                                                                                } elseif (${"group". $i ."_m" . $j . "_s1"}[0] > ${"group". $i ."_m" . $j . "_s1"}[1] && ${"group". $i ."_m" . $j . "_s2"}[0] < ${"group". $i ."_m" . $j . "_s2"}[1] && ${"group". $i ."_m" . $j . "_s3"}[0] < ${"group". $i ."_m" . $j . "_s3"}[1]) {

                                                                                    ${"group". $i ."_m" . $j . "_p1_total"} = 1;
                                                                                    ${"group". $i ."_m" . $j . "_p2_total"} = 2;

                                                                                }
                                                                            ?>                                                                             

                                                                            <ul class="matchup">

                                                                                <span class="custooltip">
                                                                                    <li
                                                                                        class="team team-top @if (${"group". $i ."_m". $j . "_p1_total"} > ${"group". $i ."_m". $j . "_p2_total"}) winnerclractive @endif">
                                                                                        {{ \Illuminate\Support\Str::limit(${"group_". $i ."_mat_" . $j}[0], 100) }}

                                                                                        <span class="score @if (${"group" .$i ."_m". $j . "_p1_total"} > ${"group" .$i ."_m". $j . "_p2_total"}) winnerclractive @endif">{{ ${"group" .$i ."_m". $j . "_s1"}[0] }}
                                                                                        {{ ${"group" .$i ."_m". $j . "_s2"}[0] }} {{ ${"group" .$i ."_m". $j . "_s3"}[0] }}</span>
                                                                                        

                                                                                    </li>
                                                                                    <span class="custooltiptext">{{ ${"group_". $i ."_mat_" . $j}[0] }}
                                                                                    </span>

                                                                                </span>

                                                                                <span class="custooltip">
                                                                                    
                                                                                    <li
                                                                                        class="team team-top @if (${"group". $i ."_m". $j . "_p1_total"} < ${"group". $i ."_m". $j . "_p2_total"}) winnerclractive @endif">
                                                                                        {{ \Illuminate\Support\Str::limit(${"group_". $i ."_mat_" . $j}[1], 100) }}

                                                                                        <span class="score @if (${"group". $i ."_m". $j . "_p2_total"} > ${"group". $i ."_m". $j . "_p1_total"}) winnerclractive @endif">{{ ${"group". $i ."_m". $j . "_s1"}[1] }}
                                                                                        {{ ${"group". $i ."_m". $j . "_s2"}[1] }} {{ ${"group". $i ."_m". $j . "_s3"}[1] }}</span>

                                                                                    </li>
                                                                                    <span class="custooltiptext">{{ ${"group_". $i ."_mat_" . $j}[1] }}

                                                                                    </span>

                                                                                </span>

                                                                            </ul>

                                                                        @else
                                                                            <ul class="matchup">

                                                                                <span class="custooltipleft">
                                                                                    <li class="team team-top">
                                                                                        {{ \Illuminate\Support\Str::limit(${"group_". $i ."_mat_" . $j}[0], 100) }}

                                                                                        <span class="score">N/A</span>

                                                                                    </li>
                                                                                    <span class="custooltiplefttext">{{ ${"group_". $i ."_mat_" . $j}[0] }}</span>
                                                                                </span>

                                                                                <span class="custooltipleft">
                                                                                    <li class="team team-bottom">
                                                                                        {{ \Illuminate\Support\Str::limit(${"group_". $i ."_mat_" . $j}[1], 100) }}

                                                                                        <span class="score">N/A</span>

                                                                                    </li>
                                                                                    <span class="custooltiplefttext">{{ ${"group_". $i ."_mat_" . $j}[1] }}</span>
                                                                                </span>

                                                                            </ul>
                                                                        @endif
                                                                    @endif

                                                                @else
                                                                    @if (array_key_exists('match_'.$j, $group_results))

                                                                        <?php
                                                                            if (${"group". $i ."_m" . $j . "_s1"}[0] > ${"group". $i ."_m" . $j . "_s1"}[1] && ${"group". $i ."_m" . $j . "_s2"}[0] > ${"group". $i ."_m" . $j . "_s2"}[1]) {

                                                                                ${"group". $i ."_m" . $j . "_p1_total"} = 2;
                                                                                ${"group". $i ."_m" . $j . "_p2_total"} = 0;

                                                                            } elseif (${"group". $i ."_m" . $j . "_s1"}[1] > ${"group". $i ."_m" . $j . "_s1"}[0] && ${"group". $i ."_m" . $j . "_s2"}[1] > ${"group". $i ."_m" . $j . "_s2"}[0]) {

                                                                                ${"group". $i ."_m" . $j . "_p1_total"} = 0;
                                                                                ${"group". $i ."_m" . $j . "_p2_total"} = 2;

                                                                            } elseif (${"group". $i ."_m" . $j . "_s1"}[0] > ${"group". $i ."_m" . $j . "_s1"}[1] && ${"group". $i ."_m" . $j . "_s2"}[0] < ${"group". $i ."_m" . $j . "_s2"}[1] && ${"group". $i ."_m" . $j . "_s3"}[0] > ${"group". $i ."_m" . $j . "_s3"}[1]) {

                                                                                ${"group". $i ."_m" . $j . "_p1_total"} = 2;
                                                                                ${"group". $i ."_m" . $j . "_p2_total"} = 1;

                                                                            } elseif (${"group". $i ."_m" . $j . "_s1"}[0] < ${"group". $i ."_m" . $j . "_s1"}[1] && ${"group". $i ."_m" . $j . "_s2"}[0] > ${"group". $i ."_m" . $j . "_s2"}[1] && ${"group". $i ."_m" . $j . "_s3"}[0] < ${"group". $i ."_m" . $j . "_s3"}[1]) {

                                                                                ${"group". $i ."_m" . $j . "_p1_total"} = 1;
                                                                                ${"group". $i ."_m" . $j . "_p2_total"} = 2;

                                                                            } elseif (${"group". $i ."_m" . $j . "_s1"}[0] < ${"group". $i ."_m" . $j . "_s1"}[1] && ${"group". $i ."_m" . $j . "_s2"}[0] > ${"group". $i ."_m" . $j . "_s2"}[1] && ${"group". $i ."_m" . $j . "_s3"}[0] > ${"group". $i ."_m" . $j . "_s3"}[1]) {

                                                                                ${"group". $i ."_m" . $j . "_p1_total"} = 2;
                                                                                ${"group". $i ."_m" . $j . "_p2_total"} = 1;

                                                                            } elseif (${"group". $i ."_m" . $j . "_s1"}[0] > ${"group". $i ."_m" . $j . "_s1"}[1] && ${"group". $i ."_m" . $j . "_s2"}[0] < ${"group". $i ."_m" . $j . "_s2"}[1] && ${"group". $i ."_m" . $j . "_s3"}[0] < ${"group". $i ."_m" . $j . "_s3"}[1]) {

                                                                                ${"group". $i ."_m" . $j . "_p1_total"} = 1;
                                                                                ${"group". $i ."_m" . $j . "_p2_total"} = 2;

                                                                            }
                                                                        ?>                                                                             

                                                                        <ul class="matchup">

                                                                            <span class="custooltip">
                                                                                <li
                                                                                    class="team team-top @if (${"group". $i ."_m" . $j . "_p1_total"} > ${"group". $i ."_m" . $j . "_p2_total"}) winnerclractive @endif">
                                                                                    {{ \Illuminate\Support\Str::limit(${"group_". $i ."_mat_" . $j}[0], 100) }}

                                                                                    <span class="score @if (${"group" .$i ."_m". $j . "_p1_total"} > ${"group" .$i ."_m". $j . "_p2_total"}) winnerclractive @endif">{{ ${"group" .$i ."_m". $j . "_s1"}[0] }}
                                                                                        {{ ${"group" .$i ."_m". $j . "_s2"}[0] }} {{ ${"group" .$i ."_m". $j . "_s3"}[0] }}</span>

                                                                                </li>
                                                                                <span class="custooltiptext">{{ ${"group_". $i ."_mat_" . $j}[0] }}
                                                                                </span>

                                                                            </span>

                                                                            <span class="custooltip">
                                                                                
                                                                                <li
                                                                                    class="team team-top @if (${"group". $i ."_m" . $j . "_p1_total"} < ${"group". $i ."_m" . $j . "_p2_total"}) winnerclractive @endif">
                                                                                    {{ \Illuminate\Support\Str::limit(${"group_". $i ."_mat_" . $j}[1], 100) }}

                                                                                    <span class="score @if (${"group". $i ."_m". $j . "_p2_total"} > ${"group". $i ."_m". $j . "_p1_total"}) winnerclractive @endif">{{ ${"group". $i ."_m". $j . "_s1"}[1] }}
                                                                                        {{ ${"group". $i ."_m". $j . "_s2"}[1] }} {{ ${"group". $i ."_m". $j . "_s3"}[1] }}</span>

                                                                                </li>
                                                                                <span class="custooltiptext">{{ ${"group_". $i ."_mat_" . $j}[1] }}
                                                                                </span>

                                                                            </span>

                                                                        </ul>

                                                                    @else
                                                                        <ul class="matchup">

                                                                            <span class="custooltipleft">
                                                                                <li class="team team-top">
                                                                                    {{ \Illuminate\Support\Str::limit(${"group_". $i ."_mat_" . $j}[0], 100) }}

                                                                                    <span class="score">N/A</span>

                                                                                </li>
                                                                                <span class="custooltiplefttext">{{ ${"group_". $i ."_mat_" . $j}[0] }}</span>
                                                                            </span>

                                                                            <span class="custooltipleft">
                                                                                <li class="team team-bottom">
                                                                                    {{ \Illuminate\Support\Str::limit(${"group_". $i ."_mat_" . $j}[1], 100) }}

                                                                                    <span class="score">N/A</span>

                                                                                </li>
                                                                                <span class="custooltiplefttext">{{ ${"group_". $i ."_mat_" . $j}[1] }}</span>
                                                                            </span>

                                                                        </ul>
                                                                    @endif
                                                                @endif

                                                            @else
                                                                <ul class="matchup">

                                                                    <span class="custooltipleft">
                                                                        <li class="team team-top">
                                                                            {{ \Illuminate\Support\Str::limit(${"group_". $i ."_mat_" . $j}[0], 100) }}

                                                                            <span class="score">N/A</span>

                                                                        </li>
                                                                        <span class="custooltiplefttext">{{ ${"group_". $i ."_mat_" . $j}[0] }}</span>
                                                                    </span>

                                                                    <span class="custooltipleft">
                                                                        <li class="team team-bottom">
                                                                            {{ \Illuminate\Support\Str::limit(${"group_". $i ."_mat_" . $j}[1], 100) }}

                                                                            <span class="score">N/A</span>

                                                                        </li>
                                                                        <span class="custooltiplefttext">{{ ${"group_". $i ."_mat_" . $j}[1] }}</span>
                                                                    </span>
                                                                </ul>
                                                            @endif

                                                        @else
                                                            <ul class="matchup">

                                                                <li class="team team-top">n/a<span class="score">N/A</span></li>
                                                                <li class="team team-bottom">n/a<span class="score">N/A</span></li>
                                                            </ul>
                                                        @endif
                                                    @endfor

                                                </div>
                                            </div>
                                            
                                        @else
                                            <div class="col-6">
                                                <div class="round round-one">
                                                    <div class="round-details">Group - {{ $i }}<br/><span class="date">@if($league->{"group_" . $grp_word . "_deadline"}) {{ ${"strt_g". $i}[0] }} - {{ ${"endd_g". $i}[0] }} @else N/A @endif</span></div>

                                                    @for($j = 1; $j < $gr_matches + 1; $j++)
                                                        <ul class="matchup">
                                                            
                                                            <li class="team team-top"><span class="score"></span></li>
                                                            <li class="team team-bottom"><span class="score"></span></li>
                                                        </ul>   
                                                    @endfor

                                                </div>
                                            </div>
                                        @endif
                                    @endfor
                                
                            </div> 

                        </div>
                    </section>

                @endif
            @endif

            <br> <br>

            @if($league->group_size && $league->player_per_group)
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">

                                @if($league->tree_size)
                                    <div class="row">

                                        <div class="col-xl-12">

                                            <div class="mb-3 position-relative text-center">                                            
                                                <label for="" class="form-label text-success">The group stage is completed & a {{ $league->tree_size }} players tree is generated.</label>
                                                
                                                <div><a class="btn btn-success" @if($league->tree_size == 8) href="{{ route('draw.tournament.eight.players', $league->id) }}" @elseif($league->tree_size == 16) href="{{ route('draw.tournament.sixteen.players', $league->id) }}" @else href="{{ route('draw.tournament.thirtytwo.players', $league->id) }}" @endif style="margin-top: 6px !important; width: 50% !important">Manage Generated Tree</a></div>

                                            </div>
                                        </div>

                                    </div>
                                @else
                                    <form class="needs-validation" action="{{ route('tree.tournament', $league->id) }}" method="post" novalidate="">
                                    @csrf

                                        <div class="row">

                                            <div class="col-xl-12">
                                                <div class="mb-3 position-relative">
                                                    <label for="validationTooltip01" class="form-label">Number of Players</label>
                                                                                                
                                                    <select class="form-control select2" id="validationTooltip01" name="tree_size" required="">
                                            
                                                        <option value="">Select Number of Players</option>
                                                        
                                                        <option @if($league->tree_size == '8') selected @endif value="8">8 Players</option>
                                                        <option @if($league->tree_size == '16') selected @endif value="16">16 Players</option>

                                                    </select>

                                                    <div class="valid-tooltip">
                                                        Looks good!
                                                    </div>

                                                    <div class="invalid-tooltip">
                                                        Please select number of players.
                                                    </div>                                          

                                                </div>
                                            </div>


                                            <div class="col-xl-12">

                                                <div class="mb-3 position-relative text-center">                                            
                                                    <label for="" class="form-label text-danger">Is the group stage completed? Generate a 8 or 16 players tree with the winners of the groups.</label>
                                                    
                                                    <button class="btn btn-primary" onclick="return confirm('Are you sure that the group stage is completed?');" style="margin-top: 6px !important; width: 100% !important" type="submit">Generate Draw Tree</button>

                                                </div>
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
            @endif


        </div>
    </div>

@endsection


@section('styles')
    <style type="text/css">
        .select2-container--default .select2-results>.select2-results__options{
            max-height: 570px !important;
        }
    </style>
    
    <link href="https://fonts.googleapis.com/css?family=Fjalla+One" rel="stylesheet">
    
    <style type="text/css">    
        .ptable table {
            font-family: Fjalla One;
            background-color: #000000;
            border-radius: 5px !important;
            border-collapse: collapse;
        }

        .ptable {
            margin: 0px 0% 30px 0%;
        }

        th, td {
            padding: 10px;
        }

        th{
            background-color: black;
            color: #ffe221;
        }

        .headin {
            text-align: center;
            text-decoration: none;
            color: #ffe221;
            display: block;
        }

        .wpos {
            text-align: center;
        }

        .wpos td {
            color: #77ff21;
        }

        .pos {
            text-align: center;
        }

        .pos td {
            color: #ff7b21;
        }

        table .col {
            border-bottom: 1px solid #FFFFFF;
        }

        .wpos:hover {
            background-color: #77ff21;
        }

        .wpos:hover td {
            color: #000000;
        }

        .pos:hover {
            background-color: #ff7b21;
        }

        .pos:hover td {
            color: #000000;
        }

        .ypos {
            text-align: center;
        }
        .ypos td {
            color: #ffe221;
        }
        .ypos:hover {
            background-color: #ffe221;
        }

        .ypos:hover td {
            color: #000000;
        }
    </style>


    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">
    <link href='https://fonts.googleapis.com/css?family=Holtwood+One+SC' rel='stylesheet' type='text/css'>
    <link href='https://fonts.googleapis.com/css?family=Kaushan+Script|Herr+Von+Muellerhoff' rel='stylesheet' type='text/css'>
    <link href='https://fonts.googleapis.com/css?family=Abel' rel='stylesheet' type='text/css'>
    <link href='https://fonts.googleapis.com/css?family=Istok+Web|Roboto+Condensed:700' rel='stylesheet' type='text/css'>

    <style type="text/css">
        .winnerclractive {
            color : #D07030 !important;
        }

        .hero {
          font-family: "Istok Web", sans-serif;
          background: url("http://picjumbo.com/wp-content/uploads/HNCK2189-1300x866.jpg")
            no-repeat #000;
          background-size: cover;
          min-height: 100%;
          margin: 0;

          position: relative;
          text-align: center;
          overflow: hidden;
          color: #fcfcfc;
        }
        .hero h1 {
          font-family: "Holtwood One SC", serif;
          font-weight: normal;
          font-size: 5.4em;
          margin-top: 40px !important;
          color: #fff;
          margin: 0 0 20px;
          text-shadow: 0 0 12px rgba(0, 0, 0, 0.5);
          text-transform: uppercase;
          letter-spacing: -1px;
        }
        .hero p {
          font-family: "Abel", sans-serif;
          text-transform: uppercase;
          color: #5cca87;
          letter-spacing: 6px;
          text-shadow: 0 0 12px rgba(0, 0, 0, 0.5);
          font-size: 1.2em;
        }
        .hero-wrap {
          padding: 3.5em 10px;
        }
        .hero p.intro {
          font-family: "Holtwood One SC", serif;
          text-transform: uppercase;
          letter-spacing: 1px;
          font-size: 3em;
          margin-bottom: -40px;
        }
        .hero p.year {
          color: #fff;
          letter-spacing: 20px;
          font-size: 34px;
          margin: -25px 0 25px;
        }
        .hero p.year i {
          font-size: 14px;
          vertical-align: middle;
        }
        #bracket {
          overflow: hidden;
          background-color: #e1e1e1;
          background-color: rgba(225, 225, 225, 0.9);
          padding-top: 20px;
          font-size: 12px;
          padding: 40px 0;
        }
        .container {
          max-width: 1100px;
          margin: 0 auto;
          display: block;
          display: -webkit-box;
          display: -moz-box;
          display: -ms-flexbox;
          display: -webkit-flex;
          display: -webkit-flex;
          display: flex;
          -webkit-flex-direction: row;
          flex-direction: row;
        }
        .split {
          display: block;
          float: left;
          display: -webkit-box;
          display: -moz-box;
          display: -ms-flexbox;
          display: -webkit-flex;
          display: flex;
          width: 45%;
          -webkit-flex-direction: row;
          -moz-flex-direction: row;
          flex-direction: row;
        }
        .champion {
          float: left;
          display: block;
          width: 16%;
          -webkit-flex-direction: row;
          flex-direction: row;
          -webkit-align-self: center;
          align-self: center;
          margin-top: -15px;
          text-align: center;
          padding: 230px 0\9;
        }
        .champion i {
          color: #a0a6a8;
          font-size: 45px;
          padding: 10px 0;
        }
        .round {
          display: block;
          float: left;
          display: -webkit-box;
          display: -moz-box;
          display: -ms-flexbox;
          display: -webkit-flex;
          display: flex;
          -webkit-flex-direction: column;
          flex-direction: column;
          width: 95%;
          width: 30.8333%\9;
        }
        .split-two {
        }
        .split-one .round {
          margin: 0 2.5% 0 0;
        }
        .split-two .round {
          margin: 0 0 0 2.5%;
        }
        .matchup {
          margin: 0;
          width: 100%;
          padding: 10px 0;
          -webkit-transition: all 0.2s;
          transition: all 0.2s;
        }
        .score {
          font-size: 11px;
          text-transform: uppercase;
          float: right;
          color: #2c7399;
          font-weight: bold;
          font-family: "Roboto Condensed", sans-serif;
          position: absolute;
          right: 5px;
        }
        .team {
          padding: 0 5px;
          margin: 3px 0;
          height: 25px;
          line-height: 25px;
          white-space: nowrap;
          overflow: hidden;
          position: relative;
        }
        .round-two .matchup {
          margin: 0;
          padding: 50px 0;
        }
        .round-three .matchup {
          margin: 0;
          padding: 130px 0;
        }
        .round-details {
          font-family: "Roboto Condensed", sans-serif;
          font-size: 13px;
          color: #2c7399;
          text-transform: uppercase;
          text-align: center;
          height: 40px;
        }
        .champion li,
        .round li {
          background-color: #fff;
          box-shadow: none;
          opacity: 0.45;
        }
        .current li {
          opacity: 1;
        }
        .current li.team {
          background-color: #fff;
          box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1);
          opacity: 1;
        }
        .vote-options {
          display: block;
          height: 52px;
        }
        .share .container {
          margin: 0 auto;
          text-align: center;
        }
        .share-icon {
          font-size: 24px;
          color: #fff;
          padding: 25px;
        }
        .share-wrap {
          max-width: 1100px;
          text-align: center;
          margin: 60px auto;
        }
        .final {
          margin: 4.5em 0;
        }

        @-webkit-keyframes pulse {
          0% {
            -webkit-transform: scale(1);
            transform: scale(1);
          }

          50% {
            -webkit-transform: scale(1.3);
            transform: scale(1.3);
          }

          100% {
            -webkit-transform: scale(1);
            transform: scale(1);
          }
        }

        @keyframes pulse {
          0% {
            -webkit-transform: scale(1);
            -ms-transform: scale(1);
            transform: scale(1);
          }

          50% {
            -webkit-transform: scale(1.3);
            -ms-transform: scale(1.3);
            transform: scale(1.3);
          }

          100% {
            -webkit-transform: scale(1);
            -ms-transform: scale(1);
            transform: scale(1);
          }
        }

        .share-icon {
          color: #fff;
          opacity: 0.35;
        }
        .share-icon:hover {
          opacity: 1;
          -webkit-animation: pulse 0.5s;
          animation: pulse 0.5s;
        }
        .date {
          font-size: 10px;
          letter-spacing: 2px;
          font-family: "Istok Web", sans-serif;
          color: #3f915f;
        }

        @media screen and (min-width: 981px) and (max-width: 1099px) {
          .container {
            margin: 0 1%;
          }
          .champion {
            width: 14%;
          }
          .split {
            width: 65%;
          }
          .split-one .vote-box {
            margin-left: 138px;
          }
          .hero p.intro {
            font-size: 28px;
          }
          .hero p.year {
            margin: 5px 0 10px;
          }
        }

        @media screen and (max-width: 980px) {
          .container {
            -webkit-flex-direction: column;
            -moz-flex-direction: column;
            flex-direction: column;
          }
          .split,
          .champion {
            width: 130%;
            margin: 35px 5%;
          }
          .champion {
            -webkit-box-ordinal-group: 3;
            -moz-box-ordinal-group: 3;
            -ms-flex-order: 3;
            -webkit-order: 3;
            order: 3;
          }
          .split {
            border-bottom: 1px solid #b6b6b6;
            padding-bottom: 20px;
          }
          .hero p.intro {
            font-size: 24px;
          }
          .hero h1 {
            font-size: 3em;
            margin: 15px 0;
          }
          .hero p {
            font-size: 1em;
          }
        }

        @media screen and (max-width: 400px) {
          .split {
            width: 95%;
            margin: 25px 2.5%;
          }
          .round {
            width: 21%;
          }
          .current {
            -webkit-flex-grow: 1;
            -moz-flex-grow: 1;
            flex-grow: 1;
          }
          .hero h1 {
            font-size: 2.15em;
            letter-spacing: 0;
            margin: 0;
          }
          .hero p.intro {
            font-size: 1.15em;
            margin-bottom: -10px;
          }
          .round-details {
            font-size: 90%;
          }
          .hero-wrap {
            padding: 2.5em;
          }
          .hero p.year {
            margin: 5px 0 10px;
            font-size: 18px;
          }
        }


        /*.CellWithComment{
            position:relative;
        }

        .CellComment{
            display: none;
            position: relative; 
            z-index: 100;
            padding: .25em .4em;
            font-size: 87%;
            font-weight: 500;
            line-height: 1;
            color: #fff;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: .25rem;
            right: 40%;
            bottom: 0.55px;
        }

        .CellWithComment:hover span.CellComment {
            display:inline-block !important;
        }*/


        .TopCellWithComment{
            position:relative;
        }

        .TopCellComment{
            display: none;
            position: relative; 
            z-index: 100;
            padding: .25em 1em;
            font-size: 87%;
            font-weight: 500;
            line-height: 1;
            color: #fff;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: .25rem;
            right: 40%;
            bottom: 0.55px;
        }

        .TopCellWithComment:hover .TopCellComment {
            display:inline-block !important;
        }



        .custooltip {
            position: relative;
            display: block;
        }

        .custooltip .custooltiptext {
            visibility: hidden;
            width: 180px;
            background-color: black;
            color: #fff;
            text-align: center;
            padding: 3px 0;
            border-radius: 4px;

            /* Position the tooltip text - see examples below! */
            position: absolute;
            z-index: 1;
            top: 0;
            left: 105%;
        }

        .custooltip:hover .custooltiptext {
            visibility: visible;
        }

        .custooltip .custooltiptext::after {
          content: " ";
          position: absolute;
          top: 50%;
          right: 100%; /* To the left of the tooltip */
          margin-top: -5px;
          border-width: 5px;
          border-style: solid;
          border-color: transparent black transparent transparent;
        }


        .custooltipleft {
            position: relative;
            display: block;
        }

        .custooltipleft .custooltiplefttext {
            visibility: hidden;
            width: 180px;
            background-color: black;
            color: #fff;
            text-align: center;
            padding: 3px 0;
            border-radius: 4px;

            /* Position the tooltip text - see examples below! */
            position: absolute;
            z-index: 1;
            top: 0;
            right: 105%;
        }

        .custooltipleft:hover .custooltiplefttext {
            visibility: visible;
        }

        .custooltipleft .custooltiplefttext::after {
          content: " ";
          position: absolute;
          top: 50%;
          left: 100%; /* To the left of the tooltip */
          margin-top: -5px;
          border-width: 5px;
          border-style: solid;
          border-color: transparent transparent transparent black;
        }
    </style>
@endsection
