@extends('layouts.master')
@section('title', 'Tournament Tree')

@section('content')

    <div class="page-content">
        <div class="container-fluid">

            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">Tournament Draw & Tree</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard </a></li>
                                <li class="breadcrumb-item active" style="color: #74788d;">View Tournament Tree</li>
                            </ol>
                        </div>

                    </div>
                </div>
            </div>
            <!-- end page title -->


            
            <div class="row mb-1 mt-1">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="mb-sm-0 font-size-14 text-center text-success">Tournament : <span style="font-weight: 510;">{{ $tournament->name }}</span> ({{ count($players) }} Participations)</h4>
                        </div>
                    </div>
                </div>
            </div>
                        

            <?php 

                for ($i = 1; $i < 5; $i++) {
                    
                    ${"rou_1_mat_" . $i} = [];

                    if($tournament->round_one_matches) {
                        $find_matches = json_decode($tournament->round_one_matches, true);

                        if(array_key_exists('match_'.$i, $find_matches)) {
                            $matches = $find_matches['match_'.$i];
                            $match = explode(" VS ", $matches);
                            array_push(${"rou_1_mat_" . $i}, \App\Models\User::findOrFail($match[0])->name);
                            array_push(${"rou_1_mat_" . $i}, \App\Models\User::findOrFail($match[1])->name);
                        }
                    }
                }


                if ($tournament->round_one_winners) {
                    for ($i = 1; $i < 3; $i++) {
                        
                        ${"sem_mat_" . $i} = [];

                        if($tournament->semi_final_matches) {
                            $find_matches = json_decode($tournament->semi_final_matches, true);

                            if(array_key_exists('match_'.$i, $find_matches)) {
                                $matches = $find_matches['match_'.$i];
                                $match = explode(" VS ", $matches);
                                array_push(${"sem_mat_" . $i}, \App\Models\User::findOrFail($match[0])->name);
                                array_push(${"sem_mat_" . $i}, \App\Models\User::findOrFail($match[1])->name);
                            }
                        }
                    }
                }


                if ($tournament->semi_final_winners) {
                    for ($i = 1; $i < 2; $i++) {
                       
                        ${"final_mat_" . $i} = [];

                        if($tournament->final_matches) {
                            $find_matches = json_decode($tournament->final_matches, true);

                            if(array_key_exists('match_'.$i, $find_matches)) {
                                $matches = $find_matches['match_'.$i];
                                $match = explode(" VS ", $matches);
                                array_push(${"final_mat_" . $i}, \App\Models\User::findOrFail($match[0])->name);
                                array_push(${"final_mat_" . $i}, \App\Models\User::findOrFail($match[1])->name);
                            }
                        }
                    }
                }

            ?>


            <?php 

                for ($i = 1; $i < 5; $i++) {

                    if($tournament->round_one_matches) {
                        $find_matches = json_decode($tournament->round_one_matches, true);

                        if(array_key_exists('match_'.$i, $find_matches)) {
                            $matches = $find_matches['match_'.$i];
                            $match = explode(" VS ", $matches);

                            ${"p1_m".$i} = \App\Models\User::findOrFail($match[0]);
                            ${"p2_m".$i} = \App\Models\User::findOrFail($match[1]);
                        }

                    }

                    if($tournament->round_one_results) {
                        ${"rou1_m".$i."_s1"} = [];
                        ${"rou1_m".$i."_s2"} = [];
                        ${"rou1_m".$i."_s3"} = [];

                        $find_results = json_decode($tournament->round_one_results, true);

                        if(array_key_exists('match_'.$i, $find_results)) {
                            $results = $find_results['match_'.$i];
                            foreach($results as $result_array) {
                                if(array_key_exists('set_1', $result_array)) {
                                    $rs = $result_array['set_1'];
                                    array_push(${"rou1_m".$i."_s1"}, $rs);
                                }

                                if(array_key_exists('set_2', $result_array)) {
                                    $rs2 = $result_array['set_2'];
                                    array_push(${"rou1_m".$i."_s2"}, $rs2);
                                }

                                if(array_key_exists('set_3', $result_array)) {
                                    $rs3 = $result_array['set_3'];
                                    array_push(${"rou1_m".$i."_s3"}, $rs3);
                                }
                            }
                        }
                    }

                }


                for ($i = 1; $i < 3; $i++) {

                    if($tournament->semi_final_matches) {
                        $find_matches = json_decode($tournament->semi_final_matches, true);

                        if(array_key_exists('match_'.$i, $find_matches)) {
                            $matches = $find_matches['match_'.$i];
                            $match = explode(" VS ", $matches);

                            ${"p1_m".$i} = \App\Models\User::findOrFail($match[0]);
                            ${"p2_m".$i} = \App\Models\User::findOrFail($match[1]);
                        }

                    }

                    if($tournament->semi_final_results) {
                        ${"sem_m".$i."_s1"} = [];
                        ${"sem_m".$i."_s2"} = [];
                        ${"sem_m".$i."_s3"} = [];

                        $find_results = json_decode($tournament->semi_final_results, true);

                        if(array_key_exists('match_'.$i, $find_results)) {
                            $results = $find_results['match_'.$i];
                            foreach($results as $result_array) {
                                if(array_key_exists('set_1', $result_array)) {
                                    $rs = $result_array['set_1'];
                                    array_push(${"sem_m".$i."_s1"}, $rs);
                                }

                                if(array_key_exists('set_2', $result_array)) {
                                    $rs2 = $result_array['set_2'];
                                    array_push(${"sem_m".$i."_s2"}, $rs2);
                                }

                                if(array_key_exists('set_3', $result_array)) {
                                    $rs3 = $result_array['set_3'];
                                    array_push(${"sem_m".$i."_s3"}, $rs3);
                                }
                            }
                        }
                    }

                }


                for ($i = 1; $i < 2; $i++) {

                    if($tournament->final_matches) {
                        $find_matches = json_decode($tournament->final_matches, true);

                        if(array_key_exists('match_'.$i, $find_matches)) {
                            $matches = $find_matches['match_'.$i];
                            $match = explode(" VS ", $matches);

                            ${"p1_m".$i} = \App\Models\User::findOrFail($match[0]);
                            ${"p2_m".$i} = \App\Models\User::findOrFail($match[1]);
                        }

                    }

                    if($tournament->final_results) {
                        ${"final_m".$i."_s1"} = [];
                        ${"final_m".$i."_s2"} = [];
                        ${"final_m".$i."_s3"} = [];

                        $find_results = json_decode($tournament->final_results, true);

                        if(array_key_exists('match_'.$i, $find_results)) {
                            $results = $find_results['match_'.$i];
                            foreach($results as $result_array) {
                                if(array_key_exists('set_1', $result_array)) {
                                    $rs = $result_array['set_1'];
                                    array_push(${"final_m".$i."_s1"}, $rs);
                                }

                                if(array_key_exists('set_2', $result_array)) {
                                    $rs2 = $result_array['set_2'];
                                    array_push(${"final_m".$i."_s2"}, $rs2);
                                }

                                if(array_key_exists('set_3', $result_array)) {
                                    $rs3 = $result_array['set_3'];
                                    array_push(${"final_m".$i."_s3"}, $rs3);
                                }
                            }
                        }
                    }

                }

            ?>


            <?php 
                $rou_1_mat_auto = []; 

                for($i = 1; $i < 5; $i++) {
                    if($round_one_auto_selection) {               
                        if(array_key_exists('match_'.$i, $round_one_auto_selection)) {
                            $auto_player = $round_one_auto_selection['match_'.$i];
                            array_push($rou_1_mat_auto, \App\Models\User::findOrFail($auto_player)->name);
                        }
                    }
                }


                $sem_mat_auto = []; 

                for($i = 1; $i < 3; $i++) {
                    if($semi_final_auto_selection) {               
                        if(array_key_exists('match_'.$i, $semi_final_auto_selection)) {
                            $auto_player = $semi_final_auto_selection['match_'.$i];
                            array_push($sem_mat_auto, \App\Models\User::findOrFail($auto_player)->name);
                        }
                    }
                }
            ?>


            <?php 
                $rou_1_mat_retires = []; 

                for($i = 1; $i < 5; $i++) {
                    if($round_one_retires) {               
                        if(array_key_exists('match_'.$i, $round_one_retires)) {
                            $retire_player = $round_one_retires['match_'.$i];
                            $rou_1_mat_retires['match_'.$i] = \App\Models\User::findOrFail($retire_player)->name;
                        }
                    }
                }


                $sem_mat_retires = []; 

                for($i = 1; $i < 3; $i++) {
                    if($semi_final_retires) {               
                        if(array_key_exists('match_'.$i, $semi_final_retires)) {
                            $retire_player = $semi_final_retires['match_'.$i];
                            $sem_mat_retires['match_'.$i] = \App\Models\User::findOrFail($retire_player)->name;
                        }
                    }
                }


                $final_mat_retires = []; 

                for($i = 1; $i < 2; $i++) {
                    if($final_retires) {               
                        if(array_key_exists('match_'.$i, $final_retires)) {
                            $retire_player = $final_retires['match_'.$i];
                            $final_mat_retires['match_'.$i] = \App\Models\User::findOrFail($retire_player)->name;
                        }
                    }
                }
            ?>

            
            <?php 
                if($t_d_semf) {
                    $strt_sem = explode(", ", $t_d_semf->start);
                    $endd_sem = explode(", ", $t_d_semf->end);
                }
            ?>

            <?php 
                if($t_d_rou1) {
                    $strt_r1 = explode(", ", $t_d_rou1->start);
                    $endd_r1 = explode(", ", $t_d_rou1->end);
                }
            ?>

            <?php 
                if($t_d_quar) {
                    $strt_quar = explode(", ", $t_d_quar->start);
                    $endd_quar = explode(", ", $t_d_quar->end);
                }
            ?>

            <?php 
                if($t_d_final) {
                    $strt_f = explode(", ", $t_d_final->start);
                    $endd_f = explode(", ", $t_d_final->end);
                }
            ?>

            <?php
                if($t_d_rou2) {
                    $strt_r2 = explode(', ', $t_d_rou2->start);
                    $endd_r2 = explode(', ', $t_d_rou2->end);
                }
            ?>

            <?php
                if($t_d_rou3) {
                    $strt_r3 = explode(', ', $t_d_rou3->start);
                    $endd_r3 = explode(', ', $t_d_rou3->end);
                }
            ?>

            
            {{-- Group Start --}}
            
            <?php 

                for ($i = 1; $i < $tournament->group_size + 1; $i++) {

                    $grp_word = strtolower(ucwords((new NumberFormatter('en_IN', NumberFormatter::SPELLOUT))->format($i)));

                    if($tournament->{"group_" . $grp_word . "_players"}) {
                        $group_players = json_decode($tournament->{"group_" . $grp_word . "_players"}, true);
                        $count_players = count($group_players);
                        $gr_matches = ($count_players * ($count_players - 1)) / 2;
                    } else {
                        $gr_matches = ($tournament->player_per_group * ($tournament->player_per_group - 1)) / 2;
                    }
                    
                    for($j = 1; $j < $gr_matches + 1; $j++) {
                        ${"group_" . $i . "_mat_" . $j} = [];

                        if($tournament->{"group_" . $grp_word . "_matches"}) {
                            $find_matches = json_decode($tournament->{"group_" . $grp_word . "_matches"}, true);

                            if(array_key_exists('match_'.$j, $find_matches)) {
                                $matches = $find_matches['match_'.$j];
                                $match = explode(" VS ", $matches);
                                array_push(${"group_". $i ."_mat_" . $j}, \App\Models\User::findOrFail($match[0])->name);
                                array_push(${"group_". $i ."_mat_" . $j}, \App\Models\User::findOrFail($match[1])->name);
                            }
                        }

                    }
                }

            ?>


            <?php 

                for ($i = 1; $i < $tournament->group_size + 1; $i++) {

                    $grp_word = strtolower(ucwords((new NumberFormatter('en_IN', NumberFormatter::SPELLOUT))->format($i)));

                    if($tournament->{"group_" . $grp_word . "_players"}) {
                        $group_players = json_decode($tournament->{"group_" . $grp_word . "_players"}, true);
                        $count_players = count($group_players);
                        $gr_matches = ($count_players * ($count_players - 1)) / 2;
                    } else {
                        $gr_matches = ($tournament->player_per_group * ($tournament->player_per_group - 1)) / 2;
                    }

                    for($j = 1; $j < $gr_matches + 1; $j++) {

                        if($tournament->{"group_" . $grp_word . "_matches"}) {
                            $find_matches = json_decode($tournament->{"group_" . $grp_word . "_matches"}, true);

                            if(array_key_exists('match_'.$j, $find_matches)) {
                                $matches = $find_matches['match_'.$j];
                                $match = explode(" VS ", $matches);

                                ${"p1_m".$i} = \App\Models\User::findOrFail($match[0]);
                                ${"p2_m".$i} = \App\Models\User::findOrFail($match[1]);
                            }

                        }

                        if($tournament->{"group_" . $grp_word . "_results"}) {
                            ${"group".$i."_m".$j."_s1"} = [];
                            ${"group".$i."_m".$j."_s2"} = [];
                            ${"group".$i."_m".$j."_s3"} = [];

                            $find_results = json_decode($tournament->{"group_" . $grp_word . "_results"}, true);

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

                    }

                }

            ?>


            <?php 

                for($i = 1; $i < $tournament->group_size + 1; $i++) {

                    ${"group_".$i."_mat_retires"} = [];
                    $grp_word = strtolower(ucwords((new NumberFormatter('en_IN', NumberFormatter::SPELLOUT))->format($i)));

                    if($tournament->{"group_" . $grp_word . "_players"}) {
                        $group_players = json_decode($tournament->{"group_" . $grp_word . "_players"}, true);
                        $count_players = count($group_players);
                        $gr_matches = ($count_players * ($count_players - 1)) / 2;
                    } else {
                        $gr_matches = ($tournament->player_per_group * ($tournament->player_per_group - 1)) / 2;
                    }


                    for($j = 1; $j < $gr_matches + 1; $j++) {
                        if($tournament->{"group_".$grp_word."_retires"}) {
                            $find_retires = json_decode($tournament->{"group_".$grp_word."_retires"}, true);          
                            if(array_key_exists('match_'.$j, $find_retires)) {
                                $retire_player = $find_retires['match_'.$j];
                                ${"group_".$i."_mat_retires"}['match_'.$j] = \App\Models\User::findOrFail($retire_player)->name;
                            }
                        }
                    }

                }

            ?>


            @if($tournament->group_size && $tournament->player_per_group)
                @if($tournament->group_one_players)

                    <header class="hero">
                        <div class="hero-wrap">
                        <p class="intro" id="intro">Tennis4All</p>
                            <h1 id="headline">Tournament</h1>
                            <p class="year"><i class="fa fa-star"></i> {{ $tournament->name }} <i class="fa fa-star"></i></p>
                        <p>Group Fixtures ({{ count($players) }} Players)</p>
                    </div>
                    </header>

                    <section id="bracket">
                        <div class="container" style="overflow: scroll;">
                            <div class="splitgrp split-one">

                                    @for($i = 1; $i < $tournament->group_size + 1; $i++)
                                        <?php 
                                            $grp_word = strtolower(ucwords((new NumberFormatter('en_IN', NumberFormatter::SPELLOUT))->format($i)));
                                        ?>

                                        <?php
                                            if($tournament->{"group_" . $grp_word . "_deadline"}) {
                                                ${"t_d_group" . $i} = json_decode($tournament->{"group_" . $grp_word . "_deadline"});
                                                ${"strt_g". $i} = explode(', ', ${"t_d_group" . $i}->start);
                                                ${"endd_g". $i} = explode(', ', ${"t_d_group" . $i}->end);
                                            }
                                        ?>

                                        <?php
                                            if($tournament->{"group_" . $grp_word . "_players"}) {
                                                $group_players = json_decode($tournament->{"group_" . $grp_word . "_players"}, true);
                                                $count_players = count($group_players);
                                                $gr_matches = ($count_players * ($count_players - 1)) / 2;
                                            } else {
                                                $gr_matches = ($tournament->player_per_group * ($tournament->player_per_group - 1)) / 2;
                                            }
                                        ?>

                                        @if($tournament->{"group_" . $grp_word . "_matches"})

                                            <?php 
                                                $group_matches = json_decode($tournament->{"group_" . $grp_word . "_matches"}, true);
                                            ?>
                                            
                                            
                                            <div class="col-6">
                                                <div class="round round-one current">
                                                    <div class="round-details">Group - {{ $i }}<br/><span class="date">@if($tournament->{"group_" . $grp_word . "_deadline"}) {{ ${"strt_g". $i}[0] }} - {{ ${"endd_g". $i}[0] }} @else N/A @endif</span>
                                                    </div>
                                                    
                                                    @for($j = 1; $j < $gr_matches + 1; $j++)
                                                        @if (array_key_exists('match_'.$j, $group_matches))

                                                            <?php 
                                                                $get_matches = $group_matches['match_'.$j];
                                                                $vs_match = explode(" VS ", $get_matches);
                                                            ?>

                                                            @if ($tournament->{"group_" . $grp_word . "_results"})

                                                                <?php 
                                                                    $group_results = json_decode($tournament->{"group_" . $grp_word . "_results"}, true);
                                                                ?>
                                                                
                                                                @if($tournament->{"group_" . $grp_word . "_status"})

                                                                    <?php 
                                                                        $group_status = json_decode($tournament->{"group_" . $grp_word . "_status"}, true);
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
                                                                                                @if($tournament->{"group_". $grp_word ."_retires"})
                                                                                                <?php 
                                                                                                    $group_retires = json_decode($tournament->{"group_" . $grp_word . "_retires"}, true);
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
                                                                                                @if($tournament->{"group_". $grp_word ."_retires"})
                                                                                                <?php 
                                                                                                    $group_retires = json_decode($tournament->{"group_" . $grp_word . "_retires"}, true);
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
                                                                                                @if($tournament->{"group_". $grp_word ."_retires"})
                                                                                                <?php 
                                                                                                    $group_retires = json_decode($tournament->{"group_" . $grp_word . "_retires"}, true);
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
                                                                                                @if($tournament->{"group_". $grp_word ."_retires"})
                                                                                                <?php 
                                                                                                    $group_retires = json_decode($tournament->{"group_" . $grp_word . "_retires"}, true);
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
                                                                                                @if($tournament->{"group_". $grp_word ."_retires"})
                                                                                                <?php 
                                                                                                    $group_retires = json_decode($tournament->{"group_" . $grp_word . "_retires"}, true);
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
                                                                                                @if($tournament->{"group_". $grp_word ."_retires"})
                                                                                                <?php 
                                                                                                    $group_retires = json_decode($tournament->{"group_" . $grp_word . "_retires"}, true);
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
                                                                                                    @if($tournament->{"group_". $grp_word ."_retires"})
                                                                                                    <?php 
                                                                                                        $group_retires = json_decode($tournament->{"group_" . $grp_word . "_retires"}, true);
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
                                                                                                    @if($tournament->{"group_". $grp_word ."_retires"})
                                                                                                    <?php 
                                                                                                        $group_retires = json_decode($tournament->{"group_" . $grp_word . "_retires"}, true);
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
                                                                                                    @if($tournament->{"group_". $grp_word ."_retires"})
                                                                                                    <?php 
                                                                                                        $group_retires = json_decode($tournament->{"group_" . $grp_word . "_retires"}, true);
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
                                                                                                    @if($tournament->{"group_". $grp_word ."_retires"})
                                                                                                    <?php 
                                                                                                        $group_retires = json_decode($tournament->{"group_" . $grp_word . "_retires"}, true);
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
                                                                                                    @if($tournament->{"group_". $grp_word ."_retires"})
                                                                                                    <?php 
                                                                                                        $group_retires = json_decode($tournament->{"group_" . $grp_word . "_retires"}, true);
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
                                                                                                    @if($tournament->{"group_". $grp_word ."_retires"})
                                                                                                    <?php 
                                                                                                        $group_retires = json_decode($tournament->{"group_" . $grp_word . "_retires"}, true);
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
                                                    <div class="round-details">Group - {{ $i }}<br/><span class="date">@if($tournament->{"group_" . $grp_word . "_deadline"}) {{ ${"strt_g". $i}[0] }} - {{ ${"endd_g". $i}[0] }} @else N/A @endif</span></div>

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

            <br>

            @if($tournament->group_size && $tournament->player_per_group)
                @if($tournament->group_one_players)

                    <!--- GROUP TABLE -->
                    <div class="row mt-4">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">

                                        @for($i = 1; $i < $tournament->group_size + 1; $i++)
                                            <?php 
                                                $grp_word = strtolower(ucwords((new NumberFormatter('en_IN', NumberFormatter::SPELLOUT))->format($i)));
                                            ?>
                                            
                                            @if($tournament->{"group_" . $grp_word . "_players"})

                                                @if($tournament->{"group_" . $grp_word . "_stats"})

                                                    <?php 
                                                        $stats_array = json_decode($tournament->{"group_" . $grp_word . "_stats"}, true);
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
                                                        $group_players = json_decode($tournament->{"group_" . $grp_word . "_players"}, true);
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

                                                                @for($inc = 1; $inc < $tournament->player_per_group + 1; $inc++)
                                                                    
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

            <br>


            @if($tournament->tree_size == 8)
                <header class="hero">
                    <div class="hero-wrap">
                     <p class="intro" id="intro">Tennis4All</p>
                         <h1 id="headline">Tournament</h1>
                         <p class="year" style="margin-top: -10px;"><i class="fa fa-star"></i> {{ $tournament->name }} <i class="fa fa-star"></i></p>
                     <p>Fixtures ({{ $tournament->tree_size }} Players)</p>
                   </div>
                </header>

                <section id="bracket">
                    <div class="container" style="overflow: scroll;">
                        <div class="split split-one">

                            @if($round_one_auto_selection)

                                <div class="round round-one @if($round_one_auto_selection) current @elseif($round_one_matches) current @endif">
                                    <div class="round-details">Round 1<br/><span class="date">@if($t_d_rou1) {{ $strt_r1[0] }} - {{ $endd_r1[0] }} @else N/A @endif</span></div>
                                    
                                    @for($i = 1; $i < 3; $i++)
                                        @if (array_key_exists('match_'.$i, $round_one_auto_selection))
                                            
                                            <ul class="matchup">

                                                <span class="custooltipleft">
                                                    <li
                                                        class="team team-top winnerclractive">
                                                        {{ \Illuminate\Support\Str::limit($rou_1_mat_auto['match_'.$i], 100) }}

                                                        <span
                                                            class="score winnerclractive">N/A</span>

                                                    </li>
                                                    <span class="custooltiplefttext">{{ $rou_1_mat_auto['match_'.$i] }}</span>
                                                </span>

                                                <span class="custooltipleft">
                                                    <li
                                                        class="team team-bottom">
                                                        N/A

                                                        <span
                                                            class="score">N/A</span>

                                                    </li>
                                                    <span class="custooltiplefttext">N/A</span>
                                                </span>

                                            </ul>
                                            
                                        @else

                                            @if($tournament->round_one_matches)
                                                                                                   
                                                @if (array_key_exists('match_'.$i, $round_one_matches))
                                                    
                                                    <?php 
                                                        $get_matches = $round_one_matches['match_'.$i];
                                                        $vs_match = explode(" VS ", $get_matches);
                                                    ?>

                                                    @if ($tournament->round_one_results)
                                                        
                                                        @if($round_one_status)
                                                            @if (array_key_exists('match_'.$i, $round_one_status))
                                                                @if($round_one_status['match_'.$i])
                                                                    <?php
                                                                        if (${"rou1_m" . $i . "_s1"}[0] > ${"rou1_m" . $i . "_s1"}[1] && ${"rou1_m" . $i . "_s2"}[0] > ${"rou1_m" . $i . "_s2"}[1]) {

                                                                            ${"rou1_m" . $i . "_p1_total"} = 2;
                                                                            ${"rou1_m" . $i . "_p2_total"} = 0;

                                                                        } elseif (${"rou1_m" . $i . "_s1"}[1] > ${"rou1_m" . $i . "_s1"}[0] && ${"rou1_m" . $i . "_s2"}[1] > ${"rou1_m" . $i . "_s2"}[0]) {

                                                                            ${"rou1_m" . $i . "_p1_total"} = 0;
                                                                            ${"rou1_m" . $i . "_p2_total"} = 2;

                                                                        } elseif (${"rou1_m" . $i . "_s1"}[0] > ${"rou1_m" . $i . "_s1"}[1] && ${"rou1_m" . $i . "_s2"}[0] < ${"rou1_m" . $i . "_s2"}[1] && ${"rou1_m" . $i . "_s3"}[0] > ${"rou1_m" . $i . "_s3"}[1]) {

                                                                            ${"rou1_m" . $i . "_p1_total"} = 2;
                                                                            ${"rou1_m" . $i . "_p2_total"} = 1;

                                                                        } elseif (${"rou1_m" . $i . "_s1"}[0] < ${"rou1_m" . $i . "_s1"}[1] && ${"rou1_m" . $i . "_s2"}[0] > ${"rou1_m" . $i . "_s2"}[1] && ${"rou1_m" . $i . "_s3"}[0] < ${"rou1_m" . $i . "_s3"}[1]) {

                                                                            ${"rou1_m" . $i . "_p1_total"} = 1;
                                                                            ${"rou1_m" . $i . "_p2_total"} = 2;

                                                                        } elseif (${"rou1_m" . $i . "_s1"}[0] < ${"rou1_m" . $i . "_s1"}[1] && ${"rou1_m" . $i . "_s2"}[0] > ${"rou1_m" . $i . "_s2"}[1] && ${"rou1_m" . $i . "_s3"}[0] > ${"rou1_m" . $i . "_s3"}[1]) {

                                                                            ${"rou1_m" . $i . "_p1_total"} = 2;
                                                                            ${"rou1_m" . $i . "_p2_total"} = 1;

                                                                        } elseif (${"rou1_m" . $i . "_s1"}[0] > ${"rou1_m" . $i . "_s1"}[1] && ${"rou1_m" . $i . "_s2"}[0] < ${"rou1_m" . $i . "_s2"}[1] && ${"rou1_m" . $i . "_s3"}[0] < ${"rou1_m" . $i . "_s3"}[1]) {

                                                                            ${"rou1_m" . $i . "_p1_total"} = 1;
                                                                            ${"rou1_m" . $i . "_p2_total"} = 2;

                                                                        }
                                                                    ?>


                                                                    <ul class="matchup">

                                                                        <span class="custooltipleft">
                                                                            <li
                                                                                class="team team-top @if (${"rou1_m" . $i . "_p1_total"} > ${"rou1_m" . $i . "_p2_total"}) winnerclractive @endif">
                                                                                {{ \Illuminate\Support\Str::limit(${"rou_1_mat_" . $i}[0], 100) }}

                                                                                @if (${"rou1_m" . $i . "_p1_total"} < ${"rou1_m" . $i . "_p2_total"})
                                                                                    @if($round_one_status['match_'.$i] == 'Retired')
                                                                                        @if($round_one_retires)
                                                                                            @if (array_key_exists('match_'.$i, $round_one_retires))
                                                                                                @if($round_one_retires['match_'.$i] == $vs_match[0])
                                                                                                    
                                                                                                    <span class="text-danger text-center" style="font-size: 8px;font-weight: 500;">(R)</span>

                                                                                                @endif
                                                                                            @endif
                                                                                        @endif
                                                                                        <span
                                                                                            class="score">{{ ${"rou1_m" . $i . "_s1"}[0] }}
                                                                                            {{ ${"rou1_m" . $i . "_s2"}[0] }} {{ ${"rou1_m" . $i . "_s3"}[0] }}</span>
                                                                                    @elseif($round_one_status['match_'.$i] == 'Withdraw')
                                                                                        <span class="text-danger text-center" style="font-size: 8px;font-weight: 500;">(W)</span>
                                                                                        <span
                                                                                            class="score">&#8212;</span>
                                                                                    @elseif($round_one_status['match_'.$i] == 'Decided by Organisers')
                                                                                        <span class="text-danger text-center" style="font-size: 8px;font-weight: 500;">(D)</span>
                                                                                        <span
                                                                                            class="score">&#8212;</span>
                                                                                    @endif

                                                                                @else
                                                                                    @if($round_one_status['match_'.$i] == 'Retired')
                                                                                        @if($round_one_retires)
                                                                                            @if (array_key_exists('match_'.$i, $round_one_retires))
                                                                                                @if($round_one_retires['match_'.$i] == $vs_match[0])
                                                                                                    
                                                                                                    <span class="text-danger text-center" style="font-size: 8px;font-weight: 500;">(R)</span>

                                                                                                @endif
                                                                                            @endif
                                                                                        @endif
                                                                                        <span
                                                                                        class="score winnerclractive">{{ ${"rou1_m" . $i . "_s1"}[0] }}
                                                                                        {{ ${"rou1_m" . $i . "_s2"}[0] }} {{ ${"rou1_m" . $i . "_s3"}[0] }}</span>
                                                                                    @elseif($round_one_status['match_'.$i] == 'Withdraw')
                                                                                        <span
                                                                                        class="score winnerclractive">&#8212;</span>
                                                                                    @elseif($round_one_status['match_'.$i] == 'Decided by Organisers')
                                                                                        <span
                                                                                        class="score winnerclractive">&#8212;</span>
                                                                                    @endif
                                                                                        
                                                                                @endif

                                                                            </li>

                                                                            <span class="custooltiplefttext">{{ ${"rou_1_mat_" . $i}[0] }} 

                                                                                @if($round_one_status)
                                                                                    @if($round_one_status['match_'.$i] == 'Retired')
                                                                                        @if($round_one_retires)
                                                                                            @if (array_key_exists('match_'.$i, $round_one_retires))
                                                                                                @if($round_one_retires['match_'.$i] == $vs_match[0])
                                                                                                    
                                                                                                    ({{ $round_one_status['match_'.$i] }})

                                                                                                @endif
                                                                                            @endif
                                                                                        @endif
                                                                                    @else
                                                                                        @if (${"rou1_m" . $i . "_p1_total"} < ${"rou1_m" . $i . "_p2_total"}) 
                                                                                            ({{ $round_one_status['match_'.$i] }}) 
                                                                                        @endif
                                                                                    @endif
                                                                                @endif

                                                                            </span>

                                                                        </span>

                                                                        <span class="custooltipleft">
                                                                            <li
                                                                                class="team team-bottom @if (${"rou1_m" . $i . "_p2_total"} > ${"rou1_m" . $i . "_p1_total"}) winnerclractive @endif">
                                                                                {{ \Illuminate\Support\Str::limit(${"rou_1_mat_" . $i}[1], 100) }}

                                                                                @if (${"rou1_m" . $i . "_p1_total"} > ${"rou1_m" . $i . "_p2_total"})
                                                                                    @if($round_one_status['match_'.$i] == 'Retired')
                                                                                        @if($round_one_retires)
                                                                                            @if (array_key_exists('match_'.$i, $round_one_retires))
                                                                                                @if($round_one_retires['match_'.$i] == $vs_match[1])
                                                                                                    
                                                                                                    <span class="text-danger text-center" style="font-size: 8px;font-weight: 500;">(R)</span>

                                                                                                @endif
                                                                                            @endif
                                                                                        @endif
                                                                                        <span
                                                                                            class="score">{{ ${"rou1_m" . $i . "_s1"}[1] }}
                                                                                            {{ ${"rou1_m" . $i . "_s2"}[1] }} {{ ${"rou1_m" . $i . "_s3"}[1] }}</span>
                                                                                    @elseif($round_one_status['match_'.$i] == 'Withdraw')
                                                                                        <span class="text-danger text-center" style="font-size: 8px;font-weight: 500;">(W)</span>
                                                                                        <span
                                                                                            class="score">&#8212;</span>
                                                                                    @elseif($round_one_status['match_'.$i] == 'Decided by Organisers')
                                                                                        <span class="text-danger text-center" style="font-size: 8px;font-weight: 500;">(D)</span>
                                                                                        <span
                                                                                            class="score">&#8212;</span>
                                                                                    @endif

                                                                                @else
                                                                                    @if($round_one_status['match_'.$i] == 'Retired')
                                                                                        @if($round_one_retires)
                                                                                            @if (array_key_exists('match_'.$i, $round_one_retires))
                                                                                                @if($round_one_retires['match_'.$i] == $vs_match[1])
                                                                                                    
                                                                                                    <span class="text-danger text-center" style="font-size: 8px;font-weight: 500;">(R)</span>

                                                                                                @endif
                                                                                            @endif
                                                                                        @endif
                                                                                        <span
                                                                                        class="score winnerclractive">{{ ${"rou1_m" . $i . "_s1"}[1] }}
                                                                                        {{ ${"rou1_m" . $i . "_s2"}[1] }} {{ ${"rou1_m" . $i . "_s3"}[1] }}</span>
                                                                                    @elseif($round_one_status['match_'.$i] == 'Withdraw')
                                                                                        <span
                                                                                        class="score winnerclractive">&#8212;</span>
                                                                                    @elseif($round_one_status['match_'.$i] == 'Decided by Organisers')
                                                                                        <span
                                                                                        class="score winnerclractive">&#8212;</span>
                                                                                    @endif
                                                                                @endif

                                                                            </li>

                                                                            <span class="custooltiplefttext">{{ ${"rou_1_mat_" . $i}[1] }} 

                                                                                @if($round_one_status)
                                                                                    @if($round_one_status['match_'.$i] == 'Retired')
                                                                                        @if($round_one_retires)
                                                                                            @if (array_key_exists('match_'.$i, $round_one_retires))
                                                                                                @if($round_one_retires['match_'.$i] == $vs_match[1])
                                                                                                    
                                                                                                    ({{ $round_one_status['match_'.$i] }})

                                                                                                @endif
                                                                                            @endif
                                                                                        @endif
                                                                                    @else
                                                                                        @if (${"rou1_m" . $i . "_p1_total"} > ${"rou1_m" . $i . "_p2_total"}) 
                                                                                            ({{ $round_one_status['match_'.$i] }}) 
                                                                                        @endif
                                                                                    @endif
                                                                                @endif

                                                                            </span>

                                                                        </span>

                                                                    </ul>

                                                                @else
                                                                    @if (array_key_exists('match_'.$i, $round_one_results))
                                                                        <?php
                                                                            if (${"rou1_m" . $i . "_s1"}[0] > ${"rou1_m" . $i . "_s1"}[1] && ${"rou1_m" . $i . "_s2"}[0] > ${"rou1_m" . $i . "_s2"}[1]) {

                                                                                ${"rou1_m" . $i . "_p1_total"} = 2;
                                                                                ${"rou1_m" . $i . "_p2_total"} = 0;

                                                                            } elseif (${"rou1_m" . $i . "_s1"}[1] > ${"rou1_m" . $i . "_s1"}[0] && ${"rou1_m" . $i . "_s2"}[1] > ${"rou1_m" . $i . "_s2"}[0]) {

                                                                                ${"rou1_m" . $i . "_p1_total"} = 0;
                                                                                ${"rou1_m" . $i . "_p2_total"} = 2;

                                                                            } elseif (${"rou1_m" . $i . "_s1"}[0] > ${"rou1_m" . $i . "_s1"}[1] && ${"rou1_m" . $i . "_s2"}[0] < ${"rou1_m" . $i . "_s2"}[1] && ${"rou1_m" . $i . "_s3"}[0] > ${"rou1_m" . $i . "_s3"}[1]) {

                                                                                ${"rou1_m" . $i . "_p1_total"} = 2;
                                                                                ${"rou1_m" . $i . "_p2_total"} = 1;

                                                                            } elseif (${"rou1_m" . $i . "_s1"}[0] < ${"rou1_m" . $i . "_s1"}[1] && ${"rou1_m" . $i . "_s2"}[0] > ${"rou1_m" . $i . "_s2"}[1] && ${"rou1_m" . $i . "_s3"}[0] < ${"rou1_m" . $i . "_s3"}[1]) {

                                                                                ${"rou1_m" . $i . "_p1_total"} = 1;
                                                                                ${"rou1_m" . $i . "_p2_total"} = 2;

                                                                            } elseif (${"rou1_m" . $i . "_s1"}[0] < ${"rou1_m" . $i . "_s1"}[1] && ${"rou1_m" . $i . "_s2"}[0] > ${"rou1_m" . $i . "_s2"}[1] && ${"rou1_m" . $i . "_s3"}[0] > ${"rou1_m" . $i . "_s3"}[1]) {

                                                                                ${"rou1_m" . $i . "_p1_total"} = 2;
                                                                                ${"rou1_m" . $i . "_p2_total"} = 1;

                                                                            } elseif (${"rou1_m" . $i . "_s1"}[0] > ${"rou1_m" . $i . "_s1"}[1] && ${"rou1_m" . $i . "_s2"}[0] < ${"rou1_m" . $i . "_s2"}[1] && ${"rou1_m" . $i . "_s3"}[0] < ${"rou1_m" . $i . "_s3"}[1]) {

                                                                                ${"rou1_m" . $i . "_p1_total"} = 1;
                                                                                ${"rou1_m" . $i . "_p2_total"} = 2;

                                                                            }
                                                                        ?>
                                                                        

                                                                        <ul class="matchup">

                                                                            <span class="custooltipleft">
                                                                                <li
                                                                                    class="team team-top @if (${"rou1_m" . $i . "_p1_total"} > ${"rou1_m" . $i . "_p2_total"}) winnerclractive @endif">
                                                                                    {{ \Illuminate\Support\Str::limit(${"rou_1_mat_" . $i}[0], 100) }}

                                                                                    <span
                                                                                        class="score @if (${"rou1_m" . $i . "_p1_total"} > ${"rou1_m" . $i . "_p2_total"}) winnerclractive @endif">{{ ${"rou1_m" . $i . "_s1"}[0] }}
                                                                                        {{ ${"rou1_m" . $i . "_s2"}[0] }} {{ ${"rou1_m" . $i . "_s3"}[0] }}</span>

                                                                                </li>
                                                                                <span class="custooltiplefttext">{{ ${"rou_1_mat_" . $i}[0] }}</span>
                                                                            </span>

                                                                            <span class="custooltipleft">
                                                                                <li
                                                                                    class="team team-bottom @if (${"rou1_m" . $i . "_p2_total"} > ${"rou1_m" . $i . "_p1_total"}) winnerclractive @endif">
                                                                                    {{ \Illuminate\Support\Str::limit(${"rou_1_mat_" . $i}[1], 100) }}

                                                                                    <span
                                                                                        class="score @if (${"rou1_m" . $i . "_p2_total"} > ${"rou1_m" . $i . "_p1_total"}) winnerclractive @endif">{{ ${"rou1_m" . $i . "_s1"}[1] }}
                                                                                        {{ ${"rou1_m" . $i . "_s2"}[1] }} {{ ${"rou1_m" . $i . "_s3"}[1] }}</span>

                                                                                </li>
                                                                                <span class="custooltiplefttext">{{ ${"rou_1_mat_" . $i}[1] }}</span>
                                                                            </span>

                                                                        </ul>
                                                                    @else
                                                                        <ul class="matchup">

                                                                            <span class="custooltipleft">
                                                                                <li class="team team-top">
                                                                                    {{ \Illuminate\Support\Str::limit(${"rou_1_mat_" . $i}[0], 100) }}

                                                                                    <span class="score">N/A</span>

                                                                                </li>
                                                                                <span class="custooltiplefttext">{{ ${"rou_1_mat_" . $i}[0] }}</span>
                                                                            </span>

                                                                            <span class="custooltipleft">
                                                                                <li class="team team-bottom">
                                                                                    {{ \Illuminate\Support\Str::limit(${"rou_1_mat_" . $i}[1], 100) }}

                                                                                    <span class="score">N/A</span>

                                                                                </li>
                                                                                <span class="custooltiplefttext">{{ ${"rou_1_mat_" . $i}[1] }}</span>
                                                                            </span>

                                                                        </ul>
                                                                    @endif
                                                                @endif


                                                            @else
                                                                @if (array_key_exists('match_'.$i, $round_one_results))
                                                                    <?php
                                                                        if (${"rou1_m" . $i . "_s1"}[0] > ${"rou1_m" . $i . "_s1"}[1] && ${"rou1_m" . $i . "_s2"}[0] > ${"rou1_m" . $i . "_s2"}[1]) {

                                                                            ${"rou1_m" . $i . "_p1_total"} = 2;
                                                                            ${"rou1_m" . $i . "_p2_total"} = 0;

                                                                        } elseif (${"rou1_m" . $i . "_s1"}[1] > ${"rou1_m" . $i . "_s1"}[0] && ${"rou1_m" . $i . "_s2"}[1] > ${"rou1_m" . $i . "_s2"}[0]) {

                                                                            ${"rou1_m" . $i . "_p1_total"} = 0;
                                                                            ${"rou1_m" . $i . "_p2_total"} = 2;

                                                                        } elseif (${"rou1_m" . $i . "_s1"}[0] > ${"rou1_m" . $i . "_s1"}[1] && ${"rou1_m" . $i . "_s2"}[0] < ${"rou1_m" . $i . "_s2"}[1] && ${"rou1_m" . $i . "_s3"}[0] > ${"rou1_m" . $i . "_s3"}[1]) {

                                                                            ${"rou1_m" . $i . "_p1_total"} = 2;
                                                                            ${"rou1_m" . $i . "_p2_total"} = 1;

                                                                        } elseif (${"rou1_m" . $i . "_s1"}[0] < ${"rou1_m" . $i . "_s1"}[1] && ${"rou1_m" . $i . "_s2"}[0] > ${"rou1_m" . $i . "_s2"}[1] && ${"rou1_m" . $i . "_s3"}[0] < ${"rou1_m" . $i . "_s3"}[1]) {

                                                                            ${"rou1_m" . $i . "_p1_total"} = 1;
                                                                            ${"rou1_m" . $i . "_p2_total"} = 2;

                                                                        } elseif (${"rou1_m" . $i . "_s1"}[0] < ${"rou1_m" . $i . "_s1"}[1] && ${"rou1_m" . $i . "_s2"}[0] > ${"rou1_m" . $i . "_s2"}[1] && ${"rou1_m" . $i . "_s3"}[0] > ${"rou1_m" . $i . "_s3"}[1]) {

                                                                            ${"rou1_m" . $i . "_p1_total"} = 2;
                                                                            ${"rou1_m" . $i . "_p2_total"} = 1;

                                                                        } elseif (${"rou1_m" . $i . "_s1"}[0] > ${"rou1_m" . $i . "_s1"}[1] && ${"rou1_m" . $i . "_s2"}[0] < ${"rou1_m" . $i . "_s2"}[1] && ${"rou1_m" . $i . "_s3"}[0] < ${"rou1_m" . $i . "_s3"}[1]) {

                                                                            ${"rou1_m" . $i . "_p1_total"} = 1;
                                                                            ${"rou1_m" . $i . "_p2_total"} = 2;

                                                                        }
                                                                    ?>
                                                                    

                                                                    <ul class="matchup">

                                                                        <span class="custooltipleft">
                                                                            <li
                                                                                class="team team-top @if (${"rou1_m" . $i . "_p1_total"} > ${"rou1_m" . $i . "_p2_total"}) winnerclractive @endif">
                                                                                {{ \Illuminate\Support\Str::limit(${"rou_1_mat_" . $i}[0], 100) }}

                                                                                <span
                                                                                    class="score @if (${"rou1_m" . $i . "_p1_total"} > ${"rou1_m" . $i . "_p2_total"}) winnerclractive @endif">{{ ${"rou1_m" . $i . "_s1"}[0] }}
                                                                                    {{ ${"rou1_m" . $i . "_s2"}[0] }} {{ ${"rou1_m" . $i . "_s3"}[0] }}</span>

                                                                            </li>
                                                                            <span class="custooltiplefttext">{{ ${"rou_1_mat_" . $i}[0] }}</span>
                                                                        </span>

                                                                        <span class="custooltipleft">
                                                                            <li
                                                                                class="team team-bottom @if (${"rou1_m" . $i . "_p2_total"} > ${"rou1_m" . $i . "_p1_total"}) winnerclractive @endif">
                                                                                {{ \Illuminate\Support\Str::limit(${"rou_1_mat_" . $i}[1], 100) }}

                                                                                <span
                                                                                    class="score @if (${"rou1_m" . $i . "_p2_total"} > ${"rou1_m" . $i . "_p1_total"}) winnerclractive @endif">{{ ${"rou1_m" . $i . "_s1"}[1] }}
                                                                                    {{ ${"rou1_m" . $i . "_s2"}[1] }} {{ ${"rou1_m" . $i . "_s3"}[1] }}</span>

                                                                            </li>
                                                                            <span class="custooltiplefttext">{{ ${"rou_1_mat_" . $i}[1] }}</span>
                                                                        </span>

                                                                    </ul>
                                                                @else
                                                                    <ul class="matchup">

                                                                        <span class="custooltipleft">
                                                                            <li class="team team-top">
                                                                                {{ \Illuminate\Support\Str::limit(${"rou_1_mat_" . $i}[0], 100) }}

                                                                                <span class="score">N/A</span>

                                                                            </li>
                                                                            <span class="custooltiplefttext">{{ ${"rou_1_mat_" . $i}[0] }}</span>
                                                                        </span>

                                                                        <span class="custooltipleft">
                                                                            <li class="team team-bottom">
                                                                                {{ \Illuminate\Support\Str::limit(${"rou_1_mat_" . $i}[1], 100) }}

                                                                                <span class="score">N/A</span>

                                                                            </li>
                                                                            <span class="custooltiplefttext">{{ ${"rou_1_mat_" . $i}[1] }}</span>
                                                                        </span>

                                                                    </ul>
                                                                @endif
                                                            @endif

                                                        @else
                                                            @if (array_key_exists('match_'.$i, $round_one_results))
                                                                <?php
                                                                    if (${"rou1_m" . $i . "_s1"}[0] > ${"rou1_m" . $i . "_s1"}[1] && ${"rou1_m" . $i . "_s2"}[0] > ${"rou1_m" . $i . "_s2"}[1]) {

                                                                        ${"rou1_m" . $i . "_p1_total"} = 2;
                                                                        ${"rou1_m" . $i . "_p2_total"} = 0;

                                                                    } elseif (${"rou1_m" . $i . "_s1"}[1] > ${"rou1_m" . $i . "_s1"}[0] && ${"rou1_m" . $i . "_s2"}[1] > ${"rou1_m" . $i . "_s2"}[0]) {

                                                                        ${"rou1_m" . $i . "_p1_total"} = 0;
                                                                        ${"rou1_m" . $i . "_p2_total"} = 2;

                                                                    } elseif (${"rou1_m" . $i . "_s1"}[0] > ${"rou1_m" . $i . "_s1"}[1] && ${"rou1_m" . $i . "_s2"}[0] < ${"rou1_m" . $i . "_s2"}[1] && ${"rou1_m" . $i . "_s3"}[0] > ${"rou1_m" . $i . "_s3"}[1]) {

                                                                        ${"rou1_m" . $i . "_p1_total"} = 2;
                                                                        ${"rou1_m" . $i . "_p2_total"} = 1;

                                                                    } elseif (${"rou1_m" . $i . "_s1"}[0] < ${"rou1_m" . $i . "_s1"}[1] && ${"rou1_m" . $i . "_s2"}[0] > ${"rou1_m" . $i . "_s2"}[1] && ${"rou1_m" . $i . "_s3"}[0] < ${"rou1_m" . $i . "_s3"}[1]) {

                                                                        ${"rou1_m" . $i . "_p1_total"} = 1;
                                                                        ${"rou1_m" . $i . "_p2_total"} = 2;

                                                                    } elseif (${"rou1_m" . $i . "_s1"}[0] < ${"rou1_m" . $i . "_s1"}[1] && ${"rou1_m" . $i . "_s2"}[0] > ${"rou1_m" . $i . "_s2"}[1] && ${"rou1_m" . $i . "_s3"}[0] > ${"rou1_m" . $i . "_s3"}[1]) {

                                                                        ${"rou1_m" . $i . "_p1_total"} = 2;
                                                                        ${"rou1_m" . $i . "_p2_total"} = 1;

                                                                    } elseif (${"rou1_m" . $i . "_s1"}[0] > ${"rou1_m" . $i . "_s1"}[1] && ${"rou1_m" . $i . "_s2"}[0] < ${"rou1_m" . $i . "_s2"}[1] && ${"rou1_m" . $i . "_s3"}[0] < ${"rou1_m" . $i . "_s3"}[1]) {

                                                                        ${"rou1_m" . $i . "_p1_total"} = 1;
                                                                        ${"rou1_m" . $i . "_p2_total"} = 2;

                                                                    }
                                                                ?>
                                                                

                                                                <ul class="matchup">

                                                                    <span class="custooltipleft">
                                                                        <li
                                                                            class="team team-top @if (${"rou1_m" . $i . "_p1_total"} > ${"rou1_m" . $i . "_p2_total"}) winnerclractive @endif">
                                                                            {{ \Illuminate\Support\Str::limit(${"rou_1_mat_" . $i}[0], 100) }}

                                                                            <span
                                                                                class="score @if (${"rou1_m" . $i . "_p1_total"} > ${"rou1_m" . $i . "_p2_total"}) winnerclractive @endif">{{ ${"rou1_m" . $i . "_s1"}[0] }}
                                                                                {{ ${"rou1_m" . $i . "_s2"}[0] }} {{ ${"rou1_m" . $i . "_s3"}[0] }}</span>

                                                                        </li>
                                                                        <span class="custooltiplefttext">{{ ${"rou_1_mat_" . $i}[0] }}</span>
                                                                    </span>

                                                                    <span class="custooltipleft">
                                                                        <li
                                                                            class="team team-bottom @if (${"rou1_m" . $i . "_p2_total"} > ${"rou1_m" . $i . "_p1_total"}) winnerclractive @endif">
                                                                            {{ \Illuminate\Support\Str::limit(${"rou_1_mat_" . $i}[1], 100) }}

                                                                            <span
                                                                                class="score @if (${"rou1_m" . $i . "_p2_total"} > ${"rou1_m" . $i . "_p1_total"}) winnerclractive @endif">{{ ${"rou1_m" . $i . "_s1"}[1] }}
                                                                                {{ ${"rou1_m" . $i . "_s2"}[1] }} {{ ${"rou1_m" . $i . "_s3"}[1] }}</span>

                                                                        </li>
                                                                        <span class="custooltiplefttext">{{ ${"rou_1_mat_" . $i}[1] }}</span>
                                                                    </span>

                                                                </ul>
                                                            @else
                                                                <ul class="matchup">

                                                                    <span class="custooltipleft">
                                                                        <li class="team team-top">
                                                                            {{ \Illuminate\Support\Str::limit(${"rou_1_mat_" . $i}[0], 100) }}

                                                                            <span class="score">N/A</span>

                                                                        </li>
                                                                        <span class="custooltiplefttext">{{ ${"rou_1_mat_" . $i}[0] }}</span>
                                                                    </span>

                                                                    <span class="custooltipleft">
                                                                        <li class="team team-bottom">
                                                                            {{ \Illuminate\Support\Str::limit(${"rou_1_mat_" . $i}[1], 100) }}

                                                                            <span class="score">N/A</span>

                                                                        </li>
                                                                        <span class="custooltiplefttext">{{ ${"rou_1_mat_" . $i}[1] }}</span>
                                                                    </span>

                                                                </ul>
                                                            @endif
                                                        @endif

                                                    @else
                                                        <ul class="matchup">

                                                            <span class="custooltipleft">
                                                                <li class="team team-top">
                                                                    {{ \Illuminate\Support\Str::limit(${"rou_1_mat_" . $i}[0], 100) }}

                                                                    <span class="score">N/A</span>

                                                                </li>
                                                                <span class="custooltiplefttext">{{ ${"rou_1_mat_" . $i}[0] }}</span>
                                                            </span>

                                                            <span class="custooltipleft">
                                                                <li class="team team-bottom">
                                                                    {{ \Illuminate\Support\Str::limit(${"rou_1_mat_" . $i}[1], 100) }}

                                                                    <span class="score">N/A</span>

                                                                </li>
                                                                <span class="custooltiplefttext">{{ ${"rou_1_mat_" . $i}[1] }}</span>
                                                            </span>
                                                        </ul>
                                                    @endif

                                                @else
                                                    <ul class="matchup">

                                                        <li class="team team-top">n/a<span class="score">N/A</span></li>
                                                        <li class="team team-bottom">n/a<span class="score">N/A</span></li>
                                                    </ul>
                                                @endif
                                                
                                            @else
                                                
                                                <ul class="matchup">
                                                    
                                                    <li class="team team-top"><span class="score"></span></li>
                                                    <li class="team team-bottom"><span class="score"></span></li>
                                                </ul>
                                                
                                            @endif

                                        @endif

                                    @endfor

                                </div>                                
                            @else
                                @if($tournament->round_one_matches)
                                    
                                    <div class="round round-one current">
                                        <div class="round-details">Round 1<br/><span class="date">{{ $strt_r1[0] }} - {{ $endd_r1[0] }}</span>
                                        </div>
                                        
                                        @for($i = 1; $i < 3; $i++)
                                            @if (array_key_exists('match_'.$i, $round_one_matches))
                                                    
                                                <?php 
                                                    $get_matches = $round_one_matches['match_'.$i];
                                                    $vs_match = explode(" VS ", $get_matches);
                                                ?>

                                                @if ($tournament->round_one_results)
                                                    
                                                    @if($round_one_status)
                                                        @if (array_key_exists('match_'.$i, $round_one_status))
                                                            @if($round_one_status['match_'.$i])
                                                                <?php
                                                                    if (${"rou1_m" . $i . "_s1"}[0] > ${"rou1_m" . $i . "_s1"}[1] && ${"rou1_m" . $i . "_s2"}[0] > ${"rou1_m" . $i . "_s2"}[1]) {

                                                                        ${"rou1_m" . $i . "_p1_total"} = 2;
                                                                        ${"rou1_m" . $i . "_p2_total"} = 0;

                                                                    } elseif (${"rou1_m" . $i . "_s1"}[1] > ${"rou1_m" . $i . "_s1"}[0] && ${"rou1_m" . $i . "_s2"}[1] > ${"rou1_m" . $i . "_s2"}[0]) {

                                                                        ${"rou1_m" . $i . "_p1_total"} = 0;
                                                                        ${"rou1_m" . $i . "_p2_total"} = 2;

                                                                    } elseif (${"rou1_m" . $i . "_s1"}[0] > ${"rou1_m" . $i . "_s1"}[1] && ${"rou1_m" . $i . "_s2"}[0] < ${"rou1_m" . $i . "_s2"}[1] && ${"rou1_m" . $i . "_s3"}[0] > ${"rou1_m" . $i . "_s3"}[1]) {

                                                                        ${"rou1_m" . $i . "_p1_total"} = 2;
                                                                        ${"rou1_m" . $i . "_p2_total"} = 1;

                                                                    } elseif (${"rou1_m" . $i . "_s1"}[0] < ${"rou1_m" . $i . "_s1"}[1] && ${"rou1_m" . $i . "_s2"}[0] > ${"rou1_m" . $i . "_s2"}[1] && ${"rou1_m" . $i . "_s3"}[0] < ${"rou1_m" . $i . "_s3"}[1]) {

                                                                        ${"rou1_m" . $i . "_p1_total"} = 1;
                                                                        ${"rou1_m" . $i . "_p2_total"} = 2;

                                                                    } elseif (${"rou1_m" . $i . "_s1"}[0] < ${"rou1_m" . $i . "_s1"}[1] && ${"rou1_m" . $i . "_s2"}[0] > ${"rou1_m" . $i . "_s2"}[1] && ${"rou1_m" . $i . "_s3"}[0] > ${"rou1_m" . $i . "_s3"}[1]) {

                                                                        ${"rou1_m" . $i . "_p1_total"} = 2;
                                                                        ${"rou1_m" . $i . "_p2_total"} = 1;

                                                                    } elseif (${"rou1_m" . $i . "_s1"}[0] > ${"rou1_m" . $i . "_s1"}[1] && ${"rou1_m" . $i . "_s2"}[0] < ${"rou1_m" . $i . "_s2"}[1] && ${"rou1_m" . $i . "_s3"}[0] < ${"rou1_m" . $i . "_s3"}[1]) {

                                                                        ${"rou1_m" . $i . "_p1_total"} = 1;
                                                                        ${"rou1_m" . $i . "_p2_total"} = 2;

                                                                    }
                                                                ?>


                                                                <ul class="matchup">

                                                                    <span class="custooltipleft">
                                                                        <li
                                                                            class="team team-top @if (${"rou1_m" . $i . "_p1_total"} > ${"rou1_m" . $i . "_p2_total"}) winnerclractive @endif">
                                                                            {{ \Illuminate\Support\Str::limit(${"rou_1_mat_" . $i}[0], 100) }}

                                                                            @if (${"rou1_m" . $i . "_p1_total"} < ${"rou1_m" . $i . "_p2_total"})
                                                                                @if($round_one_status['match_'.$i] == 'Retired')
                                                                                    @if($round_one_retires)
                                                                                        @if (array_key_exists('match_'.$i, $round_one_retires))
                                                                                            @if($round_one_retires['match_'.$i] == $vs_match[0])
                                                                                                
                                                                                                <span class="text-danger text-center" style="font-size: 8px;font-weight: 500;">(R)</span>

                                                                                            @endif
                                                                                        @endif
                                                                                    @endif
                                                                                    <span
                                                                                        class="score">{{ ${"rou1_m" . $i . "_s1"}[0] }}
                                                                                        {{ ${"rou1_m" . $i . "_s2"}[0] }} {{ ${"rou1_m" . $i . "_s3"}[0] }}</span>
                                                                                @elseif($round_one_status['match_'.$i] == 'Withdraw')
                                                                                    <span class="text-danger text-center" style="font-size: 8px;font-weight: 500;">(W)</span>
                                                                                    <span
                                                                                        class="score">&#8212;</span>
                                                                                @elseif($round_one_status['match_'.$i] == 'Decided by Organisers')
                                                                                    <span class="text-danger text-center" style="font-size: 8px;font-weight: 500;">(D)</span>
                                                                                    <span
                                                                                        class="score">&#8212;</span>
                                                                                @endif

                                                                            @else
                                                                                @if($round_one_status['match_'.$i] == 'Retired')
                                                                                    @if($round_one_retires)
                                                                                        @if (array_key_exists('match_'.$i, $round_one_retires))
                                                                                            @if($round_one_retires['match_'.$i] == $vs_match[0])
                                                                                                
                                                                                                <span class="text-danger text-center" style="font-size: 8px;font-weight: 500;">(R)</span>

                                                                                            @endif
                                                                                        @endif
                                                                                    @endif
                                                                                    <span
                                                                                    class="score winnerclractive">{{ ${"rou1_m" . $i . "_s1"}[0] }}
                                                                                    {{ ${"rou1_m" . $i . "_s2"}[0] }} {{ ${"rou1_m" . $i . "_s3"}[0] }}</span>
                                                                                @elseif($round_one_status['match_'.$i] == 'Withdraw')
                                                                                    <span
                                                                                    class="score winnerclractive">&#8212;</span>
                                                                                @elseif($round_one_status['match_'.$i] == 'Decided by Organisers')
                                                                                    <span
                                                                                    class="score winnerclractive">&#8212;</span>
                                                                                @endif
                                                                                    
                                                                            @endif

                                                                        </li>

                                                                        <span class="custooltiplefttext">{{ ${"rou_1_mat_" . $i}[0] }} 

                                                                            @if($round_one_status)
                                                                                @if($round_one_status['match_'.$i] == 'Retired')
                                                                                    @if($round_one_retires)
                                                                                        @if (array_key_exists('match_'.$i, $round_one_retires))
                                                                                            @if($round_one_retires['match_'.$i] == $vs_match[0])
                                                                                                
                                                                                                ({{ $round_one_status['match_'.$i] }})

                                                                                            @endif
                                                                                        @endif
                                                                                    @endif
                                                                                @else
                                                                                    @if (${"rou1_m" . $i . "_p1_total"} < ${"rou1_m" . $i . "_p2_total"}) 
                                                                                        ({{ $round_one_status['match_'.$i] }}) 
                                                                                    @endif
                                                                                @endif
                                                                            @endif

                                                                        </span>

                                                                    </span>

                                                                    <span class="custooltipleft">
                                                                        <li
                                                                            class="team team-bottom @if (${"rou1_m" . $i . "_p2_total"} > ${"rou1_m" . $i . "_p1_total"}) winnerclractive @endif">
                                                                            {{ \Illuminate\Support\Str::limit(${"rou_1_mat_" . $i}[1], 100) }}

                                                                            @if (${"rou1_m" . $i . "_p1_total"} > ${"rou1_m" . $i . "_p2_total"})
                                                                                @if($round_one_status['match_'.$i] == 'Retired')
                                                                                    @if($round_one_retires)
                                                                                        @if (array_key_exists('match_'.$i, $round_one_retires))
                                                                                            @if($round_one_retires['match_'.$i] == $vs_match[1])
                                                                                                
                                                                                                <span class="text-danger text-center" style="font-size: 8px;font-weight: 500;">(R)</span>

                                                                                            @endif
                                                                                        @endif
                                                                                    @endif
                                                                                    <span
                                                                                        class="score">{{ ${"rou1_m" . $i . "_s1"}[1] }}
                                                                                        {{ ${"rou1_m" . $i . "_s2"}[1] }} {{ ${"rou1_m" . $i . "_s3"}[1] }}</span>
                                                                                @elseif($round_one_status['match_'.$i] == 'Withdraw')
                                                                                    <span class="text-danger text-center" style="font-size: 8px;font-weight: 500;">(W)</span>
                                                                                    <span
                                                                                        class="score">&#8212;</span>
                                                                                @elseif($round_one_status['match_'.$i] == 'Decided by Organisers')
                                                                                    <span class="text-danger text-center" style="font-size: 8px;font-weight: 500;">(D)</span>
                                                                                    <span
                                                                                        class="score">&#8212;</span>
                                                                                @endif

                                                                            @else
                                                                                @if($round_one_status['match_'.$i] == 'Retired')
                                                                                    @if($round_one_retires)
                                                                                        @if (array_key_exists('match_'.$i, $round_one_retires))
                                                                                            @if($round_one_retires['match_'.$i] == $vs_match[1])
                                                                                                
                                                                                                <span class="text-danger text-center" style="font-size: 8px;font-weight: 500;">(R)</span>

                                                                                            @endif
                                                                                        @endif
                                                                                    @endif
                                                                                    <span
                                                                                    class="score winnerclractive">{{ ${"rou1_m" . $i . "_s1"}[1] }}
                                                                                    {{ ${"rou1_m" . $i . "_s2"}[1] }} {{ ${"rou1_m" . $i . "_s3"}[1] }}</span>
                                                                                @elseif($round_one_status['match_'.$i] == 'Withdraw')
                                                                                    <span
                                                                                    class="score winnerclractive">&#8212;</span>
                                                                                @elseif($round_one_status['match_'.$i] == 'Decided by Organisers')
                                                                                    <span
                                                                                    class="score winnerclractive">&#8212;</span>
                                                                                @endif
                                                                            @endif

                                                                        </li>

                                                                        <span class="custooltiplefttext">{{ ${"rou_1_mat_" . $i}[1] }} 

                                                                            @if($round_one_status)
                                                                                @if($round_one_status['match_'.$i] == 'Retired')
                                                                                    @if($round_one_retires)
                                                                                        @if (array_key_exists('match_'.$i, $round_one_retires))
                                                                                            @if($round_one_retires['match_'.$i] == $vs_match[1])
                                                                                                
                                                                                                ({{ $round_one_status['match_'.$i] }})

                                                                                            @endif
                                                                                        @endif
                                                                                    @endif
                                                                                @else
                                                                                    @if (${"rou1_m" . $i . "_p1_total"} > ${"rou1_m" . $i . "_p2_total"}) 
                                                                                        ({{ $round_one_status['match_'.$i] }}) 
                                                                                    @endif
                                                                                @endif
                                                                            @endif

                                                                        </span>

                                                                    </span>

                                                                </ul>

                                                            @else
                                                                @if (array_key_exists('match_'.$i, $round_one_results))
                                                                    <?php
                                                                        if (${"rou1_m" . $i . "_s1"}[0] > ${"rou1_m" . $i . "_s1"}[1] && ${"rou1_m" . $i . "_s2"}[0] > ${"rou1_m" . $i . "_s2"}[1]) {

                                                                            ${"rou1_m" . $i . "_p1_total"} = 2;
                                                                            ${"rou1_m" . $i . "_p2_total"} = 0;

                                                                        } elseif (${"rou1_m" . $i . "_s1"}[1] > ${"rou1_m" . $i . "_s1"}[0] && ${"rou1_m" . $i . "_s2"}[1] > ${"rou1_m" . $i . "_s2"}[0]) {

                                                                            ${"rou1_m" . $i . "_p1_total"} = 0;
                                                                            ${"rou1_m" . $i . "_p2_total"} = 2;

                                                                        } elseif (${"rou1_m" . $i . "_s1"}[0] > ${"rou1_m" . $i . "_s1"}[1] && ${"rou1_m" . $i . "_s2"}[0] < ${"rou1_m" . $i . "_s2"}[1] && ${"rou1_m" . $i . "_s3"}[0] > ${"rou1_m" . $i . "_s3"}[1]) {

                                                                            ${"rou1_m" . $i . "_p1_total"} = 2;
                                                                            ${"rou1_m" . $i . "_p2_total"} = 1;

                                                                        } elseif (${"rou1_m" . $i . "_s1"}[0] < ${"rou1_m" . $i . "_s1"}[1] && ${"rou1_m" . $i . "_s2"}[0] > ${"rou1_m" . $i . "_s2"}[1] && ${"rou1_m" . $i . "_s3"}[0] < ${"rou1_m" . $i . "_s3"}[1]) {

                                                                            ${"rou1_m" . $i . "_p1_total"} = 1;
                                                                            ${"rou1_m" . $i . "_p2_total"} = 2;

                                                                        } elseif (${"rou1_m" . $i . "_s1"}[0] < ${"rou1_m" . $i . "_s1"}[1] && ${"rou1_m" . $i . "_s2"}[0] > ${"rou1_m" . $i . "_s2"}[1] && ${"rou1_m" . $i . "_s3"}[0] > ${"rou1_m" . $i . "_s3"}[1]) {

                                                                            ${"rou1_m" . $i . "_p1_total"} = 2;
                                                                            ${"rou1_m" . $i . "_p2_total"} = 1;

                                                                        } elseif (${"rou1_m" . $i . "_s1"}[0] > ${"rou1_m" . $i . "_s1"}[1] && ${"rou1_m" . $i . "_s2"}[0] < ${"rou1_m" . $i . "_s2"}[1] && ${"rou1_m" . $i . "_s3"}[0] < ${"rou1_m" . $i . "_s3"}[1]) {

                                                                            ${"rou1_m" . $i . "_p1_total"} = 1;
                                                                            ${"rou1_m" . $i . "_p2_total"} = 2;

                                                                        }
                                                                    ?>
                                                                    

                                                                    <ul class="matchup">

                                                                        <span class="custooltipleft">
                                                                            <li
                                                                                class="team team-top @if (${"rou1_m" . $i . "_p1_total"} > ${"rou1_m" . $i . "_p2_total"}) winnerclractive @endif">
                                                                                {{ \Illuminate\Support\Str::limit(${"rou_1_mat_" . $i}[0], 100) }}

                                                                                <span
                                                                                    class="score @if (${"rou1_m" . $i . "_p1_total"} > ${"rou1_m" . $i . "_p2_total"}) winnerclractive @endif">{{ ${"rou1_m" . $i . "_s1"}[0] }}
                                                                                    {{ ${"rou1_m" . $i . "_s2"}[0] }} {{ ${"rou1_m" . $i . "_s3"}[0] }}</span>

                                                                            </li>
                                                                            <span class="custooltiplefttext">{{ ${"rou_1_mat_" . $i}[0] }}</span>
                                                                        </span>

                                                                        <span class="custooltipleft">
                                                                            <li
                                                                                class="team team-bottom @if (${"rou1_m" . $i . "_p2_total"} > ${"rou1_m" . $i . "_p1_total"}) winnerclractive @endif">
                                                                                {{ \Illuminate\Support\Str::limit(${"rou_1_mat_" . $i}[1], 100) }}

                                                                                <span
                                                                                    class="score @if (${"rou1_m" . $i . "_p2_total"} > ${"rou1_m" . $i . "_p1_total"}) winnerclractive @endif">{{ ${"rou1_m" . $i . "_s1"}[1] }}
                                                                                    {{ ${"rou1_m" . $i . "_s2"}[1] }} {{ ${"rou1_m" . $i . "_s3"}[1] }}</span>

                                                                            </li>
                                                                            <span class="custooltiplefttext">{{ ${"rou_1_mat_" . $i}[1] }}</span>
                                                                        </span>

                                                                    </ul>
                                                                @else
                                                                    <ul class="matchup">

                                                                        <span class="custooltipleft">
                                                                            <li class="team team-top">
                                                                                {{ \Illuminate\Support\Str::limit(${"rou_1_mat_" . $i}[0], 100) }}

                                                                                <span class="score">N/A</span>

                                                                            </li>
                                                                            <span class="custooltiplefttext">{{ ${"rou_1_mat_" . $i}[0] }}</span>
                                                                        </span>

                                                                        <span class="custooltipleft">
                                                                            <li class="team team-bottom">
                                                                                {{ \Illuminate\Support\Str::limit(${"rou_1_mat_" . $i}[1], 100) }}

                                                                                <span class="score">N/A</span>

                                                                            </li>
                                                                            <span class="custooltiplefttext">{{ ${"rou_1_mat_" . $i}[1] }}</span>
                                                                        </span>

                                                                    </ul>
                                                                @endif
                                                            @endif


                                                        @else
                                                            @if (array_key_exists('match_'.$i, $round_one_results))
                                                                <?php
                                                                    if (${"rou1_m" . $i . "_s1"}[0] > ${"rou1_m" . $i . "_s1"}[1] && ${"rou1_m" . $i . "_s2"}[0] > ${"rou1_m" . $i . "_s2"}[1]) {

                                                                        ${"rou1_m" . $i . "_p1_total"} = 2;
                                                                        ${"rou1_m" . $i . "_p2_total"} = 0;

                                                                    } elseif (${"rou1_m" . $i . "_s1"}[1] > ${"rou1_m" . $i . "_s1"}[0] && ${"rou1_m" . $i . "_s2"}[1] > ${"rou1_m" . $i . "_s2"}[0]) {

                                                                        ${"rou1_m" . $i . "_p1_total"} = 0;
                                                                        ${"rou1_m" . $i . "_p2_total"} = 2;

                                                                    } elseif (${"rou1_m" . $i . "_s1"}[0] > ${"rou1_m" . $i . "_s1"}[1] && ${"rou1_m" . $i . "_s2"}[0] < ${"rou1_m" . $i . "_s2"}[1] && ${"rou1_m" . $i . "_s3"}[0] > ${"rou1_m" . $i . "_s3"}[1]) {

                                                                        ${"rou1_m" . $i . "_p1_total"} = 2;
                                                                        ${"rou1_m" . $i . "_p2_total"} = 1;

                                                                    } elseif (${"rou1_m" . $i . "_s1"}[0] < ${"rou1_m" . $i . "_s1"}[1] && ${"rou1_m" . $i . "_s2"}[0] > ${"rou1_m" . $i . "_s2"}[1] && ${"rou1_m" . $i . "_s3"}[0] < ${"rou1_m" . $i . "_s3"}[1]) {

                                                                        ${"rou1_m" . $i . "_p1_total"} = 1;
                                                                        ${"rou1_m" . $i . "_p2_total"} = 2;

                                                                    } elseif (${"rou1_m" . $i . "_s1"}[0] < ${"rou1_m" . $i . "_s1"}[1] && ${"rou1_m" . $i . "_s2"}[0] > ${"rou1_m" . $i . "_s2"}[1] && ${"rou1_m" . $i . "_s3"}[0] > ${"rou1_m" . $i . "_s3"}[1]) {

                                                                        ${"rou1_m" . $i . "_p1_total"} = 2;
                                                                        ${"rou1_m" . $i . "_p2_total"} = 1;

                                                                    } elseif (${"rou1_m" . $i . "_s1"}[0] > ${"rou1_m" . $i . "_s1"}[1] && ${"rou1_m" . $i . "_s2"}[0] < ${"rou1_m" . $i . "_s2"}[1] && ${"rou1_m" . $i . "_s3"}[0] < ${"rou1_m" . $i . "_s3"}[1]) {

                                                                        ${"rou1_m" . $i . "_p1_total"} = 1;
                                                                        ${"rou1_m" . $i . "_p2_total"} = 2;

                                                                    }
                                                                ?>
                                                                

                                                                <ul class="matchup">

                                                                    <span class="custooltipleft">
                                                                        <li
                                                                            class="team team-top @if (${"rou1_m" . $i . "_p1_total"} > ${"rou1_m" . $i . "_p2_total"}) winnerclractive @endif">
                                                                            {{ \Illuminate\Support\Str::limit(${"rou_1_mat_" . $i}[0], 100) }}

                                                                            <span
                                                                                class="score @if (${"rou1_m" . $i . "_p1_total"} > ${"rou1_m" . $i . "_p2_total"}) winnerclractive @endif">{{ ${"rou1_m" . $i . "_s1"}[0] }}
                                                                                {{ ${"rou1_m" . $i . "_s2"}[0] }} {{ ${"rou1_m" . $i . "_s3"}[0] }}</span>

                                                                        </li>
                                                                        <span class="custooltiplefttext">{{ ${"rou_1_mat_" . $i}[0] }}</span>
                                                                    </span>

                                                                    <span class="custooltipleft">
                                                                        <li
                                                                            class="team team-bottom @if (${"rou1_m" . $i . "_p2_total"} > ${"rou1_m" . $i . "_p1_total"}) winnerclractive @endif">
                                                                            {{ \Illuminate\Support\Str::limit(${"rou_1_mat_" . $i}[1], 100) }}

                                                                            <span
                                                                                class="score @if (${"rou1_m" . $i . "_p2_total"} > ${"rou1_m" . $i . "_p1_total"}) winnerclractive @endif">{{ ${"rou1_m" . $i . "_s1"}[1] }}
                                                                                {{ ${"rou1_m" . $i . "_s2"}[1] }} {{ ${"rou1_m" . $i . "_s3"}[1] }}</span>

                                                                        </li>
                                                                        <span class="custooltiplefttext">{{ ${"rou_1_mat_" . $i}[1] }}</span>
                                                                    </span>

                                                                </ul>
                                                            @else
                                                                <ul class="matchup">

                                                                    <span class="custooltipleft">
                                                                        <li class="team team-top">
                                                                            {{ \Illuminate\Support\Str::limit(${"rou_1_mat_" . $i}[0], 100) }}

                                                                            <span class="score">N/A</span>

                                                                        </li>
                                                                        <span class="custooltiplefttext">{{ ${"rou_1_mat_" . $i}[0] }}</span>
                                                                    </span>

                                                                    <span class="custooltipleft">
                                                                        <li class="team team-bottom">
                                                                            {{ \Illuminate\Support\Str::limit(${"rou_1_mat_" . $i}[1], 100) }}

                                                                            <span class="score">N/A</span>

                                                                        </li>
                                                                        <span class="custooltiplefttext">{{ ${"rou_1_mat_" . $i}[1] }}</span>
                                                                    </span>

                                                                </ul>
                                                            @endif
                                                        @endif

                                                    @else
                                                        @if (array_key_exists('match_'.$i, $round_one_results))
                                                            <?php
                                                                if (${"rou1_m" . $i . "_s1"}[0] > ${"rou1_m" . $i . "_s1"}[1] && ${"rou1_m" . $i . "_s2"}[0] > ${"rou1_m" . $i . "_s2"}[1]) {

                                                                    ${"rou1_m" . $i . "_p1_total"} = 2;
                                                                    ${"rou1_m" . $i . "_p2_total"} = 0;

                                                                } elseif (${"rou1_m" . $i . "_s1"}[1] > ${"rou1_m" . $i . "_s1"}[0] && ${"rou1_m" . $i . "_s2"}[1] > ${"rou1_m" . $i . "_s2"}[0]) {

                                                                    ${"rou1_m" . $i . "_p1_total"} = 0;
                                                                    ${"rou1_m" . $i . "_p2_total"} = 2;

                                                                } elseif (${"rou1_m" . $i . "_s1"}[0] > ${"rou1_m" . $i . "_s1"}[1] && ${"rou1_m" . $i . "_s2"}[0] < ${"rou1_m" . $i . "_s2"}[1] && ${"rou1_m" . $i . "_s3"}[0] > ${"rou1_m" . $i . "_s3"}[1]) {

                                                                    ${"rou1_m" . $i . "_p1_total"} = 2;
                                                                    ${"rou1_m" . $i . "_p2_total"} = 1;

                                                                } elseif (${"rou1_m" . $i . "_s1"}[0] < ${"rou1_m" . $i . "_s1"}[1] && ${"rou1_m" . $i . "_s2"}[0] > ${"rou1_m" . $i . "_s2"}[1] && ${"rou1_m" . $i . "_s3"}[0] < ${"rou1_m" . $i . "_s3"}[1]) {

                                                                    ${"rou1_m" . $i . "_p1_total"} = 1;
                                                                    ${"rou1_m" . $i . "_p2_total"} = 2;

                                                                } elseif (${"rou1_m" . $i . "_s1"}[0] < ${"rou1_m" . $i . "_s1"}[1] && ${"rou1_m" . $i . "_s2"}[0] > ${"rou1_m" . $i . "_s2"}[1] && ${"rou1_m" . $i . "_s3"}[0] > ${"rou1_m" . $i . "_s3"}[1]) {

                                                                    ${"rou1_m" . $i . "_p1_total"} = 2;
                                                                    ${"rou1_m" . $i . "_p2_total"} = 1;

                                                                } elseif (${"rou1_m" . $i . "_s1"}[0] > ${"rou1_m" . $i . "_s1"}[1] && ${"rou1_m" . $i . "_s2"}[0] < ${"rou1_m" . $i . "_s2"}[1] && ${"rou1_m" . $i . "_s3"}[0] < ${"rou1_m" . $i . "_s3"}[1]) {

                                                                    ${"rou1_m" . $i . "_p1_total"} = 1;
                                                                    ${"rou1_m" . $i . "_p2_total"} = 2;

                                                                }
                                                            ?>
                                                            

                                                            <ul class="matchup">

                                                                <span class="custooltipleft">
                                                                    <li
                                                                        class="team team-top @if (${"rou1_m" . $i . "_p1_total"} > ${"rou1_m" . $i . "_p2_total"}) winnerclractive @endif">
                                                                        {{ \Illuminate\Support\Str::limit(${"rou_1_mat_" . $i}[0], 100) }}

                                                                        <span
                                                                            class="score @if (${"rou1_m" . $i . "_p1_total"} > ${"rou1_m" . $i . "_p2_total"}) winnerclractive @endif">{{ ${"rou1_m" . $i . "_s1"}[0] }}
                                                                            {{ ${"rou1_m" . $i . "_s2"}[0] }} {{ ${"rou1_m" . $i . "_s3"}[0] }}</span>

                                                                    </li>
                                                                    <span class="custooltiplefttext">{{ ${"rou_1_mat_" . $i}[0] }}</span>
                                                                </span>

                                                                <span class="custooltipleft">
                                                                    <li
                                                                        class="team team-bottom @if (${"rou1_m" . $i . "_p2_total"} > ${"rou1_m" . $i . "_p1_total"}) winnerclractive @endif">
                                                                        {{ \Illuminate\Support\Str::limit(${"rou_1_mat_" . $i}[1], 100) }}

                                                                        <span
                                                                            class="score @if (${"rou1_m" . $i . "_p2_total"} > ${"rou1_m" . $i . "_p1_total"}) winnerclractive @endif">{{ ${"rou1_m" . $i . "_s1"}[1] }}
                                                                            {{ ${"rou1_m" . $i . "_s2"}[1] }} {{ ${"rou1_m" . $i . "_s3"}[1] }}</span>

                                                                    </li>
                                                                    <span class="custooltiplefttext">{{ ${"rou_1_mat_" . $i}[1] }}</span>
                                                                </span>

                                                            </ul>
                                                        @else
                                                            <ul class="matchup">

                                                                <span class="custooltipleft">
                                                                    <li class="team team-top">
                                                                        {{ \Illuminate\Support\Str::limit(${"rou_1_mat_" . $i}[0], 100) }}

                                                                        <span class="score">N/A</span>

                                                                    </li>
                                                                    <span class="custooltiplefttext">{{ ${"rou_1_mat_" . $i}[0] }}</span>
                                                                </span>

                                                                <span class="custooltipleft">
                                                                    <li class="team team-bottom">
                                                                        {{ \Illuminate\Support\Str::limit(${"rou_1_mat_" . $i}[1], 100) }}

                                                                        <span class="score">N/A</span>

                                                                    </li>
                                                                    <span class="custooltiplefttext">{{ ${"rou_1_mat_" . $i}[1] }}</span>
                                                                </span>

                                                            </ul>
                                                        @endif
                                                    @endif

                                                @else
                                                    <ul class="matchup">

                                                        <span class="custooltipleft">
                                                            <li class="team team-top">
                                                                {{ \Illuminate\Support\Str::limit(${"rou_1_mat_" . $i}[0], 100) }}

                                                                <span class="score">N/A</span>

                                                            </li>
                                                            <span class="custooltiplefttext">{{ ${"rou_1_mat_" . $i}[0] }}</span>
                                                        </span>

                                                        <span class="custooltipleft">
                                                            <li class="team team-bottom">
                                                                {{ \Illuminate\Support\Str::limit(${"rou_1_mat_" . $i}[1], 100) }}

                                                                <span class="score">N/A</span>

                                                            </li>
                                                            <span class="custooltiplefttext">{{ ${"rou_1_mat_" . $i}[1] }}</span>
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

                                @else
                                    <div class="round round-one">
                                        <div class="round-details">Round 1<br/><span class="date">@if($t_d_rou1) {{ $strt_r1[0] }} - {{ $endd_r1[0] }} @else N/A @endif</span></div>

                                        @for($i = 1; $i < 3; $i++)
                                            <ul class="matchup">
                                                
                                                <li class="team team-top"><span class="score"></span></li>
                                                <li class="team team-bottom"><span class="score"></span></li>
                                            </ul>
                                        @endfor

                                    </div>
                                @endif
                            @endif


                            @if($semi_final_auto_selection)

                                <div class="round round-two @if($semi_final_auto_selection) current @elseif($semi_final_matches) current @endif">
                                    <div class="round-details">semifinal<br/><span class="date">@if($t_d_semf) {{ $strt_sem[0] }} - {{ $endd_sem[0] }} @else N/A @endif</span></div>
                                    
                                    @for($i = 1; $i < 2; $i++)
                                        @if (array_key_exists('match_'.$i, $semi_final_auto_selection))
                                            
                                            <ul class="matchup">

                                                <span class="custooltip">
                                                    <li
                                                        class="team team-top winnerclractive">
                                                        {{ \Illuminate\Support\Str::limit($sem_mat_auto[0], 100) }}

                                                        <span
                                                            class="score winnerclractive">N/A</span>

                                                    </li>
                                                    <span class="custooltiptext">{{ $sem_mat_auto[0] }}</span>
                                                </span>

                                                <span class="custooltip">
                                                    <li
                                                        class="team team-bottom">
                                                        N/A

                                                        <span
                                                            class="score">N/A</span>

                                                    </li>
                                                    <span class="custooltiptext">N/A</span>
                                                </span>

                                            </ul>
                                            
                                        @else

                                            @if($tournament->semi_final_matches)
                                                                                                   
                                                @if (array_key_exists('match_'.$i, $semi_final_matches))

                                                    <?php 
                                                        $get_matches = $semi_final_matches['match_'.$i];
                                                        $vs_match = explode(" VS ", $get_matches);
                                                    ?>

                                                    @if ($tournament->semi_final_results)
                                                            
                                                        @if($semi_final_status)
                                                            @if (array_key_exists('match_'.$i, $semi_final_status))
                                                                @if($semi_final_status['match_'.$i])
                                                                    <?php
                                                                        if (${"sem_m" . $i . "_s1"}[0] > ${"sem_m" . $i . "_s1"}[1] && ${"sem_m" . $i . "_s2"}[0] > ${"sem_m" . $i . "_s2"}[1]) {

                                                                            ${"sem_m" . $i . "_p1_total"} = 2;
                                                                            ${"sem_m" . $i . "_p2_total"} = 0;

                                                                        } elseif (${"sem_m" . $i . "_s1"}[1] > ${"sem_m" . $i . "_s1"}[0] && ${"sem_m" . $i . "_s2"}[1] > ${"sem_m" . $i . "_s2"}[0]) {

                                                                            ${"sem_m" . $i . "_p1_total"} = 0;
                                                                            ${"sem_m" . $i . "_p2_total"} = 2;

                                                                        } elseif (${"sem_m" . $i . "_s1"}[0] > ${"sem_m" . $i . "_s1"}[1] && ${"sem_m" . $i . "_s2"}[0] < ${"sem_m" . $i . "_s2"}[1] && ${"sem_m" . $i . "_s3"}[0] > ${"sem_m" . $i . "_s3"}[1]) {

                                                                            ${"sem_m" . $i . "_p1_total"} = 2;
                                                                            ${"sem_m" . $i . "_p2_total"} = 1;

                                                                        } elseif (${"sem_m" . $i . "_s1"}[0] < ${"sem_m" . $i . "_s1"}[1] && ${"sem_m" . $i . "_s2"}[0] > ${"sem_m" . $i . "_s2"}[1] && ${"sem_m" . $i . "_s3"}[0] < ${"sem_m" . $i . "_s3"}[1]) {

                                                                            ${"sem_m" . $i . "_p1_total"} = 1;
                                                                            ${"sem_m" . $i . "_p2_total"} = 2;

                                                                        } elseif (${"sem_m" . $i . "_s1"}[0] < ${"sem_m" . $i . "_s1"}[1] && ${"sem_m" . $i . "_s2"}[0] > ${"sem_m" . $i . "_s2"}[1] && ${"sem_m" . $i . "_s3"}[0] > ${"sem_m" . $i . "_s3"}[1]) {

                                                                            ${"sem_m" . $i . "_p1_total"} = 2;
                                                                            ${"sem_m" . $i . "_p2_total"} = 1;

                                                                        } elseif (${"sem_m" . $i . "_s1"}[0] > ${"sem_m" . $i . "_s1"}[1] && ${"sem_m" . $i . "_s2"}[0] < ${"sem_m" . $i . "_s2"}[1] && ${"sem_m" . $i . "_s3"}[0] < ${"sem_m" . $i . "_s3"}[1]) {

                                                                            ${"sem_m" . $i . "_p1_total"} = 1;
                                                                            ${"sem_m" . $i . "_p2_total"} = 2;

                                                                        }
                                                                    ?>


                                                                    <ul class="matchup">

                                                                        <span class="custooltip">
                                                                            <li
                                                                                class="team team-top @if (${"sem_m" . $i . "_p1_total"} > ${"sem_m" . $i . "_p2_total"}) winnerclractive @endif">
                                                                                {{ \Illuminate\Support\Str::limit(${"sem_mat_" . $i}[0], 100) }}

                                                                                @if (${"sem_m" . $i . "_p1_total"} < ${"sem_m" . $i . "_p2_total"})
                                                                                    @if($semi_final_status['match_'.$i] == 'Retired')
                                                                                        @if($semi_final_retires)
                                                                                            @if (array_key_exists('match_'.$i, $semi_final_retires))
                                                                                                @if($semi_final_retires['match_'.$i] == $vs_match[0])
                                                                                                    
                                                                                                    <span class="text-danger text-center" style="font-size: 8px;font-weight: 500;">(R)</span>

                                                                                                @endif
                                                                                            @endif
                                                                                        @endif
                                                                                        <span
                                                                                            class="score">{{ ${"sem_m" . $i . "_s1"}[0] }}
                                                                                            {{ ${"sem_m" . $i . "_s2"}[0] }} {{ ${"sem_m" . $i . "_s3"}[0] }}</span>
                                                                                    @elseif($semi_final_status['match_'.$i] == 'Withdraw')
                                                                                        <span class="text-danger text-center" style="font-size: 8px;font-weight: 500;">(W)</span>
                                                                                        <span
                                                                                            class="score">&#8212;</span>
                                                                                    @elseif($semi_final_status['match_'.$i] == 'Decided by Organisers')
                                                                                        <span class="text-danger text-center" style="font-size: 8px;font-weight: 500;">(D)</span>
                                                                                        <span
                                                                                            class="score">&#8212;</span>
                                                                                    @endif

                                                                                @else
                                                                                    @if($semi_final_status['match_'.$i] == 'Retired')
                                                                                        @if($semi_final_retires)
                                                                                            @if (array_key_exists('match_'.$i, $semi_final_retires))
                                                                                                @if($semi_final_retires['match_'.$i] == $vs_match[0])
                                                                                                    
                                                                                                    <span class="text-danger text-center" style="font-size: 8px;font-weight: 500;">(R)</span>

                                                                                                @endif
                                                                                            @endif
                                                                                        @endif
                                                                                        <span
                                                                                        class="score winnerclractive">{{ ${"sem_m" . $i . "_s1"}[0] }}
                                                                                        {{ ${"sem_m" . $i . "_s2"}[0] }} {{ ${"sem_m" . $i . "_s3"}[0] }}</span>
                                                                                    @elseif($semi_final_status['match_'.$i] == 'Withdraw')
                                                                                        <span
                                                                                        class="score winnerclractive">&#8212;</span>
                                                                                    @elseif($semi_final_status['match_'.$i] == 'Decided by Organisers')
                                                                                        <span
                                                                                        class="score winnerclractive">&#8212;</span>
                                                                                    @endif
                                                                                        
                                                                                @endif

                                                                            </li>

                                                                            <span class="custooltiptext">{{ ${"sem_mat_" . $i}[0] }} 
                                                                                @if($semi_final_status)
                                                                                    @if($semi_final_status['match_'.$i] == 'Retired')
                                                                                        @if($semi_final_retires)
                                                                                            @if (array_key_exists('match_'.$i, $semi_final_retires))
                                                                                                @if($semi_final_retires['match_'.$i] == $vs_match[0])
                                                                                                    
                                                                                                    ({{ $semi_final_status['match_'.$i] }})

                                                                                                @endif
                                                                                            @endif
                                                                                        @endif
                                                                                    @else
                                                                                        @if (${"sem_m" . $i . "_p1_total"} < ${"sem_m" . $i . "_p2_total"}) 
                                                                                            ({{ $semi_final_status['match_'.$i] }}) 
                                                                                        @endif
                                                                                    @endif
                                                                                @endif
                                                                            </span>

                                                                        </span>

                                                                        <span class="custooltip">
                                                                            <li
                                                                                class="team team-bottom @if (${"sem_m" . $i . "_p2_total"} > ${"sem_m" . $i . "_p1_total"}) winnerclractive @endif">
                                                                                {{ \Illuminate\Support\Str::limit(${"sem_mat_" . $i}[1], 100) }}

                                                                                @if (${"sem_m" . $i . "_p1_total"} > ${"sem_m" . $i . "_p2_total"})
                                                                                    @if($semi_final_status['match_'.$i] == 'Retired')
                                                                                        @if($semi_final_retires)
                                                                                            @if (array_key_exists('match_'.$i, $semi_final_retires))
                                                                                                @if($semi_final_retires['match_'.$i] == $vs_match[1])
                                                                                                    
                                                                                                    <span class="text-danger text-center" style="font-size: 8px;font-weight: 500;">(R)</span>

                                                                                                @endif
                                                                                            @endif
                                                                                        @endif
                                                                                        <span
                                                                                            class="score">{{ ${"sem_m" . $i . "_s1"}[1] }}
                                                                                            {{ ${"sem_m" . $i . "_s2"}[1] }} {{ ${"sem_m" . $i . "_s3"}[1] }}</span>
                                                                                    @elseif($semi_final_status['match_'.$i] == 'Withdraw')
                                                                                        <span class="text-danger text-center" style="font-size: 8px;font-weight: 500;">(W)</span>
                                                                                        <span
                                                                                            class="score">&#8212;</span>
                                                                                    @elseif($semi_final_status['match_'.$i] == 'Decided by Organisers')
                                                                                        <span class="text-danger text-center" style="font-size: 8px;font-weight: 500;">(D)</span>
                                                                                        <span
                                                                                            class="score">&#8212;</span>
                                                                                    @endif

                                                                                @else
                                                                                    @if($semi_final_status['match_'.$i] == 'Retired')
                                                                                        @if($semi_final_retires)
                                                                                            @if (array_key_exists('match_'.$i, $semi_final_retires))
                                                                                                @if($semi_final_retires['match_'.$i] == $vs_match[1])
                                                                                                    
                                                                                                    <span class="text-danger text-center" style="font-size: 8px;font-weight: 500;">(R)</span>

                                                                                                @endif
                                                                                            @endif
                                                                                        @endif
                                                                                        <span
                                                                                        class="score winnerclractive">{{ ${"sem_m" . $i . "_s1"}[1] }}
                                                                                        {{ ${"sem_m" . $i . "_s2"}[1] }} {{ ${"sem_m" . $i . "_s3"}[1] }}</span>
                                                                                    @elseif($semi_final_status['match_'.$i] == 'Withdraw')
                                                                                        <span
                                                                                        class="score winnerclractive">&#8212;</span>
                                                                                    @elseif($semi_final_status['match_'.$i] == 'Decided by Organisers')
                                                                                        <span
                                                                                        class="score winnerclractive">&#8212;</span>
                                                                                    @endif
                                                                                @endif

                                                                            </li>

                                                                            <span class="custooltiptext">{{ ${"sem_mat_" . $i}[1] }} 

                                                                                @if($semi_final_status)
                                                                                    @if($semi_final_status['match_'.$i] == 'Retired')
                                                                                        @if($semi_final_retires)
                                                                                            @if (array_key_exists('match_'.$i, $semi_final_retires))
                                                                                                @if($semi_final_retires['match_'.$i] == $vs_match[1])
                                                                                                    
                                                                                                    ({{ $semi_final_status['match_'.$i] }})

                                                                                                @endif
                                                                                            @endif
                                                                                        @endif
                                                                                    @else
                                                                                        @if (${"sem_m" . $i . "_p1_total"} > ${"sem_m" . $i . "_p2_total"}) 
                                                                                            ({{ $semi_final_status['match_'.$i] }}) 
                                                                                        @endif
                                                                                    @endif
                                                                                @endif

                                                                            </span>

                                                                        </span>

                                                                    </ul>

                                                                @else
                                                                    @if (array_key_exists('match_'.$i, $semi_final_results))
                                                                        <?php
                                                                            if (${"sem_m" . $i . "_s1"}[0] > ${"sem_m" . $i . "_s1"}[1] && ${"sem_m" . $i . "_s2"}[0] > ${"sem_m" . $i . "_s2"}[1]) {

                                                                                ${"sem_m" . $i . "_p1_total"} = 2;
                                                                                ${"sem_m" . $i . "_p2_total"} = 0;

                                                                            } elseif (${"sem_m" . $i . "_s1"}[1] > ${"sem_m" . $i . "_s1"}[0] && ${"sem_m" . $i . "_s2"}[1] > ${"sem_m" . $i . "_s2"}[0]) {

                                                                                ${"sem_m" . $i . "_p1_total"} = 0;
                                                                                ${"sem_m" . $i . "_p2_total"} = 2;

                                                                            } elseif (${"sem_m" . $i . "_s1"}[0] > ${"sem_m" . $i . "_s1"}[1] && ${"sem_m" . $i . "_s2"}[0] < ${"sem_m" . $i . "_s2"}[1] && ${"sem_m" . $i . "_s3"}[0] > ${"sem_m" . $i . "_s3"}[1]) {

                                                                                ${"sem_m" . $i . "_p1_total"} = 2;
                                                                                ${"sem_m" . $i . "_p2_total"} = 1;

                                                                            } elseif (${"sem_m" . $i . "_s1"}[0] < ${"sem_m" . $i . "_s1"}[1] && ${"sem_m" . $i . "_s2"}[0] > ${"sem_m" . $i . "_s2"}[1] && ${"sem_m" . $i . "_s3"}[0] < ${"sem_m" . $i . "_s3"}[1]) {

                                                                                ${"sem_m" . $i . "_p1_total"} = 1;
                                                                                ${"sem_m" . $i . "_p2_total"} = 2;

                                                                            } elseif (${"sem_m" . $i . "_s1"}[0] < ${"sem_m" . $i . "_s1"}[1] && ${"sem_m" . $i . "_s2"}[0] > ${"sem_m" . $i . "_s2"}[1] && ${"sem_m" . $i . "_s3"}[0] > ${"sem_m" . $i . "_s3"}[1]) {

                                                                                ${"sem_m" . $i . "_p1_total"} = 2;
                                                                                ${"sem_m" . $i . "_p2_total"} = 1;

                                                                            } elseif (${"sem_m" . $i . "_s1"}[0] > ${"sem_m" . $i . "_s1"}[1] && ${"sem_m" . $i . "_s2"}[0] < ${"sem_m" . $i . "_s2"}[1] && ${"sem_m" . $i . "_s3"}[0] < ${"sem_m" . $i . "_s3"}[1]) {

                                                                                ${"sem_m" . $i . "_p1_total"} = 1;
                                                                                ${"sem_m" . $i . "_p2_total"} = 2;

                                                                            }
                                                                        ?>
                                                                        

                                                                        <ul class="matchup">

                                                                            <span class="custooltip">
                                                                                <li
                                                                                    class="team team-top @if (${"sem_m" . $i . "_p1_total"} > ${"sem_m" . $i . "_p2_total"}) winnerclractive @endif">
                                                                                    {{ \Illuminate\Support\Str::limit(${"sem_mat_" . $i}[0], 100) }}

                                                                                    <span
                                                                                        class="score @if (${"sem_m" . $i . "_p1_total"} > ${"sem_m" . $i . "_p2_total"}) winnerclractive @endif">{{ ${"sem_m" . $i . "_s1"}[0] }}
                                                                                        {{ ${"sem_m" . $i . "_s2"}[0] }} {{ ${"sem_m" . $i . "_s3"}[0] }}</span>

                                                                                </li>
                                                                                <span class="custooltiptext">{{ ${"sem_mat_" . $i}[0] }}</span>
                                                                            </span>

                                                                            <span class="custooltip">
                                                                                <li
                                                                                    class="team team-bottom @if (${"sem_m" . $i . "_p2_total"} > ${"sem_m" . $i . "_p1_total"}) winnerclractive @endif">
                                                                                    {{ \Illuminate\Support\Str::limit(${"sem_mat_" . $i}[1], 100) }}

                                                                                    <span
                                                                                        class="score @if (${"sem_m" . $i . "_p2_total"} > ${"sem_m" . $i . "_p1_total"}) winnerclractive @endif">{{ ${"sem_m" . $i . "_s1"}[1] }}
                                                                                        {{ ${"sem_m" . $i . "_s2"}[1] }} {{ ${"sem_m" . $i . "_s3"}[1] }}</span>

                                                                                </li>
                                                                                <span class="custooltiptext">{{ ${"sem_mat_" . $i}[1] }}</span>
                                                                            </span>

                                                                        </ul>
                                                                    @else
                                                                        <ul class="matchup">

                                                                            <span class="custooltip">
                                                                                <li class="team team-top">
                                                                                    {{ \Illuminate\Support\Str::limit(${"sem_mat_" . $i}[0], 100) }}

                                                                                    <span class="score">N/A</span>

                                                                                </li>
                                                                                <span class="custooltiptext">{{ ${"sem_mat_" . $i}[0] }}</span>
                                                                            </span>

                                                                            <span class="custooltip">
                                                                                <li class="team team-bottom">
                                                                                    {{ \Illuminate\Support\Str::limit(${"sem_mat_" . $i}[1], 100) }}

                                                                                    <span class="score">N/A</span>

                                                                                </li>
                                                                                <span class="custooltiptext">{{ ${"sem_mat_" . $i}[1] }}</span>
                                                                            </span>

                                                                        </ul>
                                                                    @endif
                                                                @endif


                                                            @else
                                                                @if (array_key_exists('match_'.$i, $semi_final_results))
                                                                    <?php
                                                                        if (${"sem_m" . $i . "_s1"}[0] > ${"sem_m" . $i . "_s1"}[1] && ${"sem_m" . $i . "_s2"}[0] > ${"sem_m" . $i . "_s2"}[1]) {

                                                                            ${"sem_m" . $i . "_p1_total"} = 2;
                                                                            ${"sem_m" . $i . "_p2_total"} = 0;

                                                                        } elseif (${"sem_m" . $i . "_s1"}[1] > ${"sem_m" . $i . "_s1"}[0] && ${"sem_m" . $i . "_s2"}[1] > ${"sem_m" . $i . "_s2"}[0]) {

                                                                            ${"sem_m" . $i . "_p1_total"} = 0;
                                                                            ${"sem_m" . $i . "_p2_total"} = 2;

                                                                        } elseif (${"sem_m" . $i . "_s1"}[0] > ${"sem_m" . $i . "_s1"}[1] && ${"sem_m" . $i . "_s2"}[0] < ${"sem_m" . $i . "_s2"}[1] && ${"sem_m" . $i . "_s3"}[0] > ${"sem_m" . $i . "_s3"}[1]) {

                                                                            ${"sem_m" . $i . "_p1_total"} = 2;
                                                                            ${"sem_m" . $i . "_p2_total"} = 1;

                                                                        } elseif (${"sem_m" . $i . "_s1"}[0] < ${"sem_m" . $i . "_s1"}[1] && ${"sem_m" . $i . "_s2"}[0] > ${"sem_m" . $i . "_s2"}[1] && ${"sem_m" . $i . "_s3"}[0] < ${"sem_m" . $i . "_s3"}[1]) {

                                                                            ${"sem_m" . $i . "_p1_total"} = 1;
                                                                            ${"sem_m" . $i . "_p2_total"} = 2;

                                                                        } elseif (${"sem_m" . $i . "_s1"}[0] < ${"sem_m" . $i . "_s1"}[1] && ${"sem_m" . $i . "_s2"}[0] > ${"sem_m" . $i . "_s2"}[1] && ${"sem_m" . $i . "_s3"}[0] > ${"sem_m" . $i . "_s3"}[1]) {

                                                                            ${"sem_m" . $i . "_p1_total"} = 2;
                                                                            ${"sem_m" . $i . "_p2_total"} = 1;

                                                                        } elseif (${"sem_m" . $i . "_s1"}[0] > ${"sem_m" . $i . "_s1"}[1] && ${"sem_m" . $i . "_s2"}[0] < ${"sem_m" . $i . "_s2"}[1] && ${"sem_m" . $i . "_s3"}[0] < ${"sem_m" . $i . "_s3"}[1]) {

                                                                            ${"sem_m" . $i . "_p1_total"} = 1;
                                                                            ${"sem_m" . $i . "_p2_total"} = 2;

                                                                        }
                                                                    ?>
                                                                    

                                                                    <ul class="matchup">

                                                                        <span class="custooltip">
                                                                            <li
                                                                                class="team team-top @if (${"sem_m" . $i . "_p1_total"} > ${"sem_m" . $i . "_p2_total"}) winnerclractive @endif">
                                                                                {{ \Illuminate\Support\Str::limit(${"sem_mat_" . $i}[0], 100) }}

                                                                                <span
                                                                                    class="score @if (${"sem_m" . $i . "_p1_total"} > ${"sem_m" . $i . "_p2_total"}) winnerclractive @endif">{{ ${"sem_m" . $i . "_s1"}[0] }}
                                                                                    {{ ${"sem_m" . $i . "_s2"}[0] }} {{ ${"sem_m" . $i . "_s3"}[0] }}</span>

                                                                            </li>
                                                                            <span class="custooltiptext">{{ ${"sem_mat_" . $i}[0] }}</span>
                                                                        </span>

                                                                        <span class="custooltip">
                                                                            <li
                                                                                class="team team-bottom @if (${"sem_m" . $i . "_p2_total"} > ${"sem_m" . $i . "_p1_total"}) winnerclractive @endif">
                                                                                {{ \Illuminate\Support\Str::limit(${"sem_mat_" . $i}[1], 100) }}

                                                                                <span
                                                                                    class="score @if (${"sem_m" . $i . "_p2_total"} > ${"sem_m" . $i . "_p1_total"}) winnerclractive @endif">{{ ${"sem_m" . $i . "_s1"}[1] }}
                                                                                    {{ ${"sem_m" . $i . "_s2"}[1] }} {{ ${"sem_m" . $i . "_s3"}[1] }}</span>

                                                                            </li>
                                                                            <span class="custooltiptext">{{ ${"sem_mat_" . $i}[1] }}</span>
                                                                        </span>

                                                                    </ul>
                                                                @else
                                                                    <ul class="matchup">

                                                                        <span class="custooltip">
                                                                            <li class="team team-top">
                                                                                {{ \Illuminate\Support\Str::limit(${"sem_mat_" . $i}[0], 100) }}

                                                                                <span class="score">N/A</span>

                                                                            </li>
                                                                            <span class="custooltiptext">{{ ${"sem_mat_" . $i}[0] }}</span>
                                                                        </span>

                                                                        <span class="custooltip">
                                                                            <li class="team team-bottom">
                                                                                {{ \Illuminate\Support\Str::limit(${"sem_mat_" . $i}[1], 100) }}

                                                                                <span class="score">N/A</span>

                                                                            </li>
                                                                            <span class="custooltiptext">{{ ${"sem_mat_" . $i}[1] }}</span>
                                                                        </span>

                                                                    </ul>
                                                                @endif
                                                            @endif

                                                        @else
                                                            @if (array_key_exists('match_'.$i, $semi_final_results))
                                                                <?php
                                                                        if (${"sem_m" . $i . "_s1"}[0] > ${"sem_m" . $i . "_s1"}[1] && ${"sem_m" . $i . "_s2"}[0] > ${"sem_m" . $i . "_s2"}[1]) {

                                                                            ${"sem_m" . $i . "_p1_total"} = 2;
                                                                            ${"sem_m" . $i . "_p2_total"} = 0;

                                                                        } elseif (${"sem_m" . $i . "_s1"}[1] > ${"sem_m" . $i . "_s1"}[0] && ${"sem_m" . $i . "_s2"}[1] > ${"sem_m" . $i . "_s2"}[0]) {

                                                                            ${"sem_m" . $i . "_p1_total"} = 0;
                                                                            ${"sem_m" . $i . "_p2_total"} = 2;

                                                                        } elseif (${"sem_m" . $i . "_s1"}[0] > ${"sem_m" . $i . "_s1"}[1] && ${"sem_m" . $i . "_s2"}[0] < ${"sem_m" . $i . "_s2"}[1] && ${"sem_m" . $i . "_s3"}[0] > ${"sem_m" . $i . "_s3"}[1]) {

                                                                            ${"sem_m" . $i . "_p1_total"} = 2;
                                                                            ${"sem_m" . $i . "_p2_total"} = 1;

                                                                        } elseif (${"sem_m" . $i . "_s1"}[0] < ${"sem_m" . $i . "_s1"}[1] && ${"sem_m" . $i . "_s2"}[0] > ${"sem_m" . $i . "_s2"}[1] && ${"sem_m" . $i . "_s3"}[0] < ${"sem_m" . $i . "_s3"}[1]) {

                                                                            ${"sem_m" . $i . "_p1_total"} = 1;
                                                                            ${"sem_m" . $i . "_p2_total"} = 2;

                                                                        } elseif (${"sem_m" . $i . "_s1"}[0] < ${"sem_m" . $i . "_s1"}[1] && ${"sem_m" . $i . "_s2"}[0] > ${"sem_m" . $i . "_s2"}[1] && ${"sem_m" . $i . "_s3"}[0] > ${"sem_m" . $i . "_s3"}[1]) {

                                                                            ${"sem_m" . $i . "_p1_total"} = 2;
                                                                            ${"sem_m" . $i . "_p2_total"} = 1;

                                                                        } elseif (${"sem_m" . $i . "_s1"}[0] > ${"sem_m" . $i . "_s1"}[1] && ${"sem_m" . $i . "_s2"}[0] < ${"sem_m" . $i . "_s2"}[1] && ${"sem_m" . $i . "_s3"}[0] < ${"sem_m" . $i . "_s3"}[1]) {

                                                                            ${"sem_m" . $i . "_p1_total"} = 1;
                                                                            ${"sem_m" . $i . "_p2_total"} = 2;

                                                                        }
                                                                ?>
                                                                

                                                                <ul class="matchup">

                                                                    <span class="custooltip">
                                                                        <li
                                                                            class="team team-top @if (${"sem_m" . $i . "_p1_total"} > ${"sem_m" . $i . "_p2_total"}) winnerclractive @endif">
                                                                            {{ \Illuminate\Support\Str::limit(${"sem_mat_" . $i}[0], 100) }}

                                                                            <span
                                                                                class="score @if (${"sem_m" . $i . "_p1_total"} > ${"sem_m" . $i . "_p2_total"}) winnerclractive @endif">{{ ${"sem_m" . $i . "_s1"}[0] }}
                                                                                {{ ${"sem_m" . $i . "_s2"}[0] }} {{ ${"sem_m" . $i . "_s3"}[0] }}</span>

                                                                        </li>
                                                                        <span class="custooltiptext">{{ ${"sem_mat_" . $i}[0] }}</span>
                                                                    </span>

                                                                    <span class="custooltip">
                                                                        <li
                                                                            class="team team-bottom @if (${"sem_m" . $i . "_p2_total"} > ${"sem_m" . $i . "_p1_total"}) winnerclractive @endif">
                                                                            {{ \Illuminate\Support\Str::limit(${"sem_mat_" . $i}[1], 100) }}

                                                                            <span
                                                                                class="score @if (${"sem_m" . $i . "_p2_total"} > ${"sem_m" . $i . "_p1_total"}) winnerclractive @endif">{{ ${"sem_m" . $i . "_s1"}[1] }}
                                                                                {{ ${"sem_m" . $i . "_s2"}[1] }} {{ ${"sem_m" . $i . "_s3"}[1] }}</span>

                                                                        </li>
                                                                        <span class="custooltiptext">{{ ${"sem_mat_" . $i}[1] }}</span>
                                                                    </span>

                                                                </ul>
                                                            @else
                                                                <ul class="matchup">

                                                                    <span class="custooltip">
                                                                        <li class="team team-top">
                                                                            {{ \Illuminate\Support\Str::limit(${"sem_mat_" . $i}[0], 100) }}

                                                                            <span class="score">N/A</span>

                                                                        </li>
                                                                        <span class="custooltiptext">{{ ${"sem_mat_" . $i}[0] }}</span>
                                                                    </span>

                                                                    <span class="custooltip">
                                                                        <li class="team team-bottom">
                                                                            {{ \Illuminate\Support\Str::limit(${"sem_mat_" . $i}[1], 100) }}

                                                                            <span class="score">N/A</span>

                                                                        </li>
                                                                        <span class="custooltiptext">{{ ${"sem_mat_" . $i}[1] }}</span>
                                                                    </span>

                                                                </ul>
                                                            @endif
                                                        @endif


                                                    @else
                                                        <ul class="matchup">

                                                            <span class="custooltip">
                                                                <li class="team team-top">
                                                                    {{ \Illuminate\Support\Str::limit(${"sem_mat_" . $i}[0], 100) }}

                                                                    <span class="score">N/A</span>

                                                                </li>
                                                                <span class="custooltiptext">{{ ${"sem_mat_" . $i}[0] }}</span>
                                                            </span>

                                                            <span class="custooltip">
                                                                <li class="team team-bottom">
                                                                    {{ \Illuminate\Support\Str::limit(${"sem_mat_" . $i}[1], 100) }}

                                                                    <span class="score">N/A</span>

                                                                </li>
                                                                <span class="custooltiptext">{{ ${"sem_mat_" . $i}[1] }}</span>
                                                            </span>
                                                        </ul>
                                                    @endif
                                                @else
                                                    <ul class="matchup">

                                                        <li class="team team-top">n/a<span class="score">N/A</span></li>
                                                        <li class="team team-bottom">n/a<span class="score">N/A</span></li>
                                                    </ul>
                                                @endif
                                                
                                            @else
                                                
                                                <ul class="matchup">
                                                    
                                                    <li class="team team-top"><span class="score"></span></li>
                                                    <li class="team team-bottom"><span class="score"></span></li>
                                                </ul>
                                                
                                            @endif

                                        @endif

                                    @endfor

                                </div>                                
                            @else
                                @if($tournament->semi_final_matches)
                                    
                                    <div class="round round-two current">
                                        <div class="round-details">semifinal<br/><span class="date">@if($t_d_semf) {{ $strt_sem[0] }} - {{ $endd_sem[0] }} @else N/A @endif</span>
                                        </div>
                                        
                                        @for($i = 1; $i < 2; $i++)
                                            @if (array_key_exists('match_'.$i, $semi_final_matches))

                                                <?php 
                                                    $get_matches = $semi_final_matches['match_'.$i];
                                                    $vs_match = explode(" VS ", $get_matches);
                                                ?>

                                                @if ($tournament->semi_final_results)
                                                        
                                                    @if($semi_final_status)
                                                        @if (array_key_exists('match_'.$i, $semi_final_status))
                                                            @if($semi_final_status['match_'.$i])
                                                                <?php
                                                                    if (${"sem_m" . $i . "_s1"}[0] > ${"sem_m" . $i . "_s1"}[1] && ${"sem_m" . $i . "_s2"}[0] > ${"sem_m" . $i . "_s2"}[1]) {

                                                                        ${"sem_m" . $i . "_p1_total"} = 2;
                                                                        ${"sem_m" . $i . "_p2_total"} = 0;

                                                                    } elseif (${"sem_m" . $i . "_s1"}[1] > ${"sem_m" . $i . "_s1"}[0] && ${"sem_m" . $i . "_s2"}[1] > ${"sem_m" . $i . "_s2"}[0]) {

                                                                        ${"sem_m" . $i . "_p1_total"} = 0;
                                                                        ${"sem_m" . $i . "_p2_total"} = 2;

                                                                    } elseif (${"sem_m" . $i . "_s1"}[0] > ${"sem_m" . $i . "_s1"}[1] && ${"sem_m" . $i . "_s2"}[0] < ${"sem_m" . $i . "_s2"}[1] && ${"sem_m" . $i . "_s3"}[0] > ${"sem_m" . $i . "_s3"}[1]) {

                                                                        ${"sem_m" . $i . "_p1_total"} = 2;
                                                                        ${"sem_m" . $i . "_p2_total"} = 1;

                                                                    } elseif (${"sem_m" . $i . "_s1"}[0] < ${"sem_m" . $i . "_s1"}[1] && ${"sem_m" . $i . "_s2"}[0] > ${"sem_m" . $i . "_s2"}[1] && ${"sem_m" . $i . "_s3"}[0] < ${"sem_m" . $i . "_s3"}[1]) {

                                                                        ${"sem_m" . $i . "_p1_total"} = 1;
                                                                        ${"sem_m" . $i . "_p2_total"} = 2;

                                                                    } elseif (${"sem_m" . $i . "_s1"}[0] < ${"sem_m" . $i . "_s1"}[1] && ${"sem_m" . $i . "_s2"}[0] > ${"sem_m" . $i . "_s2"}[1] && ${"sem_m" . $i . "_s3"}[0] > ${"sem_m" . $i . "_s3"}[1]) {

                                                                        ${"sem_m" . $i . "_p1_total"} = 2;
                                                                        ${"sem_m" . $i . "_p2_total"} = 1;

                                                                    } elseif (${"sem_m" . $i . "_s1"}[0] > ${"sem_m" . $i . "_s1"}[1] && ${"sem_m" . $i . "_s2"}[0] < ${"sem_m" . $i . "_s2"}[1] && ${"sem_m" . $i . "_s3"}[0] < ${"sem_m" . $i . "_s3"}[1]) {

                                                                        ${"sem_m" . $i . "_p1_total"} = 1;
                                                                        ${"sem_m" . $i . "_p2_total"} = 2;

                                                                    }
                                                                ?>


                                                                <ul class="matchup">

                                                                    <span class="custooltip">
                                                                        <li
                                                                            class="team team-top @if (${"sem_m" . $i . "_p1_total"} > ${"sem_m" . $i . "_p2_total"}) winnerclractive @endif">
                                                                            {{ \Illuminate\Support\Str::limit(${"sem_mat_" . $i}[0], 100) }}

                                                                            @if (${"sem_m" . $i . "_p1_total"} < ${"sem_m" . $i . "_p2_total"})
                                                                                @if($semi_final_status['match_'.$i] == 'Retired')
                                                                                    @if($semi_final_retires)
                                                                                        @if (array_key_exists('match_'.$i, $semi_final_retires))
                                                                                            @if($semi_final_retires['match_'.$i] == $vs_match[0])
                                                                                                
                                                                                                <span class="text-danger text-center" style="font-size: 8px;font-weight: 500;">(R)</span>

                                                                                            @endif
                                                                                        @endif
                                                                                    @endif
                                                                                    <span
                                                                                        class="score">{{ ${"sem_m" . $i . "_s1"}[0] }}
                                                                                        {{ ${"sem_m" . $i . "_s2"}[0] }} {{ ${"sem_m" . $i . "_s3"}[0] }}</span>
                                                                                @elseif($semi_final_status['match_'.$i] == 'Withdraw')
                                                                                    <span class="text-danger text-center" style="font-size: 8px;font-weight: 500;">(W)</span>
                                                                                    <span
                                                                                        class="score">&#8212;</span>
                                                                                @elseif($semi_final_status['match_'.$i] == 'Decided by Organisers')
                                                                                    <span class="text-danger text-center" style="font-size: 8px;font-weight: 500;">(D)</span>
                                                                                    <span
                                                                                        class="score">&#8212;</span>
                                                                                @endif

                                                                            @else
                                                                                @if($semi_final_status['match_'.$i] == 'Retired')
                                                                                    @if($semi_final_retires)
                                                                                        @if (array_key_exists('match_'.$i, $semi_final_retires))
                                                                                            @if($semi_final_retires['match_'.$i] == $vs_match[0])
                                                                                                
                                                                                                <span class="text-danger text-center" style="font-size: 8px;font-weight: 500;">(R)</span>

                                                                                            @endif
                                                                                        @endif
                                                                                    @endif
                                                                                    <span
                                                                                    class="score winnerclractive">{{ ${"sem_m" . $i . "_s1"}[0] }}
                                                                                    {{ ${"sem_m" . $i . "_s2"}[0] }} {{ ${"sem_m" . $i . "_s3"}[0] }}</span>
                                                                                @elseif($semi_final_status['match_'.$i] == 'Withdraw')
                                                                                    <span
                                                                                    class="score winnerclractive">&#8212;</span>
                                                                                @elseif($semi_final_status['match_'.$i] == 'Decided by Organisers')
                                                                                    <span
                                                                                    class="score winnerclractive">&#8212;</span>
                                                                                @endif
                                                                                    
                                                                            @endif

                                                                        </li>

                                                                        <span class="custooltiptext">{{ ${"sem_mat_" . $i}[0] }} 
                                                                            @if($semi_final_status)
                                                                                @if($semi_final_status['match_'.$i] == 'Retired')
                                                                                    @if($semi_final_retires)
                                                                                        @if (array_key_exists('match_'.$i, $semi_final_retires))
                                                                                            @if($semi_final_retires['match_'.$i] == $vs_match[0])
                                                                                                
                                                                                                ({{ $semi_final_status['match_'.$i] }})

                                                                                            @endif
                                                                                        @endif
                                                                                    @endif
                                                                                @else
                                                                                    @if (${"sem_m" . $i . "_p1_total"} < ${"sem_m" . $i . "_p2_total"}) 
                                                                                        ({{ $semi_final_status['match_'.$i] }}) 
                                                                                    @endif
                                                                                @endif
                                                                            @endif
                                                                        </span>

                                                                    </span>

                                                                    <span class="custooltip">
                                                                        <li
                                                                            class="team team-bottom @if (${"sem_m" . $i . "_p2_total"} > ${"sem_m" . $i . "_p1_total"}) winnerclractive @endif">
                                                                            {{ \Illuminate\Support\Str::limit(${"sem_mat_" . $i}[1], 100) }}

                                                                            @if (${"sem_m" . $i . "_p1_total"} > ${"sem_m" . $i . "_p2_total"})
                                                                                @if($semi_final_status['match_'.$i] == 'Retired')
                                                                                    @if($semi_final_retires)
                                                                                        @if (array_key_exists('match_'.$i, $semi_final_retires))
                                                                                            @if($semi_final_retires['match_'.$i] == $vs_match[1])
                                                                                                
                                                                                                <span class="text-danger text-center" style="font-size: 8px;font-weight: 500;">(R)</span>

                                                                                            @endif
                                                                                        @endif
                                                                                    @endif
                                                                                    <span
                                                                                        class="score">{{ ${"sem_m" . $i . "_s1"}[1] }}
                                                                                        {{ ${"sem_m" . $i . "_s2"}[1] }} {{ ${"sem_m" . $i . "_s3"}[1] }}</span>
                                                                                @elseif($semi_final_status['match_'.$i] == 'Withdraw')
                                                                                    <span class="text-danger text-center" style="font-size: 8px;font-weight: 500;">(W)</span>
                                                                                    <span
                                                                                        class="score">&#8212;</span>
                                                                                @elseif($semi_final_status['match_'.$i] == 'Decided by Organisers')
                                                                                    <span class="text-danger text-center" style="font-size: 8px;font-weight: 500;">(D)</span>
                                                                                    <span
                                                                                        class="score">&#8212;</span>
                                                                                @endif

                                                                            @else
                                                                                @if($semi_final_status['match_'.$i] == 'Retired')
                                                                                    @if($semi_final_retires)
                                                                                        @if (array_key_exists('match_'.$i, $semi_final_retires))
                                                                                            @if($semi_final_retires['match_'.$i] == $vs_match[1])
                                                                                                
                                                                                                <span class="text-danger text-center" style="font-size: 8px;font-weight: 500;">(R)</span>

                                                                                            @endif
                                                                                        @endif
                                                                                    @endif
                                                                                    <span
                                                                                    class="score winnerclractive">{{ ${"sem_m" . $i . "_s1"}[1] }}
                                                                                    {{ ${"sem_m" . $i . "_s2"}[1] }} {{ ${"sem_m" . $i . "_s3"}[1] }}</span>
                                                                                @elseif($semi_final_status['match_'.$i] == 'Withdraw')
                                                                                    <span
                                                                                    class="score winnerclractive">&#8212;</span>
                                                                                @elseif($semi_final_status['match_'.$i] == 'Decided by Organisers')
                                                                                    <span
                                                                                    class="score winnerclractive">&#8212;</span>
                                                                                @endif
                                                                            @endif

                                                                        </li>

                                                                        <span class="custooltiptext">{{ ${"sem_mat_" . $i}[1] }} 

                                                                            @if($semi_final_status)
                                                                                @if($semi_final_status['match_'.$i] == 'Retired')
                                                                                    @if($semi_final_retires)
                                                                                        @if (array_key_exists('match_'.$i, $semi_final_retires))
                                                                                            @if($semi_final_retires['match_'.$i] == $vs_match[1])
                                                                                                
                                                                                                ({{ $semi_final_status['match_'.$i] }})

                                                                                            @endif
                                                                                        @endif
                                                                                    @endif
                                                                                @else
                                                                                    @if (${"sem_m" . $i . "_p1_total"} > ${"sem_m" . $i . "_p2_total"}) 
                                                                                        ({{ $semi_final_status['match_'.$i] }}) 
                                                                                    @endif
                                                                                @endif
                                                                            @endif

                                                                        </span>

                                                                    </span>

                                                                </ul>

                                                            @else
                                                                @if (array_key_exists('match_'.$i, $semi_final_results))
                                                                    <?php
                                                                        if (${"sem_m" . $i . "_s1"}[0] > ${"sem_m" . $i . "_s1"}[1] && ${"sem_m" . $i . "_s2"}[0] > ${"sem_m" . $i . "_s2"}[1]) {

                                                                            ${"sem_m" . $i . "_p1_total"} = 2;
                                                                            ${"sem_m" . $i . "_p2_total"} = 0;

                                                                        } elseif (${"sem_m" . $i . "_s1"}[1] > ${"sem_m" . $i . "_s1"}[0] && ${"sem_m" . $i . "_s2"}[1] > ${"sem_m" . $i . "_s2"}[0]) {

                                                                            ${"sem_m" . $i . "_p1_total"} = 0;
                                                                            ${"sem_m" . $i . "_p2_total"} = 2;

                                                                        } elseif (${"sem_m" . $i . "_s1"}[0] > ${"sem_m" . $i . "_s1"}[1] && ${"sem_m" . $i . "_s2"}[0] < ${"sem_m" . $i . "_s2"}[1] && ${"sem_m" . $i . "_s3"}[0] > ${"sem_m" . $i . "_s3"}[1]) {

                                                                            ${"sem_m" . $i . "_p1_total"} = 2;
                                                                            ${"sem_m" . $i . "_p2_total"} = 1;

                                                                        } elseif (${"sem_m" . $i . "_s1"}[0] < ${"sem_m" . $i . "_s1"}[1] && ${"sem_m" . $i . "_s2"}[0] > ${"sem_m" . $i . "_s2"}[1] && ${"sem_m" . $i . "_s3"}[0] < ${"sem_m" . $i . "_s3"}[1]) {

                                                                            ${"sem_m" . $i . "_p1_total"} = 1;
                                                                            ${"sem_m" . $i . "_p2_total"} = 2;

                                                                        } elseif (${"sem_m" . $i . "_s1"}[0] < ${"sem_m" . $i . "_s1"}[1] && ${"sem_m" . $i . "_s2"}[0] > ${"sem_m" . $i . "_s2"}[1] && ${"sem_m" . $i . "_s3"}[0] > ${"sem_m" . $i . "_s3"}[1]) {

                                                                            ${"sem_m" . $i . "_p1_total"} = 2;
                                                                            ${"sem_m" . $i . "_p2_total"} = 1;

                                                                        } elseif (${"sem_m" . $i . "_s1"}[0] > ${"sem_m" . $i . "_s1"}[1] && ${"sem_m" . $i . "_s2"}[0] < ${"sem_m" . $i . "_s2"}[1] && ${"sem_m" . $i . "_s3"}[0] < ${"sem_m" . $i . "_s3"}[1]) {

                                                                            ${"sem_m" . $i . "_p1_total"} = 1;
                                                                            ${"sem_m" . $i . "_p2_total"} = 2;

                                                                        }
                                                                    ?>
                                                                    

                                                                    <ul class="matchup">

                                                                        <span class="custooltip">
                                                                            <li
                                                                                class="team team-top @if (${"sem_m" . $i . "_p1_total"} > ${"sem_m" . $i . "_p2_total"}) winnerclractive @endif">
                                                                                {{ \Illuminate\Support\Str::limit(${"sem_mat_" . $i}[0], 100) }}

                                                                                <span
                                                                                    class="score @if (${"sem_m" . $i . "_p1_total"} > ${"sem_m" . $i . "_p2_total"}) winnerclractive @endif">{{ ${"sem_m" . $i . "_s1"}[0] }}
                                                                                    {{ ${"sem_m" . $i . "_s2"}[0] }} {{ ${"sem_m" . $i . "_s3"}[0] }}</span>

                                                                            </li>
                                                                            <span class="custooltiptext">{{ ${"sem_mat_" . $i}[0] }}</span>
                                                                        </span>

                                                                        <span class="custooltip">
                                                                            <li
                                                                                class="team team-bottom @if (${"sem_m" . $i . "_p2_total"} > ${"sem_m" . $i . "_p1_total"}) winnerclractive @endif">
                                                                                {{ \Illuminate\Support\Str::limit(${"sem_mat_" . $i}[1], 100) }}

                                                                                <span
                                                                                    class="score @if (${"sem_m" . $i . "_p2_total"} > ${"sem_m" . $i . "_p1_total"}) winnerclractive @endif">{{ ${"sem_m" . $i . "_s1"}[1] }}
                                                                                    {{ ${"sem_m" . $i . "_s2"}[1] }} {{ ${"sem_m" . $i . "_s3"}[1] }}</span>

                                                                            </li>
                                                                            <span class="custooltiptext">{{ ${"sem_mat_" . $i}[1] }}</span>
                                                                        </span>

                                                                    </ul>
                                                                @else
                                                                    <ul class="matchup">

                                                                        <span class="custooltip">
                                                                            <li class="team team-top">
                                                                                {{ \Illuminate\Support\Str::limit(${"sem_mat_" . $i}[0], 100) }}

                                                                                <span class="score">N/A</span>

                                                                            </li>
                                                                            <span class="custooltiptext">{{ ${"sem_mat_" . $i}[0] }}</span>
                                                                        </span>

                                                                        <span class="custooltip">
                                                                            <li class="team team-bottom">
                                                                                {{ \Illuminate\Support\Str::limit(${"sem_mat_" . $i}[1], 100) }}

                                                                                <span class="score">N/A</span>

                                                                            </li>
                                                                            <span class="custooltiptext">{{ ${"sem_mat_" . $i}[1] }}</span>
                                                                        </span>

                                                                    </ul>
                                                                @endif
                                                            @endif


                                                        @else
                                                            @if (array_key_exists('match_'.$i, $semi_final_results))
                                                                <?php
                                                                    if (${"sem_m" . $i . "_s1"}[0] > ${"sem_m" . $i . "_s1"}[1] && ${"sem_m" . $i . "_s2"}[0] > ${"sem_m" . $i . "_s2"}[1]) {

                                                                        ${"sem_m" . $i . "_p1_total"} = 2;
                                                                        ${"sem_m" . $i . "_p2_total"} = 0;

                                                                    } elseif (${"sem_m" . $i . "_s1"}[1] > ${"sem_m" . $i . "_s1"}[0] && ${"sem_m" . $i . "_s2"}[1] > ${"sem_m" . $i . "_s2"}[0]) {

                                                                        ${"sem_m" . $i . "_p1_total"} = 0;
                                                                        ${"sem_m" . $i . "_p2_total"} = 2;

                                                                    } elseif (${"sem_m" . $i . "_s1"}[0] > ${"sem_m" . $i . "_s1"}[1] && ${"sem_m" . $i . "_s2"}[0] < ${"sem_m" . $i . "_s2"}[1] && ${"sem_m" . $i . "_s3"}[0] > ${"sem_m" . $i . "_s3"}[1]) {

                                                                        ${"sem_m" . $i . "_p1_total"} = 2;
                                                                        ${"sem_m" . $i . "_p2_total"} = 1;

                                                                    } elseif (${"sem_m" . $i . "_s1"}[0] < ${"sem_m" . $i . "_s1"}[1] && ${"sem_m" . $i . "_s2"}[0] > ${"sem_m" . $i . "_s2"}[1] && ${"sem_m" . $i . "_s3"}[0] < ${"sem_m" . $i . "_s3"}[1]) {

                                                                        ${"sem_m" . $i . "_p1_total"} = 1;
                                                                        ${"sem_m" . $i . "_p2_total"} = 2;

                                                                    } elseif (${"sem_m" . $i . "_s1"}[0] < ${"sem_m" . $i . "_s1"}[1] && ${"sem_m" . $i . "_s2"}[0] > ${"sem_m" . $i . "_s2"}[1] && ${"sem_m" . $i . "_s3"}[0] > ${"sem_m" . $i . "_s3"}[1]) {

                                                                        ${"sem_m" . $i . "_p1_total"} = 2;
                                                                        ${"sem_m" . $i . "_p2_total"} = 1;

                                                                    } elseif (${"sem_m" . $i . "_s1"}[0] > ${"sem_m" . $i . "_s1"}[1] && ${"sem_m" . $i . "_s2"}[0] < ${"sem_m" . $i . "_s2"}[1] && ${"sem_m" . $i . "_s3"}[0] < ${"sem_m" . $i . "_s3"}[1]) {

                                                                        ${"sem_m" . $i . "_p1_total"} = 1;
                                                                        ${"sem_m" . $i . "_p2_total"} = 2;

                                                                    }
                                                                ?>
                                                                

                                                                <ul class="matchup">

                                                                    <span class="custooltip">
                                                                        <li
                                                                            class="team team-top @if (${"sem_m" . $i . "_p1_total"} > ${"sem_m" . $i . "_p2_total"}) winnerclractive @endif">
                                                                            {{ \Illuminate\Support\Str::limit(${"sem_mat_" . $i}[0], 100) }}

                                                                            <span
                                                                                class="score @if (${"sem_m" . $i . "_p1_total"} > ${"sem_m" . $i . "_p2_total"}) winnerclractive @endif">{{ ${"sem_m" . $i . "_s1"}[0] }}
                                                                                {{ ${"sem_m" . $i . "_s2"}[0] }} {{ ${"sem_m" . $i . "_s3"}[0] }}</span>

                                                                        </li>
                                                                        <span class="custooltiptext">{{ ${"sem_mat_" . $i}[0] }}</span>
                                                                    </span>

                                                                    <span class="custooltip">
                                                                        <li
                                                                            class="team team-bottom @if (${"sem_m" . $i . "_p2_total"} > ${"sem_m" . $i . "_p1_total"}) winnerclractive @endif">
                                                                            {{ \Illuminate\Support\Str::limit(${"sem_mat_" . $i}[1], 100) }}

                                                                            <span
                                                                                class="score @if (${"sem_m" . $i . "_p2_total"} > ${"sem_m" . $i . "_p1_total"}) winnerclractive @endif">{{ ${"sem_m" . $i . "_s1"}[1] }}
                                                                                {{ ${"sem_m" . $i . "_s2"}[1] }} {{ ${"sem_m" . $i . "_s3"}[1] }}</span>

                                                                        </li>
                                                                        <span class="custooltiptext">{{ ${"sem_mat_" . $i}[1] }}</span>
                                                                    </span>

                                                                </ul>
                                                            @else
                                                                <ul class="matchup">

                                                                    <span class="custooltip">
                                                                        <li class="team team-top">
                                                                            {{ \Illuminate\Support\Str::limit(${"sem_mat_" . $i}[0], 100) }}

                                                                            <span class="score">N/A</span>

                                                                        </li>
                                                                        <span class="custooltiptext">{{ ${"sem_mat_" . $i}[0] }}</span>
                                                                    </span>

                                                                    <span class="custooltip">
                                                                        <li class="team team-bottom">
                                                                            {{ \Illuminate\Support\Str::limit(${"sem_mat_" . $i}[1], 100) }}

                                                                            <span class="score">N/A</span>

                                                                        </li>
                                                                        <span class="custooltiptext">{{ ${"sem_mat_" . $i}[1] }}</span>
                                                                    </span>

                                                                </ul>
                                                            @endif
                                                        @endif

                                                    @else
                                                        @if (array_key_exists('match_'.$i, $semi_final_results))
                                                            <?php
                                                                    if (${"sem_m" . $i . "_s1"}[0] > ${"sem_m" . $i . "_s1"}[1] && ${"sem_m" . $i . "_s2"}[0] > ${"sem_m" . $i . "_s2"}[1]) {

                                                                        ${"sem_m" . $i . "_p1_total"} = 2;
                                                                        ${"sem_m" . $i . "_p2_total"} = 0;

                                                                    } elseif (${"sem_m" . $i . "_s1"}[1] > ${"sem_m" . $i . "_s1"}[0] && ${"sem_m" . $i . "_s2"}[1] > ${"sem_m" . $i . "_s2"}[0]) {

                                                                        ${"sem_m" . $i . "_p1_total"} = 0;
                                                                        ${"sem_m" . $i . "_p2_total"} = 2;

                                                                    } elseif (${"sem_m" . $i . "_s1"}[0] > ${"sem_m" . $i . "_s1"}[1] && ${"sem_m" . $i . "_s2"}[0] < ${"sem_m" . $i . "_s2"}[1] && ${"sem_m" . $i . "_s3"}[0] > ${"sem_m" . $i . "_s3"}[1]) {

                                                                        ${"sem_m" . $i . "_p1_total"} = 2;
                                                                        ${"sem_m" . $i . "_p2_total"} = 1;

                                                                    } elseif (${"sem_m" . $i . "_s1"}[0] < ${"sem_m" . $i . "_s1"}[1] && ${"sem_m" . $i . "_s2"}[0] > ${"sem_m" . $i . "_s2"}[1] && ${"sem_m" . $i . "_s3"}[0] < ${"sem_m" . $i . "_s3"}[1]) {

                                                                        ${"sem_m" . $i . "_p1_total"} = 1;
                                                                        ${"sem_m" . $i . "_p2_total"} = 2;

                                                                    } elseif (${"sem_m" . $i . "_s1"}[0] < ${"sem_m" . $i . "_s1"}[1] && ${"sem_m" . $i . "_s2"}[0] > ${"sem_m" . $i . "_s2"}[1] && ${"sem_m" . $i . "_s3"}[0] > ${"sem_m" . $i . "_s3"}[1]) {

                                                                        ${"sem_m" . $i . "_p1_total"} = 2;
                                                                        ${"sem_m" . $i . "_p2_total"} = 1;

                                                                    } elseif (${"sem_m" . $i . "_s1"}[0] > ${"sem_m" . $i . "_s1"}[1] && ${"sem_m" . $i . "_s2"}[0] < ${"sem_m" . $i . "_s2"}[1] && ${"sem_m" . $i . "_s3"}[0] < ${"sem_m" . $i . "_s3"}[1]) {

                                                                        ${"sem_m" . $i . "_p1_total"} = 1;
                                                                        ${"sem_m" . $i . "_p2_total"} = 2;

                                                                    }
                                                            ?>
                                                            

                                                            <ul class="matchup">

                                                                <span class="custooltip">
                                                                    <li
                                                                        class="team team-top @if (${"sem_m" . $i . "_p1_total"} > ${"sem_m" . $i . "_p2_total"}) winnerclractive @endif">
                                                                        {{ \Illuminate\Support\Str::limit(${"sem_mat_" . $i}[0], 100) }}

                                                                        <span
                                                                            class="score @if (${"sem_m" . $i . "_p1_total"} > ${"sem_m" . $i . "_p2_total"}) winnerclractive @endif">{{ ${"sem_m" . $i . "_s1"}[0] }}
                                                                            {{ ${"sem_m" . $i . "_s2"}[0] }} {{ ${"sem_m" . $i . "_s3"}[0] }}</span>

                                                                    </li>
                                                                    <span class="custooltiptext">{{ ${"sem_mat_" . $i}[0] }}</span>
                                                                </span>

                                                                <span class="custooltip">
                                                                    <li
                                                                        class="team team-bottom @if (${"sem_m" . $i . "_p2_total"} > ${"sem_m" . $i . "_p1_total"}) winnerclractive @endif">
                                                                        {{ \Illuminate\Support\Str::limit(${"sem_mat_" . $i}[1], 100) }}

                                                                        <span
                                                                            class="score @if (${"sem_m" . $i . "_p2_total"} > ${"sem_m" . $i . "_p1_total"}) winnerclractive @endif">{{ ${"sem_m" . $i . "_s1"}[1] }}
                                                                            {{ ${"sem_m" . $i . "_s2"}[1] }} {{ ${"sem_m" . $i . "_s3"}[1] }}</span>

                                                                    </li>
                                                                    <span class="custooltiptext">{{ ${"sem_mat_" . $i}[1] }}</span>
                                                                </span>

                                                            </ul>
                                                        @else
                                                            <ul class="matchup">

                                                                <span class="custooltip">
                                                                    <li class="team team-top">
                                                                        {{ \Illuminate\Support\Str::limit(${"sem_mat_" . $i}[0], 100) }}

                                                                        <span class="score">N/A</span>

                                                                    </li>
                                                                    <span class="custooltiptext">{{ ${"sem_mat_" . $i}[0] }}</span>
                                                                </span>

                                                                <span class="custooltip">
                                                                    <li class="team team-bottom">
                                                                        {{ \Illuminate\Support\Str::limit(${"sem_mat_" . $i}[1], 100) }}

                                                                        <span class="score">N/A</span>

                                                                    </li>
                                                                    <span class="custooltiptext">{{ ${"sem_mat_" . $i}[1] }}</span>
                                                                </span>

                                                            </ul>
                                                        @endif
                                                    @endif


                                                @else
                                                    <ul class="matchup">

                                                        <span class="custooltip">
                                                            <li class="team team-top">
                                                                {{ \Illuminate\Support\Str::limit(${"sem_mat_" . $i}[0], 100) }}

                                                                <span class="score">N/A</span>

                                                            </li>
                                                            <span class="custooltiptext">{{ ${"sem_mat_" . $i}[0] }}</span>
                                                        </span>

                                                        <span class="custooltip">
                                                            <li class="team team-bottom">
                                                                {{ \Illuminate\Support\Str::limit(${"sem_mat_" . $i}[1], 100) }}

                                                                <span class="score">N/A</span>

                                                            </li>
                                                            <span class="custooltiptext">{{ ${"sem_mat_" . $i}[1] }}</span>
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

                                @else
                                    <div class="round round-two">
                                        <div class="round-details">semifinal<br/><span class="date">@if($t_d_semf) {{ $strt_sem[0] }} - {{ $endd_sem[0] }} @else N/A @endif</span></div>

                                        @for($i = 1; $i < 2; $i++)
                                            <ul class="matchup">
                                                
                                                <li class="team team-top"><span class="score"></span></li>
                                                <li class="team team-bottom"><span class="score"></span></li>
                                            </ul>
                                        @endfor

                                    </div>
                                @endif
                            @endif
                                                        
                        </div> 

                        <div class="champion">
                            
                            {{-- @if($tournament->final_matches)
                                
                                <div class="semis-l current">
                                    <div class="round-details">final <br/><span class="date">{{ $strt_f[0] }} - {{ $endd_f[0] }}</span>
                                    </div>
                                    
                                    @if(array_key_exists('match_1', $final_matches))                                        
                                        @if($tournament->final_results)
                                            @if(array_key_exists('match_1', $final_results))             
                                                <?php 
                                                    $final_m1_p1_total = $final_m1_s1[0] + $final_m1_s2[0] + $final_m1_s3[0];
                                                    $final_m1_p2_total = $final_m1_s1[1] + $final_m1_s2[1] + $final_m1_s3[1];
                                                ?>

                                                <ul class="matchup championship">
                                                    
                                                    <span class="custooltip">
                                                        <li style="text-align: left;" class="team team-top @if($final_m1_p1_total > $final_m1_p2_total) winnerclractive @endif">

                                                            {{ \Illuminate\Support\Str::limit($final_mat_1[0], 17) }}

                                                            <span class="score @if($final_m1_p1_total > $final_m1_p2_total) winnerclractive @endif">{{ $final_m1_s1[0] }} {{ $final_m1_s2[0] }} {{ $final_m1_s3[0] }}</span>
                                                        </li>
                                                        <span class="custooltiptext">{{ $final_mat_1[0] }}</span>
                                                    </span>
                                                    

                                                    <span class="custooltip">
                                                        <li style="text-align: left;" class="team team-bottom @if($final_m1_p2_total > $final_m1_p1_total) winnerclractive @endif"> 
                                                            
                                                            {{ \Illuminate\Support\Str::limit($final_mat_1[1], 17) }}
                                                            
                                                            <span class="score @if($final_m1_p2_total > $final_m1_p1_total) winnerclractive @endif">{{ $final_m1_s1[1] }} {{ $final_m1_s2[1] }} {{ $final_m1_s3[1] }}</span>

                                                        </li>
                                                        <span class="custooltiptext">{{ $final_mat_1[1] }}</span>
                                                    </span>

                                                </ul>
                                            @else
                                                <ul class="matchup championship">
                                                    
                                                    <li style="text-align: left;" class="team team-top">{{ $final_mat_1[0] }}<span class="score">N/A</span></li>
                                                    <li style="text-align: left;" class="team team-bottom">{{ $final_mat_1[1] }}<span class="score">N/A</span></li>
                                                </ul>
                                            @endif

                                        @else
                                            <ul class="matchup championship">
                                                
                                                <li style="text-align: left;" class="team team-top">{{ $final_mat_1[0] }}<span class="score">N/A</span></li>
                                                <li style="text-align: left;" class="team team-bottom">{{ $final_mat_1[1] }}<span class="score">N/A</span></li>
                                            </ul>
                                        @endif

                                    @else

                                        <ul class="matchup championship">
                                            
                                            <li style="text-align: left;" class="team team-top">n/a<span class="score">N/A</span></li>
                                            <li style="text-align: left;" class="team team-bottom">n/a<span class="score">N/A</span></li>
                                        </ul>                          
                                        
                                    @endif

                                </div>

                            @else
                                <div class="semis-l">
                                    <div class="round-details">final<br/><span class="date">@if($t_d_final) {{ $strt_f[0] }} - {{ $endd_f[0] }} @else N/A @endif</span></div>
                                    <ul class="matchup championship">
                                        
                                        <li style="text-align: left;" class="team team-top"><span class="score"></span></li>
                                        <li style="text-align: left;" class="team team-bottom"><span class="score"></span></li>
                                    </ul>
                                                             
                                </div>
                            @endif --}}


                            @if($tournament->final_matches)
                                
                                <div class="semis-l current">
                                    <div class="round-details">final<br/><span class="date">@if($t_d_final) {{ $strt_f[0] }} - {{ $endd_f[0] }} @else N/A @endif</span>
                                    </div>
                                    
                                    @for($i = 1; $i < 2; $i++)
                                        @if (array_key_exists('match_'.$i, $final_matches))

                                            <?php 
                                                $get_matches = $final_matches['match_'.$i];
                                                $vs_match = explode(" VS ", $get_matches);
                                            ?>

                                            @if ($tournament->final_results)
                                                
                                                @if($final_status)
                                                    @if (array_key_exists('match_'.$i, $final_status))
                                                        @if($final_status['match_'.$i])
                                                            <?php
                                                                if (${"final_m" . $i . "_s1"}[0] > ${"final_m" . $i . "_s1"}[1] && ${"final_m" . $i . "_s2"}[0] > ${"final_m" . $i . "_s2"}[1]) 
                                                                {

                                                                    ${"final_m" . $i . "_p1_total"} = 2;
                                                                    ${"final_m" . $i . "_p2_total"} = 0;

                                                                } elseif (${"final_m" . $i . "_s1"}[1] > ${"final_m" . $i . "_s1"}[0] && ${"final_m" . $i . "_s2"}[1] > ${"final_m" . $i . "_s2"}[0]) {

                                                                    ${"final_m" . $i . "_p1_total"} = 0;
                                                                    ${"final_m" . $i . "_p2_total"} = 2;

                                                                } elseif (${"final_m" . $i . "_s1"}[0] > ${"final_m" . $i . "_s1"}[1] && ${"final_m" . $i . "_s2"}[0] < ${"final_m" . $i . "_s2"}[1] && ${"final_m" . $i . "_s3"}[0] > ${"final_m" . $i . "_s3"}[1]) {

                                                                    ${"final_m" . $i . "_p1_total"} = 2;
                                                                    ${"final_m" . $i . "_p2_total"} = 1;

                                                                } elseif (${"final_m" . $i . "_s1"}[0] < ${"final_m" . $i . "_s1"}[1] && ${"final_m" . $i . "_s2"}[0] > ${"final_m" . $i . "_s2"}[1] && ${"final_m" . $i . "_s3"}[0] < ${"final_m" . $i . "_s3"}[1]) {

                                                                    ${"final_m" . $i . "_p1_total"} = 1;
                                                                    ${"final_m" . $i . "_p2_total"} = 2;

                                                                } elseif (${"final_m" . $i . "_s1"}[0] < ${"final_m" . $i . "_s1"}[1] && ${"final_m" . $i . "_s2"}[0] > ${"final_m" . $i . "_s2"}[1] && ${"final_m" . $i . "_s3"}[0] > ${"final_m" . $i . "_s3"}[1]) {

                                                                    ${"final_m" . $i . "_p1_total"} = 2;
                                                                    ${"final_m" . $i . "_p2_total"} = 1;

                                                                } elseif (${"final_m" . $i . "_s1"}[0] > ${"final_m" . $i . "_s1"}[1] && ${"final_m" . $i . "_s2"}[0] < ${"final_m" . $i . "_s2"}[1] && ${"final_m" . $i . "_s3"}[0] < ${"final_m" . $i . "_s3"}[1]) {

                                                                    ${"final_m" . $i . "_p1_total"} = 1;
                                                                    ${"final_m" . $i . "_p2_total"} = 2;

                                                                }
                                                            ?>


                                                            <ul class="matchup">

                                                                <span class="custooltip">
                                                                    <li style="text-align: left;" 
                                                                        class="team team-top @if (${"final_m" . $i . "_p1_total"} > ${"final_m" . $i . "_p2_total"}) winnerclractive @endif">
                                                                        {{ \Illuminate\Support\Str::limit(${"final_mat_" . $i}[0], 100) }}

                                                                        @if (${"final_m" . $i . "_p1_total"} < ${"final_m" . $i . "_p2_total"})
                                                                            @if($final_status['match_'.$i] == 'Retired')
                                                                                @if($final_retires)
                                                                                    @if (array_key_exists('match_'.$i, $final_retires))
                                                                                        @if($final_retires['match_'.$i] == $vs_match[0])
                                                                                            
                                                                                            <span class="text-danger text-center" style="font-size: 8px;font-weight: 500;">(R)</span>

                                                                                        @endif
                                                                                    @endif
                                                                                @endif
                                                                                <span
                                                                                    class="score">{{ ${"final_m" . $i . "_s1"}[0] }}
                                                                                    {{ ${"final_m" . $i . "_s2"}[0] }} {{ ${"final_m" . $i . "_s3"}[0] }}</span>
                                                                            @elseif($final_status['match_'.$i] == 'Withdraw')
                                                                                <span class="text-danger text-center" style="font-size: 8px;font-weight: 500;">(W)</span>
                                                                                <span
                                                                                    class="score">&#8212;</span>
                                                                            @elseif($final_status['match_'.$i] == 'Decided by Organisers')
                                                                                <span class="text-danger text-center" style="font-size: 8px;font-weight: 500;">(D)</span>
                                                                                <span
                                                                                    class="score">&#8212;</span>
                                                                            @endif

                                                                        @else
                                                                            @if($final_status['match_'.$i] == 'Retired')
                                                                                @if($final_retires)
                                                                                    @if (array_key_exists('match_'.$i, $final_retires))
                                                                                        @if($final_retires['match_'.$i] == $vs_match[0])
                                                                                            
                                                                                            <span class="text-danger text-center" style="font-size: 8px;font-weight: 500;">(R)</span>

                                                                                        @endif
                                                                                    @endif
                                                                                @endif
                                                                                <span
                                                                                class="score winnerclractive">{{ ${"final_m" . $i . "_s1"}[0] }}
                                                                                {{ ${"final_m" . $i . "_s2"}[0] }} {{ ${"final_m" . $i . "_s3"}[0] }}</span>
                                                                            @elseif($final_status['match_'.$i] == 'Withdraw')
                                                                                <span
                                                                                class="score winnerclractive">&#8212;</span>
                                                                            @elseif($final_status['match_'.$i] == 'Decided by Organisers')
                                                                                <span
                                                                                class="score winnerclractive">&#8212;</span>
                                                                            @endif
                                                                                
                                                                        @endif

                                                                    </li>

                                                                    <span class="custooltiptext">{{ ${"final_mat_" . $i}[0] }} 

                                                                        @if($final_status)
                                                                            @if($final_status['match_'.$i] == 'Retired')
                                                                                @if($final_retires)
                                                                                    @if (array_key_exists('match_'.$i, $final_retires))
                                                                                        @if($final_retires['match_'.$i] == $vs_match[0])
                                                                                            
                                                                                            ({{ $final_status['match_'.$i] }})

                                                                                        @endif
                                                                                    @endif
                                                                                @endif
                                                                            @else
                                                                                @if (${"final_m" . $i . "_p1_total"} < ${"final_m" . $i . "_p2_total"}) 
                                                                                    ({{ $final_status['match_'.$i] }}) 
                                                                                @endif
                                                                            @endif
                                                                        @endif

                                                                    </span>

                                                                </span>

                                                                <span class="custooltip">
                                                                    <li style="text-align: left;" 
                                                                        class="team team-bottom @if (${"final_m" . $i . "_p2_total"} > ${"final_m" . $i . "_p1_total"}) winnerclractive @endif">
                                                                        {{ \Illuminate\Support\Str::limit(${"final_mat_" . $i}[1], 100) }}

                                                                        @if (${"final_m" . $i . "_p1_total"} > ${"final_m" . $i . "_p2_total"})
                                                                            @if($final_status['match_'.$i] == 'Retired')
                                                                                @if($final_retires)
                                                                                    @if (array_key_exists('match_'.$i, $final_retires))
                                                                                        @if($final_retires['match_'.$i] == $vs_match[1])
                                                                                            
                                                                                            <span class="text-danger text-center" style="font-size: 8px;font-weight: 500;">(R)</span>

                                                                                        @endif
                                                                                    @endif
                                                                                @endif
                                                                                <span
                                                                                    class="score">{{ ${"final_m" . $i . "_s1"}[1] }}
                                                                                    {{ ${"final_m" . $i . "_s2"}[1] }} {{ ${"final_m" . $i . "_s3"}[1] }}</span>
                                                                            @elseif($final_status['match_'.$i] == 'Withdraw')
                                                                                <span class="text-danger text-center" style="font-size: 8px;font-weight: 500;">(W)</span>
                                                                                <span
                                                                                    class="score">&#8212;</span>
                                                                            @elseif($final_status['match_'.$i] == 'Decided by Organisers')
                                                                                <span class="text-danger text-center" style="font-size: 8px;font-weight: 500;">(D)</span>
                                                                                <span
                                                                                    class="score">&#8212;</span>
                                                                            @endif

                                                                        @else
                                                                            @if($final_status['match_'.$i] == 'Retired')
                                                                                @if($final_retires)
                                                                                    @if (array_key_exists('match_'.$i, $final_retires))
                                                                                        @if($final_retires['match_'.$i] == $vs_match[1])
                                                                                            
                                                                                            <span class="text-danger text-center" style="font-size: 8px;font-weight: 500;">(R)</span>

                                                                                        @endif
                                                                                    @endif
                                                                                @endif
                                                                                <span
                                                                                class="score winnerclractive">{{ ${"final_m" . $i . "_s1"}[1] }}
                                                                                {{ ${"final_m" . $i . "_s2"}[1] }} {{ ${"final_m" . $i . "_s3"}[1] }}</span>
                                                                            @elseif($final_status['match_'.$i] == 'Withdraw')
                                                                                <span
                                                                                class="score winnerclractive">&#8212;</span>
                                                                            @elseif($final_status['match_'.$i] == 'Decided by Organisers')
                                                                                <span
                                                                                class="score winnerclractive">&#8212;</span>
                                                                            @endif
                                                                        @endif

                                                                    </li>

                                                                    <span class="custooltiptext">{{ ${"final_mat_" . $i}[1] }} 

                                                                        @if($final_status)
                                                                            @if($final_status['match_'.$i] == 'Retired')
                                                                                @if($final_retires)
                                                                                    @if (array_key_exists('match_'.$i, $final_retires))
                                                                                        @if($final_retires['match_'.$i] == $vs_match[1])
                                                                                            
                                                                                            ({{ $final_status['match_'.$i] }})

                                                                                        @endif
                                                                                    @endif
                                                                                @endif
                                                                            @else
                                                                                @if (${"final_m" . $i . "_p1_total"} > ${"final_m" . $i . "_p2_total"}) 
                                                                                    ({{ $final_status['match_'.$i] }}) 
                                                                                @endif
                                                                            @endif
                                                                        @endif

                                                                    </span>

                                                                </span>

                                                            </ul>

                                                        @else
                                                            @if (array_key_exists('match_'.$i, $final_results))
                                                                <?php
                                                                    if (${"final_m" . $i . "_s1"}[0] > ${"final_m" . $i . "_s1"}[1] && ${"final_m" . $i . "_s2"}[0] > ${"final_m" . $i . "_s2"}[1]) 
                                                                    {

                                                                        ${"final_m" . $i . "_p1_total"} = 2;
                                                                        ${"final_m" . $i . "_p2_total"} = 0;

                                                                    } elseif (${"final_m" . $i . "_s1"}[1] > ${"final_m" . $i . "_s1"}[0] && ${"final_m" . $i . "_s2"}[1] > ${"final_m" . $i . "_s2"}[0]) {

                                                                        ${"final_m" . $i . "_p1_total"} = 0;
                                                                        ${"final_m" . $i . "_p2_total"} = 2;

                                                                    } elseif (${"final_m" . $i . "_s1"}[0] > ${"final_m" . $i . "_s1"}[1] && ${"final_m" . $i . "_s2"}[0] < ${"final_m" . $i . "_s2"}[1] && ${"final_m" . $i . "_s3"}[0] > ${"final_m" . $i . "_s3"}[1]) {

                                                                        ${"final_m" . $i . "_p1_total"} = 2;
                                                                        ${"final_m" . $i . "_p2_total"} = 1;

                                                                    } elseif (${"final_m" . $i . "_s1"}[0] < ${"final_m" . $i . "_s1"}[1] && ${"final_m" . $i . "_s2"}[0] > ${"final_m" . $i . "_s2"}[1] && ${"final_m" . $i . "_s3"}[0] < ${"final_m" . $i . "_s3"}[1]) {

                                                                        ${"final_m" . $i . "_p1_total"} = 1;
                                                                        ${"final_m" . $i . "_p2_total"} = 2;

                                                                    } elseif (${"final_m" . $i . "_s1"}[0] < ${"final_m" . $i . "_s1"}[1] && ${"final_m" . $i . "_s2"}[0] > ${"final_m" . $i . "_s2"}[1] && ${"final_m" . $i . "_s3"}[0] > ${"final_m" . $i . "_s3"}[1]) {

                                                                        ${"final_m" . $i . "_p1_total"} = 2;
                                                                        ${"final_m" . $i . "_p2_total"} = 1;

                                                                    } elseif (${"final_m" . $i . "_s1"}[0] > ${"final_m" . $i . "_s1"}[1] && ${"final_m" . $i . "_s2"}[0] < ${"final_m" . $i . "_s2"}[1] && ${"final_m" . $i . "_s3"}[0] < ${"final_m" . $i . "_s3"}[1]) {

                                                                        ${"final_m" . $i . "_p1_total"} = 1;
                                                                        ${"final_m" . $i . "_p2_total"} = 2;

                                                                    }
                                                                ?>
                                                                

                                                                <ul class="matchup">

                                                                    <span class="custooltip">
                                                                        <li style="text-align: left;" 
                                                                            class="team team-top @if (${"final_m" . $i . "_p1_total"} > ${"final_m" . $i . "_p2_total"}) winnerclractive @endif">
                                                                            {{ \Illuminate\Support\Str::limit(${"final_mat_" . $i}[0], 100) }}

                                                                            <span
                                                                                class="score @if (${"final_m" . $i . "_p1_total"} > ${"final_m" . $i . "_p2_total"}) winnerclractive @endif">{{ ${"final_m" . $i . "_s1"}[0] }}
                                                                                {{ ${"final_m" . $i . "_s2"}[0] }} {{ ${"final_m" . $i . "_s3"}[0] }}</span>

                                                                        </li>
                                                                        <span class="custooltiptext">{{ ${"final_mat_" . $i}[0] }}</span>
                                                                    </span>

                                                                    <span class="custooltip">
                                                                        <li style="text-align: left;" 
                                                                            class="team team-bottom @if (${"final_m" . $i . "_p2_total"} > ${"final_m" . $i . "_p1_total"}) winnerclractive @endif">
                                                                            {{ \Illuminate\Support\Str::limit(${"final_mat_" . $i}[1], 100) }}

                                                                            <span
                                                                                class="score @if (${"final_m" . $i . "_p2_total"} > ${"final_m" . $i . "_p1_total"}) winnerclractive @endif">{{ ${"final_m" . $i . "_s1"}[1] }}
                                                                                {{ ${"final_m" . $i . "_s2"}[1] }} {{ ${"final_m" . $i . "_s3"}[1] }}</span>

                                                                        </li>
                                                                        <span class="custooltiptext">{{ ${"final_mat_" . $i}[1] }}</span>
                                                                    </span>

                                                                </ul>
                                                            @else
                                                                <ul class="matchup">

                                                                    <span class="custooltip">
                                                                        <li style="text-align: left;"  class="team team-top">
                                                                            {{ \Illuminate\Support\Str::limit(${"final_mat_" . $i}[0], 100) }}

                                                                            <span class="score">N/A</span>

                                                                        </li>
                                                                        <span class="custooltiptext">{{ ${"final_mat_" . $i}[0] }}</span>
                                                                    </span>

                                                                    <span class="custooltip">
                                                                        <li style="text-align: left;"  class="team team-bottom">
                                                                            {{ \Illuminate\Support\Str::limit(${"final_mat_" . $i}[1], 100) }}

                                                                            <span class="score">N/A</span>

                                                                        </li>
                                                                        <span class="custooltiptext">{{ ${"final_mat_" . $i}[1] }}</span>
                                                                    </span>

                                                                </ul>
                                                            @endif
                                                        @endif


                                                    @else
                                                        @if (array_key_exists('match_'.$i, $final_results))
                                                            <?php
                                                                if (${"final_m" . $i . "_s1"}[0] > ${"final_m" . $i . "_s1"}[1] && ${"final_m" . $i . "_s2"}[0] > ${"final_m" . $i . "_s2"}[1]) 
                                                                {

                                                                    ${"final_m" . $i . "_p1_total"} = 2;
                                                                    ${"final_m" . $i . "_p2_total"} = 0;

                                                                } elseif (${"final_m" . $i . "_s1"}[1] > ${"final_m" . $i . "_s1"}[0] && ${"final_m" . $i . "_s2"}[1] > ${"final_m" . $i . "_s2"}[0]) {

                                                                    ${"final_m" . $i . "_p1_total"} = 0;
                                                                    ${"final_m" . $i . "_p2_total"} = 2;

                                                                } elseif (${"final_m" . $i . "_s1"}[0] > ${"final_m" . $i . "_s1"}[1] && ${"final_m" . $i . "_s2"}[0] < ${"final_m" . $i . "_s2"}[1] && ${"final_m" . $i . "_s3"}[0] > ${"final_m" . $i . "_s3"}[1]) {

                                                                    ${"final_m" . $i . "_p1_total"} = 2;
                                                                    ${"final_m" . $i . "_p2_total"} = 1;

                                                                } elseif (${"final_m" . $i . "_s1"}[0] < ${"final_m" . $i . "_s1"}[1] && ${"final_m" . $i . "_s2"}[0] > ${"final_m" . $i . "_s2"}[1] && ${"final_m" . $i . "_s3"}[0] < ${"final_m" . $i . "_s3"}[1]) {

                                                                    ${"final_m" . $i . "_p1_total"} = 1;
                                                                    ${"final_m" . $i . "_p2_total"} = 2;

                                                                } elseif (${"final_m" . $i . "_s1"}[0] < ${"final_m" . $i . "_s1"}[1] && ${"final_m" . $i . "_s2"}[0] > ${"final_m" . $i . "_s2"}[1] && ${"final_m" . $i . "_s3"}[0] > ${"final_m" . $i . "_s3"}[1]) {

                                                                    ${"final_m" . $i . "_p1_total"} = 2;
                                                                    ${"final_m" . $i . "_p2_total"} = 1;

                                                                } elseif (${"final_m" . $i . "_s1"}[0] > ${"final_m" . $i . "_s1"}[1] && ${"final_m" . $i . "_s2"}[0] < ${"final_m" . $i . "_s2"}[1] && ${"final_m" . $i . "_s3"}[0] < ${"final_m" . $i . "_s3"}[1]) {

                                                                    ${"final_m" . $i . "_p1_total"} = 1;
                                                                    ${"final_m" . $i . "_p2_total"} = 2;

                                                                }
                                                            ?>
                                                            

                                                            <ul class="matchup">

                                                                <span class="custooltip">
                                                                    <li style="text-align: left;" 
                                                                        class="team team-top @if (${"final_m" . $i . "_p1_total"} > ${"final_m" . $i . "_p2_total"}) winnerclractive @endif">
                                                                        {{ \Illuminate\Support\Str::limit(${"final_mat_" . $i}[0], 100) }}

                                                                        <span
                                                                            class="score @if (${"final_m" . $i . "_p1_total"} > ${"final_m" . $i . "_p2_total"}) winnerclractive @endif">{{ ${"final_m" . $i . "_s1"}[0] }}
                                                                            {{ ${"final_m" . $i . "_s2"}[0] }} {{ ${"final_m" . $i . "_s3"}[0] }}</span>

                                                                    </li>
                                                                    <span class="custooltiptext">{{ ${"final_mat_" . $i}[0] }}</span>
                                                                </span>

                                                                <span class="custooltip">
                                                                    <li style="text-align: left;" 
                                                                        class="team team-bottom @if (${"final_m" . $i . "_p2_total"} > ${"final_m" . $i . "_p1_total"}) winnerclractive @endif">
                                                                        {{ \Illuminate\Support\Str::limit(${"final_mat_" . $i}[1], 100) }}

                                                                        <span
                                                                            class="score @if (${"final_m" . $i . "_p2_total"} > ${"final_m" . $i . "_p1_total"}) winnerclractive @endif">{{ ${"final_m" . $i . "_s1"}[1] }}
                                                                            {{ ${"final_m" . $i . "_s2"}[1] }} {{ ${"final_m" . $i . "_s3"}[1] }}</span>

                                                                    </li>
                                                                    <span class="custooltiptext">{{ ${"final_mat_" . $i}[1] }}</span>
                                                                </span>

                                                            </ul>
                                                        @else
                                                            <ul class="matchup">

                                                                <span class="custooltip">
                                                                    <li style="text-align: left;"  class="team team-top">
                                                                        {{ \Illuminate\Support\Str::limit(${"final_mat_" . $i}[0], 100) }}

                                                                        <span class="score">N/A</span>

                                                                    </li>
                                                                    <span class="custooltiptext">{{ ${"final_mat_" . $i}[0] }}</span>
                                                                </span>

                                                                <span class="custooltip">
                                                                    <li style="text-align: left;"  class="team team-bottom">
                                                                        {{ \Illuminate\Support\Str::limit(${"final_mat_" . $i}[1], 100) }}

                                                                        <span class="score">N/A</span>

                                                                    </li>
                                                                    <span class="custooltiptext">{{ ${"final_mat_" . $i}[1] }}</span>
                                                                </span>

                                                            </ul>
                                                        @endif
                                                    @endif

                                                @else
                                                    @if (array_key_exists('match_'.$i, $final_results))
                                                        <?php
                                                            if (${"final_m" . $i . "_s1"}[0] > ${"final_m" . $i . "_s1"}[1] && ${"final_m" . $i . "_s2"}[0] > ${"final_m" . $i . "_s2"}[1]) 
                                                            {

                                                                ${"final_m" . $i . "_p1_total"} = 2;
                                                                ${"final_m" . $i . "_p2_total"} = 0;

                                                            } elseif (${"final_m" . $i . "_s1"}[1] > ${"final_m" . $i . "_s1"}[0] && ${"final_m" . $i . "_s2"}[1] > ${"final_m" . $i . "_s2"}[0]) {

                                                                ${"final_m" . $i . "_p1_total"} = 0;
                                                                ${"final_m" . $i . "_p2_total"} = 2;

                                                            } elseif (${"final_m" . $i . "_s1"}[0] > ${"final_m" . $i . "_s1"}[1] && ${"final_m" . $i . "_s2"}[0] < ${"final_m" . $i . "_s2"}[1] && ${"final_m" . $i . "_s3"}[0] > ${"final_m" . $i . "_s3"}[1]) {

                                                                ${"final_m" . $i . "_p1_total"} = 2;
                                                                ${"final_m" . $i . "_p2_total"} = 1;

                                                            } elseif (${"final_m" . $i . "_s1"}[0] < ${"final_m" . $i . "_s1"}[1] && ${"final_m" . $i . "_s2"}[0] > ${"final_m" . $i . "_s2"}[1] && ${"final_m" . $i . "_s3"}[0] < ${"final_m" . $i . "_s3"}[1]) {

                                                                ${"final_m" . $i . "_p1_total"} = 1;
                                                                ${"final_m" . $i . "_p2_total"} = 2;

                                                            } elseif (${"final_m" . $i . "_s1"}[0] < ${"final_m" . $i . "_s1"}[1] && ${"final_m" . $i . "_s2"}[0] > ${"final_m" . $i . "_s2"}[1] && ${"final_m" . $i . "_s3"}[0] > ${"final_m" . $i . "_s3"}[1]) {

                                                                ${"final_m" . $i . "_p1_total"} = 2;
                                                                ${"final_m" . $i . "_p2_total"} = 1;

                                                            } elseif (${"final_m" . $i . "_s1"}[0] > ${"final_m" . $i . "_s1"}[1] && ${"final_m" . $i . "_s2"}[0] < ${"final_m" . $i . "_s2"}[1] && ${"final_m" . $i . "_s3"}[0] < ${"final_m" . $i . "_s3"}[1]) {

                                                                ${"final_m" . $i . "_p1_total"} = 1;
                                                                ${"final_m" . $i . "_p2_total"} = 2;

                                                            }
                                                        ?>
                                                        

                                                        <ul class="matchup">

                                                            <span class="custooltip">
                                                                <li style="text-align: left;" 
                                                                    class="team team-top @if (${"final_m" . $i . "_p1_total"} > ${"final_m" . $i . "_p2_total"}) winnerclractive @endif">
                                                                    {{ \Illuminate\Support\Str::limit(${"final_mat_" . $i}[0], 100) }}

                                                                    <span
                                                                        class="score @if (${"final_m" . $i . "_p1_total"} > ${"final_m" . $i . "_p2_total"}) winnerclractive @endif">{{ ${"final_m" . $i . "_s1"}[0] }}
                                                                        {{ ${"final_m" . $i . "_s2"}[0] }} {{ ${"final_m" . $i . "_s3"}[0] }}</span>

                                                                </li>
                                                                <span class="custooltiptext">{{ ${"final_mat_" . $i}[0] }}</span>
                                                            </span>

                                                            <span class="custooltip">
                                                                <li style="text-align: left;" 
                                                                    class="team team-bottom @if (${"final_m" . $i . "_p2_total"} > ${"final_m" . $i . "_p1_total"}) winnerclractive @endif">
                                                                    {{ \Illuminate\Support\Str::limit(${"final_mat_" . $i}[1], 100) }}

                                                                    <span
                                                                        class="score @if (${"final_m" . $i . "_p2_total"} > ${"final_m" . $i . "_p1_total"}) winnerclractive @endif">{{ ${"final_m" . $i . "_s1"}[1] }}
                                                                        {{ ${"final_m" . $i . "_s2"}[1] }} {{ ${"final_m" . $i . "_s3"}[1] }}</span>

                                                                </li>
                                                                <span class="custooltiptext">{{ ${"final_mat_" . $i}[1] }}</span>
                                                            </span>

                                                        </ul>
                                                    @else
                                                        <ul class="matchup">

                                                            <span class="custooltip">
                                                                <li style="text-align: left;"  class="team team-top">
                                                                    {{ \Illuminate\Support\Str::limit(${"final_mat_" . $i}[0], 100) }}

                                                                    <span class="score">N/A</span>

                                                                </li>
                                                                <span class="custooltiptext">{{ ${"final_mat_" . $i}[0] }}</span>
                                                            </span>

                                                            <span class="custooltip">
                                                                <li style="text-align: left;"  class="team team-bottom">
                                                                    {{ \Illuminate\Support\Str::limit(${"final_mat_" . $i}[1], 100) }}

                                                                    <span class="score">N/A</span>

                                                                </li>
                                                                <span class="custooltiptext">{{ ${"final_mat_" . $i}[1] }}</span>
                                                            </span>

                                                        </ul>
                                                    @endif
                                                @endif


                                            @else
                                                <ul class="matchup">

                                                    <span class="custooltip">
                                                        <li style="text-align: left;"  class="team team-top">
                                                            {{ \Illuminate\Support\Str::limit(${"final_mat_" . $i}[0], 100) }}

                                                            <span class="score">N/A</span>

                                                        </li>
                                                        <span class="custooltiptext">{{ ${"final_mat_" . $i}[0] }}</span>
                                                    </span>

                                                    <span class="custooltip">
                                                        <li style="text-align: left;"  class="team team-bottom">
                                                            {{ \Illuminate\Support\Str::limit(${"final_mat_" . $i}[1], 100) }}

                                                            <span class="score">N/A</span>

                                                        </li>
                                                        <span class="custooltiptext">{{ ${"final_mat_" . $i}[1] }}</span>
                                                    </span>
                                                </ul>
                                            @endif
                                        @else
                                            <ul class="matchup">

                                                <li style="text-align: left;"  class="team team-top">n/a<span class="score">N/A</span></li>
                                                <li style="text-align: left;"  class="team team-bottom">n/a<span class="score">N/A</span></li>
                                            </ul>
                                        @endif
                                    @endfor

                                </div>
                            @else
                                <div class="semis-l">
                                    <div class="round-details">final<br/><span class="date">@if($t_d_final) {{ $strt_f[0] }} - {{ $endd_f[0] }} @else N/A @endif</span></div>

                                    @for($i = 1; $i < 2; $i++)
                                        <ul class="matchup">
                                            
                                            <li style="text-align: left;"  class="team team-top"><span class="score"></span></li>
                                            <li style="text-align: left;"  class="team team-bottom"><span class="score"></span></li>
                                        </ul>
                                    @endfor

                                </div>
                            @endif


                            @if($tournament->final_results)
                                <div class="final current">
                                    <i class="fa fa-trophy"></i>
                                    <div class="round-details">final championship <br/><span class="date" style="font-weight:550;">Winner</span></div>      
                                    <ul class ="matchup championship">
                                        
                                        <span class="custooltip">
                                            <li style="text-align: center;" class="team team-top winnerclractive">

                                                @if($final_m1_p1_total > $final_m1_p2_total) {{ $final_mat_1[0] }} @else {{ $final_mat_1[1] }} @endif

                                                
                                            </li>
                                            <span class="custooltiptext">{{ $final_m1_p1_total > $final_m1_p2_total ? $final_mat_1[0] : $final_mat_1[1] }}</span>
                                        </span>
                                    </ul>
                                </div>
                            @else

                                <div class="final current">
                                    <i class="fa fa-trophy"></i>
                                    <div class="round-details">final championship <br/><span class="date" style="font-weight:550;">Winner</span></div>      
                                    <ul class ="matchup championship">
                                        <li class="team team-top">&nbsp;<span class="vote-count">&nbsp;</span></li>
                                    </ul>
                                </div>

                            @endif
                        </div>

                        {{-- <li class="team"style="width: 25%;margin: 0 auto;text-align: center;color: #2c7399 !important;font-weight: bold;font-family: 'Roboto Condensed', sans-serif;">M-2</li> --}}

                        <div class="split split-two">

                            @if($semi_final_auto_selection)

                                <div class="round round-two @if($semi_final_auto_selection) current @elseif($semi_final_matches) current @endif">
                                    <div class="round-details">semifinal<br/><span class="date">@if($t_d_semf) {{ $strt_sem[0] }} - {{ $endd_sem[0] }} @else N/A @endif</span></div>
                                    
                                    @for($i = 2; $i < 3; $i++)
                                        @if (array_key_exists('match_'.$i, $semi_final_auto_selection))
                                            
                                            <ul class="matchup">

                                                <span class="custooltip">
                                                    <li
                                                        class="team team-top winnerclractive">
                                                        {{ \Illuminate\Support\Str::limit($sem_mat_auto[0], 100) }}

                                                        <span
                                                            class="score winnerclractive">N/A</span>

                                                    </li>
                                                    <span class="custooltiptext">{{ $sem_mat_auto[0] }}</span>
                                                </span>

                                                <span class="custooltip">
                                                    <li
                                                        class="team team-bottom">
                                                        N/A

                                                        <span
                                                            class="score">N/A</span>

                                                    </li>
                                                    <span class="custooltiptext">N/A</span>
                                                </span>

                                            </ul>
                                            
                                        @else

                                            @if($tournament->semi_final_matches)
                                                                                                   
                                                @if (array_key_exists('match_'.$i, $semi_final_matches))

                                                    <?php 
                                                        $get_matches = $semi_final_matches['match_'.$i];
                                                        $vs_match = explode(" VS ", $get_matches);
                                                    ?>

                                                    @if ($tournament->semi_final_results)
                                                            
                                                        @if($semi_final_status)
                                                            @if (array_key_exists('match_'.$i, $semi_final_status))
                                                                @if($semi_final_status['match_'.$i])
                                                                    <?php
                                                                        if (${"sem_m" . $i . "_s1"}[0] > ${"sem_m" . $i . "_s1"}[1] && ${"sem_m" . $i . "_s2"}[0] > ${"sem_m" . $i . "_s2"}[1]) {

                                                                            ${"sem_m" . $i . "_p1_total"} = 2;
                                                                            ${"sem_m" . $i . "_p2_total"} = 0;

                                                                        } elseif (${"sem_m" . $i . "_s1"}[1] > ${"sem_m" . $i . "_s1"}[0] && ${"sem_m" . $i . "_s2"}[1] > ${"sem_m" . $i . "_s2"}[0]) {

                                                                            ${"sem_m" . $i . "_p1_total"} = 0;
                                                                            ${"sem_m" . $i . "_p2_total"} = 2;

                                                                        } elseif (${"sem_m" . $i . "_s1"}[0] > ${"sem_m" . $i . "_s1"}[1] && ${"sem_m" . $i . "_s2"}[0] < ${"sem_m" . $i . "_s2"}[1] && ${"sem_m" . $i . "_s3"}[0] > ${"sem_m" . $i . "_s3"}[1]) {

                                                                            ${"sem_m" . $i . "_p1_total"} = 2;
                                                                            ${"sem_m" . $i . "_p2_total"} = 1;

                                                                        } elseif (${"sem_m" . $i . "_s1"}[0] < ${"sem_m" . $i . "_s1"}[1] && ${"sem_m" . $i . "_s2"}[0] > ${"sem_m" . $i . "_s2"}[1] && ${"sem_m" . $i . "_s3"}[0] < ${"sem_m" . $i . "_s3"}[1]) {

                                                                            ${"sem_m" . $i . "_p1_total"} = 1;
                                                                            ${"sem_m" . $i . "_p2_total"} = 2;

                                                                        } elseif (${"sem_m" . $i . "_s1"}[0] < ${"sem_m" . $i . "_s1"}[1] && ${"sem_m" . $i . "_s2"}[0] > ${"sem_m" . $i . "_s2"}[1] && ${"sem_m" . $i . "_s3"}[0] > ${"sem_m" . $i . "_s3"}[1]) {

                                                                            ${"sem_m" . $i . "_p1_total"} = 2;
                                                                            ${"sem_m" . $i . "_p2_total"} = 1;

                                                                        } elseif (${"sem_m" . $i . "_s1"}[0] > ${"sem_m" . $i . "_s1"}[1] && ${"sem_m" . $i . "_s2"}[0] < ${"sem_m" . $i . "_s2"}[1] && ${"sem_m" . $i . "_s3"}[0] < ${"sem_m" . $i . "_s3"}[1]) {

                                                                            ${"sem_m" . $i . "_p1_total"} = 1;
                                                                            ${"sem_m" . $i . "_p2_total"} = 2;

                                                                        }
                                                                    ?>


                                                                    <ul class="matchup">

                                                                        <span class="custooltip">
                                                                            <li
                                                                                class="team team-top @if (${"sem_m" . $i . "_p1_total"} > ${"sem_m" . $i . "_p2_total"}) winnerclractive @endif">
                                                                                {{ \Illuminate\Support\Str::limit(${"sem_mat_" . $i}[0], 100) }}

                                                                                @if (${"sem_m" . $i . "_p1_total"} < ${"sem_m" . $i . "_p2_total"})
                                                                                    @if($semi_final_status['match_'.$i] == 'Retired')
                                                                                        @if($semi_final_retires)
                                                                                            @if (array_key_exists('match_'.$i, $semi_final_retires))
                                                                                                @if($semi_final_retires['match_'.$i] == $vs_match[0])
                                                                                                    
                                                                                                    <span class="text-danger text-center" style="font-size: 8px;font-weight: 500;">(R)</span>

                                                                                                @endif
                                                                                            @endif
                                                                                        @endif
                                                                                        <span
                                                                                            class="score">{{ ${"sem_m" . $i . "_s1"}[0] }}
                                                                                            {{ ${"sem_m" . $i . "_s2"}[0] }} {{ ${"sem_m" . $i . "_s3"}[0] }}</span>
                                                                                    @elseif($semi_final_status['match_'.$i] == 'Withdraw')
                                                                                        <span class="text-danger text-center" style="font-size: 8px;font-weight: 500;">(W)</span>
                                                                                        <span
                                                                                            class="score">&#8212;</span>
                                                                                    @elseif($semi_final_status['match_'.$i] == 'Decided by Organisers')
                                                                                        <span class="text-danger text-center" style="font-size: 8px;font-weight: 500;">(D)</span>
                                                                                        <span
                                                                                            class="score">&#8212;</span>
                                                                                    @endif

                                                                                @else
                                                                                    @if($semi_final_status['match_'.$i] == 'Retired')
                                                                                        @if($semi_final_retires)
                                                                                            @if (array_key_exists('match_'.$i, $semi_final_retires))
                                                                                                @if($semi_final_retires['match_'.$i] == $vs_match[0])
                                                                                                    
                                                                                                    <span class="text-danger text-center" style="font-size: 8px;font-weight: 500;">(R)</span>

                                                                                                @endif
                                                                                            @endif
                                                                                        @endif
                                                                                        <span
                                                                                        class="score winnerclractive">{{ ${"sem_m" . $i . "_s1"}[0] }}
                                                                                        {{ ${"sem_m" . $i . "_s2"}[0] }} {{ ${"sem_m" . $i . "_s3"}[0] }}</span>
                                                                                    @elseif($semi_final_status['match_'.$i] == 'Withdraw')
                                                                                        <span
                                                                                        class="score winnerclractive">&#8212;</span>
                                                                                    @elseif($semi_final_status['match_'.$i] == 'Decided by Organisers')
                                                                                        <span
                                                                                        class="score winnerclractive">&#8212;</span>
                                                                                    @endif
                                                                                        
                                                                                @endif

                                                                            </li>

                                                                            <span class="custooltiptext">{{ ${"sem_mat_" . $i}[0] }} 
                                                                                @if($semi_final_status)
                                                                                    @if($semi_final_status['match_'.$i] == 'Retired')
                                                                                        @if($semi_final_retires)
                                                                                            @if (array_key_exists('match_'.$i, $semi_final_retires))
                                                                                                @if($semi_final_retires['match_'.$i] == $vs_match[0])
                                                                                                    
                                                                                                    ({{ $semi_final_status['match_'.$i] }})

                                                                                                @endif
                                                                                            @endif
                                                                                        @endif
                                                                                    @else
                                                                                        @if (${"sem_m" . $i . "_p1_total"} < ${"sem_m" . $i . "_p2_total"}) 
                                                                                            ({{ $semi_final_status['match_'.$i] }}) 
                                                                                        @endif
                                                                                    @endif
                                                                                @endif
                                                                            </span>

                                                                        </span>

                                                                        <span class="custooltip">
                                                                            <li
                                                                                class="team team-bottom @if (${"sem_m" . $i . "_p2_total"} > ${"sem_m" . $i . "_p1_total"}) winnerclractive @endif">
                                                                                {{ \Illuminate\Support\Str::limit(${"sem_mat_" . $i}[1], 100) }}

                                                                                @if (${"sem_m" . $i . "_p1_total"} > ${"sem_m" . $i . "_p2_total"})
                                                                                    @if($semi_final_status['match_'.$i] == 'Retired')
                                                                                        @if($semi_final_retires)
                                                                                            @if (array_key_exists('match_'.$i, $semi_final_retires))
                                                                                                @if($semi_final_retires['match_'.$i] == $vs_match[1])
                                                                                                    
                                                                                                    <span class="text-danger text-center" style="font-size: 8px;font-weight: 500;">(R)</span>

                                                                                                @endif
                                                                                            @endif
                                                                                        @endif
                                                                                        <span
                                                                                            class="score">{{ ${"sem_m" . $i . "_s1"}[1] }}
                                                                                            {{ ${"sem_m" . $i . "_s2"}[1] }} {{ ${"sem_m" . $i . "_s3"}[1] }}</span>
                                                                                    @elseif($semi_final_status['match_'.$i] == 'Withdraw')
                                                                                        <span class="text-danger text-center" style="font-size: 8px;font-weight: 500;">(W)</span>
                                                                                        <span
                                                                                            class="score">&#8212;</span>
                                                                                    @elseif($semi_final_status['match_'.$i] == 'Decided by Organisers')
                                                                                        <span class="text-danger text-center" style="font-size: 8px;font-weight: 500;">(D)</span>
                                                                                        <span
                                                                                            class="score">&#8212;</span>
                                                                                    @endif

                                                                                @else
                                                                                    @if($semi_final_status['match_'.$i] == 'Retired')
                                                                                        @if($semi_final_retires)
                                                                                            @if (array_key_exists('match_'.$i, $semi_final_retires))
                                                                                                @if($semi_final_retires['match_'.$i] == $vs_match[1])
                                                                                                    
                                                                                                    <span class="text-danger text-center" style="font-size: 8px;font-weight: 500;">(R)</span>

                                                                                                @endif
                                                                                            @endif
                                                                                        @endif
                                                                                        <span
                                                                                        class="score winnerclractive">{{ ${"sem_m" . $i . "_s1"}[1] }}
                                                                                        {{ ${"sem_m" . $i . "_s2"}[1] }} {{ ${"sem_m" . $i . "_s3"}[1] }}</span>
                                                                                    @elseif($semi_final_status['match_'.$i] == 'Withdraw')
                                                                                        <span
                                                                                        class="score winnerclractive">&#8212;</span>
                                                                                    @elseif($semi_final_status['match_'.$i] == 'Decided by Organisers')
                                                                                        <span
                                                                                        class="score winnerclractive">&#8212;</span>
                                                                                    @endif
                                                                                @endif

                                                                            </li>

                                                                            <span class="custooltiptext">{{ ${"sem_mat_" . $i}[1] }} 

                                                                                @if($semi_final_status)
                                                                                    @if($semi_final_status['match_'.$i] == 'Retired')
                                                                                        @if($semi_final_retires)
                                                                                            @if (array_key_exists('match_'.$i, $semi_final_retires))
                                                                                                @if($semi_final_retires['match_'.$i] == $vs_match[1])
                                                                                                    
                                                                                                    ({{ $semi_final_status['match_'.$i] }})

                                                                                                @endif
                                                                                            @endif
                                                                                        @endif
                                                                                    @else
                                                                                        @if (${"sem_m" . $i . "_p1_total"} > ${"sem_m" . $i . "_p2_total"}) 
                                                                                            ({{ $semi_final_status['match_'.$i] }}) 
                                                                                        @endif
                                                                                    @endif
                                                                                @endif

                                                                            </span>

                                                                        </span>

                                                                    </ul>

                                                                @else
                                                                    @if (array_key_exists('match_'.$i, $semi_final_results))
                                                                        <?php
                                                                            if (${"sem_m" . $i . "_s1"}[0] > ${"sem_m" . $i . "_s1"}[1] && ${"sem_m" . $i . "_s2"}[0] > ${"sem_m" . $i . "_s2"}[1]) {

                                                                                ${"sem_m" . $i . "_p1_total"} = 2;
                                                                                ${"sem_m" . $i . "_p2_total"} = 0;

                                                                            } elseif (${"sem_m" . $i . "_s1"}[1] > ${"sem_m" . $i . "_s1"}[0] && ${"sem_m" . $i . "_s2"}[1] > ${"sem_m" . $i . "_s2"}[0]) {

                                                                                ${"sem_m" . $i . "_p1_total"} = 0;
                                                                                ${"sem_m" . $i . "_p2_total"} = 2;

                                                                            } elseif (${"sem_m" . $i . "_s1"}[0] > ${"sem_m" . $i . "_s1"}[1] && ${"sem_m" . $i . "_s2"}[0] < ${"sem_m" . $i . "_s2"}[1] && ${"sem_m" . $i . "_s3"}[0] > ${"sem_m" . $i . "_s3"}[1]) {

                                                                                ${"sem_m" . $i . "_p1_total"} = 2;
                                                                                ${"sem_m" . $i . "_p2_total"} = 1;

                                                                            } elseif (${"sem_m" . $i . "_s1"}[0] < ${"sem_m" . $i . "_s1"}[1] && ${"sem_m" . $i . "_s2"}[0] > ${"sem_m" . $i . "_s2"}[1] && ${"sem_m" . $i . "_s3"}[0] < ${"sem_m" . $i . "_s3"}[1]) {

                                                                                ${"sem_m" . $i . "_p1_total"} = 1;
                                                                                ${"sem_m" . $i . "_p2_total"} = 2;

                                                                            } elseif (${"sem_m" . $i . "_s1"}[0] < ${"sem_m" . $i . "_s1"}[1] && ${"sem_m" . $i . "_s2"}[0] > ${"sem_m" . $i . "_s2"}[1] && ${"sem_m" . $i . "_s3"}[0] > ${"sem_m" . $i . "_s3"}[1]) {

                                                                                ${"sem_m" . $i . "_p1_total"} = 2;
                                                                                ${"sem_m" . $i . "_p2_total"} = 1;

                                                                            } elseif (${"sem_m" . $i . "_s1"}[0] > ${"sem_m" . $i . "_s1"}[1] && ${"sem_m" . $i . "_s2"}[0] < ${"sem_m" . $i . "_s2"}[1] && ${"sem_m" . $i . "_s3"}[0] < ${"sem_m" . $i . "_s3"}[1]) {

                                                                                ${"sem_m" . $i . "_p1_total"} = 1;
                                                                                ${"sem_m" . $i . "_p2_total"} = 2;

                                                                            }
                                                                        ?>
                                                                        

                                                                        <ul class="matchup">

                                                                            <span class="custooltip">
                                                                                <li
                                                                                    class="team team-top @if (${"sem_m" . $i . "_p1_total"} > ${"sem_m" . $i . "_p2_total"}) winnerclractive @endif">
                                                                                    {{ \Illuminate\Support\Str::limit(${"sem_mat_" . $i}[0], 100) }}

                                                                                    <span
                                                                                        class="score @if (${"sem_m" . $i . "_p1_total"} > ${"sem_m" . $i . "_p2_total"}) winnerclractive @endif">{{ ${"sem_m" . $i . "_s1"}[0] }}
                                                                                        {{ ${"sem_m" . $i . "_s2"}[0] }} {{ ${"sem_m" . $i . "_s3"}[0] }}</span>

                                                                                </li>
                                                                                <span class="custooltiptext">{{ ${"sem_mat_" . $i}[0] }}</span>
                                                                            </span>

                                                                            <span class="custooltip">
                                                                                <li
                                                                                    class="team team-bottom @if (${"sem_m" . $i . "_p2_total"} > ${"sem_m" . $i . "_p1_total"}) winnerclractive @endif">
                                                                                    {{ \Illuminate\Support\Str::limit(${"sem_mat_" . $i}[1], 100) }}

                                                                                    <span
                                                                                        class="score @if (${"sem_m" . $i . "_p2_total"} > ${"sem_m" . $i . "_p1_total"}) winnerclractive @endif">{{ ${"sem_m" . $i . "_s1"}[1] }}
                                                                                        {{ ${"sem_m" . $i . "_s2"}[1] }} {{ ${"sem_m" . $i . "_s3"}[1] }}</span>

                                                                                </li>
                                                                                <span class="custooltiptext">{{ ${"sem_mat_" . $i}[1] }}</span>
                                                                            </span>

                                                                        </ul>
                                                                    @else
                                                                        <ul class="matchup">

                                                                            <span class="custooltip">
                                                                                <li class="team team-top">
                                                                                    {{ \Illuminate\Support\Str::limit(${"sem_mat_" . $i}[0], 100) }}

                                                                                    <span class="score">N/A</span>

                                                                                </li>
                                                                                <span class="custooltiptext">{{ ${"sem_mat_" . $i}[0] }}</span>
                                                                            </span>

                                                                            <span class="custooltip">
                                                                                <li class="team team-bottom">
                                                                                    {{ \Illuminate\Support\Str::limit(${"sem_mat_" . $i}[1], 100) }}

                                                                                    <span class="score">N/A</span>

                                                                                </li>
                                                                                <span class="custooltiptext">{{ ${"sem_mat_" . $i}[1] }}</span>
                                                                            </span>

                                                                        </ul>
                                                                    @endif
                                                                @endif


                                                            @else
                                                                @if (array_key_exists('match_'.$i, $semi_final_results))
                                                                    <?php
                                                                        if (${"sem_m" . $i . "_s1"}[0] > ${"sem_m" . $i . "_s1"}[1] && ${"sem_m" . $i . "_s2"}[0] > ${"sem_m" . $i . "_s2"}[1]) {

                                                                            ${"sem_m" . $i . "_p1_total"} = 2;
                                                                            ${"sem_m" . $i . "_p2_total"} = 0;

                                                                        } elseif (${"sem_m" . $i . "_s1"}[1] > ${"sem_m" . $i . "_s1"}[0] && ${"sem_m" . $i . "_s2"}[1] > ${"sem_m" . $i . "_s2"}[0]) {

                                                                            ${"sem_m" . $i . "_p1_total"} = 0;
                                                                            ${"sem_m" . $i . "_p2_total"} = 2;

                                                                        } elseif (${"sem_m" . $i . "_s1"}[0] > ${"sem_m" . $i . "_s1"}[1] && ${"sem_m" . $i . "_s2"}[0] < ${"sem_m" . $i . "_s2"}[1] && ${"sem_m" . $i . "_s3"}[0] > ${"sem_m" . $i . "_s3"}[1]) {

                                                                            ${"sem_m" . $i . "_p1_total"} = 2;
                                                                            ${"sem_m" . $i . "_p2_total"} = 1;

                                                                        } elseif (${"sem_m" . $i . "_s1"}[0] < ${"sem_m" . $i . "_s1"}[1] && ${"sem_m" . $i . "_s2"}[0] > ${"sem_m" . $i . "_s2"}[1] && ${"sem_m" . $i . "_s3"}[0] < ${"sem_m" . $i . "_s3"}[1]) {

                                                                            ${"sem_m" . $i . "_p1_total"} = 1;
                                                                            ${"sem_m" . $i . "_p2_total"} = 2;

                                                                        } elseif (${"sem_m" . $i . "_s1"}[0] < ${"sem_m" . $i . "_s1"}[1] && ${"sem_m" . $i . "_s2"}[0] > ${"sem_m" . $i . "_s2"}[1] && ${"sem_m" . $i . "_s3"}[0] > ${"sem_m" . $i . "_s3"}[1]) {

                                                                            ${"sem_m" . $i . "_p1_total"} = 2;
                                                                            ${"sem_m" . $i . "_p2_total"} = 1;

                                                                        } elseif (${"sem_m" . $i . "_s1"}[0] > ${"sem_m" . $i . "_s1"}[1] && ${"sem_m" . $i . "_s2"}[0] < ${"sem_m" . $i . "_s2"}[1] && ${"sem_m" . $i . "_s3"}[0] < ${"sem_m" . $i . "_s3"}[1]) {

                                                                            ${"sem_m" . $i . "_p1_total"} = 1;
                                                                            ${"sem_m" . $i . "_p2_total"} = 2;

                                                                        }
                                                                    ?>
                                                                    

                                                                    <ul class="matchup">

                                                                        <span class="custooltip">
                                                                            <li
                                                                                class="team team-top @if (${"sem_m" . $i . "_p1_total"} > ${"sem_m" . $i . "_p2_total"}) winnerclractive @endif">
                                                                                {{ \Illuminate\Support\Str::limit(${"sem_mat_" . $i}[0], 100) }}

                                                                                <span
                                                                                    class="score @if (${"sem_m" . $i . "_p1_total"} > ${"sem_m" . $i . "_p2_total"}) winnerclractive @endif">{{ ${"sem_m" . $i . "_s1"}[0] }}
                                                                                    {{ ${"sem_m" . $i . "_s2"}[0] }} {{ ${"sem_m" . $i . "_s3"}[0] }}</span>

                                                                            </li>
                                                                            <span class="custooltiptext">{{ ${"sem_mat_" . $i}[0] }}</span>
                                                                        </span>

                                                                        <span class="custooltip">
                                                                            <li
                                                                                class="team team-bottom @if (${"sem_m" . $i . "_p2_total"} > ${"sem_m" . $i . "_p1_total"}) winnerclractive @endif">
                                                                                {{ \Illuminate\Support\Str::limit(${"sem_mat_" . $i}[1], 100) }}

                                                                                <span
                                                                                    class="score @if (${"sem_m" . $i . "_p2_total"} > ${"sem_m" . $i . "_p1_total"}) winnerclractive @endif">{{ ${"sem_m" . $i . "_s1"}[1] }}
                                                                                    {{ ${"sem_m" . $i . "_s2"}[1] }} {{ ${"sem_m" . $i . "_s3"}[1] }}</span>

                                                                            </li>
                                                                            <span class="custooltiptext">{{ ${"sem_mat_" . $i}[1] }}</span>
                                                                        </span>

                                                                    </ul>
                                                                @else
                                                                    <ul class="matchup">

                                                                        <span class="custooltip">
                                                                            <li class="team team-top">
                                                                                {{ \Illuminate\Support\Str::limit(${"sem_mat_" . $i}[0], 100) }}

                                                                                <span class="score">N/A</span>

                                                                            </li>
                                                                            <span class="custooltiptext">{{ ${"sem_mat_" . $i}[0] }}</span>
                                                                        </span>

                                                                        <span class="custooltip">
                                                                            <li class="team team-bottom">
                                                                                {{ \Illuminate\Support\Str::limit(${"sem_mat_" . $i}[1], 100) }}

                                                                                <span class="score">N/A</span>

                                                                            </li>
                                                                            <span class="custooltiptext">{{ ${"sem_mat_" . $i}[1] }}</span>
                                                                        </span>

                                                                    </ul>
                                                                @endif
                                                            @endif

                                                        @else
                                                            @if (array_key_exists('match_'.$i, $semi_final_results))
                                                                <?php
                                                                        if (${"sem_m" . $i . "_s1"}[0] > ${"sem_m" . $i . "_s1"}[1] && ${"sem_m" . $i . "_s2"}[0] > ${"sem_m" . $i . "_s2"}[1]) {

                                                                            ${"sem_m" . $i . "_p1_total"} = 2;
                                                                            ${"sem_m" . $i . "_p2_total"} = 0;

                                                                        } elseif (${"sem_m" . $i . "_s1"}[1] > ${"sem_m" . $i . "_s1"}[0] && ${"sem_m" . $i . "_s2"}[1] > ${"sem_m" . $i . "_s2"}[0]) {

                                                                            ${"sem_m" . $i . "_p1_total"} = 0;
                                                                            ${"sem_m" . $i . "_p2_total"} = 2;

                                                                        } elseif (${"sem_m" . $i . "_s1"}[0] > ${"sem_m" . $i . "_s1"}[1] && ${"sem_m" . $i . "_s2"}[0] < ${"sem_m" . $i . "_s2"}[1] && ${"sem_m" . $i . "_s3"}[0] > ${"sem_m" . $i . "_s3"}[1]) {

                                                                            ${"sem_m" . $i . "_p1_total"} = 2;
                                                                            ${"sem_m" . $i . "_p2_total"} = 1;

                                                                        } elseif (${"sem_m" . $i . "_s1"}[0] < ${"sem_m" . $i . "_s1"}[1] && ${"sem_m" . $i . "_s2"}[0] > ${"sem_m" . $i . "_s2"}[1] && ${"sem_m" . $i . "_s3"}[0] < ${"sem_m" . $i . "_s3"}[1]) {

                                                                            ${"sem_m" . $i . "_p1_total"} = 1;
                                                                            ${"sem_m" . $i . "_p2_total"} = 2;

                                                                        } elseif (${"sem_m" . $i . "_s1"}[0] < ${"sem_m" . $i . "_s1"}[1] && ${"sem_m" . $i . "_s2"}[0] > ${"sem_m" . $i . "_s2"}[1] && ${"sem_m" . $i . "_s3"}[0] > ${"sem_m" . $i . "_s3"}[1]) {

                                                                            ${"sem_m" . $i . "_p1_total"} = 2;
                                                                            ${"sem_m" . $i . "_p2_total"} = 1;

                                                                        } elseif (${"sem_m" . $i . "_s1"}[0] > ${"sem_m" . $i . "_s1"}[1] && ${"sem_m" . $i . "_s2"}[0] < ${"sem_m" . $i . "_s2"}[1] && ${"sem_m" . $i . "_s3"}[0] < ${"sem_m" . $i . "_s3"}[1]) {

                                                                            ${"sem_m" . $i . "_p1_total"} = 1;
                                                                            ${"sem_m" . $i . "_p2_total"} = 2;

                                                                        }
                                                                ?>
                                                                

                                                                <ul class="matchup">

                                                                    <span class="custooltip">
                                                                        <li
                                                                            class="team team-top @if (${"sem_m" . $i . "_p1_total"} > ${"sem_m" . $i . "_p2_total"}) winnerclractive @endif">
                                                                            {{ \Illuminate\Support\Str::limit(${"sem_mat_" . $i}[0], 100) }}

                                                                            <span
                                                                                class="score @if (${"sem_m" . $i . "_p1_total"} > ${"sem_m" . $i . "_p2_total"}) winnerclractive @endif">{{ ${"sem_m" . $i . "_s1"}[0] }}
                                                                                {{ ${"sem_m" . $i . "_s2"}[0] }} {{ ${"sem_m" . $i . "_s3"}[0] }}</span>

                                                                        </li>
                                                                        <span class="custooltiptext">{{ ${"sem_mat_" . $i}[0] }}</span>
                                                                    </span>

                                                                    <span class="custooltip">
                                                                        <li
                                                                            class="team team-bottom @if (${"sem_m" . $i . "_p2_total"} > ${"sem_m" . $i . "_p1_total"}) winnerclractive @endif">
                                                                            {{ \Illuminate\Support\Str::limit(${"sem_mat_" . $i}[1], 100) }}

                                                                            <span
                                                                                class="score @if (${"sem_m" . $i . "_p2_total"} > ${"sem_m" . $i . "_p1_total"}) winnerclractive @endif">{{ ${"sem_m" . $i . "_s1"}[1] }}
                                                                                {{ ${"sem_m" . $i . "_s2"}[1] }} {{ ${"sem_m" . $i . "_s3"}[1] }}</span>

                                                                        </li>
                                                                        <span class="custooltiptext">{{ ${"sem_mat_" . $i}[1] }}</span>
                                                                    </span>

                                                                </ul>
                                                            @else
                                                                <ul class="matchup">

                                                                    <span class="custooltip">
                                                                        <li class="team team-top">
                                                                            {{ \Illuminate\Support\Str::limit(${"sem_mat_" . $i}[0], 100) }}

                                                                            <span class="score">N/A</span>

                                                                        </li>
                                                                        <span class="custooltiptext">{{ ${"sem_mat_" . $i}[0] }}</span>
                                                                    </span>

                                                                    <span class="custooltip">
                                                                        <li class="team team-bottom">
                                                                            {{ \Illuminate\Support\Str::limit(${"sem_mat_" . $i}[1], 100) }}

                                                                            <span class="score">N/A</span>

                                                                        </li>
                                                                        <span class="custooltiptext">{{ ${"sem_mat_" . $i}[1] }}</span>
                                                                    </span>

                                                                </ul>
                                                            @endif
                                                        @endif


                                                    @else
                                                        <ul class="matchup">

                                                            <span class="custooltip">
                                                                <li class="team team-top">
                                                                    {{ \Illuminate\Support\Str::limit(${"sem_mat_" . $i}[0], 100) }}

                                                                    <span class="score">N/A</span>

                                                                </li>
                                                                <span class="custooltiptext">{{ ${"sem_mat_" . $i}[0] }}</span>
                                                            </span>

                                                            <span class="custooltip">
                                                                <li class="team team-bottom">
                                                                    {{ \Illuminate\Support\Str::limit(${"sem_mat_" . $i}[1], 100) }}

                                                                    <span class="score">N/A</span>

                                                                </li>
                                                                <span class="custooltiptext">{{ ${"sem_mat_" . $i}[1] }}</span>
                                                            </span>
                                                        </ul>
                                                    @endif
                                                @else
                                                    <ul class="matchup">

                                                        <li class="team team-top">n/a<span class="score">N/A</span></li>
                                                        <li class="team team-bottom">n/a<span class="score">N/A</span></li>
                                                    </ul>
                                                @endif
                                                
                                            @else
                                                
                                                <ul class="matchup">
                                                    
                                                    <li class="team team-top"><span class="score"></span></li>
                                                    <li class="team team-bottom"><span class="score"></span></li>
                                                </ul>
                                                
                                            @endif

                                        @endif

                                    @endfor

                                </div>                                
                            @else
                                @if($tournament->semi_final_matches)
                                    
                                    <div class="round round-two current">
                                        <div class="round-details">semifinal<br/><span class="date">@if($t_d_semf) {{ $strt_sem[0] }} - {{ $endd_sem[0] }} @else N/A @endif</span>
                                        </div>
                                        
                                        @for($i = 2; $i < 3; $i++)
                                            @if (array_key_exists('match_'.$i, $semi_final_matches))

                                                <?php 
                                                    $get_matches = $semi_final_matches['match_'.$i];
                                                    $vs_match = explode(" VS ", $get_matches);
                                                ?>

                                                @if ($tournament->semi_final_results)
                                                        
                                                    @if($semi_final_status)
                                                        @if (array_key_exists('match_'.$i, $semi_final_status))
                                                            @if($semi_final_status['match_'.$i])
                                                                <?php
                                                                    if (${"sem_m" . $i . "_s1"}[0] > ${"sem_m" . $i . "_s1"}[1] && ${"sem_m" . $i . "_s2"}[0] > ${"sem_m" . $i . "_s2"}[1]) {

                                                                        ${"sem_m" . $i . "_p1_total"} = 2;
                                                                        ${"sem_m" . $i . "_p2_total"} = 0;

                                                                    } elseif (${"sem_m" . $i . "_s1"}[1] > ${"sem_m" . $i . "_s1"}[0] && ${"sem_m" . $i . "_s2"}[1] > ${"sem_m" . $i . "_s2"}[0]) {

                                                                        ${"sem_m" . $i . "_p1_total"} = 0;
                                                                        ${"sem_m" . $i . "_p2_total"} = 2;

                                                                    } elseif (${"sem_m" . $i . "_s1"}[0] > ${"sem_m" . $i . "_s1"}[1] && ${"sem_m" . $i . "_s2"}[0] < ${"sem_m" . $i . "_s2"}[1] && ${"sem_m" . $i . "_s3"}[0] > ${"sem_m" . $i . "_s3"}[1]) {

                                                                        ${"sem_m" . $i . "_p1_total"} = 2;
                                                                        ${"sem_m" . $i . "_p2_total"} = 1;

                                                                    } elseif (${"sem_m" . $i . "_s1"}[0] < ${"sem_m" . $i . "_s1"}[1] && ${"sem_m" . $i . "_s2"}[0] > ${"sem_m" . $i . "_s2"}[1] && ${"sem_m" . $i . "_s3"}[0] < ${"sem_m" . $i . "_s3"}[1]) {

                                                                        ${"sem_m" . $i . "_p1_total"} = 1;
                                                                        ${"sem_m" . $i . "_p2_total"} = 2;

                                                                    } elseif (${"sem_m" . $i . "_s1"}[0] < ${"sem_m" . $i . "_s1"}[1] && ${"sem_m" . $i . "_s2"}[0] > ${"sem_m" . $i . "_s2"}[1] && ${"sem_m" . $i . "_s3"}[0] > ${"sem_m" . $i . "_s3"}[1]) {

                                                                        ${"sem_m" . $i . "_p1_total"} = 2;
                                                                        ${"sem_m" . $i . "_p2_total"} = 1;

                                                                    } elseif (${"sem_m" . $i . "_s1"}[0] > ${"sem_m" . $i . "_s1"}[1] && ${"sem_m" . $i . "_s2"}[0] < ${"sem_m" . $i . "_s2"}[1] && ${"sem_m" . $i . "_s3"}[0] < ${"sem_m" . $i . "_s3"}[1]) {

                                                                        ${"sem_m" . $i . "_p1_total"} = 1;
                                                                        ${"sem_m" . $i . "_p2_total"} = 2;

                                                                    }
                                                                ?>


                                                                <ul class="matchup">

                                                                    <span class="custooltip">
                                                                        <li
                                                                            class="team team-top @if (${"sem_m" . $i . "_p1_total"} > ${"sem_m" . $i . "_p2_total"}) winnerclractive @endif">
                                                                            {{ \Illuminate\Support\Str::limit(${"sem_mat_" . $i}[0], 100) }}

                                                                            @if (${"sem_m" . $i . "_p1_total"} < ${"sem_m" . $i . "_p2_total"})
                                                                                @if($semi_final_status['match_'.$i] == 'Retired')
                                                                                    @if($semi_final_retires)
                                                                                        @if (array_key_exists('match_'.$i, $semi_final_retires))
                                                                                            @if($semi_final_retires['match_'.$i] == $vs_match[0])
                                                                                                
                                                                                                <span class="text-danger text-center" style="font-size: 8px;font-weight: 500;">(R)</span>

                                                                                            @endif
                                                                                        @endif
                                                                                    @endif
                                                                                    <span
                                                                                        class="score">{{ ${"sem_m" . $i . "_s1"}[0] }}
                                                                                        {{ ${"sem_m" . $i . "_s2"}[0] }} {{ ${"sem_m" . $i . "_s3"}[0] }}</span>
                                                                                @elseif($semi_final_status['match_'.$i] == 'Withdraw')
                                                                                    <span class="text-danger text-center" style="font-size: 8px;font-weight: 500;">(W)</span>
                                                                                    <span
                                                                                        class="score">&#8212;</span>
                                                                                @elseif($semi_final_status['match_'.$i] == 'Decided by Organisers')
                                                                                    <span class="text-danger text-center" style="font-size: 8px;font-weight: 500;">(D)</span>
                                                                                    <span
                                                                                        class="score">&#8212;</span>
                                                                                @endif

                                                                            @else
                                                                                @if($semi_final_status['match_'.$i] == 'Retired')
                                                                                    @if($semi_final_retires)
                                                                                        @if (array_key_exists('match_'.$i, $semi_final_retires))
                                                                                            @if($semi_final_retires['match_'.$i] == $vs_match[0])
                                                                                                
                                                                                                <span class="text-danger text-center" style="font-size: 8px;font-weight: 500;">(R)</span>

                                                                                            @endif
                                                                                        @endif
                                                                                    @endif
                                                                                    <span
                                                                                    class="score winnerclractive">{{ ${"sem_m" . $i . "_s1"}[0] }}
                                                                                    {{ ${"sem_m" . $i . "_s2"}[0] }} {{ ${"sem_m" . $i . "_s3"}[0] }}</span>
                                                                                @elseif($semi_final_status['match_'.$i] == 'Withdraw')
                                                                                    <span
                                                                                    class="score winnerclractive">&#8212;</span>
                                                                                @elseif($semi_final_status['match_'.$i] == 'Decided by Organisers')
                                                                                    <span
                                                                                    class="score winnerclractive">&#8212;</span>
                                                                                @endif
                                                                                    
                                                                            @endif

                                                                        </li>

                                                                        <span class="custooltiptext">{{ ${"sem_mat_" . $i}[0] }} 
                                                                            @if($semi_final_status)
                                                                                @if($semi_final_status['match_'.$i] == 'Retired')
                                                                                    @if($semi_final_retires)
                                                                                        @if (array_key_exists('match_'.$i, $semi_final_retires))
                                                                                            @if($semi_final_retires['match_'.$i] == $vs_match[0])
                                                                                                
                                                                                                ({{ $semi_final_status['match_'.$i] }})

                                                                                            @endif
                                                                                        @endif
                                                                                    @endif
                                                                                @else
                                                                                    @if (${"sem_m" . $i . "_p1_total"} < ${"sem_m" . $i . "_p2_total"}) 
                                                                                        ({{ $semi_final_status['match_'.$i] }}) 
                                                                                    @endif
                                                                                @endif
                                                                            @endif
                                                                        </span>

                                                                    </span>

                                                                    <span class="custooltip">
                                                                        <li
                                                                            class="team team-bottom @if (${"sem_m" . $i . "_p2_total"} > ${"sem_m" . $i . "_p1_total"}) winnerclractive @endif">
                                                                            {{ \Illuminate\Support\Str::limit(${"sem_mat_" . $i}[1], 100) }}

                                                                            @if (${"sem_m" . $i . "_p1_total"} > ${"sem_m" . $i . "_p2_total"})
                                                                                @if($semi_final_status['match_'.$i] == 'Retired')
                                                                                    @if($semi_final_retires)
                                                                                        @if (array_key_exists('match_'.$i, $semi_final_retires))
                                                                                            @if($semi_final_retires['match_'.$i] == $vs_match[1])
                                                                                                
                                                                                                <span class="text-danger text-center" style="font-size: 8px;font-weight: 500;">(R)</span>

                                                                                            @endif
                                                                                        @endif
                                                                                    @endif
                                                                                    <span
                                                                                        class="score">{{ ${"sem_m" . $i . "_s1"}[1] }}
                                                                                        {{ ${"sem_m" . $i . "_s2"}[1] }} {{ ${"sem_m" . $i . "_s3"}[1] }}</span>
                                                                                @elseif($semi_final_status['match_'.$i] == 'Withdraw')
                                                                                    <span class="text-danger text-center" style="font-size: 8px;font-weight: 500;">(W)</span>
                                                                                    <span
                                                                                        class="score">&#8212;</span>
                                                                                @elseif($semi_final_status['match_'.$i] == 'Decided by Organisers')
                                                                                    <span class="text-danger text-center" style="font-size: 8px;font-weight: 500;">(D)</span>
                                                                                    <span
                                                                                        class="score">&#8212;</span>
                                                                                @endif

                                                                            @else
                                                                                @if($semi_final_status['match_'.$i] == 'Retired')
                                                                                    @if($semi_final_retires)
                                                                                        @if (array_key_exists('match_'.$i, $semi_final_retires))
                                                                                            @if($semi_final_retires['match_'.$i] == $vs_match[1])
                                                                                                
                                                                                                <span class="text-danger text-center" style="font-size: 8px;font-weight: 500;">(R)</span>

                                                                                            @endif
                                                                                        @endif
                                                                                    @endif
                                                                                    <span
                                                                                    class="score winnerclractive">{{ ${"sem_m" . $i . "_s1"}[1] }}
                                                                                    {{ ${"sem_m" . $i . "_s2"}[1] }} {{ ${"sem_m" . $i . "_s3"}[1] }}</span>
                                                                                @elseif($semi_final_status['match_'.$i] == 'Withdraw')
                                                                                    <span
                                                                                    class="score winnerclractive">&#8212;</span>
                                                                                @elseif($semi_final_status['match_'.$i] == 'Decided by Organisers')
                                                                                    <span
                                                                                    class="score winnerclractive">&#8212;</span>
                                                                                @endif
                                                                            @endif

                                                                        </li>

                                                                        <span class="custooltiptext">{{ ${"sem_mat_" . $i}[1] }} 

                                                                            @if($semi_final_status)
                                                                                @if($semi_final_status['match_'.$i] == 'Retired')
                                                                                    @if($semi_final_retires)
                                                                                        @if (array_key_exists('match_'.$i, $semi_final_retires))
                                                                                            @if($semi_final_retires['match_'.$i] == $vs_match[1])
                                                                                                
                                                                                                ({{ $semi_final_status['match_'.$i] }})

                                                                                            @endif
                                                                                        @endif
                                                                                    @endif
                                                                                @else
                                                                                    @if (${"sem_m" . $i . "_p1_total"} > ${"sem_m" . $i . "_p2_total"}) 
                                                                                        ({{ $semi_final_status['match_'.$i] }}) 
                                                                                    @endif
                                                                                @endif
                                                                            @endif

                                                                        </span>

                                                                    </span>

                                                                </ul>

                                                            @else
                                                                @if (array_key_exists('match_'.$i, $semi_final_results))
                                                                    <?php
                                                                        if (${"sem_m" . $i . "_s1"}[0] > ${"sem_m" . $i . "_s1"}[1] && ${"sem_m" . $i . "_s2"}[0] > ${"sem_m" . $i . "_s2"}[1]) {

                                                                            ${"sem_m" . $i . "_p1_total"} = 2;
                                                                            ${"sem_m" . $i . "_p2_total"} = 0;

                                                                        } elseif (${"sem_m" . $i . "_s1"}[1] > ${"sem_m" . $i . "_s1"}[0] && ${"sem_m" . $i . "_s2"}[1] > ${"sem_m" . $i . "_s2"}[0]) {

                                                                            ${"sem_m" . $i . "_p1_total"} = 0;
                                                                            ${"sem_m" . $i . "_p2_total"} = 2;

                                                                        } elseif (${"sem_m" . $i . "_s1"}[0] > ${"sem_m" . $i . "_s1"}[1] && ${"sem_m" . $i . "_s2"}[0] < ${"sem_m" . $i . "_s2"}[1] && ${"sem_m" . $i . "_s3"}[0] > ${"sem_m" . $i . "_s3"}[1]) {

                                                                            ${"sem_m" . $i . "_p1_total"} = 2;
                                                                            ${"sem_m" . $i . "_p2_total"} = 1;

                                                                        } elseif (${"sem_m" . $i . "_s1"}[0] < ${"sem_m" . $i . "_s1"}[1] && ${"sem_m" . $i . "_s2"}[0] > ${"sem_m" . $i . "_s2"}[1] && ${"sem_m" . $i . "_s3"}[0] < ${"sem_m" . $i . "_s3"}[1]) {

                                                                            ${"sem_m" . $i . "_p1_total"} = 1;
                                                                            ${"sem_m" . $i . "_p2_total"} = 2;

                                                                        } elseif (${"sem_m" . $i . "_s1"}[0] < ${"sem_m" . $i . "_s1"}[1] && ${"sem_m" . $i . "_s2"}[0] > ${"sem_m" . $i . "_s2"}[1] && ${"sem_m" . $i . "_s3"}[0] > ${"sem_m" . $i . "_s3"}[1]) {

                                                                            ${"sem_m" . $i . "_p1_total"} = 2;
                                                                            ${"sem_m" . $i . "_p2_total"} = 1;

                                                                        } elseif (${"sem_m" . $i . "_s1"}[0] > ${"sem_m" . $i . "_s1"}[1] && ${"sem_m" . $i . "_s2"}[0] < ${"sem_m" . $i . "_s2"}[1] && ${"sem_m" . $i . "_s3"}[0] < ${"sem_m" . $i . "_s3"}[1]) {

                                                                            ${"sem_m" . $i . "_p1_total"} = 1;
                                                                            ${"sem_m" . $i . "_p2_total"} = 2;

                                                                        }
                                                                    ?>
                                                                    

                                                                    <ul class="matchup">

                                                                        <span class="custooltip">
                                                                            <li
                                                                                class="team team-top @if (${"sem_m" . $i . "_p1_total"} > ${"sem_m" . $i . "_p2_total"}) winnerclractive @endif">
                                                                                {{ \Illuminate\Support\Str::limit(${"sem_mat_" . $i}[0], 100) }}

                                                                                <span
                                                                                    class="score @if (${"sem_m" . $i . "_p1_total"} > ${"sem_m" . $i . "_p2_total"}) winnerclractive @endif">{{ ${"sem_m" . $i . "_s1"}[0] }}
                                                                                    {{ ${"sem_m" . $i . "_s2"}[0] }} {{ ${"sem_m" . $i . "_s3"}[0] }}</span>

                                                                            </li>
                                                                            <span class="custooltiptext">{{ ${"sem_mat_" . $i}[0] }}</span>
                                                                        </span>

                                                                        <span class="custooltip">
                                                                            <li
                                                                                class="team team-bottom @if (${"sem_m" . $i . "_p2_total"} > ${"sem_m" . $i . "_p1_total"}) winnerclractive @endif">
                                                                                {{ \Illuminate\Support\Str::limit(${"sem_mat_" . $i}[1], 100) }}

                                                                                <span
                                                                                    class="score @if (${"sem_m" . $i . "_p2_total"} > ${"sem_m" . $i . "_p1_total"}) winnerclractive @endif">{{ ${"sem_m" . $i . "_s1"}[1] }}
                                                                                    {{ ${"sem_m" . $i . "_s2"}[1] }} {{ ${"sem_m" . $i . "_s3"}[1] }}</span>

                                                                            </li>
                                                                            <span class="custooltiptext">{{ ${"sem_mat_" . $i}[1] }}</span>
                                                                        </span>

                                                                    </ul>
                                                                @else
                                                                    <ul class="matchup">

                                                                        <span class="custooltip">
                                                                            <li class="team team-top">
                                                                                {{ \Illuminate\Support\Str::limit(${"sem_mat_" . $i}[0], 100) }}

                                                                                <span class="score">N/A</span>

                                                                            </li>
                                                                            <span class="custooltiptext">{{ ${"sem_mat_" . $i}[0] }}</span>
                                                                        </span>

                                                                        <span class="custooltip">
                                                                            <li class="team team-bottom">
                                                                                {{ \Illuminate\Support\Str::limit(${"sem_mat_" . $i}[1], 100) }}

                                                                                <span class="score">N/A</span>

                                                                            </li>
                                                                            <span class="custooltiptext">{{ ${"sem_mat_" . $i}[1] }}</span>
                                                                        </span>

                                                                    </ul>
                                                                @endif
                                                            @endif


                                                        @else
                                                            @if (array_key_exists('match_'.$i, $semi_final_results))
                                                                <?php
                                                                    if (${"sem_m" . $i . "_s1"}[0] > ${"sem_m" . $i . "_s1"}[1] && ${"sem_m" . $i . "_s2"}[0] > ${"sem_m" . $i . "_s2"}[1]) {

                                                                        ${"sem_m" . $i . "_p1_total"} = 2;
                                                                        ${"sem_m" . $i . "_p2_total"} = 0;

                                                                    } elseif (${"sem_m" . $i . "_s1"}[1] > ${"sem_m" . $i . "_s1"}[0] && ${"sem_m" . $i . "_s2"}[1] > ${"sem_m" . $i . "_s2"}[0]) {

                                                                        ${"sem_m" . $i . "_p1_total"} = 0;
                                                                        ${"sem_m" . $i . "_p2_total"} = 2;

                                                                    } elseif (${"sem_m" . $i . "_s1"}[0] > ${"sem_m" . $i . "_s1"}[1] && ${"sem_m" . $i . "_s2"}[0] < ${"sem_m" . $i . "_s2"}[1] && ${"sem_m" . $i . "_s3"}[0] > ${"sem_m" . $i . "_s3"}[1]) {

                                                                        ${"sem_m" . $i . "_p1_total"} = 2;
                                                                        ${"sem_m" . $i . "_p2_total"} = 1;

                                                                    } elseif (${"sem_m" . $i . "_s1"}[0] < ${"sem_m" . $i . "_s1"}[1] && ${"sem_m" . $i . "_s2"}[0] > ${"sem_m" . $i . "_s2"}[1] && ${"sem_m" . $i . "_s3"}[0] < ${"sem_m" . $i . "_s3"}[1]) {

                                                                        ${"sem_m" . $i . "_p1_total"} = 1;
                                                                        ${"sem_m" . $i . "_p2_total"} = 2;

                                                                    } elseif (${"sem_m" . $i . "_s1"}[0] < ${"sem_m" . $i . "_s1"}[1] && ${"sem_m" . $i . "_s2"}[0] > ${"sem_m" . $i . "_s2"}[1] && ${"sem_m" . $i . "_s3"}[0] > ${"sem_m" . $i . "_s3"}[1]) {

                                                                        ${"sem_m" . $i . "_p1_total"} = 2;
                                                                        ${"sem_m" . $i . "_p2_total"} = 1;

                                                                    } elseif (${"sem_m" . $i . "_s1"}[0] > ${"sem_m" . $i . "_s1"}[1] && ${"sem_m" . $i . "_s2"}[0] < ${"sem_m" . $i . "_s2"}[1] && ${"sem_m" . $i . "_s3"}[0] < ${"sem_m" . $i . "_s3"}[1]) {

                                                                        ${"sem_m" . $i . "_p1_total"} = 1;
                                                                        ${"sem_m" . $i . "_p2_total"} = 2;

                                                                    }
                                                                ?>
                                                                

                                                                <ul class="matchup">

                                                                    <span class="custooltip">
                                                                        <li
                                                                            class="team team-top @if (${"sem_m" . $i . "_p1_total"} > ${"sem_m" . $i . "_p2_total"}) winnerclractive @endif">
                                                                            {{ \Illuminate\Support\Str::limit(${"sem_mat_" . $i}[0], 100) }}

                                                                            <span
                                                                                class="score @if (${"sem_m" . $i . "_p1_total"} > ${"sem_m" . $i . "_p2_total"}) winnerclractive @endif">{{ ${"sem_m" . $i . "_s1"}[0] }}
                                                                                {{ ${"sem_m" . $i . "_s2"}[0] }} {{ ${"sem_m" . $i . "_s3"}[0] }}</span>

                                                                        </li>
                                                                        <span class="custooltiptext">{{ ${"sem_mat_" . $i}[0] }}</span>
                                                                    </span>

                                                                    <span class="custooltip">
                                                                        <li
                                                                            class="team team-bottom @if (${"sem_m" . $i . "_p2_total"} > ${"sem_m" . $i . "_p1_total"}) winnerclractive @endif">
                                                                            {{ \Illuminate\Support\Str::limit(${"sem_mat_" . $i}[1], 100) }}

                                                                            <span
                                                                                class="score @if (${"sem_m" . $i . "_p2_total"} > ${"sem_m" . $i . "_p1_total"}) winnerclractive @endif">{{ ${"sem_m" . $i . "_s1"}[1] }}
                                                                                {{ ${"sem_m" . $i . "_s2"}[1] }} {{ ${"sem_m" . $i . "_s3"}[1] }}</span>

                                                                        </li>
                                                                        <span class="custooltiptext">{{ ${"sem_mat_" . $i}[1] }}</span>
                                                                    </span>

                                                                </ul>
                                                            @else
                                                                <ul class="matchup">

                                                                    <span class="custooltip">
                                                                        <li class="team team-top">
                                                                            {{ \Illuminate\Support\Str::limit(${"sem_mat_" . $i}[0], 100) }}

                                                                            <span class="score">N/A</span>

                                                                        </li>
                                                                        <span class="custooltiptext">{{ ${"sem_mat_" . $i}[0] }}</span>
                                                                    </span>

                                                                    <span class="custooltip">
                                                                        <li class="team team-bottom">
                                                                            {{ \Illuminate\Support\Str::limit(${"sem_mat_" . $i}[1], 100) }}

                                                                            <span class="score">N/A</span>

                                                                        </li>
                                                                        <span class="custooltiptext">{{ ${"sem_mat_" . $i}[1] }}</span>
                                                                    </span>

                                                                </ul>
                                                            @endif
                                                        @endif

                                                    @else
                                                        @if (array_key_exists('match_'.$i, $semi_final_results))
                                                            <?php
                                                                    if (${"sem_m" . $i . "_s1"}[0] > ${"sem_m" . $i . "_s1"}[1] && ${"sem_m" . $i . "_s2"}[0] > ${"sem_m" . $i . "_s2"}[1]) {

                                                                        ${"sem_m" . $i . "_p1_total"} = 2;
                                                                        ${"sem_m" . $i . "_p2_total"} = 0;

                                                                    } elseif (${"sem_m" . $i . "_s1"}[1] > ${"sem_m" . $i . "_s1"}[0] && ${"sem_m" . $i . "_s2"}[1] > ${"sem_m" . $i . "_s2"}[0]) {

                                                                        ${"sem_m" . $i . "_p1_total"} = 0;
                                                                        ${"sem_m" . $i . "_p2_total"} = 2;

                                                                    } elseif (${"sem_m" . $i . "_s1"}[0] > ${"sem_m" . $i . "_s1"}[1] && ${"sem_m" . $i . "_s2"}[0] < ${"sem_m" . $i . "_s2"}[1] && ${"sem_m" . $i . "_s3"}[0] > ${"sem_m" . $i . "_s3"}[1]) {

                                                                        ${"sem_m" . $i . "_p1_total"} = 2;
                                                                        ${"sem_m" . $i . "_p2_total"} = 1;

                                                                    } elseif (${"sem_m" . $i . "_s1"}[0] < ${"sem_m" . $i . "_s1"}[1] && ${"sem_m" . $i . "_s2"}[0] > ${"sem_m" . $i . "_s2"}[1] && ${"sem_m" . $i . "_s3"}[0] < ${"sem_m" . $i . "_s3"}[1]) {

                                                                        ${"sem_m" . $i . "_p1_total"} = 1;
                                                                        ${"sem_m" . $i . "_p2_total"} = 2;

                                                                    } elseif (${"sem_m" . $i . "_s1"}[0] < ${"sem_m" . $i . "_s1"}[1] && ${"sem_m" . $i . "_s2"}[0] > ${"sem_m" . $i . "_s2"}[1] && ${"sem_m" . $i . "_s3"}[0] > ${"sem_m" . $i . "_s3"}[1]) {

                                                                        ${"sem_m" . $i . "_p1_total"} = 2;
                                                                        ${"sem_m" . $i . "_p2_total"} = 1;

                                                                    } elseif (${"sem_m" . $i . "_s1"}[0] > ${"sem_m" . $i . "_s1"}[1] && ${"sem_m" . $i . "_s2"}[0] < ${"sem_m" . $i . "_s2"}[1] && ${"sem_m" . $i . "_s3"}[0] < ${"sem_m" . $i . "_s3"}[1]) {

                                                                        ${"sem_m" . $i . "_p1_total"} = 1;
                                                                        ${"sem_m" . $i . "_p2_total"} = 2;

                                                                    }
                                                            ?>
                                                            

                                                            <ul class="matchup">

                                                                <span class="custooltip">
                                                                    <li
                                                                        class="team team-top @if (${"sem_m" . $i . "_p1_total"} > ${"sem_m" . $i . "_p2_total"}) winnerclractive @endif">
                                                                        {{ \Illuminate\Support\Str::limit(${"sem_mat_" . $i}[0], 100) }}

                                                                        <span
                                                                            class="score @if (${"sem_m" . $i . "_p1_total"} > ${"sem_m" . $i . "_p2_total"}) winnerclractive @endif">{{ ${"sem_m" . $i . "_s1"}[0] }}
                                                                            {{ ${"sem_m" . $i . "_s2"}[0] }} {{ ${"sem_m" . $i . "_s3"}[0] }}</span>

                                                                    </li>
                                                                    <span class="custooltiptext">{{ ${"sem_mat_" . $i}[0] }}</span>
                                                                </span>

                                                                <span class="custooltip">
                                                                    <li
                                                                        class="team team-bottom @if (${"sem_m" . $i . "_p2_total"} > ${"sem_m" . $i . "_p1_total"}) winnerclractive @endif">
                                                                        {{ \Illuminate\Support\Str::limit(${"sem_mat_" . $i}[1], 100) }}

                                                                        <span
                                                                            class="score @if (${"sem_m" . $i . "_p2_total"} > ${"sem_m" . $i . "_p1_total"}) winnerclractive @endif">{{ ${"sem_m" . $i . "_s1"}[1] }}
                                                                            {{ ${"sem_m" . $i . "_s2"}[1] }} {{ ${"sem_m" . $i . "_s3"}[1] }}</span>

                                                                    </li>
                                                                    <span class="custooltiptext">{{ ${"sem_mat_" . $i}[1] }}</span>
                                                                </span>

                                                            </ul>
                                                        @else
                                                            <ul class="matchup">

                                                                <span class="custooltip">
                                                                    <li class="team team-top">
                                                                        {{ \Illuminate\Support\Str::limit(${"sem_mat_" . $i}[0], 100) }}

                                                                        <span class="score">N/A</span>

                                                                    </li>
                                                                    <span class="custooltiptext">{{ ${"sem_mat_" . $i}[0] }}</span>
                                                                </span>

                                                                <span class="custooltip">
                                                                    <li class="team team-bottom">
                                                                        {{ \Illuminate\Support\Str::limit(${"sem_mat_" . $i}[1], 100) }}

                                                                        <span class="score">N/A</span>

                                                                    </li>
                                                                    <span class="custooltiptext">{{ ${"sem_mat_" . $i}[1] }}</span>
                                                                </span>

                                                            </ul>
                                                        @endif
                                                    @endif


                                                @else
                                                    <ul class="matchup">

                                                        <span class="custooltip">
                                                            <li class="team team-top">
                                                                {{ \Illuminate\Support\Str::limit(${"sem_mat_" . $i}[0], 100) }}

                                                                <span class="score">N/A</span>

                                                            </li>
                                                            <span class="custooltiptext">{{ ${"sem_mat_" . $i}[0] }}</span>
                                                        </span>

                                                        <span class="custooltip">
                                                            <li class="team team-bottom">
                                                                {{ \Illuminate\Support\Str::limit(${"sem_mat_" . $i}[1], 100) }}

                                                                <span class="score">N/A</span>

                                                            </li>
                                                            <span class="custooltiptext">{{ ${"sem_mat_" . $i}[1] }}</span>
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

                                @else
                                    <div class="round round-two">
                                        <div class="round-details">semifinal<br/><span class="date">@if($t_d_semf) {{ $strt_sem[0] }} - {{ $endd_sem[0] }} @else N/A @endif</span></div>

                                        @for($i = 2; $i < 3; $i++)
                                            <ul class="matchup">
                                                
                                                <li class="team team-top"><span class="score"></span></li>
                                                <li class="team team-bottom"><span class="score"></span></li>
                                            </ul>
                                        @endfor

                                    </div>
                                @endif
                            @endif


                            @if($round_one_auto_selection)

                                <div class="round round-one @if($round_one_auto_selection) current @elseif($round_one_matches) current @endif">
                                    <div class="round-details">Round 1<br/><span class="date">@if($t_d_rou1) {{ $strt_r1[0] }} - {{ $endd_r1[0] }} @else N/A @endif</span></div>
                                    
                                    @for($i = 3; $i < 5; $i++)
                                        @if (array_key_exists('match_'.$i, $round_one_auto_selection))
                                            
                                            <ul class="matchup">

                                                <span class="custooltipleft">
                                                    <li
                                                        class="team team-top winnerclractive">
                                                        {{ \Illuminate\Support\Str::limit($rou_1_mat_auto['match_'.$i], 100) }}

                                                        <span
                                                            class="score winnerclractive">N/A</span>

                                                    </li>
                                                    <span class="custooltiplefttext">{{ $rou_1_mat_auto['match_'.$i] }}</span>
                                                </span>

                                                <span class="custooltipleft">
                                                    <li
                                                        class="team team-bottom">
                                                        N/A

                                                        <span
                                                            class="score">N/A</span>

                                                    </li>
                                                    <span class="custooltiplefttext">N/A</span>
                                                </span>

                                            </ul>
                                            
                                        @else

                                            @if($tournament->round_one_matches)
                                                                                                   
                                                @if (array_key_exists('match_'.$i, $round_one_matches))
                                                    
                                                    <?php 
                                                        $get_matches = $round_one_matches['match_'.$i];
                                                        $vs_match = explode(" VS ", $get_matches);
                                                    ?>

                                                    @if ($tournament->round_one_results)
                                                        
                                                        @if($round_one_status)
                                                            @if (array_key_exists('match_'.$i, $round_one_status))
                                                                @if($round_one_status['match_'.$i])
                                                                    <?php
                                                                        if (${"rou1_m" . $i . "_s1"}[0] > ${"rou1_m" . $i . "_s1"}[1] && ${"rou1_m" . $i . "_s2"}[0] > ${"rou1_m" . $i . "_s2"}[1]) {

                                                                            ${"rou1_m" . $i . "_p1_total"} = 2;
                                                                            ${"rou1_m" . $i . "_p2_total"} = 0;

                                                                        } elseif (${"rou1_m" . $i . "_s1"}[1] > ${"rou1_m" . $i . "_s1"}[0] && ${"rou1_m" . $i . "_s2"}[1] > ${"rou1_m" . $i . "_s2"}[0]) {

                                                                            ${"rou1_m" . $i . "_p1_total"} = 0;
                                                                            ${"rou1_m" . $i . "_p2_total"} = 2;

                                                                        } elseif (${"rou1_m" . $i . "_s1"}[0] > ${"rou1_m" . $i . "_s1"}[1] && ${"rou1_m" . $i . "_s2"}[0] < ${"rou1_m" . $i . "_s2"}[1] && ${"rou1_m" . $i . "_s3"}[0] > ${"rou1_m" . $i . "_s3"}[1]) {

                                                                            ${"rou1_m" . $i . "_p1_total"} = 2;
                                                                            ${"rou1_m" . $i . "_p2_total"} = 1;

                                                                        } elseif (${"rou1_m" . $i . "_s1"}[0] < ${"rou1_m" . $i . "_s1"}[1] && ${"rou1_m" . $i . "_s2"}[0] > ${"rou1_m" . $i . "_s2"}[1] && ${"rou1_m" . $i . "_s3"}[0] < ${"rou1_m" . $i . "_s3"}[1]) {

                                                                            ${"rou1_m" . $i . "_p1_total"} = 1;
                                                                            ${"rou1_m" . $i . "_p2_total"} = 2;

                                                                        } elseif (${"rou1_m" . $i . "_s1"}[0] < ${"rou1_m" . $i . "_s1"}[1] && ${"rou1_m" . $i . "_s2"}[0] > ${"rou1_m" . $i . "_s2"}[1] && ${"rou1_m" . $i . "_s3"}[0] > ${"rou1_m" . $i . "_s3"}[1]) {

                                                                            ${"rou1_m" . $i . "_p1_total"} = 2;
                                                                            ${"rou1_m" . $i . "_p2_total"} = 1;

                                                                        } elseif (${"rou1_m" . $i . "_s1"}[0] > ${"rou1_m" . $i . "_s1"}[1] && ${"rou1_m" . $i . "_s2"}[0] < ${"rou1_m" . $i . "_s2"}[1] && ${"rou1_m" . $i . "_s3"}[0] < ${"rou1_m" . $i . "_s3"}[1]) {

                                                                            ${"rou1_m" . $i . "_p1_total"} = 1;
                                                                            ${"rou1_m" . $i . "_p2_total"} = 2;

                                                                        }
                                                                    ?>


                                                                    <ul class="matchup">

                                                                        <span class="custooltipleft">
                                                                            <li
                                                                                class="team team-top @if (${"rou1_m" . $i . "_p1_total"} > ${"rou1_m" . $i . "_p2_total"}) winnerclractive @endif">
                                                                                {{ \Illuminate\Support\Str::limit(${"rou_1_mat_" . $i}[0], 100) }}

                                                                                @if (${"rou1_m" . $i . "_p1_total"} < ${"rou1_m" . $i . "_p2_total"})
                                                                                    @if($round_one_status['match_'.$i] == 'Retired')
                                                                                        @if($round_one_retires)
                                                                                            @if (array_key_exists('match_'.$i, $round_one_retires))
                                                                                                @if($round_one_retires['match_'.$i] == $vs_match[0])
                                                                                                    
                                                                                                    <span class="text-danger text-center" style="font-size: 8px;font-weight: 500;">(R)</span>

                                                                                                @endif
                                                                                            @endif
                                                                                        @endif
                                                                                        <span
                                                                                            class="score">{{ ${"rou1_m" . $i . "_s1"}[0] }}
                                                                                            {{ ${"rou1_m" . $i . "_s2"}[0] }} {{ ${"rou1_m" . $i . "_s3"}[0] }}</span>
                                                                                    @elseif($round_one_status['match_'.$i] == 'Withdraw')
                                                                                        <span class="text-danger text-center" style="font-size: 8px;font-weight: 500;">(W)</span>
                                                                                        <span
                                                                                            class="score">&#8212;</span>
                                                                                    @elseif($round_one_status['match_'.$i] == 'Decided by Organisers')
                                                                                        <span class="text-danger text-center" style="font-size: 8px;font-weight: 500;">(D)</span>
                                                                                        <span
                                                                                            class="score">&#8212;</span>
                                                                                    @endif

                                                                                @else
                                                                                    @if($round_one_status['match_'.$i] == 'Retired')
                                                                                        @if($round_one_retires)
                                                                                            @if (array_key_exists('match_'.$i, $round_one_retires))
                                                                                                @if($round_one_retires['match_'.$i] == $vs_match[0])
                                                                                                    
                                                                                                    <span class="text-danger text-center" style="font-size: 8px;font-weight: 500;">(R)</span>

                                                                                                @endif
                                                                                            @endif
                                                                                        @endif
                                                                                        <span
                                                                                        class="score winnerclractive">{{ ${"rou1_m" . $i . "_s1"}[0] }}
                                                                                        {{ ${"rou1_m" . $i . "_s2"}[0] }} {{ ${"rou1_m" . $i . "_s3"}[0] }}</span>
                                                                                    @elseif($round_one_status['match_'.$i] == 'Withdraw')
                                                                                        <span
                                                                                        class="score winnerclractive">&#8212;</span>
                                                                                    @elseif($round_one_status['match_'.$i] == 'Decided by Organisers')
                                                                                        <span
                                                                                        class="score winnerclractive">&#8212;</span>
                                                                                    @endif
                                                                                        
                                                                                @endif

                                                                            </li>

                                                                            <span class="custooltiplefttext">{{ ${"rou_1_mat_" . $i}[0] }} 

                                                                                @if($round_one_status)
                                                                                    @if($round_one_status['match_'.$i] == 'Retired')
                                                                                        @if($round_one_retires)
                                                                                            @if (array_key_exists('match_'.$i, $round_one_retires))
                                                                                                @if($round_one_retires['match_'.$i] == $vs_match[0])
                                                                                                    
                                                                                                    ({{ $round_one_status['match_'.$i] }})

                                                                                                @endif
                                                                                            @endif
                                                                                        @endif
                                                                                    @else
                                                                                        @if (${"rou1_m" . $i . "_p1_total"} < ${"rou1_m" . $i . "_p2_total"}) 
                                                                                            ({{ $round_one_status['match_'.$i] }}) 
                                                                                        @endif
                                                                                    @endif
                                                                                @endif

                                                                            </span>

                                                                        </span>

                                                                        <span class="custooltipleft">
                                                                            <li
                                                                                class="team team-bottom @if (${"rou1_m" . $i . "_p2_total"} > ${"rou1_m" . $i . "_p1_total"}) winnerclractive @endif">
                                                                                {{ \Illuminate\Support\Str::limit(${"rou_1_mat_" . $i}[1], 100) }}

                                                                                @if (${"rou1_m" . $i . "_p1_total"} > ${"rou1_m" . $i . "_p2_total"})
                                                                                    @if($round_one_status['match_'.$i] == 'Retired')
                                                                                        @if($round_one_retires)
                                                                                            @if (array_key_exists('match_'.$i, $round_one_retires))
                                                                                                @if($round_one_retires['match_'.$i] == $vs_match[1])
                                                                                                    
                                                                                                    <span class="text-danger text-center" style="font-size: 8px;font-weight: 500;">(R)</span>

                                                                                                @endif
                                                                                            @endif
                                                                                        @endif
                                                                                        <span
                                                                                            class="score">{{ ${"rou1_m" . $i . "_s1"}[1] }}
                                                                                            {{ ${"rou1_m" . $i . "_s2"}[1] }} {{ ${"rou1_m" . $i . "_s3"}[1] }}</span>
                                                                                    @elseif($round_one_status['match_'.$i] == 'Withdraw')
                                                                                        <span class="text-danger text-center" style="font-size: 8px;font-weight: 500;">(W)</span>
                                                                                        <span
                                                                                            class="score">&#8212;</span>
                                                                                    @elseif($round_one_status['match_'.$i] == 'Decided by Organisers')
                                                                                        <span class="text-danger text-center" style="font-size: 8px;font-weight: 500;">(D)</span>
                                                                                        <span
                                                                                            class="score">&#8212;</span>
                                                                                    @endif

                                                                                @else
                                                                                    @if($round_one_status['match_'.$i] == 'Retired')
                                                                                        @if($round_one_retires)
                                                                                            @if (array_key_exists('match_'.$i, $round_one_retires))
                                                                                                @if($round_one_retires['match_'.$i] == $vs_match[1])
                                                                                                    
                                                                                                    <span class="text-danger text-center" style="font-size: 8px;font-weight: 500;">(R)</span>

                                                                                                @endif
                                                                                            @endif
                                                                                        @endif
                                                                                        <span
                                                                                        class="score winnerclractive">{{ ${"rou1_m" . $i . "_s1"}[1] }}
                                                                                        {{ ${"rou1_m" . $i . "_s2"}[1] }} {{ ${"rou1_m" . $i . "_s3"}[1] }}</span>
                                                                                    @elseif($round_one_status['match_'.$i] == 'Withdraw')
                                                                                        <span
                                                                                        class="score winnerclractive">&#8212;</span>
                                                                                    @elseif($round_one_status['match_'.$i] == 'Decided by Organisers')
                                                                                        <span
                                                                                        class="score winnerclractive">&#8212;</span>
                                                                                    @endif
                                                                                @endif

                                                                            </li>

                                                                            <span class="custooltiplefttext">{{ ${"rou_1_mat_" . $i}[1] }} 

                                                                                @if($round_one_status)
                                                                                    @if($round_one_status['match_'.$i] == 'Retired')
                                                                                        @if($round_one_retires)
                                                                                            @if (array_key_exists('match_'.$i, $round_one_retires))
                                                                                                @if($round_one_retires['match_'.$i] == $vs_match[1])
                                                                                                    
                                                                                                    ({{ $round_one_status['match_'.$i] }})

                                                                                                @endif
                                                                                            @endif
                                                                                        @endif
                                                                                    @else
                                                                                        @if (${"rou1_m" . $i . "_p1_total"} > ${"rou1_m" . $i . "_p2_total"}) 
                                                                                            ({{ $round_one_status['match_'.$i] }}) 
                                                                                        @endif
                                                                                    @endif
                                                                                @endif

                                                                            </span>

                                                                        </span>

                                                                    </ul>

                                                                @else
                                                                    @if (array_key_exists('match_'.$i, $round_one_results))
                                                                        <?php
                                                                            if (${"rou1_m" . $i . "_s1"}[0] > ${"rou1_m" . $i . "_s1"}[1] && ${"rou1_m" . $i . "_s2"}[0] > ${"rou1_m" . $i . "_s2"}[1]) {

                                                                                ${"rou1_m" . $i . "_p1_total"} = 2;
                                                                                ${"rou1_m" . $i . "_p2_total"} = 0;

                                                                            } elseif (${"rou1_m" . $i . "_s1"}[1] > ${"rou1_m" . $i . "_s1"}[0] && ${"rou1_m" . $i . "_s2"}[1] > ${"rou1_m" . $i . "_s2"}[0]) {

                                                                                ${"rou1_m" . $i . "_p1_total"} = 0;
                                                                                ${"rou1_m" . $i . "_p2_total"} = 2;

                                                                            } elseif (${"rou1_m" . $i . "_s1"}[0] > ${"rou1_m" . $i . "_s1"}[1] && ${"rou1_m" . $i . "_s2"}[0] < ${"rou1_m" . $i . "_s2"}[1] && ${"rou1_m" . $i . "_s3"}[0] > ${"rou1_m" . $i . "_s3"}[1]) {

                                                                                ${"rou1_m" . $i . "_p1_total"} = 2;
                                                                                ${"rou1_m" . $i . "_p2_total"} = 1;

                                                                            } elseif (${"rou1_m" . $i . "_s1"}[0] < ${"rou1_m" . $i . "_s1"}[1] && ${"rou1_m" . $i . "_s2"}[0] > ${"rou1_m" . $i . "_s2"}[1] && ${"rou1_m" . $i . "_s3"}[0] < ${"rou1_m" . $i . "_s3"}[1]) {

                                                                                ${"rou1_m" . $i . "_p1_total"} = 1;
                                                                                ${"rou1_m" . $i . "_p2_total"} = 2;

                                                                            } elseif (${"rou1_m" . $i . "_s1"}[0] < ${"rou1_m" . $i . "_s1"}[1] && ${"rou1_m" . $i . "_s2"}[0] > ${"rou1_m" . $i . "_s2"}[1] && ${"rou1_m" . $i . "_s3"}[0] > ${"rou1_m" . $i . "_s3"}[1]) {

                                                                                ${"rou1_m" . $i . "_p1_total"} = 2;
                                                                                ${"rou1_m" . $i . "_p2_total"} = 1;

                                                                            } elseif (${"rou1_m" . $i . "_s1"}[0] > ${"rou1_m" . $i . "_s1"}[1] && ${"rou1_m" . $i . "_s2"}[0] < ${"rou1_m" . $i . "_s2"}[1] && ${"rou1_m" . $i . "_s3"}[0] < ${"rou1_m" . $i . "_s3"}[1]) {

                                                                                ${"rou1_m" . $i . "_p1_total"} = 1;
                                                                                ${"rou1_m" . $i . "_p2_total"} = 2;

                                                                            }
                                                                        ?>
                                                                        

                                                                        <ul class="matchup">

                                                                            <span class="custooltipleft">
                                                                                <li
                                                                                    class="team team-top @if (${"rou1_m" . $i . "_p1_total"} > ${"rou1_m" . $i . "_p2_total"}) winnerclractive @endif">
                                                                                    {{ \Illuminate\Support\Str::limit(${"rou_1_mat_" . $i}[0], 100) }}

                                                                                    <span
                                                                                        class="score @if (${"rou1_m" . $i . "_p1_total"} > ${"rou1_m" . $i . "_p2_total"}) winnerclractive @endif">{{ ${"rou1_m" . $i . "_s1"}[0] }}
                                                                                        {{ ${"rou1_m" . $i . "_s2"}[0] }} {{ ${"rou1_m" . $i . "_s3"}[0] }}</span>

                                                                                </li>
                                                                                <span class="custooltiplefttext">{{ ${"rou_1_mat_" . $i}[0] }}</span>
                                                                            </span>

                                                                            <span class="custooltipleft">
                                                                                <li
                                                                                    class="team team-bottom @if (${"rou1_m" . $i . "_p2_total"} > ${"rou1_m" . $i . "_p1_total"}) winnerclractive @endif">
                                                                                    {{ \Illuminate\Support\Str::limit(${"rou_1_mat_" . $i}[1], 100) }}

                                                                                    <span
                                                                                        class="score @if (${"rou1_m" . $i . "_p2_total"} > ${"rou1_m" . $i . "_p1_total"}) winnerclractive @endif">{{ ${"rou1_m" . $i . "_s1"}[1] }}
                                                                                        {{ ${"rou1_m" . $i . "_s2"}[1] }} {{ ${"rou1_m" . $i . "_s3"}[1] }}</span>

                                                                                </li>
                                                                                <span class="custooltiplefttext">{{ ${"rou_1_mat_" . $i}[1] }}</span>
                                                                            </span>

                                                                        </ul>
                                                                    @else
                                                                        <ul class="matchup">

                                                                            <span class="custooltipleft">
                                                                                <li class="team team-top">
                                                                                    {{ \Illuminate\Support\Str::limit(${"rou_1_mat_" . $i}[0], 100) }}

                                                                                    <span class="score">N/A</span>

                                                                                </li>
                                                                                <span class="custooltiplefttext">{{ ${"rou_1_mat_" . $i}[0] }}</span>
                                                                            </span>

                                                                            <span class="custooltipleft">
                                                                                <li class="team team-bottom">
                                                                                    {{ \Illuminate\Support\Str::limit(${"rou_1_mat_" . $i}[1], 100) }}

                                                                                    <span class="score">N/A</span>

                                                                                </li>
                                                                                <span class="custooltiplefttext">{{ ${"rou_1_mat_" . $i}[1] }}</span>
                                                                            </span>

                                                                        </ul>
                                                                    @endif
                                                                @endif


                                                            @else
                                                                @if (array_key_exists('match_'.$i, $round_one_results))
                                                                    <?php
                                                                        if (${"rou1_m" . $i . "_s1"}[0] > ${"rou1_m" . $i . "_s1"}[1] && ${"rou1_m" . $i . "_s2"}[0] > ${"rou1_m" . $i . "_s2"}[1]) {

                                                                            ${"rou1_m" . $i . "_p1_total"} = 2;
                                                                            ${"rou1_m" . $i . "_p2_total"} = 0;

                                                                        } elseif (${"rou1_m" . $i . "_s1"}[1] > ${"rou1_m" . $i . "_s1"}[0] && ${"rou1_m" . $i . "_s2"}[1] > ${"rou1_m" . $i . "_s2"}[0]) {

                                                                            ${"rou1_m" . $i . "_p1_total"} = 0;
                                                                            ${"rou1_m" . $i . "_p2_total"} = 2;

                                                                        } elseif (${"rou1_m" . $i . "_s1"}[0] > ${"rou1_m" . $i . "_s1"}[1] && ${"rou1_m" . $i . "_s2"}[0] < ${"rou1_m" . $i . "_s2"}[1] && ${"rou1_m" . $i . "_s3"}[0] > ${"rou1_m" . $i . "_s3"}[1]) {

                                                                            ${"rou1_m" . $i . "_p1_total"} = 2;
                                                                            ${"rou1_m" . $i . "_p2_total"} = 1;

                                                                        } elseif (${"rou1_m" . $i . "_s1"}[0] < ${"rou1_m" . $i . "_s1"}[1] && ${"rou1_m" . $i . "_s2"}[0] > ${"rou1_m" . $i . "_s2"}[1] && ${"rou1_m" . $i . "_s3"}[0] < ${"rou1_m" . $i . "_s3"}[1]) {

                                                                            ${"rou1_m" . $i . "_p1_total"} = 1;
                                                                            ${"rou1_m" . $i . "_p2_total"} = 2;

                                                                        } elseif (${"rou1_m" . $i . "_s1"}[0] < ${"rou1_m" . $i . "_s1"}[1] && ${"rou1_m" . $i . "_s2"}[0] > ${"rou1_m" . $i . "_s2"}[1] && ${"rou1_m" . $i . "_s3"}[0] > ${"rou1_m" . $i . "_s3"}[1]) {

                                                                            ${"rou1_m" . $i . "_p1_total"} = 2;
                                                                            ${"rou1_m" . $i . "_p2_total"} = 1;

                                                                        } elseif (${"rou1_m" . $i . "_s1"}[0] > ${"rou1_m" . $i . "_s1"}[1] && ${"rou1_m" . $i . "_s2"}[0] < ${"rou1_m" . $i . "_s2"}[1] && ${"rou1_m" . $i . "_s3"}[0] < ${"rou1_m" . $i . "_s3"}[1]) {

                                                                            ${"rou1_m" . $i . "_p1_total"} = 1;
                                                                            ${"rou1_m" . $i . "_p2_total"} = 2;

                                                                        }
                                                                    ?>
                                                                    

                                                                    <ul class="matchup">

                                                                        <span class="custooltipleft">
                                                                            <li
                                                                                class="team team-top @if (${"rou1_m" . $i . "_p1_total"} > ${"rou1_m" . $i . "_p2_total"}) winnerclractive @endif">
                                                                                {{ \Illuminate\Support\Str::limit(${"rou_1_mat_" . $i}[0], 100) }}

                                                                                <span
                                                                                    class="score @if (${"rou1_m" . $i . "_p1_total"} > ${"rou1_m" . $i . "_p2_total"}) winnerclractive @endif">{{ ${"rou1_m" . $i . "_s1"}[0] }}
                                                                                    {{ ${"rou1_m" . $i . "_s2"}[0] }} {{ ${"rou1_m" . $i . "_s3"}[0] }}</span>

                                                                            </li>
                                                                            <span class="custooltiplefttext">{{ ${"rou_1_mat_" . $i}[0] }}</span>
                                                                        </span>

                                                                        <span class="custooltipleft">
                                                                            <li
                                                                                class="team team-bottom @if (${"rou1_m" . $i . "_p2_total"} > ${"rou1_m" . $i . "_p1_total"}) winnerclractive @endif">
                                                                                {{ \Illuminate\Support\Str::limit(${"rou_1_mat_" . $i}[1], 100) }}

                                                                                <span
                                                                                    class="score @if (${"rou1_m" . $i . "_p2_total"} > ${"rou1_m" . $i . "_p1_total"}) winnerclractive @endif">{{ ${"rou1_m" . $i . "_s1"}[1] }}
                                                                                    {{ ${"rou1_m" . $i . "_s2"}[1] }} {{ ${"rou1_m" . $i . "_s3"}[1] }}</span>

                                                                            </li>
                                                                            <span class="custooltiplefttext">{{ ${"rou_1_mat_" . $i}[1] }}</span>
                                                                        </span>

                                                                    </ul>
                                                                @else
                                                                    <ul class="matchup">

                                                                        <span class="custooltipleft">
                                                                            <li class="team team-top">
                                                                                {{ \Illuminate\Support\Str::limit(${"rou_1_mat_" . $i}[0], 100) }}

                                                                                <span class="score">N/A</span>

                                                                            </li>
                                                                            <span class="custooltiplefttext">{{ ${"rou_1_mat_" . $i}[0] }}</span>
                                                                        </span>

                                                                        <span class="custooltipleft">
                                                                            <li class="team team-bottom">
                                                                                {{ \Illuminate\Support\Str::limit(${"rou_1_mat_" . $i}[1], 100) }}

                                                                                <span class="score">N/A</span>

                                                                            </li>
                                                                            <span class="custooltiplefttext">{{ ${"rou_1_mat_" . $i}[1] }}</span>
                                                                        </span>

                                                                    </ul>
                                                                @endif
                                                            @endif

                                                        @else
                                                            @if (array_key_exists('match_'.$i, $round_one_results))
                                                                <?php
                                                                    if (${"rou1_m" . $i . "_s1"}[0] > ${"rou1_m" . $i . "_s1"}[1] && ${"rou1_m" . $i . "_s2"}[0] > ${"rou1_m" . $i . "_s2"}[1]) {

                                                                        ${"rou1_m" . $i . "_p1_total"} = 2;
                                                                        ${"rou1_m" . $i . "_p2_total"} = 0;

                                                                    } elseif (${"rou1_m" . $i . "_s1"}[1] > ${"rou1_m" . $i . "_s1"}[0] && ${"rou1_m" . $i . "_s2"}[1] > ${"rou1_m" . $i . "_s2"}[0]) {

                                                                        ${"rou1_m" . $i . "_p1_total"} = 0;
                                                                        ${"rou1_m" . $i . "_p2_total"} = 2;

                                                                    } elseif (${"rou1_m" . $i . "_s1"}[0] > ${"rou1_m" . $i . "_s1"}[1] && ${"rou1_m" . $i . "_s2"}[0] < ${"rou1_m" . $i . "_s2"}[1] && ${"rou1_m" . $i . "_s3"}[0] > ${"rou1_m" . $i . "_s3"}[1]) {

                                                                        ${"rou1_m" . $i . "_p1_total"} = 2;
                                                                        ${"rou1_m" . $i . "_p2_total"} = 1;

                                                                    } elseif (${"rou1_m" . $i . "_s1"}[0] < ${"rou1_m" . $i . "_s1"}[1] && ${"rou1_m" . $i . "_s2"}[0] > ${"rou1_m" . $i . "_s2"}[1] && ${"rou1_m" . $i . "_s3"}[0] < ${"rou1_m" . $i . "_s3"}[1]) {

                                                                        ${"rou1_m" . $i . "_p1_total"} = 1;
                                                                        ${"rou1_m" . $i . "_p2_total"} = 2;

                                                                    } elseif (${"rou1_m" . $i . "_s1"}[0] < ${"rou1_m" . $i . "_s1"}[1] && ${"rou1_m" . $i . "_s2"}[0] > ${"rou1_m" . $i . "_s2"}[1] && ${"rou1_m" . $i . "_s3"}[0] > ${"rou1_m" . $i . "_s3"}[1]) {

                                                                        ${"rou1_m" . $i . "_p1_total"} = 2;
                                                                        ${"rou1_m" . $i . "_p2_total"} = 1;

                                                                    } elseif (${"rou1_m" . $i . "_s1"}[0] > ${"rou1_m" . $i . "_s1"}[1] && ${"rou1_m" . $i . "_s2"}[0] < ${"rou1_m" . $i . "_s2"}[1] && ${"rou1_m" . $i . "_s3"}[0] < ${"rou1_m" . $i . "_s3"}[1]) {

                                                                        ${"rou1_m" . $i . "_p1_total"} = 1;
                                                                        ${"rou1_m" . $i . "_p2_total"} = 2;

                                                                    }
                                                                ?>
                                                                

                                                                <ul class="matchup">

                                                                    <span class="custooltipleft">
                                                                        <li
                                                                            class="team team-top @if (${"rou1_m" . $i . "_p1_total"} > ${"rou1_m" . $i . "_p2_total"}) winnerclractive @endif">
                                                                            {{ \Illuminate\Support\Str::limit(${"rou_1_mat_" . $i}[0], 100) }}

                                                                            <span
                                                                                class="score @if (${"rou1_m" . $i . "_p1_total"} > ${"rou1_m" . $i . "_p2_total"}) winnerclractive @endif">{{ ${"rou1_m" . $i . "_s1"}[0] }}
                                                                                {{ ${"rou1_m" . $i . "_s2"}[0] }} {{ ${"rou1_m" . $i . "_s3"}[0] }}</span>

                                                                        </li>
                                                                        <span class="custooltiplefttext">{{ ${"rou_1_mat_" . $i}[0] }}</span>
                                                                    </span>

                                                                    <span class="custooltipleft">
                                                                        <li
                                                                            class="team team-bottom @if (${"rou1_m" . $i . "_p2_total"} > ${"rou1_m" . $i . "_p1_total"}) winnerclractive @endif">
                                                                            {{ \Illuminate\Support\Str::limit(${"rou_1_mat_" . $i}[1], 100) }}

                                                                            <span
                                                                                class="score @if (${"rou1_m" . $i . "_p2_total"} > ${"rou1_m" . $i . "_p1_total"}) winnerclractive @endif">{{ ${"rou1_m" . $i . "_s1"}[1] }}
                                                                                {{ ${"rou1_m" . $i . "_s2"}[1] }} {{ ${"rou1_m" . $i . "_s3"}[1] }}</span>

                                                                        </li>
                                                                        <span class="custooltiplefttext">{{ ${"rou_1_mat_" . $i}[1] }}</span>
                                                                    </span>

                                                                </ul>
                                                                
                                                            @else
                                                                <ul class="matchup">

                                                                    <span class="custooltipleft">
                                                                        <li class="team team-top">
                                                                            {{ \Illuminate\Support\Str::limit(${"rou_1_mat_" . $i}[0], 100) }}

                                                                            <span class="score">N/A</span>

                                                                        </li>
                                                                        <span class="custooltiplefttext">{{ ${"rou_1_mat_" . $i}[0] }}</span>
                                                                    </span>

                                                                    <span class="custooltipleft">
                                                                        <li class="team team-bottom">
                                                                            {{ \Illuminate\Support\Str::limit(${"rou_1_mat_" . $i}[1], 100) }}

                                                                            <span class="score">N/A</span>

                                                                        </li>
                                                                        <span class="custooltiplefttext">{{ ${"rou_1_mat_" . $i}[1] }}</span>
                                                                    </span>

                                                                </ul>
                                                            @endif
                                                        @endif

                                                    @else
                                                        <ul class="matchup">

                                                            <span class="custooltipleft">
                                                                <li class="team team-top">
                                                                    {{ \Illuminate\Support\Str::limit(${"rou_1_mat_" . $i}[0], 100) }}

                                                                    <span class="score">N/A</span>

                                                                </li>
                                                                <span class="custooltiplefttext">{{ ${"rou_1_mat_" . $i}[0] }}</span>
                                                            </span>

                                                            <span class="custooltipleft">
                                                                <li class="team team-bottom">
                                                                    {{ \Illuminate\Support\Str::limit(${"rou_1_mat_" . $i}[1], 100) }}

                                                                    <span class="score">N/A</span>

                                                                </li>
                                                                <span class="custooltiplefttext">{{ ${"rou_1_mat_" . $i}[1] }}</span>
                                                            </span>
                                                        </ul>
                                                    @endif

                                                @else
                                                    <ul class="matchup">

                                                        <li class="team team-top">n/a<span class="score">N/A</span></li>
                                                        <li class="team team-bottom">n/a<span class="score">N/A</span></li>
                                                    </ul>
                                                @endif
                                                
                                            @else
                                                
                                                <ul class="matchup">
                                                    
                                                    <li class="team team-top"><span class="score"></span></li>
                                                    <li class="team team-bottom"><span class="score"></span></li>
                                                </ul>
                                                
                                            @endif

                                        @endif

                                    @endfor

                                </div>                                
                            @else
                                @if($tournament->round_one_matches)
                                    
                                    <div class="round round-one current">
                                        <div class="round-details">Round 1<br/><span class="date">{{ $strt_r1[0] }} - {{ $endd_r1[0] }}</span>
                                        </div>
                                        
                                        @for($i = 3; $i < 5; $i++)
                                            @if (array_key_exists('match_'.$i, $round_one_matches))
                                                    
                                                <?php 
                                                    $get_matches = $round_one_matches['match_'.$i];
                                                    $vs_match = explode(" VS ", $get_matches);
                                                ?>

                                                @if ($tournament->round_one_results)
                                                    
                                                    @if($round_one_status)
                                                        @if (array_key_exists('match_'.$i, $round_one_status))
                                                            @if($round_one_status['match_'.$i])
                                                                <?php
                                                                    if (${"rou1_m" . $i . "_s1"}[0] > ${"rou1_m" . $i . "_s1"}[1] && ${"rou1_m" . $i . "_s2"}[0] > ${"rou1_m" . $i . "_s2"}[1]) {

                                                                        ${"rou1_m" . $i . "_p1_total"} = 2;
                                                                        ${"rou1_m" . $i . "_p2_total"} = 0;

                                                                    } elseif (${"rou1_m" . $i . "_s1"}[1] > ${"rou1_m" . $i . "_s1"}[0] && ${"rou1_m" . $i . "_s2"}[1] > ${"rou1_m" . $i . "_s2"}[0]) {

                                                                        ${"rou1_m" . $i . "_p1_total"} = 0;
                                                                        ${"rou1_m" . $i . "_p2_total"} = 2;

                                                                    } elseif (${"rou1_m" . $i . "_s1"}[0] > ${"rou1_m" . $i . "_s1"}[1] && ${"rou1_m" . $i . "_s2"}[0] < ${"rou1_m" . $i . "_s2"}[1] && ${"rou1_m" . $i . "_s3"}[0] > ${"rou1_m" . $i . "_s3"}[1]) {

                                                                        ${"rou1_m" . $i . "_p1_total"} = 2;
                                                                        ${"rou1_m" . $i . "_p2_total"} = 1;

                                                                    } elseif (${"rou1_m" . $i . "_s1"}[0] < ${"rou1_m" . $i . "_s1"}[1] && ${"rou1_m" . $i . "_s2"}[0] > ${"rou1_m" . $i . "_s2"}[1] && ${"rou1_m" . $i . "_s3"}[0] < ${"rou1_m" . $i . "_s3"}[1]) {

                                                                        ${"rou1_m" . $i . "_p1_total"} = 1;
                                                                        ${"rou1_m" . $i . "_p2_total"} = 2;

                                                                    } elseif (${"rou1_m" . $i . "_s1"}[0] < ${"rou1_m" . $i . "_s1"}[1] && ${"rou1_m" . $i . "_s2"}[0] > ${"rou1_m" . $i . "_s2"}[1] && ${"rou1_m" . $i . "_s3"}[0] > ${"rou1_m" . $i . "_s3"}[1]) {

                                                                        ${"rou1_m" . $i . "_p1_total"} = 2;
                                                                        ${"rou1_m" . $i . "_p2_total"} = 1;

                                                                    } elseif (${"rou1_m" . $i . "_s1"}[0] > ${"rou1_m" . $i . "_s1"}[1] && ${"rou1_m" . $i . "_s2"}[0] < ${"rou1_m" . $i . "_s2"}[1] && ${"rou1_m" . $i . "_s3"}[0] < ${"rou1_m" . $i . "_s3"}[1]) {

                                                                        ${"rou1_m" . $i . "_p1_total"} = 1;
                                                                        ${"rou1_m" . $i . "_p2_total"} = 2;

                                                                    }
                                                                ?>


                                                                <ul class="matchup">

                                                                    <span class="custooltipleft">
                                                                        <li
                                                                            class="team team-top @if (${"rou1_m" . $i . "_p1_total"} > ${"rou1_m" . $i . "_p2_total"}) winnerclractive @endif">
                                                                            {{ \Illuminate\Support\Str::limit(${"rou_1_mat_" . $i}[0], 100) }}

                                                                            @if (${"rou1_m" . $i . "_p1_total"} < ${"rou1_m" . $i . "_p2_total"})
                                                                                @if($round_one_status['match_'.$i] == 'Retired')
                                                                                    @if($round_one_retires)
                                                                                        @if (array_key_exists('match_'.$i, $round_one_retires))
                                                                                            @if($round_one_retires['match_'.$i] == $vs_match[0])
                                                                                                
                                                                                                <span class="text-danger text-center" style="font-size: 8px;font-weight: 500;">(R)</span>

                                                                                            @endif
                                                                                        @endif
                                                                                    @endif
                                                                                    <span
                                                                                        class="score">{{ ${"rou1_m" . $i . "_s1"}[0] }}
                                                                                        {{ ${"rou1_m" . $i . "_s2"}[0] }} {{ ${"rou1_m" . $i . "_s3"}[0] }}</span>
                                                                                @elseif($round_one_status['match_'.$i] == 'Withdraw')
                                                                                    <span class="text-danger text-center" style="font-size: 8px;font-weight: 500;">(W)</span>
                                                                                    <span
                                                                                        class="score">&#8212;</span>
                                                                                @elseif($round_one_status['match_'.$i] == 'Decided by Organisers')
                                                                                    <span class="text-danger text-center" style="font-size: 8px;font-weight: 500;">(D)</span>
                                                                                    <span
                                                                                        class="score">&#8212;</span>
                                                                                @endif

                                                                            @else
                                                                                @if($round_one_status['match_'.$i] == 'Retired')
                                                                                    @if($round_one_retires)
                                                                                        @if (array_key_exists('match_'.$i, $round_one_retires))
                                                                                            @if($round_one_retires['match_'.$i] == $vs_match[0])
                                                                                                
                                                                                                <span class="text-danger text-center" style="font-size: 8px;font-weight: 500;">(R)</span>

                                                                                            @endif
                                                                                        @endif
                                                                                    @endif
                                                                                    <span
                                                                                    class="score winnerclractive">{{ ${"rou1_m" . $i . "_s1"}[0] }}
                                                                                    {{ ${"rou1_m" . $i . "_s2"}[0] }} {{ ${"rou1_m" . $i . "_s3"}[0] }}</span>
                                                                                @elseif($round_one_status['match_'.$i] == 'Withdraw')
                                                                                    <span
                                                                                    class="score winnerclractive">&#8212;</span>
                                                                                @elseif($round_one_status['match_'.$i] == 'Decided by Organisers')
                                                                                    <span
                                                                                    class="score winnerclractive">&#8212;</span>
                                                                                @endif
                                                                                    
                                                                            @endif

                                                                        </li>

                                                                        <span class="custooltiplefttext">{{ ${"rou_1_mat_" . $i}[0] }} 

                                                                            @if($round_one_status)
                                                                                @if($round_one_status['match_'.$i] == 'Retired')
                                                                                    @if($round_one_retires)
                                                                                        @if (array_key_exists('match_'.$i, $round_one_retires))
                                                                                            @if($round_one_retires['match_'.$i] == $vs_match[0])
                                                                                                
                                                                                                ({{ $round_one_status['match_'.$i] }})

                                                                                            @endif
                                                                                        @endif
                                                                                    @endif
                                                                                @else
                                                                                    @if (${"rou1_m" . $i . "_p1_total"} < ${"rou1_m" . $i . "_p2_total"}) 
                                                                                        ({{ $round_one_status['match_'.$i] }}) 
                                                                                    @endif
                                                                                @endif
                                                                            @endif

                                                                        </span>

                                                                    </span>

                                                                    <span class="custooltipleft">
                                                                        <li
                                                                            class="team team-bottom @if (${"rou1_m" . $i . "_p2_total"} > ${"rou1_m" . $i . "_p1_total"}) winnerclractive @endif">
                                                                            {{ \Illuminate\Support\Str::limit(${"rou_1_mat_" . $i}[1], 100) }}

                                                                            @if (${"rou1_m" . $i . "_p1_total"} > ${"rou1_m" . $i . "_p2_total"})
                                                                                @if($round_one_status['match_'.$i] == 'Retired')
                                                                                    @if($round_one_retires)
                                                                                        @if (array_key_exists('match_'.$i, $round_one_retires))
                                                                                            @if($round_one_retires['match_'.$i] == $vs_match[1])
                                                                                                
                                                                                                <span class="text-danger text-center" style="font-size: 8px;font-weight: 500;">(R)</span>

                                                                                            @endif
                                                                                        @endif
                                                                                    @endif
                                                                                    <span
                                                                                        class="score">{{ ${"rou1_m" . $i . "_s1"}[1] }}
                                                                                        {{ ${"rou1_m" . $i . "_s2"}[1] }} {{ ${"rou1_m" . $i . "_s3"}[1] }}</span>
                                                                                @elseif($round_one_status['match_'.$i] == 'Withdraw')
                                                                                    <span class="text-danger text-center" style="font-size: 8px;font-weight: 500;">(W)</span>
                                                                                    <span
                                                                                        class="score">&#8212;</span>
                                                                                @elseif($round_one_status['match_'.$i] == 'Decided by Organisers')
                                                                                    <span class="text-danger text-center" style="font-size: 8px;font-weight: 500;">(D)</span>
                                                                                    <span
                                                                                        class="score">&#8212;</span>
                                                                                @endif

                                                                            @else
                                                                                @if($round_one_status['match_'.$i] == 'Retired')
                                                                                    @if($round_one_retires)
                                                                                        @if (array_key_exists('match_'.$i, $round_one_retires))
                                                                                            @if($round_one_retires['match_'.$i] == $vs_match[1])
                                                                                                
                                                                                                <span class="text-danger text-center" style="font-size: 8px;font-weight: 500;">(R)</span>

                                                                                            @endif
                                                                                        @endif
                                                                                    @endif
                                                                                    <span
                                                                                    class="score winnerclractive">{{ ${"rou1_m" . $i . "_s1"}[1] }}
                                                                                    {{ ${"rou1_m" . $i . "_s2"}[1] }} {{ ${"rou1_m" . $i . "_s3"}[1] }}</span>
                                                                                @elseif($round_one_status['match_'.$i] == 'Withdraw')
                                                                                    <span
                                                                                    class="score winnerclractive">&#8212;</span>
                                                                                @elseif($round_one_status['match_'.$i] == 'Decided by Organisers')
                                                                                    <span
                                                                                    class="score winnerclractive">&#8212;</span>
                                                                                @endif
                                                                            @endif

                                                                        </li>

                                                                        <span class="custooltiplefttext">{{ ${"rou_1_mat_" . $i}[1] }} 

                                                                            @if($round_one_status)
                                                                                @if($round_one_status['match_'.$i] == 'Retired')
                                                                                    @if($round_one_retires)
                                                                                        @if (array_key_exists('match_'.$i, $round_one_retires))
                                                                                            @if($round_one_retires['match_'.$i] == $vs_match[1])
                                                                                                
                                                                                                ({{ $round_one_status['match_'.$i] }})

                                                                                            @endif
                                                                                        @endif
                                                                                    @endif
                                                                                @else
                                                                                    @if (${"rou1_m" . $i . "_p1_total"} > ${"rou1_m" . $i . "_p2_total"}) 
                                                                                        ({{ $round_one_status['match_'.$i] }}) 
                                                                                    @endif
                                                                                @endif
                                                                            @endif

                                                                        </span>

                                                                    </span>

                                                                </ul>

                                                            @else
                                                                @if (array_key_exists('match_'.$i, $round_one_results))
                                                                    <?php
                                                                        if (${"rou1_m" . $i . "_s1"}[0] > ${"rou1_m" . $i . "_s1"}[1] && ${"rou1_m" . $i . "_s2"}[0] > ${"rou1_m" . $i . "_s2"}[1]) {

                                                                            ${"rou1_m" . $i . "_p1_total"} = 2;
                                                                            ${"rou1_m" . $i . "_p2_total"} = 0;

                                                                        } elseif (${"rou1_m" . $i . "_s1"}[1] > ${"rou1_m" . $i . "_s1"}[0] && ${"rou1_m" . $i . "_s2"}[1] > ${"rou1_m" . $i . "_s2"}[0]) {

                                                                            ${"rou1_m" . $i . "_p1_total"} = 0;
                                                                            ${"rou1_m" . $i . "_p2_total"} = 2;

                                                                        } elseif (${"rou1_m" . $i . "_s1"}[0] > ${"rou1_m" . $i . "_s1"}[1] && ${"rou1_m" . $i . "_s2"}[0] < ${"rou1_m" . $i . "_s2"}[1] && ${"rou1_m" . $i . "_s3"}[0] > ${"rou1_m" . $i . "_s3"}[1]) {

                                                                            ${"rou1_m" . $i . "_p1_total"} = 2;
                                                                            ${"rou1_m" . $i . "_p2_total"} = 1;

                                                                        } elseif (${"rou1_m" . $i . "_s1"}[0] < ${"rou1_m" . $i . "_s1"}[1] && ${"rou1_m" . $i . "_s2"}[0] > ${"rou1_m" . $i . "_s2"}[1] && ${"rou1_m" . $i . "_s3"}[0] < ${"rou1_m" . $i . "_s3"}[1]) {

                                                                            ${"rou1_m" . $i . "_p1_total"} = 1;
                                                                            ${"rou1_m" . $i . "_p2_total"} = 2;

                                                                        } elseif (${"rou1_m" . $i . "_s1"}[0] < ${"rou1_m" . $i . "_s1"}[1] && ${"rou1_m" . $i . "_s2"}[0] > ${"rou1_m" . $i . "_s2"}[1] && ${"rou1_m" . $i . "_s3"}[0] > ${"rou1_m" . $i . "_s3"}[1]) {

                                                                            ${"rou1_m" . $i . "_p1_total"} = 2;
                                                                            ${"rou1_m" . $i . "_p2_total"} = 1;

                                                                        } elseif (${"rou1_m" . $i . "_s1"}[0] > ${"rou1_m" . $i . "_s1"}[1] && ${"rou1_m" . $i . "_s2"}[0] < ${"rou1_m" . $i . "_s2"}[1] && ${"rou1_m" . $i . "_s3"}[0] < ${"rou1_m" . $i . "_s3"}[1]) {

                                                                            ${"rou1_m" . $i . "_p1_total"} = 1;
                                                                            ${"rou1_m" . $i . "_p2_total"} = 2;

                                                                        }
                                                                    ?>
                                                                    

                                                                    <ul class="matchup">

                                                                        <span class="custooltipleft">
                                                                            <li
                                                                                class="team team-top @if (${"rou1_m" . $i . "_p1_total"} > ${"rou1_m" . $i . "_p2_total"}) winnerclractive @endif">
                                                                                {{ \Illuminate\Support\Str::limit(${"rou_1_mat_" . $i}[0], 100) }}

                                                                                <span
                                                                                    class="score @if (${"rou1_m" . $i . "_p1_total"} > ${"rou1_m" . $i . "_p2_total"}) winnerclractive @endif">{{ ${"rou1_m" . $i . "_s1"}[0] }}
                                                                                    {{ ${"rou1_m" . $i . "_s2"}[0] }} {{ ${"rou1_m" . $i . "_s3"}[0] }}</span>

                                                                            </li>
                                                                            <span class="custooltiplefttext">{{ ${"rou_1_mat_" . $i}[0] }}</span>
                                                                        </span>

                                                                        <span class="custooltipleft">
                                                                            <li
                                                                                class="team team-bottom @if (${"rou1_m" . $i . "_p2_total"} > ${"rou1_m" . $i . "_p1_total"}) winnerclractive @endif">
                                                                                {{ \Illuminate\Support\Str::limit(${"rou_1_mat_" . $i}[1], 100) }}

                                                                                <span
                                                                                    class="score @if (${"rou1_m" . $i . "_p2_total"} > ${"rou1_m" . $i . "_p1_total"}) winnerclractive @endif">{{ ${"rou1_m" . $i . "_s1"}[1] }}
                                                                                    {{ ${"rou1_m" . $i . "_s2"}[1] }} {{ ${"rou1_m" . $i . "_s3"}[1] }}</span>

                                                                            </li>
                                                                            <span class="custooltiplefttext">{{ ${"rou_1_mat_" . $i}[1] }}</span>
                                                                        </span>

                                                                    </ul>
                                                                @else
                                                                    <ul class="matchup">

                                                                        <span class="custooltipleft">
                                                                            <li class="team team-top">
                                                                                {{ \Illuminate\Support\Str::limit(${"rou_1_mat_" . $i}[0], 100) }}

                                                                                <span class="score">N/A</span>

                                                                            </li>
                                                                            <span class="custooltiplefttext">{{ ${"rou_1_mat_" . $i}[0] }}</span>
                                                                        </span>

                                                                        <span class="custooltipleft">
                                                                            <li class="team team-bottom">
                                                                                {{ \Illuminate\Support\Str::limit(${"rou_1_mat_" . $i}[1], 100) }}

                                                                                <span class="score">N/A</span>

                                                                            </li>
                                                                            <span class="custooltiplefttext">{{ ${"rou_1_mat_" . $i}[1] }}</span>
                                                                        </span>

                                                                    </ul>
                                                                @endif
                                                            @endif


                                                        @else
                                                            @if (array_key_exists('match_'.$i, $round_one_results))
                                                                <?php
                                                                    if (${"rou1_m" . $i . "_s1"}[0] > ${"rou1_m" . $i . "_s1"}[1] && ${"rou1_m" . $i . "_s2"}[0] > ${"rou1_m" . $i . "_s2"}[1]) {

                                                                        ${"rou1_m" . $i . "_p1_total"} = 2;
                                                                        ${"rou1_m" . $i . "_p2_total"} = 0;

                                                                    } elseif (${"rou1_m" . $i . "_s1"}[1] > ${"rou1_m" . $i . "_s1"}[0] && ${"rou1_m" . $i . "_s2"}[1] > ${"rou1_m" . $i . "_s2"}[0]) {

                                                                        ${"rou1_m" . $i . "_p1_total"} = 0;
                                                                        ${"rou1_m" . $i . "_p2_total"} = 2;

                                                                    } elseif (${"rou1_m" . $i . "_s1"}[0] > ${"rou1_m" . $i . "_s1"}[1] && ${"rou1_m" . $i . "_s2"}[0] < ${"rou1_m" . $i . "_s2"}[1] && ${"rou1_m" . $i . "_s3"}[0] > ${"rou1_m" . $i . "_s3"}[1]) {

                                                                        ${"rou1_m" . $i . "_p1_total"} = 2;
                                                                        ${"rou1_m" . $i . "_p2_total"} = 1;

                                                                    } elseif (${"rou1_m" . $i . "_s1"}[0] < ${"rou1_m" . $i . "_s1"}[1] && ${"rou1_m" . $i . "_s2"}[0] > ${"rou1_m" . $i . "_s2"}[1] && ${"rou1_m" . $i . "_s3"}[0] < ${"rou1_m" . $i . "_s3"}[1]) {

                                                                        ${"rou1_m" . $i . "_p1_total"} = 1;
                                                                        ${"rou1_m" . $i . "_p2_total"} = 2;

                                                                    } elseif (${"rou1_m" . $i . "_s1"}[0] < ${"rou1_m" . $i . "_s1"}[1] && ${"rou1_m" . $i . "_s2"}[0] > ${"rou1_m" . $i . "_s2"}[1] && ${"rou1_m" . $i . "_s3"}[0] > ${"rou1_m" . $i . "_s3"}[1]) {

                                                                        ${"rou1_m" . $i . "_p1_total"} = 2;
                                                                        ${"rou1_m" . $i . "_p2_total"} = 1;

                                                                    } elseif (${"rou1_m" . $i . "_s1"}[0] > ${"rou1_m" . $i . "_s1"}[1] && ${"rou1_m" . $i . "_s2"}[0] < ${"rou1_m" . $i . "_s2"}[1] && ${"rou1_m" . $i . "_s3"}[0] < ${"rou1_m" . $i . "_s3"}[1]) {

                                                                        ${"rou1_m" . $i . "_p1_total"} = 1;
                                                                        ${"rou1_m" . $i . "_p2_total"} = 2;

                                                                    }
                                                                ?>
                                                                

                                                                <ul class="matchup">

                                                                    <span class="custooltipleft">
                                                                        <li
                                                                            class="team team-top @if (${"rou1_m" . $i . "_p1_total"} > ${"rou1_m" . $i . "_p2_total"}) winnerclractive @endif">
                                                                            {{ \Illuminate\Support\Str::limit(${"rou_1_mat_" . $i}[0], 100) }}

                                                                            <span
                                                                                class="score @if (${"rou1_m" . $i . "_p1_total"} > ${"rou1_m" . $i . "_p2_total"}) winnerclractive @endif">{{ ${"rou1_m" . $i . "_s1"}[0] }}
                                                                                {{ ${"rou1_m" . $i . "_s2"}[0] }} {{ ${"rou1_m" . $i . "_s3"}[0] }}</span>

                                                                        </li>
                                                                        <span class="custooltiplefttext">{{ ${"rou_1_mat_" . $i}[0] }}</span>
                                                                    </span>

                                                                    <span class="custooltipleft">
                                                                        <li
                                                                            class="team team-bottom @if (${"rou1_m" . $i . "_p2_total"} > ${"rou1_m" . $i . "_p1_total"}) winnerclractive @endif">
                                                                            {{ \Illuminate\Support\Str::limit(${"rou_1_mat_" . $i}[1], 100) }}

                                                                            <span
                                                                                class="score @if (${"rou1_m" . $i . "_p2_total"} > ${"rou1_m" . $i . "_p1_total"}) winnerclractive @endif">{{ ${"rou1_m" . $i . "_s1"}[1] }}
                                                                                {{ ${"rou1_m" . $i . "_s2"}[1] }} {{ ${"rou1_m" . $i . "_s3"}[1] }}</span>

                                                                        </li>
                                                                        <span class="custooltiplefttext">{{ ${"rou_1_mat_" . $i}[1] }}</span>
                                                                    </span>

                                                                </ul>
                                                            @else
                                                                <ul class="matchup">

                                                                    <span class="custooltipleft">
                                                                        <li class="team team-top">
                                                                            {{ \Illuminate\Support\Str::limit(${"rou_1_mat_" . $i}[0], 100) }}

                                                                            <span class="score">N/A</span>

                                                                        </li>
                                                                        <span class="custooltiplefttext">{{ ${"rou_1_mat_" . $i}[0] }}</span>
                                                                    </span>

                                                                    <span class="custooltipleft">
                                                                        <li class="team team-bottom">
                                                                            {{ \Illuminate\Support\Str::limit(${"rou_1_mat_" . $i}[1], 100) }}

                                                                            <span class="score">N/A</span>

                                                                        </li>
                                                                        <span class="custooltiplefttext">{{ ${"rou_1_mat_" . $i}[1] }}</span>
                                                                    </span>

                                                                </ul>
                                                            @endif
                                                        @endif

                                                    @else
                                                        @if (array_key_exists('match_'.$i, $round_one_results))
                                                            <?php
                                                                if (${"rou1_m" . $i . "_s1"}[0] > ${"rou1_m" . $i . "_s1"}[1] && ${"rou1_m" . $i . "_s2"}[0] > ${"rou1_m" . $i . "_s2"}[1]) {

                                                                    ${"rou1_m" . $i . "_p1_total"} = 2;
                                                                    ${"rou1_m" . $i . "_p2_total"} = 0;

                                                                } elseif (${"rou1_m" . $i . "_s1"}[1] > ${"rou1_m" . $i . "_s1"}[0] && ${"rou1_m" . $i . "_s2"}[1] > ${"rou1_m" . $i . "_s2"}[0]) {

                                                                    ${"rou1_m" . $i . "_p1_total"} = 0;
                                                                    ${"rou1_m" . $i . "_p2_total"} = 2;

                                                                } elseif (${"rou1_m" . $i . "_s1"}[0] > ${"rou1_m" . $i . "_s1"}[1] && ${"rou1_m" . $i . "_s2"}[0] < ${"rou1_m" . $i . "_s2"}[1] && ${"rou1_m" . $i . "_s3"}[0] > ${"rou1_m" . $i . "_s3"}[1]) {

                                                                    ${"rou1_m" . $i . "_p1_total"} = 2;
                                                                    ${"rou1_m" . $i . "_p2_total"} = 1;

                                                                } elseif (${"rou1_m" . $i . "_s1"}[0] < ${"rou1_m" . $i . "_s1"}[1] && ${"rou1_m" . $i . "_s2"}[0] > ${"rou1_m" . $i . "_s2"}[1] && ${"rou1_m" . $i . "_s3"}[0] < ${"rou1_m" . $i . "_s3"}[1]) {

                                                                    ${"rou1_m" . $i . "_p1_total"} = 1;
                                                                    ${"rou1_m" . $i . "_p2_total"} = 2;

                                                                } elseif (${"rou1_m" . $i . "_s1"}[0] < ${"rou1_m" . $i . "_s1"}[1] && ${"rou1_m" . $i . "_s2"}[0] > ${"rou1_m" . $i . "_s2"}[1] && ${"rou1_m" . $i . "_s3"}[0] > ${"rou1_m" . $i . "_s3"}[1]) {

                                                                    ${"rou1_m" . $i . "_p1_total"} = 2;
                                                                    ${"rou1_m" . $i . "_p2_total"} = 1;

                                                                } elseif (${"rou1_m" . $i . "_s1"}[0] > ${"rou1_m" . $i . "_s1"}[1] && ${"rou1_m" . $i . "_s2"}[0] < ${"rou1_m" . $i . "_s2"}[1] && ${"rou1_m" . $i . "_s3"}[0] < ${"rou1_m" . $i . "_s3"}[1]) {

                                                                    ${"rou1_m" . $i . "_p1_total"} = 1;
                                                                    ${"rou1_m" . $i . "_p2_total"} = 2;

                                                                }
                                                            ?>
                                                            

                                                            <ul class="matchup">

                                                                <span class="custooltipleft">
                                                                    <li
                                                                        class="team team-top @if (${"rou1_m" . $i . "_p1_total"} > ${"rou1_m" . $i . "_p2_total"}) winnerclractive @endif">
                                                                        {{ \Illuminate\Support\Str::limit(${"rou_1_mat_" . $i}[0], 100) }}

                                                                        <span
                                                                            class="score @if (${"rou1_m" . $i . "_p1_total"} > ${"rou1_m" . $i . "_p2_total"}) winnerclractive @endif">{{ ${"rou1_m" . $i . "_s1"}[0] }}
                                                                            {{ ${"rou1_m" . $i . "_s2"}[0] }} {{ ${"rou1_m" . $i . "_s3"}[0] }}</span>

                                                                    </li>
                                                                    <span class="custooltiplefttext">{{ ${"rou_1_mat_" . $i}[0] }}</span>
                                                                </span>

                                                                <span class="custooltipleft">
                                                                    <li
                                                                        class="team team-bottom @if (${"rou1_m" . $i . "_p2_total"} > ${"rou1_m" . $i . "_p1_total"}) winnerclractive @endif">
                                                                        {{ \Illuminate\Support\Str::limit(${"rou_1_mat_" . $i}[1], 100) }}

                                                                        <span
                                                                            class="score @if (${"rou1_m" . $i . "_p2_total"} > ${"rou1_m" . $i . "_p1_total"}) winnerclractive @endif">{{ ${"rou1_m" . $i . "_s1"}[1] }}
                                                                            {{ ${"rou1_m" . $i . "_s2"}[1] }} {{ ${"rou1_m" . $i . "_s3"}[1] }}</span>

                                                                    </li>
                                                                    <span class="custooltiplefttext">{{ ${"rou_1_mat_" . $i}[1] }}</span>
                                                                </span>

                                                            </ul>
                                                        @else
                                                            <ul class="matchup">

                                                                <span class="custooltipleft">
                                                                    <li class="team team-top">
                                                                        {{ \Illuminate\Support\Str::limit(${"rou_1_mat_" . $i}[0], 100) }}

                                                                        <span class="score">N/A</span>

                                                                    </li>
                                                                    <span class="custooltiplefttext">{{ ${"rou_1_mat_" . $i}[0] }}</span>
                                                                </span>

                                                                <span class="custooltipleft">
                                                                    <li class="team team-bottom">
                                                                        {{ \Illuminate\Support\Str::limit(${"rou_1_mat_" . $i}[1], 100) }}

                                                                        <span class="score">N/A</span>

                                                                    </li>
                                                                    <span class="custooltiplefttext">{{ ${"rou_1_mat_" . $i}[1] }}</span>
                                                                </span>

                                                            </ul>
                                                        @endif
                                                    @endif

                                                @else
                                                    <ul class="matchup">

                                                        <span class="custooltipleft">
                                                            <li class="team team-top">
                                                                {{ \Illuminate\Support\Str::limit(${"rou_1_mat_" . $i}[0], 100) }}

                                                                <span class="score">N/A</span>

                                                            </li>
                                                            <span class="custooltiplefttext">{{ ${"rou_1_mat_" . $i}[0] }}</span>
                                                        </span>

                                                        <span class="custooltipleft">
                                                            <li class="team team-bottom">
                                                                {{ \Illuminate\Support\Str::limit(${"rou_1_mat_" . $i}[1], 100) }}

                                                                <span class="score">N/A</span>

                                                            </li>
                                                            <span class="custooltiplefttext">{{ ${"rou_1_mat_" . $i}[1] }}</span>
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

                                @else
                                    <div class="round round-one">
                                        <div class="round-details">Round 1<br/><span class="date">@if($t_d_rou1) {{ $strt_r1[0] }} - {{ $endd_r1[0] }} @else N/A @endif</span></div>

                                        @for($i = 3; $i < 5; $i++)
                                            <ul class="matchup">
                                                
                                                <li class="team team-top"><span class="score"></span></li>
                                                <li class="team team-bottom"><span class="score"></span></li>
                                            </ul>
                                        @endfor

                                    </div>
                                @endif
                            @endif

                        </div>
                    </div>
                </section>
            @endif
            
            {{-- Group End --}}

        </div>
    </div>

@endsection


@section('styles')
    <style type="text/css">
        .select2-container--default .select2-results>.select2-results__options{
            max-height: 550px !important;
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
        display: -webkit-box !important;
        display: -moz-box !important;
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
        width: 65%;
        -webkit-flex-direction: row;
        -moz-flex-direction: row;
        flex-direction: row;
        }
        .splitgrp {
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
        width: 22%;
        -webkit-flex-direction: row;
        flex-direction: row;
        -webkit-align-self: center;
        align-self: center;
        margin-top: -1px;
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
            width: 35%;
        }
        .split {
            width: 110%;
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

        @media screen and (max-width: 1175px) and (min-width: 1100px) {
            .split {
                width: 90%;
            }
        }

        @media screen and (max-width: 1380px) and (min-width: 1176px) {
            .split {
                width: 80%;
            }
        }

        @media screen and (max-width: 980px) {
        .container {
            display: -webkit-box !important;
            display: -moz-box !important;
            -webkit-flex-direction: column;
            -moz-flex-direction: column;
            flex-direction: column;
        }
        .split {
            width: 190%;
            margin: 35px 5%;
        }
        .champion {
            width: 65%;
            margin: 20px 1%;
        }
        .split {
            border-bottom: 1px solid #b6b6b6;
            padding-bottom: 20px;
        }
        .round {
            width: 40% !important;
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
            width: 215%;
            margin: 25px 2.5%;
        }
        .champion {
            width: 75%;
        }
        .round {
            width: 40%;
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
            left: 105%;
        }

        .custooltipleft:hover .custooltiplefttext {
            visibility: visible;
        }

        .custooltipleft .custooltiplefttext::after {
            content: " ";
            position: absolute;
            top: 50%;
            right: 100%; /* To the left of the tooltip */
            margin-top: -5px;
            border-width: 5px;
            border-style: solid;
            border-color: transparent black transparent transparent;
        }
    </style>
@endsection



