<?php

namespace App\Http\Controllers\Ums;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Tournament;
use App\Models\League;
use App\Models\Payment;
use App\Models\FullFreeMember;
use App\Models\Settings;

use App\Mail\PaymentApprove;
use App\Mail\Decline;
use App\Mail\Approve;
use App\Mail\PaymentApproveLeague;
use App\Mail\ApproveMembership;
use App\Mail\DeclineMembership;
use App\Mail\MailToAllPlayers;
use App\Mail\MailToAllFullMember;
use App\Models\Ranking;
use Carbon\Carbon;

use Session;
use Auth;
use DB;
use Mail;

class RankingsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['role:Administrator']);
    }

    public function submit_round_one_points(Request $request, $id)
    {
        
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }

        $pointsArr = [];
        for($i = 1; $i < 33; $i++) {
            
            if(isset($request->{'p1_m'.$i.'_id'}) && isset($request->{'p2_m'.$i.'_id'}) && isset($request->{'p1_m'.$i}) && isset($request->{'p2_m'.$i})) {
                $pointsArr['match_'.$i][$request->{'p1_m'.$i.'_id'}] = $request->{'p1_m'.$i};
                $pointsArr['match_'.$i][$request->{'p2_m'.$i.'_id'}] = $request->{'p2_m'.$i};

                
                $year = date('Y', strtotime($tournament->start));
                $findOne = Ranking::where(['year' => $year, 'user_id' => $request->{'p1_m'.$i.'_id'}])->first();
                $findTwo = Ranking::where(['year' => $year, 'user_id' => $request->{'p1_m'.$i.'_id'}])->first();
                
                if($findOne) {
                    if($tournament->draw_status == '1st Draw') {
                        if($request->chk_type && $request->chk_type == 'League') {
                            $findOne->{'1st_league'} = $findOne->{'1st_league'} + $request->{'p1_m'.$i};
                        }
                        if (strpos($tournament->name, "ELITE") !== false) {
                            $findOne->{'1st_elite'} = $findOne->{'1st_elite'} + $request->{'p1_m'.$i};
                        }
                        if (strpos($tournament->name, "PRO")) {
                            $findOne->{'1st_pro'} = $findOne->{'1st_pro'} + $request->{'p1_m'.$i};
                        }
                        if (strpos($tournament->name, "ADV")) {
                            $findOne->{'1st_adv'} = $findOne->{'1st_adv'} + $request->{'p1_m'.$i};
                        }
                        if (strpos($tournament->name, "INT")) {
                            $findOne->{'1st_int'} = $findOne->{'1st_int'} + $request->{'p1_m'.$i};
                        }
                        if (strpos($tournament->name, "ROOKIE")) {
                            $findOne->{'1st_rook'} = $findOne->{'1st_rook'} + $request->{'p1_m'.$i};
                        }
                    }
                    if($tournament->draw_status == '2nd Draw') {
                        if($request->chk_type && $request->chk_type == 'League') {
                            $findOne->{'2nd_league'} = $findOne->{'2nd_league'} + $request->{'p1_m'.$i};
                        }
                        if (strpos($tournament->name, "ELITE") !== false) {
                            $findOne->{'2nd_elite'} = $findOne->{'2nd_elite'} + $request->{'p1_m'.$i};
                        }
                        if (strpos($tournament->name, "PRO")) {
                            $findOne->{'2nd_pro'} = $findOne->{'2nd_pro'} + $request->{'p1_m'.$i};
                        }
                        if (strpos($tournament->name, "ADV")) {
                            $findOne->{'2nd_adv'} = $findOne->{'2nd_adv'} + $request->{'p1_m'.$i};
                        }
                        if (strpos($tournament->name, "INT")) {
                            $findOne->{'2nd_int'} = $findOne->{'2nd_int'} + $request->{'p1_m'.$i};
                        }
                        if (strpos($tournament->name, "ROOKIE")) {
                            $findOne->{'2nd_rook'} = $findOne->{'2nd_rook'} + $request->{'p1_m'.$i};
                        }
                    }
                    if($tournament->draw_status == '3rd Draw') {
                        if (strpos($tournament->name, "ELITE") !== false) {
                            $findOne->{'3rd_elite'} = $findOne->{'3rd_elite'} + $request->{'p1_m'.$i};
                        }
                        if (strpos($tournament->name, "PRO")) {
                            $findOne->{'3rd_pro'} = $findOne->{'3rd_pro'} + $request->{'p1_m'.$i};
                        }
                        if (strpos($tournament->name, "ADV")) {
                            $findOne->{'3rd_adv'} = $findOne->{'3rd_adv'} + $request->{'p1_m'.$i};
                        }
                        if (strpos($tournament->name, "INT")) {
                            $findOne->{'3rd_int'} = $findOne->{'3rd_int'} + $request->{'p1_m'.$i};
                        }
                        if (strpos($tournament->name, "ROOKIE")) {
                            $findOne->{'3rd_rook'} = $findOne->{'3rd_rook'} + $request->{'p1_m'.$i};
                        }
                    }
                    if($tournament->draw_status == '4th Draw') {
                        if (strpos($tournament->name, "ELITE") !== false) {
                            $findOne->{'4th_elite'} = $findOne->{'4th_elite'} + $request->{'p1_m'.$i};
                        }
                        if (strpos($tournament->name, "PRO")) {
                            $findOne->{'4th_pro'} = $findOne->{'4th_pro'} + $request->{'p1_m'.$i};
                        }
                        if (strpos($tournament->name, "ADV")) {
                            $findOne->{'4th_adv'} = $findOne->{'4th_adv'} + $request->{'p1_m'.$i};
                        }
                        if (strpos($tournament->name, "INT")) {
                            $findOne->{'4th_int'} = $findOne->{'4th_int'} + $request->{'p1_m'.$i};
                        }
                        if (strpos($tournament->name, "ROOKIE")) {
                            $findOne->{'4th_rook'} = $findOne->{'4th_rook'} + $request->{'p1_m'.$i};
                        }
                    }
                    if($tournament->draw_status == 'Top16 Finals'){
                        if($request->chk_type && $request->chk_type == 'League') {
                            $findOne->{'top16_finals'} = $findOne->{'top16_finals'} + $request->{'p1_m'.$i};
                        }
                    }

                    $findOne->save();

                } else {
                    $newRankOne = new Ranking();
                    $newRankOne->year = date('Y');
                    $newRankOne->user_id = $request->{'p1_m'.$i.'_id'};
                    if($tournament->draw_status == '1st Draw') {
                        if($request->chk_type && $request->chk_type == 'League') {
                            $newRankOne->{'1st_league'} = $request->{'p1_m'.$i};
                        }
                        if (strpos($tournament->name, "ELITE") !== false) {
                            $newRankOne->{'1st_elite'} = $request->{'p1_m'.$i};
                        }
                        if (strpos($tournament->name, "PRO")) {
                            $newRankOne->{'1st_pro'} = $request->{'p1_m'.$i};
                        }
                        if (strpos($tournament->name, "ADV")) {
                            $newRankOne->{'1st_adv'} = $request->{'p1_m'.$i};
                        }
                        if (strpos($tournament->name, "INT")) {
                            $newRankOne->{'1st_int'} = $request->{'p1_m'.$i};
                        }
                        if (strpos($tournament->name, "ROOKIE")) {
                            $newRankOne->{'1st_rook'} = $request->{'p1_m'.$i};
                        }
                    }
                    if($tournament->draw_status == '2nd Draw') {
                        if($request->chk_type && $request->chk_type == 'League') {
                            $newRankOne->{'2nd_league'} = $request->{'p1_m'.$i};
                        }
                        if (strpos($tournament->name, "ELITE") !== false) {
                            $newRankOne->{'2nd_elite'} = $request->{'p1_m'.$i};
                        }
                        if (strpos($tournament->name, "PRO")) {
                            $newRankOne->{'2nd_pro'} = $request->{'p1_m'.$i};
                        }
                        if (strpos($tournament->name, "ADV")) {
                            $newRankOne->{'2nd_adv'} = $request->{'p1_m'.$i};
                        }
                        if (strpos($tournament->name, "INT")) {
                            $newRankOne->{'2nd_int'} = $request->{'p1_m'.$i};
                        }
                        if (strpos($tournament->name, "ROOKIE")) {
                            $newRankOne->{'2nd_rook'} = $request->{'p1_m'.$i};
                        }
                    }
                    if($tournament->draw_status == '3rd Draw') {
                        if (strpos($tournament->name, "ELITE") !== false) {
                            $newRankOne->{'3rd_elite'} = $request->{'p1_m'.$i};
                        }
                        if (strpos($tournament->name, "PRO")) {
                            $newRankOne->{'3rd_pro'} = $request->{'p1_m'.$i};
                        }
                        if (strpos($tournament->name, "ADV")) {
                            $newRankOne->{'3rd_adv'} = $request->{'p1_m'.$i};
                        }
                        if (strpos($tournament->name, "INT")) {
                            $newRankOne->{'3rd_int'} = $request->{'p1_m'.$i};
                        }
                        if (strpos($tournament->name, "ROOKIE")) {
                            $newRankOne->{'3rd_rook'} = $request->{'p1_m'.$i};
                        }
                    }
                    if($tournament->draw_status == '4th Draw') {
                        if (strpos($tournament->name, "ELITE") !== false) {
                            $newRankOne->{'4th_elite'} = $request->{'p1_m'.$i};
                        }
                        if (strpos($tournament->name, "PRO")) {
                            $newRankOne->{'4th_pro'} = $request->{'p1_m'.$i};
                        }
                        if (strpos($tournament->name, "ADV")) {
                            $newRankOne->{'4th_adv'} = $request->{'p1_m'.$i};
                        }
                        if (strpos($tournament->name, "INT")) {
                            $newRankOne->{'4th_int'} = $request->{'p1_m'.$i};
                        }
                        if (strpos($tournament->name, "ROOKIE")) {
                            $newRankOne->{'4th_rook'} = $request->{'p1_m'.$i};
                        }
                    }

                    $newRankOne->save();

                }

                if($findTwo) {
                    if($tournament->draw_status == '1st Draw') {
                        if($request->chk_type && $request->chk_type == 'League') {
                            $findTwo->{'1st_league'} = $findTwo->{'1st_league'} + $request->{'p2_m'.$i};
                        }
                        if (strpos($tournament->name, "ELITE") !== false) {
                            $findTwo->{'1st_elite'} = $findTwo->{'1st_elite'} + $request->{'p2_m'.$i};
                        }
                        if (strpos($tournament->name, "PRO")) {
                            $findTwo->{'1st_pro'} = $findTwo->{'1st_pro'} + $request->{'p2_m'.$i};
                        }
                        if (strpos($tournament->name, "ADV")) {
                            $findTwo->{'1st_adv'} = $findTwo->{'1st_adv'} + $request->{'p2_m'.$i};
                        }
                        if (strpos($tournament->name, "INT")) {
                            $findTwo->{'1st_int'} = $findTwo->{'1st_int'} + $request->{'p2_m'.$i};
                        }
                        if (strpos($tournament->name, "ROOKIE")) {
                            $findTwo->{'1st_rook'} = $findTwo->{'1st_rook'} + $request->{'p2_m'.$i};
                        }
                    }
                    if($tournament->draw_status == '2nd Draw') {
                        if($request->chk_type && $request->chk_type == 'League') {
                            $findTwo->{'2nd_league'} = $findTwo->{'2nd_league'} + $request->{'p2_m'.$i};
                        }
                        if (strpos($tournament->name, "ELITE") !== false) {
                            $findTwo->{'2nd_elite'} = $findTwo->{'2nd_elite'} + $request->{'p2_m'.$i};
                        }
                        if (strpos($tournament->name, "PRO")) {
                            $findTwo->{'2nd_pro'} = $findTwo->{'2nd_pro'} + $request->{'p2_m'.$i};
                        }
                        if (strpos($tournament->name, "ADV")) {
                            $findTwo->{'2nd_adv'} = $findTwo->{'2nd_adv'} + $request->{'p2_m'.$i};
                        }
                        if (strpos($tournament->name, "INT")) {
                            $findTwo->{'2nd_int'} = $findTwo->{'2nd_int'} + $request->{'p2_m'.$i};
                        }
                        if (strpos($tournament->name, "ROOKIE")) {
                            $findTwo->{'2nd_rook'} = $findTwo->{'2nd_rook'} + $request->{'p2_m'.$i};
                        }
                    }
                    if($tournament->draw_status == '3rd Draw') {
                        if (strpos($tournament->name, "ELITE") !== false) {
                            $findTwo->{'3rd_elite'} = $findTwo->{'3rd_elite'} + $request->{'p2_m'.$i};
                        }
                        if (strpos($tournament->name, "PRO")) {
                            $findTwo->{'3rd_pro'} = $findTwo->{'3rd_pro'} + $request->{'p2_m'.$i};
                        }
                        if (strpos($tournament->name, "ADV")) {
                            $findTwo->{'3rd_adv'} = $findTwo->{'3rd_adv'} + $request->{'p2_m'.$i};
                        }
                        if (strpos($tournament->name, "INT")) {
                            $findTwo->{'3rd_int'} = $findTwo->{'3rd_int'} + $request->{'p2_m'.$i};
                        }
                        if (strpos($tournament->name, "ROOKIE")) {
                            $findTwo->{'3rd_rook'} = $findTwo->{'3rd_rook'} + $request->{'p2_m'.$i};
                        }
                    }
                    if($tournament->draw_status == '4th Draw') {
                        if (strpos($tournament->name, "ELITE") !== false) {
                            $findTwo->{'4th_elite'} = $findTwo->{'4th_elite'} + $request->{'p2_m'.$i};
                        }
                        if (strpos($tournament->name, "PRO")) {
                            $findTwo->{'4th_pro'} = $findTwo->{'4th_pro'} + $request->{'p2_m'.$i};
                        }
                        if (strpos($tournament->name, "ADV")) {
                            $findTwo->{'4th_adv'} = $findTwo->{'4th_adv'} + $request->{'p2_m'.$i};
                        }
                        if (strpos($tournament->name, "INT")) {
                            $findTwo->{'4th_int'} = $findTwo->{'4th_int'} + $request->{'p2_m'.$i};
                        }
                        if (strpos($tournament->name, "ROOKIE")) {
                            $findTwo->{'4th_rook'} = $findTwo->{'4th_rook'} + $request->{'p2_m'.$i};
                        }
                    }

                    $findTwo->save();

                } else {
                    $newRankTwo = new Ranking();
                    $newRankTwo->year = date('Y');
                    $newRankTwo->user_id = $request->{'p2_m'.$i.'_id'};
                    if($tournament->draw_status == '1st Draw') {
                        if($request->chk_type && $request->chk_type == 'League') {
                            $newRankTwo->{'1st_league'} = $request->{'p2_m'.$i};
                        }
                        if (strpos($tournament->name, "ELITE") !== false) {
                            $newRankTwo->{'1st_elite'} = $request->{'p2_m'.$i};
                        }
                        if (strpos($tournament->name, "PRO")) {
                            $newRankTwo->{'1st_pro'} = $request->{'p2_m'.$i};
                        }
                        if (strpos($tournament->name, "ADV")) {
                            $newRankTwo->{'1st_adv'} = $request->{'p2_m'.$i};
                        }
                        if (strpos($tournament->name, "INT")) {
                            $newRankTwo->{'1st_int'} = $request->{'p2_m'.$i};
                        }
                        if (strpos($tournament->name, "ROOKIE")) {
                            $newRankTwo->{'1st_rook'} = $request->{'p2_m'.$i};
                        }
                    }
                    if($tournament->draw_status == '2nd Draw') {
                        if($request->chk_type && $request->chk_type == 'League') {
                            $newRankTwo->{'2nd_league'} = $request->{'p2_m'.$i};
                        }
                        if (strpos($tournament->name, "ELITE") !== false) {
                            $newRankTwo->{'2nd_elite'} = $request->{'p2_m'.$i};
                        }
                        if (strpos($tournament->name, "PRO")) {
                            $newRankTwo->{'2nd_pro'} = $request->{'p2_m'.$i};
                        }
                        if (strpos($tournament->name, "ADV")) {
                            $newRankTwo->{'2nd_adv'} = $request->{'p2_m'.$i};
                        }
                        if (strpos($tournament->name, "INT")) {
                            $newRankTwo->{'2nd_int'} = $request->{'p2_m'.$i};
                        }
                        if (strpos($tournament->name, "ROOKIE")) {
                            $newRankTwo->{'2nd_rook'} = $request->{'p2_m'.$i};
                        }
                    }
                    if($tournament->draw_status == '3rd Draw') {
                        if (strpos($tournament->name, "ELITE") !== false) {
                            $newRankTwo->{'3rd_elite'} = $request->{'p2_m'.$i};
                        }
                        if (strpos($tournament->name, "PRO")) {
                            $newRankTwo->{'3rd_pro'} = $request->{'p2_m'.$i};
                        }
                        if (strpos($tournament->name, "ADV")) {
                            $newRankTwo->{'3rd_adv'} = $request->{'p2_m'.$i};
                        }
                        if (strpos($tournament->name, "INT")) {
                            $newRankTwo->{'3rd_int'} = $request->{'p2_m'.$i};
                        }
                        if (strpos($tournament->name, "ROOKIE")) {
                            $newRankTwo->{'3rd_rook'} = $request->{'p2_m'.$i};
                        }
                    }
                    if($tournament->draw_status == '4th Draw') {
                        if (strpos($tournament->name, "ELITE") !== false) {
                            $newRankTwo->{'4th_elite'} = $request->{'p2_m'.$i};
                        }
                        if (strpos($tournament->name, "PRO")) {
                            $newRankTwo->{'4th_pro'} = $request->{'p2_m'.$i};
                        }
                        if (strpos($tournament->name, "ADV")) {
                            $newRankTwo->{'4th_adv'} = $request->{'p2_m'.$i};
                        }
                        if (strpos($tournament->name, "INT")) {
                            $newRankTwo->{'4th_int'} = $request->{'p2_m'.$i};
                        }
                        if (strpos($tournament->name, "ROOKIE")) {
                            $newRankTwo->{'4th_rook'} = $request->{'p2_m'.$i};
                        }
                    }

                    $newRankTwo->save();

                }


            }
        }


        // $pointsArr = [];
        // for ($i = 1; $i < 33; $i++) {
        //     if (isset($request->{'p1_m' . $i . '_id'}) && isset($request->{'p2_m' . $i . '_id'}) && isset($request->{'p1_m' . $i}) && isset($request->{'p2_m' . $i})) {
        //         $pointsArr['match_' . $i][$request->{'p1_m' . $i . '_id'}] = $request->{'p1_m' . $i};
        //         $pointsArr['match_' . $i][$request->{'p2_m' . $i . '_id'}] = $request->{'p2_m' . $i};

        //         $year = date('Y', strtotime($tournament->start));

        //         $chk_points = json_decode($tournament->round_one_points, true);
                
        //         $points_one = isset($chk_points['match_' . $i][$request->{'p1_m' . $i . '_id'}])
        //             ? $request->{'p1_m' . $i} - $chk_points['match_' . $i][$request->{'p1_m' . $i . '_id'}]
        //             : $request->{'p1_m' . $i};
                
        //         $points_two = isset($chk_points['match_' . $i][$request->{'p2_m' . $i . '_id'}])
        //             ? $request->{'p2_m' . $i} - $chk_points['match_' . $i][$request->{'p2_m' . $i . '_id'}]
        //             : $request->{'p2_m' . $i};
                
        //         $this->processRanking($year, $request->{'p1_m' . $i . '_id'}, $points_one, $tournament, $request->chk_type);
        //         $this->processRanking($year, $request->{'p2_m' . $i . '_id'}, $points_two, $tournament, $request->chk_type);
                
        //     }
        // }

        
        $tournament->round_one_points = json_encode($pointsArr);

        $tournament->save();

        Session::flash('success', 'Points Submitted Successfully!');
        return redirect()->back();
    }

    public function submit_round_two_points(Request $request, $id)
    {
        
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }


        $pointsArr = [];
        for($i = 1; $i < 17; $i++) {
            if(isset($request->{'p1_m'.$i.'_id'}) && isset($request->{'p2_m'.$i.'_id'}) && isset($request->{'p1_m'.$i}) && isset($request->{'p2_m'.$i})) {
                $pointsArr['match_'.$i][$request->{'p1_m'.$i.'_id'}] = $request->{'p1_m'.$i};
                $pointsArr['match_'.$i][$request->{'p2_m'.$i.'_id'}] = $request->{'p2_m'.$i};

                
                $year = date('Y', strtotime($tournament->start));
                $findOne = Ranking::where(['year' => $year, 'user_id' => $request->{'p1_m'.$i.'_id'}])->first();
                $findTwo = Ranking::where(['year' => $year, 'user_id' => $request->{'p2_m'.$i.'_id'}])->first();
                
                if($findOne) {
                    if($tournament->draw_status == '1st Draw') {
                        if($request->chk_type && $request->chk_type == 'League') {
                            $findOne->{'1st_league'} = $findOne->{'1st_league'} + $request->{'p1_m'.$i};
                        }
                        if (strpos($tournament->name, "ELITE") !== false) {
                            $findOne->{'1st_elite'} = $findOne->{'1st_elite'} + $request->{'p1_m'.$i};
                        }
                        if (strpos($tournament->name, "PRO")) {
                            $findOne->{'1st_pro'} = $findOne->{'1st_pro'} + $request->{'p1_m'.$i};
                        }
                        if (strpos($tournament->name, "ADV")) {
                            $findOne->{'1st_adv'} = $findOne->{'1st_adv'} + $request->{'p1_m'.$i};
                        }
                        if (strpos($tournament->name, "INT")) {
                            $findOne->{'1st_int'} = $findOne->{'1st_int'} + $request->{'p1_m'.$i};
                        }
                        if (strpos($tournament->name, "ROOKIE")) {
                            $findOne->{'1st_rook'} = $findOne->{'1st_rook'} + $request->{'p1_m'.$i};
                        }
                    }
                    if($tournament->draw_status == '2nd Draw') {
                        if($request->chk_type && $request->chk_type == 'League') {
                            $findOne->{'2nd_league'} = $findOne->{'2nd_league'} + $request->{'p1_m'.$i};
                        }
                        if (strpos($tournament->name, "ELITE") !== false) {
                            $findOne->{'2nd_elite'} = $findOne->{'2nd_elite'} + $request->{'p1_m'.$i};
                        }
                        if (strpos($tournament->name, "PRO")) {
                            $findOne->{'2nd_pro'} = $findOne->{'2nd_pro'} + $request->{'p1_m'.$i};
                        }
                        if (strpos($tournament->name, "ADV")) {
                            $findOne->{'2nd_adv'} = $findOne->{'2nd_adv'} + $request->{'p1_m'.$i};
                        }
                        if (strpos($tournament->name, "INT")) {
                            $findOne->{'2nd_int'} = $findOne->{'2nd_int'} + $request->{'p1_m'.$i};
                        }
                        if (strpos($tournament->name, "ROOKIE")) {
                            $findOne->{'2nd_rook'} = $findOne->{'2nd_rook'} + $request->{'p1_m'.$i};
                        }
                    }
                    if($tournament->draw_status == '3rd Draw') {
                        if (strpos($tournament->name, "ELITE") !== false) {
                            $findOne->{'3rd_elite'} = $findOne->{'3rd_elite'} + $request->{'p1_m'.$i};
                        }
                        if (strpos($tournament->name, "PRO")) {
                            $findOne->{'3rd_pro'} = $findOne->{'3rd_pro'} + $request->{'p1_m'.$i};
                        }
                        if (strpos($tournament->name, "ADV")) {
                            $findOne->{'3rd_adv'} = $findOne->{'3rd_adv'} + $request->{'p1_m'.$i};
                        }
                        if (strpos($tournament->name, "INT")) {
                            $findOne->{'3rd_int'} = $findOne->{'3rd_int'} + $request->{'p1_m'.$i};
                        }
                        if (strpos($tournament->name, "ROOKIE")) {
                            $findOne->{'3rd_rook'} = $findOne->{'3rd_rook'} + $request->{'p1_m'.$i};
                        }
                    }
                    if($tournament->draw_status == '4th Draw') {
                        if (strpos($tournament->name, "ELITE") !== false) {
                            $findOne->{'4th_elite'} = $findOne->{'4th_elite'} + $request->{'p1_m'.$i};
                        }
                        if (strpos($tournament->name, "PRO")) {
                            $findOne->{'4th_pro'} = $findOne->{'4th_pro'} + $request->{'p1_m'.$i};
                        }
                        if (strpos($tournament->name, "ADV")) {
                            $findOne->{'4th_adv'} = $findOne->{'4th_adv'} + $request->{'p1_m'.$i};
                        }
                        if (strpos($tournament->name, "INT")) {
                            $findOne->{'4th_int'} = $findOne->{'4th_int'} + $request->{'p1_m'.$i};
                        }
                        if (strpos($tournament->name, "ROOKIE")) {
                            $findOne->{'4th_rook'} = $findOne->{'4th_rook'} + $request->{'p1_m'.$i};
                        }
                    }
                    if($tournament->draw_status == 'Top16 Finals'){
                        if($request->chk_type && $request->chk_type == 'League') {
                            $findOne->{'top16_finals'} = $findOne->{'top16_finals'} + $request->{'p1_m'.$i};
                        }
                    }

                    $findOne->save();

                } else {
                    $newRankOne = new Ranking();
                    $newRankOne->year = date('Y');
                    $newRankOne->user_id = $request->{'p1_m'.$i.'_id'};
                    if($tournament->draw_status == '1st Draw') {
                        if($request->chk_type && $request->chk_type == 'League') {
                            $newRankOne->{'1st_league'} = $request->{'p1_m'.$i};
                        }
                        if (strpos($tournament->name, "ELITE") !== false) {
                            $newRankOne->{'1st_elite'} = $request->{'p1_m'.$i};
                        }
                        if (strpos($tournament->name, "PRO")) {
                            $newRankOne->{'1st_pro'} = $request->{'p1_m'.$i};
                        }
                        if (strpos($tournament->name, "ADV")) {
                            $newRankOne->{'1st_adv'} = $request->{'p1_m'.$i};
                        }
                        if (strpos($tournament->name, "INT")) {
                            $newRankOne->{'1st_int'} = $request->{'p1_m'.$i};
                        }
                        if (strpos($tournament->name, "ROOKIE")) {
                            $newRankOne->{'1st_rook'} = $request->{'p1_m'.$i};
                        }
                    }
                    if($tournament->draw_status == '2nd Draw') {
                        if($request->chk_type && $request->chk_type == 'League') {
                            $newRankOne->{'2nd_league'} = $request->{'p1_m'.$i};
                        }
                        if (strpos($tournament->name, "ELITE") !== false) {
                            $newRankOne->{'2nd_elite'} = $request->{'p1_m'.$i};
                        }
                        if (strpos($tournament->name, "PRO")) {
                            $newRankOne->{'2nd_pro'} = $request->{'p1_m'.$i};
                        }
                        if (strpos($tournament->name, "ADV")) {
                            $newRankOne->{'2nd_adv'} = $request->{'p1_m'.$i};
                        }
                        if (strpos($tournament->name, "INT")) {
                            $newRankOne->{'2nd_int'} = $request->{'p1_m'.$i};
                        }
                        if (strpos($tournament->name, "ROOKIE")) {
                            $newRankOne->{'2nd_rook'} = $request->{'p1_m'.$i};
                        }
                    }
                    if($tournament->draw_status == '3rd Draw') {
                        if (strpos($tournament->name, "ELITE") !== false) {
                            $newRankOne->{'3rd_elite'} = $request->{'p1_m'.$i};
                        }
                        if (strpos($tournament->name, "PRO")) {
                            $newRankOne->{'3rd_pro'} = $request->{'p1_m'.$i};
                        }
                        if (strpos($tournament->name, "ADV")) {
                            $newRankOne->{'3rd_adv'} = $request->{'p1_m'.$i};
                        }
                        if (strpos($tournament->name, "INT")) {
                            $newRankOne->{'3rd_int'} = $request->{'p1_m'.$i};
                        }
                        if (strpos($tournament->name, "ROOKIE")) {
                            $newRankOne->{'3rd_rook'} = $request->{'p1_m'.$i};
                        }
                    }
                    if($tournament->draw_status == '4th Draw') {
                        if (strpos($tournament->name, "ELITE") !== false) {
                            $newRankOne->{'4th_elite'} = $request->{'p1_m'.$i};
                        }
                        if (strpos($tournament->name, "PRO")) {
                            $newRankOne->{'4th_pro'} = $request->{'p1_m'.$i};
                        }
                        if (strpos($tournament->name, "ADV")) {
                            $newRankOne->{'4th_adv'} = $request->{'p1_m'.$i};
                        }
                        if (strpos($tournament->name, "INT")) {
                            $newRankOne->{'4th_int'} = $request->{'p1_m'.$i};
                        }
                        if (strpos($tournament->name, "ROOKIE")) {
                            $newRankOne->{'4th_rook'} = $request->{'p1_m'.$i};
                        }
                    }

                    $newRankOne->save();

                }
                
                if($findTwo) {
                    if($tournament->draw_status == '1st Draw') {
                        if($request->chk_type && $request->chk_type == 'League') {
                            $findTwo->{'1st_league'} = $findTwo->{'1st_league'} + $request->{'p2_m'.$i};
                        }
                        if (strpos($tournament->name, "ELITE") !== false) {
                            $findTwo->{'1st_elite'} = $findTwo->{'1st_elite'} + $request->{'p2_m'.$i};
                        }
                        if (strpos($tournament->name, "PRO")) {
                            $findTwo->{'1st_pro'} = $findTwo->{'1st_pro'} + $request->{'p2_m'.$i};
                        }
                        if (strpos($tournament->name, "ADV")) {
                            $findTwo->{'1st_adv'} = $findTwo->{'1st_adv'} + $request->{'p2_m'.$i};
                        }
                        if (strpos($tournament->name, "INT")) {
                            $findTwo->{'1st_int'} = $findTwo->{'1st_int'} + $request->{'p2_m'.$i};
                        }
                        if (strpos($tournament->name, "ROOKIE")) {
                            $findTwo->{'1st_rook'} = $findTwo->{'1st_rook'} + $request->{'p2_m'.$i};
                        }
                    }
                    if($tournament->draw_status == '2nd Draw') {
                        if($request->chk_type && $request->chk_type == 'League') {
                            $findTwo->{'2nd_league'} = $findTwo->{'2nd_league'} + $request->{'p2_m'.$i};
                        }
                        if (strpos($tournament->name, "ELITE") !== false) {
                            $findTwo->{'2nd_elite'} = $findTwo->{'2nd_elite'} + $request->{'p2_m'.$i};
                        }
                        if (strpos($tournament->name, "PRO")) {
                            $findTwo->{'2nd_pro'} = $findTwo->{'2nd_pro'} + $request->{'p2_m'.$i};
                        }
                        if (strpos($tournament->name, "ADV")) {
                            $findTwo->{'2nd_adv'} = $findTwo->{'2nd_adv'} + $request->{'p2_m'.$i};
                        }
                        if (strpos($tournament->name, "INT")) {
                            $findTwo->{'2nd_int'} = $findTwo->{'2nd_int'} + $request->{'p2_m'.$i};
                        }
                        if (strpos($tournament->name, "ROOKIE")) {
                            $findTwo->{'2nd_rook'} = $findTwo->{'2nd_rook'} + $request->{'p2_m'.$i};
                        }
                    }
                    if($tournament->draw_status == '3rd Draw') {
                        if (strpos($tournament->name, "ELITE") !== false) {
                            $findTwo->{'3rd_elite'} = $findTwo->{'3rd_elite'} + $request->{'p2_m'.$i};
                        }
                        if (strpos($tournament->name, "PRO")) {
                            $findTwo->{'3rd_pro'} = $findTwo->{'3rd_pro'} + $request->{'p2_m'.$i};
                        }
                        if (strpos($tournament->name, "ADV")) {
                            $findTwo->{'3rd_adv'} = $findTwo->{'3rd_adv'} + $request->{'p2_m'.$i};
                        }
                        if (strpos($tournament->name, "INT")) {
                            $findTwo->{'3rd_int'} = $findTwo->{'3rd_int'} + $request->{'p2_m'.$i};
                        }
                        if (strpos($tournament->name, "ROOKIE")) {
                            $findTwo->{'3rd_rook'} = $findTwo->{'3rd_rook'} + $request->{'p2_m'.$i};
                        }
                    }
                    if($tournament->draw_status == '4th Draw') {
                        if (strpos($tournament->name, "ELITE") !== false) {
                            $findTwo->{'4th_elite'} = $findTwo->{'4th_elite'} + $request->{'p2_m'.$i};
                        }
                        if (strpos($tournament->name, "PRO")) {
                            $findTwo->{'4th_pro'} = $findTwo->{'4th_pro'} + $request->{'p2_m'.$i};
                        }
                        if (strpos($tournament->name, "ADV")) {
                            $findTwo->{'4th_adv'} = $findTwo->{'4th_adv'} + $request->{'p2_m'.$i};
                        }
                        if (strpos($tournament->name, "INT")) {
                            $findTwo->{'4th_int'} = $findTwo->{'4th_int'} + $request->{'p2_m'.$i};
                        }
                        if (strpos($tournament->name, "ROOKIE")) {
                            $findTwo->{'4th_rook'} = $findTwo->{'4th_rook'} + $request->{'p2_m'.$i};
                        }
                    }
                    
                    $findTwo->save();

                } else {
                    $newRankTwo = new Ranking();
                    $newRankTwo->year = date('Y');
                    $newRankTwo->user_id = $request->{'p2_m'.$i.'_id'};
                    if($tournament->draw_status == '1st Draw') {
                        if($request->chk_type && $request->chk_type == 'League') {
                            $newRankTwo->{'1st_league'} = $request->{'p2_m'.$i};
                        }
                        if (strpos($tournament->name, "ELITE") !== false) {
                            $newRankTwo->{'1st_elite'} = $request->{'p2_m'.$i};
                        }
                        if (strpos($tournament->name, "PRO")) {
                            $newRankTwo->{'1st_pro'} = $request->{'p2_m'.$i};
                        }
                        if (strpos($tournament->name, "ADV")) {
                            $newRankTwo->{'1st_adv'} = $request->{'p2_m'.$i};
                        }
                        if (strpos($tournament->name, "INT")) {
                            $newRankTwo->{'1st_int'} = $request->{'p2_m'.$i};
                        }
                        if (strpos($tournament->name, "ROOKIE")) {
                            $newRankTwo->{'1st_rook'} = $request->{'p2_m'.$i};
                        }
                    }
                    if($tournament->draw_status == '2nd Draw') {
                        if($request->chk_type && $request->chk_type == 'League') {
                            $newRankTwo->{'2nd_league'} = $request->{'p2_m'.$i};
                        }
                        if (strpos($tournament->name, "ELITE") !== false) {
                            $newRankTwo->{'2nd_elite'} = $request->{'p2_m'.$i};
                        }
                        if (strpos($tournament->name, "PRO")) {
                            $newRankTwo->{'2nd_pro'} = $request->{'p2_m'.$i};
                        }
                        if (strpos($tournament->name, "ADV")) {
                            $newRankTwo->{'2nd_adv'} = $request->{'p2_m'.$i};
                        }
                        if (strpos($tournament->name, "INT")) {
                            $newRankTwo->{'2nd_int'} = $request->{'p2_m'.$i};
                        }
                        if (strpos($tournament->name, "ROOKIE")) {
                            $newRankTwo->{'2nd_rook'} = $request->{'p2_m'.$i};
                        }
                    }
                    if($tournament->draw_status == '3rd Draw') {
                        if (strpos($tournament->name, "ELITE") !== false) {
                            $newRankTwo->{'3rd_elite'} = $request->{'p2_m'.$i};
                        }
                        if (strpos($tournament->name, "PRO")) {
                            $newRankTwo->{'3rd_pro'} = $request->{'p2_m'.$i};
                        }
                        if (strpos($tournament->name, "ADV")) {
                            $newRankTwo->{'3rd_adv'} = $request->{'p2_m'.$i};
                        }
                        if (strpos($tournament->name, "INT")) {
                            $newRankTwo->{'3rd_int'} = $request->{'p2_m'.$i};
                        }
                        if (strpos($tournament->name, "ROOKIE")) {
                            $newRankTwo->{'3rd_rook'} = $request->{'p2_m'.$i};
                        }
                    }
                    if($tournament->draw_status == '4th Draw') {
                        if (strpos($tournament->name, "ELITE") !== false) {
                            $newRankTwo->{'4th_elite'} = $request->{'p2_m'.$i};
                        }
                        if (strpos($tournament->name, "PRO")) {
                            $newRankTwo->{'4th_pro'} = $request->{'p2_m'.$i};
                        }
                        if (strpos($tournament->name, "ADV")) {
                            $newRankTwo->{'4th_adv'} = $request->{'p2_m'.$i};
                        }
                        if (strpos($tournament->name, "INT")) {
                            $newRankTwo->{'4th_int'} = $request->{'p2_m'.$i};
                        }
                        if (strpos($tournament->name, "ROOKIE")) {
                            $newRankTwo->{'4th_rook'} = $request->{'p2_m'.$i};
                        }
                    }

                    $newRankTwo->save();

                }


            }
        }

        // $pointsArr = [];
        // for($i = 1; $i < 17; $i++) {
        //     if(isset($request->{'p1_m'.$i.'_id'}) && isset($request->{'p2_m'.$i.'_id'}) && isset($request->{'p1_m'.$i}) && isset($request->{'p2_m'.$i})) {
        //         $pointsArr['match_' . $i][$request->{'p1_m' . $i . '_id'}] = $request->{'p1_m' . $i};
        //         $pointsArr['match_' . $i][$request->{'p2_m' . $i . '_id'}] = $request->{'p2_m' . $i};

        //         $year = date('Y', strtotime($tournament->start));

        //         $chk_points = json_decode($tournament->round_two_points, true);
                
        //         $points_one = isset($chk_points['match_' . $i][$request->{'p1_m' . $i . '_id'}])
        //             ? $request->{'p1_m' . $i} - $chk_points['match_' . $i][$request->{'p1_m' . $i . '_id'}]
        //             : $request->{'p1_m' . $i};
                
        //         $points_two = isset($chk_points['match_' . $i][$request->{'p2_m' . $i . '_id'}])
        //             ? $request->{'p2_m' . $i} - $chk_points['match_' . $i][$request->{'p2_m' . $i . '_id'}]
        //             : $request->{'p2_m' . $i};
                
        //         $this->processRanking($year, $request->{'p1_m' . $i . '_id'}, $points_one, $tournament, $request->chk_type);
        //         $this->processRanking($year, $request->{'p2_m' . $i . '_id'}, $points_two, $tournament, $request->chk_type);

        //     }
        // }
        
        $tournament->round_two_points = json_encode($pointsArr);

        $tournament->save();

        Session::flash('success', 'Points Submitted Successfully!');
        return redirect()->back();
    }

    public function submit_round_three_points(Request $request, $id)
    {
        
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }

        $pointsArr = [];
        for($i = 1; $i < 9; $i++) {
            if(isset($request->{'p1_m'.$i.'_id'}) && isset($request->{'p2_m'.$i.'_id'}) && isset($request->{'p1_m'.$i}) && isset($request->{'p2_m'.$i})) {
                $pointsArr['match_' . $i][$request->{'p1_m' . $i . '_id'}] = $request->{'p1_m' . $i};
                $pointsArr['match_' . $i][$request->{'p2_m' . $i . '_id'}] = $request->{'p2_m' . $i};

                $year = date('Y', strtotime($tournament->start));

                $chk_points = json_decode($tournament->round_three_points, true);
                
                $points_one = isset($chk_points['match_' . $i][$request->{'p1_m' . $i . '_id'}])
                    ? $request->{'p1_m' . $i} - $chk_points['match_' . $i][$request->{'p1_m' . $i . '_id'}]
                    : $request->{'p1_m' . $i};
                
                $points_two = isset($chk_points['match_' . $i][$request->{'p2_m' . $i . '_id'}])
                    ? $request->{'p2_m' . $i} - $chk_points['match_' . $i][$request->{'p2_m' . $i . '_id'}]
                    : $request->{'p2_m' . $i};
                
                $this->processRanking($year, $request->{'p1_m' . $i . '_id'}, $points_one, $tournament, $request->chk_type);
                $this->processRanking($year, $request->{'p2_m' . $i . '_id'}, $points_two, $tournament, $request->chk_type);

            }
        }
        
        $tournament->round_three_points = json_encode($pointsArr);

        $tournament->save();

        Session::flash('success', 'Points Submitted Successfully!');
        return redirect()->back();
    }

    public function submit_quarter_final_points(Request $request, $id)
    {
        
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }

        $pointsArr = [];
        for($i = 1; $i < 5; $i++) {
            if(isset($request->{'p1_m'.$i.'_id'}) && isset($request->{'p2_m'.$i.'_id'}) && isset($request->{'p1_m'.$i}) && isset($request->{'p2_m'.$i})) {
                $pointsArr['match_' . $i][$request->{'p1_m' . $i . '_id'}] = $request->{'p1_m' . $i};
                $pointsArr['match_' . $i][$request->{'p2_m' . $i . '_id'}] = $request->{'p2_m' . $i};

                $year = date('Y', strtotime($tournament->start));

                $chk_points = json_decode($tournament->quarter_final_points, true);
                
                $points_one = isset($chk_points['match_' . $i][$request->{'p1_m' . $i . '_id'}])
                    ? $request->{'p1_m' . $i} - $chk_points['match_' . $i][$request->{'p1_m' . $i . '_id'}]
                    : $request->{'p1_m' . $i};
                
                $points_two = isset($chk_points['match_' . $i][$request->{'p2_m' . $i . '_id'}])
                    ? $request->{'p2_m' . $i} - $chk_points['match_' . $i][$request->{'p2_m' . $i . '_id'}]
                    : $request->{'p2_m' . $i};
                
                $this->processRanking($year, $request->{'p1_m' . $i . '_id'}, $points_one, $tournament, $request->chk_type);
                $this->processRanking($year, $request->{'p2_m' . $i . '_id'}, $points_two, $tournament, $request->chk_type);

            }
        }
        
        $tournament->quarter_final_points = json_encode($pointsArr);

        $tournament->save();

        Session::flash('success', 'Points Submitted Successfully!');
        return redirect()->back();
    }

    public function submit_semi_final_points(Request $request, $id)
    {
        
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }

        $pointsArr = [];
        for($i = 1; $i < 3; $i++) {
            if(isset($request->{'p1_m'.$i.'_id'}) && isset($request->{'p2_m'.$i.'_id'}) && isset($request->{'p1_m'.$i}) && isset($request->{'p2_m'.$i})) {
                $pointsArr['match_' . $i][$request->{'p1_m' . $i . '_id'}] = $request->{'p1_m' . $i};
                $pointsArr['match_' . $i][$request->{'p2_m' . $i . '_id'}] = $request->{'p2_m' . $i};

                $year = date('Y', strtotime($tournament->start));

                $chk_points = json_decode($tournament->semi_final_points, true);
                
                $points_one = isset($chk_points['match_' . $i][$request->{'p1_m' . $i . '_id'}])
                    ? $request->{'p1_m' . $i} - $chk_points['match_' . $i][$request->{'p1_m' . $i . '_id'}]
                    : $request->{'p1_m' . $i};
                
                $points_two = isset($chk_points['match_' . $i][$request->{'p2_m' . $i . '_id'}])
                    ? $request->{'p2_m' . $i} - $chk_points['match_' . $i][$request->{'p2_m' . $i . '_id'}]
                    : $request->{'p2_m' . $i};
                
                $this->processRanking($year, $request->{'p1_m' . $i . '_id'}, $points_one, $tournament, $request->chk_type);
                $this->processRanking($year, $request->{'p2_m' . $i . '_id'}, $points_two, $tournament, $request->chk_type);

            }
        }
        
        $tournament->semi_final_points = json_encode($pointsArr);

        $tournament->save();

        Session::flash('success', 'Points Submitted Successfully!');
        return redirect()->back();
    }

    public function submit_final_points(Request $request, $id)
    {
        
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }

        $pointsArr = [];
        for($i = 1; $i < 2; $i++) {
            if(isset($request->{'p1_m'.$i.'_id'}) && isset($request->{'p2_m'.$i.'_id'}) && isset($request->{'p1_m'.$i}) && isset($request->{'p2_m'.$i})) {
                $pointsArr['match_' . $i][$request->{'p1_m' . $i . '_id'}] = $request->{'p1_m' . $i};
                $pointsArr['match_' . $i][$request->{'p2_m' . $i . '_id'}] = $request->{'p2_m' . $i};

                $year = date('Y', strtotime($tournament->start));

                $chk_points = json_decode($tournament->final_points, true);
                
                $points_one = isset($chk_points['match_' . $i][$request->{'p1_m' . $i . '_id'}])
                    ? $request->{'p1_m' . $i} - $chk_points['match_' . $i][$request->{'p1_m' . $i . '_id'}]
                    : $request->{'p1_m' . $i};
                
                $points_two = isset($chk_points['match_' . $i][$request->{'p2_m' . $i . '_id'}])
                    ? $request->{'p2_m' . $i} - $chk_points['match_' . $i][$request->{'p2_m' . $i . '_id'}]
                    : $request->{'p2_m' . $i};
                
                $this->processRanking($year, $request->{'p1_m' . $i . '_id'}, $points_one, $tournament, $request->chk_type);
                $this->processRanking($year, $request->{'p2_m' . $i . '_id'}, $points_two, $tournament, $request->chk_type);

            }
        }
        
        $tournament->final_points = json_encode($pointsArr);

        $tournament->save();

        Session::flash('success', 'Points Submitted Successfully!');
        return redirect()->back();
    }


    public function submit_group_points(Request $request, $grp_word, $id)
    {
        
        if($request->chk_type == 'Tournament') {
            $tournament = Tournament::findOrFail($id);
        } else {
            $tournament = League::findOrFail($id);
        }

        for($i = 1; $i < $tournament->group_size + 1; $i++) {
            $convert_word = strtolower(ucwords((new \NumberFormatter('en_IN', \NumberFormatter::SPELLOUT))->format($i)));

            if($convert_word == $grp_word) {

                $group_players = json_decode($tournament->{"group_".$grp_word."_players"}, true);
                $count_players = count($group_players);        
                $gr_matches = ($count_players * ($count_players - 1)) / 2;

                $pointsArr = [];
                for($j = 1; $j < $gr_matches + 1; $j++) {
                    if(isset($request->{'p1_m'.$j.'_id'}) && isset($request->{'p2_m'.$j.'_id'}) && isset($request->{'p1_m'.$j}) && isset($request->{'p2_m'.$j})) {
                        $pointsArr['match_' . $j][$request->{'p1_m' . $j . '_id'}] = $request->{'p1_m' . $j};
                        $pointsArr['match_' . $j][$request->{'p2_m' . $j . '_id'}] = $request->{'p2_m' . $j};

                        $year = date('Y', strtotime($tournament->start));

                        $chk_points = json_decode($tournament->{"group_".$grp_word."_points"}, true);
                        
                        $points_one = isset($chk_points['match_' . $j][$request->{'p1_m' . $j . '_id'}])
                            ? $request->{'p1_m' . $j} - $chk_points['match_' . $j][$request->{'p1_m' . $j . '_id'}]
                            : $request->{'p1_m' . $j};
                        
                        $points_two = isset($chk_points['match_' . $j][$request->{'p2_m' . $j . '_id'}])
                            ? $request->{'p2_m' . $j} - $chk_points['match_' . $j][$request->{'p2_m' . $j . '_id'}]
                            : $request->{'p2_m' . $j};
                        
                        $this->processRanking($year, $request->{'p1_m' . $j . '_id'}, $points_one, $tournament, $request->chk_type);
                        $this->processRanking($year, $request->{'p2_m' . $j . '_id'}, $points_two, $tournament, $request->chk_type);

                    }
                }
                
                $tournament->{"group_".$grp_word."_points"} = json_encode($pointsArr);

                $tournament->save();

                Session::flash('success', 'Points Submitted Successfully!');
                return redirect()->back();
            }
        }
    }


    function processRanking($year, $user_id, $points, $tournament, $chk_type)
    {
        $ranking = Ranking::firstOrNew(['year' => $year, 'user_id' => $user_id]);
        $draw_status = $this->getDrawKey($tournament->draw_status);
        $categories = ['ELITE', 'PRO', 'ADV', 'INT', 'ROOKIE'];
        $isLeague = $chk_type && $chk_type == 'League';
        
        if ($tournament->draw_status == 'Top16 Finals') {    
            $ranking->{'top16_finals'} = ($ranking->{'top16_finals'} ?? 0) + $points;
        } elseif ($isLeague && $tournament->draw_status != 'Top16 Finals') {
            $field = "{$draw_status}_league";
            $ranking->$field = ($ranking->$field ?? 0) + $points;
        } else {
            foreach ($categories as $category) {
                if (strpos($tournament->name, $category) !== false) {
                    $field = "{$draw_status}_" . strtolower($category);
                    $ranking->$field = ($ranking->$field ?? 0) + $points;
                }
            }
        }

        $ranking->save();
    }

    function getDrawKey($draw_status)
    {
        $draw_keys = [
            '1st Draw' => '1st',
            '2nd Draw' => '2nd',
            '3rd Draw' => '3rd',
            '4th Draw' => '4th',
            'Top16 Finals' => 'top16_finals'
        ];

        return $draw_keys[$draw_status] ?? '';
    }


    public function previewRankings()
    {
        $settings = Settings::findOrFail(1);

        $currentYear = date('Y');
        $previousYear = date('Y', strtotime('-1 year'));
        // $currentYear = '2023';
        // $previousYear = '2022';
        
        $users = User::all();
        $quarter_rankings = [];

        
        foreach($users as $user) {
            if($user->rankings()->exists()) {
                $previousYearRankings = $user->rankings()->where('year', $previousYear)->first();
                $currentYearRankings = $user->rankings()->where('year', $currentYear)->first();
                
                if ($settings->rankings_type == null || $settings->rankings_type == '4th Quarter') {
                    
                    // 1st Quarter Calculation
                    if (
                        isset($currentYearRankings->{'1st_elite'}) || isset($currentYearRankings->{'1st_pro'}) || 
                        isset($currentYearRankings->{'1st_adv'}) || isset($currentYearRankings->{'1st_int'}) || 
                        isset($currentYearRankings->{'1st_rookie'}) || 

                        isset($previousYearRankings->{'2nd_elite'}) || isset($previousYearRankings->{'2nd_pro'}) || 
                        isset($previousYearRankings->{'2nd_adv'}) || isset($previousYearRankings->{'2nd_int'}) || 
                        isset($previousYearRankings->{'2nd_rookie'}) ||

                        isset($previousYearRankings->{'3rd_elite'}) || isset($previousYearRankings->{'3rd_pro'}) || 
                        isset($previousYearRankings->{'3rd_adv'}) || isset($previousYearRankings->{'3rd_int'}) || 
                        isset($previousYearRankings->{'3rd_rookie'}) || 
                        
                        isset($previousYearRankings->{'4th_elite'}) || isset($previousYearRankings->{'4th_pro'}) || 
                        isset($previousYearRankings->{'4th_adv'}) || isset($previousYearRankings->{'4th_int'}) || 
                        isset($previousYearRankings->{'4th_rookie'}) ||

                        isset($previousYearRankings->{'1st_league'}) || isset($previousYearRankings->{'2nd_league'}) || 
                        isset($previousYearRankings->{'top16_finals'})
                    ) {
                        
                        if(
                            ($previousYearRankings->{'1st_elite'} ?? null) === null &&
                            ($previousYearRankings->{'1st_pro'} ?? null) === null &&
                            ($previousYearRankings->{'1st_adv'} ?? null) === null &&
                            ($previousYearRankings->{'1st_int'} ?? null) === null &&
                            ($previousYearRankings->{'1st_rookie'} ?? null) === null &&

                            ($previousYearRankings->{'2nd_elite'} ?? null) === null &&
                            ($previousYearRankings->{'2nd_pro'} ?? null) === null &&
                            ($previousYearRankings->{'2nd_adv'} ?? null) === null &&
                            ($previousYearRankings->{'2nd_int'} ?? null) === null &&
                            ($previousYearRankings->{'2nd_rookie'} ?? null) === null &&

                            ($previousYearRankings->{'3rd_elite'} ?? null) === null &&
                            ($previousYearRankings->{'3rd_pro'} ?? null) === null &&
                            ($previousYearRankings->{'3rd_adv'} ?? null) === null &&
                            ($previousYearRankings->{'3rd_int'} ?? null) === null &&
                            ($previousYearRankings->{'3rd_rookie'} ?? null) === null &&

                            ($previousYearRankings->{'4th_elite'} ?? null) === null &&
                            ($previousYearRankings->{'4th_pro'} ?? null) === null &&
                            ($previousYearRankings->{'4th_adv'} ?? null) === null &&
                            ($previousYearRankings->{'4th_int'} ?? null) === null &&
                            ($previousYearRankings->{'4th_rookie'} ?? null) === null &&

                            ($previousYearRankings->{'1st_league'} ?? null) === null &&
                            ($previousYearRankings->{'2nd_league'} ?? null) === null &&
                            ($previousYearRankings->{'top16_finals'} ?? null) === null
                        ) {
                            $isNew = true;
                        } else {
                            $isNew = false;
                        }

                        $total = 
                            ($currentYearRankings->{'1st_elite'} ?? 0) + 
                            ($currentYearRankings->{'1st_pro'} ?? 0) + 
                            ($currentYearRankings->{'1st_adv'} ?? 0) + 
                            ($currentYearRankings->{'1st_int'} ?? 0) + 
                            ($currentYearRankings->{'1st_rookie'} ?? 0) +

                            ($previousYearRankings->{'2nd_elite'} ?? 0) + 
                            ($previousYearRankings->{'2nd_pro'} ?? 0) + 
                            ($previousYearRankings->{'2nd_adv'} ?? 0) + 
                            ($previousYearRankings->{'2nd_int'} ?? 0) + 
                            ($previousYearRankings->{'2nd_rookie'} ?? 0) + 

                            ($previousYearRankings->{'3rd_elite'} ?? 0) + 
                            ($previousYearRankings->{'3rd_pro'} ?? 0) + 
                            ($previousYearRankings->{'3rd_adv'} ?? 0) + 
                            ($previousYearRankings->{'3rd_int'} ?? 0) + 
                            ($previousYearRankings->{'3rd_rookie'} ?? 0) + 

                            ($previousYearRankings->{'4th_elite'} ?? 0) + 
                            ($previousYearRankings->{'4th_pro'} ?? 0) + 
                            ($previousYearRankings->{'4th_adv'} ?? 0) + 
                            ($previousYearRankings->{'4th_int'} ?? 0) + 
                            ($previousYearRankings->{'4th_rookie'} ?? 0) + 

                            ($previousYearRankings->{'1st_league'} ?? 0) + 
                            ($previousYearRankings->{'2nd_league'} ?? 0) +
                            ($previousYearRankings->{'top16_finals'} ?? 0);

                        $played = 
                            (isset($currentYearRankings->{'1st_elite'}) ? 1 : 0) + 
                            (isset($currentYearRankings->{'1st_pro'}) ? 1 : 0) + 
                            (isset($currentYearRankings->{'1st_adv'}) ? 1 : 0) + 
                            (isset($currentYearRankings->{'1st_int'}) ? 1 : 0) + 
                            (isset($currentYearRankings->{'1st_rookie'}) ? 1 : 0) +
                    
                            (isset($previousYearRankings->{'2nd_elite'}) ? 1 : 0) + 
                            (isset($previousYearRankings->{'2nd_pro'}) ? 1 : 0) + 
                            (isset($previousYearRankings->{'2nd_adv'}) ? 1 : 0) + 
                            (isset($previousYearRankings->{'2nd_int'}) ? 1 : 0) + 
                            (isset($previousYearRankings->{'2nd_rookie'}) ? 1 : 0) + 

                            (isset($previousYearRankings->{'3rd_elite'}) ? 1 : 0) + 
                            (isset($previousYearRankings->{'3rd_pro'}) ? 1 : 0) + 
                            (isset($previousYearRankings->{'3rd_adv'}) ? 1 : 0) + 
                            (isset($previousYearRankings->{'3rd_int'}) ? 1 : 0) + 
                            (isset($previousYearRankings->{'3rd_rookie'}) ? 1 : 0) + 

                            (isset($previousYearRankings->{'4th_elite'}) ? 1 : 0) + 
                            (isset($previousYearRankings->{'4th_pro'}) ? 1 : 0) + 
                            (isset($previousYearRankings->{'4th_adv'}) ? 1 : 0) + 
                            (isset($previousYearRankings->{'4th_int'}) ? 1 : 0) + 
                            (isset($previousYearRankings->{'4th_rookie'}) ? 1 : 0) + 
                    
                            (isset($previousYearRankings->{'1st_league'}) ? 1 : 0) + 
                            (isset($previousYearRankings->{'2nd_league'}) ? 1 : 0) +
                            (isset($previousYearRankings->{'top16_finals'}) ? 1 : 0);
                        
                        $quarter_rankings[] = [
                            'player' => $user->name,
                            '1st_elite' => $currentYearRankings->{'1st_elite'} ?? "n/a",
                            '1st_pro' => $currentYearRankings->{'1st_pro'} ?? "n/a",
                            '1st_adv' => $currentYearRankings->{'1st_adv'} ?? "n/a",
                            '1st_int' => $currentYearRankings->{'1st_int'} ?? "n/a",
                            '1st_rookie' => $currentYearRankings->{'1st_rookie'} ?? "n/a",

                            '2nd_elite' => $previousYearRankings->{'2nd_elite'} ?? "n/a",
                            '2nd_pro' => $previousYearRankings->{'2nd_pro'} ?? "n/a",
                            '2nd_adv' => $previousYearRankings->{'2nd_adv'} ?? "n/a",
                            '2nd_int' => $previousYearRankings->{'2nd_int'} ?? "n/a",
                            '2nd_rookie' => $previousYearRankings->{'2nd_rookie'} ?? "n/a",

                            '3rd_elite' => $previousYearRankings->{'3rd_elite'} ?? "n/a",
                            '3rd_pro' => $previousYearRankings->{'3rd_pro'} ?? "n/a",
                            '3rd_adv' => $previousYearRankings->{'3rd_adv'} ?? "n/a",
                            '3rd_int' => $previousYearRankings->{'3rd_int'} ?? "n/a",
                            '3rd_rookie' => $previousYearRankings->{'3rd_rookie'} ?? "n/a",

                            '4th_elite' => $previousYearRankings->{'4th_elite'} ?? "n/a",
                            '4th_pro' => $previousYearRankings->{'4th_pro'} ?? "n/a",
                            '4th_adv' => $previousYearRankings->{'4th_adv'} ?? "n/a",
                            '4th_int' => $previousYearRankings->{'4th_int'} ?? "n/a",
                            '4th_rookie' => $previousYearRankings->{'4th_rookie'} ?? "n/a",

                            '1st_league' => $previousYearRankings->{'1st_league'} ?? "n/a", 
                            '2nd_league' => $previousYearRankings->{'2nd_league'} ?? "n/a",
                            'top16_finals' => $previousYearRankings->{'top16_finals'} ?? "n/a",

                            'total' => $total,
                            'played' => $played,
                            'previous_ranking' => $user->previous_ranking,
                            'current_ranking' => $user->current_ranking,
                            'isNew' => $isNew
                        ];
                    }

                } else if ($settings->rankings_type == '1st Quarter') {
                    
                    // 2nd Quarter Calculation
                    if (
                        isset($currentYearRankings->{'1st_elite'}) || isset($currentYearRankings->{'1st_pro'}) || 
                        isset($currentYearRankings->{'1st_adv'}) || isset($currentYearRankings->{'1st_int'}) || 
                        isset($currentYearRankings->{'1st_rookie'}) || 

                        isset($currentYearRankings->{'2nd_elite'}) || isset($currentYearRankings->{'2nd_pro'}) || 
                        isset($currentYearRankings->{'2nd_adv'}) || isset($currentYearRankings->{'2nd_int'}) || 
                        isset($currentYearRankings->{'2nd_rookie'}) ||

                        isset($previousYearRankings->{'3rd_elite'}) || isset($previousYearRankings->{'3rd_pro'}) || 
                        isset($previousYearRankings->{'3rd_adv'}) || isset($previousYearRankings->{'3rd_int'}) || 
                        isset($previousYearRankings->{'3rd_rookie'}) || 
                        
                        isset($previousYearRankings->{'4th_elite'}) || isset($previousYearRankings->{'4th_pro'}) || 
                        isset($previousYearRankings->{'4th_adv'}) || isset($previousYearRankings->{'4th_int'}) || 
                        isset($previousYearRankings->{'4th_rookie'}) ||

                        isset($currentYearRankings->{'1st_league'}) || isset($previousYearRankings->{'2nd_league'}) || 
                        isset($previousYearRankings->{'top16_finals'})
                    ) {

                        if(
                            ($currentYearRankings->{'1st_elite'} ?? null) === null &&
                            ($currentYearRankings->{'1st_pro'} ?? null) === null &&
                            ($currentYearRankings->{'1st_adv'} ?? null) === null &&
                            ($currentYearRankings->{'1st_int'} ?? null) === null &&
                            ($currentYearRankings->{'1st_rookie'} ?? null) === null &&

                            ($previousYearRankings->{'2nd_elite'} ?? null) === null &&
                            ($previousYearRankings->{'2nd_pro'} ?? null) === null &&
                            ($previousYearRankings->{'2nd_adv'} ?? null) === null &&
                            ($previousYearRankings->{'2nd_int'} ?? null) === null &&
                            ($previousYearRankings->{'2nd_rookie'} ?? null) === null &&

                            ($previousYearRankings->{'3rd_elite'} ?? null) === null &&
                            ($previousYearRankings->{'3rd_pro'} ?? null) === null &&
                            ($previousYearRankings->{'3rd_adv'} ?? null) === null &&
                            ($previousYearRankings->{'3rd_int'} ?? null) === null &&
                            ($previousYearRankings->{'3rd_rookie'} ?? null) === null &&

                            ($previousYearRankings->{'4th_elite'} ?? null) === null &&
                            ($previousYearRankings->{'4th_pro'} ?? null) === null &&
                            ($previousYearRankings->{'4th_adv'} ?? null) === null &&
                            ($previousYearRankings->{'4th_int'} ?? null) === null &&
                            ($previousYearRankings->{'4th_rookie'} ?? null) === null &&

                            ($previousYearRankings->{'1st_league'} ?? null) === null &&
                            ($previousYearRankings->{'2nd_league'} ?? null) === null &&
                            ($previousYearRankings->{'top16_finals'} ?? null) === null
                        ) {
                            $isNew = true;
                        } else {
                            $isNew = false;
                        }

                        $total = 
                            ($currentYearRankings->{'1st_elite'} ?? 0) + 
                            ($currentYearRankings->{'1st_pro'} ?? 0) + 
                            ($currentYearRankings->{'1st_adv'} ?? 0) + 
                            ($currentYearRankings->{'1st_int'} ?? 0) + 
                            ($currentYearRankings->{'1st_rookie'} ?? 0) +

                            ($currentYearRankings->{'2nd_elite'} ?? 0) + 
                            ($currentYearRankings->{'2nd_pro'} ?? 0) + 
                            ($currentYearRankings->{'2nd_adv'} ?? 0) + 
                            ($currentYearRankings->{'2nd_int'} ?? 0) + 
                            ($currentYearRankings->{'2nd_rookie'} ?? 0) + 

                            ($previousYearRankings->{'3rd_elite'} ?? 0) + 
                            ($previousYearRankings->{'3rd_pro'} ?? 0) + 
                            ($previousYearRankings->{'3rd_adv'} ?? 0) + 
                            ($previousYearRankings->{'3rd_int'} ?? 0) + 
                            ($previousYearRankings->{'3rd_rookie'} ?? 0) + 

                            ($previousYearRankings->{'4th_elite'} ?? 0) + 
                            ($previousYearRankings->{'4th_pro'} ?? 0) + 
                            ($previousYearRankings->{'4th_adv'} ?? 0) + 
                            ($previousYearRankings->{'4th_int'} ?? 0) + 
                            ($previousYearRankings->{'4th_rookie'} ?? 0) +

                            ($currentYearRankings->{'1st_league'} ?? 0) + 
                            ($previousYearRankings->{'2nd_league'} ?? 0) +
                            ($previousYearRankings->{'top16_finals'} ?? 0);

                        $played = 
                            (isset($currentYearRankings->{'1st_elite'}) ? 1 : 0) + 
                            (isset($currentYearRankings->{'1st_pro'}) ? 1 : 0) + 
                            (isset($currentYearRankings->{'1st_adv'}) ? 1 : 0) + 
                            (isset($currentYearRankings->{'1st_int'}) ? 1 : 0) + 
                            (isset($currentYearRankings->{'1st_rookie'}) ? 1 : 0) +
                    
                            (isset($currentYearRankings->{'2nd_elite'}) ? 1 : 0) + 
                            (isset($currentYearRankings->{'2nd_pro'}) ? 1 : 0) + 
                            (isset($currentYearRankings->{'2nd_adv'}) ? 1 : 0) + 
                            (isset($currentYearRankings->{'2nd_int'}) ? 1 : 0) + 
                            (isset($currentYearRankings->{'2nd_rookie'}) ? 1 : 0) + 

                            (isset($previousYearRankings->{'3rd_elite'}) ? 1 : 0) + 
                            (isset($previousYearRankings->{'3rd_pro'}) ? 1 : 0) + 
                            (isset($previousYearRankings->{'3rd_adv'}) ? 1 : 0) + 
                            (isset($previousYearRankings->{'3rd_int'}) ? 1 : 0) + 
                            (isset($previousYearRankings->{'3rd_rookie'}) ? 1 : 0) + 

                            (isset($previousYearRankings->{'4th_elite'}) ? 1 : 0) + 
                            (isset($previousYearRankings->{'4th_pro'}) ? 1 : 0) + 
                            (isset($previousYearRankings->{'4th_adv'}) ? 1 : 0) + 
                            (isset($previousYearRankings->{'4th_int'}) ? 1 : 0) + 
                            (isset($previousYearRankings->{'4th_rookie'}) ? 1 : 0) + 
                    
                            (isset($currentYearRankings->{'1st_league'}) ? 1 : 0) + 
                            (isset($previousYearRankings->{'2nd_league'}) ? 1 : 0) +
                            (isset($previousYearRankings->{'top16_finals'}) ? 1 : 0);

                        $quarter_rankings[] = [
                            'player' => $user->name,
                            '1st_elite' => $currentYearRankings->{'1st_elite'} ?? "n/a",
                            '1st_pro' => $currentYearRankings->{'1st_pro'} ?? "n/a",
                            '1st_adv' => $currentYearRankings->{'1st_adv'} ?? "n/a",
                            '1st_int' => $currentYearRankings->{'1st_int'} ?? "n/a",
                            '1st_rookie' => $currentYearRankings->{'1st_rookie'} ?? "n/a",

                            '2nd_elite' => $currentYearRankings->{'2nd_elite'} ?? "n/a",
                            '2nd_pro' => $currentYearRankings->{'2nd_pro'} ?? "n/a",
                            '2nd_adv' => $currentYearRankings->{'2nd_adv'} ?? "n/a",
                            '2nd_int' => $currentYearRankings->{'2nd_int'} ?? "n/a",
                            '2nd_rookie' => $currentYearRankings->{'2nd_rookie'} ?? "n/a",

                            '3rd_elite' => $previousYearRankings->{'3rd_elite'} ?? "n/a",
                            '3rd_pro' => $previousYearRankings->{'3rd_pro'} ?? "n/a",
                            '3rd_adv' => $previousYearRankings->{'3rd_adv'} ?? "n/a",
                            '3rd_int' => $previousYearRankings->{'3rd_int'} ?? "n/a",
                            '3rd_rookie' => $previousYearRankings->{'3rd_rookie'} ?? "n/a",

                            '4th_elite' => $previousYearRankings->{'4th_elite'} ?? "n/a",
                            '4th_pro' => $previousYearRankings->{'4th_pro'} ?? "n/a",
                            '4th_adv' => $previousYearRankings->{'4th_adv'} ?? "n/a",
                            '4th_int' => $previousYearRankings->{'4th_int'} ?? "n/a",
                            '4th_rookie' => $previousYearRankings->{'4th_rookie'} ?? "n/a",

                            '1st_league' => $currentYearRankings->{'1st_league'} ?? "n/a", 
                            '2nd_league' => $previousYearRankings->{'2nd_league'} ?? "n/a",
                            'top16_finals' => $previousYearRankings->{'top16_finals'} ?? "n/a",

                            'total' => $total,
                            'played' => $played,
                            'previous_ranking' => $user->previous_ranking,
                            'current_ranking' => $user->current_ranking,
                            'isNew' => $isNew
                        ];
                    }

                } else if ($settings->rankings_type == '2nd Quarter') {
                    
                    // 3rd Quarter Calculation
                    if (
                        isset($currentYearRankings->{'1st_elite'}) || isset($currentYearRankings->{'1st_pro'}) || 
                        isset($currentYearRankings->{'1st_adv'}) || isset($currentYearRankings->{'1st_int'}) || 
                        isset($currentYearRankings->{'1st_rookie'}) || 

                        isset($currentYearRankings->{'2nd_elite'}) || isset($currentYearRankings->{'2nd_pro'}) || 
                        isset($currentYearRankings->{'2nd_adv'}) || isset($currentYearRankings->{'2nd_int'}) || 
                        isset($currentYearRankings->{'2nd_rookie'}) ||

                        isset($currentYearRankings->{'3rd_elite'}) || isset($currentYearRankings->{'3rd_pro'}) || 
                        isset($currentYearRankings->{'3rd_adv'}) || isset($currentYearRankings->{'3rd_int'}) || 
                        isset($currentYearRankings->{'3rd_rookie'}) || 
                        
                        isset($previousYearRankings->{'4th_elite'}) || isset($previousYearRankings->{'4th_pro'}) || 
                        isset($previousYearRankings->{'4th_adv'}) || isset($previousYearRankings->{'4th_int'}) || 
                        isset($previousYearRankings->{'4th_rookie'}) ||

                        isset($currentYearRankings->{'1st_league'}) || isset($previousYearRankings->{'2nd_league'}) || 
                        isset($previousYearRankings->{'top16_finals'})
                    ) {

                        if(
                            ($currentYearRankings->{'1st_elite'} ?? null) === null &&
                            ($currentYearRankings->{'1st_pro'} ?? null) === null &&
                            ($currentYearRankings->{'1st_adv'} ?? null) === null &&
                            ($currentYearRankings->{'1st_int'} ?? null) === null &&
                            ($currentYearRankings->{'1st_rookie'} ?? null) === null &&

                            ($currentYearRankings->{'2nd_elite'} ?? null) === null &&
                            ($currentYearRankings->{'2nd_pro'} ?? null) === null &&
                            ($currentYearRankings->{'2nd_adv'} ?? null) === null &&
                            ($currentYearRankings->{'2nd_int'} ?? null) === null &&
                            ($currentYearRankings->{'2nd_rookie'} ?? null) === null &&

                            ($previousYearRankings->{'3rd_elite'} ?? null) === null &&
                            ($previousYearRankings->{'3rd_pro'} ?? null) === null &&
                            ($previousYearRankings->{'3rd_adv'} ?? null) === null &&
                            ($previousYearRankings->{'3rd_int'} ?? null) === null &&
                            ($previousYearRankings->{'3rd_rookie'} ?? null) === null &&

                            ($previousYearRankings->{'4th_elite'} ?? null) === null &&
                            ($previousYearRankings->{'4th_pro'} ?? null) === null &&
                            ($previousYearRankings->{'4th_adv'} ?? null) === null &&
                            ($previousYearRankings->{'4th_int'} ?? null) === null &&
                            ($previousYearRankings->{'4th_rookie'} ?? null) === null &&

                            ($currentYearRankings->{'1st_league'} ?? null) === null &&
                            ($previousYearRankings->{'2nd_league'} ?? null) === null &&
                            ($previousYearRankings->{'top16_finals'} ?? null) === null
                        ) {
                            $isNew = true;
                        } else {
                            $isNew = false;
                        }

                        $total = 
                            ($currentYearRankings->{'1st_elite'} ?? 0) + 
                            ($currentYearRankings->{'1st_pro'} ?? 0) + 
                            ($currentYearRankings->{'1st_adv'} ?? 0) + 
                            ($currentYearRankings->{'1st_int'} ?? 0) + 
                            ($currentYearRankings->{'1st_rookie'} ?? 0) +

                            ($currentYearRankings->{'2nd_elite'} ?? 0) + 
                            ($currentYearRankings->{'2nd_pro'} ?? 0) + 
                            ($currentYearRankings->{'2nd_adv'} ?? 0) + 
                            ($currentYearRankings->{'2nd_int'} ?? 0) + 
                            ($currentYearRankings->{'2nd_rookie'} ?? 0) + 

                            ($currentYearRankings->{'3rd_elite'} ?? 0) + 
                            ($currentYearRankings->{'3rd_pro'} ?? 0) + 
                            ($currentYearRankings->{'3rd_adv'} ?? 0) + 
                            ($currentYearRankings->{'3rd_int'} ?? 0) + 
                            ($currentYearRankings->{'3rd_rookie'} ?? 0) + 

                            ($previousYearRankings->{'4th_elite'} ?? 0) + 
                            ($previousYearRankings->{'4th_pro'} ?? 0) + 
                            ($previousYearRankings->{'4th_adv'} ?? 0) + 
                            ($previousYearRankings->{'4th_int'} ?? 0) + 
                            ($previousYearRankings->{'4th_rookie'} ?? 0) + 

                            ($currentYearRankings->{'1st_league'} ?? 0) + 
                            ($previousYearRankings->{'2nd_league'} ?? 0) +
                            ($previousYearRankings->{'top16_finals'} ?? 0);

                        $played = 
                            (isset($currentYearRankings->{'1st_elite'}) ? 1 : 0) + 
                            (isset($currentYearRankings->{'1st_pro'}) ? 1 : 0) + 
                            (isset($currentYearRankings->{'1st_adv'}) ? 1 : 0) + 
                            (isset($currentYearRankings->{'1st_int'}) ? 1 : 0) + 
                            (isset($currentYearRankings->{'1st_rookie'}) ? 1 : 0) +
                    
                            (isset($currentYearRankings->{'2nd_elite'}) ? 1 : 0) + 
                            (isset($currentYearRankings->{'2nd_pro'}) ? 1 : 0) + 
                            (isset($currentYearRankings->{'2nd_adv'}) ? 1 : 0) + 
                            (isset($currentYearRankings->{'2nd_int'}) ? 1 : 0) + 
                            (isset($currentYearRankings->{'2nd_rookie'}) ? 1 : 0) + 

                            (isset($currentYearRankings->{'3rd_elite'}) ? 1 : 0) + 
                            (isset($currentYearRankings->{'3rd_pro'}) ? 1 : 0) + 
                            (isset($currentYearRankings->{'3rd_adv'}) ? 1 : 0) + 
                            (isset($currentYearRankings->{'3rd_int'}) ? 1 : 0) + 
                            (isset($currentYearRankings->{'3rd_rookie'}) ? 1 : 0) + 

                            (isset($previousYearRankings->{'4th_elite'}) ? 1 : 0) + 
                            (isset($previousYearRankings->{'4th_pro'}) ? 1 : 0) + 
                            (isset($previousYearRankings->{'4th_adv'}) ? 1 : 0) + 
                            (isset($previousYearRankings->{'4th_int'}) ? 1 : 0) + 
                            (isset($previousYearRankings->{'4th_rookie'}) ? 1 : 0) + 
                    
                            (isset($currentYearRankings->{'1st_league'}) ? 1 : 0) + 
                            (isset($previousYearRankings->{'2nd_league'}) ? 1 : 0) +
                            (isset($previousYearRankings->{'top16_finals'}) ? 1 : 0);

                        $quarter_rankings[] = [
                            'player' => $user->name,
                            '1st_elite' => $currentYearRankings->{'1st_elite'} ?? "n/a",
                            '1st_pro' => $currentYearRankings->{'1st_pro'} ?? "n/a",
                            '1st_adv' => $currentYearRankings->{'1st_adv'} ?? "n/a",
                            '1st_int' => $currentYearRankings->{'1st_int'} ?? "n/a",
                            '1st_rookie' => $currentYearRankings->{'1st_rookie'} ?? "n/a",

                            '2nd_elite' => $currentYearRankings->{'2nd_elite'} ?? "n/a",
                            '2nd_pro' => $currentYearRankings->{'2nd_pro'} ?? "n/a",
                            '2nd_adv' => $currentYearRankings->{'2nd_adv'} ?? "n/a",
                            '2nd_int' => $currentYearRankings->{'2nd_int'} ?? "n/a",
                            '2nd_rookie' => $currentYearRankings->{'2nd_rookie'} ?? "n/a",

                            '3rd_elite' => $currentYearRankings->{'3rd_elite'} ?? "n/a",
                            '3rd_pro' => $currentYearRankings->{'3rd_pro'} ?? "n/a",
                            '3rd_adv' => $currentYearRankings->{'3rd_adv'} ?? "n/a",
                            '3rd_int' => $currentYearRankings->{'3rd_int'} ?? "n/a",
                            '3rd_rookie' => $currentYearRankings->{'3rd_rookie'} ?? "n/a",

                            '4th_elite' => $previousYearRankings->{'4th_elite'} ?? "n/a",
                            '4th_pro' => $previousYearRankings->{'4th_pro'} ?? "n/a",
                            '4th_adv' => $previousYearRankings->{'4th_adv'} ?? "n/a",
                            '4th_int' => $previousYearRankings->{'4th_int'} ?? "n/a",
                            '4th_rookie' => $previousYearRankings->{'4th_rookie'} ?? "n/a",

                            '1st_league' => $currentYearRankings->{'1st_league'} ?? "n/a", 
                            '2nd_league' => $previousYearRankings->{'2nd_league'} ?? "n/a",
                            'top16_finals' => $previousYearRankings->{'top16_finals'} ?? "n/a",

                            'total' => $total,
                            'played' => $played,
                            'previous_ranking' => $user->previous_ranking,
                            'current_ranking' => $user->current_ranking,
                            'isNew' => $isNew
                        ];
                    }

                } else if ($settings->rankings_type == '3rd Quarter') {
                    
                    // 4th Quarter Calculation
                    if (
                        isset($currentYearRankings->{'1st_elite'}) || isset($currentYearRankings->{'1st_pro'}) || 
                        isset($currentYearRankings->{'1st_adv'}) || isset($currentYearRankings->{'1st_int'}) || 
                        isset($currentYearRankings->{'1st_rookie'}) || 

                        isset($currentYearRankings->{'2nd_elite'}) || isset($currentYearRankings->{'2nd_pro'}) || 
                        isset($currentYearRankings->{'2nd_adv'}) || isset($currentYearRankings->{'2nd_int'}) || 
                        isset($currentYearRankings->{'2nd_rookie'}) ||

                        isset($currentYearRankings->{'3rd_elite'}) || isset($currentYearRankings->{'3rd_pro'}) || 
                        isset($currentYearRankings->{'3rd_adv'}) || isset($currentYearRankings->{'3rd_int'}) || 
                        isset($currentYearRankings->{'3rd_rookie'}) || 
                        
                        isset($currentYearRankings->{'4th_elite'}) || isset($currentYearRankings->{'4th_pro'}) || 
                        isset($currentYearRankings->{'4th_adv'}) || isset($currentYearRankings->{'4th_int'}) || 
                        isset($currentYearRankings->{'4th_rookie'}) ||

                        isset($currentYearRankings->{'1st_league'}) || isset($currentYearRankings->{'2nd_league'}) || 
                        isset($currentYearRankings->{'top16_finals'})
                    ) {

                        if(
                            ($currentYearRankings->{'1st_elite'} ?? null) === null &&
                            ($currentYearRankings->{'1st_pro'} ?? null) === null &&
                            ($currentYearRankings->{'1st_adv'} ?? null) === null &&
                            ($currentYearRankings->{'1st_int'} ?? null) === null &&
                            ($currentYearRankings->{'1st_rookie'} ?? null) === null &&

                            ($currentYearRankings->{'2nd_elite'} ?? null) === null &&
                            ($currentYearRankings->{'2nd_pro'} ?? null) === null &&
                            ($currentYearRankings->{'2nd_adv'} ?? null) === null &&
                            ($currentYearRankings->{'2nd_int'} ?? null) === null &&
                            ($currentYearRankings->{'2nd_rookie'} ?? null) === null &&

                            ($currentYearRankings->{'3rd_elite'} ?? null) === null &&
                            ($currentYearRankings->{'3rd_pro'} ?? null) === null &&
                            ($currentYearRankings->{'3rd_adv'} ?? null) === null &&
                            ($currentYearRankings->{'3rd_int'} ?? null) === null &&
                            ($currentYearRankings->{'3rd_rookie'} ?? null) === null &&

                            ($previousYearRankings->{'4th_elite'} ?? null) === null &&
                            ($previousYearRankings->{'4th_pro'} ?? null) === null &&
                            ($previousYearRankings->{'4th_adv'} ?? null) === null &&
                            ($previousYearRankings->{'4th_int'} ?? null) === null &&
                            ($previousYearRankings->{'4th_rookie'} ?? null) === null &&

                            ($currentYearRankings->{'1st_league'} ?? null) === null &&
                            ($previousYearRankings->{'2nd_league'} ?? null) === null &&
                            ($previousYearRankings->{'top16_finals'} ?? null) === null
                        ) {
                            $isNew = true;
                        } else {
                            $isNew = false;
                        }

                        $total = 
                            ($currentYearRankings->{'1st_elite'} ?? 0) + 
                            ($currentYearRankings->{'1st_pro'} ?? 0) + 
                            ($currentYearRankings->{'1st_adv'} ?? 0) + 
                            ($currentYearRankings->{'1st_int'} ?? 0) + 
                            ($currentYearRankings->{'1st_rookie'} ?? 0) +

                            ($currentYearRankings->{'2nd_elite'} ?? 0) + 
                            ($currentYearRankings->{'2nd_pro'} ?? 0) + 
                            ($currentYearRankings->{'2nd_adv'} ?? 0) + 
                            ($currentYearRankings->{'2nd_int'} ?? 0) + 
                            ($currentYearRankings->{'2nd_rookie'} ?? 0) + 

                            ($currentYearRankings->{'3rd_elite'} ?? 0) + 
                            ($currentYearRankings->{'3rd_pro'} ?? 0) + 
                            ($currentYearRankings->{'3rd_adv'} ?? 0) + 
                            ($currentYearRankings->{'3rd_int'} ?? 0) + 
                            ($currentYearRankings->{'3rd_rookie'} ?? 0) + 

                            ($currentYearRankings->{'4th_elite'} ?? 0) + 
                            ($currentYearRankings->{'4th_pro'} ?? 0) + 
                            ($currentYearRankings->{'4th_adv'} ?? 0) + 
                            ($currentYearRankings->{'4th_int'} ?? 0) + 
                            ($currentYearRankings->{'4th_rookie'} ?? 0) +

                            ($currentYearRankings->{'1st_league'} ?? 0) + 
                            ($currentYearRankings->{'2nd_league'} ?? 0) +
                            ($currentYearRankings->{'top16_finals'} ?? 0);

                        $played = 
                            (isset($currentYearRankings->{'1st_elite'}) ? 1 : 0) + 
                            (isset($currentYearRankings->{'1st_pro'}) ? 1 : 0) + 
                            (isset($currentYearRankings->{'1st_adv'}) ? 1 : 0) + 
                            (isset($currentYearRankings->{'1st_int'}) ? 1 : 0) + 
                            (isset($currentYearRankings->{'1st_rookie'}) ? 1 : 0) +
                    
                            (isset($currentYearRankings->{'2nd_elite'}) ? 1 : 0) + 
                            (isset($currentYearRankings->{'2nd_pro'}) ? 1 : 0) + 
                            (isset($currentYearRankings->{'2nd_adv'}) ? 1 : 0) + 
                            (isset($currentYearRankings->{'2nd_int'}) ? 1 : 0) + 
                            (isset($currentYearRankings->{'2nd_rookie'}) ? 1 : 0) + 

                            (isset($currentYearRankings->{'3rd_elite'}) ? 1 : 0) + 
                            (isset($currentYearRankings->{'3rd_pro'}) ? 1 : 0) + 
                            (isset($currentYearRankings->{'3rd_adv'}) ? 1 : 0) + 
                            (isset($currentYearRankings->{'3rd_int'}) ? 1 : 0) + 
                            (isset($currentYearRankings->{'3rd_rookie'}) ? 1 : 0) + 

                            (isset($currentYearRankings->{'4th_elite'}) ? 1 : 0) + 
                            (isset($currentYearRankings->{'4th_pro'}) ? 1 : 0) + 
                            (isset($currentYearRankings->{'4th_adv'}) ? 1 : 0) + 
                            (isset($currentYearRankings->{'4th_int'}) ? 1 : 0) + 
                            (isset($currentYearRankings->{'4th_rookie'}) ? 1 : 0) + 
                    
                            (isset($currentYearRankings->{'1st_league'}) ? 1 : 0) + 
                            (isset($currentYearRankings->{'2nd_league'}) ? 1 : 0) +
                            (isset($currentYearRankings->{'top16_finals'}) ? 1 : 0);

                        $quarter_rankings[] = [
                            'player' => $user->name,
                            '1st_elite' => $currentYearRankings->{'1st_elite'} ?? "n/a",
                            '1st_pro' => $currentYearRankings->{'1st_pro'} ?? "n/a",
                            '1st_adv' => $currentYearRankings->{'1st_adv'} ?? "n/a",
                            '1st_int' => $currentYearRankings->{'1st_int'} ?? "n/a",
                            '1st_rookie' => $currentYearRankings->{'1st_rookie'} ?? "n/a",

                            '2nd_elite' => $currentYearRankings->{'2nd_elite'} ?? "n/a",
                            '2nd_pro' => $currentYearRankings->{'2nd_pro'} ?? "n/a",
                            '2nd_adv' => $currentYearRankings->{'2nd_adv'} ?? "n/a",
                            '2nd_int' => $currentYearRankings->{'2nd_int'} ?? "n/a",
                            '2nd_rookie' => $currentYearRankings->{'2nd_rookie'} ?? "n/a",

                            '3rd_elite' => $currentYearRankings->{'3rd_elite'} ?? "n/a",
                            '3rd_pro' => $currentYearRankings->{'3rd_pro'} ?? "n/a",
                            '3rd_adv' => $currentYearRankings->{'3rd_adv'} ?? "n/a",
                            '3rd_int' => $currentYearRankings->{'3rd_int'} ?? "n/a",
                            '3rd_rookie' => $currentYearRankings->{'3rd_rookie'} ?? "n/a",

                            '4th_elite' => $currentYearRankings->{'4th_elite'} ?? "n/a",
                            '4th_pro' => $currentYearRankings->{'4th_pro'} ?? "n/a",
                            '4th_adv' => $currentYearRankings->{'4th_adv'} ?? "n/a",
                            '4th_int' => $currentYearRankings->{'4th_int'} ?? "n/a",
                            '4th_rookie' => $currentYearRankings->{'4th_rookie'} ?? "n/a",

                            '1st_league' => $currentYearRankings->{'1st_league'} ?? "n/a", 
                            '2nd_league' => $currentYearRankings->{'2nd_league'} ?? "n/a",
                            'top16_finals' => $currentYearRankings->{'top16_finals'} ?? "n/a",

                            'total' => $total,
                            'played' => $played,
                            'previous_ranking' => $user->previous_ranking,
                            'current_ranking' => $user->current_ranking,
                            'isNew' => $isNew
                        ];
                    }

                }
            
            }
        }

        usort($quarter_rankings, function($a, $b) {
            return $b['total'] <=> $a['total'];
        });
        
        $rank = 1;
        $skip = 1;
        for ($i = 0; $i < count($quarter_rankings); $i++) {
            if ($i > 0 && $quarter_rankings[$i]['total'] != $quarter_rankings[$i - 1]['total']) {
                $rank += $skip;
                $skip = 1;
            } else if ($i > 0 && $quarter_rankings[$i]['total'] == $quarter_rankings[$i - 1]['total']) {
                $skip++;
            }
            $quarter_rankings[$i]['ranking'] = $rank;
        }
        
        return view('backend.administrator.previewRankings', compact('settings', 'quarter_rankings'));
        
    }

    public function publishRankings(Request $request)
    {
        $this->validate($request, [
            'rankings_type' => 'required',
        ]);

        $rankings_type = $request->rankings_type;

        $settings = Settings::findOrFail(1);

        $currentYear = date('Y');
        $previousYear = date('Y', strtotime('-1 year'));
        // $currentYear = '2023';
        // $previousYear = '2022';
        
        $users = User::all();
        $quarter_rankings = [];

        
        foreach($users as $user) {
            if($user->rankings()->exists()) {
                $previousYearRankings = $user->rankings()->where('year', $previousYear)->first();
                $currentYearRankings = $user->rankings()->where('year', $currentYear)->first();
                
                if ($settings->rankings_type == null || $settings->rankings_type == '4th Quarter') {
                    
                    // 1st Quarter Calculation
                    if (
                        isset($currentYearRankings->{'1st_elite'}) || isset($currentYearRankings->{'1st_pro'}) || 
                        isset($currentYearRankings->{'1st_adv'}) || isset($currentYearRankings->{'1st_int'}) || 
                        isset($currentYearRankings->{'1st_rookie'}) || 

                        isset($previousYearRankings->{'2nd_elite'}) || isset($previousYearRankings->{'2nd_pro'}) || 
                        isset($previousYearRankings->{'2nd_adv'}) || isset($previousYearRankings->{'2nd_int'}) || 
                        isset($previousYearRankings->{'2nd_rookie'}) ||

                        isset($previousYearRankings->{'3rd_elite'}) || isset($previousYearRankings->{'3rd_pro'}) || 
                        isset($previousYearRankings->{'3rd_adv'}) || isset($previousYearRankings->{'3rd_int'}) || 
                        isset($previousYearRankings->{'3rd_rookie'}) || 
                        
                        isset($previousYearRankings->{'4th_elite'}) || isset($previousYearRankings->{'4th_pro'}) || 
                        isset($previousYearRankings->{'4th_adv'}) || isset($previousYearRankings->{'4th_int'}) || 
                        isset($previousYearRankings->{'4th_rookie'}) ||

                        isset($previousYearRankings->{'1st_league'}) || isset($previousYearRankings->{'2nd_league'}) || 
                        isset($previousYearRankings->{'top16_finals'})
                    ) {
                        
                        if(
                            ($previousYearRankings->{'1st_elite'} ?? null) === null &&
                            ($previousYearRankings->{'1st_pro'} ?? null) === null &&
                            ($previousYearRankings->{'1st_adv'} ?? null) === null &&
                            ($previousYearRankings->{'1st_int'} ?? null) === null &&
                            ($previousYearRankings->{'1st_rookie'} ?? null) === null &&

                            ($previousYearRankings->{'2nd_elite'} ?? null) === null &&
                            ($previousYearRankings->{'2nd_pro'} ?? null) === null &&
                            ($previousYearRankings->{'2nd_adv'} ?? null) === null &&
                            ($previousYearRankings->{'2nd_int'} ?? null) === null &&
                            ($previousYearRankings->{'2nd_rookie'} ?? null) === null &&

                            ($previousYearRankings->{'3rd_elite'} ?? null) === null &&
                            ($previousYearRankings->{'3rd_pro'} ?? null) === null &&
                            ($previousYearRankings->{'3rd_adv'} ?? null) === null &&
                            ($previousYearRankings->{'3rd_int'} ?? null) === null &&
                            ($previousYearRankings->{'3rd_rookie'} ?? null) === null &&

                            ($previousYearRankings->{'4th_elite'} ?? null) === null &&
                            ($previousYearRankings->{'4th_pro'} ?? null) === null &&
                            ($previousYearRankings->{'4th_adv'} ?? null) === null &&
                            ($previousYearRankings->{'4th_int'} ?? null) === null &&
                            ($previousYearRankings->{'4th_rookie'} ?? null) === null &&

                            ($previousYearRankings->{'1st_league'} ?? null) === null &&
                            ($previousYearRankings->{'2nd_league'} ?? null) === null &&
                            ($previousYearRankings->{'top16_finals'} ?? null) === null
                        ) {
                            $isNew = true;
                        } else {
                            $isNew = false;
                        }

                        $total = 
                            ($currentYearRankings->{'1st_elite'} ?? 0) + 
                            ($currentYearRankings->{'1st_pro'} ?? 0) + 
                            ($currentYearRankings->{'1st_adv'} ?? 0) + 
                            ($currentYearRankings->{'1st_int'} ?? 0) + 
                            ($currentYearRankings->{'1st_rookie'} ?? 0) +

                            ($previousYearRankings->{'2nd_elite'} ?? 0) + 
                            ($previousYearRankings->{'2nd_pro'} ?? 0) + 
                            ($previousYearRankings->{'2nd_adv'} ?? 0) + 
                            ($previousYearRankings->{'2nd_int'} ?? 0) + 
                            ($previousYearRankings->{'2nd_rookie'} ?? 0) + 

                            ($previousYearRankings->{'3rd_elite'} ?? 0) + 
                            ($previousYearRankings->{'3rd_pro'} ?? 0) + 
                            ($previousYearRankings->{'3rd_adv'} ?? 0) + 
                            ($previousYearRankings->{'3rd_int'} ?? 0) + 
                            ($previousYearRankings->{'3rd_rookie'} ?? 0) + 

                            ($previousYearRankings->{'4th_elite'} ?? 0) + 
                            ($previousYearRankings->{'4th_pro'} ?? 0) + 
                            ($previousYearRankings->{'4th_adv'} ?? 0) + 
                            ($previousYearRankings->{'4th_int'} ?? 0) + 
                            ($previousYearRankings->{'4th_rookie'} ?? 0) + 

                            ($previousYearRankings->{'1st_league'} ?? 0) + 
                            ($previousYearRankings->{'2nd_league'} ?? 0) +
                            ($previousYearRankings->{'top16_finals'} ?? 0);

                        $played = 
                            (isset($currentYearRankings->{'1st_elite'}) ? 1 : 0) + 
                            (isset($currentYearRankings->{'1st_pro'}) ? 1 : 0) + 
                            (isset($currentYearRankings->{'1st_adv'}) ? 1 : 0) + 
                            (isset($currentYearRankings->{'1st_int'}) ? 1 : 0) + 
                            (isset($currentYearRankings->{'1st_rookie'}) ? 1 : 0) +
                    
                            (isset($previousYearRankings->{'2nd_elite'}) ? 1 : 0) + 
                            (isset($previousYearRankings->{'2nd_pro'}) ? 1 : 0) + 
                            (isset($previousYearRankings->{'2nd_adv'}) ? 1 : 0) + 
                            (isset($previousYearRankings->{'2nd_int'}) ? 1 : 0) + 
                            (isset($previousYearRankings->{'2nd_rookie'}) ? 1 : 0) + 

                            (isset($previousYearRankings->{'3rd_elite'}) ? 1 : 0) + 
                            (isset($previousYearRankings->{'3rd_pro'}) ? 1 : 0) + 
                            (isset($previousYearRankings->{'3rd_adv'}) ? 1 : 0) + 
                            (isset($previousYearRankings->{'3rd_int'}) ? 1 : 0) + 
                            (isset($previousYearRankings->{'3rd_rookie'}) ? 1 : 0) + 

                            (isset($previousYearRankings->{'4th_elite'}) ? 1 : 0) + 
                            (isset($previousYearRankings->{'4th_pro'}) ? 1 : 0) + 
                            (isset($previousYearRankings->{'4th_adv'}) ? 1 : 0) + 
                            (isset($previousYearRankings->{'4th_int'}) ? 1 : 0) + 
                            (isset($previousYearRankings->{'4th_rookie'}) ? 1 : 0) + 
                    
                            (isset($previousYearRankings->{'1st_league'}) ? 1 : 0) + 
                            (isset($previousYearRankings->{'2nd_league'}) ? 1 : 0) +
                            (isset($previousYearRankings->{'top16_finals'}) ? 1 : 0);
                        
                        $quarter_rankings[] = [
                            'user_id' => $user->id,
                            'player' => $user->name,
                            '1st_elite' => $currentYearRankings->{'1st_elite'} ?? "n/a",
                            '1st_pro' => $currentYearRankings->{'1st_pro'} ?? "n/a",
                            '1st_adv' => $currentYearRankings->{'1st_adv'} ?? "n/a",
                            '1st_int' => $currentYearRankings->{'1st_int'} ?? "n/a",
                            '1st_rookie' => $currentYearRankings->{'1st_rookie'} ?? "n/a",

                            '2nd_elite' => $previousYearRankings->{'2nd_elite'} ?? "n/a",
                            '2nd_pro' => $previousYearRankings->{'2nd_pro'} ?? "n/a",
                            '2nd_adv' => $previousYearRankings->{'2nd_adv'} ?? "n/a",
                            '2nd_int' => $previousYearRankings->{'2nd_int'} ?? "n/a",
                            '2nd_rookie' => $previousYearRankings->{'2nd_rookie'} ?? "n/a",

                            '3rd_elite' => $previousYearRankings->{'3rd_elite'} ?? "n/a",
                            '3rd_pro' => $previousYearRankings->{'3rd_pro'} ?? "n/a",
                            '3rd_adv' => $previousYearRankings->{'3rd_adv'} ?? "n/a",
                            '3rd_int' => $previousYearRankings->{'3rd_int'} ?? "n/a",
                            '3rd_rookie' => $previousYearRankings->{'3rd_rookie'} ?? "n/a",

                            '4th_elite' => $previousYearRankings->{'4th_elite'} ?? "n/a",
                            '4th_pro' => $previousYearRankings->{'4th_pro'} ?? "n/a",
                            '4th_adv' => $previousYearRankings->{'4th_adv'} ?? "n/a",
                            '4th_int' => $previousYearRankings->{'4th_int'} ?? "n/a",
                            '4th_rookie' => $previousYearRankings->{'4th_rookie'} ?? "n/a",

                            '1st_league' => $previousYearRankings->{'1st_league'} ?? "n/a", 
                            '2nd_league' => $previousYearRankings->{'2nd_league'} ?? "n/a",
                            'top16_finals' => $previousYearRankings->{'top16_finals'} ?? "n/a",

                            'total' => $total,
                            'played' => $played,
                            'previous_ranking' => $user->previous_ranking,
                            'current_ranking' => $user->current_ranking,
                            'isNew' => $isNew
                        ];
                    }

                } else if ($settings->rankings_type == '1st Quarter') {
                    
                    // 2nd Quarter Calculation
                    if (
                        isset($currentYearRankings->{'1st_elite'}) || isset($currentYearRankings->{'1st_pro'}) || 
                        isset($currentYearRankings->{'1st_adv'}) || isset($currentYearRankings->{'1st_int'}) || 
                        isset($currentYearRankings->{'1st_rookie'}) || 

                        isset($currentYearRankings->{'2nd_elite'}) || isset($currentYearRankings->{'2nd_pro'}) || 
                        isset($currentYearRankings->{'2nd_adv'}) || isset($currentYearRankings->{'2nd_int'}) || 
                        isset($currentYearRankings->{'2nd_rookie'}) ||

                        isset($previousYearRankings->{'3rd_elite'}) || isset($previousYearRankings->{'3rd_pro'}) || 
                        isset($previousYearRankings->{'3rd_adv'}) || isset($previousYearRankings->{'3rd_int'}) || 
                        isset($previousYearRankings->{'3rd_rookie'}) || 
                        
                        isset($previousYearRankings->{'4th_elite'}) || isset($previousYearRankings->{'4th_pro'}) || 
                        isset($previousYearRankings->{'4th_adv'}) || isset($previousYearRankings->{'4th_int'}) || 
                        isset($previousYearRankings->{'4th_rookie'}) ||

                        isset($currentYearRankings->{'1st_league'}) || isset($previousYearRankings->{'2nd_league'}) || 
                        isset($previousYearRankings->{'top16_finals'})
                    ) {

                        if(
                            ($currentYearRankings->{'1st_elite'} ?? null) === null &&
                            ($currentYearRankings->{'1st_pro'} ?? null) === null &&
                            ($currentYearRankings->{'1st_adv'} ?? null) === null &&
                            ($currentYearRankings->{'1st_int'} ?? null) === null &&
                            ($currentYearRankings->{'1st_rookie'} ?? null) === null &&

                            ($previousYearRankings->{'2nd_elite'} ?? null) === null &&
                            ($previousYearRankings->{'2nd_pro'} ?? null) === null &&
                            ($previousYearRankings->{'2nd_adv'} ?? null) === null &&
                            ($previousYearRankings->{'2nd_int'} ?? null) === null &&
                            ($previousYearRankings->{'2nd_rookie'} ?? null) === null &&

                            ($previousYearRankings->{'3rd_elite'} ?? null) === null &&
                            ($previousYearRankings->{'3rd_pro'} ?? null) === null &&
                            ($previousYearRankings->{'3rd_adv'} ?? null) === null &&
                            ($previousYearRankings->{'3rd_int'} ?? null) === null &&
                            ($previousYearRankings->{'3rd_rookie'} ?? null) === null &&

                            ($previousYearRankings->{'4th_elite'} ?? null) === null &&
                            ($previousYearRankings->{'4th_pro'} ?? null) === null &&
                            ($previousYearRankings->{'4th_adv'} ?? null) === null &&
                            ($previousYearRankings->{'4th_int'} ?? null) === null &&
                            ($previousYearRankings->{'4th_rookie'} ?? null) === null &&

                            ($previousYearRankings->{'1st_league'} ?? null) === null &&
                            ($previousYearRankings->{'2nd_league'} ?? null) === null &&
                            ($previousYearRankings->{'top16_finals'} ?? null) === null
                        ) {
                            $isNew = true;
                        } else {
                            $isNew = false;
                        }

                        $total = 
                            ($currentYearRankings->{'1st_elite'} ?? 0) + 
                            ($currentYearRankings->{'1st_pro'} ?? 0) + 
                            ($currentYearRankings->{'1st_adv'} ?? 0) + 
                            ($currentYearRankings->{'1st_int'} ?? 0) + 
                            ($currentYearRankings->{'1st_rookie'} ?? 0) +

                            ($currentYearRankings->{'2nd_elite'} ?? 0) + 
                            ($currentYearRankings->{'2nd_pro'} ?? 0) + 
                            ($currentYearRankings->{'2nd_adv'} ?? 0) + 
                            ($currentYearRankings->{'2nd_int'} ?? 0) + 
                            ($currentYearRankings->{'2nd_rookie'} ?? 0) + 

                            ($previousYearRankings->{'3rd_elite'} ?? 0) + 
                            ($previousYearRankings->{'3rd_pro'} ?? 0) + 
                            ($previousYearRankings->{'3rd_adv'} ?? 0) + 
                            ($previousYearRankings->{'3rd_int'} ?? 0) + 
                            ($previousYearRankings->{'3rd_rookie'} ?? 0) + 

                            ($previousYearRankings->{'4th_elite'} ?? 0) + 
                            ($previousYearRankings->{'4th_pro'} ?? 0) + 
                            ($previousYearRankings->{'4th_adv'} ?? 0) + 
                            ($previousYearRankings->{'4th_int'} ?? 0) + 
                            ($previousYearRankings->{'4th_rookie'} ?? 0) +

                            ($currentYearRankings->{'1st_league'} ?? 0) + 
                            ($previousYearRankings->{'2nd_league'} ?? 0) +
                            ($previousYearRankings->{'top16_finals'} ?? 0);

                        $played = 
                            (isset($currentYearRankings->{'1st_elite'}) ? 1 : 0) + 
                            (isset($currentYearRankings->{'1st_pro'}) ? 1 : 0) + 
                            (isset($currentYearRankings->{'1st_adv'}) ? 1 : 0) + 
                            (isset($currentYearRankings->{'1st_int'}) ? 1 : 0) + 
                            (isset($currentYearRankings->{'1st_rookie'}) ? 1 : 0) +
                    
                            (isset($currentYearRankings->{'2nd_elite'}) ? 1 : 0) + 
                            (isset($currentYearRankings->{'2nd_pro'}) ? 1 : 0) + 
                            (isset($currentYearRankings->{'2nd_adv'}) ? 1 : 0) + 
                            (isset($currentYearRankings->{'2nd_int'}) ? 1 : 0) + 
                            (isset($currentYearRankings->{'2nd_rookie'}) ? 1 : 0) + 

                            (isset($previousYearRankings->{'3rd_elite'}) ? 1 : 0) + 
                            (isset($previousYearRankings->{'3rd_pro'}) ? 1 : 0) + 
                            (isset($previousYearRankings->{'3rd_adv'}) ? 1 : 0) + 
                            (isset($previousYearRankings->{'3rd_int'}) ? 1 : 0) + 
                            (isset($previousYearRankings->{'3rd_rookie'}) ? 1 : 0) + 

                            (isset($previousYearRankings->{'4th_elite'}) ? 1 : 0) + 
                            (isset($previousYearRankings->{'4th_pro'}) ? 1 : 0) + 
                            (isset($previousYearRankings->{'4th_adv'}) ? 1 : 0) + 
                            (isset($previousYearRankings->{'4th_int'}) ? 1 : 0) + 
                            (isset($previousYearRankings->{'4th_rookie'}) ? 1 : 0) + 
                    
                            (isset($currentYearRankings->{'1st_league'}) ? 1 : 0) + 
                            (isset($previousYearRankings->{'2nd_league'}) ? 1 : 0) +
                            (isset($previousYearRankings->{'top16_finals'}) ? 1 : 0);

                        $quarter_rankings[] = [
                            'user_id' => $user->id,
                            'player' => $user->name,
                            '1st_elite' => $currentYearRankings->{'1st_elite'} ?? "n/a",
                            '1st_pro' => $currentYearRankings->{'1st_pro'} ?? "n/a",
                            '1st_adv' => $currentYearRankings->{'1st_adv'} ?? "n/a",
                            '1st_int' => $currentYearRankings->{'1st_int'} ?? "n/a",
                            '1st_rookie' => $currentYearRankings->{'1st_rookie'} ?? "n/a",

                            '2nd_elite' => $currentYearRankings->{'2nd_elite'} ?? "n/a",
                            '2nd_pro' => $currentYearRankings->{'2nd_pro'} ?? "n/a",
                            '2nd_adv' => $currentYearRankings->{'2nd_adv'} ?? "n/a",
                            '2nd_int' => $currentYearRankings->{'2nd_int'} ?? "n/a",
                            '2nd_rookie' => $currentYearRankings->{'2nd_rookie'} ?? "n/a",

                            '3rd_elite' => $previousYearRankings->{'3rd_elite'} ?? "n/a",
                            '3rd_pro' => $previousYearRankings->{'3rd_pro'} ?? "n/a",
                            '3rd_adv' => $previousYearRankings->{'3rd_adv'} ?? "n/a",
                            '3rd_int' => $previousYearRankings->{'3rd_int'} ?? "n/a",
                            '3rd_rookie' => $previousYearRankings->{'3rd_rookie'} ?? "n/a",

                            '4th_elite' => $previousYearRankings->{'4th_elite'} ?? "n/a",
                            '4th_pro' => $previousYearRankings->{'4th_pro'} ?? "n/a",
                            '4th_adv' => $previousYearRankings->{'4th_adv'} ?? "n/a",
                            '4th_int' => $previousYearRankings->{'4th_int'} ?? "n/a",
                            '4th_rookie' => $previousYearRankings->{'4th_rookie'} ?? "n/a",

                            '1st_league' => $currentYearRankings->{'1st_league'} ?? "n/a", 
                            '2nd_league' => $previousYearRankings->{'2nd_league'} ?? "n/a",
                            'top16_finals' => $previousYearRankings->{'top16_finals'} ?? "n/a",

                            'total' => $total,
                            'played' => $played,
                            'previous_ranking' => $user->previous_ranking,
                            'current_ranking' => $user->current_ranking,
                            'isNew' => $isNew
                        ];
                    }

                } else if ($settings->rankings_type == '2nd Quarter') {
                    
                    // 3rd Quarter Calculation
                    if (
                        isset($currentYearRankings->{'1st_elite'}) || isset($currentYearRankings->{'1st_pro'}) || 
                        isset($currentYearRankings->{'1st_adv'}) || isset($currentYearRankings->{'1st_int'}) || 
                        isset($currentYearRankings->{'1st_rookie'}) || 

                        isset($currentYearRankings->{'2nd_elite'}) || isset($currentYearRankings->{'2nd_pro'}) || 
                        isset($currentYearRankings->{'2nd_adv'}) || isset($currentYearRankings->{'2nd_int'}) || 
                        isset($currentYearRankings->{'2nd_rookie'}) ||

                        isset($currentYearRankings->{'3rd_elite'}) || isset($currentYearRankings->{'3rd_pro'}) || 
                        isset($currentYearRankings->{'3rd_adv'}) || isset($currentYearRankings->{'3rd_int'}) || 
                        isset($currentYearRankings->{'3rd_rookie'}) || 
                        
                        isset($previousYearRankings->{'4th_elite'}) || isset($previousYearRankings->{'4th_pro'}) || 
                        isset($previousYearRankings->{'4th_adv'}) || isset($previousYearRankings->{'4th_int'}) || 
                        isset($previousYearRankings->{'4th_rookie'}) ||

                        isset($currentYearRankings->{'1st_league'}) || isset($previousYearRankings->{'2nd_league'}) || 
                        isset($previousYearRankings->{'top16_finals'})
                    ) {

                        if(
                            ($currentYearRankings->{'1st_elite'} ?? null) === null &&
                            ($currentYearRankings->{'1st_pro'} ?? null) === null &&
                            ($currentYearRankings->{'1st_adv'} ?? null) === null &&
                            ($currentYearRankings->{'1st_int'} ?? null) === null &&
                            ($currentYearRankings->{'1st_rookie'} ?? null) === null &&

                            ($currentYearRankings->{'2nd_elite'} ?? null) === null &&
                            ($currentYearRankings->{'2nd_pro'} ?? null) === null &&
                            ($currentYearRankings->{'2nd_adv'} ?? null) === null &&
                            ($currentYearRankings->{'2nd_int'} ?? null) === null &&
                            ($currentYearRankings->{'2nd_rookie'} ?? null) === null &&

                            ($previousYearRankings->{'3rd_elite'} ?? null) === null &&
                            ($previousYearRankings->{'3rd_pro'} ?? null) === null &&
                            ($previousYearRankings->{'3rd_adv'} ?? null) === null &&
                            ($previousYearRankings->{'3rd_int'} ?? null) === null &&
                            ($previousYearRankings->{'3rd_rookie'} ?? null) === null &&

                            ($previousYearRankings->{'4th_elite'} ?? null) === null &&
                            ($previousYearRankings->{'4th_pro'} ?? null) === null &&
                            ($previousYearRankings->{'4th_adv'} ?? null) === null &&
                            ($previousYearRankings->{'4th_int'} ?? null) === null &&
                            ($previousYearRankings->{'4th_rookie'} ?? null) === null &&

                            ($currentYearRankings->{'1st_league'} ?? null) === null &&
                            ($previousYearRankings->{'2nd_league'} ?? null) === null &&
                            ($previousYearRankings->{'top16_finals'} ?? null) === null
                        ) {
                            $isNew = true;
                        } else {
                            $isNew = false;
                        }

                        $total = 
                            ($currentYearRankings->{'1st_elite'} ?? 0) + 
                            ($currentYearRankings->{'1st_pro'} ?? 0) + 
                            ($currentYearRankings->{'1st_adv'} ?? 0) + 
                            ($currentYearRankings->{'1st_int'} ?? 0) + 
                            ($currentYearRankings->{'1st_rookie'} ?? 0) +

                            ($currentYearRankings->{'2nd_elite'} ?? 0) + 
                            ($currentYearRankings->{'2nd_pro'} ?? 0) + 
                            ($currentYearRankings->{'2nd_adv'} ?? 0) + 
                            ($currentYearRankings->{'2nd_int'} ?? 0) + 
                            ($currentYearRankings->{'2nd_rookie'} ?? 0) + 

                            ($currentYearRankings->{'3rd_elite'} ?? 0) + 
                            ($currentYearRankings->{'3rd_pro'} ?? 0) + 
                            ($currentYearRankings->{'3rd_adv'} ?? 0) + 
                            ($currentYearRankings->{'3rd_int'} ?? 0) + 
                            ($currentYearRankings->{'3rd_rookie'} ?? 0) + 

                            ($previousYearRankings->{'4th_elite'} ?? 0) + 
                            ($previousYearRankings->{'4th_pro'} ?? 0) + 
                            ($previousYearRankings->{'4th_adv'} ?? 0) + 
                            ($previousYearRankings->{'4th_int'} ?? 0) + 
                            ($previousYearRankings->{'4th_rookie'} ?? 0) + 

                            ($currentYearRankings->{'1st_league'} ?? 0) + 
                            ($previousYearRankings->{'2nd_league'} ?? 0) +
                            ($previousYearRankings->{'top16_finals'} ?? 0);

                        $played = 
                            (isset($currentYearRankings->{'1st_elite'}) ? 1 : 0) + 
                            (isset($currentYearRankings->{'1st_pro'}) ? 1 : 0) + 
                            (isset($currentYearRankings->{'1st_adv'}) ? 1 : 0) + 
                            (isset($currentYearRankings->{'1st_int'}) ? 1 : 0) + 
                            (isset($currentYearRankings->{'1st_rookie'}) ? 1 : 0) +
                    
                            (isset($currentYearRankings->{'2nd_elite'}) ? 1 : 0) + 
                            (isset($currentYearRankings->{'2nd_pro'}) ? 1 : 0) + 
                            (isset($currentYearRankings->{'2nd_adv'}) ? 1 : 0) + 
                            (isset($currentYearRankings->{'2nd_int'}) ? 1 : 0) + 
                            (isset($currentYearRankings->{'2nd_rookie'}) ? 1 : 0) + 

                            (isset($currentYearRankings->{'3rd_elite'}) ? 1 : 0) + 
                            (isset($currentYearRankings->{'3rd_pro'}) ? 1 : 0) + 
                            (isset($currentYearRankings->{'3rd_adv'}) ? 1 : 0) + 
                            (isset($currentYearRankings->{'3rd_int'}) ? 1 : 0) + 
                            (isset($currentYearRankings->{'3rd_rookie'}) ? 1 : 0) + 

                            (isset($previousYearRankings->{'4th_elite'}) ? 1 : 0) + 
                            (isset($previousYearRankings->{'4th_pro'}) ? 1 : 0) + 
                            (isset($previousYearRankings->{'4th_adv'}) ? 1 : 0) + 
                            (isset($previousYearRankings->{'4th_int'}) ? 1 : 0) + 
                            (isset($previousYearRankings->{'4th_rookie'}) ? 1 : 0) + 
                    
                            (isset($currentYearRankings->{'1st_league'}) ? 1 : 0) + 
                            (isset($previousYearRankings->{'2nd_league'}) ? 1 : 0) +
                            (isset($previousYearRankings->{'top16_finals'}) ? 1 : 0);

                        $quarter_rankings[] = [
                            'user_id' => $user->id,
                            'player' => $user->name,
                            '1st_elite' => $currentYearRankings->{'1st_elite'} ?? "n/a",
                            '1st_pro' => $currentYearRankings->{'1st_pro'} ?? "n/a",
                            '1st_adv' => $currentYearRankings->{'1st_adv'} ?? "n/a",
                            '1st_int' => $currentYearRankings->{'1st_int'} ?? "n/a",
                            '1st_rookie' => $currentYearRankings->{'1st_rookie'} ?? "n/a",

                            '2nd_elite' => $currentYearRankings->{'2nd_elite'} ?? "n/a",
                            '2nd_pro' => $currentYearRankings->{'2nd_pro'} ?? "n/a",
                            '2nd_adv' => $currentYearRankings->{'2nd_adv'} ?? "n/a",
                            '2nd_int' => $currentYearRankings->{'2nd_int'} ?? "n/a",
                            '2nd_rookie' => $currentYearRankings->{'2nd_rookie'} ?? "n/a",

                            '3rd_elite' => $currentYearRankings->{'3rd_elite'} ?? "n/a",
                            '3rd_pro' => $currentYearRankings->{'3rd_pro'} ?? "n/a",
                            '3rd_adv' => $currentYearRankings->{'3rd_adv'} ?? "n/a",
                            '3rd_int' => $currentYearRankings->{'3rd_int'} ?? "n/a",
                            '3rd_rookie' => $currentYearRankings->{'3rd_rookie'} ?? "n/a",

                            '4th_elite' => $previousYearRankings->{'4th_elite'} ?? "n/a",
                            '4th_pro' => $previousYearRankings->{'4th_pro'} ?? "n/a",
                            '4th_adv' => $previousYearRankings->{'4th_adv'} ?? "n/a",
                            '4th_int' => $previousYearRankings->{'4th_int'} ?? "n/a",
                            '4th_rookie' => $previousYearRankings->{'4th_rookie'} ?? "n/a",

                            '1st_league' => $currentYearRankings->{'1st_league'} ?? "n/a", 
                            '2nd_league' => $previousYearRankings->{'2nd_league'} ?? "n/a",
                            'top16_finals' => $previousYearRankings->{'top16_finals'} ?? "n/a",

                            'total' => $total,
                            'played' => $played,
                            'previous_ranking' => $user->previous_ranking,
                            'current_ranking' => $user->current_ranking,
                            'isNew' => $isNew
                        ];
                    }

                } else if ($settings->rankings_type == '3rd Quarter') {
                    
                    // 4th Quarter Calculation
                    if (
                        isset($currentYearRankings->{'1st_elite'}) || isset($currentYearRankings->{'1st_pro'}) || 
                        isset($currentYearRankings->{'1st_adv'}) || isset($currentYearRankings->{'1st_int'}) || 
                        isset($currentYearRankings->{'1st_rookie'}) || 

                        isset($currentYearRankings->{'2nd_elite'}) || isset($currentYearRankings->{'2nd_pro'}) || 
                        isset($currentYearRankings->{'2nd_adv'}) || isset($currentYearRankings->{'2nd_int'}) || 
                        isset($currentYearRankings->{'2nd_rookie'}) ||

                        isset($currentYearRankings->{'3rd_elite'}) || isset($currentYearRankings->{'3rd_pro'}) || 
                        isset($currentYearRankings->{'3rd_adv'}) || isset($currentYearRankings->{'3rd_int'}) || 
                        isset($currentYearRankings->{'3rd_rookie'}) || 
                        
                        isset($currentYearRankings->{'4th_elite'}) || isset($currentYearRankings->{'4th_pro'}) || 
                        isset($currentYearRankings->{'4th_adv'}) || isset($currentYearRankings->{'4th_int'}) || 
                        isset($currentYearRankings->{'4th_rookie'}) ||

                        isset($currentYearRankings->{'1st_league'}) || isset($currentYearRankings->{'2nd_league'}) || 
                        isset($currentYearRankings->{'top16_finals'})
                    ) {

                        if(
                            ($currentYearRankings->{'1st_elite'} ?? null) === null &&
                            ($currentYearRankings->{'1st_pro'} ?? null) === null &&
                            ($currentYearRankings->{'1st_adv'} ?? null) === null &&
                            ($currentYearRankings->{'1st_int'} ?? null) === null &&
                            ($currentYearRankings->{'1st_rookie'} ?? null) === null &&

                            ($currentYearRankings->{'2nd_elite'} ?? null) === null &&
                            ($currentYearRankings->{'2nd_pro'} ?? null) === null &&
                            ($currentYearRankings->{'2nd_adv'} ?? null) === null &&
                            ($currentYearRankings->{'2nd_int'} ?? null) === null &&
                            ($currentYearRankings->{'2nd_rookie'} ?? null) === null &&

                            ($currentYearRankings->{'3rd_elite'} ?? null) === null &&
                            ($currentYearRankings->{'3rd_pro'} ?? null) === null &&
                            ($currentYearRankings->{'3rd_adv'} ?? null) === null &&
                            ($currentYearRankings->{'3rd_int'} ?? null) === null &&
                            ($currentYearRankings->{'3rd_rookie'} ?? null) === null &&

                            ($previousYearRankings->{'4th_elite'} ?? null) === null &&
                            ($previousYearRankings->{'4th_pro'} ?? null) === null &&
                            ($previousYearRankings->{'4th_adv'} ?? null) === null &&
                            ($previousYearRankings->{'4th_int'} ?? null) === null &&
                            ($previousYearRankings->{'4th_rookie'} ?? null) === null &&

                            ($currentYearRankings->{'1st_league'} ?? null) === null &&
                            ($previousYearRankings->{'2nd_league'} ?? null) === null &&
                            ($previousYearRankings->{'top16_finals'} ?? null) === null
                        ) {
                            $isNew = true;
                        } else {
                            $isNew = false;
                        }

                        $total = 
                            ($currentYearRankings->{'1st_elite'} ?? 0) + 
                            ($currentYearRankings->{'1st_pro'} ?? 0) + 
                            ($currentYearRankings->{'1st_adv'} ?? 0) + 
                            ($currentYearRankings->{'1st_int'} ?? 0) + 
                            ($currentYearRankings->{'1st_rookie'} ?? 0) +

                            ($currentYearRankings->{'2nd_elite'} ?? 0) + 
                            ($currentYearRankings->{'2nd_pro'} ?? 0) + 
                            ($currentYearRankings->{'2nd_adv'} ?? 0) + 
                            ($currentYearRankings->{'2nd_int'} ?? 0) + 
                            ($currentYearRankings->{'2nd_rookie'} ?? 0) + 

                            ($currentYearRankings->{'3rd_elite'} ?? 0) + 
                            ($currentYearRankings->{'3rd_pro'} ?? 0) + 
                            ($currentYearRankings->{'3rd_adv'} ?? 0) + 
                            ($currentYearRankings->{'3rd_int'} ?? 0) + 
                            ($currentYearRankings->{'3rd_rookie'} ?? 0) + 

                            ($currentYearRankings->{'4th_elite'} ?? 0) + 
                            ($currentYearRankings->{'4th_pro'} ?? 0) + 
                            ($currentYearRankings->{'4th_adv'} ?? 0) + 
                            ($currentYearRankings->{'4th_int'} ?? 0) + 
                            ($currentYearRankings->{'4th_rookie'} ?? 0) +

                            ($currentYearRankings->{'1st_league'} ?? 0) + 
                            ($currentYearRankings->{'2nd_league'} ?? 0) +
                            ($currentYearRankings->{'top16_finals'} ?? 0);

                        $played = 
                            (isset($currentYearRankings->{'1st_elite'}) ? 1 : 0) + 
                            (isset($currentYearRankings->{'1st_pro'}) ? 1 : 0) + 
                            (isset($currentYearRankings->{'1st_adv'}) ? 1 : 0) + 
                            (isset($currentYearRankings->{'1st_int'}) ? 1 : 0) + 
                            (isset($currentYearRankings->{'1st_rookie'}) ? 1 : 0) +
                    
                            (isset($currentYearRankings->{'2nd_elite'}) ? 1 : 0) + 
                            (isset($currentYearRankings->{'2nd_pro'}) ? 1 : 0) + 
                            (isset($currentYearRankings->{'2nd_adv'}) ? 1 : 0) + 
                            (isset($currentYearRankings->{'2nd_int'}) ? 1 : 0) + 
                            (isset($currentYearRankings->{'2nd_rookie'}) ? 1 : 0) + 

                            (isset($currentYearRankings->{'3rd_elite'}) ? 1 : 0) + 
                            (isset($currentYearRankings->{'3rd_pro'}) ? 1 : 0) + 
                            (isset($currentYearRankings->{'3rd_adv'}) ? 1 : 0) + 
                            (isset($currentYearRankings->{'3rd_int'}) ? 1 : 0) + 
                            (isset($currentYearRankings->{'3rd_rookie'}) ? 1 : 0) + 

                            (isset($currentYearRankings->{'4th_elite'}) ? 1 : 0) + 
                            (isset($currentYearRankings->{'4th_pro'}) ? 1 : 0) + 
                            (isset($currentYearRankings->{'4th_adv'}) ? 1 : 0) + 
                            (isset($currentYearRankings->{'4th_int'}) ? 1 : 0) + 
                            (isset($currentYearRankings->{'4th_rookie'}) ? 1 : 0) + 
                    
                            (isset($currentYearRankings->{'1st_league'}) ? 1 : 0) + 
                            (isset($currentYearRankings->{'2nd_league'}) ? 1 : 0) +
                            (isset($currentYearRankings->{'top16_finals'}) ? 1 : 0);

                        $quarter_rankings[] = [
                            'user_id' => $user->id,
                            'player' => $user->name,
                            '1st_elite' => $currentYearRankings->{'1st_elite'} ?? "n/a",
                            '1st_pro' => $currentYearRankings->{'1st_pro'} ?? "n/a",
                            '1st_adv' => $currentYearRankings->{'1st_adv'} ?? "n/a",
                            '1st_int' => $currentYearRankings->{'1st_int'} ?? "n/a",
                            '1st_rookie' => $currentYearRankings->{'1st_rookie'} ?? "n/a",

                            '2nd_elite' => $currentYearRankings->{'2nd_elite'} ?? "n/a",
                            '2nd_pro' => $currentYearRankings->{'2nd_pro'} ?? "n/a",
                            '2nd_adv' => $currentYearRankings->{'2nd_adv'} ?? "n/a",
                            '2nd_int' => $currentYearRankings->{'2nd_int'} ?? "n/a",
                            '2nd_rookie' => $currentYearRankings->{'2nd_rookie'} ?? "n/a",

                            '3rd_elite' => $currentYearRankings->{'3rd_elite'} ?? "n/a",
                            '3rd_pro' => $currentYearRankings->{'3rd_pro'} ?? "n/a",
                            '3rd_adv' => $currentYearRankings->{'3rd_adv'} ?? "n/a",
                            '3rd_int' => $currentYearRankings->{'3rd_int'} ?? "n/a",
                            '3rd_rookie' => $currentYearRankings->{'3rd_rookie'} ?? "n/a",

                            '4th_elite' => $currentYearRankings->{'4th_elite'} ?? "n/a",
                            '4th_pro' => $currentYearRankings->{'4th_pro'} ?? "n/a",
                            '4th_adv' => $currentYearRankings->{'4th_adv'} ?? "n/a",
                            '4th_int' => $currentYearRankings->{'4th_int'} ?? "n/a",
                            '4th_rookie' => $currentYearRankings->{'4th_rookie'} ?? "n/a",

                            '1st_league' => $currentYearRankings->{'1st_league'} ?? "n/a", 
                            '2nd_league' => $currentYearRankings->{'2nd_league'} ?? "n/a",
                            'top16_finals' => $currentYearRankings->{'top16_finals'} ?? "n/a",

                            'total' => $total,
                            'played' => $played,
                            'previous_ranking' => $user->previous_ranking,
                            'current_ranking' => $user->current_ranking,
                            'isNew' => $isNew
                        ];
                    }

                }
            
            }
        }

        usort($quarter_rankings, function($a, $b) {
            return $b['total'] <=> $a['total'];
        });
        
        $rank = 1;
        $skip = 1;
        for ($i = 0; $i < count($quarter_rankings); $i++) {
            if ($i > 0 && $quarter_rankings[$i]['total'] != $quarter_rankings[$i - 1]['total']) {
                $rank += $skip;
                $skip = 1;
            } else if ($i > 0 && $quarter_rankings[$i]['total'] == $quarter_rankings[$i - 1]['total']) {
                $skip++;
            }
            $quarter_rankings[$i]['ranking'] = $rank;
        }
        
        return view('backend.administrator.publishRankings', compact('settings', 'quarter_rankings', 'rankings_type'));
    
    }

    public function publishRankingsSubmit(Request $request) 
    {
        $quarterRankingsJson = $request->input('quarter_rankings');
        $getRankings = json_decode($quarterRankingsJson, true);

        $all_users = User::all();
        foreach($all_users as $getUser) {
            $getUser->is_current = 'No';
            $getUser->save();
        }
        
        foreach($getRankings as $ranking) {
            $user = User::findOrFail($ranking['user_id']);
            
            $user->previous_ranking = $user->current_ranking;
            $user->current_ranking = $ranking['ranking'];
            
            if($ranking['isNew']) {
                $user->move = 'New';
            } else {
                if($user->current_ranking == $user->previous_ranking) {
                    $user->move = '-';
                } else {
                    $user->move = $user->previous_ranking - $user->current_ranking;
                }
            }
            
            $user->total_points = $ranking['total'];
            $user->tour_played = $ranking['played'];
            $user->is_current = 'Yes';
            $user->save();

        }

        $settings = Settings::findOrFail(1);
        $settings->rankings_type = $request->rankings_type;
        $settings->rankings_last_updated = now();
        $settings->publish_button_status = 'Locked';
        $settings->save();

        Session::flash('success', 'Rankings Published Successfully !');
        return redirect()->route('frontend.bg.rankings');

    }
}
